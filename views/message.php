<?php

/**
 * Pop up Messages
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
if (!isset($pub_id_message) || !isset($pub_info)) {
    redirection("index.php");
}

if (!check_var($pub_id_message, "Char") || !check_var($pub_info, "Special", "#^[\sa-zA-Z0-9~¤_.\-\:\[\]]+$#")) {
    redirection("index.php");
}

$action = "";
$message = $lang['MSG_SYSTEM'] . '<br/><br/>';

switch ($pub_id_message) {
    case "forbidden":
        $message .= '<span style="color: red;">' . $lang['MSG_FORBIDDEN'] . '</span><br/>';
        break;
    case "errorfatal":
        $message .= '<span style="color: red;">' . $lang['MSG_ERRORFATAL'] . '</span><br/>';
        break;

    case "errormod":
        $message .= '<span style="color: red;">' . $lang['MSG_ERRORMOD'] . '</span><br/>';
        break;
    case "errordata":
        $message .= '<span style="color: red;">' . $lang['MSG_ERRORDATA'] . '</span><br/>';
        break;

    case "createuser_success":
        list($user_id, $password) = explode(':', $pub_info);
        $user_info = user_get($user_id);
        $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
        $server_name = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL);
        $message .= '<span style="color: lime;">' . $lang['MSG_CREATE_USER_TITLE'] . '<a>' . $user_info[0]['user_name'] . '</a></span><br/>';
        $message .= $lang['MSG_CREATE_USER_INFO'] . '<br/><br/>';
        $message .= '- ' . $lang['MSG_CREATE_USER_URL'] . ' :<br/><a>https://' . $server_name . $phpSelf . '</a><br/><br/>';
        $message .= '- ' . $lang['MSG_CREATE_USER_PASSWORD'] . ' :<br/><a>' . $password . '</a><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "regeneratepwd_success":
        list($user_id, $password) = explode(':', $pub_info);
        $user_info = user_get($user_id);
        $message .= '<span style="color: lime;">' . $lang['MSG_PWD_REGEN_OK'] . '<a>' . $user_info[0]['user_name'] . '</a></span><br/>';
        if ($password == "mail") {
            $message .= $lang['MSG_PWD_REGEN_INFO_MAIL'];
        } else {
            $message .= $lang['MSG_PWD_REGEN_INFO'] . ' : <a>' . $password . '</a>';
        }
        $action = 'action=administration&amp;subaction=member';
        break;

    case "regeneratepwd_failed":
        $message .= '<span style="color: red;">' . $lang['MSG_PWD_REGEN_KO'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "createuser_failed_pseudolocked":
        $message .= '<span style="color: red;">' . $lang['MSG_NEW_ACCOUNT_KO'] . '</span><br/>';
        $message .= '<span style="font-style: italic;">' . $lang['MSG_NEW_ACCOUNT_KO_NAME'] . ' (' . $pub_info . ')</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "createuser_failed_pseudo":
        $message .= '<span style="color: red;">' . $lang['MSG_NEW_ACCOUNT_KO'] . '</span><br/>';
        $message .= '<span style="font-style: italic;">' . $lang['MSG_NEW_ACCOUNT_KO_NAME_ILLEGAL'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "createuser_failed_password":
        $message .= '<span style="color: red;">' . $lang['MSG_NEW_ACCOUNT_KO'] . '</span><br/>';
        $message .= '<span style="font-style: italic;">' . $lang['MSG_NEW_ACCOUNT_KO_PASSWORD_ILLEGAL'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "createuser_failed_email":
        $message .= '<span style="color: red;">' . $lang['MSG_NEW_ACCOUNT_KO'] . '</span><br/>';
        $message .= '<span style="font-style: italic;">' . $lang['MSG_NEW_ACCOUNT_KO_EMAIL_ILLEGAL'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "createuser_failed_general":
        $message .= '<span style="color: red;">' . $lang['MSG_NEW_ACCOUNT_KO'] . '</span><br>';
        $message .= '<span style="font-style: italic;">' . $lang['MSG_NEW_ACCOUNT_KO_OTHER'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "admin_modifyuser_success":
        $user_info = user_get($pub_info);
        $message .= '<span style="color: lime;">' . $lang['MSG_PROFILE_OK'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "admin_modifyuser_failed":
        $message .= '<span style="color: red;">' . $lang['MSG_PROFILE_KO'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "member_modifyuser_success":
        $message .= '<span style="color: lime;">' . $lang['MSG_PROFILE_SAVE_OK'] . '</span><br/>';
        $action = 'action=profile';
        break;

    case "member_modifyuser_failed":
        $message .= '<span style="color: red;">' . $lang['MSG_PROFILE_SAVE_KO'] . '</span><br/>';
        $action = 'action=profile';
        break;

    case "member_modifyuser_failed_passwordcheck":
        $message .= '<span style="color: red;">' . $lang['MSG_PROFILE_SAVE_KO'] . '</span><br/>';
        $message .= $lang['MSG_PROFILE_SAVE_PWD'];
        $action = 'action=profile';
        break;

    case "member_modifyuser_failed_password":
        $message .= '<span style="color: red;">' . $lang['MSG_PROFILE_SAVE_KO'] . '</span><br/>';
        $message .= $lang['MSG_PROFILE_SAVE_PWD_ILLEGAL'];
        $action = 'action=profile';
        break;

    case "member_modifyuser_failed_pseudolocked":
        $message .= '<span style="color: red;">' . $lang['MSG_PROFILE_SAVE_KO'] . '</span><br/>';
        $message .= $lang['MSG_PROFILE_SAVE_NAME_INUSE'];
        $action = 'action=profile';
        break;

    case "member_modifyuser_failed_pseudo":
        $message .= '<span style="color: red;">' . $lang['MSG_PROFILE_SAVE_KO'] . '</span><br/>';
        $message .= $lang['MSG_PROFILE_SAVE_NAME_ILLEGAL'];
        $action = 'action=profile';
        break;

    case "deleteuser_success":
        $message .= '<span style="color: lime;">' . $lang['MSG_DELETE_USER_OK'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "deleteuser_failed":
        $message .= '<span style="color: red;">' . $lang['MSG_DELETE_USER_KO'] . '</span><br./>';
        $action = 'action=administration&amp;subaction=member';
        break;

    case "login_wrong":
        $message .= '<span style="color: red;">' . $lang['MSG_LOGIN_WRONG'] . '</span><br/>';
        break;

    case "account_lock":
        $message .= '<span style="color: red;">' . $lang['MSG_LOGIN_INACTIVE'] . '</span><br/>';
        $message .= $lang['MSG_LOGIN_INACTIVE_CONTACT'];
        break;

    case "max_favorites":
        $message .= '<span style="color: orange;>' . $lang['MSG_MAX_FAVORITES'] . ' (' . $server_config["max_favorites"] . ')</span><br/>';
        break;

    case "setting_serverconfig_success":
        $message .= '<span style="color: lime;">' . $lang['MSG_SETTINGS_SERVERCONFIG_OK'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=parameter';
        break;

    case "setting_serverconfig_failed":
        $message .= '<span style="color: red;">' . $lang['MSG_SETTINGS_SERVERCONFIG_KO'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=parameter';
        break;

    case "setting_server_view_success":
        $message .= '<span style="color: lime;">' . $lang['MSG_SETTINGS_SERVERVIEW_OK'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=affichage';
        break;

    case "setting_server_view_failed":
        $message .= '<span style="color: red;">' . $lang['MSG_SETTINGS_SERVERVIEW_KO'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=affichage';
        break;

    case "log_missing":
        $message .= '<span style="color: orange;">' . $lang['MSG_LOG_MISSING'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=viewer';
        break;

    case "log_remove":
        $message .= '<span style="color: lime;">' . $lang['MSG_LOG_REMOVE'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=viewer';
        break;

    case "set_building_failed_planet_id":
        $message .= '<span style="color: orange;">' . $lang['MSG_FAILED_PLANETID'] . '</span><br/>';
        $action = 'action=home&amp;subaction=empire';
        break;

    case "install_directory":
        $message .= '<span style="color: red;">' . $lang['MSG_INSTALLFOLDER'] . '</span><br/>';
        break;

    case "createusergroup_success":
        $message .= '<span style="color: lime;">' . $lang['MSG_GROUP_CREATE'] . '</span><br/>';
        $action = 'action=administration&amp;subaction=group';
        break;

    case "createusergroup_failed_groupnamelocked":
        $message .= '<span style="color: red;">' . $lang['MSG_GROUP_CREATE_FAILED'] . '</span><br/>';
        $message .= $lang['MSG_GROUP_CREATE_FAILED_NAME'];
        $action = 'action=administration&amp;subaction=group';
        break;

        //
    case "createusergroup_failed_groupname":
        $message .= '<span style="color: red;">' . $lang['MSG_GROUP_CREATE_FAILED'] . '</span><br/>';
        $message .= $lang['MSG_GROUP_CREATE_FAILED_ILLEGAL'];
        $action = 'action=administration&amp;subaction=group';
        break;

    case "createusergroup_failed_general":
        $message .= '<span style="color: red;">' . $lang['MSG_GROUP_CREATE_FAILED'] . '</span><br/>';
        $message .= $lang['MSG_GROUP_CREATE_FAILED_OTHER'];
        $action = 'action=administration&amp;subaction=group';
        break;

    case "db_optimize":
        list($dbSize_before, $dbSize_after) = explode('¤', $pub_info);
        $message .= '<span style="color: lime;">' . $lang['MSG_DB_OPTIM_OK'] . '</span><br/>';
        $message .= $lang['MSG_DB_OPTIM_BEFORE'] . ' : ' . $dbSize_before . '<br/>';
        $message .= $lang['MSG_DB_OPTIM_AFTER'] . ' : ' . $dbSize_after . '<br/><br/>';
        $action = 'action=administration&amp;subaction=infoserver';
        break;

    case "set_empire_failed_data":
        $message .= '<span style="color: red;">' . $lang['MSG_EMPIRE_DATA_FAILURE'] . '</span><br/>';
        $action = 'action=home&amp;subaction=empire';
        break;

    case "raz_ratio":
        $message .= '<span style="color: lime;">' . $lang['MSG_RATIO_RAZ'] . '</span><br/>';
        $action = 'action=statistic';
        break;

    default:
        redirection('index.php');
        break;
}
$action = $action != "" ? "?" . $action : "";
$message .= '<br/><br/><a href="index.php' . $action . '">' . $lang['MSG_BACK'] . '</a>';

require_once('views/page_header_2.php');
?>
<table style="display:inline-block">
    <tr>
        <td class="c">
            <div style="font-weight:bold"><?php echo $message; ?></div>
        </td>
    </tr>
</table>
<?php require_once('views/page_tail_2.php'); ?>
