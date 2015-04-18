<?php
/**
 * OGSpy Global functions
 * @package OGSpy
 * @subpackage Common
 * @author Kyser
 * @copyright Copyright &copy; 2012, http://www.ogsteam.fr/
 * @version 3.1.1 ($Rev: 7752 $)
 * @modified $Date: 2012-11-05 13:04:30 +0100 (Mon, 05 Nov 2012) $
 * @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/functions.php $
 * $Id: functions.php 7752 2012-11-05 12:04:30Z darknoon $
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
/**
 * URL Redirection
 * @param string $url target URL
 */
function redirection($url)
{
    if (headers_sent()) {
        die('<meta http-equiv="refresh" content="0; URL=' . $url . '">');
    } else {
        header("Location: " . $url);
        exit();
    }
}

/**
 * Write a text or a table in a file
 * @param string $file Filename
 * @param string $mode File Opening Mode
 * @param string|Array $text String or table to write
 * @return boolean false if failed
 */
function write_file($file, $mode, $text)
{
    if ($fp = fopen($file, $mode)) {
        if (is_array($text)) {
            foreach ($text as $t) {
                fwrite($fp, rtrim($t));
                fwrite($fp, "\r\n");
            }
        } else {
            fwrite($fp, $text);
            fwrite($fp, "\r\n");
        }
        fclose($fp);
        return true;
    } else
        return false;
}

/**
 * Write a text or a table in a gz compressed file
 * @param string $file Filename
 * @param string $mode File Opening Mode
 * @param string|Array $text String or table to write
 * @return boolean false if failed
 */
function write_file_gz($file, $mode, $text)
{
    if ($fp = gzopen($file . ".gz", $mode)) {
        if (is_array($text)) {
            foreach ($text as $t) {
                gzwrite($fp, rtrim($t));
                gzwrite($fp, "\r\n");
            }
        } else {
            gzwrite($fp, $text);
            gzwrite($fp, "\r\n");
        }
        gzclose($fp);
        return true;
    } else
        return false;
}

/**
 * Convert an IP in Hex Format
 * @param string $ip format xxx.xxx.xxx.xxx in IPv4 and xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx in IPv6
 * @return string IP in hex : HHHHHHHH for IPv4 and HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH for IPv6
 */
function encode_ip ($ip)
{
	$d = explode('.', $ip);
	if (count($d) == 4) return sprintf('%02x%02x%02x%02x', $d[0], $d[1], $d[2], $d[3]);

	$d = explode(':', preg_replace('/(^:)|(:$)/', '', $ip));
	$res = '';
	foreach ($d as $x)
	$res .= sprintf('%0'. ($x == '' ? (9 - count($d)) * 4 : 4) .'s', $x);
	return $res;
}

/**
 * Convert an IP in Hex format to an IPv4 or IPv6 format
 * @param string $int_ip IP encoded
 * @return string $ip format xxx.xxx.xxx.xxx in IPv4 and xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx in IPv6
 */
function decode_ip($int_ip)
{ 
    if (strlen($int_ip) == 32) {
        $int_ip = substr(chunk_split($int_ip, 4, ':'), 0, 39);
        $int_ip = ':'. implode(':', array_map("hexhex", explode(':',$int_ip))) .':';
        preg_match_all("/(:0)+/", $int_ip, $zeros);
        if (count($zeros[0]) > 0) {
            $match = '';
            foreach($zeros[0] as $zero)
                if (strlen($zero) > strlen($match))
                    $match = $zero;
            $int_ip = preg_replace('/'. $match .'/', ':', $int_ip, 1);
        }
        return preg_replace('/(^:([^:]))|(([^:]):$)/', '$2$4', $int_ip);
    }
    $hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
    return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}
/**
 * Converts a hex value to another hew value (depnding of the current php version on the server)
 * @param string $value The initial hexvalue
 * @return string the new hew value
 */
function hexhex($value) {
	return dechex(hexdec($value));
};

/**
 * Generates a random password with 6 chars
 * @return string $password The generated password
 */
function password_generator()
{
    $string = "abBDEFcdefghijkmnPQRSTUVWXYpqrst23456789";
    srand((double)microtime() * 1000000);
    $password = '';
    for ($i = 0; $i < 6; $i++) {
        $password .= $string[rand() % strlen($string)];
    }
    return $password;
}
/**
 * Initialisation of the cache for all Mod settings
 * 
 * Generates a file which contains all configurations for different installed OGSpy Modules
 */
function init_mod_cache()
{
    global $cache_mod, $server_config;

    // Load cached config
    $filename = 'cache/cache_mod.php';

    if (file_exists($filename)) {
        include $filename;
        // regeneration si besoin
        if ((filemtime($filename) + $server_config['mod_cache']) < time()) {
            generate_mod_cache();
        }

    } else {
        generate_mod_cache();
        if (file_exists($filename)) {
            include $filename; // on reinjecte le fichier s'il existe'
        }

    }

}

/**
 * Initialisation of the cache for all Server settings
 * 
 * Generates a file which contains all configurations for the OGSpy Server
 */
function init_serverconfig()
{
    global $server_config;

    // Load cached config
    $filename = 'cache/cache_config.php';

    if (file_exists($filename)) {
        include $filename;
        // regeneration si besoin
        if ((filemtime($filename) + $server_config['config_cache']) < time()) {
            generate_config_cache();
        }

    } else {
        generate_config_cache();
        if (file_exists($filename)) {
            include $filename; // on reinjecte le fichier s'il existe'
        }

    }

}
/**
 *  Updates in the database all configurations displayed in the display administration Page.
 * @todo Query: update  . TABLE_CONFIG .  set config_value =  . $pub_enable_portee_missil . where config_name = \'portee_missil\'
 * @todo Query: "update " . TABLE_CONFIG . " set config_value = " . $pub_galaxy_by_line_stat . " where config_name = 'galaxy_by_line_stat'"
 * @todo Query: "update " . TABLE_CONFIG . " set config_value = " . $pub_system_by_line_stat . " where config_name = 'system_by_line_stat'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $pub_open_user ."' where config_name = 'open_user'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $pub_open_admin . "' where config_name = 'open_admin'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_enable_stat_view ." where config_name = 'enable_stat_view'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_enable_members_view ." where config_name = 'enable_members_view'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_nb_colonnes_ally) ."' where config_name = 'nb_colonnes_ally'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($color_ally) . "' where config_name = 'color_ally'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_galaxy_by_line_ally ." where config_name = 'galaxy_by_line_ally'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_system_by_line_ally ." where config_name = 'system_by_line_ally'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $pub_enable_register_view ."' where config_name = 'enable_register_view'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_register_alliance) ."' where config_name = 'register_alliance'"
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_register_forum) ."' where config_name = 'register_forum'"
 */
