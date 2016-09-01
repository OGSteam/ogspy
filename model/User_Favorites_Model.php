<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 01/09/2016
 * Time: 20:34
 */

namespace Ogsteam\Ogspy\Model;


class User_Favorites_Model
{
    public function select_user_favorites($user_id)
    {
        //Empty for the moment
    }

    /**
     * @param $user_id
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_user_favorites($user_id)
    {

        global $db;
        $request = "SELECT * FROM " . TABLE_USER_FAVORITE . " WHERE user_id = " . $user_id;
        $result = $db->sql_query($request);
        return $db->sql_numrows($result);

    }

    /**
     * @param $user_id
     * @param $galaxy
     * @param $system
     */
    public function set_user_favorites($user_id, $galaxy, $system)
    {

        global $db;
        $request = "INSERT IGNORE INTO " . TABLE_USER_FAVORITE .
            " (user_id, galaxy, system) VALUES (" . $user_id . ", '" . $galaxy . "', " . $system . ")";
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $galaxy
     * @param $system
     */
    public function delete_user_favorites($user_id, $galaxy, $system)
    {
        global $db;
        $request = "delete from " . TABLE_USER_FAVORITE . " where user_id = " . $user_id .
            " and galaxy = '" . $galaxy . "' and system = " . $system;
        $db->sql_query($request);

    }
}
