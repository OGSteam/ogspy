<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/install/migrations/20261122001_AddLifeformData.php';

class LifeformMigrationTest extends TestCase
{
    public function testMigrationCreatesAndDropsBothLifeformTables(): void
    {
        $migration = new Migration_20261122001_AddLifeformData();

        $this->assertSame('20261122001', $migration->getVersion());
        $this->assertStringContainsString('game_astro_object_lifeform_building', $migration->up());
        $this->assertStringContainsString('game_astro_object_lifeform_research', $migration->up());
        $this->assertStringContainsString('DROP TABLE IF EXISTS `ogspy_game_astro_object_lifeform_building`', $migration->down());
        $this->assertStringContainsString('DROP TABLE IF EXISTS `ogspy_game_astro_object_lifeform_research`', $migration->down());
    }
}
