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
    die('Hacking attempt');
}

$user_name      = $user_data['user_name'];
$user_galaxy    = $user_data['user_galaxy'];
$user_system    = $user_data['user_system'];
$user_email     = $user_data['user_email'];
$user_stat_name = $user_data['user_stat_name'];
$user_class     = $user_data['user_class'];

$user_token = (new Ogsteam\Ogspy\Model\Tokens_Model)->get_token($user_data['user_id'], 'PAT');
if ($user_token != false) {
    $user_token_displayed = $user_token;
} else {
    $user_token_displayed = $lang['PROFILE_TOKEN_TO_BE_UPDATED'];
}

if ($server_config['disable_ip_check'] == 1) {
    $disable_ip_check = $user_data['disable_ip_check'] == 1 ? 'checked' : '';
} else {
    $disable_ip_check = 'disabled';
}
$off_commandant  = (isset ($user_data['off_commandant'])  && $user_data['off_commandant'] == 1)  ? 'checked' : '';
$off_amiral      = (isset ($user_data['off_amiral'])      && $user_data['off_amiral'] == 1)      ? 'checked' : '';
$off_ingenieur   = (isset ($user_data['off_ingenieur'])   && $user_data['off_ingenieur'] == 1)   ? 'checked' : '';
$off_geologue    = (isset ($user_data['off_geologue'])    && $user_data['off_geologue'] == 1)    ? 'checked' : '';
$off_technocrate = (isset ($user_data['off_technocrate']) && $user_data['off_technocrate'] == 1) ? 'checked' : '';

require_once('views/page_header.php');
?>
    <!-- DEBUT DU SCRIPT -->
    <script>
        function check_password(form) {
            let old_password = form.old_password.value;
            let new_password = form.new_password.value;
            let new_password2 = form.new_password2.value;

            if (old_password !== "" && (new_password === "" || new_password2 === "")) {
                alert("<?php echo($lang['PROFILE_ERROR_RETRY']); ?>");
                return false;
            }
            if (old_password === "" && (new_password !== "" || new_password2 !== "")) {
                alert("<?php echo($lang['PROFILE_ERROR_OLDPWD']); ?>");
                return false;
            }
            if (old_password !== "" && new_password !== new_password2) {
                alert("<?php echo($lang['PROFILE_ERROR_ERROR']); ?>");
                return false;
            }
            if (old_password !== "" && new_password !== "" && new_password2 !== "") {
                if (new_password.length < 6 || new_password.length > 64) {
                    alert("<?php echo($lang['PROFILE_ERROR_ILLEGAL']); ?>");
                    return false;
                }
            }

            return true;
        }
    </script>
    <!-- FIN DU SCRIPT -->
    <form method="POST" action="index.php" onSubmit="return check_password(this);">
        <fieldset>
            <legend> <?php echo($lang['PROFILE_TITLE']); ?></legend>
            <input name="action" type="hidden" value="member_modify_member">

            <label for="pseudo"><?php echo $lang['PROFILE_PSEUDO'] . help("profile_login"); ?></label>
            <input  name="pseudo"  id="pseudo"  type="text" size="20" maxlength="20" value="<?php echo $user_name; ?>">

            <label for="old_password"><?php echo($lang['PROFILE_OLDPWD']); ?></label>
            <input name="old_password" id="old_password" type="password" autocomplete="off" size="20" maxlength="64">

            <label for="new_password"><?php echo $lang['PROFILE_NEWPWD'] . help("profile_password"); ?></label>
            <input name="new_password" id="new_password" type="password" autocomplete="off" size="20" maxlength="64">

            <label for="new_password2"><?php echo $lang['PROFILE_NEWPWDAGAIN'] . help("profile_login"); ?></label>
            <input name="new_password2" id="new_password2" type="password" autocomplete="off" size="20" maxlength="64">

            <label for="pseudo_email"><?php echo $lang['PROFILE_EMAIL'] . help("profile_pseudo_email"); ?></label>
            <input name="pseudo_email" id="pseudo_email" type="text" size="32" value="<?php echo $user_email; ?>">

            <label for="pseudo_user_token"><?php echo $lang['PROFILE_TOKEN'] . help("profile_token"); ?></label>
            <input name="pseudo_user_token" id="pseudo_user_token" type="text" size="32" maxlength="64" value="<?php echo $user_token_displayed; ?>">

            <label for="renew_user_token"><?php echo $lang['PROFILE_TOKEN_UPDATE']; ?></label>
            <input name="renew_user_token" id="renew_user_token" value="1" type="checkbox" >
        </fieldset>
        <fieldset>
            <legend> <?php echo($lang['PROFILE_GAME']); ?></legend>
            <label for="galaxy"><?php echo $lang['PROFILE_MAINPLANET'] . help('profile_main_planet'); ?></label>
            <div class="inputgalaxy" >
                    <input name="galaxy" id="galaxy"  type="text" size="3" maxlength="2" value="<?php echo $user_galaxy; ?>"> :
                    <input name="system" id="system" type="text" size="3" maxlength="3" value="<?php echo $user_system; ?>">
            </div>
            <label for="pseudo_ingame"><?php echo $lang['PROFILE_PLAYERNAME'] . help('profile_pseudo_ingame'); ?></label>
            <input name="pseudo_ingame" id="pseudo_ingame" type="text" size="20" value="<?php echo $user_stat_name; ?>">

            <label for="user_class"><?php echo $lang['PROFILE_CLASS']; ?></label>