function set_server_view()
{
    global $db, $user_data;
    global $pub_enable_portee_missil, $pub_enable_members_view, $pub_enable_stat_view,
        $pub_galaxy_by_line_stat, $pub_system_by_line_stat, $pub_galaxy_by_line_ally, $pub_system_by_line_ally,
        $pub_nb_colonnes_ally, $pub_color_ally, $pub_enable_register_view, $pub_register_alliance,
        $pub_register_forum, $pub_open_user, $pub_open_admin;

    if (!check_var($pub_enable_members_view, "Num") || !check_var($pub_enable_stat_view,
        "Num") || !check_var($pub_galaxy_by_line_stat, "Num") || !check_var($pub_system_by_line_stat,
        "Num") || !check_var($pub_galaxy_by_line_ally, "Num") || !check_var($pub_system_by_line_ally,
        "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("planetindex.php?action=message&id_message=forbidden&info");
    }

    if (!isset($pub_galaxy_by_line_stat) || !isset($pub_system_by_line_stat) || !
        isset($pub_galaxy_by_line_ally) || !isset($pub_system_by_line_ally)) {
        redirection("index.php?action=message&id_message=setting_server_view_failed&info");
    }

    if (is_null($pub_enable_portee_missil))
        $pub_enable_portee_missil = 0;
    if (is_null($pub_enable_stat_view))
        $pub_enable_stat_view = 0;
    if (is_null($pub_enable_members_view))
        $pub_enable_members_view = 0;

    $break = false;


    if (!is_numeric($pub_galaxy_by_line_stat))
        $break = true;
    if (!is_numeric($pub_system_by_line_stat))
        $break = true;
    if ($pub_enable_stat_view != 0 && $pub_enable_stat_view != 1)
        $break = true;
    if ($pub_enable_members_view != 0 && $pub_enable_members_view != 1)
        $break = true;
    if (!is_numeric($pub_galaxy_by_line_ally))
        $break = true;
    if (!is_numeric($pub_system_by_line_ally))
        $break = true;
    if ($pub_nb_colonnes_ally == 0 || $pub_nb_colonnes_ally > 9 || !is_numeric($pub_nb_colonnes_ally))
        $break = true;
    if ($pub_enable_register_view != 0 && $pub_enable_register_view != 1)
        $break = true;

    if ($break) {
        redirection("index.php?action=message&id_message=setting_server_view_failed&info");
    }

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_enable_portee_missil .
        " where config_name = 'portee_missil'";
    $db->sql_query($request);

    //
    if ($pub_galaxy_by_line_stat < 1)
        $pub_galaxy_by_line_stat = 1;
    if ($pub_galaxy_by_line_stat > 100)
        $pub_galaxy_by_line_stat = 100;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_galaxy_by_line_stat .
        " where config_name = 'galaxy_by_line_stat'";
    $db->sql_query($request);

    //
    if ($pub_system_by_line_stat < 1)
        $pub_system_by_line_stat = 1;
    if ($pub_system_by_line_stat > 100)
        $pub_system_by_line_stat = 100;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_system_by_line_stat .
        " where config_name = 'system_by_line_stat'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $pub_open_user .
        "' where config_name = 'open_user'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $pub_open_admin .
        "' where config_name = 'open_admin'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_enable_stat_view .
        " where config_name = 'enable_stat_view'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_enable_members_view .
        " where config_name = 'enable_members_view'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_nb_colonnes_ally) .
        "' where config_name = 'nb_colonnes_ally'";
    $db->sql_query($request);


    $array = $pub_color_ally; //die(var_dump($pub_color_ally));
    $color_ally = implode("_", $array);
    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($color_ally) . "' where config_name = 'color_ally'";
    $db->sql_query($request);

    //
    if ($pub_galaxy_by_line_ally < 1)
        $pub_galaxy_by_line_ally = 1;
    if ($pub_galaxy_by_line_ally > 100)
        $pub_galaxy_by_line_ally = 100;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_galaxy_by_line_ally .
        " where config_name = 'galaxy_by_line_ally'";
    $db->sql_query($request);

    //
    if ($pub_system_by_line_ally < 1)
        $pub_system_by_line_ally = 1;
    if ($pub_system_by_line_ally > 100)
        $pub_system_by_line_ally = 100;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_system_by_line_ally .
        " where config_name = 'system_by_line_ally'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $pub_enable_register_view .
        "' where config_name = 'enable_register_view'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_register_alliance) .
        "' where config_name = 'register_alliance'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_register_forum) .
        "' where config_name = 'register_forum'";
    $db->sql_query($request);

    // mise a jour des caches avec les modifs
    generate_config_cache();
    log_("set_server_view");
    redirection("index.php?action=administration&subaction=affichage");
}
/**
 *  Updates in the database all configurations displayed in the parameters administration Page.
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_server_active ." where config_name = 'server_active'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_debug_log ." where config_name = 'debug_log'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_block_ratio ." where config_name = 'block_ratio'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_log_phperror ." where config_name = 'log_phperror'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_max_favorites ." where config_name = 'max_favorites'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_max_favorites_spy ." where config_name = 'max_favorites_spy'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_ratio_limit ." where config_name = 'ratio_limit'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_max_spyreport ." where config_name = 'max_spyreport'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_max_battlereport ." where config_name = 'max_battlereport'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_session_time ." where config_name = 'session_time'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_max_keeplog ." where config_name = 'max_keeplog'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_default_skin) . "' where config_name = 'default_skin'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_reason) . "' where config_name = 'reason'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_ally_protection) ."' where config_name = 'ally_protection'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_url_forum) . "' where config_name = 'url_forum'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_max_keeprank ." where config_name = 'max_keeprank'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_keeprank_criterion) ."' where config_name = 'keeprank_criterion'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_max_keepspyreport ." where config_name = 'max_keepspyreport'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_servername) . "' where config_name = 'servername'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $db->sql_escape_string($pub_allied) . "' where config_name = 'allied'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_disable_ip_check ." where config_name = 'disable_ip_check'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_num_of_galaxies ." where config_name = 'num_of_galaxies'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_num_of_systems ." where config_name = 'num_of_systems'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $pub_ddr ."' where config_name = 'ddr'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $pub_astro_strict ."' where config_name = 'astro_strict'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $pub_uni_arrondi_galaxy ."' where config_name = 'uni_arrondi_galaxy'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = '" . $pub_uni_arrondi_system ."' where config_name = 'uni_arrondi_system'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_speed_uni ." where config_name = 'speed_uni'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_mod_cache ." where config_name = 'mod_cache'";
 * @todo Query : "update " . TABLE_CONFIG . " set config_value = " . $pub_config_cache ." where config_name = 'config_cache'";
 */
function set_serverconfig()
{
    global $db, $user_data, $server_config;
    global $pub_max_battlereport, $pub_max_favorites, $pub_max_favorites_spy, $pub_max_spyreport,
        $pub_server_active, $pub_session_time, $pub_max_keeplog, $pub_default_skin, $pub_debug_log,
        $pub_reason, $pub_ally_protection, $pub_url_forum, $pub_max_keeprank, $pub_keeprank_criterion,
        $pub_max_keepspyreport, $pub_servername, $pub_allied, $pub_disable_ip_check, $pub_num_of_galaxies,
        $pub_num_of_systems, $pub_log_phperror, $pub_block_ratio, $pub_ratio_limit, $pub_speed_uni,
        $pub_ddr, $pub_astro_strict, $pub_uni_arrondi_galaxy, $pub_uni_arrondi_system, $pub_config_cache, $pub_mod_cache;


    if (!isset($pub_num_of_galaxies))
        $pub_num_of_galaxies = intval($server_config['num_of_galaxies']);
    if (!isset($pub_num_of_systems))
        $pub_num_of_systems = intval($server_config['num_of_systems']);

    if (!check_var($pub_max_battlereport, "Num") || !check_var($pub_max_favorites,
        "Num") || !check_var($pub_max_favorites_spy, "Num") || !check_var($pub_ratio_limit,
        "Special", "#^[\w\s,\.\-]+$#") || !check_var($pub_max_spyreport, "Num") || !
        check_var($pub_server_active, "Num") || !check_var($pub_session_time, "Num") ||
        !check_var($pub_max_keeplog, "Num") || !check_var($pub_default_skin, "URL") || !
        check_var($pub_debug_log, "Num") || !check_var($pub_block_ratio, "Num") || !
        check_var(stripslashes($pub_reason), "Text") || !check_var($pub_ally_protection,
        "Special", "#^[\w\s,\.\-]+$#") || !check_var($pub_url_forum, "URL") || !
        check_var($pub_max_keeprank, "Num") || !check_var($pub_keeprank_criterion,
        "Char") || !check_var($pub_max_keepspyreport, "Num") || !check_var(stripslashes
        ($pub_servername), "Text") || !check_var($pub_allied, "Special", "#^[\w\s,\.\-]+$#") ||
        !check_var($pub_disable_ip_check, "Num") || !check_var($pub_num_of_galaxies,
        "Galaxies") || !check_var($pub_num_of_systems, "Galaxies") || !check_var($pub_config_cache,
        "Num") || !check_var($pub_mod_cache, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("planetindex.php?action=message&id_message=forbidden&info");
    }

    if (!isset($pub_max_battlereport) || !isset($pub_max_favorites) || !isset($pub_max_favorites_spy) ||
        !isset($pub_ratio_limit) || !isset($pub_max_spyreport) || !isset($pub_session_time) ||
        !isset($pub_max_keeplog) || !isset($pub_default_skin) || !isset($pub_reason) ||
        !isset($pub_ally_protection) || !isset($pub_url_forum) || !isset($pub_max_keeprank) ||
        !isset($pub_keeprank_criterion) || !isset($pub_max_keepspyreport) || !isset($pub_servername) ||
        !isset($pub_allied) || !isset($pub_mod_cache) || !isset($pub_config_cache)) {
        redirection("index.php?action=message&id_message=setting_serverconfig_failed&info");
    }

    if (is_null($pub_server_active))
        $pub_server_active = 0;
    if (is_null($pub_disable_ip_check))
        $pub_disable_ip_check = 0;
    if (is_null($pub_log_phperror))
        $pub_log_phperror = 0;

    if (is_null($pub_debug_log))
        $pub_debug_log = 0;
    if (is_null($pub_block_ratio))
        $pub_block_ratio = 0;

    $break = false;


    if ($pub_server_active != 0 && $pub_server_active != 1)
        $break = true;
    if ($pub_debug_log != 0 && $pub_debug_log != 1)
        $break = true;
    if ($pub_block_ratio != 0 && $pub_block_ratio != 1)
        $break = true;
    if (!is_numeric($pub_max_favorites))
        $break = true;
    if (!is_numeric($pub_max_favorites_spy))
        $break = true;
    if (!is_numeric($pub_ratio_limit))
        $break = true;
    if (!is_numeric($pub_max_spyreport))
        $break = true;
    if (!is_numeric($pub_max_battlereport))
        $break = true;
    if (!is_numeric($pub_session_time))
        $break = true;
    if (!is_numeric($pub_max_keeplog))
        $break = true;
    if ($pub_disable_ip_check != 0 && $pub_disable_ip_check != 1)
        $break = true;
    if ($pub_log_phperror != 0 && $pub_log_phperror != 1)
        $break = true;

    if ($break) {
        redirection("index.php?action=message&id_message=setting_serverconfig_failed&info");
    }

    if (($pub_num_of_galaxies != intval($server_config['num_of_galaxies'])) || ($pub_num_of_systems !=
        intval($server_config['num_of_systems']))) {
        resize_db($pub_num_of_galaxies, $pub_num_of_systems);
    }
    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_server_active .
        " where config_name = 'server_active'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_debug_log .
        " where config_name = 'debug_log'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_block_ratio .
        " where config_name = 'block_ratio'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_log_phperror .
        " where config_name = 'log_phperror'";
    $db->sql_query($request);
    //
    $pub_max_favorites = intval($pub_max_favorites);
    if ($pub_max_favorites < 0)
        $pub_max_favorites = 0;
    if ($pub_max_favorites > 99)
        $pub_max_favorites = 99;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_max_favorites .
        " where config_name = 'max_favorites'";
    $db->sql_query($request);

    //
    $pub_max_favorites_spy = intval($pub_max_favorites_spy);
    if ($pub_max_favorites_spy < 0)
        $pub_max_favorites_spy = 0;
    if ($pub_max_favorites_spy > 99)
        $pub_max_favorites_spy = 99;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_max_favorites_spy .
        " where config_name = 'max_favorites_spy'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_ratio_limit .
        " where config_name = 'ratio_limit'";
    $db->sql_query($request);

    //
    $pub_max_spyreport = intval($pub_max_spyreport);
    if ($pub_max_spyreport < 1)
        $pub_max_spyreport = 1;
    if ($pub_max_spyreport > 50)
        $pub_max_spyreport = 50;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_max_spyreport .
        " where config_name = 'max_spyreport'";
    $db->sql_query($request);

    //
    $pub_max_battlereport = intval($pub_max_battlereport);
    if ($pub_max_battlereport < 0)
        $pub_max_battlereport = 0;
    if ($pub_max_battlereport > 999)
        $pub_max_battlereport = 999;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_max_battlereport .
        " where config_name = 'max_battlereport'";
    $db->sql_query($request);

    //
    $pub_session_time = intval($pub_session_time);
    if ($pub_session_time < 5 && $pub_session_time != 0)
        $pub_session_time = 5;
    if ($pub_session_time > 180)
        $pub_session_time = 180;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_session_time .
        " where config_name = 'session_time'";
    $db->sql_query($request);

    //
    $pub_max_keeplog = intval($pub_max_keeplog);
    if ($pub_max_keeplog < 0)
        $pub_max_keeplog = 0;
    if ($pub_max_keeplog > 365)
        $pub_max_keeplog = 365;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_max_keeplog .
        " where config_name = 'max_keeplog'";
    $db->sql_query($request);

    //
    if (substr($pub_default_skin, strlen($pub_default_skin) - 1) != "/")
        $pub_default_skin .= "/";
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_default_skin) . "' where config_name = 'default_skin'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_reason) . "' where config_name = 'reason'";
    $db->sql_query($request);

    //
    if (substr($pub_ally_protection, strlen($pub_ally_protection) - 1) == ",")
        $pub_ally_protection = substr($pub_ally_protection, 0, strlen($pub_ally_protection) -
            1);
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_ally_protection) .
        "' where config_name = 'ally_protection'";
    $db->sql_query($request);

    //
    if ($pub_url_forum != "" && !preg_match("#^http://#", $pub_url_forum))
        $pub_url_forum = "http://" . $pub_url_forum;
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_url_forum) . "' where config_name = 'url_forum'";
    $db->sql_query($request);

    //
    $pub_max_keeprank = intval($pub_max_keeprank);
    if ($pub_max_keeprank < 1)
        $pub_max_keeprank = 1;
    if ($pub_max_keeprank > 999)
        $pub_max_keeprank = 999;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_max_keeprank .
        " where config_name = 'max_keeprank'";
    $db->sql_query($request);

    //
    if ($pub_keeprank_criterion != "quantity" && $pub_keeprank_criterion != "day")
        $pub_keeprank_criterion = "quantity";
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_keeprank_criterion) .
        "' where config_name = 'keeprank_criterion'";
    $db->sql_query($request);

    //
    $pub_max_keepspyreport = intval($pub_max_keepspyreport);
    if ($pub_max_keepspyreport < 1)
        $pub_max_keepspyreport = 1;
    if ($pub_max_keepspyreport > 999)
        $pub_max_keepspyreport = 999;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_max_keepspyreport .
        " where config_name = 'max_keepspyreport'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_servername) . "' where config_name = 'servername'";
    $db->sql_query($request);

    //
    if (substr($pub_allied, strlen($pub_allied) - 1) == ",")
        $pub_allied = substr($pub_allied, 0, strlen($pub_allied) - 1);
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $db->
        sql_escape_string($pub_allied) . "' where config_name = 'allied'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_disable_ip_check .
        " where config_name = 'disable_ip_check'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_num_of_galaxies .
        " where config_name = 'num_of_galaxies'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_num_of_systems .
        " where config_name = 'num_of_systems'";
    $db->sql_query($request);

    //
    if (!isset($pub_ddr) || !is_numeric($pub_ddr))
        $pub_ddr = 0;
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $pub_ddr .
        "' where config_name = 'ddr'";
    $db->sql_query($request);

    //
    if (!isset($pub_astro_strict) || !is_numeric($pub_astro_strict))
        $pub_astro_strict = 0;
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $pub_astro_strict .
        "' where config_name = 'astro_strict'";
    $db->sql_query($request);

    //
    if (!isset($pub_uni_arrondi_galaxy) || !is_numeric($pub_uni_arrondi_galaxy))
        $pub_uni_arrondi_galaxy = 0;
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $pub_uni_arrondi_galaxy .
        "' where config_name = 'uni_arrondi_galaxy'";
    $db->sql_query($request);
    
    //
    if (!isset($pub_uni_arrondi_system) || !is_numeric($pub_uni_arrondi_system))
        $pub_uni_arrondi_system = 0;
    $request = "update " . TABLE_CONFIG . " set config_value = '" . $pub_uni_arrondi_system .
        "' where config_name = 'uni_arrondi_system'";
    $db->sql_query($request);
    
    //
    if (!is_numeric($pub_speed_uni) || $pub_speed_uni < 1)
        $pub_speed_uni = 1;
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_speed_uni .
        " where config_name = 'speed_uni'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_mod_cache .
        " where config_name = 'mod_cache'";
    $db->sql_query($request);

    //
    $request = "update " . TABLE_CONFIG . " set config_value = " . $pub_config_cache .
        " where config_name = 'config_cache'";
    $db->sql_query($request);


    // mise a jour des caches avec les mofids
    generate_config_cache();
    log_("set_serverconfig");
    redirection("index.php?action=administration&subaction=parameter");
}
/**
 * Returns the Status of the Database used size.
 * @return Array [Server], et [Total]
 * @todo : Query : "SHOW TABLE STATUS"
 */
