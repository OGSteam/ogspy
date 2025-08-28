<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Combat_Report_Model  extends Model_Abstract
{
    /**
     * Retrieves the number of combat reports associated with a specific planet
     * identified by its coordinates (galaxy, system, row).
     *
     * @param int $galaxy The galaxy number of the planet.
     * @param int $system The system number of the planet.
     * @param int $row The row number of the planet.
     * @return int The number of combat reports corresponding to the specified planet.
     */
    public function get_nb_combat_report_by_planet(int $galaxy, int $system, int $row)
    {
        $request = "SELECT COUNT(rc.id_rc) as rc_count
                FROM " . TABLE_PARSEDRC . " rc
                INNER JOIN " . TABLE_USER_BUILDING . " astro ON rc.astro_object_id = astro.id
                WHERE astro.galaxy = " . $galaxy . "
                AND astro.system = " . $system . "
                AND astro.row = " . $row;

        $result = $this->db->sql_query($request);
        $data = $this->db->sql_fetch_assoc($result);
        $nb_rc = $data ? (int)$data['rc_count'] : 0;

        return $nb_rc;
    }

    /**
     * Retrieves a list of combat report (CR) IDs by planet coordinates.
     *
     * @param int $galaxy The galaxy number of the target coordinates.
     * @param int $system The system number of the target coordinates.
     * @param int $row The row number of the target planet within the galaxy and system.
     * @return array An array of combat report IDs (id_rc) sorted by date in descending order.
     */
    public function get_cr_id_list_by_planet(int $galaxy, int $system, int $row)
    {

        $request = "SELECT rc.id_rc
                FROM " . TABLE_PARSEDRC . " rc
                INNER JOIN " . TABLE_USER_BUILDING . " astro ON rc.astro_object_id = astro.id
                WHERE astro.galaxy = " . $galaxy . "
                AND astro.system = " . $system . "
                AND astro.row = " . $row . "
                ORDER BY rc.dateRC DESC";

        $result = $this->db->sql_query($request);

        $tResult = array();
        while ($data_row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = $data_row["id_rc"];
        }
        return $tResult;
    }

    /**
     * @param $id_rc
     * @return mixed
     */
    public function get_combat_report($id_rc)
    {
        $id_rc = (int)$id_rc;

        // Get the RC with id $id_rc
        $query = "SELECT `dateRC`, `astro_object_id`, `nb_rounds`, `victoire`, `pertes_A`, `pertes_D`, `gain_M`, `gain_C`, `gain_D`, `debris_M`, `debris_C`, `lune` FROM " . TABLE_PARSEDRC . " WHERE `id_rc` = $id_rc";
        $result = $this->db->sql_query($query);
        $report = $this->db->sql_fetch_assoc($result);

        // récupération des Rounds
        $rounds = array();
        $tRcroundId = array();
        $query = "SELECT `id_rcround`, `id_rc`, `numround`, `attaque_tir`, `attaque_puissance`, `attaque_bouclier`, `defense_tir`, `defense_puissance`, `defense_bouclier`
                  FROM " . TABLE_PARSEDRCROUND . " WHERE `id_rc` =  $id_rc  ORDER BY `numround`";
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
        $query = "SELECT `id_rcround`, `player`, `astro_object_id`, `Armes`, `Bouclier`, `Protection`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`, `DST`, `EDLM`, `TRA`, `ECL`, `FAU`  FROM " . TABLE_ROUND_ATTACK . " WHERE  `id_rcround` IN ( $sRcroundId ) ";
        $result_attack = $this->db->sql_query($query);
        while ($attack = $this->db->sql_fetch_assoc($result_attack)) {
            $rounds[$attack['id_rcround']]['attacks'][] = $attack;
        }

        // recuperation des defenseurs
        $query = "SELECT `id_rcround`, `player`, `astro_object_id`, `Armes`, `Bouclier`, `Protection`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`, `SAT`, `DST`, `EDLM`, `TRA`, `ECL`, `FAU`, `FOR`, `LM`, `LLE`, `LLO`, `CG`, `AI`, `LP`, `PB`, `GB` FROM " . TABLE_ROUND_DEFENSE . " WHERE  `id_rcround` IN ( $sRcroundId ) ";
        $result_def = $this->db->sql_query($query);
        while ($defenses = $this->db->sql_fetch_assoc($result_def)) {
            $rounds[$defenses['id_rcround']]['defenses'][] = $defenses;
        }

        $report['rounds'] = $rounds;
        return $report;
    }

    /**
     * @param $id_rc
     */
    public function delete_combat_report($id_rc)
    {
        $id_rc = (int)$id_rc;

        // Récupérer les IDs des rounds associés au rapport de combat
        $query = "SELECT `id_rcround` FROM " . TABLE_PARSEDRCROUND . " WHERE `id_rc` = $id_rc";
        $result = $this->db->sql_query($query);
        $roundIds = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $roundIds[] = $row['id_rcround'];
        }

        if (!empty($roundIds)) {
            $sRcroundId = implode(',', $roundIds);

            // Supprimer les données d'attaque des rounds
            $request = "DELETE FROM " . TABLE_ROUND_ATTACK . " WHERE `id_rcround` IN ($sRcroundId)";
            $this->db->sql_query($request);

            // Supprimer les données de défense des rounds
            $request = "DELETE FROM " . TABLE_ROUND_DEFENSE . " WHERE `id_rcround` IN ($sRcroundId)";
            $this->db->sql_query($request);
        }

        // Supprimer les rounds
        $request = "DELETE FROM " . TABLE_PARSEDRCROUND . " WHERE `id_rc` = $id_rc";
        $this->db->sql_query($request);

        // Supprimer le rapport de combat principal
        $request = "DELETE FROM " . TABLE_PARSEDRC . " WHERE `id_rc` = $id_rc";
        $this->db->sql_query($request);
    }
}
