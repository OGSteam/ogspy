<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 01/09/2016
 * Time: 18:21
 */

namespace Ogsteam\Ogspy\Model;


class User_Defense_Model
{
    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     * @todo Could we do that in one request ?
     */
    public function update_moon_id($user_id, $previous_id, $new_id){

        global $db;
        $request = "UPDATE " . TABLE_USER_DEFENCE . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $db->sql_query($request);

        //We adjust the id if we go upper than 299
        $request = "UPDATE " . TABLE_USER_DEFENCE .
            " SET planet_id  = planet_id -100 WHERE  planet_id > 299 and user_id = " . $user_id;
        $db->sql_query($request);


    }
    public function update_planet_id($user_id, $previous_id, $new_id){

        global $db;
        $request = "UPDATE " . TABLE_USER_DEFENCE . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $db->sql_query($request);
    }
}