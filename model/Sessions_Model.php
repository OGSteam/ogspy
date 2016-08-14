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

    public function drop_all(){

        global $db;
        $db->sql_query("TRUNCATE TABLE " . TABLE_SESSIONS);

    }

    public function who_is_online(){
        global $db;
        $request = "SELECT `user_name`, `session_start`, `session_expire`, `session_ip`, `session_ogs`";
        $request .= " FROM" . TABLE_SESSIONS . " LEFT JOIN " . TABLE_USER;
        $request .= " ON `session_user_id` = `user_id`";
        $request .= " ORDER BY `user_name`";
        $result = $db->sql_query($request);

        return $result;
    }

    public function close_session($cookie_id){
        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_id` = '" . $cookie_id . "'";
        $db->sql_query($request, true, false);
    }

    public function close_user_session($user_id){
        global $db;
        $request = "DELETE FROM " . TABLE_SESSIONS . " WHERE `session_user_id` = " . $user_id;
        $db->sql_query($request, true, false);
    }

}