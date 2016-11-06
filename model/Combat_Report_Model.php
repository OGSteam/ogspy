<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 06/11/2016
 * Time: 17:43
 */

namespace Ogsteam\Ogspy\Model;


class Combat_Report_Model
{
    /**
     * @param int $galaxy
     * @param int $system
     * @param int $row
     * @return int $nb_spy
     */
    public function get_nb_combat_report_by_planet ($galaxy, $system, $row){
        global $db;

        $request = "SELECT * FROM " . TABLE_PARSEDRC . " WHERE `coordinates` = '" . $galaxy . ":" . $system . ":" . $row . "'";
        $result = $db->sql_query($request);
        $nb_rc = $db->sql_numrows($result);

        return $nb_rc;
    }

    /**
     * @param $id_rc
     */
    public function delete_combat_report($id_rc){
        global $db;

        $request = "DELETE FROM " . TABLE_PARSEDRC . " WHERE `id_rc` = '" . $id_rc . "'";
        $db->sql_query($request);
    }
}