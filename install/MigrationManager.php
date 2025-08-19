<?php

/**
 * Database Migration Manager
 * @package OGSpy
 * @subpackage install
 */

class MigrationManager {
    private $db;
    private $config;
    private $migrations_table;
    private $migrations_path;
    private $logger;
    private $table_prefix; // Ajout du préfixe comme propriété

    public function __construct($db, $logger = null, $table_prefix = 'ogspy_') {
        if (!$logger) {
            throw new InvalidArgumentException("Logger is required for MigrationManager");
        }

        $this->db = $db;
        $this->logger = $logger;
        $this->table_prefix = $table_prefix; // Stocker le préfixe
        $this->config = require __DIR__ . '/config/database_config.php';
        $this->migrations_table = $this->config['migrations_table'];
        $this->migrations_path = $this->config['migrations_path'];

        $this->initMigrationsTable();
    }

    /**
     * Initializes the migration tracking table
     */
    private function initMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->migrations_table}` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL UNIQUE,
            `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `execution_time` DECIMAL(10,3) NULL,
            `success` BOOLEAN DEFAULT TRUE,
            `error_message` TEXT NULL,
            INDEX `idx_migration` (`migration`),
            INDEX `idx_executed_at` (`executed_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->sql_query($sql);
    }

    /**
     * Gets the current database version
     */
    public function getCurrentDbVersion() {
        try {
            $sql = "SELECT MAX(migration) as version FROM {$this->migrations_table} WHERE success = TRUE";
            $result = $this->db->sql_query($sql);
            $row = $this->db->sql_fetch_assoc($result);
            // If no migration has been executed, return 'null' to allow execution of migration 0
            return $row['version'] ?? null;
        } catch (Exception $e) {
            // If the migrations table does not exist yet, we are at version 0
            return null;
        }
    }

    /**
     * Discovers and returns all available migrations
     */
    public function getAvailableMigrations() {
        $migrations = [];

        $migrationsPath = $this->migrations_path;

        $this->logger->info("Scanning for migrations in: " . $migrationsPath);

        // Check if the directory exists
        if (!is_dir($migrationsPath)) {
            $this->logger->error("Migrations directory does not exist: " . $migrationsPath);
            return [];
        }

        // Scan all PHP files in the migrations directory
        $files = glob($migrationsPath . '/*.php');

        if ($files === false) {
            $this->logger->error("glob() failed for path: " . $migrationsPath);
            return [];
        }

        if (empty($files)) {
            $this->logger->warning("No migration files found in: " . $migrationsPath);
            return $migrations;
        }

        foreach ($files as $file) {
            $filename = basename($file, '.php');

            // Extract version from filename - improved regex to accept different formats
            if (preg_match('/^(\d{8,11})_(.+)$/', $filename, $matches)) {
                $version = str_pad($matches[1], 11, '0', STR_PAD_LEFT); // Normalize to 11 digits
                $description = str_replace('_', ' ', $matches[2]);

                // Generate migration class name
                $className = 'Migration_' . $matches[1] . '_' . $this->toCamelCase($matches[2]);

                $migrations[$version] = [
                    'version' => $version,
                    'original_version' => $matches[1], // Keep original version for the class
                    'description' => $description,
                    'file' => $file,
                    'filename' => $filename,
                    'class_name' => $className
                ];

                $this->logger->info("Migration found: {$filename} -> class {$className}");
            } else {
                $this->logger->warning("Migration file ignored (invalid format): " . $filename);
            }
        }

        // Sort migrations by version
        ksort($migrations);

        $this->logger->info("Total migrations detected: " . count($migrations));

        return $migrations;
    }

    /**
     * Converts a string to CamelCase
     */
    private function toCamelCase($string) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * Returns the migrations to be executed
     */
    public function getPendingMigrations() {
        $currentVersion = $this->getCurrentDbVersion();
        $availableMigrations = $this->getAvailableMigrations();

        $pending = [];
        foreach ($availableMigrations as $version => $migration) {
            // Correction : gérer le cas où $currentVersion est null
            if ($currentVersion === null || strcmp($version, $currentVersion) > 0) {
                $pending[$version] = $migration;
            }
        }

        return $pending;
    }


    /**
     * Runs a specific migration
     */
    public function runMigration($migration) {
        $startTime = microtime(true);

        try {
            $this->logger->info("Running migration: {$migration['version']} - {$migration['description']}");

            // Start a transaction
            $this->db->sql_transaction('begin');

            // Load and execute the migration
            if (!file_exists($migration['file'])) {
                throw new Exception("Migration file not found: {$migration['file']}");
            }

            // Include the migration file (class)
            require_once $migration['file'];

            // Instantiate the migration class
            $className = $migration['class_name'];
            if (!class_exists($className)) {
                throw new Exception("Migration class '{$className}' not found");
            }

            $migrationInstance = new $className();

            // Check that the class implements the correct methods
            if (!method_exists($migrationInstance, 'up')) {
                throw new Exception("Method 'up' missing in migration {$migration['version']}");
            }

            // Get the SQL to execute
            $sqlContent = $migrationInstance->up();
            if (is_string($sqlContent)) {
                // If it's a string, assume it's raw SQL and prepare it
                $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
                // Correction : gérer le cas où $table_prefix est null
                $tablePrefix = $this->table_prefix;
                $sqlContent = str_replace('ogspy_', $tablePrefix, $sqlContent);
                $sqlContent = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $sqlContent);
                $queries = [$sqlContent];
            }

            $this->logger->info("Migration {$migration['version']} to execute: " . count($queries) . " queries");


            foreach ($queries as $query) {
                foreach (explode(';', $query) as $sql) {
                    $sql = trim($sql);
                    if ($sql) {
                        $this->db->sql_query($sql);
                    }
                }
            }

            $executionTime = microtime(true) - $startTime;

            // Record the migration as executed
            $this->recordMigration($migration['version'], $executionTime, true);

            // Commit the transaction
            $this->db->sql_transaction('commit');

            $this->logger->info("Migration {$migration['version']} successfully executed in " . round($executionTime, 3) . "s");

            return true;

        } catch (Exception $e) {
            // Rollback the transaction
            $this->db->sql_transaction('rollback');

            $executionTime = microtime(true) - $startTime;

            // Record the failure
            $this->recordMigration($migration['version'], $executionTime, false, $e->getMessage());

            $this->logger->error("ERROR migrating {$migration['version']}: " . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Runs all pending migrations
     */
    public function runPendingMigrations($interactive = true) {
        $pendingMigrations = $this->getPendingMigrations();

        if (empty($pendingMigrations)) {
            $this->logger->info("No pending migrations");
            return [];
        }

        $this->logger->info("Running " . count($pendingMigrations) . " migration(s)");

        $results = [];

        foreach ($pendingMigrations as $version => $migration) {
            try {
                if ($interactive) {
                    echo "Running migration {$version}... ";
                }

                $this->runMigration($migration);
                $results[$version] = ['success' => true];

                if ($interactive) {
                    echo "✅ OK\n";
                }

            } catch (Exception $e) {
                $results[$version] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'version' => $version
                ];

                if ($interactive) {
                    echo "❌ ERROR: " . $e->getMessage() . "\n";
                }

                // Continue even on error
                continue;
            }
        }

        return $results;
    }

    /**
     * Records the execution of a migration
     */
    private function recordMigration($version, $executionTime, $success, $errorMessage = null) {
        $sql = "INSERT INTO `{$this->migrations_table}`
                (`migration`, `executed_at`, `execution_time`, `success`, `error_message`)
                VALUES ('" . $this->db->sql_escape_string($version) . "', NOW(), " .
                round($executionTime, 3) . ", " . ($success ? '1' : '0') . ", " .
                ($errorMessage ? "'" . $this->db->sql_escape_string($errorMessage) . "'" : 'NULL') . ")
                ON DUPLICATE KEY UPDATE
                executed_at = NOW(),
                execution_time = " . round($executionTime, 3) . ",
                success = " . ($success ? '1' : '0') . ",
                error_message = " . ($errorMessage ? "'" . $this->db->sql_escape_string($errorMessage) . "'" : 'NULL');

        $this->db->sql_query($sql);
    }

    /**
     * Rolls back a migration
     */
    public function rollbackMigration($version) {
        $availableMigrations = $this->getAvailableMigrations();

        if (!isset($availableMigrations[$version])) {
            throw new Exception("Migration {$version} not found");
        }

        $migration = $availableMigrations[$version];

        try {
            $this->logger->info("Rolling back migration: {$migration['version']}");

            // Start a transaction
            $this->db->sql_transaction('begin');

            // Load and execute the rollback
            require_once $migration['file'];
            $className = $migration['class_name'];
            $migrationInstance = new $className();

            if (method_exists($migrationInstance, 'down')) {
                $migrationInstance->down($this->db);
            }

            // Delete the migration record
            $sql = "DELETE FROM `{$this->migrations_table}` WHERE migration = '" .
                   $this->db->sql_escape_string($version) . "'";
            $this->db->sql_query($sql);

            // Commit the transaction
            $this->db->sql_transaction('commit');

            $this->logger->info("Rollback {$migration['version']} completed");

            return true;

        } catch (Exception $e) {
            // Rollback the transaction
            $this->db->sql_transaction('rollback');
            $this->logger->error("ERROR rolling back {$migration['version']}: " . $e->getMessage());
            throw $e;
        }
    }
}
