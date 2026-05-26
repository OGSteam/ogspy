<?php

namespace Monolog {
    class Logger
    {
    }
}

namespace {
    define('IN_SPYOGAME', true);

    require_once __DIR__ . '/../../core/abstract/Model_Abstract.php';
    require_once __DIR__ . '/../../model/Combat_Report_Model.php';

    use Ogsteam\Ogspy\Model\Combat_Report_Model;
    use PHPUnit\Framework\TestCase;

    class CombatReportModelTest extends TestCase
    {
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
    }
}
