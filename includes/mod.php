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

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\Mod_Model;
use Ogsteam\Ogspy\Model\Mod_Config_Model;

/**
 * Fetch the mod list (admin only)
 * @return array $mod_list The list of mods in an array.
 * @todo Query : "select id, title, root, link, version, active, admin_only from ".TABLE_MOD." order by position, title";
 */
function mod_list()
{
    global $db, $user_data;

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

    //récuérration des mods
    $Mod_Model = new Mod_Model();
    $tMods = $Mod_Model->find_by(null, array('position' => 'ASC', 'title' => 'ASC'));

    foreach ($tMods as $mod) {
        $id = $mod['id'];
        $title = $mod['title'];
        $root = $mod['root'];
        $link = $mod['link'];
        $version = $mod['version'];
        $active = $mod['active'];
        $admin_only = $mod['admin_only'];

        if (isset($directories[$root])) { //Mod présent du répertoire "mod"
            if (in_array($link, $directories[$root]) && in_array("version.txt", $directories[$root])) {
                //Vérification disponibilité mise à jour de version
                $line = file("mod/" . $root . "/version.txt");
                $up_to_date = true;
                if (isset($line[1])) {
                    $current_mod_version = trim($line[1]);
                    if (file_exists("mod/" . $root . "/update.php")) {
                        $up_to_date = version_compare($current_mod_version, $version, '<=');
                    }
                }

                if ($active == 0) { // Mod désactivé
                    $mod_list["disabled"][] = array("id" => $id, "title" => $title, "version" => $version, "up_to_date" => $up_to_date);
                } else { //Mod activé
                    $mod_list["actived"][] = array("id" => $id, "title" => $title, "version" => $version, "up_to_date" => $up_to_date, "admin_only" => $admin_only);
                }
            } else { //Mod invalide
                $mod_list["wrong"][] = array("id" => $id, "title" => $title);
            }

            unset($directories[$root]);
        } else { //Mod absent du répertoire "mod"
            $mod_list["wrong"][] = array("id" => $id, "title" => $title);
        }


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

    if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
        redirection("index.php?action=message&id_message=forbidden&info");
    }

    switch ($check) {
        case "mod_id" :
            if (!check_var($pub_mod_id, "Num")) {
                redirection("index.php?action=message&id_message=errordata&info");
            }
            if (!isset($pub_mod_id)) {
                redirection("index.php?action=message&id_message=errorfatal&info");
            }
            break;

        case "directory" :
            if (!check_var($pub_directory, "Text")) {
                redirection("index.php?action=message&id_message=errordata&info");
            }
            if (!isset($pub_directory)) {
                redirection("index.php?action=message&id_message=errorfatal&info");
            }
            break;
    }
}

/**
 * Installs a Mod from a mod folder name (Fonction utilisée par la partie admin)
 * @global $pub_directory
 */
