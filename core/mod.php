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

use Ogsteam\Ogspy\Model\Mod_Config_Model;
use Ogsteam\Ogspy\Model\Mod_Model;
use Ogsteam\Ogspy\Model\Mod_User_Config_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Fetch the mod list (admin only)
 * @return array $mod_list The list of mods in an array.
 * @todo Query : "select id, title, root, link, version, active, admin_only from ".TABLE_MOD." order by position, title";
 */
function mod_list()
{
    global $db, $user_data;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1)
        redirection("index.php?action=message&id_message=forbidden&info");

    //Listing des mod présents dans le répertoire "mod"
    $path = opendir("mod/");

    //Récupération de la liste des répertoires correspondant
    $directories = array();
    while ($file = readdir($path)) {
        if ($file != "." && $file != "..") {
            if (is_dir("mod/" . $file)) $directories[$file] = array();
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
        if (sizeof($directories[$d]) == 0) unset ($directories[$d]);
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
 * @param string $check type of varaible to be checked
 */
function mod_check($check)
{
    global $user_data;
    global $pub_mod_id, $pub_directory;

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1)
        redirection("index.php?action=message&id_message=forbidden&info");

    switch ($check) {
        case "mod_id" :
            if (!check_var($pub_mod_id, "Num")) redirection("index.php?action=message&id_message=errordata&info");
            if (!isset($pub_mod_id)) redirection("index.php?action=message&id_message=errorfatal&info");
            break;

        case "directory" :
            if (!check_var($pub_directory, "Text")) redirection("index.php?action=message&id_message=errordata&info");
            if (!isset($pub_directory)) redirection("index.php?action=message&id_message=errorfatal&info");
            break;
    }
}

/**
 * Installs a Mod from a mod folder name (Fonction utilisée par la partie admin)
 * @global $pub_directory
 * @todo Query : "SELECT title FROM " . TABLE_MOD . " WHERE title='" . $value_mod[0] .
 * @todo Query : "select id from ".TABLE_MOD." where root = '{$pub_directory}'"
 * @todo Query : "select max(position) from ".TABLE_MOD
 * @todo Query : "update ".TABLE_MOD." set position = ".($position+1)." where root = '{$pub_directory}'"
 * @todo Query :  "select title from ".TABLE_MOD." where id = '{$mod_id}'"
 */
function mod_install()
{
    global $db;
    global $pub_directory, $server_config;

    mod_check("directory");
    // modif pour 3.0.7 
    // check d un mod " normalisé"
    // voir @ shad 

    // fichier install non present
    if (!file_exists("mod/" . $pub_directory . "/install.php")) {
        log_("mod_erreur_install_php", $pub_directory);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //fichier . txt non present 
    if (!file_exists("mod/" . $pub_directory . "/version.txt")) {
        log_("mod_erreur_install_txt", $pub_directory);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }


    //verification  presence de majuscule
    if (!ctype_lower($pub_directory)) {
        log_("mod_erreur_minuscule", $pub_directory);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // verification sur le fichier .txt
    $filename = 'mod/' . $pub_directory . '/version.txt';
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

        log_("mod_erreur_txt_warning", $pub_directory);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    $mod_required_ogspy = trim($file[3]);
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            log_("mod_erreur_txt_version", $pub_directory);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    $position = $modRepository->get_position_max() +1;

    $mod = array_combine(array('title', 'menu', 'action', 'root', 'link', 'active', 'admin_only'), $value_mod);
    $mod['version'] = $mod_version;
    $mod['position'] = $position;
    $modRepository->add($mod);

    // si on arrive jusque la on peut installer
    require_once("mod/" . $pub_directory . "/install.php");

    log_("mod_install", $mod['title']);
    generate_mod_cache();

    redirection("index.php?action=administration&subaction=mod");
}

/**
 * mod_update (Fonction utilisée par la partie admin): Updates a mod version
 * @todo Query :  "select root from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 * @todo Query :  "select title from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 */
function mod_update()
{
    global $pub_mod_id, $server_config;

    mod_check("mod_id");

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    // Mod inconnu
    if(count($mods) != 1)
    {
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
 * @todo Query : "select root from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 * @todo Query : "select title from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 * @todo Query : "delete from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 *
 */
function mod_uninstall()
{
    global $db;
    global $pub_mod_id;

    mod_check("mod_id");

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if(count($mods) != 1)
    {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    if (file_exists("mod/" . $mod['root'] . "/uninstall.php")) {
        require_once("mod/" . $mod['root'] . "/uninstall.php");
    }

    //Suppression des paramètres du mod
    mod_del_all_option();
    // Suppression des paramètres utilisateur du mod
    mod_del_all_user_option();

    $modRepository->delete($mod['id']);


    log_("mod_uninstall", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}


/**
 * Mod Activation
 * @todo Query : "update ".TABLE_MOD." set active='1' where id = '{$pub_mod_id}'"
 * @todo Query : "select title from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 */
function mod_active()
{
    global $db;
    global $pub_mod_id;

    mod_check("mod_id");

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if(count($mods) != 1)
    {
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
 * @todo Query : "update ".TABLE_MOD." set active='0' where id = '{$pub_mod_id}'"
 * @todo Query : "select title from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 */
function mod_disable()
{
    global $db;
    global $pub_mod_id;

    mod_check("mod_id");

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if(count($mods) != 1)
    {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    $mod['active'] = 0;
    $modRepository->update($mod);

    log_("mod_disable", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Set the visibility of the mod (Admin)
 * @todo Query : "update ".TABLE_MOD." set active='0' where id = '{$pub_mod_id}'"
 * @todo Query : "select title from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 */
function mod_admin()
{
    global $db;
    global $pub_mod_id;

    mod_check("mod_id");

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if(count($mods) != 1)
    {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    $mod['admin_only'] = 1;
    $modRepository->update($mod);

    log_("mod_admin", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Set the visibility of the mod (User)
 * @todo Query : "update ".TABLE_MOD." set admin_only='0' where id = '{$pub_mod_id}'"
 * @todo Query : "select title from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 */
function mod_normal()
{
    global $db;
    global $pub_mod_id;

    mod_check("mod_id");

    $modRepository = new Mod_Model();
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));

    if(count($mods) != 1)
    {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $mod = $mods[0];

    $mod['admin_only'] = 0;
    $modRepository->update($mod);

    log_("mod_normal", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Function to set the position of a mod into the mod list
 * @param string $order up or down according to the new desired postion.
 * @todo Query : "select id from ".TABLE_MOD." order by position, title"
 * @todo Query : "update ".TABLE_MOD." set position = ".$i." where id = ".key($mods)
 * @todo Query : "select title from ".TABLE_MOD." where id = '{$pub_mod_id}'"
 */
function mod_sort($order)
{
    global $db;
    global $pub_mod_id;

    mod_check("mod_id");

    $modRepository = new Mod_Model();
    // On récupère le mod souhaité
    $mods = $modRepository->find_by(array('id' => $pub_mod_id));
    if(count($mods) != 1)
    {
        log_("mod_erreur_unknown", $pub_mod_id);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }
    $maxPosition = $modRepository->get_position_max();

    $currentMod = $mods[0];
    $oldPosition = $currentMod['position'];

    switch ($order) {
        case "up" :
            $newPosition =  max(1, $oldPosition-1);
            break;
        case "down" :
            $newPosition =  min($maxPosition, $oldPosition+1);
            break;
    }

    // Pas de changement de position
    if($newPosition == $oldPosition)
    {
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
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Returns the version number of the current Mod.
 *
 * The function uses the $pub_action value to know what is the current mod
 * @global $pub_action
 * @return string Current mod version number
 * @todo Query : "select `version` from ".TABLE_MOD." where root = '{$pub_action}'"
 * @api
 */
function mod_version()
{
    global $pub_action;

    $modsRepository = new Mod_Model();
    $mods = $modsRepository->find_by(array('root' => $pub_action));
    if(count($mods) == 1)
        return $mods[0]['version'];

    return "(ModInconnu:'{$pub_action}')";
}

/**
 * Mod Configs: Add or updates a configuration option for the mod
 * @param string $param Name of the parameter
 * @param string $value Value of the parameter
 * @param string $nom_mod Mod name
 * @global $db
 * @return boolean returns true if the parameter is correctly saved. false in other cases.
 * @todo Query : 'REPLACE INTO ' . TABLE_MOD_CFG . ' VALUES ("' . $nom_mod . '", "' . $param . '", "' . $value . '")'
 * @api
 */
function mod_set_option($param, $value, $nom_mod = '')
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) redirection("index.php?action=message&id_message=errordata&info");
    $modModel = new Mod_Config_Model();
    return $modModel->set_mod_config($nom_mod, $param, $value);
}

/**
 * Mod Configs: Add or updates a configuration option for the mod
 * @param string $param Name of the parameter
 * @param integer $user_id Id of the user
 * @param string $value Value of the parameter
 * @param string $nom_mod Mod name
 * @return bool returns true if the parameter is correctly saved. false in other cases.
 * @global $db
 * @api
 */
function mod_set_user_option($param, $user_id, $value, $nom_mod = '')
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) redirection("index.php?action=message&id_message=errordata&info");
    if (!check_var($user_id, "Num")) redirection("index.php?action=message&id_message=errordata&info");

    $modModel = new Mod_User_Config_Model();
    return $modModel->set_mod_config($nom_mod, $param, $user_id, $value);
}

/**
 * Mod Configs: Deletes a parameter for a mod
 * @param string $param Name of the parameter
 * @global $db
 * @return boolean returns true if the parameter is correctly saved. false in other cases.
 * @todo Query : 'DELETE FROM ' . TABLE_MOD_CFG . ' WHERE `mod` = "' . $nom_mod . '" AND `config` = "' . $param . '"'
 * @api
 */
function mod_del_option($param)
{
    global $db;

    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) redirection("index.php?action=message&id_message=errordata&info");
    $modModel = new Mod_Config_Model();
    return $modModel->delete_mod_config($nom_mod, $param);
}

/**
 * Mod Configs: Deletes a parameter for a mod and a user
 * @param string $param Name of the parameter
 * @param $user_id Id of the user
 * @return bool returns true if the parameter is correctly saved. false in other cases.
 * @global $db
 * @api
 */
function mod_del_user_option($param, $user_id)
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) redirection("index.php?action=message&id_message=errordata&info");
    if (!check_var($user_id, "Num")) redirection("index.php?action=message&id_message=errordata&info");

    $modModel = new Mod_User_Config_Model();
    return $modModel->delete_mod_config($nom_mod, $user_id, $param);
}

/**
 * Mod Configs : Reads a parameter value for the current mod
 * @param string $param Name of the parameter
 * @global $db
 * @return string Returns the value of the requested parameter
 * @todo Query : 'SELECT value FROM ' . TABLE_MOD_CFG . ' WHERE `mod` = "' . $nom_mod . '" AND `config` = "' . $param . '"'
 * @api
 */
function mod_get_option($param)
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) redirection("index.php?action=message&id_message=errordata&info");

    $modModel = new Mod_Config_Model();
    $result = $modModel->get_mod_config($nom_mod, $param);
    if (count($result) == 0)
        return '-1';

    return $result;
}

/**
 * Mod Configs : Reads a parameter value for the current mod and a specific user
 * @param string $param Name of the parameter
 * @param integer $user_id Id of the user
 * @return array Returns an array with the value of the requested parameter
 * @global $db
 * @api
 */
function mod_get_user_option($user_id, $param = null)
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) redirection("index.php?action=message&id_message=errordata&info");
    if (!check_var($user_id, "Num")) redirection("index.php?action=message&id_message=errordata&info");

    $modModel = new Mod_User_Config_Model();
    $result = $modModel->get_mod_config($nom_mod, $user_id, $param);

    return $result;
}

/**
 * Mod Configs : Gets the current mod name
 * @global $db
 * @global $pub_action
 * @global $directory
 * @global $mod_id
 * @return string Returns the current mod name
 * @todo Query : 'SELECT `action` FROM ' . TABLE_MOD . ' WHERE id=' . $pub_mod_id
 */
function mod_get_nom()
{
    global $db;
    global $pub_action;

    $nom_mod = '';
    if ($pub_action == 'mod_install') {
        global $pub_directory;
        $nom_mod = $pub_directory;
    } elseif ($pub_action == 'mod_update' || $pub_action == 'mod_uninstall') {
        global $pub_mod_id;

        $modsRepository = new Mod_Model();
        $mods = $modsRepository->find_by(array('id' => $pub_mod_id));
        if(count($mods) == 1)
            $nom_mod = $mods[0]['action'];
        else
            $nom_mod = $pub_action;
    } else {
        $nom_mod = $pub_action;
    }
    return $nom_mod;
}

/**
 * Deletes all configurations for the current mod
 * @global $db
 * @return boolean Returns true if at least one entry has been deleted. False if nothing has been removed.
 * @todo Query : 'DELETE FROM ' . TABLE_MOD_CFG . ' WHERE `mod` = "' . $nom_mod . '"'
 */
function mod_del_all_option()
{
    global $db;

    $nom_mod = mod_get_nom();
    $modModel = new Mod_Config_Model();
    return $modModel->delete_mod_config($nom_mod);
}

/**
 * Deletes all user configurations for the current mod
 * @global $db
 * @return boolean Returns true if at least one entry has been deleted. False if nothing has been removed.
 */
function mod_del_all_user_option()
{
    global $db;

    $nom_mod = mod_get_nom();
    $modModel = new Mod_User_Config_Model();
    return $modModel->delete_mod_config($nom_mod);
}

/**
 * Function to uninstall an OGSpy Module
 * @param string $mod_uninstall_table : Name of the Database table used by the Mod that we need to remove
 * @todo Query: "DELETE FROM " . TABLE_MOD . " WHERE title='" . $mod_uninstall_name ."'
 * @api
 */
function uninstall_mod($mod_uninstall_table)
{
    global $db;
    if (!empty($mod_uninstall_table)) {
        log_("debug", "DROP TABLE IF EXISTS " . $mod_uninstall_table);
        $db->sql_query("DROP TABLE IF EXISTS " . $mod_uninstall_table);
    }
}