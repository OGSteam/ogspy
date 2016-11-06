<?php
/**
 * Created by PhpStorm.
 * User: Itori
 * Date: 28/08/2016
 * Time: 10:42
 */

namespace Ogsteam\Ogspy\Model;


use Ogsteam\Ogspy\Entity\Universe\Search_Criteria;

class Universe_Model
{
    /**
     * Mettre à jour une planète
     * @param array $planet
     */
    public function update(array $planet)
    {
        global $db;

        $query = 'UPDATE ' . TABLE_UNIVERSE . ' 
                  SET `name` = "' . quote($planet['planet_name']) . '",
                      `player` = "' . quote($planet['player_name']) . '",
                      `ally` = "' . quote($planet['ally_tag']) . '",
                      `status` = "' . $planet['status'] . '",
                      `moon` = "' . $planet['moon'] . '",
                      `last_update` = ' . $planet['last_update'] . ',
                      `last_update_user_id` = ' . $planet['last_update_user_id']
            . ' WHERE `galaxy` = ' . $planet['galaxy'] . ' AND `system` = ' . $planet['system'] . ' AND `row` = ' . $planet['row'];

        $db->sql_query($query);
    }

    /**
     * Ajouter une nouvelle planète
     * @param array $planet
     */
    public function add(array $planet)
    {
        global $db;

        $query = 'INSERT INTO ' . TABLE_UNIVERSE . ' (`galaxy`, `system`, `row`, `name`, `player`, `ally`, `status`, `last_update`, `last_update_user_id`, `moon`)
                         VALUES (' . $planet['galaxy'] . ',
                                 ' . $planet['system'] . ',
                                 ' . $planet['row'] . ', 
                                 "' . quote($planet['planet_name']) . '",
                                 "' . quote($planet['player_name']) . '", 
                                 "' . quote($planet['ally_tag']) . '", 
                                 "' . $planet['status'] . '", 
                                 ' . $planet['last_update'] . ', 
                                 ' . $planet['last_update_user_id'] . ', 
                                 "' . quote($planet['moon']) . '")';

        $db->sql_query($query);
    }