function db_size_info()
{
    global $db;
    global $table_prefix;

    $dbSizeServer = 0;
    $dbSizeTotal = 0;

    $request = "SHOW TABLE STATUS";
    $result = $db->sql_query($request);
    while ($row = $db->sql_fetch_assoc($result)) {
        $dbSizeTotal += $row['Data_length'] + $row['Index_length'];
        if (preg_match("#^" . $table_prefix . ".*$#", $row['Name'])) {
            $dbSizeServer += $row['Data_length'] + $row['Index_length'];
        }
    }

    $bytes = array('Octets', 'Ko', 'Mo', 'Go', 'To');

    if ($dbSizeServer < 1024)
        $dbSizeServer = 1;
    for ($i = 0; $dbSizeServer > 1024; $i++)
        $dbSizeServer /= 1024;
    $dbSize_info["Server"] = round($dbSizeServer, 2) . " " . $bytes[$i];

    if ($dbSizeTotal < 1024)
        $dbSizeTotal = 1;
    for ($i = 0; $dbSizeTotal > 1024; $i++)
        $dbSizeTotal /= 1024;
    $dbSize_info["Total"] = round($dbSizeTotal, 2) . " " . $bytes[$i];

    return $dbSize_info;
}
/**
 * Function to Optimize all tables of the OGSpy Database
 * @param boolean $maintenance_action true if no url redirection is requested,false to redirect to another page
 * @todo : Query : "SHOW TABLES"
 */
