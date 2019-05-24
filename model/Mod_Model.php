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

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

/***
 * Class Mod_Model
 *
 * todo revoir tout ca avec refonte des mods (mod factory => cf old 3.4 )
 * @package Ogsteam\Ogspy\Model
 */
class Mod_Model  extends Model_Abstract
{
    /**
     * Fonction de recherche d'un mod en fonction des filtres définis
     * @param array $filter Tableau associatif ayant pour clé le champ à filtrer, et pour valeur la valeur souhaitée
     * @param array $orderBy tableau associatif sur le champ et l'ordre
     * @return array Liste de mods
     */
    public function find_by($filter = array(), $orderBy = array())
    {


        if ($filter == null) {
            $filter = array();
        }
        if ($orderBy == null) {
            $orderBy = array();
        }

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
        foreach ($filter as $key => $value) {
            if ($i == 0) {
                $query .= " WHERE ";
            } else {
                $query .= " AND ";
            }

            $query .= "`" .$this->db->sql_escape_string($key) . "` = '" .$this->db->sql_escape_string($value) . "'";
            $i++;
        }

        $i = 0;
        foreach ($orderBy as $key => $value) {
            if ($i == 0) {
                $query .= " ORDER BY ";
            } else {
                $query .= ", ";
            }

            $query .=$this->db->sql_escape_string($key);
            if ($value == 'DESC') {
                $query .= ' DESC';
            }
            $i++;
        }

        $result =$this->db->sql_query($query);
        $mods = array();
        while ($mod =$this->db->sql_fetch_assoc($result)) {
            $mods[] = $mod;
        }

        return $mods;
    }

    /**
     * Fonction de recherche d'un mod en fonction des filtres définis
     * @param array $filter Tableau associatif ayant pour clé le champ à filtrer, et pour valeur la valeur souhaitée
     * @param array $orderBy tableau associatif sur le champ et l'ordre
     * @return array Liste de mods
     */
    public function find_one_by($filter = array(), $orderBy = array())
    {
        $tMod = $this->find_by($filter,$orderBy);
        if (count($tMod)==0)
        {
            return NULL;
        }
        else
        {
            return $tMod[0];
        }
    }



    /**
     * @param array $mod Tableau associatif représentant un mod
     * @throws \Exception
     */
    public function add(array $mod)
    {
        if ($mod == null || count($mod) != 9) {
            throw new \Exception('Invalid parameter');
        }

        // On vérifie le nombre de valeur de l'explode
        $query = "INSERT INTO " . TABLE_MOD . " (title, menu, action, root, link, version, position, active,admin_only) VALUES (
                    '" .$this->db->sql_escape_string($mod['title']) . "',
                    '" .$this->db->sql_escape_string($mod['menu']) . "',
                    '" .$this->db->sql_escape_string($mod['action']) . "',
                    '" .$this->db->sql_escape_string($mod['root']) . "',
                    '" .$this->db->sql_escape_string($mod['link']) . "',
                    '" .$this->db->sql_escape_string($mod['version']) . "',
                    '" .$this->db->sql_escape_string($mod['position']) . "',
                    '" .$this->db->sql_escape_string($mod['active']) . "',
                    '" .$this->db->sql_escape_string($mod['admin_only']) . "')";

       $this->db->sql_query($query);
    }

    /**
     * Indique la valeur de la position la plus élevée
     * @return integer
     */
    public function get_position_max()
    {
        $query = "select max(position) from " . TABLE_MOD;
        $result =$this->db->sql_query($query);
        list($position) =$this->db->sql_fetch_row($result);

        return $position;
    }

    /**
     * retourne l'id du mod
     * @return integer
     */
    public function get_mod_id_by_root($root)
    {
        $root =$this->db->sql_escape_string($root);

        $request = "select id from " . TABLE_MOD . " where root = '".$root."'";
        $result = $this->db->sql_query($request);
        list($id) = $this->db->sql_fetch_row($result);

        return $id;
    }
    /**
     * Met à jour le mod
     * @param array $mod tableau associatif représentant le mod
     */
    public function update(array $mod)
    {


        $query = "UPDATE " . TABLE_MOD . " SET 
                    `title` = '" .$this->db->sql_escape_string($mod['title']) . "',
                    `menu` = '" .$this->db->sql_escape_string($mod['menu']) . "',
                    `action` = '" .$this->db->sql_escape_string($mod['action']) . "',
                    `root` = '" .$this->db->sql_escape_string($mod['root']) . "',
                    `link` = '" .$this->db->sql_escape_string($mod['link']) . "',
                    `version`= '" .$this->db->sql_escape_string($mod['version']) . "',
                    `position` = '" .$this->db->sql_escape_string($mod['position']) . "',
                    `active` = '" .$this->db->sql_escape_string($mod['active']) . "',
                    `admin_only` = '" .$this->db->sql_escape_string($mod['admin_only']) . "'
                 WHERE `id` = '" .$this->db->sql_escape_string($mod['id']) . "'";
        $this->db->sql_query($query);
    }


    /**
     * Actualise la position du mod
     */
    public function update_posisiton($mod_id,$position)
    {
        $mod_id=(int)$mod_id;
        $position=(int)$position;

        $request = "update " . TABLE_MOD . " set position = " . $position . " where id = '".$mod_id."'";
        $this->db->sql_query($request);
    }


    /**
     * Supprime le mod
     * @param $mod_id id du mod
     */
    public function delete($mod_id)
    {
        $mod_id=(int)$mod_id;

        $request = "delete from " . TABLE_MOD . " where id = '{$this->db->sql_escape_string($mod_id)}'";
        $this->db->sql_query($request);
    }


    /**
     * Supprime le mod
     * @param $mod_uninstall_title title du mod
     */
    public function delete_by_title($mod_uninstall_title)
    {
        $mod_uninstall_title = $this->db->sql_escape_string($mod_uninstall_title);

        $request = "delete from " . TABLE_MOD . " where title = '{$this->db->sql_escape_string($mod_uninstall_title)}'";
        $this->db->sql_query($request);
    }


    /**
     * Supprime table d un mod
     * @param $table_name nom de la table
     */
    public function drop_custum_table($table_name)
    {
        if (is_array($table_name))
        {
            foreach ($table_name as $tablename)
            {
                $table_name=$this->db->sql_escape_string($tablename);

                $request ="DROP TABLE IF EXISTS " . $table_name ;
                $this->db->sql_query($request);
            }

        }
        else
        {
            $table_name=$this->db->sql_escape_string($table_name);

            $request ="DROP TABLE IF EXISTS " . $table_name ;
            $this->db->sql_query($request);

        }


    }



    public function isExistByTitle($title)
    {
        $title = $this->db->sql_escape_string($title);

        $request = "SELECT title FROM " . TABLE_MOD . " WHERE title='" . $title ."'";
        $this->db->sql_query($request);
        $number = $this->db->sql_numrows();
         if ($number > 0)
         {
             return True;
         }
        Return False;

    }

}
