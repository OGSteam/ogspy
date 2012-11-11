<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @version 1.0
 * @licence GNU
 */

if (!defined('IN_SPYOGAME')) die("Hacking Attemp!");
global $db,$table_prefix;

$mod_folder = "xtense";
$mod_name = "xtense";

define('TABLE_XTENSE_CALLBACKS', $table_prefix.'xtense_callbacks');

$query = $db->sql_query('SELECT id, version FROM '.TABLE_MOD.' WHERE root = "Xtense"');
list($id, $version) = mysql_fetch_row($query);

if (version_compare($version, '2.4.0', '<=')) {
	$db->sql_query("ALTER TABLE `" . TABLE_XTENSE_CALLBACKS . "` CHANGE `type` `type` ENUM( 'rc_cdr', 'overview', 'system', 'ally_list', 'buildings', 'research', 'fleet', 'fleetSending', 'defense', 'spy', 'ennemy_spy', 'rc', 'msg', 'ally_msg', 'expedition', 'ranking_player_fleet', 'ranking_player_points', 'ranking_player_research', 'ranking_ally_fleet', 'ranking_ally_points', 'ranking_ally_research', 'trade', 'trade_me', 'hostiles' ) NOT NULL");
}

update_mod($mod_folder, $mod_name);


?>