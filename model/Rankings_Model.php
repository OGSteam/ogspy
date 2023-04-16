<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2017, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

abstract class Rankings_Model extends Model_Abstract
{
    /* Liste des Tables en BDD */
    protected  $rank_tables;
    protected  $rank_tables_sql_table;
    protected  $rank_table_ref;

    public function get_rank_tables()
    {
        return $this->rank_tables;
    }
    public function get_rank_table_ref()
    {
        return $this->rank_table_ref;
    }
    /**
     * Selection du max rank pour définir la taille des tableaux parmi tous les classements
     * @return mixed
     */
    public function select_max_rank_row()
    {
        $max_rank = array();
        $i = 0;
        foreach ($this->rank_tables as $table) {
            $request = "SELECT max(`rank`) FROM `" . $table . "`    LIMIT 0,1";
            $result = $this->db->sql_query($request);
            $max = $this->db->sql_fetch_row($result);
            $max_rank[$i] = $max[0];
            $i++;
        }
        return $max_rank;
    }
    /**
     * @param $rank_table
     */
    public function get_rank_latest_table_date($rank_table)
    {
        $rank_table = $this->db->sql_escape_string($rank_table);

        $request = "SELECT MAX(`datadate`) FROM `" . $rank_table . "`" . " LIMIT 0,1";
        $request = "SELECT datadate FROM `" . $rank_table . "`" . " LIMIT 0,1";
        $result = $this->db->sql_query($request);
        list($max) = $this->db->sql_fetch_row($result);
        return $max;
    }
    /**
     * get distinct datatable available
     *
     * @return array
     */
    public function get_all_distinct_date_ranktable($rank_table)
    {
        $rank_table = $this->db->sql_escape_string($rank_table);

        $ranking_available = array();
        $request = "SELECT DISTINCT datadate FROM `" . $rank_table . "`  ORDER BY datadate DESC";
        $result = $this->db->sql_query($request);
        while ($row = $this->db->sql_fetch_assoc($result)) {
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
    public function get_ranktable($rank_table, $datadate, $bydate = false, $higher_rank = 1, $lower_rank = 100)
    {
        $rank_table = $this->db->sql_escape_string($rank_table);
        $datadate = (int)$datadate;
        $bydate = (bool)$bydate;
        $higher_rank = (int)$higher_rank;
        $lower_rank = (int)$lower_rank;


        $request  = "SELECT `" . implode("`,`", $this->rank_tables_sql_table) . "`";
        $request .= " FROM `" . $rank_table . "`";
        if ($bydate) {
            $request .= " WHERE `datadate` = '" . $datadate . "'" . " AND `rank` >= '" . $higher_rank . "' AND `rank` <= '" . $lower_rank . "'";
        }
        $result = $this->db->sql_query($request);
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $ranking_content[] = $row;
        }
        return $ranking_content;
    }

    /**
     * Remove old ranking
     * @param datadate temps en seconde
     * @param $table nom de la table impacté
     * remove entry from database when datadate is out of time
     */
    public function remove_all_rank_older_than($datadate, $table = null)
    {
        $datadate = (int)$datadate;
        $table = $this->db->sql_escape_string($table);

        $tTables = $this->rank_tables;
        if (in_array($table, $this->rank_tables)) {
            // suppression pour une seule table
            $tTables = array($table);
        }

        foreach ($tTables as $table) {
            $request = "DELETE FROM " . $table . " WHERE datadate < " . $datadate;
            $this->db->sql_query($request);
        }
    }

    /**
     * Remove ranking by one datadate
     * @param datadate  temps en seconde
     * remove entry from database by datadate
     */
    public function remove_all_rank_by_datadate($datadate, $table = null)
    {
        $datadate = (int)$datadate;
        $table = $this->db->sql_escape_string($table);
        $tTables = $this->rank_tables;
        if (in_array($table, $this->rank_tables)) {
            // suppression pour une seule table
            $tTables = array($table);
        }

        foreach ($tTables as $table) {
            $request = "DELETE FROM " . $table . " WHERE datadate = " . $datadate;
            $this->db->sql_query($request);
        }
    }






    /**
     * @param $datadate
     * @param int $higher_rank
     * @param int $lower_rank
     * @return array
     */
    abstract public function get_all_ranktable_bydate($datadate, $higher_rank = 1, $lower_rank = 100);
}
