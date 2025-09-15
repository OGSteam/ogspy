<?php global $server_config, $lang, $user_data;

/**
 * Affichage Empire - Page Simulation
 * @package OGSpy
 * @version 3.04b
 * @subpackage views
 * @author Kyser
 * @created  09/2009
 * @copyright 2009-2025 OGSteam
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

use Ogsteam\Ogspy\Model\Player_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$player_data = (new Player_Model())->get_player_data($user_data['player_id']);
if (empty($player_data)) {
    echo '<div class="og-msg og-msg-warning ">' .
        '<h3 class="og-title">' . $lang['MSG_SYSTEM'] . '</h3>' .
        '<p class="og-content">' . $lang['MSG_EMPIRE_DATA_FAILURE'] . '</p>' .
        '</div>';
    require_once 'views/page_tail.php';
    exit;
}
$player_empire = player_get_empire($player_data['id']);

// Raw data (planets + moons mixed)
$player_building = $player_empire["building"];
$player_defense = $player_empire["defense"];
$player_percentage = $player_empire["player_percentage"];

// Séparation des planètes et des lunes
$player_planets = [];
$player_moons = [];
$planet_defense = [];
$moon_defense = [];

// Split mixed building list (planets + moons) into dedicated arrays.
// IMPORTANT: The main simulation table must only display planets; moons have their own table later.
// Detection heuristic: if 'type' === 'moon' (set by model layer) we classify as moon, otherwise planet.
foreach ($player_building as $id => $building) {
    // Distinguish moon vs planet using provided 'type' (fallback to planet)
    if (isset($building['type']) && $building['type'] === 'moon') {
        $player_moons[$id] = $building;
        if (isset($player_defense[$id])) { $moon_defense[$id] = $player_defense[$id]; }
    } else {
        $player_planets[$id] = $building;
        if (isset($player_defense[$id])) { $planet_defense[$id] = $player_defense[$id]; }
    }
}

if ($player_empire["technology"]) {
    $player_technology = $player_empire["technology"];
} else {
    $player_technology['NRJ'] = 0;
    $player_technology['Plasma'] = 0;
}

// Counts used for column generation (planets only in main simulation table)
$nb_planete = count($player_planets);
$nb_moon = count($player_moons);


// ajout infos pour gestion js ...

$officier = $player_data['off_commandant'] + $player_data['off_amiral'] + $player_data['off_ingenieur']
    + $player_data['off_geologue'] + $player_data['off_technocrate'];
$off_full = ($officier == 5) ? '1' : '0';
$class_collect = ($player_data['class'] === 'COL') ? '1' : '0';
echo "<input type='hidden' id='vitesse_uni' size='2' maxlength='5' value='" . $server_config['speed_uni'] . "'/>";
echo "<input type='hidden' id='off_ingenieur' value='" . $player_data["off_ingenieur"] . "'/>";
echo "<input type='hidden' id='off_geologue' value='" . $player_data["off_geologue"] . "'/>";
echo "<input type='hidden' id='off_full' value='" . $off_full . "'/>";
echo "<input type='hidden' id='class_collect' value='" . $class_collect . "'/>";

// Calcul & attach boosters + extension fields (planets & moons separately)
// NOTE: previous code iterated on all buildings and did NOT write back booster_tab to arrays (pass-by-value) -> boosters always 0 in view.
foreach ($player_planets as $pid => &$p) {
    $p['booster_tab'] = booster_decode($p['boosters']);
    $p['fields'] = (int)$p['fields'] + (int)$p['booster_tab']['extention_p'];
}
unset($p);
foreach ($player_moons as $mid => &$m) {
    $m['booster_tab'] = booster_decode($m['boosters']);
    $m['fields'] = (int)$m['fields'] + (int)$m['booster_tab']['extention_p'];
}
unset($m);

?>
<?php
// compute sensible min-width like in home_empire.php
$per_column_px = isset($per_column_px) ? intval($per_column_px) : 70;
$label_width_px = isset($label_width_px) ? intval($label_width_px) : 140;
$totals_column_px = $per_column_px * 2; // Double width for totals column
$nb_cols = 2; // For parameters table, we'll use 2 columns
$min_width_px = $label_width_px + ($nb_cols * $per_column_px) + $totals_column_px;
?>
<div class="og-table-wrapper">
<table class="og-table og-full-table og-table-empire og-full-table" id="simu_params" title="Simulation Parameters">
<colgroup>
    <col />
    <col />
</colgroup>
    <thead>
    <tr>
        <th>
            <?= $lang['HOME_SIMU_TECH_ENERGY']; ?>
            <input type="text" id="NRJ" size="2" maxlength="2" value="<?= $player_technology['NRJ']; ?>"
                   onchange="refresh_page();"> -
            <?= $lang['HOME_SIMU_TECH_PLASMA']; ?>
            <input type="text" id="Plasma" size="2" maxlength="2" value="<?= $player_technology['Plasma']; ?>"
                   onchange="refresh_page();"> -
            <?= $lang['HOME_SIMU_OFF_INGE']; ?>
            <input type="checkbox" id="c_off_ingenieur" <?= $player_data["off_ingenieur"] ? 'checked' : ''; ?>
                   onclick="refresh_page();"> -
            <?= $lang['HOME_SIMU_OFF_GEO']; ?>
            <input type="checkbox" id="c_off_geologue" <?= $player_data["off_geologue"] ? 'checked' : ''; ?>
                   onclick="refresh_page();"> -
            <?= $lang['HOME_SIMU_OFF_FULL']; ?>
            <input type="checkbox" id="c_off_full" <?= $off_full ? 'checked' : ''; ?> onclick="refresh_page();"> -
            <?= $lang['HOME_SIMU_CLASS_COLLECT']; ?>
            <input type="checkbox" id="c_class_collect" <?= $class_collect ? 'checked' : ''; ?>
                   onclick="refresh_page();">
        </th>
    </tr>
    </thead>
</table>
</div>

<?php
// Compute min width for simulation table - this has more columns since it shows all planets
$nb_cols = count($player_planets); // One column per planet
$min_width_px = $label_width_px + ($nb_cols * $per_column_px * 2) + $totals_column_px; // *2 because each planet has 2 cols, plus totals column
?>

<div class="og-table-wrapper">
<table class="og-table og-full-table og-table-empire" id="simu" title="Simulation">
<colgroup>
    <col style="width: <?php echo $label_width_px; ?>px;" />
    <?php for ($ci = 0; $ci < count($player_planets); $ci++): ?>
        <col style="width: <?php echo $per_column_px; ?>px;" />
        <col style="width: <?php echo $per_column_px; ?>px;" />
    <?php endfor; ?>
    <col class="og-total-col" /> <!-- Totaux column -->
</colgroup>
    <thead>
    <tr>
        <th></th>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <th colspan="2">
                <?php echo ($planet["name"] == "") ? "xxx" : $planet["name"]; ?>
            </th>
        <?php endforeach; ?>
    <th class="og-total-col">
            <?php echo $lang['HOME_SIMU_TOTALS']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_COORD']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <?php $coordinates = "[" . $planet["galaxy"] . "&nbsp;" . $planet["system"] . "&nbsp;" . $planet["row"] . "]"; ?>
            <td colspan='2' class="tdcontent">
                <?php echo $coordinates; ?><input id='position_<?php echo $planet["id"]; ?>' type='hidden' value='<?php echo $planet["row"]; ?>'>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_FIELDS']; ?>
        </td>
        <?php $sum_field = 0; ?>
        <?php $sum_filed_used = 0; ?>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <?php $fields = ($planet["fields"] == "0") ? "?" : $planet["fields"]; ?>
            <?php $fields_used = ($planet["fields_used"] >= 0) ? $planet["fields_used"] : "&nbsp;"; ?>
            <td colspan='2' class="tdcontent">
                <?php echo $fields . " / " . $fields_used; ?>
            </td>
            <?php // Pour totaux
            ?>
            <?php $sum_field = (is_numeric($fields)) ? $sum_field + $fields : $sum_field; ?>
            <?php $sum_filed_used = (is_numeric($fields_used)) ? $sum_filed_used + $fields_used : $fields_used; ?>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id='T_cases'>
            <?php echo $sum_filed_used . " / " . $sum_field; ?>
        </div>
    </td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_MINTEMP']; ?>
        </td>
        <?php $t_min = +INF; ?>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <?php $temperature_min = ($planet["temperature_min"] == "") ? "&nbsp;" : $planet["temperature_min"]; ?>
            <td colspan='2' class="tdcontent">
                <?php echo $temperature_min; ?><input id='temperature_min_<?php echo $planet_id; ?>' type='hidden' value='<?php echo $temperature_min; ?>'>
            </td>
            <?php $t_min = (is_numeric($temperature_min) && $temperature_min < $t_min) ? $temperature_min : $t_min; // Pour totaux
            ?>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id='T_min'>
            <?php echo $t_min; ?>
        </div>
    </td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_MAXTEMP']; ?>
        </td>
        <?php $t_max = -INF; ?>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <?php $temperature_max = ($planet["temperature_max"] == "") ? "&nbsp;" : $planet["temperature_max"]; ?>
            <td colspan='2' class="tdcontent">
                <?php echo $temperature_max; ?><input id='temperature_max_<?php echo $planet_id; ?>' type='hidden' value='<?php echo $temperature_max; ?>'>
            </td>
            <?php $t_max = (is_numeric($temperature_max) && $temperature_max > $t_max) ? $temperature_max : $t_max; ?>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id='T_max'>
            <?php echo $t_max; ?>
        </div>
    </td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_EXTENSION']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <?php $booster = (isset($planet["booster_tab"]['extention_p'])) ? $planet["booster_tab"]['extention_p'] : "0"; ?>
            <th colspan='2' class="tdcontent">
                <?php echo $booster; ?><input id='extension_<?php echo $planet_id; ?>' type='hidden'
                                              value='<?php echo $booster; ?>'>
            </th>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th><?php echo $lang['HOME_SIMU_ENERGYS']; ?></th><!-- Colonne des noms -->
        <th colspan="<?php echo(2 * $nb_planete); ?>"></th>
        <th></th><!-- Colonne totaux -->
    </tr>
    </thead>
    <tbody>

    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_SOLARPLANT_SHORT']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan="2">
                <input type="text" id="CES_<?php echo $planet_id; ?>" size="5" maxlength="5"
                       value="<?php echo $planet['CES']; ?>" onchange="refresh_page();">
                <select id="CES_<?php echo $planet_id; ?>_percentage" onchange="refresh_page();">
                    <?php for ($j = 100; $j >= 0; $j -= 10) : ?>
                        <option
                            value="<?php echo $j; ?>" <?php echo ($player_percentage[$planet_id]['CES_percentage'] == $j) ? 'selected' : ''; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_FUSIONPLANT_SHORT']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan="2">
                <input type="text" id="CEF_<?php echo $planet_id; ?>" size="5" maxlength="5"
                       value="<?php echo $planet['CEF']; ?>" onchange="refresh_page();">
                <select id="CEF_<?php echo $planet_id; ?>_percentage" onchange="refresh_page();">
                    <?php for ($j = 100; $j >= 0; $j -= 10) : ?>
                        <option
                            value="<?php echo $j; ?>" <?php echo ($player_percentage[$planet_id]['CEF_percentage'] == $j) ? 'selected' : ''; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_SATELLITES']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan="2">
                <input type='text' id='Sat_<?php echo $planet_id; ?>' size='5' maxlength='5'
                       value='<?php echo $planet["Sat"]; ?>' onchange='refresh_page();'>
                <select id='Sat_<?php echo $planet_id; ?>_percentage' onchange='refresh_page();'
                        onKeyUp='refresh_page();'>
                    <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                        <?php $isSlected = ($player_percentage[$planet_id]['Sat_percentage'] == $j) ? " selected='selected' " : "" ?>
                        <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_BOOSTERNRJ']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan='2'>
                <select id='E_<?php echo $planet_id; ?>_booster' onchange='refresh_page();' onKeyUp='refresh_page();'>
                    <?php $planet["booster_tab"]['booster_e_val'] = (!isset($planet["booster_tab"]['booster_e_val'])) ? 0 : $planet["booster_tab"]['booster_e_val']; ?>
                    <?php for ($j = 80; $j >= 0; $j = $j - 20) : ?>
                        <?php $isSlected = ($planet["booster_tab"]['booster_e_val'] == $j) ? " selected='selected' " : "" ?>
                        <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_ENERGY']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan='2'>
                <div id='NRJ_<?php echo $planet_id; ?>'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id="E_NRJ">-</div>
    </td>
    </tr>
    </tbody>
    <!--
    Foreuse
    -->
    <thead>
    <tr>
        <th><?php echo $lang['HOME_SIMU_CRAWLER']; ?></th> <!-- Colonne des noms -->
        <th colspan="<?php echo(2 * $nb_planete); ?>"></th> <!-- Colonne planètes -->
        <th></th> <!-- Colonne totaux -->
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_CRAWLER']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan="2">
                <input type='text' id='For_<?php echo $planet_id; ?>' size='4' maxlength='4'
                       value='<?php echo $planet["FOR"]; ?>' onchange='refresh_page();'>
                /<span id='FOR_<?php echo $planet_id; ?>_max'>-</span>
                <div>
                    <select id='For_<?php echo $planet_id; ?>_percentage' onchange='refresh_page();'
                            onKeyUp='refresh_page();'>
                        <?php for ($j = 150; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($player_percentage[$planet_id]['FOR_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </td>
        <?php endforeach; ?>
    <td class="og-total"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='FOR_<?php echo $planet_id; ?>_conso'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id="FOR_conso">-</div>
    </td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_PRODUCTION']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='FOR_<?php echo $planet_id; ?>_prod'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    </tbody>
    <!--
    Métal
    -->
    <thead>
    <tr>
        <th><?php echo($lang['HOME_SIMU_METAL']); ?></th>
        <th colspan="<?php echo(2 * $nb_planete); ?>"></th>
        <th></th> <!-- Colonne totaux -->
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_LEVEL']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <?php $M = $planet["M"]; ?>
            <td class="tdcontent" colspan="2">
                <input type='text' id='M_<?php echo $planet_id; ?>' size='5' maxlength='5' value='<?php echo $M; ?>'
                       onchange='refresh_page();'>
                <select id='M_<?php echo $planet_id; ?>_percentage' onchange='refresh_page();'
                        onKeyUp='refresh_page();'>
                    <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                        <?php $selected = ($player_percentage[$planet_id]['M_percentage'] == $j) ? " selected='selected'" : "" ?>
                        <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_BOOSTERMETAL']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <select id='M_<?php echo $planet_id; ?>_booster' onchange='refresh_page();' onKeyUp='refresh_page();'>
                    <?php for ($j = 40; $j >= 0; $j = $j - 10) : ?>
                        <?php $selected = ($planet["booster_tab"]['booster_m_val'] == $j) ? " selected='selected'" : "" ?>
                        <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='M_<?php echo $planet_id; ?>_conso'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id="M_conso">-</div>
    </td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_PRODUCTION']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='M_<?php echo $planet_id; ?>_prod'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id="M_prod">-</div>
    </td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th><?php echo($lang['HOME_SIMU_CRYSTAL']); ?></th> <!-- Colonne des noms -->
        <th colspan="<?php echo(2 * $nb_planete); ?>"></th>
        <th></th> <!-- Colonne totaux -->
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_LEVEL']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan="2">
                <input type='text' id='C_<?php echo $planet_id; ?>' size='5' maxlength='5'
                       value='<?php echo $planet["C"]; ?>' onchange='refresh_page();'>
                <select id='C_<?php echo $planet_id; ?>_percentage' onchange='refresh_page();'
                        onKeyUp='refresh_page();'>
                    <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                        <?php $selected = ($player_percentage[$planet_id]['C_percentage'] == $j) ? " selected='selected'" : "" ?>
                        <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo($lang['HOME_SIMU_BOOSTERCRYSTAL']); ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <select id='C_<?php echo $planet_id; ?>_booster' onchange='refresh_page();' onKeyUp='refresh_page();'>
                    <?php for ($j = 40; $j >= 0; $j -= 10) : ?>
                        <?php $selected = ($planet['booster_tab']['booster_c_val'] == $j) ? " selected='selected'" : ""; ?>
                        <option value='<?php echo $j; ?>' <?php echo $selected; ?>><?php echo $j; ?>%</option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_ENERGY_USAGE']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='C_<?php echo $planet_id; ?>_conso'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id="C_conso">-</div>
    </td>
    </tr>

    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_PRODUCTION']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='C_<?php echo $planet_id; ?>_prod'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
        <div id="C_prod">-</div>
    </td>
    </tr>
    </tbody>
    <!--
    Deutérium
    -->
    <thead>
    <tr>
        <th><?php echo $lang['HOME_SIMU_DEUT']; ?></th> <!-- Colonne des noms -->
        <th colspan="<?php echo 2 * ($nb_planete); ?>"></th>
        <th></th> <!-- Colonne totaux -->
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_LEVEL']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td class="tdcontent" colspan="2">
                <input type='text' id='D_<?php echo $planet_id; ?>' size='5' maxlength='5'
                       value='<?php echo $planet["D"]; ?>' onchange='refresh_page();'>
                <select id='D_<?php echo $planet_id; ?>_percentage' onchange='refresh_page();'
                        onKeyUp='refresh_page();'>
                    <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                        <?php $selected = ($player_percentage[$planet_id]['D_percentage'] == $j) ? " selected='selected'" : "" ?>
                        <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                            <?php echo $j; ?>%
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_BOOSTERDEUT']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <select id='D_<?php echo $planet_id; ?>_booster' onchange='refresh_page();' onKeyUp='refresh_page();'>
                    <?php for ($j = 40; $j >= 0; $j -= 10) : ?>
                        <?php $selected = ($planet['booster_tab']['booster_d_val'] == $j) ? " selected='selected'" : ""; ?>
                        <option value='<?php echo $j; ?>' <?php echo $selected; ?>><?php echo $j; ?>%</option>
                    <?php endfor; ?>
                </select>
            </td>
        <?php endforeach; ?>
        <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_ENERGY_USAGE']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='D_<?php echo $planet_id; ?>_conso'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
            <div id="D_conso">-</div>
           </td>
    </tr>

    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_PRODUCTION']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='D_<?php echo $planet_id; ?>_prod'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
            <div id="D_prod">-</div>
           </td>
    </tr>
    </tbody>
    <!--total-->
    <thead>
    <tr>
        <th><?php echo $lang['HOME_SIMU_POINTSBYPLANET']; ?></th>  <!-- Colonne des noms -->
        <th colspan="<?php echo(2 * $nb_planete) ?>"></th>
        <th></th> <!-- Colonne totaux -->
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_BUILDINGS']; ?>
        </td>
        <?php $lab_max = 0; ?>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <?php $lab_max = max($lab_max, $planet["Lab"]); ?>
            <td colspan="2" class="tdcontent">
                <div id="building_pts_<?php echo $planet_id; ?>">-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
            <span id="total_b_pts">-</span>
        </td>
     </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_DEFENCES']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='defence_pts_<?php echo $planet_id; ?>'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
            <span id='total_d_pts'>-</span>
        </td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_SATS']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='sat_pts_<?php echo $planet_id; ?>'>-</div>
                <input type='hidden' id='sat_lune_<?php echo $planet_id; ?>' value='<?php echo $planet["Sat"] != "" ? $planet["Sat"] : 0; ?>'/>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
            <span id='total_sat_pts'>-</span>
        </td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_TECHNOLOGIES']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <?php if (!empty($player_empire["technology"]) && $planet["Lab"] == $lab_max) : ?>
                    <div id="techno_pts">-</div>
                <?php else : ?>
                    -
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col"></td>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_TOTALS']; ?>
        </td>
        <?php foreach ($player_planets as $planet_id => $planet) : ?>
            <td colspan="2" class="tdcontent">
                <div id='total_pts_<?php echo $planet_id; ?>'>-</div>
            </td>
        <?php endforeach; ?>
    <td class="og-total-col">
            <span id='total_pts'>-</span>
        </td>
    </tr>
    </tbody>
</table>
</div>

<!-- Tableau des Lunes -->
<?php
// Map moons by coordinates for alignment under parent planet columns
$moon_by_coords = [];
foreach ($player_moons as $mid => $m) {
    $k = $m['galaxy'] . '_' . $m['system'] . '_' . $m['row'];
    $moon_by_coords[$k] = ['id' => $mid, 'moon' => $m];
}
?>
<?php if ($nb_moon > 0): ?>
<div class="og-table-wrapper">
<table class="og-table og-full-table og-table-empire" style="table-layout: fixed;">
    <colgroup>
        <col style="width: <?php echo $label_width_px; ?>px;" />
        <?php for ($ci = 0; $ci < $nb_planete; $ci++): ?>
            <col style="width: <?php echo $per_column_px; ?>px;" />
            <col style="width: <?php echo $per_column_px; ?>px;" />
        <?php endfor; ?>
    </colgroup>
    <thead>
    <tr>
        <th colspan="<?php echo 1 + ($nb_planete * 2); ?>">
            🌙 <?php echo $lang['HOME_SIMU_MOON']; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_EMPIRE_NAME']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <?php $k = $planet['galaxy'] . '_' . $planet['system'] . '_' . $planet['row']; ?>
            <?php $moon_info = $moon_by_coords[$k] ?? null; ?>
            <td class="tdcontent" colspan="2">
                <span class="og-highlight">
                    <?php echo ($moon_info === null || $moon_info['moon']['name'] == "") ? "&nbsp;" : $moon_info['moon']['name']; ?>
                </span>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_COORD']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <?php $k = $planet['galaxy'] . '_' . $planet['system'] . '_' . $planet['row']; ?>
            <?php $moon_info = $moon_by_coords[$k] ?? null; ?>
            <td class="tdcontent" colspan="2">
                <?php if ($moon_info === null): ?>
                    &nbsp;
                <?php else: ?>
                    [<?php echo $moon_info['moon']["galaxy"] . "&nbsp;" . $moon_info['moon']["system"] . "&nbsp;" . $moon_info['moon']["row"] ?>]
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_FIELDS']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <?php $k = $planet['galaxy'] . '_' . $planet['system'] . '_' . $planet['row']; ?>
            <?php $moon_info = $moon_by_coords[$k] ?? null; ?>
            <td class="tdcontent" colspan="2">
                <?php if ($moon_info === null): ?>
                    &nbsp;
                <?php else: ?>
                    <?php $fields = ($moon_info['moon']["fields"] == "0") ? 0 : $moon_info['moon']["fields"]; ?>
                    <?php echo $moon_info['moon']["fields_used"] . " / " . $fields; ?>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname">
            <?php echo $lang['HOME_SIMU_SATS']; ?>
        </td>
        <?php foreach ($player_planets as $i => $planet) : ?>
            <?php $k = $planet['galaxy'] . '_' . $planet['system'] . '_' . $planet['row']; ?>
            <?php $moon_info = $moon_by_coords[$k] ?? null; ?>
            <td class="tdcontent" colspan="2">
                <?php if ($moon_info === null): ?>
                    &nbsp;
                <?php else: ?>
                    <div id='sat_pts_<?php echo $moon_info['id']; ?>_moon'>-</div>
                    <input type='hidden' id='sat_lune_<?php echo $moon_info['id']; ?>' value='<?php echo $moon_info['moon']["Sat"] ?? 0; ?>'/>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <?php
    // Server-side moon point computation to pre-fill table
    $moon_building_points = [];
    $moon_defense_points = [];
    $moon_points = [];
    $building_costs = [
        'Lab' => 800, 'Silo' => 41000, 'Dock' => 250, 'BaLu' => 80000, 'Pha' => 80000, 'PoSa' => 8000000
    ];
    $defense_costs = [
        'LM' => 2000, 'LLE' => 2000, 'LLO' => 8000, 'CG' => 37000, 'AI' => 8000,
        'LP' => 130000, 'PB' => 20000, 'GB' => 100000, 'MIC' => 10000, 'MIP' => 25000
    ];
    foreach ($player_moons as $mid => $mdata) {
        $b_pts = 0; $d_pts = 0;
        foreach ($building_costs as $kname => $base) {
            $lvl = isset($mdata[$kname]) ? (int)$mdata[$kname] : 0;
            if ($lvl > 0) { $b_pts += $base * (pow(2, $lvl) - 1); }
        }
        if (isset($moon_defense[$mid])) {
            foreach ($moon_defense[$mid] as $dname => $amount) {
                if (isset($defense_costs[$dname]) && (int)$amount > 0) {
                    $d_pts += $defense_costs[$dname] * (int)$amount;
                }
            }
        }
        $moon_building_points[$mid] = $b_pts;
        $moon_defense_points[$mid] = $d_pts;
        $moon_points[$mid] = $b_pts + $d_pts;
    }
    $total_moon_points = array_sum($moon_points);
    ?>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_BUILDINGS']; ?></td>
        <?php foreach ($player_planets as $i => $planet): ?>
            <?php $k = $planet['galaxy'] . '_' . $planet['system'] . '_' . $planet['row']; $moon_info = $moon_by_coords[$k] ?? null; ?>
            <td class="tdcontent" colspan="2">
                <?php if ($moon_info === null): ?>&nbsp;<?php else: ?>
                    <span id="moon_building_pts_<?php echo $moon_info['id']; ?>"><?php $mid=$moon_info['id']; echo isset($moon_building_points[$mid])?number_format(round($moon_building_points[$mid]/1000),0,',',' '):'-'; ?></span>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_DEFENCES']; ?></td>
        <?php foreach ($player_planets as $i => $planet): ?>
            <?php $k = $planet['galaxy'] . '_' . $planet['system'] . '_' . $planet['row']; $moon_info = $moon_by_coords[$k] ?? null; ?>
            <td class="tdcontent" colspan="2">
                <?php if ($moon_info === null): ?>&nbsp;<?php else: ?>
                    <span id="moon_defense_pts_<?php echo $moon_info['id']; ?>"><?php $mid=$moon_info['id']; echo isset($moon_defense_points[$mid])?number_format(round($moon_defense_points[$mid]/1000),0,',',' '):'-'; ?></span>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_TOTALS']; ?></td>
        <?php foreach ($player_planets as $i => $planet): ?>
            <?php $k = $planet['galaxy'] . '_' . $planet['system'] . '_' . $planet['row']; $moon_info = $moon_by_coords[$k] ?? null; ?>
            <td class="tdcontent" colspan="2">
                <?php if ($moon_info === null): ?>&nbsp;<?php else: ?>
                    <span id="moon_total_pts_<?php echo $moon_info['id']; ?>"><?php $mid=$moon_info['id']; echo isset($moon_points[$mid])?number_format(round($moon_points[$mid]/1000),0,',',' '):'-'; ?></span>
                <?php endif; ?>
            </td>
        <?php endforeach; ?>
    </tr>
    </tbody>
</table>
</div>
<?php endif; ?>

<?php
// --- Server-side totals pre-calculation for initial fill ---
// Planet Building Points
$planet_building_total = 0; $planet_defense_total = 0; $planet_sat_total = 0; $moon_sat_total = 0; $tech_total = 0;

$init_b_prix = [
    'UdR' => 720,
    'UdN' => 1600000,
    'CSp' => 700,
    'HM' => 1000,
    'HC' => 1500,
    'HD' => 2000,
    'Lab' => 800,
    'Ter' => 150000,
    'DdR' => 60000,
    'Silo' => 41000,
    'Dock' => 250,
    'BaLu' => 80000,
    'Pha' => 80000,
    'PoSa' => 8000000
];
$init_d_prix = [
    'LM' => 2000,
    'LLE' => 2000,
    'LLO' => 8000,
    'CG' => 37000,
    'AI' => 8000,
    'LP' => 130000,
    'PB' => 20000,
    'GB' => 100000,
    'MIC' => 10000,
    'MIP' => 25000
];

foreach ($player_planets as $pid => $pdata) {
    $M = (int)($pdata['M'] ?? 0);
    $C = (int)($pdata['C'] ?? 0);
    $D = (int)($pdata['D'] ?? 0);
    $CES = (int)($pdata['CES'] ?? 0);
    $CEF = (int)($pdata['CEF'] ?? 0);
    // Main mines & energy buildings (match JS formulas)
    $b_pts = ((60 + 15) * (1 - pow(1.5, $M)) / (-0.5))
        + ((48 + 24) * (1 - pow(1.6, $C)) / (-0.6))
        + ((225 + 75) * (1 - pow(1.5, $D)) / (-0.5))
        + ((75 + 30) * (1 - pow(1.5, $CES)) / (-0.5))
        + ((900 + 360 + 180) * (1 - pow(1.8, $CEF)) / (-0.8));
    // Additional buildings
    foreach ($init_b_prix as $bcode => $base) {
        if (isset($pdata[$bcode])) {
            $lvl = (int)$pdata[$bcode];
            if ($lvl > 0) { $b_pts += $base * (pow(2, $lvl) - 1); }
        }
    }
    $planet_building_total += $b_pts;
    // Defenses
    if (isset($planet_defense[$pid])) {
        foreach ($planet_defense[$pid] as $dcode => $amount) {
            if (isset($init_d_prix[$dcode]) && (int)$amount > 0) {
                $planet_defense_total += $init_d_prix[$dcode] * (int)$amount;
            }
        }
    }
    // Satellites (planet)
    $planet_sat_total += ((int)($pdata['Sat'] ?? 0)) * 2.5;
}

// Moon satellites (not aggregated per planet like JS, kept separate)
foreach ($player_moons as $mid => $mdata) {
    $moon_sat_total += ((int)($mdata['Sat'] ?? 0)) * 2.5;
}

// Tech points
$technoPrix = [
    'Esp' => 1400,
    'Ordi' => 1000,
    'Armes' => 1000,
    'Bouclier' => 800,
    'Protection' => 1000,
    'NRJ' => 1200,
    'Hyp' => 6000,
    'RC' => 1000,
    'RI' => 6600,
    'PH' => 36000,
    'Laser' => 300,
    'Ions' => 1400,
    'Plasma' => 7000,
    'RRI' => 800000,
    'Graviton' => 0,
    'Astrophysique' => 16000
];
foreach ($player_technology as $tcode => $lvl) {
    if ($tcode === 'player_id') continue;
    if ($tcode === 'Astrophysique') continue; // special handling below
    if (isset($technoPrix[$tcode])) {
        $lvl_i = (int)$lvl;
        if ($lvl_i > 0) { $tech_total += $technoPrix[$tcode] * (pow(2, $lvl_i) - 1); }
    }
}
// Astrophysique progressive cost
$astro_lvl = (int)($player_technology['Astrophysique'] ?? 0);
if ($astro_lvl > 0) {
    $tech_total += $technoPrix['Astrophysique'];
    $prev = $technoPrix['Astrophysique'];
    for ($i = 1; $i < $astro_lvl; $i++) {
        $prev = $prev * 1.75;
        $tech_total += $prev;
    }
}

$total_moon_points_k = isset($total_moon_points) ? round($total_moon_points / 1000) : 0;
$total_b_pts_k = round($planet_building_total / 1000);
$total_d_pts_k = round($planet_defense_total / 1000);
$tech_total_k = round($tech_total / 1000);
$total_sat_pts_moon_k = round($moon_sat_total / 1); // already points (2.5 per sat) keep as integer display
$total_sat_pts_k = round(($planet_sat_total + $moon_sat_total) / 1);
$overall_total_k = $total_b_pts_k + $total_d_pts_k + $tech_total_k + $total_sat_pts_k + $total_moon_points_k;
?>

<!-- Tableau des Totaux (aligné comme les tableaux précédents) -->
<div class="og-table-wrapper">
<table class="og-table og-full-table og-table-empire">
    <thead>
    <tr>
        <th class="tdname">&nbsp;</th>
        <th class="tdcontent" colspan="3"><?php echo $lang['HOME_SIMU_TOTALS']; ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_BUILDINGS']; ?></td>
        <td class="tdcontent" colspan="3"><span id="total_b_pts"><?php echo number_format($total_b_pts_k,0,',',' '); ?></span></td>
    </tr>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_DEFENCES']; ?></td>
        <td class="tdcontent" colspan="3"><span id="total_d_pts"><?php echo number_format($total_d_pts_k,0,',',' '); ?></span></td>
    </tr>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_TECHNOLOGIES']; ?></td>
        <td class="tdcontent" colspan="3"><span id="techno_pts"><?php echo number_format($tech_total_k,0,',',' '); ?></span></td>
    </tr>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_SATS']; ?> (<?php echo $lang['HOME_SIMU_MOON']; ?>)</td>
        <td class="tdcontent" colspan="3"><span id='total_sat_pts_moon'><?php echo number_format($total_sat_pts_moon_k,0,',',' '); ?></span></td>
    </tr>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_SATS']; ?> (<?php echo $lang['HOME_EMPIRE_SUMMARY']; ?>)</td>
        <td class="tdcontent" colspan="3"><span id='total_sat_pts'><?php echo number_format($total_sat_pts_k,0,',',' '); ?></span></td>
    </tr>
    <tr>
        <td class="tdname">🌙 <?php echo $lang['HOME_SIMU_MOON'].' '.$lang['HOME_SIMU_TOTALS']; ?></td>
        <td class="tdcontent" colspan="3"><span id='total_moon_pts'><?php echo $total_moon_points_k>0?number_format($total_moon_points_k,0,',',' '):'-'; ?></span></td>
    </tr>
    <tr>
        <td class="tdname"><?php echo $lang['HOME_SIMU_TOTALS']; ?></td>
        <td class="tdcontent" colspan="3"><span id='total_pts'><?php echo number_format($overall_total_k,0,',',' '); ?></span></td>
    </tr>
    </tbody>
</table>
</div>

<script>
    function refresh_page() {
        const planetIds = [<?php echo implode(',', array_keys($player_planets)); ?>];
        const moonIds = [<?php echo implode(',', array_keys($player_moons)); ?>];
        const planetBuildings = <?php echo json_encode($player_planets); ?>;
        const moonBuildings = <?php echo json_encode($player_moons); ?>;
        const technologies = <?php echo json_encode($player_technology); ?>;
        const planetDefenses = <?php echo json_encode($planet_defense); ?>;
        const moonDefenses = <?php echo json_encode($moon_defense); ?>;
        
        // Update for both planets and moons
        update_page(planetIds, planetBuildings, technologies, planetDefenses);
        if (moonIds.length > 0) {
            update_moon_page(moonIds, moonBuildings, technologies, moonDefenses);
        }
    }

    refresh_page();
</script>
