<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Defense_Model  extends Model_Abstract
{
    /**
     * @param $playerId
     * @return array
     */
    public function select_player_defense($playerId)
    {
        global $log;
        $playerId = (int)$playerId;
        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - Player ID: " . $playerId);

        $tElemList = array("astro_object_id", "LM", "LLE", "LLO", "CG", "AI", "LP", "PB", "GB", "MIC", "MIP");

        // Colonnes préfixées par l'alias de la table udef pour éviter toute ambiguïté
        $selectFields = array();
        foreach ($tElemList as $field) {
            $selectFields[] = "udef.`" . $field . "`";
        }

        $request = "SELECT " . implode(", ", $selectFields) . " ";
        $request .= " FROM " . TABLE_USER_DEFENSE . " AS udef";
        // Utilisation des alias dans la clause ON et qualification de player_id dans WHERE
        $request .= " INNER JOIN " . TABLE_USER_BUILDING . " AS object ON udef.`astro_object_id` = object.`id`";
        $request .= " WHERE object.`player_id` = " . $playerId;
        $request .= " ORDER BY udef.`astro_object_id`";
        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - SQL Query: " . $request);

        $result = $this->db->sql_query($request);

        if (!$result) {
            $log->error("[OGSpy_Player_Defense_Model] select_player_defense - SQL Query FAILED!", ['error' => $this->db->sql_error()]);
        }

        $tDefense = array();
        if ($result) {
            while ($row =  $this->db->sql_fetch_assoc($result)) {
                $tDefense[$row["astro_object_id"]] = array();
                foreach ($tElemList as $elem) {
                    $tDefense[$row["astro_object_id"]][$elem] = $row[$elem] ?? 0;
                }
            }
        }
        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - Number of defense entries found: " . count($tDefense));
        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - Returned Defense Data:", ['data' => $tDefense]);
        return $tDefense;
    }

    public function select_player_defense_planete($planet_id)
    {
        $planet_id = (int)$planet_id;

        $tElemList = array("astro_object_id", "LM", "LLE", "LLO", "CG", "AI", "LP", "PB", "GB", "MIC", "MIP");

        $request = "SELECT `" . implode("`, `", $tElemList) . "` ";
        $request .= " FROM " . TABLE_USER_DEFENSE;
        // Corrigé: Utilisation de WHERE au lieu de AND
        $request .= " WHERE `astro_object_id` = " . $planet_id . " ";

        $result = $this->db->sql_query($request);

        return $this->db->sql_fetch_assoc($result);
    }


    /**
     * @param $aster_id Planet or moon to be deleted
     */
    public function delete_user_aster($aster_id)
    {
        $aster_id = (int)$aster_id;

        // Corrigé: Utilisation de astro_object_id au lieu de planet_id
        $request = "DELETE FROM " . TABLE_USER_DEFENSE . " WHERE `astro_object_id` = " . intval($aster_id);
        $this->db->sql_query($request);
    }
}