function db_optimize($maintenance_action = false)
{
    global $db;

    $dbSize_before = db_size_info();
    $dbSize_before = $dbSize_before["Total"];

    $request = 'SHOW TABLES';
    $res = $db->sql_query($request);
    while (list($table) = $db->sql_fetch_row($res)) {
        $request = 'OPTIMIZE TABLE ' . $table;
        $db->sql_query($request);
    }
    // 09-07-2012 : Commenté car cette table n'est plus utilisée
    //$request = 'TRUNCATE ' . TABLE_UNIVERSE_TEMPORARY;
    //$db->sql_query($request);

    $dbSize_after = db_size_info();
    $dbSize_after = $dbSize_after["Total"];

    if (!$maintenance_action) {
        redirection("index.php?action=message&id_message=db_optimize&info=" . $dbSize_before .
            "¤" . $dbSize_after);
    }
}
/**
 * Adapt the database to fit on the number of galaxies and solar systems
 * @param int $new_num_of_galaxies Galaxy total
 * @param int $new_num_of_systems Solar Systems total
 * @return null
 * @todo : Query : sql_query("DELETE FROM " . TABLE_UNIVERSE . " WHERE galaxy > $new_num_of_galaxies");
 * @todo : Query : sql_query("UPDATE " . TABLE_USER . " SET user_galaxy=1 WHERE user_galaxy > $new_num_of_galaxies");
 * @todo : Query : sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE galaxy > $new_num_of_galaxies");
 * @todo : Query : sql_query("DELETE FROM " . TABLE_UNIVERSE . " WHERE system > $new_num_of_systems");
 * @todo : Query : sql_query("UPDATE " . TABLE_USER . " SET user_system=1 WHERE user_system > $new_num_of_systems");
 * @todo : Query : sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE system > $new_num_of_systems");
 * @todo : Query : "ALTER TABLE `" . TABLE_UNIVERSE . "` CHANGE `galaxy` `galaxy` ENUM("; -> Voir Fonction
 * @todo : Query : "ALTER TABLE `" . TABLE_USER ." CHANGE `user_galaxy` `user_galaxy` -> Voir fonction
 * @todo : Query : $request = "ALTER TABLE `" . TABLE_USER_FAVORITE ."` CHANGE `galaxy` `galaxy` ENUM(" -> Voir fonction
 * @todo : Query : "REPLACE INTO " . TABLE_CONFIG ." (config_name, config_value) VALUES ('num_of_galaxies','$new_num_of_galaxies')";
 * @todo : Query : $requests = "REPLACE INTO " . TABLE_CONFIG ." (config_name, config_value) VALUES ('num_of_systems','$new_num_of_systems')";
*/
function resize_db($new_num_of_galaxies, $new_num_of_systems)
{
    global $db, $db_host, $db_user, $db_password, $db_database, $table_prefix, $server_config;

    // si on reduit on doit supprimez toutes les entrées qui font reference au systemes ou galaxies que l'on va enlever
    if ($new_num_of_galaxies < intval($server_config['num_of_galaxies'])) {
        $db->sql_query("DELETE FROM " . TABLE_UNIVERSE . " WHERE galaxy > $new_num_of_galaxies");
        $db->sql_query("UPDATE " . TABLE_USER . " SET user_galaxy=1 WHERE user_galaxy > $new_num_of_galaxies");
        $db->sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE galaxy > $new_num_of_galaxies");
    }
    if ($new_num_of_systems < intval($server_config['num_of_systems'])) {
        $db->sql_query("DELETE FROM " . TABLE_UNIVERSE . " WHERE system > $new_num_of_systems");
        $db->sql_query("UPDATE " . TABLE_USER . " SET user_system=1 WHERE user_system > $new_num_of_systems");
        $db->sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE system > $new_num_of_systems");
    }

    $request = "ALTER TABLE `" . TABLE_UNIVERSE . "` CHANGE `galaxy` `galaxy` ENUM(";
    for ($i = 1; $i < $new_num_of_galaxies; $i++)
        $request .= "'$i' , ";
    $request .= "'$new_num_of_galaxies') NOT NULL DEFAULT '1'";
    $db->sql_query($request);

    $request = "ALTER TABLE `" . TABLE_USER .
        "` CHANGE `user_galaxy` `user_galaxy` ENUM(";
    for ($i = 1; $i < $new_num_of_galaxies; $i++)
        $request .= "'$i' , ";
    $request .= "'$new_num_of_galaxies') NOT NULL DEFAULT '1'";
    $db->sql_query($request);

    $request = "ALTER TABLE `" . TABLE_USER_FAVORITE .
        "` CHANGE `galaxy` `galaxy` ENUM(";
    for ($i = 1; $i < $new_num_of_galaxies; $i++)
        $request .= "'$i' , ";
    $request .= "'$new_num_of_galaxies') NOT NULL DEFAULT '1'";
    $db->sql_query($request);

    $server_config['num_of_galaxies'] = "$new_num_of_galaxies";
    $server_config['num_of_systems'] = "$new_num_of_systems";
    $requests = "REPLACE INTO " . TABLE_CONFIG .
        " (config_name, config_value) VALUES ('num_of_galaxies','$new_num_of_galaxies')";
    $db->sql_query($request);
    $requests = "REPLACE INTO " . TABLE_CONFIG .
        " (config_name, config_value) VALUES ('num_of_systems','$new_num_of_systems')";
    $db->sql_query($request);

    log_("set_db_size");
}
/**
 * File Log size on the Server
 * @return Array tableau [type] and [size]
 */
function log_size_info()
{
    $logSize = 0;
    $res = opendir(PATH_LOG);
    $directory = array();
    //Récupération de la liste des fichiers présents dans les répertoires répertoriés
    while ($file = readdir($res)) {
        if ($file != "." && $file != "..") {
            if (is_dir(PATH_LOG . $file)) {
                $directory[] = PATH_LOG . $file;
            }
        }
    }
    closedir($res);

    foreach ($directory as $v) {
        $res = opendir($v);
        $directory = array();
        //Récupération de la liste des fichiers présents dans les répertoires répertoriés
        while ($file = readdir($res)) {
            if ($file != "." && $file != "..") {
                $logSize += @filesize($v . "/" . $file);
            }
        }
        closedir($res);
    }

    $bytes = array('Octets', 'Ko', 'Mo', 'Go', 'To');

    if ($logSize < 1024)
        $logSize = 1;

    for ($i = 0; $logSize > 1024; $i++)
        $logSize /= 1024;

    $log_size_info['size'] = round($logSize, 2);
    $log_size_info['type'] = $bytes[$i];

    return $log_size_info;
}
/**
 * Checks the availability of a log File
 * @param int $date Requested Date 
 * @return boolean true if the log file exists
 * @internal To be improved...
 */
function log_check_exist($date)
{
    if (!isset($date))
        redirection("index.php?action=message&id_message=errorfatal&info");

    $typelog = array("sql", "log", "txt");

    $root = PATH_LOG;
    $path = opendir("$root");

    //Récupération de la liste des répertoires correspondant à cette date
    while ($file = readdir($path)) {
        if ($file != "." && $file != "..") {
            if (is_dir($root . $file) && preg_match("/^" . $date . "/", $file))
                $directories[] = $file;
        }
    }
    closedir($path);

    if (!isset($directories)) {
        return false;
    }

    foreach ($directories as $d) {
        $path = opendir($root . $d);

        while ($file = readdir($path)) {
            if ($file != "." && $file != "..") {
                $extension = substr($file, (strrpos($file, ".") + 1));
                if (in_array($extension, $typelog)) {
                    $files[] = $d . "/" . $file;
                }
            }
        }
        closedir($path);
    }

    if (!isset($files)) {
        return false;
    }

    return true;
}
/**
 * Sends a Compressed archive to the browser for a specific date
 * @global array $user_data
 */
