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
if (empty($player_data)) {
    echo '<div class="og-msg og-msg-warning ">' .
        '<h3 class="og-title">' . $lang['MSG_SYSTEM'] . '</h3>' .
        '<p class="og-content">' . $lang['MSG_EMPIRE_DATA_FAILURE'] . '</p>' .
        '</div>';
    require_once 'views/page_tail.php';
    exit;
}

$user_empire = player_get_empire($player_data['id']);

$player_building = $user_empire['building'];
$player_defense = $user_empire['defense'];
$user_technology = $user_empire['technology'];

// Séparation des planètes et des lunes
$player_planets = [];
$player_moons = [];
$planet_defense = [];
$moon_defense = [];

foreach ($player_building as $id => $building) {
    // Le champ 'type' est maintenant inclus dans les données récupérées
    if (isset($building['type']) && $building['type'] === 'moon') {
        $player_moons[$id] = $building;
    } else {
        // Par défaut, considérer comme planète
        $player_planets[$id] = $building;
    }
}

// Séparation des défenses en utilisant les mêmes clés
foreach ($player_defense as $id => $defense) {
    if (isset($player_moons[$id])) {
        $moon_defense[$id] = $defense;
    } else {
        $planet_defense[$id] = $defense;
    }
}

$nb_planete = count($player_planets);
$nb_moon = count($player_moons);

?>
<?php
// compute clear colspan values (exact counts). The view already shows a warning if no planet is defined.
$colspan_planets = $nb_planete + 1; // one extra column for the label column
$colspan_planets_nine = $nb_planete; // used where colspan previously was '9' for content cells
$colspan_moons = $nb_moon + 1;

// Compute production per astre (normal behavior for this view)
$user_production = [];
// Compute production for planets
foreach ($player_planets as $i => $planet) {
    // ogame_production_planet expects a building-like array
    $user_production[$i] = ogame_production_planet($planet, $user_technology, $player_data, $server_config);
}
// Also compute for moons (some pages may reference same indices)
foreach ($player_moons as $i => $moon) {
    $user_production[$i] = ogame_production_planet($moon, $user_technology, $player_data, $server_config);
}

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

