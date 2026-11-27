<?php

/**
 * Model for managing player technologies.
 *
 * This class extends `Model_Abstract` and provides methods
 * to interact with player technology data in the database.
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
    * Retrieves technologies for a specific player.
     *
    * @param int $player_id Unique player identifier.
    * @return array Associative array containing player technologies.
    *               Array keys include: `Esp`, `Ordi`, `Armes`, `Bouclier`,
     *               `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`,
     *               `Plasma`, `RRI`, `Graviton`, `Astrophysique`.
     */
    public function select_player_technologies(int $player_id)
    {
        $request = "SELECT `player_id`,`Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`";
        $request .= " FROM " . TABLE_USER_TECHNOLOGY;
        $request .= " WHERE `player_id` = " . $player_id;
        $result = $this->db->sql_query($request);
        return  $this->db->sql_fetch_assoc($result);
    }
    /**
    * Deletes technologies for a specific player.
     *
    * @param int $player_id Unique player identifier.
    *                       Matches the primary key in the player technology table.
    * @return void This method does not return a value.
     */
    public function delete_user_technologies(int $player_id)
    {
        $request = "DELETE FROM " . TABLE_USER_TECHNOLOGY . " WHERE `player_id` = " . $player_id;
        $this->db->sql_query($request);
    }

    /**
    * Updates espionage technology level for a specific player.
     *
    * @param int $player_id Unique player identifier.
    *                       Matches the primary key in the player technology table.
    * @param int $level New espionage technology level.
    *                   Must be a positive integer representing the target level.
    * @return void This method does not return a value.
     */
    public function update_esp(int $player_id, int $level)
    {
        $request = "UPDATE " . TABLE_USER_TECHNOLOGY . " SET `Esp` = " . $level . " WHERE `player_id` = " . $player_id;
        $this->db->sql_query($request);
    }
}
