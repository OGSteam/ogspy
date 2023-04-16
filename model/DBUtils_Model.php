<?php

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

/**
 * Class DBUtils_Model
 * Classe de maintenance base de donnée
 * @package Ogsteam\Ogspy\Model
 */
class DBUtils_Model extends Model_Abstract
{

    /**
     * Retourne la taille de la base ainsi que la taille total des tables ogspy
     * @return array
     */
    public function SizeInfo()
    {
        global $table_prefix;
        $dbSizeServer = 0;
        $dbSizeTotal = 0;

        $request = "SHOW TABLE STATUS";
        $result = $this->db->sql_query($request);
        while ($row =  $this->db->sql_fetch_assoc($result)) {
            $dbSizeTotal += $row['Data_length'] + $row['Index_length'];
            if (preg_match("#^" . $table_prefix . ".*$#", $row['Name'])) {
                $dbSizeServer += $row['Data_length'] + $row['Index_length'];
            }
        }

        return array("dbSizeServer" => $dbSizeServer, "dbSizeTotal" => $dbSizeTotal);
    }

    /**
     * Optimize l'espace utilisé par la base de donnée
     */
    public function Optimize()
    {
        $request = 'SHOW TABLES';
        $res = $this->db->sql_query($request);
        while (list($table) = $this->db->sql_fetch_row($res)) {
            $request = 'OPTIMIZE TABLE ' . $table;
            $this->db->sql_query($request);
        }
    }
}
