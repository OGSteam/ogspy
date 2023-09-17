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

$msgTitle=$lang['MSG_SYSTEM']; //titre
$msgContent=""; //contenu
$msgURLButton="index.php"; // lien retour par defaut
$msgType=""; // type : vide|warning|success|danger


switch ($pub_id_message) {
    case "forbidden":
        $msgType = "danger";
        $msgContent = $lang['MSG_FORBIDDEN'];
        break;
    case "errorfatal":
        $msgType = "danger";
        $msgContent = $lang['MSG_ERRORFATAL'];
        break;

    case "errormod":
        $message .= '<span style="color: red;">' . $lang['MSG_ERRORMOD'] . '</span><br/>';
        break;
    case "errordata":
        $msgType = "danger";
        $msgContent = $lang['MSG_ERRORDATA'];
        break;

    case "createuser_success":
        list($user_id, $password) = explode(':', $pub_info);
        $user_info = user_get($user_id);
        $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
        $server_name = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL);

        $msgType = "success";

        $msgContent = '' . $lang['MSG_CREATE_USER_TITLE'] . ' <a>' . $user_info[0]['user_name'] . '</a><br/><br/>';
        $msgContent .= $lang['MSG_CREATE_USER_INFO'] . '<br/><br/>';
        $msgContent .= '' . $lang['MSG_CREATE_USER_TITLE'] . ' <a>' . $user_info[0]['user_name'] . '</a></br></br>';
        $msgContent .= '- ' . $lang['MSG_CREATE_USER_URL'] . ' : <a>https://' . $server_name . $phpSelf . '</a><br/>';
        $msgContent .= '- ' . $lang['MSG_CREATE_USER_PASSWORD'] . ' : <a>' . $password . '</a><br/>';

        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "regeneratepwd_success":
        $msgType = "success";

        list($user_id, $password) = explode(':', $pub_info);
        $user_info = user_get($user_id);
        $msgContent = $lang['MSG_PWD_REGEN_OK'] . '<a>' . $user_info[0]['user_name'] . '</a><br/>';

        if ($password == "mail") {
            $msgContent .= $lang['MSG_PWD_REGEN_INFO_MAIL'];
        } else {
            $msgContent .= $lang['MSG_PWD_REGEN_INFO'] . ' : <a>' . $password . '</a>';
        }
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "regeneratepwd_failed":

        $msgType = "danger";
        $msgContent = $lang['MSG_PWD_REGEN_KO'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "createuser_failed_pseudolocked":
        $msgType = "danger";
        $msgContent = $lang['MSG_NEW_ACCOUNT_KO'] . '<br/>';
        $msgContent .= $lang['MSG_NEW_ACCOUNT_KO_NAME'] . ' (' . $pub_info . ')';
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "createuser_failed_pseudo":
        $msgType = "danger";
        $msgContent = $lang['MSG_NEW_ACCOUNT_KO'] . '<br/>';
        $msgContent .= $lang['MSG_NEW_ACCOUNT_KO_NAME_ILLEGAL'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "createuser_failed_password":
        $msgType = "danger";
        $msgContent = $lang['MSG_NEW_ACCOUNT_KO'] . '<br/>';
        $msgContent .= $lang['MSG_NEW_ACCOUNT_KO_PASSWORD_ILLEGAL'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "createuser_failed_email":
        $msgType = "danger";
        $msgContent = $lang['MSG_NEW_ACCOUNT_KO'] . '<br/>';
        $msgContent .= $lang['MSG_NEW_ACCOUNT_KO_EMAIL_ILLEGAL'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "createuser_failed_general":
        $msgType = "danger";
        $msgContent = $lang['MSG_NEW_ACCOUNT_KO'] . '<br/>';
        $msgContent .= $lang['MSG_NEW_ACCOUNT_KO_OTHER'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "admin_modifyuser_success":
        $msgType = "success";
        $msgContent = $lang['MSG_PROFILE_OK'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';

        //$user_info = user_get($pub_info); PK ?
        //$message .= '<span style="color: lime;">' . $lang['MSG_PROFILE_OK'] . '</span><br/>';
        //$action = 'action=administration&amp;subaction=member';
        break;

    case "admin_modifyuser_failed":
        $msgType = "danger";
        $msgContent = $lang['MSG_PROFILE_KO'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "member_modifyuser_success":
        $msgType = "success";
        $msgContent = $lang['MSG_PROFILE_SAVE_OK'];
        $msgURLButton = 'index.php?action=profile';
        break;

    case "member_modifyuser_failed":
        $msgType = "danger";
        $msgContent = $lang['MSG_PROFILE_SAVE_KO'];
        $msgURLButton = 'index.php?action=profile';
        break;

    case "member_modifyuser_failed_passwordcheck":
        $msgType = "danger";
        $msgContent = $lang['MSG_PROFILE_SAVE_KO'] . '<br/>';
        $msgContent .= $lang['MSG_PROFILE_SAVE_PWD'];
        $msgURLButton = 'index.php?action=profile';
        break;

    case "member_modifyuser_failed_password":
        $msgType = "danger";
        $msgContent = $lang['MSG_PROFILE_SAVE_KO'] . '<br/>';
        $msgContent .= $lang['MSG_PROFILE_SAVE_PWD_ILLEGAL'];
        $msgURLButton = 'index.php?action=profile';
        break;

    case "member_modifyuser_failed_pseudolocked":
        $msgType = "danger";
        $msgContent = $lang['MSG_PROFILE_SAVE_KO'] . '<br/>';
        $msgContent .= $lang['MSG_PROFILE_SAVE_NAME_INUSE'];
        $msgURLButton = 'index.php?action=profile';
        break;

    case "member_modifyuser_failed_pseudo":
        $msgType = "danger";
        $msgContent = $lang['MSG_PROFILE_SAVE_KO'] . '<br/>';
        $msgContent .= $lang['MSG_PROFILE_SAVE_NAME_ILLEGAL'];
        $msgURLButton = 'index.php?action=profile';
        break;

    case "deleteuser_success":
        $msgType = "success";
        $msgContent = $lang['MSG_DELETE_USER_OK'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "deleteuser_failed":
        $msgType = "danger";
        $msgContent = $lang['MSG_DELETE_USER_KO'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=member';
        break;

    case "login_wrong":
        $msgType = "danger";
        $msgContent = $lang['MSG_LOGIN_WRONG'];
        break;

    case "account_lock":
        $msgType = "danger";
        $msgContent = $lang['MSG_LOGIN_INACTIVE'] . '<br/>';
        $msgContent .= $lang['MSG_LOGIN_INACTIVE'];
        break;

    case "max_favorites":
        $msgType = "warning";
        $msgContent = $lang['MSG_MAX_FAVORITES'] . ' (' . $server_config["max_favorites"] . ')';
        break;

    case "setting_serverconfig_success":
        $msgType = "success";
        $msgContent = $lang['MSG_SETTINGS_SERVERCONFIG_OK'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=parameter';
        break;

    case "setting_serverconfig_failed":
        $msgType = "danger";
        $msgContent = $lang['MSG_SETTINGS_SERVERCONFIG_KO'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=parameter';
        break;

    case "setting_server_view_success":
        $msgType = "success";
        $msgContent = $lang['MSG_SETTINGS_SERVERVIEW_OK'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=affichage';
        break;

    case "setting_server_view_failed":
        $msgType = "danger";
        $msgContent = $lang['MSG_SETTINGS_SERVERVIEW_KO'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=affichage';
        break;

    case "log_missing":
        $msgType = "warning";
        $msgContent = $lang['MSG_LOG_MISSING'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=viewer';
        break;

    case "log_remove":
        $msgType = "warning";
        $msgContent = $lang['MSG_LOG_REMOVE'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=viewer';
        break;

    case "set_building_failed_planet_id":
        $msgType = "warning";
        $msgContent = $lang['MSG_FAILED_PLANETID'];
        $msgURLButton = 'index.php?action=home&amp;subaction=empire';
        break;

    case "install_directory":
        $msgType = "danger";
        $msgContent = $lang['MSG_INSTALLFOLDER'];
        break;

    case "createusergroup_success":
        $msgType = "success";
        $msgContent = $lang['MSG_GROUP_CREATE'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=group';
        break;

    case "createusergroup_failed_groupnamelocked":
        $msgType = "danger";
        $msgContent = $lang['MSG_GROUP_CREATE_FAILED'] . '<br/>';
        $msgContent .= $lang['MSG_GROUP_CREATE_FAILED_NAME'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=group';
        break;

    //
    case "createusergroup_failed_groupname":
        $msgType = "danger";
        $msgContent = $lang['MSG_GROUP_CREATE_FAILED'] . '<br/>';
        $msgContent .= $lang['MSG_GROUP_CREATE_FAILED_ILLEGAL'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=group';
        break;

    case "createusergroup_failed_general":
        $msgType = "danger";
        $msgContent = $lang['MSG_GROUP_CREATE_FAILED'] . '<br/>';
        $msgContent .= $lang['MSG_GROUP_CREATE_FAILED_OTHER'];
        $msgURLButton = 'index.php?action=administration&amp;subaction=group';
        break;

    case "db_optimize":
        list($dbSize_before, $dbSize_after) = explode('¤', $pub_info);

        $msgType = "success";
        $msgContent = $lang['MSG_DB_OPTIM_OK'] . '<br/>';
        $msgContent = $lang['MSG_DB_OPTIM_BEFORE'] . ' : ' . $dbSize_before . '<br/>';
        $msgContent = $lang['MSG_DB_OPTIM_AFTER'] . ' : ' . $dbSize_after . '<br/>';
        $msgURLButton = 'index.php?action=administration&amp;subaction=infoserver';
        break;

    case "set_empire_failed_data":
        $msgType = "danger";
        $msgContent = $lang['MSG_EMPIRE_DATA_FAILURE'];
        $msgURLButton = 'index.php?action=home&amp;subaction=empire';
        break;

    case "raz_ratio":
        $msgType = "success";
        $msgContent = $lang['MSG_RATIO_RAZ'];
        $msgURLButton = 'action=statistic';
        break;

    //todo : custom message ?
    default:
        redirection('index.php');
        break;
}

require_once('views/page_header_2.php');
?>



<div class="page_message">
    <div class="og-msg og-msg-<?php echo $msgType; ?>">
        <h3 class="og-title"><?php echo $msgTitle; ?></h3>
        <p class="og-content"><?php echo $msgContent; ?></p>
        <button type="button"  onclick="location.href='<?php echo $msgURLButton; ?>'" class="og-button og-button-<?php echo $msgType; ?>">
            <?php echo $lang['MSG_BACK']; ?>
        </button>
    </div>
</div>


<?php require_once('views/page_tail_2.php'); ?>
