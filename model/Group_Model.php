<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Group_Model extends Model_Abstract
{
    /*
     * @return array $info_usergroup
     */
    public function get_all_group_rights()
    {

        $request = "SELECT `id`, `name`, ";
        $request .= " `server_set_system`, `server_set_spy`, `server_set_rc`, `server_set_ranking`, `server_show_positionhided`,";
        $request .= " `ogs_connection`, `ogs_set_system`, `ogs_get_system`, `ogs_set_spy` , `ogs_get_spy`, `ogs_set_ranking`, `ogs_get_ranking`";
        $request .= " FROM " . TABLE_GROUP;
        $request .= " ORDER BY `id`";

        $result = $this->db->sql_query($request);

        $info_usergroup = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $info_usergroup[] = $row;
        }
        return $info_usergroup;
    }
    /**
     * @param $group_id
     * @return array $info_usergroup
     */
    public function get_group_rights($group_id)
    {

        $group_id = intval($group_id);
        $info_usergroup = null; // Initialisation de la variable

        $request = "SELECT `id`, `name`, ";
        $request .= " `server_set_system`, `server_set_spy`, `server_set_rc`, `server_set_ranking`, `server_show_positionhided`,";
        $request .= " `ogs_connection`, `ogs_set_system`, `ogs_get_system`, `ogs_set_spy`, `ogs_get_spy`, `ogs_set_ranking`, `ogs_get_ranking`";
        $request .= " FROM " . TABLE_GROUP;
        $request .= " WHERE `id` = " . $group_id;
        $request .= " ORDER BY `id`";

        $result = $this->db->sql_query($request);

        while ($row = $this->db->sql_fetch_assoc($result)) {
            $info_usergroup = $row;
        }
        return $info_usergroup;
    }

    /**
     * @return array
     */
    public function get_group_list()
    {
        $request = "SELECT `id`, `name` ";
        $request .= " FROM " . TABLE_GROUP;
        $request .= " ORDER BY `id`";

        $result = $this->db->sql_query($request);

        $info_usergroup = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $info_usergroup[] = $row;
        }
        return $info_usergroup;
    }

    /**
     * @param $group_id
     * @return array
     */
    public function get_user_list($group_id)
    {
        $group_id = intval($group_id);

        $usergroup_member = array();

        $request = "SELECT u.`id`, u.`name` FROM " . TABLE_USER . " AS  u, " .
            TABLE_USER_GROUP . " AS g";
        $request .= " WHERE u.`id` = g.`user_id`";
        $request .= " AND g.`group_id` = " . $group_id;
        $request .= " ORDER BY u.`name`";
        $result = $this->db->sql_query($request);
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $usergroup_member[] = $row;
        }
        return $usergroup_member;
    }

    /**
     * @param $group_id
     * @return array
     */
    public function get_user_group($user_id)
    {

        $user_id = intval($user_id);

        $request = "SELECT `group_id` FROM  " . TABLE_USER_GROUP . " ";
        $request .= " WHERE `user_id` = " . $user_id;
        $result = $this->db->sql_query($request);
        $user_group = $this->db->sql_fetch_assoc($result);
        if (isset($user_group["group_id"])) {
            return $user_group["group_id"];
        }
        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function insert_group($name)
    {

        $name = $this->db->sql_escape_string($name);

        $request = "SELECT `id` FROM " . TABLE_GROUP . " WHERE `name` = '" . $name . "'";
        $result = $this->db->sql_query($request);

        if ($this->db->sql_numrows($result) == 0) {
            $request = "INSERT INTO " . TABLE_GROUP . " (`name`)" . " VALUES ('" . $name . "')";
            $this->db->sql_query($request);
            $group_id = $this->db->sql_insertid();
            return $group_id;
        }
        return false;
    }

    /**
     * Adding an user into the group
     * @param $user_id
     * @param $group_id
     * @return bool
     * @internal param $name
     */
    public function insert_user_togroup($user_id, $group_id)
    {
        $user_id =  intval($user_id);
        $group_id = intval($group_id);

        $request = "SELECT `user_id` FROM " . TABLE_USER_GROUP . " WHERE `group_id` = '" . $group_id . "' AND `user_id` = '" . $user_id . "'";
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) == 0) {
            $request = "INSERT INTO " . TABLE_USER_GROUP . " (`group_id`, `user_id`)" . " VALUES (" . intval($group_id) . ", " . intval($user_id) . ")";
            $this->db->sql_query($request);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $group_id
     * @param string $name
     * @param int $server_set_system
     * @param int $server_set_spy
     * @param int $server_set_rc
     * @param int $server_set_ranking
     * @param int $server_show_positionhided
     * @param int $ogs_connection
     * @param int $ogs_set_system
     * @param int $ogs_get_system
     * @param int $ogs_set_spy
     * @param int $ogs_get_spy
     * @param int $ogs_set_ranking
     * @param int $ogs_get_ranking
     */
    public function update_group(
        $group_id,
        $name,
        $server_set_system = 0,
        $server_set_spy = 0,
        $server_set_rc = 0,
        $server_set_ranking = 0,
        $server_show_positionhided = 0,
        $ogs_connection = 0,
        $ogs_set_system = 0,
        $ogs_get_system = 0,
        $ogs_set_spy = 0,
        $ogs_get_spy = 0,
        $ogs_set_ranking = 0,
        $ogs_get_ranking = 0
    ) {

        //control variable
        $name = $this->db->sql_escape_string($name);
        $group_id = intval($group_id);
        $server_set_system = intval($server_set_system);
        $server_set_spy = intval($server_set_spy);
        $server_set_rc = intval($server_set_rc);
        $server_set_ranking = intval($server_set_ranking);
        $server_show_positionhided = intval($server_show_positionhided);
        $ogs_connection = intval($ogs_connection);
        $ogs_set_system = intval($ogs_set_system);
        $ogs_get_system = intval($ogs_get_system);
        $ogs_set_spy = intval($ogs_set_spy);
        $ogs_get_spy = intval($ogs_get_spy);
        $ogs_set_ranking = intval($ogs_set_ranking);
        $ogs_get_ranking = intval($ogs_get_ranking);



        $request = "UPDATE " . TABLE_GROUP . " SET
            `name` = '$name',
            `server_set_system` = '$server_set_system',
            `server_set_spy` = '$server_set_spy',
            `server_set_rc` = '$server_set_rc',
            `server_set_ranking` = '$server_set_ranking',
            `server_show_positionhided` = '$server_show_positionhided',
            `ogs_connection` = '$ogs_connection',
            `ogs_set_system` = '$ogs_set_system',
            `ogs_get_system` = '$ogs_get_system',
            `ogs_set_spy` = '$ogs_set_spy',
            `ogs_get_spy` = '$ogs_get_spy',
            `ogs_set_ranking` = '$ogs_set_ranking',
            `ogs_get_ranking` = '$ogs_get_ranking'
        WHERE `id` = $group_id";
        $this->db->sql_query($request);
    }

    /**
     * suppression de l'utlisateur
     * @param $group_id
     * @return bool
     */
    public function delete_user($user_id)
    {
        $user_id = (int)$user_id;

        $request = "DELETE FROM " . TABLE_USER_GROUP . " WHERE `user_id` = " . intval($user_id);
        $this->db->sql_query($request);
        if ($this->db->sql_affectedrows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $group_id
     * @param $user_id
     * @return bool
     */
    public function delete_user_from_group($user_id, $group_id)
    {
        $user_id = (int)$user_id;
        $group_id = (int)$group_id;


        $request = "DELETE FROM " . TABLE_USER_GROUP . " WHERE `group_id` = " . intval($group_id) .
            " AND `user_id` = " . intval($user_id);
        $this->db->sql_query($request);

        if ($this->db->sql_affectedrows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param $group_id
     */
    public function delete_group($group_id)
    {
        $group_id = intval($group_id);

        $request = "DELETE FROM " . TABLE_USER_GROUP . " WHERE `group_id` = " . $group_id;
        $this->db->sql_query($request);

        $request = "DELETE FROM " . TABLE_GROUP . " WHERE `id` = " . $group_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $group_id
     */
    public function  group_exist_by_id($group_id)
    {
        $group_id = intval($group_id);

        $request = "SELECT `id` FROM " . TABLE_GROUP . " WHERE `id` = " . $group_id;
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) == 0) {
            return false;
        }
        return true;
    }

    /**
     * @param $group_name
     */
    public function  group_exist_by_name($group_name)
    {
        $group_name =  $this->db->sql_escape_string($group_name);

        $request = "SELECT `id` FROM " . TABLE_GROUP . " WHERE `name` = '" . $group_name . "'";
        $result = $this->db->sql_query($request);

        if ($this->db->sql_numrows($result) == 0) {
            return false;
        }
        return true;
    }
}
