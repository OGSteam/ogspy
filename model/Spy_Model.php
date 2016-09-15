<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 15/09/2016
 * Time: 19:25
 */

namespace Ogsteam\Ogspy\Model;


class Spy_Model
{
    public function get_favoriteSpyList($user_id, $sort = 5 , $sort2 = 0){

        global $db;

        switch ($sort2) {
            case 0:
                $order = " desc";
                break;
            case 1:
                $order = " asc";
                break;
            default:
                $order = " asc";
        }

        switch ($sort) {
            case 1:
                $ordered_by = "coordinates" . $order . "";
                break;
            case 2:
                $ordered_by = "ally " . $order;
                break;
            case 3:
                $ordered_by = "player " . $order;
                break;
            case 4:
                $ordered_by = "moon " . $order;
                break;
            case 5:
                $ordered_by = "dateRE " . $order;
                break;
            default:
                $ordered_by = "dateRE " . $order;
        }


        $favorite = array();

        $request = "select " . TABLE_PARSEDSPY .
            ".id_spy, coordinates, dateRE, sender_id, " . TABLE_UNIVERSE . ".moon, " . TABLE_UNIVERSE . ".ally, " . TABLE_UNIVERSE . ".player, " . TABLE_UNIVERSE . ".status";
        $request .= " from " . TABLE_PARSEDSPY . ", " . TABLE_UNIVERSE;
        $request .= " where " . TABLE_PARSEDSPY . ".sender_id = " . $user_id . " and CONCAT(" . TABLE_UNIVERSE . ".galaxy,':'," . TABLE_UNIVERSE . ".system,':'," . TABLE_UNIVERSE . ".row)=coordinates";
        $request .= " order by " . $ordered_by;
        $result = $db->sql_query($request);

        while (list($spy_id, $coordinates, $datadate, $sender_id, $moon, $ally, $player, $status) = $db->sql_fetch_row($result)) {
            $request = "select user_name from " . TABLE_USER;
            $request .= " where user_id=" . $sender_id;
            $result_2 = $db->sql_query($request);
            list($user_name) = $db->sql_fetch_row($result_2);

            $favorite[$spy_id] = array("spy_id" => $spy_id, "spy_galaxy" => substr($coordinates,
                0, strpos($coordinates, ':')), "spy_system" => substr($coordinates, strpos($coordinates,
                    ':') + 1, strrpos($coordinates, ':') - strpos($coordinates, ':') - 1), "spy_row" =>
                substr($coordinates, strrpos($coordinates, ':') + 1), "player" => $player,
                "ally" => $ally, "moon" => $moon, "status" => $status, "datadate" => $datadate,
                "poster" => $user_name);
        }

        return $favorite;


    }


}