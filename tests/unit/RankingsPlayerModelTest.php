<?php

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Ogsteam\Ogspy\Model\Rankings_Player_Model;
use Monolog\Logger;

// Table constants are already defined in RankingsAllyModelTest.php which
// loads first (alphabetically). Guards ensure idempotency when running this
// file in isolation.
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

class MockRankingsPlayerDatabase
{
    public function sql_query($query) { return true; }
    public function sql_fetch_row($result) { return false; }
    public function sql_fetch_assoc($result) { return []; }
    public function sql_escape_string($string) { return $string; }
    public function sql_numrows($result) { return 0; }
}

#[AllowMockObjectsWithoutExpectations]
class RankingsPlayerModelTest extends TestCase
{
    private MockObject $mockDb;
    private Stub $mockLog;

    protected function setUp(): void
    {
        $this->mockDb = $this->createMock(MockRankingsPlayerDatabase::class);
        $this->mockDb->method('sql_query')->willReturn(true);
        $this->mockDb->method('sql_fetch_row')->willReturn(false);
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);

        $this->mockLog = $this->createStub(Logger::class);

        $GLOBALS['db'] = $this->mockDb;
        $GLOBALS['log'] = $this->mockLog;
    }

    // --- get_all_ranktable_byplayer() ---

    public function testGetAllRanktableByplayerQueryUsesPlayerId(): void
    {
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsPlayerDatabase::class);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Player_Model())->get_all_ranktable_byplayer(123);

        $this->assertStringContainsString('player_id', $capturedQuery,
            'Query must join on player_id, not the old `player` text column');
        $this->assertStringNotContainsString('`general`.`player` = `eco`.`player`', $capturedQuery,
            'Old name-based JOIN must not appear');
        $this->assertStringContainsString('ogspy_game_player', $capturedQuery,
            'Must JOIN game_player to resolve player name at render time');
    }

    public function testGetAllRanktableByplayerSqlHasSingleWhereClause(): void
    {
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsPlayerDatabase::class);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Player_Model())->get_all_ranktable_byplayer(123);

        $this->assertSame(1, substr_count(strtoupper($capturedQuery), ' WHERE '),
            'Refactored SQL must have exactly one WHERE clause (regression: old + new clauses were stacked)');
        $this->assertSame(1, substr_count(strtoupper($capturedQuery), 'ORDER BY'),
            'Refactored SQL must have exactly one ORDER BY clause');
    }

    public function testGetAllRanktableByplayerWhereFiltersById(): void
    {
        $playerId = 77;
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsPlayerDatabase::class);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Player_Model())->get_all_ranktable_byplayer($playerId);

        $this->assertStringContainsString('player_id` = ' . $playerId, $capturedQuery);
        $this->assertStringNotContainsString("`general`.`player` = '", $capturedQuery,
            'Must not filter by player name string');
    }

    public function testGetAllRanktableByplayerReturnsEmptyWhenNoRows(): void
    {
        $GLOBALS['db'] = $this->mockDb;
        $this->assertSame([], (new Rankings_Player_Model())->get_all_ranktable_byplayer(1));
    }

    public function testGetAllRanktableByplayerReturnsOneRowCorrectly(): void
    {
        // 19 columns: datadate, player_name, ally_name, general_rank, general_pts,
        // eco_rank, eco_pts, tech_rank, tech_pts, mil_rank, mil_pts,
        // milb_rank, milb_pts, mill_rank, mill_pts, mild_rank, mild_pts,
        // milh_rank, milh_pts
        $dbRow = [
            1735340400, 'Picard', 'Borg Collective',
            1, 90000, 2, 30000, 3, 15000,
            4, 20000, 5, 5000, 6, 3000,
            7, 2000, 8, 1000,
        ];

        $mockDb = $this->createMock(MockRankingsPlayerDatabase::class);
        $mockDb->method('sql_query')->willReturn(true);
        $mockDb->method('sql_fetch_row')
            ->willReturnOnConsecutiveCalls($dbRow, false);

        $GLOBALS['db'] = $mockDb;
        $result = (new Rankings_Player_Model())->get_all_ranktable_byplayer(1);

        $this->assertCount(1, $result);
        $this->assertSame(1735340400, $result[0]['datadate']);
        $this->assertSame('Picard', $result[0]['player_name']);
        $this->assertSame('Borg Collective', $result[0]['ally_name']);
        $this->assertSame(1, $result[0]['general_rank']);
        $this->assertSame(90000, $result[0]['general_pts']);
    }

    // --- get_all_ranktable_bydate() ---

    public function testGetAllRanktableByDateQueryUsesPlayerId(): void
    {
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsPlayerDatabase::class);
        $mockDb->method('sql_escape_string')->willReturnArgument(0);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Player_Model())->get_all_ranktable_bydate(1735340400);

        $this->assertStringContainsString('player_id', $capturedQuery,
            'Query must join on player_id, not the old `player` text column');
        $this->assertStringNotContainsString('`general`.`player` = `eco`.`player`', $capturedQuery);
        $this->assertStringContainsString('ogspy_game_player', $capturedQuery,
            'Must JOIN game_player to resolve player name at render time');
        $this->assertStringContainsString('ogspy_game_ally', $capturedQuery,
            'Must JOIN game_ally to resolve ally name at render time');
    }

    public function testGetAllRanktableByDateReturnsEmptyWhenNoRows(): void
    {
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);

        $GLOBALS['db'] = $this->mockDb;
        $this->assertSame([], (new Rankings_Player_Model())->get_all_ranktable_bydate(1735340400));
    }

    public function testGetAllRanktableByDateInvalidRefFallsBackToGeneral(): void
    {
        $capturedQuery = '';

        $mockDb = $this->createMock(MockRankingsPlayerDatabase::class);
        $mockDb->method('sql_escape_string')->willReturnArgument(0);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Rankings_Player_Model())->get_all_ranktable_bydate(1735340400, 1, 100, 'invalid_ref');

        $this->assertStringContainsString('`general`.`rank`', $capturedQuery,
            'Invalid ref must be replaced with general');
        $this->assertStringNotContainsString('invalid_ref', $capturedQuery);
    }
}
