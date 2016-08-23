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

namespace Ogsteam\Ogspy;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Function generate_config_cache()
 * Initialize the Cache filesystem
 * @todo Queries : "select * from " . TABLE_CONFIG,
 */
function generate_config_cache()
{
    global $db, $table_prefix, $server_config;
    $output = NULL;

    $request = "select * from " . TABLE_CONFIG;
    $result = $db->sql_query($request);

    // Output config as PHP code
    while ($cur_config_item = $db->sql_fetch_row($result)) {
        $output[$cur_config_item[0]] = stripslashes($cur_config_item[1]);
    }

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
    global $db;
    $modExport = NULL;

    // On récupère les mods actifs
    $modModel = new Model\Mod_Model();
    $mods = $modModel->find_by(array('active' => 1), array('position' => 'ASC', 'title' => 'ASC'));

    // On extrait les propriétés souhaitées
    foreach($mods as $mod)
    {
        $modExport[$mod['action']] = array('action' => $mod['action'],
                                           'menu' => $mod['menu'],
                                           'root' => $mod['root'],
                                           'link' => $mod['link'],
                                           'admin_only' => $mod['admin_only']
        );
    }

    $fh = @fopen('cache/cache_mod.php', 'wb');
    if (!$fh) {
        if (!defined('UPGRADE_IN_PROGRESS')) {
            echo '<p>Impossible d écrire sur le fichier cache. Vérifier les droits d acces au dossier  \'cache\' </p>';
            log_("erreur_mod_cache");
        }

    } else {
        fwrite($fh, '<?php' . "\n\n" . 'define(\'OGSPY_MOD_LOADED\', 1);' . "\n\n" . '$cache_mod = ' . var_export($modExport, true) . ';' . "\n\n" . '?>');

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

