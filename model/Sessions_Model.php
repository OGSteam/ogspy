<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 14/08/2016
 * Time: 14:25
 */

namespace Ogsteam\Ogspy;


class Sessions_Model
{

    /**
     * Deletes all sessions
     */
    public function drop_all(){

        global $db;
        $db->sql_query("TRUNCATE TABLE " . TABLE_SESSIONS);
    }

    /**
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function who_is_online(){
        global $db;
        $request = "SELECT `user_name`, `session_start`, `session_expire`, `session_ip`, `session_ogs`";
        $request .= " FROM" . TABLE_SESSIONS . " LEFT JOIN " . TABLE_USER;
        $request .= " ON `session_user_id` = `user_id`";
        $request .= " ORDER BY `user_name`";
        $result = $db->sql_query($request);

        return $result;
    }

    /**
     * @param $cookie_id
     */
    public function close_session($cookie_id){
        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_id` = '" . $cookie_id . "'";
        $db->sql_query($request, true, false);
    }

    /**
     * @param $user_id
     */
    public function close_user_session($user_id){
        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_user_id` = " . $user_id;
        $db->sql_query($request, true, false);
    }

    /**
     * @param        $user_id
     * @param        $lastvisit
     * @param        $cookie_id
     * @param string $user_ip
     */
    public function update_session($user_id, $lastvisit, $cookie_id, $user_ip = '' ){

        global $db;
        $request = "UPDATE " . TABLE_SESSIONS . " SET session_user_id = " . $user_id . ", session_lastvisit = " . $lastvisit . " WHERE session_id = '" . $cookie_id . "'";
        $request .= " and session_ip = '" . $user_ip . "'";
        $db->sql_query($request);
    }

    /**
     * Removes all expired sessions from the table
     */
    public function clean_expired_sessions(){

        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE session_expire < " . time();
        $db->sql_query($request, true, false);
    }

    /**
     * @param        $cookie_id
     * @param string $user_ip
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function get_session_id($cookie_id, $user_ip = ''){
        global $db;
        $request = "SELECT session_id FROM " . TABLE_SESSIONS . " WHERE session_id = '" . $cookie_id . "'" . " AND session_ip = '" . $user_ip . "'";
        $result = $db->sql_query($request);

        return $result;
    }
}