<?php

/**
 * Fichier de gestion des sessions utilisateurs sur OGSpy
 * @package OGSpy
 * @subpackage Main
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b
 * @created 06/12/2005
 */

/**
 * Interdiction de l'appel direct
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\Sessions_Model;


/**
 * Starting a user Session
 * @param $user_ip
 */
function session_begin($user_ip): void
{
    global $cookie_id, $server_config, $pub_toolbar_type;
    $Sessions_Model = new Sessions_Model();

    $cookie_name = COOKIE_NAME;
    $cookie_time = ($server_config["session_time"] == 0) ? 525600 : $server_config["session_time"];
    $cookie_id = md5(uniqid(mt_rand(), true));

    $cookie_expire = time() + $cookie_time * 60;

    if (!isset($pub_toolbar_type)) {
        $Sessions_Model->add_user_session($cookie_id, $cookie_expire, $user_ip);
    } else {
        $Sessions_Model->insert_xtense_session($cookie_id, $cookie_expire, $user_ip);
    }

    setcookie($cookie_name, $cookie_id, $cookie_expire);
}

/**
 * Gets the current session and creates it if the session for the current user does not exists
 */
function session(): void
{
    global $user_ip, $cookie_id, $server_config, $log;
    global $_COOKIE;
    $Sessions_Model = new Sessions_Model();

    $cookie_id = "";
    $cookie_name = COOKIE_NAME;
    $cookie_time = ($server_config["session_time"] == 0) ? 525600 : $server_config["session_time"];

    //Purge des sessions expirées
    if ($server_config["session_time"] != 0) {
        $log->debug("Cleaning expired sessions");
        $Sessions_Model->clean_expired_sessions();
    }

    //Récupération de l'id de session si cookie présent
    if (isset($_COOKIE[$cookie_name])) {
        $cookie_id = $_COOKIE[$cookie_name];
        $log->debug("Session cookie found: " . substr($cookie_id, 0, 8) . "...");

        //Vérification de la validité de le session
        if (!$Sessions_Model->is_valid_session_id($cookie_id, $user_ip)) {
            $log->warning("Invalid session for cookie_id: " . $cookie_id . " and user_ip: " . $user_ip);

            if (isset($server_config["disable_ip_check"]) && $server_config["disable_ip_check"] == 1) {
                //Mise à jour de l'adresse ip de session si le contrôle des ip est désactivé
                $log->info("Attempting to update IP address - IP check disabled");

                if (!$Sessions_Model->update_session_public_ip($cookie_id, $user_ip)) {
                    $log->warning("Failed to update session IP address");
                    $cookie_id = "";
                } else {
                    $log->info("Session IP address updated successfully");
                }
            } else {
                $cookie_id = "";
            }
        } else {
            $log->debug("Valid session found");
        }
    } else {
        $log->debug("No session cookie found");
    }
    if ($cookie_id == "") {
        $log->info("Creating new session for IP address: " . $user_ip);
        session_begin($user_ip);
    } else {
        $cookie_expire = time() + $cookie_time * 60;
        $log->debug("Updating session expiration time");
        $Sessions_Model->update_session_expiration_time($cookie_id, $cookie_expire);
    }
    session_set_user_data($cookie_id);
}

/**
 * Updates the session in the database and the cookie
 * @param int $user_id The current user
 * @param int $lastvisit Lastvisit timestamp
 */
function session_set_user_id($user_id, $lastvisit = 0): void
{
    global $user_ip, $cookie_id, $server_config,$log;
    $Sessions_Model = new Sessions_Model();

    $log->info("Updating session for user ID: " . $user_id);

    if (isset($server_config["disable_ip_check"]) && $server_config["disable_ip_check"] != 1) {
        $log->debug("Updating session with IP verification enabled");
        $Sessions_Model->update_session($user_id, $lastvisit, $cookie_id, $user_ip);
    } else {
        $log->debug("Updating session without IP verification");
        $Sessions_Model->update_session($user_id, $lastvisit, $cookie_id);
    }
    $log->debug("Retrieving user data after session update");
    session_set_user_data($cookie_id);
    $log->info("User session ID: " . $user_id . " updated successfully");
}

/**
 * Set the user_data array according to the user parameters in the database
 * @param string $cookie_id The cookie id of the user
 *
 */
function session_set_user_data(string $cookie_id)
{
    global $user_ip, $user_data, $user_auth, $user_token,$log;

    $log->debug("Retrieving user data for cookie_id: " . substr($cookie_id, 0, 8) . "...");
    $user_data = (new Sessions_Model())->select_user_data_session($cookie_id, $user_ip);

    if (!$user_data) {
        $log->warning("No user data found for cookie_id: " . substr($cookie_id, 0, 8) . "...");
        unset($user_data);
        unset($user_auth);
        unset($user_token);
    } else {
        if (isset($user_data["id"])) {
            $log->info("User identified - ID: " . $user_data["id"] . ", username: " . $user_data["name"]);
        } else {
            $log->info("Unidentified visitor");
        }

    }
}

/**
 * Closing a user session
 * @param boolean $user_id ID user session
 */
function session_close(bool $user_id = false): void
{
    global $user_ip, $cookie_id, $server_config;

    $Sessions_Model = new Sessions_Model();

    if (!$user_id) {
        global $_COOKIE;

        $cookie_name = COOKIE_NAME;
        $cookie_id = $_COOKIE[$cookie_name];

        if (isset($server_config["disable_ip_check"]) && $server_config["disable_ip_check"] != 1) {
            $Sessions_Model->close_session_by_cookie_session_ip($cookie_id, $user_ip);
        } else {
            $Sessions_Model->close_session_by_cookie($cookie_id);
        }
    } else {
        $Sessions_Model->close_user_session($user_id);
    }
}

/**
 * Who is Online ?
 */
function session_whois_online()
{
    global $server_config;

    $cookie_time = ($server_config["session_time"] == 0) ? 525600 : $server_config["session_time"];

    $tOnline = (new Sessions_Model())->who_is_online();

    $guests = [];
    $members = [];
    foreach ($tOnline as $online) {
        $time_lastactivity = $online["session_expire"] - $cookie_time * 60;
        $session_ip = decode_ip($online["session_ip"]);

        if (is_null($online["name"])) {
            $username = "Visiteur non identifié";
            $guests[] = array("user" => $username, "time_start" => $online["session_start"], "time_lastactivity" => $time_lastactivity, "ip" => $session_ip, "session_type" => 0);
        } else {
            $username = $online["name"];
            $members[] = array("user" => $username, "time_start" => $online["session_start"], "time_lastactivity" => $time_lastactivity, "ip" => $session_ip, "session_type" => $online["session_type"]);
        }
    }
    return array_merge($members, $guests);
}

/**
 * drop_sessions
 * Clean All sessions
 *
 */
function drop_sessions()
{
    (new Sessions_Model())->drop_all();
}
