<?php

/**
 * Test Manager - Tests automatisés pour installation et mises à niveau
 * @package OGSpy
 * @subpackage install
 */

class TestManager {
    private $db;
    private $logger;
    private $testDbName;
    private $originalDbName;
    private $backupPath;
    private $createdTestMigrations = []; // Nouveau: suivi des migrations de test créées

    public function __construct($db, $logger) {
        $this->db = $db;
        $this->logger = $logger;
        $this->testDbName = 'ogspy_test_' . uniqid();
        $this->backupPath = dirname(__DIR__) . '/tests/backup/';

        // Créer le dossier de backup si nécessaire
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    /**
     * Lance tous les tests (installation + mises à niveau)
     */
    public function runAllTests() {
        $results = [
            'install_test' => null,
            'upgrade_test' => null,
            'table_prefix_test' => null,
            'success' => false,
            'errors' => []
        ];

        try {
            echo "🧪 DÉBUT DES TESTS AUTOMATISÉS\n";
            echo "================================\n\n";

            // Créer les migrations de test avant les tests
            $this->createTestMigrations();

            // Test 1: Installation initiale
            echo "📦 Test d'installation initiale...\n";
            $results['install_test'] = $this->testFreshInstall();

            if ($results['install_test']['success']) {
                echo "✓ Test d'installation réussi\n\n";

                // Test 2: Mise à niveau
                echo "⬆️  Test de mise à niveau...\n";
                $results['upgrade_test'] = $this->testUpgrade();

                if ($results['upgrade_test']['success']) {
                    echo "✓ Test de mise à niveau réussi\n\n";

                    // Test 3: Configuration des préfixes de table
                    echo "🏷️  Test de configuration des préfixes de table...\n";
                    $results['table_prefix_test'] = $this->testTablePrefix();

                    if ($results['table_prefix_test']['success']) {
                        echo "✓ Test de préfixe de table réussi\n\n";
                        $results['success'] = true;
                    } else {
                        echo "✗ Test de préfixe de table échoué\n\n";
                        $results['errors'][] = $results['table_prefix_test']['error'];
                    }
                } else {
                    echo "✗ Test de mise à niveau échoué\n\n";
                    $results['errors'][] = $results['upgrade_test']['error'];
                }
            } else {
                echo "✗ Test d'installation échoué\n\n";
                $results['errors'][] = $results['install_test']['error'];
            }

        } catch (Exception $e) {
            $results['errors'][] = "Erreur critique: " . $e->getMessage();
            $this->logger->error("Test crítico fallado", ['error' => $e->getMessage()]);
        } finally {
            // Nettoyage
            $this->cleanup();
        }

        return $results;
    }

    /**
     * Test d'installation initiale sur une base vierge
     */
    public function testFreshInstall() {
        $result = ['success' => false, 'error' => null, 'details' => []];
        global $table_prefix;

        try {
            // 1. Créer une base de test vierge
            $this->createTestDatabase();

            // 2. Basculer vers la base de test AVANT de créer le MigrationManager
            $this->switchToTestDatabase();

            // 3. Créer le MigrationManager maintenant que nous sommes sur la bonne base
            $migrationManager = new MigrationManager($this->db, $this->logger,$table_prefix);

            // 4. Vérifier qu'aucune table OGSpy n'existe (sauf migrations qui vient d'être créée)
            $existingTables = $this->getOGSpyTables();
            $filteredTables = array_filter($existingTables, function($table) {
               // Exclure toutes les tables de migrations légitimes : ogspy_migrations et custom_migrations
                return !in_array($table, ['ogspy_migrations', 'custom_migrations']);
            });

            if (!empty($filteredTables)) {
                throw new Exception("La base de test n'est pas vierge: " . implode(', ', $filteredTables));
            }

            // 5. Exécuter toutes les migrations depuis le début
            $allMigrations = $migrationManager->getAvailableMigrations();
            $result['details']['total_migrations'] = count($allMigrations);

            foreach ($allMigrations as $migration) {
                echo "  Exécution migration: {$migration['version']}\n";
                $migrationResult = $migrationManager->runMigration($migration);

                if (!$migrationResult) {
                    throw new Exception("Migration {$migration['version']} échouée");
                }

                $result['details']['executed_migrations'][] = $migration['version'];
            }

            // 6. Vérifier l'intégrité de l'installation
            $this->verifyInstallIntegrity($table_prefix);

            // 7. Vérifier que la version DB correspond
            $dbVersion = $migrationManager->getCurrentDbVersion();
            $expectedVersion = $this->getLatestMigrationVersion();

            if ($dbVersion !== $expectedVersion) {
                throw new Exception("Version DB incorrecte: attendue {$expectedVersion}, trouvée {$dbVersion}");
            }

            $result['details']['final_version'] = $dbVersion;
            $result['success'] = true;

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            $this->logger->error("Test d'installation échoué", ['error' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Test de mise à niveau depuis une version antérieure
     */
    public function testUpgrade() {
        $result = ['success' => false, 'error' => null, 'details' => []];

        try {
            // 1. Créer une nouvelle base de test pour ce test spécifique
            $this->testDbName = 'ogspy_test_upgrade_' . uniqid();
            $this->createTestDatabase();
            $this->switchToTestDatabase();

            // 2. Créer un état "ancien" en n'exécutant que les premières migrations
            $this->createOlderVersionState();

            // 3. Simuler une mise à niveau avec le bon préfixe de table
            global $table_prefix;
            $tablePrefix = $table_prefix ?? 'ogspy_';

            $autoUpgrade = new AutoUpgradeManager($this->db, $this->logger, $tablePrefix, '9.9.9');
            $migrationManager = new MigrationManager($this->db, $this->logger, $tablePrefix);

            $initialVersion = $migrationManager->getCurrentDbVersion();
            echo "  Version initiale: {$initialVersion}\n";

            // Vérifier l'état de la version applicative avant upgrade
            $this->logApplicationVersionState("BEFORE UPGRADE");

            // 4. Exécuter la mise à niveau automatique
            $upgradeResult = $autoUpgrade->checkAndUpgrade();

            if ($upgradeResult['status'] !== 'success' && $upgradeResult['status'] !== 'up_to_date') {
                throw new Exception("Mise à niveau échouée: " . ($upgradeResult['message'] ?? 'Erreur inconnue'));
            }

            // 5. Vérifier que toutes les migrations ont été appliquées
            $finalVersion = (new MigrationManager($this->db, $this->logger, $tablePrefix))->getCurrentDbVersion();
            $expectedVersion = $this->getLatestMigrationVersion();

            if ($finalVersion !== $expectedVersion) {
                throw new Exception("Version finale incorrecte: attendue {$expectedVersion}, trouvée {$finalVersion}");
            }

            // 6. Vérifier l'état de la version applicative après upgrade
            $this->logApplicationVersionState("AFTER UPGRADE");

            // 7. Vérifier que la version applicative a été mise à jour dans la DB
            $appVersionInDb = $this->getConfigValue('version');
            if ($appVersionInDb !== '9.9.9') {
                throw new Exception("Version applicative incorrecte après upgrade: attendue 9.9.9, trouvée {$appVersionInDb}");
            }
            echo "  ✓ Application version verified in database: {$appVersionInDb}\n";

            // 8. Vérifier l'intégrité après mise à niveau
            $this->verifyInstallIntegrity($tablePrefix);

            $result['details']['final_version'] = $finalVersion;
            $result['details']['upgrade_result'] = $upgradeResult;
            $result['success'] = true;

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            $this->logger->error("Test de mise à niveau échoué", ['error' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Crée une base de données de test vierge
     */
    private function createTestDatabase() {
        $this->originalDbName = $this->db->getDatabaseName();

        // Créer la base de test
        $this->db->sql_query("CREATE DATABASE IF NOT EXISTS `{$this->testDbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        echo "  Base de test créée: {$this->testDbName}\n";
    }

    /**
     * Bascule vers la base de données de test
     */
    private function switchToTestDatabase() {
        $this->db->sql_select_db($this->testDbName);
        echo "  Basculé vers la base de test\n";
    }

    /**
     * Retourne à la base de données originale
     */
    private function switchToOriginalDatabase() {
        if ($this->originalDbName) {
            $this->db->sql_select_db($this->originalDbName);
        }
    }

    /**
     * Récupère toutes les tables OGSpy existantes
     */
    private function getOGSpyTables() {
        $tables = [];
        $result = $this->db->sql_query("SHOW TABLES");

        while ($row = $this->db->sql_fetch_row($result)) {
            $tables[] = $row[0];
        }

        return $tables;
    }

    /**
     * Crée un état "ancien" pour tester les mises à niveau
     */
    private function createOlderVersionState() {
        global $table_prefix;
        $tablePrefix = $table_prefix ?? 'ogspy_';
        $migrationManager = new MigrationManager($this->db, $this->logger, $tablePrefix);
        $allMigrations = $migrationManager->getAvailableMigrations();

        // Identifier les migrations permanentes vs temporaires de test
        $permanentMigrations = [];
        $testMigrations = [];

        foreach ($allMigrations as $migration) {
            // Les migrations de test ont des versions du format 2025MMDDNNN
            if (preg_match('/^2025\d{7}$/', $migration['version'])) {
                $testMigrations[] = $migration;
            } else {
                $permanentMigrations[] = $migration;
            }
        }

        // Trier les migrations de test par version pour s'assurer de l'ordre
        usort($testMigrations, function($a, $b) {
            return version_compare($a['version'], $b['version']);
        });

        // Exécuter toutes les migrations permanentes
        foreach ($permanentMigrations as $migration) {
            $migrationManager->runMigration($migration);
        }

        // Exécuter seulement la première migration de test pour simuler un état ancien
        if (!empty($testMigrations)) {
            $firstTestMigration = $testMigrations[0]; // Force à prendre la première
            $migrationManager->runMigration($firstTestMigration);

            echo "  État ancien créé avec " . (count($permanentMigrations) + 1) . " migrations\n";
            echo "  Migration de test exécutée: {$firstTestMigration['version']}\n";
            echo "  Migration(s) de test restante(s): " . (count($testMigrations) - 1) . "\n";
        } else {
            echo "  État ancien créé avec " . count($permanentMigrations) . " migrations\n";
        }
    }

    /**
     * Vérifie l'intégrité de l'installation :
     * - présence de toutes les tables définies dans ogspy_structure.sql
     * - présence de toutes les colonnes définies dans ogspy_structure.sql
     * - données de configuration
     * - conformité des index avec ogspy_structure.sql
     */
    private function verifyInstallIntegrity($tablePrefix = 'ogspy_') {
        $schemaFile = __DIR__ . '/schemas/ogspy_structure.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception("Fichier de schéma introuvable: {$schemaFile}");
        }

        $expectedIndexes = $this->parseIndexesFromSchema($schemaFile, 'ogspy_', $tablePrefix);
        $expectedColumns = $this->parseColumnsFromSchema($schemaFile, 'ogspy_', $tablePrefix);
        $expectedTables  = array_keys($expectedColumns);

        // Vérifier que toutes les tables du schéma existent en DB
        $missingTables = [];
        foreach ($expectedTables as $table) {
            $result = $this->db->sql_query("SHOW TABLES LIKE '" . $this->db->sql_escape_string($table) . "'");
            if ($this->db->sql_numrows($result) === 0) {
                $missingTables[] = $table;
            }
        }
        if (!empty($missingTables)) {
            throw new Exception("Tables manquantes (" . count($missingTables) . "/" . count($expectedTables) . "): " . implode(', ', $missingTables));
        }
        echo "  Intégrité vérifiée: " . count($expectedTables) . " tables présentes\n";

        // Vérifier que la table de configuration a des données
        $result = $this->db->sql_query("SELECT COUNT(*) as count FROM {$tablePrefix}config");
        $row = $this->db->sql_fetch_assoc($result);
        if ($row['count'] == 0) {
            throw new Exception("Table de configuration vide");
        }

        // Vérifier que toutes les colonnes du schéma existent en DB
        $this->verifyColumnsAgainstSchema($tablePrefix, $expectedColumns);

        // Comparer les index de la DB avec ceux définis dans ogspy_structure.sql
        $this->verifyIndexesAgainstSchema($tablePrefix, $expectedIndexes);
    }

    /**
     * Compare les index attendus (issus de $expectedIndexes ou parsés depuis le schéma)
     * avec ceux présents en base de données.
     */
    private function verifyIndexesAgainstSchema(string $tablePrefix = 'ogspy_', array $expectedIndexes = []): void {
        $schemaFile = __DIR__ . '/schemas/ogspy_structure.sql';

        if (empty($expectedIndexes)) {
            if (!file_exists($schemaFile)) {
                throw new Exception("Fichier de schéma introuvable: {$schemaFile}");
            }
            $expectedIndexes = $this->parseIndexesFromSchema($schemaFile, 'ogspy_', $tablePrefix);
        }

        $actual = $this->getIndexesFromDb($tablePrefix);

        $errors = [];
        foreach ($expectedIndexes as $table => $indexes) {
            foreach ($indexes as $indexName => $expectedCols) {
                $actualCols = $actual[$table][$indexName] ?? null;
                if ($actualCols === null) {
                    $errors[] = "Index '{$indexName}' absent sur {$table} (attendu: " . implode(', ', $expectedCols) . ")";
                } elseif ($actualCols !== $expectedCols) {
                    $errors[] = "Index '{$indexName}' sur {$table}: ["
                        . implode(', ', $actualCols) . "] (attendu: ["
                        . implode(', ', $expectedCols) . "])";
                }
            }
        }

        if (!empty($errors)) {
            throw new Exception("Divergences d'index (structure.sql vs DB):\n  - " . implode("\n  - ", $errors));
        }

        echo "  Intégrité vérifiée: index conformes au schéma de référence\n";
    }

    /**
     * Parse les définitions d'index de toutes les tables dans un fichier SQL de structure.
     * Remplace $sourcePrefix par $targetPrefix dans les noms de tables.
     * Retourne [ tableName => [ indexName => [col1, col2, ...] ] ]
     */
    private function parseIndexesFromSchema(string $file, string $sourcePrefix, string $targetPrefix): array {
        $indexes = [];
        $currentTable = null;

        foreach (explode("\n", file_get_contents($file)) as $line) {
            $line = trim($line);

            if (preg_match('/^CREATE\s+TABLE\s+`?(\w+)`?/i', $line, $m)) {
                $raw = $m[1];
                $currentTable = str_starts_with($raw, $sourcePrefix)
                    ? $targetPrefix . substr($raw, strlen($sourcePrefix))
                    : $raw;
                $indexes[$currentTable] = [];
                continue;
            }

            if ($currentTable === null) {
                continue;
            }

            if (preg_match('/^PRIMARY\s+KEY\s+\((.+?)\)/i', $line, $m)) {
                $indexes[$currentTable]['PRIMARY'] = $this->parseIndexColumns($m[1]);
                continue;
            }

            if (preg_match('/^(?:UNIQUE\s+)?KEY\s+`?(\w+)`?\s+\((.+?)\)/i', $line, $m)) {
                $indexes[$currentTable][$m[1]] = $this->parseIndexColumns($m[2]);
            }
        }

        return array_filter($indexes, fn($idx) => !empty($idx));
    }

    /**
     * Découpe une liste de colonnes d'index SQL en tableau.
     * Ex: "`col1`, `col2`(191)" → ["col1", "col2"]
     */
    private function parseIndexColumns(string $rawCols): array {
        $cols = [];
        foreach (explode(',', $rawCols) as $col) {
            $col = trim(preg_replace('/\(\d+\)/', '', trim($col, " `\t")));
            if ($col !== '') {
                $cols[] = $col;
            }
        }
        return $cols;
    }

    /**
     * Récupère tous les index des tables avec $tablePrefix depuis information_schema.
     * Retourne [ tableName => [ indexName => [col1, col2, ...] ] ]
     */
    private function getIndexesFromDb(string $tablePrefix): array {
        $result = $this->db->sql_query(
            "SELECT Table_name, Index_name, Column_name
             FROM information_schema.STATISTICS
             WHERE Table_schema = DATABASE()
               AND Table_name LIKE '" . $this->db->sql_escape_string($tablePrefix) . "%'
             ORDER BY Table_name, Index_name, Seq_in_index"
        );

        $indexes = [];
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $indexes[$row['Table_name']][$row['Index_name']][] = $row['Column_name'];
        }

        return $indexes;
    }

    /**
     * Parse les colonnes de toutes les tables dans un fichier SQL de structure.
     * Remplace $sourcePrefix par $targetPrefix dans les noms de tables.
     * Retourne [ tableName => [col1, col2, ...] ]
     */
    private function parseColumnsFromSchema(string $file, string $sourcePrefix, string $targetPrefix): array {
        $columns = [];
        $currentTable = null;

        foreach (explode("\n", file_get_contents($file)) as $line) {
            $line = trim($line);

            if (preg_match('/^CREATE\s+TABLE\s+`?(\w+)`?/i', $line, $m)) {
                $raw = $m[1];
                $currentTable = str_starts_with($raw, $sourcePrefix)
                    ? $targetPrefix . substr($raw, strlen($sourcePrefix))
                    : $raw;
                $columns[$currentTable] = [];
                continue;
            }

            if ($currentTable === null) {
                continue;
            }

            // Ligne de définition de colonne : commence par `nomColonne`
            // On exclut les lignes KEY / PRIMARY KEY / UNIQUE KEY
            if (preg_match('/^`(\w+)`\s+/i', $line, $m)) {
                $columns[$currentTable][] = $m[1];
            }
        }

        return array_filter($columns, fn($cols) => !empty($cols));
    }

    /**
     * Vérifie que toutes les colonnes définies dans le schéma existent en DB.
     */
    private function verifyColumnsAgainstSchema(string $tablePrefix, array $expectedColumns): void {
        $actual = $this->getColumnsFromDb($tablePrefix);

        $errors = [];
        foreach ($expectedColumns as $table => $cols) {
            $actualCols = $actual[$table] ?? [];
            foreach ($cols as $col) {
                if (!in_array($col, $actualCols, true)) {
                    $errors[] = "Colonne '{$col}' absente sur {$table}";
                }
            }
        }

        if (!empty($errors)) {
            throw new Exception("Colonnes manquantes (structure.sql vs DB):\n  - " . implode("\n  - ", $errors));
        }

        echo "  Intégrité vérifiée: colonnes conformes au schéma de référence\n";
    }

    /**
     * Récupère toutes les colonnes des tables avec $tablePrefix depuis information_schema.
     * Retourne [ tableName => [col1, col2, ...] ]
     */
    private function getColumnsFromDb(string $tablePrefix): array {
        $result = $this->db->sql_query(
            "SELECT Table_name, Column_name
             FROM information_schema.COLUMNS
             WHERE Table_schema = DATABASE()
               AND Table_name LIKE '" . $this->db->sql_escape_string($tablePrefix) . "%'
             ORDER BY Table_name, Ordinal_position"
        );

        $columns = [];
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $columns[$row['Table_name']][] = $row['Column_name'];
        }

        return $columns;
    }

    /**
     * Récupère la version de la dernière migration disponible
     */
    private function getLatestMigrationVersion() {
        global $table_prefix;
        $migrationManager = new MigrationManager($this->db, $this->logger, $table_prefix);
        $allMigrations = $migrationManager->getAvailableMigrations();

        if (empty($allMigrations)) {
            return '0.0.0';
        }

        $lastMigration = end($allMigrations);
        return $lastMigration['version'];
    }

    /**
     * Crée dynamiquement les fichiers de migration de test
     */
    private function createTestMigrations() {
        echo "🔧 Création des migrations de test...\n";

        $migrationsPath = __DIR__ . '/migrations/';

        // Migration de test 1: Ajout de fonctionnalités
        $testMigration1 = $migrationsPath . '20250815001_add_test_features.php';
        $content1 = $this->getTestMigration1Content();
        file_put_contents($testMigration1, $content1);
        $this->createdTestMigrations[] = $testMigration1;

        // Migration de test 2: Mise à jour des fonctionnalités
        $testMigration2 = $migrationsPath . '20250815002_update_test_features.php';
        $content2 = $this->getTestMigration2Content();
        file_put_contents($testMigration2, $content2);
        $this->createdTestMigrations[] = $testMigration2;

        echo "  ✓ " . count($this->createdTestMigrations) . " migration(s) de test créée(s)\n";
    }

    /**
     * Supprime les fichiers de migration de test créés
     */
    private function removeTestMigrations() {
        if (!empty($this->createdTestMigrations)) {
            echo "🧹 Suppression des migrations de test...\n";

            foreach ($this->createdTestMigrations as $migrationFile) {
                if (file_exists($migrationFile)) {
                    unlink($migrationFile);
                    echo "  ✓ Supprimé: " . basename($migrationFile) . "\n";
                }
            }

            $this->createdTestMigrations = [];
        }
    }

    /**
     * Génère le contenu de la première migration de test
     */
    private function getTestMigration1Content() {
        return '<?php
/**
 * Migration de test pour enrichir les tests d\'upgrade
 * Ajoute quelques fonctionnalités fictives pour valider le système de migration
 */
class Migration_20250815001_AddTestFeatures {

    public function getVersion(): string {
        return \'20250815001\';
    }

    public function getDescription(): string {
        return \'Ajout fonctionnalités de test (table test, colonnes additionnelles)\';
    }

    public function up(): string {
        global $table_prefix;

        return "
        -- Ajout d\'une table de test
        CREATE TABLE IF NOT EXISTS {$table_prefix}test_features (
            id INT AUTO_INCREMENT PRIMARY KEY,
            feature_name VARCHAR(100) NOT NULL,
            feature_value TEXT,
            enabled BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        -- Insertion de quelques données de test
        INSERT IGNORE INTO {$table_prefix}test_features (feature_name, feature_value, enabled)
        VALUES
        (\'feature_test_1\', \'Configuration de test 1\', 1),
        (\'feature_test_2\', \'Configuration de test 2\', 1),
        (\'debug_mode\', \'Mode debug pour tests\', 0);

        -- Ajout d\'un index pour optimiser les requêtes de test
        CREATE INDEX idx_feature_name ON {$table_prefix}test_features (feature_name);
        ";
    }

    public function down(): string {
        global $table_prefix;

        return "
        -- Suppression de la table de test
        DROP TABLE IF EXISTS {$table_prefix}test_features;
        ";
    }
}';
    }

    /**
     * Génère le contenu de la seconde migration de test
     */
    private function getTestMigration2Content() {
        return '<?php
/**
 * Migration de test avancée pour enrichir davantage les tests
 * Simule une mise à jour de fonctionnalité existante
 */
class Migration_20250815002_UpdateTestFeatures {

    public function getVersion(): string {
        return \'20250815002\';
    }

    public function getDescription(): string {
        return \'Mise à jour des fonctionnalités de test (nouveaux champs, optimisations)\';
    }

    public function up(): string {
        global $table_prefix;

        return "
        -- Ajout de nouvelles colonnes à la table de test
        ALTER TABLE {$table_prefix}test_features
        ADD COLUMN priority INT DEFAULT 0,
        ADD COLUMN category VARCHAR(50) DEFAULT \'general\',
        ADD COLUMN metadata TEXT DEFAULT NULL;

        -- Mise à jour des données existantes avec les nouvelles colonnes
        UPDATE {$table_prefix}test_features SET priority = 1, category = \'core\' WHERE feature_name = \'feature_test_1\';
        UPDATE {$table_prefix}test_features SET priority = 2, category = \'admin\' WHERE feature_name = \'feature_test_2\';
        UPDATE {$table_prefix}test_features SET priority = 3, category = \'debug\' WHERE feature_name = \'debug_mode\';

        -- Ajout de nouvelles fonctionnalités de test
        INSERT IGNORE INTO {$table_prefix}test_features (feature_name, feature_value, enabled, priority, category)
        VALUES
        (\'advanced_caching\', \'Système de cache avancé\', 1, 2, \'performance\'),
        (\'api_v2\', \'API version 2.0\', 0, 1, \'api\'),
        (\'security_audit\', \'Audit de sécurité automatique\', 1, 3, \'security\');

        -- Création d\'un index composite pour optimiser les requêtes
        CREATE INDEX idx_category_priority ON {$table_prefix}test_features (category, priority);

        -- Ajout d\'une table de logs de test pour simuler une fonctionnalité de monitoring
        CREATE TABLE IF NOT EXISTS {$table_prefix}test_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            log_level ENUM(\'INFO\', \'WARNING\', \'ERROR\', \'DEBUG\') DEFAULT \'INFO\',
            message TEXT NOT NULL,
            context TEXT DEFAULT NULL,
            migration_version VARCHAR(20) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        -- Insertion d\'un log de migration
        INSERT INTO {$table_prefix}test_logs (log_level, message, migration_version)
        VALUES (\'INFO\', \'Migration 20250815002 exécutée avec succès\', \'20250815002\');
        ";
    }

    public function down(): string {
        global $table_prefix;

        return "
        -- Suppression de la table de logs
        DROP TABLE IF EXISTS {$table_prefix}test_logs;

        -- Suppression de l\'index composite
        DROP INDEX IF EXISTS idx_category_priority ON {$table_prefix}test_features;

        -- Suppression des fonctionnalités ajoutées dans cette migration
        DELETE FROM {$table_prefix}test_features WHERE feature_name IN (\'advanced_caching\', \'api_v2\', \'security_audit\');

        -- Suppression des colonnes ajoutées
        ALTER TABLE {$table_prefix}test_features
        DROP COLUMN priority,
        DROP COLUMN category,
        DROP COLUMN metadata;
        ";
    }
}';
    }

    /**
     * Nettoyage après les tests
     */
    private function cleanup() {
        try {
            // Supprimer les migrations de test créées
            $this->removeTestMigrations();

            // Retourner à la base originale
            $this->switchToOriginalDatabase();

            // Supprimer la base de test
            if ($this->testDbName) {
                $this->db->sql_query("DROP DATABASE IF EXISTS `{$this->testDbName}`");
                echo "🧹 Base de test supprimée: {$this->testDbName}\n";
            }

        } catch (Exception $e) {
            $this->logger->error("Erreur lors du nettoyage", ['error' => $e->getMessage()]);
            echo "⚠️  Erreur lors du nettoyage: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Test de performance des migrations
     */
    public function testMigrationPerformance() {
        $result = ['success' => false, 'error' => null, 'performance_data' => []];

        try {
            echo "⚡ DÉBUT DU TEST DE PERFORMANCE\n";
            echo "==============================\n\n";

            // Créer les migrations de test pour ce test spécifique
            $this->createTestMigrations();

            // 1. Créer une base de test vierge
            $this->createTestDatabase();
            $this->switchToTestDatabase();

            // 2. Mesurer les performances de chaque migration
            $migrationManager = new MigrationManager($this->db, $this->logger);
            $allMigrations = $migrationManager->getAvailableMigrations();

            foreach ($allMigrations as $migration) {
                $startTime = microtime(true);

                echo "  ⏱️  Mesure migration: {$migration['version']}\n";

                try {
                    $migrationResult = $migrationManager->runMigration($migration);
                    $endTime = microtime(true);
                    $executionTime = $endTime - $startTime;

                    $result['performance_data'][] = [
                        'version' => $migration['version'],
                        'description' => $migration['description'],
                        'execution_time' => $executionTime,
                        'success' => $migrationResult
                    ];

                } catch (Exception $e) {
                    $endTime = microtime(true);
                    $executionTime = $endTime - $startTime;

                    $result['performance_data'][] = [
                        'version' => $migration['version'],
                        'description' => $migration['description'],
                        'execution_time' => $executionTime,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            $result['success'] = true;

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            $this->logger->error("Test de performance échoué", ['error' => $e->getMessage()]);
        } finally {
            // Nettoyage spécifique pour ce test
            $this->removeTestMigrations();
            $this->switchToOriginalDatabase();
            if ($this->testDbName) {
                $this->db->sql_query("DROP DATABASE IF EXISTS `{$this->testDbName}`");
            }
        }

        return $result;
    }

    /**
     * Récupère une valeur de la table de configuration
     */
    private function getConfigValue($name) {
        global $table_prefix;
        $result = $this->db->sql_query("SELECT value FROM {$table_prefix}config WHERE name = '" . $this->db->sql_escape_string($name) . "'");
        if ($this->db->sql_numrows($result) > 0) {
            $row = $this->db->sql_fetch_assoc($result);
            return $row['value'];
        }
        return null;
    }


    /**
     * Journalise l'état de la version applicative
     */
    private function logApplicationVersionState($stage) {
        $appVersion = $this->getConfigValue('version');
        echo "  État de la version applicative ({$stage}): {$appVersion}\n";
    }

    /**
     * Test avec différents préfixes de table
     */
    public function testTablePrefix() {
        $result = ['success' => false, 'error' => null, 'details' => []];

        // Liste des préfixes de table à tester
        $tablePrefixes = ['ogspy_', 'ogspy_alt_', 'test_'];

        try {
            foreach ($tablePrefixes as $prefix) {
                echo "🏷️  Test avec le préfixe de table: {$prefix}\n";

                // 1. Créer une nouvelle base de test avec le préfixe spécifié
                $this->testDbName = 'ogspy_test_prefix_' . uniqid();
                $this->createTestDatabase();
                $this->switchToTestDatabase();

                // 3. Exécuter les migrations avec le nouveau préfixe
                $migrationManager = new MigrationManager($this->db, $this->logger, $prefix);
                $allMigrations = $migrationManager->getAvailableMigrations();

                foreach ($allMigrations as $migration) {
                    echo "  Exécution migration: {$migration['version']}\n";
                    $migrationResult = $migrationManager->runMigration($migration);

                    if (!$migrationResult) {
                        throw new Exception("Migration {$migration['version']} échouée avec le préfixe {$prefix}");
                    }
                }

                // 4. Vérifier l'intégrité de l'installation avec le nouveau préfixe
                $this->verifyInstallIntegrity($prefix);

                // 5. Vérifier que la version DB correspond
                $dbVersion = $migrationManager->getCurrentDbVersion();
                $expectedVersion = $this->getLatestMigrationVersion();

                if ($dbVersion !== $expectedVersion) {
                    throw new Exception("Version DB incorrecte avec le préfixe {$prefix}: attendue {$expectedVersion}, trouvée {$dbVersion}");
                }

                $result['details'][$prefix] = [
                    'success' => true,
                    'version' => $dbVersion,
                    'migrations' => array_column($allMigrations, 'version')
                ];

                echo "✓ Test réussi avec le préfixe de table: {$prefix}\n\n";
            }

            $result['success'] = true;

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            $this->logger->error("Test de préfixe de table échoué", ['error' => $e->getMessage()]);
        } finally {
            // Nettoyage
            $this->cleanup();
        }

        return $result;
    }


}
