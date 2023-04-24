<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Itori
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;
use Ogsteam\Ogspy\Helper\SearchCriteria_Helper;

class Universe_Model extends Model_Abstract
{
    /**
     * Mettre à jour une planète
     * @param array $planet
     */
    public function update(array $planet)
    {
        $query = 'UPDATE ' . TABLE_UNIVERSE . '
                  SET `name` = "' . $this->db->sql_escape_string($planet['planet_name']) . '",
                      `player` = "' . $this->db->sql_escape_string($planet['player_name']) . '",
                      `ally` = "' . $this->db->sql_escape_string($planet['ally_tag']) . '",
                      `status` = "' . $this->db->sql_escape_string($planet['status']) . '",
                      `moon` = "' . $this->db->sql_escape_string($planet['moon']) . '",
                      `last_update` = ' . (int)$planet['last_update'] . ',
                      `last_update_user_id` = ' . (int)$planet['last_update_user_id']
            . ' WHERE `galaxy` = ' . (int)$planet['galaxy'] . ' AND `system` = ' . (int)$planet['system'] . ' AND `row` = ' . (int)$planet['row'];

        $this->db->sql_query($query);
    }

    /**
     * Ajouter une nouvelle planète
     * @param array $planet
     */
    public function add(array $planet)
    {

        $query = 'INSERT INTO ' . TABLE_UNIVERSE . ' (`galaxy`, `system`, `row`, `name`, `player`, `ally`, `status`, `last_update`, `last_update_user_id`, `moon`)
                         VALUES (' . (int)$planet['galaxy'] . ',
                                 ' . (int)$planet['system'] . ',
                                 ' . (int)$planet['row'] . ',
                                 "' . $this->db->sql_escape_string($planet['planet_name']) . '",
                                 "' . $this->db->sql_escape_string($planet['player_name']) . '",
                                 "' . $this->db->sql_escape_string($planet['ally_tag']) . '",
                                 "' . $this->db->sql_escape_string($planet['status']) . '",
                                 ' . (int)$planet['last_update'] . ',
                                 ' . (int)$planet['last_update_user_id'] . ',
                                 "' . $this->db->sql_escape_string($planet['moon']) . '")';
        $this->db->sql_query($query);
    }

