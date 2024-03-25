<?php
/**
 * Panneau d'Administration : paramètres et options du serveur
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

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

//check mail
if (isset($pub_testmail)) {
    sendMail($user_data["user_email"], "TEST", "<h1>TEST OK</h1>");
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
$num_of_galaxies = (isset($pub_num_of_galaxies)) ? $pub_num_of_galaxies : $server_config['num_of_galaxies'];
$num_of_systems = (isset($pub_num_of_systems)) ? $pub_num_of_systems : $server_config['num_of_systems'];
$block_ratio = $server_config['block_ratio'] == 1 ? "checked" : "";
$ratio_limit = $server_config['ratio_limit'];
$speed_uni = $server_config['speed_uni'];
$speed_fleet_peaceful = $server_config['speed_fleet_peaceful'];
$speed_fleet_war = $server_config['speed_fleet_war'];
$speed_fleet_holding = $server_config['speed_fleet_holding'];
$speed_research_divisor = $server_config['speed_research_divisor'];
$ddr = $server_config['ddr'];
$astro_strict = $server_config['astro_strict'];
$config_cache = $server_config['config_cache'];
$mod_cache = $server_config['mod_cache'];
$donutSystem = $server_config['donutSystem'];
$donutGalaxy = $server_config['donutGalaxy'];
//mail
$server_config['mail_use'] = (isset($server_config['mail_use'])) ? $server_config['mail_use'] : 0;
$mail_use = $server_config['mail_use'] == 1 ? "checked" : "";
$server_config['mail_smtp_use'] = (isset($server_config['mail_smtp_use'])) ? $server_config['mail_smtp_use'] : 0;
$mail_smtp_use = $server_config['mail_smtp_use'] == 1 ? "checked" : "";
$server_config['mail_smtp_secure'] = (isset($server_config['mail_smtp_secure'])) ? $server_config['mail_smtp_secure'] : 0;
$mail_smtp_secure = $server_config['mail_smtp_secure'] == 1 ? "checked" : "";
$server_config['mail_smtp_port'] = (isset($server_config['mail_smtp_port'])) ? $server_config['mail_smtp_port'] : 0;
$mail_smtp_port = (int) $server_config['mail_smtp_port'];
$mail_smtp_host = (isset($server_config['mail_smtp_host'])) ? $server_config['mail_smtp_host'] : "";
$mail_smtp_username = (isset($server_config['mail_smtp_username'])) ? $server_config['mail_smtp_username'] : "";
$mail_smtp_password = "";
//fin mail
?>

<form method="POST" action="index.php">
    <input type="hidden" name="action" value="set_serverconfig">
    <input name="max_battlereport" type="hidden" size="5" value="10">
    <table class="og-table og-medium-table">
        <thead>
            <tr>
                <th colspan="2">
                    <?php echo ($lang['ADMIN_PARAMS_GENERAL']); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_SERVERNAME']); ?></td>
                <td class="tdvalue"><input type="text" name="servername" size="60"  value="<?php echo $servername; ?>"></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_ACTIVATESERVER']); ?><?php echo help("admin_server_status"); ?></td>
                <td class="tdvalue"><input name="server_active" type="checkbox" value="1" <?php echo $server_active; ?>></td>
            </tr>
            <tr>
                <td class="tdstat"> <?php echo ($lang['ADMIN_PARAMS_OFFREASON']); ?><?php echo help("admin_server_status_message"); ?></td>
                <td class="tdvalue"><input type="text" name="reason" size="60" value="<?php echo $reason; ?>"></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="2">
                    <?php echo ($lang['ADMIN_PARAMS_MEMBEROPTIONS']); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_ALLOW_IPCHECKDISABLING']); ?><?php echo help("admin_check_ip"); ?></td>
                <td class="tdvalue"><input name="disable_ip_check" type="checkbox" value="1" <?php echo $disable_ip_check; ?>></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAXSSFAVORITES']); ?></td>
                <td class="tdvalue"><input name="max_favorites" type="text" size="5" maxlength="2" value="<?php echo $max_favorites; ?>">
                </td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAXREFAVORITES']); ?></td>
                <td class="tdvalue"><input name="max_favorites_spy" type="text" size="5" maxlength="2" value="<?php echo $max_favorites_spy; ?>"></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="2">
                    <?php echo ($lang['ADMIN_PARAMS_SESSIONS_TITLE']); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_SESSIONS_DURATION']); ?><?php echo help("admin_session_infini"); ?></th>
                <td class="tdvalue"><input name="session_time" type="text" size="5" maxlength="3" value="<?php echo $session_time; ?>"></th>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="2">
                    <?php echo ($lang['ADMIN_PARAMS_ALLYPROTECT']); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_ALLYPROTECTLIST']); ?><br />

                    <i class="og-warning"><?php echo ($lang['ADMIN_PARAMS_ALLYPROTECTNOTICE']); ?></i>
                </td>
                <td class="tdvalue"><input type="text" size="60" name="ally_protection" value="<?php echo $ally_protection; ?>"></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_ALLYPROTECTFRIENDS']); ?>
                    <br />
                    <i class="og-warning"><?php echo ($lang['ADMIN_PARAMS_ALLYPROTECTNOTICE']); ?></i>
                </td>
                <td class="tdvalue"><input type="text" size="60" name="allied" value="<?php echo $allied; ?>"></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="2"><?php echo ($lang['ADMIN_PARAMS_OTHER']); ?></th>
            </tr> 
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_FORUMLINK']); ?></td>
                <td class="tdvalue"><input type="text" size="60" name="url_forum" value="<?php echo $url_forum; ?>"></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_DEBUGSQL']); ?><?php echo help("admin_save_transaction"); ?><br />
                    <i class="og-warning"><?php echo ($lang['ADMIN_PARAMS_DEBUGSQLALERT']); ?></i>
                </td>
                <td class="tdvalue"><input name="debug_log" type="checkbox" value="1" <?php echo $debug_log; ?>></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_RATIOMOD']); ?></td>
                <td class="tdvalue"><input name="block_ratio" type="checkbox" value="1" <?php echo $block_ratio; ?>></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_RATIOBLIMIT']); ?></td>
                <td class="tdvalue"><input name="ratio_limit" type="number" size="10" maxlength="9" value="<?php echo $ratio_limit; ?>"></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="2"><?php echo ($lang['ADMIN_PARAMS_MAIL']); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAIL_USE']); ?></td>
                <td class="tdvalue"><input name="mail_use" type="checkbox" value="1" <?php echo $mail_use; ?>></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAIL_SMTP_USE']); ?></td>
                <td class="tdvalue"><input name="mail_smtp_use" type="checkbox" value="1" <?php echo $mail_smtp_use; ?>></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAIL_SMTP_SECURE']); ?></td>
                <td class="tdvalue"><input name="mail_smtp_secure" type="checkbox" value="1" <?php echo $mail_smtp_secure; ?>></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAIL_SMTP_HOST']); ?></td>
                <td class="tdvalue"><input type="text" size="60" name="mail_smtp_host" value="<?php echo $mail_smtp_host; ?>"></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAIL_SMTP_PORT']); ?></td>
                <td class="tdvalue"><input type="text" name="mail_smtp_port" maxlength="4" size="5" value="<?php echo $mail_smtp_port; ?>"></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAIL_SMTP_USERNAME']); ?></td>
                <td class="tdvalue"><input type="text" size="30" name="mail_smtp_username" value="<?php echo $mail_smtp_username; ?>"></td>
            </tr>
            <tr>
                <td class="tdstat""><?php echo ($lang['ADMIN_PARAMS_MAIL_SMTP_PASSEWORD']); ?></td>
                <td class="tdvalue"><input type="password" size="30" name="mail_smtp_password" value="<?php echo $mail_smtp_password; ?>"> (<input type="checkbox" name="enable_mail_smtp_password" />)</td>
            </tr>
            <?php if ($server_config['mail_use'] == 1 && check_var($user_data["user_email"], "Email")) : ?>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAIL_TEST'] . $user_data["user_email"]); ?></td>
                    <td class="tdvalue"><a href="index.php?action=administration&subaction=parameter&testmail">TEST</a></td>
                </tr>
            <?php endif; ?>
        </tbody>
        <thead>
            <tr>
                <th colspan="2"><?php echo ($lang['ADMIN_PARAMS_SERVICE']); ?></th>
            </tr>  
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_DURATION_RANKS']); ?></td>
                <td class="tdvalue"><input type="text" name="max_keeprank" maxlength="4" size="5" value="<?php echo $max_keeprank; ?>">&nbsp;<select name="keeprank_criterion">
                        <option value="quantity" <?php echo $keeprank_criterion == "quantity" ? "selected" : ""; ?>><?php echo ($lang['ADMIN_PARAMS_DURATION_NUMBER']); ?></option>
                        <option value="day" <?php echo $keeprank_criterion == "day" ? "selected" : ""; ?>><?php echo ($lang['ADMIN_PARAMS_DURATION_DAYS']); ?></option>
                    </select></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_MAX_SPYREPORTS']); ?></td>
                <td class="tdvalue"><input type="text" name="max_spyreport" maxlength="4" size="5" value="<?php echo $max_spyreport; ?>">
                </td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_DURATION_SPYREPORTS']); ?></td>
                <td class="tdvalue"><input type="text" name="max_keepspyreport" maxlength="4" size="5" value="<?php echo $max_keepspyreport; ?>"></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_DURATION_LOGS']); ?></td>
                <td class="tdvalue"><input name="max_keeplog" type="text" size="5" maxlength="3" value="<?php echo $max_keeplog; ?>"></td>
            </tr>     
        </tbody>
        <?php if ($user_data["user_admin"] == 1) : ?>
            <thead>
                <tr>
                    <th colspan="2"><?php echo ($lang['ADMIN_PARAMS_GAME_OPTIONS']); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_GALAXIES']); ?><?php echo help("profile_galaxy"); ?></td>
                    <td class="tdvalue"><input name="num_of_galaxies" id="galaxies" type="text" size="5" maxlength="3" value="<?php echo $num_of_galaxies; ?>" onChange="if (!confirm('<?php echo ($lang['ADMIN_PARAMS_GAME_GALAXIES_POPUP']); ?>')) {
                                document.getElementById('galaxies').value = '<?php echo $num_of_galaxies; ?>';
                            }" readonly="readonly">(<input name="enable_input_num_galaxies" type="checkbox" onClick="(this.checked) ? document.getElementById('galaxies').readOnly = false : document.getElementById('galaxies').readOnly = true;">)
                    </td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_SYSTEMS']); ?><?php echo help("profile_galaxy"); ?></td>
                    <td class="tdvalue"><input name="num_of_systems" id="systems" type="text" size="5" maxlength="3" value="<?php echo $num_of_systems; ?>" onChange="if (!confirm('<?php echo ($lang['ADMIN_PARAMS_GAME_SYSTEMS_POPUP']); ?>')) {
                                document.getElementById('systems').value = '<?php echo $num_of_systems; ?>';
                            }" readonly="readonly">(<input name="enable_input_num_systems" type="checkbox" onClick="(this.checked) ? document.getElementById('systems').readOnly = false : document.getElementById('systems').readOnly = true;">)
                    </td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_SPEED']); ?><?php echo help("profile_speed_uni"); ?></td>
                    <td class="tdvalue"><input name="speed_uni" id="speed_uni" type="text" size="5" maxlength="2" value="<?php echo $speed_uni; ?>"></td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_SPEED_FLEET_PEACEFUL']); ?><?php echo help("profile_speed_fleet_peaceful"); ?></td>
                    <td class="tdvalue"><input name="speed_fleet_peaceful" id="speed_fleet_peaceful" type="text" size="5" maxlength="2" value="<?php echo $speed_fleet_peaceful; ?>"></td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_SPEED_FLEET_WAR']); ?><?php echo help("profile_speed_fleet_war"); ?></td>
                    <td class="tdvalue"><input name="speed_fleet_war" id="speed_fleet_war" type="text" size="5" maxlength="2" value="<?php echo $speed_fleet_war; ?>"></td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_SPEED_FLEET_HOLDING']); ?><?php echo help("profile_speed_fleet_holding"); ?></td>
                    <td class="tdvalue"><input name="speed_fleet_holding" id="speed_fleet_holding" type="text" size="5" maxlength="2" value="<?php echo $speed_fleet_holding; ?>"></td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_DDR']); ?><?php echo help("profile_ddr"); ?></td>
                    <td class="tdvalue"><input name="ddr" value="1" type="checkbox" <?php print ($ddr == 1) ? ' checked' : ''  ?>></td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_ASTRO']); ?><?php echo help("astro_strict"); ?></td>
                    <td class="tdvalue"><input name="astro_strict" value="1" type="checkbox" <?php print ($astro_strict == 1) ? ' checked' : ''  ?>></td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_DONUT_SYSTEM']); ?><?php echo help("donutSystem"); ?></td>
                    <td class="tdvalue"><input name="donutSystem" value="1" type="checkbox" <?php print ($donutSystem == 1) ? ' checked' : ''  ?>></td>
                </tr>
                <tr>
                    <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_GAME_DONUT_GALAXY']); ?><?php echo help("donutGalaxy"); ?></td>
                    <td class="tdvalue"><input name="donutGalaxy" value="1" type="checkbox" <?php print ($donutGalaxy == 1) ? ' checked' : ''  ?>></td>
                </tr>
            </tbody>
        <?php endif; ?>
        <thead>
            <tr>
                <th colspan="2"><?php echo ($lang['ADMIN_PARAMS_CACHE']); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_CACHE_RESET']); ?></td>
                <td class="tdvalue"><input name="regenere_cache" type="checkbox" value="0" /></td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_CACHE_DURATION_CONFIG']); ?> <?php echo help("config_cache"); ?> 
                    <br />
                    <i class="og-warning"><?php echo ($lang['ADMIN_PARAMS_CACHE_DURATION_NOTICE']); ?></i>
                </td>
                <td class="tdvalue"><input type="text" name="config_cache" maxlength="10" size="10" value="<?php echo $config_cache; ?>">
                </td>
            </tr>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_CACHE_DURATION_MOD']); ?><?php echo help("mod_cache"); ?></a></td>
                <td class="tdvalue"><input type="text" name="mod_cache" maxlength="10" size="10" value="<?php echo $mod_cache; ?>"></td>
            </tr>

        </tbody>
        <thead>
            <tr>
                <th colspan="2"><?php echo ($lang['ADMIN_PARAMS_DEBUG']); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?php echo ($lang['ADMIN_PARAMS_DEBUG_PHP']); ?><br />

                    <i class="og-warning"><?php echo ($lang['ADMIN_PARAMS_DEBUG_PHP_NOTICE']); ?></i>
                </td>
                <td class="tdvalue"><input name="log_phperror" type="checkbox" value="1" <?php echo $log_phperror; ?>></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input class="og-button" type="submit" value="<?php echo ($lang['ADMIN_PARAMS_VALIDATE']); ?>">
                    <input class="og-button og-button-warning " type="reset" value="<?php echo ($lang['ADMIN_PARAMS_CANCEL']); ?>">
                </td>
            </tr>
        </tbody>

    </table>

</form>
