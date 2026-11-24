<?php

require_once __DIR__ . '/../SchemaFileMigration.php';

/**
 * Adds normalized per-planet storage for life-form buildings and research.
 */
class Migration_20261122001_AddLifeformData extends SchemaFileMigration // NOSONAR - migration discovery requires this versioned class name
{
    public function getVersion(): string
    {
        return '20261122001';
    }

    public function getDescription(): string
    {
        return 'Add life-form building and research storage';
    }

}
