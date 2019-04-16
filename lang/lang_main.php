<?php
/**
 * Language include file list
 * @package OGSpy
 * @subpackage i18n
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.0
 */

function load_lang_file($ui_lang, $filename, $parent_dir = ".") {
    global $lang;
    if(empty($lang)) {
        $lang = array();       
    }
    $default_language = 'fr';
    $file_path = $parent_dir . "/lang/" . $ui_lang . "/" . $filename;
    $default_file_path = $parent_dir . "/lang/" . $default_language . "/" . $filename;
    if (file_exists($file_path)) {
        require_once ($file_path);
        return;
    }
    trigger_error($file_path . " does not exist! Please consider helping with the translation!", E_USER_WARNING);
    trigger_error("Loading " . $default_file_path, E_USER_WARNING);
    require_once ($default_file_path);
}

global $lang = [];

if (defined("INSTALL_IN_PROGRESS") || defined("UPGRADE_IN_PROGRESS")) {
    load_lang_file($ui_lang, "lang_install.php", "..");
    load_lang_file($ui_lang, "lang_help.php", "..");
} else
{
    load_lang_file($ui_lang, "lang_about.php");
    load_lang_file($ui_lang, "lang_admin.php");
    load_lang_file($ui_lang, "lang_cartography.php");
    load_lang_file($ui_lang, "lang_galaxy.php");
    load_lang_file($ui_lang, "lang_game.php");
    load_lang_file($ui_lang, "lang_header_tail.php");
    load_lang_file($ui_lang, "lang_help.php");
    load_lang_file($ui_lang, "lang_home.php");
    load_lang_file($ui_lang, "lang_login.php");
    load_lang_file($ui_lang, "lang_mail.php");
    load_lang_file($ui_lang, "lang_menu.php");
    load_lang_file($ui_lang, "lang_message.php");
    load_lang_file($ui_lang, "lang_profile.php");
    load_lang_file($ui_lang, "lang_ranking.php");
    load_lang_file($ui_lang, "lang_report.php");
    load_lang_file($ui_lang, "lang_search.php");
    load_lang_file($ui_lang, "lang_serverdown.php");
    load_lang_file($ui_lang, "lang_statistic.php");
}


