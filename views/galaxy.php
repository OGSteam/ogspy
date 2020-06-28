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

use Ogsteam\Ogspy\Helper\ToolTip_Helper;
use Ogsteam\Ogspy\Helper\html_ogspy_Helper;

global $user_data;
$ToolTip_Helper = new ToolTip_Helper();

//var_dump($user_data);

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
//TODO sortir requete de la vue
//recherche du group
$user_group = (new \Ogsteam\Ogspy\Model\Group_Model())->get_user_group($user_data["user_id"]);
//recherche des droits liés
$tInfosGroups = (new \Ogsteam\Ogspy\Model\Group_Model())->get_group_rights($user_group);


//si autorisé server_show_positionhided doit etre a 1 !!!!!!!!!!!
//todo info a communiquer avec release
if ($tInfosGroups["server_show_positionhided"] == 1) {
    if (($server_config["portee_missil"] != "0" && $server_config["portee_missil"] != "")) {
        $missil = portee_missiles($galaxy, $system);
    }
}

//stockage du html des tooltips joueur alliance
$tab_tooltip_player = array();
$tab_tooltip_player[""] = '&nbsp;';
$tab_tooltip_ally = array();
$tab_tooltip_ally[""] = '&nbsp;';

/// préparation html des tooltip
/// dans galaxie et phalange
$tab_data = array($population, $phalanx_list);
foreach ($tab_data as $value) {
    foreach ($value as $v) {
        $ally = $v["ally"];
        $player = $v["player"];
        //alliance
        if ($ally != "" && !isset($tab_tooltip_ally[$ally])) {
            $tab_tooltip_ally[$ally] = (new html_ogspy_Helper())->show_html_ranking_unique_ally($ally);
        }
        //player
        if ($player != "" && !isset($tab_tooltip_player[$player])) {
            $tab_tooltip_player[$player] = (new html_ogspy_Helper())->show_html_ranking_unique_player($player);
        }
    }
}


require_once("views/page_header.php");

?>
    <form>
        <input name="action" value="galaxy" type="hidden">
        <div>
            <label for="galaxy"><?php echo($lang['GALAXY_SELECT_GALAXY']); ?></label>
            <input type="button" value="<<<"
                   onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy_down; ?>&amp;system=<?php echo $system; ?>';">
            <input type="text" name="galaxy" id="galaxy" maxlength="3" size="5" value="<?php echo $galaxy; ?>"
                   tabindex="1">
            <input type="button" value=">>>"
                   onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy_up; ?>&amp;system=<?php echo $system; ?>';">
        </div>

        <div>
            <label for="system"><?php echo($lang['GALAXY_SELECT_SYSTEM']); ?></label>
            <input type="button" value="<<<"
                   onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy; ?>&amp;system=<?php echo $system_down; ?>';">
            <input type="text" name="system" id="system" maxlength="3" size="5" value="<?php echo $system; ?>"
                   tabindex="2">
            <input type="button" value=">>>"
                   onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy; ?>&amp;system=<?php echo $system_up; ?>';">
        </div>
        <div>

            <input type="submit" value="<?php echo($lang['GALAXY_DISPLAY']); ?>">
        </div>
        <div class="sep">
        </div>
        <div>
            <?php
            if (sizeof($favorites) < $server_config['max_favorites']) {
                $string_addfavorites = "window.location = 'index.php?action=add_favorite&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "';";
            } else {
                $string_addfavorites = "alert('" . $lang['GALAXY_MAX_FAVORITES_MESSAGE'] . " (" . $server_config['max_favorites'] . ")')";
            }

            if (sizeof($favorites) > 0) {
                $string_delfavorites = "window.location = 'index.php?action=del_favorite&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "';";
            } else {
                $string_delfavorites = "alert('" . $lang['GALAXY_NO_FAVORITES_MESSAGE'] . "')";
            }
            ?>
            <select name="coordinates" onchange="this.form.submit();" onkeyup="this.form.submit();">
                <option><?php echo($lang['GALAXY_FAVORITE_LIST']); ?></option>
                <?php
                foreach ($favorites as $v) {
                    $coordinate = $v["galaxy"] . ":" . $v["system"];
                    echo "\t\t\t" . "<option value='" . $coordinate . "'>" . $coordinate . "</option>";
                }
                ?>
            </select>
            <input type="button" value="<?php echo($lang['GALAXY_ADD_FAVORITES']); ?>"
                   onclick="<?php echo $string_addfavorites; ?>">
            <input type="button" value="<?php echo($lang['GALAXY_REMOVE_FAVORITES']); ?>"
                   onclick="<?php echo $string_delfavorites; ?>">
        </div>

    </form>


    <table>
    <thead>
    <tr>
        <th colspan="9"><?php echo($lang['GALAXY_SYSTEMS']); ?><?php echo $missil; ?></th>
    </tr>
    </thead>
    <tbody>

