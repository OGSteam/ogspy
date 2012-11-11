<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @licence GNU
 */

if (!defined('IN_SPYOGAME')) die("Hacking Attemp!");

/**
 * Fonctions commune d'installation des callbacks des mods
 *
 * @param string $action - Action du mod
 * @param array $data - Appels à installer
 * @param string $version - Optionnel, version miniale requise de xtense
 * @return false/int - Retourne false si il y a une erreur ou le nombre d'appels ajoutés
 */
function install_callbacks ($action, $data, $version = null) {
	global $db, $table_prefix;
	
	define('XTENSE_LITE_CONFIG', 1);
	require_once('mod/xtense/includes/config.php');
	
	if ($version != null && version_compare($version, MOD_VERSION, '<=')) return false;
	
	$query = $db->sql_query('SELECT id FROM '.TABLE_MOD.' WHERE action = "'.$action.'"');
	list($mod_id) = mysql_fetch_row($query);
	
	$replace = array();
	foreach ($data as $k => $call) {
		if (!isset($call['function'], $call['type'])) return false;
		if (!isset($call['active'])) $call['active'] = 1; 
		$replace[] = '('.$mod_id.', "'.$call['function'].'", "'.$call['type'].'", '.$call['active'].')';
	}
	
	$db->sql_query('INSERT IGNORE INTO '.TABLE_XTENSE_CALLBACKS.' (mod_id, function, type, active) VALUES '.implode(',', $replace));
	return $db->sql_affectedrows();
}

function js_compatibility($string){
	return str_replace('<br>','\n',(htmlspecialchars_decode($string)));
}

function parseOgameDate($date) {
	preg_match('!([0-9]+)-([0-9]+) ([0-9]+):([0-9]+):([0-9]+)!i', $date, $parts);
	return mktime($parts[3], $parts[4], $parts[5], $parts[1], $parts[2], date('Y') - ($parts[1] == 12 && date('n') == 1 ? 1 : 0));
}

function clean_nb($str) {
	return (int)str_replace('.', '', $str);
}

function error_handler($no, $str, $file, $line) {
	global $call;
	
	if ($call->currentCallback !== false) {
		global $io;
		
		throw new Exception('Erreur PHP lors de l\'execution'."\n".' '.$file.' @ '.$line.' : "'.$str.'"');
		//$io->append_call_error($call->currentCallback, 'Erreur PHP lors de l\'execution'."\n".' '.$file.':'.$line.' : "'.$str.'"');
		return !DEBUG;
	}
	
	return false;
}

/**
 * Amélioration de var_dump()
 *
 */
function dump() {
	$n = func_num_args();
	ob_start();
	for ($i = 0; $i < $n; $i++)
		var_dump(func_get_arg($i));
	$content = ob_get_clean()."\n";
	//echo str_replace(array('<', '>'), array('&lt;', '&gt;'), $content)."\n";
	echo $content."\n";
}


/**
 * Echappement forcé pour la syntaxe Json
 *
 * @param string $str
 * @return string
 */
function json_quote($str) {
	return str_replace('"', '\\"', $str);
}

/**
 * Addslashes en fonction des magic_quotes
 *
 * @param string $str
 * @return string
 */
function quote($str) {
	return (MAGIC_QUOTES ? $str : addslashes($str));
}

/**
 * Verification de l'empire (Mise à jour, rajout, empire plein)
 *
 * @param int $type
 * @param string $coords
 * @return mixed(bool/int)
 */

