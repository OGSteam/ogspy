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
use Ogsteam\Ogspy\Model\Statistics_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Ogsteam\Ogspy\Model\Spy_Model;
use Ogsteam\Ogspy\Model\Tokens_Model;

require_once __DIR__ . '/token.php';


/**
 * Checks if the current user has authorization to perform the specified action.
 *
 * @param string $action The action to check authorization for (e.g., "user_create", "user_update").
 * @param int $user_id The user ID associated with the action (optional).
 * @return void
 */
function user_check_auth($action, $user_id = null)
{
    global $user_data, $log;

    $log->debug("Starting user authorization check", [
        'action' => $action,
        'user_id' => $user_id,
        'current_user' => $user_data['user_id'] ?? 'unknown',
        'admin' => $user_data["admin"] ?? 'undefined',
        'coadmin' => $user_data["coadmin"] ?? 'undefined',
        'management_user' => $user_data["management_user"] ?? 'undefined'
    ]);

    switch ($action) {
        case "user_create":
        case "usergroup_manage":
            $log->debug("Checking permissions for user creation/group management", ['action' => $action]);

            if (
                $user_data["admin"] != 1 && $user_data["coadmin"] != 1 && $user_data["management_user"] !=
                1
            ) {
                $log->warning("Access denied for user creation/group management", [
                    'action' => $action,
                    'user_id' => $user_data['user_id'] ?? 'unknown',
                    'admin' => $user_data["admin"] ?? 'undefined',
                    'coadmin' => $user_data["coadmin"] ?? 'undefined',
                    'management_user' => $user_data["management_user"] ?? 'undefined'
                ]);
                redirection("index.php?action=message&id_message=forbidden&info");
            }

            $log->info("User creation/group management access granted", [
                'action' => $action,
                'user_id' => $user_data['user_id'] ?? 'unknown'
            ]);
            break;

        case "user_update":
            $log->debug("Checking permissions for user update", ['action' => $action, 'target_user_id' => $user_id]);

            if (
                $user_data["admin"] != 1 && $user_data["coadmin"] != 1 && $user_data["management_user"] !=
                1
            ) {
                $log->warning("Basic access denied for user update", [
                    'action' => $action,
                    'user_id' => $user_data['user_id'] ?? 'unknown',
                    'target_user_id' => $user_id,
                    'admin' => $user_data["admin"] ?? 'undefined',
                    'coadmin' => $user_data["coadmin"] ?? 'undefined',
                    'management_user' => $user_data["management_user"] ?? 'undefined'
                ]);
                redirection("index.php?action=message&id_message=forbidden&info");
            }

            $info_user = user_get($user_id);
            if ($info_user === false) {
                $log->error("Target user not found for update", [
                    'action' => $action,
                    'target_user_id' => $user_id,
                    'current_user' => $user_data['user_id'] ?? 'unknown'
                ]);
                redirection("index.php?action=message&id_message=deleteuser_failed&info");
            }

            $log->debug("Target user information retrieved", [
                'target_user_id' => $user_id,
                'target_admin' => $info_user[0]["admin"] ?? 'undefined',
                'target_coadmin' => $info_user[0]["coadmin"] ?? 'undefined',
                'target_management_user' => $info_user[0]["management_user"] ?? 'undefined'
            ]);

            if (($user_data["admin"] != 1 &&
                $user_data["coadmin"] != 1 &&
                $user_data["management_user"] != 1) || ($info_user[0]["admin"] == 1) || (($user_data["coadmin"] == 1) &&
                ($info_user[0]["coadmin"] == 1)) || (($user_data["coadmin"] != 1 &&
                $user_data["management_user"] == 1) &&
                ($info_user[0]["coadmin"] == 1 || $info_user[0]["management_user"] == 1))) {

                $log->critical("Privilege escalation attempt detected", [
                    'action' => $action,
                    'current_user' => $user_data['user_id'] ?? 'unknown',
                    'target_user_id' => $user_id,
                    'current_admin' => $user_data["admin"] ?? 'undefined',
                    'current_coadmin' => $user_data["coadmin"] ?? 'undefined',
                    'current_management' => $user_data["management_user"] ?? 'undefined',
                    'target_admin' => $info_user[0]["admin"] ?? 'undefined',
                    'target_coadmin' => $info_user[0]["coadmin"] ?? 'undefined',
                    'target_management' => $info_user[0]["management_user"] ?? 'undefined'
                ]);
                redirection("index.php?action=message&id_message=forbidden&info");
            }

            $log->info("User update access granted", [
                'action' => $action,
                'current_user' => $user_data['user_id'] ?? 'unknown',
                'target_user_id' => $user_id
            ]);
            break;

        default:
            $log->error("Unknown authorization action requested", [
                'action' => $action,
                'user_id' => $user_data['user_id'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $log->debug("User authorization check completed successfully", [
        'action' => $action,
        'user_id' => $user_data['user_id'] ?? 'unknown'
    ]);
}

/**
 * Handles the user login process.
 *
 * @return void
 */
function user_login()
{
    global $pub_login, $pub_password, $pub_goto, $pub_token, $log;

    $userModel = new User_Model();

    $log->info("Starting user login attempt", [
        'username' => $pub_login ?? 'undefined',
        'goto_destination' => $pub_goto ?? 'undefined',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!token::statiCheckToken($pub_token)) {
        $log->warning("Login attempt with invalid token", [
            'username' => $pub_login ?? 'undefined',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!check_var($pub_login, "Pseudo_Groupname") || !check_var(
        $pub_password,
        "Password"
    ) || !check_var($pub_goto, "Special", "#^[\w=&%+]+$#")) {
        $log->warning("Login attempt with invalid data format", [
            'username' => $pub_login ?? 'undefined',
            'login_valid' => check_var($pub_login, "Pseudo_Groupname"),
            'password_valid' => check_var($pub_password, "Password"),
            'goto_valid' => check_var($pub_goto, "Special", "#^[\w=&%+]+$#"),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_login) || !isset($pub_password)) {
        $log->error("Login attempt with missing credentials", [
            'username_set' => isset($pub_login),
            'password_set' => isset($pub_password),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    try {
        $tlogin = $userModel->select_user_login($pub_login, $pub_password);

        // si retour
        if ($tlogin) {
            $log->debug("User found in database", [
                'username' => $pub_login,
                'user_id' => $tlogin['id'],
                'user_active' => $tlogin['active']
            ]);

            if (password_verify($pub_password, $tlogin['password_s'])) {
                // Format Mot de passe Secure
                $log->info("Authentication successful", [
                    'username' => $pub_login,
                    'user_id' => $tlogin['id'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
                user_set_connection($tlogin['id'], $tlogin['active']);
            } else {
                $log->warning("Authentication failed - incorrect password", [
                    'username' => $pub_login,
                    'user_id' => $tlogin['id'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
                redirection("index.php?action=message&id_message=login_wrong&info");
            }
        } else {
            $log->warning("Authentication failed - user not found", [
                'username' => $pub_login,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=login_wrong&info");
        }
    } catch (Exception $e) {
        $log->error("Database error during login attempt", [
            'username' => $pub_login,
            'error' => $e->getMessage(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }
}

/**
 * Sets up a user's connection based on their active status.
 *
 * @param int $user_id The ID of the user.
 * @param bool $user_active Indicates whether the user's account is active.
 * @return void
 */
function user_set_connection($user_id, $user_active)
{
    global $pub_goto, $log;

    $log->debug("Starting user connection setup", [
        'user_id' => $user_id,
        'user_active' => $user_active,
        'goto_destination' => $pub_goto ?? 'undefined'
    ]);

    try {
        (new User_Model())->update_lastvisit_time($user_id);
        $log->debug("Last visit time updated successfully", ['user_id' => $user_id]);
    } catch (Exception $e) {
        $log->error("Failed to update last visit time", [
            'user_id' => $user_id,
            'error' => $e->getMessage()
        ]);
    }

    if ($user_active) {
        try {
            $lastvisit = (new User_Model())->select_last_visit($user_id);
            $log->debug("Last visit retrieved", ['user_id' => $user_id, 'last_visit' => $lastvisit]);

            ///stat
            (new Statistics_Model())->add_user_connection();
            $log->debug("Connection statistics updated");

            $log->info("User connection established successfully", [
                'user_id' => $user_id,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            session_set_user_id($user_id, $lastvisit);
            $log->debug("User session created", ['user_id' => $user_id]);

            if (!isset($url_append)) {
                $url_append = "";
            }

            $log->info("Redirecting authenticated user", [
                'user_id' => $user_id,
                'destination' => $pub_goto ?? 'undefined'
            ]);

            redirection("index.php?action=" . $pub_goto . $url_append);
        } catch (Exception $e) {
            $log->error("Error during user connection setup", [
                'user_id' => $user_id,
                'error' => $e->getMessage()
            ]);
            redirection("index.php?action=message&id_message=errorfatal&info");
        }
    } else {
        $log->warning("User connection rejected - account locked", [
            'user_id' => $user_id,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
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
        $log->info("Login redirection to galaxy: " . $pub_galaxy . ", system: " . $pub_system);
        $url_append = "&galaxy=" . $pub_galaxy . "&system=" . $pub_system;
        user_login();
    } else {
        $log->info("Login redirection to: " . $pub_goto);
        user_login();
    }
}

/**
 * Deconnection utilisateur
 */
function user_logout()
{
    global $user_data, $log;

    $log->info("Starting user logout process", [
        'user_id' => $user_data["id"] ?? 'unknown',
        'username' => $user_data["name"] ?? 'unknown',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);

    if (isset($user_data["id"])) {
        $log->info("User logout successful", [
            'user_id' => $user_data["id"],
            'username' => $user_data["name"] ?? 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } else {
        $log->warning("Logout attempt from unidentified user", [
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }

    try {
        session_close();
        $log->debug("Session closed successfully");
    } catch (Exception $e) {
        $log->error("Error closing session during logout", [
            'error' => $e->getMessage(),
            'user_id' => $user_data["id"] ?? 'unknown'
        ]);
    }

    $log->info("User logout process completed", [
        'user_id' => $user_data["id"] ?? 'unknown'
    ]);

    redirection("index.php");
}

/**
 * Création d'un utilisateur à partir des données du formulaire admin
 * @comment redirection si erreur de type de donnée
 */
function user_create()
{
    global $pub_pseudo, $pub_active, $pub_user_coadmin, $pub_management_user,
        $pub_management_ranking, $pub_group_id, $pub_pass, $pub_email, $log;

    if (!check_var($pub_pseudo, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=errordata&info=1");
    }

    if (!isset($pub_pseudo)) {
        redirection("index.php?action=message&id_message=createuser_failed_general&info");
    }

    //Vérification des droits
    user_check_auth("user_create");

    if (!check_var($pub_pseudo, "Pseudo_Groupname")) {
        redirection("index.php?action=message&id_message=createuser_failed_pseudo&info=" . $pub_pseudo);
    }

    if (!check_var($pub_pass, "Password")) {
        redirection("index.php?action=message&id_message=createuser_failed_password&info=" . $pub_pseudo);
    }

    if (!check_var($pub_email, "Email")) {
        redirection("index.php?action=message&id_message=createuser_failed_password&info=" . $pub_email);
    }

    if ($pub_pass != "") {
        $password = $pub_pass;
    } else {
        $password = generateRandomPassword();
    }

    $userModel = new User_Model();

    //Création de l'utilisateur
    //On vérifie que le nom n'existe pas
    if ($userModel->select_is_user_name($pub_pseudo) === false) {
        $user_id = $userModel->add_new_user($pub_pseudo, $password);

        // Ajout de l'email séparément
        if (!empty($pub_email)) {
            $userModel->set_user_email($user_id, $pub_email);
        }

        // Insertion dans le groupe par défaut
        $userModel->add_user_to_group($user_id, $pub_group_id);

        $info = $user_id . ":" . $password;
        $log->debug("User account created successfully", [
            'type' => 'create_account',
            'user_id' => $user_id,
            'username' => $pub_pseudo
        ]);

        user_set_grant($user_id, null, $pub_active, $pub_user_coadmin, $pub_management_user, $pub_management_ranking);

        redirection("index.php?action=message&id_message=createuser_success&info=" . $info);
    } else {
        redirection("index.php?action=message&id_message=createuser_failed_pseudolocked&info=" . $pub_pseudo);
    }
}

/**
 * Modification des droits ogspy d'un utilisateur par l'admin
 */
function admin_user_set()
{
    global $user_data, $log;
    global $pub_user_id, $pub_active, $pub_user_coadmin, $pub_management_user, $pub_management_ranking;

    $log->info("Starting admin user privileges modification", [
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'target_user_id' => $pub_user_id ?? 'undefined',
        'new_active' => $pub_active ?? 'undefined',
        'new_coadmin' => $pub_user_coadmin ?? 'undefined',
        'new_management_user' => $pub_management_user ?? 'undefined',
        'new_management_ranking' => $pub_management_ranking ?? 'undefined',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (
        !check_var($pub_user_id, "Num") ||
        !check_var($pub_active, "Num") ||
        !check_var($pub_user_coadmin, "Num") ||
        !check_var($pub_management_user, "Num") ||
        !check_var($pub_management_ranking, "Num")
    ) {
        $log->warning("Admin user set failed - invalid data format", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id ?? 'undefined',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_user_id) || !isset($pub_active)) {
        $log->error("Admin user set failed - missing required data", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'user_id_set' => isset($pub_user_id),
            'active_set' => isset($pub_active),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
    }

    //Vérification des droits
    user_check_auth("user_update", $pub_user_id);

    if ($user_data["admin"]) {
        if (!isset($pub_user_coadmin) || !isset($pub_management_user) || !isset($pub_management_ranking)) {
            $log->error("Admin user set failed - missing admin privileges data", [
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
        }
    } elseif ($user_data["coadmin"]) {
        $pub_user_coadmin = null;
        if (!isset($pub_management_user) || !isset($pub_management_ranking)) {
            $log->error("Coadmin user set failed - missing management privileges data", [
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
        }
    } else {
        $pub_user_coadmin = $pub_management_user = null;
    }

    if (user_get($pub_user_id) === false) {
        $log->error("Admin user set failed - target user not found", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=admin_modifyuser_failed&info");
    }

    $log->info("Admin user privileges modification successful", [
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'target_user_id' => $pub_user_id,
        'new_active' => $pub_active,
        'new_coadmin' => $pub_user_coadmin,
        'new_management_user' => $pub_management_user,
        'new_management_ranking' => $pub_management_ranking,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

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
    global $pub_user_id, $lang, $server_config, $log, $user_data;
    $pass_id = "pub_pass_" . $pub_user_id;
    global $$pass_id;
    $new_pass = $$pass_id;

    $log->info("Starting admin password regeneration", [
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'target_user_id' => $pub_user_id ?? 'undefined',
        'custom_password_provided' => !empty($new_pass),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!check_var($pub_user_id, "Num")) {
        $log->warning("Password regeneration failed - invalid user ID format", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id ?? 'undefined',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_user_id)) {
        $log->error("Password regeneration failed - missing user ID", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    user_check_auth("user_update", $pub_user_id);
    $user_info = user_get($pub_user_id)[0];

    if ($user_info === false) {
        $log->error("Password regeneration failed - target user not found", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=regeneratepwd_failed&info");
    }

    if ($new_pass != "") {
        $password = $new_pass;
        $log->debug("Using custom password provided by admin", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id
        ]);
    } else {
        $password = generateRandomPassword();
        $log->debug("Generated random password", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id
        ]);
    }

    user_set_general($pub_user_id, null, $password);

    $NovisualisationMdpAdmin = true;
    if ($server_config["mail_use"] == 1 && $user_info["user_email"] !== "") {
        $log->info("Attempting to send password reset email", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id,
            'target_email' => $user_info["user_email"]
        ]);

        $NovisualisationMdpAdmin = sendMail($user_info["user_email"], $lang['MAIL_RESET_PASSWORD_SUBJECT'], "<h1>" . $lang['MAIL_RESET_PASSWORD_MESSAGE'] . $password . "</h1>");

        if ($NovisualisationMdpAdmin) {
            $log->info("Password reset email sent successfully", [
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'target_email' => $user_info["user_email"]
            ]);
        } else {
            $log->warning("Failed to send password reset email", [
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'target_email' => $user_info["user_email"]
            ]);
        }
    } else {
        $log->debug("Email not sent - mail disabled or no email address", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id,
            'mail_enabled' => $server_config["mail_use"] == 1,
            'email_available' => !empty($user_info["user_email"])
        ]);
        // pas d'usage de mail donc visualisation admin à affecter
        $NovisualisationMdpAdmin = false;
    }

    if ($NovisualisationMdpAdmin == false) {
        $log->info("Password regeneration completed - password will be displayed to admin", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id,
            'type' => 'regeneratepwd'
        ]);
        $info = $pub_user_id . ":" . $password;
        redirection("index.php?action=message&id_message=regeneratepwd_success&info=" . $info);
    } else {
        $log->info("Password regeneration completed - password sent via email", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $pub_user_id,
            'type' => 'regeneratepwd_mail'
        ]);
        $info = $pub_user_id . ":mail";
        redirection("index.php?action=message&id_message=regeneratepwd_success&info=$info");
    }
}

/**
 * Modification du profil par un utilisateur
 * @todo Query : x11
 */
function member_user_set()
{
    global $user_data, $user_technology, $log;
    global $pub_pseudo, $pub_old_password, $pub_new_password, $pub_new_password2, $pub_galaxy,
        $pub_system, $pub_disable_ip_check,
        $pub_pseudo_ingame, $pub_pseudo_email, $pub_renew_user_token;

    $user_id = $user_data["id"];

    $log->info("Starting user profile modification", [
        'user_id' => $user_id,
        'username' => $user_data["name"] ?? 'unknown',
        'password_change_requested' => !empty($pub_old_password) || !empty($pub_new_password),
        'token_renewal_requested' => $pub_renew_user_token == 1,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

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
        $log->warning("Profile modification failed - invalid data format", [
            'user_id' => $user_id,
            'username' => $user_data["name"] ?? 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    $userModel = new User_Model();
    $user_info = user_get($user_id);

    $player_id = $user_data["player_id"];
    $user_empire = player_get_empire($player_id);
    $user_technology = $user_empire["technology"];

    $password_change_validated = false;
    // Validation du changement de mot de passe
    if ($pub_old_password != "" || $pub_new_password != "" || $pub_new_password2 != "") {
        $log->debug("Processing password change request", [
            'user_id' => $user_id,
            'username' => $user_data["name"] ?? 'unknown'
        ]);

        if ($pub_old_password == "" || $pub_new_password == "" || $pub_new_password != $pub_new_password2) {
            $log->warning("Password change failed - validation error", [
                'user_id' => $user_id,
                'username' => $user_data["name"] ?? 'unknown',
                'old_password_provided' => !empty($pub_old_password),
                'new_password_provided' => !empty($pub_new_password),
                'passwords_match' => $pub_new_password == $pub_new_password2,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=member_modifyuser_failed_passwordcheck&info");
        }

        if (!check_var($pub_new_password, "Password")) {
            $log->warning("Password change failed - new password format invalid", [
                'user_id' => $user_id,
                'username' => $user_data["name"] ?? 'unknown',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=member_modifyuser_failed_password&info");
        }

        $password_change_validated = true;
        $log->info("Password change validated successfully", [
            'user_id' => $user_id,
            'username' => $user_data["name"] ?? 'unknown'
        ]);
    }

    // Token Generation
    if ($pub_renew_user_token == 1) {
        $log->info("Renewing user token", [
            'user_id' => $user_id,
            'username' => $user_data["name"] ?? 'unknown'
        ]);
        user_profile_token_updater($user_id);
    }

    if (!check_var($pub_pseudo, "Pseudo_Groupname")) {
        $log->warning("Profile modification failed - invalid username format", [
            'user_id' => $user_id,
            'current_username' => $user_data["name"] ?? 'unknown',
            'requested_username' => $pub_pseudo ?? 'undefined',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=member_modifyuser_failed_pseudo&info");
    }

    $player_id = $user_data["player_id"];
    $user_empire = player_get_empire($player_id);
    $user_technology = $user_empire["technology"];

    //Contrôle que le pseudo ne soit pas déjà utilisé si changement
    if ($userModel->select_is_other_user_name($pub_pseudo, $user_id) === true) {
        $log->warning("Profile modification failed - username already taken", [
            'user_id' => $user_id,
            'current_username' => $user_data["name"] ?? 'unknown',
            'requested_username' => $pub_pseudo,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=member_modifyuser_failed_pseudolocked&info");
    }

    // Mise à jour des données
    if (is_null($pub_disable_ip_check) || $pub_disable_ip_check != 1) {
        $pub_disable_ip_check = 0;
    }

    $changes_made = [];

    if (isset($pub_pseudo)) {
        $userModel->set_user_pseudo($user_id, $pub_pseudo);
        $changes_made[] = 'username';
    }

    if (isset($pub_new_password) && $password_change_validated === true) {
        $userModel->set_user_password($user_id, password_hash($pub_new_password, PASSWORD_DEFAULT), 0);
        $changes_made[] = 'password';
    }

    if (isset($pub_pseudo_email)) {
        $userModel->set_user_email($user_id, $pub_pseudo_email);
        $changes_made[] = 'email';
    }

    if (isset($pub_galaxy)) {
        $userModel->set_user_default_galaxy($user_id, $pub_galaxy);
        $changes_made[] = 'galaxy';
    }

    if (isset($pub_system)) {
        $userModel->set_user_default_system($user_id, $pub_system);
        $changes_made[] = 'system';
    }

    if (isset($pub_disable_ip_check)) {
        $userModel->set_user_ip_check($user_id, $pub_disable_ip_check);
        $changes_made[] = 'ip_check';
    }

    $log->info("User profile modification completed successfully", [
        'user_id' => $user_id,
        'username' => $user_data["name"] ?? 'unknown',
        'changes_made' => $changes_made,
        'password_changed' => $password_change_validated,
        'token_renewed' => $pub_renew_user_token == 1,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

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
    global $user_token, $log;

    $log->info("Starting user token update", [
        'type' => 'user_token_update_attempt',
        'user_id' => $user_id,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!isset($user_id) || !is_numeric($user_id)) {
        $log->error("Token update failed - Invalid user ID", [
            'type' => 'user_token_update_failed',
            'reason' => 'invalid_user_id',
            'provided_id' => $user_id ?? 'null',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw new Exception("Invalid user ID for token update");
    }

    try {
        $new_token = bin2hex(random_bytes(32));
        $next_year = time() + (365 * 24 * 60 * 60);

        $log->debug("Generating new PAT token", [
            'type' => 'user_token_generate',
            'user_id' => $user_id,
            'token_length' => strlen($new_token),
            'expiry_date' => date('Y-m-d H:i:s', $next_year)
        ]);

        $Tokens_Model = new Tokens_Model();
        $result = $Tokens_Model->add_token($new_token, $user_id, $next_year, "PAT");

        $user_token["token"] = $result;

        $log->info("User token updated successfully", [
            'type' => 'user_token_updated_success',
            'user_id' => $user_id,
            'token_type' => 'PAT',
            'expiry_date' => date('Y-m-d H:i:s', $next_year),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $result;
    } catch (Exception $e) {
        $log->error("Error during user token update", [
            'type' => 'user_token_update_failed',
            'reason' => 'database_error',
            'user_id' => $user_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }
}

/**
 * Get the PAT on the user request
 * @param $user_id
 * @return array|int
 * @throws Exception
 */
function get_user_profile_token($user_id)
{
    global $log;

    $log->debug("Retrieving user token", [
        'type' => 'user_token_get_attempt',
        'user_id' => $user_id,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!isset($user_id) || !is_numeric($user_id)) {
        $log->warning("Token retrieval failed - Invalid user ID", [
            'type' => 'user_token_get_failed',
            'reason' => 'invalid_user_id',
            'provided_id' => $user_id ?? 'null',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        return 1;
    }

    try {
        $Tokens_Model = new Tokens_Model();
        $token = $Tokens_Model->get_token($user_id, "PAT");

        if (!$token) {
            $log->info("No PAT token found for user", [
                'type' => 'user_token_get_not_found',
                'user_id' => $user_id,
                'token_type' => 'PAT'
            ]);
            return 1;
        }

        $log->debug("User token retrieved successfully", [
            'type' => 'user_token_get_success',
            'user_id' => $user_id,
            'token_type' => 'PAT',
            'token_exists' => !empty($token),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $token;
    } catch (Exception $e) {
        $log->error("Error during user token retrieval", [
            'type' => 'user_token_get_failed',
            'reason' => 'database_error',
            'user_id' => $user_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }
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
    global $user_data, $server_config, $log;
    $userModel = new User_Model();

    $log->info("Starting user data modification", [
        'target_user_id' => $user_id,
        'current_user_id' => $user_data['id'] ?? 'unknown',
        'username_change' => !empty($user_name),
        'password_change' => !empty($user_password_s),
        'email_change' => !empty($user_email),
        'galaxy_change' => !empty($user_galaxy),
        'system_change' => !empty($user_system),
        'ip_check_change' => !is_null($disable_ip_check),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!isset($user_id)) {
        $log->error("User data modification failed - missing user ID", [
            'current_user_id' => $user_data['id'] ?? 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    // Validation des coordonnées galaxie/système
    if (!empty($user_galaxy)) {
        $original_galaxy = $user_galaxy;
        $user_galaxy = intval($user_galaxy);
        if ($user_galaxy < 1 || $user_galaxy > intval($server_config['num_of_galaxies'])) {
            $log->warning("Invalid galaxy coordinate provided, using default", [
                'target_user_id' => $user_id,
                'provided_galaxy' => $original_galaxy,
                'corrected_galaxy' => 1,
                'max_galaxies' => $server_config['num_of_galaxies']
            ]);
            $user_galaxy = 1;
        }
    }

    if (!empty($user_system)) {
        $original_system = $user_system;
        $user_system = intval($user_system);
        if ($user_system < 1 || $user_system > intval($server_config['num_of_systems'])) {
            $log->warning("Invalid system coordinate provided, using default", [
                'target_user_id' => $user_id,
                'provided_system' => $original_system,
                'corrected_system' => 1,
                'max_systems' => $server_config['num_of_systems']
            ]);
            $user_system = 1;
        }
    }

    $changes_made = [];

    // verification ok, modification bdd possible

    //Pseudo et mot de passe
    if (!empty($user_name)) {
        $userModel->set_user_pseudo($user_id, $user_name);
        $changes_made[] = 'username';
        $log->debug("Username updated", [
            'target_user_id' => $user_id,
            'new_username' => $user_name
        ]);
    }

    if (!empty($user_password_s)) {
        $userModel->set_user_password($user_id, password_hash($user_password_s, PASSWORD_DEFAULT));
        $changes_made[] = 'password';
        $log->info("Password updated", [
            'target_user_id' => $user_id,
            'current_user_id' => $user_data['id'] ?? 'unknown'
        ]);
    }

    //Galaxy et système solaire du membre
    if (!empty($user_galaxy)) {
        $userModel->set_user_default_galaxy($user_id, $user_galaxy);
        $changes_made[] = 'galaxy';
        $log->debug("Default galaxy updated", [
            'target_user_id' => $user_id,
            'new_galaxy' => $user_galaxy
        ]);
    }

    if (!empty($user_system)) {
        $userModel->set_user_default_system($user_id, $user_system);
        $changes_made[] = 'system';
        $log->debug("Default system updated", [
            'target_user_id' => $user_id,
            'new_system' => $user_system
        ]);
    }

    //Dernière visite
    if (!empty($user_lastvisit)) {
        $userModel->update_lastvisit_time($user_id);
        $changes_made[] = 'last_visit';
        $log->debug("Last visit time updated", ['target_user_id' => $user_id]);
    }

    //Email
    if (!empty($user_email)) {
        $userModel->set_user_email($user_id, $user_email);
        $changes_made[] = 'email';
        $log->debug("Email updated", [
            'target_user_id' => $user_id,
            'new_email' => $user_email
        ]);
    }

    //Désactivation de la vérification de l'adresse ip
    if (!is_null($disable_ip_check)) {
        $userModel->set_user_ip_check($user_id, $disable_ip_check);
        $changes_made[] = 'ip_check';
        $log->debug("IP check setting updated", [
            'target_user_id' => $user_id,
            'ip_check_disabled' => $disable_ip_check == 1
        ]);
    }

    if ($user_id == $user_data['id']) {
        $log->info("User self-modification completed", [
            'user_id' => $user_id,
            'changes_made' => $changes_made,
            'type' => 'modify_account'
        ]);
    } else {
        $log->info("Admin user modification completed", [
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'target_user_id' => $user_id,
            'changes_made' => $changes_made,
            'type' => 'modify_account_admin'
        ]);
    }
}

/**
 * Enregistrement des droits d'un groupe utilisateurs
 */
function usergroup_setauth()
{
    global $pub_group_id, $pub_group_name, $pub_server_set_system, $pub_server_set_spy,
        $pub_server_set_rc, $pub_server_set_ranking, $pub_server_show_positionhided, $pub_ogs_connection,
        $pub_ogs_set_system, $pub_ogs_get_system, $pub_ogs_set_spy, $pub_ogs_get_spy, $pub_ogs_set_ranking,
        $pub_ogs_get_ranking, $log, $user_data;

    $log->info("Début de la modification des droits de groupe", [
        'type' => 'usergroup_setauth_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $pub_group_id ?? 'undefined',
        'group_name' => $pub_group_name ?? 'undefined',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

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
        $log->warning("Modification droits groupe échouée - Format de données invalide", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'invalid_data_format',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id ?? 'undefined',
            'group_name' => $pub_group_name ?? 'undefined',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_group_id) || !isset($pub_group_name)) {
        $log->error("Modification droits groupe échouée - Données manquantes", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'missing_required_data',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id_set' => isset($pub_group_id),
            'group_name_set' => isset($pub_group_name),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
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
    try {
        user_check_auth("usergroup_manage");

        $log->info("Autorisation vérifiée pour modification des droits de groupe", [
            'type' => 'usergroup_setauth_authorized',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $pub_group_name
        ]);
    } catch (Exception $e) {
        $log->error("Modification droits groupe échouée - Autorisation refusée", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'authorization_denied',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    // Log des droits qui vont être mis à jour
    $permissions_summary = [
        'server_permissions' => [
            'set_system' => $pub_server_set_system,
            'set_spy' => $pub_server_set_spy,
            'set_rc' => $pub_server_set_rc,
            'set_ranking' => $pub_server_set_ranking,
            'show_positionhided' => $pub_server_show_positionhided
        ],
        'ogs_permissions' => [
            'connection' => $pub_ogs_connection,
            'set_system' => $pub_ogs_set_system,
            'get_system' => $pub_ogs_get_system,
            'set_spy' => $pub_ogs_set_spy,
            'get_spy' => $pub_ogs_get_spy,
            'set_ranking' => $pub_ogs_set_ranking,
            'get_ranking' => $pub_ogs_get_ranking
        ]
    ];

    $log->debug("Droits à appliquer au groupe", [
        'type' => 'usergroup_setauth_permissions',
        'group_id' => $pub_group_id,
        'group_name' => $pub_group_name,
        'permissions' => $permissions_summary
    ]);

    try {
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

        $log->info("Droits de groupe mis à jour avec succès", [
            'type' => 'usergroup_setauth_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $pub_group_name,
            'permissions_updated' => $permissions_summary,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        $log->error("Erreur lors de la mise à jour des droits de groupe", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $pub_group_name,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
}

/**
 * Récupération des droits d'un groupe d'utilisateurs
 * @param bool $group_id
 * @return array|bool
 */
function usergroup_get($group_id = false)
{
    global $log, $user_data;

    $log->info("Récupération des droits de groupe d'utilisateurs", [
        'type' => 'usergroup_get_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $group_id !== false ? $group_id : 'all_groups',
        'request_all_groups' => $group_id === false,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    //Vérification des droits
    try {
        user_check_auth("usergroup_manage");

        $log->info("Autorisation vérifiée pour récupération des droits de groupe", [
            'type' => 'usergroup_get_authorized',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups'
        ]);
    } catch (Exception $e) {
        $log->error("Récupération droits groupe échouée - Autorisation refusée", [
            'type' => 'usergroup_get_failed',
            'reason' => 'authorization_denied',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups',
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    if (intval($group_id) == 0 && $group_id !== false) {
        $log->warning("Récupération droits groupe échouée - ID groupe invalide", [
            'type' => 'usergroup_get_failed',
            'reason' => 'invalid_group_id',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'provided_id' => $group_id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        return false;
    }

    try {
        $Group_Model = new Group_Model();

        //demande de tous les groupes
        if (!$group_id) {
            $log->debug("Récupération de tous les groupes d'utilisateurs", [
                'type' => 'usergroup_get_all',
                'admin_user_id' => $user_data['id'] ?? 'unknown'
            ]);
            $info_usergroup = $Group_Model->get_all_group_rights();
        } else {
            $log->debug("Récupération d'un groupe spécifique", [
                'type' => 'usergroup_get_specific',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'group_id' => $group_id
            ]);
            $info_usergroup = $Group_Model->get_group_rights($group_id);
        }

        if (sizeof($info_usergroup) == 0) {
            $log->warning("Aucun groupe trouvé", [
                'type' => 'usergroup_get_empty_result',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'group_id' => $group_id !== false ? $group_id : 'all_groups',
                'requested_all' => $group_id === false
            ]);
            return false;
        }

        $groups_count = sizeof($info_usergroup);
        $group_names = [];

        // Extraction des noms de groupes pour les logs
        foreach ($info_usergroup as $group_info) {
            if (isset($group_info['group_name'])) {
                $group_names[] = $group_info['group_name'];
            }
        }

        $log->info("Droits de groupe récupérés avec succès", [
            'type' => 'usergroup_get_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups',
            'groups_count' => $groups_count,
            'group_names' => $group_names,
            'requested_all' => $group_id === false,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $info_usergroup;
    } catch (Exception $e) {
        $log->error("Erreur lors de la récupération des droits de groupe", [
            'type' => 'usergroup_get_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups',
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }
}

/**
 * Suppression d'un groupe utilisateur
 */
function usergroup_delete()
{
    global $pub_group_id, $log, $user_data;

    $log->info("Tentative de suppression de groupe d'utilisateurs", [
        'type' => 'usergroup_delete_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $pub_group_id ?? 'undefined',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!check_var($pub_group_id, "Num")) {
        $log->warning("Suppression groupe échouée - ID groupe invalide", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'invalid_group_id',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'provided_id' => $pub_group_id ?? 'null',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_group_id)) {
        $log->error("Suppression groupe échouée - ID groupe non défini", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'group_id_not_set',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=createusergroup_failed_general&info");
    }

    //Vérification des droits
    try {
        user_check_auth("usergroup_manage");

        $log->info("Autorisation vérifiée pour suppression de groupe", [
            'type' => 'usergroup_delete_authorized',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id
        ]);
    } catch (Exception $e) {
        $log->error("Suppression groupe échouée - Autorisation refusée", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'authorization_denied',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    // Protection contre la suppression du groupe par défaut
    if ($pub_group_id == 1) {
        $log->warning("Tentative de suppression du groupe par défaut", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'default_group_protection',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=administration&subaction=group&group_id=1");
    }

    // Récupération des informations du groupe avant suppression pour les logs
    try {
        $Group_Model = new Group_Model();
        $group_info = $Group_Model->get_group_rights($pub_group_id);
        $group_name = $group_info[0]['group_name'] ?? 'inconnu';
        $group_members_count = count($Group_Model->get_user_list($pub_group_id));

        $log->debug("Informations du groupe à supprimer", [
            'type' => 'usergroup_delete_info',
            'group_id' => $pub_group_id,
            'group_name' => $group_name,
            'members_count' => $group_members_count
        ]);
    } catch (Exception $e) {
        $group_name = 'inconnu';
        $group_members_count = 0;
        $log->warning("Impossible de récupérer les infos du groupe avant suppression", [
            'type' => 'usergroup_delete_warning',
            'group_id' => $pub_group_id,
            'error' => $e->getMessage()
        ]);
    }

    try {
        (new Group_Model())->delete_group($pub_group_id);

        $log->info("Groupe d'utilisateurs supprimé avec succès", [
            'type' => 'usergroup_deleted_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $group_name,
            'former_members_count' => $group_members_count,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        $log->error("Erreur lors de la suppression du groupe", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $group_name,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    redirection("index.php?action=administration&subaction=group");
}

/**
 * Récupération des utilisateurs appartenant à un groupe
 * @param int $group_id Identificateur du groupe demandé
 * @return Array Liste des utilisateurs
 */
function usergroup_member($group_id)
{
    global $log, $user_data;

    $log->info("Récupération des membres d'un groupe", [
        'type' => 'usergroup_member_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $group_id ?? 'undefined',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!isset($group_id) || !is_numeric($group_id)) {
        $log->error("Récupération membres groupe échouée - ID groupe invalide", [
            'type' => 'usergroup_member_failed',
            'reason' => 'invalid_group_id',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'provided_id' => $group_id ?? 'null',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    try {
        $usergroup_member = (new Group_Model())->get_user_list($group_id);

        $members_count = count($usergroup_member);

        $log->info("Membres du groupe récupérés avec succès", [
            'type' => 'usergroup_member_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id,
            'members_count' => $members_count,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $usergroup_member;
    } catch (Exception $e) {
        $log->error("Erreur lors de la récupération des membres du groupe", [
            'type' => 'usergroup_member_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }
}

/**
 * Ajout d'un utilisateur à un groupe
 */
function usergroup_newmember()
{
    global $pub_user_id, $pub_group_id, $pub_add_all, $log, $user_data;

    $log->info("Tentative d'ajout d'utilisateur(s) à un groupe", [
        'type' => 'usergroup_newmember_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'target_user_id' => $pub_user_id ?? 'undefined',
        'group_id' => $pub_group_id ?? 'undefined',
        'add_all_users' => isset($pub_add_all),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    $Group_Model = new Group_Model();
    $userModel = new User_Model();

    try {
        $userid_list = $userModel->select_userid_list();
        $total_users_available = count($userid_list);

        $log->debug("Liste des utilisateurs disponibles récupérée", [
            'type' => 'usergroup_newmember_users_list',
            'total_users' => $total_users_available
        ]);
    } catch (Exception $e) {
        $log->error("Erreur lors de la récupération de la liste des utilisateurs", [
            'type' => 'usergroup_newmember_failed',
            'reason' => 'users_list_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    // Ajout de tous les utilisateurs au groupe
    if (isset($pub_add_all) && is_numeric($pub_group_id)) {
        $log->info("Ajout de tous les utilisateurs au groupe", [
            'type' => 'usergroup_newmember_add_all',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'users_to_add' => $total_users_available
        ]);

        $success_count = 0;
        $error_count = 0;

        foreach ($userid_list as $userid) {
            try {
                user_check_auth("usergroup_manage");

                if ($Group_Model->insert_user_togroup($userid, $pub_group_id)) {
                    $success_count++;
                    $log->debug("Utilisateur ajouté au groupe", [
                        'type' => 'usergroup_newmember_user_added',
                        'user_id' => $userid,
                        'group_id' => $pub_group_id
                    ]);
                } else {
                    $error_count++;
                    $log->warning("Échec de l'ajout d'un utilisateur au groupe", [
                        'type' => 'usergroup_newmember_user_failed',
                        'user_id' => $userid,
                        'group_id' => $pub_group_id,
                        'reason' => 'insert_failed'
                    ]);
                }
            } catch (Exception $e) {
                $error_count++;
                $log->error("Erreur lors de l'ajout d'un utilisateur au groupe", [
                    'type' => 'usergroup_newmember_user_error',
                    'user_id' => $userid,
                    'group_id' => $pub_group_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $log->info("Ajout de masse terminé", [
            'type' => 'usergroup_newmember_bulk_completed',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'total_users' => $total_users_available,
            'success_count' => $success_count,
            'error_count' => $error_count,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
    } else {
        // Ajout d'un utilisateur spécifique
        if (!check_var($pub_user_id, "Num") || !check_var($pub_group_id, "Num")) {
            $log->warning("Ajout utilisateur au groupe échoué - Format de données invalide", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'invalid_data_format',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'user_id' => $pub_user_id ?? 'undefined',
                'group_id' => $pub_group_id ?? 'undefined',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=errordata&info");
        }

        if (!isset($pub_user_id) || !isset($pub_group_id)) {
            $log->error("Ajout utilisateur au groupe échoué - Données manquantes", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'missing_required_data',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'user_id_set' => isset($pub_user_id),
                'group_id_set' => isset($pub_group_id),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=errorfatal&info");
        }

        //Vérification des droits
        try {
            user_check_auth("usergroup_manage");

            $log->debug("Autorisation vérifiée pour ajout utilisateur au groupe", [
                'type' => 'usergroup_newmember_authorized',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id
            ]);
        } catch (Exception $e) {
            $log->error("Ajout utilisateur au groupe échoué - Autorisation refusée", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'authorization_denied',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id,
                'error' => $e->getMessage(),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            throw $e;
        }

        // Vérification de l'existence du groupe
        if ($Group_Model->group_exist_by_id($pub_group_id) == false) {
            $log->warning("Ajout utilisateur échoué - Groupe inexistant", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'group_not_found',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=administration&subaction=group");
        }

        // Vérification de l'existence de l'utilisateur
        if (!in_array(intval($pub_user_id), $userid_list)) {
            $log->warning("Ajout utilisateur échoué - Utilisateur inexistant", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'user_not_found',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=administration&subaction=group");
        }

        // Récupération des informations pour les logs
        try {
            $user_info = user_get($pub_user_id);
            $username = $user_info[0]['user_pseudo'] ?? 'inconnu';

            $group_info = $Group_Model->get_group_rights($pub_group_id);
            $group_name = $group_info[0]['group_name'] ?? 'inconnu';
        } catch (Exception $e) {
            $username = 'inconnu';
            $group_name = 'inconnu';
            $log->debug("Impossible de récupérer les noms pour les logs", [
                'type' => 'usergroup_newmember_info_warning',
                'error' => $e->getMessage()
            ]);
        }

        // Insertion de l'utilisateur dans le groupe
        try {
            if ($Group_Model->insert_user_togroup($pub_user_id, $pub_group_id)) {
                $log->info("Utilisateur ajouté au groupe avec succès", [
                    'type' => 'usergroup_newmember_success',
                    'admin_user_id' => $user_data['id'] ?? 'unknown',
                    'target_user_id' => $pub_user_id,
                    'target_username' => $username,
                    'group_id' => $pub_group_id,
                    'group_name' => $group_name,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            } else {
                $log->warning("Échec de l'ajout de l'utilisateur au groupe", [
                    'type' => 'usergroup_newmember_failed',
                    'reason' => 'insert_failed',
                    'admin_user_id' => $user_data['id'] ?? 'unknown',
                    'target_user_id' => $pub_user_id,
                    'target_username' => $username,
                    'group_id' => $pub_group_id,
                    'group_name' => $group_name,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            }
        } catch (Exception $e) {
            $log->error("Erreur lors de l'ajout de l'utilisateur au groupe", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'database_error',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'target_username' => $username,
                'group_id' => $pub_group_id,
                'group_name' => $group_name,
                'error' => $e->getMessage(),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            throw $e;
        }

        redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
    }
}

/**
 * Suppression d'un rapport d'espionnage
 */
function user_del_spy()
{
    global $db, $user_data, $log;
    global $pub_spy_id, $pub_galaxy, $pub_system, $pub_row, $pub_info;

    $log->info("Tentative de suppression de rapport d'espionnage", [
        'type' => 'spy_delete_attempt',
        'user_id' => $user_data['id'] ?? 'unknown',
        'spy_id' => $pub_spy_id ?? 'undefined',
        'galaxy' => $pub_galaxy ?? 'undefined',
        'system' => $pub_system ?? 'undefined',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!check_var($pub_spy_id, "Num")) {
        $log->warning("Suppression rapport échouée - ID espionnage invalide", [
            'type' => 'spy_delete_failed',
            'reason' => 'invalid_spy_id',
            'provided_id' => $pub_spy_id ?? 'null',
            'user_id' => $user_data['id'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_spy_id)) {
        $log->error("Suppression rapport échouée - ID espionnage non défini", [
            'type' => 'spy_delete_failed',
            'reason' => 'spy_id_not_set',
            'user_id' => $user_data['id'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    if ($user_data["admin"] == 1 || $user_data["coadmin"] == 1) {
        try {
            (new Spy_Model())->delete_spy($pub_spy_id);

            $log->info("Rapport d'espionnage supprimé avec succès", [
                'type' => 'spy_deleted_success',
                'spy_id' => $pub_spy_id,
                'user_id' => $user_data['id'],
                'username' => $user_data['name'] ?? 'unknown',
                'admin_level' => $user_data["admin"] == 1 ? 'admin' : 'coadmin'
            ]);
        } catch (Exception $e) {
            $log->error("Erreur lors de la suppression du rapport d'espionnage", [
                'type' => 'spy_delete_failed',
                'reason' => 'database_error',
                'spy_id' => $pub_spy_id,
                'user_id' => $user_data['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    } else {
        $log->warning("Tentative de suppression non autorisée", [
            'type' => 'spy_delete_failed',
            'reason' => 'insufficient_privileges',
            'spy_id' => $pub_spy_id,
            'user_id' => $user_data['id'] ?? 'unknown',
            'admin' => $user_data["admin"] ?? 'undefined',
            'coadmin' => $user_data["coadmin"] ?? 'undefined'
        ]);
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

    return true;
}

/**
 * Get the number of active users
 * @return int Number of active users
 */
function user_get_nb_active_users()
{
    global $log;

    try {
        $user_model = new User_Model();
        $nb_users = $user_model->get_nb_active_users();

        $log->debug("Retrieved active users count", [
            'type' => 'user_get_nb_active_users_success',
            'count' => $nb_users
        ]);

        return $nb_users;
    } catch (Exception $e) {
        $log->error("Failed to get active users count", [
            'type' => 'user_get_nb_active_users_failed',
            'error' => $e->getMessage()
        ]);
        return 0;
    }
}

/**
 * Récupération des informations d'un utilisateur
 * @param int|null $user_id ID de l'utilisateur (optionnel, récupère tous les utilisateurs si null)
 * @return array|false Informations de l'utilisateur ou false si erreur
 */
function user_get($user_id = null)
{
    global $log, $user_data;

    $log->debug("Récupération des informations utilisateur", [
        'type' => 'user_get_attempt',
        'requested_user_id' => $user_id ?? 'all_users',
        'current_user_id' => $user_data['id'] ?? 'unknown',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    try {
        $userModel = new User_Model();

        if ($user_id === null) {
            // Récupération de tous les utilisateurs
            $log->debug("Récupération de tous les utilisateurs", [
                'type' => 'user_get_all',
                'current_user_id' => $user_data['id'] ?? 'unknown'
            ]);
            $user_info = $userModel->select_all_user_data();
        } else {
            // Récupération d'un utilisateur spécifique
            if (!is_numeric($user_id)) {
                $log->warning("User retrieval failed - Invalid ID", [
                    'type' => 'user_get_failed',
                    'reason' => 'invalid_user_id',
                    'provided_id' => $user_id,
                    'current_user_id' => $user_data['id'] ?? 'unknown'
                ]);
                return false;
            }

            $log->debug("Retrieving specific user", [
                'type' => 'user_get_specific',
                'requested_user_id' => $user_id,
                'current_user_id' => $user_data['id'] ?? 'unknown'
            ]);
            $user_info = $userModel->select_user_data($user_id);
        }

        if (empty($user_info)) {
            $log->info("No user found", [
                'type' => 'user_get_empty_result',
                'requested_user_id' => $user_id ?? 'all_users',
                'current_user_id' => $user_data['id'] ?? 'unknown'
            ]);
            return false;
        }

        $users_count = is_array($user_info) ? count($user_info) : 1;

        $log->info("User information retrieved successfully", [
            'type' => 'user_get_success',
            'requested_user_id' => $user_id ?? 'all_users',
            'users_count' => $users_count,
            'current_user_id' => $user_data['id'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $user_info;

    } catch (Exception $e) {
        $log->error("Error while retrieving user information", [
            'type' => 'user_get_failed',
            'reason' => 'database_error',
            'requested_user_id' => $user_id ?? 'all_users',
            'current_user_id' => $user_data['id'] ?? 'unknown',
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        return false;
    }
}

/**
 * Get user statistics data
 * @return array
 */
function user_statistic()
{
    $userModel = new User_Model();
    return $userModel->select_all_user_stats_data();
}
