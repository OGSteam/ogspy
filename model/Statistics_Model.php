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

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Statistics_Model extends Model_Abstract
{
    public function add_user_connection() {
        $request = "UPDATE " . TABLE_STATISTIC .
            " SET statistic_value = statistic_value + 1";
        $request .= " WHERE statistic_name = 'connection_server'";
        $this->db->sql_query($request);

        if ($this->db->sql_affectedrows() == 0) {
            $this->add_statistic_name('connection_server');
        }

    }

    private function add_statistic_name($name) {
        $request = "INSERT IGNORE INTO " . TABLE_STATISTIC .
            " VALUES ('" . $name . "', '1')";
        $this->db->sql_query($request);
    }

    /**
     * @return array
     */
    public function find()
    {
        $request = "select statistic_name, statistic_value from " . TABLE_STATISTIC;
        $result = $this->db->sql_query($request);

        $stats = array();

        while (list($statistic_name, $statistic_value) = $this->db->sql_fetch_row($result)) {
            $stats[$statistic_name] = $statistic_value;
        }

        return $stats;
    }


}