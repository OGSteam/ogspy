<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Building_Model  extends Model_Abstract
{
    /**
    * Retrieves the list of a user's planets, indexed by coordinates.
     *
    * This method runs an SQL query to get planet identifiers
    * and their coordinates for a given user.
     *
    * @param int $user_id User identifier.
    * @return array Associative array: key = coordinates, value = planet identifier.
     */

    public function get_planet_list($user_id)
    {
        $user_id = (int)$user_id;
        $planet_position = [];

        $request = "SELECT `planet_id`, `coordinates`";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " ORDER BY `planet_id`";
        $result =  $this->db->sql_query($request);
        while (($row = $this->db->sql_fetch_row($result)) !== false && $row !== null) {
            [$planet_id, $coordinates] = $row;
            $planet_position[$coordinates] = $planet_id;
        }
        return $planet_position;
    }
    /**
    * Retrieves the list of a player's moons, indexed by coordinates.
     *
    * @param int $player_id Player identifier.
    * @return array Associative array: key = coordinates, value = moon identifier.
     */
    public function get_moon_list($player_id)
    {
        $moon_position = [];
        // les lunes
        $request = "SELECT `id`, `coordinates`";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id;
        $request .= " AND `type` = 'moon'";
        $request .= " ORDER BY `id`";
        $result =  $this->db->sql_query($request);
        while (($row = $this->db->sql_fetch_row($result)) !== false && $row !== null) {
            [$planet_id, $coordinates] = $row;
            $moon_position[$coordinates] = $planet_id;
        }
        return $moon_position;
    }
    /**
    * Retrieves the number of planets for a specific player.
     *
    * This method runs an SQL query to count planets
    * associated with a given player in the `TABLE_USER_BUILDING` table.
     *
    * @param int $player_id Player identifier whose planets must be counted.
    * @return int Number of player planets.
     */
    public function get_nb_planets(int $player_id)
    {
        $request = "SELECT COUNT(*) ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id . " AND `type` = 'planet'";

        $result =  $this->db->sql_query($request);
        $row = $this->db->sql_fetch_row($result);
        $count = (is_array($row) && isset($row[0])) ? (int)$row[0] : 0;
        return  $count;
    }
    /**
    * Retrieves the number of moons for a specific player.
     *
    * This method runs an SQL query to count moons
    * associated with a given player in the `TABLE_USER_BUILDING` table.
     *
    * @param int $player_id Player identifier whose moons must be counted.
    * @return int Number of player moons.
     */
    public function get_nb_moons(int $player_id)
    {
        $request = "SELECT COUNT(*) ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id . " AND `type` = 'moon'";

        $result = $this->db->sql_query($request);
        $row = $this->db->sql_fetch_row($result);
        $count = (is_array($row) && isset($row[0])) ? (int)$row[0] : 0;
        return  $count;
    }



    /**
    * Retrieves boosters for a specific player.
     *
    * This method queries `TABLE_USER_BUILDING` to retrieve
    * booster information associated with a given player.
     *
    * @param int $player_id Player identifier whose boosters must be retrieved.
    * @return array Associative array containing boosters for each player planet.
    *               Each item is an associative entry with keys:
    *               - `user_id` : User identifier.
    *               - `planet_id` : Planet identifier.
    *               - `boosters` : Associated boosters.
     */
    public function get_all_booster_player(int $player_id)
    {
        $request = "SELECT `player_id`, `id`, `boosters` FROM " . TABLE_USER_BUILDING . " WHERE `player_id`=" . $player_id;
        $result = $this->db->sql_query($request);

        $playerBoosters = array();
        while (($row = $this->db->sql_fetch_row($result)) !== false && $row !== null) {
            [$player_id, $id, $boosters] = $row;
            $playerBoosters[$id] = array("user_id" => $player_id, "planet_id" => $id, "boosters" => $boosters);
        }
        return $playerBoosters;
    }

    /**
    * Retrieves boosters for all users.
     *
    * This method queries `TABLE_USER_BUILDING` to retrieve
    * booster information associated with each user and planet.
     *
    * @return array Array containing boosters for each user and planet.
    *               Each item is an associative entry with keys:
    *               - `user_id` : User identifier.
    *               - `planet_id` : Planet identifier.
    *               - `boosters` : Associated boosters.
     */
    public function get_all_booster()
    {
        $request = "SELECT `user_id`, `planet_id`, `boosters` FROM " . TABLE_USER_BUILDING;
        $result = $this->db->sql_query($request);

        $Boosters = array();
        while (($row = $this->db->sql_fetch_row($result)) !== false && $row !== null) {
            [$user_id, $planet_id, $boosters] = $row;
            $Boosters[] = array("user_id" => $user_id, "planet_id" => $planet_id, "boosters" => $boosters);
        }
        return $Boosters;
    }

    /* Écrit la string de stockage des objets Ogame dans la BDD.
     * @arg id_player   id of the joueur
    * @arg id_planet   planet ID to search for
    * @str_booster     booster storage string (provided by booster_encode() or booster_encodev())
     * @return FALSE on failure
    */
    /**
     * @param $id_player
     * @param $id_planet
     * @param $str_booster
     * @return bool
     */
    public function update_booster($user_id, $planet_id, $boosters)
    {
        $user_id = (int)$user_id;
        $planet_id = (int)$planet_id;
        $boosters = $this->db->sql_escape_string($boosters);

        $requests = "UPDATE " . TABLE_USER_BUILDING . " SET `boosters` = '" . $boosters . "' " .
            " WHERE `user_id` = " . $user_id .
            " AND `planet_id` = " . $planet_id;

        return $this->db->sql_query($requests);
    }

    /**
    * Retrieves the list of buildings associated with a specific player.
     *
    * This method queries `TABLE_USER_BUILDING` to get information
    * about all buildings for a given player, identified by `player_id`.
     *
    * @param int $player_id Unique player identifier whose buildings must be retrieved.
    * @return array Associative array containing buildings for the specified player.
    *               Main keys correspond to unique building identifiers (`id`).
    *               Each entry contains an associative array describing building attributes,
    *               such as:
    *               - `id` : Building identifier.
    *               - `name` : Building name.
    *               - `galaxy` : Galaxy where the building is located.
    *               - `system` : System where the building is located.
    *               - `row` : Slot where the building is located.
    *               - `fields`, `boosters`, `temperature_min`, `temperature_max`, and other
    *                 building-specific attributes.
    *               Each entry details correspond to columns listed in `$tElemList`.
     */
    public function select_player_building_list($player_id)
    {
        $player_id = (int)$player_id;

        $tElemList = array("id", "type", "name", "galaxy", "system","row",  "fields", "boosters", "temperature_min", "temperature_max", "Sat", "Sat_percentage", "FOR", "FOR_percentage", "M", "M_percentage", "C", "C_percentage", "D", "D_percentage", "CES", "CES_percentage", "CEF", "CEF_percentage", "UdR", "UdN", "CSp", "HM", "HC", "HD", "Lab", "Ter", "Silo", "Dock", "BaLu", "Pha", "PoSa", "DdR");

        $request = "SELECT `" . implode("`, `", $tElemList) . "` ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id;
        $request .= " ORDER BY `id`";
        $result =  $this->db->sql_query($request);

        $tbuilding = array();
        while ($row =  $this->db->sql_fetch_assoc($result)) {
            $tbuilding[$row["id"]] = array();
            foreach ($tElemList as $elem) {
                $tbuilding[$row["id"]][$elem] = $row[$elem];
            }
        }
        return $tbuilding;
    }

    /**
    * Retrieves buildings based on the specified silo level.
     *
    * This method queries `TABLE_USER_BUILDING` to retrieve
    * building information where silo level is greater than or equal
    * to the provided level.
     *
    * @param int $silo_level Minimum silo level required to retrieve buildings.
    * @return array Array containing building information matching the criterion.
    *               Each item is an associative entry with keys:
    *               - `user_id` : User identifier.
    *               - `planet_id` : Planet identifier.
    *               - `coordinates` : Building coordinates.
    *               - `Silo` : Silo level.
     */
    public function get_building_by_silo(int $silo_level)
    {
        $query =  "SELECT `id`, `player_id`, `galaxy`, `system`, `row`, `Silo` FROM " . TABLE_USER_BUILDING . " WHERE `Silo` >= $silo_level ";
        $result =  $this->db->sql_query($query);

        $tbuilding = array();
        while ($building =  $this->db->sql_fetch_assoc($result)) {
            $tbuilding[] = $building;
        }

        return $tbuilding;
    }

    /**
    * Deletes an asteroid associated with a specific user.
     *
    * This method deletes the record of a specific asteroid
    * for a given user from the `TABLE_USER_BUILDING` table.
     *
    * @param int $user_id User identifier associated with the asteroid.
    * @param int $aster_id Asteroid identifier to delete.
    * @return void This method does not return a value.
     */
    public function delete_user_aster($user_id, $aster_id)
    {
        $user_id = (int)$user_id;
        $aster_id = (int)$aster_id;

        $request = "DELETE FROM " . TABLE_USER_BUILDING . " WHERE `user_id` = " . $user_id . " AND `planet_id` = " . intval($aster_id);
        $this->db->sql_query($request);
    }
}
