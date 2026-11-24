<?php

/**
 * Adds current and daily historical life-form resource observations.
 */
class Migration_20261124001_AddLifeformResources
{
    public function getVersion(): string
    {
        return '20261124001';
    }

    public function getDescription(): string
    {
        return 'Add life-form population and food storage';
    }

    public function up(): string
    {
        return $this->readSchema('ogspy_20261124001_AddLifeformResources.sql');
    }

    public function canRollback(): bool
    {
        return true;
    }

    public function down(): string
    {
        return $this->readSchema('ogspy_20261124001_AddLifeformResources_down.sql');
    }

    private function readSchema(string $filename): string
    {
        $path = __DIR__ . '/../schemas/' . $filename;
        if (!file_exists($path)) {
            throw new MigrationRollbackException("Migration schema not found: {$path}");
        }

        return file_get_contents($path);
    }
}
