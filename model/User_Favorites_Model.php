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

class User_Favorites_Model  extends Model_Abstract
{
    /**
     * Gets the favorite system list for the defined user
     * @param $user_id
     * @return array $favorite Liste des systèmes favoris
     */
    public function select_user_favorites($user_id)
    {
        $user_id = (int)$user_id;

        $favorite = array();

        $request = "SELECT `galaxy`, system FROM " . TABLE_USER_FAVORITE;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " ORDER BY `galaxy`, `system`";
        $result = $this->db->sql_query($request);

        while (list($galaxy, $system) = $this->db->sql_fetch_row($result)) {
            $favorite[] = array("galaxy" => $galaxy, "system" => $system);
        }

        return $favorite;
    }

    /**
     * @param $user_id
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_user_favorites($user_id)
    {
        $user_id = (int)$user_id;


        $request = "SELECT * FROM " . TABLE_USER_FAVORITE . " WHERE `user_id` = " . $user_id;
        $result = $this->db->sql_query($request);
        return $this->db->sql_numrows($result);
    }

    /**
     * @param $user_id
     * @param $galaxy
     * @param $system
     */
    public function set_user_favorites($user_id, $galaxy, $system)
    {
        $user_id = (int)$user_id;
        $galaxy = (int)$galaxy;
        $system = (int)$system;

        $request = "INSERT IGNORE INTO " . TABLE_USER_FAVORITE .
            " (`user_id`, `galaxy`, `system`) VALUES (" . $user_id . ", '" . $galaxy . "', " . $system . ")";
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $galaxy
     * @param $system
     */
    public function delete_user_favorites($user_id, $galaxy, $system)
    {
        $user_id = (int)$user_id;
        $galaxy = (int)$galaxy;
        $system = (int)$system;


        $request = "DELETE FROM " . TABLE_USER_FAVORITE . " WHERE `user_id` = " . $user_id .
            " AND `galaxy` = '" . $galaxy . "' and `system` = " . $system;
        $this->db->sql_query($request);
    }

    /**
     * Supprime les Favoris qui ne sont plus accessibles après redimensionnement de univers
     * @param $nb_galaxies
     * @param $nb_system
     */
    public function delete_favorites_after_resize($nb_galaxies, $nb_system)
    {
        $nb_galaxies = (int)$nb_galaxies;
        $nb_system = (int)$nb_system;

        $this->db->sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE `galaxy` > $nb_galaxies");
        $this->db->sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE `system` > $nb_system");
    }
}
