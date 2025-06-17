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
     * Updates the details of a planet in the database.
     *
     * This method constructs and executes an SQL query to update the record
     * for a specific planet in the user building table. The update is performed
     * based on the galaxy, system, and row coordinates of the planet.
     *
     * @param array $planet An associative array containing the following keys:
     *                       - planet_name: The name of the planet (string).
     *                       - player_name: The name of the player owning the planet (string).
     *                       - ally_tag: The ally tag associated with the player (string).
     *                       - status: The status of the planet (string).
     *                       - moon: Information about the presence of a moon (string).
     *                       - last_update: The timestamp of the last update (int).
     *                       - last_update_user_id: The ID of the user who last updated (int).
     *                       - galaxy: The galaxy in which the planet is located (int).
     *                       - system: The system in which the planet is located (int).
     *                       - row: The row in which the planet is located (int).
     *
     * @return void This method does not return any value.
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
     * Adds a new planet entry to the database.
     *
     * This method inserts a new record into the user building database table with
     * the provided planet details, including galaxy, system, row, planet name,
     * player name, ally tag, status, last update, last update user, and moon information.
     *
     * @param array $planet An associative array containing the planet details:
     *                      - 'galaxy' (int): The galaxy number of the planet.
     *                      - 'system' (int): The system number of the planet.
     *                      - 'row' (int): The row number where the planet is located.
     *                      - 'planet_name' (string): The name of the planet.
     *                      - 'player_name' (string): The name of the player owning the planet.
     *                      - 'ally_tag' (string): The alliance tag of the player.
     *                      - 'status' (string): The status of the planet.
     *                      - 'last_update' (int): The timestamp of the last update.
     *                      - 'last_update_user_id' (int): The ID of the user who performed the last update.
     *                      - 'moon' (string): Indicates whether the planet has a moon.
     *
     * @return void
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
     * Resizes the universe by removing entries in the user building table that exceed the specified galaxy and system limits.
     *
     * This method executes a SQL query to delete records from the database where the galaxy number is greater than the specified new galaxy limit
     * or the system number is greater than the specified new system limit.
     *
     * @param int $newGalaxy The maximum allowed galaxy value. Entries with a galaxy number greater than this value will be deleted.
     * @param int $newSystem The maximum allowed system value. Entries with a system number greater than this value will be deleted.
     *
     * @return void This method does not return any value.
     */
    public function resize_universe($newGalaxy, $newSystem)
    {
        $newGalaxy = (int)$newGalaxy;
        $newSystem = (int)$newSystem;

        $query = "DELETE FROM `" . TABLE_USER_BUILDING . "` WHERE `galaxy` > $newGalaxy OR `system` > $newSystem";
        $this->db->sql_query($query);
    }

    /**
     * Retrieves a detailed overview of planets and moons within a specified galaxy and system range.
     *
     * This method fetches data for all slots (1-15) in each system within a specified range,
     * including information about planets, players, alliances, and moons. The data is returned
     * in a multi-dimensional array, organized by system and slot, with each entry containing
     * attributes such as player status, phalanx level, gate level, and update metadata.
     *
     * @param int $galaxy The galaxy number for which data should be retrieved.
     * @param int $system_down The starting system number of the range.
     * @param int $system_up The ending system number of the range.
     *
     * @return array A multi-dimensional array representing the population in the given galaxy and system range.
     *               Each entry contains associated data for planets, moons, players, and alliances.
     */
    public function get_system(int $galaxy, int $system_down, int $system_up)
    {
        $population = array();
        // Initialize population array for all slots
        for ($s_idx = $system_down; $s_idx <= $system_up; $s_idx++) {
            foreach (range(1, 15) as $r_idx) {
                $population[$s_idx][$r_idx] = array(
                    "type" => "planet", // Actual data, can override default type
                    "galaxy" => $galaxy,
                    "system" => $s_idx,
                    "row" => $r_idx,
                    "ally_id" => "", // Ally ID from game_ally table
                    "ally_name" => "",
                    "player_id" => "", // Player ID from game_player table
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
                    gp.`id` AS player_id,
                    gp.`status` AS player_status,
                    gp.`datadate` AS player_datadate,
                    ga.`id` AS ally_id,
                    ga.`name` AS ally_name,
                    ga.`tag` AS ally_tag
                FROM `" . TABLE_USER_BUILDING . "` p
                LEFT JOIN `" . TABLE_GAME_PLAYER . "` gp ON gp.`id` = p.`player_id`
                LEFT JOIN `" . TABLE_GAME_ALLY . "` ga ON ga.`id` = gp.`ally_id`
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
                $population[$s][$r]['ally_id'] = $p_row['ally_id'] ?? "";
                $population[$s][$r]['player_name'] = $p_row['player_name'] ?? "";
                $population[$s][$r]['player_id'] = $p_row['player_id'] ?? "";
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
     * Retrieves the number of planets within a specified galaxy and system range.
     *
     * This method counts the number of entries in the user building table
     * that are of type 'planet' and fall within the specified galaxy and
     * system range.
     *
     * @param int $galaxy The galaxy to search within.
     * @param int $system_down The lower bound of the system range.
     * @param int $system_up The upper bound of the system range.
     * @return int The number of planets found within the specified parameters.
     */
    public function get_nb_planets(int $galaxy, int $system_down, int $system_up)
    {
        $request = "SELECT count(*) FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `type` = 'planet' AND `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $this->db->sql_query($request);
        list($nb_planet) = $this->db->sql_fetch_row($result);

        return $nb_planet;
    }

    /**
     * Calculates the number of empty planets within a specified range of systems in a galaxy.
     *
     * This method determines the total number of potential planets in the given systems
     * and subtracts the number of planets already occupied. The data is retrieved by
     * querying the database to count the occupied planets in the specified range.
     *
     * @param int $galaxy The galaxy in which to count empty planets.
     * @param int $system_down The starting system in the range.
     * @param int $system_up The ending system in the range.
     * @return int The total number of empty planets in the specified galaxy and system range.
     */
    public function get_nb_empty_planets(int $galaxy, int $system_down, int $system_up)
    {
        $totalPlanets = (1 + ($system_up - $system_down)) * 15;

        $request = "SELECT count(*) FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `type` = 'planet' AND `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $this->db->sql_query($request);
        list($nb_planet_used) = $this->db->sql_fetch_row($result);

        return $totalPlanets - $nb_planet_used;
    }

    /**
     * Retrieves the last update timestamp for systems within a specified range in a galaxy.
     *
     * This method executes a SQL query to fetch the maximum value of the `last_update`
     * column from the user building table for a specified galaxy and a range of systems.
     *
     * @param int $galaxy The galaxy identifier to filter the query results.
     * @param int $system_down The lower bound of the system range.
     * @param int $system_up The upper bound of the system range.
     * @return string|null The last update timestamp retrieved from the database, or null if no data is found.
     */
    public function get_last_update(int $galaxy, int $system_down, int $system_up)
    {
        $request = "SELECT MAX(`last_update`) FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `galaxy` = " . $galaxy;
        $request .= " AND `system` BETWEEN " . $system_down . " AND " . ($system_up);

        $result = $this->db->sql_query($request);
        list($last_update) = $this->db->sql_fetch_row($result);

        return $last_update;
    }

    /**
     * Retrieves a list of ally tags from the database.
     *
     * This method executes a SQL query to fetch distinct ally tags
     * from the user building and game ally tables, where the tags are
     * not null or empty. The results are ordered alphabetically by ally tag.
     *
     * @return array An array of ally tags retrieved from the database.
     */
    public function get_ally_list()
    {
        $ally_list = [];

        $request = "SELECT DISTINCT ally.`tag` AS `ally` FROM `" . TABLE_USER_BUILDING . "` aub ";
        $request .= "INNER JOIN `" . TABLE_GAME_PLAYER . "` player ON aub.`player_id` = player.`id` ";
        $request .= "INNER JOIN `" . TABLE_GAME_ALLY . "` ally ON player.`ally_id` = ally.`id` ";
        $request .= "WHERE ally.`tag` IS NOT NULL AND ally.`tag` != '' ";
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
     * Retrieves the positions of ally members within a specific galaxy and system range.
     *
     * This method performs a SQL query to fetch positional data and player names
     * for members of a specified alliance within a given galaxy and system range.
     * The results are ordered by player name, galaxy, system, and row.
     *
     * @param int $galaxy The galaxy number to search within.
     * @param int $system_down The lower bound of the system range.
     * @param int $system_up The upper bound of the system range.
     * @param string $ally_tag_filter The tag of the ally to filter positions by.
     *
     * @return array An array of positions, where each position contains the "galaxy",
     *               "system", "row", and "player" keys.
     */
    public function get_ally_position(int $galaxy, int $system_down, int $system_up, string $ally_tag_filter)
    {
        global $log;
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
     * Retrieves the name of a planet by its astronomical object ID.
     *
     * This method executes a SQL query to fetch the name of a planet
     * from the user building table where the ID matches the provided
     * astronomical object ID and the type is set to 'planet'.
     *
     * @param int $astroObjectId The unique ID of the astronomical object used to identify the planet.
     * @return array|null An associative array containing the planet's name if found, or null if no match is found.
     */
    public function getPlanetNameByObjectId(int $astroObjectId)
    {
        $request_astre_name = "SELECT `name` FROM " . TABLE_USER_BUILDING . " WHERE `id` = " . $astroObjectId . " AND `type` = 'planet'";
        $result_astre_name = $this->db->sql_query($request_astre_name);
        [$astre_name] = $this->db->sql_fetch_row($result_astre_name); //Récupère le nom de la planète

        return $astre_name;
    }

    /**
     * Retrieves the name of a player associated with a specific astronomical object.
     *
     * This method executes a SQL query to fetch the name of the player
     * linked to the given astronomical object's ID by joining the user building
     * and game player tables.
     *
     * @param int $astroObject_id The ID of the astronomical object used to identify the player.
     * @return string The string containing the player's name retrieved from the database.
     */
    public function get_player_name(int $astroObject_id): string
    {
        $request_player_name = "SELECT p.`name` FROM " . TABLE_USER_BUILDING . " obj INNER JOIN " . TABLE_GAME_PLAYER . " p ON obj.player_id = p.id WHERE obj.`id` = " . $astroObject_id;
        $result_player_name = $this->db->sql_query($request_player_name);
        [$playerName] = $this->db->sql_fetch_row($result_player_name); //Récupère le nom du joueur
        return $playerName;
    }

    /**
     * Retrieves the planet ID based on given galaxy, system, and row coordinates.
     *
     * This method executes a SQL query to fetch the ID of a planet from the database
     * that matches the provided galaxy, system, and row coordinates.
     *
     * @param int $galaxy The galaxy coordinate of the planet.
     * @param int $system The system coordinate of the planet.
     * @param int $row The row coordinate of the planet.
     * @return int|null The ID of the planet if found, or null if no match is found.
     */
    public function get_planetId_by_coordinates(int $galaxy, int $system, int $row)
    {
        $request_planet_id = "SELECT `id` FROM " . TABLE_USER_BUILDING . " WHERE `galaxy` = " . $galaxy . " AND `system` = " . $system . " AND `row` = " . $row;
        $result_planet_id = $this->db->sql_query($request_planet_id);
        [$planet_id] = $this->db->sql_fetch_row($result_planet_id); //Récupère le nom de la planète

        return $planet_id;
    }

    /**
     * Retrieves the coordinates of a planet based on its object ID.
     *
     * This method executes a SQL query to fetch the galaxy, system, and row
     * values associated with a specific astronomical object ID from the user
     * building database table.
     *
     * @param int $astroobject_id The ID of the astronomical object for which to retrieve the coordinates.
     * @return array An array containing the galaxy, system, and row coordinates of the planet.
     */
    public function getPlanetCoordsByObjectId(int $astroObject_id): array
    {
        $galaxy = 0;
        $system = 0;
        $row = 0;

        $request_planet_id = "SELECT `galaxy`, `system`, `row` FROM " . TABLE_USER_BUILDING . " WHERE `id` = " . $astroObject_id;
        $result_planet_id = $this->db->sql_query($request_planet_id);
        if ($result_planet_id) {
            [$galaxy, $system, $row] = $this->db->sql_fetch_row($result_planet_id);
        }

        return [$galaxy, $system, $row];
    }


    /**
     * Retrieves a list of phalanxes in a specific galaxy.
     *
     * This method executes a SQL query to fetch information about moons equipped with phalanxes
     * in the specified galaxy. The result includes details such as galaxy, system, row, moon name,
     * player name, ally tag, gate level, and phalanx level.
     *
     * @param int $galaxy The galaxy number to search for phalanxes.
     * @return array An array of data about phalanxes, where each item contains the galaxy, system, row,
     *               moon name, ally tag, player name, gate level, and phalanx level.
     */
    public function get_phalanx(int $galaxy): array
    {
        $req = "SELECT ub.`galaxy`, ub.`system`, ub.`row`, ub.`Pha`, ub.`PoSa`, ub.`name`, gp.`name` AS player_name, ga.`tag` AS ally_tag
                FROM " . TABLE_USER_BUILDING . " ub
                LEFT JOIN " . TABLE_GAME_PLAYER . " gp ON ub.`player_id` = gp.`id`
                LEFT JOIN " . TABLE_GAME_ALLY . " ga ON gp.`ally_id` = ga.`id`
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
    public function get_galaxy_obsolete(int $galaxy, int $system_down, int $system_up, int $indice, int $since, bool $forMoon = false)
    {
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


    public function find(SearchCriteria_Helper $criteria, array $order_by = [], int $start = 0, int $number = 30)
    {
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
