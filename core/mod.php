<?php
/**
 * Functions used for OGSpy Mods
 * @package OGSpy
 * @subpackage mods
 * @author Kyser
 * @created 21/07/2006
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ($Rev: 7692 $)
 */

namespace Ogsteam\Ogspy;

use Ogsteam\Ogspy\Model\Mod_Model;


/**
 * Fetch the mod list (admin only)
 * @return array $mod_list The list of mods in an array.
 */
function mod_list()
{
    global $user_data;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("index.php?action=message&id_message=forbidden&info");
    }

    //Listing des mod présents dans le répertoire "mod"
    $path = opendir("mod/");

    //Récupération de la liste des répertoires correspondant
    $directories = array();
    while ($file = readdir($path)) {
        if ($file != "." && $file != "..") {
            if (is_dir("mod/" . $file)) {
                $directories[$file] = array();
            }
        }
    }
    closedir($path);

    foreach (array_keys($directories) as $d) {
        $path = opendir("mod/" . $d);

        while ($file = readdir($path)) {
            if ($file != "." && $file != "..") {
                $directories[$d][] = $file;
            }
        }
        closedir($path);
        if (sizeof($directories[$d]) == 0) {
            unset ($directories[$d]);
        }
    }


    $mod_list = array("disabled" => array(), "actived" => array(), "wrong" => array(), "unknown" => array(), "install" => array());
    $modModel = new Mod_Model();
    $mods = $modModel->find_by(null, array('position' => 'ASC', 'title' => 'ASC'));

    foreach ($mods as $mod) {
        //Mod absent du répertoire "mod"
        if (!isset($directories[$mod['root']])) {
            $mod_list["wrong"][] = array("id" => $mod['id'], "title" => $mod['title']);
            continue;
        }

        // Mod invalide
        $rootDirectory = $directories[$mod['root']];
        if (!in_array($mod['link'], $rootDirectory) || !in_array("version.txt", $rootDirectory)) {
            $mod_list["wrong"][] = array("id" => $mod['id'], "title" => $mod['title']);
            continue;
        }

        //Vérification disponibilité mise à jour de version
        $line = file("mod/" . $mod['root'] . "/version.txt");
        $up_to_date = true;
        if (isset($line[1])) {
            $current_mod_version = trim($line[1]);
            if (file_exists("mod/" . $mod['root'] . "/update.php")) {
                $up_to_date = version_compare($current_mod_version, $mod['version'], '<=');
            }
        }

        if ($mod['active'] == 0) { // Mod désactivé
            $mod_list["disabled"][] = array("id" => $mod['id'], "title" => $mod['title'], "version" => $mod['version'], "up_to_date" => $up_to_date);
        } else { //Mod activé
            $mod_list["actived"][] = array("id" => $mod['id'], "title" => $mod['title'], "version" => $mod['version'], "up_to_date" => $up_to_date, "admin_only" => $mod['admin_only']);
        }

        unset($directories[$mod['root']]);
    }

    while ($files = @current($directories)) {
        if (in_array("version.txt", $files) && in_array("install.php", $files)) {
            $line = file("mod/" . key($directories) . "/version.txt");
            if (isset($line[0])) {
                $mod_list["install"][] = array("title" => $line[0], "directory" => key($directories));
            }
        }
        next($directories);
    }

    return $mod_list;
}

/**
 * Function mod_check : Checks if an unauthorized user tries to install a mod without being admin or with wrong parameters
 */
function mod_check($check_type, $data)
{
    global $user_data;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("index.php?action=message&id_message=forbidden&info");
    }

    switch ($check_type) {
        case "mod_id" :
            if (!check_var($data, "Num")) {
                redirection("index.php?action=message&id_message=errordata&info");
            }
            break;

        case "directory" :
            if (!check_var($data, "Text")) {
                redirection("index.php?action=message&id_message=errordata&info");
            }
            break;
    }
}

