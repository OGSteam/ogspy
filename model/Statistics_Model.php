<?php
/**
 * Created by IntelliJ IDEA.
 * User: anthony
 * Date: 17/08/16
 * Time: 14:27
 */

namespace Ogsteam\Ogspy\Model;


class Statistics_Model
{
    public function add_user_connection(){
        global $db;
        $request = "UPDATE " . TABLE_STATISTIC .
            " SET statistic_value = statistic_value + 1";
        $request .= " WHERE statistic_name = 'connection_server'";
        $db->sql_query($request);

        if ($db->sql_affectedrows() == 0) {

            $this->add_statistic_name('connection_server');
        }

    }

    private function add_statistic_name($name){
        global $db;
        $request = "INSERT IGNORE INTO " . TABLE_STATISTIC .
            " VALUES ('".$name."', '1')";
        $db->sql_query($request);
    }

    /**
     * @return array
     */
    public function find()
    {
        global $db;

        $request = "select statistic_name, statistic_value from " . TABLE_STATISTIC;
        $result = $db->sql_query($request);

        $stats = array();

        while (list($statistic_name, $statistic_value) = $db->sql_fetch_row($result)) {
            $stats[$statistic_name] = $statistic_value;
        }

        return $stats;
    }


}