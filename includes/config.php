<?php
/**
 * Fichier de configuration communes
 * @package OGSpy
 * @subpackage Main
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ( $Rev: 7388 $ )
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

setlocale(LC_CTYPE, 'fr_FR.UTF-8');

if (version_compare(PHP_VERSION, "5.1.0RC1") >= 0) {
    date_default_timezone_set("Europe/Paris");
}

// Définitions des noms des tables de la BDD
if (!defined("INSTALL_IN_PROGRESS")) {
    //Tables utilisées par les programmes
    define("TABLE_CONFIG", $table_prefix . "config");
    define("TABLE_GROUP", $table_prefix . "group");
    define("TABLE_SESSIONS", $table_prefix . "sessions");
    define("TABLE_STATISTIC", $table_prefix . "statistics");
    define("TABLE_USER", $table_prefix . "user");
    define("TABLE_USER_TOKEN", $table_prefix. "user_tokens");
    define("TABLE_USER_FAVORITE", $table_prefix . "user_favorite");
    define("TABLE_USER_GROUP", $table_prefix . "user_group");

    define("TABLE_MOD", $table_prefix . "mod");
    define("TABLE_MOD_CFG", $table_prefix . "mod_config");

    // Tables Player
    define("TABLE_UNIVERSE", $table_prefix . "universe");
    define("TABLE_USER_BUILDING", $table_prefix . "user_building");
    define("TABLE_USER_DEFENCE", $table_prefix . "user_defence");
    define("TABLE_USER_SPY", $table_prefix . "user_spy");
    define("TABLE_USER_TECHNOLOGY", $table_prefix . "user_technology");
    define("TABLE_PARSEDSPY", $table_prefix . "parsedspy");
    define("TABLE_PARSEDRC", $table_prefix . "parsedRC");
    define("TABLE_PARSEDRCROUND", $table_prefix . "parsedRCRound");
    define("TABLE_ROUND_ATTACK", $table_prefix . "round_attack");
    define("TABLE_ROUND_DEFENSE", $table_prefix . "round_defense");
    define("TABLE_GAME_PLAYER", $table_prefix . "game_player");
    define("TABLE_GAME_ALLY", $table_prefix . "game_ally");

    // Google Cloud Messaging users table
    define("TABLE_GCM_USERS", $table_prefix . "gcm_users");

    // Classements joueur
    define("TABLE_RANK_PLAYER_POINTS", $table_prefix . "rank_player_points"); //points
    define("TABLE_RANK_PLAYER_ECO", $table_prefix . "rank_player_economique"); // economique
    define("TABLE_RANK_PLAYER_TECHNOLOGY", $table_prefix . "rank_player_technology"); // recherche
    define("TABLE_RANK_PLAYER_MILITARY", $table_prefix . "rank_player_military"); // militaire
    define("TABLE_RANK_PLAYER_MILITARY_BUILT", $table_prefix . "rank_player_military_built"); // militaire construit
    define("TABLE_RANK_PLAYER_MILITARY_LOOSE", $table_prefix . "rank_player_military_loose"); // militaire perdu
    define("TABLE_RANK_PLAYER_MILITARY_DESTRUCT", $table_prefix . "rank_player_military_destruct"); // militaire detruit
    define("TABLE_RANK_PLAYER_HONOR", $table_prefix . "rank_player_honor"); //point d honneur
    // fin joueur
    // Classements alliance
    define("TABLE_RANK_ALLY_POINTS", $table_prefix . "rank_ally_points"); //points
    define("TABLE_RANK_ALLY_ECO", $table_prefix . "rank_ally_economique"); // economique
    define("TABLE_RANK_ALLY_TECHNOLOGY", $table_prefix . "rank_ally_technology"); // recherche
    define("TABLE_RANK_ALLY_MILITARY", $table_prefix . "rank_ally_military"); // militaire
    define("TABLE_RANK_ALLY_MILITARY_BUILT", $table_prefix . "rank_ally_military_built"); // militaire construit
    define("TABLE_RANK_ALLY_MILITARY_LOOSE", $table_prefix . "rank_ally_military_loose"); // militaire perdu
    define("TABLE_RANK_ALLY_MILITARY_DESTRUCT", $table_prefix . "rank_ally_military_destruct"); // militaire detruit
    define("TABLE_RANK_ALLY_HONOR", $table_prefix . "rank_ally_honor"); //point d honneur
    // fin alliance

}

//Paramètres session
define("COOKIE_NAME", "ogspy_id");


//Chemin d'accès aux ressources
if (!defined("INSTALL_IN_PROGRESS") && !defined("UPGRADE_IN_PROGRESS") && !defined("GRAPHIC")) {
    define("PATH_LOG", "journal/");
} else {
    define("PATH_LOG", "../journal/");
}
$path_log_today = PATH_LOG . date("ymd") . "/";
if (!is_dir($path_log_today)) {
    mkdir($path_log_today);
    chmod($path_log_today, 0777);
    fclose(fopen("$path_log_today/index.htm", "a"));
}
define("PATH_LOG_TODAY", PATH_LOG . date("ymd") . "/");


//Bannière OGSPY
$banner[] = "logos/logo.png";

srand(time());
shuffle($banner);
$banner_selected = $banner[0];
