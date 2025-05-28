<?php

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Model extends Model_Abstract
{
    /**
     * Récupère les données d'un joueur spécifique à partir de la base de données.
     *
     * @param int $player_id L'identifiant unique du joueur à récupérer.
     * @return array|false Retourne un tableau contenant les données du joueur si trouvé, ou false si aucun joueur n'est trouvé.
     *
     * Le processus inclut :
     * - Conversion de l'identifiant du joueur en entier pour éviter les injections SQL.
     * - Exécution d'une requête SQL pour récupérer les données du joueur.
     * - Parcours des résultats pour les stocker dans un tableau associatif.
     * - Vérification si le tableau est vide, auquel cas false est retourné.
     */
    public function get_player_data($player_id)
    {
        $player_id = (int)$player_id;

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
    public function set_game_class_type($user_id, $user_class)
    {
        $user_id = (int)$user_id;
        $user_class = $this->db->sql_escape_string($user_class);

        $request = "UPDATE " . TABLE_USER . " SET `user_class`  = '" . $user_class . "' WHERE `user_id` = " . $user_id;

        $this->db->sql_query($request);
    }

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
