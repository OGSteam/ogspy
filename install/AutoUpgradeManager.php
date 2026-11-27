<?php

/**
 * Auto-Upgrade Manager - Upgrade automatique silencieuse
 * @package OGSpy
 * @subpackage install
 */

require_once __DIR__ . '/ConfigGenerator.php';

class AutoUpgradeManager {
    private $migrationManager;
    private $logger;
    private $lockFile;
    private $maxExecutionTime = 60; // 1 minute max
    private $db;
    private $tablePrefix;
    private $targetVersion;

    public function __construct($db, $logger = null, $table_prefix = 'ogspy_', $target_version = null) {
        if (!$logger) {
            throw new InvalidArgumentException("Logger requis pour AutoUpgradeManager");
        }

        $this->db = $db;
        $this->tablePrefix = $table_prefix;

        // Si aucune version cible n'est fournie, utiliser la version globale OGSpy
        if ($target_version === null) {
            global $ogspy_version;
            if (isset($ogspy_version)) {
                $this->targetVersion = $ogspy_version;
            } else {
                // Fallback : charger depuis version.php
                if (file_exists(__DIR__ . '/version.php')) {
                    require_once __DIR__ . '/version.php';
                    $this->targetVersion = $ogspy_version ?? null;
                }
            }
        } else {
            $this->targetVersion = $target_version;
        }

        $this->migrationManager = new MigrationManager($db, $logger, $table_prefix);
        $this->logger = $logger;
        $this->lockFile = dirname(__DIR__) . '/cache/upgrade.lock';
    }

    /**
     * Verifies si une update est nécessaire and l'exécute automatiquement
     */
    public function checkAndUpgrade() {
        // Vérifie s'il y a des migrations en attente
        $pendingMigrations = $this->migrationManager->getPendingMigrations();

        $this->logger->info("AutoUpgrade: Migrations en attente détectées: " . count($pendingMigrations));
        foreach ($pendingMigrations as $version => $migration) {
            $this->logger->info("  - {$version}: {$migration['description']}");
        }

        // Vérifie si seule une synchronisation de version est nécessaire
        $versionSyncNeeded = $this->isVersionSyncNeeded();

        // Vérifie si un upgrade est déjà en cours
        if ($this->isUpgradeInProgress()) {
            return ['status' => 'in_progress', 'message' => 'Mise à jour en cours...'];
        }

        // Lance la mise à jour automatique
        return $this->runAutoUpgrade($pendingMigrations, $versionSyncNeeded);
    }

