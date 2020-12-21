<?php
/**
 * User profile
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined("IN_SPYOGAME")) {
    die("Hacking attempt");
}
$user_name = $user_data["user_name"];
$user_galaxy = $user_data["user_galaxy"];
$user_system = $user_data["user_system"];
$user_email = $user_data["user_email"];
$user_stat_name = $user_data["user_stat_name"];
$user_class = $user_data["user_class"];



$user_token = (new Ogsteam\Ogspy\Model\Tokens_Model)->get_token($user_data["user_id"], "PAT");
if ($user_token != false) {
    $user_token_displayed = $user_token;
} else {
    $user_token_displayed = $lang['PROFILE_TOKEN_TO_BE_UPDATED'];
}

if ($server_config["disable_ip_check"] == 1) {
    $disable_ip_check = $user_data["disable_ip_check"] == 1 ? "checked" : "";
} else {
    $disable_ip_check = "disabled";
}
$off_commandant = (isset ($user_data["off_commandant"]) && $user_data["off_commandant"] == 1) ? "checked" : "";
$off_amiral = (isset ($user_data["off_amiral"]) && $user_data["off_amiral"] == 1) ? "checked" : "";
$off_ingenieur = (isset ($user_data["off_ingenieur"]) && $user_data["off_ingenieur"] == 1) ? "checked" : "";
$off_geologue = (isset ($user_data["off_geologue"]) && $user_data["off_geologue"] == 1) ? "checked" : "";
$off_technocrate = (isset ($user_data["off_technocrate"]) && $user_data["off_technocrate"] == 1) ? "checked" : "";

$message  = "{PROFILE_ERROR_RETRY: '" . $lang['PROFILE_ERROR_RETRY'];
$message .= "',PROFILE_ERROR_OLDPWD: '" . $lang['PROFILE_ERROR_OLDPWD'];
$message .= "',PROFILE_ERROR_ERROR: '" . $lang['PROFILE_ERROR_ERROR'];
$message .= "',PROFILE_ERROR_ILLEGAL: '" . $lang['PROFILE_ERROR_ILLEGAL'] . "'}";

require_once("views/page_header.php");
?>
    <form method="POST" action="index.php" onSubmit="return ogspy_check_password(this, <?php echo $message; ?>);">
        <input name="action" type="hidden" value="member_modify_member">
        <table width="600">
            <tr>
                <td class="c_user" colspan="2"><?php echo($lang['PROFILE_TITLE']); ?></td>
            </tr>
            <tr>
                <th width="35%"><?php echo $lang['PROFILE_PSEUDO'] . help("profile_login"); ?></th>
                <th><label>
                        <input name="pseudo" type="text" size="20" maxlength="20" value="<?php echo $user_name; ?>">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_OLDPWD']); ?></th>
                <th><label>
                        <input name="old_password" type="password" autocomplete="off" size="20" maxlength="64">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_NEWPWD'] . help("profile_password"); ?></th>
                <th><label>
                        <input name="new_password" type="password" autocomplete="off" size="20" maxlength="64">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_NEWPWDAGAIN']); ?></th>
                <th><label>
                        <input name="new_password2" type="password" autocomplete="off" size="20" maxlength="64">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_EMAIL'] . help("profile_pseudo_email"); ?></th>
                <th>
                    <label>
                        <input name="pseudo_email" type="text" size="32" value="<?php echo $user_email; ?>">
                    </label>
                </th>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_TOKEN'] . help("profile_token"); ?></th>
                <th>
                    <label>
                        <input name="pseudo_user_token" type="text" size="32" maxlength="64" value="<?php echo $user_token_displayed; ?>"><br>
                        <input name="renew_user_token" value="1" type="checkbox" ><span><?php echo $lang['PROFILE_TOKEN_UPDATE']; ?><span>
                    </label>
                </th>
            </tr>
            <tr>
                <td class="c" colspan="2"><?php echo($lang['PROFILE_GAME']); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_MAINPLANET'] . help("profile_main_planet"); ?></th>
                <th>
                    <label>
                        <input name="galaxy" type="text" size="3" maxlength="2" value="<?php echo $user_galaxy; ?>">
                    </label>&nbsp;
                    <label>
                        <input name="system" type="text" size="3" maxlength="3" value="<?php echo $user_system; ?>">
                    </label>
                </th>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_PLAYERNAME'] . help("profile_pseudo_ingame"); ?></th>
                <th>
                    <label>
                        <input name="pseudo_ingame" type="text" size="20" value="<?php echo $user_stat_name; ?>">
                    </label>
                </th>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_CLASS']; ?></th>
                <th>
                    <?php //todo aucun lieu de centralisation de ce type de donnée magique !!! fichier de conf ogame ? ?>
                    <?php $classType = array('none','COL','GEN','EXP') ; ?>
                     <select name='user_class'>
                        <?php foreach  ($classType as $class) : ?>
                            <?php  echo $class ."__".$user_class ; ?>
                            <option value='<?php echo $class; ?>'

                                    <?php if (trim($class) == trim($user_class)) :?>
                                        selected='selected' >
                                    <?php else : ?>
                                        >
                                    <?php endif ; ?>
                                <?php echo $lang['PROFILE_CLASS_'.strtoupper($class)]; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </th>
            </tr>
            <tr>
                <td class="c" colspan="2"><?php echo($lang['PROFILE_OFFICERS']); ?></td>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_CODMANDER']); ?>:</th>
                <th>
                    <label>
                        <input name="off_commandant" value="1" type="checkbox" <?php echo $off_commandant; ?>>
                    </label>
                </th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_ADMIRAL']); ?>:</th>
                <th>
                    <label>
                        <input name="off_amiral" value="1" type="checkbox" <?php echo $off_amiral; ?>>
                    </label>
                </th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_ENGINEER']); ?>:</th>
                <th>
                    <label>
                        <input name="off_ingenieur" value="1" type="checkbox" <?php echo $off_ingenieur; ?>>
                    </label>
                </th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_GEOLOGIST']); ?>:</th>
                <th>
                    <label>
                        <input name="off_geologue" value="1" type="checkbox" <?php echo $off_geologue; ?>>
                    </label>
                </th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_TECHNOCRAT']); ?>:</th>
                <th>
                    <label>
                        <input name="off_technocrate" value="1" type="checkbox" <?php echo $off_technocrate; ?>>
                    </label>
                </th>
            </tr>
            <tr>
                <td class="c" colspan="2"><?php echo($lang['PROFILE_OTHERS']); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_IPCHECK_DISABLE'] . help("profile_disable_ip_check"); ?></th>
                <th>
                    <label>
                        <input name="disable_ip_check" value="1" type="checkbox" <?php echo $disable_ip_check; ?>>
                    </label>
                </th>
            </tr>
            <tr>
                <th colspan="2">&nbsp;</th>
            </tr>
            <tr>
                <th colspan="2" align="center"><input type="submit" value="<?php echo($lang['PROFILE_SAVE']); ?>"></th>
            </tr>
        </table>
    </form>

<?php
require_once("views/page_tail.php");
?>