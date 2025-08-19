<?php

/**
 * Générateur de fichier de configuration id.php
 * @package OGSpy
 * @subpackage install
 */

class ConfigGenerator {
    private $configPath;
    private $templatePath;

    public function __construct() {
        $this->configPath = dirname(__DIR__) . '/parameters/id.php';
        $this->templatePath = __DIR__ . '/config/id_template.php';
    }

    /**
     * Génère le fichier id.php avec les paramètres de base de données
     */
    public function generateIdFile($dbConfig) {
        $template = $this->getTemplate();

        // Validation des paramètres
        $this->validateDbConfig($dbConfig);

        // Remplacement des placeholders
        $content = str_replace([
            '{{DB_HOST}}',
            '{{DB_USER}}',
            '{{DB_PASSWORD}}',
            '{{DB_NAME}}',
            '{{TABLE_PREFIX}}',
            '{{GENERATION_DATE}}',
            '{{GENERATOR_VERSION}}'
        ], [
            $dbConfig['host'],
            $dbConfig['user'],
            $dbConfig['password'],
            $dbConfig['database'],
            $dbConfig['table_prefix'] ?? 'ogspy_',
            date('Y-m-d H:i:s'),
            '2.0'
        ], $template);

        // Sauvegarde du fichier
        if (!$this->saveConfig($content)) {
            throw new Exception("Impossible d'écrire le fichier de configuration");
        }

        return true;
    }

    /**
     * Valide la configuration de base de données
     */
    private function validateDbConfig($config) {
        $required = ['host', 'user', 'password', 'database'];

        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw new Exception("Paramètre requis manquant: {$field}");
            }
        }

        // Test de connexion
        $this->testDbConnection($config);
    }

    /**
     * Teste la connexion à la base de données
     */
    public function testDbConnection($config) {
        try {
            // Utilise MySQLi au lieu de PDO pour une meilleure compatibilité
            $mysqli = new mysqli(
                $config['host'],
                $config['user'],
                $config['password'],
                $config['database']
            );

            // Vérification des erreurs de connexion
            if ($mysqli->connect_error) {
                throw new Exception("Erreur de connexion: " . $mysqli->connect_error);
            }

            // Test du charset
            if (!$mysqli->set_charset("utf8")) {
                throw new Exception("Impossible de définir le charset UTF-8: " . $mysqli->error);
            }

            // Test simple
            $result = $mysqli->query("SELECT 1");
            if (!$result) {
                throw new Exception("Erreur lors du test de requête: " . $mysqli->error);
            }

            $mysqli->close();
            return true;

        } catch (mysqli_sql_exception $e) {
            throw new Exception("Connexion à la base de données échouée: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Erreur de test de connexion: " . $e->getMessage());
        }
    }

    /**
     * Récupère le template du fichier id.php
     */
    private function getTemplate() {
        if (file_exists($this->templatePath)) {
            return file_get_contents($this->templatePath);
        }

        // Template par défaut si le fichier n'existe pas
        return $this->getDefaultTemplate();
    }

    /**
     * Template par défaut pour id.php
     */
    private function getDefaultTemplate() {
        return '<?php
/**
 * Configuration de base de données OGSpy
 * Généré automatiquement le {{GENERATION_DATE}}
 * Générateur version {{GENERATOR_VERSION}}
 */

if (!defined("IN_SPYOGAME")) {
    die("Hacking attempt");
}

// Configuration de la base de données
$db_host = "{{DB_HOST}}";
$db_user = "{{DB_USER}}";
$db_password = "{{DB_PASSWORD}}";
$db_database = "{{DB_NAME}}";

// Préfixe des tables
$table_prefix = "{{TABLE_PREFIX}}";

// Configuration avancée pour compatibilité
define("DB_HOST", $db_host);
define("DB_USER", $db_user);
define("DB_PASSWORD", $db_password);
define("DB_NAME", $db_database);
define("DB_CHARSET", "utf8");
define("DB_COLLATE", "utf8_general_ci");
';
    }

    /**
     * Sauvegarde le fichier de configuration
     */
    private function saveConfig($content) {
        // Création du dossier config s\'il n\'existe pas
        $configDir = dirname($this->configPath);
        if (!is_dir($configDir)) {
            if (!mkdir($configDir, 0755, true)) {
                return false;
            }
        }

        // Sauvegarde avec verrous
        $tempFile = $this->configPath . '.tmp';

        if (file_put_contents($tempFile, $content, LOCK_EX) === false) {
            return false;
        }

        // Renommage atomique
        if (!rename($tempFile, $this->configPath)) {
            unlink($tempFile);
            return false;
        }

        // Permissions
        chmod($this->configPath, 0644);

        return true;
    }

    /**
     * Vérifie si le fichier id.php existe
     */
    public function configExists() {
        return file_exists($this->configPath);
    }

    /**
     * Sauvegarde le fichier actuel
     */
    public function backupConfig() {
        if (!$this->configExists()) {
            return true;
        }

        $backupPath = $this->configPath . '.backup.' . date('Y-m-d_H-i-s');
        return copy($this->configPath, $backupPath);
    }

    /**
     * Génère un formulaire interactif pour la configuration
     */
    public function renderConfigForm($errors = []) {
        ob_start();
        ?>
        <form method="post" action="">
            <?php if (!empty($errors)): ?>
                <div class="install-section error" style="margin-bottom: 20px;">
                    <h3>❌ Erreurs de configuration</h3>
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <table class="form-table">
                <tr>
                    <td><label for="db_host">Serveur de base de données :</label></td>
                    <td><input type="text" id="db_host" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required placeholder="localhost"></td>
                </tr>
                <tr>
                    <td><label for="db_user">Utilisateur :</label></td>
                    <td><input type="text" id="db_user" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required placeholder="root"></td>
                </tr>
                <tr>
                    <td><label for="db_password">Mot de passe :</label></td>
                    <td><input type="password" id="db_password" name="db_password" value="<?= htmlspecialchars($_POST['db_password'] ?? '') ?>" placeholder="••••••••"></td>
                </tr>
                <tr>
                    <td><label for="db_name">Nom de la base :</label></td>
                    <td><input type="text" id="db_name" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? 'ogspy') ?>" required placeholder="ogspy"></td>
                </tr>
                <tr>
                    <td><label for="table_prefix">Préfixe des tables :</label></td>
                    <td><input type="text" id="table_prefix" name="table_prefix" value="<?= htmlspecialchars($_POST['table_prefix'] ?? 'ogspy_') ?>" placeholder="ogspy_"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 20px;">
                        <input type="submit" name="test_connection" value="🔍 Tester la connexion" class="btn btn-secondary" style="margin-right: 10px;">
                        <input type="submit" name="generate_config" value="💾 Générer la configuration" class="btn">
                    </td>
                </tr>
            </table>
        </form>
        <?php
        return ob_get_clean();
    }
}
