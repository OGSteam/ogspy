<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Sessions_Model extends Model_Abstract
{
    /**
     * @param        $cookie_id
     * @param string $user_ip
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function is_valid_session_id($cookie_id, $user_ip = '')
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $user_ip = $this->db->sql_escape_string($user_ip);

        $request = "SELECT `id` FROM " . TABLE_SESSIONS . " WHERE `id` = '" . $cookie_id . "'" . " AND `session_ip` = '" . $user_ip . "'";
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) != 1) {
            return false;
        }
        return true;
    }

    public function get_xtense_session($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `session_type` FROM " . TABLE_SESSIONS . " WHERE `user_id` = " . $user_id;
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) > 0) {
            list($session_type) = $this->db->sql_fetch_row($result);
            return $session_type;
        }
        return -1;
    }
    /**
     * @param        $user_id
     * @param        $lastvisit
     * @param        $cookie_id
     * @param string $user_ip
     */
    public function update_session($user_id, $lastvisit, $cookie_id, $user_ip = false)
    {
        $user_id = (int)$user_id;
        $lastvisit = (int)$lastvisit;
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $user_ip = (bool)$user_ip;


        $request = "UPDATE " . TABLE_SESSIONS . " SET `user_id` = " . $user_id . ", `session_lastvisit` = " . $lastvisit . " WHERE `id` = '" . $cookie_id . "'";
        if ($user_ip) {
            $request .= " and `session_ip` = '" . $user_ip . "'";
        }
        $this->db->sql_query($request);
    }
    /**
     * @param $cookie_id
     * @param $user_ip
     */
    public function update_session_public_ip($cookie_id, $user_ip)
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $user_ip = $this->db->sql_escape_string($user_ip);

        //Mise à jour de l'adresse ip de session si le contrôle des ip est désactivé
        $request = "SELECT s.`id` FROM " . TABLE_SESSIONS . " AS s LEFT JOIN " . TABLE_USER . " AS u ON s.`user_id` = u.`id`" . " WHERE s.`id` = '" . $cookie_id . "'" . " AND  u.`disable_ip_check` = '1'";
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) > 0) {
            $request = "UPDATE " . TABLE_SESSIONS . " SET `session_ip` = '" . $user_ip . "' WHERE `id` = '" . $cookie_id . "'";
            $this->db->sql_query($request, true, false);
            return true;
        }
        return false;
    }
    /**
     * @param $cookie_id
     * @param $cookie_expire
     */
    public function update_session_expiration_time($cookie_id, $cookie_expire)
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $cookie_expire = (int)$cookie_expire;

        $request = "UPDATE " . TABLE_SESSIONS . " SET `session_expire` = " . $cookie_expire . " WHERE `id` = '" . $cookie_id . "'";
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $cookie_id
     * @param $cookie_expire
     * @param $user_ip
     */
    public function add_user_session($cookie_id, $cookie_expire, $user_ip)
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $cookie_expire = (int)$cookie_expire;
        $user_ip = $this->db->sql_escape_string($user_ip);

        $request = "INSERT INTO " . TABLE_SESSIONS . " (`id`, `user_id`, `session_start`, `session_expire`, `session_ip`) VALUES ('" . $cookie_id . "', 0, " . time() . ", " . $cookie_expire . ", '" . $user_ip . "')";
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $cookie_id
     * @param $cookie_expire
     * @param $user_ip
     */
    public function insert_xtense_session($cookie_id, $cookie_expire, $user_ip)
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $cookie_expire = (int)$cookie_expire;
        $user_ip = $this->db->sql_escape_string($user_ip);

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_ip` = '" . $user_ip . "' AND `session_type` = '1'";
        $this->db->sql_query($request, true, false);
        $request = "INSERT INTO " . TABLE_SESSIONS . " (`id`, `user_id`, `session_start`, `session_expire`, `session_ip`, `session_type`) VALUES ('" . $cookie_id . "', 0, " . time() . ", " . $cookie_expire . ", '" . $user_ip . "', '1')";
        $this->db->sql_query($request, true, false);
    }
    /**
     * Deletes all sessions
     */
    public function drop_all()
    {
        $this->db->sql_query("TRUNCATE TABLE " . TABLE_SESSIONS);
    }
    /**
     * @param $cookie_id
     */
    public function close_session_by_cookie($cookie_id)
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `id` = '" . $cookie_id . "'";
        $this->db->sql_query($request, true, false);
    }

    public function close_session_by_cookie_session_ip($cookie_id, $user_ip)
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $user_ip = $this->db->sql_escape_string($user_ip);

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `id` = '" . $cookie_id . "' AND session_ip = '" . $user_ip . "' ";
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $user_id
     */
    public function close_user_session($user_id)
    {
        $user_id = (int)$user_id;

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request, true, false);
    }
    /**
     * Removes all expired sessions from the table
     */
    public function clean_expired_sessions()
    {
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_expire` < " . time();
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $cookie_id
     * @param $user_ip
     * @return mixed array|bool
     */
    public function select_user_data_session($cookie_id, $user_ip)
    {
        global $log;
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $user_ip = $this->db->sql_escape_string($user_ip);

        $request = "SELECT user.`id`, user.`name`, user.`admin`, user.`coadmin`, user.`email`, user.`default_galaxy`, user.`default_system`, s.`session_lastvisit`, ";
        $request .= "user.`management_user`, user.`management_ranking`, user.`disable_ip_check`, user.`pwd_change`, user.`email_valid`, user.`player_id` ";
        $request .= " FROM " . TABLE_USER . " user, " . TABLE_SESSIONS . " s";
        $request .= " WHERE user.`id` = s.`user_id`";
        $request .= " AND s.`id` = '" . $cookie_id . "'";
        $request .= " AND s.`session_ip` = '" . $user_ip . "'";
        $result = $this->db->sql_query($request);

        if ($this->db->sql_numrows($result) == 1) {
            return $this->db->sql_fetch_assoc($result);
        }
        $log->warning("Session not found for cookie_id: " . $cookie_id . " and user_ip: " . $user_ip);
        return false;
    }

    /**
     * @return array
     */
    public function who_is_online()
    {
        $request = "SELECT user.`name`, session.`session_start`, session.`session_expire`, session.`session_ip`, session.`session_type`";
        $request .= " FROM " . TABLE_SESSIONS . " session LEFT JOIN " . TABLE_USER. " user";
        $request .= " ON session.`user_id` = user.`id`";
        $request .= " GROUP BY user.`name`, session.`session_ip`, session.`session_type`";
        $request .= " ORDER BY user.`name`";
        $result = $this->db->sql_query($request);

        $retour = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $retour[] = $row;
        }
        return $retour;
    }
    /**
     * Number of sessions
     * @return int number of sessions
     */
    public function count_online()
    {
        $request = "SELECT COUNT(`session_ip`) FROM " . TABLE_SESSIONS;
        $connectes_req = $this->db->sql_query($request);
        list($connectes) = $this->db->sql_fetch_row($connectes_req);
        return $connectes;
    }
}
