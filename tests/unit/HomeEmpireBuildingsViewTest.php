<?php

use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

class HomeEmpireBuildingsViewTest extends TestCase
{
    public function testDisplaysCompletePlanetBuildingSectionIncludingScanner(): void
    {
        $html = $this->render(true);

        $this->assertStringContainsString('Planet buildings', $html);
        $this->assertStringContainsString('Interstellar Anomaly Scanner', $html);
        $this->assertStringContainsString('scanner_101', $html);
        $this->assertStringContainsString('dock_101', $html);
        $this->assertStringContainsString('Alliance Depot', $html);
        $this->assertMatchesRegularExpression('/scanner_101[^>]*>7</', $html);
        $this->assertMatchesRegularExpression('/scanner_102[^>]*>0</', $html);
    }

    public function testHidesDisabledAllianceDepotWithoutAffectingScanner(): void
    {
        $html = $this->render(false);

        $this->assertStringNotContainsString('Alliance Depot', $html);
        $this->assertStringContainsString('Interstellar Anomaly Scanner', $html);
    }

    private function render(bool $allianceDepotEnabled): string
    {
        $labels = [
            'HOME_EMPIRE_BUILDINGS' => 'Planet buildings',
            'HOME_EMPIRE_MINE_METAL' => 'Metal Mine',
            'HOME_EMPIRE_MINE_CRYSTAL' => 'Crystal Mine',
            'HOME_EMPIRE_MINE_DEUT' => 'Deuterium Synthesizer',
            'HOME_EMPIRE_METALSTORAGE' => 'Metal Storage',
            'HOME_EMPIRE_CRYSTALSTORAGE' => 'Crystal Storage',
            'HOME_EMPIRE_DEUTSTORAGE' => 'Deuterium Tank',
            'HOME_EMPIRE_SOLAR_PLANT' => 'Solar Plant',
            'HOME_EMPIRE_FUSION_PLANT' => 'Fusion Reactor',
            'HOME_EMPIRE_ROBOTS_PLANT' => 'Robotics Factory',
            'HOME_EMPIRE_NANITES_PLANT' => 'Nanite Factory',
            'HOME_EMPIRE_SHIPYARD' => 'Shipyard',
            'HOME_EMPIRE_RESEARCHLAB' => 'Research Lab',
            'HOME_EMPIRE_ALLIANCEDEPOT' => 'Alliance Depot',
            'HOME_EMPIRE_TERRAFORMER' => 'Terraformer',
            'HOME_EMPIRE_MISSILESSILO' => 'Missile Silo',
            'HOME_EMPIRE_DOCK' => 'Space Dock',
            'HOME_EMPIRE_ANOMALY_SCANNER' => 'Interstellar Anomaly Scanner',
        ];
        $lang = $labels;
        $codes = ['M', 'C', 'D', 'HM', 'HC', 'HD', 'CES', 'CEF', 'UdR', 'UdN', 'CSp', 'Lab', 'DdR', 'Ter', 'Silo', 'Dock'];
        $firstPlanet = array_fill_keys($codes, 1);
        $secondPlanet = array_fill_keys($codes, 0);
        $firstPlanet['Scanner'] = 7;
        $secondPlanet['Scanner'] = 0;
        $player_planets = [101 => $firstPlanet, 102 => $secondPlanet];
        $server_config = ['ddr' => $allianceDepotEnabled ? 1 : 0];
        $colspan_planets = 3;

        ob_start();
        require dirname(__DIR__, 2) . '/views/home_empire_buildings.php'; // NOSONAR - render the section under test
        return ob_get_clean();
    }
}
