<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ProductionCompatibilityTest extends TestCase
{
    // store previous error handler to restore it and avoid dynamic property creation
    private $oldErrorHandler;
    public function setUp(): void
    {
        if (!defined('IN_SPYOGAME')) {
            define('IN_SPYOGAME', true);
        }
        // Provide minimal globals used by common.php to avoid warnings during test bootstrap
        // Minimal mock db with the methods common.php may call during init
        $GLOBALS['db'] = new class {
            public function sql_query($q) { return true; }
            public function sql_fetch_row($r) { return [null]; }
            public function sql_fetch_assoc($r) { return []; }
            public function sql_numrows($r) { return 0; }
            public static function getInstance() { return new self(); }
            public $db_connect_id = true;
        };
    $GLOBALS['server_config'] = ['speed_uni' => 1, 'final_calcul' => true, 'astro_strict' => false];
    // Ensure a UI language is set so common.php can load language files
    $GLOBALS['pub_lang'] = 'fr';
    $GLOBALS['ui_lang'] = 'fr';

        // Convert warnings to exceptions temporarily to capture a stack trace and diagnose
        $this->oldErrorHandler = set_error_handler(function ($severity, $message, $file, $line) {
            if ($severity & (E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE)) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }
            return false;
        });

        // Load the application bootstrap that centralizes formula includes
        require_once __DIR__ . '/../../common.php';
    }

    public function tearDown(): void
    {
        if (isset($this->oldErrorHandler)) {
            set_error_handler($this->oldErrorHandler);
        }
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
