<?php
/**
 * Initial OGSpy migration: creates schema and inserts base data.
 */
class Migration_00000000000_Initial {
    public function getVersion(): string {
        return '00000000000';
    }

    public function getDescription(): string {
        return 'Initialize OGSpy schema and data';
    }

    public function up(): string {

        $structureFile = __DIR__ . '/../schemas/ogspy_structure.sql';
        $dataFile = __DIR__ . '/../schemas/ogspy_init-data.sql';

        $combinedSQL = '';
        if (file_exists($structureFile)) {
            $sql = file_get_contents($structureFile);
            $combinedSQL .= $sql;
        }
        if (file_exists($dataFile)) {
            $sql = file_get_contents($dataFile);
            $combinedSQL .= "\n" . $sql;
        }

        return $combinedSQL;
    }

    public function canRollback(): bool {
        return false;
    }

    public function down($db = null): void {
        throw new RuntimeException("Rollback is not supported for migration 00000000000 (Initial schema).");
    }
}
