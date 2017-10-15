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



class Group_Model
{
    /**
     * @param $group_id
     * @return \Ogsteam\Ogspy\the
     */
    public function get_group_rights($group_id) {

        global $db;
        $request = "select group_id, group_name, ";
        $request .= " server_set_system, server_set_spy, server_set_rc, server_set_ranking, server_show_positionhided,";
        $request .= " ogs_connection, ogs_set_system, ogs_get_system, ogs_set_spy, ogs_get_spy, ogs_set_ranking, ogs_get_ranking";
        $request .= " from " . TABLE_GROUP;
        $request .= " where group_id = " . $group_id;
        $request .= " order by group_name";

        $result = $db->sql_query($request);

        while ($row = $db->sql_fetch_assoc($result)) {
            $info_usergroup = $row;
        }
        return $info_usergroup;

    }

    /**
     * @return array
     */
    public function get_group_list() {
        global $db;
        $request = "select group_id, group_name ";
        $request .= " from " . TABLE_GROUP;
        $request .= " order by group_name";

        $result = $db->sql_query($request);

        $info_usergroup = array();
        while ($row = $db->sql_fetch_assoc($result)) {
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
        global $db;
        $usergroup_member = array();

        $request = "SELECT u.user_id, u.user_name FROM " . TABLE_USER . " AS  u, " .
            TABLE_USER_GROUP . " AS g";
        $request .= " where u.user_id = g.user_id";
        $request .= " and g.group_id = " . $group_id;
        $request .= " order by user_name";
        $result = $db->sql_query($request);
        while ($row = $db->sql_fetch_assoc($result)) {
            $usergroup_member[] = $row;
        }
        return $usergroup_member;
    }

    /**
     * @param $name
     * @return bool|\Ogsteam\Ogspy\Returs
     */
    public function insert_group($name)
    {
        global $db;

        $name = $db->sql_escape_string($name);

        $request = "SELECT group_id FROM " . TABLE_GROUP . " WHERE group_name = '" . $name . "'";
        $result = $db->sql_query($request);

        if ($db->sql_numrows($result) == 0) {
            $request = "INSERT INTO " . TABLE_GROUP . " (group_name)" . " VALUES ('" . $name . "')";
            $db->sql_query($request);
            $group_id = $db->sql_insertid();
            return $group_id;
        } else {
                    return false;
        }
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
        global $db;

        $request = "SELECT $user_id FROM " . TABLE_USER_GROUP . " WHERE group_id = '" . $group_id . "'";
        $result = $db->sql_query($request);

        if ($db->sql_numrows($result) == 0) {
            $request = "INSERT INTO " . TABLE_USER_GROUP . " (group_id, user_id)" . " VALUES (" . intval($group_id) . ", " . intval($user_id) . ")";
            $db->sql_query($request);
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
    public function update_group($group_id, $name, $server_set_system = 0, $server_set_spy = 0,
                                    $server_set_rc = 0, $server_set_ranking = 0, $server_show_positionhided = 0,
                                    $ogs_connection = 0, $ogs_set_system = 0, $ogs_get_system = 0, $ogs_set_spy = 0,
                                    $ogs_get_spy = 0, $ogs_set_ranking = 0, $ogs_get_ranking = 0)
    {
        global $db;

        $name = $db->sql_escape_string($name);

        $request = "UPDATE " . TABLE_GROUP;
        $request .= " SET group_name = '" . $name .
            "',";
        $request .= " server_set_system = '" . $server_set_system .
            "', server_set_spy = '" . $server_set_spy .
            "', server_set_rc = '" . $server_set_rc .
            "', server_set_ranking = '" . $server_set_ranking .
            "', server_show_positionhided = '" . $server_show_positionhided .
            "', ogs_connection = '" . $ogs_connection .
            "', ogs_set_system = '" . $ogs_set_system .
            "', ogs_get_system = '" . $ogs_get_system .
            "', ogs_set_spy = '" . $ogs_set_spy .
            "', ogs_get_spy = '" . $ogs_get_spy .
            "', ogs_set_ranking = '" . $ogs_set_ranking .
            "', ogs_get_ranking = '" . $ogs_get_ranking .
            "'";
        $request .= " WHERE group_id = " . $group_id;
        $db->sql_query($request);
    }

    /**
     * @param $group_id
     * @param $user_id
     * @return bool
     */
    public function delete_user_from_group($user_id, $group_id)
    {
        global $db;
        $request = "DELETE FROM " . TABLE_USER_GROUP . " WHERE group_id = " . intval($group_id) .
            " and user_id = " . intval($user_id);
        $db->sql_query($request);

        if ($db->sql_affectedrows() > 0) {
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
        global $db;
        $request = "DELETE FROM " . TABLE_USER_GROUP . " WHERE group_id = " . $group_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_GROUP . " WHERE group_id = " . $group_id;
        $db->sql_query($request);

    }

}
