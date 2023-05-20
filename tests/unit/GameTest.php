<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testArrayressourcesContent(): void
    {
        define("IN_SPYOGAME", true);
        require_once('includes/ogame.php');

        $expected = array('M' => 1000, 'C' => 2000, 'D' => 3000, 'NRJ' => 100, 'AM' => 500);
        $result = ogame_array_ressource(1000, 2000, 3000, 100, 500);

        $this->assertIsArray($result);
        $this->assertSame($expected, $result);
    }


}
