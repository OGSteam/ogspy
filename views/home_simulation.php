<?php
/**
 * Affichage Empire - Page Simulation
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

$user_empire = user_get_empire($user_data['user_id']);
$user_building = $user_empire["building"];
$user_defence = $user_empire["defence"];
$user_percentage = $user_empire["user_percentage"];


if ($user_empire["technology"]) {
    $user_technology = $user_empire["technology"];
} else {
    $user_technology['NRJ'] = 0;
    $user_technology['Plasma'] = 0;
}


$nb_planete = find_nb_planete_user($user_data['user_id']);


// ajout infos pour gestion js ...
$officier = $user_data['off_commandant'] + $user_data['off_amiral'] + $user_data['off_ingenieur']
    + $user_data['off_geologue'] + $user_data['off_technocrate'];
$off_full = ($officier == 5) ? '1' : '0';
$class_collect = ($user_data['user_class'] === 'COL') ? '1' : '0';
echo "<input type='hidden' id='vitesse_uni' value='" . $server_config['speed_uni'] . "'/>";
echo "<input type='hidden' id='off_ingenieur' value='" . $user_data["off_ingenieur"] . "'/>";
echo "<input type='hidden' id='off_geologue' value='" . $user_data["off_geologue"] . "'/>";
echo "<input type='hidden' id='off_full' value='" . $off_full . "'/>";
echo "<input type='hidden' id='class_collect' value='" . $class_collect . "'/>";

//Calcul et correction boosters :
for ($i = 101; $i <= $nb_planete + 100; $i++) {
    /*Boosters et extensions modification :*/
    //booster dans fonctions
    $booster_tab[$i] = booster_decode($user_building[$i]["boosters"]);
    $iFields = (int)$user_building[$i]["fields"]; // si pas d'info sur batiment, variable string
    $user_building[$i]["fields"] = $iFields + $booster_tab[$i]['extention_p'];
}
?>

<script src="js/ogame_formula.js"></script>

