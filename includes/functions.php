<?php
/**
 * OGSpy Global functions
 * @package OGSpy
 * @subpackage Common
 * @author Kyser
 * @copyright Copyright &copy; 2012, http://www.ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.1.1 ($Rev: 7752 $)
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\DBUtils_Model;
use Ogsteam\Ogspy\Model\Config_Model;
use Ogsteam\Ogspy\Model\Universe_Model;
use Ogsteam\Ogspy\Model\User_Building_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Ogsteam\Ogspy\Model\User_Favorites_Model;


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
 * @param string $text String or table to write
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
    } else {
        return false;
    }
}

/**
 * Write a text or a table in a gz compressed file
 * @param string $file Filename
 * @param string $mode File Opening Mode
 * @param string $text String or table to write
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
    } else {
        return false;
    }
}

/**
 * Remove a Folder with its content
 * @param string $folder Chemin vers le dossier à supprimer
 */
function remove_dir_from_ogspy($folder)
{

    $dir_iterator = new RecursiveDirectoryIterator($folder);
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

    // On supprime chaque dossier et chaque fichier	du dossier cible
    foreach ($iterator as $fichier) {
        $fichier->isDir() ? rmdir($fichier) : unlink($fichier);
    }

    // On supprime le dossier cible
    rmdir($folder);
}


/**
 * Convert an IP in Hex Format
 * @param string $ip format xxx.xxx.xxx.xxx in IPv4 and xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx in IPv6
 * @return string IP in hex : HHHHHHHH for IPv4 and HHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH for IPv6
 */
function encode_ip($ip)
{
    $d = explode('.', $ip);
    if (count($d) == 4) {
        return sprintf('%02x%02x%02x%02x', $d[0], $d[1], $d[2], $d[3]);
    }

    $d = explode(':', preg_replace('/(^:)|(:$)/', '', $ip));
    $res = '';
    foreach ($d as $x) {
        $res .= sprintf('%0' . ($x == '' ? (9 - count($d)) * 4 : 4) . 's', $x);
    }
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
        $int_ip = ':' . implode(':', array_map("hexhex", explode(':', $int_ip))) . ':';
        preg_match_all("/(:0)+/", $int_ip, $zeros);
        if (count($zeros[0]) > 0) {
            $match = '';
            foreach ($zeros[0] as $zero) {
                if (strlen($zero) > strlen($match)) {
                    $match = $zero;
                }
            }
            $int_ip = preg_replace('/' . $match . '/', ':', $int_ip, 1);
        }
        return preg_replace('/(^:([^:]))|(([^:]):$)/', '$2$4', $int_ip);
    }
    $hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
    return hexdec($hexipbang[0]) . '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

/**
 * Converts a hex value to another hew value (depnding of the current php version on the server)
 * @param string $value The initial hexvalue
 * @return string the new hew value
 */
function hexhex($value)
{
    return dechex(hexdec($value));
}

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
    global $cache_mod, $server_config;      //$cache_mod nécessaire, définie dans cache/cache_mod.php !!

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
 */
function set_server_view()
{
    global $user_data;
    global $pub_enable_portee_missil, $pub_enable_members_view, $pub_enable_stat_view,
           $pub_galaxy_by_line_stat, $pub_system_by_line_stat, $pub_galaxy_by_line_ally, $pub_system_by_line_ally,
           $pub_nb_colonnes_ally, $pub_color_ally, $pub_enable_register_view, $pub_register_alliance,
           $pub_register_forum, $pub_open_user, $pub_open_admin;

    //appel de la couche" Model"
    $Config_Model = new Config_Model();


    if (!check_var($pub_enable_members_view, "Num") || !check_var($pub_enable_stat_view,
            "Num") || !check_var($pub_galaxy_by_line_stat, "Num") || !check_var($pub_system_by_line_stat,
            "Num") || !check_var($pub_galaxy_by_line_ally, "Num") || !check_var($pub_system_by_line_ally,
            "Num")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("planetindex.php?action=message&id_message=forbidden&info");
    }

    if (!isset($pub_galaxy_by_line_stat) || !isset($pub_system_by_line_stat) || !isset($pub_galaxy_by_line_ally) || !isset($pub_system_by_line_ally)
    ) {
        redirection("index.php?action=message&id_message=setting_server_view_failed&info");
    }

    if (is_null($pub_enable_portee_missil)) {
        $pub_enable_portee_missil = 0;
    }
    if (is_null($pub_enable_stat_view)) {
        $pub_enable_stat_view = 0;
    }
    if (is_null($pub_enable_members_view)) {
        $pub_enable_members_view = 0;
    }

    $break = false;


    if (!is_numeric($pub_galaxy_by_line_stat)) {
        $break = true;
    }
    if (!is_numeric($pub_system_by_line_stat)) {
        $break = true;
    }
    if ($pub_enable_stat_view != 0 && $pub_enable_stat_view != 1) {
        $break = true;
    }
    if ($pub_enable_members_view != 0 && $pub_enable_members_view != 1) {
        $break = true;
    }
    if (!is_numeric($pub_galaxy_by_line_ally)) {
        $break = true;
    }
    if (!is_numeric($pub_system_by_line_ally)) {
        $break = true;
    }
    if ($pub_nb_colonnes_ally == 0 || $pub_nb_colonnes_ally > 9 || !is_numeric($pub_nb_colonnes_ally)) {
        $break = true;
    }
    if ($pub_enable_register_view != 0 && $pub_enable_register_view != 1) {
        $break = true;
    }

    if ($break) {
        redirection("index.php?action=message&id_message=setting_server_view_failed&info");
    }

    $Config_Model->update(array("config_value" => $pub_enable_portee_missil, "config_name" => "portee_missil"));

    if ($pub_galaxy_by_line_stat < 1) {
        $pub_galaxy_by_line_stat = 1;
    }
    if ($pub_galaxy_by_line_stat > 100) {
        $pub_galaxy_by_line_stat = 100;
    }
    $Config_Model->update_one($pub_galaxy_by_line_stat, "galaxy_by_line_stat");

    if ($pub_system_by_line_stat < 1) {
        $pub_system_by_line_stat = 1;
    }
    if ($pub_system_by_line_stat > 100) {
        $pub_system_by_line_stat = 100;
    }

    $Config_Model->update_one($pub_system_by_line_stat, "system_by_line_stat");

    $Config_Model->update_one($pub_open_user, "open_user");
    $Config_Model->update_one($pub_open_admin, "open_admin");

    $Config_Model->update_one($pub_enable_stat_view, "enable_stat_view");
    $Config_Model->update_one($pub_enable_members_view, "enable_members_view");
    $Config_Model->update_one($pub_nb_colonnes_ally, "nb_colonnes_ally");

    $array = $pub_color_ally; //die(var_dump($pub_color_ally));
    $color_ally = implode("_", $array);
    $Config_Model->update_one($color_ally, "color_ally");

    if ($pub_galaxy_by_line_ally < 1) {
        $pub_galaxy_by_line_ally = 1;
    }
    if ($pub_galaxy_by_line_ally > 100) {
        $pub_galaxy_by_line_ally = 100;
    }
    $Config_Model->update_one($pub_galaxy_by_line_ally, "galaxy_by_line_ally");

    if ($pub_system_by_line_ally < 1) {
        $pub_system_by_line_ally = 1;
    }
    if ($pub_system_by_line_ally > 100) {
        $pub_system_by_line_ally = 100;
    }

    $Config_Model->update_one($pub_system_by_line_ally, "system_by_line_ally");
    $Config_Model->update_one($pub_enable_register_view, "enable_register_view");
    $Config_Model->update_one($pub_register_alliance, "register_alliance");
    $Config_Model->update_one($pub_register_forum, "register_forum");

    // mise a jour des caches avec les modifs
    generate_config_cache();
    log_("set_server_view");
    redirection("index.php?action=administration&subaction=affichage");
}

