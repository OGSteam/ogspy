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
            101 => [
                'buildings' => [11101 => 12, 12101 => 8],
                'research' => [11201 => 4, 13201 => 5],
            ],
            102 => [
                'buildings' => [11101 => 7, 13101 => 3, 14101 => 2],
                'research' => [12201 => 3, 14201 => 6],
            ],
        ]);

        $this->assertStringContainsString('Bâtiments des formes de vie', $html);
        $this->assertStringContainsString('Recherches des formes de vie', $html);
        $this->assertStringContainsString('Humains', $html);
        $this->assertStringContainsString('Rock&#039;tal', $html);
        $this->assertStringContainsString('Secteur résidentiel', $html);
        $this->assertStringContainsString('Enclave stoïque', $html);
        $this->assertStringContainsString('Chaîne de production', $html);
        $this->assertStringContainsString('Refugium', $html);
        $this->assertStringContainsString('Ambassadeurs intergalactiques', $html);
        $this->assertStringContainsString('Batteries volcaniques', $html);
        $this->assertStringContainsString('Technique de catalyse', $html);
        $this->assertStringContainsString('Récupération de chaleur', $html);
        $this->assertStringNotContainsString('11101', $html);
        $this->assertStringNotContainsString('14201', $html);
        $this->assertMatchesRegularExpression('/>\s*12\s*</', $html);
        $this->assertMatchesRegularExpression('/>\s*7\s*</', $html);

        $this->assertLessThan(strpos($html, 'Rock&#039;tal'), strpos($html, 'Humains'));
        $this->assertLessThan(strpos($html, 'Mécas'), strpos($html, 'Rock&#039;tal'));
        $this->assertLessThan(strpos($html, 'Kaelesh'), strpos($html, 'Mécas'));
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

    private function render(array $lifeforms): string
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
            'HOME_EMPIRE_LIFEFORM_SPECIES_HUMANS' => 'Humains',
            'HOME_EMPIRE_LIFEFORM_SPECIES_ROCKTAL' => "Rock'tal",
            'HOME_EMPIRE_LIFEFORM_SPECIES_MECHAS' => 'Mécas',
            'HOME_EMPIRE_LIFEFORM_SPECIES_KAELESH' => 'Kaelesh',
            'HOME_EMPIRE_LIFEFORM_SPECIES_UNKNOWN' => 'Espèce inconnue',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_11101' => 'Secteur résidentiel',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_11201' => 'Ambassadeurs intergalactiques',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_12101' => 'Enclave stoïque',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_12201' => 'Batteries volcaniques',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_13101' => 'Chaîne de production',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_13201' => 'Technique de catalyse',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_14101' => 'Refugium',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_14201' => 'Récupération de chaleur',
        ];
        $player_lifeforms = $lifeforms;
        $player_planets = [101 => ['name' => 'Planet A'], 102 => ['name' => 'Planet B']];
        $colspan_planets = 3;
        $colspan_planets_nine = 2;

        ob_start();
        require dirname(__DIR__, 2) . '/views/home_empire_lifeforms.php'; // NOSONAR - the partial must render for every test case
        return ob_get_clean();
    }
}