    /**
     * Executes la update automatique
     */
    private function runAutoUpgrade($migrations, $versionSyncNeeded = false) {
        $startTime = time();

        try {
            // Crée le fichier de verrouillage
            $this->createLock();

            $this->logger->info("=== DÉBUT AUTO-UPGRADE ===");
            $this->logger->info("Migrations à exécuter: " . count($migrations));

            // Vérifie si on peut raisonnablement exécuter toutes les migrations
            if (count($migrations) > 10) {
                $this->logger->warning("Beaucoup de migrations en attente (" . count($migrations) . "), considérez l'exécution manuelle");
            }

            // Exécute les migrations
            $results = $this->migrationManager->runPendingMigrations(false);

                        // Vérifier si c'est uniquement une synchronisation de version
            $versionSyncOnly = empty($migrations) && $versionSyncNeeded;
            
            $successful = array_filter($results, function($r, $k) { 
                return $k !== 'version_sync' && isset($r['success']) && $r['success']; 
            }, ARRAY_FILTER_USE_BOTH);
            $failed = array_filter($results, function($r, $k) { 
                return $k !== 'version_sync' && isset($r['success']) && !$r['success']; 
            }, ARRAY_FILTER_USE_BOTH);

            if (!empty($failed)) {
                $this->logger->error("ERREUR: " . count($failed) . " migration(s) échouée(s)");
                foreach ($failed as $version => $result) {
                    $this->logger->error("  - {$version}: {$result['error']}");
                }

                $this->releaseLock();
                return [
                    'status' => 'error',
                    'message' => 'Erreur lors de la mise à jour',
                    'details' => $failed
                ];
            }

            // TOUTES LES MIGRATIONS ONT RÉUSSI - Mettre à jour la version applicative
            if ($this->targetVersion) {
                $this->updateApplicationVersion($this->targetVersion);
            }

            // Nettoyage du cache
            $this->clearCache();

            $executionTime = time() - $startTime;
            $newVersion = $this->migrationManager->getCurrentDbVersion();

            $this->logger->info("SUCCÈS: " . count($successful) . " migration(s) réussie(s)");
            $this->logger->info("Nouvelle version DB: {$newVersion}");
            if ($this->targetVersion) {
                $this->logger->info("Version applicative mise à jour: {$this->targetVersion}");
            }
            $this->logger->info("Temps d'exécution: {$executionTime}s");
            $this->logger->info("=== FIN AUTO-UPGRADE ===");

            $this->releaseLock();

            return [
                'status' => 'success',
                'message' => $versionSyncOnly ? 'Version synchronisée automatiquement' : 'Mise à jour automatique réussie',
                'version' => $newVersion,
                'app_version' => $this->targetVersion,
                'migrations_count' => count($successful),
                'version_sync_only' => $versionSyncOnly,
                'execution_time' => $executionTime
            ];

        } catch (Exception $e) {
            $this->logger->critical("ERREUR CRITIQUE: " . $e->getMessage());
            $this->logger->critical("=== ÉCHEC AUTO-UPGRADE ===");

            $this->releaseLock();

            return [
                'status' => 'critical_error',
                'message' => 'Erreur critique lors de la mise à jour',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Updates the application version in the database after successful migrations.
     */
    private function updateApplicationVersion($version) {
        try {
            $this->logger->info("=== APPLICATION VERSION UPDATE ===");
            $this->logger->info("Target version: {$version}");

            // Vérifier la version actuelle avant mise à jour
            $currentVersionQuery = "SELECT value FROM {$this->tablePrefix}config WHERE name = 'version'";
            $this->logger->debug("Current version verification query: {$currentVersionQuery}");

            $result = $this->db->sql_query($currentVersionQuery);
            $currentVersion = null;
            if ($this->db->sql_numrows($result) > 0) {
                $row = $this->db->sql_fetch_assoc($result);
                $currentVersion = $row['value'];
                $this->logger->info("Current version found: {$currentVersion}");
            } else {
                $this->logger->info("No application version found in database (first installation)");
            }

            // Effectuer la mise à jour via ConfigGenerator
            $configGenerator = new ConfigGenerator();
            $this->logger->info("Calling ConfigGenerator->setApplicationVersion()");
            $configGenerator->setApplicationVersion($this->db, $this->tablePrefix, $version);

            // Vérifier que la mise à jour a bien fonctionné
            $verificationQuery = "SELECT value FROM {$this->tablePrefix}config WHERE name = 'version'";
            $this->logger->debug("Post-update verification query: {$verificationQuery}");

            $verificationResult = $this->db->sql_query($verificationQuery);
            if ($this->db->sql_numrows($verificationResult) > 0) {
                $verificationRow = $this->db->sql_fetch_assoc($verificationResult);
                $newVersionInDb = $verificationRow['value'];

                if ($newVersionInDb === $version) {
                    $this->logger->info("✓ Application version updated successfully: {$currentVersion} → {$newVersionInDb}");
                } else {
                    $this->logger->error("✗ Version update failed: expected '{$version}', found '{$newVersionInDb}'");
                    throw new Exception("Application version was not properly updated");
                }
            } else {
                $this->logger->error("✗ Unable to verify version after update");
                throw new Exception("Unable to verify application version after update");
            }

            $this->logger->info("=== END APPLICATION VERSION UPDATE ===");

        } catch (Exception $e) {
            $this->logger->error("Error during application version update: " . $e->getMessage());
            $this->logger->error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Verifies si un upgrade est en cours
     */
    private function isUpgradeInProgress() {
        if (!file_exists($this->lockFile)) {
            return false;
        }

        $lockTime = filemtime($this->lockFile);
        $currentTime = time();

        // Si le lock est trop ancien (> 5 minutes), on le supprime
        if (($currentTime - $lockTime) > 300) {
            unlink($this->lockFile);
            return false;
        }

        return true;
    }

    /**
     * Creates le fichier of verrouillage
     */
    private function createLock() {
        $lockDir = dirname($this->lockFile);
        if (!is_dir($lockDir)) {
            mkdir($lockDir, 0755, true);
        }

        file_put_contents($this->lockFile, json_encode([
            'start_time' => time(),
            'pid' => getmypid(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
        ]));
    }

    /**
     * Deletes le fichier of verrouillage
     */
    private function releaseLock() {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }

    /**
     * Cleanup of the cache
     */
    private function clearCache() {
        $cacheFiles = glob(dirname(__DIR__) . '/cache/*.php');
        $cleared = 0;

        foreach ($cacheFiles as $file) {
            if (basename($file) !== 'upgrade.lock' && unlink($file)) {
                $cleared++;
            }
        }

        $this->logger->info("Cache nettoyé: {$cleared} fichier(s) supprimé(s)");
        return $cleared;
    }

    /**
     * Verifies si the migrations peuvent être exécutées automatiquement
     */
    public function canAutoUpgrade() {
        global $server_config;
        // Vérifie les permissions d'écriture
        $cacheDir = dirname(__DIR__) . '/cache';
        if (!is_writable($cacheDir)) {
            return false;
        }
        // Vérifie que nous ne sommes pas en mode maintenance
        if (isset($server_config['server_active']) && $server_config['server_active'] == 0) {
            return false;
        }
        return true;
    }

    /**
     * Mode of secours : force la update même en cas of problème
     */
    public function forceUpgrade() {
        $this->releaseLock(); // Supprime les verrous existants
        $pendingMigrations = $this->migrationManager->getPendingMigrations();

        if (empty($pendingMigrations)) {
            return ['status' => 'up_to_date', 'message' => 'Aucune migration nécessaire'];
        }

        return $this->runAutoUpgrade($pendingMigrations, false);
    }

    /**
     * Verifies si une synchronisation of version est nécessaire
     */
    public function isVersionSyncNeeded() {
        try {
            if (!$this->targetVersion) {
                return false;
            }

            // Vérifier la version actuelle en base
            $currentVersionQuery = "SELECT value FROM {$this->tablePrefix}config WHERE name = 'version'";
            $result = $this->db->sql_query($currentVersionQuery);
            
            if ($this->db->sql_numrows($result) > 0) {
                $row = $this->db->sql_fetch_assoc($result);
                $currentVersion = $row['value'];
                
                $this->logger->info("Version sync check: DB='{$currentVersion}', Target='{$this->targetVersion}'");
                
                return $currentVersion !== $this->targetVersion;
            }
            
            return false;
        } catch (Exception $e) {
            $this->logger->error("Error checking version sync need: " . $e->getMessage());
            return false;
        }
    }
}
