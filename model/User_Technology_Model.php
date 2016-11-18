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


class User_Technology_Model
{
    /**
     * @param $user_id
     * @return array
     */
    public function select_user_technologies($user_id){

        global $db;
        $request = "SELECT Esp, Ordi, Armes, Bouclier, Protection, NRJ, Hyp, RC, RI, PH, Laser, Ions, Plasma, RRI, Graviton, Astrophysique";
        $request .= " FROM " . TABLE_USER_TECHNOLOGY;
        $request .= " WHERE user_id = " . $user_id;
        $result = $db->sql_query($request);

        return  $db->sql_fetch_assoc($result);
    }

    /**
     * @param $user_id
     */
    public function delete_user_technologies($user_id){

        global $db;
        $request = "DELETE FROM " . TABLE_USER_TECHNOLOGY . " WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

    }
}