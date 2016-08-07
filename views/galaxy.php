<?php
/**
 * Fonctions Affichage de la Galaxie
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


$info_system = galaxy_show();
$population = $info_system["population"];
$galaxy = $info_system["galaxy"];
$system = $info_system["system"];

$phalanx_list = galaxy_get_phalanx($galaxy, $system);

$galaxy_down = (($galaxy - 1) < 1) ? 1 : $galaxy - 1;
$galaxy_up = (($galaxy - 1) > intval($server_config['num_of_galaxies'])) ? intval($server_config['num_of_galaxies']) : $galaxy + 1;

$system_down = (($system - 1) < 1) ? 1 : $system - 1;
$system_up = (($system - 1) > intval($server_config['num_of_systems'])) ? intval($server_config['num_of_systems']) : $system + 1;

$favorites = galaxy_getfavorites();

$missil = "";
$request_usergroup = $db->sql_query("SELECT u.group_id, u.user_id, g.group_id, g.server_show_positionhided FROM " . TABLE_GROUP . " AS g, " . TABLE_USER_GROUP . " AS u WHERE g.server_show_positionhided >0 AND g.group_id = u.group_id AND u.user_id = '1' LIMIT 1 ");
if ($db->sql_numrows($request_usergroup)) {
    if (($server_config["portee_missil"] != "0" && $server_config["portee_missil"] != "")) {
        $missil = portee_missiles($galaxy, $system);
    }

}


require_once("views/page_header.php");
?>

<form>
    <input name="action" value="galaxy" type="hidden">
    <table border="0">
        <tr>
            <td>
                <table align="center">
                    <tr>
                        <td class="c" colspan="3"><?php echo($lang['GALAXY_SELECT_GALAXY']); ?></td>
                    </tr>
                    <tr>
                        <td class="l"><input type="button" value="<<<"
                                             onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy_down; ?>&amp;system=<?php echo $system; ?>';">
                        </td>
                        <td class="l"><input type="text" name="galaxy" maxlength="3" size="5"
                                             value="<?php echo $galaxy; ?>" tabindex="1"></td>
                        <td class="l"><input type="button" value=">>>"
                                             onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy_up; ?>&amp;system=<?php echo $system; ?>';">
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table align="center">
                    <tr>
                        <td class="c" colspan="3"><?php echo($lang['GALAXY_SELECT_SYSTEM']); ?></td>
                    </tr>
                    <tr>
                        <td class="l"><input type="button" value="<<<"
                                             onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy; ?>&amp;system=<?php echo $system_down; ?>';">
                        </td>
                        <td class="l"><input type="text" name="system" maxlength="3" size="5"
                                             value="<?php echo $system; ?>" tabindex="2"></td>
                        <td class="l"><input type="button" value=">>>"
                                             onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy; ?>&amp;system=<?php echo $system_up; ?>';">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr align="center">
            <td colspan="3"><input type="submit" value="<?php echo($lang['GALAXY_DISPLAY']); ?>"></td>
        </tr>
    </table>
</form>
<form method="POST" action="index.php?action=galaxy">
    <table width="860">
        <tr>
            <td colspan="3" align="left">
                <label>
                    <select name="coordinates" onchange="this.form.submit();" onkeyup="this.form.submit();">
                        <option><?php echo($lang['GALAXY_FAVORITE_LIST']); ?></option>
                        <?php
                    foreach ($favorites as $v) {
                        $coordinate = $v["galaxy"] . ":" . $v["system"];
                        echo "\t\t\t" . "<option value='" . $coordinate . "'>" . $coordinate . "</option>";
                        }
                        ?>
                    </select>
                </label>
            </td>

            <td colspan="6" align="right">
                <?php
                if (sizeof($favorites) < $server_config['max_favorites'])
                    $string_addfavorites = "window.location = 'index.php?action=add_favorite&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "';";
                else
                    $string_addfavorites = "alert('".$lang['GALAXY_MAX_FAVORITES_MESSAGE']." (" . $server_config['max_favorites'] . ")')";

                if (sizeof($favorites) > 0)
                    $string_delfavorites = "window.location = 'index.php?action=del_favorite&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "';";
                else
                    $string_delfavorites = "alert('".$lang['GALAXY_NO_FAVORITES_MESSAGE']."')";
                ?>
                <input type="button" value="<?php echo($lang['GALAXY_ADD_FAVORITES']); ?>" onclick="<?php echo $string_addfavorites; ?>">
                <input type="button" value="<?php echo($lang['GALAXY_REMOVE_FAVORITES']); ?>" onclick="<?php echo $string_delfavorites; ?>">
            </td>
        </tr>
        <tr>
            <td class="c" style="text-align: left" colspan="9"><?php echo($lang['GALAXY_SYSTEMS']); ?><?php echo $missil;?></td>
        </tr>
        <tr>
            <td class="c" width="25">&nbsp;</td>
            <td class="c" width="175"><?php echo($lang['GALAXY_PLANETS']); ?></td>
            <td class="c" width="175"><?php echo($lang['GALAXY_ALLIES']); ?></td>
            <td class="c" width="175"><?php echo($lang['GALAXY_PLAYERS']); ?></td>
            <td class="c" width="40">&nbsp;</td>
            <td class="c" width="20">&nbsp;</td>
            <td class="c" width="20">&nbsp;</td>
            <td class="c" width="20">&nbsp;</td>
            <td class="c" width="250"><?php echo($lang['GALAXY_UPDATES']); ?></td>
        </tr>
        <?php
        $i = 1;
        foreach ($population as $v) {
            $begin_hided = "";
            $end_hided = "";
            if ($v["hided"]) {
                $begin_hided = "<span style=\"color: lime; \">";
                $end_hided = "</span>";
            }
            $begin_allied = "";
            $end_allied = "";
            if ($v["allied"]) {
                $begin_allied = "<blink>";
                $end_allied = "</blink>";
            }

            $id = $i;
            $planet = $v["planet"];
            $ally = $v["ally"];
            $player = $v["player"];
            $moon = $v["moon"];
            $last_update_moon = $v["last_update_moon"];
            $phalanx = $v["phalanx"];
            $gate = $v["gate"] == 1;
            $status = $v["status"];
            $timestamp = $v["timestamp"];
            $poster = "&nbsp;";
            if ($timestamp != 0) {
                $timestamp = strftime("%d %b %Y %H:%M", $timestamp);
                $poster = $timestamp . " - " . $v["poster"];
            }

            if ($planet == "") $planet = "&nbsp;";
            else $planet = "<a href='index.php?action=search&amp;type_search=planet&amp;string_search=" . $planet . "&amp;strict=on'>" . $begin_allied . $begin_hided . $planet . $end_hided . $end_allied . "</a>";

            if ($ally == "") $ally = "&nbsp;";
            else {
                $tooltip = "<table width=\"250\" style=\"color:white;\">";
                $tooltip .= "<tr><td colspan=\"3\" class=\"c\" align=\"center\">".$lang['GALAXY_ALLY']." " . $ally . "</td></tr>";

                $individual_ranking = galaxy_show_ranking_unique_ally($ally);
                while ($ranking = current($individual_ranking)) {
                    $datadate = strftime("%d %b %Y %H:%M", key($individual_ranking));
                    $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
                    $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : "&nbsp;";
                    $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : "&nbsp;";
                    $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : "&nbsp;";
                    $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : "&nbsp;";
                    $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : "&nbsp;";
                    $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : "&nbsp;";
                    $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : "&nbsp;";
                    $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) : "&nbsp;";
                    $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) : "&nbsp;";
                    $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : "&nbsp;";
                    $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : "&nbsp;";
                    $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : "&nbsp;";
                    $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : "&nbsp;";
                    $honnor_rank = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["rank"]) : "&nbsp;";
                    $honnor_points = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["points"]) : "&nbsp;";

                    $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\">".$lang['GALAXY_RANK']." " . $datadate . "</td></tr>";
                    $tooltip .= "<tr><td class=\"c\" width=\"75\">".$lang['GALAXY_RANK_GENERAL']."</td><th width=\"30\">" . $general_rank . "</th><th>" . $general_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_ECONOMY']."</td><th>" . $eco_rank . "</th><th>" . $eco_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_LAB']."</td><th>" . $techno_rank . "</th><th>" . $techno_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY']."</td><th width=\"30\">" . $military_rank . "</th><th>" . $military_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_BUILT']."</td><th width=\"30\">" . $military_b_rank . "</th><th>" . $military_b_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_LOST']."</td><th>" . $military_l_rank . "</th><th>" . $military_l_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_DESTROYED']."</td><th>" . $military_d_rank . "</th><th>" . $military_d_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_HONNOR']."</td><th>" . $honnor_rank . "</th><th>" . $honnor_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\">" . formate_number($ranking["number_member"]) . " ".$lang['GALAXY_MEMBERS']."</td></tr>";
                    break;
                }
                $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\"><a href=\"index.php?action=search&amp;type_search=ally&amp;string_search=" . $ally . "&strict=on\">".$lang['GALAXY_SEE_DETAILS']."</a></td></tr>";
                $tooltip .= "</table>";
                if (version_compare(phpversion(), '5.4.0', '>=')) {
                    $tooltip = htmlentities($tooltip, ENT_COMPAT | ENT_HTML401, "UTF-8");
                } else {
                    $tooltip = htmlentities($tooltip, ENT_COMPAT, "UTF-8");
                }

                $ally = "<a href='index.php?action=search&amp;type_search=ally&amp;string_search=" . $ally . "&amp;strict=on' onmouseover=\"this.T_WIDTH=260;this.T_TEMP=15000;return escape('" . $tooltip . "')\">" . $begin_allied . $begin_hided . $ally . $end_hided . $end_allied . "</a>";
            }

            if ($player == "") $player = "&nbsp;";
            else {
                $tooltip = "<table width=\"250\" style=\"color:white;\">";
                $tooltip .= "<tr><td colspan=\"3\" class=\"c\" align=\"center\">".$lang['GALAXY_PLAYER']." " . $player . "</td></tr>";

                $individual_ranking = galaxy_show_ranking_unique_player($player);
                while ($ranking = current($individual_ranking)) {
                    $datadate = strftime("%d %b %Y %H:%M", key($individual_ranking));
                    $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
                    $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : "&nbsp;";
                    $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : "&nbsp;";
                    $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : "&nbsp;";
                    $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : "&nbsp;";
                    $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : "&nbsp;";
                    $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : "&nbsp;";
                    $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : "&nbsp;";
                    $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) : "&nbsp;";
                    $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) : "&nbsp;";
                    $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : "&nbsp;";
                    $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : "&nbsp;";
                    $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : "&nbsp;";
                    $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : "&nbsp;";
                    $honnor_rank = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["rank"]) : "&nbsp;";
                    $honnor_points = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["points"]) : "&nbsp;";

                    $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\">".$lang['GALAXY_RANK']." " . $datadate . "</td></tr>";
                    $tooltip .= "<tr><td class=\"c\" width=\"75\">".$lang['GALAXY_RANK_GENERAL']."</td><th width=\"30\">" . $general_rank . "</th><th>" . $general_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_ECONOMY']."</td><th>" . $eco_rank . "</th><th>" . $eco_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_LAB']."</td><th>" . $techno_rank . "</th><th>" . $techno_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY']."</td><th width=\"30\">" . $military_rank . "</th><th>" . $military_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_BUILT']."</td><th>" . $military_b_rank . "</th><th>" . $military_b_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_LOST']."</td><th>" . $military_l_rank . "</th><th>" . $military_l_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_DESTROYED']."</td><th>" . $military_d_rank . "</th><th>" . $military_d_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_MILITARY_HONNOR']."</td><th>" . $honnor_rank . "</th><th>" . $honnor_points . "</th></tr>";
                    break;
                }
                $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\"><a href=\"index.php?action=search&amp;type_search=player&amp;string_search=" . $player . "&amp;strict=on\">".$lang['GALAXY_SEE_DETAILS']."</a></td></tr>";
                $tooltip .= "</table>";
                if (version_compare(phpversion(), '5.4.0', '>=')) {
                    $tooltip = htmlentities($tooltip, ENT_COMPAT | ENT_HTML401, "UTF-8");
                } else {
                    $tooltip = htmlentities($tooltip, ENT_COMPAT, "UTF-8");
                }

                $player = "<a href='index.php?action=search&amp;type_search=player&amp;string_search=" . $player . "&amp;strict=on' onmouseover=\"this.T_WIDTH=260;this.T_TEMP=15000;return escape('" . $tooltip . "')\">" . $begin_allied . $begin_hided . $player . $end_hided . $end_allied . "</a>";
            }

            if ($status == "") $status = "&nbsp;";

            if ($moon == 1) {
                $moon = '<img src="assets/default_skin/img/lune.png">';
                $detail = "";
                if ($last_update_moon > 0) {
                    $detail .= $phalanx;
                }
                if ($gate == 1) {
                    $detail .= "P";
                }
                if ($detail != "") $moon .= " - " . $detail;
            } else $moon = "&nbsp;";

            if ($v["report_spy"] > 0) $spy = "<a href='#' onClick=\"window.open('index.php?action=show_reportspy&amp;galaxy=$galaxy&amp;system=$system&amp;row=$i','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)\">".$lang['GALAXY_SR']."</a>";
            else $spy = "&nbsp;";

            if (isset($v["report_rc"]) && $v["report_rc"] > 0) $rc = "<a href='#' onClick=\"window.open('index.php?action=show_reportrc&amp;galaxy=$galaxy&amp;system=$system&amp;row=$i','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)\">" . $v["report_rc"] . $lang['GALAXY_CR']."</a>";
            else $rc = "&nbsp;";

            echo "<tr>" . "\n";
            echo "\t" . "<th>" . $id . "</th>" . "\n";
            echo "\t" . "<th>" . $planet . "</th>" . "\n";
            echo "\t" . "<th>" . $ally . "</th>" . "\n";
            echo "\t" . "<th>" . $player . "</th>" . "\n";
            echo "\t" . "<th>" . $moon . "</th>" . "\n";
            echo "\t" . "<th>" . $status . "</th>" . "\n";
            echo "\t" . "<th>" . $spy . "</th>" . "\n";
            echo "\t" . "<th>" . $rc . "</th>" . "\n";
            echo "\t" . "<th>" . $poster . "</th>" . "\n";
            echo "</tr>" . "\n";

            $i++;
        }
        $legend = "<table width=\"225\">";
        $legend .= "<tr><td class=\"c\" colspan=\"2\" align=\"center\"e width=\"150\">".$lang['GALAXY_LEGEND']."</td></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_INACTIVE_7Days']."</td><th>".$lang['GALAXY_INACTIVE_7Days_SYMBOL']."</th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_INACTIVE_28Days']."</td><th>".$lang['GALAXY_INACTIVE_28Days_SYMBOL']."</th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_HOLIDAYS']."</td><th>".$lang['GALAXY_HOLIDAYS_SYMBOL']."</th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_WEAK_PROTECTION']."</td><th>".$lang['GALAXY_WEAK_PROTECTION_SYMBOL']."</th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_MOON']."<br><i>".$lang['GALAXY_MOON_PHALANX']."</i></td><th><img src=\"assets/default_skin/img/lune.png\">".$lang['GALAXY_MOON_PHALANX_SYMBOL']."</th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_SPYREPORT']."</td><th>".$lang['GALAXY_SPYREPORT_SYMBOL']."</th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_COMBATREPORT']."</td><th>".$lang['GALAXY_COMBATREPORT_SYMBOL']."</th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_ALLY_FRIEND']."</td><th><blink><a>abc</a></blink></th></tr>";
        $legend .= "<tr><td class=\"c\">".$lang['GALAXY_ALLY_HIDDEN']. "</td><th><span style=\"color: lime; \">abc</span></th></tr>";
        $legend .= "</table>";
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $legend = htmlentities($legend, ENT_COMPAT | ENT_HTML401, "UTF-8");
        } else {
            $legend = htmlentities($legend, ENT_COMPAT, "UTF-8");
        }

        echo "<tr align='center'><td class='c' colspan='9'><a style='cursor:pointer' onmouseover=\"this.T_WIDTH=210;this.T_TEMP=0;return escape('" . $legend . "')\">".$lang['GALAXY_LEGEND']."</a></td></tr>";
        echo "</table></form>";


        //Phalange
        echo "<br><table width='860' border='1'>";
        echo "<tr><td class='c' align='center'>".$lang['GALAXY_PHALANX_LIST']. help("galaxy_phalanx") . "</td></tr>";
        if (sizeof($phalanx_list) > 0) {
            foreach ($phalanx_list as $value) {

                echo "<tr align='left'><th>";

                if ($value["ally"] != "") {
                    $individual_ranking = galaxy_show_ranking_unique_ally($value["ally"]);
                    $tooltip = "<table width=\"250\" style=\"color:white;\">";
                    $tooltip .= "<tr><td colspan=\"3\" class=\"c\" align=\"center\">".$lang['GALAXY_ALLY']." " . $value["ally"] . "</td></tr>";
                    while ($ranking = current($individual_ranking)) {
                        $datadate = strftime("%d %b %Y à %Hh", key($individual_ranking));
                        $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
                        $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) . " <i>( " . formate_number($ranking["general"]["points_per_member"]) . " )</i>" : "&nbsp;";
                        $fleet_rank = isset($ranking["fleet"]) ? formate_number($ranking["fleet"]["rank"]) : "&nbsp;";
                        $fleet_points = isset($ranking["fleet"]) ? formate_number($ranking["fleet"]["points"]) . " <i>( " . formate_number($ranking["fleet"]["points_per_member"]) . " )</i>" : "&nbsp;";
                        $research_rank = isset($ranking["research"]) ? formate_number($ranking["research"]["rank"]) : "&nbsp;";
                        $research_points = isset($ranking["research"]) ? formate_number($ranking["research"]["points"]) . " <i>( " . formate_number($ranking["research"]["points_per_member"]) . " )</i>" : "&nbsp;";

                        $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\">".$lang['GALAXY_RANK']." ". $datadate . "</td></tr>";
                        $tooltip .= "<tr><td class=\"c\" width=\"75\">".$lang['GALAXY_RANK_GENERAL']."</td><th width=\"30\">" . $general_rank . "</th><th>" . $general_points . "</th></tr>";
                        $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_FLEET']."</td><th>" . $fleet_rank . "</th><th>" . $fleet_points . "</th></tr>";
                        $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_LAB']."</td><th>" . $research_rank . "</th><th>" . $research_points . "</th></tr>";
                        $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\">" . formate_number($ranking["number_member"]) . " ".$lang['GALAXY_MEMBERS']."</td></tr>";
                        break;
                    }
                    $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\"><a href=\"index.php?action=search&amp;type_search=ally&amp;string_search=" . $value["ally"] . "&amp;strict=on\">".$lang['GALAXY_SEE_DETAILS']."</a></td></tr>";
                    $tooltip .= "</table>";
                    if (version_compare(phpversion(), '5.4.0', '>=')) {
                        $tooltip = htmlentities($tooltip, ENT_COMPAT | ENT_HTML401, "UTF-8");
                    } else {
                        $tooltip = htmlentities($tooltip, ENT_COMPAT, "UTF-8");
                    }
                    echo "[<a href='index.php?action=search&&amp;type_search=ally&amp;string_search=" . $value["ally"] . "&amp;strict=on' onmouseover=\"this.T_WIDTH=260;this.T_TEMP=15000;return escape('" . $tooltip . "')\">" . $value["ally"] . "</a>]" . " ";
                }

                $individual_ranking = galaxy_show_ranking_unique_player($value["player"]);
                $tooltip = "<table width=\"250\" style=\"color:white;\">";
                $tooltip .= "<tr><td colspan=\"3\" class=\"c\" align=\"center\">".$lang['GALAXY_PLAYER']." " . $value["player"] . "</td></tr>";
                while ($ranking = current($individual_ranking)) {
                    $datadate = strftime("%d %b %Y à %Hh", key($individual_ranking));
                    $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
                    $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : "&nbsp;";
                    $fleet_rank = isset($ranking["fleet"]) ? formate_number($ranking["fleet"]["rank"]) : "&nbsp;";
                    $fleet_points = isset($ranking["fleet"]) ? formate_number($ranking["fleet"]["points"]) : "&nbsp;";
                    $research_rank = isset($ranking["research"]) ? formate_number($ranking["research"]["rank"]) : "&nbsp;";
                    $research_points = isset($ranking["research"]) ? formate_number($ranking["research"]["points"]) : "&nbsp;";

                    $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\">".$lang['GALAXY_RANK']." " . $datadate . "</td></tr>";
                    $tooltip .= "<tr><td class=\"c\" width=\"75\">".$lang['GALAXY_RANK_GENERAL']."</td><th width=\"30\">" . $general_rank . "</th><th>" . $general_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_FLEET']."</td><th>" . $fleet_rank . "</th><th>" . $fleet_points . "</th></tr>";
                    $tooltip .= "<tr><td class=\"c\">".$lang['GALAXY_RANK_LAB']."</td><th>" . $research_rank . "</th><th>" . $research_points . "</th></tr>";
                    break;
                }
                $tooltip .= "<tr><td class=\"c\" colspan=\"3\" align=\"center\"><a href=\"index.php?action=search&amp;type_search=player&amp;string_search=" . $value["player"] . "&amp;strict=on\">".$lang['GALAXY_SEE_DETAILS']."</a></td></tr>";
                $tooltip .= "</table>";
                if (version_compare(phpversion(), '5.4.0', '>=')) {
                    $tooltip = htmlentities($tooltip, ENT_COMPAT | ENT_HTML401, "UTF-8");
                } else {
                    $tooltip = htmlentities($tooltip, ENT_COMPAT, "UTF-8");
                }
                echo "<a href=\"index.php?action=search&amp;type_search=player&amp;string_search=" . $value["player"] . "&amp;strict=on\" onmouseover=\"this.T_WIDTH=260;this.T_TEMP=15000;return escape('" . $tooltip . "')\">" . $value["player"] . "</a> ".$lang['GALAXY_LUNA_PHALANX']." " . $value["level"];
                echo " en <a href='index.php?action=galaxy&amp;galaxy=" . $value["galaxy"] . "&amp;system=" . $value["system"] . "'>" . $value["galaxy"] . ":" . $value["system"] . ":" . $value["row"] . "</a> [<span style=\"color: orange; \">" . $value["galaxy"] . ":";


                echo $value['range_down'] . " <-> " . $value["galaxy"] . ":" . $value['range_up'] . "</span>]";

                if ($value["gate"] == "1") echo "<span style=\"color: red; \"> " .$lang['GALAXY_LUNA_GATE']. " </span>";
                echo ".</th></tr>";
            }
        } else echo "<tr><th>".$lang['GALAXY_LUNA_NOPHALANX']."</th></tr>";
        echo "</table>";


        //Raccourci recherche
        $tooltip_begin = "<table width=\"200\">";
        $tooltip_end = "</table>";

        $tooltip_colonization = $tooltip_moon = $tooltip_away = $tooltip_spy = "";
        for ($i = 10; $i <= 50; $i = $i + 10) {
            if ($system - $i >= 1) $down = $system - $i;
            else $down = 1;

            if ($system + $i <= intval($server_config['num_of_systems'])) $up = $system + $i;
            else $up = intval($server_config['num_of_systems']);

            $tooltip_colonization .= "<tr><th><a href=\"index.php?action=search&amp;type_search=colonization&amp;galaxy_down=" . $galaxy . "&amp;galaxy_up=" . $galaxy . "&amp;system_down=" . $down . "&amp;system_up=" . $up . "&amp;row_down=&amp;row_up=\">" . $i . " ".$lang['GALAXY_SURROUNDING_SYSTEMS']."</a></th></tr>";
            $tooltip_moon .= "<tr><th><a href=\"index.php?action=search&amp;type_search=moon&amp;galaxy_down=" . $galaxy . "&amp;galaxy_up=" . $galaxy . "&amp;system_down=" . $down . "&amp;system_up=" . $up . "&amp;row_down=&amp;row_up=\">" . $i . " ".$lang['GALAXY_SURROUNDING_SYSTEMS']."</a></th></tr>";
            $tooltip_away .= "<tr><th><a href=\"index.php?action=search&amp;type_search=away&amp;galaxy_down=" . $galaxy . "&amp;galaxy_up=" . $galaxy . "&amp;system_down=" . $down . "&amp;system_up=" . $up . "&amp;row_down=&amp;row_up=\">" . $i . " ".$lang['GALAXY_SURROUNDING_SYSTEMS']."</a></th></tr>";
            $tooltip_spy .= "<tr><th><a href=\"index.php?action=search&amp;type_search=spy&amp;galaxy_down=" . $galaxy . "&amp;galaxy_up=" . $galaxy . "&amp;system_down=" . $down . "&amp;system_up=" . $up . "&amp;row_down=&amp;row_up=\">" . $i . " ".$lang['GALAXY_SURROUNDING_SYSTEMS']."</a></th></tr>";
        }

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $tooltip_colonization = htmlentities($tooltip_begin . $tooltip_colonization . $tooltip_end, ENT_COMPAT | ENT_HTML401, "UTF-8");
            $tooltip_moon = htmlentities($tooltip_begin . $tooltip_moon . $tooltip_end, ENT_COMPAT | ENT_HTML401, "UTF-8");
            $tooltip_away = htmlentities($tooltip_begin . $tooltip_away . $tooltip_end, ENT_COMPAT | ENT_HTML401, "UTF-8");
            $tooltip_spy = htmlentities($tooltip_begin . $tooltip_spy . $tooltip_end, ENT_COMPAT | ENT_HTML401, "UTF-8");
        } else {
            $tooltip_colonization = htmlentities($tooltip_begin . $tooltip_colonization . $tooltip_end, ENT_COMPAT, "UTF-8");
            $tooltip_moon = htmlentities($tooltip_begin . $tooltip_moon . $tooltip_end, ENT_COMPAT, "UTF-8");
            $tooltip_away = htmlentities($tooltip_begin . $tooltip_away . $tooltip_end, ENT_COMPAT, "UTF-8");
            $tooltip_spy = htmlentities($tooltip_begin . $tooltip_spy . $tooltip_end, ENT_COMPAT, "UTF-8");

        }
        echo "<br><table width='860' border='1'>";
        echo "<tr><td class='c' align='center' colspan='4'>".$lang['GALAXY_SEARCH']."</td></tr>";
        echo "<tr align='center'>";
        echo "<th width='25%' onmouseover=\"this.T_WIDTH=210;return escape('" . $tooltip_colonization . "')\">".$lang['GALAXY_SEARCH_PLANETS_AVAILABLE']."</th>";
        echo "<th width='25%' onmouseover=\"this.T_WIDTH=210;return escape('" . $tooltip_moon . "')\">".$lang['GALAXY_SEARCH_MOONS']."</th>";
        echo "<th width='25%' onmouseover=\"this.T_WIDTH=210;return escape('" . $tooltip_away . "')\">".$lang['GALAXY_SEARCH_INACTIVES']."</th>";
        echo "<th width='25%' onmouseover=\"this.T_WIDTH=210;return escape('" . $tooltip_spy . "')\">".$lang['GALAXY_SEARCH_SPYREPORTS']."</th>";
        echo "</tr>";
        echo "</table>";


        require_once("views/page_tail.php");
        ?>