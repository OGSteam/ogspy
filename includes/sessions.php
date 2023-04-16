<?php

/**
 * Fichier de gestion des sessions utilisateurs sur OGSpy
 * @package OGSpy
 * @subpackage Main
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
 * Stating an user Session
 * @param $user_ip
 */
function session_begin($user_ip)
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

    setcookie($cookie_name, $cookie_id, 0);
}

/**
 * Gets the current session and creates it if the session for the current user does not exists
 */
function session()
{
    global $user_ip, $cookie_id, $server_config;
    global $_COOKIE;
    $Sessions_Model = new Sessions_Model();

    $cookie_id = "";
    $cookie_name = COOKIE_NAME;
    $cookie_time = ($server_config["session_time"] == 0) ? 525600 : $server_config["session_time"];

    //Purge des sessions expirées
    if ($server_config["session_time"] != 0) {
        $Sessions_Model->clean_expired_sessions();
    }

    //Récupération de l'id de session si cookie présent
    if (isset($_COOKIE[$cookie_name])) {
        $cookie_id = $_COOKIE[$cookie_name];

        //Vérification de la validité de le session
        if (!$Sessions_Model->is_valid_session_id($cookie_id, $user_ip)) {
            if (isset($server_config["disable_ip_check"]) && $server_config["disable_ip_check"] == 1) {
                //Mise à jour de l'adresse ip de session si le contrôle des ip est désactivé
                if (!$Sessions_Model->update_session_public_ip($cookie_id, $user_ip)) {
                    $cookie_id = "";
                }
            } else {
                $cookie_id = "";
            }
        }
    }
    if ($cookie_id == "") {
        session_begin($user_ip);
    } else {
        $cookie_expire = time() + $cookie_time * 60;
        $Sessions_Model->update_session_expiration_time($cookie_id, $cookie_expire);
    }

    session_set_user_data($cookie_id);
}

/**
 * Updates the session in the database and the cookie
 * @param int $user_id The current user
 * @param int $lastvisit Lastvisit timestamp
 */
function session_set_user_id($user_id, $lastvisit = 0)
{
    global $user_ip, $cookie_id, $server_config;
    $Sessions_Model = new Sessions_Model();

    if (isset($server_config["disable_ip_check"]) && $server_config["disable_ip_check"] != 1) {
        $Sessions_Model->update_session($user_id, $lastvisit, $cookie_id, $user_ip);
    } else {
        $Sessions_Model->update_session($user_id, $lastvisit, $cookie_id);
    }

    session_set_user_data($cookie_id);
}

/**
 * Set the user_data array according to the user parameters in the database
 * @param int $cookie_id The cookie id of the user
 * @todo Y a comme un probleme dans cette fonction... ne semble pas prendre de parametres alors que la fonction precedente lui en donne un...
 */
function session_set_user_data($cookie_id)
{
    global $user_ip, $user_data, $user_auth, $user_token;

    $user_data = (new Sessions_Model())->select_user_data_session($cookie_id, $user_ip);

    if ($user_data == false) {
        unset($user_data);
        unset($user_auth);
        unset($user_token);
    }
}

/**
 * Get the user token (list) for the current user
 * @return mixed
 *
 *function session_set_user_tokens_data()
 *{
 *  global $db, $user_data, $user_token;
 *
 *  $request_tokens = "SELECT `name`,`token`,`expiration_date` FROM " . TABLE_USER_TOKEN . " WHERE `user_id` = " . $user_data["user_id"];
 *  $result_tokens = $db->sql_query($request_tokens);
 *
 *  if ($db->sql_numrows($result_tokens) > 0) {
 *
 *      $user_token = $db->sql_fetch_assoc($result_tokens);
 *  }
 *  else
 *  {
 *      $user_auth = user_get_auth($user_data["user_id"]);
 *  }
 *}

/**
 * Closing an user session
 * @param boolean $user_id ID user session
 */
function session_close($user_id = false)
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

    $guests = array();
    $members = array();
    foreach ($tOnline as $online) {
        $time_lastactivity = $online["session_expire"] - $cookie_time * 60;
        $session_ip = decode_ip($online["session_ip"]);

        if (is_null($online["user_name"])) {
            $username = "Visiteur non identifié";
            $guests[] = array("user" => $username, "time_start" => $online["session_start"], "time_lastactivity" => $time_lastactivity, "ip" => $session_ip, "ogs" => 0);
        } else {
            $username = $online["user_name"];
            $members[] = array("user" => $username, "time_start" => $online["session_start"], "time_lastactivity" => $time_lastactivity, "ip" => $session_ip, "ogs" => $online["session_ogs"]);
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
    (new Sessions_Model())->drop_all();
}