function log_extractor()
{
    global $pub_date, $user_data;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("index.php?action=message&id_message=forbidden&info");
    }

    if (!isset($pub_date))
        redirection("index.php?action=message&id_message=errorfatal&info");
	
	$typelog = array("sql", "log", "txt");

    $root = PATH_LOG;
	$zip_file= $root."log.zip";
    $path = opendir("$root");
	unlink($zip_file);
	
    //Récupération de la liste des répertoires correspondant à cette date
    while ($file = readdir($path)) {
        if ($file != "." && $file != "..") {
            if (is_dir($root . $file) && preg_match("/^" . $pub_date . "/", $file))
                $directories[] = $file;
        }
    }
    closedir($path);

    if (!isset($directories)) {
        redirection("index.php?action=message&id_message=log_missing&info");
    }

    foreach ($directories as $d) {
        $path = opendir($root . $d);

        while ($file = readdir($path)) {
            if ($file != "." && $file != "..") {
                $extension = substr($file, (strrpos($file, ".") + 1));
                if (in_array($extension, $typelog)) {
                    $files[] = $d . "/" . $file;
                }
            }
        }
        closedir($path);
    }

    if (!isset($files)) {
        redirection("index.php?action=message&id_message=log_missing&info");
    }

    // création d'un objet 'zipfile'
	
	$zip = new ZipArchive;
	$zip->open($zip_file, ZipArchive::CREATE);
    foreach ($files as $filename) {
        // ajout du fichier dans cet objet
        $zip->addFile($root.$filename);
		log_('debug',"fichier dans archive:".$filename);
		}
	
	// production de l'archive Zip
    $zip->close();

    // entêtes HTTP
    header('Content-Type: application/x-zip');
    // force le téléchargement
    header('Content-disposition: attachment; filename=log_' . $pub_date . '.zip');
	header('Content-Transfer-Encoding: binary');

    // envoi du fichier au navigateur
    flush();
	readfile($zip_file);
}

/**
 * Deletes a specified Log File
 *
 */
function log_remove()
{
    global $pub_date, $user_data, $pub_directory;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1)
        redirection("index.php?action=message&id_message=forbidden&info");

    if ($pub_directory == true) {
        @unlink("journal/" . $pub_date . "/log_" . $pub_date . ".log");
        @unlink("journal/" . $pub_date . "/index.htm");
        if (rmdir("journal/" . $pub_date)) {
            redirection("index.php?action=message&id_message=log_remove&info");
        } else {
            redirection("index.php?action=message&id_message=log_missing&info");
        }
    } else {
        if (unlink("journal/" . $pub_date . "/log_" . $pub_date . ".log")) {
            redirection("index.php?action=message&id_message=log_remove&info");
        } else {
            redirection("index.php?action=message&id_message=log_missing&info");
        }
    }
}

/**
 * Log file cleaning according the the Server configuration
 */
function log_purge()
{
    global $server_config;

    $time = $server_config["max_keeplog"];
    $limit = time() - (60 * 60 * 24 * $time);
    $limit = intval(date("ymd", $limit));

    $root = PATH_LOG;
    $path = opendir("$root");
    while ($file = readdir($path)) {
        if ($file != "." && $file != "..") {
            if (is_dir($root . $file) && intval($file) < $limit && @ereg("[0-9]{6}", $file)) {
                $directories[] = $file;
            }
        }
    }
    closedir($path);

    if (!isset($directories)) {
        return;
    }

    $files = array();
    foreach ($directories as $d) {
        $path = opendir($root . $d);

        while ($file = readdir($path)) {
            if ($file != "." && $file != "..") {
                $extension = substr($file, (strrpos($file, ".") + 1));
                unlink($root . $d . "/" . $file);
            }
        }
        closedir($path);
        rmdir($root . $d);
    }
}
/**
 * Formats a number.
 * @param int $number The value to be converted
 * @param int $decimal Sets the number of decimal points.
 * @return string The number with the new formatting
 */
function formate_number($number, $decimal = 0)
{
    return number_format($number, $decimal, ",", " ");
}

/**
 * Server Maintenance (Cleaning of Galaxy, Spy reports and Logs)
 */
function maintenance_action()
{
    global $db, $server_config;

    $time = mktime(0, 0, 0);
    if (isset($server_config["last_maintenance_action"]) && $time > $server_config["last_maintenance_action"]) {
        galaxy_purge_ranking();
        log_purge();
        galaxy_purge_spy();
        
        $request = "update " . TABLE_CONFIG . " set config_value = '" . $time . "' where config_name = 'last_maintenance_action'";
        $db->sql_query($request);
    }
}
/**
 * Security Function : Variable Verification according the type(Pseudo, Password, string, number,...)
 * @param string $value Value of the data to check
 * @param string $type_check Type of the value (Pseudo_Groupname, Pseudo_ingame, Password, Text, CharNum, Char, Num, Galaxies, URL, Special)
 * @param string $mask Can be used to specify a Regex for the check when the type is set as Special
 * @param boolean $auth_null Workarround linked to the authentification
 * @return boolean true if the value is ok or empty and false if the checking has failed.
 */
function check_var($value, $type_check, $mask = "", $auth_null = true)
{
    if ($auth_null && $value == "") {
        return true;
    }

    switch ($type_check) {
            //Pseudo des membres
        case "Pseudo_Groupname":
            if (!preg_match("#^[\w\s\-]{3,15}$#", $value)) {
                log_("check_var", array("Pseudo_Groupname", $value));
                return false;
            }
            break;

        //Pseudo ingame
        case "Pseudo_ingame": // caracteres autorises entre 3 et 20 ( interdit au 05/11/11 = > &"'()# `/,;+ ) 
            if (!preg_match("#^[\w@äàçéèêëïîöôûü\^\{\}\[\]\.\*\-_~%§]{3,20}$#", $value)) {
                log_("check_var", array("Text", $value));
                return false;
            }
            break;

            //Mot de passe des membres
        case "Password":
            if (!preg_match("#^[\w\s\-]{6,20}$#", $value)) {
                return false;
            }
            break;

            //Chaîne de caractères avec espace
        case "Text":
            if (!preg_match("#^[\w'äàçéèêëïîöôûü\s\.\*\-]+$#", $value)) {
                log_("check_var", array("Text", $value));
                return false;
            }
            break;

            //Chaîne de caractères et  chiffre
        case "CharNum":
            if (!preg_match("#^[\w\.\*\-\#]+$#", $value)) {
                log_("check_var", array("CharNum", $value));
                return false;
            }
            break;

            //Caractères
        case "Char":
            if (!preg_match("#^[[:alpha:]_\.\*\-]+$#", $value)) {
                log_("check_var", array("Char", $value));
                return false;
            }
            break;

            //Chiffres
        case "Num":
            if (!preg_match("#^[[:digit:]]+$#", $value)) {
                log_("check_var", array("Num", $value));
                return false;
            }
            break;
		            //Chiffres
        case "Email":
            if (!preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $value)) {
                log_("check_var", array("Email", $value));
                return false;
            }
            break;	

            //Galaxies
        case "Galaxies":
            if ($value < 1 || $value > 999) {
                log_("check_var", array("Galaxy or system", $value));
                return false;
            }
            break;

            //Adresse internet
        case "URL":
            if (!preg_match("#^(((?:http?)://)?(?(2)(www\.)?|(www\.){1})?[-a-z0-9~_]{2,}(\.[-a-z0-9~._]{2,})?[-a-z0-9~_\/&\?=.]{2,})$#i",
                $value)) {
                log_("check_var", array("URL", $value));
                return false;
            }
            break;

            //Planète, Joueur et alliance
        case "Galaxy":
            //		if (!preg_match("#^[\w\s\.\*\-]+$#", $value)) {
            //			log_("check_var", array("Galaxy", $value));
            //			return false;
            //		}
            break;

            //Rapport d'espionnage
        case "Spyreport":
            //		if (!preg_match("#^[\w\s\[\]\:\-'%\.\*]+$#", $value)) {
            //			log_("check_var", array("Spyreport", $value));
            //			return false;
            //		}
            break;

            //Masque paramétrable
        case "Special":
            if (!preg_match($mask, $value)) {
                log_("check_var", array("Special", $value));
                return false;
            }
            break;

        default:
            return false;
    }

    return true;
}
/**
 * Resets the User for imported datas.
 * @param boolean $maintenance_action If true the function does not redirect the user to the raz_ration Page
 */
function admin_raz_ratio($maintenance_action = false)
{
    global $db, $user_data;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] !=
        1) {
        die("Acces interdit");
    }

    $request = "UPDATE " . TABLE_USER . " set search='0'";
    $db->sql_query($request);

    if (!$maintenance_action) {
        redirection("index.php?action=message&id_message=raz_ratio&info");
    }
}
/**
 *  Microtime Value formatted for benchmark functions
 * @return int Current microtime
 */
function benchmark()
{
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];

    return $mtime;
}
/**
 * Security : HTTP GET Data verifications
 * @param string $secvalue The value to be checked
 * @return boolean true if the verification is ok
 */
