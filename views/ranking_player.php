<?php
/**
 * Rankings - Player Page
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

list($order, $ranking, $ranking_available, $maxrank) = galaxy_show_ranking_player();

$order_by = $pub_order_by;
$interval = $pub_interval;

$link_general = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=general'>".$lang['RANK_GENERAL']."</a>";
$link_eco = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=eco'>".$lang['RANK_ECONOMY']."</a>";
$link_techno = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=techno'>".$lang['RANK_RESEARCH']."</a>";
$link_military = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military'>".$lang['RANK_MILITARY']."</a>";
$link_military_b = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military_b'>".$lang['RANK_MILITARY_BUILT']."</a>";
$link_military_l = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military_l'>M".$lang['RANK_MILITARY_LOST']."</a>";
$link_military_d = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military_d'>".$lang['RANK_MILITARY_DESTROYED']."</a>";
$link_honnor = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=honnor'>".$lang['RANK_MILITARY_HONOR']."</a>";

switch ($order_by) {
    case "general":
        $link_general = str_replace($lang['RANK_GENERAL'], "<img src='images/asc.png'>&nbsp;".$lang['RANK_GENERAL']."&nbsp;<img src='images/asc.png'>", $link_general);
        break;
    case "eco":
        $link_eco = str_replace($lang['RANK_ECONOMY'], "<img src='images/asc.png'>&nbsp;".$lang['RANK_ECONOMY']."&nbsp;<img src='images/asc.png'>", $link_eco);
        break;
    case "techno":
        $link_techno = str_replace($lang['RANK_RESEARCH'], "<img src='images/asc.png'>&nbsp;".$lang['RANK_RESEARCH']."&nbsp;<img src='images/asc.png'>", $link_techno);
        break;
    case "military":
        $link_military = str_replace($lang['RANK_MILITARY'] , "<img src='images/asc.png'>&nbsp;".$lang['RANK_MILITARY']."&nbsp;<img src='images/asc.png'>", $link_military);
        break;
    case "military_b":
        $link_military_b = str_replace($lang['RANK_MILITARY_BUILT'] , "<img src='images/asc.png'>&nbsp;".$lang['RANK_MILITARY_BUILT']."&nbsp;<img src='images/asc.png'>", $link_military_b);
        break;
    case "military_l":
        $link_military_l = str_replace($lang['RANK_MILITARY_LOST'] , "<img src='images/asc.png'>&nbsp;".$lang['RANK_MILITARY_LOST']."&nbsp;<img src='images/asc.png'>", $link_military_l);
        break;
    case "military_d":
        $link_military_d = str_replace($lang['RANK_MILITARY_DESTROYED'] , "<img src='images/asc.png'>&nbsp;".$lang['RANK_MILITARY_DESTROYED']."&nbsp;<img src='images/asc.png'>", $link_military_d);
        break;
    case "honnor":
        $link_honnor = str_replace($lang['RANK_MILITARY_HONOR'] , "<img src='images/asc.png'>&nbsp;".$lang['RANK_MILITARY_HONOR']."&nbsp;<img src='images/asc.png'>", $link_honnor);
        break;
}
?>

<table>
    <tr>
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="ranking">
            <input type="hidden" name="subaction" value="player">
            <input type="hidden" name="order_by" value="<?php echo $order_by; ?>">
            <td align="right">
                <select name="date" onchange="this.form.submit();">
                    <?php
                    $date_selected = "";
                    $datadate = 0;
                    foreach ($ranking_available as $v) {
                        $selected = "";
                        if (!isset($pub_date_selected) && !isset($datadate)) {
                            $datadate = $v;
                            $date_selected = strftime("%d %b %Y %Hh", $v);
                        }
                        if ($pub_date == $v) {
                            $selected = "selected";
                            $datadate = $v;
                            $date_selected = strftime("%d %b %Y %Hh", $v);
                        }
                        $string_date = strftime("%d %b %Y %Hh", $v);
                        echo "\t\t\t" . "<option value='" . $v . "' " . $selected . ">" . $string_date . "</option>" . "\n";
                    }
                    ?>
                </select>
                &nbsp;
                <select name="interval" onchange="this.form.submit();">
                    <?php
                    if (sizeof($ranking_available) > 0) {
                        for ($i = 1; $i <= $maxrank; $i = $i + 100) {
                            $selected = "";
                            if ($i == $interval) $selected = "selected";
                            echo "\t\t\t" . "<option value='" . $i . "' " . $selected . ">" . $i . " - " . ($i + 99) . "</option>" . "\n";
                        }
                    }
                    ?>
                </select>
            </td>
        </form>

        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_ranking"] == 1) { ?>
            <form method="POST" action="index.php"
                  onsubmit="return confirm('<?php echo($lang['RANK_DELETE_CONFIRMATION']); ?>');">
                <input type="hidden" name="action" value="drop_ranking">
                <input type="hidden" name="subaction" value="player">
                <input type="hidden" name="datadate" value="<?php echo $datadate; ?>">
                <td align="right"><input type="image" src="images/drop.png"
                                         title="<?php echo $lang['RANK_DELETE']." ".$date_selected; ?>"></td>
            </form>
        <?php }?>
    </tr>
</table>

<table width="1200">
    <tr>
        <td class="c" width="30"><?php echo($lang['RANK_ID']); ?></td>
        <td class="c"><?php echo($lang['RANK_PLAYER']); ?></td>
        <td class="c"><?php echo($lang['RANK_ALLY']); ?></td>
        <td class="c_classement_points" colspan="2"><?php echo $link_general;?></td>
        <td class="c" colspan="2"><?php echo $link_eco;?></td>
        <td class="c_classement_recherche" colspan="2"><?php echo $link_techno;?></td>
        <td class="c_classement_flotte" colspan="2"><?php echo $link_military;?></td>
        <td class="c_classement_flotte" colspan="2"><?php echo $link_military_b;?></td>
        <td class="c_classement_flotte" colspan="2"><?php echo $link_military_l;?></td>
        <td class="c_classement_flotte" colspan="2"><?php echo $link_military_d;?></td>
        <td class="c" colspan="2"><?php echo $link_honnor;?></td>
    </tr>
    <?php

    while ($value = current($order)) {
        $player = "<a href='index.php?action=search&amp;type_search=player&amp;string_search=" . $value . "&strict=on'>";
        if ($value == $user_data["user_name"]) {
            $player .= "<span style=\"color: lime; \">";
        }
        $player .= $value;

        if ($value == $user_data["user_name"]) {
            $player .= "</font>";
        }
        $player .= "</a>";

        $ally = "<a href='index.php?action=search&amp;type_search=ally&amp;string_search=" . $ranking[$value]["ally"] . "&strict=on'>";
        $ally .= $ranking[$value]["ally"];
        $ally .= "</a>";

        $general_pts = "&nbsp;";
        $general_rank = "&nbsp;";
        $eco_pts = "&nbsp;";
        $eco_rank = "&nbsp;";
        $techno_pts = "&nbsp;";
        $techno_rank = "&nbsp;";
        $military_pts = "&nbsp;";
        $military_rank = "&nbsp;";
        $military_b_pts = "&nbsp;";
        $military_b_rank = "&nbsp;";
        $military_l_pts = "&nbsp;";
        $military_l_rank = "&nbsp;";
        $military_d_pts = "&nbsp;";
        $military_d_rank = "&nbsp;";
        $honnor_pts = "&nbsp;";
        $honnor_rank = "&nbsp;";

        if (isset($ranking[$value]["general"]["points"])) {
            $general_pts = formate_number($ranking[$value]["general"]["points"]);
            $general_rank = formate_number($ranking[$value]["general"]["rank"]);
        }
        if (isset($ranking[$value]["eco"]["points"])) {
            $eco_pts = formate_number($ranking[$value]["eco"]["points"]);
            $eco_rank = formate_number($ranking[$value]["eco"]["rank"]);
        }
        if (isset($ranking[$value]["techno"]["points"])) {
            $techno_pts = formate_number($ranking[$value]["techno"]["points"]);
            $techno_rank = formate_number($ranking[$value]["techno"]["rank"]);
        }
        if (isset($ranking[$value]["military"]["points"])) {
            $military_pts = formate_number($ranking[$value]["military"]["points"]);
            $military_rank = formate_number($ranking[$value]["military"]["rank"]);
        }
        if (isset($ranking[$value]["military_b"]["points"])) {
            $military_b_pts = formate_number($ranking[$value]["military_b"]["points"]);
            $military_b_rank = formate_number($ranking[$value]["military_b"]["rank"]);
        }
        if (isset($ranking[$value]["military_l"]["points"])) {
            $military_l_pts = formate_number($ranking[$value]["military_l"]["points"]);
            $military_l_rank = formate_number($ranking[$value]["military_l"]["rank"]);
        }
        if (isset($ranking[$value]["military_d"]["points"])) {
            $military_d_pts = formate_number($ranking[$value]["military_d"]["points"]);
            $military_d_rank = formate_number($ranking[$value]["military_d"]["rank"]);
        }
        if (isset($ranking[$value]["honnor"]["points"])) {
            $honnor_pts = formate_number($ranking[$value]["honnor"]["points"]);
            $honnor_rank = formate_number($ranking[$value]["honnor"]["rank"]);
        }

        echo "<tr>";
        echo "\t" . "<th>" . formate_number(key($order)) . "</th>";
        echo "\t" . "<th>" . $player . "</th>";
        echo "\t" . "<th>" . $ally . "</th>";
        echo "\t" . "<th width='70'>" . $general_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $general_rank . "</i></font></th>";
        echo "\t" . "<th width='70'>" . $eco_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $eco_rank . "</i></font></th>";
        echo "\t" . "<th width='70'>" . $techno_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $techno_rank . "</i></font></th>";
        echo "\t" . "<th width='70'>" . $military_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $military_rank . "</i></font></th>";
        echo "\t" . "<th width='70'>" . $military_b_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $military_b_rank . "</i></font></th>";
        echo "\t" . "<th width='70'>" . $military_l_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $military_l_rank . "</i></font></th>";
        echo "\t" . "<th width='70'>" . $military_d_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $military_d_rank . "</i></font></th>";
        echo "\t" . "<th width='70'>" . $honnor_pts . "</th>";
        echo "\t" . "<th width='40'><font color='lime'><i>" . $honnor_rank . "</i></font></th>";
        echo "</tr>";

        next($order);
    }
    ?>
</table>