/**
 *  Updates in the database all configurations displayed in the parameters administration Page.
 */
function set_serverconfig()
{
    global $user_data, $server_config;
    global $pub_max_battlereport, $pub_max_favorites, $pub_max_favorites_spy, $pub_max_spyreport,
           $pub_server_active, $pub_session_time, $pub_max_keeplog, $pub_debug_log,
           $pub_reason, $pub_ally_protection, $pub_url_forum, $pub_max_keeprank, $pub_keeprank_criterion,
           $pub_max_keepspyreport, $pub_servername, $pub_allied, $pub_disable_ip_check, $pub_num_of_galaxies,
           $pub_num_of_systems, $pub_log_phperror, $pub_block_ratio, $pub_ratio_limit, $pub_speed_uni,
           $pub_ddr, $pub_astro_strict, $pub_config_cache, $pub_mod_cache,
           $pub_mail_use, $pub_mail_smtp_use, $pub_mail_smtp_secure, $pub_mail_smtp_port, $pub_mail_smtp_host, $pub_mail_smtp_username, $pub_mail_smtp_password, $pub_enable_mail_smtp_password;

    //appel de la couche" Model"
    $Config_Model = new Config_Model();

    if (!isset($pub_num_of_galaxies)) {
        $pub_num_of_galaxies = intval($server_config['num_of_galaxies']);
    }
    if (!isset($pub_num_of_systems)) {
        $pub_num_of_systems = intval($server_config['num_of_systems']);
    }

    if (!check_var($pub_max_battlereport, "Num") || !check_var($pub_max_favorites,
            "Num") || !check_var($pub_max_favorites_spy, "Num") || !check_var($pub_ratio_limit,
            "Special", "#^[\w\s,\.\-]+$#") || !check_var($pub_max_spyreport, "Num") || !check_var($pub_server_active, "Num") || !check_var($pub_session_time, "Num") ||
        !check_var($pub_max_keeplog, "Num") || !check_var($pub_debug_log, "Num") || !check_var($pub_block_ratio, "Num") || !check_var(stripslashes($pub_reason), "Text") || !check_var($pub_ally_protection,
            "Special", "#^[\w\s,\.\-]+$#") || !check_var($pub_url_forum, "URL") || !check_var($pub_max_keeprank, "Num") || !check_var($pub_keeprank_criterion,
            "Char") || !check_var($pub_max_keepspyreport, "Num") || !check_var(stripslashes($pub_servername), "Text") || !check_var($pub_allied, "Special", "#^[\w\s,\.\-]+$#") ||
        !check_var($pub_disable_ip_check, "Num") || !check_var($pub_num_of_galaxies,
            "Galaxies") || !check_var($pub_num_of_systems, "Galaxies") || !check_var($pub_config_cache,
            "Num") || !check_var($pub_mod_cache, "Num")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("planetindex.php?action=message&id_message=forbidden&info");
    }

    if (!isset($pub_max_battlereport) || !isset($pub_max_favorites) || !isset($pub_max_favorites_spy) ||
        !isset($pub_ratio_limit) || !isset($pub_max_spyreport) || !isset($pub_session_time) ||
        !isset($pub_max_keeplog) || !isset($pub_reason) ||
        !isset($pub_ally_protection) || !isset($pub_url_forum) || !isset($pub_max_keeprank) ||
        !isset($pub_keeprank_criterion) || !isset($pub_max_keepspyreport) || !isset($pub_servername) ||
        !isset($pub_allied) || !isset($pub_mod_cache) || !isset($pub_config_cache)
    ) {
        redirection("index.php?action=message&id_message=setting_serverconfig_failed&info");
    }

    if (is_null($pub_server_active))    { $pub_server_active = 0; }
    if (is_null($pub_disable_ip_check)) { $pub_disable_ip_check = 0; }
    if (is_null($pub_log_phperror))     { $pub_log_phperror = 0; }
    if (is_null($pub_debug_log))        { $pub_debug_log = 0; }
    if (is_null($pub_block_ratio))      { $pub_block_ratio = 0; }
    if (is_null($pub_mail_use))         { $pub_mail_use = 0; }
    if (is_null($pub_mail_smtp_use))    { $pub_mail_smtp_use = 0; }
    if (is_null($pub_mail_smtp_secure)) { $pub_mail_smtp_secure = 0; }

    $break = false;
    if ($pub_server_active != 0 && $pub_server_active != 1) {
        $break = true;
    }
    if ($pub_debug_log != 0 && $pub_debug_log != 1) {
        $break = true;
    }
    if ($pub_block_ratio != 0 && $pub_block_ratio != 1) {
        $break = true;
    }
    if (!is_numeric($pub_max_favorites)) {
        $break = true;
    }
    if (!is_numeric($pub_max_favorites_spy)) {
        $break = true;
    }
    if (!is_numeric($pub_ratio_limit)) {
        $break = true;
    }
    if (!is_numeric($pub_max_spyreport)) {
        $break = true;
    }
    if (!is_numeric($pub_max_battlereport)) {
        $break = true;
    }
    if (!is_numeric($pub_session_time)) {
        $break = true;
    }
    if (!is_numeric($pub_max_keeplog)) {
        $break = true;
    }
    if ($pub_disable_ip_check != 0 && $pub_disable_ip_check != 1) {
        $break = true;
    }
    if ($pub_log_phperror != 0 && $pub_log_phperror != 1) {
        $break = true;
    }

    if ($break) {
        redirection("index.php?action=message&id_message=setting_serverconfig_failed&info");
    }

    if (($pub_num_of_galaxies != intval($server_config['num_of_galaxies'])) || ($pub_num_of_systems != intval($server_config['num_of_systems']))
    ) {
        resize_db($pub_num_of_galaxies, $pub_num_of_systems);
    }
    $Config_Model->update_one($pub_server_active, "server_active");

    $Config_Model->update_one($pub_debug_log, "debug_log");
    $Config_Model->update_one($pub_block_ratio, "block_ratio");
    $Config_Model->update_one($pub_log_phperror, "log_phperror");

    $pub_max_favorites = intval($pub_max_favorites);
    if ($pub_max_favorites < 0) {
        $pub_max_favorites = 0;
    }
    if ($pub_max_favorites > 99) {
        $pub_max_favorites = 99;
    }
    $Config_Model->update_one($pub_max_favorites, "max_favorites");

    $pub_max_favorites_spy = intval($pub_max_favorites_spy);
    if ($pub_max_favorites_spy < 0) {
        $pub_max_favorites_spy = 0;
    }
    if ($pub_max_favorites_spy > 99) {
        $pub_max_favorites_spy = 99;
    }
    $Config_Model->update_one($pub_max_favorites_spy, "max_favorites_spy");

    $Config_Model->update_one($pub_ratio_limit, "ratio_limit");

    $pub_max_spyreport = intval($pub_max_spyreport);
    if ($pub_max_spyreport < 1) {
        $pub_max_spyreport = 1;
    }
    if ($pub_max_spyreport > 50) {
        $pub_max_spyreport = 50;
    }
    $Config_Model->update_one($pub_max_spyreport, "max_spyreport");

    $pub_max_battlereport = intval($pub_max_battlereport);
    if ($pub_max_battlereport < 0) {
        $pub_max_battlereport = 0;
    }
    if ($pub_max_battlereport > 999) {
        $pub_max_battlereport = 999;
    }
    $Config_Model->update_one($pub_max_battlereport, "max_battlereport");

    $pub_session_time = intval($pub_session_time);
    if ($pub_session_time < 5 && $pub_session_time != 0) {
        $pub_session_time = 5;
    }
    if ($pub_session_time > 180) {
        $pub_session_time = 180;
    }
    $Config_Model->update_one($pub_session_time, "session_time");

    $pub_max_keeplog = intval($pub_max_keeplog);
    if ($pub_max_keeplog < 0) {
        $pub_max_keeplog = 0;
    }
    if ($pub_max_keeplog > 365) {
        $pub_max_keeplog = 365;
    }
    $Config_Model->update_one($pub_max_keeplog, "max_keeplog");

    $Config_Model->update_one($pub_reason, "reason");

    if (substr($pub_ally_protection, strlen($pub_ally_protection) - 1) == ",") {
        $pub_ally_protection = substr($pub_ally_protection, 0, strlen($pub_ally_protection) -
            1);
    }
    $Config_Model->update_one($pub_ally_protection, "ally_protection");

    if ($pub_url_forum != "" && !preg_match("#[^http://]|[^https://]#", $pub_url_forum)) {
        $pub_url_forum = "http://" . $pub_url_forum;
    }
    $Config_Model->update_one($pub_url_forum, "url_forum");

    $pub_max_keeprank = intval($pub_max_keeprank);
    if ($pub_max_keeprank < 1) {
        $pub_max_keeprank = 1;
    }
    if ($pub_max_keeprank > 999) {
        $pub_max_keeprank = 999;
    }
    $Config_Model->update_one($pub_max_keeprank, "max_keeprank");

    if ($pub_keeprank_criterion != "quantity" && $pub_keeprank_criterion != "day") {
        $pub_keeprank_criterion = "quantity";
    }
    $Config_Model->update_one($pub_keeprank_criterion, "keeprank_criterion");

    $pub_max_keepspyreport = intval($pub_max_keepspyreport);
    if ($pub_max_keepspyreport < 1) {
        $pub_max_keepspyreport = 1;
    }
    if ($pub_max_keepspyreport > 999) {
        $pub_max_keepspyreport = 999;
    }
    $Config_Model->update_one($pub_max_keepspyreport, "max_keepspyreport");

    $Config_Model->update_one($pub_servername, "servername");

    if (substr($pub_allied, strlen($pub_allied) - 1) == ",") {
        $pub_allied = substr($pub_allied, 0, strlen($pub_allied) - 1);
    }
    $Config_Model->update_one($pub_allied, "allied");

    $Config_Model->update_one($pub_disable_ip_check, "disable_ip_check");
    $Config_Model->update_one($pub_num_of_galaxies, "num_of_galaxies");
    $Config_Model->update_one($pub_num_of_systems, "num_of_systems");

    if (!isset($pub_ddr) || !is_numeric($pub_ddr)) {
        $pub_ddr = 0;
    }
    $Config_Model->update_one($pub_ddr, "ddr");

    if (!isset($pub_astro_strict) || !is_numeric($pub_astro_strict)) {
        $pub_astro_strict = 0;
    }
    $Config_Model->update_one($pub_astro_strict, "astro_strict");

    if (!is_numeric($pub_speed_uni) || $pub_speed_uni < 1) {
        $pub_speed_uni = 1;
    }
    $Config_Model->update_one($pub_speed_uni, "speed_uni");

    $Config_Model->update_one($pub_mod_cache, "mod_cache");
    $Config_Model->update_one($pub_config_cache, "config_cache");


    // param mail
    $Config_Model->update_one($pub_mail_use, "mail_use");
    $Config_Model->update_one($pub_mail_smtp_use, "mail_smtp_use");
    $Config_Model->update_one($pub_mail_smtp_secure, "mail_smtp_secure");
    $Config_Model->update_one($pub_mail_smtp_port, "mail_smtp_port");
    $Config_Model->update_one($pub_mail_smtp_host, "mail_smtp_host");
    $Config_Model->update_one($pub_mail_smtp_username, "mail_smtp_username");

    if (isset($pub_enable_mail_smtp_password)) {
        setMailSMTPPassword($pub_mail_smtp_password);
    }


    // mise a jour des caches avec les mofids
    generate_config_cache();
    log_("set_serverconfig");
    redirection("index.php?action=administration&subaction=parameter");
}

