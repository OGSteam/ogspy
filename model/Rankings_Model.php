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

    /**
     * @param $rank_table
     */
    public function get_rank_latest_table_date($rank_table){

        global $db;

        $request = "SELECT MAX(`datadate`) FROM `" .$rank_table . "`". " LIMIT 0,1";
        $result = $db->sql_query($request);
        list($max) = $db->sql_fetch_row($result);
        return $max;
    }

    /**
     * Gets the Selected Ranktable
     * @param $rank_table
     * @param $datadate
     * @param int $higher_rank
     * @param int $lower_rank
     * @return array
     */
    public function get_ranktable_bydate($rank_table, $datadate, $higher_rank = 1, $lower_rank = 100){

        global $db;
        $request  = "SELECT `rank`,`player`,`ally`,`points`";
        $request .= " FROM `" . $rank_table . "`";
        $request .= " WHERE `datadate` = '".$datadate."'"." AND `rank` >= '".$higher_rank ."' AND `rank` <= '".$lower_rank."'";

        $result = $db->sql_query($request);

        while ($row = $db->sql_fetch_assoc($result)) {
            $ranking_content[] = $row;
        }
        return $ranking_content;
    }

    /**
     * @param $datadate
     * @param int $higher_rank
     * @param int $lower_rank
     * @return array
     */
    public function get_all_player_ranktable_bydate($datadate, $higher_rank = 1, $lower_rank = 100){

        global $db;
        $request  = "SELECT ref_points.rank, ref_points.player, ref_points.ally, ref_points.rank, ref_points.points, ref_eco.rank, ref_eco.points, ref_tech.rank, ref_tech.points, ref_mil.rank, ref_mil.points, ref_milb.rank, ref_milb.points, ref_mill.rank, ref_mill.points, ref_mild.rank, ref_mild.points, ref_milh.rank, ref_milh.points";
        $request .= " FROM `" . TABLE_RANK_PLAYER_POINTS . "` AS ref_points";
        $request .= " LEFT JOIN ". TABLE_RANK_PLAYER_ECO." AS ref_eco ON ref_points.player = ref_eco.player AND ref_eco.`datadate` = '".$datadate."'";
        $request .= " LEFT JOIN ". TABLE_RANK_PLAYER_TECHNOLOGY." AS ref_tech ON ref_points.player = ref_tech.player AND ref_tech.`datadate` = '".$datadate."'";
        $request .= " LEFT JOIN ". TABLE_RANK_PLAYER_MILITARY." AS ref_mil ON ref_points.player = ref_mil.player  AND ref_mil.`datadate` = '".$datadate."'";
        $request .= " LEFT JOIN ". TABLE_RANK_PLAYER_MILITARY_BUILT." AS ref_milb ON ref_points.player = ref_milb.player AND ref_milb.`datadate` = '".$datadate."'";
        $request .= " LEFT JOIN ". TABLE_RANK_PLAYER_MILITARY_LOOSE." AS ref_mill ON ref_points.player = ref_mill.player  AND ref_mill.`datadate` = '".$datadate."'";
        $request .= " LEFT JOIN ". TABLE_RANK_PLAYER_MILITARY_DESTRUCT." AS ref_mild ON ref_points.player = ref_mild.player AND ref_mild.`datadate` = '".$datadate."'";
        $request .= " LEFT JOIN ". TABLE_RANK_PLAYER_HONOR." AS ref_milh ON ref_points.player = ref_milh.player AND ref_milh.`datadate` = '".$datadate."'";
        $request .= " WHERE ref_points.`datadate` = '".$datadate."'";
        $request .= " AND ref_points.`rank` >= '".$higher_rank ."'";
        $request .= " AND ref_points.`rank` <= '".$lower_rank."'";

        $result = $db->sql_query($request);

        //Remplissage du ranking content. Toutes les valeurs doivent être présentes dans l'array sous peine de soucis d'affichages
        $ranking_content = array ();
        $row = 0;
        while (list($position, $player_name, $ally_name,$general_rank, $general_pts, $eco_rank, $eco_pts, $tech_rank,  $tech_pts, $mil_rank, $mil_pts, $milb_rank, $milb_pts, $mill_rank, $mill_pts, $mild_rank, $mild_pts, $milh_rank, $milh_pts ) = $db->sql_fetch_row($result)) {
            $ranking_content[$row]['postion'] = $position;
            $ranking_content[$row]['player_name'] = $player_name;
            $ranking_content[$row]['ally_name'] = $ally_name;
            $ranking_content[$row]['general_rank'] = $general_rank;
            $ranking_content[$row]['general_pts'] = $general_pts;
            $ranking_content[$row]['tech_rank'] = $tech_rank;
            $ranking_content[$row]['tech_pts'] = $tech_pts;
            $ranking_content[$row]['eco_rank'] = $eco_rank;
            $ranking_content[$row]['eco_pts'] = $eco_pts;
            $ranking_content[$row]['tech_rank'] = $tech_rank;
            $ranking_content[$row]['tech_pts'] = $tech_pts;
            $ranking_content[$row]['mil_rank'] = $mil_rank;
            $ranking_content[$row]['mil_pts'] = $mil_pts;
            $ranking_content[$row]['milb_rank'] = $milb_rank;
            $ranking_content[$row]['milb_pts'] = $milb_pts;
            $ranking_content[$row]['mill_rank'] = $mill_rank;
            $ranking_content[$row]['mill_pts'] = $mill_pts;
            $ranking_content[$row]['mild_rank'] = $mild_rank;
            $ranking_content[$row]['mild_pts'] = $mild_pts;
            $ranking_content[$row]['milh_rank'] = $milh_rank;
            $ranking_content[$row]['milh_pts'] = $milh_pts;
            $row++;
        }
        return $ranking_content;
    }


    /**
     * get distinct datatable available
     *
     * @return array
     */
    public function get_all_player_distinct_date_ranktable()
    {
        global $db;
        $request = "SELECT DISTINCT datadate FROM " . TABLE_RANK_PLAYER_POINTS . " ORDER BY datadate DESC";
        $result = $db->sql_query($request);
        while ($row = $db->sql_fetch_assoc($result)) {
            $ranking_available[] = $row["datadate"];
        }

        return $ranking_available;
    }


}