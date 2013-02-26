<?php
/**
 * Main file which do includes et set up all data for the application
 * @package OGSpy
 * @subpackage main
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @version 3.04
 * @since 3.03 - 21 sept. 07
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
// PHP5 with register_long_arrays off?
if (!isset($HTTP_POST_VARS) && isset($_POST)) {
    $HTTP_POST_VARS = $_POST;
    $HTTP_GET_VARS = $_GET;
    $HTTP_SERVER_VARS = $_SERVER;
    $HTTP_COOKIE_VARS = $_COOKIE;
    $HTTP_ENV_VARS = $_ENV;
    $HTTP_POST_FILES = $_FILES;

    // _SESSION is the only superglobal which is conditionally set
    if (isset($_SESSION)) {
        $HTTP_SESSION_VARS = $_SESSION;
    }
}

//Récupération des paramètres de connexion à la base de données
if (file_exists("parameters/id.php")) {
    require_once ("parameters/id.php");
} else {
    if (!defined("OGSPY_INSTALLED") && !defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
        header("Location: install/index.php");
        exit();
    } elseif (file_exists('../parameters/id.php'))
        require_once ('../parameters/id.php');
}

//Appel des fonctions
require_once ("includes/config.php");
require_once ("includes/functions.php");
require_once ("includes/mysql.php");
require_once ("includes/log.php");
require_once ("includes/galaxy.php");
require_once ("includes/user.php");
require_once ("includes/sessions.php");
require_once ("includes/help.php");
require_once ("includes/mod.php");
require_once ("includes/ogame.php");
require_once ("includes/cache.php");
require_once ("includes/chart_js.php");

//Récupération des valeur GET, POST, COOKIE
extract($_GET,EXTR_PREFIX_ALL , "pub");
extract($_POST,EXTR_PREFIX_ALL , "pub");
extract($_COOKIE,EXTR_PREFIX_ALL , "pub");

foreach ($_GET as $secvalue) {
    if (!check_getvalue($secvalue)) {
        die("I don't like you...");
    }
}

foreach ($_POST as $secvalue) {
    if (!check_postvalue($secvalue)) {
        Header("Location: index.php");
        die();
    }
}

// ajout fichier clef unique
if (!defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
if (file_exists('parameters/key.php')) {
    require_once ('parameters/key.php');
    $dossierParent = (__FILE__);
    $path =  $_SERVER["SCRIPT_FILENAME"];;
    if ($path != $serveur_path) {
        generate_key();
    } // regenere que si incoherence d url

} else // non bloquant
{
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

//BBClone
define("_BBC_PAGE_NAME", "OGSpy server");
define("_BBCLONE_DIR", "bbclone/");
define("COUNTER", _BBCLONE_DIR . "mark_page.php");
if (is_readable(COUNTER))
    include_once (COUNTER);

if (MODE_DEBUG) {
    error_reporting(E_ALL);
}

if (isset($server_config["log_phperror"]) && $server_config["log_phperror"] == 1)
    set_error_handler('ogspy_error_handler');
?>