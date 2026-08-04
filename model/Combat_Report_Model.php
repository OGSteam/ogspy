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
    public static function get_empire_report_order_by(int $sort = 2, int $sort2 = 0): string
    {
        $direction = $sort2 === 0 ? 'DESC' : 'ASC';

        return match ($sort) {
            1 => "astro.galaxy $direction, astro.system $direction, astro.row $direction",
            3 => "total_gain $direction, rc.dateRC DESC",
            4 => "total_losses $direction, rc.dateRC DESC",
            5 => "total_debris $direction, rc.dateRC DESC",
            6 => "rc.nb_rounds $direction, rc.dateRC DESC",
            default => "rc.dateRC $direction",
        };
    }

    public static function get_empire_report_filter_clauses(array $filters): array
    {
        $clauses = [];

        if (!empty($filters['galaxy']) && is_numeric($filters['galaxy'])) {
            $clauses[] = "astro.galaxy = " . (int)$filters['galaxy'];
        }
        if (!empty($filters['system']) && is_numeric($filters['system'])) {
            $clauses[] = "astro.system = " . (int)$filters['system'];
        }
        if (!empty($filters['row']) && is_numeric($filters['row'])) {
            $clauses[] = "astro.row = " . (int)$filters['row'];
        }

        if (!empty($filters['date_from']) && is_numeric($filters['date_from'])) {
            $clauses[] = "rc.dateRC >= " . (int)$filters['date_from'];
        }
        if (!empty($filters['date_to']) && is_numeric($filters['date_to'])) {
            $clauses[] = "rc.dateRC <= " . (int)$filters['date_to'];
        }

        if (isset($filters['gains_min']) && $filters['gains_min'] !== '' && is_numeric($filters['gains_min'])) {
            $clauses[] = "rc.gain_M >= 0 AND rc.gain_C >= 0 AND rc.gain_D >= 0 AND (rc.gain_M + rc.gain_C + rc.gain_D) >= " . (int)$filters['gains_min'];
        }
        if (isset($filters['gains_max']) && $filters['gains_max'] !== '' && is_numeric($filters['gains_max'])) {
            $clauses[] = "rc.gain_M >= 0 AND rc.gain_C >= 0 AND rc.gain_D >= 0 AND (rc.gain_M + rc.gain_C + rc.gain_D) <= " . (int)$filters['gains_max'];
        }

        if (isset($filters['losses_min']) && $filters['losses_min'] !== '' && is_numeric($filters['losses_min'])) {
            $clauses[] = "(rc.pertes_A + rc.pertes_D) >= " . (int)$filters['losses_min'];
        }
        if (isset($filters['losses_max']) && $filters['losses_max'] !== '' && is_numeric($filters['losses_max'])) {
            $clauses[] = "(rc.pertes_A + rc.pertes_D) <= " . (int)$filters['losses_max'];
        }

        if (isset($filters['debris_min']) && $filters['debris_min'] !== '' && is_numeric($filters['debris_min'])) {
            $clauses[] = "rc.debris_M >= 0 AND rc.debris_C >= 0 AND (rc.debris_M + rc.debris_C) >= " . (int)$filters['debris_min'];
        }
        if (isset($filters['debris_max']) && $filters['debris_max'] !== '' && is_numeric($filters['debris_max'])) {
            $clauses[] = "rc.debris_M >= 0 AND rc.debris_C >= 0 AND (rc.debris_M + rc.debris_C) <= " . (int)$filters['debris_max'];
        }

        if (!empty($filters['rounds_min']) && is_numeric($filters['rounds_min'])) {
            $clauses[] = "rc.nb_rounds >= " . (int)$filters['rounds_min'];
        }
        if (!empty($filters['rounds_max']) && is_numeric($filters['rounds_max'])) {
            $clauses[] = "rc.nb_rounds <= " . (int)$filters['rounds_max'];
        }

        if (!empty($filters['hide_one_round']) && (int)$filters['hide_one_round'] === 1) {
            $clauses[] = "rc.nb_rounds > 1";
        }

        return $clauses;
    }

    public function get_empire_combat_report_list(int $player_id, int $sort = 2, int $sort2 = 0, array $filters = []): array
    {
        $player_id = (int)$player_id;
        if ($player_id <= 0) {
            return [];
        }

        $request = "SELECT rc.id_rc, astro.galaxy, astro.system, astro.row, rc.dateRC, rc.nb_rounds,";
        $request .= " rc.pertes_A, rc.pertes_D, rc.gain_M, rc.gain_C, rc.gain_D, rc.debris_M, rc.debris_C,";
        $request .= " CASE WHEN rc.gain_M >= 0 AND rc.gain_C >= 0 AND rc.gain_D >= 0";
        $request .= " THEN rc.gain_M + rc.gain_C + rc.gain_D ELSE -1 END AS total_gain,";
        $request .= " (rc.pertes_A + rc.pertes_D) AS total_losses,";
        $request .= " CASE WHEN rc.debris_M >= 0 AND rc.debris_C >= 0";
        $request .= " THEN rc.debris_M + rc.debris_C ELSE -1 END AS total_debris";
        $request .= " FROM " . TABLE_PARSEDRC . " rc";
        $request .= " INNER JOIN " . TABLE_USER_BUILDING . " astro ON rc.astro_object_id = astro.id";
        $request .= " WHERE astro.player_id = " . $player_id;

        foreach (self::get_empire_report_filter_clauses($filters) as $clause) {
            $request .= " AND " . $clause;
        }

        $request .= " ORDER BY " . self::get_empire_report_order_by($sort, $sort2);
        $result = $this->db->sql_query($request);

        $reports = [];
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $row['coordinates'] = $row['galaxy'] . ":" . $row['system'] . ":" . $row['row'];
            $reports[] = $row;
        }

        return $reports;
    }

    /**
    * Retrieves the number of combat reports associated with a planet
    * identified by its coordinates (galaxy, system, position).
     *
    * @param int $galaxy Galaxy number of the planet.
    * @param int $system System number of the planet.
    * @param int $row Planet position.
    * @return int Number of combat reports matching the planet.
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
    * Retrieves the list of combat report (CR) identifiers by planet coordinates.
     *
    * @param int $galaxy Galaxy number for target coordinates.
    * @param int $system System number for target coordinates.
    * @param int $row Position of the target planet in galaxy and system.
    * @return array Array of combat report identifiers (id_rc), sorted by descending date.
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
