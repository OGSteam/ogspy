<?php
/**
 * Rankings - Ally Page
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


global $order_by ;
global $ranking ;


$link_general    = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=general">'    . $lang['RANK_GENERAL'] . '</a>';
$link_eco        = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=eco">'        . $lang['RANK_ECONOMY'] . '</a>';
$link_techno     = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=techno">'     . $lang['RANK_RESEARCH'] . '</a>';
$link_military   = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=military">'   . $lang['RANK_MILITARY'] . '</a>';
$link_military_b = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=military_b">' . $lang['RANK_MILITARY_BUILT'] . '</a>';
$link_military_l = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=military_l">' . $lang['RANK_MILITARY_LOST'] . '</a>';
$link_military_d = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=military_d">' . $lang['RANK_MILITARY_DESTROYED'] . '</a>';
$link_honnor     = '<a href="index.php?action=ranking&amp;subaction=ally&amp;order_by=honnor">'     . $lang['RANK_MILITARY_HONOR'] . '</a>';

switch ($order_by) {
    case 'general':
        $link_general = str_replace($lang['RANK_GENERAL'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_GENERAL'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_general);
        break;
    case 'eco':
        $link_eco = str_replace($lang['RANK_ECONOMY'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_ECONOMY'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_eco);
        break;
    case 'techno':
        $link_techno = str_replace($lang['RANK_RESEARCH'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_RESEARCH'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_techno);
        break;
    case 'military':
        $link_military = str_replace($lang['RANK_MILITARY'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_MILITARY'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_military);
        break;
    case 'military_b':
        $link_military_b = str_replace($lang['RANK_MILITARY_BUILT'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_MILITARY_BUILT'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_military_b);
        break;
    case 'military_l':
        $link_military_l = str_replace($lang['RANK_MILITARY_LOST'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_MILITARY_LOST'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_military_l);
        break;
    case 'military_d':
        $link_military_d = str_replace($lang['RANK_MILITARY_DESTROYED'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_MILITARY_DESTROYED'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_military_d);
        break;
    case 'honnor':
        $link_honnor = str_replace($lang['RANK_MILITARY_HONOR'], '<img alt="up" src="images/asc.png">&nbsp;' . $lang['RANK_MILITARY_HONOR'] . '&nbsp;<img alt="up" src="images/asc.png">', $link_honnor);
        break;
}
?>

<table>
    <thead>
    <tr>
        <th><?php echo($lang['RANK_ID']); ?></th>
        <th><?php echo($lang['RANK_ALLY']); ?></th>
        <th><?php echo($lang['RANK_MEMBER']); ?></th>
        <th colspan="2"><?php echo $link_general; ?></th>
        <th colspan="2"><?php echo $link_eco; ?></th>
        <th colspan="2"><?php echo $link_techno; ?></th>
        <th colspan="2"><?php echo $link_military; ?></th>
        <th colspan="2"><?php echo $link_military_b; ?></th>
        <th colspan="2"><?php echo $link_military_l; ?></th>
        <th colspan="2"><?php echo $link_military_d; ?></th>
        <th colspan="2"><?php echo $link_honnor; ?></th>
    </tr>
    </thead>
<tbody>

<?php
    while ($value = current($order)) {
        $ally = '<a href="index.php?action=search&amp;type_search=ally&amp;string_search=' . $value . '&amp;strict=on">';
        $ally .= $value;
        $ally .= '</a>';

        $member = formate_number($ranking[$value]['number_member']);

        $general_pts = '&nbsp;';
        $general_pts_per_member = '&nbsp;';
        $general_rank = '&nbsp;';
        $techno_pts = '&nbsp;';
        $techno_pts_per_member = '&nbsp;';
        $techno_rank = '&nbsp;';
        $eco_pts = '&nbsp;';
        $eco_pts_per_member = '&nbsp;';
        $eco_rank = '&nbsp;';
        $military_pts = '&nbsp;';
        $military_pts_per_member = '&nbsp;';
        $military_rank = '&nbsp;';
        $military_b_pts = '&nbsp;';
        $military_b_pts_per_member = '&nbsp;';
        $military_b_rank = '&nbsp;';
        $military_l_pts = '&nbsp;';
        $military_l_pts_per_member = '&nbsp;';
        $military_l_rank = '&nbsp;';
        $military_d_pts = '&nbsp;';
        $military_d_pts_per_member = '&nbsp;';
        $military_d_rank = '&nbsp;';
        $honnor_pts = '&nbsp;';
        $honnor_pts_per_member = '&nbsp;';
        $honnor_rank = '&nbsp;';

        if (isset($ranking[$value]['general']['points'])) {
            $general_pts = formate_number($ranking[$value]['general']['points']);
            $general_pts_per_member = formate_number($ranking[$value]['general']['points'] / $member);
            $general_rank = formate_number($ranking[$value]['general']['rank']);
        }
        if (isset($ranking[$value]['eco']['points'])) {
            $eco_pts = formate_number($ranking[$value]['eco']['points']);
            $eco_pts_per_member = formate_number($ranking[$value]['eco']['points'] / $member);
            $eco_rank = formate_number($ranking[$value]['eco']['rank']);
        }
        if (isset($ranking[$value]['techno']['points'])) {
            $techno_pts = formate_number($ranking[$value]['techno']['points']);
            $techno_pts_per_member = formate_number($ranking[$value]['techno']['points'] / $member);
            $techno_rank = formate_number($ranking[$value]['techno']['rank']);
        }

        if (isset($ranking[$value]['military']['points'])) {
            $military_pts = formate_number($ranking[$value]['military']['points']);
            $military_pts_per_member = formate_number($ranking[$value]['military']['points'] / $member);
            $military_rank = formate_number($ranking[$value]['military']['rank']);
        }
        if (isset($ranking[$value]["military_b"]['points'])) {
            $military_b_pts = formate_number($ranking[$value]["military_b"]['points']);
            $military_b_pts_per_member = formate_number($ranking[$value]["military_b"]['points'] / $member);
            $military_b_rank = formate_number($ranking[$value]["military_b"]['rank']);
        }
        if (isset($ranking[$value]['military_l']['points'])) {
            $military_l_pts = formate_number($ranking[$value]['military_l']['points']);
            $military_l_pts_per_member = formate_number($ranking[$value]['military_l']['points'] / $member);
            $military_l_rank = formate_number($ranking[$value]['military_l']['rank']);
        }

        if (isset($ranking[$value]['military_d']['points'])) {
            $military_d_pts = formate_number($ranking[$value]['military_d']['points']);
            $military_d_pts_per_member = formate_number($ranking[$value]['military_d']['points'] / $member);
            $military_d_rank = formate_number($ranking[$value]['military_d']['rank']);
        }

        if (isset($ranking[$value]['honnor']['points'])) {
            $honnor_pts = formate_number($ranking[$value]['honnor']['points']);
            $honnor_pts_per_member = formate_number($ranking[$value]['honnor']['points'] / $member);
            $honnor_rank = formate_number($ranking[$value]['honnor']['rank']);
        }

        echo "<tr>\n";
        echo "\t" . '<th>' . formate_number(key($order)) . "</th>\n";
        echo "\t" . '<td>' . $ally . "</td>\n";
        echo "\t" . '<td>' . $member . "</td>\n";
        echo "\t" . '<td>' . $general_pts . '<br>(<span >' . $general_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $general_rank . "</span></td>\n";
        echo "\t" . '<td>' . $eco_pts . '<br>(<span >' . $eco_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $eco_rank . "</span></td>\n";
        echo "\t" . '<td>' . $techno_pts . '<br>(<span >' . $techno_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $techno_rank . "</span></td>\n";
        echo "\t" . '<td>' . $military_pts . "<br>(<span><i>" . $military_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $military_rank . "</span></td>\n";
        echo "\t" . '<td>' . $military_b_pts . '<br>(<span >' . $military_b_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $military_b_rank . "</span></td>\n";
        echo "\t" . '<td>' . $military_l_pts . '<br>(<span >' . $military_l_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $military_l_rank . "</span></td>\n";
        echo "\t" . '<td>' . $military_d_pts . '<br>(<span >' . $military_d_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $military_d_rank . "</span></td>\n";
        echo "\t" . '<td>' . $honnor_pts . '<br>(<span >' . $honnor_pts_per_member . "</span>)</td>\n";
        echo "\t" . '<td ><span >' . $honnor_rank . "</span></td>\n";
        echo "</tr>\n";

        next($order);
    }
?>
</tbody>
</table>