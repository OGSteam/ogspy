<?php

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Ogsteam\Ogspy\Model\Ally_Model;
use Monolog\Logger;

class MockAllyDatabase
{
    public function sql_query($query) { return true; }
    public function sql_fetch_assoc($result) { return []; }
    public function sql_fetch_row($result) { return false; }
    public function sql_escape_string($string) { return $string; }
}

#[AllowMockObjectsWithoutExpectations]
class AllyModelTest extends TestCase
{
    private MockObject $mockDb;
    private Stub $mockLog;

    protected function setUp(): void
    {
        if (!defined('IN_SPYOGAME')) {
            define('IN_SPYOGAME', true);
        }
        if (!defined('TABLE_GAME_ALLY')) {
            define('TABLE_GAME_ALLY', 'ogspy_game_ally');
        }

        $this->mockDb = $this->createMock(MockAllyDatabase::class);
        $this->mockDb->method('sql_query')->willReturn(true);
        $this->mockDb->method('sql_fetch_assoc')->willReturn([]);
        $this->mockDb->method('sql_fetch_row')->willReturn(false);
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);

        $this->mockLog = $this->createStub(Logger::class);

        $GLOBALS['db'] = $this->mockDb;
        $GLOBALS['log'] = $this->mockLog;
    }

    // --- get_ally_name() ---

    public function testGetAllyNameReturnsNameFromDatabase(): void
    {
        $allyId  = 42;
        $expectedName = 'Borg Collective';

        $mockDb = $this->createMock(MockAllyDatabase::class);
        $mockDb->expects($this->once())
            ->method('sql_fetch_assoc')
            ->willReturn(['name' => $expectedName]);
        $mockDb->method('sql_query')->willReturn(true);

        $GLOBALS['db'] = $mockDb;
        $result = (new Ally_Model())->get_ally_name($allyId);

        $this->assertSame($expectedName, $result);
    }

    public function testGetAllyNameReturnsFalseWhenAllyNotFound(): void
    {
        $this->mockDb->method('sql_fetch_assoc')->willReturn([]);

        $GLOBALS['db'] = $this->mockDb;
        $this->assertFalse((new Ally_Model())->get_ally_name(999));
    }

    public function testGetAllyNameQueryFiltersById(): void
    {
        $allyId = 7;
        $capturedQuery = '';

        $mockDb = $this->createMock(MockAllyDatabase::class);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_assoc')->willReturn([]);

        $GLOBALS['db'] = $mockDb;
        (new Ally_Model())->get_ally_name($allyId);

        $this->assertStringContainsString('WHERE `id` = ' . $allyId, $capturedQuery);
        $this->assertStringContainsString('ogspy_game_ally', $capturedQuery);
    }

    // --- getAllyId() ---

    public function testGetAllyIdEscapesTagInputBeforeQuery(): void
    {
        $maliciousTag = "TAG' OR '1'='1";

        $mockDb = $this->createMock(MockAllyDatabase::class);
        $mockDb->expects($this->once())
            ->method('sql_escape_string')
            ->with($maliciousTag)
            ->willReturn("TAG\\' OR \\'1\\'=\\'1");
        $mockDb->method('sql_query')->willReturn(true);
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Ally_Model())->getAllyId($maliciousTag);
    }

    public function testGetAllyIdQueryFiltersEscapedTagInWhere(): void
    {
        $tag = 'BORG';
        $capturedQuery = '';

        $mockDb = $this->createMock(MockAllyDatabase::class);
        $mockDb->method('sql_escape_string')->willReturnArgument(0);
        $mockDb->method('sql_query')
            ->willReturnCallback(function ($q) use (&$capturedQuery) {
                $capturedQuery = $q;
                return true;
            });
        $mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $mockDb;
        (new Ally_Model())->getAllyId($tag);

        $this->assertStringContainsString("WHERE `tag` = 'BORG'", $capturedQuery);
    }

    public function testGetAllyIdReturnsIdWhenFound(): void
    {
        $expectedId = 7;

        $mockDb = $this->createMock(MockAllyDatabase::class);
        $mockDb->method('sql_escape_string')->willReturnArgument(0);
        $mockDb->method('sql_query')->willReturn(true);
        $mockDb->method('sql_fetch_row')->willReturn([$expectedId]);

        $GLOBALS['db'] = $mockDb;
        $result = (new Ally_Model())->getAllyId('BORG');

        $this->assertSame($expectedId, $result);
    }

    public function testGetAllyIdReturnsFalseWhenNoRowFound(): void
    {
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);
        $this->mockDb->method('sql_fetch_row')->willReturn([null]);

        $GLOBALS['db'] = $this->mockDb;
        $this->assertFalse((new Ally_Model())->getAllyId('UNKNOWN'));
    }

    public function testGetAllyIdReturnsFalseWhenRowIsEmpty(): void
    {
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);
        $this->mockDb->method('sql_fetch_row')->willReturn(false);

        $GLOBALS['db'] = $this->mockDb;
        $this->assertFalse((new Ally_Model())->getAllyId('NONE'));
    }
}
