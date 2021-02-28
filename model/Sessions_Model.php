<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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

        $request = "SELECT `session_id` FROM " . TABLE_SESSIONS . " WHERE `session_id` = '" . $cookie_id . "'" . " AND `session_ip` = '" . $user_ip . "'";
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) != 1) {
            return false;
        }
         return true;
    }
    
    public function get_xtense_session($user_id)
    {
        $user_id=(int)$user_id;

        $request = "SELECT `session_ogs` FROM " . TABLE_SESSIONS . " WHERE `session_user_id` = " . $user_id;
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) > 0) {
            list($session_ogs) = $this->db->sql_fetch_row($result);
            return $session_ogs;
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
        $user_id=(int)$user_id;
        $lastvisit=(int)$lastvisit;
        $cookie_id=$this->db->sql_escape_string($cookie_id);
        $user_ip= (bool)$user_ip;


        $request = "UPDATE " . TABLE_SESSIONS . " SET session_user_id = " . $user_id . ", session_lastvisit = " . $lastvisit . " WHERE session_id = '" . $cookie_id . "'";
        if ($user_ip!=False)
        {
            $request .= " and session_ip = '" . $user_ip . "'";
        }
        $this->db->sql_query($request);
    }
    /**
     * @param $cookie_id
     * @param $user_ip
     */
    public function update_session_public_ip($cookie_id, $user_ip)
    {
        $cookie_id=$this->db->sql_escape_string($cookie_id);
        $user_ip=$this->db->sql_escape_string($user_ip);

        //Mise à jour de l'adresse ip de session si le contrôle des ip est désactivé
        $request = "SELECT `session_id` FROM " . TABLE_SESSIONS . " LEFT JOIN " . TABLE_USER . " ON `session_user_id` = `user_id`" . " WHERE `session_id` = '" . $cookie_id . "'" . " and `disable_ip_check` = '1'";
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) > 0) {
            $request = "update " . TABLE_SESSIONS . " set session_ip = '" . $user_ip . "' where session_id = '" . $cookie_id . "'";
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
        $cookie_id=$this->db->sql_escape_string($cookie_id);
        $cookie_expire=(int)$cookie_expire;

        $request = "UPDATE " . TABLE_SESSIONS . " SET `session_expire` = " . $cookie_expire . " WHERE `session_id` = '" . $cookie_id . "'";
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $cookie_id
     * @param $cookie_expire
     * @param $user_ip
     */
    public function add_user_session($cookie_id, $cookie_expire, $user_ip)
    {
        $cookie_id=$this->db->sql_escape_string($cookie_id);
        $cookie_expire=(int)$cookie_expire;
        $user_ip=$this->db->sql_escape_string($user_ip);

        $request = "INSERT INTO " . TABLE_SESSIONS . " (`session_id`, `session_user_id`, `session_start`, `session_expire`, `session_ip`) VALUES ('" . $cookie_id . "', 0, " . time() . ", " . $cookie_expire . ", '" . $user_ip . "')";
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $cookie_id
     * @param $cookie_expire
     * @param $user_ip
     */
    public function insert_xtense_session($cookie_id, $cookie_expire, $user_ip)
    {
        $cookie_id=$this->db->sql_escape_string($cookie_id);
        $cookie_expire=(int)$cookie_expire;
        $user_ip=$this->db->sql_escape_string($user_ip);

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_ip` = '" . $user_ip . "' AND `session_ogs` = '1'";
        $this->db->sql_query($request, true, false);
        $request = "INSERT INTO " . TABLE_SESSIONS . " (`session_id`, `session_user_id`, `session_start`, `session_expire`, `session_ip`, `session_ogs`) VALUES ('" . $cookie_id . "', 0, " . time() . ", " . $cookie_expire . ", '" . $user_ip . "', '1')";
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

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_id` = '" . $cookie_id . "'";
        $this->db->sql_query($request, true, false);
    }

    public function close_session_by_cookie_session_ip($cookie_id, $user_ip)
    {
        $cookie_id = $this->db->sql_escape_string($cookie_id);
        $user_ip=$this->db->sql_escape_string($user_ip);

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_id` = '" . $cookie_id . "' AND session_ip = '" . $user_ip . "' ";
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $user_id
     */
    public function close_user_session($user_id)
    {
        $user_id=(int)$user_id;

        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_user_id` = " . $user_id;
        $this->db->sql_query($request, true, false);
    }
    /**
     * Removes all expired sessions from the table
     */
    public function clean_expired_sessions()
    {
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE session_expire < " . time();
        $this->db->sql_query($request, true, false);
    }
    /**
     * @param $cookie_id
     * @param $user_ip
     * @return mixed array|bool
     */
    public function select_user_data_session($cookie_id, $user_ip)
    {
        $cookie_id=$this->db->sql_escape_string($cookie_id);
        $user_ip=$this->db->sql_escape_string($user_ip);

        $request = "SELECT `user_id`, `user_name`, `user_admin`, `user_coadmin`, `user_email`, `user_galaxy`, `user_system`, `session_lastvisit`, `user_stat_name`, ";
        $request .= "`management_user`, `management_ranking`, `disable_ip_check`, `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate` , `user_class`, `user_pwd_change`, `user_email_valid` ";
        $request .= " FROM " . TABLE_USER . " u, " . TABLE_SESSIONS . " s";
        $request .= " WHERE u.user_id = s.session_user_id";
        $request .= " AND session_id = '" . $cookie_id . "'";
        $request .= " AND session_ip = '" . $user_ip . "'";
        $result = $this->db->sql_query($request);

        if ($this->db->sql_numrows($result) == 1) {
            return $this->db->sql_fetch_assoc($result);
        }
        return false;
    }

    /**
     * @return array
     */
    public function who_is_online()
    {
        $request = "SELECT `user_name`, `session_start`, `session_expire`, `session_ip`, `session_ogs`";
        $request .= " FROM " . TABLE_SESSIONS . " LEFT JOIN " . TABLE_USER;
        $request .= " ON `session_user_id` = `user_id`";
        $request .= " GROUP BY `user_name`,`session_ip`,`session_ogs`";
        $request .= " ORDER BY `user_name`";
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
        $request = "SELECT COUNT(session_ip) FROM " . TABLE_SESSIONS;
        $connectes_req = $this->db->sql_query($request);
        list($connectes) = $this->db->sql_fetch_row($connectes_req);
        return $connectes;
    }
}