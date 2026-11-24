<?php

require_once __DIR__ . '/../SchemaFileMigration.php';

/**
 * Adds the planet-specific Interstellar Anomaly Scanner level.
 */
class Migration_20261124002_AddAnomalyScanner extends SchemaFileMigration // NOSONAR - migration discovery requires this versioned class name
{
    public function getVersion(): string
    {
        return '20261124002';
    }

    public function getDescription(): string
    {
        return 'Add Interstellar Anomaly Scanner level';
    }
}
