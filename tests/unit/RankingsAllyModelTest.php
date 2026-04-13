<?php

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Ogsteam\Ogspy\Model\Rankings_Ally_Model;
use Monolog\Logger;

class MockRankingsAllyDatabase
{
    public function sql_query($query) { return true; }
    public function sql_fetch_row($result) { return false; }
    public function sql_fetch_assoc($result) { return []; }
    public function sql_escape_string($string) { return $string; }
    public function sql_numrows($result) { return 0; }
}

// Define all table constants needed by ranking models (idempotent guards)
foreach ([
    'IN_SPYOGAME'                         => true,
    'TABLE_GAME_ALLY'                     => 'ogspy_game_ally',
    'TABLE_GAME_PLAYER'                   => 'ogspy_game_player',
    'TABLE_RANK_ALLY_POINTS'              => 'ogspy_game_rank_ally_points',
    'TABLE_RANK_ALLY_ECO'                 => 'ogspy_game_rank_ally_economics',
    'TABLE_RANK_ALLY_TECHNOLOGY'          => 'ogspy_game_rank_ally_technology',
    'TABLE_RANK_ALLY_MILITARY'            => 'ogspy_game_rank_ally_military',
    'TABLE_RANK_ALLY_MILITARY_BUILT'      => 'ogspy_game_rank_ally_military_built',
    'TABLE_RANK_ALLY_MILITARY_LOOSE'      => 'ogspy_game_rank_ally_military_loose',
    'TABLE_RANK_ALLY_MILITARY_DESTRUCT'   => 'ogspy_game_rank_ally_military_destruct',
    'TABLE_RANK_ALLY_HONOR'               => 'ogspy_game_rank_ally_honor',
    'TABLE_RANK_PLAYER_POINTS'            => 'ogspy_game_rank_player_points',
    'TABLE_RANK_PLAYER_ECO'               => 'ogspy_game_rank_player_economics',
    'TABLE_RANK_PLAYER_TECHNOLOGY'        => 'ogspy_game_rank_player_technology',
    'TABLE_RANK_PLAYER_MILITARY'          => 'ogspy_game_rank_player_military',
    'TABLE_RANK_PLAYER_MILITARY_BUILT'    => 'ogspy_game_rank_player_military_built',
    'TABLE_RANK_PLAYER_MILITARY_LOOSE'    => 'ogspy_game_rank_player_military_loose',
    'TABLE_RANK_PLAYER_MILITARY_DESTRUCT' => 'ogspy_game_rank_player_military_destruct',
    'TABLE_RANK_PLAYER_HONOR'             => 'ogspy_game_rank_player_honor',
] as $name => $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

#[AllowMockObjectsWithoutExpectations]
class RankingsAllyModelTest extends TestCase
{
    private MockObject $mockDb;
    private Stub $mockLog;

    protected function setUp(): void
    {
        $this->mockDb = $this->createMock(MockRankingsAllyDatabase::class);
        $this->mockDb->method('sql_query')->willReturn(true);
        $this->mockDb->method('sql_fetch_row')->willReturn(false);
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);

        $this->mockLog = $this->createStub(Logger::class);

        $GLOBALS['db'] = $this->mockDb;
        $GLOBALS['log'] = $this->mockLog;
    }

    // --- get_all_ranktable_byally() ---

    public function testGetAllRanktableByallyQueryUsesAllyId(): void
    {
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsAllyDatabase::class);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Ally_Model())->get_all_ranktable_byally(5);

        $this->assertStringContainsString('ally_id', $capturedQuery,
            'Query should join on ally_id, not the old `ally` text column');
        $this->assertStringNotContainsString('`general`.`ally` = `eco`.`ally`', $capturedQuery,
            'Query must not join on the dropped `ally` name column');
        $this->assertStringContainsString('ogspy_game_ally', $capturedQuery,
            'Query should join ogspy_game_ally to resolve ally name at render time');
    }

    public function testGetAllRanktableByallyWhereFiltersById(): void
    {
        $allyId = 42;
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsAllyDatabase::class);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Ally_Model())->get_all_ranktable_byally($allyId);

        $this->assertStringContainsString((string)$allyId, $capturedQuery);
        $this->assertStringNotContainsString("`general`.`ally` = '", $capturedQuery,
            'Must not filter by alliance name string');
    }

    public function testGetAllRanktableByallyReturnsEmptyArrayWhenNoRows(): void
    {
        $GLOBALS['db'] = $this->mockDb;
        $this->assertSame([], (new Rankings_Ally_Model())->get_all_ranktable_byally(1));
    }

    public function testGetAllRanktableByallyReturnsOneRowCorrectly(): void
    {
        // 19 columns: datadate, ally_name, member, general_rank, general_pts,
        // eco_rank, eco_pts, tech_rank, tech_pts, mil_rank, mil_pts,
        // milb_rank, milb_pts, mill_rank, mill_pts, mild_rank, mild_pts,
        // milh_rank, milh_pts
        $dbRow = [
            1735340400, 'Borg Collective', 10,
            1, 50000, 2, 10000, 3, 8000,
            4, 20000, 5, 5000, 6, 3000,
            7, 2000, 8, 1000,
        ];

        $mockDb = $this->createMock(MockRankingsAllyDatabase::class);
        $mockDb->method('sql_query')->willReturn(true);
        $mockDb->method('sql_fetch_row')
            ->willReturnOnConsecutiveCalls($dbRow, false);

        $GLOBALS['db'] = $mockDb;
        $result = (new Rankings_Ally_Model())->get_all_ranktable_byally(1);

        $this->assertCount(1, $result);
        $this->assertSame(1735340400, $result[0]['datadate']);
        $this->assertSame('Borg Collective', $result[0]['ally_name']);
        $this->assertSame(10, $result[0]['member']);
        $this->assertSame(1, $result[0]['general_rank']);
        $this->assertSame(50000, $result[0]['general_pts']);
        $this->assertSame(5000, $result[0]['general_pts_mb']); // 50000/10
    }

    // --- get_all_ranktable_bydate() ---

    public function testGetAllRanktableByDateQueryUsesAllyId(): void
    {
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsAllyDatabase::class);
        $mockDb->method('sql_escape_string')->willReturnArgument(0);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Ally_Model())->get_all_ranktable_bydate(1735340400);

        $this->assertStringContainsString('ally_id', $capturedQuery);
        $this->assertStringNotContainsString('`general`.`ally` = `eco`.`ally`', $capturedQuery);
        $this->assertStringContainsString('ogspy_game_ally', $capturedQuery,
            'Must JOIN game_ally to resolve names at render time');
    }

    public function testGetAllRanktableByDateReturnsEmptyWhenNoRows(): void
    {
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);

        $GLOBALS['db'] = $this->mockDb;
        $this->assertSame([], (new Rankings_Ally_Model())->get_all_ranktable_bydate(1735340400));
    }
}
