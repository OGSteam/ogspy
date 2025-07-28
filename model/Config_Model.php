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
        $this->log->debug("Config Model get: " . print_r($output, true));
        return $output;
    }

    /**
     * Fonction de recherche de la configuration
     * @param array|null $filter
     * @return array|bool
     */
    public function get(?array $filter): bool|array
    {
        if ($filter === null) {
            return false;
        }

        $escapedFilter = array_map([$this->db, 'sql_escape_string'], $filter);
        $queryStr = "'" . implode("','", $escapedFilter) . "'";
        $query = "SELECT `name`, `value` FROM " . TABLE_CONFIG . " WHERE `name` IN ($queryStr)";

        $result = $this->db->sql_query($query);
        $output = [];
        while ($row = $this->db->sql_fetch_row($result)) {
            $output[$row[0]] = stripslashes($row[1]);
        }
        return $output;
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

        $query = "SELECT `name`, `value` FROM " . TABLE_CONFIG;
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
                    `value` = '" . $this->db->sql_escape_string($config['value']) . "'
                 WHERE `name` = '" . $this->db->sql_escape_string($config['name']) . "'";
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
        $query = "INSERT INTO " . TABLE_CONFIG . " (`name`, `value`)
              VALUES ('" . $this->db->sql_escape_string($configName) . "',
                      '" . $this->db->sql_escape_string($configValue) . "')
              ON DUPLICATE KEY UPDATE `value` = '" . $this->db->sql_escape_string($configValue) . "'";
        $this->db->sql_query($query);
    }
}