/**
 * Returns the Status of the Database used size.
 * @return Array [Server], et [Total]
 */
function db_size_info()
{
    $dbSize = (new DBUtils_Model())->SizeInfo();
    $dbSizeServer = $dbSize['dbSizeServer'];
    $dbSizeTotal = $dbSize['dbSizeTotal'];

    $bytes = array('Octets', 'Ko', 'Mo', 'Go', 'To');

    if ($dbSizeServer < 1024) {
        $dbSizeServer = 1;
    }
    for ($i = 0; $dbSizeServer > 1024; $i++) {
        $dbSizeServer /= 1024;
    }
    $dbSize_info["Server"] = round($dbSizeServer, 2) . " " . $bytes[$i];

    if ($dbSizeTotal < 1024) {
        $dbSizeTotal = 1;
    }
    for ($i = 0; $dbSizeTotal > 1024; $i++) {
        $dbSizeTotal /= 1024;
    }
    $dbSize_info["Total"] = round($dbSizeTotal, 2) . " " . $bytes[$i];

    return $dbSize_info;
}

/**
 * Function to Optimize all tables of the OGSpy Database
 * @param boolean $maintenance_action true if no url redirection is requested,false to redirect to another page
 */
function db_optimize($maintenance_action = false)
{
    $dbSize_before = db_size_info();
    $dbSize_before = $dbSize_before["Total"];

    (new DBUtils_Model())->Optimize();

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
 */
function resize_db($new_num_of_galaxies, $new_num_of_systems)
{
    global $server_config;

    //appel de la couche" Model"
    $Config_Model = new Config_Model();
    $User_Model = new User_Model();
    $User_Favorites_Model = new User_Favorites_Model();

    // si on reduit on doit supprimez toutes les entrées qui font reference au systemes ou galaxies que l'on va enlever
    (new Universe_Model())->resize_universe($new_num_of_galaxies, $new_num_of_systems);
    $User_Favorites_Model->delete_favorites_after_resize($new_num_of_galaxies, $new_num_of_systems); //suppression des favoris plus utils
    if ($new_num_of_galaxies < intval($server_config['num_of_galaxies'])) {
        $User_Model->set_default_galaxy_after_resize($new_num_of_galaxies);
    }
    if ($new_num_of_systems < intval($server_config['num_of_systems'])) {
        $User_Model->set_default_system_after_resize($new_num_of_systems);
    }

    $server_config['num_of_galaxies'] = $new_num_of_galaxies;
    $server_config['num_of_systems'] = $new_num_of_systems;

    $Config_Model->update_one($new_num_of_galaxies, "num_of_galaxies");
    $Config_Model->update_one($new_num_of_systems, "num_of_systems");

    // mise a jour des caches avec les modifs
    generate_config_cache();
    log_("set_db_size");
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
    global $server_config;


    $time = mktime(0, 0, 0);
    if (isset($server_config["last_maintenance_action"]) && $time > $server_config["last_maintenance_action"]) {
        galaxy_purge_ranking();
        log_purge();
        galaxy_purge_spy();

        (new Config_Model())->update_one($time, "last_maintenance_action");
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
            if (!preg_match("/^[\w\s\-]{3,15}$/", $value)) {
                log_("check_var", array("Pseudo_Groupname", $value));
                return false;
            }
            break;

        //Pseudo ingame
        case "Pseudo_ingame": // caracteres autorises entre 3 et 20 + espace ( interdit au 05/11/11 = > &"'()# `/,;+ )
            if (!preg_match("/^[\w@äàçéèêëïîöôûü \^\{\}\[\]\.\*\-_~%§]{3,20}$/", $value)) {
                log_("check_var", array("Text", $value));
                return false;
            }
            break;

        //Mot de passe des membres
        case "Password": //Tout caractère sauf ; ' et ".
            // if (!preg_match("/^[\w\s\-]{6,64}$/", $value)) {
                // return false;
            // }
			if (!preg_match("/^[^;'\"]{6,64}$/", $value)) { //Protection encore supplémentaire (même si sql_escape_string fait)
                return false;
            }
            break;

        //Chaîne de caractères avec espace
        case "Text":
            if (!preg_match("/^[\w'äàçéèêëïîöôûü\s\.\*\-]+$/", $value)) {
                log_("check_var", array("Text", $value));
                return false;
            }
            break;

        //Chaîne de caractères et  chiffre
        case "CharNum":
            if (!preg_match("/^[\w\.\*\-\#]+$/", $value)) {
                log_("check_var", array("CharNum", $value));
                return false;
            }
            break;

        //Caractères
        case "Char":
            if (!preg_match("/^[[:alpha:]_\.\*\-]+$/", $value)) {
                log_("check_var", array("Char", $value));
                return false;
            }
            break;

        //Chiffres
        case "Num":
            if (!preg_match("/^[[:digit:]]+$/", $value)) {
                log_("check_var", array("Num", $value));
                return false;
            }
            break;
        //Email
        case "Email":
            if (!preg_match('/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/', $value)) {
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
            if (!preg_match("/^(((https|http):\/\/)?(?(2)(www\.)?|(www\.){1})?[-a-z0-9~_]{2,}(\.[-a-z0-9~._]{2,})?[-a-z0-9~_\/&\?=.]{2,})$/i",
                $value)
            ) {
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
    global $user_data;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] !=
        1
    ) {
        die("Acces interdit");
    }

    (new User_Model())->all_raz_ratio_search();

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
                $secvalue)) || (preg_match("/<[^>]*iframe*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*applet*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*meta*\"?[^>]*>/i",
                $secvalue)) || (preg_match("/<[^>]*style*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*form*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*img*\"?[^>]*>/i",
                $secvalue)) || (preg_match("/\([^>]*\"?[^)]*\)/i", $secvalue)) || (preg_match("/\"/i",
                $secvalue))
        ) {
            return false;
        }
    } else {
        foreach ($secvalue as $subsecvalue) {
            if (!check_getvalue($subsecvalue)) {
                return false;
            }
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
                $secvalue))
        ) {
            return false;
        }
    } else {
        foreach ($secvalue as $subsecvalue) {
            if (!check_postvalue($subsecvalue)) {
                return false;
            }
        }
    }
    return true;
}

