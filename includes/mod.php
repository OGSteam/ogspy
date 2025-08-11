<?php

/**
 * Functions used for OGSpy Mods
 * @package OGSpy
 * @subpackage mods
 * @author Kyser
 * @created 21/07/2006
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
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
 */
function mod_list()
{
    global $user_data, $log;

    $log->debug("Starting mod_list function", ['user_id' => $user_data['user_id'] ?? 'unknown']);

    if ($user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
        $log->warning("Unauthorized access attempt to mod_list", ['user_id' => $user_data['user_id'] ?? 'unknown']);
        redirection("index.php?action=message&id_message=forbidden&info");
    }

    $log->info("Admin user accessing mod list", ['user_id' => $user_data['user_id'] ?? 'unknown']);

    //Listing des mod présents dans le répertoire "mod"
    $path = opendir("mod/");
    if (!$path) {
        $log->error("Failed to open mod directory", ['directory' => 'mod/']);
        return array("disabled" => array(), "actived" => array(), "wrong" => array(), "unknown" => array(), "install" => array());
    }

    $log->debug("Successfully opened mod directory");

    //Récupération de la liste des répertoires correspondant
    $directories = array();
    while ($file = readdir($path)) {
        if ($file != "." && $file != "..") {
            if (is_dir("mod/" . $file)) {
                $directories[$file] = array();
                $log->debug("Found mod directory", ['directory' => $file]);
            }
        }
    }
    closedir($path);

    $log->info("Found mod directories", ['count' => count($directories), 'directories' => array_keys($directories)]);

    foreach (array_keys($directories) as $d) {
        $path = opendir("mod/" . $d);
        if (!$path) {
            $log->warning("Failed to open mod subdirectory", ['directory' => "mod/$d"]);
            continue;
        }

        while ($file = readdir($path)) {
            if ($file != "." && $file != "..") {
                $directories[$d][] = $file;
            }
        }
        closedir($path);
        if (sizeof($directories[$d]) == 0) {
            $log->debug("Removing empty mod directory", ['directory' => $d]);
            unset($directories[$d]);
        }
    }

    $mod_list = array("disabled" => array(), "actived" => array(), "wrong" => array(), "unknown" => array(), "install" => array());

    //récuérration des mods
    $Mod_Model = new Mod_Model();
    try {
        $tMods = $Mod_Model->find_by(null, array('position' => 'ASC', 'title' => 'ASC'));
        $log->debug("Retrieved mods from database", ['count' => count($tMods)]);
    } catch (Exception $e) {
        $log->error("Failed to retrieve mods from database", ['error' => $e->getMessage()]);
        return $mod_list;
    }

    foreach ($tMods as $mod) {
        $id = $mod['id'];
        $title = $mod['title'];
        $root = $mod['root'];
        $link = $mod['link'];
        $version = $mod['version'];
        $active = $mod['active'];
        $admin_only = $mod['admin_only'];
        $position = $mod['position'];

        $log->debug("Processing mod", ['title' => $title, 'root' => $root, 'active' => $active]);

        if (isset($directories[$root])) { //Mod présent du répertoire "mod"
            if (in_array($link, $directories[$root]) && in_array("version.txt", $directories[$root])) {
                //Vérification disponibilité mise à jour de version
                $version_file = "mod/" . $root . "/version.txt";
                if (!file_exists($version_file)) {
                    $log->warning("Version file missing for mod", ['mod' => $title, 'file' => $version_file]);
                } else {
                    $line = file($version_file);
                    $up_to_date = true;
                    if (isset($line[1])) {
                        $current_mod_version = trim($line[1]);
                        if (file_exists("mod/" . $root . "/update.php")) {
                            $up_to_date = version_compare($current_mod_version, $version, '<=');
                            if (!$up_to_date) {
                                $log->info("Update available for mod", ['mod' => $title, 'current_version' => $current_mod_version, 'installed_version' => $version]);
                            }
                        }
                    }
                }

                if ($active == 0) { // Mod désactivé
                    $mod_list["disabled"][] = array("id" => $id, "title" => $title, "version" => $version, "up_to_date" => $up_to_date);
                    $log->debug("Added disabled mod to list", ['mod' => $title]);
                } else { //Mod activé
                    $mod_list["actived"][] = array("id" => $id, "title" => $title, "version" => $version, "up_to_date" => $up_to_date, "admin_only" => $admin_only, 'position' => $position);
                    $log->debug("Added active mod to list", ['mod' => $title, 'admin_only' => $admin_only]);
                }
            } else { //Mod invalide
                $mod_list["wrong"][] = array("id" => $id, "title" => $title);
                $log->warning("Invalid mod configuration", ['mod' => $title, 'root' => $root, 'link_exists' => in_array($link, $directories[$root]), 'version_exists' => in_array("version.txt", $directories[$root])]);
            }

            unset($directories[$root]);
        } else { //Mod absent du répertoire "mod"
            $mod_list["wrong"][] = array("id" => $id, "title" => $title);
            $log->warning("Mod directory missing", ['mod' => $title, 'expected_root' => $root]);
        }
    }

    while ($files = @current($directories)) {
        $directory = key($directories);
        if (in_array("version.txt", $files) && in_array("install.php", $files)) {
            $version_file = "mod/" . $directory . "/version.txt";
            if (!file_exists($version_file)) {
                $log->warning("Version file missing for installable mod", ['directory' => $directory]);
            } else {
                $line = file($version_file);
                if (isset($line[0])) {
                    $mod_list["install"][] = array("title" => $line[0], "directory" => $directory);
                    $log->debug("Found installable mod", ['title' => trim($line[0]), 'directory' => $directory]);
                }
            }
        } else {
            $log->debug("Directory missing required files for installation", ['directory' => $directory, 'has_version' => in_array("version.txt", $files), 'has_install' => in_array("install.php", $files)]);
        }
        next($directories);
    }

    $log->info("Mod list generated successfully", [
        'active_count' => count($mod_list["actived"]),
        'disabled_count' => count($mod_list["disabled"]),
        'wrong_count' => count($mod_list["wrong"]),
        'install_count' => count($mod_list["install"])
    ]);

    return $mod_list;
}