function check_getvalue($secvalue)
{
    if (!is_array($secvalue)) {
        if ((preg_match("/<[^>]*script*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*object*\"?[^>]*>/i",
            $secvalue)) || (preg_match("/<[^>]*iframe*\"?[^>]*>/i", $secvalue)) || (preg_match
            ("/<[^>]*applet*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*meta*\"?[^>]*>/i",
            $secvalue)) || (preg_match("/<[^>]*style*\"?[^>]*>/i", $secvalue)) || (preg_match
            ("/<[^>]*form*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*img*\"?[^>]*>/i",
            $secvalue)) || (preg_match("/\([^>]*\"?[^)]*\)/i", $secvalue)) || (preg_match("/\"/i",
            $secvalue))) {
            return false;
        }
    } else {
        foreach ($secvalue as $subsecvalue) {
            if (!check_getvalue($subsecvalue))
                return false;
        }
    }
    return true;
}
/**
 * Security : HTTP POST Data verifications
 * @param string $secvalue The value to be checked
 * @return boolean true if the verification is ok
 */
function check_postvalue($secvalue)
{
    if (!is_array($secvalue)) {
        if ((preg_match("/<[^>]*script*\"?[^>]*>/", $secvalue)) || (preg_match("/<[^>]*style*\"?[^>]*>/",
            $secvalue))) {
            return false;
        }
    } else {
        foreach ($secvalue as $subsecvalue) {
            if (!check_postvalue($subsecvalue))
                return false;
        }
    }
    return true;
}


//\\ fonctions utilisable pour les mods //\\
/**
 * Funtion to install a new mod in OGSpy
 * @param string $mod_folder : Folder name which contains the mod
 * @todo Query: "SELECT title FROM " . TABLE_MOD . " WHERE title='" . $value_mod[0] ."'"."'"
 * @todo Query: "INSERT INTO " . TABLE_MOD .
                " (title, menu, action, root, link, version, active,admin_only) VALUES ('" . $value_mod[0] .
                "','" . $value_mod[1] . "','" . $value_mod[2] . "','" . $value_mod[3] . "','" .
                $value_mod[4] . "','" . $mod_version . "','" . $value_mod[5] . "','" . $value_mod[6] .
                "')"
 * @return boolean true if the mod has been correctly installed
 * @api
 */
