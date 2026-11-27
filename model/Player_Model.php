<?php

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Model extends Model_Abstract
{
    /**
    * Retrieves in-game player data from its identifier.
     *
    * @param int $player_id In-game player identifier.
    * @return array|bool Returns an array containing player information, or false if not found.
     */
    public function get_player_data(int $player_id)
    {
        $request = "SELECT `id`, `name`, `status`, `class`, `ally_id`, `datadate`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`".
            " FROM " . TABLE_GAME_PLAYER;
        $request .= " WHERE `id` = " . $player_id;
        $request .= " ORDER BY `name`";
        $result = $this->db->sql_query($request);

       $info_users = $this->db->sql_fetch_assoc($result);

        if (empty($info_users)) {
            return false;
        }

        return $info_users;
    }

    /**
    * Retrieves the in-game player name from its identifier.
     *
    * @param int $player_id In-game player identifier.
     * @return string|false Returns le name of the joueur en jeu, ou false si non trouvé.
     */
    public function get_player_name(int $player_id)
    {
        $request = "SELECT `name`".
            " FROM " . TABLE_GAME_PLAYER;
        $request .= " WHERE `id` = " . $player_id;
        $result = $this->db->sql_query($request);

        $row = $this->db->sql_fetch_row($result);
        if (!is_array($row) || !isset($row[0]) || $row[0] === '') {
            return false;
        }

        return $row[0];
    }

    /**
    * Retrieves the player identifier from its name.
     *
     * @param string $player_name Name of the joueur.
     * @return int|false Identifier of the joueur si trouvé, sinon false.
     */
    public function getPlayerId(string $player_name)
    {
        $player_name = $this->db->sql_escape_string($player_name);
        $request = "SELECT `id`".
            " FROM " . TABLE_GAME_PLAYER;
        $request .= " WHERE `name` = '" . $player_name . "'";
        $result = $this->db->sql_query($request);

        $row = $this->db->sql_fetch_row($result);
        if (!is_array($row) || !isset($row[0]) || $row[0] === '') {
            return false;
        }

        return $row[0];
    }


    /**
     * A quoi sert donc cette fonction ? :p
     * Reponse elle sert a mettre a jour le pseudo ingame afin d afficher the stats users dans son espace perso
     *
     * set_game_account_id ?
     * 
     * @param $user_id
     * @param $user_stat_name
     */
    public function set_game_account_name($user_id, $player_id)
    {
        $user_id = (int)$user_id;
        $player_id = $this->db->sql_escape_string($player_id);

        $request = "UPDATE " . TABLE_USER . " SET `player_id` = '$player_id' WHERE `id` = $user_id";
        $this->db->sql_query($request);
    }

    /**
     *
     * @param user_class
     */
    /*public function set_game_class_type($user_id, $user_class)
    {
        $user_id = (int)$user_id;
        $user_class = $this->db->sql_escape_string($user_class);

        $request = "UPDATE " . TABLE_USER . " SET `user_class`  = '" . $user_class . "' WHERE `user_id` = " . $user_id;

        $this->db->sql_query($request);
    }*/

    /**
     * @param $user_id
     * @param $officer
     * @param $value
     */
    public function set_player_officer($user_id, $officer, $value)
    {
        $officer = $this->db->sql_escape_string($officer);
        $value = (int)$value;

        $allowedOfficers = [
            'off_commandant',
            'off_amiral',
            'off_ingenieur',
            'off_geologue',
            'off_technocrate',
        ];

        if (in_array($officer, $allowedOfficers)) {
            $request = "UPDATE " . TABLE_USER . " SET `" . $officer . "` = " . $value . " WHERE `user_id` = " . (int)$user_id;
            $this->db->sql_query($request);
        }
    }

    /**
    * Retrieves the in-game player ID associated with an OGSpy user.
     *
    * @param int $ogspy_user_id OGSpy user identifier.
     * @return int|null Returns l'ID of the joueur en jeu, ou null si non trouvé ou non défini.
     */
    public function get_game_player_id_for_user($ogspy_user_id)
    {
        $ogspy_user_id = (int)$ogspy_user_id;

        $request = "SELECT `player_id` FROM " . TABLE_USER . " WHERE `id` = " . $ogspy_user_id;
        $result = $this->db->sql_query($request);

        if ($row = $this->db->sql_fetch_assoc($result)) {
            // La colonne player_id peut être NULL dans la base de données
            return $row['player_id'] !== null ? (int)$row['player_id'] : null;
        }

        return null; // Utilisateur non trouvé ou player_id non défini
    }
}
