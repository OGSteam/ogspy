<?php

use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

class AnomalyScannerViewTest extends TestCase
{
    public function testDisplaysScannerLevelForEveryPlanet(): void
    {
        $lang = ['HOME_EMPIRE_ANOMALY_SCANNER' => 'Scanner d’anomalies interstellaire'];
        $player_planets = [101 => ['Scanner' => 7], 102 => ['Scanner' => 0]];

        ob_start();
        require dirname(__DIR__, 2) . '/views/home_empire_anomaly_scanner.php'; // NOSONAR - render the partial under test
        $html = ob_get_clean();

        $this->assertStringContainsString('Scanner d’anomalies interstellaire', $html);
        $this->assertStringContainsString('scanner_101', $html);
        $this->assertMatchesRegularExpression('/>\s*7\s*</', $html);
        $this->assertMatchesRegularExpression('/>\s*0\s*</', $html);
    }
}