<?php
    //todo aucun lieu de centralisation de ce type de donnée magique !!! fichier de conf ogame ?
    $classType = array('none','COL','GEN','EXP');
?>
            <select name='user_class' id='user_class'>
<?php foreach  ($classType as $class) {
    // echo $class . '__' . $user_class;
    $selected = '';
    if(trim($class) == trim($user_class)) {
        $selected = 'selected="selected"';
    }
?>
                <option value="<?php echo $class; ?>" <?php echo $selected ?>><?php echo $lang['PROFILE_CLASS_'.strtoupper($class)]; ?></option>
<?php 
    }
?>
            </select>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['PROFILE_OFFICERS']); ?></legend>
            <label for="off_commandant"><?php echo($lang['PROFILE_CODMANDER']); ?></label>
            <input name="off_commandant" id="off_commandant" value="1" type="checkbox" <?php echo $off_commandant; ?>  />

            <label for="off_amiral"><?php echo($lang['PROFILE_ADMIRAL']); ?></label>
            <input name="off_amiral" id="off_amiral" value="1" type="checkbox" <?php echo $off_amiral; ?> />

            <label for="off_ingenieur"><?php echo($lang['PROFILE_ENGINEER']); ?></label>
            <input name="off_ingenieur" id="off_ingenieur" value="1" type="checkbox" <?php echo $off_ingenieur; ?> />

            <label for="off_geologue"><?php echo($lang['PROFILE_GEOLOGIST']); ?></label>
            <input name="off_geologue" id="off_geologue" value="1" type="checkbox" <?php echo $off_geologue; ?> />

            <label for="off_technocrate"><?php echo($lang['PROFILE_TECHNOCRAT']); ?></label>
            <input name="off_technocrate" id="off_technocrate" value="1" type="checkbox" <?php echo $off_technocrate; ?> />
         </fieldset>
        <fieldset>
            <legend><?php echo($lang['PROFILE_OTHERS']); ?></legend>
            <label for="disable_ip_check"><?php echo $lang['PROFILE_IPCHECK_DISABLE'] . help("profile_disable_ip_check"); ?></label>
            <input name="disable_ip_check" id="disable_ip_check"  value="1" type="checkbox" <?php echo $disable_ip_check; ?> />
        </fieldset>
        <input type="submit" class="button" value="<?php echo($lang['PROFILE_SAVE']); ?>">
    </form>
<?php
    require_once('views/page_tail.php');
?>