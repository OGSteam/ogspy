<?php global $lang, $server_config;

/**
 * User profile
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
use Ogsteam\Ogspy\Model\Tokens_Model;
use Ogsteam\Ogspy\Model\Player_Model;

if (!defined("IN_SPYOGAME")) {
    die("Hacking attempt");
}

// Get User Data


// Get Player Data

$player_data = (new Player_Model)->get_player_data($user_data["player_id"]);

$user_name = $user_data["name"];
$user_galaxy = $user_data["default_galaxy"];
$user_system = $user_data["default_system"];
$user_email = $user_data["email"];
$user_stat_name = $player_data["name"] ?? "Inconnu";
$player_class = $player_data["class"] ?? "unknown";

$user_token = (new Tokens_Model)->get_token($user_data["id"], "PAT");
if (!empty($user_token)) {
    $user_token_displayed = $user_token;
} else {
    $user_token_displayed = $lang['PROFILE_TOKEN_TO_BE_UPDATED'];
}

if ($server_config["disable_ip_check"] == 1) {
    $disable_ip_check = $user_data["disable_ip_check"] == 1 ? "checked" : "";
} else {
    $disable_ip_check = "disabled";
}
$off_commandant = (isset($player_data["off_commandant"]) && $player_data["off_commandant"] == 1) ? "checked" : "";
$off_amiral = (isset($player_data["off_amiral"]) && $player_data["off_amiral"] == 1) ? "checked" : "";
$off_ingenieur = (isset($player_data["off_ingenieur"]) && $player_data["off_ingenieur"] == 1) ? "checked" : "";
$off_geologue = (isset($player_data["off_geologue"]) && $player_data["off_geologue"] == 1) ? "checked" : "";
$off_technocrate = (isset($player_data["off_technocrate"]) && $player_data["off_technocrate"] == 1) ? "checked" : "";

$message = "{PROFILE_ERROR_RETRY: '" . $lang['PROFILE_ERROR_RETRY'];
$message .= "',PROFILE_ERROR_OLDPWD: '" . $lang['PROFILE_ERROR_OLDPWD'];
$message .= "',PROFILE_ERROR_ERROR: '" . $lang['PROFILE_ERROR_ERROR'];
$message .= "',PROFILE_ERROR_ILLEGAL: '" . $lang['PROFILE_ERROR_ILLEGAL'] . "'}";

require_once("views/page_header.php");

if ($user_data['pwd_change']) {
    echo '<div id="pwdchange">' . $lang['PROFILE_CHANGEPWD'] . "</div>\n";
}
?>
<div class="page_profil">
    <form method="POST" action="index.php" onSubmit="return ogspy_checkPassword(this, <?php echo $message; ?>);">
        <input name="action" type="hidden" value="member_modify_member">
        <table class="og-table og-little-table">
            <thead>
                <tr>
                    <th  colspan="2"><?php echo ($lang['PROFILE_TITLE']); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tdstat">
                        <?php echo $lang['PROFILE_PSEUDO'] . help("profile_login"); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="pseudo" type="text" size="20" maxlength="20" value="<?php echo $user_name; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['PROFILE_OLDPWD']); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="old_password" type="password" autocomplete="off" size="20" maxlength="64">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo $lang['PROFILE_NEWPWD'] . help("profile_password"); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="new_password" type="password" autocomplete="off" size="20" maxlength="64">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['PROFILE_NEWPWDAGAIN']); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="new_password2" type="password" autocomplete="off" size="20" maxlength="64">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo $lang['PROFILE_EMAIL'] . help("profile_pseudo_email"); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="pseudo_email" type="text" size="32" value="<?php echo $user_email; ?>">
                    </td>
                </tr>
                <tr>
                    <td  class="tdstat">
                        <?php echo $lang['PROFILE_TOKEN'] . help("profile_token"); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="pseudo_user_token" type="text" size="32" maxlength="64" value="<?php echo $user_token_displayed; ?>"><br>
                        <input name="renew_user_token" value="1" type="checkbox"><span><?php echo $lang['PROFILE_TOKEN_UPDATE']; ?></span>

                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2"><?php echo ($lang['PROFILE_GAME']); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tdstat">
                        <?php echo $lang['PROFILE_MAINPLANET'] . help("profile_main_planet"); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="galaxy" type="text" size="3" maxlength="2" value="<?php echo $user_galaxy; ?>">
                        &nbsp;
                        <input name="system" type="text" size="3" maxlength="3" value="<?php echo $user_system; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo $lang['PROFILE_PLAYERNAME'] . help("profile_pseudo_ingame"); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="pseudo_ingame" type="text" size="20" value="<?php echo $user_stat_name; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo $lang['PROFILE_CLASS']; ?>
                    </td>
                    <td class="tdvalue">
                        <?php $classType = ogame_get_element_names()['CLASS']; ?>
                        <select name='user_class'>
                            <?php foreach ($classType as $class) : ?>
                                <?php echo $class . "__" . $player_class; ?>
                                <option value='<?php echo $class; ?>' <?php if (trim($class) == trim($player_class)) : ?> selected='selected'>
                                    <?php else : ?>
                                        >
                                    <?php endif; ?>
                                    <?php echo $lang['PROFILE_CLASS_' . strtoupper($class)]; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2"><?php echo ($lang['PROFILE_OFFICERS']); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['PROFILE_CODMANDER']); ?>:
                    </td>
                    <td class="tdvalue">
                        <label>
                            <input name="off_commandant" value="1" type="checkbox" <?php echo $off_commandant; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['PROFILE_ADMIRAL']); ?>:
                    </td>
                    <td class="tdvalue">
                        <label>
                            <input name="off_amiral" value="1" type="checkbox" <?php echo $off_amiral; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['PROFILE_ENGINEER']); ?>:
                    </td>
                    <td class="tdvalue">
                        <label>
                            <input name="off_ingenieur" value="1" type="checkbox" <?php echo $off_ingenieur; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['PROFILE_GEOLOGIST']); ?>:
                    </td>
                    <td class="tdvalue">
                        <label>
                            <input name="off_geologue" value="1" type="checkbox" <?php echo $off_geologue; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['PROFILE_TECHNOCRAT']); ?>:
                    </td>
                    <td class="tdvalue">
                        <label>
                            <input name="off_technocrate" value="1" type="checkbox" <?php echo $off_technocrate; ?>>
                        </label>
                    </td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="2">
                        <?php echo ($lang['PROFILE_OTHERS']); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tdstat"><?php echo $lang['PROFILE_IPCHECK_DISABLE'] . help("profile_disable_ip_check"); ?></td>
                    <td class="tdvalue">
                        <label>
                            <input name="disable_ip_check" value="1" type="checkbox" <?php echo $disable_ip_check; ?>>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="og-button" type="submit" value="<?php echo ($lang['PROFILE_SAVE']); ?>">
                    </td>
                </tr>
            </tbody>
        </table>

                                </form>
                                </div> <!-- fin div class="page_profil"> -->
                                <?php
                                require_once("views/page_tail.php");
                                ?>
