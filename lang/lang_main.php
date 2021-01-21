<?php
/**
 * Language include file list
 * @package OGSpy
 * @subpackage i18n
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
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

/**
 * Encode du texte pour un affichage sans problème en HTML avec prise en compte \n.
 *
 * @param string $texte Le texte à sécuriser
 * @param string $format Le type de format final à encoder [défaut=HTML]
 * @return string le texte encodé.
 */
function lang_print($texte, $format = 'HTML') {
    $text = '';
    if ($format === 'HTML') { //Pour HTML
        $text = htmlspecialchars($texte, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8');
        // $text = nl2br($text, false);
        $text = str_replace( array("\r\n", "\r", "\n"), '<br>', $text ); //Afin de retirer les retours à ligne, non supp par nl2br
    }

    return $text;
}

/**
 * @brief Sécurise en formatant correctement les textes de langues.
 *
 * @param [in] $s_lang Tableau des textes de langue
 * @return tableau encodé
 *
 */
function lang_secure($s_lang) {
    foreach($s_lang as $key => $value){
        $s_lang[$key] = lang_print($value);
    }
    return $s_lang;
}

global $lang;
$lang = array();

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
    load_lang_file($ui_lang, "lang_help.php");
}

//TODO: Nettoyer les fichiers de lang avant ! 
// $lang = lang_secure($lang);
