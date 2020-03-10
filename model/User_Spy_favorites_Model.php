<?php

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class User_Spy_favorites_Model  extends Model_Abstract
{

    public function delete_favorite_spy($user_id,$spy_id)
    {
        $user_id=(int)$user_id;
        $spy_id=(int)$spy_id;

        $request = "DELETE FROM " . TABLE_USER_SPY . " WHERE `spy_id` = '" . $spy_id . "' AND user_id = '" . $user_id . "'  ";
        $this->db->sql_query($request);

    }

    public function add_favorite_spy($user_id,$spy_id)
    {
        $user_id=(int)$user_id;
        $spy_id=(int)$spy_id;

        $request = "INSERT IGNORE INTO " . TABLE_USER_SPY . " (`user_id`, `spy_id`) values ( $user_id, $spy_id)";
        $this->db->sql_query($request);
    }



    public function Get_favorite_spy($user_id)
    {
        $user_id=(int)$user_id;

        $request = "SELECT * FROM " . TABLE_USER_SPY . " where `user_id` = $user_id ";
        $result = $this->db->sql_query($request);

        $tResult = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = $row;
        }
        return $tResult;
    }


    public function Count_favorite_spy($user_id=null)
    {
        $request="";
        if ($user_id==null) {
            $request = "SELECT * FROM " . TABLE_USER_SPY . " ";
        }
        else
        {
            $user_id=(int)$user_id;
            $request = "SELECT * FROM " . TABLE_USER_SPY . " where `user_id` = $user_id ";
        }
        $result = $this->db->sql_query($request);

        $nb_favorites = $this->db->sql_numrows($result);
        return $nb_favorites;
    }



}