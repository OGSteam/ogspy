<?php

/**
 * user.php Fonctions concernant les utilisateurs
 * @package OGSpy
 * @subpackage user
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b
 * @created 06/12/2005
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\Group_Model;
use Ogsteam\Ogspy\Model\Sessions_Model;
use Ogsteam\Ogspy\Model\Statistics_Model;
use Ogsteam\Ogspy\Model\Player_Building_Model;
use Ogsteam\Ogspy\Model\Player_Defense_Model;
use Ogsteam\Ogspy\Model\Player_Technology_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Ogsteam\Ogspy\Model\Player_Model;
use Ogsteam\Ogspy\Model\User_Favorites_Model;
use Ogsteam\Ogspy\Model\Spy_Model;
use Ogsteam\Ogspy\Model\Tokens_Model;
use Ogsteam\Ogspy\Model\User_Spy_favorites_Model;
use Ogsteam\Ogspy\Model\Combat_Report_Model;


/**Tokens_Model
 * Verification des droits utilisateurs sur une action avec redirection le cas echeant
 * @param string $action Action verifie
 * @param int $user_id identificateur optionnel de l'utilisateur teste
 */
function user_check_auth($action, $user_id = null)
{
    global $user_data;

    switch ($action) {
        case "user_create":
        case "usergroup_manage":
            if (
                $user_data["admin"] != 1 && $user_data["coadmin"] != 1 && $user_data["management_user"] !=
                1
            ) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }

            break;

        case "user_update":
            if (
                $user_data["admin"] != 1 && $user_data["coadmin"] != 1 && $user_data["management_user"] !=
                1
            ) {
                redirection("index.php?action=message&id_message=forbidden&info");
            }

            $info_user = user_get($user_id);
            if ($info_user === false) {
                redirection("index.php?action=message&id_message=deleteuser_failed&info");
            }

            if (($user_data["admin"] != 1 &&
                $user_data["coadmin"] != 1 &&
                $user_data["management_user"] != 1) || ($info_user[0]["admin"] == 1) || (($user_data["coadmin"] == 1) &&
                ($info_user[0]["coadmin"] == 1)) || (($user_data["coadmin"] != 1 &&
                $user_data["management_user"] == 1) &&
                ($info_user[0]["coadmin"] == 1 || $info_user[0]["management_user"] == 1))) {
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
    global $pub_login, $pub_password, $pub_goto, $pub_token,$log;

    $User_Model = new User_Model();

    $log->info("Tentative de connexion pour l'utilisateur: " . $pub_login);

    if (!token::statiCheckToken($pub_token)) {
        $log->warning("Échec de validation du token pour l'utilisateur: " . $pub_login);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!check_var($pub_login, "Pseudo_Groupname") || !check_var(
        $pub_password,
        "Password"
    ) || !check_var($pub_goto, "Special", "#^[\w=&%+]+$#")) {
        $log->warning("Données de connexion invalides pour l'utilisateur: " . $pub_login);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_login) || !isset($pub_password)) {
        $log->error("Paramètres de connexion manquants");
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $tlogin = $User_Model->select_user_login($pub_login, $pub_password);
    // si  retour
    if ($tlogin) {
        if (password_verify($pub_password, $tlogin['password_s'])) {
            // Format Mot de passe Secure
            $log->info("Authentification réussie pour l'utilisateur: " . $pub_login);
            user_set_connection($tlogin['id'], $tlogin['active']);
        } else {
            $log->warning("Mot de passe incorrect pour l'utilisateur: " . $pub_login);
            redirection("index.php?action=message&id_message=login_wrong&info");
        }
    } else {
        $log->warning("Utilisateur non trouvé: " . $pub_login);
        redirection("index.php?action=message&id_message=login_wrong&info");
    }
}

function user_set_connection($user_id, $user_active)
{
    global $pub_goto,$log;
    (new User_Model())->update_lastvisit_time($user_id);

    if ($user_active) {
        $lastvisit = (new User_Model())->select_last_visit($user_id);

        ///stat
        (new Statistics_Model())->add_user_connection();
        $log->info("Connexion établie pour l'utilisateur : " . $user_id);
        session_set_user_id($user_id, $lastvisit);
        log_('login');
        if (!isset($url_append)) {
            $url_append = "";
        }
        $log->info("Redirection de l'utilisateur ID: " . $user_id . " vers " . $pub_goto);
        redirection("index.php?action=" . $pub_goto . $url_append);
    } else {
        $log->warning("Tentative de connexion rejetée pour l'utilisateur ID: " . $user_id . " (compte verrouillé)");
        redirection("index.php?action=message&id_message=account_lock&info");
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
    global $pub_goto, $url_append,$pub_galaxy, $pub_system,$log;

    if ($pub_goto == 'galaxy') {
        $log->info("Redirection de connexion vers la galaxie: " . $pub_galaxy . ", système: " . $pub_system);
        $url_append = "&galaxy=" . $pub_galaxy . "&system=" . $pub_system;
        user_login();
    } else {
        $log->info("Redirection de connexion vers: " . $pub_goto);
        user_login();
    }
}

/**
 * Deconnection utilisateur
 */
function user_logout()
{
    global $user_data, $log;

    if (isset($user_data["id"])) {
        $log->info("Déconnexion de l'utilisateur ID: " . $user_data["id"] . ", pseudo: " . $user_data["name"]);
    } else {
        $log->warning("Tentative de déconnexion d'un utilisateur non identifié");
    }

    log_("logout");
    session_close();

    $log->info("Session fermée avec succès");
    redirection("index.php");
}

/**
 * Modification des droits ogspy d'un utilisateur par l'admin
 */
function admin_user_set()
{
    global $user_data;
    global $pub_user_id, $pub_active, $pub_user_coadmin, $pub_management_user, $pub_management_ranking;

    if (
        !check_var($pub_user_id, "Num") ||
        !check_var($pub_active, "Num") ||
        !check_var($pub_user_coadmin, "Num") ||
        !check_var($pub_management_user, "Num") ||
        !check_var($pub_management_ranking, "Num")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_user_id) || !isset($pub_active)) {
        redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
    }

    //Vérification des droits
    user_check_auth("user_update", $pub_user_id);

    if ($user_data["admin"]) {
        if (!isset($pub_user_coadmin) || !isset($pub_management_user) || !isset($pub_management_ranking)) {
            redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
        }
    } elseif ($user_data["coadmin"]) {
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
    user_set_grant(
        $pub_user_id,
        null,
        $pub_active,
        $pub_user_coadmin,
        $pub_management_user,
        $pub_management_ranking
    );
    redirection("index.php?action=administration&subaction=member");
}

/**
 * Generation d'un mot de passe par l'admin pour un utilisateur
 */
function admin_regeneratepwd()
{
    global $pub_user_id, $lang, $server_config;
    $pass_id = "pub_pass_" . $pub_user_id;
    global $$pass_id;
    $new_pass = $$pass_id;

    if (!check_var($pub_user_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_user_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    user_check_auth("user_update", $pub_user_id);
    $user_info = user_get($pub_user_id)[0];

    if ($user_info === false) {
        redirection("index.php?action=message&id_message=regeneratepwd_failed&info");
    }
    if ($new_pass != "") {
        $password = $new_pass;
    } else {
        $password = password_generator();
    }
    user_set_general($pub_user_id, null, $password);

    $NovisualisationMdpAdmin = true;
    if ($server_config["mail_use"] == 1 && $user_info["user_email"] !== "") {
        $NovisualisationMdpAdmin = sendMail($user_info["user_email"], $lang['MAIL_RESET_PASSWORD_SUBJECT'], "<h1>" . $lang['MAIL_RESET_PASSWORD_MESSAGE'] . $password . "</h1>");
        log_("debug", "Reset mot de passe : Le mail a été envoyé à " . $user_info["user_email"]);
    } else {
        // pas d'usage de mail donc visualisation admin à affectuer
        $NovisualisationMdpAdmin = false;
    }

    if ($NovisualisationMdpAdmin == false) {
        log_("regeneratepwd", $pub_user_id);
        $info = $pub_user_id . ":" . $password;
        redirection("index.php?action=message&id_message=regeneratepwd_success&info=" . $info);
    } else {
        $info = $pub_user_id . ":mail";
        log_("regeneratepwd_", $pub_user_id);
        redirection("index.php?action=message&id_message=regeneratepwd_success&info=$info");
    }
}

/**
 * Modification du profil par un utilisateur
 * @todo Query : x11
 */
function member_user_set()
{
    global $user_data, $user_technology;
    global $pub_pseudo, $pub_old_password, $pub_new_password, $pub_new_password2, $pub_galaxy,
        $pub_system, $pub_disable_ip_check,
        $pub_pseudo_ingame, $pub_pseudo_email, $pub_renew_user_token;

    if (
        !check_var($pub_pseudo, "Text") || !check_var($pub_old_password, "Text") ||
        !check_var($pub_new_password, "Password") ||
        !check_var($pub_new_password2, "Password") ||
        !check_var($pub_pseudo_email, "Email") ||
        !check_var($pub_galaxy, "Num") ||
        !check_var($pub_system, "Num") ||
        !check_var($pub_disable_ip_check, "Num") ||
        !check_var($pub_pseudo_ingame, "Pseudo_ingame")
    ) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $User_Model = new User_Model();

    $user_id = $user_data["id"];
    $user_info = user_get($user_id);

    $player_id = $user_data["player_id"];
    $user_empire = player_get_empire($player_id);
    $user_technology = $user_empire["technology"];

    $password_change_validated = false;
    if (
        !isset($pub_pseudo) || !isset($pub_old_password) || !isset($pub_new_password) ||
        !isset($pub_new_password2) || !isset($pub_pseudo_email) || !isset($pub_galaxy) || !isset($pub_system)
    ) {
        redirection("index.php?action=message&id_message=member_modifyuser_failed&info");
    }

    if ($pub_old_password != "" || $pub_new_password != "" || $pub_new_password2 != "") {
        if ($pub_old_password == "" || $pub_new_password == "" || $pub_new_password != $pub_new_password2) {
            redirection("index.php?action=message&id_message=member_modifyuser_failed_passwordcheck&info");
        }
        if (password_verify($pub_old_password, $user_info[0]["user_password"])) {
            redirection("index.php?action=message&id_message=member_modifyuser_failed_passwordcheck&info");
        }
        if (!check_var($pub_new_password, "Password")) {
            redirection("index.php?action=message&id_message=member_modifyuser_failed_password&info");
        }
        $password_change_validated = true;
    }
    // Token Generation
    if ($pub_renew_user_token == 1) {

        user_profile_token_updater($user_id);
    }

    if (!check_var($pub_pseudo, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=member_modifyuser_failed_pseudo&info");
    }

    $player_id = $user_data["player_id"];
    $user_empire = player_get_empire($player_id);
    $user_technology = $user_empire["technology"];

    //pseudo ingame
    if ($user_data["player_id"] !== $pub_pseudo_ingame) {
        $User_Model->set_game_account_name($user_id, $pub_pseudo_ingame);
    }

    //Contrôle que le pseudo ne soit pas déjà utilisé si changement
    if ($User_Model->select_is_other_user_name($pub_pseudo, $user_id) === true) {
        redirection("index.php?action=message&id_message=member_modifyuser_failed_pseudolocked&info");
    }

    if (is_null($pub_disable_ip_check) || $pub_disable_ip_check != 1) {
        $pub_disable_ip_check = 0;
    }
    if (isset($pub_pseudo)) {
        $User_Model->set_user_pseudo($user_id, $pub_pseudo);
    }
    if (isset($pub_new_password) && $password_change_validated === true) {
        $User_Model->set_user_password($user_id, password_hash($pub_new_password, PASSWORD_DEFAULT), 0);
    }
    if (isset($pub_pseudo_email)) {
        $User_Model->set_user_email($user_id, $pub_pseudo_email);
    }
    if (isset($pub_galaxy)) {
        $User_Model->set_user_default_galaxy($user_id, $pub_galaxy);
    }
    if (isset($pub_system)) {
        $User_Model->set_user_default_system($user_id, $pub_system);
    }
    if (isset($pub_disable_ip_check)) {
        $User_Model->set_user_ip_check($user_id, $pub_disable_ip_check);
    }
    redirection("index.php?action=profile");
}

/**
 * Update the PAT on the user request
 * @param $user_id
 * @return array
 * @throws Exception
 */
function user_profile_token_updater($user_id)
{
    //todo mettre dans un helper ( poru réutilisation generate password / id ogspy (parameters) , token login, ... )
    global $user_token;

    $new_token = bin2hex(random_bytes(32));
    $next_year = time() + (365 * 24 * 60 * 60);

    $Tokens_Model = new Tokens_Model();
    $user_token["token"] = $Tokens_Model->add_token($new_token, $user_id, $next_year, "PAT");
}

/**
 * Get the PAT on the user request
 * @param $user_id
 * @return array
 * @throws Exception
 */
function get_user_profile_token($user_id)
{
    $Tokens_Model = new Tokens_Model();
    $token = $Tokens_Model->get_token($user_id, "PAT");
    if (!$token) {
        return 1;
    }
    return $token;
}

/**
 * Entree en BDD de donnees utilisateur
 * @param $user_id
 * @param null $user_name
 * @param null $user_password_s
 * @param null $user_email
 * @param null $user_lastvisit
 * @param null $user_galaxy
 * @param null $user_system
 * @param integer $disable_ip_check
 */
function user_set_general(
    $user_id,
    $user_name = null,
    $user_password_s = null,
    $user_email = null,
    $user_lastvisit = null,
    $user_galaxy = null,
    $user_system = null,
    $disable_ip_check = null
) {
    global $user_data, $server_config;
    $User_Model = new User_Model();


    if (!isset($user_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    if (!empty($user_galaxy)) {
        $user_galaxy = intval($user_galaxy);
        if ($user_galaxy < 1 || $user_galaxy > intval($server_config['num_of_galaxies'])) {
            $user_galaxy = 1;
        }
    }
    if (!empty($user_system)) {
        $user_system = intval($user_system);
        if ($user_system < 1 || $user_system > intval($server_config['num_of_systems'])) {
            $user_system = 1;
        }
    }

    // verification ok, modification bdd possible

    //Pseudo et mot de passe
    if (!empty($user_name)) {
        $User_Model->set_user_pseudo($user_id, $user_name);
    }
    if (!empty($user_password_s)) {
        $User_Model->set_user_password($user_id, password_hash($user_password_s, PASSWORD_DEFAULT));
    }

    //Galaxy et système solaire du membre
    if (!empty($user_galaxy)) {
        $User_Model->set_user_default_galaxy($user_id, $user_galaxy);
    }
    if (!empty($user_system)) {
        $User_Model->set_user_default_system($user_id, $user_system);
    }

    //Dernière visite
    if (!empty($user_lastvisit)) {
        $User_Model->update_lastvisit_time($user_id);
    }

    //Email
    if (!empty($user_email)) {
        $User_Model->set_user_email($user_id, $user_email);
    }

    //Désactivation de la vérification de l'adresse ip
    if (!is_null($disable_ip_check)) {
        $User_Model->set_user_ip_check($user_id, $disable_ip_check);
    }

    if ($user_id == $user_data['id']) {
        log_("modify_account");
    } else {
        log_("modify_account_admin", $user_id);
    }
}

/**
 * Enregistrement des droits et status utilisateurs
 * @param $user_id
 * @param null $user_admin
 * @param null $user_active
 * @param null $user_coadmin
 * @param null $management_user
 * @param null $management_ranking
 *
 * todo : ajouter la possibilité de changer admin prinicpal '$useradmin non utilisé ....
 */

function user_set_grant(
    $user_id,
    $user_admin = null,
    $user_active = null,
    $user_coadmin = null,
    $management_user = null,
    $management_ranking = null
) {
    global $user_data;

    if (!isset($user_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }
    //Vérification des droits
    user_check_auth("user_update", $user_id);
    $data_user = new User_Model();
    //Activation membre
    if (!is_null($user_active)) {
        $data_user->set_user_active($user_id, intval($user_active));
        if (intval($user_active) == 0) {
            $data_session = new Sessions_Model();
            $data_session->close_user_session($user_id);
        }
    }
    //Co-administration
    if (!is_null($user_coadmin)) {
        $data_user->set_user_coadmin($user_id, intval($user_coadmin));
    }
    //Gestion des membres
    if (!is_null($management_user)) {
        $data_user->set_user_management_user($user_id, intval($management_user));
    }
    //Gestion des classements
    if (!is_null($management_ranking)) {
        $data_user->set_user_management_ranking($user_id, intval($management_ranking));
    }
    if ($user_id == $user_data['id']) {
        log_("modify_account");
    } else {
        log_("modify_account_admin", $user_id);
    }
}

/**
 * Enregistrement des statistiques utilisateurs
 * @param null $planet_imports
 * @param null $planet_imports
 * @param integer $search
 * @param null $spy_imports
 * @param null $spy_imports
 * @param null $rank_imports
 * @param null $rank_imports
 * @param null $planet_exported
 * @param null $spy_exported
 * @param null $rank_exported
 */
function user_set_stat($planet_imports = null, $search = null, $spy_imports = null, $rank_imports = null
) {
    global $user_data;

    $User_Model = new User_Model();
    //Statistiques envoi systèmes solaires et rapports d'espionnage

    if (!is_null($planet_imports)) {
        $User_Model->add_stat_planet_inserted($user_data["id"], $planet_imports);
    }
    if (!is_null($search)) {
        $User_Model->add_stat_search_made($user_data["id"], $search);
    }
    if (!is_null($spy_imports)) {
        $User_Model->add_stat_spy_inserted($user_data["id"], $spy_imports);
    }
    if (!is_null($rank_imports)) {
        $User_Model->add_stat_rank_inserted($user_data["id"], $rank_imports);
    }
}

/**
 * Recuperation d'une ligne d'information utilisateur
 * @param bool|int $user_id Identificateur optionnel d'1 utilisateur specifique
 * @return array Liste des utilisateurs ou de l'utilisateur specifique
 */
function user_get($user_id = null)
{
    $User_Model = new User_Model();
    if (isset($user_id)) {
        $info_users = $User_Model->select_user_data($user_id);
    } else {
        $info_users = $User_Model->select_all_user_data();
    }
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
    if ($user_info["admin"] == 1 || $user_info["coadmin"] == 1) {
        $user_auth = array(
            "server_set_system" => 1,
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
            "ogs_get_ranking" => 1
        );
        return $user_auth;
    }
    $User_Model = new User_Model();
    $user_auth = $User_Model->select_user_rights($user_id);
    return $user_auth;
}

/**
 * Creation d'un utilisateur a partir des donnees du formulaire admin
 * @comment redirection si erreur de type de donnee
 */
function user_create()
{
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
    $User_Model = new User_Model();

    //Création de l'utilisateur
    //On vérifie que le nom n'existe pas
    if ($User_Model->select_is_user_name($pub_pseudo) === false) {
        $user_id = $User_Model->add_new_user($pub_pseudo, $password);
        // Insertion dans le groupe par défaut
        $User_Model->add_user_to_group($user_id, $pub_group_id);
        $info = $user_id . ":" . $password;
        log_("create_account", $user_id);
        user_set_grant(
            $user_id,
            null,
            $pub_active,
            $pub_user_coadmin,
            $pub_management_user,
            $pub_management_ranking
        );

        redirection("index.php?action=message&id_message=createuser_success&info=$info");
    } else {
        redirection("index.php?action=message&id_message=createuser_failed_pseudolocked&info=$pub_pseudo");
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

    log_("delete_account", $pub_user_id);

    (new User_Model())->delete_user($pub_user_id);

    session_close($pub_user_id);

    redirection("index.php?action=administration&subaction=member");
}

/**
 * Recuperation des statistiques
 */
function user_statistic()
{
    $all_user_stats_data = (new User_Model())->select_all_user_stats_data();
    $user_statistic = array();
    foreach ($all_user_stats_data as $row) {
        $here = "";
        $session_type = (new Sessions_Model())->get_xtense_session($row["id"]);
        if ($session_type != -1) {
            $here = "(*)";
            if ($session_type == 1) {
                $here = "(**)";
            }
        }

        $user_statistic[] = array_merge($row, array("here" => $here));
    }
    return $user_statistic;
}

/**
 * Recuperation du nombres de comptes actifs
 */
function user_get_nb_active_users()
{
    $number = (new User_Model())->get_nb_active_users();
    return ($number);
}


/**
 * Ajout d'un système favori
 */
function user_add_favorite()
{
    global $user_data, $server_config;
    global $pub_galaxy, $pub_system;

    $User_Favorites_Model = new User_Favorites_Model();

    if (!isset($pub_galaxy) || !isset($pub_system)) {
        redirection("index.php");
    }
    if (
        intval($pub_galaxy) < 1 || intval($pub_galaxy) > intval($server_config['num_of_galaxies']) ||
        intval($pub_system) < 1 || intval($pub_system) > intval($server_config['num_of_systems'])
    ) {
        redirection("index.php?action=galaxy");
    }

    $nb_favorites = $User_Favorites_Model->get_nb_user_favorites($user_data["id"]);
    if ($nb_favorites < $server_config["max_favorites"]) {
        $User_Favorites_Model->set_user_favorites($user_data["id"], $pub_galaxy, $pub_system);
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
    if (
        intval($pub_galaxy) < 1 || intval($pub_galaxy) > intval($server_config['num_of_galaxies']) ||
        intval($pub_system) < 1 || intval($pub_system) > intval($server_config['num_of_systems'])
    ) {
        redirection("index.php?action=galaxy");
    }

    //suppression
    (new User_Favorites_Model())->delete_user_favorites($user_data["id"], $pub_galaxy, $pub_system);

    redirection("index.php?action=galaxy&galaxy=" . $pub_galaxy . "&system=" . $pub_system .
        "");
}

/**
 * Récupération des rapports favoris
 */
function user_getfavorites_spy()
{
    global $user_data;
    global $sort, $sort2;

    $Spy_Model = new Spy_Model();
    if (!is_numeric($sort) || !is_numeric($sort2)) {
        //Ordering by date Desc
        $sort = 5;
        $sort2 = 0;
    }
    return $Spy_Model->get_favoriteSpyList($user_data["id"], $sort, $sort2);
}


/**
 * Ajout d'un rapport favori
 */
function user_add_favorite_spy()
{
    global $user_data, $server_config;
    global $pub_spy_id, $pub_galaxy, $pub_system, $pub_row;

    $User_Spy_favorites_Model = new User_Spy_favorites_Model();

    if (!check_var($pub_spy_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_spy_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $nb_favorites = $User_Spy_favorites_Model->Count_favorite_spy($user_data["id"]);
    if ($nb_favorites < $server_config["max_favorites_spy"]) {
        $User_Spy_favorites_Model->add_favorite_spy($user_data["id"], $pub_spy_id);
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
    //(new Spy_Model())->delete_spy_senderId($pub_spy_id, $user_data["id"]);
    (new User_Spy_favorites_Model())->delete_favorite_spy($user_data["id"], $pub_spy_id);

    if (!isset($pub_info)) {
        $pub_info = 1;
    }

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
}

/**
 * Création d'un groupe
 */
function usergroup_create()
{
    global $pub_groupname;
    $Group_Model = new Group_Model();

    if (!isset($pub_groupname)) {
        redirection("index.php?action=message&id_message=createusergroup_failed_general&info");
    }

    //Vérification des droits
    user_check_auth("usergroup_manage");

    if (!check_var($pub_groupname, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=createusergroup_failed_groupname&info");
    }


    if (!$Group_Model->group_exist_by_name($pub_groupname)) {
        $Group_Model->insert_group($pub_groupname);
        $group_id = $Group_Model->sql_insertid();

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

    (new Group_Model())->delete_group($pub_group_id);

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

    if (intval($group_id) == 0 && $group_id !== false) {
        die("return false");
        return false;
    }

    $Group_Model = new Group_Model();

    //demande de tous les groupes
    if (!$group_id) {
        $info_usergroup = $Group_Model->get_all_group_rights();
    } else {
        $info_usergroup = $Group_Model->get_group_rights($group_id);
    }

    if (sizeof($info_usergroup) == 0) {
        return false;
    }

    return $info_usergroup;
}

/**
 * Enregistrement des droits d'un groupe utilisateurs
 */
function usergroup_setauth()
{
    global $pub_group_id, $pub_group_name, $pub_server_set_system, $pub_server_set_spy,
        $pub_server_set_rc, $pub_server_set_ranking, $pub_server_show_positionhided, $pub_ogs_connection,
        $pub_ogs_set_system, $pub_ogs_get_system, $pub_ogs_set_spy, $pub_ogs_get_spy, $pub_ogs_set_ranking,
        $pub_ogs_get_ranking;

    if (!check_var($pub_group_id, "Num") || !check_var(
        $pub_group_name,
        "Pseudo_Groupname"
    ) || !check_var($pub_server_set_system, "Num") || !check_var(
        $pub_server_set_spy,
        "Num"
    ) || !check_var($pub_server_set_rc, "Num") || !check_var(
        $pub_server_set_ranking,
        "Num"
    ) || !check_var($pub_server_show_positionhided, "Num") || !check_var(
        $pub_ogs_connection,
        "Num"
    ) || !check_var($pub_ogs_set_system, "Num") || !check_var(
        $pub_ogs_get_system,
        "Num"
    ) || !check_var($pub_ogs_set_spy, "Num") || !check_var(
        $pub_ogs_get_spy,
        "Num"
    ) || !check_var($pub_ogs_set_ranking, "Num") || !check_var(
        $pub_ogs_get_ranking,
        "Num"
    )) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_group_id) || !isset($pub_group_name)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $pub_server_set_system = $pub_server_set_system ?? 0;
    $pub_server_set_spy = $pub_server_set_spy ?? 0;
    $pub_server_set_rc = $pub_server_set_rc ?? 0;
    $pub_server_set_ranking = $pub_server_set_ranking ?? 0;
    $pub_server_show_positionhided = $pub_server_show_positionhided ?? 0;
    $pub_ogs_connection = $pub_ogs_connection ?? 0;
    $pub_ogs_set_system = $pub_ogs_set_system ?? 0;
    $pub_ogs_get_system = $pub_ogs_get_system ?? 0;
    $pub_ogs_set_spy = $pub_ogs_set_spy ?? 0;
    $pub_ogs_get_spy = $pub_ogs_get_spy ?? 0;
    $pub_ogs_set_ranking = $pub_ogs_set_ranking ?? 0;
    $pub_ogs_get_ranking = $pub_ogs_get_ranking ?? 0;
    //Vérification des droits

    user_check_auth("usergroup_manage");

    log_("modify_usergroup", $pub_group_id);

    (new Group_Model())->update_group(
        $pub_group_id,
        $pub_group_name,
        $pub_server_set_system,
        $pub_server_set_spy,
        $pub_server_set_rc,
        $pub_server_set_ranking,
        $pub_server_show_positionhided,
        $pub_ogs_connection,
        $pub_ogs_set_system,
        $pub_ogs_get_system,
        $pub_ogs_set_spy,
        $pub_ogs_get_spy,
        $pub_ogs_set_ranking,
        $pub_ogs_get_ranking
    );

    redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
}

/**
 * Récupération des utilisateurs appartenant à un groupe
 * @param int $group_id Identificateur du groupe demandé
 * @return Array Liste des utilisateurs
 */
function usergroup_member($group_id)
{
    if (!isset($group_id) || !is_numeric($group_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $usergroup_member = (new Group_Model())->get_user_list($group_id);
    return $usergroup_member;
}

/**
 * Ajout d'un utilisateur à un groupe
 */
function usergroup_newmember()
{
    global $pub_user_id, $pub_group_id, $pub_add_all;

    $Group_Model = new Group_Model();
    $User_Model = new User_Model();
    $userid_list = $User_Model->select_userid_list();

    if (isset($pub_add_all) && is_numeric($pub_group_id)) {
        foreach ($userid_list as $userid) {
            user_check_auth("usergroup_manage");
            //insertion
            if ($Group_Model->insert_user_togroup($userid, $pub_group_id)) {
                log_("add_usergroup", array($pub_group_id, $userid));
            }
        }
        redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
    } else {
        if (!check_var($pub_user_id, "Num") || !check_var($pub_group_id, "Num")) {
            redirection("index.php?action=message&id_message=errordata&info");
        }

        if (!isset($pub_user_id) || !isset($pub_group_id)) {
            redirection("index.php?action=message&id_message=errorfatal&info");
        }

        //Vérification des droits
        user_check_auth("usergroup_manage");

        if ($Group_Model->group_exist_by_id($pub_group_id) == false) {
            redirection("index.php?action=administration&subaction=group");
        }

        //si le compte n existe pas
        if (!in_array(intval($pub_user_id), $userid_list)) {
            redirection("index.php?action=administration&subaction=group");
        }

        //insertion
        if ($Group_Model->insert_user_togroup($pub_user_id, $pub_group_id)) {
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
    global $pub_user_id, $pub_group_id;

    $Group_Model = new Group_Model();

    if (!isset($pub_user_id) || !isset($pub_group_id)) {
        redirection("index.php?action=message&id_message=errorfatal&info");
    }
    if (!check_var($pub_user_id, "Num") || !check_var($pub_group_id, "Num")) {
        redirection("index.php?action=message&id_message=errordata&info");
    }

    //Vérification des droits
    user_check_auth("usergroup_manage");

    $Group_Model->delete_user_from_group($pub_user_id, $pub_group_id);
    if ($Group_Model->sql_affectedrows() > 0) {
        log_("del_usergroup", array($pub_group_id, $pub_user_id));
    }

    redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
}

/**
 * A quoi sert donc cette fonction ? :p
 * Reponse elle sert a mettre a jour le pseudo ingame afin d afficher les stats users dans son espace perso
 * @param $user_stat_name
 */
function user_set_stat_name($user_stat_name)
{
    global $user_data;
    (new User_Model())->set_game_account_name($user_data['id'], $user_stat_name);
}

//Suppression d'un rapport d'espionnage
/**
 * Deletes a Spy Report
 * @return null|boolean
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

    if ($user_data["admin"] == 1 || $user_data["coadmin"] == 1) {
        (new Spy_Model())->delete_spy($pub_spy_id);
    }

    if (!isset($pub_info)) {
        $pub_info = 1;
    }

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

    $data_user = new User_Model();
    $user_stat = $data_user->select_user_stats_data($player);
    $total_user_stats = $data_user->select_user_stats_sum();
    //$total_users = $data_user->get_nb_users();
    //pour éviter la division par zéro
    if ($total_user_stats["planetimporttotal"] == 0) {
        $total_user_stats["planetimporttotal"] = 1;
    }
    if ($total_user_stats["spyimporttotal"] == 0) {
        $total_user_stats["spyimporttotal"] = 1;
    }
    if ($total_user_stats["rankimporttotal"] == 0) {
        $total_user_stats["rankimporttotal"] = 1;
    }
    if ($total_user_stats["searchtotal"] == 0) {
        $total_user_stats["searchtotal"] = 1;
    }
    //et on commence le calcul
    $ratio_planet = $user_stat["planet_added_xtense"] / $total_user_stats["planetimporttotal"];
    $ratio_spy = $user_stat["spy_added_xtense"] / $total_user_stats["spyimporttotal"];
    $ratio_rank = $user_stat["rank_added_xtense"] / $total_user_stats["rankimporttotal"];
    $ratio = ($ratio_planet * 4 + $ratio_spy * 2 + $ratio_rank) / (4 + 2 + 1);
    $ratio_planet_penality = $user_stat["planet_added_xtense"] / $total_user_stats["planetimporttotal"];
    $ratio_spy_penality = $user_stat["spy_added_xtense"] / $total_user_stats["spyimporttotal"];
    $ratio_rank_penality = $user_stat["rank_added_xtense"] / $total_user_stats["rankimporttotal"];
    $ratio_penality = ($ratio_planet_penality * 4 + $ratio_spy_penality * 2 + $ratio_rank_penality) / (4 +
        2 + 1);
    $ratio_search = $user_stat["search"] / $total_user_stats["searchtotal"];
    $ratio_searchpenality = ($ratio - $ratio_search);
    $result = ($ratio + $ratio_penality + $ratio_searchpenality) * 1000;
    $array = array(
        $result, $ratio_searchpenality, $ratio_search, $ratio_penality, $ratio_rank_penality,
        $ratio_spy_penality, $ratio_planet_penality
    );
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

    if ($result != null) {
        return $result;
    }
    if (isset($server_config["block_ratio"]) && $server_config["block_ratio"] == 1) {
        if (
            $user_data["admin"] == 1 || $user_data["coadmin"] == 1 || $user_data["management_user"] == 1) {
            return true;
        } else {
            $result = ratio_calc($user_data['id']);
            $result = $result[0] >= $server_config["ratio_limit"];
            return $result;
        }
    } else {
        return true;
    }
}
