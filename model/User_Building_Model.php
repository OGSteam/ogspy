<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;


class User_Building_Model
{
    /**
     * @param $user_id
     * @return mixed
     */
    public function get_planet_list($user_id) {

        global $db;
        $request = "select planet_id, coordinates";
        $request .= " from " . TABLE_USER_BUILDING;
        $request .= " where user_id = " . $user_id;
        $request .= " and planet_id <= 199";
        $request .= " order by planet_id";
        $result = $db->sql_query($request);

        while (list($planet_id, $coordinates) = $db->sql_fetch_row($result)) {
            $planet_position[$coordinates] = $planet_id;
        }

        return $planet_position;

    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function get_moon_list($user_id) {

        global $db;
        // les lunes
        $request = "select planet_id, coordinates";
        $request .= " from " . TABLE_USER_BUILDING;
        $request .= " where user_id = " . $user_id;
        $request .= " and planet_id > 199";
        $request .= " order by planet_id";
        $result = $db->sql_query($request);

        while (list($planet_id, $coordinates) = $db->sql_fetch_row($result)) {
            $moon_position[$coordinates] = $planet_id;
        }

        return $moon_position;
    }

    /**
     * @param $user_id
     * @return int|\Ogsteam\Ogspy\the
     */
    public function get_nb_planets($user_id) {

        global $db;
        $request = "SELECT planet_id ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE user_id = " . $user_id;
        $request .= " AND planet_id < 199 ";
        $request .= " ORDER BY planet_id";

        $result = $db->sql_query($request);

        //mini 9 pour eviter bug affichage
        /*if ($db->sql_numrows($result) <= 9)
            return 9;*/

        return $db->sql_numrows($result);

    }
    /**
     * @param $user_id
     * @return int|\Ogsteam\Ogspy\the
     */
    public function get_nb_moons($user_id)
    {

        global $db;
        $request = "select planet_id ";
        $request .= " from " . TABLE_USER_BUILDING;
        $request .= " where user_id = " . $user_id;
        $request .= " and planet_id > 199 ";
        $request .= " order by planet_id";

        $result = $db->sql_query($request);

        //mini 9 pour eviter bug affichage
        if ($db->sql_numrows($result) <= 9) {
                    return 9;
        }

        return $db->sql_numrows($result);
    }

    /**
     * @param $user_id
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function select_user_building_list($user_id) {
        global $db;
        $request = "SELECT planet_id, planet_name, coordinates, fields, boosters, temperature_min, temperature_max, Sat, Sat_percentage, M, M_percentage, C, C_Percentage, D, D_percentage, CES, CES_percentage, CEF, CEF_percentage, UdR, UdN, CSp, HM, HC, HD, Lab, Ter, Silo, BaLu, Pha, PoSa, DdR";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE user_id = " . $user_id;
        $request .= " ORDER BY planet_id";
        $result = $db->sql_query($request);

        return $result;

    }

    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     */
    public function update_moon_id($user_id, $previous_id, $new_id) {

        global $db;
        $request = "UPDATE " . TABLE_USER_BUILDING . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $db->sql_query($request);

        //We adjust the id if we go upper than 299
        $request = "UPDATE " . TABLE_USER_BUILDING .
            " SET planet_id  = planet_id -100 WHERE  planet_id > 299 and user_id = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     */
    public function update_planet_id($user_id, $previous_id, $new_id) {

        global $db;
        $request = "UPDATE " . TABLE_USER_BUILDING . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $aster_id Planet or moon to be deleted
     */
    public function delete_user_aster($user_id, $aster_id) {

        global $db;
        $request = "DELETE FROM " . TABLE_USER_BUILDING . " WHERE `user_id` = " . $user_id . " AND `planet_id` = " . intval($aster_id);
        $db->sql_query($request);

    }
}