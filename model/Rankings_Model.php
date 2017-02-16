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
            $request = "SELECT MAX(`rank`) FROM `" . $table . "` LIMIT 0,1";
            $result = $db->sql_query($request);
            $max = $db->sql_fetch_row($result);
            $max_rank[$i] = $max[0];
            $i++;
        }

        // selection de rank max !
        return max($max_rank);
    }

    private function get_rank_type_tablename($rank_type){

        if(!isset($rank_type)) return -1;

        switch($rank_type){
            case 'player_points_rank' :
                $table = TABLE_RANK_PLAYER_POINTS;
                break;
            case 'player_eco_rank' :
                $table = TABLE_RANK_PLAYER_ECO;
                break;
            case 'player_techno_rank' :
                $table = TABLE_RANK_PLAYER_TECHNOLOGY;
                break;
            case 'player_military_rank' :
                $table = TABLE_RANK_PLAYER_MILITARY;
                break;
            case 'player_military_built_rank' :
                $table = TABLE_RANK_PLAYER_MILITARY;
                break;
            case 'player_military_lost_rank' :
                $table = TABLE_RANK_PLAYER_MILITARY_LOOSE;
                break;
            case 'player_military_destroyed_rank' :
                $table = TABLE_RANK_PLAYER_MILITARY_DESTRUCT;
                break;
            case 'player_honor_rank' :
                $table = TABLE_RANK_PLAYER_HONOR;
                break;
            default:
                $table = TABLE_RANK_PLAYER_POINTS;
        }
        return $table;
    }

    /**
     * @param $rank_type
     */
    public function get_rank_latest_table_date($rank_type){

        global $db;

        $request = "SELECT MAX(`datadate`) FROM `" . $this->get_rank_type_tablename($rank_type) . "`".
        " LIMIT 0,1";
        $result = $db->sql_query($request);
        list($max) = $db->sql_fetch_row($result);
        return $max;
    }

    public function get_ranktable_bydate($rank_type, $higher_rank = 1, $lower_rank = 100, $datadate){

        global $db;

        $request = "SELECT * FROM `" . $this->get_rank_type_tablename($rank_type) . "`".
                    "WHERE `datadate` = '".$datadate."'".
        "AND `rank` >= '".$higher_rank ."' AND `rank` <= '".$lower_rank."'";

        $result = $db->sql_query($request);

        while ($row = $db->sql_fetch_assoc($result)) {
            $ranking_content[] = $row;
        }
        return $ranking_content;
    }
}