<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 15/09/2016
 * Time: 20:23
 */

namespace Ogsteam\Ogspy\Model;


class Group_Model
{

    public function get_group_rights($group_id){

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

    public function get_group_list(){
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
        } else
            return false;
    }

    public function update_group()
    {
        global $db;

    }

    public function delete_group($group_id)
    {
        global $db;
        $request = "DELETE FROM " . TABLE_USER_GROUP . " WHERE group_id = " . $group_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_GROUP . " WHERE group_id = " . $group_id;
        $db->sql_query($request);

    }

}
