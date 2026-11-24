<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/install/migrations/20261124001_AddLifeformResources.php';

class LifeformResourceMigrationTest extends TestCase
{
    public function testMigrationCreatesCurrentAndDailyHistoryTables(): void
    {
        $migration = new Migration_20261124001_AddLifeformResources();

        $this->assertSame('20261124001', $migration->getVersion());
        $this->assertSame('Add life-form population and food storage', $migration->getDescription());
        $this->assertTrue($migration->canRollback());
        $this->assertStringContainsString('game_lifeform_resource`', $migration->up());
        $this->assertStringContainsString('game_lifeform_resource_history`', $migration->up());
        $this->assertStringContainsString('PRIMARY KEY (`astro_object_id`, `observation_day`)', $migration->up());
        $this->assertStringContainsString('DROP TABLE IF EXISTS `ogspy_game_lifeform_resource_history`', $migration->down());
        $this->assertStringContainsString('DROP TABLE IF EXISTS `ogspy_game_lifeform_resource`', $migration->down());
    }
}
