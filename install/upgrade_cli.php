#!/usr/bin/env php
<?php

/**
 * Script CLI pour la gestion des mises à jour OGSpy
 * Usage: php upgrade_cli.php [action]
 * Actions: install, check, upgrade, force, status, logs
 */

// Vérification que nous sommes bien en CLI
if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande\n");
}

define("IN_SPYOGAME", true);
define("CLI_MODE", true);

// Vérification de l'action pour décider si id.php est requis
$action = $argv[1] ?? 'help';
$requiresIdFile = !in_array($action, ['install', 'help']);

// Chargement des composants nécessaires
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;

// Chargement minimal pour CLI (sans common.php)
try {
    // Pour l'installation, on n'a pas besoin du fichier id.php au départ
    if ($requiresIdFile) {
        // Chargement des paramètres DB pour toutes les autres actions
        if (!file_exists(dirname(__DIR__) . '/config/id.php')) {
            die("Erreur: Fichier de configuration id.php introuvable.\nLancez d'abord l'installation: php upgrade_cli.php install\n");
        }

        require_once dirname(__DIR__) . '/config/id.php';
        require_once dirname(__DIR__) . '/includes/mysql.php';

        // Compatibilité ancien/nouveau format id.php
        if (defined('DB_HOST')) {
            // Nouveau format (constantes)
            $dbHost = DB_HOST;
            $dbUser = DB_USER;
            $dbPassword = DB_PASSWORD;
            $dbName = DB_NAME;
        } else {
            // Ancien format (variables)
            $dbHost = $db_host ?? 'localhost';
            $dbUser = $db_user ?? '';
            $dbPassword = $db_password ?? '';
            $dbName = $db_database ?? '';
        }

        // Connexion DB
        $db = sql_db::getInstance($dbHost, $dbUser, $dbPassword, $dbName);
    }

    // Logger pour CLI
    $log = new Logger('OGSpy-CLI');
    $log->pushHandler(new RotatingFileHandler(dirname(__DIR__) . '/logs/OGSpy-cli.log', 365, Level::Debug));

    // Loggers SQL nécessaires pour mysql.php (si fichier id.php chargé)
    if ($requiresIdFile) {
        $logSQL = new Logger('OGSpy-CLI-SQL');
        $logSQL->pushHandler(new RotatingFileHandler(dirname(__DIR__) . '/logs/OGSpy-cli-sql.log', 365, Level::Debug));

        $logSlowSQL = new Logger('OGSpy-CLI-SlowSQL');
        $logSlowSQL->pushHandler(new RotatingFileHandler(dirname(__DIR__) . '/logs/OGSpy-cli-sql-slow.log', 365, Level::Debug));
    }

    echo "🔧 OGSpy CLI Tools - Version 4.0\n";
    echo "================================\n\n";

} catch (Exception $e) {
    die("Erreur d'initialisation: " . $e->getMessage() . "\n");
}

require_once 'MigrationManager.php';
require_once 'AutoUpgradeManager.php';
require_once 'TestManager.php';
require_once 'version.php';

class UpgradeCLI {
    private $autoUpgrade;
    private $migrationManager;

    public function __construct() {
        // Ne pas initialiser les objets pour l'installation ou l'aide
        global $argv;
        $action = $argv[1] ?? 'help';

        if (in_array($action, ['install', 'help'])) {
            // Pour l'installation et l'aide, on n'a pas besoin des objets
            return;
        }

        global $db, $log;
        try {
            $this->migrationManager = new MigrationManager($db, $log);
            $this->autoUpgrade = new AutoUpgradeManager($db, $log);
        } catch (Exception $e) {
            die("Erreur initialisation classes: " . $e->getMessage() . "\n");
        }
    }

