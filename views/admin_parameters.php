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

<form method="POST" action="index.php">
    <input type="hidden" name="action" value="set_serverconfig">
    <input name="max_battlereport" type="hidden" size="5" value="10">
    <table width="100%">
        <tr>
            <td class="c_ogspy" colspan="2"><?php echo($lang['ADMIN_PARAMS_GENERAL']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_SERVERNAME']); ?></th>
            <th><input type="text" name="servername" size="60" value="<?php echo $servername; ?>"></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_ACTIVATESERVER']); ?><?php echo help("admin_server_status"); ?></th>
            <th><input name="server_active" type="checkbox" value="1" <?php echo $server_active; ?>></th>
        </tr>
        <tr>
            <th width="60%"<?php echo($lang['ADMIN_PARAMS_OFFREASON']); ?><?php echo help("admin_server_status_message"); ?></th>
            <th><input type="text" name="reason" size="60" value="<?php echo $reason; ?>"></th>
        </tr>
        <tr>
            <td class="c" colspan="2"><?php echo($lang['ADMIN_PARAMS_MEMBEROPTIONS']); ?></td>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_ALLOW_IPCHECKDISABLING']); ?><?php echo help("admin_check_ip"); ?></th>
            <th><input name="disable_ip_check" type="checkbox" value="1" <?php echo $disable_ip_check; ?>></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_MAXSSFAVORITES']); ?></th>
            <th><input name="max_favorites" type="text" size="5" maxlength="2" value="<?php echo $max_favorites; ?>">
            </th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_MAXREFAVORITES']); ?></th>
            <th><input name="max_favorites_spy" type="text" size="5" maxlength="2"
                       value="<?php echo $max_favorites_spy; ?>"></th>
        </tr>
        <tr>
            <td class="c_tech" colspan="2"><?php echo($lang['ADMIN_PARAMS_SESSIONS_TITLE']); ?></td>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_SESSIONS_DURATION']); ?><?php echo help("admin_session_infini"); ?></a></th>
            <th><input name="session_time" type="text" size="5" maxlength="3" value="<?php echo $session_time; ?>"></th>
        </tr>
        <tr>
            <td class="c" colspan="2"><?php echo($lang['ADMIN_PARAMS_ALLYPROTECT']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_ALLYPROTECTLIST']); ?><br/>

                <div class="z"><i><?php echo($lang['ADMIN_PARAMS_ALLYPROTECTNOTICE']); ?></i></div>
            </th>
            <th><input type="text" size="60" name="ally_protection" value="<?php echo $ally_protection; ?>"></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_ALLYPROTECTFRIENDS']); ?><br/>

                <div class="z"><i><?php echo($lang['ADMIN_PARAMS_ALLYPROTECTNOTICE']); ?></i></div>
            </th>
            <th><input type="text" size="60" name="allied" value="<?php echo $allied; ?>"></th>
        </tr>
        <tr>
            <td class="c" colspan="2"><?php echo($lang['ADMIN_PARAMS_OTHER']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_FORUMLINK']); ?></th>
            <th><input type="text" size="60" name="url_forum" value="<?php echo $url_forum; ?>"></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_DEBUGSQL']); ?><?php echo help("admin_save_transaction"); ?><br/>

                <div class="z"><i><?php echo($lang['ADMIN_PARAMS_DEBUGSQLALERT']); ?></i></div>
            </th>
            <th><input name="debug_log" type="checkbox" value="1" <?php echo $debug_log; ?>></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_RATIOMOD']); ?></th>
            <th><input name="block_ratio" type="checkbox" value="1" <?php echo $block_ratio; ?>></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_RATIOBLIMIT']); ?></th>
            <th><input name="ratio_limit" type="text" size="10" maxlength="9" value="<?php echo $ratio_limit; ?>"></th>
        </tr>
        <tr>
            <td class="c_tech" colspan="2"><?php echo($lang['ADMIN_PARAMS_MAIL']); ?></td>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_MAIL_USE']); ?></th>
            <th><input name="mail_use" type="checkbox" value="1" <?php echo $mail_use; ?>></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_USE']); ?></th>
            <th><input name="mail_smtp_use" type="checkbox" value="1" <?php echo $mail_smtp_use; ?>></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_SECURE']); ?></th>
            <th><input name="mail_smtp_secure" type="checkbox" value="1" <?php echo $mail_smtp_secure; ?>></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_HOST']); ?></th>
            <th><input type="text" size="60" name="mail_smtp_host" value="<?php echo $mail_smtp_host; ?>"></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_PORT']); ?></th>
            <th><input type="text" name="mail_smtp_port" maxlength="4" size="5" value="<?php echo $mail_smtp_port; ?>"></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_USERNAME']); ?></th>
            <th><input type="text" size="30" name="mail_smtp_username" value="<?php echo $mail_smtp_username; ?>"></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_MAIL_SMTP_PASSEWORD']); ?></th>
            <th><input type="password" size="30" name="mail_smtp_password" value="<?php echo $mail_smtp_password; ?>"> (<input type="checkbox"  name="enable_mail_smtp_password" />)</th>
        </tr>
        <?php if ($server_config['mail_use'] == 1 && check_var($user_data["user_email"], "Email") ) : ?>
            <tr>
                <th width="60%"><?php echo($lang['ADMIN_PARAMS_MAIL_TEST'].$user_data["user_email"]); ?></th>
                <th><a href="index.php?action=administration&subaction=parameter&testmail">TEST</a></th>
            </tr>
        <?php endif ;?>
        <tr>
            <td class="c_tech" colspan="2"><?php echo($lang['ADMIN_PARAMS_SERVICE']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_DURATION_RANKS']); ?></th>
            <th><input type="text" name="max_keeprank" maxlength="4" size="5" value="<?php echo $max_keeprank; ?>">&nbsp;<select
                    name="keeprank_criterion">
                    <option value="quantity" <?php echo $keeprank_criterion == "quantity" ? "selected" : ""; ?>><?php echo($lang['ADMIN_PARAMS_DURATION_NUMBER']); ?></option>
                    <option value="day" <?php echo $keeprank_criterion == "day" ? "selected" : ""; ?>><?php echo($lang['ADMIN_PARAMS_DURATION_DAYS']); ?></option>
                </select></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_MAX_SPYREPORTS']); ?></th>
            <th><input type="text" name="max_spyreport" maxlength="4" size="5" value="<?php echo $max_spyreport; ?>">
            </th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_DURATION_SPYREPORTS']); ?></th>
            <th><input type="text" name="max_keepspyreport" maxlength="4" size="5"
                       value="<?php echo $max_keepspyreport; ?>"></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_DURATION_LOGS']); ?></th>
            <th><input name="max_keeplog" type="text" size="5" maxlength="3" value="<?php echo $max_keeplog; ?>"></th>
        </tr>
        <?php
        if ($user_data["user_admin"] == 1) {
    ?>
        <tr>
            <td class="c_ogame" colspan="2"><?php echo($lang['ADMIN_PARAMS_GAME_OPTIONS']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_GAME_GALAXIES']); ?><?php echo help("profile_galaxy"); ?></th>
            <th><input name="num_of_galaxies" id="galaxies" type="text" size="5" maxlength="3"
                       value="<?php echo $num_of_galaxies; ?>"
                       onChange="if (!confirm('<?php echo($lang['ADMIN_PARAMS_GAME_GALAXIES_POPUP']); ?>')){document.getElementById('galaxies').value='<?php echo $num_of_galaxies; ?>';}"
                       readonly="readonly">(<input name="enable_input_num_galaxies"
                                                                                  type="checkbox"
                                                                                  onClick="(this.checked)? document.getElementById('galaxies').readOnly=false : document.getElementById('galaxies').readOnly=true;">)
            </th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_GAME_SYSTEMS']); ?><?php echo help("profile_galaxy"); ?></th>
            <th><input name="num_of_systems" id="systems" type="text" size="5" maxlength="3"
                       value="<?php echo $num_of_systems; ?>"
                       onChange="if (!confirm('<?php echo($lang['ADMIN_PARAMS_GAME_SYSTEMS_POPUP']); ?>')){document.getElementById('systems').value='<?php echo $num_of_systems; ?>';}"
                       readonly="readonly">(<input name="enable_input_num_systems"
                                                                                  type="checkbox"
                                                                                  onClick="(this.checked)? document.getElementById('systems').readOnly=false : document.getElementById('systems').readOnly=true;">)
            </th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_GAME_SPEED']); ?><?php echo help("profile_speed_uni"); ?></th>
            <th><input name="speed_uni" id="speed_uni" type="text" size="5" maxlength="2"
                       value="<?php echo $speed_uni; ?>"
                       onChange="if (!confirm('<?php echo($lang['ADMIN_PARAMS_GAME_SPEED_POPUP']); ?>\n')){document.getElementById('speed_uni').value='<?php echo $speed_uni; ?>';}"
                       readonly="readonly">(<input name="enable_input_speed_uni"
                                                                                  type="checkbox"
                                                                                  onClick="(this.checked)? document.getElementById('speed_uni').readOnly=false : document.getElementById('speed_uni').readOnly=true;">)
            </th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_GAME_DDR']); ?><?php echo help("profile_ddr"); ?></th>
            <th><input name="ddr" value="1" type="checkbox"<?php print ($ddr == 1) ? ' checked' : '' ?>></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_GAME_ASTRO']); ?><?php echo help("astro_strict"); ?></th>
            <th><input name="astro_strict" value="1"
                       type="checkbox"<?php print ($astro_strict == 1) ? ' checked' : '' ?>></th>
        </tr>
        <tr>
            <?php
            }
            ?>
        <tr>
            <td class="c_tech" colspan="2"><?php echo($lang['ADMIN_PARAMS_CACHE']); ?></td>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_CACHE_RESET']); ?><br/></th>
            <th><input name="regenere_cache" type="checkbox" value="0"/></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_CACHE_DURATION_CONFIG']); ?> <?php echo help("config_cache"); ?> <br/>

                <div class="z"><i><?php echo($lang['ADMIN_PARAMS_CACHE_DURATION_NOTICE']); ?></i></div>
            </th>
            <th><input type="text" name="config_cache" maxlength="10" size="10" value="<?php echo $config_cache; ?>">
            </th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_PARAMS_CACHE_DURATION_MOD']); ?><?php echo help("mod_cache"); ?></a></th>
            <th><input type="text" name="mod_cache" maxlength="10" size="10" value="<?php echo $mod_cache; ?>"></th>
        </tr>

        <tr>
            <td class="c_tech" colspan="2"><?php echo($lang['ADMIN_PARAMS_DEBUG']); ?></td>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_PARAMS_DEBUG_PHP']); ?><br/>

                <div class="z"><i><?php echo($lang['ADMIN_PARAMS_DEBUG_PHP_NOTICE']); ?></i></div>
            </th>
            <th><input name="log_phperror" type="checkbox" value="1" <?php echo $log_phperror; ?>></th>

        </tr>
        <?php
        if ($user_data["user_admin"] == 1) {
            ?>
            <tr>
                <td class="c_ogame" colspan="2"><?php echo($lang['ADMIN_PARAMS_GOOGLE_CLOUD']); ?>
                    <div class="z"><i><?php echo($lang['ADMIN_PARAMS_GOOGLE_NOTIF']); ?></i></div>
                </td>
            </tr>
            <tr>
                <th colspan="2"><?php require_once 'gcm_users.php'; ?></th>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th colspan="2"><input type="submit" value="<?php echo($lang['ADMIN_PARAMS_VALIDATE']); ?>">&nbsp;<input type="reset" value="<?php echo($lang['ADMIN_PARAMS_CANCEL']); ?>"></th>
        </tr>
    </table>
</form>
