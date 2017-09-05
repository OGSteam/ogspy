<?php
/**
 * Panneau administration des options Modules
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Aeris
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$mod_list = mod_list();
?>
<table align="center">
    <tr>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td class="c" colspan="6" width="550"><?php echo($lang['ADMIN_MOD_LIST']); ?></td>
    </tr>
    <tr>
        <td><?php echo($lang['ADMIN_MOD_USER']); ?></td>
        <td colspan="4"></td>
        <th><?php echo($lang['ADMIN_MOD_MENUVIEW']); ?></th>
    </tr>
    <?php
    $mods = $mod_list["actived"];
    while ($mod = current($mods)) {
        if ($mod["admin_only"] == 0) {
            echo "\t" . "<tr>";
            echo "<th width='200'>" . $mod["title"] . " (" . $mod["version"] . ")</th>";
            echo "<th width='50'><a href='index.php?action=mod_up&amp;mod_id=" . $mod['id'] . "'><img src='images/asc.png' title='Monter'></a>&nbsp;<a href='index.php?action=mod_down&amp;mod_id=" . $mod['id'] . "'><img src='images/desc.png' title='Descendre'></a></th>";
            echo "<th width='100'><a href='index.php?action=mod_disable&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_DISABLE'] . "</a></th>";
            echo "<th width='100'><a href='index.php?action=mod_uninstall&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_REMOVE'] . "</a></th>";
            echo "<th width='100'>";
            if (!$mod["up_to_date"]) {
                echo "<a href='index.php?action=mod_update&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_UPDATE'] . "</a>";
            }
            echo "</th>";
            echo "<th width='100'><a href='index.php?action=mod_admin&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_NORMAL'] . "</a></th>";
            echo "</tr>";
            echo "\n";


        }
        next($mods);
    }
    echo "<tr><td>" . $lang['ADMIN_MOD_ADMIN'] . "</td><td colspan='5'></td></tr>";
    $mods = $mod_list["actived"];
    while ($mod = current($mods)) {
        if ($mod["admin_only"] == 1) {
            echo "\t" . "<tr>";
            echo "<th width='200'>" . $mod["title"] . " (" . $mod["version"] . ")</th>";
            echo "<th width='50'><a href='index.php?action=mod_up&amp;mod_id=" . $mod['id'] . "'><img src='images/asc.png' title='Monter'></a>&nbsp;<a href='index.php?action=mod_down&amp;mod_id=" . $mod['id'] . "'><img src='images/desc.png' title='Descendre'></a></th>";
            echo "<th width='100'><a href='index.php?action=mod_disable&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_DISABLE'] . "</a></th>";
            echo "<th width='100'><a href='index.php?action=mod_uninstall&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_REMOVE'] . "</a></th>";
            echo "<th width='100'>";
            if (!$mod["up_to_date"]) {
                echo "<a href='index.php?action=mod_update&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_UPDATE'] . "</a>";
            }
            echo "</th>";
            echo "<th width='100'><a href='index.php?action=mod_normal&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_ADMIN'] . "</a></th>";
            echo "</tr>";
            echo "\n";


        }
        next($mods);
    }
    ?>
    <tr>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td class="c" colspan="6" width="550"><?php echo($lang['ADMIN_MOD_LIST_INACTIVE']); ?></td>
    </tr>
    <?php
    $mods = $mod_list["disabled"];
    while ($mod = current($mods)) {
        echo "\t" . "<tr>";
        echo "<th width='250' colspan='2'>" . $mod["title"] . " (" . $mod["version"] . ")</th>";
        echo "<th width='100'><a href='index.php?action=mod_active&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_ENABLE'] . "</a></th>";
        echo "<th width='100'><a href='index.php?action=mod_uninstall&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_REMOVE'] . "</a></th>";
        if (!$mod["up_to_date"]) {
            echo "<th width='100'><a href='index.php?action=mod_update&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_UPDATE'] . "</a></th>";
        } else echo "<th width='100'>&nbsp;</th>";
        echo "<th width='100'>&nbsp;</th>";
        echo "</tr>";
        echo "\n";

        next($mods);
    }
    ?>
    <tr>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td class="c" colspan="6" width="550"><?php echo($lang['ADMIN_MOD_NOT_INSTALLED']); ?></td>
    </tr>
    <?php
    $mods = $mod_list["install"];
    while ($mod = current($mods)) {
        echo "\t" . "<tr>";
        echo "<th width='200'>" . $mod["title"] . "</th>";
        echo "<th width='300' colspan='5'><a href='index.php?action=mod_install&amp;directory=" . $mod['directory'] . "'>" . $lang['ADMIN_MOD_INSTALL'] . "</a></th>";
        echo "</tr>";
        echo "\n";

        next($mods);
    }
    ?>
    <tr>
        <td>&nbsp;</td>
    </tr>

    <tr>
        <td class="c" colspan="6" width="550"><?php echo($lang['ADMIN_MOD_INVALID']); ?></td>
    </tr>
    <?php
    $mods = $mod_list["wrong"];
    while ($mod = current($mods)) {
        echo "\t" . "<tr>";
        echo "<th width='200'>" . $mod["title"] . "</th>";
        echo "<th width='300' colspan='5'><a href='index.php?action=mod_uninstall&amp;mod_id=" . $mod['id'] . "'>" . $lang['ADMIN_MOD_REMOVE'] . "</a></th>";
        echo "</tr>";
        echo "\n";

        next($mods);
    }
    ?>
</table>
