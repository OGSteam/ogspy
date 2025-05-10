<?php global $server_config, $user_data, $lang;

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

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


$user_empire = user_get_empire($user_data['id']);
$user_building = $user_empire["building"];
$user_defence = $user_empire["defence"];
$user_percentage = $user_empire["user_percentage"];


if ($user_empire["technology"]) {
    $user_technology = $user_empire["technology"];
} else {
    $user_technology['NRJ'] = 0;
    $user_technology['Plasma'] = 0;
}


$nb_planete = find_nb_planete_user($user_data['player_id']);


// ajout infos pour gestion js ...

$officier = $player_data['off_commandant'] + $player_data['off_amiral'] + $player_data['off_ingenieur']
    + $player_data['off_geologue'] + $player_data['off_technocrate'];
$off_full = ($officier == 5) ? '1' : '0';
$class_collect = ($player_data['user_class'] === 'COL') ? '1' : '0';
echo "<input type='hidden' id='vitesse_uni' size='2' maxlength='5' value='" . $server_config['speed_uni'] . "'/>";
echo "<input type='hidden' id='off_ingenieur' value='" . $player_data["off_ingenieur"] . "'/>";
echo "<input type='hidden' id='off_geologue' value='" . $player_data["off_geologue"] . "'/>";
echo "<input type='hidden' id='off_full' value='" . $off_full . "'/>";
echo "<input type='hidden' id='class_collect' value='" . $class_collect . "'/>";

//Calcul et correction boosters :
for ($i = 101; $i <= $nb_planete + 100; $i++) {
    /*Boosters et extensions modification :*/
    //booster dans fonctions
    $booster_tab[$i] = booster_decode($user_building[$i]["boosters"]);
    $iFields = (int) $user_building[$i]["fields"]; // si pas d'info sur batiment, variable string
    $user_building[$i]["fields"] = $iFields + $booster_tab[$i]['extention_p'];
}
?>

