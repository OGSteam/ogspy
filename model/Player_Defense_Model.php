<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @version 4.0.0
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
            $selectFields[] = "def.`" . $field . "`";
        }

        $request = "SELECT " . implode(", ", $selectFields) . " ";
        $request .= " FROM " . TABLE_GAME_PLAYER_DEFENSE . " AS def";
        // Jointure avec la table des objets astraux pour obtenir les informations du joueur
        $request .= " INNER JOIN " . TABLE_GAME_ASTRO_OBJECT . " AS astro ON def.`astro_object_id` = astro.`id`";
        $request .= " WHERE astro.`player_id` = " . $playerId;
        $request .= " ORDER BY def.`astro_object_id`";
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

        $tElemList = array("id", "astro_object_id", "LM", "LLE", "LLO", "CG", "AI", "LP", "PB", "GB", "MIC", "MIP");

        $request = "SELECT `" . implode("`, `", $tElemList) . "` ";
        $request .= " FROM " . TABLE_GAME_PLAYER_DEFENSE;
        $request .= " WHERE `astro_object_id` = " . $planet_id;

        $result = $this->db->sql_query($request);

        return $this->db->sql_fetch_assoc($result);
    }


    /**
     * @param $aster_id Planet or moon to be deleted
     */
    public function delete_user_aster($aster_id)
    {
        $aster_id = (int)$aster_id;

        $request = "DELETE FROM " . TABLE_GAME_PLAYER_DEFENSE . " WHERE `astro_object_id` = " . intval($aster_id);
        $this->db->sql_query($request);
    }

    /**
     * Insère ou met à jour les informations de défense pour un objet astral
     * @param $astro_object_id ID de l'objet astral
     * @param $defense_data Tableau contenant les données de défense
     * @return bool Succès de l'opération
     */
    public function insert_update_defense($astro_object_id, $defense_data)
    {
        global $log;
        $astro_object_id = (int)$astro_object_id;

        // Vérifier si l'enregistrement existe déjà
        $request = "SELECT `id` FROM " . TABLE_GAME_PLAYER_DEFENSE . " WHERE `astro_object_id` = " . $astro_object_id;
        $result = $this->db->sql_query($request);
        $row = $this->db->sql_fetch_assoc($result);

        if ($row) {
            // Mise à jour
            $fields = [];
            foreach ($defense_data as $key => $value) {
                if (in_array($key, ["LM", "LLE", "LLO", "CG", "AI", "LP", "PB", "GB", "MIC", "MIP"])) {
                    $fields[] = "`" . $key . "` = " . (int)$value;
                }
            }

            if (count($fields) > 0) {
                $request = "UPDATE " . TABLE_GAME_PLAYER_DEFENSE . " SET " . implode(", ", $fields);
                $request .= " WHERE `astro_object_id` = " . $astro_object_id;
                $log->info("[OGSpy_Player_Defense_Model] insert_update_defense - Update SQL Query: " . $request);
                return $this->db->sql_query($request);
            }

            return true;
        } else {
            // Insertion
            $fields = ["`astro_object_id`"];
            $values = [$astro_object_id];

            foreach ($defense_data as $key => $value) {
                if (in_array($key, ["LM", "LLE", "LLO", "CG", "AI", "LP", "PB", "GB", "MIC", "MIP"])) {
                    $fields[] = "`" . $key . "`";
                    $values[] = (int)$value;
                }
            }

            $request = "INSERT INTO " . TABLE_GAME_PLAYER_DEFENSE . " (" . implode(", ", $fields) . ")";
            $request .= " VALUES (" . implode(", ", $values) . ")";
            $log->info("[OGSpy_Player_Defense_Model] insert_update_defense - Insert SQL Query: " . $request);
            return $this->db->sql_query($request);
        }
    }
}
