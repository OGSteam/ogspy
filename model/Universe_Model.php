<?php
/**
 * Created by PhpStorm.
 * User: Itori
 * Date: 28/08/2016
 * Time: 10:42
 */

namespace Ogsteam\Ogspy\Model;


class Universe_Model
{
    /**
     * Supprime les galaxies et les systèmes supérieurs aux nouvelles limites
     * @param $newGalaxy int nouveau nombre de galaxies
     * @param $newSystem int nouveau nombre de systèmes
     */
    public function resize_universe($newGalaxy, $newSystem)
    {
        global $db;

        $query = "DELETE FROM `" . TABLE_UNIVERSE . "` WHERE galaxy > $newGalaxy OR system > $newSystem";
        $db->sql_query($query);
    }

    /**
     * Obtiens la liste des planètes situés dans les systèmes requis
     * @param $galaxy integer Galaxie
     * @param $system_down integer Limite basse pour les systèmes
     * @param $system_up integer Limite haute pour les systèmes
     * @return array
     */
    public function get_system($galaxy, $system_down, $system_up)
    {
        global $db;

        $request = "SELECT galaxy, system, row, name, ally, player, moon, phalanx, gate, last_update_moon, status, last_update, user_name
                    FROM " . TABLE_UNIVERSE . " 
                        LEFT JOIN " . TABLE_USER . " 
                            ON user_id = last_update_user_id
                    WHERE galaxy = $galaxy AND system BETWEEN $system_down AND $system_up 
                    ORDER BY system, row";
        $result = $db->sql_query($request);

        $population = array();
        for($system = $system_down; $system <= $system_up; $system++)
        {
            for($row = 1; $row <= 15; $row++) {
                $population[$system] = array();
                $population[$system][$row] =  array("galaxy" => $galaxy,
                    "system" => $system,
                    "row" => $row,
                    "ally" => "",
                    "player" => "",
                    "moon" => "",
                    "last_update_moon" => "",
                    "phalanx" => "",
                    "gate" => "",
                    "planet" => "",
                    "status" => "",
                    "timestamp" => "",
                    "poster" => "");
            }
        }

        while($row = $db->sql_fetch_assoc($result))
        {
            $population[$row['system']][$row['row']]['galaxy'] = $row['galaxy'];
            $population[$row['system']][$row['row']]['system'] = $row['system'];
            $population[$row['system']][$row['row']]['row'] = $row['row'];
            $population[$row['system']][$row['row']]['planet'] = $row['name'];
            $population[$row['system']][$row['row']]['ally'] = $row['ally'];
            $population[$row['system']][$row['row']]['player'] = $row['player'];
            $population[$row['system']][$row['row']]['moon'] = $row['moon'];
            $population[$row['system']][$row['row']]['phalanx'] = $row['phalanx'];
            $population[$row['system']][$row['row']]['gate'] = $row['gate'];
            $population[$row['system']][$row['row']]['last_update_moon'] = $row['last_update_moon'];
            $population[$row['system']][$row['row']]['status'] = $row['status'];
            $population[$row['system']][$row['row']]['timestamp'] = $row['last_update'];
            $population[$row['system']][$row['row']]['poster'] = $row['user_name'];
        }

        return $population;
    }
}