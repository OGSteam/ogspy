<?php

// Monolog is provided via composer autoload (phpunit.xml bootstrap).

namespace {
    define('IN_SPYOGAME', true);

    require_once __DIR__ . '/../../core/abstract/Model_Abstract.php';
    require_once __DIR__ . '/../../model/Combat_Report_Model.php';

    use Ogsteam\Ogspy\Model\Combat_Report_Model;
    use PHPUnit\Framework\TestCase;

    class FakeCombatReportDb
    {
        public array $queries = [];
        private array $rows;

        public function __construct(array $rows = [])
        {
            $this->rows = $rows;
        }

        public function sql_query(string $query): string
        {
            $this->queries[] = $query;
            return 'result';
        }

        public function sql_fetch_assoc(string $result)
        {
            return array_shift($this->rows) ?: false;
        }
    }

    class CombatReportModelTest extends TestCase
    {
        protected function setUp(): void
        {
            if (!defined('TABLE_PARSEDRC')) {
                define('TABLE_PARSEDRC', 'ogspy_game_rc');
            }
            if (!defined('TABLE_USER_BUILDING')) {
                define('TABLE_USER_BUILDING', 'ogspy_game_astro_object');
            }
        }

        public function testEmpireReportOrderByUsesExpectedColumns(): void
        {
            $this->assertSame('rc.dateRC DESC', Combat_Report_Model::get_empire_report_order_by());
            $this->assertSame('astro.galaxy ASC, astro.system ASC, astro.row ASC', Combat_Report_Model::get_empire_report_order_by(1, 1));
            $this->assertSame('total_debris DESC, rc.dateRC DESC', Combat_Report_Model::get_empire_report_order_by(5, 0));
            $this->assertSame('rc.nb_rounds ASC, rc.dateRC DESC', Combat_Report_Model::get_empire_report_order_by(6, 1));
        }

        public function testEmpireReportFilterClausesIncludeRangesAndHideOneRound(): void
        {
            $clauses = Combat_Report_Model::get_empire_report_filter_clauses([
                'galaxy' => 2,
                'system' => 145,
                'row' => 9,
                'date_from' => 100,
                'date_to' => 200,
                'gains_min' => 3000,
                'gains_max' => 9000,
                'losses_min' => 4000,
                'losses_max' => 12000,
                'debris_min' => 5000,
                'debris_max' => 15000,
                'rounds_min' => 2,
                'rounds_max' => 6,
                'hide_one_round' => 1,
            ]);

            $this->assertContains('astro.galaxy = 2', $clauses);
            $this->assertContains('astro.system = 145', $clauses);
            $this->assertContains('astro.row = 9', $clauses);
            $this->assertContains('rc.dateRC >= 100', $clauses);
            $this->assertContains('rc.dateRC <= 200', $clauses);
            $this->assertContains('rc.gain_M >= 0 AND rc.gain_C >= 0 AND rc.gain_D >= 0 AND (rc.gain_M + rc.gain_C + rc.gain_D) >= 3000', $clauses);
            $this->assertContains('rc.gain_M >= 0 AND rc.gain_C >= 0 AND rc.gain_D >= 0 AND (rc.gain_M + rc.gain_C + rc.gain_D) <= 9000', $clauses);
            $this->assertContains('(rc.pertes_A + rc.pertes_D) >= 4000', $clauses);
            $this->assertContains('(rc.pertes_A + rc.pertes_D) <= 12000', $clauses);
            $this->assertContains('rc.debris_M >= 0 AND rc.debris_C >= 0 AND (rc.debris_M + rc.debris_C) >= 5000', $clauses);
            $this->assertContains('rc.debris_M >= 0 AND rc.debris_C >= 0 AND (rc.debris_M + rc.debris_C) <= 15000', $clauses);
            $this->assertContains('rc.nb_rounds >= 2', $clauses);
            $this->assertContains('rc.nb_rounds <= 6', $clauses);
            $this->assertContains('rc.nb_rounds > 1', $clauses);
        }

        public function testEmpireReportFilterClausesSkipEmptyValues(): void
        {
            $clauses = Combat_Report_Model::get_empire_report_filter_clauses([
                'galaxy' => 0,
                'gains_min' => '',
                'debris_max' => null,
                'hide_one_round' => 0,
            ]);

            $this->assertSame([], $clauses);
        }

        public function testGetEmpireCombatReportListBuildsQueryAndFormatsCoordinates(): void
        {
            global $db, $log;

            $db = new FakeCombatReportDb([[
                'id_rc' => '4',
                'galaxy' => '1',
                'system' => '22',
                'row' => '7',
                'dateRC' => '1710000000',
                'nb_rounds' => '3',
                'pertes_A' => '1000',
                'pertes_D' => '2000',
                'gain_M' => '300',
                'gain_C' => '400',
                'gain_D' => '500',
                'debris_M' => '600',
                'debris_C' => '700',
                'total_gain' => '1200',
                'total_losses' => '3000',
                'total_debris' => '1300',
            ]]);
            $log = new \Monolog\Logger('test');

            $model = new Combat_Report_Model();
            $reports = $model->get_empire_combat_report_list(42, 3, 1, [
                'galaxy' => 1,
                'hide_one_round' => 1,
            ]);

            $this->assertCount(1, $reports);
            $this->assertSame('1:22:7', $reports[0]['coordinates']);
            $this->assertStringContainsString('FROM ogspy_game_rc rc INNER JOIN ogspy_game_astro_object astro ON rc.astro_object_id = astro.id WHERE astro.player_id = 42', $db->queries[0]);
            $this->assertStringContainsString('AND astro.galaxy = 1', $db->queries[0]);
            $this->assertStringContainsString('AND rc.nb_rounds > 1', $db->queries[0]);
            $this->assertStringContainsString('ORDER BY total_gain ASC, rc.dateRC DESC', $db->queries[0]);
        }
    }
}
