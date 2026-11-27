<?php

use Ogsteam\Ogspy\Helper\SearchCriteria_Helper;
use Ogsteam\Ogspy\Model\AstroObject_Model;
use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

foreach ([
    'TABLE_USER_BUILDING' => 'ogspy_game_astro_object',
    'TABLE_USER' => 'ogspy_user',
    'TABLE_GAME_PLAYER' => 'ogspy_game_player',
    'TABLE_GAME_ALLY' => 'ogspy_game_ally',
    'TABLE_PARSEDSPY' => 'ogspy_game_spy',
] as $constant => $value) {
    if (!defined($constant)) {
        define($constant, $value);
    }
}

class FakeAstroObjectDb
{
    public array $queries = [];

    public function __construct(
        private array $rows,
        private int $count,
        private array $spyRows = []
    ) {
    }

    public function sql_query(string $query): string
    {
        $this->queries[] = $query;
        if (str_contains($query, 'SELECT `coordinates`, `active`')) {
            return 'spy';
        }
        return str_starts_with($query, 'SELECT count(*)') ? 'count' : 'rows';
    }

    public function sql_fetch_row(string $result): array
    {
        return [$this->count];
    }

    public function sql_fetch_assoc(string $result): array|false
    {
        if ($result === 'spy') {
            return array_shift($this->spyRows) ?: false;
        }
        return array_shift($this->rows) ?: false;
    }

    public function sql_escape_string(string $value): string
    {
        return addslashes($value);
    }
}

class AstroObjectModelTest extends TestCase
{
    private array $serverConfig = [
        'num_of_galaxies' => 9,
        'num_of_systems' => 499,
    ];

    public function testRegularSearchReturnsPlanetWithAggregatedMoon(): void
    {
        $criteria = (new SearchCriteria_Helper($this->serverConfig))
            ->setPlayerName('Obelix')
            ->setGalaxyDown(1)
            ->setGalaxyUp(2);

        [$result, $db] = $this->runFind($criteria, [[
            'type' => 'planet',
            'galaxy' => '1',
            'system' => '14',
            'row' => '5',
            'moon_id' => '987',
            'Pha' => '7',
            'PoSa' => '1',
            'planet_name' => 'Gaul',
        ]]);

        $this->assertSame(1, $result['total_row']);
        $this->assertCount(1, $result['planets']);
        $this->assertTrue($result['planets'][0]['has_moon']);
        $this->assertSame('planet', $result['planets'][0]['type']);
        $this->assertSame('7', $result['planets'][0]['Pha']);
        $this->assertStringContainsString(
            "LEFT JOIN ogspy_game_astro_object moon ON moon.`galaxy` = uni.`galaxy`",
            $db->queries[0]
        );
        $this->assertStringContainsString("moon.`type` = 'moon'", $db->queries[0]);
        $this->assertStringContainsString("WHERE uni.`type` = 'planet'", $db->queries[0]);
        $this->assertStringContainsString("uni.`galaxy` BETWEEN 1 AND 2", $db->queries[0]);
        $this->assertStringContainsString('LIMIT 0, 30', $db->queries[1]);
    }

    public function testRegularSearchMarksPlanetWithoutMoon(): void
    {
        $criteria = (new SearchCriteria_Helper($this->serverConfig))->setPlayerName('Asterix');

        [$result] = $this->runFind($criteria, [[
            'type' => 'planet',
            'galaxy' => '1',
            'system' => '15',
            'row' => '8',
            'moon_id' => null,
            'planet_name' => 'Village',
        ]]);

        $this->assertFalse($result['planets'][0]['has_moon']);
    }

    public function testMoonSearchKeepsMoonRowsWithoutAggregationJoin(): void
    {
        $criteria = (new SearchCriteria_Helper($this->serverConfig))->setIsMoon(true);

        [$result, $db] = $this->runFind($criteria, [[
            'type' => 'moon',
            'galaxy' => '1',
            'system' => '14',
            'row' => '5',
            'moon_id' => null,
            'planet_name' => 'Gaul - Moon',
        ]]);

        $this->assertCount(1, $result['planets']);
        $this->assertTrue($result['planets'][0]['has_moon']);
        $this->assertSame('moon', $result['planets'][0]['type']);
        $this->assertStringContainsString("WHERE uni.`type` = 'moon'", $db->queries[0]);
        $this->assertStringNotContainsString('ogspy_game_astro_object moon', $db->queries[0]);
    }

    public function testSpiedSearchKeepsTypeFilterAcrossAllCoordinates(): void
    {
        $criteria = (new SearchCriteria_Helper($this->serverConfig))->setIsSpied(true);

        [, $db] = $this->runFind($criteria, [[
            'type' => 'planet',
            'galaxy' => '1',
            'system' => '14',
            'row' => '5',
            'moon_id' => null,
        ]], [
            ['coordinates' => '1:14:5'],
            ['coordinates' => '2:20:8'],
        ]);

        $selectQuery = $db->queries[2];
        $this->assertStringContainsString(
            "uni.`type` = 'planet' AND ((uni.`galaxy` = 1 AND uni.`system` = 14 AND uni.`row` = 5)"
                . " OR (uni.`galaxy` = 2 AND uni.`system` = 20 AND uni.`row` = 8))",
            $selectQuery
        );
    }

    private function runFind(SearchCriteria_Helper $criteria, array $rows, array $spyRows = []): array
    {
        global $db, $log;

        $db = new FakeAstroObjectDb($rows, count($rows), $spyRows);
        $log = new Monolog\Logger('test');
        $model = new AstroObject_Model();

        return [$model->find($criteria, ['galaxy' => 'DESC']), $db];
    }
}
