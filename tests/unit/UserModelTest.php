<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ogsteam\Ogspy\Model\User_Model;
use Monolog\Logger;

// Mock database class for User_Model tests
class MockUserDatabase
{
    public function sql_query($query) { return true; }
    public function sql_fetch_row($result) { return []; }
    public function sql_numrows($result) { return 0; }
    public function sql_escape_string($string) { return $string; }
}

class UserModelTest extends TestCase
{
    private MockObject $mockDb;
    private MockObject $mockLog;
    private User_Model $userModel;

    protected function setUp(): void
    {
        // Define constants that OGSpy expects
        if (!defined('IN_SPYOGAME')) {
            define('IN_SPYOGAME', true);
        }
        if (!defined('TABLE_USER')) {
            define('TABLE_USER', 'ogspy_user');
        }

        // Mock database connection using our custom mock class
        $this->mockDb = $this->createMock(MockUserDatabase::class);
        $this->mockDb->method('sql_query')->willReturn(true);
        $this->mockDb->method('sql_fetch_row')->willReturn([]);
        $this->mockDb->method('sql_numrows')->willReturn(0);
        $this->mockDb->method('sql_escape_string')->willReturnArgument(0);

        // Mock logger
        $this->mockLog = $this->createMock(Logger::class);

        // Set global variables that the model constructor expects
        $GLOBALS['db'] = $this->mockDb;
        $GLOBALS['log'] = $this->mockLog;

        $this->userModel = new User_Model();
    }

    public function testSelectUserLoginReturnsFalseWhenNoUser(): void
    {
        $login = 'testuser';
        $password = 'testpass';

        $this->mockDb->expects($this->exactly(2)) // Called twice: once for login, once for password
            ->method('sql_escape_string')
            ->willReturnArgument(0);

        $this->mockDb->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains("WHERE `name` = '$login'"))
            ->willReturn(true);

        $this->mockDb->expects($this->once())
            ->method('sql_numrows')
            ->willReturn(0);

        $result = $this->userModel->select_user_login($login, $password);

        $this->assertFalse($result);
    }

    public function testSelectUserLoginReturnsUserDataWhenFound(): void
    {
        $login = 'testuser';
        $password = 'testpass';
        $expectedUserData = [123, 1, 'hashed_password'];

        // Create a fresh mock for this test
        $mockDb = $this->createMock(MockUserDatabase::class);
        $mockDb->expects($this->exactly(2))
            ->method('sql_escape_string')
            ->willReturnArgument(0);

        $mockDb->expects($this->once())
            ->method('sql_query')
            ->with($this->stringContains("WHERE `name` = '$login'"))
            ->willReturn(true);

        $mockDb->expects($this->once())
            ->method('sql_numrows')
            ->willReturn(1);

        $mockDb->expects($this->once())
            ->method('sql_fetch_row')
            ->willReturn($expectedUserData);

        // Replace the global db for this test
        $GLOBALS['db'] = $mockDb;
        $userModel = new User_Model();

        $result = $userModel->select_user_login($login, $password);

        $this->assertEquals($expectedUserData, $result);
    }

    public function testSelectUserLoginEscapesInput(): void
    {
        $login = "dangerous'login";
        $password = "dangerous'password";

        $this->mockDb->expects($this->exactly(2))
            ->method('sql_escape_string')
            ->willReturnArgument(0);

        $this->mockDb->expects($this->once())
            ->method('sql_query')
            ->willReturn(true);

        $this->mockDb->expects($this->once())
            ->method('sql_numrows')
            ->willReturn(0);

        $result = $this->userModel->select_user_login($login, $password);

        $this->assertFalse($result);
    }

    protected function tearDown(): void
    {
        // Clean up global variables
        unset($GLOBALS['db']);
        unset($GLOBALS['log']);
    }
}