    /**
     * Supprime les galaxies et les systèmes supérieurs aux nouvelles limites
     * @param $newGalaxy int nouveau nombre de galaxies
     * @param $newSystem int nouveau nombre de systèmes
     */
    public function resize_universe($newGalaxy, $newSystem)
    {
        global $db;

        $query = "DELETE FROM `" . TABLE_UNIVERSE . "` WHERE `galaxy` > $newGalaxy OR `system` > $newSystem";
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

        $request = "SELECT `galaxy`, `system`, `row`, `name`, `ally`, `player`, `moon`, `phalanx`, `gate`, `last_update_moon`, `status`, last_update, user_name
                    FROM " . TABLE_UNIVERSE . " 
                        LEFT JOIN " . TABLE_USER . " 
                            ON `user_id` = `last_update_user_id`
                    WHERE `galaxy` = $galaxy AND `system` BETWEEN $system_down AND $system_up 
                    ORDER BY `system`, `row`";
        $result = $db->sql_query($request);

        $population = array();
        for($system = $system_down; $system <= $system_up; $system++)
        {
            foreach(range(1,15) as $row) {
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

        if ($db->sql_numrows($result) > 0){

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
        }

        return $population;
    }


    /**
     * Obtiens le nombre de planètes présentes dans les systèmes demandés
     * @param $galaxy
     * @param $system_down
     * @param $system_up
     * @return integer Nombre de planètes
     */
    public function get_nb_planets($galaxy, $system_down, $system_up)
    {
        global $db;

        $request = "SELECT count(*) FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $db->sql_query($request);
        list($nb_planet) = $db->sql_fetch_row($result);

        return $nb_planet;
    }

    /**
     * Obtiens le nombre de planètes vides présentes dans les systèmes demandés
     * @param $galaxy
     * @param $system_down
     * @param $system_up
     * @return integer Number of planets
     */
    public function get_nb_empty_planets($galaxy, $system_down, $system_up)
    {
        global $db;

        $request = "SELECT count(*) FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `player` = '' AND `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $db->sql_query($request);
        list($nb_planet) = $db->sql_fetch_row($result);

        return $nb_planet;
    }

    /**
     * Obtiens la date maximale de dernière mise à jour des systèmes demandés
     * @param $galaxy
     * @param $system_down
     * @param $system_up
     * @return mixed last_update
     */
    public function get_last_update($galaxy, $system_down, $system_up)
    {
        global $db;

        $request = "SELECT MAX(`last_update`) FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $db->sql_query($request);
        list($last_update) = $db->sql_fetch_row($result);

        return $last_update;
    }

    /**
     * Obtiens la liste des alliances
     * @return array
     */
    public function get_ally_list()
    {
        global $db;

        $ally_list = array();

        $request = "SELECT DISTINCT `ally` FROM " . TABLE_UNIVERSE . " ORDER BY `ally`";
        $result = $db->sql_query($request);
        while ($row = $db->sql_fetch_assoc($result)) {
            if ($row["ally"] != "") $ally_list[] = $row["ally"];
        }

        return $ally_list;
    }

    /**
     * Obtiens la liste des joueurs d'une alliance
     * @param $galaxy
     * @param $system_down
     * @param $system_up
     * @param $ally_name
     * @return array
     */
    public function get_ally_position($galaxy, $system_down, $system_up, $ally_name)
    {
        global $db;

        $request = "SELECT `galaxy`, `system`, `row`, `player` FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);
        $request .= " AND `ally` LIKE '" . $ally_name . "'";
        $request .= " ORDER BY `player`, `galaxy`, `system`, `row`";
        $result = $db->sql_query($request);

        $population = array();
        while (list($galaxy_, $system_, $row_, $player) = $db->sql_fetch_row($result)) {
            $population[] = array("galaxy" => $galaxy_, "system" => $system_, "row" => $row_, "player" => $player);
        }

        return $population;
    }

    /**
     * Retourne le nom de la planète
     * @param $galaxy
     * @param $system
     * @param $row
     * @return mixed
     */
    public function get_planet_name($galaxy, $system, $row)
    {
        global $db;

        $request_astre_name = "SELECT `name` FROM " . TABLE_UNIVERSE . " WHERE `galaxy` = " . intval($galaxy) . " AND `system` = " . intval($system) . " AND `row` = " . intval($row);
        $result_astre_name = $db->sql_query($request_astre_name);
        $astre_name = $db->sql_fetch_assoc($result_astre_name); //Récupère le nom de la planète

        return $astre_name;
    }

    /**
     * Retourne la liste des phalanges d'une galaxie
     * @param $galaxy
     * @return array
     */
    public function get_phalanx($galaxy)
    {
        global $db;

        $req = "SELECT galaxy, system, row, phalanx, gate, name, ally, player FROM " . TABLE_UNIVERSE . " WHERE galaxy = '" . $galaxy . "' AND moon = '1' AND phalanx > 0";

        $result = $db->sql_query($req);
        $data = array();
            //Construction liste phalanges
            while ($row = $db->sql_fetch_assoc($result)) {
                $data[] = array('galaxy' => $row["galaxy"],
                                'system' => $row["system"],
                                'row' => $row["row"],
                                'name' => $row["name"],
                                'ally' => $row["ally"],
                                'player' => $row["player"],
                                'gate' => $row["gate"],
                                'level' => $row["phalanx"]);
            }

        return $data;
    }

    public function find(Search_Criteria $criteria, array $order_by = array(), $start, $number = 30)
    {
        global $db;

        $select = "SELECT `galaxy`, `system`, `row`, `moon`, `phalanx`, `gate`, `last_update_moon`, `ally`, `player`, `status`, `last_update`, `user_name`";
        $request = " FROM " . TABLE_UNIVERSE . " LEFT JOIN " . TABLE_USER .
                   "    ON `last_update_user_id` = `user_id`";

        $where = "";
        if($criteria->getPlayerName() != null) {
            if($where != "")
                $where .= " AND ";
            $where .= " `player` LIKE '" . $db->sql_escape_string($criteria->getPlayerName()) . "'";
        }

        if($criteria->getAllyName() != null) {
            if($where != "")
                $where .= " AND ";
            $where .= " `ally` LIKE '" . $db->sql_escape_string($criteria->getAllyName()) . "'";
        }

        if($criteria->getPlanetName() != null)
        {
            if($where != "")
                $where .= " AND ";
            $where .= " `name` LIKE '" . $db->sql_escape_string($criteria->getPlanetName()) . "'";
        }

        if($criteria->getGalaxyDown() != null && $criteria->getGalaxyUp() != null)
        {
            if($where != "")
                $where .= " AND ";
            $where .= " `galaxy` BETWEEN " . $criteria->getGalaxyDown() . " AND " . $criteria->getGalaxyUp();
        }

        if($criteria->getSystemDown() != null && $criteria->getSystemUp() != null)
        {
            if($where != "")
                $where .= " AND ";
            $where .= " `system` BETWEEN " . $criteria->getSystemDown() . " AND " . $criteria->getSystemUp();
        }

        if($criteria->getRowDown() != null && $criteria->getRowUp() != null)
        {
            if($where != "")
                $where .= " AND ";
            $where .= " `row` BETWEEN " . $criteria->getRowDown() . " AND " . $criteria->getRowUp();
        }

        if($criteria->getIsMoon())
        {
            if($where != "")
                $where .= " AND ";
            $where .= " `moon` = 1";
        }

        if($criteria->getIsInactive())
        {
            if($where != "")
                $where .= " AND ";
            $where .= " `status` LIKE ('%i%')";
        }

        $query = $select . $request;
        if($where != "")
            $query .= " WHERE " . $where;

        $i = 0;
        foreach ($order_by as $key => $value) {
            if ($i == 0)
                $query .= " ORDER BY ";
            else
                $query .= ", ";

            $query .= $db->sql_escape_string($key);
            if ($value == 'DESC')
                $query .= ' DESC';
            $i++;
        }

        $query .= " LIMIT $start, $number";

        $queryCount = "SELECT count(*) " . $request;
        if($where != "")
            $queryCount .= " WHERE " . $where;
        $result = $db->sql_query($queryCount);
        list($total_row) = $db->sql_fetch_row($result);

        $result = $db->sql_query($query);
        $planets = array();
        while ($planet = $db->sql_fetch_assoc($result))
            $planets[] = $planet;

        return array('total_row' => $total_row, 'planets' => $planets);
    }



}