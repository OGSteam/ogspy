<?php

/**
 * Auto-Upgrade Manager - Mise à jour automatique silencieuse
 * @package OGSpy
 * @subpackage install
 */

class AutoUpgradeManager {
    private $migrationManager;
    private $logger;
    private $lockFile;
    private $maxExecutionTime = 60; // 1 minute max

    public function __construct($db, $logger = null, $table_prefix = 'ogspy_') {
        if (!$logger) {
            throw new InvalidArgumentException("Logger requis pour AutoUpgradeManager");
        }

        $this->migrationManager = new MigrationManager($db, $logger, $table_prefix);
        $this->logger = $logger;
        $this->lockFile = dirname(__DIR__) . '/cache/upgrade.lock';
    }

    /**
     * Vérifie si une mise à jour est nécessaire et l'exécute automatiquement
     */
    public function checkAndUpgrade() {
        // Vérifie s'il y a des migrations en attente
        $pendingMigrations = $this->migrationManager->getPendingMigrations();

        $this->logger->info("AutoUpgrade: Migrations en attente détectées: " . count($pendingMigrations));
        foreach ($pendingMigrations as $version => $migration) {
            $this->logger->info("  - {$version}: {$migration['description']}");
        }

        if (empty($pendingMigrations)) {
            return ['status' => 'up_to_date', 'message' => 'Base de données à jour'];
        }

        // Vérifie si un upgrade est déjà en cours
        if ($this->isUpgradeInProgress()) {
            return ['status' => 'in_progress', 'message' => 'Mise à jour en cours...'];
        }

        // Lance la mise à jour automatique
        return $this->runAutoUpgrade($pendingMigrations);
    }

    /**
     * Exécute la mise à jour automatique
     */
    private function runAutoUpgrade($migrations) {
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

            $successful = array_filter($results, function($r) { return $r['success']; });
            $failed = array_filter($results, function($r) { return !$r['success']; });

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

            // Nettoyage du cache
            $this->clearCache();

            $executionTime = time() - $startTime;
            $newVersion = $this->migrationManager->getCurrentDbVersion();

            $this->logger->info("SUCCÈS: " . count($successful) . " migration(s) réussie(s)");
            $this->logger->info("Nouvelle version DB: {$newVersion}");
            $this->logger->info("Temps d'exécution: {$executionTime}s");
            $this->logger->info("=== FIN AUTO-UPGRADE ===");

            $this->releaseLock();

            return [
                'status' => 'success',
                'message' => 'Mise à jour automatique réussie',
                'version' => $newVersion,
                'migrations_count' => count($successful),
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
     * Vérifie si un upgrade est en cours
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
     * Crée le fichier de verrouillage
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
     * Supprime le fichier de verrouillage
     */
    private function releaseLock() {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }

    /**
     * Nettoyage du cache
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
     * Vérifie si les migrations peuvent être exécutées automatiquement
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
     * Mode de secours : force la mise à jour même en cas de problème
     */
    public function forceUpgrade() {
        $this->releaseLock(); // Supprime les verrous existants
        $pendingMigrations = $this->migrationManager->getPendingMigrations();

        if (empty($pendingMigrations)) {
            return ['status' => 'up_to_date', 'message' => 'Aucune migration nécessaire'];
        }

        return $this->runAutoUpgrade($pendingMigrations);
    }
}
