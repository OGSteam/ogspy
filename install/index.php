<?php global $ui_lang, $lang;

/**
 * Fichier d'installation d'OGSpy 4.0
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 4.0
 */

define("IN_SPYOGAME", true);
define("INSTALL_IN_PROGRESS", true);

// SÉCURITÉ : Vérifier si l'installation est terminée
$installCompleted = file_exists("../parameters/id.php");
$installLocked = file_exists("install.lock");

// Si installation terminée ET verrouillée, bloquer l'accès web
if ($installCompleted && $installLocked) {
    http_response_code(403);
    echo "<!DOCTYPE html>
    <html><head><title>Installation verrouillée</title></head>
    <body style='font-family: Arial; text-align: center; margin-top: 100px;'>
        <h1>🔒 Installation verrouillée</h1>
        <p>L'installation d'OGSpy est terminée et verrouillée pour des raisons de sécurité.</p>
        <p>Pour accéder à l'interface d'installation :</p>
        <ol style='text-align: left; display: inline-block;'>
            <li>Supprimez le fichier <code>install/install.lock</code></li>
            <li>Ou utilisez l'outil CLI : <code>php upgrade_cli.php unlock-install</code></li>
        </ol>
        <p><a href='../index.php'>Retour au site principal</a></p>
    </body></html>";
    exit;
}

require_once("../common.php");
require_once("ConfigGenerator.php");
require_once("MigrationManager.php");
require_once("AutoUpgradeManager.php");

if (!isset($ogspy_version)) {
    require_once("./version.php");
}

// Traitement des actions
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$errors = [];
$success = false;

// Gestion des actions du formulaire
if ($_POST) {
    try {
        if (isset($_POST['generate_config'])) {
            $configGenerator = new ConfigGenerator();

            $dbConfig = [
                'host' => $_POST['db_host'],
                'user' => $_POST['db_user'],
                'password' => $_POST['db_password'],
                'database' => $_POST['db_name'],
                'table_prefix' => $_POST['table_prefix'] ?? 'ogspy_'
            ];

            if ($configGenerator->generateIdFile($dbConfig)) {
                $success = "Configuration générée avec succès !";
                // Pas de redirection - la page se rechargera automatiquement et affichera l'étape suivante
            }
        }

        if (isset($_POST['test_connection'])) {
            $configGenerator = new ConfigGenerator();

            $dbConfig = [
                'host' => $_POST['db_host'],
                'user' => $_POST['db_user'],
                'password' => $_POST['db_password'],
                'database' => $_POST['db_name']
            ];

            // Test uniquement sans génération
            $configGenerator->testDbConnection($dbConfig);
            $success = "Connexion à la base de données réussie !";
        }

        if (isset($_POST['run_migrations'])) {
            // Charger la configuration et créer une connexion directe
            require_once("../parameters/id.php");
            $migrationDb = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);

            if ($migrationDb && $migrationDb->db_connect_id) {
                $migrationManager = new MigrationManager($migrationDb, $log);
                $results = $migrationManager->runPendingMigrations(false);

                $successful = array_filter($results, function($r) { return $r['success']; });
                $failed = array_filter($results, function($r) { return !$r['success']; });

                if (empty($failed)) {
                    $success = count($successful) . " migration(s) exécutée(s) avec succès !";
                } else {
                    $errors[] = count($failed) . " migration(s) échouée(s)";
                    foreach ($failed as $failedMigration) {
                        if (isset($failedMigration['error'])) {
                            $errors[] = "Migration {$failedMigration['version']}: " . $failedMigration['error'];
                        }
                    }
                }
            } else {
                $errors[] = "Base de données non accessible pour l'exécution des migrations";
            }
        }

        if (isset($_POST['lock_install'])) {
            // Création du fichier de verrouillage
            file_put_contents("install.lock", "");
            $success = "Installation verrouillée avec succès. L'accès web à l'interface d'installation est désormais bloqué.";
        }

        if (isset($_POST['create_admin'])) {
            // Charger la configuration et créer une connexion directe
            require_once("../parameters/id.php");
            $adminDb = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);

            if ($adminDb && $adminDb->db_connect_id) {
                $adminUser = $_POST['admin_user'];
                $adminPass = $_POST['admin_pass'];
                $adminEmail = $_POST['admin_email'] ?? '';

                // Vérification que l'utilisateur n'existe pas déjà
                $checkUser = $adminDb->sql_query("SELECT COUNT(*) as count FROM {$table_prefix}user WHERE name = '" . $adminDb->sql_escape_string($adminUser) . "'");
                $userExists = $adminDb->sql_fetch_assoc($checkUser);

                if ($userExists['count'] == 0) {
                    // Création de l'utilisateur admin - utiliser le bon format de champs pour OGSpy
                    $hashedPass = password_hash($adminPass, PASSWORD_DEFAULT);
                    $currentTime = time();

                    $sql = "INSERT INTO {$table_prefix}user (name, password_s, email, admin, coadmin, active, regdate, lastvisit)
                            VALUES ('" . $adminDb->sql_escape_string($adminUser) . "',
                                   '" . $adminDb->sql_escape_string($hashedPass) . "',
                                   '" . $adminDb->sql_escape_string($adminEmail) . "',
                                   1, 1, 1, {$currentTime}, {$currentTime})";

                    if ($adminDb->sql_query($sql)) {
                        $success = "Compte administrateur créé avec succès ! Utilisateur: $adminUser";
                        header("Location: index.php?step=complete");
                        exit;
                    } else {
                        $errors[] = "Erreur lors de la création du compte administrateur";
                    }
                } else {
                    $errors[] = "Un utilisateur avec ce nom existe déjà";
                }
            } else {
                $errors[] = "Base de données non accessible pour la création de l'administrateur";
            }
        }

    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Déterminer l'état du système
