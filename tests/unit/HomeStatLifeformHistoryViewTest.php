<?php

use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

class HomeStatLifeformHistoryViewTest extends TestCase
{
    public function testDisplaysPopulationAndFoodHistoryControls(): void
    {
        $html = $this->render(
            [
                101 => ['name' => 'Planet A', 'galaxy' => 1, 'system' => 2, 'row' => 3],
                102 => ['name' => 'Planet B', 'galaxy' => 1, 'system' => 2, 'row' => 4],
            ],
            [
                101 => [
                    [
                        'observation_day' => '2026-11-24',
                        'population' => '12345678901234567890',
                        'food' => '987654321',
                        'observed_at' => 1795478400,
                    ],
                    [
                        'observation_day' => '2026-11-25',
                        'population' => '12345678901234567999',
                        'food' => '987654000',
                        'observed_at' => 1795564800,
                    ],
                ],
            ]
        );

        $this->assertStringContainsString('Historique population et nourriture', $html);
        $this->assertStringContainsString('Planet A', $html);
        $this->assertStringContainsString('Planet B', $html);
        $this->assertStringContainsString('7 jours', $html);
        $this->assertStringContainsString('30 jours', $html);
        $this->assertStringContainsString('90 jours', $html);
        $this->assertStringContainsString('2026-11-24', $html);
        $this->assertStringContainsString('12345678901234567890', $html);
        $this->assertStringContainsString('987654321', $html);
        $this->assertStringNotContainsString('astro_object_id', $html);
        $this->assertStringNotContainsString('value="101"', $html);
    }

    public function testDisplaysCoordinatesAndInsufficientHistoryMessage(): void
    {
        $html = $this->render(
            [33717002 => ['name' => '', 'galaxy' => 3, 'system' => 17, 'row' => 2]],
            []
        );

        $this->assertStringContainsString('[3:17:2]', $html);
        $this->assertStringContainsString('Historique insuffisant', $html);
        $this->assertStringContainsString('"points":[]', $html);
        $this->assertStringNotContainsString('33717002', $html);
    }

    private function render(array $planets, array $history): string
    {
        $lang = [
            'HOME_STATS_LIFEFORM_HISTORY' => 'Historique population et nourriture',
            'HOME_STATS_LIFEFORM_HISTORY_PLANET' => 'Planète',
            'HOME_STATS_LIFEFORM_HISTORY_PERIOD' => 'Période',
            'HOME_STATS_LIFEFORM_HISTORY_DAYS' => '%d jours',
            'HOME_STATS_LIFEFORM_HISTORY_NO_DATA' => 'Historique insuffisant',
            'HOME_EMPIRE_LIFEFORM_POPULATION' => 'Population',
            'HOME_EMPIRE_LIFEFORM_FOOD' => 'Nourriture',
        ];
        $player_planets = $planets;
        $player_lifeform_history = $history;

        ob_start();
        require dirname(__DIR__, 2) . '/views/home_stat_lifeform_history.php'; // NOSONAR - render the partial
        return ob_get_clean();
    }
}