    /**
     * Supprime les galaxies et les systèmes supérieurs aux nouvelles limites
     * @param $newGalaxy int nouveau nombre de galaxies
     * @param $newSystem int nouveau nombre de systèmes
     */
    public function resize_universe($newGalaxy, $newSystem)
    {
        $newGalaxy = (int)$newGalaxy;
        $newSystem = (int)$newSystem;

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
        $galaxy = (int)$galaxy;
        $system_down = (int)$system_down;
        $system_up = (int)$system_up;

        $request = "SELECT `galaxy`, `system`, `row`, `name`, `ally`, `player`, `moon`, `phalanx`, `gate`, `last_update_moon`, `status`, `last_update`, `user_name`
                    FROM " . TABLE_UNIVERSE . "
                        LEFT JOIN " . TABLE_USER . "
                            ON `user_id` = `last_update_user_id`
                    WHERE `galaxy` = $galaxy AND `system` BETWEEN $system_down AND $system_up
                    ORDER BY `system`, `row`";
        $result = $this->db->sql_query($request);

        $population = array();
        for ($system = $system_down; $system <= $system_up; $system++) {
            foreach (range(1, 15) as $row) {
                $population[$system][$row] = array(
                    "galaxy" => $galaxy,
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
                    "poster" => ""
                );
            }
        }

        if ($this->db->sql_numrows($result) > 0) {

            while ($row = $this->db->sql_fetch_assoc($result)) {
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
        $galaxy = (int)$galaxy;
        $system_down = (int)$system_down;
        $system_up = (int)$system_up;

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
        $galaxy = (int)$galaxy;
        $system_down = (int)$system_down;
        $system_up = (int)$system_up;

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
        $galaxy = (int)$galaxy;
        $system_down = (int)$system_down;
        $system_up = (int)$system_up;

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
        $galaxy = (int)$galaxy;
        $system_down = (int)$system_down;
        $system_up = (int)$system_up;
        $ally_name = $this->db->sql_escape_string($ally_name);

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
        $galaxy = (int)$galaxy;
        $system = (int)$system;
        $row = (int)$row;

        $request_astre_name = "SELECT `name` FROM " . TABLE_UNIVERSE . " WHERE `galaxy` = " . intval($galaxy) . " AND `system` = " . intval($system) . " AND `row` = " . intval($row);
        $result_astre_name = $this->db->sql_query($request_astre_name);
        $astre_name = $this->db->sql_fetch_assoc($result_astre_name); //Récupère le nom de la planète

        return $astre_name;
    }

    /**
     * Retourne le nom du joueur
     * @param $galaxy
     * @param $system
     * @param $row
     * @return mixed
     */
    public function get_player_name($galaxy, $system, $row)
    {
        $galaxy = (int)$galaxy;
        $system = (int)$system;
        $row = (int)$row;

        $request_player_name = "SELECT `player` FROM " . TABLE_UNIVERSE . " WHERE `galaxy` = " . intval($galaxy) . " AND `system` = " . intval($system) . " AND `row` = " . intval($row);
        $result_player_name = $this->db->sql_query($request_player_name);
        $player_name = $this->db->sql_fetch_assoc($result_player_name); //Récupère le nom de la planète

        return $player_name;
    }

    /**
     * Retourne la liste des phalanges d'une galaxie
     * @param $galaxy
     * @return array
     */
    public function get_phalanx($galaxy)
    {
        $galaxy = (int)$galaxy;

        $req = "SELECT `galaxy`, `system`, `row`, `phalanx`, `gate`, `name`, `ally`, `player` FROM " . TABLE_UNIVERSE . " WHERE `galaxy` = '" . $galaxy . "' AND `moon` = '1' AND `phalanx` > 0";

        $result = $this->db->sql_query($req);
        $data = array();
        //Construction liste phalanges
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $data[] = array(
                'galaxy' => $row["galaxy"],
                'system' => $row["system"],
                'row' => $row["row"],
                'name' => $row["name"],
                'ally' => $row["ally"],
                'player' => $row["player"],
                'gate' => $row["gate"],
                'level' => $row["phalanx"]
            );
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
    public function get_galaxy_obsolete($galaxy, $system_down, $system_up, $indice, $since, $forMoon = false)
    {
        $galaxy = (int)$galaxy;
        $system_down = (int)$system_down;
        $system_up = (int)$system_up;
        $indice = (int)$indice;
        $since = (int)$since;
        $forMoon = (bool)$forMoon;


        $obsolete = array();

        $field = "last_update";
        $row_field = "";
        $moon = 0;
        if ($forMoon) {
            $field = "last_update_moon";
            $row_field = ", `row`";
            $moon = 1;
        }

        $request = "SELECT DISTINCT `galaxy`, `system`" . $row_field . " FROM " . TABLE_UNIVERSE . " WHERE moon = '" . $moon . "' AND " . $field . " BETWEEN " . $system_up . " AND " . $system_down;
        if ($galaxy != 0) {
            $request .= " AND `galaxy` = " . (int)$galaxy;
        }
        $request .= " ORDER BY `galaxy`, `system`, `row` LIMIT 0, 51";
        $result = $this->db->sql_query($request);


        while ($row = $this->db->sql_fetch_assoc($result)) {
            $request = "SELECT MIN(" . $field . ") FROM " . TABLE_UNIVERSE . " WHERE `galaxy` = " . $row["galaxy"] . " AND `system` = " . $row["system"];
            $result2 = $this->db->sql_query($request);
            list($last_update) = $this->db->sql_fetch_row($result2);
            $row["last_update"] = $last_update;

            $obsolete[$since[$indice]][] = $row;
        }
        return $obsolete;
    }


    public function find(SearchCriteria_Helper $criteria, array $order_by = array(), $start = 0, $number = 30)
    {
        $start = (int)$start;
        $number = (int)$number;


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
        //Binu : changement de la comparaison
        if ($criteria->getPlanetName() !== null) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `name` LIKE '" . $this->db->sql_escape_string($criteria->getPlanetName()) . "'";
        }

        if ($criteria->getGalaxyDown() != null && $criteria->getGalaxyUp() != null && !$criteria->getIsSpied()) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `galaxy` BETWEEN " . $criteria->getGalaxyDown() . " AND " . $criteria->getGalaxyUp();
        }

        if ($criteria->getSystemDown() != null && $criteria->getSystemUp() != null && !$criteria->getIsSpied()) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `system` BETWEEN " . $criteria->getSystemDown() . " AND " . $criteria->getSystemUp();
        }

        if ($criteria->getRowDown() != null && $criteria->getRowUp() != null && !$criteria->getIsSpied()) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `row` BETWEEN " . $criteria->getRowDown() . " AND " . $criteria->getRowUp();
        }