/**
 * OGSpy Simple Hash Function for unsecure tokens
 * @param string The string to Hash
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

/********************************************************************************/
/**                     Booster partie                                         *
 * @param $id_player
 * @param $id_planet
 * @return array|null
 */
/* Description :
  "m:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0" =>booster_m;booster_c;booster_d;booster_e;extension_p;extension_moon
  booster_x    => ressource:%:date_de_fin  (ressource= m|c|d|e)
  extension_x  => type:+" (type= p|m)
  "m:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0" = string de stockage par défaut
*/
/*##Base de donnée  ##*/
/* Lit les informations des objets Ogame dans la BDD et les transformes en un tableau
 * @arg id_player id du joueur
 * @arg id_planet id de la planète à rechercher
 * @return tableau associatif des boosters ou NULL en cas d'échec
 * array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_d_val', 'booster_d_date', 'booster_e_val', 'booster_e_date', 'extention_p', 'extention_m')
 *
*/
function booster_lire_bdd($id_player, $id_planet)
{
    $result = NULL;
    $User_Building_Model = new User_Building_Model();
    $tBoosters = $User_Building_Model->get_all_booster_player($id_player);

    if (isset($tBoosters[$id_planet])) {
        return booster_decode($tBoosters[$id_planet]);
    }
    return $result;
}


