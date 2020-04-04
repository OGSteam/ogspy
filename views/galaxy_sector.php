<?php
/**
 * Affichage Galaxie Sector
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

$info_sector = galaxy_show_sector();

$population = $info_sector['population'];
$galaxy = $info_sector['galaxy'];
$system_down = $info_sector['system_down'];
$system_up = $info_sector['system_up'];
$system = $system_down;
$totalsystem = abs($system_up - $system_down);
$nbsystem = 0;

require_once('views/page_header.php');

$link_left1 = $link_left2 = $link_right1 = $link_right2 = '';
if (($system_down - $totalsystem - 1) >= 1) {
    $link_left1 = '<a href="index.php?action=galaxy_sector&amp;galaxy=' . $galaxy . '&amp;system_down=' . ($system_down - $totalsystem - 1) . '&amp;system_up=' . ($system_down - 1) . '">';
    $link_left2 = '</a>';
}
if (($system_down + $totalsystem + 1) <= intval($server_config['num_of_systems'])) {
    $link_right1 = '<a href="index.php?action=galaxy_sector&amp;galaxy=' . $galaxy . '&amp;system_down=' . ($system_up + 1) . '&amp;system_up=' . ($system_up + $totalsystem + 1) . '">';
    $link_right2 = '</a>';
}

echo "<table>\n";
echo '<tr>';
echo '<td class="c">' . $link_left1 . $lang['GALAXY_SECTOR_PREVIOUS'] . ' - ' . $link_left2 . "</td>\n";
echo '<td class="c" colspan="3">' . $lang['GALAXY_SECTOR_NAVIGATE'] . '<br>' . $galaxy . ':' . $system_down . ' - ' . $galaxy . ':' . $system_up . "</td>\n";
echo '<td class="c">' . $link_right1 . $lang['GALAXY_SECTOR_NEXT'] . ' +' . $link_right2 . "</td>\n";
echo "</tr>\n";
for ($lines = 0; $lines < ceil($totalsystem / 5); $lines++) {
    echo "<tr>\n";
    for ($cols = $system; $cols < $system + 5; $cols++) {
        $last_update = '&nbsp;';
        if (isset($population[$cols]['last_update'])) {
            $last_update = strftime('%d %b %Y %H:%M', $population[$cols]['last_update']);
        }

        echo "\t<td style='vertical-align:top'>\n";
        echo "\t\t<table style='width:190px'>\n";
        echo '<tr>';
        echo '<td class="c" style="width:30px">&nbsp;</td>';
        echo '<td class="c"><a href="index.php?action=galaxy&amp;galaxy=' . $galaxy . '&amp;system=' . $cols . '">' . $galaxy . ':' . $cols . '</a><br>' . $last_update . '</td>';
        echo "</tr>\n";
        for ($row = 1; $row <= 15; $row++) {
            $head = $row;
            $box = '&nbsp;';
            if (isset($population[$cols][$row])) {
                $begin_hided = '';
                $end_hided = '';
                if ($population[$cols][$row]["hided"]) {
                    $begin_hided = '<span style="color:lime">';
                    $end_hided = '</span>';
                }
                $begin_allied = '';
                $end_allied = '';
                if ($population[$cols][$row]['allied']) {
                    $begin_allied = '<span classe="allied">';
                    $end_allied = '</span>';
                }
                if ($population[$cols][$row]['moon'] == 1) {
                    $detail = '';
                    if ($population[$cols][$row]['last_update_moon'] > 0) {
                        $detail .= $population[$cols][$row]['phalanx'];
                    }
                    if ($population[$cols][$row]['gate'] == 1) {
                        $detail .= 'P';
                    }
                    if ($detail != '') {
                        $detail = ' - ' . $detail;
                    }
                    $head .= '<br><span style="color:lime">M' . $detail . '</span>';
                }
                if ($population[$cols][$row]['report_spy'] > 0) {
                    $head .= '<br><a href="#" onClick="window.open(\'index.php?action=show_reportspy&amp;galaxy=$galaxy&amp;system=$cols&amp;row=$row\',\'_blank\',\'width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0\');return(false)"><span style="color:orange">' . $population[$cols][$row]['report_spy'] . 'E</span></a>';
                }
                $head .= '<br><span style="color:red">' . $population[$cols][$row]['status'] . '</span>';
                $box  = $population[$cols][$row]['planet'] != '' ? '<a href="index.php?action=search&amp;type_search=planet&amp;string_search=' . $population[$cols][$row]['planet'] . '&amp;strict=on"><span style="color:orange; font-style: italic;">' . $population[$cols][$row]['planet'] . '</span></a><br>' : '&nbsp;';
                $box .= $population[$cols][$row]['player'] != '' ? $begin_allied . '<a href="index.php?action=search&amp;type_search=player&amp;string_search=' . $population[$cols][$row]['player'] . '&amp;strict=on">' . $begin_hided . $population[$cols][$row]['player'] . $end_hided . '</a><br>' : '&nbsp;';
                $box .= $population[$cols][$row]['ally']   != '' ? '[<a href="index.php?action=search&amp;type_search=ally&amp;string_search=' . $population[$cols][$row]['ally'] . '&amp;strict=on">' . $begin_hided . $population[$cols][$row]['ally'] . $end_hided . '</a>]' . $end_allied : '&nbsp;';
            }
            echo '<tr style="height:50px"><td class="c" style="vertical-align:top">' . $head . '</td><th style="vertical-align:top">' . $box . "</th></tr>\n";
        }
        echo "</table></td>\n";
        if ($nbsystem == $totalsystem) {
            echo "</tr>\n";
            break 2;
        }
        $nbsystem++;
    }
    $system = $cols;
    echo "</tr>\n";
}
echo "<tr>\n";
echo '<td class="c">' . $link_left1 . $lang['GALAXY_SECTOR_PREVIOUS'] . ' -' . $link_left2 . "</td>\n";
echo '<td class="c" colspan="3">' . $lang['GALAXY_SECTOR_NAVIGATE'] . '<br>' . $galaxy . ':' . $system_down . ' - ' . $galaxy . ':' . $system_up . "</td>\n";
echo '<td class="c">' . $link_right1 . $lang['GALAXY_SECTOR_NEXT'] . ' +' . $link_right2 . "</td></tr>\n";
echo "</table>\n";

require_once('views/page_tail.php');
?>