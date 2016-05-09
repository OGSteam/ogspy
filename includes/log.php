<?php
/**
 * OGSpy Log Functions
 * @package OGSpy
 * @subpackage Log
 * @author Kyser
 * @copyright Copyright &copy; 2012, http://www.ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.1.1 ($Rev: 7690 $)
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
global $ogspy_phperror;
$ogspy_phperror = Array();
/**
 * Function log_() to add a line to the Log File
 *
 * Log types can be : mod, set_serverconfig, set_server_view, set_db_size, mod_install, mod_update, mod_uninstall, mod_active, mod_disable, mod_order, mod_normal,
 * mod_admin, mod_erreur_install_php, mod_erreur_install_txt, mod_erreur_update, mod_erreur_minuscule, mod_erreur_install_bis, mod_erreur_txt_warning, load_system, load_system_OGS,
 * get_system_OGS, load_spy, load_spy_OGS, export_spy_sector, export_spy_date, mysql_error, login, login_OGS, logout, modify_account, modify_account_admin, create_account, regeneratepwd,
 * create_usergroup, delete_usergroup, modify_usergroup, add_usergroup, del_usergroup, load_rank, get_rank, erreur_config_cache, erreur_mod_cache, key, check_var, debug, php-error)
 * @param string $parameter Log type
 * @param mixed $option Optionnal data
 */
