<?php

/**
 * Base class for reversible migrations backed by matching SQL schema files.
 */
abstract class SchemaFileMigration
{
    abstract public function getVersion(): string;

    abstract public function getDescription(): string;

    public function up(): string
    {
        return $this->readSchema($this->schemaBaseName() . '.sql');
    }

    public function canRollback(): bool
    {
        return true;
    }

    public function down(): string
    {
        return $this->readSchema($this->schemaBaseName() . '_down.sql');
    }

    protected function readSchema(string $filename): string
    {
        $path = __DIR__ . '/schemas/' . $filename;
        if (!file_exists($path)) {
            throw new MigrationRollbackException("Migration schema not found: {$path}");
        }

        return file_get_contents($path);
    }

    private function schemaBaseName(): string
    {
        return 'ogspy_' . substr(static::class, strlen('Migration_'));
    }
}
