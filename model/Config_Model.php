<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author Itori
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Config_Model  extends Model_Abstract
{

    /**
     * Retourne tous les elements de la configuration
     * @return array
     */
    public function get_all()
    {
        $output = array();
        $request = "select * from " . TABLE_CONFIG;
        $result = $this->db->sql_query($request);
        // Output config as PHP code
        while ($cur_config_item = $this->db->sql_fetch_row($result)) {
            $output[$cur_config_item[0]] = stripslashes($cur_config_item[1]);
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
     * @param $config_value
     * @param $config_name
     */
    public function update_one($config_value, $config_name)
    {
        $query = "UPDATE " . TABLE_CONFIG . " SET
                    `config_value` = '" . $this->db->sql_escape_string($config_value) . "'
                 WHERE `config_name` = '" . $this->db->sql_escape_string($config_name) . "'";
        $this->db->sql_query($query);
    }
}
