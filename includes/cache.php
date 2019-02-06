<?php
/**
 * Functions relatives to the data cache file system
 * @package OGSpy
 * @subpackage Data Cache
 * @author Machine ( inspired by fluxbb cache system )
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.0.7
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\Config_Model;

/**
 * Function generate_config_cache()
 * Initialize the Cache filesystem
 */
function generate_config_cache()
{

    $output = (new  Config_Model())->get_all();

    $fh = @fopen('cache/cache_config.php', 'wb');
    if (!$fh) {
        if (!defined('UPGRADE_IN_PROGRESS')) {
            echo '<p>Impossible d écrire sur le fichier cache. Vérifier les droits d acces au dossier  \'cache\' </p>';
            log_("erreur_config_cache");
        }

    } else {
        fwrite($fh, '<?php' . "\n\n" . 'define(\'OGSPY_CONFIG_LOADED\', 1);' . "\n\n" . '$server_config = ' . var_export($output, true) . ';' . "\n\n" . '?>');

        fclose($fh);

    }

}


/**
 * Function generate_mod_cache()
 *
 * Generates the mod cache file system
 * @todo Queries : "SELECT action ,  menu ,  root, link, admin_only FROM ".TABLE_MOD." WHERE active = '1' order by position, title"
 */
function generate_mod_cache()
{
    global $db, $table_prefix, $server_config;
    $mod = NULL;

    $query = "SELECT action ,  menu ,  root, link, admin_only FROM " . TABLE_MOD . " WHERE active = '1' order by position, title";
    $result = $db->sql_query($query);

    while ($row = $db->sql_fetch_assoc($result)) {
        $mod[$row['action']] = $row;
    }

    $fh = @fopen('cache/cache_mod.php', 'wb');
    if (!$fh) {
        if (!defined('UPGRADE_IN_PROGRESS')) {
            echo '<p>Impossible d écrire sur le fichier cache. Vérifier les droits d acces au dossier  \'cache\' </p>';
            log_("erreur_mod_cache");
        }

    } else {
        fwrite($fh, '<?php' . "\n\n" . 'define(\'OGSPY_MOD_LOADED\', 1);' . "\n\n" . '$cache_mod = ' . var_export($mod, true) . ';' . "\n\n" . '?>');

        fclose($fh);

    }

}

/**
 * Fonction generate_all_cache()
 * Description: Generates the all cache file system
 */
function generate_all_cache()
{
    //'on supprime tous les fichier php du dossier cache'
    $files = glob('cache/*.php');
    foreach ($files as $filename) {
        unlink($filename);
    }

    // on les génére a nouveau
    generate_config_cache();
    generate_mod_cache();
}