<table id="simu" title="<?php echo $nb_planete; ?>">
    <thead>
    <tr>
        <th></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $name = $user_building[$i]["planet_name"];
            if ($name == "") {
                $name = "xxx";
            }

            echo "\t" . "<th class='c' colspan='2'><a>" . $name . "</a></td>" . "\n";
        }
        ?>
        <th><?php echo($lang['HOME_SIMU_TOTALS']); ?></th>
    </tr>
    </thead>
    <body>
    <tr>
        <th><?php echo($lang['HOME_SIMU_COORD']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $coordinates = $user_building[$i]["coordinates"];
            if ($coordinates == "") {
                $coordinates = "&nbsp;";
            } else {
                $coordinates = "[" . $coordinates . "]";
            }

            echo "\t" . "<td colspan='2'>" . $coordinates . "</td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_FIELDS']); ?></th>
        <?php
        $sum_field = 0;
        $sum_filed_used = 0;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $fields = $user_building[$i]["fields"];
            if ($fields == "0") {
                $fields = "?";
            }
            $fields_used = $user_building[$i]["fields_used"];
            if ($fields_used >= 0) {
                $fields = $fields_used . " / " . $fields;
            } else {
                $fields = "&nbsp;";
            }

            echo "\t" . "<td colspan='2'>" . $fields . "</td>" . "\n";

            if (is_numeric($user_building[$i]["fields"])) {
                $sum_field += $user_building[$i]["fields"];
            }
            if (is_numeric($user_building[$i]["fields_used"])) {
                $sum_filed_used += $user_building[$i]["fields_used"];
            }
        }
        echo "\t<td><div id='T_cases'>" . $sum_filed_used . "/" . $sum_field . "</div></td>";
        ?>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_MINTEMP']); ?></th>
        <?php
        $t_min = +INF;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $temperature_min = $user_building[$i]["temperature_min"];
            if ($temperature_min == "") {
                $temperature_min = "&nbsp;";
            }
            echo "\t" . "<td colspan='2'>" . $temperature_min . "<input id='temperature_min_" . $i . "' type='hidden' value='" . $temperature_min . "'></td>" . "\n";

            if (is_numeric($user_building[$i]["temperature_min"]) && $user_building[$i]["temperature_min"] < $t_min) {
                $t_min = $user_building[$i]["temperature_min"];
            }
        }
        echo "\t<td><div id='T_min'>" . $t_min . "</div></td>";
        ?>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_MAXTEMP']); ?></th>
        <?php
        $t_max = -INF;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $temperature_max = $user_building[$i]["temperature_max"];
            if ($temperature_max == "") {
                $temperature_max = "&nbsp;";
            }
            echo "\t" . "<td colspan='2'>" . $temperature_max . "<input id='temperature_max_" . $i . "' type='hidden' value='" . $temperature_max . "'></td>" . "\n";

            if (is_numeric($user_building[$i]["temperature_max"]) && $user_building[$i]["temperature_max"] > $t_max) {
                $t_max = $user_building[$i]["temperature_max"];
            }
        }
        echo "\t<td><div id='T_max'>" . $t_max . "</div></td>";
        ?>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_EXTENSION']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $booster = "0";

            if (isset($user_building[$i]["booster_tab"]['extention_p'])) {
                $booster = $user_building[$i]["booster_tab"]['extention_p']; // La vue Lune n'existe pas sur la page simulation
            }


            echo "\t" . "<td colspan='2'>" . $booster . "<input id='extension" . $i . "' type='hidden' value='" . $booster . "'></th>\n";
        }
        ?>
        <td></td>
    </tr>
    <!--
    Energie
    -->
    <tr>
        <th ><?php echo($lang['HOME_SIMU_ENERGYS']); ?></th>
        <td  colspan="1"><?php echo($lang['HOME_SIMU_TECH_ENERGY']); ?> <input type="text" id="NRJ" size="2"
                                                                                        maxlength="2"
                                                                                        value="<?php print $user_technology['NRJ'] ?>"
                                                                                        onchange='update_page();'></td>
        <td  colspan="1"><?php echo($lang['HOME_SIMU_TECH_PLASMA']); ?> <input type="text" id="Plasma" size="2"
                                                                                        maxlength="2"
                                                                                        value="<?php print $user_technology['Plasma'] ?>"
                                                                                        onchange='update_page();'></td>
        <td  colspan="1"><?php echo($lang['HOME_SIMU_OFF_INGE']); ?> <input type='checkbox'
                                                                                     id='c_off_ingenieur' <?php print ($user_data["off_ingenieur"] == 1) ? 'checked="checked"' : '' ?>
                                                                                     onClick='update_page();'>
        <td  colspan="1"><?php echo($lang['HOME_SIMU_OFF_GEO']); ?> <input type='checkbox'
                                                                                    id='c_off_geologue' <?php print ($user_data["off_geologue"] == 1) ? 'checked="checked"' : '' ?>
                                                                                    onClick='update_page();'>
        <td  colspan="1"><?php echo($lang['HOME_SIMU_OFF_FULL']); ?> <input type='checkbox'
                                                                                     id='c_off_full' <?php print ($off_full == 1) ? 'checked="checked"' : '' ?>
                                                                                     onClick='update_page();'>
        <td  colspan="1"><?php echo($lang['HOME_SIMU_CLASS_COLLECT']); ?> <input type='checkbox'
                                                                                          id='c_class_collect' <?php print ($class_collect == 1) ? 'checked="checked"' : '' ?>
                                                                                          onClick='update_page();'>
        <td  colspan="<?php echo 2 * ($nb_planete + 1) - 6; ?>">&nbsp;</td>
    </tr>

    <tr>
        <th><?php echo($lang['HOME_SIMU_SOLARPLANT_SHORT']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $CES = $user_building[$i]["CES"];
            echo "\t" . "<td><input type='text' id='CES_" . $i . "' size='2' maxlength='2' value='" . $CES . "' onchange='update_page();'></td>" . "\n";

            echo "\t" . "<td>";
            echo "<select id='CES_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['CES_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_FUSIONPLANT_SHORT']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $CEF = $user_building[$i]["CEF"];
            echo "\t" . "<td><input type='text' id='CEF_" . $i . "' size='2' maxlength='2' value='" . $CEF . "' onchange='update_page();'></td>" . "\n";

            echo "\t" . "<td>";
            echo "<select id='CEF_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['CEF_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_SATELLITES']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $Sat = $user_building[$i]["Sat"];
            echo "\t" . "<td><input type='text' id='Sat_" . $i . "' size='2' maxlength='5' value='" . $Sat . "' onchange='update_page();'></td>" . "\n";

            echo "\t" . "<td>";
            echo "<select id='Sat_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['Sat_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_ENERGY']); ?></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='NRJ_" . $i . "' '>-</div></td>\n";
        }
        ?>
        <td>
            <div id="E_NRJ">-</div>
        </td>
    </tr>
    <!--
    Foreuse
    -->
    <tr>
        <th  colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_CRAWLER']); ?></th>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_CRAWLER']); ?></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $FOR = $user_building[$i]["FOR"];
            echo "\t" . "<td><input type='text' id='For_" . $i . "' size='2' maxlength='2' value='" . $FOR . "' onchange='update_page();'></td>" . "\n";
            echo "\t" . "<td>";
            echo "<select id='For_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['FOR_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='FOR_" . $i . "_conso' '>-</div></span></td>\n";
        }
        ?>
        <td>
            <div id="FOR_conso">-</div>
        </td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='FOR_" . $i . "_prod' '>-</div></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>


    <!--
    Métal
    -->

    <tr>
        <th  colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_METAL']); ?></th>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_LEVEL']); ?></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $M = $user_building[$i]["M"];
            echo "\t" . "<td><input type='text' id='M_" . $i . "' size='2' maxlength='2' value='" . $M . "' onchange='update_page();'></td>" . "\n";

            echo "\t" . "<td>";
            echo "<select id='M_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['M_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr style='font-style:italic;'>
        <th><?php echo($lang['HOME_SIMU_BOOSTERMETAL']); ?></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<td colspan='2'>";
            echo "<select id='M_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 30; $j >= 0; $j = $j - 10) {
                if (!isset($user_building[$i]["booster_tab"]['booster_m_val'])) {
                    $user_building[$i]["booster_tab"]['booster_m_val'] = 0;
                }
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_building[$i]["booster_tab"]['booster_m_val'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='M_" . $i . "_conso' '>-</div></td>\n";
        }
        ?>
        <td>
            <div id="M_conso">-</div>
        </td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='M_" . $i . "_prod' '>-</div></td>\n";
        }
        ?>
        <td>
            <div id="M_prod">-</div>
        </td>
    </tr>

    <!--
    Cristal
    -->

    <tr>
        <th  colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_CRYSTAL']); ?></th>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_LEVEL']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $C = $user_building[$i]["C"];
            echo "\t" . "<td><input type='text' id='C_" . $i . "' size='2' maxlength='2' value='" . $C . "' onchange='update_page();'></td>" . "\n";

            echo "\t" . "<td>";
            echo "<select id='C_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['C_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_BOOSTERCRYSTAL']); ?></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<td colspan='2'>";
            echo "<select id='C_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 30; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";

                if (!isset($user_building[$i]["booster_tab"]['booster_c_val'])) {
                    $user_building[$i]["booster_tab"]['booster_c_val'] = 0;
                }
                if ($user_building[$i]["booster_tab"]['booster_c_val'] == $j) echo " selected='selected'";

                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='C_" . $i . "_conso' '>-</div></td>\n";
        }
        ?>
        <td>
            <div id="C_conso">-</div>
        </td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='C_" . $i . "_prod' '>-</div></td>" . "\n";
        }
        ?>
        <td>
            <div id="C_prod">-</div>
        </td>
    </tr>

    <!--
    Deutérium
    -->

    <tr>
        <th  colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_DEUT']); ?></th>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_LEVEL']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $D = $user_building[$i]["D"];
            echo "\t" . "<td><input type='text' id='D_" . $i . "' size='2' maxlength='2' value='" . $D . "' onchange='update_page();'></td>" . "\n";

            echo "\t" . "<td>";
            echo "<select id='D_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['D_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_BOOSTERDEUT']); ?></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<td colspan='2'>";
            echo "<select id='D_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 30; $j >= 0; $j = $j - 10) {
                if (!isset($user_building[$i]["booster_tab"]['booster_d_val'])) {
                    $user_building[$i]["booster_tab"]['booster_d_val'] = 0;
                }
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_building[$i]["booster_tab"]['booster_d_val'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></td>" . "\n";
        }
        ?>
        <td></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='D_" . $i . "_conso' '>-</div></td>\n";
        }
        ?>
        <td>
            <div id="D_conso">-</div>
        </td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='D_" . $i . "_prod' '>-</div></td>\n";
        }
        ?>
        <td>
            <div id="D_prod">-</div>
        </td>
    </tr>
    <tr>
        <th
            colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_POINTSBYPLANET']); ?>
        </th>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_BUILDINGS']); ?></th>
        <?php
        $lab_max = 0;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='building_pts_" . $i . "' '>-</div>\n";
            echo "\t<input type='hidden' id='building_" . $i . "' value='" . implode(array_slice($user_building[$i], 21, -3, true), "<>") . "' /></td>";

            if ($lab_max < $user_building[$i]["Lab"]) {
                $lab_max = $user_building[$i]["Lab"];
            }
        }
        ?>
        <td><span id='total_b_pts'>-</span></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_DEFENCES']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='defence_pts_" . $i . "' '>-</div>\n";
            echo "\t<input type='hidden' id='defence_" . $i . "' value='" . implode($user_defence[$i], "<>") . "' /></td>";
        }
        ?>
        <td><span><span id='total_d_pts'>-</span></span></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_MOON']); ?></th>
        <?php
        for ($i = 201; $i <= 200 + $nb_planete; $i++) {
            echo "\t<td colspan='2'><div id='lune_pts_" . $i . "' '>-</div>\n";
            if ($user_building[$i]) {
                echo "\t<input type='hidden' id='lune_b_" . $i . "' value='" . implode(array_slice($user_building[$i], 23, -3, true), "<>") . "' />";
                //print_r($user_building[$i]);
            } else {
                echo "\t<input type='hidden' id='lune_b_" . $i . "' value='0' />";
            }
            echo "\t<input type='hidden' id='lune_d_" . $i . "' value='" . implode($user_defence[$i], "<>") . "' /></td>";
        }
        ?>
        <td><span><span id='total_lune_pts'>-</span></span></td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_SATS']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='sat_pts_" . $i . "' '>-</div>\n";
            echo "\t<input type='hidden' id='sat_lune_" . $i . "' value=" . ($user_building[$i + 100]["Sat"] != "" ? $user_building[$i + 100]["Sat"] : 0) . " /></td>";
        }
        ?>
        <td>
            <div id="total_sat_pts">-</div>
        </td>
    </tr>
    <tr>
        <th><?php echo($lang['HOME_SIMU_TECHNOLOGIES']); ?></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            if ($user_empire["technology"] != NULL && $user_building[$i]["Lab"] == $lab_max) {
                echo "\t<td colspan='2'><div id='techno_pts_" . $i . "' '>-</div>\n";
                echo "\t<input type='hidden' id='techno_" . $i . "' value='" . implode($user_empire["technology"], "<>") . "' /></td>";
            } else {
                echo "<td colspan='2'><span \">-</span></td>";
            }
        }
        ?>
        <td>-</td>
    </tr>
    <tr>
        <th><a><span"><?php echo($lang['HOME_SIMU_TOTALS']); ?></span></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<td colspan='2'><div id='total_pts_" . $i . "' >-</div></td>\n";
        }
        ?>
        <td><span id='total_pts'>-</span></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $name = $user_building[$i]["planet_name"];
            if ($name == "") {
                $name = "xxx";
            }

            echo "\t" . "<th class='c' colspan='2'><a>" . $name . "</a></th>" . "\n";
        }
        ?>
        <th class='c'><?php echo($lang['HOME_SIMU_TOTALS']); ?></th>
    </tr>
    </body>

</table>




<script>
    update_page();
</script>
