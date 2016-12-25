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

/**
 * Classe permettant l'accès à la table de configuration des mods
 * @package Ogsteam\Ogspy
 */
class Mod_Config_Model
{
    /**
     * Retourne la configuration pour le module
     * @param string $module Nom du module
     * @param string $config Configuration
     * @return array Liste des valeurs correspondantes
     */
    public function get_mod_config($module, $config = null)
    {
        global $db;
        $request = "SELECT `value` FROM `" . TABLE_MOD_CFG . "` WHERE `mod` = '" . $module . "'";
         if($config != null)
            $request .= " AND `config` = '" . $config . "'";

        $queryResult = $db->sql_query($request);

        $values = array();
        while($value = $db->sql_fetch_row($queryResult))
            $values[] = $value[0];

        if (count($values) == 1)
            return $values[0];
        else
            return $values;
    }

    /**
     * Supprime la configuration souhaitée pour le module
     * @param string $module Nom du module
     * @param string $config Configuration
     * @return bool succès
     */
    public function delete_mod_config($module, $config = null)
    {
        global $db;
        $query = "DELETE FROM `" . TABLE_MOD_CFG . "` WHERE `mod` = '" . $module . "'";
        if($config != null)
            $query .= " AND `config` = '" . $config . "'";

        if(!$db->sql_query($query))
            return false;

        return true;
    }

    /**
     * Défini la valeur de la configuration fournie
     * @param string $module Nom du module
     * @param string $config Configuration
     * @param string $value Valeur
     * @return bool succès
     */
    public function set_mod_config($module, $config, $value)
    {
        global $db;

        $query = 'REPLACE INTO `' . TABLE_MOD_CFG . '` VALUES ("' . $module . '", "' . $config . '", "' . $db->sql_escape_string($value) . '")';

        if (!$db->sql_query($query)) return false;
        return true;
    }
}