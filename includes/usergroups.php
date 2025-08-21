<?php

use Ogsteam\Ogspy\Model\Group_Model;
use Ogsteam\Ogspy\Model\User_Model;

/**
 * Création d'un groupe
 */
function usergroup_create()
{
    global $pub_groupname, $log, $user_data;
    $Group_Model = new Group_Model();

    if (!isset($pub_groupname)) {
        $log->warning("Group creation attempt without group name", [
            'type' => 'usergroup_create_attempt',
            'reason' => 'missing_group_name',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } else {
        $log->info("Attempt to create a new user group", [
            'group_name' => $pub_groupname,
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }

    if (!isset($pub_groupname) || trim($pub_groupname) === '') {
        $log->error("Group creation failed - Group name not provided", [
            'type' => 'usergroup_create_failed',
            'reason' => 'missing_group_name',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=createusergroup_failed_general&info");
    }

    //Vérification des droits
    try {
        user_check_auth("usergroup_manage");
        $log->info("Authorization checked for group creation", [
            'type' => 'usergroup_create_authorized',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_name' => $pub_groupname
        ]);
    } catch (Exception $e) {
        $log->error("Group creation failed - Authorization denied", [
            'type' => 'usergroup_create_failed',
            'reason' => 'authorization_denied',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_name' => $pub_groupname,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    if (!check_var($pub_groupname, "Pseudo_Groupname")) {
        $log->warning("Group creation failed - Invalid group name", [
            'type' => 'usergroup_create_failed',
            'reason' => 'invalid_group_name',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_name' => $pub_groupname,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=createusergroup_failed_groupname&info");
    }


    if (!$Group_Model->group_exist_by_name($pub_groupname)) {
        $Group_Model->insert_group($pub_groupname);
        $group_id = $Group_Model->sql_insertid();

        $log->info("Group created successfully", [
            'type' => 'usergroup_create_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id,
            'group_name' => $pub_groupname,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=administration&subaction=group&group_id=" . $group_id);
    } else {
        $log->warning("Group creation failed - Group name already exists", [
            'type' => 'usergroup_create_failed',
            'reason' => 'group_name_exists',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_name' => $pub_groupname,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=createusergroup_failed_groupnamelocked&info=" .
            $pub_groupname);
    }
}

/**
 * Récupération des droits d'un groupe d'utilisateurs
 * @param bool $group_id
 * @return array|bool
 * @throws Exception
 */
function usergroup_get($group_id = false)
{
    global $log, $user_data;

    $log->info("Retrieving user group rights", [
        'type' => 'usergroup_get_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $group_id !== false ? $group_id : 'all_groups',
        'request_all_groups' => $group_id === false,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    //Vérification des droits
    try {
        user_check_auth("usergroup_manage");

        $log->info("Authorization checked for retrieving group rights", [
            'type' => 'usergroup_get_authorized',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups'
        ]);
    } catch (Exception $e) {
        $log->error("Failed to retrieve group rights - Authorization denied", [
            'type' => 'usergroup_get_failed',
            'reason' => 'authorization_denied',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups',
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    if (intval($group_id) == 0 && $group_id !== false) {
        $log->warning("Failed to retrieve group rights - Invalid group ID", [
            'type' => 'usergroup_get_failed',
            'reason' => 'invalid_group_id',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'provided_id' => $group_id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        return false;
    }

    try {
        $Group_Model = new Group_Model();

        //demande de tous les groupes
        if (!$group_id) {
            $log->debug("Retrieving all user groups", [
                'type' => 'usergroup_get_all',
                'admin_user_id' => $user_data['id'] ?? 'unknown'
            ]);
            $info_usergroup = $Group_Model->get_all_group_rights();
        } else {
            $log->debug("Retrieving a specific group", [
                'type' => 'usergroup_get_specific',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'group_id' => $group_id
            ]);
            $info_usergroup = $Group_Model->get_group_rights($group_id);
        }

        if (sizeof($info_usergroup) == 0) {
            $log->warning("No group found", [
                'type' => 'usergroup_get_empty_result',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'group_id' => $group_id !== false ? $group_id : 'all_groups',
                'requested_all' => $group_id === false
            ]);
            return false;
        }

        $groups_count = sizeof($info_usergroup);
        $group_names = [];

        // Extraction des noms de groupes pour les logs
        foreach ($info_usergroup as $group_info) {
            if (isset($group_info['group_name'])) {
                $group_names[] = $group_info['group_name'];
            }
        }

        $log->info("Group rights successfully retrieved", [
            'type' => 'usergroup_get_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups',
            'groups_count' => $groups_count,
            'group_names' => $group_names,
            'requested_all' => $group_id === false,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $info_usergroup;
    } catch (Exception $e) {
        $log->error("Error while retrieving group rights", [
            'type' => 'usergroup_get_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id !== false ? $group_id : 'all_groups',
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }
}

/**
 * Enregistrement des droits d'un groupe utilisateurs
 */
function usergroup_setauth()
{
    global $pub_group_id, $pub_group_name, $pub_server_set_system, $pub_server_set_spy,
           $pub_server_set_rc, $pub_server_set_ranking, $pub_server_show_positionhided, $pub_ogs_connection,
           $pub_ogs_set_system, $pub_ogs_get_system, $pub_ogs_set_spy, $pub_ogs_get_spy, $pub_ogs_set_ranking,
           $pub_ogs_get_ranking, $log, $user_data;

    $log->info("Starting group rights modification", [
        'type' => 'usergroup_setauth_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $pub_group_id ?? 'undefined',
        'group_name' => $pub_group_name ?? 'undefined',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!check_var($pub_group_id, "Num") || !check_var(
            $pub_group_name,
            "Pseudo_Groupname"
        ) || !check_var($pub_server_set_system, "Num") || !check_var(
            $pub_server_set_spy,
            "Num"
        ) || !check_var($pub_server_set_rc, "Num") || !check_var(
            $pub_server_set_ranking,
            "Num"
        ) || !check_var($pub_server_show_positionhided, "Num") || !check_var(
            $pub_ogs_connection,
            "Num"
        ) || !check_var($pub_ogs_set_system, "Num") || !check_var(
            $pub_ogs_get_system,
            "Num"
        ) || !check_var($pub_ogs_set_spy, "Num") || !check_var(
            $pub_ogs_get_spy,
            "Num"
        ) || !check_var($pub_ogs_set_ranking, "Num") || !check_var(
            $pub_ogs_get_ranking,
            "Num"
        )) {
        $log->warning("Group rights modification failed - Invalid data format", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'invalid_data_format',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id ?? 'undefined',
            'group_name' => $pub_group_name ?? 'undefined',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_group_id) || !isset($pub_group_name)) {
        $log->error("Group rights modification failed - Missing data", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'missing_required_data',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id_set' => isset($pub_group_id),
            'group_name_set' => isset($pub_group_name),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    $pub_server_set_system = $pub_server_set_system ?? 0;
    $pub_server_set_spy = $pub_server_set_spy ?? 0;
    $pub_server_set_rc = $pub_server_set_rc ?? 0;
    $pub_server_set_ranking = $pub_server_set_ranking ?? 0;
    $pub_server_show_positionhided = $pub_server_show_positionhided ?? 0;
    $pub_ogs_connection = $pub_ogs_connection ?? 0;
    $pub_ogs_set_system = $pub_ogs_set_system ?? 0;
    $pub_ogs_get_system = $pub_ogs_get_system ?? 0;
    $pub_ogs_set_spy = $pub_ogs_set_spy ?? 0;
    $pub_ogs_get_spy = $pub_ogs_get_spy ?? 0;
    $pub_ogs_set_ranking = $pub_ogs_set_ranking ?? 0;
    $pub_ogs_get_ranking = $pub_ogs_get_ranking ?? 0;

    //Vérification des droits
    try {
        user_check_auth("usergroup_manage");

        $log->info("Authorization checked for group rights modification", [
            'type' => 'usergroup_setauth_authorized',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $pub_group_name
        ]);
    } catch (Exception $e) {
        $log->error("Group rights modification failed - Authorization denied", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'authorization_denied',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    // Log des droits qui vont être mis à jour
    $permissions_summary = [
        'server_permissions' => [
            'set_system' => $pub_server_set_system,
            'set_spy' => $pub_server_set_spy,
            'set_rc' => $pub_server_set_rc,
            'set_ranking' => $pub_server_set_ranking,
            'show_positionhided' => $pub_server_show_positionhided
        ],
        'ogs_permissions' => [
            'connection' => $pub_ogs_connection,
            'set_system' => $pub_ogs_set_system,
            'get_system' => $pub_ogs_get_system,
            'set_spy' => $pub_ogs_set_spy,
            'get_spy' => $pub_ogs_get_spy,
            'set_ranking' => $pub_ogs_set_ranking,
            'get_ranking' => $pub_ogs_get_ranking
        ]
    ];

    $log->debug("Rights to be applied to the group", [
        'type' => 'usergroup_setauth_permissions',
        'group_id' => $pub_group_id,
        'group_name' => $pub_group_name,
        'permissions' => $permissions_summary
    ]);

    try {
        (new Group_Model())->update_group(
            $pub_group_id,
            $pub_group_name,
            $pub_server_set_system,
            $pub_server_set_spy,
            $pub_server_set_rc,
            $pub_server_set_ranking,
            $pub_server_show_positionhided,
            $pub_ogs_connection,
            $pub_ogs_set_system,
            $pub_ogs_get_system,
            $pub_ogs_set_spy,
            $pub_ogs_get_spy,
            $pub_ogs_set_ranking,
            $pub_ogs_get_ranking
        );

        $log->info("Group rights updated successfully", [
            'type' => 'usergroup_setauth_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $pub_group_name,
            'permissions_updated' => $permissions_summary,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        $log->error("Error while updating group rights", [
            'type' => 'usergroup_setauth_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $pub_group_name,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
}

/**
 * Récupération des utilisateurs appartenant à un groupe
 * @param int $group_id Identificateur du groupe demandé
 * @return Array Liste des utilisateurs
 * @throws Exception
 */
function usergroup_member($group_id)
{
    global $log, $user_data;

    $log->info("Retrieving group members", [
        'type' => 'usergroup_member_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $group_id ?? 'undefined',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!isset($group_id) || !is_numeric($group_id)) {
        $log->error("Failed to retrieve group members - Invalid group ID", [
            'type' => 'usergroup_member_failed',
            'reason' => 'invalid_group_id',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'provided_id' => $group_id ?? 'null',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    try {
        $usergroup_member = (new Group_Model())->get_user_list($group_id);

        $members_count = count($usergroup_member);

        $log->info("Group members successfully retrieved", [
            'type' => 'usergroup_member_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id,
            'members_count' => $members_count,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        return $usergroup_member;
    } catch (Exception $e) {
        $log->error("Error while retrieving group members", [
            'type' => 'usergroup_member_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $group_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }
}

/**
 * Ajout d'un utilisateur à un groupe
 */
function usergroup_newmember()
{
    global $pub_user_id, $pub_group_id, $pub_add_all, $log, $user_data;

    $log->info("Attempting to add user(s) to a group", [
        'type' => 'usergroup_newmember_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'target_user_id' => $pub_user_id ?? 'undefined',
        'group_id' => $pub_group_id ?? 'undefined',
        'add_all_users' => isset($pub_add_all),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    $Group_Model = new Group_Model();
    $userModel = new User_Model();

    try {
        $userid_list = $userModel->select_userid_list();
        $total_users_available = count($userid_list);

        $log->debug("Retrieved list of available users", [
            'type' => 'usergroup_newmember_users_list',
            'total_users' => $total_users_available
        ]);
    } catch (Exception $e) {
        $log->error("Error while retrieving user list", [
            'type' => 'usergroup_newmember_failed',
            'reason' => 'users_list_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errorfatal&info");
    }

    // Ajout de tous les utilisateurs au groupe
    if (isset($pub_add_all) && is_numeric($pub_group_id)) {
        $log->info("Adding all users to the group", [
            'type' => 'usergroup_newmember_add_all',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'users_to_add' => $total_users_available
        ]);

        $success_count = 0;
        $error_count = 0;

        foreach ($userid_list as $userid) {
            try {
                user_check_auth("usergroup_manage");

                if ($Group_Model->insert_user_togroup($userid, $pub_group_id)) {
                    $success_count++;
                    $log->debug("User added to group", [
                        'type' => 'usergroup_newmember_user_added',
                        'user_id' => $userid,
                        'group_id' => $pub_group_id
                    ]);
                } else {
                    $error_count++;
                    $log->warning("Failed to add user to group", [
                        'type' => 'usergroup_newmember_user_failed',
                        'user_id' => $userid,
                        'group_id' => $pub_group_id,
                        'reason' => 'insert_failed'
                    ]);
                }
            } catch (Exception $e) {
                $error_count++;
                $log->error("Error while adding user to group", [
                    'type' => 'usergroup_newmember_user_error',
                    'user_id' => $userid,
                    'group_id' => $pub_group_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $log->info("Bulk add completed", [
            'type' => 'usergroup_newmember_bulk_completed',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'total_users' => $total_users_available,
            'success_count' => $success_count,
            'error_count' => $error_count,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
    } else {
        // Ajout d'un utilisateur spécifique
        if (!check_var($pub_user_id, "Num") || !check_var($pub_group_id, "Num")) {
            $log->warning("Failed to add user to group - Invalid data format", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'invalid_data_format',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'user_id' => $pub_user_id ?? 'undefined',
                'group_id' => $pub_group_id ?? 'undefined',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=errordata&info");
        }

        if (!isset($pub_user_id) || !isset($pub_group_id)) {
            $log->error("Failed to add user to group - Missing data", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'missing_required_data',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'user_id_set' => isset($pub_user_id),
                'group_id_set' => isset($pub_group_id),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=message&id_message=errorfatal&info");
        }

        //Vérification des droits
        try {
            user_check_auth("usergroup_manage");

            $log->debug("Authorization checked for adding user to group", [
                'type' => 'usergroup_newmember_authorized',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id
            ]);
        } catch (Exception $e) {
            $log->error("Failed to add user to group - Authorization denied", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'authorization_denied',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id,
                'error' => $e->getMessage(),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            throw $e;
        }

        // Vérification de l'existence du groupe
        if ($Group_Model->group_exist_by_id($pub_group_id) == false) {
            $log->warning("Failed to add user - Group not found", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'group_not_found',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=administration&subaction=group");
        }

        // Vérification de l'existence de l'utilisateur
        if (!in_array(intval($pub_user_id), $userid_list)) {
            $log->warning("Failed to add user - User not found", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'user_not_found',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'group_id' => $pub_group_id,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            redirection("index.php?action=administration&subaction=group");
        }

        // Récupération des informations pour les logs
        try {
            $user_info = user_get($pub_user_id);
            $username = $user_info[0]['user_pseudo'] ?? 'unknown';

            $group_info = $Group_Model->get_group_rights($pub_group_id);
            $group_name = $group_info[0]['group_name'] ?? 'unknown';
        } catch (Exception $e) {
            $username = 'unknown';
            $group_name = 'unknown';
            $log->debug("Could not retrieve names for logging", [
                'type' => 'usergroup_newmember_info_warning',
                'error' => $e->getMessage()
            ]);
        }

        // Insertion de l'utilisateur dans le groupe
        try {
            if ($Group_Model->insert_user_togroup($pub_user_id, $pub_group_id)) {
                $log->info("User successfully added to group", [
                    'type' => 'usergroup_newmember_success',
                    'admin_user_id' => $user_data['id'] ?? 'unknown',
                    'target_user_id' => $pub_user_id,
                    'target_username' => $username,
                    'group_id' => $pub_group_id,
                    'group_name' => $group_name,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            } else {
                $log->warning("Failed to add user to group", [
                    'type' => 'usergroup_newmember_failed',
                    'reason' => 'insert_failed',
                    'admin_user_id' => $user_data['id'] ?? 'unknown',
                    'target_user_id' => $pub_user_id,
                    'target_username' => $username,
                    'group_id' => $pub_group_id,
                    'group_name' => $group_name,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            }
        } catch (Exception $e) {
            $log->error("Error while adding user to group", [
                'type' => 'usergroup_newmember_failed',
                'reason' => 'database_error',
                'admin_user_id' => $user_data['id'] ?? 'unknown',
                'target_user_id' => $pub_user_id,
                'target_username' => $username,
                'group_id' => $pub_group_id,
                'group_name' => $group_name,
                'error' => $e->getMessage(),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            throw $e;
        }

        redirection("index.php?action=administration&subaction=group&group_id=" . $pub_group_id);
    }
}


/**
 * Suppression d'un groupe utilisateur
 */
function usergroup_delete()
{
    global $pub_group_id, $log, $user_data;

    $log->info("Attempting to delete user group", [
        'type' => 'usergroup_delete_attempt',
        'admin_user_id' => $user_data['id'] ?? 'unknown',
        'group_id' => $pub_group_id ?? 'undefined',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);

    if (!check_var($pub_group_id, "Num")) {
        $log->warning("Group deletion failed - Invalid group ID", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'invalid_group_id',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'provided_id' => $pub_group_id ?? 'null',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=errordata&info");
    }

    if (!isset($pub_group_id)) {
        $log->error("Group deletion failed - Group ID not set", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'group_id_not_set',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=message&id_message=createusergroup_failed_general&info");
    }

    //Vérification des droits
    try {
        user_check_auth("usergroup_manage");

        $log->info("Authorization checked for group deletion", [
            'type' => 'usergroup_delete_authorized',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id
        ]);
    } catch (Exception $e) {
        $log->error("Group deletion failed - Authorization denied", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'authorization_denied',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    // Protection contre la suppression du groupe par défaut
    if ($pub_group_id == 1) {
        $log->warning("Attempt to delete default group", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'default_group_protection',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        redirection("index.php?action=administration&subaction=group&group_id=1");
    }

    // Récupération des informations du groupe avant suppression pour les logs
    try {
        $Group_Model = new Group_Model();
        $group_info = $Group_Model->get_group_rights($pub_group_id);
        $group_name = $group_info[0]['group_name'] ?? 'unknown';
        $group_members_count = count($Group_Model->get_user_list($pub_group_id));

        $log->debug("Information of the group to be deleted", [
            'type' => 'usergroup_delete_info',
            'group_id' => $pub_group_id,
            'group_name' => $group_name,
            'members_count' => $group_members_count
        ]);
    } catch (Exception $e) {
        $group_name = 'unknown';
        $group_members_count = 0;
        $log->warning("Could not retrieve group info before deletion", [
            'type' => 'usergroup_delete_warning',
            'group_id' => $pub_group_id,
            'error' => $e->getMessage()
        ]);
    }

    try {
        (new Group_Model())->delete_group($pub_group_id);

        $log->info("User group deleted successfully", [
            'type' => 'usergroup_deleted_success',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $group_name,
            'former_members_count' => $group_members_count,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (Exception $e) {
        $log->error("Error while deleting group", [
            'type' => 'usergroup_delete_failed',
            'reason' => 'database_error',
            'admin_user_id' => $user_data['id'] ?? 'unknown',
            'group_id' => $pub_group_id,
            'group_name' => $group_name,
            'error' => $e->getMessage(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        throw $e;
    }

    redirection("index.php?action=administration&subaction=group");
}
