<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 01/09/2016
 * Time: 18:01
 */

namespace Ogsteam\Ogspy\Model;


class User_Building_Model
{
    /**
     * @param $user_id
     * @return mixed
     */
    public function get_planet_list($user_id){

        global $db;
        $request = "select planet_id, coordinates";
        $request .= " from " . TABLE_USER_BUILDING;
        $request .= " where user_id = " . $user_id;
        $request .= " and planet_id <= 199";
        $request .= " order by planet_id";
        $result = $db->sql_query($request);

        while (list($planet_id, $coordinates) = $db->sql_fetch_row($result)) {
            $planet_position[$coordinates] = $planet_id;
        }

        return $planet_position;

    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function get_moon_list($user_id){

        global $db;
        // les lunes
        $request = "select planet_id, coordinates";
        $request .= " from " . TABLE_USER_BUILDING;
        $request .= " where user_id = " . $user_id;
        $request .= " and planet_id > 199";
        $request .= " order by planet_id";
        $result = $db->sql_query($request);

        while (list($planet_id, $coordinates) = $db->sql_fetch_row($result)) {
            $moon_position[$coordinates] = $planet_id;
        }

        return $moon_position;
    }

    public function update_moon_id($user_id, $previous_id, $new_id){

        global $db;
        $request = "UPDATE " . TABLE_USER_BUILDING . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $db->sql_query($request);

        //We adjust the id if we go upper than 299
        $request = "UPDATE " . TABLE_USER_BUILDING .
            " SET planet_id  = planet_id -100 WHERE  planet_id > 299 and user_id = " . $user_id;
        $db->sql_query($request);
    }

    public function update_planet_id($user_id, $previous_id, $new_id){

        global $db;
        $request = "UPDATE " . TABLE_USER_BUILDING . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $db->sql_query($request);
    }


}