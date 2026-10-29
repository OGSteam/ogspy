<?php

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

require_once __DIR__ . '/../../install/MigrationManager.php';

final class MigrationRollbackIntegrationTest extends TestCase
{
    private ?mysqli $connection = null;
    private ?string $tablePrefix = null;
    private ?MigrationManager $manager = null;

    protected function setUp(): void
    {
        $required = [
            'OGSPY_TEST_DB_HOST',
            'OGSPY_TEST_DB_NAME',
            'OGSPY_TEST_DB_USER',
            'OGSPY_TEST_DB_PASSWORD',
        ];
        foreach ($required as $name) {
            if (getenv($name) === false || getenv($name) === '') {
                self::markTestSkipped(
                    'Integration test requires dedicated OGSPY_TEST_DB_* settings; no database was contacted.'
                );
            }
        }

        $databaseName = getenv('OGSPY_TEST_DB_NAME');
        if (!preg_match('/^ogspy_test(?:_[a-z0-9]+)?$/i', $databaseName)) {
            self::fail('Refusing to run migrations outside a dedicated ogspy_test database.');
        }

        $port = getenv('OGSPY_TEST_DB_PORT');
        $this->connection = new mysqli(
            getenv('OGSPY_TEST_DB_HOST'),
            getenv('OGSPY_TEST_DB_USER'),
            getenv('OGSPY_TEST_DB_PASSWORD'),
            $databaseName,
            $port === false || $port === '' ? 3306 : (int) $port
        );
        $this->connection->set_charset('utf8mb4');

        $this->tablePrefix = 'ogspy_test_' . bin2hex(random_bytes(4)) . '_';
        $this->manager = new MigrationManager(
            new MigrationRollbackMysqliAdapter($this->connection),
            new NullLogger(),
            $this->tablePrefix
        );
    }

    protected function tearDown(): void
    {
        if ($this->connection === null || $this->tablePrefix === null) {
            return;
        }

        $statement = $this->connection->prepare(
            'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()'
        );
        $statement->execute();
        $tables = $statement->get_result();
        $tableNames = [];
        while ($row = $tables->fetch_assoc()) {
            if (str_starts_with($row['TABLE_NAME'], $this->tablePrefix)) {
                $tableNames[] = $row['TABLE_NAME'];
            }
        }

        $this->connection->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tableNames as $tableName) {
            $quotedTableName = '`' . str_replace('`', '``', $tableName) . '`';
            $this->connection->query("DROP TABLE IF EXISTS {$quotedTableName}");
        }
        $this->connection->query('SET FOREIGN_KEY_CHECKS = 1');
        $this->connection->close();
    }

    public function testUnsupportedRollbackPreservesAppliedSchemaAndHistory(): void
    {
        $available = $this->manager->getAvailableMigrations();
        $migration = $available['00000000000'];
        $this->assertTrue($this->manager->runMigration($migration));

        $userTable = $this->tablePrefix . 'user';
        $columnStatement = $this->connection->prepare(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $expectedColumn = 'name';
        $columnStatement->bind_param('ss', $userTable, $expectedColumn);
        $columnStatement->execute();
        $this->assertNotFalse($columnStatement->get_result()->fetch_assoc(), 'Migration should create the user.name column.');

        try {
            $this->manager->rollbackMigration('00000000000');
            $this->fail('Unsupported rollback must fail explicitly.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('not supported', $exception->getMessage());
        }

        $columnStatement->execute();
        $this->assertNotFalse($columnStatement->get_result()->fetch_assoc(), 'Failed rollback must leave the migrated schema applied.');

        $historyTable = $this->tablePrefix . 'migrations';
        $historyStatement = $this->connection->prepare(
            "SELECT migration, success FROM `{$historyTable}` WHERE migration = ?"
        );
        $version = '00000000000';
        $historyStatement->bind_param('s', $version);
        $historyStatement->execute();
        $history = $historyStatement->get_result()->fetch_assoc();
        $this->assertNotFalse($history, 'Failed rollback must preserve the migration history row.');
        $this->assertSame('1', (string) $history['success']);
    }
}

final class MigrationRollbackMysqliAdapter
{
    public function __construct(private mysqli $connection)
    {
    }

    public function sql_query(string $sql)
    {
        return $this->connection->query($sql);
    }

    public function sql_transaction(string $action): void
    {
        match ($action) {
            'begin' => $this->connection->begin_transaction(),
            'commit' => $this->connection->commit(),
            'rollback' => $this->connection->rollback(),
            default => throw new InvalidArgumentException("Unknown transaction action: {$action}"),
        };
    }

    public function sql_escape_string(string $value): string
    {
        return $this->connection->real_escape_string($value);
    }

    public function sql_fetch_assoc(mysqli_result $result): ?array
    {
        return $result->fetch_assoc();
    }
}