    public function run($argv) {
        $action = $argv[1] ?? 'help';

        switch ($action) {
            case 'install':
                $this->runInstall($argv);
                break;
            case 'check':
                $this->checkStatus();
                break;
            case 'upgrade':
                $this->runUpgrade();
                break;
            case 'force':
                $this->forceUpgrade();
                break;
            case 'status':
                $this->showStatus();
                break;
            case 'logs':
                $this->showLogs();
                break;
            case 'unlock':
                $this->unlock();
                break;
            case 'unlock-install':
                $this->unlockInstall();
                break;
            case 'uninstall':
                $this->uninstall();
                break;
            case 'test':
                $this->runTests();
                break;
            case 'test-install':
                $this->testInstall();
                break;
            case 'test-upgrade':
                $this->testUpgrade();
                break;
            case 'test-performance':
                $this->testPerformance();
                break;
            default:
                $this->showHelp();
        }
    }

    /**
     * Installation complète d'OGSpy via CLI
     */
    private function runInstall($argv) {
        // Vérifier si les arguments sont fournis
        if (count($argv) < 8) {
            echo "❌ Arguments manquants pour l'installation automatisée\n";
            echo "Usage: php upgrade_cli.php install <db_host> <db_user> <db_password> <db_name> <admin_user> <admin_password> [admin_email] [table_prefix] [--no-lock]\n\n";
            echo "Exemples:\n";
            echo "  php upgrade_cli.php install localhost root mypass ogspy admin admin123\n";
            echo "  php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_\n";
            echo "  php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_ --no-lock\n";
            echo "\nNote: L'installation est verrouillée par défaut. Utilisez --no-lock pour désactiver le verrouillage.\n";
            exit(1);
        }

        // Récupération des arguments
        $dbHost = $argv[2];
        $dbUser = $argv[3];
        $dbPassword = $argv[4];
        $dbName = $argv[5];
        $adminUser = $argv[6];
        $adminPassword = $argv[7];
        $adminEmail = $argv[8] ?? '';
        $tablePrefix = $argv[9] ?? 'ogspy_';
        // Modification : --lock activé par défaut, utiliser --no-lock pour désactiver
        $lockInstall = !in_array('--no-lock', $argv);

        echo "🚀 INSTALLATION AUTOMATISÉE OGSpy 4.0\n";
        echo "=====================================\n\n";

        // Étape 1: Vérifications préalables
        echo "📋 Étape 1: Vérifications préalables\n";
        echo "------------------------------------\n";

        // Vérification PHP
        $phpVersion = PHP_VERSION;
        if (version_compare($phpVersion, '7.4.0', '<')) {
            echo "❌ Version PHP incompatible: {$phpVersion}\n";
            echo "   PHP 7.4 minimum requis\n";
            exit(1);
        }
        echo "✓ Version PHP: {$phpVersion}\n";

        // Vérification des extensions
        $requiredExtensions = ['mysqli', 'json', 'mbstring'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                echo "❌ Extension PHP manquante: {$ext}\n";
                exit(1);
            }
            echo "✓ Extension {$ext}: disponible\n";
        }

        // Vérification des permissions
        $directories = [
            dirname(__DIR__) . '/cache',
            dirname(__DIR__) . '/logs',
            dirname(__DIR__) . '/config'
        ];

        foreach ($directories as $dir) {
            if (!is_writable($dir)) {
                echo "❌ Répertoire non accessible en écriture: {$dir}\n";
                exit(1);
            }
            echo "✓ Permissions: {$dir}\n";
        }

        echo "\n";

        // Étape 2: Configuration de la base de données
        echo "🔧 Étape 2: Configuration de la base de données\n";
        echo "------------------------------------------------\n";

        $dbConfig = [
            'host' => $dbHost,
            'user' => $dbUser,
            'password' => $dbPassword,
            'database' => $dbName,
            'table_prefix' => $tablePrefix
        ];

        echo "Host: {$dbHost}\n";
        echo "User: {$dbUser}\n";
        echo "Database: {$dbName}\n";
        echo "Prefix: {$tablePrefix}\n";

        // Tester la connexion
        echo "🔍 Test de connexion à la base de données...\n";
        try {
            require_once 'ConfigGenerator.php';
            $configGenerator = new ConfigGenerator();
            $configGenerator->testDbConnection($dbConfig);
            echo "✓ Connexion à la base de données réussie\n";
        } catch (Exception $e) {
            echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
            exit(1);
        }

