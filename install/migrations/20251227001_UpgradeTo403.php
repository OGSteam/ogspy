<?php
/**
 * Migration Mise a jour de la structure pour la version 4.0.3
 */
class Migration_20251227001_UpgradeTo403 {
    public function getVersion(): string {
        return '20251227001';
    }

    public function getDescription(): string {
        return 'Mise à jour vers OGSpy 4.0.3';
    }

    public function up(): string {

        global $table_prefix, $db;

          $structureFile = __DIR__ . '/../schemas/ogspy_20251227001_UpgradeTo403.sql';
     

        $combinedSQL = '';
        if (file_exists($structureFile)) {
            $sql = file_get_contents($structureFile);
            $combinedSQL .= $sql;
        }
        return $combinedSQL;


    }



    public function down($db): void {
        // Optionnel : suppression des tables créées
        // À compléter si besoin
    }
}
