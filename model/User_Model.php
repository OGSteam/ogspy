<?php
/**
 * Created by IntelliJ IDEA.
 * User: anthony
 * Date: 17/08/16
 * Time: 13:41
 */

namespace Ogsteam\Ogspy\Model;


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

    public function select_user_name($user_id, $username){
        global $db;
        $request = "SELECT * FROM " . TABLE_USER . " WHERE user_name = '" .$db->sql_escape_string($username) . "' AND user_id <> " . $user_id;
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

    public function set_user_pseudo($user_id, $user_name){
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET user_name = " . $db->sql_escape_string($user_name) .
            " WHERE user_id = " . $user_id;
        $db->sql_query($request);

    }
    public function set_user_password($user_id, $user_password){
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET user_pasword = " . md5(sha1($user_password)) .
            " WHERE user_id = " . $user_id;
        $db->sql_query($request);

    }
    public function set_user_email($user_id, $user_email){
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET user_email = " . $user_email .
            " WHERE user_id = " . $user_id;
        $db->sql_query($request);

    }
    public function set_user_default_galaxy($user_id, $default_galaxy){
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET user_galaxy = " . $default_galaxy .
            " WHERE user_id = " . $user_id;
        $db->sql_query($request);

    }
    public function set_user_default_system($user_id, $default_system){
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET user_system = " .$default_system .
            " WHERE user_id = " . $user_id;
        $db->sql_query($request);

    }
    public function set_user_ip_check($user_id, $disable_ip_check){
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET disable_ip_check = " . $disable_ip_check .
            " WHERE user_id = " . $user_id;
        $db->sql_query($request);

    }

    /**
     * A quoi sert donc cette fonction ? :p
     * Reponse elle sert a mettre a jour le pseudo ingame afin d afficher les stats users dans son espace perso
     * @param $user_stat_name
     */
    public function set_game_account_name($user_id, $user_stat_name)
    {
        global $db, $user_data;

        $request = "update " . TABLE_USER . " set user_stat_name = '" . $user_stat_name .
            "' where user_id = " . $user_id;
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