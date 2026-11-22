<?php

/**
 * Adds normalized per-planet storage for life-form buildings and research.
 */
class Migration_20261122001_AddLifeformData
{
    public function getVersion(): string
    {
        return '20261122001';
    }

    public function getDescription(): string
    {
        return 'Add life-form building and research storage';
    }

    public function up(): string
    {
        return $this->readSchema('ogspy_20261122001_AddLifeformData.sql');
    }

    public function canRollback(): bool
    {
        return true;
    }

    public function down(): string
    {
        return $this->readSchema('ogspy_20261122001_AddLifeformData_down.sql');
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
