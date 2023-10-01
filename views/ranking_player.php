<?php

/**
 * Rankings - Player Page
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

list($order, $ranking, $ranking_available, $maxrank) = galaxy_show_ranking_player();

$order_by = $pub_order_by;
$interval = $pub_interval;


$link_general = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=general'>" . $lang['RANK_GENERAL'] . "</a>";
$link_eco = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=eco'>" . $lang['RANK_ECONOMY'] . "</a>";
$link_techno = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=techno'>" . $lang['RANK_RESEARCH'] . "</a>";
$link_military = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military'>" . $lang['RANK_MILITARY'] . "</a>";
$link_military_b = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military_b'>" . $lang['RANK_MILITARY_BUILT'] . "</a>";
$link_military_l = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military_l'>M" . $lang['RANK_MILITARY_LOST'] . "</a>";
$link_military_d = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=military_d'>" . $lang['RANK_MILITARY_DESTROYED'] . "</a>";
$link_honnor = "<a href='index.php?action=ranking&amp;subaction=player&amp;order_by=honnor'>" . $lang['RANK_MILITARY_HONOR'] . "</a>";

$active_link_value = "active-rank";
$general_active_link = "";
$eco_active_link = "";
$techno_active_link = "";
$military_active_link = "";
$military_b_active_link = "";
$military_l_active_link = "";
$military_d_active_link = "";
$honnor_active_link = "";

switch ($order_by) {
    case "general":
        $link_general = str_replace($lang['RANK_GENERAL'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_GENERAL'] . "&nbsp;<img src='images/asc.png'>", $link_general);
        $general_active_link = $active_link_value;
        break;
    case "eco":
        $link_eco = str_replace($lang['RANK_ECONOMY'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_ECONOMY'] . "&nbsp;<img src='images/asc.png'>", $link_eco);
        $eco_active_link = $active_link_value;
        break;
    case "techno":
        $link_techno = str_replace($lang['RANK_RESEARCH'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_RESEARCH'] . "&nbsp;<img src='images/asc.png'>", $link_techno);
        $techno_active_link = $active_link_value;
        break;
    case "military":
        $link_military = str_replace($lang['RANK_MILITARY'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_MILITARY'] . "&nbsp;<img src='images/asc.png'>", $link_military);
        $military_active_link = $active_link_value;
        break;
    case "military_b":
        $link_military_b = str_replace($lang['RANK_MILITARY_BUILT'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_MILITARY_BUILT'] . "&nbsp;<img src='images/asc.png'>", $link_military_b);
        $military_b_active_link = $active_link_value;
    case "military_l":
        $link_military_l = str_replace($lang['RANK_MILITARY_LOST'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_MILITARY_LOST'] . "&nbsp;<img src='images/asc.png'>", $link_military_l);
        $military_l_active_link = $active_link_value;
        break;
    case "military_d":
        $link_military_d = str_replace($lang['RANK_MILITARY_DESTROYED'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_MILITARY_DESTROYED'] . "&nbsp;<img src='images/asc.png'>", $link_military_d);
        $military_d_active_link = $active_link_value;
        break;
    case "honnor":
        $link_honnor = str_replace($lang['RANK_MILITARY_HONOR'], "<img src='images/asc.png'>&nbsp;" . $lang['RANK_MILITARY_HONOR'] . "&nbsp;<img src='images/asc.png'>", $link_honnor);
        $honnor_active_link = $active_link_value;
        break;
}
?>


<table class="og-table  ">
    <thead>
        <tr>
            <th>
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="ranking">
                    <input type="hidden" name="subaction" value="player">
                    <input type="hidden" name="order_by" value="<?php echo $order_by; ?>">
                    <select name="date" onchange="this.form.submit();">
                        <?php
                        $date_selected = "";
                        $datadate = 0;
                        foreach ($ranking_available as $v) {
                            $selected = "";
                            if (!isset($pub_date_selected) && !isset($datadate)) {
                                $datadate = $v;
                                $date_selected = date("d M Y H", $v) . "h";
                            }
                            if ($pub_date == $v) {
                                $selected = "selected";
                                $datadate = $v;
                                $date_selected = date("d M Y H", $v) . "h";
                            }
                            $string_date = date("d M Y H", $v) . "h";
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
                                if ($i == $interval) {
                                    $selected = "selected";
                                }
                                echo "\t\t\t" . "<option value='" . $i . "' " . $selected . ">" . $i . " - " . ($i + 99) . "</option>" . "\n";
                            }
                        }
                        ?>
                    </select>

                </form>
            </th>

            <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_ranking"] == 1) { ?>
                <th>
                    <form method="POST" action="index.php" onsubmit="return confirm('<?php echo ($lang['RANK_DELETE_CONFIRMATION']); ?>');">
                        <input type="hidden" name="action" value="drop_ranking">
                        <input type="hidden" name="subaction" value="player">
                        <input type="hidden" name="datadate" value="<?php echo $datadate; ?>">
                        <input type="image" src="images/drop.png" title="<?php echo $lang['RANK_DELETE'] . " " . $date_selected; ?>">
                    </form>
                </th>
            <?php } ?>
        </tr>
    </thead>
</table>

<table class="og-table og-full-table og-table-ranking">

    <thead>
        <tr>
            <th>
                <?php echo ($lang['RANK_ID']); ?>
            </th>
            <th>
                <?php echo ($lang['RANK_PLAYER']); ?>
            </th>
            <th>
                <?php echo ($lang['RANK_ALLY']); ?>
            </th>
            <th colspan="2" class="<?php echo $general_active_link; ?>">
                <?php echo $link_general; ?>
            </th>
            <th colspan="2" class="<?php echo $eco_active_link; ?>">
                <?php echo $link_eco; ?>
            </th>
            <th colspan="2" class="<?php echo $techno_active_link; ?>">
                <?php echo $link_techno; ?>
            </th>
            <th colspan="2" class="<?php echo $military_active_link; ?>">
                <?php echo $link_military; ?>
            </th>
            <th colspan="2" class="<?php echo $military_b_active_link; ?>">
                <?php echo $link_military_b; ?>
            </th>
            <th colspan="2" class="<?php echo $military_l_active_link; ?>" >
                <?php echo $link_military_l; ?>
            </th>
            <th colspan="2" class="<?php echo $military_d_active_link; ?>">
                <?php echo $link_military_d; ?>
            </th>
            <th colspan="2" class="<?php echo $honnor_active_link; ?>">
                <?php echo $link_honnor; ?>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php while ($value = current($order)) : ?>
            <?php

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
            ?>

            <tr>
                <td>
                    <?php echo formate_number(key($order)) ?>
                </td>
                <td>
                    <a href='index.php?action=search&amp;type_search=player&amp;string_search=<?php echo $value; ?>&strict=on'>
                        <?php if ($value == $user_data["user_name"]) : ?>
                            <span class="og-highlight">
                                <?php echo $value; ?>
                            </span>
                        <?php else : ?>
                            <?php echo $value; ?>
                        <?php endif; ?>
                    </a>
                </td>
                <td>
                    <a href='index.php?action=search&amp;type_search=ally&amp;string_search=<?php echo $ranking[$value]["ally"]; ?>&strict=on'>
                        <?php echo $ranking[$value]["ally"]; ?>
                    </a>
                </td>
                <td class="<?php echo $general_active_link;?>">
                    <?php echo $general_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $general_active_link;?>">
                    <span class="ranking-subrank-number"><?php echo $general_rank; ?></span>
                </td>
                <td class="<?php echo $eco_active_link;?>">
                    <?php echo $eco_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $eco_active_link;?>">
                    <span class="ranking-subrank-number"><?php echo $eco_rank; ?></span>
                </td>
                <td class="<?php echo $techno_active_link;?>">
                    <?php echo $techno_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $techno_active_link;?>">
                    <span class="ranking-subrank-number"><?php echo $techno_rank; ?></span>
                </td>
                <td class="<?php echo $military_active_link;?>">
                    <?php echo $military_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $military_active_link;?> ">
                    <span class="ranking-subrank-number"><?php echo $military_rank; ?></span>
                </td>
                <td class="<?php echo $military_b_active_link;?>">
                    <?php echo $military_b_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $military_b_active_link;?>">
                    <span class="ranking-subrank-number"><?php echo $military_b_rank; ?></span>
                </td>
                <td class="<?php echo $military_l_active_link;?>">
                    <?php echo $military_l_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $military_l_active_link;?>">
                    <span class="ranking-subrank-number"><?php echo $military_l_rank; ?></span>
                </td>
                <td class="<?php echo $military_d_active_link;?>">
                    <?php echo $military_d_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $military_d_active_link;?>">
                    <span class="ranking-subrank-number"><?php echo $military_d_rank; ?></span>
                </td>
                <td class="<?php echo $honnor_active_link;?>">
                    <?php echo $honnor_pts; ?>
                </td>
                <td class="table-ranking-td-subrank <?php echo $honnor_active_link;?>">
                    <span class="ranking-subrank-number"><?php echo $honnor_rank; ?></span>
                </td>
            </tr>
                  <?php next($order); ?>
            <?php endwhile; ?>
    </tbody>
</table>