/* Écrit les informations des objets Ogame dans la BDD sous forme d'une string de stockage.
 * @arg id_player   id du joueur
 * @arg id_planet   id de la planète à rechercher
 * @tab_booster     tableau infos des boosters (donnée par les fonctions booster_lire_bdd() ou booster_decode())
 * @return FALSE en cas d'échec
*/
/**
 * @param $id_player
 * @param $id_planet
 * @param $tab_booster
 * @return bool|mixed|\mysqli_result
 *
 */
function booster_ecrire_bdd_tab($id_player, $id_planet, $tab_booster)
{
    $User_Building_Model = new User_Building_Model();
    return $User_Building_Model->update_booster($id_player, $id_planet, booster_encode($tab_booster));
}

/* Mets à jour les boosters de tous les users en fonction de la date de fin dans la BDD
*/
function booster_maj_bdd()
{
    $User_Building_Model = new User_Building_Model();

    // recupération de tous les booster et verification
    $tUserBoosters = $User_Building_Model->get_all_booster();
    $tUpdateBoosters = array();
    foreach ($tUserBoosters as $UserBooster) {
        $tmp = booster_verify_str($UserBooster['boosters']);
        if ($tmp !== $UserBooster['boosters']) {
            $tmptoUpdate = array();
            $tmptoUpdate["user_id"] = $UserBooster['user_id'];
            $tmptoUpdate["planet_id"] = $UserBooster['planet_id'];
            $tmptoUpdate["boosters"] = $tmp;

            $tUpdateBoosters[] = $tmptoUpdate;
        }
    }

    //sauvegarde des boosters actualisé
    foreach ($tUpdateBoosters as $UpdateBooster) {
        $User_Building_Model->update_booster($UpdateBooster["user_id"], $UpdateBooster["planet_id"], $UpdateBooster["boosters"]);
    }
}

/*#######Contrôles et modifications poussées  #######*/

/**
 * Contrôle la date de validité des boosters et reset si la date est dépassée
 * @param $boosters tableau infos des boosters (donnée par les fonctions booster_lire_bdd() ou booster_decode())
 * @return tableau associatif des boosters mis à jour array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_d_val', 'booster_d_date', 'booster_e_val', 'booster_e_date', 'extention_p', 'extention_m')
 */
function booster_verify($boosters)
{
    $b_control = array('booster_m_', 'booster_c_', 'booster_d_', 'booster_e_');
    $current_time = time();

    foreach ($b_control as $b) {
        if ($boosters[$b . 'date'] <= $current_time) {
            $boosters[$b . 'val'] = 0;
            $boosters[$b . 'date'] = 0;
        }
    }
    return $boosters;
}

/**
 * Contrôle la date de validité des boosters et reset si la date est dépassée
 * @param $str     string de stockage des boosters (donnée par les fonctions booster_encode() ou booster_encodev() ou directement from BDD)
 * @return tableau associatif des boosters mis à jour
 * array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_d_val', 'booster_d_date', 'booster_e_val', 'booster_e_date', 'extention_p', 'extention_m')
 */
function booster_verify_str($str)
{
    return booster_encode(booster_verify(booster_decode($str)));
}

