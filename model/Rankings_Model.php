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

abstract class Rankings_Model
{
    /* Liste des Tables en BDD */
    protected  $rank_tables ;
    protected  $rank_tables_sql_table;

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
     * get distinct datatable available
     *
     * @return array
     */
    public function get_all_distinct_date_ranktable()
    {
        global $db;
        $ranking_available = array();
        $request = "SELECT DISTINCT datadate FROM " . $this->rank_tables[0] . " ORDER BY datadate DESC";
        $result = $db->sql_query($request);
        while ($row = $db->sql_fetch_assoc($result)) {
            $ranking_available[] = $row["datadate"];
        }

        return $ranking_available;
    }


    /**
     * Gets the Selected Ranktable
     * @param $rank_table
     * @param $datadate
     * @param $bydate
     * @param int $higher_rank
     * @param int $lower_rank
     * @return array
     */
    public function get_ranktable($rank_table, $datadate, $bydate = false, $higher_rank = 1, $lower_rank = 100){

        global $db;
        $request  = "SELECT `".implode("`,`", $this->rank_tables_sql_table)."`";
        $request .= " FROM `" . $rank_table . "`";
        if($bydate)
        {
            $request .= " WHERE `datadate` = '".$datadate."'"." AND `rank` >= '".$higher_rank ."' AND `rank` <= '".$lower_rank."'";

        }
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
    abstract public function get_all_ranktable_bydate($datadate, $higher_rank = 1, $lower_rank = 100);

}