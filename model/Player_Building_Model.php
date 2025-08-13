<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Player_Building_Model  extends Model_Abstract
{
    /**
     * Retrieves the list of planets associated with a user, organized by coordinates.
     *
     * This method executes an SQL query to get the planet IDs and their coordinates
     * associated with a given user. The results are organized in an associative array
     * where the keys are the coordinates and the values are the planet IDs.
     *
     * @param int $user_id The user identifier.
     * @return array An associative array containing coordinates as keys and planet IDs as values.
     */

    public function get_planet_list($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `planet_id`, `coordinates`";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " ORDER BY `planet_id`";
        $result =  $this->db->sql_query($request);
        while (list($planet_id, $coordinates) = $this->db->sql_fetch_row($result)) {
            $planet_position[$coordinates] = $planet_id;
        }
        return $planet_position;
    }
    /**
     * @param $user_id
     * @return mixed
     */
    public function get_moon_list($player_id)
    {
        // les lunes
        $request = "SELECT `id`, `coordinates`";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id;
        $request .= " AND `type` = 'moon'";
        $request .= " ORDER BY `id`";
        $result =  $this->db->sql_query($request);
        while (list($planet_id, $coordinates) = $this->db->sql_fetch_row($result)) {
            $moon_position[$coordinates] = $planet_id;
        }
        return $moon_position;
    }
    /**
     * Récupère le nombre de planètes d'un joueur spécifique.
     *
     * Cette méthode exécute une requête SQL pour compter le nombre de planètes
     * associées à un joueur donné dans la table `TABLE_USER_BUILDING`.
     *
     * @param int $player_id L'identifiant du joueur dont les planètes doivent être comptées.
     * @return int Le nombre de planètes du joueur.
     */
    public function get_nb_planets(int $player_id)
    {
        $request = "SELECT COUNT(*) ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id . " AND `type` = 'planet'";

        $result =  $this->db->sql_query($request);
        list($count) = $this->db->sql_fetch_row($result);
        return  $count;
    }
    /**
     * Récupère le nombre de lunes d'un joueur spécifique.
     *
     * Cette méthode exécute une requête SQL pour compter le nombre de lunes
     * associées à un joueur donné dans la table `TABLE_USER_BUILDING`.
     *
     * @param int $player_id L'identifiant du joueur dont les lunes doivent être comptées.
     * @return int Le nombre de lunes du joueur.
     */
    public function get_nb_moons(int $player_id)
    {
        $request = "SELECT COUNT(*) ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id . " AND `type` = 'moon'";

        $result = $this->db->sql_query($request);
        list($count) = $this->db->sql_fetch_row($result);
        return  $count;
    }



    /**
     * Récupère les boosters d'un joueur spécifique.
     *
     * Cette méthode interroge la table `TABLE_USER_BUILDING` pour récupérer
     * les informations sur les boosters associés à un joueur donné.
     *
     * @param int $player_id L'identifiant du joueur dont les boosters doivent être récupérés.
     * @return array Un tableau associatif contenant les boosters pour chaque planète du joueur.
     *               Chaque élément du tableau est une entrée associative avec les clés :
     *               - `user_id` : L'identifiant de l'utilisateur.
     *               - `planet_id` : L'identifiant de la planète.
     *               - `boosters` : Les boosters associés.
     */
    public function get_all_booster_player(int $player_id)
    {
        $request = "SELECT `player_id`, `id`, `boosters` FROM " . TABLE_USER_BUILDING . " WHERE `player_id`=" . $player_id;
        $result = $this->db->sql_query($request);

        $playerBoosters = array();
        while (list($player_id, $id, $boosters) = $this->db->sql_fetch_row($result)) {
            $playerBoosters[$id] = array("user_id" => $player_id, "planet_id" => $id, "boosters" => $boosters);
        }
        return $playerBoosters;
    }

    /**
     * Récupère les boosters de tous les utilisateurs.
     *
     * Cette méthode interroge la table `TABLE_USER_BUILDING` pour récupérer
     * les informations sur les boosters associés à chaque utilisateur et planète.
     *
     * @return array Un tableau contenant les boosters pour chaque utilisateur et planète.
     *               Chaque élément du tableau est une entrée associative avec les clés :
     *               - `user_id` : L'identifiant de l'utilisateur.
     *               - `planet_id` : L'identifiant de la planète.
     *               - `boosters` : Les boosters associés.
     */
    public function get_all_booster()
    {
        $request = "SELECT `user_id`, `planet_id`, `boosters` FROM " . TABLE_USER_BUILDING;
        $result = $this->db->sql_query($request);

        $Boosters = array();
        while (list($user_id, $planet_id, $boosters) = $this->db->sql_fetch_row($result)) {
            $Boosters[] = array("user_id" => $user_id, "planet_id" => $planet_id, "boosters" => $boosters);
        }
        return $Boosters;
    }

    /* Écrit la string de stockage des objets Ogame dans la BDD.
     * @arg id_player   id du joueur
     * @arg id_planet   id de la planète à rechercher
     * @str_booster     string de stockage des boosters (donnée par les fonctions booster_encode() ou booster_encodev())
     * @return FALSE en cas d'échec
    */
    /**
     * @param $id_player
     * @param $id_planet
     * @param $str_booster
     * @return bool
     */
    public function update_booster($user_id, $planet_id, $boosters)
    {
        $user_id = (int)$user_id;
        $planet_id = (int)$planet_id;
        $boosters = $this->db->sql_escape_string($boosters);

        $requests = "UPDATE " . TABLE_USER_BUILDING . " SET `boosters` = '" . $boosters . "' " .
            " WHERE `user_id` = " . $user_id .
            " AND `planet_id` = " . $planet_id;

        return $this->db->sql_query($requests);
    }

    /**
     * Récupère la liste des bâtiments associés à un joueur spécifique.
     *
     * Cette méthode interroge la table `TABLE_USER_BUILDING` pour obtenir les informations
     * sur tous les bâtiments d'un joueur donné, identifiés par son `player_id`.
     *
     * @param int $player_id L'identifiant unique du joueur dont les bâtiments doivent être récupérés.
     * @return array Un tableau associatif contenant les bâtiments du joueur spécifié.
     *               Les clés principales du tableau correspondent aux identifiants uniques des bâtiments (`id`).
     *               Chaque entrée contient un tableau associatif décrivant les attributs du bâtiment,
     *               tels que :
     *               - `id` : L'identifiant du bâtiment.
     *               - `name` : Le nom du bâtiment.
     *               - `galaxy` : La galaxie dans laquelle le bâtiment se trouve.
     *               - `system` : Le système dans lequel le bâtiment se trouve.
     *               - `row` : La rangée dans laquelle le bâtiment se situe.
     *               - `fields`, `boosters`, `temperature_min`, `temperature_max`, et d'autres attributs
     *                 spécifiques au bâtiment.
     *               Les détails de chaque élément correspondent aux colonnes listées dans `$tElemList`.
     */
    public function select_player_building_list($player_id)
    {
        $player_id = (int)$player_id;

        $tElemList = array("id", "name", "galaxy", "system","row",  "fields", "boosters", "temperature_min", "temperature_max", "Sat", "Sat_percentage", "FOR", "FOR_percentage", "M", "M_percentage", "C", "C_percentage", "D", "D_percentage", "CES", "CES_percentage", "CEF", "CEF_percentage", "UdR", "UdN", "CSp", "HM", "HC", "HD", "Lab", "Ter", "Silo", "Dock", "BaLu", "Pha", "PoSa", "DdR");

        $request = "SELECT `" . implode("`, `", $tElemList) . "` ";
        $request .= " FROM " . TABLE_USER_BUILDING;
        $request .= " WHERE `player_id` = " . $player_id;
        $request .= " ORDER BY `id`";
        $result =  $this->db->sql_query($request);

        $tbuilding = array();
        while ($row =  $this->db->sql_fetch_assoc($result)) {
            $tbuilding[$row["id"]] = array();
            foreach ($tElemList as $elem) {
                $tbuilding[$row["id"]][$elem] = $row[$elem];
            }
        }
        return $tbuilding;
    }

    /**
     * Récupère les bâtiments en fonction du niveau de silo spécifié.
     *
     * Cette méthode interroge la table `TABLE_USER_BUILDING` pour récupérer
     * les informations sur les bâtiments qui ont un niveau de silo supérieur ou égal
     * au niveau fourni en paramètre.
     *
     * @param int $silo_level Le niveau de silo minimum requis pour récupérer les bâtiments.
     * @return array Un tableau contenant les informations des bâtiments correspondant au critère.
     *               Chaque élément du tableau est une entrée associative avec les clés :
     *               - `user_id` : L'identifiant de l'utilisateur.
     *               - `planet_id` : L'identifiant de la planète.
     *               - `coordinates` : Les coordonnées du bâtiment.
     *               - `Silo` : Le niveau du silo.
     */
    public function get_building_by_silo(int $silo_level)
    {
        $query =  "SELECT `id`, `player_id`, `galaxy`, `system`, `row`, `Silo` FROM " . TABLE_USER_BUILDING . " WHERE `Silo` >= $silo_level ";
        $result =  $this->db->sql_query($query);

        $tbuilding = array();
        while ($building =  $this->db->sql_fetch_assoc($result)) {
            $tbuilding[] = $building;
        }

        return $tbuilding;
    }

    /**
     * Supprime un astéroïde associé à un utilisateur spécifique.
     *
     * Cette méthode supprime l'enregistrement d'un astéroïde spécifique
     * pour un utilisateur particulier de la table `TABLE_USER_BUILDING`.
     *
     * @param int $user_id L'identifiant de l'utilisateur auquel l'astéroïde est associé.
     * @param int $aster_id L'identifiant de l'astéroïde à supprimer.
     * @return void Cette méthode ne retourne aucune valeur.
     */
    public function delete_user_aster($user_id, $aster_id)
    {
        $user_id = (int)$user_id;
        $aster_id = (int)$aster_id;

        $request = "DELETE FROM " . TABLE_USER_BUILDING . " WHERE `user_id` = " . $user_id . " AND `planet_id` = " . intval($aster_id);
        $this->db->sql_query($request);
    }
}