function install_mod($mod_folder)
{
    global $db,$server_config;
    $is_ok = false;
    $filename = 'mod/' . $mod_folder . '/version.txt';
    if (file_exists($filename)) {
        $file = file($filename);
    }


    // On récupère les données du fichier version.txt
    $mod_version = trim($file[1]);
    $mod_config = trim($file[2]);

    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    $mod_required_ogspy = trim($file[3]);
    if(isset($mod_required_ogspy)){
        if (version_compare($mod_required_ogspy,$server_config["version"]) > 0 ){
            log_("mod_erreur_txt_version",$mod_folder);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);

    // On vérifie si le mod est déjà installé""
    $check = "SELECT title FROM " . TABLE_MOD . " WHERE title='" . $value_mod[0] ."'";
    $query_check = $db->sql_query($check);
    $result_check = $db->sql_numrows($query_check);

    if ($result_check != 0) {
    } else
        if (count($value_mod) == 7) {
            // On vérifie le nombre de valeur de l'explode
            $query = "INSERT INTO " . TABLE_MOD .
                " (title, menu, action, root, link, version, active,admin_only) VALUES ('" . $value_mod[0] .
                "','" . $value_mod[1] . "','" . $value_mod[2] . "','" . $value_mod[3] . "','" .
                $value_mod[4] . "','" . $mod_version . "','" . $value_mod[5] . "','" . $value_mod[6] .
                "')";
            $db->sql_query($query);
            $is_ok = true; /// tout c 'est bien passe'
        }
    return $is_ok;
}
/**
 * Function to uninstall an OGSpy Module
 * @param string $mod_uninstall_name : Mod name
 * @param string $mod_uninstall_table : Name of the Database table used by the Mod that we need to remove
 * @todo Query: "DELETE FROM " . TABLE_MOD . " WHERE title='" . $mod_uninstall_name ."'
 * @api
 */
function uninstall_mod($mod_uninstall_name, $mod_uninstall_table)
{
    global $db;
    $db->sql_query("DELETE FROM " . TABLE_MOD . " WHERE title='" . $mod_uninstall_name ."';");
    if (!empty($mod_uninstall_table)) {
        $db->sql_query("DROP TABLE IF EXISTS " . $mod_uninstall_table . "");
    }
}
/**
 * Fonction to update the OGSpy mod
 * @param string $mod_folder : Folder name which contains the mod
 * @param string $mod_name : Mod name
 * @todo Query: "UPDATE " . TABLE_MOD . " SET version='" . $mod_version ."' WHERE action='" . $mod_name . "'";
 * @return boolean true if the mod has been correctly updated
 * @api [Mod] Function to be called in the update.php file to set up the new version.
 */
function update_mod($mod_folder, $mod_name)
{
    global $db, $server_config;
    $is_oki = false;
    $filename = 'mod/' . $mod_folder . '/version.txt';
    if (file_exists($filename)) {
        $file = file($filename);
    } else {
        return $is_oki;
    }

    $mod_version = trim($file[1]);

    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    $mod_required_ogspy = trim($file[3]);
    if(isset($mod_required_ogspy)){
        if (version_compare($mod_required_ogspy,$server_config["version"]) > 0 ){
            log_("mod_erreur_txt_version",$mod_folder);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }


    $query = "UPDATE " . TABLE_MOD . " SET version='" . $mod_version .
        "' WHERE action='" . $mod_name . "'";
    $db->sql_query($query);
    $is_oki = true;
    return $is_oki;
}

/**
 * OGSpy Hash Function
 * @param string The string to Hash (usually the password)
 * todo : remplacer les sha1(md5()) d'ogspy par la fonction'
 * @return string Returns the hash of the input function
 */
function crypto($str)
{
    return md5(sha1($str));
}

/**
 * OGSpy Key Generator : This key will be the unique id of the current OGSpy installation.
 *
 * The current OGSpy Key is written in a file named parameters/key.php
 */
function generate_key()
{
    //création de la clef
    $str = "abcdefghijklmnopqrstuvwxyzABCDEVGHIJKLMOPQRSTUVWXYZ";
    srand((double)microtime() * 1000000);
    $pass = time();
    for ($i = 0; $i < 20; $i++) {
        $pass .= $str[rand() % strlen($str)];
    }
    $key = crypto($pass);
    // création du path
    $path = $_SERVER["SCRIPT_FILENAME"];;


    $key_php[] = '<?php';
    $key_php[] = '/***************************************************************************';
    $key_php[] = '*	filename	: key.php';
    $key_php[] = '*	generated	: ' . date("d/M/Y H:i:s");
    $key_php[] = '***************************************************************************/';
    $key_php[] = '';
    $key_php[] = 'if (!defined("IN_SPYOGAME")) die("Hacking attempt");';
    $key_php[] = '';
    $key_php[] = '//Paramètres unique a ne pas communiquer';
    $key_php[] = '$serveur_key = "' . $key . '";';
    $key_php[] = '$serveur_date = "' . time() . '";';
    $key_php[] = '$serveur_path = "' . $path . '";';
    $key_php[] = '';
    $key_php[] = 'define("OGSPY_KEY", TRUE);';
    $key_php[] = '?>';
    if (!write_file("./parameters/key.php", "w", $key_php)) {
        die("Echec , impossible de générer le fichier 'parameters/key.php'");
    }

}


/***********************************************************************
 ************** Fonctions pour table Google Cloud Messaging ************
 ***********************************************************************/

/** Storing new GCM user
 * @return : user true or false
 */
function storeGCMUser($name,$gcm_regid) {	
	global $db;
	$user_id = "0";
	$result = $db->sql_query("SELECT user_id FROM " . TABLE_USER . " WHERE user_name = '" . $name . "' OR user_stat_name = '" . $name . "'");
	
	$nbGcmUsers = $db->sql_numrows("SELECT * FROM " . TABLE_GCM_USERS . " WHERE gcm_regid = '" . $gcm_regid . "'");
	
	while (list($userid) = $db->sql_fetch_row($result))	{
		$user_id = $userid;		
	}
	
	//return "Nombre de users GCM = " . $nbGcmUsers;
	
	if($nbGcmUsers == 0){
		// insert user into database
		$result = $db->sql_query("INSERT INTO " . TABLE_GCM_USERS . "(user_id, gcm_regid, created_at) VALUES('" . $user_id ."' , '" . $gcm_regid . "', NOW())");
		// check for successful store
		if ($result) {
			return 1;
		} else {
			return 0;
		}
	} else {
		return -1;
	}
}

/** Storing new GCM user
* @return : user true or false
*/
function deleteGCMUser($gcm_regid) {
	global $db;

	// delete user in database
	$result = $db->sql_query("DELETE FROM " . TABLE_GCM_USERS . " WHERE gcm_regid = '" . $gcm_regid . "'");
	// check for successful store
	if ($result) {
		return true;
	} else {
		return false;
	}
}

/**
 * Getting all GCM users
 */
function getAllGCMUsers() {
	global $db;
	$query = "SELECT gcm.user_id AS user_id, users.user_name AS name, users.user_stat_name AS name2, users.user_email AS email, gcm.gcm_regid AS gcm_regid, gcm.created_at AS created_at, gcm.version_android AS version_android, gcm.version_ogspy AS version_ogspy, gcm.device AS device ".
			 "FROM " . TABLE_GCM_USERS . " gcm ".
			 "INNER JOIN " . TABLE_USER . " users ON users.user_id = gcm.user_id";
	return $db->sql_query($query);
}

/**
 * Getting all GCM users but not me
 */
function getAllGCMUsersExceptMe($id) {
	global $db;
	$query = "SELECT gcm_regid ".
			 "FROM " . TABLE_GCM_USERS .
			 " WHERE gcm_regid != '" . $id ."'";
	return $db->sql_query($query);
}

/**
 * Calcule la distance entre a et b, a - b ; en tenant en compte des univers arrondis.
 * type = Représente le type de distance à calculer
 *      0 : Galaxie
 *      1 : Système
 *      2 : Planète
 * typeArrondi = true pour un univers arrondi selon le type donnée
 */
function calc_distance($a, $b, $type, $typeArrondi = false) {//a-b
    global $server_config;
    
    $max_type = 0;
    
    switch($type){
        case 0: //Galaxy
            $max_type = $server_config['num_of_galaxies']; //9
            break;
        case 1: //System
            $max_type = $server_config['num_of_systems']; //499
            break;
    }
    if($typeArrondi) {
        if(abs($a - $b) < $max_type/2) {
            return abs($a - $b);//|a-b|
        } else {
            return abs(abs($a - $b) - $max_type); //||a-b| - base|
        }
    } else {
        return abs($a - $b);//|a-b|
    }
}
/********************************************************************************/
/**                     Booster partie                                         **/
/* Description :
  "m:0:0_c:0:0_d:0:0_p:0_m:0" =>booster_m;booster_c;booster_d;extension_p;extension_moon
  booster_x    => ressource:%:date_de_fin  (ressource= m|c|d)
  extension_x  => type:+" (type= p|m)
  "m:0:0_c:0:0_d:0:0_p:0_m:0" = string de stockage par défaut
*/
/*##Base de donnée  ##*/
/* Lit les informations des objets Ogame dans la BDD et les transformes en un tableau
 * @arg id_player id du joueur
 * @arg id_planet id de la planète à rechercher
 * @return tableau associatif des boosters ou NULL en cas d'échec
 * array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_c_val', 'booster_c_date', 'extention_p', 'extention_m')
*/
function booster_lire_bdd($id_player, $id_planet){
    global $db;
    $result = NULL;

    $request = "SELECT boosters FROM ".TABLE_USER_BUILDING." WHERE user_id=".$id_player." AND planet_id=".$id_planet;
    $res = $db->sql_query($request);
    if($res) {
        $str = $db->sql_fetch_row($res);
        if($str){
            return booster_decode($str[0]);
        }
    }
    return $result;
}
/* Écrit la string de stockage des objets Ogame dans la BDD.
 * @arg id_player   id du joueur
 * @arg id_planet   id de la planète à rechercher
 * @str_booster     string de stockage des boosters (donnée par les fonctions booster_encode() ou booster_encodev())
 * @return FALSE en cas d'échec
*/
function booster_ecrire_bdd_str($id_player, $id_planet, $str_booster){
    global $db;
    
    $request = "UPDATE ".TABLE_USER_BUILDING." SET boosters='".$str_booster."' WHERE user_id=".$id_player." AND planet_id=".$id_planet;
    return $db->sql_query($request);
}
/* Écrit les informations des objets Ogame dans la BDD sous forme d'une string de stockage.
 * @arg id_player   id du joueur
 * @arg id_planet   id de la planète à rechercher
 * @tab_booster     tableau infos des boosters (donnée par les fonctions booster_lire_bdd() ou booster_decode())
 * @return FALSE en cas d'échec
*/
function booster_ecrire_bdd_tab($id_player, $id_planet, $tab_booster){
    return booster_ecrire_bdd_str($id_player, $id_planet, booster_encode($tab_booster));
}
/* Mets à jour les boosters de tous les users en fonction de la date de fin dans la BDD
 *
*/
function booster_maj_bdd(){
    global $db;

    $request = "SELECT user_id, planet_id, boosters FROM ".TABLE_USER_BUILDING;
    $res = $db->sql_query($request);
    if($res) {
        $requests = array();
        while($row = $db->sql_fetch_assoc($res)) {            
            $tmp = booster_verify_str($row['boosters']);
            if($tmp !== $row['boosters']) {
                $row['boosters'] = $tmp;
                $requests[] = "UPDATE ".TABLE_USER_BUILDING." SET boosters = '".$row['boosters']."' ".
                             " WHERE user_id = ".$row['user_id'].
                             " AND planet_id = ".$row['planet_id'];
            }
        }
        foreach ($requests as $request) {
            $db->sql_query($request);
        }
    }
}

/*#######Contrôles et modifications poussées  #######*/
/* Contrôle la date de validité des boosters et reset si la date est dépassée
 * @param $boosters     tableau infos des boosters (donnée par les fonctions booster_lire_bdd() ou booster_decode())
 * @return tableau associatif des boosters mis à jour
 * array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_d_val', 'booster_d_date', 'extention_p', 'extention_m')
*/
function booster_verify($boosters) {
    $b_control = array('booster_m_', 'booster_c_', 'booster_d_');
    $current_time = time();
    
    foreach($b_control as $b) {
        if($boosters[$b.'date'] <= $current_time) {
            $boosters[$b.'val'] = 0;
            $boosters[$b.'date'] = 0;
        }
    }
    return $boosters;
}
/* Contrôle la date de validité des boosters et reset si la date est dépassée
 * @param $str     string de stockage des boosters (donnée par les fonctions booster_encode() ou booster_encodev() ou directement from BDD)
 * @return tableau associatif des boosters mis à jour
 * array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_d_val', 'booster_d_date', 'extention_p', 'extention_m')
*/
function booster_verify_str($str) {
    return booster_encode(booster_verify(booster_decode($str)));
}
/* donne des tableaux d'informations en relation avec les objets Ogame 
 * @type    détermine les informations renvoyées
 *      [Default] donne un tableau avec les uuid des objets Ogame
 *      'definition' donne un tableau avec le nom de l'objet (ex. 'Booster de métal en or')
 *      'array'      donne un tableau asso de tab uuid=>array('booster_x'|'extension_x', valeur)
 *      'string'     donne un tableau asso de string uuid=>'x:valeur:0'|'x:valeur'
 *      'full'       donne les tableaux simple : définition, uuid, string, array)
 *      'separateur' donne le char qui sert de séparateur entre les objets Ogame
 *      'default_str' donne la string de stockage par défaut : "m:0:0_c:0:0_d:0:0_p:0_m:0"
 * @return  le tableau correspondant au type
*/
function booster_objets_tab($type='') {
    $objet_str = array('Booster de métal en or'    ,'Booster de métal en argent'    ,'Booster de métal en bronze',
                       'Booster de cristal en or'  ,'Booster de cristal en argent'  ,'Booster de cristal en bronze',
                       'Booster de deutérium en or','Booster de deutérium en argent','Booster de deutérium en bronze',
                       'Extension planétaire en or','Extension planétaire en argent','Extension planétaire en bronze',
                       'Extension lunaire en or'   ,'Extension lunaire en argent'   ,'Extension lunaire en bronze');                    
    $objet_uuid = array('05294270032e5dc968672425ab5611998c409166',//'Booster de métal +30%'
                        'ba85cc2b8a5d986bbfba6954e2164ef71af95d4a',//'Booster de métal +20%'
                        'de922af379061263a56d7204d1c395cefcfb7d75',//'Booster de métal +10%'
                        '118d34e685b5d1472267696d1010a393a59aed03',//'Booster de cristal +30%'
                        '422db99aac4ec594d483d8ef7faadc5d40d6f7d3',//'Booster de cristal +20%'
                        '3c9f85221807b8d593fa5276cdf7af9913c4a35d',//'Booster de cristal +10%'
                        '5560a1580a0330e8aadf05cb5bfe6bc3200406e2',//'Booster de deutérium +30%'
                        'e4b78acddfa6fd0234bcb814b676271898b0dbb3',//'Booster de deutérium +20%'
                        'd9fa5f359e80ff4f4c97545d07c66dbadab1d1be',//'Booster de deutérium +10%'
                        '04e58444d6d0beb57b3e998edc34c60f8318825a',//'Extension planétaire +15'
                        '0e41524dc46225dca21c9119f2fb735fd7ea5cb3',//'Extension planétaire +9'
                        '16768164989dffd819a373613b5e1a52e226a5b0',//'Extension planétaire +4'
                        '05ee9654bd11a261f1ff0e5d0e49121b5e7e4401',//'Extension lunaire +6'
                        'c21ff33ba8f0a7eadb6b7d1135763366f0c4b8bf',//'Extension lunaire +4'
                        'be67e009a5894f19bbf3b0c9d9b072d49040a2cc');//'Extension lunaire +2'
    $objet_uuid_str = array('m:30:0','m:20:0','m:10:0','c:30:0','c:20:0','c:10:0','d:30:0','d:20:0','d:10:0','p:15','p:9','p:4','m:6','m:4','m:2');
    $objet_uuid_tab = array(array('booster_m', 30),array('booster_m', 20),array('booster_m', 10),
                        array('booster_c', 30),array('booster_c', 20),array('booster_c', 10),
                        array('booster_d', 30),array('booster_d', 20),array('booster_d', 10),
                        array('extention_p', 15),array('extention_p', 9),array('extention_p', 4),
                        array('extention_m', 6),array('extention_m', 4),array('extention_m', 2));
    $separateur = '_';
    $default_str = array('m:0:0', 'c:0:0', 'd:0:0', 'p:0', 'm:0');
    
    switch($type) {
        case 'definition':
            return $objet_str;
        case 'array':
            $n = count($objet_uuid);
            for($i=0 ; $i<$n ; $i++) {
                $result[$objet_uuid[$i]] = $objet_uuid_tab[$i]; 
            }
            return $result;
        case 'string':
            $n = count($objet_uuid);
            for($i=0 ; $i<$n ; $i++) {
                $result[$objet_uuid[$i]] = $objet_uuid_str[$i]; 
            }
            return $result;
        case 'full':
            return array($objet_str, $objet_uuid, $objet_uuid_str, $objet_uuid_str);
        case 'separateur':
            return $separateur;
        case 'default_str':
            return implode($separateur, $default_str);
        default:
            return $objet_uuid;
    }
}
/* Indique si un uuid est enregistré dans OGSpy (il existe)
 * @uuid    string uuid récupéré de la page Ogame
*/
function booster_is_uuid($uuid) {
    return in_array($uuid, booster_objets_tab());
}
/* Mets à jour le tableau infos des boosters.
 * @boosters tableau infos des boosters (donnée par les fonctions booster_lire_bdd() ou booster_decode())
 * @uuid     string uuid de l'objet Ogame récupéré de la page Ogame
 * @date     date de fin de l'objet Ogame. [defaut=0]
 * return   le tableau à jour (par uuid et date)
 *          si $boosters==NULL OU booster_uuid($b) sans uuid -> donne tableau avec valeurs par défaut (équivalent booster_decode())
 *          NULL en cas d'erreur (uuid inconnu)
*/
function booster_uuid($boosters, $uuid='', $date=0) {
    if($boosters==NULL || $uuid=='') {
        $boosters = booster_decode();
        return $boosters;
    } else {
        $objet_uuid = booster_objets_tab('array');
        //if(isset($objet_uuid[$uuid]) || array_key_exists($uuid, $objet_uuid)) {
        if(isset($objet_uuid[$uuid])) {
            if($objet_uuid[$uuid][0][0] == 'b') { //1er lettre de booster
                $boosters[$objet_uuid[$uuid][0].'_val'] = $objet_uuid[$uuid][1];
                $boosters[$objet_uuid[$uuid][0].'_date'] = $date;
            } elseif($objet_uuid[$uuid][0][0] == 'e') { //1er lettre de extension
                $boosters[$objet_uuid[$uuid][0]] = $objet_uuid[$uuid][1];
            } else { return NULL;} //Ne devrait jamais arriver si les tableaux dans booster_objets_tab() sont bien construit
            return $boosters;
        }
    }
    return NULL;
}

/* Transforme la date Ogame de format "*s *j *h *m *s" en nombre de seconde
 * @str string contenant le temps
 * @return int nombre de seconde correspondant à $str. 0 si problème
*/
function booster_lire_date($str) {
    static $tri = array(   's',   'j',   'h', 'm', 's');
    static $num = array(604800, 86400,  3600,  60,  1);
    static $n = 5;
    $result = 0;
    $j = $n-1;  //Lecture de droite à gauche on démarre par les secondes.
    
    $parts = sscanf($str, '%d%c %d%c %d%c %d%c %d%c');
    for($i=$n-1 ; $i >= 0 ; $i--) {
        $car = $parts[$i*2+1];  //%c
        $valeur = $parts[$i*2]; //%d
        if(!is_null($car)) {
            if($car == $tri[$j]){
                $result += $valeur * $num[$j--];
            }
        }
    }
    return $result;
}

/*#######Lecture et modifications poussées  #######*/
/* Transforme en tableau les données des objets Ogame contenues dans une string de stockage.
 * Si aucun argument n'ai donné alors elle renvoie les valeurs des objets par défaut.
 * @param $str  string de stockage des objets Ogame
 * @return      tableau contenant les informations des objets
 * array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_c_val', 'booster_c_date', 'extention_p', 'extention_m')
*/
function booster_decode($str=NULL, &$boosters=NULL) {
    if($str) {
        $s = booster_objets_tab('separateur');
        $a = preg_match("/m:(\d+):(\d+)".$s."c:(\d+):(\d+)".$s."d:(\d+):(\d+)".$s."p:(\d+)".$s."m:(\d+)/", $str, $boosters);
        if($a) {
            $i = 1;
        return array('booster_m_val'=>intval($boosters[$i++]), 'booster_m_date'=>intval($boosters[$i++]),
                     'booster_c_val'=>intval($boosters[$i++]), 'booster_c_date'=>intval($boosters[$i++]),
                     'booster_d_val'=>intval($boosters[$i++]), 'booster_d_date'=>intval($boosters[$i++]),
                     'extention_p'=>intval($boosters[$i++]), 'extention_m'=>intval($boosters[$i++]));
        }
    }    
    return array('booster_m_val'=>0, 'booster_m_date'=>0,
                 'booster_c_val'=>0, 'booster_c_date'=>0,
                 'booster_d_val'=>0, 'booster_d_date'=>0,
                 'extention_p'=>0, 'extention_m'=>0);
}
/* Transforme le tableau des informations des objets Ogame en une string de stockage.
 * @b tableau associatif des infos array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_c_val', 'booster_c_date', 'extention_p', 'extention_m')
 * @return objet sous format string de stockage ("m:0:0_c:0:0_d:0:0_p:0_m:0 si pas d'argument)
*/
function booster_encode($b=NULL) {
    $str = '';
    if($b){
        $separateur= booster_objets_tab('separateur');
        $str .= 'm:'.$b['booster_m_val'].':'.$b['booster_m_date'].$separateur;
        $str .= 'c:'.$b['booster_c_val'].':'.$b['booster_c_date'].$separateur;
        $str .= 'd:'.$b['booster_d_val'].':'.$b['booster_d_date'].$separateur;
        $str .= 'p:'.$b['extention_p'].$separateur;
        $str .= 'm:'.$b['extention_m'];
    } else {
        $str = booster_objets_tab('default_str');//"m:0:0_c:0:0_d:0:0_p:0_m:0";
    }
    return $str;
}
/* Transforme les valeurs des objets Ogame en une string de stockage.
 * string de stockage par défaut = m:0:0_c:0:0_d:0:0_p:0_m:0
 * @return objet sous format string de stockage ("m:0:0_c:0:0_d:0:0_p:0_m:0" si pas d'argument)
*/
function booster_encodev($booster_m_val=0, $booster_m_date=0, $booster_c_val=0, $booster_c_date=0,
                $booster_d_val=0, $booster_d_date=0, $extention_p=0, $extention_m=0) {
    $separateur= booster_objets_tab('separateur');
    $str = '';
    $str .= 'm:'.$booster_m_val.':'.$booster_m_date.$separateur;
    $str .= 'c:'.$booster_c_val.':'.$booster_c_date.$separateur;
    $str .= 'd:'.$booster_d_val.':'.$booster_d_date.$separateur;
    $str .= 'p:'.$extention_p.$separateur;
    $str .= 'm:'.$extention_m;
    return $str;
}
/**                     Fin booster partie                                     **/
/********************************************************************************/
?>