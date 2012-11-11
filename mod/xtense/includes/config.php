<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @licence GNU
 */

if (!defined('IN_SPYOGAME')) die("Hacking Attemp!");

global $table_prefix;

define('TABLE_XTENSE_GROUPS', $table_prefix.'xtense_groups');
define('TABLE_XTENSE_CALLBACKS', $table_prefix.'xtense_callbacks');
define('TABLE_PARSEDREC', $table_prefix.'parsedRec');
define('TABLE_PARSEDSPYEN', $table_prefix.'parsedSpyEn');

define('TYPE_PLANET', 0);
define('TYPE_MOON', 1);

define('MAGIC_QUOTES', false);
if (file_exists ("../mod/{$root}/version.txt"))
	list($mod_name, $mod_version, $mod_install, $ogspy_min_version, $toolbar_min_version) = file("../mod/{$root}/version.txt");
else
	list($mod_name, $mod_version, $mod_install, $ogspy_min_version, $toolbar_min_version) = file("mod/{$root}/version.txt");
	
define('PLUGIN_VERSION', trim($mod_version));
define('TOOLBAR_MIN_VERSION', trim($toolbar_min_version));

$database = array(
	'ressources' => array('metal','cristal','deuterium','energie','activite'),
	'buildings' => array('M', 'C', 'D', 'CES', 'CEF', 'UdR', 'UdN', 'CSp', 'SAT', 'HM', 'HC', 'HD', 'CM','CC','CD','Lab', 'Ter', 'Silo', 'DdR', 'BaLu', 'Pha', 'PoSa'),
	'labo' => array('Esp', 'Ordi', 'Armes', 'Bouclier', 'Protection', 'NRJ', 'Hyp', 'RC', 'RI', 'PH', 'Laser', 'Ions', 'Plasma', 'RRI', 'Graviton', 'Astrophysique'),
	'defense' => array('LM', 'LLE', 'LLO', 'CG', 'LP', 'AI', 'PB', 'GB', 'MIC', 'MIP'),
	'fleet' => array('PT', 'GT', 'CLE', 'CLO', 'CR', 'VB', 'VC', 'REC', 'SE', 'BMD', 'SAT', 'DST', 'EDLM', 'TRA')
);

$callbackTypesNames = array(
	'overview','system','ally_list','buildings','research','fleet','fleetSending','defense','spy','ennemy_spy','rc',
	'rc_cdr', 'msg', 'ally_msg', 'expedition','ranking_player_fleet','ranking_player_points','ranking_player_research','ranking_ally_fleet',
	'ranking_ally_points' , 'ranking_ally_research' , 'trade' , 'trade_me' , 'hostiles'
);

?>