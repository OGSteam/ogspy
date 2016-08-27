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
    public function select_user_login ($login, $password)
    {
        global $db;
        $request = "SELECT `user_id`, `user_active` FROM " . TABLE_USER . " WHERE `user_name` = '" . $db->sql_escape_string($login) . "' AND `user_password` = '" . md5(sha1($password)) . "'";
        $result = $db->sql_query($request);

        return $result;
    }

    /**
     * @param $user_id
     * @param $username
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function select_user_name ($user_id, $username)
    {
        global $db;
        $request = "SELECT * FROM " . TABLE_USER . " WHERE `user_name` = '" . $db->sql_escape_string($username) . "' AND `user_id` <> " . $user_id;
        $result = $db->sql_query($request);

        return $result;
    }


    /**
     * @param $user_id
     * @return mixed
     */
    public function select_last_visit ($user_id)
    {
        global $db;
        $request = "SELECT `user_lastvisit` FROM " . TABLE_USER . " WHERE `user_id` = " . $user_id;
        $result = $db->sql_query($request);
        list($lastvisit) = $db->sql_fetch_row($result);

        return $lastvisit;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function select_user_data ($user_id)
    {
        global $db;
        $request = "SELECT `user_id`, `user_name`, `user_password`, `user_email`, `user_active`, `user_regdate`, `user_lastvisit`," .
            " `user_galaxy`, `user_system`, `user_admin`, `user_coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`" .
            " FROM " . TABLE_USER;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " ORDER BY `user_name`";
        $result = $db->sql_query($request);

        $info_users = array();
        while ($row = $db->sql_fetch_assoc($result)) {
            $info_users[] = $row;
        }

        if (count($info_users) == 0) {
            return false;
        }
        return $info_users;
    }

    /**
     * @return mixed
     */
    public function select_all_user_data ()
    {
        global $db;
        $request = "SELECT `user_id`, `user_name`, `user_password`, `user_email`, `user_active`, `user_regdate`, `user_lastvisit`," .
            " `user_galaxy`, `user_system`, `user_admin`, `user_coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`" .
            " FROM " . TABLE_USER;

        $request .= " ORDER BY `user_name`";
        $result = $db->sql_query($request);

        $info_users = array();
        while ($row = $db->sql_fetch_assoc($result)) {
            $info_users[] = $row;
        }

        if (count($info_users) == 0) {
            return false;
        }
        return $info_users;
    }

    /**
     * @param $user_id
     */
    public function update_lastvisit_time ($user_id)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_lastvisit` = " . time() . " WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_name
     */
    public function set_user_pseudo ($user_id, $user_name)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_name` = '" . $db->sql_escape_string($user_name) . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $user_password
     */
    public function set_user_password ($user_id, $user_password)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_password` = '" . md5(sha1($user_password)) . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $user_email
     */
    public function set_user_email ($user_id, $user_email)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_email` = '" . $user_email . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $default_galaxy
     */
    public function set_user_default_galaxy ($user_id, $default_galaxy)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_galaxy` = '" . $default_galaxy . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $default_system
     */
    public function set_user_default_system ($user_id, $default_system)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_system` = '" . $default_system . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $disable_ip_check
     */
    public function set_user_ip_check ($user_id, $disable_ip_check)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `disable_ip_check` = '" . $disable_ip_check . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_active boolean 1/0
     */
    public function set_user_active ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_active` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
 * @param $user_id
 * @param $value boolean 1/0
 */
    public function set_user_coadmin ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_coadmin` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_user ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `management_user` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_ranking ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `management_ranking` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_planet_inserted ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `planet_added_xtense` = planet_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_spy_inserted ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `spy_added_xtense` = spy_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_rank_inserted ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `rank_added_xtense` = rank_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value int
     */
    public function add_stat_search_made ($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `search` = search + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }



    /* Fonctions concerning game account */

    /**
     * A quoi sert donc cette fonction ? :p
     * Reponse elle sert a mettre a jour le pseudo ingame afin d afficher les stats users dans son espace perso
     *
     * @param $user_stat_name
     */
    public function set_game_account_name ($user_id, $user_stat_name)
    {
        global $db;

        $request = "update " . TABLE_USER . " set user_stat_name = '" . $user_stat_name . "' where user_id = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $officer
     * @param $value
     */
    public function set_player_officer ($user_id, $officer, $value)
    {
        global $db;
        switch ($officer) {
            case 'off_commandant':
                $request = "UPDATE " . TABLE_USER . " SET `off_commandant` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_amiral':
                $request = "UPDATE " . TABLE_USER . " SET `off_amiral` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_ingenieur':
                $request = "UPDATE " . TABLE_USER . " SET `off_ingenieur` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_geologue':
                $request = "UPDATE " . TABLE_USER . " SET `off_geologue` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_technocrate':
                $request = "UPDATE " . TABLE_USER . " SET `off_technocrate` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            default:
                $request = "";
        }
        $db->sql_query($request);
    }

}