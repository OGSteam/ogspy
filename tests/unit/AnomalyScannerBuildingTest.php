<?php

use Monolog\Logger;
use Ogsteam\Ogspy\Model\Player_Building_Model;
use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}
if (!defined('TABLE_USER_BUILDING')) {
    define('TABLE_USER_BUILDING', 'ogspy_game_astro_object');
}

require_once dirname(__DIR__, 2) . '/includes/player.php';

class AnomalyScannerBuildingDatabase
{
    public string $query = '';

    public function sql_query(string $query): string
    {
        $this->query = $query;
        return $query;
    }

    public function sql_fetch_assoc(): false
    {
        return false;
    }
}

class AnomalyScannerBuildingTest extends TestCase
{
    public function testBuildingModelSelectsScannerLevel(): void
    {
        $database = new AnomalyScannerBuildingDatabase();
        $GLOBALS['db'] = $database;
        $GLOBALS['log'] = $this->createStub(Logger::class);

        (new Player_Building_Model())->select_player_building_list(42);

        $this->assertStringContainsString('`Scanner`', $database->query);
    }

    public function testScannerLevelsCountAsUsedPlanetFields(): void
    {
        $this->assertSame(10, calculate_fields_used([
            'M' => 3,
            'Scanner' => 7,
            'Dock' => 5,
            'fields' => 200,
        ]));
    }
}
