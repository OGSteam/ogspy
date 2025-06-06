<?php
global $user_data, $lang,$log;

/**
 * Affichage Empire - Page Astres
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

require_once "includes/ogame.php";

use Ogsteam\Ogspy\Model\Player_Model;

global $server_config;


// On récupère les données de l'utilisateur
$player_data = (new Player_Model())->get_player_data($user_data['player_id']);
$user_empire = player_get_empire($player_data['id']);

$player_building = $user_empire['building'];
$player_defense = $user_empire['defense'];
$user_technology = $user_empire['technology'];

$nb_planete = getPlanetCountForPlayer($user_data['player_id']);

$name = $coordinates = $fields = $temperature_min = $temperature_max = $satellite = "";
//Planète

foreach ($player_building as $planet_id => $planet) {
    $name .= "'" . $planet['name'] . "', ";
    $coordinates .= "'" . implode(":", [$planet['galaxy'], $planet['system'], $planet['row']]) . "', ";
    $fields .= "'" . $planet['fields'] . "', ";
    $temperature_min .= "'" . $planet['temperature_min'] . "', ";
    $temperature_max .= "'" . $planet['temperature_max'] . "', ";
    $satellite .= "'" . $planet['Sat'] . "', ";
}

foreach ($player_building as $planet_id => $planet) {
    $user_production[$planet_id] = ogame_production_planet($planet, $user_technology, $player_data, $server_config);
}


?>

<?php
// vérification de compte de planete/lune avec la technologie astro
if (!isset($user_technology['Astrophysique']) || $user_technology['Astrophysique'] == '') {
    $user_technology['Astrophysique'] = 0;
}
$astro = astro_max_planete($user_technology['Astrophysique']);
?>
<?php if (((getPlanetCountForPlayer($user_data['id']) > $astro) || (find_nb_moon_user($user_data['id']) > $astro)) && $user_technology) : ?>
    <div class="og-msg og-msg-danger">
        <h3 class="og-title"><?php echo $lang['HOME_EMPIRE_ERROR']; ?></h3>
        <p class="og-content">
            <?php echo (getPlanetCountForPlayer($user_data['id']) > $astro) ? $lang['HOME_EMPIRE_ERROR_PLANET'] . '<br>' : ''; ?>
            <?php echo (find_nb_moon_user($user_data['id']) > $astro) ? $lang['HOME_EMPIRE_ERROR_MOON'] . '<br>' : ''; ?>
        </p>
    </div>
<?php endif; ?>
<?php // fin vérification de compte de planete/lune avec la technologie astro
?>

<table class="og-table og-full-table og-table-empire">
    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_SUMMARY'] . " (". $player_data['name'] . ")"; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>&nbsp;</td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_NAME']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                    <span class="og-highlight">
                        <?php echo ($planet["name"] == "") ? "&nbsp;" : $planet["name"]; ?>
                    </span>

            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_COORD']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                [<?php echo $planet["galaxy"] . "&nbsp;" . $planet["system"] . "&nbsp;" . $planet["row"] ?>
                ]
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_FIELDS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $fields = ($planet["fields"] == "0") ? 0 : $planet["fields"]; ?>
                <?php echo $planet["fields_used"] . " / " . $fields; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MINTEMP']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($planet["temperature_min"] == "") ? "&nbsp;" : $planet["temperature_min"]; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MAXTEMP']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($planet["temperature_max"] == "") ? "&nbsp;" : $planet["temperature_max"]; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_EXTENSION']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $booster = "&nbsp;"; ?>
                <?php $booster_tab = booster_decode($planet["boosters"]); ?>
                <?php echo $booster_tab['extention_p']; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_EXTENSION_MOON']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $booster = "&nbsp;"; ?>
                <?php $booster_tab = booster_decode($planet["boosters"]); ?>
                <?php echo $booster_tab['extention_m']; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>

    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_PRODUCTION_EXPECTED']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_METAL']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($M = $planet['M'] == "") ? "&nbsp;" : $user_production[$i]['prod_theorique']['M']; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_CRYSTAL']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($M = $planet['C'] == "") ? "&nbsp;" : $user_production[$i]['prod_theorique']['C']; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_DEUT']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($M = $planet['D'] == "") ? "&nbsp;" : $user_production[$i]['prod_theorique']['D']; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_ENERGY']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $NRJ = (!isset($user_production[$i]['NRJ'])) ? "&nbsp;" : $user_production[$i]['NRJ']; ?>
                <?php echo number_format($NRJ, 0, ',', ' '); ?>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_PRODUCTION_REAL']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_EMPIRE_RATIO']; ?></td>

        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $user_production[$i]['ratio'] = (!isset($user_production[$i]['ratio'])) ? 0 : $user_production[$i]['ratio']; ?>
                <?php if ($user_production[$i]['ratio'] != 1) : ?>
                    <span
                        class="og-alert"><?php echo number_format(round($user_production[$i]['ratio'], 3), 3, ',', ' '); ?></span>
                <?php else : ?>
                    <span
                        class="og-success"><?php echo number_format(round($user_production[$i]['ratio'], 3), 3, ',', ' '); ?></span>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_METAL']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php if ($planet['M'] != "") : ?>
                    <?php echo number_format(floor($user_production[$i]['prod_reel']['M']), 0, ',', ' '); ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>

    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_CRYSTAL']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php if ($planet['C'] != "") : ?>
                    <?php echo number_format(floor($user_production[$i]['prod_reel']['C']), 0, ',', ' '); ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_DEUT']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php if ($planet['D'] != "") : ?>
                    <?php echo number_format(floor($user_production[$i]['prod_reel']['D']), 0, ',', ' '); ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_BOOSTER']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $booster_tab = booster_decode($planet["boosters"]); ?>
                m:<?php echo $booster_tab['booster_m_val']; ?>%, c:<?php echo $booster_tab['booster_c_val']; ?>,
                d:<?php echo $booster_tab['booster_d_val']; ?>, e:<?php echo $booster_tab['booster_e_val']; ?>%

            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_BUILDINGS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MINE_METAL']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $M = ($planet["M"] == "") ? "&nbsp;" : $planet["M"]; ?>
                <span id='15<?php echo '_' . $i ?>'>
                            <?php echo $M ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MINE_CRYSTAL']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $C = ($planet["C"] == "") ? "&nbsp;" : $planet["C"]; ?>
                <span id='16<?php echo '_' . $i ?>'>
                            <?php echo $C ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MINE_DEUT']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $D = ($planet["D"] == "") ? "&nbsp;" : $planet["D"]; ?>
                <span id='17<?php echo '_' . $i ?>'>
                            <?php echo $D ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_METALSTORAGE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $HM = ($planet["HM"] == "") ? "&nbsp;" : $planet["HM"]; ?>
                <span id='3<?php echo '_' . $i ?>'>
                        <?php echo $HM ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_CRYSTALSTORAGE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $HC = ($planet["HC"] == "") ? "&nbsp;" : $planet["HC"]; ?>
                <span id='4<?php echo '_' . $i ?>'>
                        <?php echo $HC ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_DEUTSTORAGE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $HD = ($planet["HD"] == "") ? "&nbsp;" : $planet["HD"]; ?>
                <span id='5<?php echo '_' . $i ?>'>
                        <?php echo $HD ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_SOLAR_PLANT']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $CES = ($planet["CES"] == "") ? "&nbsp;" : $planet["CES"]; ?>
                <span id='20<?php echo '_' . $i ?>'>
                            <?php echo $CES ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_FUSION_PLANT']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $CEF = ($planet["CEF"] == "") ? "&nbsp;" : $planet["CEF"]; ?>
                <span id='21<?php echo '_' . $i ?>'>
                            <?php echo $CEF ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_BUILDINGS']; ?>
        </th>
    </tr>
    </thead>


    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_ROBOTS_PLANT']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $UdR = ($planet["UdR"] == "") ? "&nbsp;" : $planet["UdR"]; ?>
                <span id='1<?php echo '_' . $i ?>'>
                        <?php echo $UdR ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_NANITES_PLANT']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $UdN = ($planet["UdN"] == "") ? "&nbsp;" : $planet["UdN"]; ?>
                <span id='22<?php echo '_' . $i ?>'>
                            <?php echo $UdN ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_SHIPYARD']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $CSp = ($planet["CSp"] == "") ? "&nbsp;" : $planet["CSp"]; ?>
                <span id='2<?php echo '_' . $i ?>'>
                        <?php echo $CSp ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_RESEARCHLAB']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $Lab = ($planet["Lab"] == "") ? "&nbsp;" : $planet["Lab"]; ?>
                <span id='23<?php echo '_' . $i ?>'>
                            <?php echo $Lab ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>

    <?php if ($server_config['ddr'] == 1) : ?>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_EMPIRE_ALLIANCEDEPOT']; ?>
            </td>
            <?php foreach ($player_building as $i => $planet) : ?>
                <td class="tdcontent">
                    <?php $DdR = ($planet["DdR"] == "") ? "&nbsp;" : $planet["DdR"]; ?>
                    <span id='42<?php echo '_' . $i ?>'>
                                <?php echo $DdR ?>
                            </span>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endif; ?>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TERRAFORMER']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $Ter = ($planet["Ter"] == "") ? "&nbsp;" : $planet["Ter"]; ?>
                <span id='24<?php echo '_' . $i ?>'>
                            <?php echo $Ter ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MISSILESSILO']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $Silo = ($planet["Silo"] == "") ? "&nbsp;" : $planet["Silo"]; ?>
                <span id='25<?php echo '_' . $i ?>'>
                            <?php echo $Silo ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_DOCK']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $Dock = ($planet["Dock"] == "") ? "&nbsp;" : $planet["Dock"]; ?>
                <span id='25<?php echo '_' . $i ?>'>
                            <?php echo $Dock ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_ORBITAL_BUILDINGS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_LUNARSTATION']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $BaLu = ($planet["BaLu"] == "") ? "&nbsp;" : $planet["BaLu"]; ?>
                <span id='15<?php echo '_' . $i ?>'>
                            <?php echo $BaLu ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_LUNARPHALANX']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $Pha = ($planet["Pha"] == "") ? "&nbsp;" : $planet["Pha"]; ?>
                <span id='16<?php echo '_' . $i ?>'>
                            <?php echo $Pha ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_LUNARJUMPGATE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $PoSa = ($planet["PoSa"] == "") ? "&nbsp;" : $planet["PoSa"]; ?>
                <span id='17<?php echo '_' . $i ?>'>
                            <?php echo $PoSa ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_OTHERS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_SATELLITES']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $Sat = ($planet["Sat"] == "") ? "&nbsp;" : $planet["Sat"]; ?>
                <span id='6<?php echo '_' . $i ?>'>
                        <?php echo $Sat ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>

    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_CRAWLER']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $For = ($planet["FOR"] == "") ? "&nbsp;" : number_format($planet["FOR"], 0, ',', ' '); ?>
                <?php $class_collect = ($player_data['class'] === 'COL') ? '1' : '0'; ?>
                <?php $nb_max = foreuse_max($planet['M'], $planet['C'], $planet['D'], $player_data['off_geologue'], $class_collect); ?>
                <span id='43<?php echo '_' . $i; ?>'>
                            <?php echo $For . " / " . $nb_max; ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_TECHNOS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_SPY']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='26<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Esp", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Esp"] != "") ? $user_technology["Esp"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_COMPUTER']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='27<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Ordi", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Ordi"] != "") ? $user_technology["Ordi"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_WEAPONS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='28<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Armes", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Armes"] != "") ? $user_technology["Armes"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_SHIELD']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='29<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Bouclier", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Bouclier"] != "") ? $user_technology["Bouclier"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_PROTECTION']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='30<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Protection", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Protection"] != "") ? $user_technology["Protection"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?= $lang['HOME_EMPIRE_TECHNOS_ENERGY'] ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='31<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("NRJ", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["NRJ"] != "") ? $user_technology["NRJ"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_HYPERSPACE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='32<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Hyp", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Hyp"] != "") ? $user_technology["Hyp"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_COMBUSTION_DRIVE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='33<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("RC", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["RC"] != "") ? $user_technology["RC"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_IMPULSE_DRIVE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='34<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("RI", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["RI"] != "") ? $user_technology["RI"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_HYPER_DRIVE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='35<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("PH", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["PH"] != "") ? $user_technology["PH"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_LASER']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='36<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Laser", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Laser"] != "") ? $user_technology["Laser"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_IONS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='37<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Ions", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Ions"] != "") ? $user_technology["Ions"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_PLASMA']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='38<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Plasma", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Plasma"] != "") ? $user_technology["Plasma"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_RESEARCH_NETWORK']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='39<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("RRI", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["RRI"] != "") ? $user_technology["RRI"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_ASTRO']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='41<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Astrophysique", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Astrophysique"] != "") ? $user_technology["Astrophysique"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>

    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_GRAVITY']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                        <span id='40<?php echo '_' . $i; ?>'>
                            <?php if (prerequis_Valid("Graviton", $planet, $user_technology)) : ?>
                                <span class="og-success">
                                    <?php echo ($user_technology["Graviton"] != "") ? $user_technology["Graviton"] : "0"; ?>
                                </span>
                            <?php else : ?>
                                <span class="og-alert">
                                    -
                                </span>
                            <?php endif; ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_TITLE']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_MISSILES']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $LM = ($player_defense[$i]["LM"] == "") ? "0" : $player_defense[$i]["LM"]; ?>
                <span id='7<?php echo '_' . $i ?>'>
                        <?php echo number_format($LM, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_LLASERS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $LLE = ($player_defense[$i]["LLE"] == "") ? "0" : $player_defense[$i]["LLE"]; ?>
                <span id='8<?php echo '_' . $i ?>'>
                        <?php echo number_format($LLE, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_HLASERS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $LLO = ($player_defense[$i]["LLO"] == "") ? "0" : $player_defense[$i]["LLO"]; ?>
                <span id='9<?php echo '_' . $i ?>'>
                        <?php echo number_format($LLO, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_GAUSS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $CG = ($player_defense[$i]["CG"] == "") ? "0" : $player_defense[$i]["CG"]; ?>
                <span id='10<?php echo '_' . $i ?>'>
                        <?php echo number_format($CG, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_IONS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $AI = ($player_defense[$i]["AI"] == "") ? "0" : $player_defense[$i]["AI"]; ?>
                <span id='11<?php echo '_' . $i ?>'>
                        <?php echo number_format($AI, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_PLASMA']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $LP = ($player_defense[$i]["LP"] == "") ? "0" : $player_defense[$i]["LP"]; ?>
                <span id='12<?php echo '_' . $i ?>'>
                        <?php echo number_format($LP, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>

    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_SMALLSHIELD']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $PB = ($player_defense[$i]["PB"] == "") ? "0" : $player_defense[$i]["PB"]; ?>
                <span id='13<?php echo '_' . $i ?>'>
                        <?php echo number_format($PB, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_LARGESHIELD']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $GB = ($player_defense[$i]["GB"] == "") ? "0" : $player_defense[$i]["GB"]; ?>
                <span id='14<?php echo '_' . $i ?>'>
                        <?php echo number_format($GB, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_ANTI']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $MIC = ($player_defense[$i]["MIC"] == "") ? "0" : $player_defense[$i]["MIC"]; ?>
                <span id='19<?php echo '_' . $i ?>'>
                            <?php echo number_format($MIC, 0, ',', ' '); ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_INTER']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $MIP = ($player_defense[$i]["MIP"] == "") ? "0" : $player_defense[$i]["MIP"]; ?>
                <span id='18<?php echo '_' . $i ?>'>
                            <?php echo number_format($MIP, 0, ',', ' '); ?>
                        </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo $lang['HOME_EMPIRE_POINTS_TITLE']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_BUILDINGS']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $point = all_building_cumulate(array(1 => $planet)); ?>
                <?php $point = round($point / 1000); ?>

                <span id='19<?php echo '_' . $i ?>'>
                        <?php echo number_format($point, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_TITLE']; ?>
        </td>
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $point = all_defence_cumulate(array(1 => $player_defense[$i])); ?>
                <?php $point = round($point / 1000); ?>
                <span id='20<?php echo '_' . $i ?>'>
                        <?php echo number_format($point, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_FLEET_TITLE']; ?>
        </td>
        <!-- // seulement les FOR et les SAT !! -->
        <?php foreach ($player_building as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $point = all_fleet_cumulate(array(1 => $planet)); //FOR et Sat
                ?>
                <?php $point = round($point / 1000); ?>
                <span id='20<?php echo '_' . $i ?>'>
                        <?php echo number_format($point, 0, ',', ' '); ?>
                    </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS']; ?>
        </td>
        <td colspan="<?php print ($nb_planete < 10) ? '9' : $nb_planete ?>" class="tdcontent">
                <span id='21'>
                    <?php $point = all_technology_cumulate($user_technology); ?>
                    <?php $point = round($point / 1000); ?>
                    <?php echo number_format($point, 0, ',', ' '); ?>
                </span>
        </td>
    </tr>
    </tbody>
</table>
