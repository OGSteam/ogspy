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
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function select_user_defense($user_id){

        global $db;
        $request = "SELECT planet_id, LM, LLE, LLO, CG, AI, LP, PB, GB, MIC, MIP";
        $request .= " FROM " . TABLE_USER_DEFENCE;
        $request .= " WHERE user_id = " . $user_id;
        $request .= " ORDER BY planet_id";
        $result = $db->sql_query($request);
        return $result;
    }


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

    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     */
    public function update_planet_id($user_id, $previous_id, $new_id){

        global $db;
        $request = "UPDATE " . TABLE_USER_DEFENCE . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $aster_id Planet or moon to be deleted
     */
    public function delete_user_aster($user_id, $aster_id){

        global $db;
        $request = "DELETE FROM " . TABLE_USER_DEFENCE . " WHERE `user_id` = " . $user_id . " AND `planet_id` = " . intval($aster_id);
        $db->sql_query($request);

    }

}