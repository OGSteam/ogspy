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

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class User_Defense_Model  extends Model_Abstract
{
    /**
     * @param $user_id
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function select_user_defense($user_id) {
        $tElemList = array("planet_id", "LM", "LLE", "LLO", "CG", "AI", "LP", "GB", "MIC", "MIP");

        $request = "SELECT ".implode(", ",$tElemList)." ";;
        $request .= " FROM " . TABLE_USER_DEFENCE;
        $request .= " WHERE user_id = " . $user_id;
        $request .= " ORDER BY planet_id";
        $result = $this->db->sql_query($request);

        $tDefense = array();
        while ($row =  $this->db->sql_fetch_assoc($result)){
            $tDefense[$row["planet_id"]] = array();
            foreach ($tElemList as $elem)
            {
                $tDefense[$row["planet_id"]][$elem] =$row[$elem];
            }
        }
        return $tDefense;

    }
    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     * @todo Could we do that in one request ?
     */
    public function update_moon_id($user_id, $previous_id, $new_id) {
        $request = "UPDATE " . TABLE_USER_DEFENCE . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $this->db->sql_query($request);
        //We adjust the id if we go upper than 299
        $request = "UPDATE " . TABLE_USER_DEFENCE .
            " SET planet_id  = planet_id -100 WHERE  planet_id > 299 and user_id = " . $user_id;
        $this->db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $previous_id
     * @param $new_id
     */
    public function update_planet_id($user_id, $previous_id, $new_id) {
        $request = "UPDATE " . TABLE_USER_DEFENCE . " SET planet_id  = " . $new_id .
            " WHERE  planet_id = " . $previous_id . " and user_id = " . $user_id;
        $this->db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $aster_id Planet or moon to be deleted
     */
    public function delete_user_aster($user_id, $aster_id) {
        $request = "DELETE FROM " . TABLE_USER_DEFENCE . " WHERE `user_id` = " . $user_id . " AND `planet_id` = " . intval($aster_id);
        $this->db->sql_query($request);
    }
}