function home_check($type, $coords) {
	global $db, $user_data;
	
	$empty_planets 	= array(101=>1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20);
	$empty_moons 	= array(201=>1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20);
	$planets = $moons = array();
	$offset = ($type == TYPE_PLANET ? 100 : 200);
	
	$query = $db->sql_query("SELECT planet_id, coordinates FROM ".TABLE_USER_BUILDING." WHERE user_id = ".$user_data['user_id']." ORDER BY planet_id ASC");
	while ($data = $db->sql_fetch_assoc($query)) {
		if ($data['planet_id'] < 200) {
			$planets[$data['planet_id']] = $data['coordinates'];
			unset($empty_planets[$data['planet_id']], $empty_moons[$data['planet_id']+100]);
		}
		else {
			$moons[$data['planet_id']] = $data['coordinates'];
			unset($empty_moons[$data['planet_id']], $empty_planets[$data['planet_id']-100]);
		}
	}
	foreach ($planets as $id => $p) {
		if ($p == $coords || $coords == "unknown") {
			// Si c'est une lune on check si une lune existe déjà
			if ($type == TYPE_MOON) {
				if (isset($moons[$id+100])) return array('update', 'id' => $id+100);
				else return array('add', 'id' => $id+100);
			}
			
			return array('update', 'id' => $id);
		}
	}
	
	// Si une lune correspond a la planete, on place la planete sous la lune
	foreach ($moons as $id => $m) {
		if ($m == $coords) {
			return array($type == TYPE_PLANET ? 'add' : 'update', 'id' => $id-200+$offset);
		}
	}
	
	if ($type == TYPE_PLANET) {
		if (count($empty_planets) == 0) return array('full');
		foreach ($empty_planets as $p) return array('add', 'id' => $p+$offset);
	}
	else {
		if (count($empty_moons) == 0) return array('full');
		foreach ($empty_moons as $p) return array('add', 'id' => $p+$offset);
	}
}

function check_coords($coords, $exp = 0) {
	global $server_config;
	if (!preg_match('!^([0-9]{1,2}):([0-9]{1,3}):([0-9]{1,2})$!Usi', $coords, $match)) return false;
	//$row_error = ($exp ? ($match[3] != 16) : ($match[3] > 15) );
	//if ($match[1] < 1 || $match[2] < 1 || $match[3] < 1 || $match[1] > $server_config['num_of_galaxies'] || $match[2] > $server_config['num_of_systems'] || ($exp ? ($match[3] != 16) : ($match[3] > 15))) return false;
	return !($match[1] < 1 || $match[2] < 1 || $match[3] < 1 || $match[1] > $server_config['num_of_galaxies'] || $match[2] > $server_config['num_of_systems'] || ($exp ? ($match[3] != 16) : ($match[3] > 15)));
	//return true;
}

function icon($name) {
	global $root;
	echo "<img src='mod/{$root}/tpl/icons/{$name}.png' class='icon' align='absmiddle' />";
}

function get_microtime() {
	$t = explode(' ', microtime());
	return ((float)$t[1] + (float)$t[0]);
}