function mod_install()
{
    global $pub_directory, $server_config;

    $Mod_Model = new Mod_Model();

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
    if ($Mod_Model->isExistByTitle($value_mod[0])) {
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
    // si on arrive jusque la on peut installer
    require_once("mod/" . $pub_directory . "/install.php");

    //recuperation du mod
    $mod_id = $Mod_Model->get_mod_id_by_root($pub_directory);
    //récuperation de l'emplacement possible
    $position = $Mod_Model->get_position_max();

    ///update emplacement
    $Mod_Model->update_posisiton($mod_id,(int)($position + 1));

    //récuperation du titre en base
    $mod = $Mod_Model->find_by(array("id" => $mod_id));
    if (count($mod ==0) )
    {
        log_("mod_install", $mod[0]['title']);
    }
    else{
        log_("mod_install", "undefined ".$pub_directory);
    }

    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * mod_update (Fonction utilisée par la partie admin): Updates a mod version
 */
function mod_update()
{
    global $pub_mod_id, $server_config;
    global $pub_directory;

    $Mod_Model = new Mod_Model();

    mod_check("mod_id");

    //recuperation du mod
    //récuperation du titre en base
    $mod =$Mod_Model->find_one_by(array("id" => $pub_mod_id));

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

        log_("mod_update", $mod['title']);
    }
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * mod_uninstall (Fonction utilisée par la partie admin): Uninstall a mod from the database (Mod files are not deleted)
 *
 */
function mod_uninstall()
{
    global $pub_mod_id;

    $Mod_Model = new Mod_Model();
    mod_check("mod_id");

    // selection du mod
    $mod = $Mod_Model->find_one_by(array("id"=>$pub_mod_id));

    $root = $mod["root"];
    $title = $mod["title"];

    if (file_exists("mod/" . $root . "/uninstall.php")) {
        require_once("mod/" . $root . "/uninstall.php");
    }

    $Mod_Model->delete($pub_mod_id);

    log_("mod_uninstall",$title);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}


/**
 * Mod Activation
 */
function mod_active()
{
    global $pub_mod_id;

    $Mod_Model = new Mod_Model();

    mod_check("mod_id");

    $mod =$Mod_Model->find_one_by(array("id" => $pub_mod_id));
    $mod['active'] = 1;
    $Mod_Model->update($mod);

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

    mod_check("mod_id");

    $Mod_Model = new Mod_Model();
    $mod =$Mod_Model->find_one_by(array("id" => $pub_mod_id));
    $mod['active'] = 0;
    $Mod_Model->update($mod);

    log_("mod_disable",  $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

// Modifs par naruto kun

/**
 * Set the visibility of the mod (Admin)
 */
function mod_admin()
{
    global $pub_mod_id;

    mod_check("mod_id");

    $Mod_Model = new Mod_Model();
    $mod =$Mod_Model->find_one_by(array("id" => $pub_mod_id));
    $mod['admin_only'] = 1;
    $Mod_Model->update($mod);

    log_("mod_admin", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Set the visibility of the mod (User)
 */
function mod_normal()
{
    global $pub_mod_id;

    mod_check("mod_id");

    $Mod_Model = new Mod_Model();
    $mod =$Mod_Model->find_one_by(array("id" => $pub_mod_id));
    $mod['admin_only'] = 0;
    $Mod_Model->update($mod);

    log_("mod_normal", $mod['title']);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Function to set the position of a mod into the mod list
 * @param string $order up or down according to the new desired postion.
 */
function mod_sort($order)
{
    global $pub_mod_id;

    mod_check("mod_id");

    //récuérration des mods
    $Mod_Model = new Mod_Model();
    $tMod = $Mod_Model->find_by(null, array('position' => 'ASC', 'title' => 'ASC'));

    $oldModOrder  =array();
    $oldModPosition=0;
    foreach ($tMod as $mod)
    {
        $oldModOrder[$mod["position"]]= $mod;
        if ($pub_mod_id ==$mod["id"] )
        {
            $oldModPosition=$mod["position"];
        }
    }

    //changement de position
    $myMod=$oldModOrder[$oldModPosition];
    switch ($order) {
        case "up" :
            //si on eut monter la position
            if (isset($oldModOrder[$oldModPosition+1]))
            {
                //mod courant
                $Mod_Model->update_posisiton($myMod['id'],$oldModPosition+1 );
                //mod a bouger
                $modToMove = $oldModPosition[$oldModPosition+1];
                $Mod_Model->update_posisiton($modToMove['id'],$oldModPosition );
            }
            break;
        case "down" :
            //si on eut descendre la position
            if (isset($oldModOrder[$oldModPosition-1]))
            {
                //mod courant
                $Mod_Model->update_posisiton($myMod['id'],$oldModPosition-1 );
                //mod a bouger
                $modToMove = $oldModPosition[$oldModPosition+1];
                $Mod_Model->update_posisiton($modToMove['id'],$oldModPosition );
            }
            break;
    }
    log_("mod_order", $myMod["title"]);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Returns the version number of the current Mod.
 *
 * The function uses the $pub_action value to know what is the current mod
 * @global $pub_action
 * @return string Current mod version number
 * @api
 */
function mod_version()
{
    global $pub_action;

    $Mod_Model = new Mod_Model();
    $mod =$Mod_Model->find_one_by(array("root" => $pub_action));
    if (!is_null($mod))
    {
        return $mod["version"];
    }
    return "(ModInconnu:'{$pub_action}')";
}

/**
 * Mod Configs: Add or updates a configuration option for the mod
 * @param string $param Name of the parameter
 * @param string $value Value of the parameter
 * @param string $nom_mod Mod name
 * @global $db
 * @return boolean returns true if the parameter is correctly saved. false in other cases.
 * @api
 */
function mod_set_option($param, $value, $nom_mod = '')
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    return (new Mod_Config_Model)->set_mod_config($nom_mod, $param,$value);
}

/**
 * Mod Configs: Deletes a parameter for a mod
 * @param string $param Name of the parameter
 * @global $db
 * @return boolean returns true if the parameter is correctly saved. false in other cases.
 * @api
 */
function mod_del_option($param)
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    return (new Mod_Config_Model)->delete_mod_config($nom_mod, $param);
}

/**
 * Mod Configs : Reads a parameter value for the current mod
 * @param string $param Name of the parameter
 * @global $db
 * @return array()|string Returns the value of the requested parameter
 * @api
 */
function mod_get_option($param)
{
    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $retour = (new Mod_Config_Model)->get_mod_config($nom_mod, $param);
    if (count($retour)==0)
    {
        return '-1';
    }
    return $retour;
}

/**
 * Mod Configs : Gets the current mod name
 * @global $db
 * @global $pub_action
 * @global $directory
 * @global $mod_id
 * @return string Returns the current mod name
 */
function mod_get_nom()
{

    global $pub_action,$pub_mod_id;

    $nom_mod = '';
    if ($pub_action == 'mod_install') {
        global $pub_directory;
        $nom_mod = $pub_directory;
    } elseif ($pub_action == 'mod_update' || $pub_action == 'mod_uninstall') {
        $Mod_Model = (new Mod_Model())->find_one_by(array("id" => $pub_mod_id));
        $nom_mod = $Mod_Model["action"];
    } else {
        $nom_mod = $pub_action;
    }
    return $nom_mod;
}

/**
 * Deletes all configurations for the current mod
 * @global $db
 * @return boolean Returns true if at least one entry has been deleted. False if nothing has been removed.
 */
function mod_del_all_option()
{
    $nom_mod = mod_get_nom();
    return (new Mod_Config_Model)->delete_mod_config($nom_mod);
}

//\\ fonctions utilisable pour les mods //\\
/**
 * Funtion to install a new mod in OGSpy
 * @param string $mod_folder : Folder name which contains the mod
 * @return null|boolean true if the mod has been correctly installed
 * @api
 */
function install_mod($mod_folder)
{
    global  $server_config;
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
    if (isset($file[3])) {
        $mod_required_ogspy = trim($file[3]);
        if (isset($mod_required_ogspy)) {
            if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
                log_("mod_erreur_txt_version", $mod_folder);
                redirection("index.php?action=message&id_message=errormod&info");
                exit();
            }
        }
    } else {
        log_("mod_erreur_txt_warning", $mod_folder);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);

    // On vérifie si le mod est déjà installé""
    $Mod_Model = new Mod_Model();
    $mod = $Mod_Model->find_one_by(array("title" => $value_mod[0]));
    if (isset($mod['title']) )
    {
        if (count($value_mod) == 7) {
            $newMod = array();
            $newMod['title'] =  $value_mod[0];
            $newMod['menu'] = $value_mod[1];
            $newMod['action'] = $value_mod[2];
            $newMod['root'] =$value_mod[3] ;
            $newMod['link'] = $value_mod[2];
            $newMod['version'] = $mod_version;
            $newMod['active'] = $value_mod[5];
            $newMod['admin_only'] = $value_mod[6];

            $Mod_Model->add($newMod);

            $is_ok = true; /// tout c 'est bien passe'
        }
    }
    return $is_ok;
}

/**
 * Function to uninstall an OGSpy Module
 * @param string $mod_uninstall_name : Mod name
 * @param string $mod_uninstall_table : Name of the Database table used by the Mod that we need to remove
 * @api
 */
function uninstall_mod($mod_uninstall_name, $mod_uninstall_table)
{
    global $db;
    (new Mod_Model())->delete_by_title($mod_uninstall_name);
    if (!empty($mod_uninstall_table)) {
        //todo MOD factory ?
        log_("debug", "DROP TABLE IF EXISTS " . $mod_uninstall_table);
        $db->sql_query("DROP TABLE IF EXISTS " . $mod_uninstall_table);
    }
}

/**
 * Fonction to update the OGSpy mod
 * @param string $mod_folder : Folder name which contains the mod
 * @param string $mod_name : Mod name
 * @return null|boolean true if the mod has been correctly updated
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
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            log_("mod_erreur_txt_version", $mod_folder);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    $Mod_Model = new Mod_Model(); //récuperation du mod
    $mod = $Mod_Model->find_one_by(array("title" => $mod_name));
    $mod['version']=$mod_version;
    $Mod_Model->update($mod);

    $is_oki = true;
    return $is_oki;
}