<table class="og-table og-full-table og-table-simu " id="simu" title="<?php echo $nb_planete; ?>">
    <thead>
        <tr>
            <th>
            </th>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <th colspan="2">
                    <?php echo ($user_building[$i]["planet_name"] == "") ? "xxx" : $user_building[$i]["planet_name"]; ?>
                </th>
            <?php endfor; ?>
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
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $position = ogame_find_planet_position($user_building[$i]["coordinates"]); ?>
                <?php $coordinates = ($user_building[$i]["coordinates"] == "") ? "&nbsp;" : "[" . $user_building[$i]["coordinates"] . "]"; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $coordinates; ?><input id='position_<?php echo $i; ?>' type='hidden' value='<?php echo $position; ?>'>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_FIELDS']; ?>
            </td>
            <?php $sum_field = 0; ?>
            <?php $sum_filed_used = 0; ?>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $fields = ($user_building[$i]["fields"] == "0") ? "?" : $user_building[$i]["fields"]; ?>
                <?php $fields_used = ($user_building[$i]["fields_used"] >= 0) ? $user_building[$i]["fields_used"]  : "&nbsp;"; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $fields . " / " . $fields_used; ?>
                </td>
                <?php // Pour totaux
                ?>
                <?php $sum_field = (is_numeric($fields)) ? $sum_field + $fields : $sum_field; ?>
                <?php $sum_filed_used = (is_numeric($fields_used)) ? $sum_filed_used + $fields_used : $fields_used; ?>
            <?php endfor; ?>
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
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $temperature_min = ($user_building[$i]["temperature_min"] == "") ? "&nbsp;" : $user_building[$i]["temperature_min"]; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $temperature_min; ?><input id='temperature_min_<?php echo $i; ?>' type='hidden' value='<?php echo $temperature_min; ?>'>
                </td>
                <?php $t_min = (is_numeric($temperature_min) && $temperature_min < $t_min) ? $temperature_min : $t_min; // Pour totaux
                ?>
            <?php endfor; ?>
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
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $temperature_max = ($user_building[$i]["temperature_max"] == "") ? "&nbsp;" : $user_building[$i]["temperature_max"]; ?>
                <td colspan='2' class="tdcontent">
                    <?php echo $temperature_max; ?><input id='temperature_max_<?php echo $i; ?>' type='hidden' value='<?php echo $temperature_max; ?>'>
                </td>
                <?php $t_max = (is_numeric($temperature_max) && $temperature_max > $t_max) ? $temperature_max : $t_max; // Pour totaux
                ?>
            <?php endfor; ?>
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
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $booster =  (isset($user_building[$i]["booster_tab"]['extention_p'])) ? $user_building[$i]["booster_tab"]['extention_p'] : "0"; ?>
                <th colspan='2' class="tdcontent">
                    <?php echo $booster; ?><input id='extension<?php echo $i; ?>' type='hidden' value='<?php echo $booster; ?>'>
                    </th>
                <?php endfor; ?>
                <td class="og-highlight">
                </td>
        </tr>
    </tbody>
    <!--
    Energie
    -->
    <thead>
        <tr>
            <th colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo $lang['HOME_SIMU_ENERGYS']; ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td class="tdname" colspan="<?php echo 2 * ($nb_planete + 1) - 2; ?>">
                <?php echo $lang['HOME_SIMU_TECH_ENERGY']; ?> <input type="text" id="NRJ" size="2" maxlength="2" value="<?php echo $user_technology['NRJ'] ?>" onchange='update_page();'> -
                <?php echo $lang['HOME_SIMU_TECH_PLASMA']; ?> <input type="text" id="Plasma" size="2" maxlength="2" value="<?php echo $user_technology['Plasma'] ?>" onchange='update_page();'> -
                <?php echo $lang['HOME_SIMU_OFF_INGE']; ?> <input type='checkbox' id='c_off_ingenieur' <?php echo ($user_data["off_ingenieur"] == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'> -
                <?php echo $lang['HOME_SIMU_OFF_GEO']; ?> <input type='checkbox' id='c_off_geologue' <?php echo ($user_data["off_geologue"] == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'> -
                <?php echo $lang['HOME_SIMU_OFF_FULL']; ?> <input type='checkbox' id='c_off_full' <?php echo ($off_full == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'> -
                <?php echo $lang['HOME_SIMU_CLASS_COLLECT']; ?> <input type='checkbox' id='c_class_collect' <?php echo ($class_collect == 1) ? 'checked="checked"' : '' ?> onClick='update_page();'>
            </td>
            <td class="og-highlight"></td>
        </tr>

        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_SOLARPLANT_SHORT']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td class="tdcontent">
                    <input type='text' id='CES_<?php echo $i; ?>' size='2' maxlength='2' value='<?php echo $user_building[$i]["CES"]; ?>' onchange='update_page();'>
                </td>
                <td class="tdcontent">
                    <select id='CES_<?php echo $i; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $isSlected = ($user_percentage[$i]['CES_percentage'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_FUSIONPLANT_SHORT']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td class="tdcontent">
                    <input type='text' id='CEF_<?php echo $i; ?>' size='2' maxlength='2' value='<?php echo $user_building[$i]["CEF"]; ?>' onchange='update_page();'>
                </td>
                <td class="tdcontent">
                    <select id='CEF_<?php echo $i; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $isSlected = ($user_percentage[$i]['CEF_percentage'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_SATELLITES']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td class="tdcontent">
                    <input type='text' id='Sat_<?php echo $i; ?>' size='5' maxlength='5' value='<?php echo $user_building[$i]["Sat"]; ?>' onchange='update_page();'>
                </td>
                <td class="tdcontent">
                    <select id='Sat_<?php echo $i; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $isSlected = ($user_percentage[$i]['Sat_percentage'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_BOOSTERNRJ']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td class="tdcontent" colspan='2'>
                    <select id='E_<?php echo $i; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php $user_building[$i]["booster_tab"]['booster_e_val'] = (!isset($user_building[$i]["booster_tab"]['booster_e_val'])) ? 0 : $user_building[$i]["booster_tab"]['booster_e_val']; ?>
                        <?php for ($j = 80; $j >= 0; $j = $j - 20) : ?>
                            <?php $isSlected = ($user_building[$i]["booster_tab"]['booster_e_val'] == $j) ?  " selected='selected' " : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $isSlected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_ENERGY']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td class="tdcontent" colspan='2'>
                    <div id='NRJ_<?php echo $i; ?>'>-</div>
                </td>
            <?php endfor; ?>
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
            <th colspan="<?php echo 2 * ($nb_planete + 1); ?>">
                <?php echo $lang['HOME_SIMU_CRAWLER']; ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_CRAWLER']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $FOR = $user_building[$i]["FOR"]; ?>
                <td class="tdcontent">
                    <input type='text' id='For_<?php echo $i; ?>' size='4' maxlength='4' value='<?php echo $FOR; ?>' onchange='update_page();'>
                    /
                    <div id='FOR_<?php echo $i; ?>_max'>-</div>
                </td>
                <td class="tdcontent">
                    <select id='For_<?php echo $i; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 150; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($user_percentage[$i]['FOR_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_ENERGY_USAGE']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='FOR_<?php echo $i; ?>_conso'>-</div>
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <div id="FOR_conso">-</div>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_PRODUCTION']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='FOR_<?php echo $i; ?>_prod'>-</div>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
    </tbody>
    <!--
    Métal
    -->
    <thead>
        <tr>
            <th colspan="<?php echo 2 * ($nb_planete + 1); ?>">
                <?php echo ($lang['HOME_SIMU_METAL']); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_LEVEL']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $M = $user_building[$i]["M"]; ?>
                <td lass="tdcontent">
                    <input type='text' id='M_<?php echo $i; ?>' size='2' maxlength='2' value='<?php echo $M; ?>' onchange='update_page();'>
                </td>
                <td lass="tdcontent">
                    <select id='M_<?php echo $i; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($user_percentage[$i]['M_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_BOOSTERMETAL']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <select id='M_<?php echo $i; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 40; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($user_building[$i]["booster_tab"]['booster_m_val'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_ENERGY_USAGE']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='M_<?php echo $i; ?>_conso'>-</div>
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <div id="M_conso">-</div>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_PRODUCTION']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='M_<?php echo $i; ?>_prod'>-</div>
                </td>
            <?php endfor; ?>
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
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $C = $user_building[$i]["C"]; ?>
                <td lass="tdcontent">
                    <input type='text' id='C_<?php echo $i; ?>' size='2' maxlength='2' value='<?php echo $C; ?>' onchange='update_page();'>
                </td>
                <td lass="tdcontent">
                    <select id='C_<?php echo $i; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($user_percentage[$i]['C_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo ($lang['HOME_SIMU_BOOSTERCRYSTAL']); ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <?php for ($j = 40; $j >= 0; $j = $j - 10); ?>
                    <select id='C_<?php echo $i; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 40; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($user_building[$i]["booster_tab"]['booster_c_val'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                <?php endfor; ?>
                </td>
                <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_ENERGY_USAGE']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='C_<?php echo $i; ?>_conso'>-</div>
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <div id="C_conso">-</div>
            </td>
        </tr>

        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_PRODUCTION']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='C_<?php echo $i; ?>_prod'>-</div>
                </td>
            <?php endfor; ?>
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
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $D = $user_building[$i]["D"]; ?>
                <td lass="tdcontent">
                    <input type='text' id='D_<?php echo $i; ?>' size='2' maxlength='2' value='<?php echo $D; ?>' onchange='update_page();'>
                </td>
                <td lass="tdcontent">
                    <select id='D_<?php echo $i; ?>_percentage' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 100; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($user_percentage[$i]['D_percentage'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_BOOSTERDEUT']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <?php for ($j = 40; $j >= 0; $j = $j - 10); ?>
                    <select id='D_<?php echo $i; ?>_booster' onchange='update_page();' onKeyUp='update_page();'>
                        <?php for ($j = 40; $j >= 0; $j = $j - 10) : ?>
                            <?php $selected = ($user_building[$i]["booster_tab"]['booster_d_val'] == $j) ? " selected='selected'" : "" ?>
                            <option value='<?php echo $j; ?>' <?php echo $selected; ?>>
                                <?php echo $j; ?>%
                            </option>
                        <?php endfor; ?>
                    </select>
                <?php endfor; ?>
                </td>
                <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_ENERGY_USAGE']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='D_<?php echo $i; ?>_conso'>-</div>
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <div id="D_conso">-</div>
            </td>
        </tr>

        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_PRODUCTION']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='D_<?php echo $i; ?>_prod'>-</div>
                </td>
            <?php endfor; ?>
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
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <?php $lab_max = ($lab_max < $user_building[$i]["Lab"]) ? $user_building[$i]["Lab"] : $lab_max; ?>
                <td colspan="2" class="tdcontent">
                    <div id='building_pts_<?php echo $i; ?>'>-</div>
                    <input type='hidden' id='building_<?php echo $i; ?>' value='<?php implode('<>', array_slice($user_building[$i], 21, -3, true)); ?>' />
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <span id='total_b_pts'>-</span>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_DEFENCES']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='defence_pts_<?php echo $i; ?>'>-</div>
                    <input type='hidden' id='defence_<?php echo $i; ?>' value='<?php echo implode('<>', $user_defence[$i]); ?>' />
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <span id='total_d_pts'>-</span>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_MOON']; ?>
            </td>
            <?php for ($i = 201; $i <= 200 + $nb_planete; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='lune_pts_<?php echo $i; ?>'>-</div>
                    <?php $lune_b_i_value = ($user_building[$i]) ? implode('<>', array_slice($user_building[$i], 23, -3, true)) : "0"; ?>
                    <input type='hidden' id='lune_b_<?php echo $i; ?>' value='<?php echo $lune_b_i_value; ?>' />
                    <input type='hidden' id='lune_d_<?php echo $i; ?>' value='<?php echo implode('<>', $user_defence[$i]); ?>' />
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <span id='total_lune_pts'>-</span>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_SATS']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='sat_pts_<?php echo $i; ?>'>-</div>
                    <input type='hidden' id='sat_lune_<?php echo $i; ?>' value='<?php echo $user_building[$i + 100]["Sat"] != "" ? $user_building[$i + 100]["Sat"] : 0; ?>' />
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <span id='total_sat_pts'>-</span>
            </td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_TECHNOLOGIES']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <?php if ($user_empire["technology"] != null && $user_building[$i]["Lab"] == $lab_max) : ?>
                        <div id='techno_pts'>-</div>
                        <input type='hidden' id='techno' value='<?php echo implode('<>', $user_empire['technology']); ?>' />
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
            <td class="og-highlight"></td>
        </tr>
        <tr>
            <td class="tdname">
                <?php echo $lang['HOME_SIMU_TOTALS']; ?>
            </td>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <td colspan="2" class="tdcontent">
                    <div id='total_pts_<?php echo $i; ?>'>-</div>
                </td>
            <?php endfor; ?>
            <td class="og-highlight">
                <span id='total_pts'>-</span>
            </td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th>
            </th>
            <?php for ($i = 101; $i <= $nb_planete + 100; $i++) : ?>
                <th colspan="2">
                    <?php echo ($user_building[$i]["planet_name"] == "") ? "xxx" : $user_building[$i]["planet_name"]; ?>
                </th>
            <?php endfor; ?>
            <th>
                <?php echo $lang['HOME_SIMU_TOTALS']; ?>
            </th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    update_page();
</script>
