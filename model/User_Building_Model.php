<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class User_Building_Model  extends Model_Abstract
{
    /**
     * @param $user_id
     * @return mixed
     */
    public function get_planet_list($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `planet_id`, `coordinates`";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " AND `planet_id` <= 199";
        $request .= " ORDER BY `planet_id`";
        $result =  $this->db->sql_query($request);
        while (list($planet_id, $coordinates) = $this->db->sql_fetch_row($result)) {
            $planet_position[$coordinates] = $planet_id;
        }
        return $planet_position;
    }
    /**
     * @param $user_id
     * @return mixed
     */
    public function get_moon_list($user_id)
    {
        $user_id = (int)$user_id;

        // les lunes
        $request = "SELECT `planet_id`, `coordinates`";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " AND `planet_id` > 199";
        $request .= " ORDER BY `planet_id`";
        $result =  $this->db->sql_query($request);
        while (list($planet_id, $coordinates) = $this->db->sql_fetch_row($result)) {
            $moon_position[$coordinates] = $planet_id;
        }
        return $moon_position;
    }
    /**
     * @param $user_id
     * @return int|\Ogsteam\Ogspy\the
     */
    public function get_nb_planets($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `planet_id` ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " AND `planet_id` < 199 ";
        $request .= " ORDER BY `planet_id`";
        $result =  $this->db->sql_query($request);
        return  $this->db->sql_numrows($result);
    }
    /**
     * @param $user_id
     * @return int|\Ogsteam\Ogspy\the
     */
    public function get_nb_moons($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `planet_id` ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " AND `planet_id` > 199 ";
        $request .= " ORDER BY `planet_id`";
        $result =  $this->db->sql_query($request);
        return  $this->db->sql_numrows($result);
    }



    /**
     * Recupere les boosters d'un utilisateur
     */
    public function get_all_booster_player($id_player)
    {
        $id_player = (int)$id_player;

        $request = "SELECT `user_id`, `planet_id`, `boosters` FROM " . TABLE_USER_BUILDING . " WHERE `user_id`=" . $id_player;
        $result = $this->db->sql_query($request);

        $Boosters = array();
        while (list($user_id, $planet_id, $boosters) = $this->db->sql_fetch_row($result)) {
            $Boosters[$planet_id] = array("user_id" => $user_id, "planet_id" => $planet_id, "boosters" => $boosters);
        }
        return $Boosters;
    }

    /**
     * Recupere les boosters de tous les utilisateurs
     */
    public function get_all_booster()
    {
        $request = "SELECT `user_id`, `planet_id`, `boosters` FROM " . TABLE_USER_BUILDING;
        $result = $this->db->sql_query($request);

        $Boosters = array();
        while (list($user_id, $planet_id, $boosters) = $this->db->sql_fetch_row($result)) {
            $Boosters[] = array("user_id" => $user_id, "planet_id" => $planet_id, "boosters" => $boosters);
        }
        return $Boosters;
    }

    /* Écrit la string de stockage des objets Ogame dans la BDD.
     * @arg id_player   id du joueur
     * @arg id_planet   id de la planète à rechercher
     * @str_booster     string de stockage des boosters (donnée par les fonctions booster_encode() ou booster_encodev())
     * @return FALSE en cas d'échec
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
     * @param $user_id
     * @return array
     */
    public function select_user_building_list($user_id)
    {
        $user_id = (int)$user_id;

        $tElemList = array("planet_id", "planet_name", "coordinates", "fields", "boosters", "temperature_min", "temperature_max", "Sat", "Sat_percentage", "FOR", "FOR_percentage", "M", "M_percentage", "C", "C_percentage", "D", "D_percentage", "CES", "CES_percentage", "CEF", "CEF_percentage", "UdR", "UdN", "CSp", "HM", "HC", "HD", "Lab", "Ter", "Silo", "Dock", "BaLu", "Pha", "PoSa", "DdR");

        $request = "SELECT `" . implode("`, `", $tElemList) . "` ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " ORDER BY `planet_id`";
        $result =  $this->db->sql_query($request);

        $tbuilding = array();
        while ($row =  $this->db->sql_fetch_assoc($result)) {
            $tbuilding[$row["planet_id"]] = array();
            foreach ($tElemList as $elem) {
                $tbuilding[$row["planet_id"]][$elem] = $row[$elem];
            }
        }
        return $tbuilding;
    }

    public function get_building_by_silo($silo_level)
    {
        $silo_level = (int)$silo_level;

        $request =  "SELECT `user_id`, `planet_id`, `coordinates`, `Silo` FROM " . TABLE_USER_BUILDING . " WHERE `Silo` >= $silo_level ";
        $result =  $this->db->sql_query($request);

        $tbuilding = array();
        while ($building =  $this->db->sql_fetch_assoc($result)) {
            $tbuilding[] = $building;
        }

        return $tbuilding;
    }

    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     */
    public function update_moon_id($user_id, $previous_id, $new_id)
    {
        $user_id = (int)$user_id;
        $previous_id = (int)$previous_id;
        $new_id = (int)$new_id;

        $request = "UPDATE " . TABLE_USER_BUILDING . " SET `planet_id`  = " . $new_id .
            " WHERE `planet_id` = " . $previous_id . " and `user_id` = " . $user_id;
        $this->db->sql_query($request);
        //We adjust the id if we go upper than 299
        $request = "UPDATE " . TABLE_USER_BUILDING .
            " SET planet_id  = `planet_id` -100 WHERE `planet_id` > 299 and `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     */
    public function update_planet_id($user_id, $previous_id, $new_id)
    {
        $user_id = (int)$user_id;
        $previous_id = (int)$previous_id;
        $new_id = (int)$new_id;

        $request = "UPDATE " . TABLE_USER_BUILDING . " SET `planet_id`  = " . $new_id .
            " WHERE `planet_id` = " . $previous_id . " and `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $aster_id Planet or moon to be deleted
     */
    public function delete_user_aster($user_id, $aster_id)
    {
        $user_id = (int)$user_id;
        $aster_id = (int)$aster_id;

        $request = "DELETE FROM " . TABLE_USER_BUILDING . " WHERE `user_id` = " . $user_id . " AND `planet_id` = " . intval($aster_id);
        $this->db->sql_query($request);
    }
}