        if ($criteria->getIsMoon()) {
            if ($where != "") {
                $where .= " AND ";
            }
            //Binu : ajout des cotes
            $where .= " `moon` = '1'";
            //fin
        }

        if ($criteria->getIsInactive()) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " `status` LIKE ('%i%')";
        }

        //Binu : Ajout du critère espionné
        if ($criteria->getIsSpied()) {

            //Binu : Récupération des planètes espionnées

            $spy_query = "SELECT `coordinates`, `active` FROM " . TABLE_PARSEDSPY . " WHERE `active` = '1'";
            $spy_where = "";
            if (($criteria->getGalaxyDown() !== null && $criteria->getGalaxyUp() !== null) || ($criteria->getSystemDown() !== null && $criteria->getSystemUp() !== null) || ($criteria->getRowDown() !== null && $criteria->getRowUp() !== null)) {
                $coordinates = $criteria->getArrayCoordinates();
                $spy_where .= " AND ";
                $nb_coord = count($coordinates);
                if ($nb_coord > 1) {
                    $spy_where .= "(";
                }
                for ($i = 0; $i < $nb_coord; $i++) {
                    $spy_where .= "`coordinates` = '" . $coordinates[$i] . "'";
                    if ($nb_coord > 1 && $i != ($nb_coord - 1)) {
                        $spy_where .= " OR ";
                    }
                }
                if ($nb_coord > 1) {
                    $spy_where .= ")";
                }
            }
            $spy_query .= $spy_where . " ORDER BY `coordinates`";
            $spy_result = $this->db->sql_query($spy_query);
            while ($spy_coordinate = $this->db->sql_fetch_assoc($spy_result)) {
                $spy_coordinates[] = $spy_coordinate['coordinates'];
            }

            //Binu : Sélection des planètes ayant été espionnées
            if (isset($spy_coordinates)) {
                if ($where != "") {
                    $where .= " AND ";
                }
                for ($i = 0; $i < count($spy_coordinates); $i++) {
                    $split = explode(":", $spy_coordinates[$i]);
                    $galaxy = $split[0];
                    $system = $split[1];
                    $row = $split[2];
                    if (count($spy_coordinates) > 1) {
                        $where .= "(";
                    }
                    $where .= "`galaxy` = " . $galaxy . " AND `system` = " . $system . " AND `row` = " . $row;
                    if (count($spy_coordinates) > 1) {
                        $where .= ")";
                        if ($i < (count($spy_coordinates) - 1)) {
                            $where .= " OR ";
                        }
                    }
                }
            } else {
                //Binu : Si aucune planète de l'intervalle paramétré n'a été espionné, on s'assure qu'il n'y aura pas de résultat
                $where .= "`galaxy` = 0";
            }
        }
        //Fin correctif

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
