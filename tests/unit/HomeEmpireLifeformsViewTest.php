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

        $this->assertSame(2, substr_count($html, 'Aucune donnée de formes de vie reçue'));
    }

    private function render(array $lifeforms): string
    {
        $lang = [
            'HOME_EMPIRE_LIFEFORM_BUILDINGS' => 'Bâtiments des formes de vie',
            'HOME_EMPIRE_LIFEFORM_RESEARCH' => 'Recherches des formes de vie',
            'HOME_EMPIRE_LIFEFORM_NO_DATA' => 'Aucune donnée de formes de vie reçue',
            'HOME_EMPIRE_LIFEFORM_SPECIES_HUMANS' => 'Humains',
            'HOME_EMPIRE_LIFEFORM_SPECIES_ROCKTAL' => "Rock'tal",
            'HOME_EMPIRE_LIFEFORM_SPECIES_MECHAS' => 'Mécas',
            'HOME_EMPIRE_LIFEFORM_SPECIES_KAELESH' => 'Kaelesh',
            'HOME_EMPIRE_LIFEFORM_SPECIES_UNKNOWN' => 'Espèce inconnue',
            'HOME_EMPIRE_LIFEFORM_TECHNOLOGY_11101' => 'Secteur résidentiel',
        ];
        $player_lifeforms = $lifeforms;
        $player_planets = [101 => ['name' => 'Planet A'], 102 => ['name' => 'Planet B']];
        $colspan_planets = 3;
        $colspan_planets_nine = 2;

        ob_start();
        require dirname(__DIR__, 2) . '/views/home_empire_lifeforms.php';
        return ob_get_clean();
    }
}
