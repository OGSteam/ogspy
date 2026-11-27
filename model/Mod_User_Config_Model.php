<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Itori
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Mod_User_Config_Model extends Model_Abstract
{
    /**
    * Returns module configuration for the user.
     * @param string $module Name of the module
     * @param int $userid Configuration
     * @param string $config Configuration
     * @return array Liste des values correspondantes
     */
    public function get_user_mod_config($module, $userid, $config = null)
    {
        $module = $this->db->sql_escape_string($module);

        $request = "SELECT `value` FROM `" . TABLE_MOD_USER_CFG . "` WHERE `mod` = '{$module}' AND `user_id` = {$userid}";
        if ($config != null) {
            $config = $this->db->sql_escape_string($config);
            $request .= " AND `config` = '{$config}'";
        }

        $queryResult = $this->db->sql_query($request);

        $values = array();
        while ($value = $this->db->sql_fetch_row($queryResult)) {
            $values[] = $value[0];
        }

        if (count($values) === 1) {
            return $values[0];
        }
        return $values;
    }

    /**
     * Deletes la configuration souhaitée for l'utilsateur of the module
     * @param string $module Name of the module
     * @param string $config Configuration
     * @return bool success
     */
    public function delete_user_mod_config($module, $userid, $config = null)
    {
        $module = $this->db->sql_escape_string($module);

        $query = "DELETE FROM `" . TABLE_MOD_USER_CFG . "` WHERE `mod` = '{$module}' AND `user_id` = {$userid}";
        if ($config != null) {
            $config = $this->db->sql_escape_string($config);
            $query .= " AND `config` = '{$config}'";
        }

        if (!$this->db->sql_query($query)) {
            return false;
        }

        return true;
    }

    /**
     * Defines la value of the configuration fournie for l'utilateur of the module
     * @param string $module Name of the module
     * @param int    $userid Configuration
     * @param string $config Configuration
     * @param string $value Valeur
     * @return bool success
     */
    public function set_user_mod_config($module, $userid, $config, $value)
    {
        $module = $this->db->sql_escape_string($module);
        $config = $this->db->sql_escape_string($config);
        $value = $this->db->sql_escape_string($value);

        $query = "REPLACE INTO `" . TABLE_MOD_USER_CFG . "` VALUES ('{$module}', '{$userid}', '{$config}', '{$value}')";

        if (!$this->db->sql_query($query)) {
            return false;
        }
        return true;
    }
}
