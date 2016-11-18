<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;


class User_Favorites_Model
{
    public function select_user_favorites($user_id)
    {
        //Empty for the moment
    }

    /**
     * @param $user_id
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_user_favorites($user_id)
    {

        global $db;
        $request = "SELECT * FROM " . TABLE_USER_FAVORITE . " WHERE user_id = " . $user_id;
        $result = $db->sql_query($request);
        return $db->sql_numrows($result);

    }

    /**
     * @param $user_id
     * @param $galaxy
     * @param $system
     */
    public function set_user_favorites($user_id, $galaxy, $system)
    {

        global $db;
        $request = "INSERT IGNORE INTO " . TABLE_USER_FAVORITE .
            " (user_id, galaxy, system) VALUES (" . $user_id . ", '" . $galaxy . "', " . $system . ")";
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $galaxy
     * @param $system
     */
    public function delete_user_favorites($user_id, $galaxy, $system)
    {
        global $db;
        $request = "delete from " . TABLE_USER_FAVORITE . " where user_id = " . $user_id .
            " and galaxy = '" . $galaxy . "' and system = " . $system;
        $db->sql_query($request);
    }

    /**
     * Supprime les Favoris qui ne sont plus accessibles après redimensionnement de univers
     * @param $nb_galaxies
     * @param $nb_system
     */
    public function delete_favorites_after_resize($nb_galaxies, $nb_system)
    {
        global $db;
        $db->sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE `galaxy` > $nb_galaxies");
        $db->sql_query("DELETE FROM " . TABLE_USER_FAVORITE . " WHERE `system` > $nb_system");
    }
}