/**
 * donne des tableaux d'informations en relation avec les objets Ogame
 * @type    détermine les informations renvoyées
 *      [Default] donne un tableau avec les uuid des objets Ogame
 *      'definition' donne un tableau avec le nom de l'objet (ex. 'Booster de métal en or')
 *      'array'      donne un tableau asso de tab uuid=>array('booster_x'|'extension_x', valeur)
 *      'string'     donne un tableau asso de string uuid=>'x:valeur:0'|'x:valeur'
 *      'full'       donne les tableaux simple : définition, uuid, string, array)
 *      'separateur' donne le char qui sert de séparateur entre les objets Ogame
 *      'default_str' donne la string de stockage par défaut : "m:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0"
 * @return  le tableau correspondant au type
 */
function booster_objets_tab($type = '')
{
    $objet_str = array('Booster de métal en platine', 'Booster de métal en or', 'Booster de métal en argent', 'Booster de métal en bronze',
        'Booster de cristal en platine', 'Booster de cristal en or', 'Booster de cristal en argent', 'Booster de cristal en bronze',
        'Booster de deutérium en platine', 'Booster de deutérium en or', 'Booster de deutérium en argent', 'Booster de deutérium en bronze',
        'Boosteurs d`énergie en platine', 'Boosteurs d`énergie en or', 'Boosteurs d`énergie en argent', 'Boosteurs d`énergie en bronze',
        'Extension planétaire en platine', 'Extension planétaire en or', 'Extension planétaire en argent', 'Extension planétaire en bronze',
        'Extension lunaire en platine', 'Extension lunaire en or', 'Extension lunaire en argent', 'Extension lunaire en bronze');
    $objet_uuid = array('a83cfdc15b8dba27c82962d57e50d8101d263cfb', //'Booster de métal +40% 1s'
        '05294270032e5dc968672425ab5611998c409166', //'Booster de métal +30% 1s'
        'ba85cc2b8a5d986bbfba6954e2164ef71af95d4a', //'Booster de métal +20% 1s'
        'de922af379061263a56d7204d1c395cefcfb7d75', //'Booster de métal +10% 1s'
        '35d96e441c21ef112a84c618934d9d0f026998fd', //'Booster de cristal +40% 1s'
        '118d34e685b5d1472267696d1010a393a59aed03', //'Booster de cristal +30% 1s'
        '422db99aac4ec594d483d8ef7faadc5d40d6f7d3', //'Booster de cristal +20% 1s'
        '3c9f85221807b8d593fa5276cdf7af9913c4a35d', //'Booster de cristal +10% 1s'
        '4b51d903560edd102467b110586000bd64fdb954', //'Booster de deutérium +40% 1s'
        '5560a1580a0330e8aadf05cb5bfe6bc3200406e2', //'Booster de deutérium +30% 1s'
        'e4b78acddfa6fd0234bcb814b676271898b0dbb3', //'Booster de deutérium +20% 1s'
        'd9fa5f359e80ff4f4c97545d07c66dbadab1d1be', //'Booster de deutérium +10% 1s'
        '77c36199102e074dca46f5f26ef57ce824d044dd', //'Booster d'énergie +80% 1s'
        '55b52cbfb148ec80cd4e5b0580f7bed01149d643', //'Booster d'énergie +60% 1s'
        'c2bad58fcec374d709099d11d0549e59ea7e233e', //'Booster d'énergie +40% 1s'
        '3f6f381dc9b92822406731a942c028adf8dc978f', //'Booster d'énergie +20% 1s'
        'f3d9b82e10f2e969209c1a5ad7d22181c703bb36', //'Extension planétaire +20'
        '04e58444d6d0beb57b3e998edc34c60f8318825a', //'Extension planétaire +15'
        '0e41524dc46225dca21c9119f2fb735fd7ea5cb3', //'Extension planétaire +9'
        '16768164989dffd819a373613b5e1a52e226a5b0', //'Extension planétaire +4'
        '8a426241572b2fea57844acd99bc326fe40e35cf', //'Extension lunaire +8'
        '05ee9654bd11a261f1ff0e5d0e49121b5e7e4401', //'Extension lunaire +6'
        'c21ff33ba8f0a7eadb6b7d1135763366f0c4b8bf', //'Extension lunaire +4'
        'be67e009a5894f19bbf3b0c9d9b072d49040a2cc', //'Extension lunaire +2'
        'c690f492cffe5f9f2952337e8eed307a8a62d6cf', //'Booster de métal +40% 30j'
        '6fecb993169fe918d9c63cd37a2e541cc067664e', //'Booster de métal +30% 30j'
        '742743b3b0ae1f0b8a1e01921042810b58f12f39', //'Booster de métal +20% 30j'
        '6bf45fcba8a6a68158273d04a924452eca75cf39', //'Booster de cristal +40% 30j'
        '36fb611e71d42014f5ebd0aa5a52bc0c81a0c1cb', //'Booster de cristal +30% 30j'
        '5b69663e3ba09a1fe77cf72c5094e246cfe954d6', //'Booster de cristal +20% 30j'
        '620f779dbffa1011aded69b091239727910a3d03', //'Booster de deutérium +40% 30j'
        '300493ddc756869578cb2888a3a1bc0c3c66765f', //'Booster de deutérium +30% 30j'
        '26416a3cdb94613844b1d3ca78b9057fd6ae9b15', //'Booster de deutérium +20% 30j'
        'dfe86378f8c3d7f3ee0790ea64603bc44e83ca47', //'Booster d'énergie +80% 30j'
        '4fa9a2273ee446284d5177fd9d60a22de01e932b', //'Booster d'énergie +60% 30j'
        'bedd248aaf288c27e9351cfacfa6be03f1dbb898', //'Booster d'énergie +40% 30j'
        'ca7f903a65467b70411e513b0920d66c417aa3a2', //'Booster de métal +40% 90j'
        '21c1a65ca6aecf54ffafb94c01d0c60d821b325d', //'Booster de métal +30% 90j'
        '6f44dcd2bd84875527abba69158b4e976c308bbc', //'Booster de métal +20% 90j'
        '7c2edf40c5cd54ad11c6439398b83020c0a7a6be', //'Booster de cristal +40% 90j'
        'd45f00e8b909f5293a83df4f369737ea7d69c684', //'Booster de cristal +30% 90j'
        '04d8afd5936976e32ce894b765ea8bd168aa07ef', //'Booster de cristal +20% 90j'
        '831c3ea8d868eb3601536f4d5e768842988a1ba9', //'Booster de deutérium +40% 90j'
        'dc5896bed3311434224d511fa7ced6fdbe41b4e8', //'Booster de deutérium +30% 90j'
        '6f0952a919fd2ab9c009e9ccd83c1745f98f758f', //'Booster de deutérium +20% 90j'
        'c39aa972a971e94b1d9b4d7a8f734b3d8be12534', //'Booster d'énergie +80% 90j'
        '5ad783dcfce3655ef97b36197425718a0dad6b66', //'Booster d'énergie +60% 90j'
        'e05aa5b9e3df5be3857b43da8403eafbf5ad3b96', //'Booster d'énergie +40% 90j'
        );
    $objet_uuid_str = array('m:40:0', 'm:30:0', 'm:20:0', 'm:10:0', 'c:40:0', 'c:30:0', 'c:20:0', 'c:10:0', 'd:40:0', 'd:30:0', 'd:20:0', 'd:10:0', 'e:80:0', 'e:60:0', 'e:40:0', 'e:20:0', 'p:20', 'p:15', 'p:9', 'p:4', 'm:8', 'm:6', 'm:4', 'm:2');
    $objet_uuid_tab = array(array('booster_m', 40), array('booster_m', 30), array('booster_m', 20), array('booster_m', 10),
        array('booster_c', 40), array('booster_c', 30), array('booster_c', 20), array('booster_c', 10),
        array('booster_d', 40), array('booster_d', 30), array('booster_d', 20), array('booster_d', 10),
        array('booster_e', 80), array('booster_e', 60), array('booster_e', 40), array('booster_e', 20),
        array('extention_p', 20), array('extention_p', 15), array('extention_p', 9), array('extention_p', 4),
        array('extention_m', 8), array('extention_m', 6), array('extention_m', 4), array('extention_m', 2));
    $separateur = '_';
    $default_str = array('m:0:0', 'c:0:0', 'd:0:0', 'e:0:0', 'p:0', 'm:0');

    //Protection débordement tableau, pour éviter les oublis sur la listes des boosters
    $n = count($objet_str);
    $ni = count($objet_uuid);
    $nb_type_booster = 4; //Type de booster x4 (métal, cristal, deutérium, énergie)
    $nb_booster_temps = 3; //Nombre de booster à temps (7j, 30j, 90j)
    if($ni != $n + $nb_type_booster * 3 * ($nb_booster_temps-1)) { //Erreur de codage, protection débordement tableau
        throw new Exception("Erreur interne : mauvais inventaire des boosters (uid=$ni, n=$n, add=" . ($n + $nb_type_booster * 3) . ")");
    }
    //Préparation des mêmes valeurs pour les boosters à différent temps.
    for($i=0 ; $i < $nb_booster_temps-1 ; $i++) {   //Par booster à temps supplémentaire x2 (30j, 90j)
        for($j=0 ; $j < $nb_type_booster ; $j++) {  //Par type de booster x3 (métal, cristal, deutérium)
            for($k=0 ; $k < 3 ; $k++) {             //Par nouveau % du booster x3 (+40%, +30%, +20%)
                // echo "$ni, $n, $k, $j, " . ($n + $j*3 + $k + $i*$nb_type_booster*3) . " ,$objet_str[$k]\n";
                $objet_str[$n + $k + $j*3 + $i*$nb_type_booster*3] = $objet_str[$k + $j*4];
                $objet_uuid_str[$n + $k + $j*3 + $i*$nb_type_booster*3] = $objet_uuid_str[$k + $j*4];
                $objet_uuid_tab[$n + $k + $j*3 + $i*$nb_type_booster*3] = $objet_uuid_tab[$k + $j*4];
            }
        }
    }
    if($ni != count($objet_uuid)) { //Erreur de codage, remplissage des tableaux
        throw new Exception("Erreur interne : mauvais inventaire des boosters, remplissage (uid=$ni, n=" . count($objet_uuid) . ")");
    }

    switch ($type) {
        case 'definition':
            return $objet_str;
        case 'array':
            $n = count($objet_uuid);
            for ($i = 0; $i < $n; $i++) {
                $result[$objet_uuid[$i]] = $objet_uuid_tab[$i];
            }
            return $result;
        case 'string':
            $n = count($objet_uuid);
            for ($i = 0; $i < $n; $i++) {
                $result[$objet_uuid[$i]] = $objet_uuid_str[$i];
            }
            return $result;
        case 'full':
            return array($objet_str, $objet_uuid, $objet_uuid_str, $objet_uuid_tab);
        case 'separateur':
            return $separateur;
        case 'default_str':
            return implode($separateur, $default_str);
        default:
            return $objet_uuid;
    }
}

