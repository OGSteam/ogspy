<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    public function testArrayressourcesContent(): void
    {
        if (!defined('IN_SPYOGAME')) define('IN_SPYOGAME', true);
        // Only require the small helper needed for this test
        require_once __DIR__ . '/../../includes/ogame_structs.php';

        $expected = array('M' => 1000, 'C' => 2000, 'D' => 3000, 'NRJ' => 100, 'AM' => 500);
        $result = ogame_array_ressource(1000, 2000, 3000, 100, 500);

        $this->assertIsArray($result);
        $this->assertSame($expected, $result);
    }


}
