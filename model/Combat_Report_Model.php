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
        $galaxy =(int)$galaxy;
        $system=(int)$system;
        $row=(int)$row;


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
        $galaxy =(int)$galaxy;
        $system=(int)$system;
        $row=(int)$row;

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
        $id_rc =(int)$id_rc;

        // Get the RC with id $id_rc
        $query = "SELECT `dateRC`, `coordinates`, `nb_rounds`, `victoire`, `pertes_A`, `pertes_D`, `gain_M`, `gain_C`, `gain_D`, `debris_M`, `debris_C`, `lune` FROM " . TABLE_PARSEDRC . " WHERE `id_rc` = $id_rc";
        $result = $this->db->sql_query($query);
        $report = $this->db->sql_fetch_assoc($result);

        // récupération des Rounds
        $rounds = array();
        $tRcroundId = array();
        $query = "SELECT `id_rcround`, `id_rc`, `numround`, `attaque_tir`, `attaque_puissance`, `attaque_bouclier`, `defense_tir`, `defense_puissance`, `defense_bouclier` FROM " . TABLE_PARSEDRCROUND . " WHERE `id_rc` =  $id_rc  ORDER BY `numround`";
        $result_round = $this->db->sql_query($query);
        // on indique le rounrc afin de pouvoir relier les infos (attaque / defenseusr)
        while ($round = $this->db->sql_fetch_assoc($result_round)) {
            $round['attacks'] = array();
            $round['defenses'] = array();
            $rounds[$round["id_rcround"]] = $round;

            //pour select in
            $tRcroundId[]  = $round["id_rcround"];
        }

        // préparation du "select in"
        $sRcroundId = implode(',', $tRcroundId);

        //récupération des attaquants
        $query = "SELECT `id_rcround`, `player`, `coordinates`, `Armes`, `Bouclier`, `Protection`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`, `DST`, `EDLM`, `TRA`, `ECL`, `FAU`  FROM " . TABLE_ROUND_ATTACK . " WHERE  `id_rcround` IN ( $sRcroundId ) ";
        $result_attack = $this->db->sql_query($query);
        while ($attack = $this->db->sql_fetch_assoc($result_attack)) {
            $rounds[$attack['id_rcround']]['attacks'][] = $attack;
        }

        // recuperation des defenseurs
        $query = "SELECT `id_rcround`, `player`, `coordinates`, `Armes`, `Bouclier`, `Protection`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`, `SAT`, `DST`, `EDLM`, `TRA`, `ECL`, `FAU`, `FOR`, `LM`, `LLE`, `LLO`, `CG`, `AI`, `LP`, `PB`, `GB` FROM " . TABLE_ROUND_DEFENSE . " WHERE  `id_rcround` IN ( $sRcroundId ) ";
        $result_def = $this->db->sql_query($query);
        while ($def = $this->db->sql_fetch_assoc($result_def)) {
            $rounds[$attack['id_rcround']]['defenses'][] = $def;
        }

        $report['rounds'] = $rounds;
        return $report;
    }

    /**
     * @param $id_rc
     */
    public function delete_combat_report($id_rc) {
        $id_rc=(int)$id_rc;

        $request = "DELETE FROM " . TABLE_PARSEDRC . " WHERE `id_rc` = '" . $id_rc . "'";
        $this->db->sql_query($request);
    }
}
