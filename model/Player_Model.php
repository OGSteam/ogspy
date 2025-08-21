<?php

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Model extends Model_Abstract
{
    /**
     * Récupère les données d'un joueur en jeu à partir de son identifiant.
     *
     * @param int $player_id L'identifiant du joueur en jeu.
     * @return array|bool Retourne un tableau contenant les informations du joueur, ou false si non trouvé.
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
     * Obtient le nom du joueur en jeu à partir de son identifiant.
     *
     * @param int $player_id L'identifiant du joueur en jeu.
     * @return string|false Retourne le nom du joueur en jeu, ou false si non trouvé.
     */
    public function get_player_name(int $player_id)
    {
        $request = "SELECT `name`".
            " FROM " . TABLE_GAME_PLAYER;
        $request .= " WHERE `id` = " . $player_id;
        $result = $this->db->sql_query($request);

        list($playerName) = $this->db->sql_fetch_row($result);

        if (empty($playerName)) {
            return false;
        }

        return $playerName;
    }

    /**
     * Retrieves the player ID based on the provided player name.
     *
     * @param string $player_name The name of the player to retrieve the ID for.
     * @return int|false Returns the player ID as an integer if found, or false if the player does not exist.
     */
    public function getPlayerId(string $player_name)
    {
        $request = "SELECT `id`".
            " FROM " . TABLE_GAME_PLAYER;
        $request .= " WHERE `name` = '" . $player_name . "'";
        $result = $this->db->sql_query($request);

        list($playerId) = $this->db->sql_fetch_row($result);

        if (empty($playerId)) {
            return false;
        }

        return $playerId;
    }


    /**
     * A quoi sert donc cette fonction ? :p
     * Reponse elle sert a mettre a jour le pseudo ingame afin d afficher les stats users dans son espace perso
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
     * Récupère l'ID du joueur en jeu associé à un utilisateur OGSpy.
     *
     * @param int $ogspy_user_id L'identifiant de l'utilisateur OGSpy.
     * @return int|null Retourne l'ID du joueur en jeu, ou null si non trouvé ou non défini.
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
