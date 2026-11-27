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
    * Retrieves defense data for a player.
     *
     * @param int $playerId Identifier unique of the joueur.
     * @return array Tableau associatif contenant the defenses of the joueur.
     */
    public function select_player_defense(int $playerId)
    {
        global $log;
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
        $raw_defense_data = [];
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $raw_defense_data[] = $row;
        }

        $log->info("[OGSpy_Player_Defense_Model] select_player_defense - Number of defense entries found: " . $this->db->sql_numrows($result));
        return $raw_defense_data;
    }

    /**
    * Retrieves defense configuration for a planet.
     *
    * @param int $planet_id Planet identifier.
    * @return array|null Associative array of defenses, or null if no data.
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
     * Deletes une entrée of défense liée à un objet astronomique.
     *
     * @param int $aster_id Identifier of l'objet à delete.
     * @return void
     */
    public function delete_user_aster(int $aster_id)
    {
        $request = "DELETE FROM " . TABLE_GAME_PLAYER_DEFENSE . " WHERE `astro_object_id` = " . $aster_id;
        $this->db->sql_query($request);
    }

}
