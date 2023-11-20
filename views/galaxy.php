<?php

/**
 * Fonctions Affichage de la Galaxie
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

use Ogsteam\Ogspy\Helper\ToolTip_Helper;

global $user_data;
$ToolTip_Helper = new ToolTip_Helper();

//var_dump($user_data);

$info_system = galaxy_show();
$population = $info_system["population"];
$galaxy = $info_system["galaxy"];
$system = $info_system["system"];

$phalanx_list = galaxy_get_phalanx($galaxy, $system, $user_data['user_class']);

$galaxy_down = (($galaxy - 1) < 1) ? 1 : $galaxy - 1;
$galaxy_up = (($galaxy - 1) > intval($server_config['num_of_galaxies'])) ? intval($server_config['num_of_galaxies']) : $galaxy + 1;

$system_down = (($system - 1) < 1) ? 1 : $system - 1;
$system_up = (($system - 1) > intval($server_config['num_of_systems'])) ? intval($server_config['num_of_systems']) : $system + 1;

$favorites = galaxy_getfavorites();

$tooltiptab = array("player" => array(), "ally" => array()); // conteneur des tooltips a creer

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
        $missil = galaxy_portee_missiles($galaxy, $system);
    }
}



require_once("views/page_header.php");
?>
<div class="page_galaxy">

    <form>
        <input name="action" value="galaxy" type="hidden">
        <table class="og-table og-small-table og-table-galaxyform ">
            <thead>
                <tr>
                    <th colspan="3">
                        <?php echo ($lang['GALAXY_SELECT_GALAXY']); ?>
                    </th>
                    <th colspan="3">
                        <?php echo ($lang['GALAXY_SELECT_SYSTEM']); ?>
                    </th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td clas="content">
                        <input type="button" class="og-button " value="<<<" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy_down; ?>&amp;system=<?php echo $system; ?>';">
                    </td>
                    <td clas="content">
                        <input type="text" name="galaxy" maxlength="3" size="5" value="<?php echo $galaxy; ?>" tabindex="1">
                    </td>
                    <td clas="content">
                        <input type="button" class="og-button" value=">>>" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy_up; ?>&amp;system=<?php echo $system; ?>';">
                    </td>
                    <td clas="content">
                        <input type="button" class="og-button" value="<<<" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy; ?>&amp;system=<?php echo $system_down; ?>';">
                    </td>
                    <td clas="content">
                        <input type="text" name="system" maxlength="3" size="5" value="<?php echo $system; ?>" tabindex="2">
                    </td>
                    <td clas="content">
                        <input type="button" class="og-button" value=">>>" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?php echo $galaxy; ?>&amp;system=<?php echo $system_up; ?>';">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="submit" class="og-button" value="<?php echo ($lang['GALAXY_DISPLAY']); ?>">
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <form method="POST" action="index.php?action=galaxy">
        <table class="og-table og-medium-table og-table-galaxy ">
            <thead>
                <tr>
                    <td colspan="11">
                        <select name="coordinates" onchange="this.form.submit();" onkeyup="this.form.submit();">
                            <option><?php echo ($lang['GALAXY_FAVORITE_LIST']); ?></option>
                            <?php foreach ($favorites as $v) : ?>
                                <?php $coordinate = $v["galaxy"] . ":" . $v["system"];; ?>
                                <option value='<?php echo $coordinate; ?>'>
                                    <?php echo $coordinate; ?>
                                </option>
                            <?php endforeach ?>
                        </select>

                        <?php if (sizeof($favorites) < $server_config['max_favorites']) : ?>
                            <?php $string_addfavorites = "window.location = 'index.php?action=add_favorite&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "';"; ?>
                        <?php else : ?>
                            <?php $string_addfavorites = "alert('" . $lang['GALAXY_NO_FAVORITES_MESSAGE'] . "')"; ?>
                        <?php endif; ?>

                        <?php if (sizeof($favorites) > 0) : ?>
                            <?php $string_delfavorites = "window.location ='index.php?action=del_favorite&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "';"; ?>
                        <?php else : ?>
                            <?php $string_delfavorites = "alert('" . $lang['GALAXY_NO_FAVORITES_MESSAGE'] . "')"; ?>
                        <?php endif; ?>

                        <input class="og-button og-button-success" type="button" value="<?php echo ($lang['GALAXY_ADD_FAVORITES']); ?>" onclick="<?php echo $string_addfavorites; ?>">
                        <input class="og-button og-button-danger" type="button" value="<?php echo ($lang['GALAXY_REMOVE_FAVORITES']); ?>" onclick="<?php echo $string_delfavorites; ?>">
                    </td>
                </tr>

                <tr>
                    <th colspan="11">
                        <?php echo ($lang['GALAXY_SYSTEMS']); ?><?php echo $missil; ?>
                    </th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                    <th><?php echo ($lang['GALAXY_PLANETS']); ?></th>
                    <th><?php echo ($lang['GALAXY_ALLIES']); ?></th>
                    <th><?php echo ($lang['GALAXY_PLAYERS']); ?></th>
                    <th>L</th>
                    <th>P</th>
                    <th>Ph</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><?php echo ($lang['GALAXY_UPDATES']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($population as $v) : ?>
                    <?php
                    $ally = $v["ally"];
                    $player = $v["player"];

                    if ($ally != "" && !isset($tooltiptab["ally"][$ally])) {
                        $tooltiptab["ally"][] = $ally;  //tab de creation du tooltip alliance si necessaire
                    }


                    if ($player != "" && !isset($tooltiptab["player"][$player])) {
                        $tooltiptab["player"][] = $player;  //tab de creation du tooltip joueur si necessaire
                    }

                    ?>

                    <?php $classishided =  ($v["hided"]) ? "tr-ishided" : ""; ?>
                    <?php $classisallied =  ($v["allied"]) ? "tr-isallied" : ""; ?>
                    <?php $empytag =  ($v["planet"] == "") ? "empty" : ""; ?>
                    <tr class="<?php echo $classishided . " " . $classisallied . " " . $empytag; ?>">
                        <td class="tdcontent"> <!-- compteur -->
                            <?php echo $i; ?>
                        </td>
                        <td class="tdcontent">
                            <?php if ($v["planet"] == "") : ?>
                                &nbsp;
                            <?php else : ?>
                                <a href='index.php?action=search&amp;type_search=planet&amp;string_search=<?php echo $v["planet"]; ?>&amp;strict=on'>
                                    <?php echo $v["planet"]; ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="tdcontent"><!-- alliance -->
                            <?php if ($v["ally"] != "") : ?>
                                <a <?php echo $ToolTip_Helper->GetHTMLClassContent(array("tooltipstered"), "ttp_alliance_" . $v["ally"]); ?> href="index.php?action=search&amp;type_search=ally&amp;string_search=<?php echo $ally; ?>&strict=on">
                                    <?php echo $ally; ?>
                                </a>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td class="tdcontent"><!-- player -->
                            <?php if ($v["player"] != "") : ?>
                                <a <?php echo $ToolTip_Helper->GetHTMLClassContent(array("tooltipstered"), "ttp_player_" . $v["player"]); ?> href="index.php?action=search&amp;type_search=ally&amp;string_search=<?php echo $player; ?>&strict=on">
                                    <?php echo  $player; ?>
                                </a>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td class="tdcontent">
                            <?php if ($v["moon"] == 1) : ?>
                                <span class="ogame-icon ogame-icon-moon ">
                                    &nbsp;
                                </span>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td class="tdcontent"> <!-- Porte -->
                            <?php if ($v["gate"] == 1) : ?>
                                <span class="ogame-icon ogame-icon-gate ">
                                    P
                                </span>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td class="tdcontent">
                            <?php if ($v["last_update_moon"] > 0) : ?>
                                <span class="ogame-icon ogame-icon-phalanx ">
                                    <?php echo $v["phalanx"]; ?>
                                </span>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                            <!-- todo icon-->
                        </td>
                        <td class="tdcontent"> <!-- status -->
                            <?php $states = ($v["status"] != "") ? str_split($v["status"]) : array(); ?>
                            <?php foreach ($states as $state) : ?>
                                <span class="ogame-status-<?php echo $state; ?>"><?php echo $state; ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td class="tdcontent"> <!-- spy -->
                            <?php if ($v["report_spy"] > 0) : ?>
                                <a href='#' onClick="window.open('index.php?action=show_reportspy&amp;galaxy=<?php echo $galaxy;?>&amp;system=<?php echo $system;?>&amp;row=<?php echo $i; ?>','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)">
                                    <?php echo $lang['GALAXY_SR']; ?>
                                </a>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td class="tdcontent"> <!-- RC -->
                            <?php if (isset($v["report_rc"]) && $v["report_rc"] > 0) : ?>
                                <a href='#' onClick="window.open('index.php?action=show_reportrc&amp;galaxy=<?php echo $galaxy;?>&amp;system=<?php echo $system;?>&amp;row=<?php echo $i; ?>','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)">
                                    <?php echo $v["report_rc"] . $lang['GALAXY_CR']; ?>
                                </a>
                            <?php else : ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td class="tdcontent og-galaxy-tdtimestamp"> <!-- info maj -->
                            <?php $timestamp = (intval($v["timestamp"]) != 0) ?  date("d F o G:i", $v["timestamp"]) : "&nbsp;"; ?>
                            <span class="og-galaxy-timestamp"><?php echo $timestamp; ?></span>
                           <span class="og-galaxy-poster">
                                 - <?php echo $v["poster"]; ?>
                            </span>
                        </td>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </tbody>
            <thead>
                <!--legende-->
                <tr>
                    <?php
                    //creation de la table
                    $legend = '<table class="og-table og-small-table og-table-galaxy">';
                    $legend .= '<thead>';
                    $legend .= '<tr><th colspan="2">' . $lang['GALAXY_LEGEND'] . "</th></tr>";
                    $legend .= '</thead>';
                    $legend .= '<tbody>';
                    $legend .= "<tr><td>" . $lang['GALAXY_INACTIVE_7Days'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-i\">" . $lang['GALAXY_INACTIVE_7Days_SYMBOL'] . "</span></th></tr>";
                    $legend .= "<tr><td>" . $lang['GALAXY_INACTIVE_28Days'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-i\">" . $lang['GALAXY_INACTIVE_28Days_SYMBOL'] . "</span></th></tr>";
                    $legend .= "<tr><td>" . $lang['GALAXY_HOLIDAYS'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-v\">" . $lang['GALAXY_HOLIDAYS_SYMBOL'] . "<span></th></tr>";
                    $legend .= "<tr><td>" . $lang['GALAXY_WEAK_PROTECTION'] . "</td><td class=\"tdcontent\"><span class=\"ogame-status-d\">" . $lang['GALAXY_WEAK_PROTECTION_SYMBOL'] . "</span></th></tr>";
                    $legend .= "<tr><td>" . $lang['GALAXY_MOON'] . "</td><td class=\"tdcontent\"><span class=\"ogame-icon ogame-icon-moon \"><span></th></tr>";
                    $legend .= "<tr><td>" . $lang['GALAXY_MOON_PHALANX'] . "</td><td class=\"tdcontent\"><span class=\"ogame-icon ogame-icon-phalanx \">4</span><span class=\"ogame-icon ogame-icon-gate \">P</span></th></tr>";
                    $legend .= "<tr><td>" . $lang['GALAXY_SPYREPORT'] . "</td><td class=\"tdcontent\"><a>" . $lang['GALAXY_SPYREPORT_SYMBOL'] . "</a></th></tr>";
                    $legend .= "<tr><td>" . $lang['GALAXY_COMBATREPORT'] . "</td><td class=\"tdcontent\"><a>" . $lang['GALAXY_COMBATREPORT_SYMBOL'] . "</a></th></tr>";
                    $legend .= "<tr class=\"tr-ishided\"><td >" . $lang['GALAXY_ALLY_FRIEND'] . "</td><td class=\"tdcontent\"><a>abc</a></td></tr>";
                    $legend .= "<tr class=\"tr-isallied\"><td>" . $lang['GALAXY_ALLY_HIDDEN'] . "</td><td class=\"tdcontent\">abc</td></tr>";
                    $legend .= '</tbody>';
                    $legend .= "</table>";
                    $legend = htmlentities($legend);

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
    </form>



    <!-- phalange -->
    <table class="og-table og-medium-table og-table-galaxy">
        <thead>
            <tr>
                <th>
                    <?php echo $lang['GALAXY_PHALANX_LIST'] . help("galaxy_phalanx"); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (sizeof($phalanx_list) > 0) : ?>
                <?php foreach ($phalanx_list as $value) : ?>
                    <tr>
                        <td class="tdcontent">
                            <?php if ($value["ally"] != "") : ?>
                                <?php $tooltiptab["ally"][] = $value["ally"]; //pour calcul tooltip ;
                                ?>
                                [<a href='index.php?action=search&&amp;type_search=ally&amp;string_search=<?php echo $value["ally"]; ?>&amp;strict=on' <?php echo $ToolTip_Helper->GetHTMLClassContent(array("tooltipstered"), "ttp_alliance_" . $value["ally"]); ?>>
                                    <?php echo $value["ally"]; ?>
                                </a>]
                            <?php endif; ?>
                            <?php $tooltiptab["player"][] = $value["player"]; // pour calcul tooltip
                            ?>
                            <a href="index.php?action=search&amp;type_search=player&amp;string_search=<?php echo $value["player"]; ?>&amp;strict=on" <?php echo $ToolTip_Helper->GetHTMLClassContent(array("tooltipstered"), "ttp_player_" . $value["player"]); ?>>
                                <?php echo  $value["player"]; ?>
                            </a>
                            <?php echo $lang['GALAXY_LUNA_PHALANX']; ?> <span class="og-highlight"><?php echo $value["level"]; ?></span> en <a href='index.php?action=galaxy&amp;galaxy=<?php echo $value["galaxy"]; ?>&amp;system=<?php echo $value["system"]; ?>'><?php echo $value["galaxy"] . ":" . $value["system"] . ":" . $value["row"]; ?></a>
                            [<span class="og-warning"><?php echo $value["galaxy"] . ":" . $value['range_down'] . " <-> " . $value["galaxy"] . ":" . $value['range_up']; ?></span>]

                            <?php if ($value["gate"] == "1") : ?>
                                <span class="og-alert"><?php echo $lang['GALAXY_LUNA_GATE']; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td class="tdcontent">
                        <span class=og-warning>
                            <?php echo  $lang['GALAXY_LUNA_NOPHALANX']; ?>
                        </span>
                    <td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>



    <!-- phalange -->
    <table class="og-table og-medium-table og-table-galaxy-quickrecherche">
        <thead>
            <tr>
                <th colspan='20'>
                    <?php echo $lang['GALAXY_SEARCH']; ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>

                <td colspan="5">
                    <?php echo $lang['GALAXY_SEARCH_PLANETS_AVAILABLE']; ?>
                </td>
                <td colspan="5">
                    <?php echo $lang['GALAXY_SEARCH_MOONS']; ?>
                </td>
                <td colspan="5">
                    <?php echo $lang['GALAXY_SEARCH_INACTIVES']; ?>
                </td>
                <td colspan="5">
                    <?php echo $lang['GALAXY_SEARCH_SPYREPORTS']; ?>
                </td>
            </tr>
            <tr>
                <?php $quickrecherchtype = array("colonization", "moon", "away", "spy"); ?>
                <?php foreach ($quickrecherchtype as $quicktype) : ?>
                    <?php for ($i = 10; $i <= 50; $i = $i + 10) : ?>
                        <?php if ($system - $i >= 1) : ?>
                            <?php $down = $system - $i; ?>
                        <?php else : ?>
                            <?php $down = 1; ?>
                        <?php endif; ?>
                        <?php if ($system + $i <= intval($server_config['num_of_systems'])) : ?>
                            <?php $up = $system + $i; ?>
                        <?php else : ?>
                            <?php $up = intval($server_config['num_of_systems']); ?>
                        <?php endif; ?>
                        <td class="tdcontent" colspan="1">
                            <a href="index.php?action=search&amp;type_search=<?php echo $quicktype; ?>&amp;galaxy_down=<?php echo $galaxy; ?>&amp;galaxy_up=<?php echo $galaxy; ?>&amp;system_down=<?php echo $down; ?>&amp;system_up=<?php echo $up; ?>&amp;row_down=&amp;row_up=">
                                <?php echo $i; ?>
                            </a>
                        </td>
                    <?php endfor; ?>
                <?php endforeach; ?>
            <tr>
                <td class="tdcontent" colspan='20'>
                    <?php echo $lang['GALAXY_SURROUNDING_SYSTEMS']; ?>
                </td>
            </tr>
        </tbody>
    </table>

</div><!-- fin class='page_galaxy'-->


<?php
// calcul de tous les tooltip player et alliance
//tooltip player
foreach ($tooltiptab["player"] as $player) {
    $tooltip = '<table class="og-table og-small-table">';
    $tooltip .= "<thead><tr><th colspan=\"3\" >" . $lang['GALAXY_PLAYER'] . " " . $player . "</th></tr></thead>";
    $tooltip .= '<tbody>';
    $individual_ranking = galaxy_show_ranking_unique_player($player);
    while ($ranking = current($individual_ranking)) {
        $datadate =  date("d F o G:i", key($individual_ranking));
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

        $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\"><span class=\"og-highlight\">" . $lang['GALAXY_RANK'] . " " . $datadate . "</span></td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_GENERAL'] . "</td><td class=\"tdcontent\">" . $general_rank . "</td><td class=\"tdcontent\">" . $general_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_ECONOMY'] . "</td><td class=\"tdcontent\">" . $eco_rank . "</td><td class=\"tdcontent\">" . $eco_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_LAB'] . "</td><td class=\"tdcontent\">" . $techno_rank . "</td><td class=\"tdcontent\">" . $techno_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY'] . "</td><td class=\"tdcontent\">" . $military_rank . "</td><td class=\"tdcontent\">" . $military_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_BUILT'] . "</td><td class=\"tdcontent\">" . $military_b_rank . "</td><td class=\"tdcontent\">" . $military_b_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_LOST'] . "</td><td class=\"tdcontent\">" . $military_l_rank . "</td><td class=\"tdcontent\">" . $military_l_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_DESTROYED'] . "</td><td class=\"tdcontent\">" . $military_d_rank . "</td><td class=\"tdcontent\">" . $military_d_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_MILITARY_HONNOR'] . "</td><td class=\"tdcontent\">" . $honnor_rank . "</td><td class=\"tdcontent\">" . $honnor_points . "</td></tr>";
        break;
    }
    $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\"><a href=\"index.php?action=search&amp;type_search=player&amp;string_search=" . $player . "&amp;strict=on\">" . $lang['GALAXY_SEE_DETAILS'] . "</a></td></tr>";
    $tooltip .= '</tbody>';
    $tooltip .= "</table>";
    $tooltip = htmlentities($tooltip);
    //------------  Affichage Tooltip ----------------
    $ToolTip_Helper->addTooltip("ttp_player_" . $player,  $tooltip);
}
//tooltup ally
foreach ($tooltiptab["ally"] as $ally) {


    $tooltip = '<table class="og-table og-small-table">';
    $tooltip .= '<thead><tr><th colspan="3">' . $lang['GALAXY_ALLY'] . " " . $ally . '</th></tr></thead>';
    $tooltip .= '<tbody>';

    $individual_ranking = galaxy_show_ranking_unique_ally($ally);
    while ($ranking = current($individual_ranking)) {
        $datadate =  date("d F o G:i", key($individual_ranking));
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

        $tooltip .= "<tr><td class=\"tdcontent \" colspan=\"3\" ><span class=\"og-highlight\">" . $lang['GALAXY_RANK'] . " " . $datadate . "</span> </td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\" >" . $lang['GALAXY_RANK_GENERAL'] . "</td><td class=\"tdcontent\">" . $general_rank . "</td><td class=\"tdcontent\">" . $general_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_ECONOMY'] . "</td><td class=\"tdcontent\">" . $eco_rank . "</td><td class=\"tdcontent\">" . $eco_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_LAB'] . "</td><td class=\"tdcontent\">" . $techno_rank . "</td><td class=\"tdcontent\">" . $techno_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY'] . "</td><td class=\"tdcontent\">" . $military_rank . "</td><td class=\"tdcontent\">" . $military_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_BUILT'] . "</td><td class=\"tdcontent\">" . $military_b_rank . "</td><td class=\"tdcontent\">" . $military_b_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_LOST'] . "</td><td class=\"tdcontent\">" . $military_l_rank . "</td><td class=\"tdcontent\">" . $military_l_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_DESTROYED'] . "</td><td class=\"tdcontent\">" . $military_d_rank . "</td><td class=\"tdcontent\">" . $military_d_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdstat\">" . $lang['GALAXY_RANK_MILITARY_HONNOR'] . "</td><td class=\"tdcontent\">" . $honnor_rank . "</td><td class=\"tdcontent\">" . $honnor_points . "</td></tr>";
        $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\" ><span class=\"og-highlight\">" . formate_number($ranking["number_member"]) . "</span> " . $lang['GALAXY_MEMBERS'] . "</td></tr>";
        break;
    }
    $tooltip .= "<tr><td class=\"tdcontent\" colspan=\"3\"><a href=\"index.php?action=search&amp;type_search=ally&amp;string_search=" . $ally . "&strict=on\">" . $lang['GALAXY_SEE_DETAILS'] . "</a></td></tr>";
    $tooltip .= '</tbody>';
    $tooltip .= "</table>";

    $tooltip = htmlentities($tooltip);

    //------------  Affichage Tooltip ----------------
    $ToolTip_Helper->addTooltip("ttp_alliance_" . $ally,  $tooltip);
}



require_once("views/page_tail.php");
?>