/**
 * Installs a Mod from a mod folder name (Fonction utilisée par la partie admin)
 * @global $pub_directory
 * @return null|boolean
 * @global $pub_directory
 *
 */
function mod_install($mod_folder_name)
{
    global $server_config;

    mod_check("directory", $mod_folder_name);

    // fichier install non present
    if (!file_exists("mod/" . $mod_folder_name . "/install.php")) {
        log_("mod_erreur_install_php", $mod_folder_name);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //fichier . txt non present
    if (!file_exists("mod/" . $mod_folder_name . "/version.txt")) {
        log_("mod_erreur_install_txt", $mod_folder_name);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //verification  presence de majuscule
    if (!ctype_lower($mod_folder_name)) {
        log_("mod_erreur_minuscule", $mod_folder_name);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // verification sur le fichier .txt
    $filename = 'mod/' . $mod_folder_name . '/version.txt';
    // On récupère les données du fichier version.txt
    $file = file($filename);
    $mod_version = trim($file[1]);
    $mod_config = trim($file[2]);
    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);

    // On vérifie si le mod est déjà installé""
    $modRepository = new Mod_Model();
    $installedMods = $modRepository->find_by(array('title' => $value_mod[0]));
    if (count($installedMods) != 0) {

        log_("mod_erreur_install_bis", $value_mod[0]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    if (count($value_mod) != 7) {

        log_("mod_erreur_txt_warning", $mod_folder_name);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    $mod_required_ogspy = trim($file[3]);
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            log_("mod_erreur_txt_version", $mod_folder_name);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    $position = $modRepository->get_position_max() + 1;

    $mod = array_combine(array('title', 'menu', 'action', 'root', 'link', 'active', 'admin_only'), $value_mod);
    $mod['version'] = $mod_version;
    $mod['position'] = $position;
    $modRepository->add($mod);

    // si on arrive jusque la on peut installer
    require_once("mod/" . $mod_folder_name . "/install.php");

    generate_mod_cache();
    log_("mod_install", $mod['title']);

    return true;
}

/**
 * mod_update (Fonction utilisée par la partie admin): Updates a mod version
 */
function mod_update()
{
    global $pub_mod_id, $server_config;

    mod_check("mod_id", $pub_mod_id);

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    // Mod inconnu
    if (count($mods) != 1) {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    // modif pour 3.0.7
    // check d un mod " normalisé"
    // voir @ shad

    // fichier mod_erreur_update non present
    if (!file_exists("mod/" . $mod['root'] . "/update.php")) {
        log_("mod_erreur_update", $mod['root']);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //fichier . txt non present
    if (!file_exists("mod/" . $mod['root'] . "/version.txt")) {
        log_("mod_erreur_install_txt", $mod['root']);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //verification  presence de majuscule
    if (!ctype_lower($mod['root'])) {
        log_("mod_erreur_minuscule", $mod['root']);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();

    }

    // verification sur le fichier .txt
    $filename = 'mod/' . $mod['root'] . '/version.txt';
    // On récupère les données du fichier version.txt
    $file = file($filename);
    $mod_version = trim($file[1]);
    $mod_config = trim($file[2]);
    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);
    if (count($value_mod) != 7) {
        log_("mod_erreur_txt_warning", $mod['root']);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    $mod_required_ogspy = trim($file[3]);
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            log_("mod_erreur_txt_version", $mod['root']);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    if (file_exists("mod/" . $mod['root'] . "/update.php")) {
        require_once("mod/" . $mod['root'] . "/update.php");

        $mod['version'] = $mod_version;
        $modRepository->update($mod);
        log_("mod_update", $mod['title']);
    }
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * mod_uninstall (Fonction utilisée par la partie admin): Uninstall a mod from the database (Mod files are not deleted)
 */
function mod_uninstall($mod_folder_name = "", $mod_uninstall_table = '')
{
    mod_check("Directory", $mod_folder_name);

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('root' => $mod_folder_name));

    if (count($mods) != 1) {
        log_("mod_erreur_unknown", $mod_folder_name);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    if (file_exists("mod/" . $mod['root'] . "/uninstall.php")) {
        require_once("mod/" . $mod['root'] . "/uninstall.php");
    }

    $mod_tools = new Mod_DevTools($mod['root']);
    //Suppression des paramètres du mod
    $mod_tools->mod_del_all_option();
    // Suppression des paramètres utilisateur du mod
    $mod_tools->mod_del_all_user_option();

    $modRepository->delete($mod['id']);

    log_("mod_uninstall", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Mod Activation
 */
function mod_active()
{
    global $pub_mod_id;

    mod_check("mod_id", $pub_mod_id);

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if (count($mods) != 1) {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    $mod['active'] = 1;
    $modRepository->update($mod);

    log_("mod_active", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Disables a Mod
 */
function mod_disable()
{
    global $pub_mod_id;

    mod_check("mod_id", $pub_mod_id);

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if (count($mods) != 1) {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    $mod['active'] = 0;
    $modRepository->update($mod);

    log_("mod_disable", $mod['title']);
    generate_mod_cache();
    return true;
}

/**
 * Set the visibility of the mod (Admin)
 */
function mod_admin()
{
    global $pub_mod_id;

    mod_check("mod_id", $pub_mod_id);

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if (count($mods) != 1) {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    $mod['admin_only'] = 1;
    $modRepository->update($mod);

    log_("mod_admin", $mod['title']);
    generate_mod_cache();
    return true;
}

/**
 * Set the visibility of the mod (User)
 */
function mod_normal()
{
    global $pub_mod_id;

    mod_check("mod_id", $pub_mod_id);

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if (count($mods) != 1) {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    $mod['admin_only'] = 0;
    $modRepository->update($mod);

    log_("mod_normal", $mod['title']);
    generate_mod_cache();
    return true;
}

/**
 * Function to set the position of a mod into the mod list
 * @param string $order up or down according to the new desired postion.
 * @return null|boolean
 */
function mod_sort($order)
{
    global $pub_mod_id;

    mod_check("mod_id", $pub_mod_id);

    $modRepository = new Mod_Model();
    // On récupère le mod souhaité
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));
    if (count($mods) != 1) {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $maxPosition = $modRepository->get_position_max();

    $currentMod = $mods[0];
    $oldPosition = $currentMod['position'];

    switch ($order) {
        case "up" :
            $newPosition = max(1, $oldPosition - 1);
            break;
        case "down" :
            $newPosition = min($maxPosition, $oldPosition + 1);
            break;
    }

    // Pas de changement de position
    if ($newPosition == $oldPosition) {
        log_("mod_order", $currentMod['title']);
        generate_mod_cache();
        redirection("index.php?action=administration&subaction=mod");
    }

    $currentMod['position'] = $newPosition;

    $switchMod = $modRepository->find_by(array('position' => $newPosition))[0];
    $switchMod['position'] = $oldPosition;

    $modRepository->update($currentMod);
    $modRepository->update($switchMod);

    log_("mod_order", $currentMod['title']);
    generate_mod_cache();
    return true;
}

/**
 * Mod Configs : Gets the current mod name
 * @global $pub_action
 * @global $directory
 * @global $mod_id
 * @return string Returns the current mod name
 */
function mod_get_nom($mod_id = null)
{
    global $pub_action;

    if ($pub_action == 'mod_install') {
        global $pub_directory;
        $nom_mod = $pub_directory;
    } elseif ($pub_action == 'mod_update' || $pub_action == 'mod_uninstall') {

        $modsRepository = new Mod_Model();
        $mods = $modsRepository->find_by(array('id' => $mod_id));
        if (count($mods) == 1) {
            $nom_mod = $mods[0]['action'];
        } else {
            $nom_mod = $pub_action;
        }
    } else {
        $nom_mod = $pub_action;
    }
    return $nom_mod;
}