<?php
global $table_prefix;

/**
 * Fichier de configuration communes
 * @package OGSpy
 * @subpackage Main
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ( $Rev: 7388 $ )
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

setlocale(LC_CTYPE, 'fr_FR.UTF-8');
date_default_timezone_set("UTC");


// Définitions des noms des tables de la BDD
if (!defined("INSTALL_IN_PROGRESS")) {
    //Tables utilisées par les programmes
    define("TABLE_CONFIG", $table_prefix . "config");
    define("TABLE_GROUP", $table_prefix . "group");
    define("TABLE_SESSIONS", $table_prefix . "sessions");
    define("TABLE_STATISTIC", $table_prefix . "statistics");
    define("TABLE_USER", $table_prefix . "user");
    define("TABLE_USER_TOKEN", $table_prefix . "user_tokens");
    define("TABLE_USER_FAVORITE", $table_prefix . "game_user_favorites");
    define("TABLE_USER_GROUP", $table_prefix . "user_group");

    define("TABLE_MOD", $table_prefix . "mod");
    define("TABLE_MOD_CFG", $table_prefix . "mod_config");
    define("TABLE_MOD_USER_CFG", $table_prefix . "mod_user_config");

    // Tables Player
    define("TABLE_UNIVERSE", $table_prefix . "game_universe");
    define("TABLE_USER_BUILDING", $table_prefix . "game_astro_object");
    define("TABLE_GAME_PLAYER_DEFENSE", $table_prefix . "game_player_defense");
    define("TABLE_GAME_PLAYER_FLEET", $table_prefix . "game_player_fleet");
    define("TABLE_USER_SPY", $table_prefix . "game_player_spy");
    define("TABLE_USER_TECHNOLOGY", $table_prefix . "game_player_technology");
    define("TABLE_PARSEDSPY", $table_prefix . "game_spy");
    define("TABLE_PARSEDRC", $table_prefix . "game_rc");
    define("TABLE_PARSEDRCROUND", $table_prefix . "game_rc_round");
    define("TABLE_ROUND_ATTACK", $table_prefix . "game_rc_round_attack");
    define("TABLE_ROUND_DEFENSE", $table_prefix . "game_rc_round_defense");
    define("TABLE_GAME_PLAYER", $table_prefix . "game_player");
    define("TABLE_GAME_ALLY", $table_prefix . "game_ally");

    // Classements joueur
    define("TABLE_RANK_PLAYER_POINTS", $table_prefix . "game_rank_player_points"); //points
    define("TABLE_RANK_PLAYER_ECO", $table_prefix . "game_rank_player_economics"); // economique
    define("TABLE_RANK_PLAYER_TECHNOLOGY", $table_prefix . "game_rank_player_technology"); // recherche
    define("TABLE_RANK_PLAYER_MILITARY", $table_prefix . "game_rank_player_military"); // militaire
    define("TABLE_RANK_PLAYER_MILITARY_BUILT", $table_prefix . "game_rank_player_military_built"); // militaire construit
    define("TABLE_RANK_PLAYER_MILITARY_LOOSE", $table_prefix . "game_rank_player_military_loose"); // militaire perdu
    define("TABLE_RANK_PLAYER_MILITARY_DESTRUCT", $table_prefix . "game_rank_player_military_destruct"); // militaire detruit
    define("TABLE_RANK_PLAYER_HONOR", $table_prefix . "game_rank_player_honor"); //points honneur
    // fin joueur
    // Classements alliance
    define("TABLE_RANK_ALLY_POINTS", $table_prefix . "game_rank_ally_points"); //points
    define("TABLE_RANK_ALLY_ECO", $table_prefix . "game_rank_ally_economics"); // economique
    define("TABLE_RANK_ALLY_TECHNOLOGY", $table_prefix . "game_rank_ally_technology"); // recherche
    define("TABLE_RANK_ALLY_MILITARY", $table_prefix . "game_rank_ally_military"); // militaire
    define("TABLE_RANK_ALLY_MILITARY_BUILT", $table_prefix . "game_rank_ally_military_built"); // militaire construit
    define("TABLE_RANK_ALLY_MILITARY_LOOSE", $table_prefix . "game_rank_ally_military_loose"); // militaire perdu
    define("TABLE_RANK_ALLY_MILITARY_DESTRUCT", $table_prefix . "game_rank_ally_military_destruct"); // militaire detruit
    define("TABLE_RANK_ALLY_HONOR", $table_prefix . "game_rank_ally_honor"); //points honneur
    // fin alliance

}

//Paramètres session
define("COOKIE_NAME", "ogspy_id");


//Chemin d'accès aux ressources
if (defined("INSTALL_IN_PROGRESS") || defined("UPGRADE_IN_PROGRESS")) {
    define("PATH_LOG", "../logs/");
} else {
    define("PATH_LOG", "./logs/");
}
