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


class Mod_Model
{
    /**
     * Fonction de recherche d'un mod en fonction des filtres définis
     * @param array $filter Tableau associatif ayant pour clé le champ à filtrer, et pour valeur la valeur souhaitée
     * @param array $orderBy tableau associatif sur le champ et l'ordre
     * @return array Liste de mods
     */
    public function find_by($filter = array(), $orderBy = array())
    {
        global $db;

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

            $query .= "`" . $db->sql_escape_string($key) . "` = '" . $db->sql_escape_string($value) . "'";
            $i++;
        }

        $i = 0;
        foreach ($orderBy as $key => $value) {
            if ($i == 0) {
                            $query .= " ORDER BY ";
            } else {
                            $query .= ", ";
            }

            $query .= $db->sql_escape_string($key);
            if ($value == 'DESC') {
                            $query .= ' DESC';
            }
            $i++;
        }

        $result = $db->sql_query($query);
        $mods = array();
        while ($mod = $db->sql_fetch_assoc($result)) {
                    $mods[] = $mod;
        }

        return $mods;
    }

    /**
     * @param array $mod Tableau associatif représentant un mod
     */
    public function add(array $mod)
    {
        global $db;
        if ($mod == null || count($mod) != 9)
            throw new \Exception('Invalid parameter');

        // On vérifie le nombre de valeur de l'explode
        $query = "INSERT INTO " . TABLE_MOD . " (title, menu, action, root, link, version, position, active,admin_only) VALUES (
                    '" . $db->sql_escape_string($mod['title']) . "',
                    '" . $db->sql_escape_string($mod['menu']) . "',
                    '" . $db->sql_escape_string($mod['action']) . "',
                    '" . $db->sql_escape_string($mod['root']) . "',
                    '" . $db->sql_escape_string($mod['link']) . "',
                    '" . $db->sql_escape_string($mod['version']) . "',
                    '" . $db->sql_escape_string($mod['position']) . "',
                    '" . $db->sql_escape_string($mod['active']) . "',
                    '" . $db->sql_escape_string($mod['admin_only']) . "')";
        $db->sql_query($query);
    }

    /**
     * Indique la valeur de la position la plus élevée
     * @return integer
     */
    public function get_position_max()
    {
        global $db;

        $query = "select max(position) from " . TABLE_MOD;
        $result = $db->sql_query($query);
        list($position) = $db->sql_fetch_row($result);

        return $position;
    }

    /**
     * Met à jour le mod
     * @param array $mod tableau associatif représentant le mod
     */
    public function update(array $mod)
    {
        global $db;

        $query = "UPDATE " . TABLE_MOD . " SET 
                    `title` = '" . $db->sql_escape_string($mod['title']) . "',
                    `menu` = '" . $db->sql_escape_string($mod['menu']) . "',
                    `action` = '" . $db->sql_escape_string($mod['action']) . "',
                    `root` = '" . $db->sql_escape_string($mod['root']) . "',
                    `link` = '" . $db->sql_escape_string($mod['link']) . "',
                    `version`= '" . $db->sql_escape_string($mod['version']) . "',
                    `position` = '" . $db->sql_escape_string($mod['position']) . "',
                    `active` = '" . $db->sql_escape_string($mod['active']) . "',
                    `admin_only` = '" . $db->sql_escape_string($mod['admin_only']) . "'
                 WHERE `id` = '" . $db->sql_escape_string($mod['id']) . "'";
        $db->sql_query($query);
    }

    /**
     * Supprime le mod
     * @param $mod_id id du mod
     */
    public function delete($mod_id)
    {
        global $db;

        $request = "delete from " . TABLE_MOD . " where id = '{$db->sql_escape_string($mod_id)}'";
        $db->sql_query($request);
    }
}