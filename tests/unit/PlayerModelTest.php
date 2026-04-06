<?php

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Ogsteam\Ogspy\Model\Player_Model;
use Monolog\Logger;

// Mock database class that mimics the sql_db interface
class MockDatabase
{
    public function sql_query($query) { return true; }
    public function sql_fetch_assoc($result) { return []; }
    public function sql_fetch_row($result) { return []; }
    public function sql_escape_string($string) { return $string; }
}

class PlayerModelTest extends TestCase
{
    private MockObject $mockDb;
    private Stub $mockLog;
    private Player_Model $playerModel;

    protected function setUp(): void
    {
        // Define constants that OGSpy expects
        if (!defined('IN_SPYOGAME')) {
            define('IN_SPYOGAME', true);
        }
        if (!defined('TABLE_GAME_PLAYER')) {
            define('TABLE_GAME_PLAYER', 'ogspy_game_player');
        }
        if (!defined('TABLE_USER')) {
            define('TABLE_USER', 'ogspy_user');
        }

        // Mock database connection using our custom mock class
        $this->mockDb = $this->createMock(MockDatabase::class);
        $this->mockDb->method('sql_query')->willReturn(true);
    $this->mockDb->method('sql_fetch_assoc')->willReturn([]);
    // Provide a default row with one null value so code expecting numeric offsets won't warn
    $this->mockDb->method('sql_fetch_row')->willReturn([null]);
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);

        // Mock logger
        $this->mockLog = $this->createStub(Logger::class);

        // Set global variables that the model constructor expects
        $GLOBALS['db'] = $this->mockDb;
        $GLOBALS['log'] = $this->mockLog;

        $this->playerModel = new Player_Model();
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetPlayerDataReturnsPlayerInfo(): void
    {
        $playerId = 123;
        $expectedPlayerData = [
            'id' => 123,
            'name' => 'TestPlayer',
            'status' => 'active',
            'class' => 'collector',
            'ally_id' => 456,
            'datadate' => '2025-09-29 10:00:00',
            'off_commandant' => 1,
            'off_amiral' => 0,
            'off_ingenieur' => 1,
            'off_geologue' => 0,
            'off_technocrate' => 1
        ];

        // Create a fresh mock for this test
        $mockDb = $this->createMock(MockDatabase::class);
        $mockDb->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains('SELECT `id`, `name`, `status`'))
            ->willReturn(true);

        $mockDb->expects($this->once())
            ->method('sql_fetch_assoc')
            ->willReturn($expectedPlayerData);

        // Replace the global db for this test
        $GLOBALS['db'] = $mockDb;
        $playerModel = new Player_Model();

        $result = $playerModel->get_player_data($playerId);

        $this->assertEquals($expectedPlayerData, $result);
    }

    public function testGetPlayerDataReturnsFalseWhenPlayerNotFound(): void
    {
        $playerId = 999;

        $this->mockDb->expects($this->once())
            ->method('sql_query')
            ->willReturn(true);

        $this->mockDb->expects($this->once())
            ->method('sql_fetch_assoc')
            ->willReturn([null]);

        $result = $this->playerModel->get_player_data($playerId);

        $this->assertFalse($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetPlayerNameReturnsCorrectName(): void
    {
        $playerId = 123;
        $expectedName = 'TestPlayer';

        // Create a fresh mock for this test
        $mockDb = $this->createMock(MockDatabase::class);
        $mockDb->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains('SELECT `name`'))
            ->willReturn(true);

        $mockDb->expects($this->once())
            ->method('sql_fetch_row')
            ->willReturn([$expectedName]);

        // Replace the global db for this test
        $GLOBALS['db'] = $mockDb;
        $playerModel = new Player_Model();

        $result = $playerModel->get_player_name($playerId);

        $this->assertEquals($expectedName, $result);
    }

    public function testGetPlayerNameReturnsFalseWhenNotFound(): void
    {
        $playerId = 999;

        $this->mockDb->expects($this->once())
            ->method('sql_query')
            ->willReturn(true);

        $this->mockDb->expects($this->once())
            ->method('sql_fetch_row')
            ->willReturn([null]);

        $result = $this->playerModel->get_player_name($playerId);

        $this->assertFalse($result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetPlayerIdReturnsCorrectId(): void
    {
        $playerName = 'TestPlayer';
        $expectedId = 123;

        // Create a fresh mock for this test
        $mockDb = $this->createMock(MockDatabase::class);
        $mockDb->method('sql_escape_string')->willReturnArgument(0);
        $mockDb->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains("WHERE `name` = '$playerName'"))
            ->willReturn(true);

        $mockDb->expects($this->once())
            ->method('sql_fetch_row')
            ->willReturn([$expectedId]);

        // Replace the global db for this test
        $GLOBALS['db'] = $mockDb;
        $GLOBALS['log'] = $this->mockLog;
        $playerModel = new Player_Model();

        $result = $playerModel->getPlayerId($playerName);

        $this->assertEquals($expectedId, $result);
    }

    public function testGetPlayerIdReturnsFalseWhenNotFound(): void
    {
        $playerName = 'NonExistentPlayer';

        $this->mockDb->expects($this->once())
            ->method('sql_query')
            ->willReturn(true);

        $this->mockDb->expects($this->once())
            ->method('sql_fetch_row')
            ->willReturn([null]); // Simulate not found row with null value

        $result = $this->playerModel->getPlayerId($playerName);

        $this->assertFalse($result);
    }

    public function testSetGameAccountNameUpdatesDatabase(): void
    {
        $userId = 456;
        $playerId = 123;

        $this->mockDb->expects($this->once())
            ->method('sql_escape_string')
            ->with($playerId)
            ->willReturn($playerId);

        $this->mockDb->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains("UPDATE " . TABLE_USER . " SET `player_id` = '$playerId' WHERE `id` = $userId"))
            ->willReturn(true);

        // This method doesn't return anything, so we just verify it doesn't throw
        $this->playerModel->set_game_account_name($userId, $playerId);

        $this->assertTrue(true); // If we get here without exception, test passes
    }

    public function testSetGameAccountNameHandlesIntegerConversion(): void
    {
        $userId = "456"; // String that should be converted to int
        $playerId = 123;

        $this->mockDb->expects($this->once())
            ->method('sql_escape_string')
            ->willReturn($playerId);

        $this->mockDb->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains("WHERE `id` = 456")) // Should be converted to int
            ->willReturn(true);

        $this->playerModel->set_game_account_name($userId, $playerId);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        // Clean up global variables
        unset($GLOBALS['db']);
        unset($GLOBALS['log']);
    }
}