function add_log($type, $data = null) {
	global $server_config, $user_data, $root;
	$message = '';
	if(!isset($data['toolbar'])) {$data['toolbar'] = "";}
	if ($type == 'buildings' || $type == 'overview' || $type == 'defense' || $type == 'research' || $type == 'fleet'||$type == 'info') {
		if (!$server_config['xtense_log_empire']) return;
		
		if ($type == 'buildings') 	$message = 'envoie les batiments de sa planète '.$data['planet_name'].' ('.$data['coords'].')';
		if ($type == 'overview') 	$message = 'envoie les informations de sa planète '.$data['planet_name'].' ('.$data['coords'].')';
		if ($type == 'defense') 	$message = 'envoie les defenses de sa planète '.$data['planet_name'].' ('.$data['coords'].')';
		if ($type == 'research') 	$message = 'envoie ses recherches';
		if ($type == 'fleet') 		$message = 'envoie la flotte de sa planète '.$data['planet_name'].' ('.$data['coords'].')';
		if ($type == 'info')		$message = $data['message'];
	}
	
	if ($type == 'system') {
		if (!$server_config['xtense_log_system']) return;
		
		$message = 'envoie le système solaire '.$data['coords'];
	}
	
	if ($type == 'ranking') {
		if (!$server_config['xtense_log_ranking']) return;
		
		$message = 'envoie le classement '.$data['type2'].' des '.$data['type1'].' ('.$data['offset'].'-'.($data['offset']+99).') : '.date('H', $data['time']).'h';
	}
	
	if ($type == 'ally_list') {
		$message = 'envoie la liste des membres de l\'alliance '.$data['tag'];
	}
	
	if ($type == 'rc') {
		$message = 'envoie un rapport de combat';
	}
	
	if ($type == 'messages') {
		$message = 'envoie sa page de messages';
		
		$extra = array();
		if (array_key_exists('msg', $data)) $extra[] = 'messages : '.$data['msg'];
		if (array_key_exists('ally_msg', $data)) $extra[] = $data['ally_msg'].' messages d\'alliance';
		if (array_key_exists('ennemy_spy', $data)) $extra[] = $data['ennemy_spy'].' espionnages ennemis';
		if (array_key_exists('rc_cdr', $data)) $extra[] = $data['rc_cdr'].' rapports de recyclages';
		if (array_key_exists('expedition', $data)) $extra[] = $data['expedition'].' rapports d\'expedition';
		if (array_key_exists('added_spy', $data)) $extra[] = ' Rapport d\'espionnage ajouté : '.$data['added_spy_coords'];
		if (array_key_exists('ignored_spy', $data)) $extra[] = $data['ignored_spy'].' rapports d\'espionnage ignorés';
		
		if (!empty($extra)) $message .= ' ('.implode(', ', $extra).')';
	}
	if (!empty($message)) {
		$dir = date('ymd');
		
		if ($server_config['xtense_log_ogspy']) {
			$file = 'log_'.date('ymd').'.log';
			if (!file_exists('journal/'.$dir)) @mkdir('journal/'.$dir);
			if (file_exists('journal/'.$dir)) {
				@chmod('journal/'.$date, 0777);
				$fp = @fopen('journal/'.$dir.'/'.$file, 'a+');
				if ($fp) {
					fwrite($fp, '/*'.date('d/m/Y H:i:s').'*/'.'[Xtense]'.'['.$data['toolbar'].'] '.$user_data['user_name'].' '.$message."\n");
					fclose($fp);
					@chmod('journal/'.$dir.'/'.$file, 0777);
				}
			}
		} else {
			$file = date('ymd').'log';
			$fp = @fopen("mod/{$root}/log/".$file, 'a+');
			if ($fp) {
				fwrite($fp, date('H:i:s').' | '.$user_data['user_name'].' '.$message."\n");
				fclose($fp);
				@chmod("mod/{$root}/log/".$file, 0777);
			}
		}
	}
	
	// Verif de la date des fichiers logs
	if ($server_config['xtense_keep_log'] == 0 || $server_config['xtense_log_ogspy']) return;
	
	$since = strtotime('-'.$server_config['xtense_keep_log'].' days');
	$fp = @opendir("mod/{$root}/log/");
	while (($file = @readdir($fp)) !== false) {
		if ($file != '.' && $file != '..' && preg_match('!^([0-9]{2})([0-9]{2})([0-9]{2})\.log$!', $file, $matches)) {
			if (mktime(0, 0, 1, $matches[3], $matches[2], $matches[1]) < $since) @unlink("mod/{$root}/log/".$file);
		}
	}
}

function format_size ($size) {
	if ($size < 1024) $size .= ' octets';
	elseif ($size < 1024*1024) $size = round($size/1024, 2).' Ko';
	else $size = round($size/1024/1024, 2).'Mo';
	return $size;
}

function update_statistic($stats,$value){
	global $db;
	$request = "update ".TABLE_STATISTIC." set statistic_value = statistic_value + {$value}";
	$request .= " where statistic_name = '{$stats}'";
	$db->sql_query($request);
	if (mysql_affected_rows() == 0) {
		$request = "insert ignore into ".TABLE_STATISTIC." values ('{$stats}', '{$value}')";
		$db->sql_query($request);
	}	
}