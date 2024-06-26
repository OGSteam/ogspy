<?php global $user_data, $server_config, $lang;
/**
 * Panneau administration des options d'Affichages
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author bobzer
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}


//todo sortir requete de la vue
$mod_model = new \Ogsteam\Ogspy\Model\Mod_Model();
$tMods = $mod_model->find_by();

$galaxy_by_line_stat = $server_config['galaxy_by_line_stat'];
$system_by_line_stat = $server_config['system_by_line_stat'];
$enable_stat_view = $server_config['enable_stat_view'] == 1 ? "checked" : "";
$enable_members_view = $server_config['enable_members_view'] == 1 ? "checked" : "";
$galaxy_by_line_ally = $server_config['galaxy_by_line_ally'];
$system_by_line_ally = $server_config['system_by_line_ally'];
$nb_colonnes_ally = $server_config['nb_colonnes_ally'];
$enable_register_view = $server_config['enable_register_view'] == 1 ? "checked" : "";
$register_forum = $server_config['register_forum'];
$register_alliance = $server_config['register_alliance'];
$enable_portee_missil = $server_config['portee_missil'] == 1 ? "checked" : "";
$open_user = $server_config['open_user'];
$open_admin = $server_config['open_admin'];

$color_ally_n = $server_config['color_ally'];
$color_ally_e = explode("_", $color_ally_n);
?>
<form method="POST" action="index.php" name="view">
    <input type="hidden" name="action" value="set_server_view">
    <table class="og-table og-medium-table">
        <thead>
            <tr>
                <th colspan="2">
                    <?= ($lang['ADMIN_DISPLAY_GALAXY_TITLE']) ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat"><?= ($lang['ADMIN_DISPLAY_GALAXY_MIPS']) ?>
                    <?= help("display_mips") ?>
                </td>
                <td class="tdvalue">
                    <input name="enable_portee_missil" type="checkbox" value="1" <?= $enable_portee_missil ?> onClick="if (view.enable_portee_missil.checked == false)
                                view.enable_portee_missil.checked = false;">
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan="2">
                    <?= ($lang['ADMIN_DISPLAY_STATS_TITLE']) ?>
                </th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_STATS_MEMBER']) ?>
                    <?= help("member_stats") ?>
                </td>
                <td class="tdvalue">
                    <input name="enable_stat_view" type="checkbox" value="1" <?= $enable_stat_view ?> onClick="if (view.enable_stat_view.checked == false)
                            view.enable_members_view.checked = false;">
                </td>
            </tr>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_STATS_CONNECTED']) ?>
                    <?= help("member_connected") ?>
                </td>
                <td class="tdvalue">
                    <input name="enable_members_view" type="checkbox" value="1" <?= $enable_members_view ?> onClick="if (view.enable_stat_view.checked == false)
                                view.enable_members_view.checked = false;">
                </td>
            </tr>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_STATS_GVIEW']) ?>
                </td>
                <td class="tdvalue">
                    <input name="galaxy_by_line_stat" type="text" size="5" maxlength="3" value="<?= $galaxy_by_line_stat ?>">
                </td>
            </tr>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_STATS_SVIEW']) ?>
                </td>
                <td class="tdvalue">
                    <input name="system_by_line_stat" type="text" size="5" maxlength="3" value="<?= $system_by_line_stat ?>">
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th colspan=2">
                    <?= ($lang['ADMIN_DISPLAY_ALLY_TITLE']) ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_ALLY_COLUMS']) ?>
                </td>
                <td class="tdvalue">
                    <input name="nb_colonnes_ally" type="text" size="3" maxlength="20" value="<?= $nb_colonnes_ally ?>">
                </td>
            </tr>
            <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) : ?>
                <?php $color_input = color_html_create_double_input('color_ally[' . $i . ']', $color_ally_e[$i - 1]); ?>
                <tr>
                    <th class="tdstat">
                        <span style="color: <?= $color_ally_e[$i - 1] ?>; ">
                            <?= ($lang['ADMIN_DISPLAY_ALLY_COLOR']) ?><?= $i ?>
                        </span>
                        <br />
                         <i class="og-warning"><?= ($lang['ADMIN_DISPLAY_ALLY_COLORDESC']) ?></i>
                    </th>
                    <td class="tdvalue"><?= $color_input ?></td>
                </tr>
            <?php endfor; ?>
        <tr>
            <td class="tdstat"><?= ($lang['ADMIN_DISPLAY_ALLY_GVIEW']) ?></td>
            <td class="tdvalue">
                <input name="galaxy_by_line_ally" type="text" size="5" maxlength="3" value="<?= $galaxy_by_line_ally ?>">
                </td>
        </tr>
        <tr>
            <td class="tdstat">
                <?= ($lang['ADMIN_DISPLAY_ALLY_SVIEW']) ?>
            </td>
            <td class="tdvalue">
                <input name="system_by_line_ally" type="text" size="5" maxlength="3" value="<?= $system_by_line_ally ?>">
            </td>
        </tr>
        </tbody>
                <thead>
            <tr>
                <th colspan=2">
                    <?= ($lang['ADMIN_DISPLAY_LOGIN_TITLE']) ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_LOGIN_REGISTER']) ?>
                    <?= help("member_registration") ?>
                   </td>
                <td class="tdvalue">
                    <input name="enable_register_view" type="checkbox" value="1" <?= $enable_register_view ?> onClick="if (view.enable_register_view.checked == false)
                        view.enable_members_view.checked = false;">
                </td>
            </tr>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_LOGIN_ALLYNAME']) ?>
                    <?= help("ally_name") ?>
                </td>
                <td class="tdvalue">
                    <input type="text" size="60" name="register_alliance" value="<?= $register_alliance ?>">
                </td>
            </tr>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_DISPLAY_LOGIN_FORUM']) ?><?= help("forum_link") ?>
                </td>
                <td class="tdvalue">
                    <input type="text" size="60" name="register_forum" value="<?= $register_forum ?>">
                </td>
            </tr>
                    <tr>
            <td class="tdstat">
                <?= ($lang['ADMIN_DISPLAY_LOGIN_MODULE']) ?><?= help("first_displayed_module") ?>
            </td>
            <td class="tdvalue"><select name="open_user">
                    <?php  $selectedTag = ($open_user == "./views/profile.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/profile.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_PROFILE'] ?></option>
                    <?php  $selectedTag = ($open_user == "./views/home.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/home.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_ACCOUNT'] ?></option>
                    <?php  $selectedTag = ($open_user == "./views/galaxy.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/galaxy.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_GALAXY'] ?></option>
                    <?php  $selectedTag = ($open_user == "./views/cartography.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/cartography.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_ALLY'] ?></option>
                    <?php  $selectedTag = ($open_user == "./views/search.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/search.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_SEARCH'] ?></option>
                    <?php  $selectedTag = ($open_user == "./views/ranking.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/ranking.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_RANKINGS'] ?></option>
                    <?php  $selectedTag = ($open_user == "./views/statistic.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/statistic.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_STATS'] ?></option>
                    <?php $selectedTag = ($open_user == "./views/galaxy_obsolete.php") ? "selected" : ""; ?>
                    <option <?= $selectedTag ?> value="./views/galaxy_obsolete.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_TOBEUPDATED'] ?></option>
                    <option>------</option>
                    <?php foreach ($tMods as $mod) : ?>
                        <?php if ($mod["admin_only"] == 0) : ?>
                            <?php $selectedTag = ($open_user == "./mod/" . $mod['root'] . "/" . $mod['link'] . "") ? "selected" : ""; ?>
                            <option <?= $selectedTag ?> value="<?= "./mod/" . $mod['root'] . "/" . $mod['link'] ?>"><?= $mod["title"] ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
             <td class="tdstat">
                 <?= ($lang['ADMIN_DISPLAY_LOGIN_ADMINMODULE']) ?><?= help("first_displayed_module_admin") ?>
             </td>
             <td class="tdvalue">
                 <select name="open_admin">
                     <?php  $selectedTag = ($open_admin == "./views/profile.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/profile.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_PROFILE'] ?></option>
                    <?php  $selectedTag = ($open_admin == "./views/home.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/home.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_ACCOUNT'] ?></option>
                    <?php  $selectedTag = ($open_admin == "./views/galaxy.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/galaxy.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_GALAXY'] ?></option>
                    <?php  $selectedTag = ($open_admin == "./views/cartography.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/cartography.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_ALLY'] ?></option>
                    <?php  $selectedTag = ($open_admin == "./views/search.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/search.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_SEARCH'] ?></option>
                    <?php  $selectedTag = ($open_admin == "./views/ranking.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/ranking.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_RANKINGS'] ?></option>
                    <?php  $selectedTag = ($open_admin == "./views/statistic.php") ? "selected" : "" ;?>
                    <option <?= $selectedTag ?> value="./views/statistic.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_STATS'] ?></option>
                    <?php $selectedTag = ($open_admin == "./views/galaxy_obsolete.php") ? "selected" : ""; ?>
                    <option <?= $selectedTag ?> value="./views/galaxy_obsolete.php"><?= $lang['ADMIN_DISPLAY_LOGIN_MODULE_TOBEUPDATED'] ?></option>
                    <?php foreach (array(0, 1) as $isadmin) : ?>
                        <option>------</option>
                        <?php foreach ($tMods as $mod) : ?>
                            <?php if ($mod["admin_only"] == $isadmin) : ?>
                                <?php $selectedTag = ($open_admin == "./mod/" . $mod['root'] . "/" . $mod['link'] . "") ? "selected" : ""; ?>
                                <option <?= $selectedTag ?> value="<?= "./mod/" . $mod['root'] . "/" . $mod['link'] ?>"><?= $mod["title"] ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                 </select>
             </td>

        </tr>
        <tr>
            <td colspan="2">
                <input class="og-button" type="submit" value="<?= ($lang['ADMIN_DISPLAY_SUBMIT']) ?>">
                <input class="og-button og-button-warning " type="reset" value="<?= ($lang['ADMIN_DISPLAY_RESET']) ?>">
            </td>
        </tr>
        </tbody>
    </table>
</form>