<!-- Tableau des Planètes uniquement -->
<table class="og-table og-full-table og-table-empire">
    <thead>
    <tr>
    <th colspan="<?php print $colspan_planets ?>">
            🪐 <?php echo $lang['HOME_EMPIRE_SUMMARY'] . " - Planètes (" . $player_data['name'] . ")"; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>&nbsp;</td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_NAME']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                [<?php echo $planet["galaxy"] . "&nbsp;" . $planet["system"] . "&nbsp;" . $planet["row"] ?>]
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_FIELDS']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($planet["temperature_min"] == "") ? "&nbsp;" : $planet["temperature_min"]; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MAXTEMP']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($planet["temperature_max"] == "") ? "&nbsp;" : $planet["temperature_max"]; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_EXTENSION']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $booster_tab = booster_decode($planet["boosters"]); ?>
                <?php echo $booster_tab['extention_p']; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
    <th colspan="<?php print $colspan_planets ?>">
            <?php echo $lang['HOME_EMPIRE_PRODUCTION_EXPECTED']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_METAL']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($planet['M'] == "") ? "&nbsp;" : $user_production[$i]['prod_theorique']['M'] ?? "&nbsp;"; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_CRYSTAL']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($planet['C'] == "") ? "&nbsp;" : $user_production[$i]['prod_theorique']['C'] ?? "&nbsp;"; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_DEUT']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php echo ($planet['D'] == "") ? "&nbsp;" : $user_production[$i]['prod_theorique']['D'] ?? "&nbsp;"; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_ENERGY']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $NRJ = (!isset($user_production[$i]['NRJ'])) ? 0 : $user_production[$i]['NRJ']; ?>
                <?php echo ($NRJ === 0) ? "&nbsp;" : number_format($NRJ, 0, ',', ' '); ?>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
    <th colspan="<?php print $colspan_planets ?>">
            <?php echo $lang['HOME_EMPIRE_PRODUCTION_REAL']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_EMPIRE_RATIO']; ?></td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $ratio = $user_production[$i]['ratio'] ?? 0; ?>
                <?php if ($ratio != 1) : ?>
                    <span class="og-alert"><?php echo number_format(round($ratio, 3), 3, ',', ' '); ?></span>
                <?php else : ?>
                    <span class="og-success"><?php echo number_format(round($ratio, 3), 3, ',', ' '); ?></span>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_METAL']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php if ($planet['M'] != "") : ?>
                    <?php echo number_format(floor($user_production[$i]['prod_reel']['M'] ?? 0), 0, ',', ' '); ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_CRYSTAL']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php if ($planet['C'] != "") : ?>
                    <?php echo number_format(floor($user_production[$i]['prod_reel']['C'] ?? 0), 0, ',', ' '); ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_DEUT']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php if ($planet['D'] != "") : ?>
                    <?php echo number_format(floor($user_production[$i]['prod_reel']['D'] ?? 0), 0, ',', ' '); ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_BOOSTER']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $booster_tab = booster_decode($planet["boosters"]); ?>
                m:<?php echo $booster_tab['booster_m_val']; ?>%, c:<?php echo $booster_tab['booster_c_val']; ?>%,
                d:<?php echo $booster_tab['booster_d_val']; ?>%, e:<?php echo $booster_tab['booster_e_val']; ?>%
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
    <th colspan="<?php print $colspan_planets ?>">
            <?php echo $lang['HOME_EMPIRE_BUILDINGS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_MINE_METAL']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $CEF = ($planet["CEF"] == "") ? "&nbsp;" : $planet["CEF"]; ?>
                <span id='21<?php echo '_' . $i ?>'>
                    <?php echo $CEF ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_ROBOTS_PLANT']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $Dock = ($planet["Dock"] == "") ? "&nbsp;" : $planet["Dock"]; ?>
                <span id='dock_<?php echo $i ?>'>
                    <?php echo $Dock ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
    <th colspan="<?php print $colspan_planets ?>">
            <?php echo $lang['HOME_EMPIRE_OTHERS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_SATELLITES']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <?php foreach ($player_planets as $i => $planet) : ?>
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
        <th colspan="<?php print $colspan_planets ?>">
            <?php echo $lang['HOME_EMPIRE_TECHNOS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $technologies = [
        'Esp' => 'SPY',
        'Ordi' => 'COMPUTER',
        'Armes' => 'WEAPONS',
        'Bouclier' => 'SHIELD',
        'Protection' => 'PROTECTION',
        'NRJ' => 'ENERGY',
        'Hyp' => 'HYPERSPACE',
        'RC' => 'COMBUSTION_DRIVE',
        'RI' => 'IMPULSE_DRIVE',
        'PH' => 'HYPER_DRIVE',
        'Laser' => 'LASER',
        'Ions' => 'IONS',
        'Plasma' => 'PLASMA',
        'RRI' => 'RESEARCH_NETWORK',
        'Astrophysique' => 'ASTRO',
        'Graviton' => 'GRAVITY'
    ];

    foreach($technologies as $tech_key => $tech_lang): ?>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS_' . $tech_lang]; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <span id='<?php echo strtolower($tech_key) . '_' . $i; ?>'>
                    <?php if (prerequis_Valid($tech_key, $planet, $user_technology)) : ?>
                        <span class="og-success">
                            <?php echo ($user_technology[$tech_key] != "") ? $user_technology[$tech_key] : "0"; ?>
                        </span>
                    <?php else : ?>
                        <span class="og-alert">-</span>
                    <?php endif; ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>

    <thead>
    <tr>
    <th colspan="<?php print $colspan_planets ?>">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_TITLE']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $defenses = [
        'LM' => 'MISSILES',
        'LLE' => 'LLASERS',
        'LLO' => 'HLASERS',
        'CG' => 'GAUSS',
        'AI' => 'IONS',
        'LP' => 'PLASMA',
        'PB' => 'SMALLSHIELD',
        'GB' => 'LARGESHIELD',
        'MIC' => 'ANTI',
        'MIP' => 'INTER'
    ];

    foreach($defenses as $def_key => $def_lang): ?>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_' . $def_lang]; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $def_value = $planet_defense[$i][$def_key] ?? "0"; ?>
                <span id='<?php echo strtolower($def_key) . '_' . $i ?>'>
                    <?php echo number_format($def_value, 0, ',', ' '); ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>

    <thead>
    <tr>
        <th colspan="<?php print $colspan_planets ?>">
            <?php echo $lang['HOME_EMPIRE_POINTS_TITLE']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_BUILDINGS']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $point = all_building_cumulate(array(1 => $planet)); ?>
                <?php $point = round($point / 1000); ?>
                <span id='building_points_<?php echo $i ?>'>
                    <?php echo number_format($point, 0, ',', ' '); ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_TITLE']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php
                $current_planet_defense = $planet_defense[$i] ?? [];
                $point = all_defense_cumulate(array(1 => $current_planet_defense));
                $point = round($point / 1000);
                ?>
                <span id='defense_points_<?php echo $i ?>'>
                    <?php echo number_format($point, 0, ',', ' '); ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_FLEET_TITLE']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <td class="tdcontent">
                <?php $point = all_fleet_cumulate(array(1 => $planet)); ?>
                <?php $point = round($point / 1000); ?>
                <span id='fleet_points_<?php echo $i ?>'>
                    <?php echo number_format($point, 0, ',', ' '); ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_TECHNOS']; ?>
        </td>
    <td colspan="<?php print $colspan_planets_nine ?>" class="tdcontent">
            <span id='tech_points'>
                <?php $point = all_technology_cumulate($user_technology); ?>
                <?php $point = round($point / 1000); ?>
                <?php echo number_format($point, 0, ',', ' '); ?>
            </span>
        </td>
    </tr>
    </tbody>
