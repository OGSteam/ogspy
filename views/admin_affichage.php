<?php
/**
 * Panneau administration des options d'Affichages
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author bobzer
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy;

use Ogsteam\Ogspy\Model\Mod_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

$modRepository = new Mod_Model();
$mods = $modRepository->find_by(array('active' => '1'), array('position' => 'ASC'));

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

for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
    $help["color_picker$i"] = "<table border=0 cellspacing=1 cellpadding=0 bgcolor=#000000 style=\"cursor: hand;\" onclick=Set($i)><tr height=10><td bgcolor=black onMouseOver=View('black') width=10></td><td bgcolor=#000000 onMouseOver=View('#000000') width=10></td><td bgcolor=#001900 onMouseOver=View('#001900') width=10></td><td bgcolor=#003300 onMouseOver=View('#003300') width=10></td><td bgcolor=#006600 onMouseOver=View('#006600') width=10></td><td bgcolor=#009900 onMouseOver=View('#009900') width=10></td><td bgcolor=#00CC00 onMouseOver=View('#00CC00') width=10></td><td bgcolor=#00FF00 onMouseOver=View('#00FF00') width=10></td><td bgcolor=#330000 onMouseOver=View('#330000') width=10></td><td bgcolor=#333300 onMouseOver=View('#333300') width=10></td><td bgcolor=#336600 onMouseOver=View('#336600') width=10></td><td bgcolor=#339900 onMouseOver=View('#339900') width=10></td><td bgcolor=#33CC00 onMouseOver=View('#33CC00') width=10></td><td bgcolor=#33FF00 onMouseOver=View('#33FF00') width=10></td><td bgcolor=#660000 onMouseOver=View('#660000') width=10></td><td bgcolor=#663300 onMouseOver=View('#663300') width=10></td><td bgcolor=#666600 onMouseOver=View('#666600') width=10></td><td bgcolor=#669900 onMouseOver=View('#669900') width=10></td><td bgcolor=#66CC00 onMouseOver=View('#66CC00') width=10></td><td bgcolor=#66FF00 onMouseOver=View('#66FF00') width=10></td></tr><tr height=10><td bgcolor=darkgray onMouseOver=View('darkgray')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#000033 onMouseOver=View('#000033')></td><td bgcolor=#003333 onMouseOver=View('#003333')></td><td bgcolor=#006633 onMouseOver=View('#006633')></td><td bgcolor=#009933 onMouseOver=View('#009933')></td><td bgcolor=#00CC33 onMouseOver=View('#00CC33')></td><td bgcolor=#00FF33 onMouseOver=View('#00FF33')></td><td bgcolor=#330033 onMouseOver=View('#330033')></td><td bgcolor=#333333 onMouseOver=View('#333333')></td><td bgcolor=#336633 onMouseOver=View('#336633')></td><td bgcolor=#339933 onMouseOver=View('#339933')></td><td bgcolor=#33CC33 onMouseOver=View('#33CC33')></td><td bgcolor=#33FF33 onMouseOver=View('#33FF33')></td><td bgcolor=#660033 onMouseOver=View('#660033')></td><td bgcolor=#663333 onMouseOver=View('#663333')></td><td bgcolor=#666633 onMouseOver=View('#666633')></td><td bgcolor=#669933 onMouseOver=View('#669933')></td><td bgcolor=#66CC33 onMouseOver=View('#66CC33')></td><td bgcolor=#66FF33 onMouseOver=View('#66FF33')></td></tr><tr height=10><td bgcolor=gray onMouseOver=View('gray')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#000066 onMouseOver=View('#000066')></td><td bgcolor=#003366 onMouseOver=View('#003366')></td><td bgcolor=#006666 onMouseOver=View('#006666')></td><td bgcolor=#009966 onMouseOver=View('#009966')></td><td bgcolor=#00CC66 onMouseOver=View('#00CC66')></td><td bgcolor=#00FF66 onMouseOver=View('#00FF66')></td><td bgcolor=#330066 onMouseOver=View('#330066')></td><td bgcolor=#333366 onMouseOver=View('#333366')></td><td bgcolor=#336666 onMouseOver=View('#336666')></td><td bgcolor=#339966 onMouseOver=View('#339966')></td><td bgcolor=#33CC66 onMouseOver=View('#33CC66')></td><td bgcolor=#33FF66 onMouseOver=View('#33FF66')></td><td bgcolor=#660066 onMouseOver=View('#660066')></td><td bgcolor=#663366 onMouseOver=View('#663366')></td><td bgcolor=#666666 onMouseOver=View('#666666')></td><td bgcolor=#669966 onMouseOver=View('#669966')></td><td bgcolor=#66CC66 onMouseOver=View('#66CC66')></td><td bgcolor=#66FF66 onMouseOver=View('#66FF66')></td></tr><tr height=10><td bgcolor=#999999 onMouseOver=View('#999999')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#000099 onMouseOver=View('#000099')></td><td bgcolor=#003399 onMouseOver=View('#003399')></td><td bgcolor=#006699 onMouseOver=View('#006699')></td><td bgcolor=#009999 onMouseOver=View('#009999')></td><td bgcolor=#00CC99 onMouseOver=View('#00CC99')></td><td bgcolor=#00FF99 onMouseOver=View('#00FF99')></td><td bgcolor=#330099 onMouseOver=View('#330099')></td><td bgcolor=#333399 onMouseOver=View('#333399')></td><td bgcolor=#336699 onMouseOver=View('#336699')></td><td bgcolor=#339999 onMouseOver=View('#339999')></td><td bgcolor=#33CC99 onMouseOver=View('#33CC99')></td><td bgcolor=#33FF99 onMouseOver=View('#33FF99')></td><td bgcolor=#660099 onMouseOver=View('#660099')></td><td bgcolor=#663399 onMouseOver=View('#663399')></td><td bgcolor=#666699 onMouseOver=View('#666699')></td><td bgcolor=#669999 onMouseOver=View('#669999')></td><td bgcolor=#66CC99 onMouseOver=View('#66CC99')></td><td bgcolor=#66FF99 onMouseOver=View('#66FF99')></td></tr><tr height=10><td bgcolor=#CCCCCC onMouseOver=View('#CCCCCC')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#0000CC onMouseOver=View('#0000CC')></td><td bgcolor=#0033CC onMouseOver=View('#0033CC')></td><td bgcolor=#0066CC onMouseOver=View('#0066CC')></td><td bgcolor=#0099CC onMouseOver=View('#0099CC')></td><td bgcolor=#00CCCC onMouseOver=View('#00CCCC')></td><td bgcolor=#00FFCC onMouseOver=View('#00FFCC')></td><td bgcolor=#3300CC onMouseOver=View('#3300CC')></td><td bgcolor=#3333CC onMouseOver=View('#3333CC')></td><td bgcolor=#3366CC onMouseOver=View('#3366CC')></td><td bgcolor=#3399CC onMouseOver=View('#3399CC')></td><td bgcolor=#33CCCC onMouseOver=View('#33CCCC')></td><td bgcolor=#33FFCC onMouseOver=View('#33FFCC')></td><td bgcolor=#6600CC onMouseOver=View('#6600CC')></td><td bgcolor=#6633CC onMouseOver=View('#6633CC')></td><td bgcolor=#6666CC onMouseOver=View('#6666CC')></td><td bgcolor=#6699CC onMouseOver=View('#6699CC')></td><td bgcolor=#66CCCC onMouseOver=View('#66CCCC')></td><td bgcolor=#66FFCC onMouseOver=View('#66FFCC')></td></tr><tr height=10><td bgcolor=White onMouseOver=View('White')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#0000FF onMouseOver=View('#0000FF')></td><td bgcolor=#0033FF onMouseOver=View('#0033FF')></td><td bgcolor=#0066FF onMouseOver=View('#0066FF')></td><td bgcolor=#0099FF onMouseOver=View('#0099FF')></td><td bgcolor=#00CCFF onMouseOver=View('#00CCFF')></td><td bgcolor=#00FFFF onMouseOver=View('#00FFFF')></td><td bgcolor=#3300FF onMouseOver=View('#3300FF')></td><td bgcolor=#3333FF onMouseOver=View('#3333FF')></td><td bgcolor=#3366FF onMouseOver=View('#3366FF')></td><td bgcolor=#3399FF onMouseOver=View('#3399FF')></td><td bgcolor=#33CCFF onMouseOver=View('#33CCFF')></td><td bgcolor=#33FFFF onMouseOver=View('#33FFFF')></td><td bgcolor=#6600FF onMouseOver=View('#6600FF')></td><td bgcolor=#6633FF onMouseOver=View('#6633FF')></td><td bgcolor=#6666FF onMouseOver=View('#6666FF')></td><td bgcolor=#6699FF onMouseOver=View('#6699FF')></td><td bgcolor=#66CCFF onMouseOver=View('#66CCFF')></td><td bgcolor=#66FFFF onMouseOver=View('#66FFFF')></td></tr><tr height=10><td bgcolor=Red onMouseOver=View('Red')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#990000 onMouseOver=View('#990000')></td><td bgcolor=#993300 onMouseOver=View('#993300')></td><td bgcolor=#996600 onMouseOver=View('#996600')></td><td bgcolor=#999900 onMouseOver=View('#999900')></td><td bgcolor=#99CC00 onMouseOver=View('#99CC00')></td><td bgcolor=#99FF00 onMouseOver=View('#99FF00')></td><td bgcolor=#CC0000 onMouseOver=View('#CC0000')></td><td bgcolor=#CC3300 onMouseOver=View('#CC3300')></td><td bgcolor=#CC6600 onMouseOver=View('#CC6600')></td><td bgcolor=#CC9900 onMouseOver=View('#CC9900')></td><td bgcolor=#CCCC00 onMouseOver=View('#CCCC00')></td><td bgcolor=#CCFF00 onMouseOver=View('#CCFF00')></td><td bgcolor=#FF0000 onMouseOver=View('#FF0000')></td><td bgcolor=#FF3300 onMouseOver=View('#FF3300')></td><td bgcolor=#FF6600 onMouseOver=View('#FF6600')></td><td bgcolor=#FF9900 onMouseOver=View('#FF9900')></td><td bgcolor=#FFCC00 onMouseOver=View('#FFCC00')></td><td bgcolor=#FFFF00 onMouseOver=View('#FFFF00')></td></tr><tr height=10><td bgcolor=#Green onMouseOver=View('Green')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#990033 onMouseOver=View('#990033')></td><td bgcolor=#993333 onMouseOver=View('#993333')></td><td bgcolor=#996633 onMouseOver=View('#996633')></td><td bgcolor=#999933 onMouseOver=View('#999933')></td><td bgcolor=#99CC33 onMouseOver=View('#99CC33')></td><td bgcolor=#99FF33 onMouseOver=View('#99FF33')></td><td bgcolor=#CC0033 onMouseOver=View('#CC0033')></td><td bgcolor=#CC3333 onMouseOver=View('#CC3333')></td><td bgcolor=#CC6633 onMouseOver=View('#CC6633')></td><td bgcolor=#CC9933 onMouseOver=View('#CC9933')></td><td bgcolor=#CCCC33 onMouseOver=View('#CCCC33')></td><td bgcolor=#CCFF33 onMouseOver=View('#CCFF33')></td><td bgcolor=#FF0033 onMouseOver=View('#FF0033')></td><td bgcolor=#FF3333 onMouseOver=View('#FF3333')></td><td bgcolor=#FF6633 onMouseOver=View('#FF6633')></td><td bgcolor=#FF9933 onMouseOver=View('#FF9933')></td><td bgcolor=#FFCC33 onMouseOver=View('#FFCC33')></td><td bgcolor=#FFFF33 onMouseOver=View('#FFFF33')></td></tr><tr height=10><td bgcolor=Blue onMouseOver=View('Blue')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#990066 onMouseOver=View('#990066')></td><td bgcolor=#993366 onMouseOver=View('#993366')></td><td bgcolor=#996666 onMouseOver=View('#996666')></td><td bgcolor=#999966 onMouseOver=View('#999966')></td><td bgcolor=#99CC66 onMouseOver=View('#99CC66')></td><td bgcolor=#99FF66 onMouseOver=View('#99FF66')></td><td bgcolor=#CC0066 onMouseOver=View('#CC0066')></td><td bgcolor=#CC3366 onMouseOver=View('#CC3366')></td><td bgcolor=#CC6666 onMouseOver=View('#CC6666')></td><td bgcolor=#CC9966 onMouseOver=View('#CC9966')></td><td bgcolor=#CCCC66 onMouseOver=View('#CCCC66')></td><td bgcolor=#CCFF66 onMouseOver=View('#CCFF66')></td><td bgcolor=#FF0066 onMouseOver=View('#FF0066')></td><td bgcolor=#FF3366 onMouseOver=View('#FF3366')></td><td bgcolor=#FF6666 onMouseOver=View('#FF6666')></td><td bgcolor=#FF9966 onMouseOver=View('#FF9966')></td><td bgcolor=#FFCC66 onMouseOver=View('#FFCC66')></td><td bgcolor=#FFFF66 onMouseOver=View('#FFFF66')></td></tr><tr height=10><td bgcolor=Yellow onMouseOver=View('Yellow')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#990099 onMouseOver=View('#990099')></td><td bgcolor=#993399 onMouseOver=View('#993399')></td><td bgcolor=#996699 onMouseOver=View('#996699')></td><td bgcolor=#999999 onMouseOver=View('#999999')></td><td bgcolor=#99CC99 onMouseOver=View('#99CC99')></td><td bgcolor=#99FF99 onMouseOver=View('#99FF99')></td><td bgcolor=#CC0099 onMouseOver=View('#CC0099')></td><td bgcolor=#CC3399 onMouseOver=View('#CC3399')></td><td bgcolor=#CC6699 onMouseOver=View('#CC6699')></td><td bgcolor=#CC9999 onMouseOver=View('#CC9999')></td><td bgcolor=#CCCC99 onMouseOver=View('#CCCC99')></td><td bgcolor=#CCFF99 onMouseOver=View('#CCFF99')></td><td bgcolor=#FF0099 onMouseOver=View('#FF0099')></td><td bgcolor=#FF3399 onMouseOver=View('#FF3399')></td><td bgcolor=#FF6699 onMouseOver=View('#FF6699')></td><td bgcolor=#FF9999 onMouseOver=View('#FF9999')></td><td bgcolor=#FFCC99 onMouseOver=View('#FFCC99')></td><td bgcolor=#FFFF99 onMouseOver=View('#FFFF99')></td></tr><tr height=10><td bgcolor=Cyan onMouseOver=View('Cyan')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#9900CC onMouseOver=View('#9900CC')></td><td bgcolor=#9933CC onMouseOver=View('#9933CC')></td><td bgcolor=#9966CC onMouseOver=View('#9966CC')></td><td bgcolor=#9999CC onMouseOver=View('#9999CC')></td><td bgcolor=#99CCCC onMouseOver=View('#99CCCC')></td><td bgcolor=#99FFCC onMouseOver=View('#99FFCC')></td><td bgcolor=#CC00CC onMouseOver=View('#CC00CC')></td><td bgcolor=#CC33CC onMouseOver=View('#CC33CC')></td><td bgcolor=#CC66CC onMouseOver=View('#CC66CC')></td><td bgcolor=#CC99CC onMouseOver=View('#CC99CC')></td><td bgcolor=#CCCCCC onMouseOver=View('#CCCCCC')></td><td bgcolor=#CCFFCC onMouseOver=View('#CCFFCC')></td><td bgcolor=#FF00CC onMouseOver=View('#FF00CC')></td><td bgcolor=#FF33CC onMouseOver=View('#FF33CC')></td><td bgcolor=#FF66CC onMouseOver=View('#FF66CC')></td><td bgcolor=#FF99CC onMouseOver=View('#FF99CC')></td><td bgcolor=#FFCCCC onMouseOver=View('#FFCCCC')></td><td bgcolor=#FFFFCC onMouseOver=View('#FFFFCC')></td></tr><tr height=10><td bgcolor=Magenta onMouseOver=View('Magenta')></td><td bgcolor=#000000 onMouseOver=View('#000000')></td><td bgcolor=#9900FF onMouseOver=View('#9900FF')></td><td bgcolor=#9933FF onMouseOver=View('#9933FF')></td><td bgcolor=#9966FF onMouseOver=View('#9966FF')></td><td bgcolor=#9999FF onMouseOver=View('#9999FF')></td><td bgcolor=#99CCFF onMouseOver=View('#99CCFF')></td><td bgcolor=#99FFFF onMouseOver=View('#99FFFF')></td><td bgcolor=#CC00FF onMouseOver=View('#CC00FF')></td><td bgcolor=#CC33FF onMouseOver=View('#CC33FF')></td><td bgcolor=#CC66FF onMouseOver=View('#CC66FF')></td><td bgcolor=#CC99FF onMouseOver=View('#CC99FF')></td><td bgcolor=#CCCCFF onMouseOver=View('#CCCCFF')></td><td bgcolor=#CCFFFF onMouseOver=View('#CCFFFF')></td><td bgcolor=#FF00FF onMouseOver=View('#FF00FF')></td><td bgcolor=#FF33FF onMouseOver=View('#FF33FF')></td><td bgcolor=#FF66FF onMouseOver=View('#FF66FF')></td><td bgcolor=#FF99FF onMouseOver=View('#FF99FF')></td><td bgcolor=#FFCCFF onMouseOver=View('#FFCCFF')></td><td bgcolor=#FFFFFF onMouseOver=View('#FFFFFF')></td></tr><tr height=10><td colspan=20 height=\"20\"><div id=\"ColorPreview$i\" style=\"height: 100%; width: 100%\"></div></td></tr></table>";
}
?>
<script language="JavaScript">
    var colors;
    function View(color) {
        colors = color;
        <?php for ($i = 1 ; $i <= $nb_colonnes_ally ; $i++){ ?>
        document.getElementById('ColorPreview<?php echo $i; ?>').style.backgroundColor = colors;
        <?php } ?>

    }

    function Set(ally) {
        switch (ally) {
        <?php for ($i = 1 ; $i <= $nb_colonnes_ally ; $i++){  ?>
            case <?php echo $i; ?>:
                document.getElementById('color_ally[<?php echo $i; ?>]').value = colors;
                break;
        <?php } ?>
        }
    }
</script>
<form method="POST" action="index.php" name="view">
    <input type="hidden" name="action" value="set_server_view">
    <table width="100%">
        <tr>
            <td class="c_ogspy" colspan="2"><?php echo($lang['ADMIN_DISPLAY_GALAXY_TITLE']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_GALAXY_MIPS']); ?><?php echo help("display_mips"); ?></th>
            <th><input name="enable_portee_missil" type="checkbox" value="1" <?php echo $enable_portee_missil; ?>
                       onClick="if (view.enable_portee_missil.checked == false)view.enable_portee_missil.checked=false;">
            </th>
        </tr>
        <tr>
            <td class="c_ogspy" colspan="2"><?php echo($lang['ADMIN_DISPLAY_STATS_TITLE']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_STATS_MEMBER']); ?><?php echo help("member_stats"); ?></th>
            <th><input name="enable_stat_view" type="checkbox" value="1" <?php echo $enable_stat_view; ?>
                       onClick="if (view.enable_stat_view.checked == false)view.enable_members_view.checked=false;">
            </th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_STATS_CONNECTED']); ?><?php echo help("member_connected"); ?></th>
            <th><input name="enable_members_view" type="checkbox" value="1" <?php echo $enable_members_view; ?>
                       onClick="if (view.enable_stat_view.checked == false)view.enable_members_view.checked=false;">
            </th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_DISPLAY_STATS_GVIEW']); ?></th>
            <th><input name="galaxy_by_line_stat" type="text" size="5" maxlength="3"
                       value="<?php echo $galaxy_by_line_stat; ?>"></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_DISPLAY_STATS_SVIEW']); ?></th>
            <th><input name="system_by_line_stat" type="text" size="5" maxlength="3"
                       value="<?php echo $system_by_line_stat; ?>"></th>
        </tr>
        <tr>
            <td class="c_ogspy" colspan="2"><?php echo($lang['ADMIN_DISPLAY_ALLY_TITLE']); ?></td>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_DISPLAY_ALLY_COLUMS']); ?></th>
            <th><input name="nb_colonnes_ally" type="text" size="3" maxlength="20"
                       value="<?php echo $nb_colonnes_ally; ?>"></th>
        </tr>
        <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) { ?>
            <tr>
                <th><span
                        style="color: <?php echo $color_ally_e[$i - 1]; ?>; "><?php echo($lang['ADMIN_DISPLAY_ALLY_COLOR']); ?><?php echo $i; ?></span>
                    <br/>

                    <div class="z"><i><?php echo($lang['ADMIN_DISPLAY_ALLY_COLORDESC']); ?></i></div>
                </th>
                <th><input name="color_ally[<?php echo $i; ?>]" id="color_ally[<?php echo $i; ?>]" type="text" size="15"
                           maxlength="20"
                           value="<?php echo $color_ally_e[$i - 1]; ?>"> <?php echo help("color_picker" . $i); ?></th>
            </tr>
        <?php } ?>
        <tr>
            <th><?php echo($lang['ADMIN_DISPLAY_ALLY_GVIEW']); ?></th>
            <th><input name="galaxy_by_line_ally" type="text" size="5" maxlength="3"
                       value="<?php echo $galaxy_by_line_ally; ?>"></th>
        </tr>
        <tr>
            <th><?php echo($lang['ADMIN_DISPLAY_ALLY_SVIEW']); ?></th>
            <th><input name="system_by_line_ally" type="text" size="5" maxlength="3"
                       value="<?php echo $system_by_line_ally; ?>"></th>
        </tr>
        <tr>
            <td class="c_ogspy" colspan="2"><?php echo($lang['ADMIN_DISPLAY_LOGIN_TITLE']); ?></td>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_LOGIN_REGISTER']); ?><?php echo help("member_registration"); ?></th>
            <th><input name="enable_register_view" type="checkbox" value="1" <?php echo $enable_register_view; ?>
                       onClick="if (view.enable_register_view.checked == false)view.enable_members_view.checked=false;">
            </th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_LOGIN_ALLYNAME']); ?><?php echo help("ally_name"); ?></th>
            <th><input type="text" size="60" name="register_alliance" value="<?php echo $register_alliance; ?>"></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_LOGIN_FORUM']); ?><?php echo help("forum_link"); ?></th>
            <th><input type="text" size="60" name="register_forum" value="<?php echo $register_forum; ?>"></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_LOGIN_MODULE']); ?><?php echo help("first_displayed_module"); ?></th>
            <th><select name="open_user">
                    <option>------</option>
                    <?php
                    echo Views\ViewHelper::get_option($open_user, "./views/profile.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_PROFILE']);
                    echo Views\ViewHelper::get_option($open_user, "./views/home.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_ACCOUNT']);
                    echo Views\ViewHelper::get_option($open_user, "./views/galaxy.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_GALAXY']);
                    echo Views\ViewHelper::get_option($open_user, "./views/cartography.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_ALLY']);
                    echo Views\ViewHelper::get_option($open_user, "./views/search.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_SEARCH']);
                    echo Views\ViewHelper::get_option($open_user, "./views/ranking.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_RANKINGS']);
                    echo Views\ViewHelper::get_option($open_user, "./views/statistic.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_STATS']);
                    echo Views\ViewHelper::get_option($open_user, "./views/galaxy_obsolete.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_TOBEUPDATED']);

                    if(count($mods)) {
                        echo '<option>------</option>';
                        foreach ($mods as $mod) {
                            if ($mod["admin_only"] == 1)
                                continue;

                            echo Views\ViewHelper::get_option($open_user, "./mod/" . $mod['root'] . "/" . $mod['link'], $mod["title"]);
                        }
                    } ?>
                </select></th>
        </tr>
        <tr>
            <th width="60%"><?php echo($lang['ADMIN_DISPLAY_LOGIN_ADMINMODULE']); ?><?php echo help("first_displayed_module_admin"); ?></th>
            <th><select name="open_admin">
                    <option>------</option>
                    <?php
                    echo Views\ViewHelper::get_option($open_admin, "./views/profile.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_PROFILE']);
                    echo Views\ViewHelper::get_option($open_admin, "./views/home.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_ACCOUNT']);
                    echo Views\ViewHelper::get_option($open_admin, "./views/galaxy.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_GALAXY']);
                    echo Views\ViewHelper::get_option($open_admin, "./views/cartography.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_ALLY']);
                    echo Views\ViewHelper::get_option($open_admin, "./views/search.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_SEARCH']);
                    echo Views\ViewHelper::get_option($open_admin, "./views/ranking.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_RANKINGS']);
                    echo Views\ViewHelper::get_option($open_admin, "./views/statistic.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_STATS']);
                    echo Views\ViewHelper::get_option($open_admin, "./views/galaxy_obsolete.php", $lang['ADMIN_DISPLAY_LOGIN_MODULE_TOBEUPDATED']);

                    if (count($mods > 0)) {
                        // On affichage les mods accessible à tous, puis ceux réservés aux admins
                        for ($i = 0; $i <= 1; $i++) {
                            echo "<option>------</option>";
                            foreach ($mods as $mod) {
                                if ($mod["admin_only"] != $i)
                                    continue;

                                echo Views\ViewHelper::get_option($open_admin, "./mod/" . $mod['root'] . "/" . $mod['link'], $mod['title']);
                            }
                        }
                    }
                    ?>
                </select></th>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th colspan="2"><input type="submit" value="<?php echo($lang['ADMIN_DISPLAY_SUBMIT']); ?>">&nbsp;<input
                    type="reset" value="<?php echo($lang['ADMIN_DISPLAY_RESET']); ?>"></th>
        </tr>
    </table>
</form>
