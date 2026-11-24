<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/install/migrations/20261124002_AddAnomalyScanner.php';

class AnomalyScannerMigrationTest extends TestCase
{
    public function testMigrationAddsAndRemovesScannerColumn(): void
    {
        $migration = new Migration_20261124002_AddAnomalyScanner();

        $this->assertSame('20261124002', $migration->getVersion());
        $this->assertSame('Add Interstellar Anomaly Scanner level', $migration->getDescription());
        $this->assertTrue($migration->canRollback());
        $this->assertStringContainsString('ADD COLUMN `Scanner` SMALLINT', $migration->up());
        $this->assertStringContainsString('DROP COLUMN `Scanner`', $migration->down());
    }
}
