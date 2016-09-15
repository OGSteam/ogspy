<?php
/**
 * user.php Fonctions concernant les utilisateurs
 * @package OGSpy
 * @subpackage user
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b
 * @created 06/12/2005
 */

namespace Ogsteam\Ogspy;

use Ogsteam\Ogspy\Model\Group_Model;
use Ogsteam\Ogspy\Model\Sessions_Model;
use Ogsteam\Ogspy\Model\Spy_Model;
use Ogsteam\Ogspy\Model\User_Building_Model;
use Ogsteam\Ogspy\Model\User_Defense_Model;
use Ogsteam\Ogspy\Model\User_Favorites_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Ogsteam\Ogspy\Model\Statistics_Model;
use Ogsteam\Ogspy\Model\User_SpyFavorites_Model;
use Ogsteam\Ogspy\Model\User_Technology_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Verification des droits utilisateurs sur une action avec redirection le cas écheant
 * @param string $action Action verifie
 * @param int $user_id identificateur optionnel de l'utilisateur teste
 */
function user_check_auth($action, $user_id = null)
{
    global $user_data;

    switch ($action) {
        case "user_create":
        case "usergroup_manage":
            if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] !=
                1
            )
                redirection("index.php?action=message&id_message=forbidden&info");

            break;

        case "user_update":
            if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] !=
                1
            )
                redirection("index.php?action=message&id_message=forbidden&info");

            $info_user = user_get($user_id);
            if ($info_user === false)
                redirection("index.php?action=message&id_message=deleteuser_failed&info");

            if (($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] !=
                    1) || ($info_user[0]["user_admin"] == 1) || (($user_data["user_coadmin"] == 1) &&
                    ($info_user[0]["user_coadmin"] == 1)) || (($user_data["user_coadmin"] != 1 && $user_data["management_user"] ==
                        1) && ($info_user[0]["user_coadmin"] == 1 || $info_user[0]["management_user"] ==
                        1))
            ) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }

            break;


        default:
            redirection("index.php?action=message&id_message=errorfatal&info");
    }
}

/**
 * Login d'un utilisateur
 * @global string $pub_login
 * @global string $pub_password
 * @global string $pub_goto
 */
function user_login()
{
    global $db;
    global $pub_login, $pub_password, $pub_goto, $url_append;

    if (!check_var($pub_login, "Pseudo_Groupname") || !check_var($pub_password,
            "Password") || !check_var($pub_goto, "Special", "#^[\w=&%+]+$#")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_login) || !isset($pub_password)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    } else {
        $data_user = new User_Model();
        $data_statistics = new Statistics_Model();
        $result = $data_user->select_user_login($pub_login,$pub_password);

        if (list($user_id, $user_active) = $db->sql_fetch_row($result)) {
            if ($user_active == 1) {

                /* Save last visit information before update */
                $lastvisit = $data_user->select_last_visit($user_id);

                /*Update the lastvisit time in the database */
                $data_user->update_lastvisit_time($user_id);

                $data_statistics->add_user_connection();

                session_set_user_id($user_id, $lastvisit);
                log_('login');
                if (!isset($url_append)) {
                    $url_append = "";
                }
                redirection("index.php?action=" . $pub_goto . "" . $url_append);
            } else {
                redirection("index.php?action=message&id_message=account_lock&info");
            }
        } else {
            redirection("index.php?action=message&id_message=login_wrong&info");
        }
    }
}

/**
 * Login d'un utilisateur avec redirection
 * @global string $pub_login
 * @global string $pub_password
 * @global string $pub_goto
 */
function user_login_redirection()
{
    global $pub_goto, $url_append;

    if ($pub_goto == 'galaxy') {
        global $pub_galaxy, $pub_system;
        $url_append = "&galaxy=" . $pub_galaxy . "&system=" . $pub_system;
        user_login();
    } else {
        user_login();
    }
}

/**
 * Deconnection utilisateur
 */
function user_logout()
{
    log_("logout");
    session_close();
    redirection("index.php");
}

/**
 * Modification des droits ogspy d'un utilisateur par l'admin
 */
function admin_user_set()
{
    global $user_data;
    global $pub_user_id, $pub_active, $pub_user_coadmin, $pub_management_user, $pub_management_ranking;

    if (!check_var($pub_user_id, "Num") || !check_var($pub_active, "Num") || !
        check_var($pub_user_coadmin, "Num") || !check_var($pub_management_user, "Num") ||
        !check_var($pub_management_ranking, "Num")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_user_id) || !isset($pub_active)) {
        redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
    }

    //Vérification des droits
    user_check_auth("user_update", $pub_user_id);

    if ($user_data["user_admin"] == 1) {
        if (!isset($pub_user_coadmin) || !isset($pub_management_user) || !isset($pub_management_ranking)) {
            redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
        }
    } elseif ($user_data["user_coadmin"] == 1) {
        $pub_user_coadmin = null;
        if (!isset($pub_management_user) || !isset($pub_management_ranking)) {
            redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
        }
    } else {
        $pub_user_coadmin = $pub_management_user = null;
    }
    if (user_get($pub_user_id) === false) {
        redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
    }
    user_set_grant($pub_user_id, $pub_active, $pub_user_coadmin, $pub_management_user,
        $pub_management_ranking);
    redirection("index.php?action=administration&subaction=member");
}

/**
 * Generation d'un mot de passe par l'admin pour un utilisateur
 */
