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

use Ogsteam\Ogspy\Model\Config_Model;
use Ogsteam\Ogspy\Model\Mod_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Function generate_config_cache()
 * Initialize the Cache filesystem
 */
function generate_config_cache()
{
    $output = NULL;

    $configRepository = new Config_Model();
    $configs = $configRepository->find_by();

    // Output config as PHP code
    foreach($configs as $config)
        $output[$config['config_name']] = stripslashes($config['config_value']);

    $fh = @fopen('cache/cache_config.php', 'wb');
    if (!$fh) {
        if (!defined('UPGRADE_IN_PROGRESS')) {
            echo '<p>Impossible d écrire sur le fichier cache. Vérifier les droits d acces au dossier  \'cache\' </p>';
            log_("erreur_config_cache");
        }

    } else {
        fwrite($fh, '<?php' . "\n\n" . 'define(\'OGSPY_CONFIG_LOADED\', 1);' . "\n\n" . '$server_config = ' . var_export($output, true) . ';' . "\n\n");
        fclose($fh);

    }
}


/**
 * Function generate_mod_cache()
 *
 * Generates the mod cache file system
 */
function generate_mod_cache()
{
    $modExport = NULL;

    // On récupère les mods actifs
    $modModel = new Mod_Model();
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
        fwrite($fh, '<?php' . "\n\n" . 'define(\'OGSPY_MOD_LOADED\', 1);' . "\n\n" . '$cache_mod = ' . var_export($modExport, true) . ';' . "\n\n");
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

