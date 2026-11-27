<?php

use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

require_once dirname(__DIR__, 2) . '/includes/galaxy.php';

class GalaxyDisplayTest extends TestCase
{
    public function testDisplaysMoonIconForAggregatedPlanet(): void
    {
        global $lang;
        $lang = ['GALAXY_SR' => 'RE', 'GALAXY_CR' => 'RC'];

        $html = displayGalaxyTabletbodytr($this->planetRow(true), false);

        $this->assertStringContainsString('ogame-icon-moon', $html);
    }

    public function testDoesNotDisplayMoonIconWithoutMoon(): void
    {
        global $lang;
        $lang = ['GALAXY_SR' => 'RE', 'GALAXY_CR' => 'RC'];

        $html = displayGalaxyTabletbodytr($this->planetRow(false), false);

        $this->assertStringNotContainsString('ogame-icon-moon', $html);
    }

    private function planetRow(bool $hasMoon): array
    {
        return [
            'type' => 'planet',
            'has_moon' => $hasMoon,
            'galaxy' => 1,
            'system' => 14,
            'row' => 5,
            'planet_name' => 'Gaul',
            'ally_name' => '',
            'ally_id' => 0,
            'player_name' => '',
            'player_id' => false,
            'status' => '',
            'Pha' => 0,
            'PoSa' => 0,
            'hided' => false,
            'allied' => false,
            'report_spy' => 0,
            'report_rc' => 0,
            'last_update' => 0,
            'last_update_user_name' => '',
        ];
    }
}
