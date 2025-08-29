<?php


use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testPasswordGenerator()
    {
        define("IN_SPYOGAME", true);
        require_once 'includes/functions.php';
        $password = generateRandomPassword();

        // Assert that the password is a string
        $this->assertIsString($password);

        // Assert that the password length is 12
        $this->assertEquals(12, strlen($password));
    }

    public function testPasswgenerateKey()
    {
        define("IN_SPYOGAME", true);
        require_once 'includes/functions.php';
        $result = generate_key();

        $this->assertFileExists('./config/key.php');

    }





}
