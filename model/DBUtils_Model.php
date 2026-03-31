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

    /**
     * Truncates all game data tables (universe, rankings, spy/rc reports, player data).
     * User accounts, groups and server configuration are preserved.
     */
    public function truncate_game_data()
    {
        $tables = [
            TABLE_GAME_ALLY,
            TABLE_GAME_PLAYER,
            TABLE_PARSEDRC,
            TABLE_PARSEDRCROUND,
            TABLE_PARSEDSPY,
            TABLE_RANK_ALLY_ECO,
            TABLE_RANK_ALLY_HONOR,
            TABLE_RANK_ALLY_MILITARY,
            TABLE_RANK_ALLY_MILITARY_BUILT,
            TABLE_RANK_ALLY_MILITARY_DESTRUCT,
            TABLE_RANK_ALLY_MILITARY_LOOSE,
            TABLE_RANK_ALLY_POINTS,
            TABLE_RANK_ALLY_TECHNOLOGY,
            TABLE_RANK_PLAYER_ECO,
            TABLE_RANK_PLAYER_HONOR,
            TABLE_RANK_PLAYER_MILITARY,
            TABLE_RANK_PLAYER_MILITARY_BUILT,
            TABLE_RANK_PLAYER_MILITARY_DESTRUCT,
            TABLE_RANK_PLAYER_MILITARY_LOOSE,
            TABLE_RANK_PLAYER_POINTS,
            TABLE_RANK_PLAYER_TECHNOLOGY,
            TABLE_ROUND_ATTACK,
            TABLE_ROUND_DEFENSE,
            TABLE_USER_BUILDING,
            TABLE_GAME_PLAYER_DEFENSE,
            TABLE_GAME_PLAYER_FLEET,
            TABLE_USER_FAVORITE,
            TABLE_USER_SPY,
            TABLE_USER_TECHNOLOGY,
        ];

        foreach ($tables as $table) {
            $this->db->sql_query('TRUNCATE TABLE ' . $table);
        }
    }

    /**
     * Resets all server statistics counters to zero.
     */
    public function reset_statistics()
    {
        $this->db->sql_query('TRUNCATE TABLE ' . TABLE_STATISTIC);
    }

    /**
     * Resets the import/search counters stored on user accounts.
     */
    public function reset_user_stats()
    {
        $this->db->sql_query(
            'UPDATE ' . TABLE_USER .
            ' SET `planet_imports` = 0, `spy_imports` = 0, `rank_imports` = 0, `search` = 0'
        );
    }
}
