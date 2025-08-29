<?php

/**
 * Main file which do includes et set up all data for the application
 * @package OGSpy
 * @subpackage main
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https:///opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.8 */

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\CallbackHandler;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

//Appel de l'autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Loggers
$log = new Logger('OGSpy');
$log->pushHandler(new RotatingFileHandler(__DIR__ . '/logs/OGSpy.log', 365, Level::Debug));


$logSQL = new Logger('OGSpySQL');
$logSQL->pushHandler(new RotatingFileHandler(__DIR__ . '/logs/OGSpy-sql.log', 365, Level::Debug));

$logSlowSQL = new Logger('OGSpySlowSQL');
$logSlowSQL->pushHandler(new RotatingFileHandler(__DIR__ . '/logs/OGSpy-sql-slow.log', 365, Level::Debug));

$log->info("OGSpy started");

//Récupération des paramètres de connexion à la base de données
if (file_exists("config/id.php")) {
    require_once "config/id.php";
} else {
    if (!defined("OGSPY_INSTALLED") && !defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
        header("Location: install/index.php?lang=fr");
        exit();
    } elseif (file_exists('../config/id.php')) {
        require_once '../config/id.php';
    }
}

//Appel des fonctions

require_once "includes/functions.php";
require_once "includes/config.php";
require_once "includes/mysql.php";
require_once "includes/log.php";
require_once "includes/galaxy.php";
require_once "includes/user.php";
require_once "includes/usergroups.php";
require_once "includes/player.php";
require_once "includes/sessions.php";
require_once "includes/help.php";
require_once "includes/mod.php";
require_once "includes/ogame.php";
require_once "includes/chart_js.php";
require_once "includes/cache.php"; // Toujours inclure cache.php car nécessaire pour init_serverconfig()

if (defined("OGSPY_INSTALLED")) {
    require_once "includes/mail.php";
    require_once "includes/token.php";
}

$log->info("OGSpy Configured");

//Récupération des valeur GET, POST, COOKIE
extract($_GET, EXTR_PREFIX_ALL, "pub");
extract($_POST, EXTR_PREFIX_ALL, "pub");
extract($_COOKIE, EXTR_PREFIX_ALL, "pub");

foreach ($_GET as $secvalue) {
    if (!check_getvalue($secvalue)) {
        die("I don't like you...");
    }
}

foreach ($_POST as $secvalue) {
    if (!check_postvalue($secvalue)) {
        header("Location: index.php");
        die();
    }
}

$log->info("OGSpy Parameters loaded");

//Language File
if (!isset($ui_lang)) { // Checks the ui_lang value from config file
    $ui_lang = $pub_lang ?? "fr";
    //If no language is available in id.php file we take fr by default
}
require_once "lang/lang_main.php";

// ajout fichier clef unique
if (!defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
    if (file_exists('config/key.php')) {
        require_once 'config/key.php';
    } else {
        generate_key();
    }
}

$log->info("OGSpy Language loaded - " . $ui_lang);

//Connexion à la base de donnnées
if (!defined("INSTALL_IN_PROGRESS")) {
    // appel de l instance en cours
    $db = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);

    if (!$db->db_connect_id) {
        die("Impossible de se connecter à la base de données");
    }

    //Récupération et encodage de l'adresse ip
    $user_ip = encode_ip($_SERVER['REMOTE_ADDR']);

    $log->info("OGSpy Database connected - " . $db_host);

    // initialisation des variables en cache
    init_serverconfig();
    init_mod_cache();

    $log->info("OGSpy Cache loaded");

    if (!defined("UPGRADE_IN_PROGRESS")) {
        session();
        maintenance_action();
    }

    $log->info("OGSpy Session started");

    /* Exception Handler */
    $whoops = new Run;
    $whoops->allowQuit(true);
    $whoops->writeToOutput(true);

    if (isset($server_config['log_phperror']) && !$server_config['log_phperror']) {
        $whoops->silenceErrorsInPaths('mod/*', E_ALL);
    }

    $whoops->pushHandler(new PrettyPageHandler());

    // Ajout du handler pour logger les erreurs avec Monolog
    $whoops->pushHandler(new CallbackHandler(
        function ($exception, $inspector, $run) use ($log) {
            $log->error("Whoops! Une erreur est survenue : " . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    ));

    $whoops->register();


}