        // Générer le fichier de configuration
        echo "💾 Génération du fichier de configuration...\n";
        try {
            $configGenerator->generateIdFile($dbConfig);
            echo "✓ Fichier id.php généré avec succès\n";
        } catch (Exception $e) {
            echo "❌ Erreur lors de la génération: " . $e->getMessage() . "\n";
            exit(1);
        }

        echo "\n";

        // Étape 3: Installation de la base de données
        echo "🔄 Étape 3: Installation de la base de données\n";
        echo "----------------------------------------------\n";

        // Recharger la connexion avec la nouvelle configuration
        global $db, $log, $logSQL, $logSlowSQL, $benchSQL;
        try {
            require_once dirname(__DIR__) . '/config/id.php';
            require_once dirname(__DIR__) . '/includes/mysql.php';

            // Définir les loggers SQL comme variables globales (requis par mysql.php)
            $logSQL = new Logger('OGSpy-CLI-SQL');
            $logSQL->pushHandler(new RotatingFileHandler(dirname(__DIR__) . '/logs/OGSpy-cli-sql.log', 365, Level::Debug));

            $logSlowSQL = new Logger('OGSpy-CLI-SlowSQL');
            $logSlowSQL->pushHandler(new RotatingFileHandler(dirname(__DIR__) . '/logs/OGSpy-cli-sql-slow.log', 365, Level::Debug));

            // Créer un objet benchmark simple (requis par mysql.php)
            $benchSQL = new class {
                public function start() { /* no-op */ }
                public function stop($label) { /* no-op */ }
            };

            $db = sql_db::getInstance($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);
            // Correction : passer le préfixe au constructeur de MigrationManager
            $this->migrationManager = new MigrationManager($db, $log, $dbConfig['table_prefix']);
        } catch (Exception $e) {
            echo "❌ Erreur de reconnexion: " . $e->getMessage() . "\n";
            exit(1);
        }

        // Exécuter toutes les migrations
        echo "📦 Exécution des migrations...\n";
        try {
            $pendingMigrations = $this->migrationManager->getPendingMigrations();
            if (!empty($pendingMigrations)) {
                echo "   " . count($pendingMigrations) . " migration(s) à exécuter\n";

                $results = $this->migrationManager->runPendingMigrations(false);

                $successful = array_filter($results, function($r) { return $r['success']; });
                $failed = array_filter($results, function($r) { return !$r['success']; });

                if (empty($failed)) {
                    echo "✓ " . count($successful) . " migration(s) exécutée(s) avec succès\n";
                } else {
                    echo "❌ " . count($failed) . " migration(s) échouée(s)\n";
                    foreach ($failed as $failedMigration) {
                        if (isset($failedMigration['error'])) {
                            echo "   ✗ {$failedMigration['version']}: {$failedMigration['error']}\n";
                        }
                    }
                    exit(1);
                }
            } else {
                echo "✓ Aucune migration nécessaire\n";
            }
        } catch (Exception $e) {
            echo "❌ Erreur lors des migrations: " . $e->getMessage() . "\n";
            exit(1);
        }

        echo "\n";

        // Étape 4: Création du compte administrateur
        echo "👤 Étape 4: Création du compte administrateur\n";
        echo "---------------------------------------------\n";

        echo "Admin user: {$adminUser}\n";
        if ($adminEmail) {
            echo "Admin email: {$adminEmail}\n";
        }

