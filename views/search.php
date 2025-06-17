<?php

/**
 * Search Page
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

$search_result = array();
list($search_result, $total_page) = galaxy_search();

use Ogsteam\Ogspy\Helper\ToolTip_Helper;

$ToolTip_Helper = new ToolTip_Helper();

$string_search = $pub_string_search;
$type_search = $pub_type_search;
$strict = $pub_strict;
$sort = $pub_sort;
$sort2 = $pub_sort2;
$galaxy_down = $pub_galaxy_down;
$galaxy_up = $pub_galaxy_up;
$system_down = $pub_system_down;
$system_up = $pub_system_up;
$row_down = $pub_row_down;
$row_up = $pub_row_up;
$page = $pub_page;

$link_order_coordinates = "";
$link_order_ally = "";
$link_order_player = "";
$end_link = "";

$individual_ranking_player = [];
$individual_ranking_ally = [];

$tooltiptab = [
    "playerName" => [],
    "allyName" => []
]; // Conteneur des tooltips à créer


$strict_on = "";
if ($search_result) {
    if (isset($strict)) {
        $strict_on = "&strict";
    }
    $new_sort2 = 0;
    if (isset($sort2)) {
        if ($sort2 == 0) {
            $new_sort2 = 1;
        } else {
            $new_sort2 = 0;
        }
    }

    if ($type_search != "colonization") {
        $link_order_coordinates = "<a href='index.php?action=search&amp;sort=1&amp;sort2=" . $new_sort2 . "&amp;type_search=" . $type_search . "&amp;page=" . $page . "&amp;string_search=" . $string_search . $strict_on . "'>" . $lang['SEARCH_COORDS'];
        $link_order_ally = "<a href='index.php?action=search&amp;sort=2&amp;sort2=" . $new_sort2 . "&amp;type_search=" . $type_search . "&amp;page=" . $page . "&amp;string_search=" . $string_search . $strict_on . "'>" . $lang['SEARCH_ALLYS'];
        $link_order_player = "<a href='index.php?action=search&amp;sort=3&amp;sort2=" . $new_sort2 . "&amp;type_search=" . $type_search . "&amp;page=" . $page . "&amp;string_search=" . $string_search . $strict_on . "'>" . $lang['SEARCH_PLAYERS'];

        if ($sort2 == 0) {
            switch ($sort) {
                case "1":
                    $link_order_coordinates = "<img src='images/asc.png'>&nbsp;" . $link_order_coordinates . "&nbsp;<img src='images/asc.png'>";
                    break;
                case "2":
                    $link_order_ally = "<img src='images/asc.png'>&nbsp;" . $link_order_ally . "&nbsp;<img src='images/asc.png'>";
                    break;
                case "3":
                    $link_order_player = "<img src='images/asc.png'>&nbsp;" . $link_order_player . "&nbsp;<img src='images/asc.png'>";
                    break;
            }
        } else {
            switch ($sort) {
                case "1":
                    $link_order_coordinates = "<img src='images/desc.png'>&nbsp;" . $link_order_coordinates . "&nbsp;<img src='images/desc.png'>";
                    break;
                case "2":
                    $link_order_ally = "<img src='images/desc.png'>&nbsp;" . $link_order_ally . "&nbsp;<img src='images/desc.png'>";
                    break;
                case "3":
                    $link_order_player = "<img src='images/desc.png'>&nbsp;" . $link_order_player . "&nbsp;<img src='images/desc.png'>";
                    break;
            }
        }

        $link_order_coordinates .= "</a>";
        $link_order_ally .= "</a>";
        $link_order_player .= "</a>";
    }
}

//Données recherches joueurs
if (!isset($string_search)) {
    $string_search = "";
}
if (!isset($type_search) && !isset($strict) || isset($strict)) {
    $strict = " checked";
} else {
    $strict = "";
}
$type_player = " checked";
$type_ally = "";
$type_planet = "";
if (isset($type_search)) {
    switch ($type_search) {
        case "player":
            $type_player = " checked";
            break;
        case "ally":
            $type_ally = " checked";
            break;
        case "planet":
            $type_planet = " checked";
            break;
    }
}

//Données recherche coordonnées colonisables
$galaxy_down = $galaxy_down ?? "";
$galaxy_up = $galaxy_up ?? "";
$system_down = $system_down ?? "";
$system_up = $system_up ?? "";
$row_down = $row_down ?? "";
$row_up = $row_up ?? "";

require_once("views/page_header.php");
?>
<div class="page_search">
    <table class="og-table og-little-table">
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="search">
            <thead>
                <tr>
                    <th colspan="3"><?php echo $lang['SEARCH_GLOBAL']; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input name="type_search" value="player" type="radio" <?php echo $type_player; ?>>
                    </td>
                    <td>
                        <?php echo $lang['SEARCH_PLAYER']; ?>
                    </td>
                    <td rowspan="3">
                        <input name="string_search" type="text" maxlength="25" size="25" value="<?php echo $string_search; ?>">
                    </td>
                </tr>
                <tr>
                    <td><input name="type_search" value="ally" type="radio" <?php echo $type_ally; ?>></td>
                    <td><?php echo $lang['SEARCH_ALLIANCE']; ?></td>
                </tr>
                <tr>
                    <td>
                        <input name="type_search" value="planet" type="radio" <?php echo $type_planet; ?>>
                    </td>
                    <td>
                        <?php echo $lang['SEARCH_PLANET']; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input name="strict" value="false" type="checkbox" <?php echo $strict; ?>>
                    </td>
                    <td colspan="2">
                        <?php echo $lang['SEARCH_STRICT']; ?><?php echo help("search_strict"); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input class="og-button" type="submit" value="<?php echo $lang['SEARCH_GO']; ?>">
                    </td>
                </tr>
            </tbody>
        </form>
    </table>

    <table class="og-table og-little-table">
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="search">
            <thead>
                <tr>
                    <th colspan="4">
                        <?php echo $lang['SEARCH_SPECIAL']; ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <select name="type_search">
                            <?php
                            $str_selected = array('colonization' => '', 'moon' => '', 'away' => '', 'spy' => '');
                            if (isset($type_search) && isset($str_selected[$type_search])) {
                                $str_selected[$type_search] = ' selected';
                            } ?>
                            <option value='colonization' <?php echo $str_selected['colonization'] . ">" . $lang['SEARCH_EMPTY_PLANETS'] ?></option>
                            <option value='moon' <?php echo $str_selected['moon'] . ">" . $lang['SEARCH_MOONS'] ?></option>
                            <option value='away' <?php echo $str_selected['away'] . ">" . $lang['SEARCH_INACTIVEPLAYERS'] ?></option>
                            <option value='spy' <?php echo $str_selected['spy'] . ">" . $lang['SEARCH_PLANETS_SPYED'] ?></option>
                        </select>
                    </td>
                    <td><?php echo $lang['SEARCH_MINIMUM']; ?></td>
                    <td><?php echo $lang['SEARCH_MAXIMUM']; ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><?php echo $lang['SEARCH_GALAXY']; ?></td>
                    <td><input name="galaxy_down" type="text" maxlength="2" size="3" value="<?php echo $galaxy_down; ?>"></td>
                    <td><input name="galaxy_up" type="text" maxlength="2" size="3" value="<?php echo $galaxy_up; ?>"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><?php echo $lang['SEARCH_SYSTEM']; ?></td>
                    <td><input name="system_down" type="text" maxlength="3" size="3" value="<?php echo $system_down; ?>"></td>
                    <td><input name="system_up" type="text" maxlength="3" size="3" value="<?php echo $system_up; ?>"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><?php echo $lang['SEARCH_POSITION']; ?></td>
                    <td><input name="row_down" type="text" maxlength="2" size="3" value="<?php echo $row_down; ?>">
                    </td>
                    <td><input name="row_up" type="text" maxlength="2" size="3" value="<?php echo $row_up; ?>"></td>
                </tr>
                <tr>
                    <td colspan="4"><input class="og-button" type="submit" value="<?php echo $lang['SEARCH_GO']; ?>"></td>
                </tr>

            </tbody>


        </form>
    </table>

    <?php if (count($search_result) > 0) : ?>
        <table class="og-table og-medium-table og-table-galaxy ">
            <thead>
                <tr>
                    <th class='og-legend' colspan='11'>
                        <?php
                        if ($total_page > 1) {
                            if ($type_search == "planet" || $type_search == "ally" || $type_search == "player") {
                                $option = "&string_search=" . $string_search;
                                if ($strict_on != "") {
                                    $option .= "&strict=on";
                                }
                            }
                            if ($type_search == "colonization" || $type_search == "moon" || $type_search == "away" || $type_search == "spy") {
                                $option = "&galaxy_down=" . $galaxy_down;
                                $option .= "&galaxy_up=" . $galaxy_up;
                                $option .= "&system_down=" . $system_down;
                                $option .= "&system_up=" . $system_up;
                                $option .= "&row_down=" . $row_down;
                                $option .= "&row_up=" . $row_up;
                            }

                            echo "\t\t\t" . "<input class='og-button' type='button' value='<<' onclick=\"window.location = 'index.php?action=search&amp;sort=" . $sort . "&amp;sort2=" . $sort2 . "&amp;type_search=" . $type_search . "&amp;page=1" . $option . "';\">&nbsp;";
                            echo "<input class='og-button'  type='button' value='<' onclick=\"window.location = 'index.php?action=search&amp;sort=" . $sort . "&amp;sort2=" . $sort2 . "&amp;type_search=" . $type_search . "&amp;page=" . (intval($page) - 1) . $option . "';\">&nbsp;";

                            echo "<input class='og-button'  type='button' value='>' onclick=\"window.location = 'index.php?action=search&amp;sort=" . $sort . "&amp;sort2=" . $sort2 . "&amp;type_search=" . $type_search . "&amp;page=" . (intval($page) + 1) . $option . "';\">&nbsp;";
                            echo "<input class='og-button'  type='button' value='>>' onclick=\"window.location = 'index.php?action=search&amp;sort=" . $sort . "&amp;sort2=" . $sort2 . "&amp;type_search=" . $type_search . "&amp;page=" . ($total_page) . $option . "';\">" . "\n";
                        } ?>

                        <form method='GET' action='index.php'>
                            <?php
                            echo "\t\t\t" . "<input type='hidden' name='type_search' value='" . $type_search . "'>" . "\n";
                            echo "\t\t\t" . "<input type='hidden' name='action' value='search'>" . "\n";
                            if (isset($sort) && isset($sort2)) {
                                echo "\t\t\t" . "<input type='hidden' name='sort' value='" . $sort . "'>" . "\n";
                                echo "\t\t\t" . "<input type='hidden' name='sort2' value='" . $sort2 . "'>" . "\n";
                            }
                            if ($type_search == "planet" || $type_search == "ally" || $type_search == "player") {
                                echo "\t\t\t" . "<input type='hidden' name='string_search' value='" . $string_search . "'>" . "\n";
                                if ($strict_on != "") {
                                    echo "\t\t\t" . "<input type='hidden' name='strict'>";
                                }
                            }
                            if ($type_search == "colonization" || $type_search == "moon" || $type_search == "away" || $type_search == "spy") {
                                echo "\t\t\t" . "<input type='hidden' name='galaxy_down' value='" . $galaxy_down . "'>" . "\n";
                                echo "\t\t\t" . "<input type='hidden' name='galaxy_up' value='" . $galaxy_up . "'>" . "\n";
                                echo "\t\t\t" . "<input type='hidden' name='system_down' value='" . $system_down . "'>" . "\n";
                                echo "\t\t\t" . "<input type='hidden' name='system_up' value='" . $system_up . "'>" . "\n";
                                echo "\t\t\t" . "<input type='hidden' name='row_down' value='" . $row_down . "'>" . "\n";
                                echo "\t\t\t" . "<input type='hidden' name='row_up' value='" . $row_up . "'>" . "\n";
                            }
                            if ($total_page > 1) {
                                echo "\t\t\t" . "<select class='og-button' name='page' onchange='this.form.submit();' onkeyup='this.form.submit();'>" . "\n";
                                for ($i = 1; $i <= $total_page; $i++) {
                                    $selected = "";
                                    if ($i == $page) {
                                        $selected = "selected";
                                    }
                                    echo "\t\t\t" . "<option value='" . $i . "' " . $selected . ">Page " . $i . "</option>" . "\n";
                                }
                                echo "\t\t\t" . "</select>";
                            } ?>
                        </form>
                    </th>
                </tr>
            </thead>
            <thead>
                <?php echo displayGalaxyTablethead(); ?>
            </thead>
            <tbody>
                <!-- affichage contenu de la reponse -->
                <?php foreach ($search_result as $v) : ?>

                    <?php if ($v["ally_name"] != "") : ?>
                        <?php $tooltiptab["allyName"][] = $v["ally_name"]; ?>
                        <?php $tooltiptab["allyId"][] = $v["ally_id"]; ?>
                    <?php endif; ?>
                    <?php $tooltiptab["playerName"][] = $v["player_name"]; ?>
                    <?php $tooltiptab["playerId"][] = $v["player_id"]; ?>

                    <?php echo displayGalaxyTabletbodytr($v, false); ?>
                <?php endforeach; ?>
            </tbody>
            <thead>
                <!--legende-->
                <tr>
                    <?php
                    $legend = displayGalaxyLegend();

                    //------------  ajout Tooltip ----------------
                    $ToolTip_Helper->addTooltip("legende",  $legend);
                    ?>
                    <th class="og-legend" colspan="11">
                        <a <?php echo  $ToolTip_Helper->GetHTMLClassContent(); ?>><?php echo  $lang['GALAXY_LEGEND']; ?></a>
                    </th>
                </tr>
                <!-- fin legende-->
            </thead>
        </table>

        <?php if ($type_search == "ally" || $type_search == "player") : ?>
            <?php $individual_ranking = null; ?>
            <?php if ($type_search == "ally") : ?>
                <?php $individual_ranking = galaxy_show_ranking_unique_ally($string_search); ?>
            <?php else : ?>
                <?php $individual_ranking = galaxy_show_ranking_unique_player($string_search); ?>
            <?php endif; ?>
            <table class="og-table og-medium-table og-table-ranking">

                <thead>
                    <tr>
                        <?php if ($type_search == "ally") : ?>
                            <th colspan="16">
                                <?php echo $lang['SEARCH_RANKOF']; ?> <a><?php echo $string_search; ?></a>
                            </th>
                        <?php else : ?>
                            <th colspan="15">
                                <?php echo $lang['SEARCH_RANKOF']; ?> <a><?php echo $string_search; ?></a>
                            </th>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <th>
                            <?php echo $lang['SEARCH_DATE']; ?>
                        </th>
                        <th colspan="2">
                            <?php echo $lang['SEARCH_RANK_GENERAL']; ?>
                        </th>
                        <th colspan="2">
                            <?php echo $lang['SEARCH_RANK_ECONOMY']; ?>
                        </th>
                        <th colspan="2">
                            <?php echo $lang['SEARCH_RANK_RESEARCH']; ?>
                        </th>
                        <th colspan="2">
                            <?php echo $lang['SEARCH_RANK_MILITARY']; ?>
                        </th>
                        <th colspan="2">
                            <?php echo $lang['SEARCH_RANK_MILITARY_LOST']; ?>
                        </th>
                        <th colspan="2">
                            <?php echo $lang['SEARCH_RANK_MILITARY_DESTROYED']; ?>
                        </th>
                        <th colspan="2">
                            <?php echo $lang['SEARCH_RANK_MILITARY_HONOR']; ?>
                        </th>

                        <?php if ($type_search == "ally") : ?>
                            <th>
                                <?php echo  $lang['SEARCH_NBMEMBERS']; ?>
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($individual_ranking as $ranking) : ?>
                        <?php
                        $datadate = date("d M Y H:i", key($individual_ranking));
                        $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
                        $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : "&nbsp;";
                        $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : "&nbsp;";
                        $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : "&nbsp;";
                        $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : "&nbsp;";
                        $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : "&nbsp;";
                        $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : "&nbsp;";
                        $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : "&nbsp;";
                        $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : "&nbsp;";
                        $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : "&nbsp;";
                        $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : "&nbsp;";
                        $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : "&nbsp;";
                        $honnor_rank = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["rank"]) : "&nbsp;";
                        $honnor_points = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["points"]) : "&nbsp;";
                        ?>
                        <tr>
                            <td>
                                <?php echo  $datadate; ?>
                            </td>
                            <td>
                                <?php echo  $general_points; ?>
                            </td>
                            <td>
                                <span class="ranking-subrank-number">
                                    <?php echo  $general_rank; ?>
                                </span>
                            </td>
                            <td>
                                <span class="ranking-subrank-number">
                                    <?php echo  $eco_rank; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo  $eco_points; ?>
                            </td>
                            <td>
                                <span class="ranking-subrank-number">
                                    <?php echo  $techno_rank; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo  $techno_points; ?>
                            </td>
                            <td>
                                <span class="ranking-subrank-number">
                                    <?php echo  $military_rank; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo  $military_points; ?>
                            </td>
                            <td>
                                <span class="ranking-subrank-number">
                                    <?php echo  $military_l_rank; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo  $military_l_points; ?>
                            </td>
                            <td>
                                <span class="ranking-subrank-number">
                                    <?php echo  $military_d_rank; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo  $military_d_points; ?>
                            </td>
                            <td>
                                <span class="ranking-subrank-number">
                                    <?php echo  $honnor_rank; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo  $honnor_points; ?>
                            </td>
                            <?php if ($type_search == "ally") : ?>
                                <td>
                                    <?php echo   formate_number($ranking["number_member"]); ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif;?>
        <?php endif; ?>


    <?php
    // calcul de tous les tooltip player et alliance
    //tooltip player
    foreach ($tooltiptab["player"] as $player) {
        $tooltip = displayGalaxyPlayerTooltip($player);
        //------------  Affichage Tooltip ----------------
        $ToolTip_Helper->addTooltip("ttp_player_" . $player,  $tooltip);
    }
    //tooltup ally
    foreach ($tooltiptab["ally"] as $ally) {
        $tooltip =  displayGalaxyAllyTooltip($ally);
        //------------  Affichage Tooltip ----------------
        $ToolTip_Helper->addTooltip("ttp_alliance_" . $ally,  $tooltip);
    }
    ?>

</div> <!-- fin div class="page_search" -->
<?php
require_once("views/page_tail.php");
