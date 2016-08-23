<?php
/**
 * Created by PhpStorm.
 * User: Itori
 * Date: 18/08/2016
 * Time: 21:53
 */

namespace Ogsteam\Ogspy\Model;


class Mod_Model
{
        public function find_by($filter = array(), $orderBy = array())
        {
            global $db;

            if($filter == null)
                $filter = array();
            if($orderBy == null)
                $orderBy = array();

            $query = "SELECT `id`, 
                             `title`, 
                             `menu`, 
                             `action`, 
                             `root`, 
                             `link`, 
                             `version`, 
                             `position`,
                             `active`,
                             `admin_only`
                        FROM `" . TABLE_MOD . "`";

            $i = 0;
            foreach ($filter as $key => $value)
            {
                if($i == 0)
                    $query .= " WHERE ";
                else
                    $query .= " AND ";

                $query .= "`" . $db->sql_escape_string($key) . "` = '" . $db->sql_escape_string($value) . "'";
                $i++;
            }

            $i = 0;
            foreach ($orderBy as $key => $value)
            {
                if($i == 0)
                    $query .= " ORDER BY ";
                else
                    $query .= ", ";

                $query .= $db->sql_escape_string($key);
                if($value == 'DESC')
                    $query .= ' DESC';
                $i++;
            }

            $result = $db->sql_query($query);
            $mods = array();
            while($mod = $db->sql_fetch_assoc($result))
                $mods[] = $mod;

            return $mods;
        }
}