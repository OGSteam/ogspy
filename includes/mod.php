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

    $request = "select id, title, root, link, version, active, admin_only from " . TABLE_MOD . " order by position, title";
    $result = $db->sql_query($request);
    while (list($id, $title, $root, $link, $version, $active, $admin_only) = $db->sql_fetch_row($result)) {
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
    $check = "SELECT title FROM " . TABLE_MOD . " WHERE title='" . $value_mod[0] .
        "'";
    $query_check = $db->sql_query($check);
    $result_check = $db->sql_numrows($query_check);

    if ($result_check != 0) {

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

    $request = "select id from " . TABLE_MOD . " where root = '{$pub_directory}'";
    $result = $db->sql_query($request);
    list($mod_id) = $db->sql_fetch_row($result);

    $request = "select max(position) from " . TABLE_MOD;
    $result = $db->sql_query($request);
    list($position) = $db->sql_fetch_row($result);

    $request = "update " . TABLE_MOD . " set position = " . ($position + 1) . " where root = '{$pub_directory}'";
    $db->sql_query($request);

    $request = "select title from " . TABLE_MOD . " where id = '{$mod_id}'";
    $result = $db->sql_query($request);
    list($title) = $db->sql_fetch_row($result);
    log_("mod_install", $title);
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
    global $db, $pub_mod_id, $server_config;
    global $pub_directory;

    mod_check("mod_id");

    $request = "select root from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($root) = $db->sql_fetch_row($result);


    // modif pour 3.0.7
    // check d un mod " normalisé"
    // voir @ shad 

    // fichier mod_erreur_update non present
    if (!file_exists("mod/" . $root . "/update.php")) {
        log_("mod_erreur_update", $root);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //fichier . txt non present 
    if (!file_exists("mod/" . $root . "/version.txt")) {
        log_("mod_erreur_install_txt", $root);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //verification  presence de majuscule
    if (!ctype_lower($root)) {
        log_("mod_erreur_minuscule", $root);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();

    }

    // verification sur le fichier .txt
    $filename = 'mod/' . $root . '/version.txt';
    // On récupère les données du fichier version.txt
    $file = file($filename);
    $mod_version = trim($file[1]);
    $mod_config = trim($file[2]);
    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);
    if (count($value_mod) != 7) {
        log_("mod_erreur_txt_warning", $root);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    $mod_required_ogspy = trim($file[3]);
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            log_("mod_erreur_txt_version", $root);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    if (file_exists("mod/" . $root . "/update.php")) {
        require_once("mod/" . $root . "/update.php");

        $request = "select title from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
        $result = $db->sql_query($request);
        list($title) = $db->sql_fetch_row($result);
        log_("mod_update", $title);
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

    $request = "select root from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($root) = $db->sql_fetch_row($result);
    if (file_exists("mod/" . $root . "/uninstall.php")) {
        require_once("mod/" . $root . "/uninstall.php");
    }

    $request = "select title from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($title) = $db->sql_fetch_row($result);

    $request = "delete from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $db->sql_query($request);

    log_("mod_uninstall", $title);
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

    $request = "update " . TABLE_MOD . " set active='1' where id = '{$pub_mod_id}'";
    $db->sql_query($request);

    $request = "select title from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($title) = $db->sql_fetch_row($result);
    log_("mod_active", $title);
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

    $request = "update " . TABLE_MOD . " set active='0' where id = '{$pub_mod_id}'";
    $db->sql_query($request);

    $request = "select title from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($title) = $db->sql_fetch_row($result);
    log_("mod_disable", $title);
    generate_mod_cache();
    redirection("index.php?action=administration&subaction=mod");
}

// Modifs par naruto kun

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

    $request = "update " . TABLE_MOD . " set admin_only='1' where id = '{$pub_mod_id}'";
    $db->sql_query($request);

    $request = "select title from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($title) = $db->sql_fetch_row($result);

    log_("mod_admin", $title);
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

    $request = "update " . TABLE_MOD . " set admin_only='0' where id = '{$pub_mod_id}'";
    $db->sql_query($request);

    $request = "select title from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($title) = $db->sql_fetch_row($result);

    log_("mod_normal", $title);
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

    $mods = array();
    $request = "select id from " . TABLE_MOD . " order by position, title";
    $result = $db->sql_query($request);
    $i = 1;
    while (list($id) = $db->sql_fetch_row($result)) {
        $mods[$id] = $i;
        $i++;
    }

    //Parade pour éviter les mods qui aurait les même positions
    switch ($order) {
        case "up" :
            $mods[$pub_mod_id] -= 1.5;
            break;
        case "down" :
            $mods[$pub_mod_id] += 1.5;
            break;
    }

    asort($mods);
    $i = 1;
    while (current($mods)) {
        $request = "update " . TABLE_MOD . " set position = " . $i . " where id = " . key($mods);
        $db->sql_query($request);
        $i++;
        next($mods);
    }

    $request = "select title from " . TABLE_MOD . " where id = '{$pub_mod_id}'";
    $result = $db->sql_query($request);
    list($title) = $db->sql_fetch_row($result);
    log_("mod_order", $title);
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
    global $db;
    global $pub_action;


    /** @var TYPE_NAME $request */
    $request = "select `version` from " . TABLE_MOD . " where root = '{$pub_action}'";
    $result = $db->sql_query($request);
    if ($result) {
        list($version) = $db->sql_fetch_row($result);
        return $version;
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
 * @todo Query : 'REPLACE INTO ' . TABLE_MOD_CFG . ' VALUES ("' . $nom_mod . '", "' . $param . '", "' . $value . '")'
 * @api
 */
function mod_set_option($param, $value, $nom_mod = '')
{
    global $db;

    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    $query = 'REPLACE INTO ' . TABLE_MOD_CFG . ' VALUES ("' . $nom_mod . '", "' . $param . '", "' . $value . '")';
    if (!$db->sql_query($query)) {
        return false;
    }
    return true;
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
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    $query = 'DELETE FROM ' . TABLE_MOD_CFG . ' WHERE `mod` = "' . $nom_mod . '" AND `config` = "' . $param . '"';
    if (!$db->sql_query($query)) {
        return false;
    }
    return true;
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
    global $db;

    $nom_mod = mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    $query = 'SELECT value FROM ' . TABLE_MOD_CFG . ' WHERE `mod` = "' . $nom_mod . '" AND `config` = "' . $param . '"';
    $result = $db->sql_query($query);
    if (!list ($value) = $db->sql_fetch_row($result)) {
        return '-1';
    }
    return $value;
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
        $query = 'SELECT `action` FROM ' . TABLE_MOD . ' WHERE id=' . $pub_mod_id;
        $result = $db->sql_query($query);
        list ($nom_mod) = $db->sql_fetch_row($result);
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
    $query = 'DELETE FROM ' . TABLE_MOD_CFG . ' WHERE `mod` = "' . $nom_mod . '"';
    if (!$db->sql_query($query)) {
        return false;
    }
    return true;
}

//\\ fonctions utilisable pour les mods //\\
/**
 * Funtion to install a new mod in OGSpy
 * @param string $mod_folder : Folder name which contains the mod
 * @todo Query: "SELECT title FROM " . TABLE_MOD . " WHERE title='" . $value_mod[0] ."'"."'"
 * @todo Query: "INSERT INTO " . TABLE_MOD .
 * " (title, menu, action, root, link, version, active,admin_only) VALUES ('" . $value_mod[0] .
 * "','" . $value_mod[1] . "','" . $value_mod[2] . "','" . $value_mod[3] . "','" .
 * $value_mod[4] . "','" . $mod_version . "','" . $value_mod[5] . "','" . $value_mod[6] .
 * "')"
 * @return null|boolean true if the mod has been correctly installed
 * @api
 */
function install_mod($mod_folder)
{
    global $db, $server_config;
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
    if (isset($file[3]))
    {
        $mod_required_ogspy = trim($file[3]);
        if (isset($mod_required_ogspy)) {
            if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
                log_("mod_erreur_txt_version", $mod_folder);
                redirection("index.php?action=message&id_message=errormod&info");
                exit();
            }
        }
    }else{
        log_("mod_erreur_txt_warning", $mod_folder);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);

    // On vérifie si le mod est déjà installé""
    $check = "SELECT title FROM " . TABLE_MOD . " WHERE title='" . $value_mod[0] . "'";
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
    $db->sql_query("DELETE FROM " . TABLE_MOD . " WHERE title='" . $mod_uninstall_name . "';");
    if (!empty($mod_uninstall_table)) {
        log_("debug", "DROP TABLE IF EXISTS " . $mod_uninstall_table);
        $db->sql_query("DROP TABLE IF EXISTS " . $mod_uninstall_table);
    }
}

/**
 * Fonction to update the OGSpy mod
 * @param string $mod_folder : Folder name which contains the mod
 * @param string $mod_name : Mod name
 * @todo Query: "UPDATE " . TABLE_MOD . " SET version='" . $mod_version ."' WHERE action='" . $mod_name . "'";
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


    $query = "UPDATE " . TABLE_MOD . " SET version='" . $mod_version .
        "' WHERE action='" . $mod_name . "'";
    $db->sql_query($query);
    $is_oki = true;
    return $is_oki;
}