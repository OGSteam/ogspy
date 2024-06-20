<?php global $server_config, $lang;

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
use Ogsteam\Ogspy\Model\Group_Model;

global $user_data;
$ToolTip_Helper = new ToolTip_Helper();

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
$user_group = (new Group_Model())->get_user_group($user_data["user_id"]);
//recherche des droits liés
$tInfosGroups = (new Group_Model())->get_group_rights($user_group);


//si autorisé server_show_positionhided doit etre a 1 !!!!!!!!!!!
//todo info a communiquer avec release
if ($tInfosGroups["server_show_positionhided"] == 1) {
    if ($server_config["portee_missil"] != "0" && $server_config["portee_missil"] != "") {
        $missil = galaxy_portee_missiles($galaxy, $system);
    }
}



require_once 'views/page_header.php';
?>
<div class="page_galaxy">

    <form>
        <input name="action" value="galaxy" type="hidden">
        <table class="og-table og-small-table og-table-galaxyform ">
            <thead>
                <tr>
                    <th colspan="3">
                        <?= $lang['GALAXY_SELECT_GALAXY'] ?>
                    </th>
                    <th colspan="3">
                        <?= $lang['GALAXY_SELECT_SYSTEM'] ?>
                    </th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td clas="content">
                        <input type="button" class="og-button " value="<<<" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?= $galaxy_down ?>&amp;system=<?= $system ?>';">
                    </td>
                    <td clas="content">
                        <input type="text" name="galaxy" maxlength="3" size="5" value="<?= $galaxy ?>" tabindex="1">
                    </td>
                    <td clas="content">
                        <input type="button" class="og-button" value=">>>" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?= $galaxy_up ?>&amp;system=<?= $system ?>';">
                    </td>
                    <td clas="content">
                        <input type="button" class="og-button" value="<<<" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?= $galaxy ?>&amp;system=<?= $system_down ?>';">
                    </td>
                    <td clas="content">
                        <input type="text" name="system" maxlength="3" size="5" value="<?= $system ?>" tabindex="2">
                    </td>
                    <td clas="content">
                        <input type="button" class="og-button" value=">>>" onclick="window.location = 'index.php?action=galaxy&amp;galaxy=<?= $galaxy ?>&amp;system=<?= $system_up ?>';">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="submit" class="og-button" value="<?= $lang['GALAXY_DISPLAY'] ?>">
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
                            <option><?= $lang['GALAXY_FAVORITE_LIST'] ?></option>
                            <?php foreach ($favorites as $v) : ?>
                                <?php $coordinate = $v["galaxy"] . ":" . $v["system"]; ?>
                                <option value='<?= $coordinate ?>'>
                                    <?= $coordinate ?>
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

                        <input class="og-button og-button-success" type="button" value="<?= $lang['GALAXY_ADD_FAVORITES'] ?>" onclick="<?= $string_addfavorites ?>">
                        <input class="og-button og-button-danger" type="button" value="<?= $lang['GALAXY_REMOVE_FAVORITES'] ?>" onclick="<?= $string_delfavorites ?>">
                    </td>
                </tr>

                <tr>
                    <th colspan="11">
                        <?= ($lang['GALAXY_SYSTEMS']) ?><?= $missil ?>
                    </th>
                </tr>
                <?= displayGalaxyTablethead() ?>

            </thead>
            <tbody>
                <?php foreach ($population as $v) : ?>
                    <?= displayGalaxyTabletbodytr($v) ?>
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
                        <a <?= $ToolTip_Helper->GetHTMLClassContent() ?>><?= $lang['GALAXY_LEGEND'] ?></a>
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
                    <?= $lang['GALAXY_PHALANX_LIST'] . help("galaxy_phalanx") ?>
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
                                [<a href='index.php?action=search&&amp;type_search=ally&amp;string_search=<?= $value["ally"] ?>&amp;strict=on' <?= $ToolTip_Helper->GetHTMLClassContent(array("tooltipstered"), "ttp_alliance_" . $value["ally"]) ?>>
                                    <?= $value["ally"] ?>
                                </a>]
                            <?php endif; ?>
                            <?php $tooltiptab["player"][] = $value["player"]; // pour calcul tooltip
                            ?>
                            <a href="index.php?action=search&amp;type_search=player&amp;string_search=<?= $value["player"] ?>&amp;strict=on" <?= $ToolTip_Helper->GetHTMLClassContent(array("tooltipstered"), "ttp_player_" . $value["player"]) ?>>
                                <?= $value["player"] ?>
                            </a>
                            <?= $lang['GALAXY_LUNA_PHALANX'] ?> <span class="og-highlight"><?= $value["level"] ?></span> en <a href='index.php?action=galaxy&amp;galaxy=<?= $value["galaxy"] ?>&amp;system=<?= $value["system"] ?>'><?= $value["galaxy"] . ":" . $value["system"] . ":" . $value["row"] ?></a>
                            [<span class="og-warning"><?= $value["galaxy"] . ":" . $value['range_down'] . " <-> " . $value["galaxy"] . ":" . $value['range_up'] ?></span>]

                            <?php if ($value["gate"] == "1") : ?>
                                <span class="og-alert"><?= $lang['GALAXY_LUNA_GATE'] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td class="tdcontent">
                        <span class=og-warning>
                            <?= $lang['GALAXY_LUNA_NOPHALANX'] ?>
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
                    <?= $lang['GALAXY_SEARCH'] ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>

                <td colspan="5">
                    <?= $lang['GALAXY_SEARCH_PLANETS_AVAILABLE'] ?>
                </td>
                <td colspan="5">
                    <?= $lang['GALAXY_SEARCH_MOONS'] ?>
                </td>
                <td colspan="5">
                    <?= $lang['GALAXY_SEARCH_INACTIVES'] ?>
                </td>
                <td colspan="5">
                    <?= $lang['GALAXY_SEARCH_SPYREPORTS'] ?>
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
                            <a href="index.php?action=search&amp;type_search=<?= $quicktype ?>&amp;galaxy_down=<?= $galaxy ?>&amp;galaxy_up=<?= $galaxy ?>&amp;system_down=<?= $down ?>&amp;system_up=<?= $up ?>&amp;row_down=&amp;row_up=">
                                <?= $i ?>
                            </a>
                        </td>
                    <?php endfor; ?>
                <?php endforeach; ?>
            <tr>
                <td class="tdcontent" colspan='20'>
                    <?= $lang['GALAXY_SURROUNDING_SYSTEMS'] ?>
                </td>
            </tr>
        </tbody>
    </table>

</div><!-- fin class='page_galaxy'-->


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



require_once 'views/page_tail.php';
?>
