<?php
/**
 * Panneau d'Administration : paramètres et options du serveur
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

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

//check mail
if (isset($pub_testmail))
{
    sendMail($user_data["user_email"],"TEST","<h1>TEST OK</h1>");
}


$max_battlereport = $server_config['max_battlereport'];
$max_favorites = $server_config['max_favorites'];
$max_spyreport = $server_config['max_spyreport'];
$server_active = $server_config['server_active'] == 1 ? "checked" : "";
$session_time = $server_config['session_time'];
$max_keeplog = $server_config['max_keeplog'];
$debug_log = $server_config['debug_log'] == 1 ? "checked" : "";
$log_phperror = $server_config['log_phperror'] == 1 ? "checked" : "";
$reason = $server_config['reason'];
$ally_protection = $server_config['ally_protection'];
$allied = $server_config['allied'];
$url_forum = $server_config['url_forum'];
$max_keeprank = $server_config['max_keeprank'];
$keeprank_criterion = $server_config['keeprank_criterion'];
$max_keepspyreport = $server_config['max_keepspyreport'];
$servername = $server_config['servername'];
$max_favorites_spy = $server_config['max_favorites_spy'];
$disable_ip_check = $server_config['disable_ip_check'] == 1 ? "checked" : "";
$num_of_galaxies = (isset ($pub_num_of_galaxies)) ? $pub_num_of_galaxies : $server_config['num_of_galaxies'];
$num_of_systems = (isset ($pub_num_of_systems)) ? $pub_num_of_systems : $server_config['num_of_systems'];
$block_ratio = $server_config['block_ratio'] == 1 ? "checked" : "";
$ratio_limit = $server_config['ratio_limit'];
$speed_uni = $server_config['speed_uni'];
$ddr = $server_config['ddr'];
$astro_strict = $server_config['astro_strict'];
$config_cache = $server_config['config_cache'];
$mod_cache = $server_config['mod_cache'];
//mail
$server_config['mail_use'] = (isset ($server_config['mail_use'])) ? $server_config['mail_use'] : 0;
$mail_use = $server_config['mail_use'] == 1 ? "checked" : "";
$server_config['mail_smtp_use'] = (isset ($server_config['mail_smtp_use'])) ? $server_config['mail_smtp_use'] : 0;
$mail_smtp_use = $server_config['mail_smtp_use'] == 1 ? "checked" : "";
$server_config['mail_smtp_secure'] = (isset ($server_config['mail_smtp_secure'])) ? $server_config['mail_smtp_secure'] : 0;
$mail_smtp_secure =$server_config['mail_smtp_secure'] == 1 ? "checked" : "";
$server_config['mail_smtp_port'] = (isset ($server_config['mail_smtp_port'])) ? $server_config['mail_smtp_port'] : 0;
$mail_smtp_port=(int)$server_config['mail_smtp_port'];
$mail_smtp_host=(isset ($server_config['mail_smtp_host'])) ? $server_config['mail_smtp_host'] : "";
$mail_smtp_username=(isset ($server_config['mail_smtp_username'])) ? $server_config['mail_smtp_username'] : "";
$mail_smtp_password="";
//fin mail
?>


<div class="page_adminparameter">

    <form method="POST" action="index.php">
        <input type="hidden" name="action" value="set_serverconfig">
        <!-- todo : a placer dans Maintenance ??? -->
        <input name="max_battlereport" type="hidden"  value="10">
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_GENERAL']); ?></legend>
            <label for="servername"><?php echo($lang['ADMIN_PARAMS_SERVERNAME']); ?></label>
            <input type='text'  size="60"  name='servername' id='servername'/>
            <label for="server_active"><?php echo($lang['ADMIN_PARAMS_ACTIVATESERVER']); ?> <?php echo help("admin_server_status"); ?></label>
            <input type='checkbox' value="1" <?php echo $server_active; ?>  name='server_active' id='server_active'/>
            <label for="reason"><?php echo($lang['ADMIN_PARAMS_OFFREASON']); ?><?php echo help("admin_server_status_message"); ?></label>
            <input type='text'  value="<?php echo $reason; ?>" size="60"  name='reason' id='reason'/>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_MEMBEROPTIONS']); ?></legend>
            <label for="disable_ip_check"><?php echo($lang['ADMIN_PARAMS_ALLOW_IPCHECKDISABLING']); ?> <?php echo help("admin_check_ip"); ?></label>
            <input type='checkbox' value="1" <?php echo $disable_ip_check; ?>  name='disable_ip_check' id='disable_ip_check'/>
            <label for="max_favorites"><?php echo($lang['ADMIN_PARAMS_MAXSSFAVORITES']); ?></label>
            <input type='text'  size="5" value="<?php echo $max_favorites; ?>"  name='max_favorites' id='max_favorites'/>
            <label for="max_favorites_spy"><?php echo($lang['ADMIN_PARAMS_MAXREFAVORITES']); ?></label>
            <input type='text'  size="5" value="<?php echo $max_favorites_spy; ?>"  name='max_favorites_spy' id='max_favorites_spy'/>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_SESSIONS_TITLE']); ?></legend>
            <label for="session_time">
                <?php echo($lang['ADMIN_PARAMS_SESSIONS_DURATION']); ?>
                <?php echo help("admin_session_infini"); ?>
            </label>
            <input type='text' size="5" maxlength="3"   name='session_time' id='session_time'/>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_ALLYPROTECT']); ?></legend>
            <label for="ally_protection">
                <?php echo($lang['ADMIN_PARAMS_ALLYPROTECTLIST']); ?>
                <br/>
                <?php echo($lang['ADMIN_PARAMS_ALLYPROTECTNOTICE']); ?></label>
            <input type='text'  value='<?php echo $ally_protection; ?>'  size="60"  name='ally_protection' id='ally_protection'/>
            <label for="allied">
                <?php echo($lang['ADMIN_PARAMS_ALLYPROTECTFRIENDS']); ?>
                <br/>
                <?php echo($lang['ADMIN_PARAMS_ALLYPROTECTNOTICE']); ?></label>
            <input type='text'  value='<?php echo $allied; ?>'  size="60"  name='allied' id='allied'/>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_OTHER']); ?></legend>
            <label for="url_forum"><?php echo($lang['ADMIN_PARAMS_FORUMLINK']); ?></label>
            <input type='text'  value="<?php echo $url_forum; ?>" size="60"  name='url_forum' id='url_forum'/>
            <label for="debug_log"><?php echo($lang['ADMIN_PARAMS_DEBUGSQL']); ?>
                <?php echo help("admin_save_transaction"); ?>
                <br/>
                <?php echo($lang['ADMIN_PARAMS_DEBUGSQLALERT']); ?>
            </label>
            <input type='checkbox' value="1" <?php echo $debug_log; ?>  name='debug_log' id='debug_log'/>
            <label for="block_ratio"><?php echo($lang['ADMIN_PARAMS_RATIOMOD']); ?></label>
            <input type='checkbox' value="1" <?php echo $block_ratio; ?>  name='block_ratio' id='block_ratio'/>
            <label for="ratio_limit"><?php echo($lang['ADMIN_PARAMS_RATIOMOD']); ?></label>
            <input  type="text" size="10" maxlength="9"   value="<?php echo $ratio_limit; ?>"  name='ratio_limit' id='ratio_limit'/>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_MAIL']); ?></legend>
            <label for="mail_use"><?php echo($lang['ADMIN_PARAMS_DEBUGSQL']); ?></label>
            <input type='checkbox' value="1" <?php echo $mail_use; ?>  name='mail_use' id='mail_use'/>
            <label for="mail_smtp_use"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_USE']); ?></label>
            <input type='checkbox' value="1" <?php echo $mail_smtp_use; ?>  name='mail_smtp_use' id='mail_smtp_use'/>
            <label for="mail_smtp_secure"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_SECURE']); ?></label>
            <input type='checkbox' value="1" <?php echo $mail_smtp_secure; ?>  name='mail_smtp_secure' id='mail_smtp_secure'/>
            <label for="mail_smtp_host"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_HOST']); ?></label>
            <input type='text'  value="<?php echo $mail_smtp_host; ?>" size="60"  name='mail_smtp_host' id='mail_smtp_host'/>
            <label for="mail_smtp_port"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_PORT']); ?></label>
            <input type='text'  value="<?php echo $mail_smtp_port; ?>" maxlength="4" size="5"  name='mail_smtp_port' id='mail_smtp_port'/>
            <label for="mail_smtp_username"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_USERNAME']); ?></label>
            <input type='text'  value="<?php echo $mail_smtp_username; ?>" size="30"  name='mail_smtp_username' id='mail_smtp_username'/>
            <label for="mail_smtp_password"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_PASSEWORD']); ?></label>
            <input type='password'  value="<?php echo $mail_smtp_username; ?>" size="30"  name='mail_smtp_password' id='mail_smtp_password'/> (<input type="checkbox"  name="enable_mail_smtp_password" />)
            <?php if ($server_config['mail_use'] == 1 && check_var($user_data["user_email"], "Email") ) : ?>
                <label for="mail_smtp_password"><?php echo($lang['ADMIN_PARAMS_MAIL_TEST'].$user_data["user_email"]); ?></label>
                <button type="button" onclick="window.open('index.php?action=administration&subaction=parameter&testmail');">
                    <?php echo($lang['ADMIN_PARAMS_MAIL_TEST']); ?>
                </button>
            <?php endif ;?>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_SERVICE']); ?></legend>
            <label for="max_keeprank"><?php echo($lang['ADMIN_PARAMS_DURATION_RANKS']); ?></label>
            <input type='text'  maxlength="4" size="5" value="<?php echo $max_keeprank; ?>"  name='max_keeprank' id='max_keeprank'/>
            <select  name="keeprank_criterion">
                <option value="quantity" <?php echo $keeprank_criterion == "quantity" ? "selected" : ""; ?>><?php echo($lang['ADMIN_PARAMS_DURATION_NUMBER']); ?></option>
                <option value="day" <?php echo $keeprank_criterion == "day" ? "selected" : ""; ?>><?php echo($lang['ADMIN_PARAMS_DURATION_DAYS']); ?></option>
            </select>
            <label for="max_spyreport"><?php echo($lang['ADMIN_PARAMS_MAX_SPYREPORTS']); ?></label>
            <input type='text'   maxlength="4" size="5" value="<?php echo $max_spyreport; ?>" name='max_spyreport' id='max_spyreport'/>
            <label for="max_keepspyreport"><?php echo($lang['ADMIN_PARAMS_DURATION_SPYREPORTS']); ?></label>
            <input type='text'   maxlength="4" size="5" value="<?php echo $max_keepspyreport; ?>" name='max_keepspyreport' id='max_keepspyreport'/>
            <label for="max_keeplog"><?php echo($lang['ADMIN_PARAMS_DURATION_LOGS']); ?></label>
            <input type='text' size="5" maxlength="3" value="<?php echo $max_keeplog; ?>" name='max_keeplog' id='max_keeplog'/>
        </fieldset>
        <?php if ($user_data["user_admin"] == 1): ?>
            <fieldset>
                <legend><?php echo($lang['ADMIN_PARAMS_GAME_OPTIONS']); ?></legend>
                <label for="galaxies"><?php echo($lang['ADMIN_PARAMS_GAME_GALAXIES']); ?><?php echo help("profile_galaxy"); ?></label>
                <input name="num_of_galaxies" id="galaxies" type="text" size="5" maxlength="3"  value="<?php echo $num_of_galaxies; ?>" onChange="if (!confirm('<?php echo($lang['ADMIN_PARAMS_GAME_GALAXIES_POPUP']); ?>')){document.getElementById('galaxies').value='<?php echo $num_of_galaxies; ?>';}"  readonly="readonly">
                (<input name="enable_input_num_galaxies" type="checkbox" onClick="(this.checked)? document.getElementById('galaxies').readOnly=false : document.getElementById('galaxies').readOnly=true;">)
                <label for="systems"><?php echo($lang['ADMIN_PARAMS_GAME_SYSTEMS']); ?><?php echo help("profile_galaxy"); ?></label>
                <input name="num_of_systems" id="systems" type="text" size="5" maxlength="3" value="<?php echo $num_of_systems; ?>" onChange="if (!confirm('<?php echo($lang['ADMIN_PARAMS_GAME_SYSTEMS_POPUP']); ?>')){document.getElementById('systems').value='<?php echo $num_of_systems; ?>';}"  readonly="readonly">
                (<input name="enable_input_num_systems" type="checkbox" onClick="(this.checked)? document.getElementById('systems').readOnly=false : document.getElementById('systems').readOnly=true;">)
                <label for="speed_uni"><?php echo($lang['ADMIN_PARAMS_GAME_SPEED']); ?><?php echo help("profile_speed_uni"); ?></label>
                <input name="speed_uni" id="speed_uni" type="text" size="5" maxlength="2" value="<?php echo $speed_uni; ?>" onChange="if (!confirm('<?php echo($lang['ADMIN_PARAMS_GAME_SPEED_POPUP']); ?>\n')){document.getElementById('speed_uni').value='<?php echo $speed_uni; ?>';}"  readonly="readonly">
                (<input name="enable_input_speed_uni" type="checkbox" onClick="(this.checked)? document.getElementById('speed_uni').readOnly=false : document.getElementById('speed_uni').readOnly=true;">)
                <label for="ddr"><?php echo($lang['ADMIN_PARAMS_GAME_DDR']); ?><?php echo help("profile_ddr"); ?></label>
                <input type='checkbox' value="1" <?php print ($ddr == 1) ? ' checked' : '' ?> name='ddr' id='ddr'/>
                <label for="astro_strict"><?php echo($lang['ADMIN_PARAMS_GAME_ASTRO']); ?><?php echo help("astro_strict"); ?></label>
                <input type='checkbox' value="1" <?php print ($astro_strict == 1) ? ' checked' : '' ?> name='astro_strict' id='astro_strict'/>
            </fieldset>
        <?php endif ; ?>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_CACHE']); ?></legend>
            <label for="regenere_cache"><?php echo($lang['ADMIN_PARAMS_CACHE_RESET']); ?></label>
            <input type='checkbox' value="0" name='regenere_cache' id='regenere_cache'/>
            <label for="config_cache">
                <?php echo($lang['ADMIN_PARAMS_CACHE_DURATION_CONFIG']); ?> <?php echo help("config_cache"); ?>
                <br/>
                <?php echo($lang['ADMIN_PARAMS_CACHE_DURATION_NOTICE']); ?>
            </label>
            <input type='text' maxlength="10" size="10" value="<?php echo $config_cache; ?>" name='config_cache' id='config_cache'/>
            <label for="mod_cache"><?php echo($lang['ADMIN_PARAMS_CACHE_DURATION_MOD']); ?><?php echo help("mod_cache"); ?></label>
            <input type='text' maxlength="10" size="10" value="<?php echo $config_cache; ?>" name='mod_cache' id='mod_cache'/>
        </fieldset>
        <fieldset>
            <legend><?php echo($lang['ADMIN_PARAMS_DEBUG']); ?></legend>
            <label for="log_phperror">
                <?php echo($lang['ADMIN_PARAMS_DEBUG_PHP']); ?>
                <br/>
                <?php echo($lang['ADMIN_PARAMS_DEBUG_PHP_NOTICE']); ?>
            </label>
            <input  type="checkbox" value="1" <?php echo $log_phperror; ?> name='log_phperror' id='log_phperror'/>

        </fieldset>
        <input class="button" type='submit' value='<?php echo($lang['LOGIN_CONNEXION_BUTTON']); ?>' />



    </form>
</div>

