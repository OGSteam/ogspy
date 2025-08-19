<?php

/**
 * Configuration du système de migrations OGSpy
 * @package OGSpy
 * @subpackage install
 */

return [
    // Table de suivi des migrations
    'migrations_table' => (isset($table_prefix) ? $table_prefix : 'ogspy_') . 'migrations',

    // Chemin vers les fichiers de migration - correction du chemin
    'migrations_path' => __DIR__ . '/../migrations',

    // Version de la base de données pour compatibilité
    'db_version_format' => 'YmdHis', // Format: YYYYMMDDHHMMSS (11 chiffres)

    // Configuration des migrations
    'max_execution_time' => 300, // 5 minutes max par migration
    'transaction_enabled' => true,

    // Logging
    'log_migrations' => true,
    'log_failed_migrations' => true,
];
