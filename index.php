<?php
global $server_config, $ogspy_version, $user_data, $log;
ob_start();
session_start();
/**
 * Fichier principal d'ogspy
 * @package OGSpy
 * @subpackage main
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.8
 */

/**
 * @abstract Utilisé dans les autres fichiers pour s'assurer qu'index.php est bien appelé
 */
const IN_SPYOGAME = true;

/**
 * Repère de début de traitement par OGSpy
 * @name $php_start
 */
$php_start = microtime(true);

/**
 * Tous les includes se font à partir de là
 */
require_once "common.php";

/**
 * Utilisation de la class benchmark
 * @name $benchogspy
 */
$benchogspy = new Ogsteam\Ogspy\Helper\Benchmark_Helper('ogspy');
$benchogspy->addCustomBench($php_start,microtime(true),"initialisation Ogspy/include" );
$benchogspy->start();
//$benchSQL pour temps sql

/**
 * @global string $pub_action
 */
if (!isset($pub_action)) {
    $pub_action = "";
}

if (is_file("install/version.php")) {
    require_once "install/version.php";
    $log->info("Version : " . $ogspy_version);
    if (version_compare($server_config["version"], $ogspy_version, '<')) {
        redirection("install/index.php");
    }
}

if (
    $server_config["server_active"] == 0
    && $pub_action != "login_web"
    && $pub_action != "logout" && $user_data['admin'] != 1
    && $user_data['coadmin'] != 1
) {
    $pub_action = "server_close";
}

//  Visiteur non identifié

if (!isset($user_data["id"]) && !(isset($pub_action) && $pub_action == "login_web")) {
    $log->info("Visiteur non identifié");
    if ($pub_action == "message") {
        require "views/message.php";
    } else {
        if (isset($_SERVER['QUERY_STRING']) && preg_match("/^action=(.*)/", $_SERVER['QUERY_STRING'], $matches)) {
            $goto = $matches[1];
        }
        require_once "views/login.php";
    }
    exit();
}
$log->debug("user_data : " . print_r($user_data, true));
//$log->info("Visiteur identifié : " . $user_data["name"] . " (id : " . $user_data["id"] . ")");
if ($pub_action <> '' && isset($cache_mod[$pub_action])) {
    $log->info("Action : " . $pub_action);
    if (ratio_is_ok()) {
        if ($cache_mod[$pub_action]['admin_only'] == 1 && $user_data["admin"] == 0 && $user_data["coadmin"] == 0) {
            redirection("index.php?action=message&id_message=forbidden&info");
        } else {
            require_once "mod/" . $cache_mod[$pub_action]['root'] . "/" . $cache_mod[$pub_action]['link'];
            exit();
        }
    }
}

if (isset($user_data['pwd_change'])) {
    //Changer le mdp :
    if ($pub_action !== 'logout' && $user_data['pwd_change'] == 1 && $pub_action !== 'member_modify_member') {
        $pub_action = 'profile';
    }
}

if (!isset($pub_goto)) {
    $pub_goto = null;
}

switch ($pub_action) {

        //----------------------------------------//
        //--------Connexion---------//
        //----------------------------------------//
        //Identification
    case "login_web":
        if ($pub_goto == null) {
            user_login();
        } else {
            user_login_redirection();
        }
        break;

        //Déconnexion
    case "logout":
        user_logout();
        break;

        //----------------------------------------//
        //---Administration---//
        //----------------------------------------//
    case "administration":
        require_once "views/admin.php";
        break;

    case "set_server_view":
        set_server_view();
        break;

    case "set_serverconfig":
        set_serverconfig();
        break;

    case "extractor":
        log_extractor();
        break;

    case "remove":
        log_remove();
        break;

    case "db_optimize":
        db_optimize();
        break;

    case "drop_sessions":
        drop_sessions();
        redirection("index.php?action=administration&subaction=infoserver");
        break;

    case "raz_ratio":
        admin_raz_ratio();
        break;

        //----------------------------------------//
        //---Gestion des membres---//
        //----------------------------------------//
    case "home":
        require_once "views/home.php";
        break;

    case "del_planet":
        user_del_building();
        break;

    case "move_planet":
        user_move_empire();
        break;

    case "profile":
        require_once "views/profile.php";
        break;

    case "newaccount":
        user_create();
        break;

    case "message":
        require "views/message.php";
        break;

    case "admin_modify_member":
        admin_user_set();
        break;

    case "member_modify_member":
        member_user_set();
        break;

    case "delete_member":
        user_delete();
        break;

    case "new_password":
        admin_regeneratepwd();
        break;

    case "usergroup_create":
        usergroup_create();
        break;

    case "usergroup_delete":
        usergroup_delete();
        break;

    case "usergroup_setauth":
        usergroup_setauth();
        break;

    case "usergroup_delmember":
        usergroup_delmember();
        break;

    case "usergroup_newmember":
        usergroup_newmember();
        break;

        //----------------------------------------//
        //--- ---//
        //----------------------------------------//
    case "galaxy":
        require_once "views/galaxy.php";
        break;

    case "galaxy_sector":
        require_once "views/galaxy_sector.php";
        break;

        //
    case "show_reportspy":
        require_once "views/report_spy.php";
        break;

        //
    case "show_reportrc":
        require_once "views/report_rc.php";
        break;

        //
    case "add_favorite":
        user_add_favorite();
        break;

        //
    case "del_favorite":
        user_del_favorite();
        break;

        //
    case "search":
        require_once "views/search.php";
        break;

        //
    case "cartography":
        require_once "views/cartography.php";
        break;

        //
    case "statistic":
        require_once "views/statistic.php";
        break;

        //
    case "ranking":
        require_once "views/ranking.php";
        break;

        //
    case "drop_ranking":
        galaxy_drop_ranking();
        break;

        //
    case "about":
        require_once "views/about_ogsteam.php";
        break;

        //
    case "galaxy_obsolete":
        require_once "views/galaxy_obsolete.php";
        break;

        //
    case "add_favorite_spy":
        user_add_favorite_spy();
        break;

        //
    case "del_favorite_spy":
        user_del_favorite_spy();
        break;

        //
    case "del_spy":
        user_del_spy();
        break;



        //----------------------------------------//
        //--- ---//
        //----------------------------------------//
    case "mod_disable":
        mod_disable();
        break;

        //
    case "mod_uninstall":
        mod_uninstall();
        break;

        //
    case "mod_active":
        mod_active();
        break;

        //
    case "mod_admin":
        mod_admin();
        break;

        //
    case "mod_normal":
        mod_normal();
        break;

        //
    case "mod_install":
        mod_install();
        break;

        //
    case "mod_update":
        mod_update();
        break;


        //
    case "mod_up":
        mod_sort("up");
        break;


        //
    case "mod_down":
        mod_sort("down");
        break;
        //----------------------------------------//
        //--- ---//
        //----------------------------------------//
    case "server_close":
        require_once "views/serverdown.php";
        break;

    default:
        if ($server_config['open_user'] != "" && $user_data['admin'] != 1 && $user_data['coadmin'] != 1) {

            if (file_exists($server_config['open_user'])) {
                require_once($server_config['open_user']);
            } else {
                require_once("views/galaxy.php");
            }
        } elseif ($server_config['open_admin'] != "" && ($user_data['admin'] == 1 || $user_data['coadmin'] == 1)) {
            if (file_exists($server_config['open_admin'])) {
                require_once($server_config['open_admin']);
            } else {
                require_once("views/galaxy.php");
            }
        } else {
            require_once("views/galaxy.php");
        }
        break;
}
