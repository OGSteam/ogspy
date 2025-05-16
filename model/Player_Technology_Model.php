<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Technology_Model  extends Model_Abstract
{

    /**
     * @param $user_id
     * @return array
     */
    public function select_user_technologies($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`";
        $request .= " FROM " . TABLE_USER_TECHNOLOGY;
        $request .= " WHERE `user_id` = " . $user_id;
        $result = $this->db->sql_query($request);
        return  $this->db->sql_fetch_assoc($result);
    }
    /**
     * @param $user_id
     */
    public function delete_user_technologies($user_id)
    {
        $user_id = (int)$user_id;

        $request = "DELETE FROM " . TABLE_USER_TECHNOLOGY . " WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $level niveau de l'espionnage
     */
    public function update_esp($user_id, $level)
    {
        $user_id = (int)$user_id;
        $level = (int)$level;

        $request = "UPDATE " . TABLE_USER_TECHNOLOGY . " SET `Esp` = " . $level . " WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }
}
