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

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Combat_Report_Model  extends Model_Abstract
{
    /**
     * @param int $galaxy
     * @param int $system
     * @param int $row
     * @return int $nb_spy
     */
    public function get_nb_combat_report_by_planet($galaxy, $system, $row) {
        

        $request = "SELECT * FROM " . TABLE_PARSEDRC . " WHERE `coordinates` = '" . $galaxy . ":" . $system . ":" . $row . "'";
        $result = $this->db->sql_query($request);
        $nb_rc = $this->db->sql_numrows($result);

        return $nb_rc;
    }

    /**
     * @param int $galaxy
     * @param int $system
     * @param int $row
     * @return array $tResult
     */
    public function get_cr_id_list_by_planet($galaxy, $system, $row) {
        

        $request = "SELECT `id_rc` FROM " . TABLE_PARSEDRC;
        $request .= " WHERE `coordinates` = '" . $galaxy . ':' . $system . ':' . $row . "'";
        $request .= " ORDER BY `dateRC` DESC";
        $result = $this->db->sql_query($request);

        $tResult = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = $row["id_rc"];
        }
        return $tResult;
    }

    /**
     * @param $id_rc
     * @return mixed
     */
    public function get_combat_report($id_rc)
    {
        

        $query = 'SELECT dateRC, coordinates, nb_rounds, victoire, pertes_A, pertes_D, gain_M, gain_C, gain_D, debris_M, debris_C, lune FROM ' . TABLE_PARSEDRC . ' WHERE id_rc = ' . $id_rc;
        $result = $this->db->sql_query($query);

        $report = $this->db->sql_fetch_assoc($result);

        $rounds = array();
        $query = 'SELECT id_rcround, id_rc, numround, attaque_tir, attaque_puissance, attaque_bouclier, defense_tir, defense_puissance, defense_bouclier FROM ' . TABLE_PARSEDRCROUND . ' WHERE id_rc = ' . $id_rc . ' ORDER BY numround';
        $result_round = $this->db->sql_query($query);
        while ($round = $this->db->sql_fetch_assoc($result_round)) {
            $rounds[] = $round;
        }
        $report['rounds'] = $rounds;

        return $report;
    }

    /**
     * @param $id_rc
     */
    public function delete_combat_report($id_rc) {
        

        $request = "DELETE FROM " . TABLE_PARSEDRC . " WHERE `id_rc` = '" . $id_rc . "'";
        $this->db->sql_query($request);
    }
}