</table>

<?php if ($nb_moon > 0): ?>
<!-- Tableau des Lunes séparé -->
<table class="og-table og-full-table og-table-empire" style="margin-top: 20px;">
    <thead>
    <tr>
        <th colspan="<?php print $colspan_moons ?>">
            🌙 <?php echo $lang['HOME_EMPIRE_SUMMARY'] . " - Lunes (" . $player_data['name'] . ")"; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>&nbsp;</td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_NAME']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <span class="og-highlight">
                    <?php echo ($moon["name"] == "") ? "&nbsp;" : $moon["name"]; ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_COORD']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                [<?php echo $moon["galaxy"] . "&nbsp;" . $moon["system"] . "&nbsp;" . $moon["row"] ?>]
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_FIELDS']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $fields = ($moon["fields"] == "0") ? 0 : $moon["fields"]; ?>
                <?php echo $moon["fields_used"] . " / " . $fields; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_EXTENSION_MOON']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $booster_tab = booster_decode($moon["boosters"]); ?>
                <?php echo $booster_tab['extention_m']; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
        <th colspan="<?php print $colspan_moons ?>">
            <?php echo $lang['HOME_EMPIRE_ORBITAL_BUILDINGS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_LUNARSTATION']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $BaLu = ($moon["BaLu"] == "") ? "&nbsp;" : $moon["BaLu"]; ?>
                <span id='balu_<?php echo $i ?>'>
                    <?php echo $BaLu ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_LUNARPHALANX']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $Pha = ($moon["Pha"] == "") ? "&nbsp;" : $moon["Pha"]; ?>
                <span id='pha_<?php echo $i ?>'>
                    <?php echo $Pha ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_LUNARJUMPGATE']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $PoSa = ($moon["PoSa"] == "") ? "&nbsp;" : $moon["PoSa"]; ?>
                <span id='posa_<?php echo $i ?>'>
                    <?php echo $PoSa ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_SHIPYARD']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $CSp = ($moon["CSp"] == "") ? "&nbsp;" : $moon["CSp"]; ?>
                <span id='moon_csp_<?php echo $i ?>'>
                    <?php echo $CSp ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_ROBOTS_PLANT']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $UdR = ($moon["UdR"] == "") ? "&nbsp;" : $moon["UdR"]; ?>
                <span id='moon_udr_<?php echo $i ?>'>
                    <?php echo $UdR ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_NANITES_PLANT']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $UdN = ($moon["UdN"] == "") ? "&nbsp;" : $moon["UdN"]; ?>
                <span id='moon_udn_<?php echo $i ?>'>
                    <?php echo $UdN ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>

    <thead>
    <tr>
        <th colspan="<?php print $colspan_moons ?>">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_TITLE']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <!-- Défenses lunaires -->
    <?php foreach($defenses as $def_key => $def_lang): ?>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_' . $def_lang]; ?>
        </td>
        <?php foreach ($player_moons as $moon_id => $moon) : ?>
            <td class="tdcontent">
                <?php $def_value = $moon_defense[$moon_id][$def_key] ?? "0"; ?>
                <span id='moon_<?php echo strtolower($def_key) . '_' . $moon_id ?>'>
                    <?php echo number_format($def_value, 0, ',', ' '); ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>

    <thead>
    <tr>
        <th colspan="<?php print $colspan_moons ?>">
            <?php echo $lang['HOME_EMPIRE_POINTS_TITLE']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_BUILDINGS']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php $point = all_building_cumulate(array(1 => $moon)); ?>
                <?php $point = round($point / 1000); ?>
                <span id='moon_building_points_<?php echo $i ?>'>
                    <?php echo number_format($point, 0, ',', ' '); ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_WEAPONS_TITLE']; ?>
        </td>
        <?php foreach ($player_moons as $i => $moon) : ?>
            <td class="tdcontent">
                <?php
                $current_moon_defense = $moon_defense[$i] ?? [];
                $point = all_defense_cumulate(array(1 => $current_moon_defense));
                $point = round($point / 1000);
                ?>
                <span id='moon_defense_points_<?php echo $i ?>'>
                    <?php echo number_format($point, 0, ',', ' '); ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