$configExists = file_exists("../parameters/id.php");
$dbConnected = false;
$pendingMigrations = [];

if ($configExists) {
    try {
        // Charger la configuration de base de données
        require_once("../parameters/id.php");

        // Créer une connexion directe pour l'installation en utilisant le Singleton
        require_once("../includes/mysql.php");
        $testDb = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);

        if ($testDb->db_connect_id) {
            // Test simple de la connexion
            $testResult = $testDb->sql_query("SELECT 1 as test");
            if ($testResult && $testDb->sql_fetch_assoc($testResult)) {
                $dbConnected = true;

                // Créer le gestionnaire de migrations avec la connexion directe
                $migrationManager = new MigrationManager($testDb, $log);
                $pendingMigrations = $migrationManager->getPendingMigrations();
            }
        }
    } catch (Exception $e) {
        // Connexion DB échouée - les variables restent à false/vide
        $errors[] = "Erreur de connexion DB: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Installation OGSpy 4.0</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="language" content="fr">
    <link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="icon" type="image/icon" href="../favicon.ico">
    <style>
        .install-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
        .warning { background-color: #fff3cd; color: #856404; border-color: #ffeaa7; }
    </style>
</head>

<body>

<table width="80%" align="center" cellpadding="20">
    <tr>
        <td>
            <table align="center" width="100%">
                <tr>
                    <td align="center" height="70">
                        <img src="../images/logo.png" alt="OGSpy" />
                        <h1>Installation OGSpy 4.0</h1>
                    </td>
                </tr>

                <?php if (version_compare(PHP_VERSION, "7.4.0") < 0): ?>
                <tr>
                    <td>
                        <div class="install-section error">
                            <h3>❌ Version PHP incompatible</h3>
                            <p>PHP 7.4 minimum requis. Version actuelle : <?= PHP_VERSION ?></p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>

                <!-- Messages de retour -->
                <?php if ($success): ?>
                <tr>
                    <td>
                        <div class="install-section success">
                            <h3>✅ Succès</h3>
                            <p><?= htmlspecialchars($success) ?></p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                <tr>
                    <td>
                        <div class="install-section error">
                            <h3>❌ Erreurs détectées</h3>
                            <?php foreach ($errors as $error): ?>
                                <p><?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- État du système -->
                <tr>
                    <td>
                        <div class="install-section info">
                            <h3>📊 État du système</h3>
                            <ul>
                                <li>Configuration DB : <?= $configExists ? '✅ Présente' : '❌ Manquante' ?></li>
                                <li>Connexion DB : <?= $dbConnected ? '✅ Active' : '❌ Inactive' ?></li>
                                <li>Migrations en attente : <?= count($pendingMigrations) ?></li>
                                <li>Version PHP : <?= PHP_VERSION ?> ✅</li>
                            </ul>
                        </div>
                    </td>
                </tr>

                <!-- Étape 1 : Configuration de la base de données -->
                <?php if (!$configExists): ?>
                <tr>
                    <td>
                        <div class="install-section">
                            <h3>🔧 Étape 1 : Configuration de la base de données</h3>
                            <?php
                            $configGenerator = new ConfigGenerator();
                            echo $configGenerator->renderConfigForm($errors);
                            ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Étape 2 : Migrations -->
                <?php if ($configExists && $dbConnected): ?>
                <tr>
                    <td>
                        <div class="install-section">
                            <h3>🔄 Étape 2 : Migrations de base de données</h3>

                            <?php if (empty($pendingMigrations)): ?>
                                <p class="success">✅ Base de données à jour !</p>
                            <?php else: ?>
                                <div class="warning">
                                    <p><strong><?= count($pendingMigrations) ?> migration(s) en attente :</strong></p>
                                    <ul>
                                        <?php foreach ($pendingMigrations as $migration): ?>
                                            <li><?= $migration['version'] ?> - <?= $migration['description'] ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <form method="post">
                                    <input type="hidden" name="action" value="run_migrations">
                                    <input type="submit" name="run_migrations" value="Exécuter les migrations"
                                           onclick="return confirm('Êtes-vous sûr de vouloir exécuter les migrations ?')">
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Étape 3 : Création du compte administrateur -->
                <?php if ($configExists && $dbConnected && empty($pendingMigrations)): ?>
                <tr>
                    <td>
                        <div class="install-section">
                            <h3>👤 Étape 3 : Création du compte administrateur</h3>
                            <form method="post">
                                <table>
                                    <tr>
                                        <td><label for="admin_user">Nom d'utilisateur :</label></td>
                                        <td><input type="text" id="admin_user" name="admin_user" required></td>
                                    </tr>
                                    <tr>
                                        <td><label for="admin_pass">Mot de passe :</label></td>
                                        <td><input type="password" id="admin_pass" name="admin_pass" required></td>
                                    </tr>
                                    <tr>
                                        <td><label for="admin_email">Email (optionnel) :</label></td>
                                        <td><input type="email" id="admin_email" name="admin_email"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align: center;">
                                            <input type="submit" name="create_admin" value="Créer le compte administrateur">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Étape 4 : Finalisation -->
                <?php if ($configExists && $dbConnected && empty($pendingMigrations) && isset($_GET['step']) && $_GET['step'] == 'complete'): ?>
                <tr>
                    <td>
                        <div class="install-section success">
                            <h3>🎉 Installation terminée !</h3>
                            <p>OGSpy 4.0 est prêt à être utilisé.</p>

                            <!-- Option de verrouillage automatique -->
                            <form method="post" style="margin: 20px 0;">
                                <input type="hidden" name="action" value="lock_install">
                                <p>
                                    <input type="submit" name="lock_install" value="🔒 Verrouiller l'installation"
                                           onclick="return confirm('Cela empêchera l\'accès web à l\'installation. Voulez-vous continuer ?')">
                                </p>
                                <p><small>Recommandé pour la sécurité. Vous pourrez toujours utiliser les outils CLI.</small></p>
                            </form>

                            <p><a href="../index.php">🚀 Accéder à OGSpy</a></p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Outils de diagnostic -->
                <tr>
                    <td>
                        <div class="install-section">
                            <h3>🛠️ Outils de diagnostic</h3>
                            <p>Pour un diagnostic avancé, utilisez la ligne de commande :</p>
                            <code>php upgrade_cli.php status</code>
                        </div>
                    </td>
                </tr>

                <?php endif; ?>
            </table>
        </td>
    </tr>
</table>

<div id='barre'>
    <table>
        <tr align="center">
            <td>
                <div style="text-align: center;font-size: x-small;"><i><b>OGSpy</b> is an <b>OGSteam Software</b>
                        (c) 2005-2025</i></div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