/**
 * Indique si un uuid est enregistré dans OGSpy (il existe)
 * @uuid    string uuid récupéré de la page Ogame
 */
function booster_is_uuid($uuid)
{
    return in_array($uuid, booster_objets_tab());
}

/**
 * Mets à jour le tableau infos des boosters.
 * @boosters tableau infos des boosters (donnée par les fonctions booster_lire_bdd() ou booster_decode())
 * @uuid     string uuid de l'objet Ogame récupéré de la page Ogame
 * @date     date de fin de l'objet Ogame. [defaut=0]
 * return   le tableau à jour (par uuid et date)
 *          si $boosters==NULL OU booster_uuid($b) sans uuid -> donne tableau avec valeurs par défaut (équivalent booster_decode())
 *          NULL en cas d'erreur (uuid inconnu)
 */

function booster_uuid($boosters, $uuid = '', $date = 0)
{
    if ($boosters == NULL || $uuid == '') {
        $boosters = booster_decode();
        return $boosters;
    } else {
        $objet_uuid = booster_objets_tab('array');
        //if(isset($objet_uuid[$uuid]) || array_key_exists($uuid, $objet_uuid)) {
        if (isset($objet_uuid[$uuid])) {
            if ($objet_uuid[$uuid][0][0] == 'b') { //1er lettre de booster
                $boosters[$objet_uuid[$uuid][0] . '_val'] = $objet_uuid[$uuid][1];
                $boosters[$objet_uuid[$uuid][0] . '_date'] = $date;
            } elseif ($objet_uuid[$uuid][0][0] == 'e') { //1er lettre de extension
                $boosters[$objet_uuid[$uuid][0]] = $objet_uuid[$uuid][1];
            } else {
                return NULL;
            } //Ne devrait jamais arriver si les tableaux dans booster_objets_tab() sont bien construit
            return $boosters;
        }
    }
    return NULL;
}

