<?php global $server_config, $lang, $user_data;

/**
 * Affichage Empire - Page Simulation
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

use Ogsteam\Ogspy\Model\Player_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$player_data = (new Player_Model())->get_player_data($user_data['player_id']);
$user_empire = player_get_empire($player_data['id']);

$player_building = $user_empire["building"];
$player_defense = $user_empire["defense"];
$player_percentage = $user_empire["player_percentage"];



if ($user_empire["technology"]) {
    $player_technology = $user_empire["technology"];
} else {
    $player_technology['NRJ'] = 0;
    $player_technology['Plasma'] = 0;
}


$nb_planete = find_nb_planete_user($player_data['id']);


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

//Calcul et correction boosters :
foreach ($player_building as $planet_id => $planet) {
    /*Boosters et extensions modification :*/
    //booster dans fonctions
    $booster_tab[$planet_id] = booster_decode($planet["boosters"]);
    $iFields = (int) $planet["fields"]; // si pas d'info sur batiment, variable string
    $planet["fields"] = $iFields + $booster_tab[$planet_id]['extention_p'];
}
?>

<table class="og-table og-full-table og-table-simu " id="simu" title="<?php echo $nb_planete; ?>">
    <thead>
        <tr>
            <th>
            </th>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <th colspan="2">
                    <?php echo ($planet["name"] == "") ? "xxx" : $planet["name"]; ?>
                </th>
            <?php endforeach; ?>
            <th>
                <?php echo $lang['HOME_SIMU_TOTALS']; ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_COORD']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $coordinates = "[" . $planet["galaxy"] . "&nbsp;" . $planet["system"] . "&nbsp;" . $planet["row"] . "]"; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $coordinates; ?><input id='position_<?php echo $planet["id"]; ?>' type='hidden' value='<?php echo $planet["row"]; ?>'>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_FIELDS']; ?>
            </td>
            <?php $sum_field = 0; ?>
            <?php $sum_filed_used = 0; ?>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $fields = ($planet["fields"] == "0") ? "?" : $planet["fields"]; ?>
                <?php $fields_used = ($planet["fields_used"] >= 0) ? $planet["fields_used"]  : "&nbsp;"; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $fields . " / " . $fields_used; ?>
                </td>
                <?php // Pour totaux
                ?>
                <?php $sum_field = (is_numeric($fields)) ? $sum_field + $fields : $sum_field; ?>
                <?php $sum_filed_used = (is_numeric($fields_used)) ? $sum_filed_used + $fields_used : $fields_used; ?>
            <?php endforeach; ?>
            <td class="og-highlight">
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
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $temperature_min = ($planet["temperature_min"] == "") ? "&nbsp;" : $planet["temperature_min"]; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $temperature_min; ?><input id='temperature_min_<?php echo $planet_id; ?>' type='hidden' value='<?php echo $temperature_min; ?>'>
                </td>
                <?php $t_min = (is_numeric($temperature_min) && $temperature_min < $t_min) ? $temperature_min : $t_min; // Pour totaux
                ?>
            <?php endforeach; ?>
            <td class="og-highlight">
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
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $temperature_max = ($planet["temperature_max"] == "") ? "&nbsp;" : $planet["temperature_max"]; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $temperature_max; ?><input id='temperature_max_<?php echo $planet_id; ?>' type='hidden' value='<?php echo $temperature_max; ?>'>
                </td>
                <?php $t_max = (is_numeric($temperature_max) && $temperature_max > $t_max) ? $temperature_max : $t_max; // Pour totaux
                ?>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id='T_max'>
                    <?php echo $t_max; ?>

                </div>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_EXTENSION']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $booster =  (isset($planet["booster_tab"]['extention_p'])) ? $planet["booster_tab"]['extention_p'] : "0"; ?>
                <th colspan='2' class="tdcontent">
                    <?php echo $booster; ?><input id='extension_<?php echo $planet_id; ?>' type='hidden' value='<?php echo $booster; ?>'>
                    </th>
                <?php endforeach; ?>
                <td class="og-highlight">
                </td>
        </tr>
    </tbody>
    <!--
    Energie
    -->
    <thead>
        <tr>
            <th colspan="<?php echo 2 * ($nb_planete + 2); ?>"><?php echo $lang['HOME_SIMU_ENERGYS']; ?></th>

        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td class="tdname" colspan="<?php echo 2 * ($nb_planete + 1) - 2; ?>">
                <?php echo $lang['HOME_SIMU_TECH_ENERGY']; ?> <input type="text" id="NRJ" size="2" maxlength="2" value="<?php echo $player_technology['NRJ'] ?>" onchange='update_page();'> -
                <?php echo $lang['HOME_SIMU_TECH_PLASMA']; ?> <input type="text" id="Plasma" size="2" maxlength="2" value="<?php echo $player_technology['Plasma'] ?>" onchange='update_page();'> -
                <?php echo $lang['HOME_SIMU_OFF_INGE']; ?> <input type='checkbox' id='c_off_ingenieur' <?php echo ($player_data["off_ingenieur"] == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'> -
                <?php echo $lang['HOME_SIMU_OFF_GEO']; ?> <input type='checkbox' id='c_off_geologue' <?php echo ($player_data["off_geologue"] == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'> -
                <?php echo $lang['HOME_SIMU_OFF_FULL']; ?> <input type='checkbox' id='c_off_full' <?php echo ($off_full == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'> -
                <?php echo $lang['HOME_SIMU_CLASS_COLLECT']; ?> <input type='checkbox' id='c_class_collect' <?php echo ($class_collect == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'>
            </td>
            <td class="og-highlight"></td>
        </tr>

        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_SOLARPLANT_SHORT']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td class="tdcontent">
                    <input type='text' id='CES_<?php echo $planet_id; ?>' size='2' maxlength='2' value='<?php echo $planet["CES"]; ?>' onchange='update_page();'>
                </td>
                <td class="tdcontent">
                    <select id='CES_<?php echo $planet_id; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $isSlected = ($player_percentage[$planet_id]['CES_percentage'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_FUSIONPLANT_SHORT']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td class="tdcontent">
                    <input type='text' id='CEF_<?php echo $planet_id; ?>' size='2' maxlength='2' value='<?php echo $planet["CEF"]; ?>' onchange='update_page();'>
                </td>
                <td class="tdcontent">
                    <select id='CEF_<?php echo $planet_id; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $isSlected = ($player_percentage[$planet_id]['CEF_percentage'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_SATELLITES']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td class="tdcontent">
                    <input type='text' id='Sat_<?php echo $planet_id; ?>' size='5' maxlength='5' value='<?php echo $planet["Sat"]; ?>' onchange='update_page();'>
                </td>
                <td class="tdcontent">
                    <select id='Sat_<?php echo $planet_id; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $isSlected = ($player_percentage[$planet_id]['Sat_percentage'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_BOOSTERNRJ']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td class="tdcontent" colspan='2'>
                    <select id='E_<?php echo $planet_id; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php $planet["booster_tab"]['booster_e_val'] = (!isset($planet["booster_tab"]['booster_e_val'])) ? 0 : $planet["booster_tab"]['booster_e_val']; ?>
                        <?php for ($j = 80; $j >= 0; $j = $j - 20) : ?>
                            <?php $isSlected = ($planet["booster_tab"]['booster_e_val'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_ENERGY']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td class="tdcontent" colspan='2'>
                    <div id='NRJ_<?php echo $planet_id; ?>'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="E_NRJ">-</div>
            </td>
        </tr>
    </tbody>
    <!--
    Foreuse
    -->
    <thead>
        <tr>
            <th colspan="<?php echo 2 * ($nb_planete + 2); ?>">
                <?php echo $lang['HOME_SIMU_CRAWLER']; ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_CRAWLER']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $FOR = $planet["FOR"]; ?>
                <td class="tdcontent">
                    <input type='text' id='For_<?php echo $planet_id; ?>' size='4' maxlength='4' value='<?php echo $FOR; ?>' onchange='update_page();'>
                    /
                    <div id='FOR_<?php echo $planet_id; ?>_max'>-</div>
                </td>
                <td class="tdcontent">
                    <select id='For_<?php echo $planet_id; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 150; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($player_percentage[$planet_id]['FOR_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_ENERGY_USAGE']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='FOR_<?php echo $planet_id; ?>_conso'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="FOR_conso">-</div>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_PRODUCTION']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='FOR_<?php echo $planet_id; ?>_prod'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
    </tbody>
    <!--
    Métal
    -->
    <thead>
        <tr>
            <th colspan="<?php echo 2 * ($nb_planete + 2); ?>">
                <?php echo ($lang['HOME_SIMU_METAL']); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_LEVEL']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $M = $planet["M"]; ?>
                <td lass="tdcontent">
                    <input type='text' id='M_<?php echo $planet_id; ?>' size='2' maxlength='2' value='<?php echo $M; ?>' onchange='update_page();'>
                </td>
                <td lass="tdcontent">
                    <select id='M_<?php echo $planet_id; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($player_percentage[$planet_id]['M_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_BOOSTERMETAL']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <select id='M_<?php echo $planet_id; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 40; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($planet["booster_tab"]['booster_m_val'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_ENERGY_USAGE']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='M_<?php echo $planet_id; ?>_conso'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="M_conso">-</div>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_PRODUCTION']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='M_<?php echo $planet_id; ?>_prod'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="M_prod">-</div>
            </td>
        </tr>
    </tbody>
    <!--
    Cristal
    -->
    <thead>
        <tr>

            <th colspan="<?php echo 2 * ($nb_planete + 1); ?>">
                <?php echo ($lang['HOME_SIMU_CRYSTAL']); ?>
            </th>

        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_LEVEL']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $C = $planet["C"]; ?>
                <td lass="tdcontent">
                    <input type='text' id='C_<?php echo $planet_id; ?>' size='2' maxlength='2' value='<?php echo $C; ?>' onchange='update_page();'>
                </td>
                <td lass="tdcontent">
                    <select id='C_<?php echo $planet_id; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($player_percentage[$planet_id]['C_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_BOOSTERCRYSTAL']); ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <?php for($j = 40; $j >= 0; $j = $j - 10); ?>
                    <select id='C_<?php echo $planet_id; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 40; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($planet["booster_tab"]['booster_c_val'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                <?php endforeach; ?>
                </td>
                <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_ENERGY_USAGE']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='C_<?php echo $planet_id; ?>_conso'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="C_conso">-</div>
            </td>
        </tr>

        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_PRODUCTION']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='C_<?php echo $planet_id; ?>_prod'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="C_prod">-</div>
            </td>
        </tr>
    </tbody>
    <!--
    Deutérium
    -->
    <thead>
        <tr>
            <th colspan="<?php echo 2 * ($nb_planete + 1); ?>">
                <?php echo $lang['HOME_SIMU_DEUT']; ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_LEVEL']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $D = $planet["D"]; ?>
                <td lass="tdcontent">
                    <input type='text' id='D_<?php echo $planet_id; ?>' size='2' maxlength='2' value='<?php echo $D; ?>' onchange='update_page();'>
                </td>
                <td lass="tdcontent">
                    <select id='D_<?php echo $planet_id; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($player_percentage[$planet_id]['D_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_BOOSTERDEUT']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <?php for ($j = 40; $j >= 0; $j = $j - 10); ?>
                    <select id='D_<?php echo $planet_id; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 40; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($planet["booster_tab"]['booster_d_val'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                <?php endforeach; ?>
                </td>
                <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_ENERGY_USAGE']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='D_<?php echo $planet_id; ?>_conso'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="D_conso">-</div>
            </td>
        </tr>

        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_PRODUCTION']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='D_<?php echo $planet_id; ?>_prod'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <div id="D_prod">-</div>
            </td>
        </tr>
    </tbody>
    <!--total-->
    <thead>
        <tr>
            <th colspan="<?php echo 2 * ($nb_planete + 1)  ?>">
                <?php echo $lang['HOME_SIMU_POINTSBYPLANET']; ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_BUILDINGS']; ?>
            </td>
            <?php $lab_max = 0; ?>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <?php $lab_max = ($lab_max < $planet["Lab"]) ? $planet["Lab"] : $lab_max; ?>
                <td colspan="2" class="tdcontent">
                    <div id='building_pts_<?php echo $planet_id; ?>'>-</div>
                    <input type='hidden' id='building_<?php echo $planet_id; ?>' value='<?php implode('<>', array_slice($planet, 21, -3, true)); ?>' />
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <span id='total_b_pts'>-</span>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_DEFENCES']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='defence_pts_<?php echo $planet_id; ?>'>-</div>
                    <input type='hidden' id='defence_<?php echo $planet_id; ?>' value='<?php echo implode('<>', $player_defense[$planet_id]); ?>' />
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <span id='total_d_pts'>-</span>
            </td>
        </tr>
<!--        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_SATS']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='sat_pts_<?php echo $planet_id; ?>'>-</div>
                    <input type='hidden' id='sat_lune_<?php echo $planet_id; ?>' value='<?php echo $planet["Sat"] != "" ? $planet["Sat"] : 0; ?>' />
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <span id='total_sat_pts'>-</span>
            </td>
        </tr>
        <tr>-->
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_TECHNOLOGIES']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <?php if ($user_empire["technology"] != null && $planet["Lab"] == $lab_max) : ?>
                        <div id='techno_pts'>-</div>
                        <input type='hidden' id='techno' value='<?php echo implode('<>', $user_empire['technology']); ?>' />
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_TOTALS']; ?>
            </td>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='total_pts_<?php echo $planet_id; ?>'>-</div>
                </td>
            <?php endforeach; ?>
            <td class="og-highlight">
                <span id='total_pts'>-</span>
            </td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th>
            </th>
            <?php foreach ($player_building as $planet_id => $planet) : ?>
                <th colspan="2">
                    <?php echo ($planet["name"] == "") ? "xxx" : $planet["name"]; ?>
                </th>
            <?php endforeach; ?>
            <th>
                <?php echo $lang['HOME_SIMU_TOTALS']; ?>
            </th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    update_page();
</script>
