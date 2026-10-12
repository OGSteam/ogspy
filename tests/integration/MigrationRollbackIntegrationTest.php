<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class MigrationTestLogger
{
    public function emergency($m, array $c = []): void {}
    public function alert($m, array $c = []): void {}
    public function critical($m, array $c = []): void {}
    public function error($m, array $c = []): void {}
    public function warning($m, array $c = []): void {}
    public function notice($m, array $c = []): void {}
    public function info($m, array $c = []): void {}
    public function debug($m, array $c = []): void {}
    public function log($l, $m, array $c = []): void {}
}

final class MigrationRollbackIntegrationTest extends TestCase
{
    private static string $dbName;
    private static ?sql_db $db = null;
    private static ?MigrationManager $migrationManager = null;
    private static MigrationTestLogger $logger;

    public static function setUpBeforeClass(): void
    {
        if (!defined('IN_SPYOGAME')) {
            define('IN_SPYOGAME', true);
        }

        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        require_once dirname(__DIR__, 2) . '/includes/mysql.php';
        require_once dirname(__DIR__, 2) . '/install/MigrationManager.php';

        self::$dbName = 'ogspy_test_disposable_' . bin2hex(random_bytes(4));

        try {
            $mysqli = @new mysqli('127.0.0.1', 'root', '', '');
            if ($mysqli->connect_errno) {
                return;
            }
            $mysqli->query("DROP DATABASE IF EXISTS `" . self::$dbName . "`");
            $mysqli->query("CREATE DATABASE `" . self::$dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $mysqli->close();
        } catch (Throwable $e) {
            return;
        }

        self::$logger = new MigrationTestLogger();
        $GLOBALS['logSQL'] = self::$logger;
        $GLOBALS['logSlowSQL'] = self::$logger;

        self::$db = sql_db::getInstance('127.0.0.1', 'root', '', self::$dbName);
        self::$migrationManager = new MigrationManager(self::$db, self::$logger, 'ogspy_');
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$db !== null) {
            self::$db->sql_close();
            self::$db = null;
        }

        if (isset(self::$dbName)) {
            try {
                $mysqli = @new mysqli('127.0.0.1', 'root', '', '');
                if (!$mysqli->connect_errno) {
                    $mysqli->query("DROP DATABASE IF EXISTS `" . self::$dbName . "`");
                    $mysqli->close();
                }
            } catch (Throwable $e) {
                // Ignore cleanup errors
            }
        }
    }

    protected function setUp(): void
    {
        if (self::$db === null || self::$migrationManager === null) {
            $this->markTestSkipped('Database connection to MariaDB (127.0.0.1:root) is not available.');
        }
    }

    private function getColumnType(string $table, string $column): ?string
    {
        $res = self::$db->sql_query("SHOW COLUMNS FROM `{$table}` WHERE Field = '" . self::$db->sql_escape_string($column) . "'");
        $row = self::$db->sql_fetch_assoc($res);
        return $row ? strtolower($row['Type']) : null;
    }

    private function getIndexColumns(string $table, string $indexName): array
    {
        $cols = [];
        $res = self::$db->sql_query("SHOW INDEX FROM `{$table}` WHERE Key_name = '" . self::$db->sql_escape_string($indexName) . "'");
        while ($row = self::$db->sql_fetch_assoc($res)) {
            $cols[(int)$row['Seq_in_index']] = $row['Column_name'];
        }
        ksort($cols);
        return array_values($cols);
    }

    private function isMigrationRecorded(string $version): bool
    {
        $escaped = self::$db->sql_escape_string($version);
        $res = self::$db->sql_query("SELECT id, success FROM `ogspy_migrations` WHERE migration = '{$escaped}' AND success = 1");
        $row = self::$db->sql_fetch_assoc($res);
        return $row !== false && $row !== null;
    }