function admin_regeneratepwd()
{
    global $pub_user_id; // $pub_new_pass;
    $pass_id = "pub_pass_" . $pub_user_id;
    global $$pass_id;
    $new_pass = $$pass_id;

    $data_user = new User_Model();

    if (!check_var($pub_user_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_user_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    user_check_auth("user_update", $pub_user_id);

    if (user_get($pub_user_id) === false) {
        redirection("index.php?action=message&id_message=regeneratepwd_failed&info");
    }
    if ($new_pass != "") {
        $password = $new_pass;
    } else {
        $password = password_generator();
    }
    $data_user->set_user_password($pub_user_id,$password);

    $info = $pub_user_id . ":" . $password;
    log_("regeneratepwd", $pub_user_id);
    redirection("index.php?action=message&id_message=regeneratepwd_success&info=" .
        $info);
}

/**
 * Modification du profil par un utilisateur
 */
function member_user_set()
{
    global $db, $user_data, $user_technology;
    global $pub_pseudo, $pub_old_password, $pub_new_password, $pub_new_password2, $pub_galaxy,
           $pub_system, $pub_disable_ip_check, $pub_off_commandant, $pub_off_amiral, $pub_off_ingenieur,
           $pub_off_geologue, $pub_off_technocrate, $pub_pseudo_ingame, $pub_pseudo_email;

    if (!check_var($pub_pseudo, "Text") || !check_var($pub_old_password, "Text") ||
        !check_var($pub_new_password, "Text") || !check_var($pub_new_password2,
            "CharNum") || !check_var($pub_pseudo_email, "Email")
        || !check_var($pub_galaxy, "Num") || !check_var($pub_system, "Num") || !check_var($pub_disable_ip_check, "Num") || !
        check_var($pub_pseudo_ingame, "Pseudo_ingame")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $data_user = new User_Model();

    $user_id = $user_data["user_id"];
    $user_info = user_get($user_id);
    $user_empire = user_get_empire($user_id);
    $user_technology = $user_empire["technology"];

    $password_validated = null;
    if (!isset($pub_pseudo) || !isset($pub_old_password) || !isset($pub_new_password) ||
        !isset($pub_new_password2) || !isset($pub_pseudo_email) || !isset($pub_galaxy) || !isset($pub_system)
    ) {
        redirection("index.php?action=message&id_message=member_modifyuser_failed&info");
    }

    if ($pub_old_password != "" || $pub_new_password != "" || $pub_new_password2 !=
        ""
    ) {
        if ($pub_old_password == "" || $pub_new_password == "" || $pub_new_password != $pub_new_password2) {
            redirection("index.php?action=message&id_message=member_modifyuser_failed_passwordcheck&info");
        }
        if (md5(sha1($pub_old_password)) != $user_info[0]["user_password"]) {
            redirection("index.php?action=message&id_message=member_modifyuser_failed_passwordcheck&info");
        }
        if (!check_var($pub_new_password, "Password")) {
            redirection("index.php?action=message&id_message=member_modifyuser_failed_password&info");
        }
    }

    if (!check_var($pub_pseudo, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=member_modifyuser_failed_pseudo&info");
    }

    //pseudo ingame
    if ($user_data["user_stat_name"] !== $pub_pseudo_ingame) {
        $data_user->set_game_account_name($user_id, $pub_pseudo_ingame);
    }

    //compte Commandant
    if ($user_data['off_commandant'] == "0" && $pub_off_commandant == 1)
        $data_user->set_player_officer($user_id, "off_commandant", 1);

    if ($user_data['off_commandant'] == 1 && (is_null($pub_off_commandant) || $pub_off_commandant != 1))
        $data_user->set_player_officer($user_id, "off_commandant", 0);

    //compte amiral
    if ($user_data['off_amiral'] == "0" && $pub_off_amiral == 1)
        $data_user->set_player_officer($user_id, "off_amiral", 1);

    if ($user_data['off_amiral'] == 1 && (is_null($pub_off_amiral) || $pub_off_amiral != 1))
        $data_user->set_player_officer($user_id, "off_amiral", 0);

    //compte ingenieur
    if ($user_data['off_ingenieur'] == "0" && $pub_off_ingenieur == 1)
        $data_user->set_player_officer($user_id, "off_ingenieur", 1);

    if ($user_data['off_ingenieur'] == 1 && (is_null($pub_off_ingenieur) || $pub_off_ingenieur != 1))
        $data_user->set_player_officer($user_id, "off_ingenieur", 0);

    //compte geologue
    if ($user_data['off_geologue'] == "0" && $pub_off_geologue == 1)
        $data_user->set_player_officer($user_id, "off_geologue", 1);

    if ($user_data['off_geologue'] == 1 && (is_null($pub_off_geologue) || $pub_off_geologue != 1))
        $data_user->set_player_officer($user_id, "off_geologue", 0);

    //compte technocrate
    if ($user_data['off_technocrate'] == "0" && $pub_off_technocrate == 1)
        $data_user->set_player_officer($user_id, "off_technocrate", 1);

    if ($user_data['off_technocrate'] == 1 && (is_null($pub_off_technocrate) || $pub_off_technocrate != 1))
        $data_user->set_player_officer($user_id, "off_technocrate", 0);

    //Contrôle que le pseudo ne soit pas déjà utilisé
    $result = $data_user->select_user_name($pub_pseudo);

    if ($db->sql_numrows($result) != 0) {
        redirection("index.php?action=message&id_message=member_modifyuser_failed_pseudolocked&info");
    }

    if (is_null($pub_disable_ip_check) || $pub_disable_ip_check != 1)
        $pub_disable_ip_check = 0;

    if(isset($pub_pseudo)) $data_user->set_user_pseudo($user_id, $pub_pseudo);
    if(isset($pub_new_password)) $data_user->set_user_password($user_id, $pub_new_password);
    if(isset($pub_pesudo_email)) $data_user->set_user_email($user_id, $pub_pesudo_email);
    if(isset($pub_galaxy)) $data_user->set_user_default_galaxy($user_id, $pub_galaxy);
    if(isset($pub_system)) $data_user->set_user_default_system($user_id, $pub_system);
    if(isset($pub_disable_ip_check)) $data_user->set_user_ip_check($user_id,$pub_disable_ip_check);

    redirection("index.php?action=profile");
}

/**
 * Enregistrement des droits et status utilisateurs
 * @param $user_id
 * @param null $user_active
 * @param null $user_coadmin
 * @param null $management_user
 * @param null $management_ranking
 */
function user_set_grant($user_id, $user_active = null, $user_coadmin = null,
                        $management_user = null, $management_ranking = null)
{
    global $user_data;

    if (!isset($user_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    //Vérification des droits
    user_check_auth("user_update", $user_id);


    $data_user = new User_Model();

    //Activation membre
    if (!is_null($user_active)) {
        $data_user->set_user_active($user_id,intval($user_active));
        if (intval($user_active) == 0) {
            $data_session = new Sessions_Model();
            $data_session->close_user_session($user_id);
        }
    }

    //Co-administration
    if (!is_null($user_coadmin)) {
        $data_user->set_user_coadmin($user_id,intval($user_coadmin));
    }

    //Gestion des membres
    if (!is_null($management_user)) {
        $data_user->set_user_management_user($user_id,intval($management_user));
    }

    //Gestion des classements
    if (!is_null($management_ranking)) {
        $data_user->set_user_management_ranking($user_id,intval($management_ranking));
    }

    if ($user_id == $user_data['user_id']) {
        log_("modify_account");
    } else {
        log_("modify_account_admin", $user_id);
    }
}

/**
 * Recuperation d'une ligne d'information utilisateur
 * @param bool|int $user_id Identificateur optionnel d'1 utilisateur specifique
 * @return array Liste des utilisateurs ou de l'utilisateur specifique
 */
function user_get($user_id = null)
{
    $data_user = new User_Model();

    if (isset($user_id))
        $info_users = $data_user->select_user_data($user_id);
    else
        $info_users = $data_user->select_all_user_data();

    return $info_users;
}

/**
 * Recuperation des droits d'un utilisateur
 * @param int $user_id Identificateur de l'utilisateur demande
 * @return array Tableau des droits
 */
function user_get_auth($user_id)
{

    $user_info = user_get($user_id);
    $user_info = $user_info[0];
    if ($user_info["user_admin"] == 1 || $user_info["user_coadmin"] == 1) {
        $user_auth = array("server_set_system" => 1,
            "server_set_spy" => 1,
            "server_set_rc" => 1,
            "server_set_ranking" => 1,
            "server_show_positionhided" => 1,
            "ogs_connection" => 1,
            "ogs_set_system" => 1,
            "ogs_get_system" => 1,
            "ogs_set_spy" => 1,
            "ogs_get_spy" => 1,
            "ogs_set_ranking" => 1,
            "ogs_get_ranking" => 1);

        return $user_auth;
    }
    $data_user = new User_Model();
    $user_auth = $data_user->select_user_rights($user_id);

    return $user_auth;
}

/**
 * Creation d'un utilisateur a partir des donnees du formulaire admin
 * @comment redirection si erreur de type de donnee
 */
function user_create()
{
    global $db;
    global $pub_pseudo, $pub_active, $pub_user_coadmin, $pub_management_user,
           $pub_management_ranking, $pub_group_id, $pub_pass;

    if (!check_var($pub_pseudo, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=errordata&info=1");
    }

    if (!isset($pub_pseudo)) {
        redirection("index.php?action=message&id_message=createuser_failed_general&info");
    }

    //Vérification des droits
    user_check_auth("user_create");

    if (!check_var($pub_pseudo, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=createuser_failed_pseudo&info=" .
            $pub_pseudo);
    }

    if (!check_var($pub_pass, "Password")) {
        redirection("index.php?action=message&id_message=createuser_failed_password&info=" .
            $pub_pseudo);
    }

    if ($pub_pass != "") {
        $password = $pub_pass;
    } else {
        $password = password_generator();
    }

    $data_user = new User_Model();

    //On vérifie que le nom n'existe pas

    $result = $data_user->select_user_name($pub_pseudo);

    //Création de l'utilisateur
    if ($db->sql_numrows($result) == 0) {

        $user_id = $data_user->add_new_user($pub_pseudo,$password);

        // Insertion dans le groupe par défaut

        $data_user->add_user_to_group($user_id, $pub_group_id);

        $info = $user_id . ":" . $password;
        log_("create_account", $user_id);
        user_set_grant($user_id, $pub_active, $pub_user_coadmin, $pub_management_user, $pub_management_ranking);
        redirection("index.php?action=message&id_message=createuser_success&info=" . $info);
    } else {
        redirection("index.php?action=message&id_message=createuser_failed_pseudolocked&info=" .
            $pub_pseudo);
    }
}

/**
 * Suppression d'un utilisateur ($pub_user_id)
 */
function user_delete()
{
    global $pub_user_id;

    if (!check_var($pub_user_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_user_id)) {
        redirection("index.php?action=message&id_message=createuser_failed_general&info");
    }

    user_check_auth("user_update", $pub_user_id);

    $data_user = new User_Model();
    $data_user->delete_user($pub_user_id);

    log_("delete_account", $pub_user_id);

    session_close($pub_user_id);

    redirection("index.php?action=administration&subaction=member");
}

/**
 * Recuperation des statistiques
 */
function user_statistic()
{
    global $db;

    $data_user = new User_Model();
    $data_session = new Sessions_Model();
    $result = $data_user->select_all_user_stats_data();

    $user_statistic = array();
    while ($row = $db->sql_fetch_assoc($result)) {
        //Check if connected
        $here = "";
        $session_type = $data_session->get_xtense_session($row["user_id"]);
        //Check Session type
        if ($session_type == 0)
            $here = "(*)";
        elseif ($session_type == 1)
            $here = "(**)";

        $user_statistic[] = array_merge($row, array("here" => $here));
    }

    return $user_statistic;
}

/**
 * Recuperation du nombres de comptes actifs
 */
function user_get_nb_active_users()
{
    $data_users = new User_Model();
    return ($data_users->get_nb_active_users());
}

/**
 * remise en ordre des lunes en fonctions des positions des planetes
 * @todo To be reworked
 */
function user_set_all_empire_resync_id()
{
    global $user_data;

    $data_user_building = new User_Building_Model();
    $data_user_defense = new User_Defense_Model();

    $planet_position = $data_user_building->get_planet_list($user_data["user_id"]);

    $moon_position = $data_user_building->get_moon_list($user_data["user_id"]);

    //Resync Planets

    $new_planet_id = 101;
    foreach ($planet_position as $cle => $valeur) {

        $data_user_building->update_planet_id($user_data["user_id"], $valeur, $new_planet_id);
        $data_user_defense->update_planet_id($user_data["user_id"], $valeur, $new_planet_id);

        $new_planet_id++;
    }

    //Resync moons
    // on ressort les complexes planete / lune ayant la meme cle
    $complexe = array_intersect_key($planet_position, $moon_position);

    /// on passe les id se modifiant a 300
    foreach ($complexe as $cle_com => $valeur_com) {

        $data_user_defense->update_moon_id($user_data["user_id"],$moon_position[$cle_com],$planet_position[$cle_com] + 200);
        $data_user_building->update_moon_id($user_data["user_id"],$moon_position[$cle_com],$planet_position[$cle_com] + 200);
    }

}


/**
 * Récupération des données empire de l'utilisateur loggé
 * @comment On pourrait mettre un paramètre $user_id optionnel
 * @param $user_id
 * @return array
 */
function user_get_empire($user_id)
{
    global $db;
    $data_user_building = new User_Building_Model();
    $data_user_technologies = new User_Technology_Model();
    $data_user_defense = new User_Defense_Model();

    $planet = array(false, "user_id" => "", "planet_name" => "", "coordinates" => "",
        "fields" => "", "fields_used" => "", "boosters" => booster_encode(),
        "temperature_min" => "", "temperature_max" => "",
        "Sat" => 0, "Sat_percentage" => 100, "M" => 0, "M_percentage" => 100, "C" => 0,
        "C_Percentage" => 100, "D" => 0, "D_percentage" => 100, "CES" => 0, "CES_percentage" => 100,
        "CEF" => 0, "CEF_percentage" => 100, "UdR" => 0, "UdN" => 0, "CSp" => 0,
        "HM" => 0, "HC" => 0, "HD" => 0, "Lab" => 0,
        "Ter" => 0, "Silo" => 0, "BaLu" => 0, "Pha" => 0, "PoSa" => 0, "DdR" => 0,
        "C_percentage" => 100);

    $defence = array("LM" => 0, "LLE" => 0, "LLO" => 0, "CG" => 0, "AI" => 0, "LP" =>
        0, "PB" => 0, "GB" => 0, "MIC" => 0, "MIP" => 0);

    // pour affichage on selectionne 9 planetes minis
    if (find_nb_planete_user($user_id) < 9) {
        $nb_planete = 9;
    } else {
        $nb_planete = find_nb_planete_user($user_id);
    }

    $user_building = array();

    // on met les planete a 0
    for ($i = 101; $i <= ($nb_planete + 100); $i++) {
        $user_building[$i] = $planet;
    }

    // on met les lunes a 0
    for ($i = 201; $i <= ($nb_planete + 200); $i++) {
        $user_building[$i] = $planet;
    }

    $result = $data_user_building->select_user_building_list($user_id);

    //	$user_building = array_fill(101,$nb_planete , $planet);
    while ($row = $db->sql_fetch_assoc($result)) {
        $arr = $row; //TODO This is only to calculate $fields_used ??
        unset($arr["planet_id"]);
        unset($arr["planet_name"]);
        unset($arr["coordinates"]);
        unset($arr["fields"]);
        unset($arr["boosters"]);
        unset($arr["temperature_min"]);
        unset($arr["temperature_max"]);
        unset($arr["Sat"]);
        unset($arr["Sat_percentage"]);
        unset($arr["M_percentage"]);
        unset($arr["C_Percentage"]);
        unset($arr["D_percentage"]);
        unset($arr["CES_percentage"]);
        unset($arr["CEF_percentage"]);
        $fields_used = array_sum(array_values($arr));


        $row["fields_used"] = $fields_used;
        $row["boosters"] = booster_verify_str($row["boosters"]);    //Correction et mise à jour booster from date
        $row["booster_tab"] = booster_decode($row["boosters"]);    // ajout booster dans get_empire
        // incrémentation field
        if ($row["planet_id"] > 200) {
            $row["fields"] += $row["booster_tab"]["extention_p"];
        } else {
            $row["fields"] += $row["booster_tab"]["extention_m"];
        }

        $row["C_percentage"] = $row["C_Percentage"]; // ligne inutile
        $user_building[$row["planet_id"]] = $row;
        $user_building[$row["planet_id"]][0] = true;
    }

    $user_technology = $data_user_technologies->select_user_technologies($user_id);

    $result_defense = $data_user_defense->select_user_defense($user_id);

    $user_defence = array();
    // on met les def planete a 0
    for ($i = 101; $i <= ($nb_planete + 100); $i++) {
        $user_defence[$i] = $defence;
    }

    // on met les def lunes a 0
    for ($i = 201; $i <= ($nb_planete + 200); $i++) {
        $user_defence[$i] = $defence;
    }

    while ($row = $db->sql_fetch_assoc($result_defense)) {
        $planet_id = $row["planet_id"];
        unset($row["planet_id"]);
        $user_defence[$planet_id] = $row;
    }

    return array("building" => $user_building, "technology" => $user_technology,
        "defence" => $user_defence,);
}

/**
 * Récuperation du nombre de  planete de l utilisateur
 * @param $user_id
 * @return int
 */
function find_nb_planete_user($user_id)
{
    $data_user_buildings = new User_Building_Model();

    return $data_user_buildings->get_nb_planets($user_id);
}

/**
 * @param $user_id
 * @return int
 */
function find_nb_moon_user($user_id)
{
    $data_user_buildings = new User_Building_Model();

    return $data_user_buildings->get_nb_moons($user_id);
}

/**
 * Calcul production de l'empire
 * @param array $user_empire
 * @param null $off
 * @return array
 */
function user_empire_production($user_empire, $off = NULL)
{
    $prod = array();

    if ($off == NULL) {
        $off['off_commandant'] = 0;
        $off['off_amiral'] = 0;
        $off['off_ingenieur'] = 0;
        $off['off_geologue'] = 0;
        $off['off_technocrate'] = 0;
    }
    //!\\ prepa officier
    $officier = $off['off_commandant'] + $off['off_amiral'] + $off['off_ingenieur']
        + $off['off_geologue'] + $off['off_technocrate'];
    if ($officier == 5) {
        $off_full = 1;
        $officier = 2; //full officier
    } else {
        $off_full = 0;
        $officier = $off['off_geologue'];
    }
    //!\\ fin prepa officier

    //!\\ prepa techno
    $plasma = $user_empire['technology']['Plasma'] != "" ? $user_empire['technology']['Plasma'] : "0";
    $NRJ = $user_empire['technology']['NRJ'] != "" ? $user_empire['technology']['NRJ'] : "0";
    //!\\ fin prepa techno
    // prepa ration E
    $product = array("M" => 0, "C" => 0, "D" => 0, "ratio" => 1, "conso_E" => 0, "prod_E" => 0);
    $ratio = array();
    $temp_max = 0;
    // FIN prepa ration E


    foreach ($user_empire["building"] as $content) {
        if (isset($content["planet_id"]) && $content["planet_id"] < 200) {// parcours des planetes ( < 200 )

            // les different type de prod (generique)
            $type = array("M", "C", "D");
            foreach ($type as $mine) {
                $level = $content[$mine] != "" ? $content[$mine] : "0";
                if ($level != "") {
                    if (isset($content["temperature_max"])) {
                        $temp_max = $content["temperature_max"];
                    }

                    if ($mine == "D") { // specificité deut puisque les cef pompe la prod
                        $CEF = $content["CEF"];
                        $CEF_consumption = consumption("CEF", $CEF);
                        $tmp = production($mine, $level, $officier, $temp_max, $NRJ, $plasma) - $CEF_consumption;
                        $prod["theorique"][$content["planet_id"]][$mine] = number_format(floor($tmp), 0, ',', ' ');
                    } else {
                        $tmp = production($mine, $level, $officier, $temp_max, $NRJ, $plasma);
                        $prod["theorique"][$content["planet_id"]][$mine] = number_format(floor($tmp), 0, ',', ' ');
                    }
                }
            }


            // si pas de temperature impossible de calculer le ration et donc prod theorique ...
            if (isset($content["temperature_max"])) {
                // calcul ratio
                $ratio[$content["planet_id"]] = $product;
                $ratio[$content["planet_id"]] = bilan_production_ratio($content["M"], $content["C"], $content["D"],
                    $content["CES"], $content["CEF"], $content["Sat"], $content["temperature_max"], $off['off_ingenieur'], $off['off_geologue'], $off_full,
                    $NRJ, $plasma, $content["M_percentage"] / 100, $content["C_percentage"] / 100,
                    $content["D_percentage"] / 100, $content["CES_percentage"] / 100, $content["CEF_percentage"] / 100,
                    $content["Sat_percentage"] / 100, $content["booster_tab"]);

                $prod["reel"][$content["planet_id"]] = $ratio[$content["planet_id"]];
            }

        }

    }
    return $prod;
}

/**
 * Suppression des données de batiments de l'utilisateur loggé
 */
function user_del_building()
{
    global $user_data;
    global $pub_planet_id, $pub_view;

    if (!check_var($pub_planet_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if (!isset($pub_planet_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $data_user_defense = new User_Defense_Model();
    $data_user_building = new User_Building_Model();
    $data_user_technologies = new User_Technology_Model();

    $data_user_building->delete_user_aster($user_data["user_id"], $pub_planet_id);
    $data_user_defense->delete_user_aster($user_data["user_id"], $pub_planet_id);

    // si on supprime une planete; la lune doit suivre
    if (intval($pub_planet_id) < 199) {
        $moon_id = (intval($pub_planet_id) + 100);

        $data_user_building->delete_user_aster($user_data["user_id"], $moon_id);
        $data_user_defense->delete_user_aster($user_data["user_id"], $moon_id);
    }

    $nb_planets_remaining = find_nb_planete_user($user_data["user_id"]);

    if ($nb_planets_remaining == 0) {
        $data_user_technologies->delete_user_technologies($user_data["user_id"]);
    }

    // remise en ordre des planetes :
    user_set_all_empire_resync_id();

    redirection("index.php?action=home&subaction=empire&view=" . $pub_view);
}

/**
 * Déplacement des données de planète de la page empire
 */
function user_move_empire()
{
    global $user_data;
    global $pub_planet_id, $pub_left, $pub_right;

    $nb_planete = find_nb_planete_user($user_data["user_id"]);

    if (!check_var($pub_planet_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }
    if (!isset($pub_planet_id) || (!isset($pub_left) && !isset($pub_right))) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $pub_planet_id = intval($pub_planet_id);
    if ($pub_planet_id < 101 || $pub_planet_id > (100 + $nb_planete)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }
    $new_position = 0;

    if (isset($pub_left)) {
        if ($pub_planet_id == 101)
            redirection("index.php?action=home&subaction=empire");
        $new_position = $pub_planet_id - 1;
    } elseif (isset($pub_right)) {
        if ($pub_planet_id == (100 + $nb_planete))
            redirection("index.php?action=home&subaction=empire");
        $new_position = $pub_planet_id + 1;
    }

    $data_user_building = new User_Building_Model();
    $data_user_defense = new User_Defense_Model();

    $backup_id = 9999;

    $data_user_building->update_planet_id($user_data["user_id"], $pub_planet_id, $backup_id);
    $data_user_building->update_planet_id($user_data["user_id"], $new_position, $pub_planet_id);
    $data_user_building->update_planet_id($user_data["user_id"], $backup_id, $new_position);

    $data_user_defense->update_planet_id($user_data["user_id"], $pub_planet_id, $backup_id);
    $data_user_defense->update_planet_id($user_data["user_id"], $new_position, $pub_planet_id);
    $data_user_defense->update_planet_id($user_data["user_id"], $backup_id, $new_position);

    // remise en ordre des planetes :
    user_set_all_empire_resync_id();

    redirection("index.php?action=home&subaction=empire");
}

/**
 * Ajout d'un système favori
 */
function user_add_favorite()
{
    global $user_data, $server_config;
    global $pub_galaxy, $pub_system;

    if (!isset($pub_galaxy) || !isset($pub_system)) {
        redirection("index.php");
    }
    if (intval($pub_galaxy) < 1 || intval($pub_galaxy) > intval($server_config['num_of_galaxies']) ||
        intval($pub_system) < 1 || intval($pub_system) > intval($server_config['num_of_systems'])
    ) {
        redirection("index.php?action=galaxy");
    }

    $data_user_favorites = new User_Favorites_Model();

    $nb_favorites = $data_user_favorites->get_nb_user_favorites($user_data["user_id"]);

    if ($nb_favorites < $server_config["max_favorites"]) {

        $data_user_favorites->set_user_favorites($user_data["user_id"],$pub_galaxy,$pub_system);

        redirection("index.php?action=galaxy&galaxy=" . $pub_galaxy . "&system=" . $pub_system);
    } else {
        redirection("index.php?action=message&id_message=max_favorites&info");
    }
}

/**
 * Suppression d'un système favori
 */
function user_del_favorite()
{
    global $user_data;
    global $pub_galaxy, $pub_system, $server_config;

    if (!isset($pub_galaxy) || !isset($pub_system)) {
        redirection("index.php");
    }
    if (intval($pub_galaxy) < 1 || intval($pub_galaxy) > intval($server_config['num_of_galaxies']) ||
        intval($pub_system) < 1 || intval($pub_system) > intval($server_config['num_of_systems'])
    ) {
        redirection("index.php?action=galaxy");
    }

    $data_user_favorites = new User_Favorites_Model();

    $data_user_favorites->delete_user_favorites($user_data["user_id"],$pub_galaxy,$pub_system);

    redirection("index.php?action=galaxy&galaxy=" . $pub_galaxy . "&system=" . $pub_system);
}

/**
 * Récupération des rapports favoris
 */
function user_getfavorites_spy()
{
    global $user_data;
    global $sort, $sort2;

    $data_spy = new Spy_Model();

    if(!is_numeric($sort) || !is_numeric($sort2)){
        //Ordering by date Desc
        $sort = 5 ;
        $sort2 = 0;
    }
    return $data_spy->get_favoriteSpyList($user_data["user_id"], $sort, $sort2);
}

/**
 * Ajout d'un rapport favori
 */
function user_add_favorite_spy()
{
    global $user_data, $server_config;
    global $pub_spy_id, $pub_galaxy, $pub_system, $pub_row;

    if (!check_var($pub_spy_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_spy_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $data_favorites = new User_SpyFavorites_Model();
    $nb_favorites = $data_favorites->get_nb_spyfavorites($user_data["user_id"]);

    if ($nb_favorites < $server_config["max_favorites_spy"]) {
        $data_favorites->insert_spyfavorite($user_data["user_id"], $pub_spy_id);
        redirection("index.php?action=show_reportspy&galaxy=" . $pub_galaxy . "&system=" .
            $pub_system . "&row=" . $pub_row);
    } else {
        redirection("index.php?action=message&id_message=max_favorites&info=_spy");
    }
}

/**
 * Suppression d'un rapport favori
 */
function user_del_favorite_spy()
{
    global $user_data;
    global $pub_spy_id, $pub_galaxy, $pub_system, $pub_row, $pub_info;

    if (!check_var($pub_spy_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_spy_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $data_favorites = new User_SpyFavorites_Model();

    $data_favorites->delete_spyfavorite($user_data["user_id"], $pub_spy_id);

    if (!isset($pub_info))
        $pub_info = 1;

    switch ($pub_info) {
        case 2:
            redirection("index.php?action=show_reportspy&galaxy=" . $pub_galaxy . "&system=" . $pub_system . "&row=" . $pub_row);
            break;
        case 1:
            redirection("index.php?action=home&subaction=spy");
            break;
        default:
            return true;
    }
    return false;
}

/**
 * Création d'un groupe
 */
function usergroup_create()
{
    global $pub_groupname;

    if (!isset($pub_groupname)) {
        redirection("index.php?action=message&id_message=createusergroup_failed_general&info");
    }

    //Vérification des droits
    user_check_auth("usergroup_manage");

    if (!check_var($pub_groupname, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=createusergroup_failed_groupname&info");
    }

    $data_group = new Group_Model();
    $group_id = $data_group->insert_group($pub_groupname);

    if ($group_id !== FALSE) {
        log_("create_usergroup", $pub_groupname);
        redirection("index.php?action=administration&subaction=group&group_id=" . $group_id);
    } else {
        redirection("index.php?action=message&id_message=createusergroup_failed_groupnamelocked&info=" .
            $pub_groupname);
    }
}

/**
 * Suppression d'un groupe utilisateur
 */
function usergroup_delete()
{
    global $pub_group_id;

    if (!check_var($pub_group_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_group_id)) {
        redirection("index.php?action=message&id_message=createusergroup_failed_general&info");
    }

    //Vérification des droits
    user_check_auth("usergroup_manage");

    if ($pub_group_id == 1) {
        redirection("index.php?action=administration&subaction=group&group_id=1");
    }

    log_("delete_usergroup", $pub_group_id);

    $data_group = new Group_Model();
    $data_group->delete_group($pub_group_id);

    redirection("index.php?action=administration&subaction=group");
}

/**
 * Récupération des droits d'un groupe d'utilisateurs
 * @param bool $group_id
 * @return array|bool|the
 */
function usergroup_get($group_id = false)
{
    //Vérification des droits
    user_check_auth("usergroup_manage");

    $data_group = new Group_Model();

    if ($group_id == false) {
        $info_usergroup = $data_group->get_group_list();
    }else{
        $info_usergroup = $data_group->get_group_rights($group_id);
    }

    if (sizeof($info_usergroup) == 0) return false;

    return $info_usergroup;
}

/**
 * Enregistrement des droits d'un groupe utilisateurs
 */
function usergroup_setauth()
{
    global $db;
    global $pub_group_id, $pub_group_name, $pub_server_set_system, $pub_server_set_spy,
           $pub_server_set_rc, $pub_server_set_ranking, $pub_server_show_positionhided, $pub_ogs_connection,
           $pub_ogs_set_system, $pub_ogs_get_system, $pub_ogs_set_spy, $pub_ogs_get_spy, $pub_ogs_set_ranking,
           $pub_ogs_get_ranking;

    if (!check_var($pub_group_id, "Num") || !check_var($pub_group_name,
            "Pseudo_Groupname") || !check_var($pub_server_set_system, "Num") || !check_var($pub_server_set_spy,
            "Num") || !check_var($pub_server_set_rc, "Num") || !check_var($pub_server_set_ranking,
            "Num") || !check_var($pub_server_show_positionhided, "Num") || !check_var($pub_ogs_connection,
            "Num") || !check_var($pub_ogs_set_system, "Num") || !check_var($pub_ogs_get_system,
            "Num") || !check_var($pub_ogs_set_spy, "Num") || !check_var($pub_ogs_get_spy,
            "Num") || !check_var($pub_ogs_set_ranking, "Num") || !check_var($pub_ogs_get_ranking,
            "Num")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_group_id) || !isset($pub_group_name)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    if (is_null($pub_server_set_system))
        $pub_server_set_system = 0;
    if (is_null($pub_server_set_spy))
        $pub_server_set_spy = 0;
    if (is_null($pub_server_set_rc))
        $pub_server_set_rc = 0;
    if (is_null($pub_server_set_ranking))
        $pub_server_set_ranking = 0;
    if (is_null($pub_server_show_positionhided))
        $pub_server_show_positionhided = 0;
    if (is_null($pub_ogs_connection))
        $pub_ogs_connection = 0;
    if (is_null($pub_ogs_set_system))
        $pub_ogs_set_system = 0;
    if (is_null($pub_ogs_get_system))
        $pub_ogs_get_system = 0;
    if (is_null($pub_ogs_set_spy))
        $pub_ogs_set_spy = 0;
    if (is_null($pub_ogs_get_spy))
        $pub_ogs_get_spy = 0;
    if (is_null($pub_ogs_set_ranking))
        $pub_ogs_set_ranking = 0;
    if (is_null($pub_ogs_get_ranking))
        $pub_ogs_get_ranking = 0;

    //Vérification des droits
    user_check_auth("usergroup_manage");

    log_("modify_usergroup", $pub_group_id);

    $request = "update " . TABLE_GROUP;
    $request .= " set group_name = '" . $db->sql_escape_string($pub_group_name) .
        "',";
    $request .= " server_set_system = '" . intval($pub_server_set_system) .
        "', server_set_spy = '" . intval($pub_server_set_spy) . "', server_set_rc = '" .
        intval($pub_server_set_rc) . "', server_set_ranking = '" . intval($pub_server_set_ranking) .
        "', server_show_positionhided = '" . intval($pub_server_show_positionhided) .
        "',";
    $request .= " ogs_connection = '" . intval($pub_ogs_connection) .
        "', ogs_set_system = '" . intval($pub_ogs_set_system) . "', ogs_get_system = '" .
        intval($pub_ogs_get_system) . "', ogs_set_spy = '" . intval($pub_ogs_set_spy) .
        "', ogs_get_spy = '" . intval($pub_ogs_get_spy) . "', ogs_set_ranking = '" .
        intval($pub_ogs_set_ranking) . "', ogs_get_ranking = '" . intval($pub_ogs_get_ranking) .
        "'";
    $request .= " where group_id = " . intval($pub_group_id);
    $db->sql_query($request);

    redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
}

/**
 * Récupération des utilisateurs appartenant à un groupe
 * @param int $group_id Identificateur du groupe demandé
 * @return Array Liste des utilisateurs
 */
function usergroup_member($group_id)
{
    global $db;

    if (!isset($group_id) || !is_numeric($group_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $usergroup_member = array();

    $request = "select u.user_id, u.user_name from " . TABLE_USER . " as  u, " .
        TABLE_USER_GROUP . " as g";
    $request .= " where u.user_id = g.user_id";
    $request .= " and g.group_id = " . intval($group_id);
    $request .= " order by user_name";
    $result = $db->sql_query($request);
    while ($row = $db->sql_fetch_assoc($result)) {
        $usergroup_member[] = $row;
    }

    return $usergroup_member;
}

/**
 * Ajout d'un utilisateur à un groupe
 */
function usergroup_newmember()
{
    global $db;
    global $pub_user_id, $pub_group_id, $pub_add_all;

    if ($pub_add_all == "Ajouter tout les membres") {
        $request = "SELECT user_id FROM " . TABLE_USER;
        $result = $db->sql_query($request);

        while ($res = $db->sql_fetch_assoc($result)) {
            user_check_auth("usergroup_manage");
            $request = "INSERT IGNORE INTO " . TABLE_USER_GROUP .
                " (group_id, user_id) values (" . intval($pub_group_id) . ", " . intval($res["user_id"]) .
                ")";
            $db->sql_query($request);
        }
        redirection("index.php?action=administration&subaction=group");
    } else {
        if (!check_var($pub_user_id, "Num") || !check_var($pub_group_id, "Num")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }

        if (!isset($pub_user_id) || !isset($pub_group_id)) {
            redirection("index.php?action=message&id_message=errorfatal&info");
        }

        //Vérification des droits
        user_check_auth("usergroup_manage");

        $request = "select group_id from " . TABLE_GROUP . " where group_id = " . intval($pub_group_id);
        $result = $db->sql_query($request);
        if ($db->sql_numrows($result) == 0) {
            redirection("index.php?action=administration&subaction=group");
        }

        $request = "select user_id from " . TABLE_USER . " where user_id = " . intval($pub_user_id);
        $result = $db->sql_query($request);
        if ($db->sql_numrows($result) == 0) {
            redirection("index.php?action=administration&subaction=group");
        }

        $request = "insert ignore into " . TABLE_USER_GROUP .
            " (group_id, user_id) values (" . intval($pub_group_id) . ", " . intval($pub_user_id) .
            ")";
        $db->sql_query($request);

        if ($db->sql_affectedrows() > 0) {
            log_("add_usergroup", array($pub_group_id, $pub_user_id));
        }

        redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
    }
}

/**
 * Supression d'un utilisateur d'un groupe
 * @global int $pub_user_id Identificateur utilisateur
 * @global int $pub_group_id Identificateur du Groupe
 */
function usergroup_delmember()
{
    global $db;
    global $pub_user_id, $pub_group_id;

    if (!isset($pub_user_id) || !isset($pub_group_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }
    if (!check_var($pub_user_id, "Num") || !check_var($pub_group_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    //Vérification des droits
    user_check_auth("usergroup_manage");

    $request = "delete from " . TABLE_USER_GROUP . " where group_id = " . intval($pub_group_id) .
        " and user_id = " . intval($pub_user_id);
    $db->sql_query($request);

    if ($db->sql_affectedrows() > 0) {
        log_("del_usergroup", array($pub_group_id, $pub_user_id));
    }

    redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
}

//Suppression d'un rapport d'espionnage
/**
 * Deletes a Spy Report
 * @return bool
 */
function user_del_spy()
{
    global $db, $user_data;
    global $pub_spy_id, $pub_galaxy, $pub_system, $pub_row, $pub_info;

    if (!check_var($pub_spy_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_spy_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
        $request = "delete from " . TABLE_PARSEDSPY . " where id_spy = '" . $pub_spy_id .
            "'";
        $db->sql_query($request);
    }

    if (!isset($pub_info))
        $pub_info = 1;

    switch ($pub_info) {
        case 2:
            redirection("index.php?action=show_reportspy&galaxy=" . $pub_galaxy . "&system=" . $pub_system . "&row=" . $pub_row);
            break;
        case 1:
            redirection("index.php?action=home&subaction=spy");
            break;
        default:
            return true;
    }
    return false;
}

/**
 * Reconstruction des RC
 * @global $db
 * @param int $id_RC RC à reconstituer
 * @return string $template_RC reconstitué
 */
function UNparseRC($id_RC)
{
    global $db, $lang;

    $key_ships = array('PT' => $lang['GAME_FLEET_PT_S'], 'GT' => $lang['GAME_FLEET_GT_S'], 'CLE' => $lang['GAME_FLEET_CLE_S'],
        'CLO' => $lang['GAME_FLEET_CLO_S'], 'CR' => $lang['GAME_FLEET_CR_S'], 'VB' => $lang['GAME_FLEET_VB_S'], 'VC' =>
            $lang['GAME_FLEET_VC_S'], 'REC' => $lang['GAME_FLEET_REC_S'], 'SE' => $lang['GAME_FLEET_SE_S'], 'BMD' => $lang['GAME_FLEET_BMD_S'],
        'DST' => $lang['GAME_FLEET_DST_S'], 'EDLM' => $lang['GAME_FLEET_EDLM_S'], 'SAT' => $lang['GAME_FLEET_SAT_S'], 'TRA' => $lang['GAME_FLEET_TRA_S']);
    $key_defs = array('LM' => $lang['GAME_DEF_LM_S'], 'LLE' => $lang['GAME_DEF_LLE_S'], 'LLO' => $lang['GAME_DEF_LLO_S'],
        'CG' => $lang['GAME_DEF_CG_S'], 'AI' => $lang['GAME_DEF_AI_S'], 'LP' => $lang['GAME_DEF_LP_S'], 'PB' =>
            $lang['GAME_DEF_PB_S'], 'GB' => $lang['GAME_DEF_GB_S']);
    $base_ships = array('PT' => array(4000, 10, 5), 'GT' => array(12000, 25, 5),
        'CLE' => array(4000, 10, 50), 'CLO' => array(10000, 25, 150), 'CR' => array(27000,
            50, 400), 'VB' => array(60000, 200, 1000), 'VC' => array(30000, 100, 50), 'REC' =>
            array(16000, 10, 1), 'SE' => array(1000, 0, 0), 'BMD' => array(75000, 500, 1000),
        'DST' => array(110000, 500, 2000), 'EDLM' => array(9000000, 50000, 200000),
        'SAT' => array(2000, 1, 1), 'TRA' => array(70000, 400, 700));
    $base_defs = array('LM' => array(2000, 20, 80), 'LLE' => array(2000, 25, 100),
        'LLO' => array(8000, 100, 250), 'CG' => array(35000, 200, 1100), 'AI' => array(8000,
            500, 150), 'LP' => array(100000, 300, 3000), 'PB' => array(20000, 2000, 1), 'GB' =>
            array(100000, 10000, 1));

    // Récupération des constantes du RC
    $query = 'SELECT dateRC, coordinates, nb_rounds, victoire, pertes_A, pertes_D, gain_M, gain_C, 
    gain_D, debris_M, debris_C, lune FROM ' . TABLE_PARSEDRC . ' WHERE id_rc = ' .
        $id_RC;
    $result = $db->sql_query($query);
    list($dateRC, $coordinates, $nb_rounds, $victoire, $pertes_A, $pertes_D, $gain_M,
        $gain_C, $gain_D, $debris_M, $debris_C, $lune) = $db->sql_fetch_row($result);
    $dateRC = date($lang['GAME_CREPORT_DATE'], $dateRC);
    $template = $lang['GAME_CREPORT_FIGHT'] . ' (' . $dateRC . "):\n\n";

    // Récupération de chaque round du RC
    for ($idx = 1; $idx <= $nb_rounds; $idx++) {
        $query = 'SELECT id_rcround, attaque_tir, attaque_puissance, attaque_bouclier, defense_tir, 
      defense_puissance, defense_bouclier FROM ' . TABLE_PARSEDRCROUND .
            ' WHERE id_rc = ' . $id_RC . '
     AND numround = ' . $idx;
        $result_round = $db->sql_query($query);
        list($id_rcround, $attaque_tir, $attaque_puissance, $attaque_bouclier, $defense_tir,
            $defense_puissance, $defense_bouclier) = $db->sql_fetch_row($result_round);
        // On formate les résultats
        $nf_gain_M = number_format($gain_M, 0, ',', '.');
        $nf_gain_C = number_format($gain_C, 0, ',', '.');
        $nf_gain_D = number_format($gain_D, 0, ',', '.');
        $nf_pertes_A = number_format($pertes_A, 0, ',', '.');
        $nf_pertes_D = number_format($pertes_D, 0, ',', '.');
        $nf_debris_M = number_format($debris_M, 0, ',', '.');
        $nf_debris_C = number_format($debris_C, 0, ',', '.');
        $nf_attaque_tir = number_format($attaque_tir, 0, ',', '.');
        $nf_attaque_puissance = number_format($attaque_puissance, 0, ',', '.');
        $nf_attaque_bouclier = number_format($attaque_bouclier, 0, ',', '.');
        $nf_defense_tir = number_format($defense_tir, 0, ',', '.');
        $nf_defense_puissance = number_format($defense_puissance, 0, ',', '.');
        $nf_defense_bouclier = number_format($defense_bouclier, 0, ',', '.');

        // Récupération de chaque attaquant du RC
        $query = 'SELECT player, coordinates, Armes, Bouclier, Protection, PT, GT, CLE, CLO, CR, VB, VC, REC, 
      SE, BMD, DST, EDLM, TRA FROM ' . TABLE_ROUND_ATTACK .
            ' WHERE id_rcround = ' . $id_rcround;
        $result_attack = $db->sql_query($query);
        while (list($player, $coordinates, $Armes, $Bouclier, $Protection, $PT, $GT, $CLE,
            $CLO, $CR, $VB, $VC, $REC, $SE, $BMD, $DST, $EDLM, $TRA) = $db->sql_fetch_row($result_attack)) {
            $key = '';
            $ship = 0;
            $vivant_att = false;
            $template .= $lang['GAME_CREPORT_ATT'] . ' ' . $player;
            $ship_type = $lang['GAME_CREPORT_TYPE'];
            $ship_nombre = $lang['GAME_CREPORT_NB'];
            $ship_armes = $lang['GAME_CREPORT_WEAPONS'];
            $ship_bouclier = $lang['GAME_CREPORT_SHIELD'];
            $ship_protection = $lang['GAME_CREPORT_PROTECTION'];
            foreach ($key_ships as $key => $ship) {
                if (isset($$key) && $$key > 0) {
                    $vivant_att = true;
                    $ship_type .= "\t" . $ship;
                    $ship_nombre .= "\t" . number_format($$key, 0, ',', '.');;
                    $ship_protection .= "\t" . number_format(round(($base_ships[$key][0] * (($Protection / 10) * 0.1 + 1)) / 10), 0, ',', '.');
                    $ship_bouclier .= "\t" . number_format(round($base_ships[$key][1] * (($Bouclier / 10) * 0.1 + 1)), 0, ',', '.');
                    $ship_armes .= "\t" . number_format(round($base_ships[$key][2] * (($Armes / 10) * 0.1 + 1)), 0, ',', '.');
                }
            }
            if ($vivant_att == true) {
                $template .= ' [' . $coordinates . ']';
                if ($idx == 1)
                    $template .= ' ' . $lang['GAME_CREPORT_WEAPONS'] . ': ' . $Armes . '% ' . $lang['GAME_CREPORT_SHIELD'] . ': ' . $Bouclier . '% ' . $lang['GAME_CREPORT_PROTECTION'] . ': ' . $Protection . '%';
                $template .= "\n";
                $template .= $ship_type . "\n" . $ship_nombre . "\n" . $ship_armes . "\n" . $ship_bouclier . "\n" . $ship_protection . "\n\n";
            } else
                $template .= ' détruit.' . "\n\n";
        } // Fin récupération de chaque attaquant du RC

        // Récupération de chaque défenseur du RC
        $query = 'SELECT player, coordinates, Armes, Bouclier, Protection, PT, GT, CLE, CLO, CR, VB, VC, REC, 
      SE, BMD, SAT, DST, EDLM, TRA, LM, LLE, LLO, CG, AI, LP, PB, GB FROM ' .
            TABLE_ROUND_DEFENSE . ' WHERE 
      id_rcround = ' . $id_rcround;
        $result_defense = $db->sql_query($query);
        while (list($player, $coordinates, $Armes, $Bouclier, $Protection, $PT, $GT, $CLE,
            $CLO, $CR, $VB, $VC, $REC, $SE, $BMD, $SAT, $DST, $EDLM, $TRA, $LM, $LLE, $LLO, $CG, $AI,
            $LP, $PB, $GB) = $db->sql_fetch_row($result_defense)) {
            $key = '';
            $ship = 0;
            $vivant_def = false;
            $template .= $lang['GAME_CREPORT_DEF'] . ' ' . $player;
            $ship_type = $lang['GAME_CREPORT_TYPE'];
            $ship_nombre = $lang['GAME_CREPORT_NB'];
            $ship_armes = $lang['GAME_CREPORT_WEAPONS'];
            $ship_bouclier = $lang['GAME_CREPORT_SHIELD'];
            $ship_protection = $lang['GAME_CREPORT_PROTECTION'];
            foreach ($key_ships as $key => $ship) {
                if (isset($$key) && $$key > 0) {
                    $vivant_def = true;
                    $ship_type .= "\t" . $ship;
                    $ship_nombre .= "\t" . number_format($$key, 0, ',', '.');
                    $ship_protection .= "\t" . number_format(round(($base_ships[$key][0] * (($Protection / 10) * 0.1 + 1)) / 10), 0, ',', '.');
                    $ship_bouclier .= "\t" . number_format(round($base_ships[$key][1] * (($Bouclier / 10) * 0.1 + 1)), 0, ',', '.');
                    $ship_armes .= "\t" . number_format(round($base_ships[$key][2] * (($Armes / 10) * 0.1 + 1)), 0, ',', '.');
                }
            }
            foreach ($key_defs as $key => $def) {
                if (isset($$key) && $$key > 0) {
                    $vivant_def = true;
                    $ship_type .= "\t" . $def;
                    $ship_nombre .= "\t" . number_format($$key, 0, ',', '.');
                    $ship_protection .= "\t" . number_format(round(($base_defs[$key][0] * (($Protection / 10) * 0.1 + 1)) / 10), 0, ',', '.');
                    $ship_bouclier .= "\t" . number_format(round($base_defs[$key][1] * (($Bouclier / 10) * 0.1 + 1)), 0, ',', '.');
                    $ship_armes .= "\t" . number_format(round($base_defs[$key][2] * (($Armes / 10) * 0.1 + 1)), 0, ',', '.');
                }
            }
            if ($vivant_def == true) {
                $template .= ' [' . $coordinates . ']';
                if ($idx == 1)
                    $template .= ' ' . $lang['GAME_CREPORT_WEAPONS'] . ': ' . $Armes . '% ' . $lang['GAME_CREPORT_SHIELD'] . ': ' . $Bouclier . '% ' . $lang['GAME_CREPORT_PROTECTION'] . ': ' . $Protection . '%';
                $template .= "\n";
                $template .= $ship_type . "\n" . $ship_nombre . "\n" . $ship_armes . "\n" . $ship_bouclier . "\n" . $ship_protection . "\n\n";
            } else
                $template .= ' ' . $lang['GAME_CREPORT_DESTROYED'] . ' ' . "\n\n";
        } // Fin récupération de chaque défenseur du RC

        // Résultat du round
        if ($attaque_tir != 0 || $defense_tir != 0) {
            $template .= $lang['GAME_CREPORT_RESULT_FLEET'] . ' ' . $nf_attaque_tir .
                ' ' . $lang['GAME_CREPORT_RESULT_FLEET_1'] . ' ' . $nf_attaque_puissance .
                ' ' . $lang['GAME_CREPORT_RESULT_FLEET_2'] . ' ' . $nf_defense_bouclier .
                ' ' . $lang['GAME_CREPORT_RESULT_FLEET_3'] . ' ' . "\n\n";
            $template .= $lang['GAME_CREPORT_RESULT_DEF'] . ' ' . $nf_defense_tir .
                ' ' . $lang['GAME_CREPORT_RESULT_DEF_1'] . ' ' . $nf_defense_puissance . '. ' . $lang['GAME_CREPORT_RESULT_DEF_2'] . ' ' .
                $nf_attaque_bouclier . ' ' . $lang['GAME_CREPORT_RESULT_DEF_3'] . '.' . "\n\n";
        }
    } // Fin récupération de chaque round du RC

    // Qui a remporté le combat ?
    switch ($victoire) {
        case 'N':
            $template .= $lang['GAME_CREPORT_RESULT_EVEN'] . '.' .
                "\n\n";
            break;
        case 'A':
            $template .= $lang['GAME_CREPORT_RESULT_WIN'] . ' ' .
                $nf_gain_M . ' ' . $lang['GAME_CREPORT_RESULT_WIN_1'] . ', ' . $nf_gain_C . ' ' . $lang['GAME_CREPORT_RESULT_WIN_2'] . ' ' . $nf_gain_D .
                ' ' . $lang['GAME_CREPORT_RESULT_WIN_3'] . '.' . "\n\n";
            break;
        case 'D':
            $template .= $lang['GAME_CREPORT_RESULT_LOST'] . "\n\n";
            break;
    }

    // Pertes et champs de débris
    $template .= $lang['GAME_CREPORT_RESULT_LOSTPOINTS_A'] . ' ' . $nf_pertes_A . ' ' . $lang['GAME_CREPORT_RESULT_UNITS'] . '.' . "\n";
    $template .= $lang['GAME_CREPORT_RESULT_LOSTPOINTS_D'] . ' ' . $nf_pertes_D . ' ' . $lang['GAME_CREPORT_RESULT_UNITS'] . '.' . "\n";
    $template .= $lang['GAME_CREPORT_RESULT_DEBRIS'] . ' ' . $nf_debris_M .
        ' ' . $lang['GAME_CREPORT_RESULT_DEBRIS_M'] . ' ' . $nf_debris_C . ' ' . $lang['GAME_CREPORT_RESULT_DEBRIS_C'] .
        "\n";

    $lunePourcent = floor(($debris_M + $debris_C) / 100000);
    $lunePourcent = ($lunePourcent < 0 ? 0 : ($lunePourcent > 20 ? 20 : $lunePourcent));
    if ($lunePourcent > 0)
        $template .= $lang['GAME_CREPORT_RESULT_NO_MOON'] . ' ' . $lunePourcent . ' %';

    if ($lune == 1)
        $template .= "\n" . $lang['GAME_CREPORT_RESULT_MOON'] . ".";

    return ($template);
}

/**
 * Fonction de calcul du ratio
 * @param int $player user_id ID du joueur
 * @return array ratio et divers calculs intermédiaires pour l'utilisateur en question
 * @author Bousteur 25/11/2006
 */
function ratio_calc($player)
{
    global $db, $user_data;

    //récupération des données nécessaires
    $sqlrecup = "SELECT planet_added_xtense, search, spy_added_xtense, rank_added_xtense FROM " .
        TABLE_USER . " WHERE user_id='" . $player . "'";
    $result = $db->sql_query($sqlrecup);
    list($planet_added_xtense, $search, $spy_added_xtense, $rank_added_xtense) = $db->sql_fetch_row($result);
    $request = "select planet_added_xtense, spy_added_xtense, rank_added_xtense, search";
    $request .= "from " . TABLE_USER;
    $resultat = $db->sql_query($request);

    list($planetimporttotal, $spyimporttotal, $rankimporttotal, $searchtotal) = $db->sql_fetch_row($resultat);

    $query = "SELECT COUNT(user_id) as count FROM " . TABLE_USER;
    $result = $db->sql_query($query);

    if ($db->sql_numrows($result) > 0) {
        $row = $db->sql_fetch_assoc($result);
        $max = $row['count'];
    }
    //pour éviter la division par zéro
    if ($planetimporttotal == 0)
        $planetimporttotal = 1;
    if ($spyimporttotal == 0)
        $spyimporttotal = 1;
    if ($rankimporttotal == 0)
        $rankimporttotal = 1;
    if ($searchtotal == 0)
        $searchtotal = 1;

    //et on commence le calcul
    $ratio_planet = $planet_added_xtense / $planetimporttotal;
    $ratio_spy = $spy_added_xtense / $spyimporttotal;
    $ratio_rank = $rank_added_xtense / $rankimporttotal;
    $ratio = ($ratio_planet * 4 + $ratio_spy * 2 + $ratio_rank) / (4 + 2 + 1);

    $ratio_planet_penality = $planet_added_xtense  / $planetimporttotal;
    $ratio_spy_penality = $spy_added_xtense / $spyimporttotal;
    $ratio_rank_penality = $rank_added_xtense /$rankimporttotal;
    $ratio_penality = ($ratio_planet_penality * 4 + $ratio_spy_penality * 2 + $ratio_rank_penality) / (4 +
            2 + 1);

    $ratio_search = $search / $searchtotal;
    $ratio_searchpenality = ($ratio - $ratio_search);

    $result = ($ratio + $ratio_penality + $ratio_searchpenality) * 1000;
    $array = array($result, $ratio_searchpenality, $ratio_search, $ratio_penality, $ratio_rank_penality,
        $ratio_spy_penality, $ratio_planet_penality);

    //retourne le ratio et calculs intermédiaires
    return $array;
}

/**
 * Fonction de test d'autorisation d'effectuer une action en fonction du ratio ou de l'appartenance à un groupe qui a un ratio illimité
 * @return bool vrai si l'utilisateur peut faire des recherches
 * @author Bousteur 28/11/2006
 */
function ratio_is_ok()
{
    global $user_data, $server_config;
    static $result;

    if ($result != null)
        return $result;
    if (isset($server_config["block_ratio"]) && $server_config["block_ratio"] == 1) {
        if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] ==
            1
        ) {
            return true;
        } else {
            $result = ratio_calc($user_data['user_id']);
            $result = $result[0] >= $server_config["ratio_limit"];
            return $result;
        }
    } else {
        return true;
    }
}
