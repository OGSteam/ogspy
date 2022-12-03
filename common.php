<?php

/**
 * Main file which do includes et set up all data for the application
 * @package OGSpy
 * @subpackage main
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.7 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

//Récupération des paramètres de connexion à la base de données
if (file_exists("parameters/id.php")) {
    require_once("parameters/id.php");
} else {
    if (!defined("OGSPY_INSTALLED") && !defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
        header("Location: install/index.php?lang=fr");
        exit();
    } elseif (file_exists('../parameters/id.php')) {
        require_once('../parameters/id.php');
    }
}

//Appel des fonctions
require __DIR__ . '/vendor/autoload.php';

require_once("includes/config.php");
require_once("includes/admin_functions.php");
require_once("includes/functions.php");
require_once("includes/mysql.php");
require_once("includes/log.php");
require_once("includes/galaxy.php");
require_once("includes/user.php");
require_once("includes/sessions.php");
require_once("includes/help.php");
require_once("includes/mod.php");
require_once("includes/ogame.php");
require_once("includes/cache.php");
require_once("includes/chart_js.php");

if (defined("OGSPY_INSTALLED")) {
    require_once("includes/mail.php");
    require_once("includes/token.php");
}

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

//Language File
if (!isset($ui_lang)) { // Checks the ui_lang value from parameters file
    if (isset($pub_lang)) {
        $ui_lang = $pub_lang; //This value is used during installation
    } else {
        $ui_lang = "fr";
    }
    //If no language is available in id.php file we take fr by default
}
require_once("lang/lang_main.php");

// ajout fichier clef unique
if (!defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
    if (file_exists('parameters/key.php')) {
        require_once('parameters/key.php');
    } else {
        generate_key();
    }
}

//Connexion à la base de donnnées
if (!defined("INSTALL_IN_PROGRESS")) {
    // appel de l instance en cours
    $db = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);

    if (!$db->db_connect_id) {
        die("Impossible de se connecter à la base de données");
    }

    //Récupération et encodage de l'adresse ip
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_ip = encode_ip($user_ip);

    // initialisation des variables en cache
    init_serverconfig();
    init_mod_cache();

    if (!defined("UPGRADE_IN_PROGRESS")) {
        session();
        maintenance_action();
    }
}

if (!isset($server_config["log_phperror"])) {
    $server_config["log_phperror"] = 0;
}
if ($server_config["log_phperror"] == 1) {
    set_error_handler('ogspy_error_handler');
}