/**
 * Function mod_check : Checks if an unauthorized user tries to install a mod without being admin or with wrong parameters
 * @param string $check type of varaible to be checked
 */
function mod_check($check)
{
    global $user_data, $log;
    global $pub_mod_id, $pub_directory;

    $log->debug("Starting mod_check function", ['check_type' => $check, 'user_id' => $user_data['user_id'] ?? 'unknown']);

    if ($user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
        $log->critical("Unauthorized mod operation attempt", [
            'check_type' => $check,
            'user_id' => $user_data['user_id'] ?? 'unknown',
            'admin_level' => $user_data["admin"] ?? 'undefined',
            'coadmin_level' => $user_data["coadmin"] ?? 'undefined'
        ]);
        redirection("index.php?action=message&id_message=forbidden&info");
    }

    $log->debug("User authorization verified", ['check_type' => $check, 'user_id' => $user_data['user_id'] ?? 'unknown']);

    switch ($check) {
        case "mod_id":
            $log->debug("Validating mod_id parameter", ['mod_id' => $pub_mod_id ?? 'not_set']);

            if (!check_var($pub_mod_id, "Num")) {
                $log->error("Invalid mod_id format", ['mod_id' => $pub_mod_id ?? 'null', 'expected' => 'numeric']);
                redirection("index.php?action=message&id_message=errordata&info");
            }
            if (!isset($pub_mod_id)) {
                $log->error("mod_id parameter is missing", ['check_type' => $check]);
                redirection("index.php?action=message&id_message=errorfatal&info");
            }

            $log->info("mod_id validation successful", ['mod_id' => $pub_mod_id]);
            break;

        case "directory":
            $log->debug("Validating directory parameter", ['directory' => $pub_directory ?? 'not_set']);

            if (!check_var($pub_directory, "Text")) {
                $log->error("Invalid directory format", ['directory' => $pub_directory ?? 'null', 'expected' => 'text']);
                redirection("index.php?action=message&id_message=errordata&info");
            }
            if (!isset($pub_directory)) {
                $log->error("directory parameter is missing", ['check_type' => $check]);
                redirection("index.php?action=message&id_message=errorfatal&info");
            }

            $log->info("directory validation successful", ['directory' => $pub_directory]);
            break;

        default:
            $log->warning("Unknown check type requested", ['check_type' => $check]);
            break;
    }

    $log->debug("mod_check function completed successfully", ['check_type' => $check]);
}

/**
 * Installs a Mod from a mod folder name (Fonction utilisée par la partie admin)
 * @global $pub_directory
 */
