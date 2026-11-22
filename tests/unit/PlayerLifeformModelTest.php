<?php

use Monolog\Logger;
use Ogsteam\Ogspy\Model\Player_Lifeform_Model;
use PHPUnit\Framework\TestCase;

foreach ([
    'IN_SPYOGAME' => true,
    'TABLE_USER_BUILDING' => 'ogspy_game_astro_object',
    'TABLE_GAME_LIFEFORM_BUILDING' => 'ogspy_game_astro_object_lifeform_building',
    'TABLE_GAME_LIFEFORM_RESEARCH' => 'ogspy_game_astro_object_lifeform_research',
] as $name => $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

require_once dirname(__DIR__, 2) . '/model/Player_Lifeform_Model.php';

class LifeformModelDatabase
{
    public array $queries = [];
    private array $offsets = [];

    public function __construct(private array $rowsByTable = [])
    {
    }

    public function sql_query(string $query): string
    {
        $this->queries[] = $query;
        return $query;
    }

    public function sql_fetch_assoc(string $query): array|false
    {
        $table = str_contains($query, TABLE_GAME_LIFEFORM_BUILDING)
            ? TABLE_GAME_LIFEFORM_BUILDING
            : TABLE_GAME_LIFEFORM_RESEARCH;
        $offset = $this->offsets[$table] ?? 0;
        $this->offsets[$table] = $offset + 1;

        return $this->rowsByTable[$table][$offset] ?? false;
    }
}

class PlayerLifeformModelTest extends TestCase
{
    public function testSelectGroupsRealTechnologyIdsByPlanetAndCategory(): void
    {
        $database = new LifeformModelDatabase([
            TABLE_GAME_LIFEFORM_BUILDING => [
                ['astro_object_id' => '101', 'technology_id' => '11101', 'level' => '12'],
                ['astro_object_id' => '101', 'technology_id' => '12101', 'level' => '8'],
            ],
            TABLE_GAME_LIFEFORM_RESEARCH => [
                ['astro_object_id' => '101', 'technology_id' => '11201', 'level' => '4'],
                ['astro_object_id' => '101', 'technology_id' => '12201', 'level' => '7'],
            ],
        ]);
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        $result = (new Player_Lifeform_Model())->select_player_lifeforms(42);

        $this->assertSame([
            101 => [
                'buildings' => [11101 => 12, 12101 => 8],
                'research' => [11201 => 4, 12201 => 7],
            ],
        ], $result);
        $this->assertCount(2, $database->queries);
        $this->assertStringContainsString('astro.`player_id` = 42', $database->queries[0]);
    }

    public function testDeleteRemovesBothCategoriesForAstroObject(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        (new Player_Lifeform_Model())->delete_astro_object(101);

        $this->assertSame([
            'DELETE FROM ' . TABLE_GAME_LIFEFORM_BUILDING . ' WHERE `astro_object_id` = 101',
            'DELETE FROM ' . TABLE_GAME_LIFEFORM_RESEARCH . ' WHERE `astro_object_id` = 101',
        ], $database->queries);
    }
}
