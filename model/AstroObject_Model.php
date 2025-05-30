<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Itori
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;
use Ogsteam\Ogspy\Helper\SearchCriteria_Helper;

class AstroObject_Model extends Model_Abstract
{
    /**
     * Mettre à jour une planète
     * @param array $planet
     */
    public function update(array $planet)
    {
        $query = 'UPDATE ' . TABLE_USER_BUILDING . '
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

        $query = 'INSERT INTO ' . TABLE_USER_BUILDING . ' (`galaxy`, `system`, `row`, `name`, `player`, `ally`, `status`, `last_update`, `last_update_user_id`, `moon`)
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

        $query = "DELETE FROM `" . TABLE_USER_BUILDING . "` WHERE `galaxy` > $newGalaxy OR `system` > $newSystem";
        $this->db->sql_query($query);
    }

    /**
     * Obtiens la liste des planètes située dans les systèmes requis
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

        $population = array();
        // Initialize population array for all slots
        for ($s_idx = $system_down; $s_idx <= $system_up; $s_idx++) {
            foreach (range(1, 15) as $r_idx) {
                $population[$s_idx][$r_idx] = array(
                    "type" => "planet", // Actual data, can override default type
                    "galaxy" => $galaxy,
                    "system" => $s_idx,
                    "row" => $r_idx,
                    "ally_name" => "",
                    "player_name" => "",
                    "Pha" => "",
                    "PoSa" => "",
                    "planet_name" => "", // Planet name
                    "status" => "",
                    "last_update" => "", // Planet last update (p.last_update)
                    "last_update_moon" => "", // From planet's record: p.last_update_moon
                    "last_update_user_id" => "", // Added for user ID of last update
                    "last_update_user_name" => "", // Added for user name of last update
                    "player_last_active" => "" // Player's datadate: gp.datadate
                );
            }
        }

        // 1. Fetch Planets and their direct associated data (player, ally)
        $planet_request = "
                SELECT
                    p.`galaxy`,
                    p.`system`,
                    p.`row`,
                    p.`name` AS planet_name,
                    p.`Pha` AS phalanx,
                    p.`PoSa` AS gate,
                    p.`last_update` AS planet_last_update,
                    p.`last_update_user_id`,
                    u.name AS last_update_user_name,
                    gp.`name` AS player_name,
                    gp.`status` AS player_status,
                    gp.`datadate` AS player_datadate,
                    ga.`name` AS ally_name,
                    ga.`tag` AS ally_tag
                FROM `" . TABLE_USER_BUILDING . "` p
                LEFT JOIN `" . TABLE_GAME_PLAYER . "` gp ON gp.`id` = p.`player_id`
                LEFT JOIN `" . TABLE_GAME_ALLY . "` ga ON ga.`id` = p.`ally_id`
                LEFT JOIN `" . TABLE_USER . "` u ON u.`id` = p.`last_update_user_id`
                WHERE
                    p.`type` = 'planet'
                    AND p.`galaxy` = " . $galaxy . "
                    AND p.`system` BETWEEN " . $system_down . " AND " . $system_up . "
                ORDER BY
                    p.`system`,
                    p.`row`
            ";

        $planet_result = $this->db->sql_query($planet_request);

        if ($this->db->sql_numrows($planet_result) > 0) {
            while ($p_row = $this->db->sql_fetch_assoc($planet_result)) {
                $s = $p_row['system'];
                $r = $p_row['row'];

                // $population[$s][$r] is guaranteed to exist due to pre-initialization
                $population[$s][$r]['planet_name'] = $p_row['planet_name'] ?? "";
                $population[$s][$r]['ally_name'] = $p_row['ally_tag'] ?? "";
                $population[$s][$r]['player_name'] = $p_row['player_name'] ?? "";
                $population[$s][$r]['status'] = $p_row['player_status'] ?? "";
                $population[$s][$r]['Pha'] = $p_row['phalanx'] ?? "0";
                $population[$s][$r]['PoSa'] = $p_row['gate'] ?? "0";
                $population[$s][$r]['last_update'] = $p_row['planet_last_update'] ?? "";
                $population[$s][$r]['last_update_user_id'] = $p_row['last_update_user_id'] ?? ""; // Added user ID
                $population[$s][$r]['last_update_user_name'] = $p_row['last_update_user_name'] ?? ""; // Added user ID
                $population[$s][$r]['player_last_active'] = $p_row['player_datadate'] ?? "";
            }
        }
        // 2. Fetch Moons in the same range
        $moon_request = "SELECT `galaxy`, `system`, `row`, `name` AS moon_name, `Pha` AS phalanx_level, `PoSa` AS gate_level, `last_update_moon` " .
            "FROM `" . TABLE_USER_BUILDING . "` " .
            "WHERE `type` = 'moon' " .
            "  AND `galaxy` = " . $galaxy .
            "  AND `system` BETWEEN " . $system_down . " AND " . $system_up;

        $moon_result = $this->db->sql_query($moon_request);
        $moons_details = [];
        if ($this->db->sql_numrows($moon_result) > 0) {
            while ($m_row = $this->db->sql_fetch_assoc($moon_result)) {
                $s = $m_row['system'];
                $r = $m_row['row'];
                if (!isset($moons_details[$s])) {
                    $moons_details[$s] = [];
                }
                $moons_details[$s][$r] = $m_row;
            }
        }

        // 3. Merge Moon data into Population array
        for ($s_idx = $system_down; $s_idx <= $system_up; $s_idx++) {
            foreach (range(1, 15) as $r_idx) {
                if (isset($moons_details[$s_idx][$r_idx])) {
                    $moon_info = $moons_details[$s_idx][$r_idx];
                    $population[$s_idx][$r_idx]['type'] = 'moon'; // Set type to 'moon' for moon entries
                    // $population[$s_idx][$r_idx]['moon_name'] = $moon_info['moon_name'] ?? ""; // Uncomment if moon name is needed in the view
                    $population[$s_idx][$r_idx]['Pha'] = $moon_info['phalanx_level'] ?? "";
                    $population[$s_idx][$r_idx]['PoSa'] = $moon_info['gate_level'] ?? "";
                    $population[$s_idx][$r_idx]['last_update_moon'] = $moon_info['last_update_moon'] ?? "";
                }
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

        $request = "SELECT count(*) FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `type` = 'planet' AND `galaxy` = " . $galaxy;
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

        $totalPlanets = (1 + ($system_up - $system_down)) * 15;

        $request = "SELECT count(*) FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `type` = 'planet' AND `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $this->db->sql_query($request);
        list($nb_planet_used) = $this->db->sql_fetch_row($result);

        return $totalPlanets - $nb_planet_used;
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

        $request = "SELECT MAX(`last_update`) FROM " . TABLE_USER_BUILDING;
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

        $request = "SELECT DISTINCT ga.`tag` AS `ally` FROM `" . TABLE_USER_BUILDING . "` aub ";
        $request .= "INNER JOIN `" . TABLE_GAME_ALLY . "` ga ON aub.`ally_id` = ga.`id` ";
        $request .= "WHERE ga.`tag` IS NOT NULL AND ga.`tag` != '' ";
        $request .= "ORDER BY `ally` ASC"; // Order by the alias

        $result = $this->db->sql_query($request);

        if ($result) {
            while ($row = $this->db->sql_fetch_assoc($result)) {
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
     * @param $ally_tag_filter
     * @return array
     */
    public function get_ally_position($galaxy, $system_down, $system_up, $ally_tag_filter)
    {
        global $log;
        $galaxy = (int)$galaxy;
        $system_down = (int)$system_down;
        $system_up = (int)$system_up;
        $escaped_ally_tag = $this->db->sql_escape_string($ally_tag_filter);

        $request = "SELECT aub.`galaxy`, aub.`system`, aub.`row`, gp.`name` AS player_name ";
        $request .= "FROM `" . TABLE_USER_BUILDING . "` aub ";
        $request .= "INNER JOIN `" . TABLE_GAME_PLAYER . "` gp ON aub.`player_id` = gp.`id` ";
        $request .= "INNER JOIN `" . TABLE_GAME_ALLY . "` ga ON aub.`ally_id` = ga.`id` ";
        $request .= "WHERE aub.`galaxy` = " . $galaxy . " ";
        $request .= "AND aub.`system` BETWEEN " . $system_down . " AND " . $system_up . " ";
        $request .= "AND ga.`tag` LIKE '" . $escaped_ally_tag . "' ";
        $request .= "ORDER BY gp.`name`, aub.`galaxy`, aub.`system`, aub.`row`";

        $result = $this->db->sql_query($request);
        $population = array();

        if (!$result) {
            $log->error("[OGSpy_AstroObject_Model] get_ally_position - SQL Query FAILED!", ['error' => $this->db->sql_error()]);
        } else {
            // The query selects 4 columns: aub.galaxy, aub.system, aub.row, gp.name AS player_name
            // list() assignment will map these in order.
            while ($row_data = $this->db->sql_fetch_row($result)) {
                $population[] = array(
                    "galaxy" => $row_data[0],
                    "system" => $row_data[1],
                    "row" => $row_data[2],
                    "player" => $row_data[3] // player_name from gp.name is stored in 'player' key for consistency
                );
            }
            $log->info("[OGSpy_AstroObject_Model] get_ally_position - Number of positions found: " . count($population));
            $log->debug("[OGSpy_AstroObject_Model] get_ally_position - Returned population data:", ['population' => $population]); // Changed to debug for potentially large data
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

        $request_astre_name = "SELECT `name` FROM " . TABLE_USER_BUILDING . " WHERE `galaxy` = " . intval($galaxy) . " AND `system` = " . intval($system) . " AND `row` = " . intval($row);
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

        $request_player_name = "SELECT `player` FROM " . TABLE_USER_BUILDING . " WHERE `galaxy` = " . intval($galaxy) . " AND `system` = " . intval($system) . " AND `row` = " . intval($row);
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

        $req = "SELECT ub.`galaxy`, ub.`system`, ub.`row`, ub.`Pha`, ub.`PoSa`, ub.`name`, gp.`name` AS player_name, ga.`tag` AS ally_tag
                FROM " . TABLE_USER_BUILDING . " ub
                LEFT JOIN " . TABLE_GAME_PLAYER . " gp ON ub.`player_id` = gp.`id`
                LEFT JOIN " . TABLE_GAME_ALLY . " ga ON ub.`ally_id` = ga.`id`
                WHERE ub.`galaxy` = '" . $galaxy . "' AND ub.`type` = 'moon' AND ub.`Pha` > 0";

        $result = $this->db->sql_query($req);
        $data = array();
        //Construction liste phalanges
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $data[] = array(
                'galaxy' => $row["galaxy"],
                'system' => $row["system"],
                'row' => $row["row"],
                'name' => $row["name"],
                'ally' => $row["ally_tag"], // Changed from $row["ally"]
                'player' => $row["player_name"], // Changed from $row["player"]
                'gate' => $row["PoSa"], // Assuming PoSa is gate, if not, this might need adjustment based on actual DB schema for gate
                'level' => $row["Pha"] // Assuming Pha is phalanx, if not, this might need adjustment
            );
        }

        return $data;
    }

