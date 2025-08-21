<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Machine
 * @copyright Copyright &copy; 2017, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Model\Rankings_Model;

class Rankings_Ally_Model extends Rankings_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->rank_tables = array(TABLE_RANK_ALLY_POINTS, TABLE_RANK_ALLY_ECO, TABLE_RANK_ALLY_TECHNOLOGY, TABLE_RANK_ALLY_MILITARY, TABLE_RANK_ALLY_MILITARY_BUILT, TABLE_RANK_ALLY_MILITARY_LOOSE, TABLE_RANK_ALLY_MILITARY_DESTRUCT, TABLE_RANK_ALLY_HONOR);
        $this->rank_tables_sql_table = array("rank", "ally", "number_member", "points", "user_name");
        $this->rank_table_ref = array('general', 'eco', 'techno', 'military', 'military_b', 'military_l', 'military_d', 'honor');
    }

    /**
     * @param $playername

     * @return array
     */
    public function get_all_ranktable_byally($allyName)
    {
        $ally_name = $this->db->sql_escape_string($allyName);


        $request  = "SELECT  `general`.`datadate`, `general`.`rank`, `general`.`ally`, `general`.`number_member`, `general`.`rank`, `general`.`points`, `eco`.`rank`,
        `eco`.`points`, `techno`.`rank`, `techno`.`points`, `military`.`rank`, `military`.`points`, `military_b`.`rank`, `military_b`.`points`, `military_l`.`rank`,
        military_l.points, military_d.rank, military_d.points, honor.rank, honor.points";

        $request .= " FROM `" . TABLE_RANK_ALLY_POINTS . "` AS `general`";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_ECO . " AS `eco` ON `general`.`ally` = `eco`.`ally` AND `eco`.`datadate`  = `general`.`datadate` ";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_TECHNOLOGY . " AS `techno` ON `general`.`ally` = `techno`.`ally` AND `techno`.`datadate` = `general`.`datadate` ";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY . " AS `military` ON `general`.`ally` = `military`.`ally`  AND `military`.`datadate` = `general`.`datadate` ";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_BUILT . " AS `military_b` ON `general`.`ally` = `military_b`.`ally` AND `military_b`.`datadate` = `general`.`datadate` ";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_LOOSE . " AS `military_l` ON `general`.`ally` = `military_l`.`ally`  AND `military_l`.`datadate`  = `general`.`datadate` ";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " AS `military_d` ON `general`.`ally` = `military_d`.`ally` AND `military_d`.`datadate`  = `general`.`datadate` ";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_HONOR . " AS `honor` ON `general`.`ally` = `honor`.`ally` AND `honor`.`datadate`  = `general`.`datadate` ";


        $request .= " WHERE `general`.`ally` = '" . $allyName . "'";
        $request .= " ORDER BY `general`.`datadate` DESC ";


        $result = $this->db->sql_query($request);

        //Remplissage du ranking content. Toutes les valeurs doivent être présentes dans l'array sous peine de soucis d'affichages
        $ranking_content = array();
        $row = 0;
        while (list($datadate, $position, $ally_name, $member, $general_rank, $general_pts, $eco_rank, $eco_pts, $tech_rank, $tech_pts, $mil_rank, $mil_pts, $milb_rank, $milb_pts, $mill_rank, $mill_pts, $mild_rank, $mild_pts, $milh_rank, $milh_pts) = $this->db->sql_fetch_row($result)) {
            $ranking_content[$row]['datadate'] = $datadate;
            $ranking_content[$row]['postion'] = $position;
            $ranking_content[$row]['ally_name'] = $ally_name;
            $ranking_content[$row]['member'] = $member;
            $ranking_content[$row]['general_rank'] = $general_rank;
            $ranking_content[$row]['general_pts'] = $general_pts;
            $ranking_content[$row]['general_pts_mb'] = (int) ($general_pts / $member);
            $ranking_content[$row]['eco_rank'] = $eco_rank;
            $ranking_content[$row]['eco_pts'] = $eco_pts;
            $ranking_content[$row]['eco_pts_mb'] = (is_numeric($eco_pts)) ? (int) ($eco_pts / $member) : null;
            $ranking_content[$row]['tech_rank'] = $tech_rank;
            $ranking_content[$row]['tech_pts'] = $tech_pts;
            $ranking_content[$row]['tech_pts_mb'] = (is_numeric($tech_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['mil_rank'] = $mil_rank;
            $ranking_content[$row]['mil_pts'] = $mil_pts;
            $ranking_content[$row]['mil_pts_mb'] = (is_numeric($mil_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['milb_rank'] = $milb_rank;
            $ranking_content[$row]['milb_pts'] = $milb_pts;
            $ranking_content[$row]['milb_pts_mb'] = (is_numeric($milb_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['mill_rank'] = $mill_rank;
            $ranking_content[$row]['mill_pts'] = $mill_pts;
            $ranking_content[$row]['mill_pts_mb'] = (is_numeric($mill_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['mild_rank'] = $mild_rank;
            $ranking_content[$row]['mild_pts'] = $mild_pts;
            $ranking_content[$row]['mild_pts_mb'] = (is_numeric($mild_pts)) ? (int) ($mild_pts / $member) : null;
            $ranking_content[$row]['milh_rank'] = $milh_rank;
            $ranking_content[$row]['milh_pts'] = $milh_pts;
            $ranking_content[$row]['milh_pts_mb'] = (is_numeric($milh_pts)) ? (int) ($milh_pts / $member) : null;
            $row++;
        }


        return $ranking_content;
    }




    /**
     * @param $datadate
     * @param int $higher_rank
     * @param int $lower_rank
     * @return array
     * todo fonction ecrrite pour 3.4 modification page view necessaire pour usage ...
     */
    public function get_all_ranktable_bydate($datadate, $higher_rank = 1, $lower_rank = 100, $ref = "general")
    {
        $datadate = (int)$datadate;
        $higher_rank = (int)$higher_rank;
        $lower_rank = (int)$lower_rank;
        $ref = $this->db->sql_escape_string($ref);


        if (!in_array($ref, $this->rank_table_ref)) {
            $ref = "general";
        }

        $request  = "SELECT " . $ref . ".`rank`, `general`.`ally`, `general`.`number_member`, `general`.`rank`, `general`.`points`, `eco`.`rank`,
        `eco`.`points`, `techno`.`rank`, `techno`.`points`, `military`.`rank`, `military`.`points`, `military_b`.`rank`, `military_b`.`points`, `military_l`.`rank`,
        `military_l`.`points`, `military_d`.`rank`, `military_d`.`points`, `honor`.`rank`, `honor`.`points`";

        $request .= " FROM `" . TABLE_RANK_ALLY_POINTS . "` AS `general`";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_ECO . " AS `eco` ON `general`.`ally` = `eco`.`ally` AND `eco`.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_TECHNOLOGY . " AS `techno` ON `general`.`ally` = `techno`.`ally` AND `techno`.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY . " AS `military` ON `general`.`ally` = `military`.`ally`  AND `military`.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_BUILT . " AS `military_b` ON `general`.`ally` = `military_b`.`ally` AND `military_b`.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_LOOSE . " AS `military_l` ON `general`.`ally` = `military_l`.`ally`  AND `military_l`.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " AS `military_d` ON `general`.`ally` = `military_d`.`ally` AND `military_d`.`datadate` = '" . $datadate . "'";
        $request .= " LEFT JOIN " . TABLE_RANK_ALLY_HONOR . " AS `honor` ON `general`.`ally` = `honor`.`ally` AND `honor`.`datadate` = '" . $datadate . "'";

        $request .= " WHERE `general`.`datadate` = '" . $datadate . "'";
        $request .= " AND " . $ref . ".`rank` >= '" . $higher_rank . "'";
        $request .= " AND " . $ref . ".`rank` <= '" . $lower_rank . "'";
        $request .= " ORDER BY " . $ref . ".`rank` ASC ";

        $result = $this->db->sql_query($request);

        //Remplissage du ranking content. Toutes les valeurs doivent être présentes dans l'array sous peine de soucis d'affichages
        $ranking_content = array();
        $row = 0;
        while (list($position, $ally_name, $member, $general_rank, $general_pts, $eco_rank, $eco_pts, $tech_rank, $tech_pts, $mil_rank, $mil_pts, $milb_rank, $milb_pts, $mill_rank, $mill_pts, $mild_rank, $mild_pts, $milh_rank, $milh_pts) = $this->db->sql_fetch_row($result)) {
            $ranking_content[$row]['postion'] = $position;
            $ranking_content[$row]['ally_name'] = $ally_name;
            $ranking_content[$row]['member'] = $member;
            $ranking_content[$row]['general_rank'] = $general_rank;
            $ranking_content[$row]['general_pts'] = $general_pts;
            $ranking_content[$row]['general_pts_mb'] = (int) ($general_pts / $member);
            $ranking_content[$row]['eco_rank'] = $eco_rank;
            $ranking_content[$row]['eco_pts'] = $eco_pts;
            $ranking_content[$row]['eco_pts_mb'] = (is_numeric($eco_pts)) ? (int) ($eco_pts / $member) : null;
            $ranking_content[$row]['tech_rank'] = $tech_rank;
            $ranking_content[$row]['tech_pts'] = $tech_pts;
            $ranking_content[$row]['tech_pts_mb'] = (is_numeric($tech_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['mil_rank'] = $mil_rank;
            $ranking_content[$row]['mil_pts'] = $mil_pts;
            $ranking_content[$row]['mil_pts_mb'] = (is_numeric($mil_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['milb_rank'] = $milb_rank;
            $ranking_content[$row]['milb_pts'] = $milb_pts;
            $ranking_content[$row]['milb_pts_mb'] = (is_numeric($milb_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['mill_rank'] = $mill_rank;
            $ranking_content[$row]['mill_pts'] = $mill_pts;
            $ranking_content[$row]['mill_pts_mb'] = (is_numeric($mill_pts)) ? (int) ($tech_pts / $member) : null;
            $ranking_content[$row]['mild_rank'] = $mild_rank;
            $ranking_content[$row]['mild_pts'] = $mild_pts;
            $ranking_content[$row]['mild_pts_mb'] = (is_numeric($mild_pts)) ? (int) ($mild_pts / $member) : null;
            $ranking_content[$row]['milh_rank'] = $milh_rank;
            $ranking_content[$row]['milh_pts'] = $milh_pts;
            $ranking_content[$row]['milh_pts_mb'] = (is_numeric($milh_pts)) ? (int) ($milh_pts / $member) : null;
            $row++;
        }
        return $ranking_content;
    }
}
