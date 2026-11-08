<?php

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

            public function sql_fetch_assoc($result)
            {
                return ['id' => 1, 'migration' => '99999999999', 'success' => 1];
            }

            public function sql_transaction($action)
            {
                $this->transactions[] = $action;
            }
        };
    }

    public function testRollbackOfMigrationWithoutDownMethodFailsAndKeepsHistory(): void
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'ogspy_nodown_' . uniqid();
        mkdir($dir);
        file_put_contents($dir . '/99999999999_NoDown.php', <<<'PHP'
<?php
class Migration_99999999999_NoDown
{
    public function __construct($db = null, $logger = null, $prefix = '') {}
    public function getVersion() { return '99999999999'; }
    public function getDescription() { return 'No down'; }
    public function up() {}
}
PHP);

        try {
            $db = $this->fakeDb();
            $manager = new MigrationManager($db, new NullLogger(), 'ogspy_');
            $prop = new ReflectionProperty(MigrationManager::class, 'migrations_path');
            $prop->setAccessible(true);
            $prop->setValue($manager, $dir);

            try {
                $manager->rollbackMigration('99999999999');
                $this->fail('Rollback must fail explicitly');
            } catch (Exception $e) {
                $this->assertStringContainsString("Method 'down' missing", $e->getMessage());
            }

            foreach ($db->queries as $sql) {
                $this->assertStringNotContainsString('DELETE FROM', $sql);
            }
            $this->assertSame([], $db->transactions);
        } finally {
            array_map('unlink', glob($dir . '/*'));
            rmdir($dir);
        }
    }
}
