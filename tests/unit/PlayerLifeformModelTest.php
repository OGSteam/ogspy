<?php

use Monolog\Logger;
use Ogsteam\Ogspy\Model\Player_Lifeform_Model;
use PHPUnit\Framework\TestCase;

foreach ([
    'IN_SPYOGAME' => true,
    'TABLE_USER_BUILDING' => 'ogspy_game_astro_object',
    'TABLE_GAME_LIFEFORM_BUILDING' => 'ogspy_game_lifeform_building',
    'TABLE_GAME_LIFEFORM_RESEARCH' => 'ogspy_game_lifeform_research',
    'TABLE_GAME_LIFEFORM_RESOURCE' => 'ogspy_game_lifeform_resource',
    'TABLE_GAME_LIFEFORM_RESOURCE_HISTORY' => 'ogspy_game_lifeform_resource_history',
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
        $table = match (true) {
            str_contains($query, TABLE_GAME_LIFEFORM_BUILDING) => TABLE_GAME_LIFEFORM_BUILDING,
            str_contains($query, TABLE_GAME_LIFEFORM_RESEARCH) => TABLE_GAME_LIFEFORM_RESEARCH,
            default => TABLE_GAME_LIFEFORM_RESOURCE,
        };
        $offset = $this->offsets[$table] ?? 0;
        $this->offsets[$table] = $offset + 1;

        return $this->rowsByTable[$table][$offset] ?? false;
    }

    public function sql_escape_string(string $value): string // NOSONAR - mirrors the legacy database adapter API used by the model
    {
        return addslashes($value);
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
            TABLE_GAME_LIFEFORM_RESOURCE => [[
                'astro_object_id' => '101',
                'species_code' => 'HUMANS',
                'population' => '12345678901234567890',
                'food' => '987654321',
                'last_update' => '1795478400',
            ]],
        ]);
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        $result = (new Player_Lifeform_Model())->select_player_lifeforms(42);

        $this->assertSame([
            101 => [
                'buildings' => [11101 => 12, 12101 => 8],
                'research' => [11201 => 4, 12201 => 7],
                'resources' => [
                    'species_code' => 'HUMANS',
                    'population' => '12345678901234567890',
                    'food' => '987654321',
                    'last_update' => 1795478400,
                ],
            ],
        ], $result);
        $this->assertCount(3, $database->queries);
        $this->assertStringContainsString('astro.`player_id` = 42', $database->queries[0]);
    }

    public function testStoresCurrentValuesAndOneDailySnapshot(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        $stored = (new Player_Lifeform_Model())->storeResourceObservation(101, [
            'species' => 'HUMANS',
            'population' => '0012345678901234567890',
            'food' => 0,
        ], 1795478400);

        $this->assertTrue($stored);
        $this->assertCount(2, $database->queries);
        $this->assertStringContainsString('INSERT INTO ' . TABLE_GAME_LIFEFORM_RESOURCE, $database->queries[0]);
        $this->assertStringContainsString("'12345678901234567890'", $database->queries[0]);
        $this->assertStringContainsString("'0'", $database->queries[0]);
        $this->assertStringContainsString("'2026-11-24'", $database->queries[1]);
        $this->assertStringContainsString('ON DUPLICATE KEY UPDATE', $database->queries[1]);
    }

    public function testAcceptsCompleteOverviewObservationFromXtense(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        (new Player_Lifeform_Model())->storeResourceObservation(33717002, [
            'species' => 'ROCKTAL',
            'population' => 1524,
            'food' => 120,
        ], 1776160665);

        $currentQuery = $database->queries[0];
        $this->assertStringContainsString('33717002', $currentQuery);
        $this->assertStringContainsString("'ROCKTAL'", $currentQuery);
        $this->assertStringContainsString("'1524'", $currentQuery);
        $this->assertStringContainsString("'120'", $currentQuery);
        $this->assertStringContainsString('1776160665', $currentQuery);
    }

    public function testAcceptsSpeciesOnlyObservationFromXtenseLifeformBuildings(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        (new Player_Lifeform_Model())->storeResourceObservation(
            33717002,
            ['species' => 'KAELESH'],
            1776160665
        );

        $currentQuery = $database->queries[0];
        $this->assertStringContainsString("`species_code` = 'KAELESH'", $currentQuery);
        $this->assertStringNotContainsString('`population`', $currentQuery);
        $this->assertStringNotContainsString('`food`', $currentQuery);
        $this->assertCount(2, $database->queries);
    }

    public function testMissingFieldsDoNotWriteOrEraseValues(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        $stored = (new Player_Lifeform_Model())->storeResourceObservation(101, [], 1795478400);

        $this->assertFalse($stored);
        $this->assertSame([], $database->queries);
    }

    public function testPartialAndNullValuesAreHandledExplicitly(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        (new Player_Lifeform_Model())->storeResourceObservation(101, ['population' => null], 1795478400);

        $this->assertStringContainsString('`population` = NULL', $database->queries[0]);
        $this->assertStringNotContainsString('`food`', $database->queries[0]);
        $this->assertStringNotContainsString('`species_code`', $database->queries[0]);
    }

    public function testRejectsInvalidAmountsAndSpecies(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);
        $model = new Player_Lifeform_Model();

        try {
            $model->storeResourceObservation(101, ['population' => '-1'], 1795478400);
            $this->fail('A negative population must be rejected.');
        } catch (InvalidArgumentException $exception) {
            $this->assertStringContainsString('population', $exception->getMessage());
        }

        $this->expectException(InvalidArgumentException::class);
        $model->storeResourceObservation(101, ['species' => 'UNKNOWN'], 1795478400);
    }

    public function testRejectsAmountsAboveUnsignedBigintRange(): void
    {
        $database = new LifeformModelDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('supported integer range');
        (new Player_Lifeform_Model())->storeResourceObservation(
            101,
            ['population' => '18446744073709551616'],
            1795478400
        );
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
            'DELETE FROM ' . TABLE_GAME_LIFEFORM_RESOURCE . ' WHERE `astro_object_id` = 101',
            'DELETE FROM ' . TABLE_GAME_LIFEFORM_RESOURCE_HISTORY . ' WHERE `astro_object_id` = 101',
        ], $database->queries);
    }
}