/**
 * Transforme la date Ogame de format "*s *j *h" en nombre de seconde 6j 23h
 * @str string contenant le temps
 * @return int nombre de seconde correspondant à $str. 0 si problème
 */
function booster_lire_date($str)
{
    $time = 0;

    if (preg_match("/(\d+)s.(\d+)j.(\d+)h/", $str, $matches)) {
        $time = ($matches[1] * 604800 + $matches[2] * 86400 + $matches[3] * 3600);

    } elseif (preg_match("/(\d+)j.(\d+)h/", $str, $matches)) {

        $time = ($matches[1] * 86400 + $matches[2] * 3600);
    }

    return $time;
}

/*#######Lecture et modifications poussées  #######*/
/**
 * Transforme en tableau les données des objets Ogame contenues dans une string de stockage.
 * Si aucun argument n'ai donné alors elle renvoie les valeurs des objets par défaut.
 * @param $str  string de stockage des objets Ogame
 * @param null $boosters
 * @return  array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_c_val', 'booster_c_date', 'extention_p', 'extention_m')
 */
function booster_decode($str = NULL, $boosters = NULL)
{
    $result = array('booster_m_val' => 0, 'booster_m_date' => 0,
        'booster_c_val' => 0, 'booster_c_date' => 0,
        'booster_d_val' => 0, 'booster_d_date' => 0,
        'booster_e_val' => 0, 'booster_e_date' => 0,
        'extention_p' => 0, 'extention_m' => 0);
    $tab_boos = array('m','c','d','e');
    $tab_ex = array('p','m');
    
    if($str) {
        $s = booster_objets_tab('separateur');
        $str_split = explode($s, $str);
        foreach($str_split as $objet) {
            $i = 0;
            foreach($tab_boos as $booster) {
                if (preg_match("/$booster:(\\d+):(\\d+)/", $objet, $boosters) === 1) {
                    $i = 1;
                    $result['booster_' . $booster. '_val'] = intval($boosters[$i++]);
                    $result['booster_' . $booster. '_date'] = intval($boosters[$i]);
                }
            }
            if($i === 0) {
                foreach($tab_ex as $booster) {
                    if (preg_match("/$booster:(\\d+)$/", $objet, $boosters) === 1) {
                        $i = 1;
                        $result['extention_' . $booster] = intval($boosters[$i]);
                    }
                }
            }
        }
    }
    return $result;
}

/**
 * Transforme le tableau des informations des objets Ogame en une string de stockage.
 * @b tableau associatif des infos array('booster_m_val', 'booster_m_date', 'booster_c_val', 'booster_c_date', 'booster_d_val', 'booster_d_date', 'booster_e_val', 'booster_e_date','extention_p', 'extention_m')
 * @return objet sous format string de stockage ("m:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0 si pas d'argument)
 */
function booster_encode($b = NULL)
{
    $str = '';
    if ($b) {
        $separateur = booster_objets_tab('separateur');
        $str .= 'm:' . $b['booster_m_val'] . ':' . $b['booster_m_date'] . $separateur;
        $str .= 'c:' . $b['booster_c_val'] . ':' . $b['booster_c_date'] . $separateur;
        $str .= 'd:' . $b['booster_d_val'] . ':' . $b['booster_d_date'] . $separateur;
        $str .= 'e:' . $b['booster_e_val'] . ':' . $b['booster_e_date'] . $separateur;
        $str .= 'p:' . $b['extention_p'] . $separateur;
        $str .= 'm:' . $b['extention_m'];
    } else {
        $str = booster_objets_tab('default_str'); //"m:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0";
    }
    return $str;
}

/**
 * Transforme les valeurs des objets Ogame en une string de stockage.
 * string de stockage par défaut = m:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0
 * @return string sous format string de stockage ("m:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0" si pas d'argument)
 */
function booster_encodev($booster_m_val = 0, $booster_m_date = 0, $booster_c_val = 0, $booster_c_date = 0,
                         $booster_d_val = 0, $booster_d_date = 0, $booster_e_val = 0, $booster_e_date = 0,
                         $extention_p = 0, $extention_m = 0)
{
    $separateur = booster_objets_tab('separateur');
    $str = '';
    $str .= 'm:' . $booster_m_val . ':' . $booster_m_date . $separateur;
    $str .= 'c:' . $booster_c_val . ':' . $booster_c_date . $separateur;
    $str .= 'd:' . $booster_d_val . ':' . $booster_d_date . $separateur;
    $str .= 'e:' . $booster_e_val . ':' . $booster_e_date . $separateur;
    $str .= 'p:' . $extention_p . $separateur;
    $str .= 'm:' . $extention_m;
    return $str;
}

/**                     Fin booster partie                                     **/
/********************************************************************************/

/**
 * Retourne la liste des helpers presents
 * @return array
 */
function get_Helpers()
{
    $tHelpers = array();
    foreach (glob("core/helper/*_Helper.php") as $filename) {
        $helper = array();
        $sHelperName = "\Ogsteam\Ogspy\Helper\\" . basename("$filename", ".php");


        $helper['name'] = $sHelperName::getName();
        $helper['version'] = $sHelperName::getVersion();
        $helper['description'] = $sHelperName::getDescription();

        $tHelpers[] = $helper;
    }
    return $tHelpers;
}

