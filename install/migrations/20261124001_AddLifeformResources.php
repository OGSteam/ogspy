<?php

require_once __DIR__ . '/../SchemaFileMigration.php';

/**
 * Adds current and daily historical life-form resource observations.
 */
class Migration_20261124001_AddLifeformResources extends SchemaFileMigration // NOSONAR - migration discovery requires this versioned class name
{
    public function getVersion(): string
    {
        return '20261124001';
    }

    public function getDescription(): string
    {
        return 'Add life-form population and food storage';
    }

}