        try {
            // Utiliser la variable locale $tablePrefix au lieu de global $table_prefix
            // Vérifier si l'utilisateur existe déjà
            $checkUser = $db->sql_query("SELECT COUNT(*) as count FROM {$tablePrefix}user WHERE name = '" . $db->sql_escape_string($adminUser) . "'");
            $userExists = $db->sql_fetch_assoc($checkUser);

            if ($userExists['count'] == 0) {
                $hashedPass = password_hash($adminPassword, PASSWORD_DEFAULT);
                $currentTime = time();

                $sql = "INSERT INTO {$tablePrefix}user (name, password_s, pwd_change, email, admin, coadmin, active, regdate, lastvisit)
                        VALUES ('" . $db->sql_escape_string($adminUser) . "',
                               '" . $db->sql_escape_string($hashedPass) . "',
                               0,
                               '" . $db->sql_escape_string($adminEmail) . "',
                               1, 1, 1, {$currentTime}, {$currentTime})";

                if ($db->sql_query($sql)) {
                    echo "✓ Compte administrateur créé: {$adminUser}\n";
                } else {
                    echo "❌ Erreur lors de la création du compte\n";
                    exit(1);
                }
            } else {
                echo "⚠️  Un utilisateur avec ce nom existe déjà\n";
            }
        } catch (Exception $e) {
            echo "❌ Erreur: " . $e->getMessage() . "\n";
            exit(1);
        }

        echo "\n";

        // Étape 5: Finalisation
        echo "🎉 Étape 5: Finalisation\n";
        echo "------------------------\n";

        // Verrouiller l'installation si demandé
        if ($lockInstall) {
            file_put_contents(__DIR__ . '/install.lock', '');
            echo "✓ Installation verrouillée pour la sécurité\n";
        } else {
            echo "ℹ Installation non verrouillée (utilisez --lock pour verrouiller)\n";
        }