function mod_install()
{
    global $pub_directory, $server_config, $log;

    $log->info("Starting mod installation process", ['directory' => $pub_directory ?? 'undefined']);

    $Mod_Model = new Mod_Model();

    mod_check("directory");

    $log->debug("Authorization check passed for mod installation", ['directory' => $pub_directory]);

    // modif pour 3.0.7
    // check d un mod " normalisé"
    // voir @ shad

    // fichier install non present
    if (!file_exists("mod/" . $pub_directory . "/install.php")) {
        $log->error("Install.php file missing", ['directory' => $pub_directory, 'expected_file' => "mod/$pub_directory/install.php"]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //fichier . txt non present
    if (!file_exists("mod/" . $pub_directory . "/version.txt")) {
        $log->error("version.txt file missing", ['directory' => $pub_directory, 'expected_file' => "mod/$pub_directory/version.txt"]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $log->debug("Required files found", ['directory' => $pub_directory]);

    //verification  presence de majuscule
    if (!ctype_lower($pub_directory)) {
        $log->error("Directory name contains uppercase letters", ['directory' => $pub_directory, 'requirement' => 'lowercase_only']);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $log->debug("Directory name validation passed", ['directory' => $pub_directory]);

    // verification sur le fichier .txt
    $filename = 'mod/' . $pub_directory . '/version.txt';
    // On récupère les données du fichier version.txt
    try {
        $file = file($filename);
        if (!$file) {
            throw new Exception("Failed to read version.txt file");
        }
        $log->debug("Version file read successfully", ['file' => $filename, 'lines_count' => count($file)]);
    } catch (Exception $e) {
        $log->error("Failed to read version.txt", ['file' => $filename, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // $mod_version = trim($file[1]);
    if (!isset($file[2])) {
        $log->error("Invalid version.txt format - missing configuration line", ['file' => $filename, 'expected_line' => 2]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $mod_config = trim($file[2]);
    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);

    $log->debug("Parsed mod configuration", ['config' => $mod_config, 'parsed_values' => $value_mod]);

    // On vérifie si le mod est déjà installé""
    try {
        if ($Mod_Model->isExistByTitle($value_mod[0])) {
            $log->warning("Mod installation failed - already installed", ['mod_name' => $value_mod[0], 'directory' => $pub_directory]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    } catch (Exception $e) {
        $log->error("Database error while checking existing mod", ['mod_name' => $value_mod[0], 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    if (count($value_mod) != 7) {
        $log->error("Invalid version.txt configuration format", ['expected_values' => 7, 'found_values' => count($value_mod), 'config' => $mod_config]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    if (!isset($file[3])) {
        $log->error("Missing OGSpy version requirement in version.txt", ['file' => $filename, 'expected_line' => 3]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $mod_required_ogspy = trim($file[3]);
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            $log->error("OGSpy version requirement not met", [
                'required_version' => $mod_required_ogspy,
                'current_version' => $server_config["version"],
                'mod_name' => $value_mod[0]
            ]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    $log->info("All pre-installation checks passed", [
        'mod_name' => $value_mod[0],
        'directory' => $pub_directory,
        'required_ogspy_version' => $mod_required_ogspy
    ]);

    // si on arrive jusque la on peut installer
    global $db; // fix pour mod ne faisant pas l'inclusion mais l'utilisant (xtense ... )

    try {
        $log->debug("Executing mod installation script", ['script' => "mod/$pub_directory/install.php"]);
        require_once("mod/" . $pub_directory . "/install.php");
        $log->debug("Installation script executed successfully");
    } catch (Exception $e) {
        $log->error("Error during mod installation script execution", [
            'script' => "mod/$pub_directory/install.php",
            'error' => $e->getMessage()
        ]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //recuperation du mod
    try {
        $mod_id = $Mod_Model->get_mod_id_by_root($pub_directory);
        $log->debug("Retrieved mod ID from database", ['mod_id' => $mod_id, 'directory' => $pub_directory]);
    } catch (Exception $e) {
        $log->error("Failed to retrieve mod ID after installation", ['directory' => $pub_directory, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //récuperation de l'emplacement possible
    try {
        $position = $Mod_Model->get_position_max();
        $new_position = (int)($position + 1);

        ///update emplacement
        $Mod_Model->update_posisiton($mod_id, $new_position);

        $log->debug("Updated mod position", ['mod_id' => $mod_id, 'position' => $new_position]);
    } catch (Exception $e) {
        $log->error("Failed to set mod position", ['mod_id' => $mod_id, 'error' => $e->getMessage()]);
    }

    //récuperation du titre en base
    try {
        $mod = $Mod_Model->find_by(array("id" => $mod_id));

        if (count($mod) != 0) {
            $log->info("Mod installation completed successfully", [
                'mod_title' => $mod[0]['title'],
                'mod_id' => $mod_id,
                'directory' => $pub_directory,
                'position' => $new_position ?? 'unknown'
            ]);
        } else {
            $log->error("Installation completed but mod not found in database", [
                'mod_id' => $mod_id,
                'directory' => $pub_directory
            ]);
        }
    } catch (Exception $e) {
        $log->error("Error retrieving installed mod information", ['mod_id' => $mod_id, 'error' => $e->getMessage()]);
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache", ['error' => $e->getMessage()]);
    }

    redirection("index.php?action=administration&subaction=mod");
}

/**
 * mod_update (Fonction utilisée par la partie admin): Updates a mod version
 */
function mod_update()
{
    global $pub_mod_id, $server_config, $log;

    $log->info("Starting mod update process", ['mod_id' => $pub_mod_id ?? 'undefined']);

    $Mod_Model = new Mod_Model();

    mod_check("mod_id");

    $log->debug("Authorization check passed for mod update", ['mod_id' => $pub_mod_id]);

    //recuperation du mod
    //récuperation du titre en base
    try {
        $mod = $Mod_Model->find_one_by(array("id" => $pub_mod_id));
        if (!$mod) {
            $log->error("Mod not found in database", ['mod_id' => $pub_mod_id]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
        $log->debug("Mod retrieved from database", ['mod_id' => $pub_mod_id, 'mod_title' => $mod['title'], 'mod_root' => $mod['root']]);
    } catch (Exception $e) {
        $log->error("Database error while retrieving mod", ['mod_id' => $pub_mod_id, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // fichier mod_erreur_update non present
    if (!file_exists("mod/" . $mod['root'] . "/update.php")) {
        $log->error("Update script missing", ['mod_title' => $mod['title'], 'mod_root' => $mod['root'], 'expected_file' => "mod/{$mod['root']}/update.php"]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //fichier . txt non present
    if (!file_exists("mod/" . $mod['root'] . "/version.txt")) {
        $log->error("Version file missing for mod update", ['mod_title' => $mod['title'], 'mod_root' => $mod['root'], 'expected_file' => "mod/{$mod['root']}/version.txt"]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $log->debug("Required files found for mod update", ['mod_title' => $mod['title'], 'mod_root' => $mod['root']]);

    //verification  presence de majuscule
    if (!ctype_lower($mod['root'])) {
        $log->error("Mod root directory contains uppercase letters", ['mod_title' => $mod['title'], 'mod_root' => $mod['root'], 'requirement' => 'lowercase_only']);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // verification sur le fichier .txt
    $filename = 'mod/' . $mod['root'] . '/version.txt';
    // On récupère les données du fichier version.txt
    try {
        $file = file($filename);
        if (!$file) {
            throw new Exception("Failed to read version.txt file");
        }
        $log->debug("Version file read successfully for update", ['file' => $filename, 'lines_count' => count($file)]);
    } catch (Exception $e) {
        $log->error("Failed to read version.txt for mod update", ['file' => $filename, 'mod_title' => $mod['title'], 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // $mod_version = trim($file[1]);   //TODO:Unused_code
    if (!isset($file[2])) {
        $log->error("Invalid version.txt format - missing configuration line for update", ['file' => $filename, 'mod_title' => $mod['title'], 'expected_line' => 2]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $mod_config = trim($file[2]);
    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);

    $log->debug("Parsed mod configuration for update", ['mod_title' => $mod['title'], 'config' => $mod_config, 'parsed_values' => $value_mod]);

    if (count($value_mod) != 7) {
        $log->error("Invalid version.txt configuration format for update", ['mod_title' => $mod['title'], 'expected_values' => 7, 'found_values' => count($value_mod), 'config' => $mod_config]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    if (!isset($file[3])) {
        $log->error("Missing OGSpy version requirement in version.txt for update", ['file' => $filename, 'mod_title' => $mod['title'], 'expected_line' => 3]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $mod_required_ogspy = trim($file[3]);
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            $log->error("OGSpy version requirement not met for mod update", [
                'mod_title' => $mod['title'],
                'required_version' => $mod_required_ogspy,
                'current_version' => $server_config["version"]
            ]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    $log->info("All pre-update checks passed", [
        'mod_title' => $mod['title'],
        'mod_root' => $mod['root'],
        'required_ogspy_version' => $mod_required_ogspy
    ]);

    if (file_exists("mod/" . $mod['root'] . "/update.php")) {
        // si on arrive jusque la on peut installer
        global $db; // fix pour mod ne faisant pas l'inclusion mais l'utilisant (xtense ... )

        try {
            $log->debug("Executing mod update script", ['mod_title' => $mod['title'], 'script' => "mod/{$mod['root']}/update.php"]);
            require_once("mod/" . $mod['root'] . "/update.php");
            $log->debug("Update script executed successfully", ['mod_title' => $mod['title']]);
        } catch (Exception $e) {
            $log->error("Error during mod update script execution", [
                'mod_title' => $mod['title'],
                'script' => "mod/{$mod['root']}/update.php",
                'error' => $e->getMessage()
            ]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }

        $log->info("Mod update completed successfully", ['mod_title' => $mod['title'], 'mod_id' => $pub_mod_id]);
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully after update");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache after update", ['mod_title' => $mod['title'], 'error' => $e->getMessage()]);
    }

    redirection("index.php?action=administration&subaction=mod");
}

/**
 * mod_uninstall (Fonction utilisée par la partie admin): Uninstall a mod from the database (Mod files are not deleted)
 *
 */
function mod_uninstall()
{
    global $pub_mod_id, $log;

    $log->info("Starting mod uninstallation process", ['mod_id' => $pub_mod_id ?? 'undefined']);

    $Mod_Model = new Mod_Model();
    mod_check("mod_id");

    $log->debug("Authorization check passed for mod uninstallation", ['mod_id' => $pub_mod_id]);

    // selection du mod
    try {
        $mod = $Mod_Model->find_one_by(array("id" => $pub_mod_id));
        if (!$mod) {
            $log->error("Mod not found for uninstallation", ['mod_id' => $pub_mod_id]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
        $log->debug("Mod retrieved for uninstallation", ['mod_id' => $pub_mod_id, 'mod_title' => $mod['title'], 'mod_root' => $mod['root']]);
    } catch (Exception $e) {
        $log->error("Database error while retrieving mod for uninstallation", ['mod_id' => $pub_mod_id, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $root = $mod["root"];
    $title = $mod["title"];

    if (file_exists("mod/" . $root . "/uninstall.php")) {
        try {
            $log->debug("Executing mod uninstall script", ['mod_title' => $title, 'script' => "mod/$root/uninstall.php"]);
            // si on arrive jusque la on peut installer
            global $db; // fix pour mod ne faisant pas l'inclusion mais l'utilisant (xtense ... )
            require_once("mod/" . $root . "/uninstall.php");
            $log->debug("Uninstall script executed successfully", ['mod_title' => $title]);
        } catch (Exception $e) {
            $log->error("Error during mod uninstall script execution", [
                'mod_title' => $title,
                'script' => "mod/$root/uninstall.php",
                'error' => $e->getMessage()
            ]);
            // Continue with database deletion even if script fails
        }
    } else {
        $log->debug("No uninstall script found", ['mod_title' => $title, 'expected_script' => "mod/$root/uninstall.php"]);
    }

    try {
        $Mod_Model->delete($pub_mod_id);
        $log->info("Mod uninstalled successfully", ['mod_title' => $title, 'mod_id' => $pub_mod_id]);
    } catch (Exception $e) {
        $log->error("Failed to delete mod from database", ['mod_title' => $title, 'mod_id' => $pub_mod_id, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully after uninstallation");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache after uninstallation", ['mod_title' => $title, 'error' => $e->getMessage()]);
    }

    redirection("index.php?action=administration&subaction=mod");
}


/**
 * Mod Activation
 */
function mod_active()
{
    global $pub_mod_id, $log;

    $log->info("Starting mod activation process", ['mod_id' => $pub_mod_id ?? 'undefined']);

    $Mod_Model = new Mod_Model();

    mod_check("mod_id");

    $log->debug("Authorization check passed for mod activation", ['mod_id' => $pub_mod_id]);

    try {
        $mod = $Mod_Model->find_one_by(array("id" => $pub_mod_id));
        if (!$mod) {
            $log->error("Mod not found for activation", ['mod_id' => $pub_mod_id]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }

        $log->debug("Mod retrieved for activation", ['mod_id' => $pub_mod_id, 'mod_title' => $mod['title'], 'current_status' => $mod['active']]);

        $mod['active'] = 1;
        $Mod_Model->update($mod);

        $log->info("Mod activated successfully", ['mod_title' => $mod['title'], 'mod_id' => $pub_mod_id]);
    } catch (Exception $e) {
        $log->error("Error during mod activation", ['mod_id' => $pub_mod_id, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully after activation");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache after activation", ['mod_title' => $mod['title'], 'error' => $e->getMessage()]);
    }

    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Disables a Mod
 */
function mod_disable()
{
    global $pub_mod_id, $log;

    $log->info("Starting mod deactivation process", ['mod_id' => $pub_mod_id ?? 'undefined']);

    mod_check("mod_id");

    $log->debug("Authorization check passed for mod deactivation", ['mod_id' => $pub_mod_id]);

    try {
        $Mod_Model = new Mod_Model();
        $mod = $Mod_Model->find_one_by(array("id" => $pub_mod_id));
        if (!$mod) {
            $log->error("Mod not found for deactivation", ['mod_id' => $pub_mod_id]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }

        $log->debug("Mod retrieved for deactivation", ['mod_id' => $pub_mod_id, 'mod_title' => $mod['title'], 'current_status' => $mod['active']]);

        $mod['active'] = 0;
        $Mod_Model->update($mod);

        $log->info("Mod deactivated successfully", ['mod_title' => $mod['title'], 'mod_id' => $pub_mod_id]);
    } catch (Exception $e) {
        $log->error("Error during mod deactivation", ['mod_id' => $pub_mod_id, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully after deactivation");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache after deactivation", ['mod_title' => $mod['title'], 'error' => $e->getMessage()]);
    }

    redirection("index.php?action=administration&subaction=mod");
}

// Modifs par naruto kun

/**
 * Set the visibility of the mod (Admin)
 */
function mod_admin()
{
    global $pub_mod_id, $log;

    $log->info("Starting mod admin restriction process", ['mod_id' => $pub_mod_id ?? 'undefined']);

    mod_check("mod_id");

    $log->debug("Authorization check passed for mod admin restriction", ['mod_id' => $pub_mod_id]);

    try {
        $Mod_Model = new Mod_Model();
        $mod = $Mod_Model->find_one_by(array("id" => $pub_mod_id));
        if (!$mod) {
            $log->error("Mod not found for admin restriction", ['mod_id' => $pub_mod_id]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }

        $log->debug("Mod retrieved for admin restriction", ['mod_id' => $pub_mod_id, 'mod_title' => $mod['title'], 'current_admin_only' => $mod['admin_only']]);

        $mod['admin_only'] = 1;
        $Mod_Model->update($mod);

        $log->info("Mod restricted to admin access successfully", ['mod_title' => $mod['title'], 'mod_id' => $pub_mod_id]);
    } catch (Exception $e) {
        $log->error("Error during mod admin restriction", ['mod_id' => $pub_mod_id, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully after admin restriction");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache after admin restriction", ['mod_title' => $mod['title'], 'error' => $e->getMessage()]);
    }

    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Set the visibility of the mod (User)
 */
function mod_normal()
{
    global $pub_mod_id, $log;

    $log->info("Starting mod normal access process", ['mod_id' => $pub_mod_id ?? 'undefined']);

    mod_check("mod_id");

    $log->debug("Authorization check passed for mod normal access", ['mod_id' => $pub_mod_id]);

    try {
        $Mod_Model = new Mod_Model();
        $mod = $Mod_Model->find_one_by(array("id" => $pub_mod_id));
        if (!$mod) {
            $log->error("Mod not found for normal access", ['mod_id' => $pub_mod_id]);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }

        $log->debug("Mod retrieved for normal access", ['mod_id' => $pub_mod_id, 'mod_title' => $mod['title'], 'current_admin_only' => $mod['admin_only']]);

        $mod['admin_only'] = 0;
        $Mod_Model->update($mod);

        $log->info("Mod set to normal user access successfully", ['mod_title' => $mod['title'], 'mod_id' => $pub_mod_id]);
    } catch (Exception $e) {
        $log->error("Error during mod normal access setting", ['mod_id' => $pub_mod_id, 'error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully after normal access setting");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache after normal access setting", ['mod_title' => $mod['title'], 'error' => $e->getMessage()]);
    }

    redirection("index.php?action=administration&subaction=mod");
}

/**
 * Function to set the position of a mod into the mod list
 * @param string $order up or down according to the new desired postion.
 */
function mod_sort($order)
{
    global $pub_mod_id, $log;

    $log->info("Starting mod sort process", ['mod_id' => $pub_mod_id ?? 'undefined', 'order' => $order]);

    $changed = false;
    $change_msg = '-Pas de changement-';

    mod_check("mod_id");

    $log->debug("Authorization check passed for mod sort", ['mod_id' => $pub_mod_id, 'order' => $order]);

    //récupérration des mods
    $Mod_Model = new Mod_Model();

    try {
        $tMod = $Mod_Model->find_by(null, array('position' => 'ASC', 'title' => 'ASC'));
        $log->debug("Retrieved mods for sorting", ['count' => count($tMod)]);
    } catch (Exception $e) {
        $log->error("Failed to retrieve mods for sorting", ['error' => $e->getMessage()]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $oldModOrder  = array();
    $oldModPosition = 0;
    $targetMod = null;

    foreach ($tMod as $mod) {
        $oldModOrder[$mod["position"]] = $mod;
        if ($pub_mod_id == $mod["id"]) {
            $oldModPosition = $mod["position"];
            $targetMod = $mod;
        }
    }

    if (!$targetMod) {
        $log->error("Target mod not found for sorting", ['mod_id' => $pub_mod_id]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    $log->debug("Current mod position", ['mod_title' => $targetMod['title'], 'current_position' => $oldModPosition, 'order_direction' => $order]);

    //changement de position
    $myMod = $oldModOrder[$oldModPosition];

    try {
        switch ($order) {
            case "down":
                //si on veut monter la position (donc descendre en visu)
                if (isset($oldModOrder[$oldModPosition + 1])) {
                    $Mod_Model->update_posisiton($myMod['id'], $oldModPosition + 1); //mod courant
                    $modToMove = $oldModOrder[$oldModPosition + 1]; //mod à bouger
                    $Mod_Model->update_posisiton($modToMove['id'], $oldModPosition);
                    $changed = true;
                    $log->info("Mod moved down successfully", [
                        'mod_title' => $myMod['title'],
                        'old_position' => $oldModPosition,
                        'new_position' => $oldModPosition + 1,
                        'swapped_with' => $modToMove['title']
                    ]);
                } else {
                    $log->debug("Cannot move mod down - already at bottom", ['mod_title' => $myMod['title'], 'position' => $oldModPosition]);
                }
                break;

            case "up":
                //si on veut descendre la position (donc monter en visu)
                if (isset($oldModOrder[$oldModPosition - 1])) {
                    $Mod_Model->update_posisiton($myMod['id'], $oldModPosition - 1); //mod courant
                    $modToMove = $oldModOrder[$oldModPosition - 1]; //mod à bouger
                    $Mod_Model->update_posisiton($modToMove['id'], $oldModPosition);
                    $changed = true;
                    $log->info("Mod moved up successfully", [
                        'mod_title' => $myMod['title'],
                        'old_position' => $oldModPosition,
                        'new_position' => $oldModPosition - 1,
                        'swapped_with' => $modToMove['title']
                    ]);
                } else {
                    $log->debug("Cannot move mod up - already at top", ['mod_title' => $myMod['title'], 'position' => $oldModPosition]);
                }
                break;

            default:
                $log->warning("Invalid sort order specified", ['order' => $order, 'mod_title' => $myMod['title']]);
                break;
        }
    } catch (Exception $e) {
        $log->error("Error during mod position update", [
            'mod_title' => $myMod['title'],
            'order' => $order,
            'error' => $e->getMessage()
        ]);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    if ($changed === true) {
        $change_msg = $myMod["title"];
        $log->info("Mod sort operation completed successfully", ['mod_title' => $myMod["title"], 'order' => $order]);
    } else {
        $log->info("Mod sort operation completed - no change needed", ['mod_title' => $myMod["title"], 'order' => $order]);
    }

    try {
        generate_mod_cache();
        $log->debug("Mod cache regenerated successfully after sort");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate mod cache after sort", ['mod_title' => $myMod["title"], 'error' => $e->getMessage()]);
    }

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
    $mod = $Mod_Model->find_one_by(array("root" => $pub_action));
    if (!is_null($mod)) {
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
function mod_set_option($param, $value, $modName = null)
{
    $modName = $modName ?? mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    return (new Mod_Config_Model)->set_mod_config($modName, $param, $value);
}

/**
 * Mod Configs: Deletes a parameter for a mod
 * @param string $param Name of the parameter
 * @global $db
 * @return boolean returns true if the parameter is correctly saved. false in other cases.
 * @api
 */
function mod_del_option($param, $modName = null)
{
    $modName = $modName ?? mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    return (new Mod_Config_Model)->delete_mod_config($modName, $param);
}

/**
 * Mod Configs : Reads a parameter value for the current mod
 * @param string $param Name of the parameter
 * @global $db
 * @return string Returns the value of the requested parameter
 * @api
 */
function mod_get_option($param, $modName = null)
{
    $modName = $modName ?? mod_get_nom();
    if (!check_var($param, "Text")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    return (new Mod_Config_Model)->get_mod_config($modName, $param);
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
    global $pub_action, $pub_mod_id;

    $modName = '';
    if ($pub_action == 'mod_install') {
        global $pub_directory;
        $modName = $pub_directory;
    } elseif ($pub_action == 'mod_update' || $pub_action == 'mod_uninstall') {
        $Mod_Model = (new Mod_Model())->find_one_by(array("id" => $pub_mod_id));
        $modName = $Mod_Model["action"];
    } else {
        $modName = $pub_action;
    }
    return $modName;
}

/**
 * Deletes all configurations for the current mod
 * @global $db
 * @return boolean Returns true if at least one entry has been deleted. False if nothing has been removed.
 */
function mod_del_all_option($modName = null)
{
    $modName = $modName ?? mod_get_nom();
    return (new Mod_Config_Model)->delete_mod_config($modName);
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
    global  $server_config, $log;
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
                $log->info($mod_folder, ['type' => 'mod_erreur_txt_version']);
                redirection("index.php?action=message&id_message=errormod&info");
                exit();
            }
        }
    } else {
        $log->info($mod_folder, ['type' => 'mod_erreur_txt_warning']);
        redirection("index.php?action=message&id_message=errormod&info");
        exit();
    }

    // On explode la chaine d'information
    $value_mod = explode(',', $mod_config);

    // On vérifie si le mod est déjà installé""
    $Mod_Model = new Mod_Model();
    $mod = $Mod_Model->find_one_by(array("title" => $value_mod[0]));

    if (!isset($mod['title'])) {
        if (count($value_mod) == 7) {
            $newMod = array();
            $newMod['title'] =  $value_mod[0];
            $newMod['menu'] = $value_mod[1];
            $newMod['action'] = $value_mod[2];
            $newMod['root'] = $value_mod[3];
            $newMod['link'] = $value_mod[4];
            $newMod['version'] = $mod_version;
            $newMod['active'] = $value_mod[5];
            $newMod['admin_only'] = $value_mod[6];
            $newMod['position'] = 1;
            $Mod_Model->add($newMod);

            $is_ok = true; /// tout c 'est bien passe'
        }
    }
    return $is_ok;
}

/**
 * Function to uninstall an OGSpy Module
 * @param string $mod_uninstall_name : Mod name
 * @param string|array $mod_uninstall_table : Name or list of Name of the Database table used by the Mod that we need to remove
 * @api
 */
function uninstall_mod($mod_uninstall_name, $mod_uninstall_table = null)
{
    global $log;
    $Mod_Model = new Mod_Model();

    $Mod_Model->delete_by_title($mod_uninstall_name);

    if ($mod_uninstall_table != null) {
        //todo MOD factory ?
        if(is_array($mod_uninstall_table))
        {
            $log->info( "DROP TABLE IF EXISTS " . implode(", ",$mod_uninstall_table), ['type' => 'debug']);
        }
        else
        {
            $log->info( "DROP TABLE IF EXISTS " . $mod_uninstall_table, ['type' => 'debug']);
        }
        $Mod_Model->drop_custum_table($mod_uninstall_table);
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
    global $server_config, $log;
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
            $log->info($mod_folder, ['type' => 'mod_erreur_txt_version']);
            redirection("index.php?action=message&id_message=errormod&info");
            exit();
        }
    }

    $Mod_Model = new Mod_Model(); //récuperation du mod
    $mod = $Mod_Model->find_one_by(array("title" => $mod_name));
    $mod['version'] = $mod_version;
    $Mod_Model->update($mod);

    $is_oki = true;
    return $is_oki;
}