<tr>
    <th>&nbsp;</th>
    <th><?php echo($lang['GALAXY_PLANETS']); ?></th>
    <th><?php echo($lang['GALAXY_ALLIES']); ?></th>
    <th><?php echo($lang['GALAXY_PLAYERS']); ?></th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
    <th><?php echo($lang['GALAXY_UPDATES']); ?></th>
</tr>
<?php
$i = 1;
foreach ($population as $v) {
    $begin_hided = "";
    $end_hided = "";
    if ($v["hided"]) {
        $begin_hided = '<span>'; //todo ne pas oublier dans css  / classname
        $end_hided = '</span>';
    }
    $begin_allied = "";
    $end_allied = "";
    if ($v["allied"]) {
        $begin_allied = "";//todo ne pas oublier dans css / classname
        $end_allied = "";
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
    $poster = '&nbsp;';
    if ($timestamp != 0) {
        $timestamp = strftime("%d %b %Y %H:%M", $timestamp);
        $poster = $timestamp . " - " . $v["poster"];
    }

    if ($planet == "") {
        $planet = '&nbsp;';
    } else {
        $planet = "<a href='index.php?action=search&amp;type_search=planet&amp;string_search=" . $planet . "&amp;strict=on'>" . $begin_allied . $begin_hided . $planet . $end_hided . $end_allied . "</a>";
    }

    $tooltip = $tab_tooltip_ally[$ally];
    //------------  Affichage Tooltip ----------------
    $ToolTip_Helper->addTooltip("ttp_alliance_" . $ally, $tooltip);
    $ally = '<a ' . $ToolTip_Helper->GetHTMLClassContent() . ' href="index.php?action=search&amp;type_search=ally&amp;string_search=' . $ally . '&strict=on">  ' . $begin_allied . $begin_hided . $ally . $end_hided . $end_allied . '  </a>';


    if ($player == "") {
        $player = '&nbsp;';
    } else {
        $tooltip = $tab_tooltip_player[$player];
        //------------  Affichage Tooltip ----------------
        $ToolTip_Helper->addTooltip("ttp_player_" . $player, $tooltip);
        $player = '<a ' . $ToolTip_Helper->GetHTMLClassContent() . ' href="index.php?action=search&amp;type_search=player&amp;string_search=' . $player . '&amp;strict=on">  ' . $begin_allied . $begin_hided . $player . $end_hided . $end_allied . '  </a>';
        //------------  Fin Affichage Tooltip ----------------
    }

    if ($status == "") {
        $status = '&nbsp;';
    }

    if ($moon == 1) {
        $moon = '<img src="skin/OGSpy_skin/img/lune.png">';
        $detail = "";
        if ($last_update_moon > 0) {
            $detail .= $phalanx;
        }
        if ($gate == 1) {
            $detail .= "P";
        }
        if ($detail != "") {
            $moon .= " - " . $detail;
        }
    } else {
        $moon = '&nbsp;';
    }

    if ($v["report_spy"] > 0) {
        $spy = "<a href='#' onClick=\"window.open('index.php?action=show_reportspy&amp;galaxy=$galaxy&amp;system=$system&amp;row=$i','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)\">" . $lang['GALAXY_SR'] . '</a>';
    } else {
        $spy = '&nbsp;';
    }

    if (isset($v["report_rc"]) && $v["report_rc"] > 0) {
        $rc = "<a href='#' onClick=\"window.open('index.php?action=show_reportrc&amp;galaxy=$galaxy&amp;system=$system&amp;row=$i','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)\">" . $v["report_rc"] . $lang['GALAXY_CR'] . '</a>';
    } else {
        $rc = '&nbsp;';
    }

    echo "<tr>\n";
    echo "\t<td>" . $id . "</td>\n";
    echo "\t<td>" . $planet . "</td>\n";
    echo "\t<td>" . $ally . "</td>\n";
    echo "\t<td>" . $player . "</td>\n";
    echo "\t<td>" . $moon . "</td>\n";
    echo "\t<td>" . $status . "</td>\n";
    echo "\t<td>" . $spy . "</td>\n";
    echo "\t<td>" . $rc . "</td>\n";
    echo "\t<td>" . $poster . "</td>\n";
    echo "</tr>\n";

    $i++;
}
$legend = '<table>';
$legend .= '<thead><tr><th colspan="2">' . $lang['GALAXY_LEGEND'] . '</th></tr></thead>';
$legend .= '<tbody>';
$legend .= '<tr><th >' . $lang['GALAXY_INACTIVE_7Days'] . '</th><td>' . $lang['GALAXY_INACTIVE_7Days_SYMBOL'] . '</td></tr>';
$legend .= '<tr><th  >' . $lang['GALAXY_INACTIVE_28Days'] . '</th><td>' . $lang['GALAXY_INACTIVE_28Days_SYMBOL'] . '</td></tr>';
$legend .= '<tr><th >' . $lang['GALAXY_HOLIDAYS'] . '</th><td>' . $lang['GALAXY_HOLIDAYS_SYMBOL'] . '</td></tr>';
$legend .= '<tr><th  >' . $lang['GALAXY_WEAK_PROTECTION'] . '</th><td>' . $lang['GALAXY_WEAK_PROTECTION_SYMBOL'] . '</td></tr>';
$legend .= '<tr><th  >' . $lang['GALAXY_MOON'] . '<br><span>' . $lang['GALAXY_MOON_PHALANX'] . '</span></th><td><img alt="lune" src="skin/OGSpy_skin/img/lune.png">' . $lang['GALAXY_MOON_PHALANX_SYMBOL'] . '</td></tr>';
$legend .= '<tr><th  >' . $lang['GALAXY_SPYREPORT'] . '</th><td>' . $lang['GALAXY_SPYREPORT_SYMBOL'] . '</td></tr>';
$legend .= '<tr><th  >' . $lang['GALAXY_COMBATREPORT'] . '</th><td>' . $lang['GALAXY_COMBATREPORT_SYMBOL'] . '</td></tr>';
$legend .= '<tr><th  >' . $lang['GALAXY_ALLY_FRIEND'] . '</th><td><blink><a>abc</a></blink></td></tr>';
$legend .= '<tr><th  >' . $lang['GALAXY_ALLY_HIDDEN'] . '</th><td><span style="color: lime;">abc</span></td></tr>';
$legend .= '</tbody>';
$legend .= '</table>';

//------------  Affichage Tooltip ----------------
$legend = htmlentitiesencode($legend);
$ToolTip_Helper->addTooltip("legende", $legend);
echo '<tr><td class="c" colspan="9">';
echo '<a style="cursor:pointer" ' . $ToolTip_Helper->GetHTMLClassContent() . ' >' . $lang['GALAXY_LEGEND'] . '</a>';
echo '</td></tr>';
echo ' </tbody>';
echo "</table>\n";
//------------ fin Affichage Tooltip ----------------

//Phalange
echo '<br><table>';
echo '<thead><tr><th>' . $lang['GALAXY_PHALANX_LIST'] . help("galaxy_phalanx") . '</th></tr></thead>';
echo '<tbody>';
if (sizeof($phalanx_list) > 0) {
    foreach ($phalanx_list as $value) {
        echo '<tr><td>';

        $tooltip = $tab_tooltip_ally[$value["ally"]];
        //------------  Affichage Tooltip ----------------
        $ToolTip_Helper->addTooltip("ttp_alliance_" . $value["ally"], $tooltip);
        echo '[<a href="index.php?action=search&&amp;type_search=ally&amp;string_search=' . $value["ally"] . '&amp;strict=on"  ' . $ToolTip_Helper->GetHTMLClassContent() . '>' . $value["ally"] . '</a>] ';


        //------------  Affichage Tooltip ----------------
        $tab_tooltip_player[$value['player']] = (new html_ogspy_Helper())->show_html_ranking_unique_player($value['player']);
        $ToolTip_Helper->addTooltip("ttp_player_" . $value['player'], $tooltip);
        $player = '<a ' . $ToolTip_Helper->GetHTMLClassContent() . ' href="index.php?action=search&amp;type_search=player&amp;string_search=' . $value['player'] . '&amp;strict=on">  ' . $value['player'] . '</a>';

        echo $player . $lang['GALAXY_LUNA_PHALANX'] . ' ' . $value['level'];
        echo ' en <a href="index.php?action=galaxy&amp;galaxy=' . $value['galaxy'] . '&amp;system=' . $value['system'] . '">' . $value['galaxy'] . ':' . $value['system'] . ':' . $value['row'] . '</a> [<span>' . $value['galaxy'] . ':';

        echo $value['range_down'] . ' <-> ' . $value['galaxy'] . ':' . $value['range_up'] . '</span>]';

        if ($value['gate'] == '1') {
            echo '<span> ' . $lang['GALAXY_LUNA_GATE'] . ' </span>';
        }
        echo '.</td></tr>';
    }
} else {
    echo '<tr><td>' . $lang['GALAXY_LUNA_NOPHALANX'] . '</td></tr>';
}
echo '</tbody>';
echo '</table>';


//Raccourci recherche
$tooltip_begin = '<table style="width:200px">';
$tooltip_end = '</table>';

$tooltip_colonization = $tooltip_moon = $tooltip_away = $tooltip_spy = "";
for ($i = 10; $i <= 50; $i = $i + 10) {
    if ($system - $i >= 1) {
        $down = $system - $i;
    } else {
        $down = 1;
    }

    if ($system + $i <= intval($server_config['num_of_systems'])) {
        $up = $system + $i;
    } else {
        $up = intval($server_config['num_of_systems']);
    }

    $tooltip_colonization .= '<td colspan="1"><a href="index.php?action=search&amp;type_search=colonization&amp;galaxy_down=' . $galaxy . '&amp;galaxy_up=' . $galaxy . '&amp;system_down=' . $down . '&amp;system_up=' . $up . '&amp;row_down=&amp;row_up=">' . $i . '</a></td>';
    $tooltip_moon .= '<td colspan="1"><a href="index.php?action=search&amp;type_search=moon&amp;galaxy_down=' . $galaxy . '&amp;galaxy_up=' . $galaxy . '&amp;system_down=' . $down . '&amp;system_up=' . $up . '&amp;row_down=&amp;row_up=">' . $i . '</a></td>';
    $tooltip_away .= '<td colspan="1"><a href="index.php?action=search&amp;type_search=away&amp;galaxy_down=' . $galaxy . '&amp;galaxy_up=' . $galaxy . '&amp;system_down=' . $down . '&amp;system_up=' . $up . '&amp;row_down=&amp;row_up=">' . $i . '</a></td>';
    $tooltip_spy .= '<td colspan="1"><a href="index.php?action=search&amp;type_search=spy&amp;galaxy_down=' . $galaxy . '&amp;galaxy_up=' . $galaxy . '&amp;system_down=' . $down . '&amp;system_up=' . $up . '&amp;row_down=&amp;row_up=">' . $i . '</a></td>';
}

echo '<table>';
echo '<thead><tr><th colspan="20">' . $lang['GALAXY_SEARCH'] . '</th></tr></thead>';
echo '<tbody>';
echo '<tr>';
echo '<th colspan="5">' . $lang['GALAXY_SEARCH_PLANETS_AVAILABLE'] . '</th>';
echo '<th colspan="5">' . $lang['GALAXY_SEARCH_MOONS'] . '</th>';
echo '<th colspan="5">' . $lang['GALAXY_SEARCH_INACTIVES'] . '</th>';
echo '<th  colspan="5">' . $lang['GALAXY_SEARCH_SPYREPORTS'] . '</th>';
echo '</tr>';
echo '<tr>';
echo $tooltip_colonization;
echo $tooltip_moon;
echo $tooltip_away;
echo $tooltip_spy;
echo '</tr>';
echo '<tr>';
echo '<th colspan="20">' . $lang['GALAXY_SURROUNDING_SYSTEMS'] . '</th>';
echo '</tr>';
echo '</tbody>';
echo '</table>';

require_once('views/page_tail.php');
?>