    public function testApplyMigrationsAndRollbackReversibleMigration(): void
    {
        $available = self::$migrationManager->getAvailableMigrations();
        $this->assertArrayHasKey('00000000000', $available, 'Initial migration must be available');
        $this->assertArrayHasKey('20251227001', $available, '4.0.3 migration must be available');

        // 1. Apply initial migration
        $initialApplied = self::$migrationManager->runMigration($available['00000000000']);
        $this->assertTrue($initialApplied, 'Initial migration must run successfully');
        $this->assertTrue($this->isMigrationRecorded('00000000000'), 'Initial migration must be recorded in ogspy_migrations');

        // Prepare pre-4.0.3 state (VARCHAR(255) and legacy indexes) to test migration upgrade + rollback
        self::$db->sql_query("ALTER TABLE `ogspy_config` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL");
        self::$db->sql_query("ALTER TABLE `ogspy_statistics` CHANGE `statistic_name` `statistic_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL");
        self::$db->sql_query("ALTER TABLE `ogspy_mod_config` CHANGE `config` `config` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL");
        self::$db->sql_query("ALTER TABLE `ogspy_mod_user_config` CHANGE `config` `config` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL");
        self::$db->sql_query("ALTER TABLE `ogspy_game_rank_ally_points` DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `ally`)");
        self::$db->sql_query("ALTER TABLE `ogspy_game_rank_player_points` DROP INDEX IF EXISTS `datadate`, ADD INDEX `datadate` (`datadate`, `player`)");

        // Verify pre-4.0.3 schema state
        $this->assertEquals('varchar(255)', $this->getColumnType('ogspy_config', 'name'));
        $this->assertEquals(['datadate', 'ally'], $this->getIndexColumns('ogspy_game_rank_ally_points', 'datadate'));
        $this->assertEquals(['datadate', 'player'], $this->getIndexColumns('ogspy_game_rank_player_points', 'datadate'));

        // 2. Apply 4.0.3 migration
        $v403Applied = self::$migrationManager->runMigration($available['20251227001']);
        $this->assertTrue($v403Applied, '4.0.3 migration must run successfully');
        $this->assertTrue($this->isMigrationRecorded('20251227001'), '4.0.3 migration must be recorded in ogspy_migrations');

        // Verify 4.0.3 schema state
        $this->assertEquals('varchar(190)', $this->getColumnType('ogspy_config', 'name'));
        $this->assertEquals('varchar(190)', $this->getColumnType('ogspy_statistics', 'statistic_name'));
        $this->assertEquals('varchar(190)', $this->getColumnType('ogspy_mod_config', 'config'));
        $this->assertEquals('varchar(190)', $this->getColumnType('ogspy_mod_user_config', 'config'));
        $this->assertEquals(['datadate', 'ally_id'], $this->getIndexColumns('ogspy_game_rank_ally_points', 'datadate'));
        $this->assertEquals(['datadate', 'player_id'], $this->getIndexColumns('ogspy_game_rank_player_points', 'datadate'));

        // 3. Rollback 4.0.3 migration
        $rollbackSuccess = self::$migrationManager->rollbackMigration('20251227001');
        $this->assertTrue($rollbackSuccess, 'rollbackMigration must return true on successful rollback');

        // Verify schema is reversed to pre-4.0.3 state
        $this->assertEquals('varchar(255)', $this->getColumnType('ogspy_config', 'name'), 'ogspy_config.name must revert to varchar(255)');
        $this->assertEquals('varchar(255)', $this->getColumnType('ogspy_statistics', 'statistic_name'), 'ogspy_statistics.statistic_name must revert to varchar(255)');
        $this->assertEquals('varchar(255)', $this->getColumnType('ogspy_mod_config', 'config'), 'ogspy_mod_config.config must revert to varchar(255)');
        $this->assertEquals('varchar(255)', $this->getColumnType('ogspy_mod_user_config', 'config'), 'ogspy_mod_user_config.config must revert to varchar(255)');
        $this->assertEquals(['datadate', 'ally'], $this->getIndexColumns('ogspy_game_rank_ally_points', 'datadate'), 'ogspy_game_rank_ally_points datadate index must revert to (datadate, ally)');
        $this->assertEquals(['datadate', 'player'], $this->getIndexColumns('ogspy_game_rank_player_points', 'datadate'), 'ogspy_game_rank_player_points datadate index must revert to (datadate, player)');

        // Verify migration history: 20251227001 is removed, 00000000000 remains
        $this->assertFalse($this->isMigrationRecorded('20251227001'), '20251227001 must be removed from ogspy_migrations after rollback');
        $this->assertTrue($this->isMigrationRecorded('00000000000'), '00000000000 must remain recorded in ogspy_migrations');
    }

    public function testRollbackUnsupportedMigrationFailsAndRetainsHistory(): void
    {
        $this->assertTrue($this->isMigrationRecorded('00000000000'), 'Initial migration should be recorded');

        $exceptionThrown = false;
        try {
            self::$migrationManager->rollbackMigration('00000000000');
        } catch (Exception $e) {
            $exceptionThrown = true;
            $this->assertStringContainsString('Rollback is not supported for migration 00000000000', $e->getMessage());
        }

        $this->assertTrue($exceptionThrown, 'rollbackMigration must throw when migration does not support rollback');

        // History record must be retained
        $this->assertTrue($this->isMigrationRecorded('00000000000'), '00000000000 must remain recorded in ogspy_migrations after failed rollback');

        // Schema must remain intact
        $this->assertEquals('varchar(255)', $this->getColumnType('ogspy_config', 'name'));
    }

    public function testRollbackUnappliedMigrationFails(): void
    {
        // 20251227001 was rolled back earlier and is not recorded
        $this->assertFalse($this->isMigrationRecorded('20251227001'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('is not recorded as successfully applied');
        self::$migrationManager->rollbackMigration('20251227001');
    }

    public function testRollbackFailureRetainsHistoryRecord(): void
    {
        // Insert a simulated migration into history
        self::$db->sql_query("INSERT INTO `ogspy_migrations` (migration, success) VALUES ('99999999001', 1)");
        $this->assertTrue($this->isMigrationRecorded('99999999001'));

        // Create a temporary migration file that claims canRollback() = true but throws in down()
        $tempMigrationFile = dirname(__DIR__, 2) . '/install/migrations/99999999001_rollback_fail_test.php';
        $code = '<?php
class Migration_99999999001_RollbackFailTest {
    public function getVersion(): string { return "99999999001"; }
    public function getDescription(): string { return "Rollback failure test"; }
    public function up(): string { return ""; }
    public function canRollback(): bool { return true; }
    public function down($db = null) {
        throw new RuntimeException("Simulated error inside down()");
    }
}';
        file_put_contents($tempMigrationFile, $code);

        try {
            $exceptionThrown = false;
            try {
                self::$migrationManager->rollbackMigration('99999999001');
            } catch (Exception $e) {
                $exceptionThrown = true;
                $this->assertStringContainsString('Simulated error inside down()', $e->getMessage());
            }

            $this->assertTrue($exceptionThrown, 'rollbackMigration must throw when down() fails');

            // History record MUST be retained
            $this->assertTrue($this->isMigrationRecorded('99999999001'), 'History record must be retained when rollback fails');
        } finally {
            if (file_exists($tempMigrationFile)) {
                unlink($tempMigrationFile);
            }
            self::$db->sql_query("DELETE FROM `ogspy_migrations` WHERE migration = '99999999001'");
        }
    }
}