</table>
<?php endif; ?>

<!-- Tableau des Totaux -->
<table class="og-table og-full-table og-table-empire" style="margin-top: 20px;">
    <thead>
    <tr>
        <th colspan="3">
            📊 <?php echo $lang['HOME_EMPIRE_SUMMARY'] . " - Totaux Empire (" . $player_data['name'] . ")"; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname" style="width: 40%;">Type d'astres</td>
        <td class="tdcontent" style="width: 30%; text-align: center;"><strong>Planètes 🪐</strong></td>
        <td class="tdcontent" style="width: 30%; text-align: center;"><strong>Lunes 🌙</strong></td>
    </tr>
    <tr>
        <td class="tdname">Nombre d'astres</td>
        <td class="tdcontent" style="text-align: center;">
            <span class="og-highlight"><?php echo $nb_planete; ?></span>
        </td>
        <td class="tdcontent" style="text-align: center;">
            <span class="og-highlight"><?php echo $nb_moon; ?></span>
        </td>
    </tr>
    <tr>
        <td class="tdname">Points Bâtiments (k)</td>
        <td class="tdcontent" style="text-align: center;">
            <?php
            $total_planet_buildings = 0;
            foreach ($player_planets as $planet) {
                $total_planet_buildings += round(all_building_cumulate(array(1 => $planet)) / 1000);
            }
            echo number_format($total_planet_buildings, 0, ',', ' ');
            ?>
        </td>
        <td class="tdcontent" style="text-align: center;">
            <?php
            $total_moon_buildings = 0;
            foreach ($player_moons as $moon) {
                $total_moon_buildings += round(all_building_cumulate(array(1 => $moon)) / 1000);
            }
            echo number_format($total_moon_buildings, 0, ',', ' ');
            ?>
        </td>
    </tr>
    <tr>
        <td class="tdname">Points Défenses (k)</td>
        <td class="tdcontent" style="text-align: center;">
            <?php
            $total_planet_defense = 0;
            foreach ($planet_defense as $defense) {
                $total_planet_defense += round(all_defense_cumulate(array(1 => $defense)) / 1000);
            }
            echo number_format($total_planet_defense, 0, ',', ' ');
            ?>
        </td>
        <td class="tdcontent" style="text-align: center;">
            <?php
            $total_moon_defense = 0;
            foreach ($moon_defense as $defense) {
                $total_moon_defense += round(all_defense_cumulate(array(1 => $defense)) / 1000);
            }
            echo number_format($total_moon_defense, 0, ',', ' ');
            ?>
        </td>
    </tr>
    <tr>
        <td class="tdname">Points Flotte (k)</td>
        <td class="tdcontent" style="text-align: center;">
            <?php
            $total_planet_fleet = 0;
            foreach ($player_planets as $planet) {
                $total_planet_fleet += round(all_fleet_cumulate(array(1 => $planet)) / 1000);
            }
            echo number_format($total_planet_fleet, 0, ',', ' ');
            ?>
        </td>
        <td class="tdcontent" style="text-align: center;">
            <?php
            $total_moon_fleet = 0;
            foreach ($player_moons as $moon) {
                $total_moon_fleet += round(all_fleet_cumulate(array(1 => $moon)) / 1000);
            }
            echo number_format($total_moon_fleet, 0, ',', ' ');
            ?>
        </td>
    </tr>
    <tr>
        <td class="tdname">Points Technologies (k)</td>
        <td class="tdcontent" style="text-align: center;" colspan="2">
            <?php
            $total_tech = round(all_technology_cumulate($user_technology) / 1000);
            echo number_format($total_tech, 0, ',', ' ');
            ?>
        </td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="3">📈 Production totale (par heure)</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">Production Métal/h</td>
        <td class="tdcontent" style="text-align: center;" colspan="2">
            <?php
            $total_metal = 0;
            foreach ($player_planets as $i => $planet) {
                if ($planet['M'] != "") {
                    $total_metal += floor($user_production[$i]['prod_reel']['M'] ?? 0);
                }
            }
            echo number_format($total_metal, 0, ',', ' ');
            ?>
        </td>
    </tr>
    <tr>
        <td class="tdname">Production Cristal/h</td>
        <td class="tdcontent" style="text-align: center;" colspan="2">
            <?php
            $total_crystal = 0;
            foreach ($player_planets as $i => $planet) {
                if ($planet['C'] != "") {
                    $total_crystal += floor($user_production[$i]['prod_reel']['C'] ?? 0);
                }
            }
            echo number_format($total_crystal, 0, ',', ' ');
            ?>
        </td>
    </tr>
    <tr>
        <td class="tdname">Production Deutérium/h</td>
        <td class="tdcontent" style="text-align: center;" colspan="2">
            <?php
            $total_deuterium = 0;
            foreach ($player_planets as $i => $planet) {
                if ($planet['D'] != "") {
                    $total_deuterium += floor($user_production[$i]['prod_reel']['D'] ?? 0);
                }
            }
            echo number_format($total_deuterium, 0, ',', ' ');
            ?>
        </td>
    </tr>
    <tr>
        <td class="tdname">Énergie totale</td>
        <td class="tdcontent" style="text-align: center;" colspan="2">
            <?php
            $total_energy = 0;
            foreach ($player_planets as $i => $planet) {
                $energy = $user_production[$i]['NRJ'] ?? 0;
                if (is_numeric($energy)) {
                    $total_energy += $energy;
                }
            }
            echo ($total_energy === 0) ? "&nbsp;" : number_format($total_energy, 0, ',', ' ');
            ?>
        </td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="3">🏆 Totaux Empire</th>
        <td class="tdname" style="font-weight: bold;">TOTAL POINTS EMPIRE (k)</td>
        <td class="tdcontent" style="text-align: center; font-weight: bold;" colspan="2">
        <td class="tdname">TOTAL POINTS EMPIRE (k)</td>
        <td class="tdcontent" style="text-align: center;" colspan="2">
            <?php
            $total_empire = $total_planet_buildings + $total_moon_buildings +
                           $total_planet_defense + $total_moon_defense +
                           $total_planet_fleet + $total_moon_fleet + $total_tech;
            echo number_format($total_empire, 0, ',', ' ');
            ?>
        </td>
    </tr>
    </tbody>
</table>
