<?php
/***************************************************************************
 *    filename    : profile.php
 *    desc.        :
 *    Author        : Kyser - http://ogsteam.fr/
 *    created        : 08/12/2005
 *    modified    : 13/04/2007 20:40:00
 ***************************************************************************/

if (!defined("IN_SPYOGAME")) {
    die("Hacking attempt");
}
$user_name = $user_data["user_name"];
$user_galaxy = $user_data["user_galaxy"];
$user_system = $user_data["user_system"];
$user_email = $user_data["user_email"];
$user_stat_name = $user_data["user_stat_name"];
if ($server_config["disable_ip_check"] == 1) $disable_ip_check = $user_data["disable_ip_check"] == 1 ? "checked" : "";
else $disable_ip_check = "disabled";
$off_commandant = (isset ($user_data["off_commandant"]) && $user_data["off_commandant"] == 1) ? "checked" : "";
$off_amiral = (isset ($user_data["off_amiral"]) && $user_data["off_amiral"] == 1) ? "checked" : "";
$off_ingenieur = (isset ($user_data["off_ingenieur"]) && $user_data["off_ingenieur"] == 1) ? "checked" : "";
$off_geologue = (isset ($user_data["off_geologue"]) && $user_data["off_geologue"] == 1) ? "checked" : "";
$off_technocrate = (isset ($user_data["off_technocrate"]) && $user_data["off_technocrate"] == 1) ? "checked" : "";

require_once("views/page_header.php");
?>

    <!-- DEBUT DU SCRIPT -->
    <script language="JavaScript">
        function check_password(form) {
            var old_password = form.old_password.value;
            var new_password = form.new_password.value;
            var new_password2 = form.new_password2.value;

            if (old_password != "" && (new_password == "" || new_password2 == "")) {
                alert("<?php echo($lang['PROFILE_ERROR_RETRY']); ?>");
                return false;
            }
            if (old_password == "" && (new_password != "" || new_password2 != "")) {
                alert("<?php echo($lang['PROFILE_ERROR_OLDPWD']); ?>");
                return false;
            }
            if (old_password != "" && new_password != new_password2) {
                alert("<?php echo($lang['PROFILE_ERROR_ERROR']); ?>");
                return false;
            }
            if (old_password != "" && new_password != "" && new_password2 != "") {
                if (new_password.length < 6 || new_password.length > 15) {
                    alert("<?php echo($lang['PROFILE_ERROR_ILLEGAL']); ?>");
                    return false;
                }
            }

            return true;
        }
    </script>
    <!-- FIN DU SCRIPT -->

    <form method="POST" action="index.php" onSubmit="return check_password(this);">
        <input name="action" type="hidden" value="member_modify_member">
        <table width="450">
            <tr>
                <td class="c_user" colspan="2"><?php echo($lang['PROFILE_TITLE']); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_PSEUDO'].help("profile_login"); ?></th>
                <th><label>
                        <input name="pseudo" type="text" size="20" maxlength="20" value="<?php echo $user_name; ?>">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_OLDPWD']); ?></th>
                <th><label>
                        <input name="old_password" type="password" autocomplete="off" size="20" maxlength="15">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_NEWPWD'].help("profile_password"); ?></th>
                <th><label>
                        <input name="new_password" type="password" autocomplete="off" size="20" maxlength="15">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo($lang['PROFILE_NEWPWDAGAIN']); ?></th>
                <th><label>
                        <input name="new_password2" type="password" autocomplete="off" size="20" maxlength="15">
                    </label></th>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_EMAIL'].help("profile_pseudo_email"); ?></th>
                <th>
                    <label>
                        <input name="pseudo_email" type="text" size="30" value="<?php echo $user_email; ?>">
                    </label>
                </th>
            </tr>
            <tr>
                <td class="c" colspan="2"><?php echo($lang['PROFILE_GAME']); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['PROFILE_MAINPLANET'].help("profile_main_planet"); ?></th>
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
                <th><?php echo $lang['PROFILE_PLAYERNAME'].help("profile_pseudo_ingame"); ?></th>
                <th>
                    <label>
                        <input name="pseudo_ingame" type="text" size="20" value="<?php echo $user_stat_name; ?>">
                    </label>
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
                <th><?php echo $lang['PROFILE_IPCHECK_DISABLE'].help("profile_disable_ip_check"); ?></th>
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