<?php
/**
 * Created by IntelliJ IDEA.
 * User: anthony
 * Date: 17/08/16
 * Time: 13:41
 */

namespace Ogsteam\Ogspy;


class User_Model
{

    /* Fonctions concerning user account */
    /**
     * @param $login
     * @param $password
     * @return bool|mixed|mysqli_result
     */
    public function select_user_login($login, $password){
        global $db;
        $request = "SELECT user_id, user_active FROM " . TABLE_USER .
            " WHERE user_name = '" . $db->sql_escape_string($login) .
            "' AND user_password = '" . md5(sha1($password)) . "'";
        $result = $db->sql_query($request);

        return $result;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function select_last_visit($user_id){
        global $db;
        $request = "SELECT user_lastvisit FROM " . TABLE_USER . " WHERE user_id = " . $user_id;
        $result = $db->sql_query($request);
        list($lastvisit) = $db->sql_fetch_row($result);

        return $lastvisit;
    }

    /**
     * @param $user_id
     */
    public function update_lastvisit_time($user_id){
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET user_lastvisit = " . time() .
            " WHERE user_id = " . $user_id;
        $db->sql_query($request);
    }
    /* Fonctions concerning game account */

    public function set_player_officer($user_id, $officer, $value){
        global $db;
        switch($officer){
            case 'off_commandant':
                $request = "UPDATE " . TABLE_USER . " SET `off_commandant` = '". $value ."' WHERE `user_id` = " . $user_id;
                break;
            case 'off_amiral':
                $request = "UPDATE " . TABLE_USER . " SET `off_amiral` = '". $value ."' WHERE `user_id` = " . $user_id;
                break;
            case 'off_ingenieur':
                $request = "UPDATE " . TABLE_USER . " SET `off_ingenieur` = '". $value ."' WHERE `user_id` = " . $user_id;
                break;
            case 'off_geologue':
                $request = "UPDATE " . TABLE_USER . " SET `off_geologue` = '". $value ."' WHERE `user_id` = " . $user_id;
                break;
            case 'off_technocrate':
                $request = "UPDATE " . TABLE_USER . " SET `off_technocrate` = '". $value ."' WHERE `user_id` = " . $user_id;
                break;
            default:
                $request = "";
        }
        $db->sql_query($request);
    }


}