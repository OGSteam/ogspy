<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2017, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

class Rankings_Model
{
    /* Liste des Tables en BDD */
    private  $rank_tables = array(TABLE_RANK_PLAYER_POINTS, TABLE_RANK_PLAYER_ECO, TABLE_RANK_PLAYER_TECHNOLOGY, TABLE_RANK_PLAYER_MILITARY, TABLE_RANK_PLAYER_MILITARY_BUILT, TABLE_RANK_PLAYER_MILITARY_LOOSE, TABLE_RANK_PLAYER_MILITARY_DESTRUCT, TABLE_RANK_PLAYER_HONOR);

    /**
     * Selection du max rank pour définir la taille des tableaux parmi tous les classements
     * @return mixed
     */
    public function select_max_rank_row()
{
    global $db;
    $max_rank = array();
    $i = 0;
    foreach ($this->rank_tables as $table) {
        $request = "SELECT `rank` FROM `" . $table . "` ORDER BY `rank` DESC LIMIT 0,1";
        $result = $db->sql_query($request);
        $max = $db->sql_fetch_row($result);
        $max_rank[$i] = $max[0];
        $i++;
    }

    // selection de rank max !
    return max($max_rank);
}


}