function log_($parameter, $option = 0)
{
    global $db, $user_data, $server_config, $pub_action;

    $member = "Inconnu";
    if (isset($user_data)) {
        $member = $user_data["user_name"];
    }

    switch ($parameter) {
        /* ----------- Entrée Journal générique de Mod ----------- */
        case 'mod':
            $line = "[$pub_action] " . $member . " ";
            if (is_array($option)) {
                $line .= print_r($option, true);
            } else {
                $line .= $option;
            }
            break;

        /* ----------- Administration ----------- */
        case 'set_serverconfig' :
            $line = "[admin] " . $member . " modifie les paramètres du serveur";
            break;

        case 'set_server_view' :
            $line = "[admin] " . $member . " modifie les paramètres d'affichage du serveur";
            break;

        case 'set_db_size' :
            $line = "[admin] " . $member . " modifie la taille de l'univers sa nouvelle taille est galaxy:" . $server_config['num_of_galaxies'] . " et system:" . $server_config['num_of_systems'];
            break;

        case 'mod_install' :
            $line = "[admin] " . $member . " installe le mod \"" . $option . "\"";
            break;

        case 'mod_update' :
            $line = "[admin] " . $member . " met à jour le mod \"" . $option . "\"";
            break;

        case 'mod_uninstall' :
            $line = "[admin] " . $member . " désinstalle le mod \"" . $option . "\"";
            break;

        case 'mod_active' :
            $line = "[admin] " . $member . " active le mod \"" . $option . "\"";
            break;

        case 'mod_disable' :
            $line = "[admin] " . $member . " désactive le mod \"" . $option . "\"";
            break;

        case 'mod_order' :
            $line = "[admin] " . $member . " repositionne le mod \"" . $option . "\"";
            break;

        case 'mod_normal' :
            $line = "[admin] " . $member . " affiche le mod aux utilisateurs \"" . $option . "\"";
            break;

        case 'mod_admin' :
            $line = "[admin] " . $member . " cache le mod aux utilisateurs \"" . $option . "\"";
            break;

        /* ----------- Erreur gestion mod ----------- */
        case 'mod_erreur_install_php' :
            $line = "[admin][mod_erreur] " . $member . " fichier mod/" . $option . "/install.php introuvable ";
            break;

        case 'mod_erreur_install_txt' :
            $line = "[admin][mod_erreur] " . $member . " fichier mod/" . $option . "/version.txt introuvable ";
            break;

        case 'mod_erreur_update' :
            $line = "[admin][mod_erreur] " . $member . " fichier mod/" . $option . "/update.php introuvable ";
            break;

        case 'mod_erreur_minuscule' :
            $line = "[admin][mod_erreur] " . $member . " dossier mod/" . $option . "/ n'est pas en minuscule ";
            break;

        case 'mod_erreur_install_bis' :
            $line = "[admin][mod_erreur] " . $member . "  mod " . $option . " déjà installé ";
            break;

        case 'mod_erreur_txt_warning' :
            $line = "[admin][mod_erreur] " . $member . "  mod/" . $option . "/version.txt mal formé ";
            break;

        case 'mod_erreur_txt_version' :
            $line = "[admin][mod_erreur] Le mod " . $option . " nécessite une version supérieure d'OGSpy";
            break;

        /* ----------- Gestion systèmes solaires et rapports ----------- */
        case 'load_system' :
            $line = $member . " charge le système solaire " . $option[0] . ":" . $option[1];
            break;

        case 'load_system_OGS' :
            $line = $member . " charge " . $option[0] . " planetes via OGS : " . $option[1] . " insertion(" . $option[1] . "), mise à jour(" . $option[2] . "), obsolète(" . $option[3] . "), échec(" . $option[4] . ") - " . $option[5] . " sec";
            break;

        case 'get_system_OGS' :
            if ($option != 0) $line = $member . " récupère les planètes de la galaxie " . $option;
            else $line = $member . " récupère toutes les planètes de l'univers";
            break;

        case 'load_spy' :
            $line = $member . " charge " . $option . " rapport(s) d'espionnage";
            break;

        case 'load_spy_OGS' :
            $line = $member . " charge " . $option . " rapport(s) d'espionnage via OGS";
            break;

        case 'export_spy_sector' :
            list($nb_spy, $galaxy, $system) = $option;
            $line = $member . " récupère " . $nb_spy . " rapport(s) d'espionnage du système [" . $galaxy . ":" . $system . "]";
            break;

        case 'export_spy_date' :
            list($nb_spy, $timestamp) = $option;
            $date = strftime("%d %b %Y %H:%M", $timestamp);
            $line = $member . " récupère " . $nb_spy . " rapport(s) d'espionnage postérieur au " . $date;
            break;

        /* ----------- Gestion des erreurs ----------- */
        case 'mysql_error' :
            $line = 'Erreur critique mysql - Req : ' . $option[0] . ' - Erreur n°' . $option[1] . ' ' . $option[2];
            $i = 0;
            foreach ($option[3] as $l) {
                $line .= "\n";
                $line .= "\t" . '[' . $i . ']' . "\n";
                $line .= "\t\t" . 'file => ' . $l['file'] . "\n";
                $line .= "\t\t" . 'ligne => ' . $l['line'] . "\n";
                $line .= "\t\t" . 'fonction => ' . $l['function'];
                $j = 0;
                if (isset($l['args'])) {
                    foreach ($l['args'] as $arg) {
                        $line .= "\n";
                        $line .= "\t\t\t" . '[' . $j . '] => ' . $arg;
                        $j++;

                    }
                }
                $i++;
            }
            break;

        /* ----------- Gestion des membres ----------- */
        case 'login' :
            $line = $member . " se connecte";
            break;

        case 'login_ogs' :
            $line = $member . " se connecte via OGS";
            break;

        case 'logout' :
            $line = $member . " se déconnecte";
            break;

        case 'modify_account' :
            $line = $member . " change son profil";
            break;

        case 'modify_account_admin' :
            $user_info = user_get($option);
            $line = "[admin] " . $member . " change le profil de " . $user_info[0]['user_name'];
            break;

        case 'create_account' :
            $user_info = user_get($option);
            $line = "[admin] " . $member . " créé le compte de " . $user_info[0]['user_name'];
            break;

        case 'regeneratepwd' :
            $user_info = user_get($option);
            $line = "[admin] " . $member . " génère un nouveau mot de passe pour " . $user_info[0]['user_name'];
            break;

        case 'delete_account' :
            $user_info = user_get($option);
            $line = "[admin] " . $member . " supprime le compte de " . $user_info[0]['user_name'];
            break;

        case 'create_usergroup' :
            $line = "[admin] " . $member . " créé le groupe " . $option;
            break;

        case 'modify_usergroup' :
            $usergroup_info = usergroup_get($option);
            $line = "[admin] " . $member . " modifie les paramètres du groupe " . $usergroup_info["group_name"];
            break;

        case 'delete_usergroup' :
            $usergroup_info = usergroup_get($option);
            $line = "[admin] " . $member . " supprime le groupe " . $usergroup_info["group_name"];
            break;

        case 'add_usergroup' :
            list($group_id, $user_id) = $option;
            $usergroup_info = usergroup_get($group_id);
            $user_info = user_get($user_id);
            $line = "[admin] " . $member . " ajoute " . $user_info[0]["user_name"] . " dans le groupe " . $usergroup_info["group_name"];;
            break;

        case 'del_usergroup' :
            list($group_id, $user_id) = $option;
            $usergroup_info = usergroup_get($group_id);
            $user_info = user_get($user_id);
            $line = "[admin] " . $member . " supprime " . $user_info[0]["user_name"] . " du groupe " . $usergroup_info["group_name"];;
            break;

        /* ----------- Classement ----------- */
        case 'load_rank' :
            list($support, $typerank, $typerank2, $timestamp, $countrank) = $option;
            switch ($support) {
                case "OGS":
                    $support = "OGS";
                    break;
                case "WEB":
                    $support = "serveur web";
                    break;
            }
            switch ($typerank) {
                case "general":
                    $typerank = "général";
                    break;
                case "fleet":
                    $typerank = "flotte";
                    break;
                case "research":
                    $typerank = "recherche";
                    break;
            }
            switch ($typerank2) {
                case "player":
                    $typerank2 = "joueur";
                    break;
                case "ally":
                    $typerank2 = "alliance";
                    break;
            }
            $date = strftime("%d %b %Y %Hh", $timestamp);
            $line = $member . " envoie le classement " . $typerank . " " . $typerank2 . " du " . $date . " via " . $support . " [" . $countrank . " lignes]";
            break;

        case 'get_rank' :
            list($typerank, $timestamp) = $option;
            $date = strftime("%d %b %Y %H:%M", $timestamp);
            switch ($typerank) {
                case "points":
                    $typerank = "général";
                    break;
                case "flotte":
                    $typerank = "flotte";
                    break;
                case "research":
                    $typerank = "recherche";
                    break;
            }
            $line = $member . " récupère le classement " . $typerank . " du " . $date;
            break;

        /* ----------- cache ----------- */
        case 'erreur_config_cache' :
            $line = $member . " Impossible d écrire sur le fichier donfig_cache. Vérifier les droits d acces au dossier  \'cache\' ";
            break;

        case 'erreur_mod_cache' :
            $line = $member . " Impossible d écrire sur le fichier mod_cache. Vérifier les droits d acces au dossier  \'cache\' ";
            break;

        /* ----------- cache ----------- */

        case 'key' :
            $line = $member . " Impossible de retrouver le fichier key.php. Vérifier les droits d acces au dossier  \'parameters\' ";
            break;

        /* ----------------------------------------- */

        case 'check_var' :
            $line = $member . " envoie des données refusées par le contrôleur : " . $option[0] . " - " . $option[1];
            break;

        case 'debug' :
            $line = 'DEBUG : ' . $option;
            break;
        case 'php_error' :
            $line = "[PHP-ERROR] " . $option[0] . " - " . $option[1];
            if (isset($option[2])) $line .= " ; Fichier: " . $option[2];
            if (isset($option[3])) $line .= " ; Ligne: " . $option[3];

            break;

        default:
            $line = 'Erreur appel fichier log - ' . $parameter . ' - ' . print_r($option);
            break;
    }

    $fichier = "log_" . date("ymd") . '.log';
    $line = "/*" . date("d/m/Y H:i:s") . '*/ ' . $line;
    write_file(PATH_LOG_TODAY . $fichier, "a", $line);
}

/**
 * Error handler PHP : Loging PHP errors
 * Works only if php errors are enabled in the server configuration $server_config["no_phperror"].
 * @param int $code Error code
 * @param string $message Error message
 * @param string $file Filename
 * @param int $line Error line
 */
function ogspy_error_handler($code, $message, $file, $line)
{
    global $ogspy_phperror;
    $option = Array($code, $message, $file, $line);
    log_("php_error", Array($code, $message, $file, $line));
    global $user_data;
    if ($user_data["user_admin"] == 1) {
        $line = "[PHP-ERROR] " . $option[0] . " - " . $option[1];
        if (isset($option[2])) $line .= " ; Fichier: " . $option[2];
        if (isset($option[3])) $line .= " ; Ligne: " . $option[3];
        if ($option[0] != 8) $ogspy_phperror[] = $line;
    }
}
