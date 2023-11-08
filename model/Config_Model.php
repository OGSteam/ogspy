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

class Config_Model extends Model_Abstract
{
    //TODO: There is no method to create a new configuration if not exists
    /**
     * Retourne tous les elements de la configuration
     * @return array
     */
    public function get_all()
    {
        $output = [];
        $request = "SELECT * from " . TABLE_CONFIG;
        $result = $this->db->sql_query($request);
        // Output config as PHP code
        while ($cur_config_item = $this->db->sql_fetch_row($result)) {
            $output[$cur_config_item[0]] = stripslashes($cur_config_item[1]);
        }
        return $output;
    }

    /**
     * Fonction de recherche de la configuration
     * @param array|null $filter
     * @return array|bool
     */
    public function get(array $filter = null): bool|array|null
    {
        if ($filter === null) {
            return false;
        }

        $queryStr = "'" . implode("','", $filter) . "'";
        $query = "SELECT `config_name`, `config_value` FROM " . TABLE_CONFIG . " WHERE `config_name` IN ($queryStr)";

        $result = $this->db->sql_query($query);
        return $this->db->sql_fetch_assoc($result);
    }

    /**
     * Fonction de recherche de la configuration
     * @param array $filter
     * @return array
     */
    public function find_by($filter = array())
    {
        if ($filter == null) {
            $filter = array();
        }

        $query = "SELECT `config_name`, `config_value` FROM " . TABLE_CONFIG;
        $i = 0;
        foreach ($filter as $key => $value) {
            if ($i == 0) {
                $query .= " WHERE ";
            } else {
                $query .= " AND ";
            }
            $query .= "`" . $this->db->sql_escape_string($key) . "` = '" . $this->db->sql_escape_string($value) . "'";
            $i++;
        }
        $result = $this->db->sql_query($query);
        $configs = array();
        while ($config = $this->db->sql_fetch_assoc($result)) {
            $configs[$config[0]] = $config[1];
        }

        return $configs;
    }
    /**
     * Met à jour la config
     * @param array $config tableau associatif représentant le mod
     */
    public function update(array $config)
    {
        $query = "UPDATE " . TABLE_CONFIG . " SET
                    `config_value` = '" . $this->db->sql_escape_string($config['config_value']) . "'
                 WHERE `config_name` = '" . $this->db->sql_escape_string($config['config_name']) . "'";
        $this->db->sql_query($query);
    }

    /**
     * Met à jour la config
     * @$config_value valeur de la configuration
     * @$config_name nom de la configuration
     * @param $configName
     * @param $configValue
     */
    public function update_one( $configValue, $configName)
    {
        $query = "INSERT INTO " . TABLE_CONFIG . " (`config_name`, `config_value`)
              VALUES ('" . $this->db->sql_escape_string($configName) . "',
                      '" . $this->db->sql_escape_string($configValue) . "')
              ON DUPLICATE KEY UPDATE `config_value` = '" . $this->db->sql_escape_string($configValue) . "'";
        $this->db->sql_query($query);
    }
}
