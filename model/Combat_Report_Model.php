<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
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
     * @param int $galaxy
     * @param int $system
     * @param int $row
     * @return array $tResult
     */
    public function get_cr_id_list_by_planet ($galaxy, $system, $row){
        global $db;

        $request = "SELECT `id_rc` FROM " . TABLE_PARSEDRC;
        $request .= " WHERE `coordinates` = '" . $galaxy . ':' .$system . ':' . $row . "'";
        $request .= " ORDER BY `dateRC` DESC";
        $result = $db->sql_query($request);

        $tResult = array();
        while ($row = $db->sql_fetch_assoc($result)) {
            $tResult[] = $row["id_rc"];
        }
        return $tResult;
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