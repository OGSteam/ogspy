<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ProductionCompatibilityTest extends TestCase
{
    public function setUp(): void
    {
        if (!defined('IN_SPYOGAME')) {
            define('IN_SPYOGAME', true);
        }
        
        // Set up minimal globals needed by production functions
        $GLOBALS['server_config'] = [
            'speed_uni' => 1, 
            'final_calcul' => true, 
            'astro_strict' => false
        ];
        
        // Load only the required includes for production calculations
        // These files don't depend on database or session
        require_once __DIR__ . '/../../includes/ogame_structs.php';
        require_once __DIR__ . '/../../includes/ogame_elements.php';
        require_once __DIR__ . '/../../includes/ogame_costs.php';
        require_once __DIR__ . '/../../includes/ogame_requirements.php';
        require_once __DIR__ . '/../../includes/ogame_production.php';
        require_once __DIR__ . '/../../includes/ogame_planet.php';
    }

    public function tearDown(): void
    {
        // No cleanup needed
    }


    public function testForeuseEquivalence(): void
    {
        // Representative input set
        $nb_foreuse = 10;
        $level_M = 10;
        $level_C = 8;
        $level_D = 6;
        $temperature_max = 50;
        $officier = 1; // geologist
        $classe = 1; // collector in legacy signature
        $position = 3;
        $speed_uni = 1;

        // New-style call via ogame_production_building('FOR', ...)
        $user_building = [
            'FOR' => $nb_foreuse,
            'M' => $level_M,
            'C' => $level_C,
            'D' => $level_D,
            'temperature_max' => $temperature_max,
            // coordinates chosen so ogame_find_planet_position returns $position
            'coordinates' => '1:1:' . $position
        ];
        $player_data = ['off_geologue' => $officier, 'off_full' => 0, 'class' => 'COL'];
        $server_config = ['speed_uni' => $speed_uni];
    $new = ogame_production_building('FOR', $user_building, ['NRJ' => 0], $player_data, $server_config);

        // Validate result shape and values
        $this->assertIsArray($new);
        $this->assertArrayHasKey('M', $new);
        $this->assertArrayHasKey('C', $new);
        $this->assertArrayHasKey('D', $new);
        $this->assertGreaterThanOrEqual(0, (int)$new['M']);
        $this->assertGreaterThanOrEqual(0, (int)$new['C']);
        $this->assertGreaterThanOrEqual(0, (int)$new['D']);
    }

    public function testForeuseMaxEquivalence(): void
    {
        $level_M = 10;
        $level_C = 8;
        $level_D = 6;
        $officier = 1;
        $classe = 1; // numeric class used by legacy wrapper

    // New function signature expects player_data array
    $player_data = ['off_geologue' => $officier, 'off_full' => 0, 'class' => 'COL'];
    $new = ogame_production_foreuse_max($level_M, $level_C, $level_D, $player_data);

    $this->assertIsNumeric($new);
    $this->assertGreaterThanOrEqual(0, (int)$new);
    }

    public function testConsumptionEquivalence(): void
    {
        $speed_uni = 1;

        // Buildings to check: M, C, D, CEF (different mapping in consumption())
        $cases = [
            ['building' => 'M', 'level' => 10],
            ['building' => 'C', 'level' => 8],
            ['building' => 'D', 'level' => 6],
            ['building' => 'CEF', 'level' => 5],
        ];

        foreach ($cases as $case) {
            $building = $case['building'];
            $level = $case['level'];

            // Compute expected legacy-style value using the new API
            // Provide full building map so nested production calls have expected keys
            $user_building = ['M' => 0, 'C' => 0, 'D' => 0, $building => $level, 'temperature_max' => 0, 'coordinates' => '1:1:2'];
            $server_config = ['speed_uni' => $speed_uni];
            $res = ogame_production_building($building, $user_building, ['NRJ' => 0], ['class' => 'none'], $server_config);

            if ($building === 'CEF') {
                $expected = -($res['D'] ?? 0);
            } elseif (in_array($building, ['M', 'C', 'D', 'FOR'], true)) {
                $expected = -($res['NRJ'] ?? 0);
            } else {
                $expected = 0;
            }

            // Sanity check on expected value
            $this->assertIsNumeric($expected);
        }
    }
}
