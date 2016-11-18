<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;


class Sessions_Model
{
    /**
     * @param        $cookie_id
     * @param string $user_ip
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function get_session_id($cookie_id, $user_ip = '')
    {
        global $db;
        $request = "SELECT `session_id` FROM " . TABLE_SESSIONS . " WHERE `session_id` = '" . $cookie_id . "'" . " AND `session_ip` = '" . $user_ip . "'";
        $result = $db->sql_query($request);

        return $result;
    }

    public function get_xtense_session ($user_id)
    {
        global $db;
        $request = "SELECT `session_ogs` FROM " . TABLE_SESSIONS . " WHERE session_user_id = " . $user_id;
        $result = $db->sql_query($request);

        if ($db->sql_numrows($result) > 0) {
            list($session_ogs) = $db->sql_fetch_row($result);
            return $session_ogs;
        } else
            return -1;
    }

    /**
     * @param        $user_id
     * @param        $lastvisit
     * @param        $cookie_id
     * @param string $user_ip
     */
    public function update_session($user_id, $lastvisit, $cookie_id, $user_ip = '')
    {

        global $db;
        $request = "UPDATE " . TABLE_SESSIONS . " SET session_user_id = " . $user_id . ", session_lastvisit = " . $lastvisit . " WHERE session_id = '" . $cookie_id . "'";
        $request .= " and session_ip = '" . $user_ip . "'";
        $db->sql_query($request);
    }

    /**
     * @param $cookie_id
     * @param $user_ip
     */
    public function update_session_public_ip($cookie_id, $user_ip)
    {

        global $db;
        //Mise à jour de l'adresse ip de session si le contrôle des ip est désactivé
        $request = "SELECT `session_id` FROM " . TABLE_SESSIONS . " LEFT JOIN " . TABLE_USER . " ON `session_user_id` = `user_id`" . " WHERE `session_id` = '" . $cookie_id . "'" . " and `disable_ip_check` = '1'";
        $result = $db->sql_query($request);

        if ($db->sql_numrows($result) > 0) {
            $request = "update " . TABLE_SESSIONS . " set session_ip = '" . $user_ip . "' where session_id = '" . $cookie_id . "'";
            $db->sql_query($request, true, false);
        }
    }

    /**
     * @param $cookie_id
     * @param $cookie_expire
     */
    public function update_session_expiration_time($cookie_id, $cookie_expire)
    {
        global $db;
        $request = "UPDATE " . TABLE_SESSIONS . " SET `session_expire` = " . $cookie_expire . " WHERE `session_id` = '" . $cookie_id . "'";
        $db->sql_query($request, true, false);
    }

    /**
     * @param $cookie_id
     * @param $cookie_expire
     * @param $user_ip
     */
    public function add_user_session($cookie_id, $cookie_expire, $user_ip)
    {
        global $db;
        $request = "INSERT INTO " . TABLE_SESSIONS . " (`session_id`, `session_user_id`, `session_start`, `session_expire`, `session_ip`) VALUES ('" . $cookie_id . "', 0, " . time() . ", " . $cookie_expire . ", '" . $user_ip . "')";
        $db->sql_query($request, true, false);
    }

    /**
     * @param $cookie_id
     * @param $cookie_expire
     * @param $user_ip
     */
    public function insert_xtense_session($cookie_id, $cookie_expire, $user_ip)
    {

        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_ip` = '" . $user_ip . "' AND `session_ogs` = '1'";
        $db->sql_query($request, true, false);

        $request = "INSERT INTO " . TABLE_SESSIONS . " (`session_id`, `session_user_id`, `session_start`, `session_expire`, `session_ip`, `session_ogs`) VALUES ('" . $cookie_id . "', 0, " . time() . ", " . $cookie_expire . ", '" . $user_ip . "', '1')";
        $db->sql_query($request, true, false);

    }

    /**
     * Deletes all sessions
     */
    public function drop_all()
    {

        global $db;
        $db->sql_query("TRUNCATE TABLE " . TABLE_SESSIONS);
    }

    /**
     * @param $cookie_id
     */
    public function close_session($cookie_id)
    {
        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_id` = '" . $cookie_id . "'";
        $db->sql_query($request, true, false);
    }

    /**
     * @param $user_id
     */
    public function close_user_session($user_id)
    {
        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_user_id` = " . $user_id;
        $db->sql_query($request, true, false);
    }

    /**
     * Removes all expired sessions from the table
     */
    public function clean_expired_sessions()
    {
        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE session_expire < " . time();
        $db->sql_query($request, true, false);
    }


    /**
     * @param $cookie_id
     * @param $user_ip
     * @return bool|mixed|mysqli_result
     */
    public function select_user_data_session($cookie_id, $user_ip)
    {

        global $db;

        $request = "SELECT `user_id`, `user_name`, `user_admin`, `user_coadmin`, `user_email`, `user_galaxy`, `user_system`, `session_lastvisit`, `user_stat_name`, ";
        $request .= "`management_user`, `management_ranking`, `disable_ip_check`, `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`";
        $request .= " FROM " . TABLE_USER . " u, " . TABLE_SESSIONS . " s";
        $request .= " WHERE u.user_id = s.session_user_id";
        $request .= " AND session_id = '" . $cookie_id . "'";
        $request .= " AND session_ip = '" . $user_ip . "'";

        $result = $db->sql_query($request);

        return $result;

    }

    /**
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function who_is_online()
    {
        global $db;
        $request = "SELECT `user_name`, `session_start`, `session_expire`, `session_ip`, `session_ogs`";
        $request .= " FROM " . TABLE_SESSIONS . " LEFT JOIN " . TABLE_USER;
        $request .= " ON `session_user_id` = `user_id`";
        $request .= " ORDER BY `user_name`";
        $result = $db->sql_query($request);

        return $result;
    }

    /**
     * Number of sessions
     * @return int number of sessions
     */
    public function count_online()
    {
        global $db;

        $request = "SELECT COUNT(session_ip) FROM " . TABLE_SESSIONS;
        $connectes_req = $db->sql_query($request);
        list($connectes) = $db->sql_fetch_row($connectes_req);

        return $connectes;
    }
}