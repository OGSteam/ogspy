<?php

use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

require_once dirname(__DIR__, 2) . '/includes/lifeform.php';

class HomeEmpireLifeformsViewTest extends TestCase
{
    public function testDisplaysLevelsByTechnologyAndPlanet(): void
    {
        $html = $this->render([
            101 => ['buildings' => [11101 => 12, 12101 => 8], 'research' => [11201 => 4]],
            102 => ['buildings' => [11101 => 7], 'research' => [12201 => 3]],
        ]);

        $this->assertStringContainsString('Bâtiments des formes de vie', $html);
        $this->assertStringContainsString('Recherches des formes de vie', $html);
        $this->assertStringContainsString('Humains', $html);
        $this->assertStringContainsString('Rock&#039;tal', $html);
        $this->assertStringContainsString('Secteur résidentiel', $html);
        $this->assertStringNotContainsString('#11101', $html);
        $this->assertMatchesRegularExpression('/>\s*12\s*</', $html);
        $this->assertMatchesRegularExpression('/>\s*7\s*</', $html);
    }

    public function testDisplaysAnExplicitMessageWithoutLifeformData(): void
    {
        $html = $this->render([]);

        $this->assertSame(3, substr_count($html, 'Aucune donnée de formes de vie reçue'));
    }

    public function testDisplaysCurrentResourcesWithoutTechnicalIdentifiers(): void
    {
        $html = $this->render([
            101 => [
                'buildings' => [],
                'research' => [],
                'resources' => [
                    'species_code' => 'HUMANS',
                    'population' => '12345678901234567890',
                    'food' => '0',
                    'last_update' => 1795478400,
                ],
            ],
        ]);

        $this->assertStringContainsString('Ressources des formes de vie', $html);
        $this->assertStringContainsString('Population', $html);
        $this->assertStringContainsString('Nourriture', $html);
        $this->assertStringContainsString('12 345 678 901 234 567 890', $html);
        $this->assertMatchesRegularExpression('/>\s*0\s*</', $html);
        $this->assertStringContainsString('Humains', $html);
        $this->assertStringNotContainsString('HUMANS', $html);
    }

    public function testDisplaysPopulationAndFoodHistoryControls(): void
    {
        $html = $this->render([], [
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
        ]);

        $this->assertStringContainsString('Historique population et nourriture', $html);
        $this->assertStringContainsString('Planet A', $html);
        $this->assertStringContainsString('7 jours', $html);
        $this->assertStringContainsString('30 jours', $html);
        $this->assertStringContainsString('90 jours', $html);
        $this->assertStringContainsString('2026-11-24', $html);
        $this->assertStringContainsString('12345678901234567890', $html);
        $this->assertStringContainsString('987654321', $html);
        $this->assertStringNotContainsString('astro_object_id', $html);
        $this->assertStringNotContainsString('value="101"', $html);
    }

    private function render(array $lifeforms, array $history = []): string
    {
        $lang = [
            'HOME_EMPIRE_LIFEFORM_BUILDINGS' => 'Bâtiments des formes de vie',
            'HOME_EMPIRE_LIFEFORM_RESEARCH' => 'Recherches des formes de vie',
            'HOME_EMPIRE_LIFEFORM_NO_DATA' => 'Aucune donnée de formes de vie reçue',
            'HOME_EMPIRE_LIFEFORM_RESOURCES' => 'Ressources des formes de vie',
            'HOME_EMPIRE_LIFEFORM_SPECIES' => 'Espèce',
            'HOME_EMPIRE_LIFEFORM_POPULATION' => 'Population',
            'HOME_EMPIRE_LIFEFORM_FOOD' => 'Nourriture',
            'HOME_EMPIRE_LIFEFORM_LAST_UPDATE' => 'Dernière observation',
            'HOME_EMPIRE_LIFEFORM_HISTORY' => 'Historique population et nourriture',
            'HOME_EMPIRE_LIFEFORM_HISTORY_PLANET' => 'Planète',
            'HOME_EMPIRE_LIFEFORM_HISTORY_PERIOD' => 'Période',
            'HOME_EMPIRE_LIFEFORM_HISTORY_DAYS' => '%d jours',
            'HOME_EMPIRE_LIFEFORM_HISTORY_NO_DATA' => 'Historique insuffisant',
            'HOME_EMPIRE_LIFEFORM_SPECIES_HUMANS' => 'Humains',
            'HOME_EMPIRE_LIFEFORM_SPECIES_ROCKTAL' => "Rock'tal",
            'HOME_EMPIRE_LIFEFORM_SPECIES_MECHAS' => 'Mécas',
            'HOME_EMPIRE_LIFEFORM_SPECIES_KAELESH' => 'Kaelesh',
            'HOME_EMPIRE_LIFEFORM_SPECIES_UNKNOWN' => 'Espèce inconnue',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_11101' => 'Secteur résidentiel',
        ];
        $player_lifeforms = $lifeforms;
        $player_lifeform_history = $history;
        $player_planets = [101 => ['name' => 'Planet A'], 102 => ['name' => 'Planet B']];
        $colspan_planets = 3;
        $colspan_planets_nine = 2;

        ob_start();
        require dirname(__DIR__, 2) . '/views/home_empire_lifeforms.php'; // NOSONAR - the partial must render for every test case
        return ob_get_clean();
    }
}