        echo "\n";
        echo "🎊 INSTALLATION TERMINÉE AVEC SUCCÈS !\n";
        echo "=====================================\n";
        echo "✓ OGSpy 4.0 est maintenant prêt à être utilisé\n";
        echo "✓ Connectez-vous avec: {$adminUser}\n";
        echo "✓ URL d'accès: http://votre-serveur/ogspy/\n";
        echo "\nPour gérer l'installation :\n";
        echo "  - Statut: php upgrade_cli.php status\n";
        echo "  - Déverrouiller: php upgrade_cli.php unlock-install\n";
        echo "  - Mise à jour: php upgrade_cli.php upgrade\n";
    }

    /**
     * Vérifie le statut des migrations et de la base de données
     */
    private function checkStatus() {
        echo "=== VÉRIFICATION DU STATUT ===\n";

        $currentDbVersion = $this->migrationManager->getCurrentDbVersion();
        $pendingMigrations = $this->migrationManager->getPendingMigrations();

        echo "Version actuelle DB: {$currentDbVersion}\n";
        echo "Migrations en attente: " . count($pendingMigrations) . "\n";

        if (!empty($pendingMigrations)) {
            echo "\nMigrations à exécuter:\n";
            foreach ($pendingMigrations as $migration) {
                echo "  - {$migration['version']}: {$migration['description']}\n";
            }
        } else {
            echo "✓ Base de données à jour\n";
        }

        echo "\nAuto-upgrade possible: " . ($this->autoUpgrade->canAutoUpgrade() ? "OUI" : "NON") . "\n";
    }

    private function runUpgrade() {
        echo "=== LANCEMENT DE LA MISE À JOUR ===\n";

        $result = $this->autoUpgrade->checkAndUpgrade();

        switch ($result['status']) {
            case 'up_to_date':
                echo "✓ Base de données déjà à jour\n";
                break;
            case 'success':
                echo "✓ Mise à jour réussie\n";
                echo "  Nouvelle version: {$result['version']}\n";
                echo "  Migrations exécutées: {$result['migrations_count']}\n";
                echo "  Temps d'exécution: {$result['execution_time']}s\n";
                break;
            case 'in_progress':
                echo "⚠ Mise à jour déjà en cours\n";
                break;
            case 'error':
            case 'critical_error':
                echo "✗ Erreur: {$result['message']}\n";
                if (isset($result['error'])) {
                    echo "  Détails: {$result['error']}\n";
                }
                exit(1);
        }
    }

    private function forceUpgrade() {
        echo "=== MISE À JOUR FORCÉE ===\n";
        echo "⚠ Suppression des verrous et tentative de mise à jour...\n";

        $result = $this->autoUpgrade->forceUpgrade();

        switch ($result['status']) {
            case 'up_to_date':
                echo "✓ Aucune migration nécessaire\n";
                break;
            case 'success':
                echo "✓ Mise à jour forcée réussie\n";
                echo "  Nouvelle version: {$result['version']}\n";
                echo "  Migrations exécutées: {$result['migrations_count']}\n";
                break;
            default:
                echo "✗ Échec de la mise à jour forcée\n";
                echo "  Erreur: {$result['message']}\n";
                exit(1);
        }
    }

    private function showStatus() {
        global $server_config, $ogspy_version;

        echo "=== STATUT COMPLET ===\n";
        echo "Version logiciel: {$ogspy_version}\n";
        echo "Version config: " . ($server_config['version'] ?? 'inconnue') . "\n";
        echo "Version DB: " . $this->migrationManager->getCurrentDbVersion() . "\n";

        $lockFile = dirname(__DIR__) . '/cache/upgrade.lock';
        if (file_exists($lockFile)) {
            $lockData = json_decode(file_get_contents($lockFile), true);
            echo "⚠ Verrou actif depuis: " . date('Y-m-d H:i:s', $lockData['start_time']) . "\n";
        } else {
            echo "✓ Aucun verrou actif\n";
        }

        echo "Auto-upgrade activé: " . ($this->autoUpgrade->canAutoUpgrade() ? "OUI" : "NON") . "\n";
    }

    private function showLogs() {
        echo "=== LOGS DE MISE À JOUR ===\n";

        $logFile = dirname(__DIR__) . '/logs/auto-upgrade-' . date('Y-m-d') . '.log';

        if (file_exists($logFile)) {
            echo "Logs du jour:\n";
            echo file_get_contents($logFile);
        } else {
            echo "Aucun log trouvé pour aujourd'hui\n";
        }

        // Affiche les derniers logs disponibles
        $logPattern = dirname(__DIR__) . '/logs/auto-upgrade-*.log';
        $logFiles = glob($logPattern);
        rsort($logFiles);

        if (!empty($logFiles) && $logFiles[0] !== $logFile) {
            echo "\nDernier log disponible (" . basename($logFiles[0]) . "):\n";
            echo file_get_contents($logFiles[0]);
        }
    }

    private function unlock() {
        echo "=== SUPPRESSION DES VERROUS ===\n";

        $lockFile = dirname(__DIR__) . '/cache/upgrade.lock';
        if (file_exists($lockFile)) {
            unlink($lockFile);
            echo "✓ Verrou de mise à jour supprimé\n";
        } else {
            echo "ℹ Aucun verrou de mise à jour à supprimer\n";
        }
    }

    private function unlockInstall() {
        echo "=== DÉVERROUILLAGE DE L'INSTALLATION ===\n";

        $installLockFile = __DIR__ . '/install.lock';
        if (file_exists($installLockFile)) {
            unlink($installLockFile);
            echo "✓ Installation déverrouillée\n";
            echo "ℹ L'interface web d'installation est maintenant accessible\n";
        } else {
            echo "ℹ L'installation n'était pas verrouillée\n";
        }
    }

    /**
     * Désinstalle OGSpy : purge la base et supprime les fichiers générés
     */
    private function uninstall() {
        echo "=== DÉSINSTALLATION OGSpy ===\n";
        echo "Cette opération va supprimer toutes les tables OGSpy et les fichiers générés (cache, logs, paramètres/id.php, etc.).\n";
        echo "Voulez-vous continuer ? (oui/non) : ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        if (strtolower($line) !== 'oui') {
            echo "Opération annulée.\n";
            return;
        }
        fclose($handle);

        global $db;
        // 1. Suppression des tables OGSpy
        $tables = $db->sql_query("SHOW TABLES");
        $tableNames = [];
        while ($row = $db->sql_fetch_row($tables)) {
            $tableNames[] = $row[0];
        }
        foreach ($tableNames as $table) {
            $db->sql_query("DROP TABLE IF EXISTS `{$table}`");
            echo "Table supprimée : {$table}\n";
        }

        // 2. Suppression des fichiers générés (uniquement les fichiers, pas les dossiers)
        $filePaths = [
            // Fichiers dans cache
            ...glob(dirname(__DIR__) . '/cache/*'),
            // Fichiers dans logs
            ...glob(dirname(__DIR__) . '/logs/*'),
            // Fichiers dans config
            ...glob(dirname(__DIR__) . '/config/*'),
            // Uniquement config/id.php
            dirname(__DIR__) . '/config/id.php',
            // Fichier install.lock
            __DIR__ . '/install.lock',
        ];
        foreach ($filePaths as $file) {
            if (is_file($file)) {
                unlink($file);
                echo "Fichier supprimé : {$file}\n";
            }
        }
        echo "Désinstallation terminée.\n";
    }

    private function showHelp() {
        echo "=== AIDE - UPGRADE CLI ===\n";
        echo "Usage: php upgrade_cli.php [action]\n\n";
        echo "Actions disponibles:\n";
        echo "  install <db_host> <db_user> <db_password> <db_name> <admin_user> <admin_password> [admin_email] [table_prefix] [--no-lock]\n";
        echo "                      - Installation automatisée d'OGSpy (verrouillée par défaut)\n";
        echo "  check               - Vérifie le statut des migrations\n";
        echo "  upgrade             - Lance la mise à jour automatique\n";
        echo "  force               - Force la mise à jour (supprime les verrous)\n";
        echo "  status              - Affiche le statut complet du système\n";
        echo "  logs                - Affiche les logs de mise à jour\n";
        echo "  unlock              - Supprime les verrous de mise à jour\n";
        echo "  unlock-install      - Déverrouille l'installation web\n";
        echo "  uninstall           - Désinstalle le système\n";
        echo "  test                - Lance tous les tests (install + upgrade)\n";
        echo "  test-install        - Test uniquement l'installation initiale\n";
        echo "  test-upgrade        - Test uniquement les mises à niveau\n";
        echo "  test-performance    - Test de performance des migrations\n";
        echo "  help                - Affiche cette aide\n\n";
        echo "Installation automatisée:\n";
        echo "  php upgrade_cli.php install localhost root mypass ogspy admin admin123\n";
        echo "  php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com\n";
        echo "  php upgrade_cli.php install localhost root mypass ogspy admin admin123 admin@example.com ogspy_ --no-lock\n\n";
        echo "Note: L'installation est maintenant verrouillée par défaut pour la sécurité.\n";
        echo "      Utilisez --no-lock pour désactiver le verrouillage si nécessaire.\n\n";
    }

    /**
     * Lance tous les tests (installation + mises à niveau)
     */
    private function runTests() {
        global $db, $log;

        echo "🧪 LANCEMENT DES TESTS COMPLETS\n";
        echo "===============================\n\n";
        echo "⚠️  ATTENTION: Les tests vont créer et supprimer des bases de données temporaires.\n";
        echo "Assurez-vous d'avoir les permissions nécessaires.\n\n";

        try {
            $testManager = new TestManager($db, $log);
            $results = $testManager->runAllTests();

            echo "\n=== RÉSULTATS DES TESTS ===\n";

            if ($results['success']) {
                echo "✅ TOUS LES TESTS RÉUSSIS\n";
                echo "✓ Test d'installation: RÉUSSI\n";
                echo "✓ Test de mise à niveau: RÉUSSI\n";
            } else {
                echo "❌ ÉCHEC DES TESTS\n";

                if ($results['install_test'] && !$results['install_test']['success']) {
                    echo "✗ Test d'installation: ÉCHOUÉ\n";
                    echo "  Erreur: " . $results['install_test']['error'] . "\n";
                }

                if ($results['upgrade_test'] && !$results['upgrade_test']['success']) {
                    echo "✗ Test de mise à niveau: ÉCHOUÉ\n";
                    echo "  Erreur: " . $results['upgrade_test']['error'] . "\n";
                }

                echo "\nErreurs détaillées:\n";
                foreach ($results['errors'] as $error) {
                    echo "  - {$error}\n";
                }

                exit(1);
            }

        } catch (Exception $e) {
            echo "❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Test uniquement l'installation initiale
     */
    private function testInstall() {
        global $db, $log;

        echo "📦 TEST D'INSTALLATION INITIALE\n";
        echo "================================\n\n";

        try {
            $testManager = new TestManager($db, $log);
            $result = $testManager->testFreshInstall();

            echo "\n=== RÉSULTAT DU TEST D'INSTALLATION ===\n";

            if ($result['success']) {
                echo "✅ TEST D'INSTALLATION RÉUSSI\n";
                echo "✓ Version finale: " . $result['details']['final_version'] . "\n";
                echo "✓ Migrations exécutées: " . count($result['details']['executed_migrations']) . "\n";
                echo "✓ Liste des migrations:\n";
                foreach ($result['details']['executed_migrations'] as $version) {
                    echo "    - {$version}\n";
                }
            } else {
                echo "❌ TEST D'INSTALLATION ÉCHOUÉ\n";
                echo "✗ Erreur: " . $result['error'] . "\n";
                exit(1);
            }

        } catch (Exception $e) {
            echo "❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Test uniquement les mises à niveau
     */
    private function testUpgrade() {
        global $db, $log;

        echo "⬆️  TEST DE MISE À NIVEAU\n";
        echo "========================\n\n";

        try {
            $testManager = new TestManager($db, $log);
            $result = $testManager->testUpgrade();

            echo "\n=== RÉSULTAT DU TEST DE MISE À NIVEAU ===\n";

            if ($result['success']) {
                echo "✅ TEST DE MISE À NIVEAU RÉUSSI\n";
                echo "✓ Version initiale: " . $result['details']['initial_version'] . "\n";
                echo "✓ Version finale: " . $result['details']['final_version'] . "\n";
                echo "✓ Statut: " . $result['details']['upgrade_result']['status'] . "\n";

                if (isset($result['details']['upgrade_result']['migrations_count'])) {
                    echo "✓ Migrations appliquées: " . $result['details']['upgrade_result']['migrations_count'] . "\n";
                }
            } else {
                echo "❌ TEST DE MISE À NIVEAU ÉCHOUÉ\n";
                echo "✗ Erreur: " . $result['error'] . "\n";
                exit(1);
            }

        } catch (Exception $e) {
            echo "❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Test de performance des migrations
     */
    private function testPerformance() {
        global $db, $log;

        echo "⚡ TEST DE PERFORMANCE DES MIGRATIONS\n";
        echo "====================================\n\n";

        try {
            $testManager = new TestManager($db, $log);
            $result = $testManager->testMigrationPerformance();

            echo "\n=== RÉSULTATS DU TEST DE PERFORMANCE ===\n";

            if ($result['success']) {
                echo "✅ TEST DE PERFORMANCE TERMINÉ\n\n";

                $totalTime = 0;
                $slowMigrations = [];

                foreach ($result['performance_data'] as $data) {
                    $totalTime += $data['execution_time'];

                    if ($data['execution_time'] > 1.0) {
                        $slowMigrations[] = $data;
                    }

                    $status = $data['success'] ? '✓' : '✗';
                    $time = number_format($data['execution_time'], 3);
                    echo "{$status} {$data['version']}: {$time}s\n";
                }

                echo "\n=== RÉSUMÉ ===\n";
                echo "Temps total: " . number_format($totalTime, 3) . "s\n";
                echo "Migrations lentes (>1s): " . count($slowMigrations) . "\n";

                if (!empty($slowMigrations)) {
                    echo "\n⚠️  Migrations lentes détectées:\n";
                    foreach ($slowMigrations as $data) {
                        echo "  - {$data['version']}: " . number_format($data['execution_time'], 3) . "s\n";
                    }
                }

            } else {
                echo "❌ TEST DE PERFORMANCE ÉCHOUÉ\n";
                echo "✗ Erreur: " . $result['error'] . "\n";
                exit(1);
            }

        } catch (Exception $e) {
            echo "❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

// Point d'entrée
try {
    $cli = new UpgradeCLI();
    $cli->run($argv);
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}
