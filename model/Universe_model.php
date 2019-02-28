<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Itori
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;
use Ogsteam\Ogspy\Helper\SearchCriteria_Helper;

class Universe_Model
{
    /**
     * Mettre à jour une planète
     * @param array $planet
     */
    public function update(array $planet)
    {
         $query = 'UPDATE ' . TABLE_UNIVERSE . ' 
                  SET `name` = "' . quote($planet['planet_name']) . '",
                      `player` = "' . quote($planet['player_name']) . '",
                      `ally` = "' . quote($planet['ally_tag']) . '",
                      `status` = "' . $planet['status'] . '",
                      `moon` = "' . $planet['moon'] . '",
                      `last_update` = ' . $planet['last_update'] . ',
                      `last_update_user_id` = ' . $planet['last_update_user_id']
            . ' WHERE `galaxy` = ' . $planet['galaxy'] . ' AND `system` = ' . $planet['system'] . ' AND `row` = ' . $planet['row'];

        $this->db->sql_query($query);
    }

    /**
     * Ajouter une nouvelle planète
     * @param array $planet
     */
    public function add(array $planet)
    {

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
        $this->db->sql_query($query);
    }

    /**
     * Supprime les galaxies et les systèmes supérieurs aux nouvelles limites
     * @param $newGalaxy int nouveau nombre de galaxies
     * @param $newSystem int nouveau nombre de systèmes
     */
    public function resize_universe($newGalaxy, $newSystem)
    {

        $query = "DELETE FROM `" . TABLE_UNIVERSE . "` WHERE `galaxy` > $newGalaxy OR `system` > $newSystem";
        $this->db->sql_query($query);
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

        $request = "SELECT `galaxy`, `system`, `row`, `name`, `ally`, `player`, `moon`, `phalanx`, `gate`, `last_update_moon`, `status`, last_update, user_name
                    FROM " . TABLE_UNIVERSE . " 
                        LEFT JOIN " . TABLE_USER . " 
                            ON `user_id` = `last_update_user_id`
                    WHERE `galaxy` = $galaxy AND `system` BETWEEN $system_down AND $system_up 
                    ORDER BY `system`, `row`";
        $result =  $this->db->sql_query($request);

        $population = array();
        for ($system = $system_down; $system <= $system_up; $system++)
        {
            foreach (range(1, 15) as $row) {
                $population[$system][$row] = array("galaxy" => $galaxy,
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

        if ( $this->db->sql_numrows($result) > 0) {

            while ($row =  $this->db->sql_fetch_assoc($result))
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


        $request = "SELECT count(*) FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $this->db->sql_query($request);
        list($nb_planet) = $this->db->sql_fetch_row($result);

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

        $request = "SELECT count(*) FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `player` = '' AND `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $this->db->sql_query($request);
        list($nb_planet) = $this->db->sql_fetch_row($result);

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

        $request = "SELECT MAX(`last_update`) FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $this->db->sql_query($request);
        list($last_update) = $this->db->sql_fetch_row($result);

        return $last_update;
    }

    /**
     * Obtiens la liste des alliances
     * @return array
     */
    public function get_ally_list()
    {
         $ally_list = array();

        $request = "SELECT DISTINCT `ally` FROM " . TABLE_UNIVERSE . " ORDER BY `ally`";
        $result = $this->db->sql_query($request);
        while ($row = $this->db->sql_fetch_assoc($result)) {
            if ($row["ally"] != "") {
                $ally_list[] = $row["ally"];
            }
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
        $request = "SELECT `galaxy`, `system`, `row`, `player` FROM " . TABLE_UNIVERSE;
        $request .= " WHERE `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);
        $request .= " AND `ally` LIKE '" . $ally_name . "'";
        $request .= " ORDER BY `player`, `galaxy`, `system`, `row`";
        $result = $this->db->sql_query($request);

        $population = array();
        while (list($galaxy_, $system_, $row_, $player) = $this->db->sql_fetch_row($result)) {
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
        $request_astre_name = "SELECT `name` FROM " . TABLE_UNIVERSE . " WHERE `galaxy` = " . intval($galaxy) . " AND `system` = " . intval($system) . " AND `row` = " . intval($row);
        $result_astre_name = $this->db->sql_query($request_astre_name);
        $astre_name = $this->db->sql_fetch_assoc($result_astre_name); //Récupère le nom de la planète

        return $astre_name;
    }

    /**
     * Retourne la liste des phalanges d'une galaxie
     * @param $galaxy
     * @return array
     */
    public function get_phalanx($galaxy)
    {
        $req = "SELECT galaxy, system, row, phalanx, gate, name, ally, player FROM " . TABLE_UNIVERSE . " WHERE galaxy = '" . $galaxy . "' AND moon = '1' AND phalanx > 0";

        $result = $this->db->sql_query($req);
        $data = array();
        //Construction liste phalanges
        while ($row = $this->db->sql_fetch_assoc($result)) {
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

    /**
     * Retourne les systemes obsoletes
     * @param $galaxy Integer si 0 toutes les galaxies
     * @param $system_down borne systeme basse
     * @param $row $system_up systeme haute
     * @param $moon recherche sur lune
     * @param $indice Indice de recherche
     * @param $since tableau regroupant les valuers possibles
     * @return mixed
     */
    public function get_galaxy_obsolete($galaxy , $system_down, $system_up,$indice,$since ,$forMoon =false)
    {
        $obsolete=array();

        $field = "last_update";
        $row_field = "";
        $moon = 0;
        if ($forMoon)
        {
            $field = "last_update_moon";
            $row_field = ", row";
            $moon = 1;
        }

        $request = "select distinct galaxy, system" . $row_field . " from " . TABLE_UNIVERSE . " where moon = '" . $moon . "' and " . $field . " between " . $system_up . " and " . $system_down;
        if ($galaxy != 0) {
            $request .= " and galaxy = " . (int)$galaxy;
        }
        $request .= " order by galaxy, system, row limit 0, 51";
        $result = $this->db->sql_query($request);


        while ($row = $this->db->sql_fetch_assoc($result)) {
            $request = "select min(" . $field . ") from " . TABLE_UNIVERSE . " where galaxy = " . $row["galaxy"] . " and system = " . $row["system"];
            $result2 = $this->db->sql_query($request);
            list($last_update) = $this->db->sql_fetch_row($result2);
            $row["last_update"] = $last_update;

            $obsolete[$since[$indice]][] = $row;
        }
        return $obsolete;

   }


    public function find(SearchCriteria_Helper $criteria, array $order_by = array(), $start, $number = 30)
    {
        $select = "SELECT `galaxy`, `system`, `row`, `moon`, `phalanx`, `gate`, `last_update_moon`, `ally`, `player`, `status`, `last_update`, `user_name`";
        $request = " FROM " . TABLE_UNIVERSE . " LEFT JOIN " . TABLE_USER .
            "    ON `last_update_user_id` = `user_id`";

        $where = "";
        if ($criteria->getPlayerName() != null) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `player` LIKE '" . $this->db->sql_escape_string($criteria->getPlayerName()) . "'";
        }

        if ($criteria->getAllyName() != null) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `ally` LIKE '" . $this->db->sql_escape_string($criteria->getAllyName()) . "'";
        }

        if ($criteria->getPlanetName() != null)
        {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `name` LIKE '" . $this->db->sql_escape_string($criteria->getPlanetName()) . "'";
        }

        if ($criteria->getGalaxyDown() != null && $criteria->getGalaxyUp() != null)
        {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `galaxy` BETWEEN " . $criteria->getGalaxyDown() . " AND " . $criteria->getGalaxyUp();
        }

        if ($criteria->getSystemDown() != null && $criteria->getSystemUp() != null)
        {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `system` BETWEEN " . $criteria->getSystemDown() . " AND " . $criteria->getSystemUp();
        }

        if ($criteria->getRowDown() != null && $criteria->getRowUp() != null)
        {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `row` BETWEEN " . $criteria->getRowDown() . " AND " . $criteria->getRowUp();
        }

        if ($criteria->getIsMoon())
        {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `moon` = 1";
        }

        if ($criteria->getIsInactive())
        {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `status` LIKE ('%i%')";
        }

        $query = $select . $request;
        if ($where != "") {
            $query .= " WHERE " . $where;
        }

        $i = 0;
        foreach ($order_by as $key => $value) {
            if ($i == 0) {
                $query .= " ORDER BY ";
            } else {
                $query .= ", ";
            }

            $query .= $this->db->sql_escape_string($key);
            if ($value == 'DESC') {
                $query .= ' DESC';
            }
            $i++;
        }

        $query .= " LIMIT $start, $number";

        $queryCount = "SELECT count(*) " . $request;
        if ($where != "") {
            $queryCount .= " WHERE " . $where;
        }
        $result = $this->db->sql_query($queryCount);
        list($total_row) = $this->db->sql_fetch_row($result);

        $result = $this->db->sql_query($query);
        $planets = array();
        while ($planet = $this->db->sql_fetch_assoc($result)) {
            $planets[] = $planet;
        }

        return array('total_row' => $total_row, 'planets' => $planets);
    }



}