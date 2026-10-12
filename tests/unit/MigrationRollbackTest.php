<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

require_once __DIR__ . '/../../install/MigrationManager.php';

class MigrationRollbackTest extends TestCase
{
    private function fakeDb(): object
    {
        return new class {
            public array $queries = [];
            public array $transactions = [];

            public function sql_query($sql)
            {
                $this->queries[] = $sql;
                return true;
            }

            public function sql_escape_string($s)
            {
                return addslashes($s);
            }

            public function sql_transaction($action)
            {
                $this->transactions[] = $action;
            }
        };
    }

    #[DataProvider('shippedMigrations')]
    public function testRollbackOfUnsupportedMigrationFailsAndKeepsHistory(string $version): void
    {
        $db = $this->fakeDb();
        $manager = new MigrationManager($db, new NullLogger(), 'ogspy_');

        try {
            $manager->rollbackMigration($version);
            $this->fail('Rollback must fail explicitly');
        } catch (Exception $e) {
            $this->assertStringContainsString('not supported', $e->getMessage());
        }

        foreach ($db->queries as $sql) {
            $this->assertStringNotContainsString('DELETE FROM', $sql);
        }
        $this->assertSame(['begin', 'rollback'], $db->transactions);
    }

    public static function shippedMigrations(): array
    {
        return [['00000000000'], ['20251227001']];
    }
}
