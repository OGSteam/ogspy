<?php
/**
 * Fichier de gestion des sessions utilisateurs sur OGSpy
 *
 * @package OGSpy
 * @subpackage Main
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b
 * @created 06/12/2005
 */

namespace Ogsteam\Ogspy;

/**
 * Interdiction de l'appel direct
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Stating an user Session
 *
 * @param $user_ip
 */
function session_begin($user_ip)
{
    global $server_config, $pub_toolbar_type;

    $cookie_name = COOKIE_NAME;
    $cookie_time = ($server_config["session_time"] == 0) ? 525600 : $server_config["session_time"];
    $cookie_id = md5(uniqid(mt_rand(), true));

    $cookie_expire = time() + $cookie_time * 60;

    $data_sessions = new Model\Sessions_Model();

    if (!isset($pub_toolbar_type)) {
        $data_sessions->add_user_session($cookie_id, $cookie_expire, $user_ip);
    } else {
        //Update Xtense Session
        $data_sessions->insert_xtense_session($cookie_id, $cookie_expire, $user_ip);
    }

    setcookie($cookie_name, $cookie_id, 0);
}

/**
 * Gets the current session and creates it if the session for the current user does not exists
 *
 */
function session()
{
    global $db, $user_ip, $cookie_id, $server_config;

    $cookie_id = "";
    $cookie_name = COOKIE_NAME;
    $cookie_time = ($server_config["session_time"] == 0) ? 525600 : $server_config["session_time"];

    $data_sessions = new Model\Sessions_Model();

    //Purge des sessions expirées
    if ($server_config["session_time"] != 0) $data_sessions->clean_expired_sessions();

    //Récupération de l'id de session si cookie présent
    if (isset($_COOKIE[$cookie_name])) {
        $cookie_id = $_COOKIE[$cookie_name];

        //Vérification de la validité de le session
        $result = $data_sessions->get_session_id($cookie_id, $user_ip);

        if ($db->sql_numrows($result) != 1) {
            if (isset ($server_config["disable_ip_check"]) && $server_config["disable_ip_check"] != 1) {
                $data_sessions->update_session_public_ip($cookie_id, $user_ip);
            } else {
                $cookie_id = "";
            }
        }
    }
    if ($cookie_id == "") {
        session_begin($user_ip);
    } else {
        $cookie_expire = time() + $cookie_time * 60;
        $data_sessions->update_session_expiration_time($cookie_id, $cookie_expire);
    }

    session_set_user_data($cookie_id);
}

/**
 * Updates the session in the database and the cookie
 *
 * @param int $user_id The current user
 * @param int $lastvisit Lastvisit timestamp
 */
function session_set_user_id($user_id, $lastvisit = 0)
{
    global $user_ip, $cookie_id, $server_config;

    if (isset ($server_config["disable_ip_check"]) && $server_config["disable_ip_check"] != 1) $user_ip = '';

    $data_sessions = new Model\Sessions_Model();
    $data_sessions->update_session($user_id, $lastvisit, $cookie_id, $user_ip);

    session_set_user_data($cookie_id);
}

/**
 * Set the user_data array according to the user parameters in the database
 *
 * @param int $cookie_id The cookie id of the user
 */
function session_set_user_data($cookie_id)
{
    global $db, $user_ip, $user_data, $user_auth;

    $data_sessions = new Model\Sessions_Model();
    $result = $data_sessions->select_user_data_session($cookie_id, $user_ip);

    if ($db->sql_numrows($result) == 1) {
        $user_data = $db->sql_fetch_assoc($result);
        $user_auth = user_get_auth($user_data["user_id"]);

    } else {
        unset($user_data);
        unset($user_auth);
    }
}

/**
 * Who is Online ?
 *
 */
function session_whois_online()
{
    global $db, $server_config;

    $cookie_time = ($server_config["session_time"] == 0) ? 525600 : $server_config["session_time"];

    $data_sessions = new Model\Sessions_Model();
    $result = $data_sessions->who_is_online();

    $guests = $members = array();
    while (list($user_name, $session_start, $session_expire, $session_ip, $session_ogs) = $db->sql_fetch_row($result)) {
        $time_lastactivity = $session_expire - $cookie_time * 60;
        $session_ip = decode_ip($session_ip);

        if (is_null($user_name)) {
            $user_name = "Visiteur non identifié";
            $guests[] = array("user" => $user_name, "time_start" => $session_start, "time_lastactivity" => $time_lastactivity, "ip" => $session_ip, "ogs" => 0);
        } else {
            $members[] = array("user" => $user_name, "time_start" => $session_start, "time_lastactivity" => $time_lastactivity, "ip" => $session_ip, "ogs" => $session_ogs);
        }
    }
    $online = array_merge($members, $guests);

    return $online;
}

/**
 * drop_sessions
 * Clean All sessions
 *
 */
function drop_sessions()
{
    $data_sessions = new Model\Sessions_Model();
    $data_sessions->drop_all();
}

/**
 * Closing an user session
 *
 * @param boolean $user_id ID user session
 */
function session_close($user_id = false)
{
    $data_sessions = new Model\Sessions_Model();
    if (!$user_id) {
        $cookie_name = COOKIE_NAME;
        $cookie_id = $_COOKIE [$cookie_name];
        $data_sessions->close_session($cookie_id);
    } else {
        $data_sessions->close_user_session($user_id);
    }
}


