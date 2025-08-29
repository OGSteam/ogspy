<?php global $ui_lang, $lang, $table_prefix;

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
$installCompleted = file_exists("../config/id.php");
$installLocked = file_exists("install.lock");

// Si installation terminée ET verrouillée, bloquer l'accès web
// SAUF si on affiche l'écran de finalisation
if ($installCompleted && $installLocked && !isset($_GET['step'])) {
    http_response_code(403);
    echo "<!DOCTYPE html>
    <html lang=\"fr\">
    <head>
        <title>Installation OGSpy - Verrouillée</title>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"../favicon.ico\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"installer.css\">
        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                max-width: 600px;
                text-align: center;
            }
            .content {
                padding: 40px 30px;
            }
        </style>
    </head>
    <body>
        <div class=\"container\">
            <div class=\"header\">
                <div class=\"lock-icon\">🔒</div>
                <h1>Installation Verrouillée</h1>
            </div>

            <div class=\"content\">
                <div class=\"message-section\">
                    <p><strong>L'installation d'OGSpy est terminée et verrouillée pour des raisons de sécurité.</strong></p>
                </div>

                <div class=\"instructions\">
                    <p><strong>Pour accéder à l'interface d'installation :</strong></p>
                    <ol>
                        <li>Supprimez le fichier <code>install/install.lock</code></li>
                        <li>Ou utilisez l'outil CLI : <code>php upgrade_cli.php unlock-install</code></li>
                    </ol>
                </div>

                <a href=\"../index.php\" class=\"btn\">🚀 Retour au site principal</a>
            </div>

            <div class=\"footer\">
                <p><strong>OGSpy</strong> is an <strong>OGSteam Software</strong> © 2005-2025</p>
            </div>
        </div>
    </body>
    </html>";
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
            require_once("../config/id.php");
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
            require_once("../config/id.php");
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

                    $sql = "INSERT INTO {$table_prefix}user (name, password_s, email, admin, coadmin, active, pwd_change, regdate, lastvisit)
                            VALUES ('" . $adminDb->sql_escape_string($adminUser) . "',
                                   '" . $adminDb->sql_escape_string($hashedPass) . "',
                                   '" . $adminDb->sql_escape_string($adminEmail) . "',
                                   1, 1, 1, 0, {$currentTime}, {$currentTime})";

                    if ($adminDb->sql_query($sql)) {
                        // Récupérer l'ID de l'utilisateur créé
                        $userId = $adminDb->db_connect_id->insert_id;

                        // Ajouter l'utilisateur au groupe Standard (ID 1)
                        $groupSql = "INSERT INTO {$table_prefix}user_group (user_id, group_id) VALUES ({$userId}, 1)";

                        if ($adminDb->sql_query($groupSql)) {
                            $success = "Compte administrateur créé avec succès ! Utilisateur: $adminUser (ajouté au groupe Standard)";
                        } else {
                            $success = "Compte administrateur créé avec succès ! Utilisateur: $adminUser (attention : erreur lors de l'ajout au groupe)";
                        }

                        // VERROUILLAGE AUTOMATIQUE après création de l'admin
                        file_put_contents("install.lock", "Installation completed on " . date('Y-m-d H:i:s'));

                        // Rediriger vers l'écran de finalisation
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
$configExists = file_exists("../config/id.php");
$dbConnected = false;
$pendingMigrations = [];
$adminExists = false; // Nouveau: vérifier si un admin existe

if ($configExists) {
    try {
        // Charger la configuration de base de données
        require_once("../config/id.php");

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

                // Vérifier si un compte administrateur existe déjà
                try {
                    $adminCheck = $testDb->sql_query("SELECT COUNT(*) as count FROM {$table_prefix}user WHERE admin = 1");
                    if ($adminCheck) {
                        $adminResult = $testDb->sql_fetch_assoc($adminCheck);
                        $adminExists = ($adminResult['count'] > 0);
                    }
                } catch (Exception $e) {
                    // Si la table n'existe pas encore, pas d'admin
                    $adminExists = false;
                }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="icon" type="image/icon" href="../favicon.ico">
    <link rel="stylesheet" type="text/css" href="installer.css">
</head>

<body>

<div class="container">
    <div class="header">
        <img src="../skin/OGSpy_skin/logos/logo.png" alt="OGSpy" />
        <h1>Installation OGSpy 4.0</h1>
    </div>

    <div class="content">
        <?php if (version_compare(PHP_VERSION, "7.4.0") < 0): ?>
        <div class="install-section error">
            <h3>❌ Version PHP incompatible</h3>
            <p>PHP 7.4 minimum requis. Version actuelle : <?= PHP_VERSION ?></p>
        </div>
        <?php else: ?>

        <!-- Indicateur de progression -->
        <div class="progress-steps">
            <div class="step <?= !$configExists ? 'active' : 'completed' ?>">
                <span>🔧</span> Configuration DB
            </div>
            <div class="step <?= ($configExists && $dbConnected && !empty($pendingMigrations)) ? 'active' : ($configExists && $dbConnected && empty($pendingMigrations) ? 'completed' : '') ?>">
                <span>🔄</span> Migrations
            </div>
            <div class="step <?= ($configExists && $dbConnected && empty($pendingMigrations)) ? 'active' : '' ?>">
                <span>👤</span> Administrateur
            </div>
            <div class="step <?= (isset($_GET['step']) && $_GET['step'] == 'complete') ? 'active' : '' ?>">
                <span>🎉</span> Finalisation
            </div>
        </div>

        <!-- Messages de retour -->
        <?php if ($success): ?>
        <div class="install-section success">
            <h3>✅ Succès</h3>
            <p><?= htmlspecialchars($success) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="install-section error">
            <h3>❌ Erreurs détectées</h3>
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- État du système -->
        <div class="install-section info">
            <h3>📊 État du système</h3>
            <div class="system-status">
                <li>Configuration DB : <?= $configExists ? '✅ Présente' : '❌ Manquante' ?></li>
                <li>Connexion DB : <?= $dbConnected ? '✅ Active' : '❌ Inactive' ?></li>
                <li>Migrations en attente : <?= count($pendingMigrations) ?></li>
                <li>Version PHP : <?= PHP_VERSION ?> ✅</li>
            </div>
        </div>

        <!-- Étape 1 : Configuration de la base de données -->
        <?php if (!$configExists): ?>
        <div class="install-section">
            <h3>🔧 Étape 1 : Configuration de la base de données</h3>
            <?php
            $configGenerator = new ConfigGenerator();
            echo $configGenerator->renderConfigForm($errors);
            ?>
        </div>
        <?php endif; ?>

        <!-- Étape 2 : Migrations -->
        <?php if ($configExists && $dbConnected && !$adminExists): ?>
        <div class="install-section">
            <h3>🔄 Étape 2 : Migrations de base de données</h3>

            <?php if (empty($pendingMigrations)): ?>
                <p class="success">✅ Base de données à jour !</p>
            <?php else: ?>
                <div class="migration-list warning">
                    <p><strong><?= count($pendingMigrations) ?> migration(s) en attente :</strong></p>
                    <ul>
                        <?php foreach ($pendingMigrations as $migration): ?>
                            <li><?= $migration['version'] ?> - <?= $migration['description'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <form method="post">
                    <input type="hidden" name="action" value="run_migrations">
                    <input type="submit" name="run_migrations" value="Exécuter les migrations" class="btn">
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Étape 3 : Création de l'administrateur -->
        <?php if ($configExists && $dbConnected && empty($pendingMigrations) && !$adminExists): ?>
        <div class="install-section">
            <h3>👤 Étape 3 : Création du compte administrateur</h3>
            <p>Créez le compte administrateur principal pour gérer OGSpy.</p>

            <form method="post" class="admin-form">
                <table class="form-table">
                    <tr>
                        <td style="width: 200px;">
                            <label for="admin_user">Nom d'utilisateur :</label>
                        </td>
                        <td>
                            <input type="text" id="admin_user" name="admin_user" required
                                   value="<?= htmlspecialchars($_POST['admin_user'] ?? 'admin') ?>"
                                   placeholder="Nom d'utilisateur de l'administrateur">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="admin_pass">Mot de passe :</label>
                        </td>
                        <td>
                            <input type="password" id="admin_pass" name="admin_pass" required
                                   placeholder="Mot de passe sécurisé">
                            <small style="color: #aaccff; font-size: 12px; display: block; margin-top: 5px;">
                                Minimum 8 caractères recommandé
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="admin_email">Email (optionnel) :</label>
                        </td>
                        <td>
                            <input type="email" id="admin_email" name="admin_email"
                                   value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>"
                                   placeholder="email@exemple.com">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" name="create_admin" value="Créer l'administrateur" class="btn">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php endif; ?>

        <!-- Étape 4 : Finalisation - Installation terminée -->
        <?php if ($configExists && $dbConnected && empty($pendingMigrations) && $adminExists): ?>
        <div class="install-section success">
            <h3>🎉 Installation réussie !</h3>
            <p><strong>Félicitations ! OGSpy a été installé avec succès sur votre serveur.</strong></p>

            <div class="install-section info" style="margin: 20px 0;">
                <h4>✅ Ce qui a été configuré :</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>✅ Connexion à la base de données établie</li>
                    <li>✅ Tables de base de données créées et mises à jour</li>
                    <li>✅ Compte administrateur configuré</li>
                    <li>✅ Installation sécurisée et verrouillée</li>
                </ul>
            </div>

            <div class="install-section info" style="margin: 20px 0;">
                <h4>🚀 Prochaines étapes :</h4>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li>Cliquez sur <strong>"Accéder à OGSpy"</strong> ci-dessous</li>
                    <li>Connectez-vous avec le compte administrateur que vous avez créé</li>
                    <li>Configurez Xtense pour récupérer les données du jeu</li>
                    <li>Commencez à utiliser OGSpy pour espionner l'univers !</li>
                </ol>
            </div>

            <div class="install-section warning" style="margin: 20px 0;">
                <h4>🔒 Sécurité :</h4>
                <p>L'interface d'installation a été automatiquement verrouillée pour votre sécurité.
                Pour refaire l'installation, supprimez le fichier <code>install/install.lock</code>
                ou utilisez les outils CLI.</p>
            </div>

            <p style="margin-top: 30px; text-align: center;">
                <a href="../index.php" class="btn" style="font-size: 16px; padding: 15px 30px;">
                    🚀 Accéder à OGSpy
                </a>
            </p>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>

    <div class="footer">
        <p><strong>OGSpy</strong> is an <strong>OGSteam Software</strong> © 2005-2025</p>
    </div>
</div>

</body>
</html>
