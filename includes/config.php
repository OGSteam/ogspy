<?php
global $table_prefix;

/**
 * Fichier of configuration communes
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
    // Only define table name constants if they are not already defined (helps tests require bootstrap multiple times)
    if (!defined("TABLE_CONFIG")) {
        //Tables utilisées par les programmes
    if (!defined("TABLE_CONFIG")) define("TABLE_CONFIG", $table_prefix . "config");
    if (!defined("TABLE_GROUP")) define("TABLE_GROUP", $table_prefix . "group");
    if (!defined("TABLE_SESSIONS")) define("TABLE_SESSIONS", $table_prefix . "sessions");
    if (!defined("TABLE_STATISTIC")) define("TABLE_STATISTIC", $table_prefix . "statistics");
    if (!defined("TABLE_USER")) define("TABLE_USER", $table_prefix . "user");
    if (!defined("TABLE_USER_TOKEN")) define("TABLE_USER_TOKEN", $table_prefix . "user_tokens");
    if (!defined("TABLE_USER_FAVORITE")) define("TABLE_USER_FAVORITE", $table_prefix . "game_user_favorites");
    if (!defined("TABLE_USER_GROUP")) define("TABLE_USER_GROUP", $table_prefix . "user_group");

    if (!defined("TABLE_MOD")) define("TABLE_MOD", $table_prefix . "mod");
    if (!defined("TABLE_MOD_CFG")) define("TABLE_MOD_CFG", $table_prefix . "mod_config");
    if (!defined("TABLE_MOD_USER_CFG")) define("TABLE_MOD_USER_CFG", $table_prefix . "mod_user_config");

        // Tables Player
    if (!defined("TABLE_UNIVERSE")) define("TABLE_UNIVERSE", $table_prefix . "game_universe");
    if (!defined("TABLE_USER_BUILDING")) define("TABLE_USER_BUILDING", $table_prefix . "game_astro_object");
    if (!defined("TABLE_GAME_PLAYER_DEFENSE")) define("TABLE_GAME_PLAYER_DEFENSE", $table_prefix . "game_player_defense");
    if (!defined("TABLE_GAME_PLAYER_FLEET")) define("TABLE_GAME_PLAYER_FLEET", $table_prefix . "game_player_fleet");
    if (!defined("TABLE_USER_SPY")) define("TABLE_USER_SPY", $table_prefix . "game_player_spy");
    if (!defined("TABLE_USER_TECHNOLOGY")) define("TABLE_USER_TECHNOLOGY", $table_prefix . "game_player_technology");
    if (!defined("TABLE_PARSEDSPY")) define("TABLE_PARSEDSPY", $table_prefix . "game_spy");
    if (!defined("TABLE_PARSEDRC")) define("TABLE_PARSEDRC", $table_prefix . "game_rc");
    if (!defined("TABLE_PARSEDRCROUND")) define("TABLE_PARSEDRCROUND", $table_prefix . "game_rc_round");
    if (!defined("TABLE_ROUND_ATTACK")) define("TABLE_ROUND_ATTACK", $table_prefix . "game_rc_round_attack");
    if (!defined("TABLE_ROUND_DEFENSE")) define("TABLE_ROUND_DEFENSE", $table_prefix . "game_rc_round_defense");
    if (!defined("TABLE_GAME_PLAYER")) define("TABLE_GAME_PLAYER", $table_prefix . "game_player");
    if (!defined("TABLE_GAME_ALLY")) define("TABLE_GAME_ALLY", $table_prefix . "game_ally");

        // Classements joueur
    if (!defined("TABLE_RANK_PLAYER_POINTS")) define("TABLE_RANK_PLAYER_POINTS", $table_prefix . "game_rank_player_points"); //points
    if (!defined("TABLE_RANK_PLAYER_ECO")) define("TABLE_RANK_PLAYER_ECO", $table_prefix . "game_rank_player_economics"); // economique
    if (!defined("TABLE_RANK_PLAYER_TECHNOLOGY")) define("TABLE_RANK_PLAYER_TECHNOLOGY", $table_prefix . "game_rank_player_technology"); // recherche
    if (!defined("TABLE_RANK_PLAYER_MILITARY")) define("TABLE_RANK_PLAYER_MILITARY", $table_prefix . "game_rank_player_military"); // militaire
    if (!defined("TABLE_RANK_PLAYER_MILITARY_BUILT")) define("TABLE_RANK_PLAYER_MILITARY_BUILT", $table_prefix . "game_rank_player_military_built"); // militaire construit
    if (!defined("TABLE_RANK_PLAYER_MILITARY_LOOSE")) define("TABLE_RANK_PLAYER_MILITARY_LOOSE", $table_prefix . "game_rank_player_military_loose"); // militaire perdu
    if (!defined("TABLE_RANK_PLAYER_MILITARY_DESTRUCT")) define("TABLE_RANK_PLAYER_MILITARY_DESTRUCT", $table_prefix . "game_rank_player_military_destruct"); // militaire detruit
    if (!defined("TABLE_RANK_PLAYER_HONOR")) define("TABLE_RANK_PLAYER_HONOR", $table_prefix . "game_rank_player_honor"); //points honneur
        // fin joueur
        // Classements alliance
    if (!defined("TABLE_RANK_ALLY_POINTS")) define("TABLE_RANK_ALLY_POINTS", $table_prefix . "game_rank_ally_points"); //points
    if (!defined("TABLE_RANK_ALLY_ECO")) define("TABLE_RANK_ALLY_ECO", $table_prefix . "game_rank_ally_economics"); // economique
    if (!defined("TABLE_RANK_ALLY_TECHNOLOGY")) define("TABLE_RANK_ALLY_TECHNOLOGY", $table_prefix . "game_rank_ally_technology"); // recherche
    if (!defined("TABLE_RANK_ALLY_MILITARY")) define("TABLE_RANK_ALLY_MILITARY", $table_prefix . "game_rank_ally_military"); // militaire
    if (!defined("TABLE_RANK_ALLY_MILITARY_BUILT")) define("TABLE_RANK_ALLY_MILITARY_BUILT", $table_prefix . "game_rank_ally_military_built"); // militaire construit
    if (!defined("TABLE_RANK_ALLY_MILITARY_LOOSE")) define("TABLE_RANK_ALLY_MILITARY_LOOSE", $table_prefix . "game_rank_ally_military_loose"); // militaire perdu
    if (!defined("TABLE_RANK_ALLY_MILITARY_DESTRUCT")) define("TABLE_RANK_ALLY_MILITARY_DESTRUCT", $table_prefix . "game_rank_ally_military_destruct"); // militaire detruit
    if (!defined("TABLE_RANK_ALLY_HONOR")) define("TABLE_RANK_ALLY_HONOR", $table_prefix . "game_rank_ally_honor"); //points honneur
        // fin alliance
    }

}

//Paramètres session
define("COOKIE_NAME", "ogspy_id");


//Chemin d'accès aux ressources
if (defined("INSTALL_IN_PROGRESS") || defined("UPGRADE_IN_PROGRESS")) {
    define("PATH_LOG", "../logs/");
} else {
    define("PATH_LOG", "./logs/");
}
