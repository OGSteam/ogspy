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
     * Récupère les données de défense d'un joueur spécifique.
     *
     * @param int $playerId L'identifiant unique du joueur.
     * @return array Un tableau contenant les données de défense du joueur.
     */
    public function select_player_defense($playerId)
    {
        global $log;
        $playerId = (int)$playerId;
        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - Player ID: " . $playerId);

        $request = "SELECT `astro_object_id`, `LM`, `LLE`, `LLO`, `CG`, `AI`, `LP`, `PB`, `GB`, `MIC`, `MIP`";
        $request .= " FROM " . TABLE_GAME_PLAYER_DEFENSE . " AS def";
        // Jointure avec la table des objets astraux pour obtenir les informations du joueur
        $request .= " INNER JOIN " . TABLE_USER_BUILDING . " AS astro ON def.`astro_object_id` = astro.`id`";
        $request .= " WHERE astro.`player_id` = " . $playerId;
        $result = $this->db->sql_query($request);

        if (!$result) {
            $log->error("[OGSpy_Player_Defense_Model] select_player_defense - SQL Query FAILED!", ['error' => $this->db->sql_error()]);
        }

        while ($row = $this->db->sql_fetch_assoc($result)) {
            $raw_defense_data[] = $row;
        }

        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - Number of defense entries found: " . $this->db->sql_numrows($result));
        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - Returned Defense Data:", [ $raw_defense_data]);
        return $raw_defense_data;
    }
    /**
     * Récupère les données de défense d'une planète spécifique.
     *
     * @param int $planet_id L'identifiant unique de la planète.
     * @return array Un tableau associatif contenant les données de défense de la planète.
     */
    public function select_player_defense_planete(int $planet_id)
    {
        $tElemList = array("id", "astro_object_id", "LM", "LLE", "LLO", "CG", "AI", "LP", "PB", "GB", "MIC", "MIP");

        $request = "SELECT `" . implode("`, `", $tElemList) . "` ";
        $request .= " FROM " . TABLE_GAME_PLAYER_DEFENSE;
        $request .= " WHERE `astro_object_id` = " . $planet_id;

        $result = $this->db->sql_query($request);

        return $this->db->sql_fetch_assoc($result);
    }


    /**
     * Supprime les données d'un astre (planète ou lune) spécifique.
     *
     * @param int $aster_id L'identifiant unique de l'astre à supprimer.
     * @return void
     */
    public function delete_user_aster($aster_id)
    {
        $aster_id = (int)$aster_id;

        $request = "DELETE FROM " . TABLE_GAME_PLAYER_DEFENSE . " WHERE `astro_object_id` = " . intval($aster_id);
        $this->db->sql_query($request);
    }

}
