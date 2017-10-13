<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Machine
 * @copyright Copyright &copy; 2017, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;


class Rankings_Ally_Model extends Rankings_Model
{
    public function __construct()
    {
        $this->rank_tables = array(TABLE_RANK_ALLY_POINTS, TABLE_RANK_ALLY_ECO, TABLE_RANK_ALLY_TECHNOLOGY, TABLE_RANK_ALLY_MILITARY, TABLE_RANK_ALLY_MILITARY_BUILT, TABLE_RANK_ALLY_MILITARY_LOOSE, TABLE_RANK_ALLY_MILITARY_DESTRUCT, TABLE_RANK_ALLY_HONOR);
        $this->rank_tables_sql_table = array("rank", "ally", "number_member", "points", "user_name");

    }

    /**
     * @param $datadate
     * @param int $higher_rank
     * @param int $lower_rank
     * @return array
     */
    public function get_all_ranktable_bydate($datadate, $higher_rank = 1, $lower_rank = 100) {
        global $db;
        $request  = "SELECT ref_points.rank, ref_points.ally, ref_points.number_member, ref_points.rank, ref_points.points, ref_eco.rank, 
        ref_eco.points, ref_tech.rank, ref_tech.points, ref_mil.rank, ref_mil.points, ref_milb.rank, ref_milb.points, ref_mill.rank, 
        ref_mill.points, ref_mild.rank, ref_mild.points, ref_milh.rank, ref_milh.points";
        $request .= " FROM `" . TABLE_RANK_ALLY_POINTS . "` AS ref_points";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_ECO . " AS ref_eco ON ref_points.ally = ref_eco.ally AND ref_eco.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_TECHNOLOGY . " AS ref_tech ON ref_points.ally = ref_tech.ally AND ref_tech.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY . " AS ref_mil ON ref_points.ally = ref_mil.ally  AND ref_mil.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_BUILT . " AS ref_milb ON ref_points.ally = ref_milb.ally AND ref_milb.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_LOOSE . " AS ref_mill ON ref_points.ally = ref_mill.ally  AND ref_mill.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " AS ref_mild ON ref_points.ally = ref_mild.ally AND ref_mild.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_HONOR . " AS ref_milh ON ref_points.ally = ref_milh.ally AND ref_milh.`datadate` = '" . $datadate . "'";
        $request .= " WHERE ref_points.`datadate` = '" . $datadate . "'";
        $request .= " AND ref_points.`rank` >= '" . $higher_rank . "'";
        $request .= " AND ref_points.`rank` <= '" . $lower_rank . "'";

        $result = $db->sql_query($request);

        //Remplissage du ranking content. Toutes les valeurs doivent être présentes dans l'array sous peine de soucis d'affichages
        $ranking_content = array();
        $row = 0;
        while (list($position, $ally_name, $member, $general_rank, $general_pts, $eco_rank, $eco_pts, $tech_rank, $tech_pts, $mil_rank, $mil_pts, $milb_rank, $milb_pts, $mill_rank, $mill_pts, $mild_rank, $mild_pts, $milh_rank, $milh_pts) = $db->sql_fetch_row($result)) {
            $ranking_content[$row]['postion'] = $position;
            $ranking_content[$row]['ally_name'] = $ally_name;
            $ranking_content[$row]['member'] = $member;
            $ranking_content[$row]['general_rank'] = $general_rank;
            $ranking_content[$row]['general_pts'] = $general_pts;
            $ranking_content[$row]['general_pts_mb'] = (int) ($general_pts / $member);
            $ranking_content[$row]['eco_rank'] = $eco_rank;
            $ranking_content[$row]['eco_pts'] = $eco_pts;
            $ranking_content[$row]['eco_pts_mb'] = (is_numeric($eco_pts)) ? (int) ($eco_pts / $member) : null; ;
            $ranking_content[$row]['tech_rank'] = $tech_rank;
            $ranking_content[$row]['tech_pts'] = $tech_pts;
            $ranking_content[$row]['tech_pts_mb'] = (is_numeric($tech_pts)) ? (int) ($tech_pts / $member) : null; ;
            $ranking_content[$row]['mil_rank'] = $mil_rank;
            $ranking_content[$row]['mil_pts'] = $mil_pts;
            $ranking_content[$row]['mil_pts_mb'] = (is_numeric($mil_pts)) ? (int) ($tech_pts / $member) : null; ;
            $ranking_content[$row]['milb_rank'] = $milb_rank;
            $ranking_content[$row]['milb_pts'] = $milb_pts;
            $ranking_content[$row]['milb_pts_mb'] = (is_numeric($milb_pts)) ? (int) ($tech_pts / $member) : null; ;
            $ranking_content[$row]['mill_rank'] = $mill_rank;
            $ranking_content[$row]['mill_pts'] = $mill_pts;
            $ranking_content[$row]['mill_pts_mb'] = (is_numeric($mill_pts)) ? (int) ($tech_pts / $member) : null; ;
            $ranking_content[$row]['mild_rank'] = $mild_rank;
            $ranking_content[$row]['mild_pts'] = $mild_pts;
            $ranking_content[$row]['mild_pts_mb'] = (is_numeric($mild_pts)) ? (int) ($mild_pts / $member) : null; ;
            $ranking_content[$row]['milh_rank'] = $milh_rank;
            $ranking_content[$row]['milh_pts'] = $milh_pts;
            $ranking_content[$row]['milh_pts_mb'] = (is_numeric($milh_pts)) ? (int) ($milh_pts / $member) : null; ;
            $row++;
        }
        return $ranking_content;
    }

}