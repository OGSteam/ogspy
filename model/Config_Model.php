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


class Config_Model
{

    /**
     * Fonction de recherche de la configuration
     * @param array $filter
     * @return array
     */
    public function find_by($filter = array())
    {
        global $db;

        if($filter == null)
            $filter = array();

        $query = "SELECT config_name, config_value FROM " . TABLE_CONFIG;

        $i = 0;
        foreach ($filter as $key => $value) {
            if ($i == 0)
                $query .= " WHERE ";
            else
                $query .= " AND ";

            $query .= "`" . $db->sql_escape_string($key) . "` = '" . $db->sql_escape_string($value) . "'";
            $i++;
        }

        $result = $db->sql_query($query);
        $configs = array();
        while ($config = $db->sql_fetch_assoc($result))
            $configs[] = $config;

        return $configs;
    }

    /**
     * Met à jour la config
     * @param array $config tableau associatif représentant le mod
     */
    public function update(array $config)
    {
        global $db;

        $query = "UPDATE " . TABLE_CONFIG . " SET 
                    `config_value` = '" . $db->sql_escape_string($config['config_value']) . "'
                 WHERE `config_name` = '" . $db->sql_escape_string($config['config_name']) . "'";
        $db->sql_query($query);
    }

}