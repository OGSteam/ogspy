<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Itori
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;


class Mod_User_Config_Model
{
    /**
     * Retourne la configuration pour le module pour un utilisateur
     * @param string $module Nom du module
     * @param int $user_id Utilisateur sur lequel filtrer
     * @param string $config Configuration
     * @return array Liste des valeurs correspondantes
     */
    public function get_mod_config($module, $user_id, $config = null)
    {
        global $db;
        $request = "SELECT `config`, `value` FROM `" . TABLE_MOD_USER_CFG . "` WHERE `mod` = '" . $module . "' AND `user_id` = " . $user_id;
        if ($config != null) {
                    $request .= " AND `config` = '" . $config . "'";
        }

        $queryResult = $db->sql_query($request);

        $values = array();
        while ($value = $db->sql_fetch_row($queryResult)) {
            $values[$value[0]] = $value[1];
        }

        return $values;
    }

    /**
     * Supprime la configuration souhaitée pour le module
     * @param string $module Nom du module
     * @param int $user_id Utilisateur sur lequel filtrer
     * @param string $config Configuration
     * @return bool succès
     */
    public function delete_mod_config($module, $user_id = null, $config = null)
    {
        global $db;
        $query = "DELETE FROM `" . TABLE_MOD_USER_CFG . "` WHERE `mod` = '" . $module . "'";
        if ($user_id != null) {
                    $query .= " AND `user_id` = " . $user_id;
        }
        if ($config != null) {
                    $query .= " AND `config` = '" . $config . "'";
        }

        if (!$db->sql_query($query)) {
                    return false;
        }

        return true;
    }

    /**
     * Défini la valeur de la configuration fournie
     * @param string $module Nom du module
     * @param string $config Configuration
     * @param int $user_id
     * @param string $value Valeur
     * @return bool succès
     */
    public function set_mod_config($module, $config, $user_id, $value)
    {
        global $db;

        $query = 'REPLACE INTO `' . TABLE_MOD_USER_CFG . '` VALUES ("' . $module . '", "' . $config . '", ' . $user_id . ',"' . $value . '")';

        if (!$db->sql_query($query)) {
            return false;
        }
        return true;
    }
}