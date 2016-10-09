<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 15/09/2016
 * Time: 19:50
 */

namespace Ogsteam\Ogspy\Model;


class User_SpyFavorites_Model
{
    /**
     * @param $user_id
     * @return integer $nb_favorites
     */
    public function get_nb_spyfavorites($user_id){

        global $db;

        $request = "select * from " . TABLE_USER_SPY . " where user_id = " . $user_id;
        $result = $db->sql_query($request);
        $nb_favorites = $db->sql_numrows($result);

        return $nb_favorites;
    }

    /**
     * @param $user_id
     * @param $spy_id
     */
    public function insert_spyfavorite($user_id, $spy_id){

    global $db;

    $request = "INSERT IGNORE INTO " . TABLE_USER_SPY . " (`user_id`, `spy_id`) VALUES (" . $user_id . ", " . $spy_id . ")";
    $db->sql_query($request);

    }

    public function delete_spyfavorite($user_id, $spy_id){
        global $db;

        $request = "DELETE FROM " . TABLE_USER_SPY . " WHERE `user_id` = " . $user_id . " AND `spy_id` = '" . $spy_id . "'";
        $db->sql_query($request);
    }

}