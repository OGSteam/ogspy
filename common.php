<?php
/**
 * Main file which do includes et set up all data for the application
 *
 * @package OGSpy
 * @subpackage main
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04
 */

namespace Ogsteam\Ogspy;

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
}

//Récupération des paramètres de connexion à la base de données
if (file_exists("config/id.php")) {
    require_once("config/id.php");
} else {
    if (!defined("OGSPY_INSTALLED") && !defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
        header("Location: install/index.php?lang=fr");
    } elseif (file_exists('../config/id.php')) require_once('../config/id.php');
}

//Appel des fonctions

require __DIR__ . '/vendor/autoload.php';
require("core/config.php");
require("core/functions.php");
require("core/Sql_Db.php");
require("core/Api_data.php");
require("core/log.php");
require("core/galaxy.php");
require("core/user.php");
require("core/sessions.php");
require("core/help.php");
require("core/Mod_DevTools.php");
require("core/Mod_Factory.php");
require("core/ogame.php");
require("core/cache.php");
require("core/chart_js.php");
require("core/datatable_js.php");

//Récupération des valeur GET, POST, COOKIE
extract($_GET, EXTR_PREFIX_ALL, "pub");
extract($_POST, EXTR_PREFIX_ALL, "pub");
extract($_COOKIE, EXTR_PREFIX_ALL, "pub");

foreach ($_GET as $secvalue) { check_getvalue($secvalue); }
foreach ($_POST as $secvalue) { check_postvalue($secvalue); }

//Language File
if (!isset($ui_lang)) { // Checks the ui_lang value from parameters file
    if (isset($pub_lang)) {
        $ui_lang = $pub_lang; //This value is used during installation
    } else
        $ui_lang = "fr";
    //If no language is available in id.php file we take fr by default
}
require_once("lang/lang_main.php");

// ajout fichier clef unique
if (!defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS")) {
    if (file_exists('config/key.php')) {
        require_once('config/key.php');
        $dossierParent = (__FILE__);
        $path = $_SERVER["SCRIPT_FILENAME"];
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
    $db = Sql_Db::getInstance($db_host, $db_user, $db_password, $db_database);

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

//if (isset($server_config["log_phperror"]) && $server_config["log_phperror"] == 1) set_error_handler('ogspy_error_handler');

