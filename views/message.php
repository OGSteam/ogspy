<?php
/**
 * Pop up Messages
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
use Ogsteam\Ogspy\Core\Ogspy ;

$Ogspy = Ogspy::GetInstance();

if (!isset($Ogspy->Params->id_message) || !isset($Ogspy->Params->info)) {
    redirection("index.php");
}

if (!check_var($Ogspy->Params->id_message, "Char") || !check_var($Ogspy->Params->info, "Special", "#^[\sa-zA-Z0-9~¤_.\-\:\[\]]+$#")) {
    redirection("index.php");
}

$action = "";
$message = "<b>" . $lang['MSG_SYSTEM'] . "</b><br><br>";

switch ($Ogspy->Params->id_message) {
    //
    case "forbidden" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_FORBIDDEN'] . "</b></span>";
        break;

    //
    case "errorfatal" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_ERRORFATAL'] . "</b></span>";
        break;

    case "errormod" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_ERRORMOD'] . "</b></span>";
        break;
    //
    case "errordata" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_ERRORDATA'] . "</b></span>";
        break;

    //
    case "createuser_success" :
        list($user_id, $password) = explode(":", $Ogspy->Params->info);
        $user_info = user_get($user_id);
        $phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
        $server_name = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_URL);
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_CREATE_USER_TITLE'] . " <a>" . $user_info[0]["user_name"] . "</a></b></span><br>";
        $message .= $lang['MSG_CREATE_USER_INFO'] . "<br><br>";
        $message .= "- " . $lang['MSG_CREATE_USER_URL'] . " :<br><a>https://" . $server_name . $phpSelf . "</a><br><br>";
        $message .= "- " . $lang['MSG_CREATE_USER_PASSWORD'] . " :<br><a>" . $password . "</a><br>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "regeneratepwd_success" :
        list($user_id, $password) = explode(":", $Ogspy->Params->info);
        $user_info = user_get($user_id);
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_PWD_REGEN_OK'] . " <a>" . $user_info[0]["user_name"] . "</a></b></span><br>";
        if($password=="mail")
        {
            $message .= $lang['MSG_PWD_REGEN_INFO_MAIL'];
        }
        else
        {
            $message .= $lang['MSG_PWD_REGEN_INFO'] . " : <a>" . $password . "</a>";
        }
        $action = "action=administration&subaction=member";
        break;

    //
    case "regeneratepwd_failed" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_PWD_REGEN_KO'] . "</b></span>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "createuser_failed_pseudolocked" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_NEW_ACCOUNT_KO'] . "</b></span><br>";
        $message .= "<i>" . $lang['MSG_NEW_ACCOUNT_KO_NAME'] . " (" . $Ogspy->Params->info . ")</i>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "createuser_failed_pseudo" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_NEW_ACCOUNT_KO'] . "</b></span><br>";
        $message .= "<i>" . $lang['MSG_NEW_ACCOUNT_KO_NAME_ILLEGAL'] . "</i></a>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "createuser_failed_password" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_NEW_ACCOUNT_KO'] . "</b></span><br>";
        $message .= "<i>" . $lang['MSG_NEW_ACCOUNT_KO_PASSWORD_ILLEGAL'] . "</i>";
        $action = "action=administration&subaction=member";
        break;

    case "createuser_failed_email" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_NEW_ACCOUNT_KO'] . "</b></span><br>";
        $message .= "<i>" . $lang['MSG_NEW_ACCOUNT_KO_EMAIL_ILLEGAL'] . "</i>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "createuser_failed_general" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_NEW_ACCOUNT_KO'] . "</a></b></span><br>";
        $message .= "<i>" . $lang['MSG_NEW_ACCOUNT_KO_OTHER'] . "</i></a>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "admin_modifyuser_success" :
        $user_info = user_get($Ogspy->Params->info);
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_PROFILE_OK'] . "</b></span>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "admin_modifyuser_failed" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_PROFILE_KO'] . "</b></span>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "member_modifyuser_success" :
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_PROFILE_SAVE_OK'] . "</b></span>";
        $action = "action=profile";
        break;

    //
    case "member_modifyuser_failed" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_PROFILE_SAVE_KO'] . "</b></span>";
        $action = "action=profile";
        break;

    //
    case "member_modifyuser_failed_passwordcheck" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_PROFILE_SAVE_KO'] . "</b></span><br>";
        $message .= $lang['MSG_PROFILE_SAVE_PWD'];
        $action = "action=profile";
        break;

    //
    case "member_modifyuser_failed_password" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_PROFILE_SAVE_KO'] . "</a></b></span><br>";
        $message .= $lang['MSG_PROFILE_SAVE_PWD_ILLEGAL'];
        $action = "action=profile";
        break;

    //
    case "member_modifyuser_failed_pseudolocked" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_PROFILE_SAVE_KO'] . "</b></span><br>";
        $message .= $lang['MSG_PROFILE_SAVE_NAME_INUSE'];
        $action = "action=profile";
        break;

    //
    case "member_modifyuser_failed_pseudo" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_PROFILE_SAVE_KO'] . "</b></span><br>";
        $message .= $lang['MSG_PROFILE_SAVE_NAME_ILLEGAL'];
        $action = "action=profile";
        break;

    //
    case "deleteuser_success" :
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_DELETE_USER_OK'] . "</b></span>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "deleteuser_failed" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_DELETE_USER_KO'] . "</b></span>";
        $action = "action=administration&subaction=member";
        break;

    //
    case "login_wrong" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_LOGIN_WRONG'] . "</b></span>";
        break;

    //
    case "account_lock" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_LOGIN_INACTIVE'] . "</b></span><br>";
        $message .= $lang['MSG_LOGIN_INACTIVE_CONTACT'];
        break;

    //
    case "max_favorites" :
        $message .= "<span style=\"color: orange; \"><b>" . $lang['MSG_MAX_FAVORITES'] . " (" . $server_config["max_favorites"] . ")</b></span>";

        break;

    //
    case "setting_serverconfig_success" :
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_SETTINGS_SERVERCONFIG_OK'] . "</b></span>";
        $action = "action=administration&subaction=parameter";
        break;
    //

    case "setting_serverconfig_failed" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_SETTINGS_SERVERCONFIG_KO'] . "</b></span>";
        $action = "action=administration&subaction=parameter";
        break;

    //
    case "setting_server_view_success" :
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_SETTINGS_SERVERVIEW_OK'] . "</b></span>";
        $action = "action=administration&subaction=affichage";
        break;

    //
    case "setting_server_view_failed" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_SETTINGS_SERVERVIEW_KO'] . "</b></span>";
        $action = "action=administration&subaction=affichage";
        break;


    //
    case "log_missing" :
        $message .= "<span style=\"color: orange; \"><b>" . $lang['MSG_LOG_MISSING'] . "</b></span>";
        $action = "action=administration&subaction=viewer";
        break;

    //
    case "log_remove" :
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_LOG_REMOVE'] . "</b></span>";
        $action = "action=administration&subaction=viewer";
        break;

    //
    case "set_building_failed_planet_id" :
        $message .= "<span style=\"color: orange; \"><b>" . $lang['MSG_FAILED_PLANETID'] . "</b></span>";
        $action = "action=home&subaction=empire";
        break;

    //
    case "install_directory" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_INSTALLFOLDER'] . "</b></span>";
        break;

    //

    case "createusergroup_success" :
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_GROUP_CREATE'] . "</b></span><br>";
        $action = "action=administration&subaction=group";
        break;

    //
    case "createusergroup_failed_groupnamelocked" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_GROUP_CREATE_FAILED'] . "</b></span><br>";
        $message .= $lang['MSG_GROUP_CREATE_FAILED_NAME'];
        $action = "action=administration&subaction=group";
        break;

    //
    case "createusergroup_failed_groupname" :
        $message .= "<span style=\"color: red; \"><b" . $lang['MSG_GROUP_CREATE_FAILED'] . "</b></span><br>";
        $message .= $lang['MSG_GROUP_CREATE_FAILED_ILLEGAL'];
        $action = "action=administration&subaction=group";
        break;

    //
    case "createusergroup_failed_general" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_GROUP_CREATE_FAILED'] . "</a></b></span><br>";
        $message .= $lang['MSG_GROUP_CREATE_FAILED_OTHER'];
        $action = "action=administration&subaction=group";
        break;

    //
    case "db_optimize" :
        list($dbSize_before, $dbSize_after) = explode("¤", $Ogspy->Params->info);
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_DB_OPTIM_OK'] . "</b></span><br>";
        $message .= $lang['MSG_DB_OPTIM_BEFORE'] . " : " . $dbSize_before . "<br>";
        $message .= $lang['MSG_DB_OPTIM_AFTER'] . " : " . $dbSize_after . "<br><br>";
        $action = "action=administration&subaction=infoserver";
        break;

    //
    case "set_empire_failed_data" :
        $message .= "<span style=\"color: red; \"><b>" . $lang['MSG_EMPIRE_DATA_FAILURE'] . "</b></span>";
        $action = "action=home&subaction=empire";
        break;

    //
    case "raz_ratio" :
        $message .= "<span style=\"color: lime; \"><b>" . $lang['MSG_RATIO_RAZ'] . "</b></span><br>";
        $action = "action=statistic";
        break;

    //
    default:
        redirection("index.php");
        break;
}

$action = $action != "" ? "?" . $action : "";
$message .= "<br><br><a href='index.php" . $action . "'>" . $lang['MSG_BACK'] . "</a>";

require_once("views/page_header_2.php"); ?>

<table align="center">
    <tr>
        <td class="c">
            <div align="center"><?php echo $message; ?></div>
        </td>
    </tr>
</table>

<?php
require_once("views/page_tail_2.php");
?>
