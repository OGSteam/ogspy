<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @licence GNU
 */

abstract class Check {
	static function filterSpecialChars ($string) {//http://www.wikistuce.info/doku.php/php/supprimer_tous_les_caracteres_speciaux_d-une_chaine
		$string = utf8_decode($string);
		//echo $string;
		//$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[^a-zA-Z0-9_ -]@');
		//$replace = array ('e','a','i','u','o','c','');
		
		//$search = '@[^éèêëÊËàâäÂÄîïÎÏûùüÛÜôöÔÖç_. a-zA-Z0-9-]@';
		/*$search = '@[^a-zA-Z0-9_. -]@';
		$replace = '';
		return preg_replace($search, $replace, $string);*/
		return $string;
	}
	
	static function player_name($string) {
		return preg_match('![A-Z0-9_ -]{1,20}!i', $string);
		//return preg_match('!.{1,20}!i', $string);
	}
	
	static function planet_name($string) {
		//return preg_match('![A-Z0-9éè_. -]{1,20}!i', $string);
		return preg_match('!.{1,20}!i', $string);
	}
	
	static function player_status($string) {
		return preg_match('!^[snfdvbiIoph]*$!', $string);//fdvbiIoph en français, snvbiIoph in english
	}
	
	static function player_status_forbidden($string) { //Le status "point d'honneur (ph)" est subjectif
		return preg_match('!^[ph]*$!', $string);
	}
	
	static function ally_tag($string) {
		//return preg_match('!^[A-Z0-9\\ ._-]{0,8}$!i', $string);
		return preg_match('!.{0,8}!i', $string);
	}
	
	static function galaxy($n) {
		global $server_config;
		return !($n == 0 || $n > $server_config['num_of_galaxies']);
	}
	
	static function system($n) {
		global $server_config;
		return !($n == 0 || $n > $server_config['num_of_systems']);
	}
	
	static function stats_type1($string) {
		return ($string == 'player' || $string == 'ally');
	}
	
	static function stats_type2($string) {
		return ($string != 'points' || $string != 'fleet' || $string != 'research' || $string != 'economy');
	}
	
	static function stats_type3($string) {
		return ($string >= 4 && $string <= 7);
	}
	
	static function stats_offset($off) {
		return ((($off-1) % 100) == 0);
	}
	
	static function coords($string, $exp = 0) {
		global $server_config;
		if ($string == "unknown") return true; //cas avec une seule planète
		if (!preg_match('!^([0-9]{1,2}):([0-9]{1,3}):([0-9]{1,2})$!Usi', $string, $match)) return false;
		return !($match[1] < 1 || $match[2] < 1 || $match[3] < 1 || $match[1] > $server_config['num_of_galaxies'] || $match[2] > $server_config['num_of_systems'] || ($exp ? ($match[3] != 16) : ($match[3] > 15))) ;
	}
	
	static function date($d) {
					//date au format anglais 																					//date au format de/fr
		return preg_match('!^[01][0-9]-[0-3][0-9] [0-2][0-9]:[0-6][0-9]:[0-6][0-9]$!', $d) || preg_match('!^[0-3][0-9]-[01][0-9] [0-2][0-9]:[0-6][0-9]:[0-6][0-9]$!', $d);
	}
	
	static function data() {
		foreach (func_get_args() as $v) {
			if (!$v) die('hack');
		}
	}
	
	static function data2() {
		foreach (func_get_args() as $v) {
			if (!$v) {
				return false;
			}
		}
		return true;
	}
	
	static function universe($str) {
		$universe = false;//'http://uni0.ogame.fr';
		if (preg_match('!([a-z0-9]+.ogame\\.[A-Z.]+)(\\/|$)!Ui', $str, $matches)) 
				$universe = 'http://'.strtolower($matches[1]);
		return $universe;
	}
	
}


?>