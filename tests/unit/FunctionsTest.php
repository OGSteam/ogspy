<?php


use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testPasswordGenerator()
    {
        if (!defined('IN_SPYOGAME')) define('IN_SPYOGAME', true);
        require_once 'includes/functions.php';
        $password = generateRandomPassword();

        // Assert that the password is a string
        $this->assertIsString($password);

        // Assert that the password length is 12
        $this->assertEquals(12, strlen($password));
    }

    public function testPasswgenerateKey()
    {
        if (!defined('IN_SPYOGAME')) define('IN_SPYOGAME', true);
        require_once 'includes/functions.php';
        $result = generate_key();

        $this->assertFileExists('./config/key.php');

    }

    // -------------------------------------------------------------------------
    // admin_reset_check_preconditions() tests
    // -------------------------------------------------------------------------

    private function loadResetDeps(): void
    {
        if (!defined('IN_SPYOGAME')) define('IN_SPYOGAME', true);
        require_once 'includes/functions.php';
        require_once 'includes/token.php';
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    public function testAdminResetDeniedForNonAdmin(): void
    {
        $this->loadResetDeps();
        $result = admin_reset_check_preconditions(
            ['admin' => 0, 'coadmin' => 1],
            'POST',
            ['reset_confirm' => ADMIN_RESET_CONFIRM_KEYWORD, 'token' => 'any']
        );
        $this->assertSame('access_denied', $result);
    }

    public function testAdminResetDeniedForGetRequest(): void
    {
        $this->loadResetDeps();
        $result = admin_reset_check_preconditions(
            ['admin' => 1],
            'GET',
            ['reset_confirm' => ADMIN_RESET_CONFIRM_KEYWORD, 'token' => 'any']
        );
        $this->assertSame('not_post', $result);
    }

    public function testAdminResetDeniedForWrongKeyword(): void
    {
        $this->loadResetDeps();
        $result = admin_reset_check_preconditions(
            ['admin' => 1],
            'POST',
            ['reset_confirm' => 'wrong', 'token' => 'any']
        );
        $this->assertSame('invalid_confirm', $result);
    }

    public function testAdminResetDeniedForEmptyKeyword(): void
    {
        $this->loadResetDeps();
        $result = admin_reset_check_preconditions(
            ['admin' => 1],
            'POST',
            []
        );
        $this->assertSame('invalid_confirm', $result);
    }

    public function testAdminResetDeniedForInvalidToken(): void
    {
        $this->loadResetDeps();
        $result = admin_reset_check_preconditions(
            ['admin' => 1],
            'POST',
            ['reset_confirm' => ADMIN_RESET_CONFIRM_KEYWORD, 'token' => 'bad____token']
        );
        $this->assertSame('invalid_token', $result);
    }

    public function testAdminResetAllowedWithValidToken(): void
    {
        $this->loadResetDeps();
        $token = token::staticGetToken(600, 'admin_reset');
        $result = admin_reset_check_preconditions(
            ['admin' => 1],
            'POST',
            ['reset_confirm' => ADMIN_RESET_CONFIRM_KEYWORD, 'token' => $token]
        );
        $this->assertNull($result);
    }
}