    /**
     * Récupère les systèmes solaires considérés comme obsolètes selon les critères spécifiés.
     * Un système est considéré comme obsolète lorsque sa dernière mise à jour est antérieure
     * à une date limite définie.
     *
     * @param int $galaxy Numéro de la galaxie à vérifier. Si 0, recherche dans toutes les galaxies.
     * @param int $system_down Limite inférieure pour la plage des systèmes solaires à vérifier.
     * @param int $system_up Limite supérieure pour la plage des systèmes solaires à vérifier.
     * @param int $indice Index correspondant à la période d'obsolescence à utiliser dans le tableau $since.
     * @param array $since Tableau contenant les différentes périodes d'obsolescence disponibles.
     * @param bool $forMoon Si true, recherche les lunes obsolètes. Si false, recherche les planètes obsolètes.
     * @return array Tableau associatif regroupant les systèmes obsolètes par période, avec leurs informations de galaxie, système, position et dernière mise à jour.
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

        $request = "SELECT DISTINCT `galaxy`, `system`" . $row_field . " FROM " . TABLE_USER_BUILDING . " WHERE moon = '" . $moon . "' AND " . $field . " BETWEEN " . $system_up . " AND " . $system_down;
        if ($galaxy != 0) {
            $request .= " AND `galaxy` = " . (int)$galaxy;
        }
        $request .= " ORDER BY `galaxy`, `system`, `row` LIMIT 0, 51";
        $result = $this->db->sql_query($request);


        while ($row = $this->db->sql_fetch_assoc($result)) {
            $request = "SELECT MIN(" . $field . ") FROM " . TABLE_USER_BUILDING . " WHERE `galaxy` = " . $row["galaxy"] . " AND `system` = " . $row["system"];
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

        // Modifié pour inclure les champs nécessaires et les alias correspondants à get_system
        $select = "SELECT uni.`type`, uni.`galaxy`, uni.`system`, uni.`row`, uni.`Pha`, uni.`PoSa`, uni.`last_update_moon`," .
            " ally.`name` AS ally_name, player.`name` AS player_name, player.`status` AS player_status," .
            " uni.`last_update`, user.`name` AS last_update_user_name, uni.`name` AS planet_name," .
            " uni.`last_update_user_id`, player.`datadate` AS player_last_active";
        $request = " FROM " . TABLE_USER_BUILDING . " uni" .
            " LEFT JOIN " . TABLE_USER . "  user  ON uni.`last_update_user_id` = user.`id`" . // uni.last_update_user_id
            " LEFT JOIN " . TABLE_GAME_PLAYER . "  player  ON player.`id`  = uni.`player_id`" .
            " LEFT JOIN " . TABLE_GAME_ALLY . "  ally  ON ally.`id` = uni.`ally_id`";

        $where = "";
        if ($criteria->getPlayerName() != null) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " player.`name` LIKE '" . $this->db->sql_escape_string($criteria->getPlayerName()) . "'";
        }

        if ($criteria->getAllyName() != null) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " ally.`name` LIKE '" . $this->db->sql_escape_string($criteria->getAllyName()) . "'";
        }
        //Binu : changement de la comparaison
        if ($criteria->getPlanetName() !== null) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " uni.`name` LIKE '" . $this->db->sql_escape_string($criteria->getPlanetName()) . "'";
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
            $where .= " `type` = 'moon'";
            //fin
        }

        if ($criteria->getIsInactive()) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " player.`status` LIKE ('%i%')";
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

            $query .= "`{$this->db->sql_escape_string($key)}`";
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

        // Transformation des résultats pour correspondre au format de get_system
        $raw_planets_data = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $raw_planets_data[] = $row;
        }

        $formatted_planets = array();
        foreach ($raw_planets_data as $planet_data) {
            $s = $planet_data['system'];
            $r = $planet_data['row'];

            // Initialiser l'entrée planète/lune avec la structure de type get_system
            $formatted_planets[] = array(
                "galaxy" => $planet_data['galaxy'],
                "system" => $s,
                "row" => $r,
                "ally_name" => $planet_data['ally_name'] ?? "",
                "player_name" => $planet_data['player_name'] ?? "",
                "type" => $planet_data['type'],
                "Pha" => $planet_data['Pha'] ?? "",
                "PoSa" => $planet_data['PoSa'] ?? "",
                "planet_name" => $planet_data['planet_name'] ?? "",
                "status" => $planet_data['player_status'] ?? "", // de player.status
                "last_update" => $planet_data['last_update'] ?? "", // de uni.last_update
                "last_update_moon" => $planet_data['last_update_moon'] ?? $planet_data['last_update'],
                "last_update_user_id" => $planet_data['last_update_user_id'] ?? "",
                "last_update_user_name" => $planet_data['last_update_user_name'] ?? "", // de user.name
                "player_last_active" => $planet_data['player_last_active'] ?? "" // de player.datadate
            );
        }

        return array('total_row' => $total_row, 'planets' => $formatted_planets);
    }
}
