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


if ($user_empire["technology"])
{
    $user_technology = $user_empire["technology"];
}
else {
    $user_technology['NRJ'] = 0;
    $user_technology['Plasma'] = 0;
}


$nb_planete = find_nb_planete_user($user_data['user_id']);


// ajout infos pour gestion js ...
$officier = $user_data['off_commandant'] + $user_data['off_amiral'] + $user_data['off_ingenieur']
    + $user_data['off_geologue'] + $user_data['off_technocrate'];
$off_full = ($officier == 5) ? '1' : '0';
$class_collect = ($user_data['user_class'] === 'COL') ? '1' : '0';
echo "<input type='hidden' id='vitesse_uni' size='2' maxlength='5' value='" . $server_config['speed_uni'] . "'/>";
echo "<input type='hidden' id='off_ingenieur' value='" . $user_data["off_ingenieur"] . "'/>";
echo "<input type='hidden' id='off_geologue' value='" . $user_data["off_geologue"] . "'/>";
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

<table id="simu" width="100%" title="<?php echo $nb_planete; ?>">
    <tr>
        <td class="c"></td>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $name = $user_building[$i]["planet_name"];
            if ($name == "") {
                $name = "xxx";
            }

            echo "\t" . "<td class='c' colspan='2'><a>" . $name . "</a></td>" . "\n";
        }
        ?>
        <td class="c"><?php echo($lang['HOME_SIMU_TOTALS']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_COORD']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $coordinates = $user_building[$i]["coordinates"];
            $position = find_planet_position($coordinates);
            if ($coordinates == "") {
                $coordinates = "&nbsp;";
            } else {
                $coordinates = "[" . $coordinates . "]";
            }

            echo "\t" . "<th colspan='2'>" . $coordinates . "<input id='position_" . $i . "' type='hidden' value='" . $position . "'></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_FIELDS']); ?></a></th>
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

            echo "\t" . "<th colspan='2'>" . $fields . "</th>" . "\n";

            if (is_numeric($user_building[$i]["fields"])) {
                $sum_field += $user_building[$i]["fields"];
            }
            if (is_numeric($user_building[$i]["fields_used"])) {
                $sum_filed_used += $user_building[$i]["fields_used"];
            }
        }
        echo "\t<th><div id='T_cases'>" . $sum_filed_used . " / " . $sum_field . "</div></th>";
        ?>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_MINTEMP']); ?></a></th>
        <?php
        $t_min = +INF;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $temperature_min = $user_building[$i]["temperature_min"];
            if ($temperature_min == "") {
                $temperature_min = "&nbsp;";
            }
            echo "\t" . "<th colspan='2'>" . $temperature_min . "<input id='temperature_min_" . $i . "' type='hidden' value='" . $temperature_min . "'></th>" . "\n";

            if (is_numeric($user_building[$i]["temperature_min"]) && $user_building[$i]["temperature_min"] < $t_min) {
                            $t_min = $user_building[$i]["temperature_min"];
            }
        }
        echo "\t<th><div id='T_min'>" . $t_min . "</div></th>";
        ?>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_MAXTEMP']); ?></a></th>
        <?php
        $t_max = -INF;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $temperature_max = $user_building[$i]["temperature_max"];
            if ($temperature_max == "") {
                $temperature_max = "&nbsp;";
            }
            echo "\t" . "<th colspan='2'>" . $temperature_max . "<input id='temperature_max_" . $i . "' type='hidden' value='" . $temperature_max . "'></th>" . "\n";

            if (is_numeric($user_building[$i]["temperature_max"]) && $user_building[$i]["temperature_max"] > $t_max) {
                            $t_max = $user_building[$i]["temperature_max"];
            }
        }
        echo "\t<th><div id='T_max'>" . $t_max . "</div></th>";
        ?>
    </tr>
    <tr style='font-style:italic;'>
        <th><a><?php echo($lang['HOME_SIMU_EXTENSION']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $booster = "0";

            if (isset($user_building[$i]["booster_tab"]['extention_p']))
            {
                $booster = $user_building[$i]["booster_tab"]['extention_p']; // La vue Lune n'existe pas sur la page simulation
            }


            echo "\t" . "<th colspan='2'>" . $booster . "<input id='extension" . $i . "' type='hidden' value='" . $booster . "'></th>\n";
        }
        ?>
    </tr>

    <!--
    Energie
    -->

    <tr>
        <td class="c"><?php echo($lang['HOME_SIMU_ENERGYS']); ?></td>
        <td class="c" colspan="1"><?php echo($lang['HOME_SIMU_TECH_ENERGY']); ?> <input type="text" id="NRJ" size="2" maxlength="2"
                                                                                        value="<?php print $user_technology['NRJ'] ?>"
                                                                                        onchange='update_page();'></td>
        <td class="c" colspan="1"><?php echo($lang['HOME_SIMU_TECH_PLASMA']); ?> <input type="text" id="Plasma" size="2" maxlength="2"
                                                                                        value="<?php print $user_technology['Plasma'] ?>"
                                                                                        onchange='update_page();'></td>
        <td class="c" colspan="1"><?php echo($lang['HOME_SIMU_OFF_INGE']); ?> <input type='checkbox'
                                                                                     id='c_off_ingenieur' <?php print ($user_data["off_ingenieur"] == 1) ? 'checked="checked"' : '' ?>
                                                                                     onClick='update_page();'>
        <td class="c" colspan="1"><?php echo($lang['HOME_SIMU_OFF_GEO']); ?> <input type='checkbox'
                                                                                    id='c_off_geologue' <?php print ($user_data["off_geologue"] == 1) ? 'checked="checked"' : '' ?>
                                                                                    onClick='update_page();'>
        <td class="c" colspan="1"><?php echo($lang['HOME_SIMU_OFF_FULL']); ?> <input type='checkbox'
                                                                                     id='c_off_full' <?php print ($off_full == 1) ? 'checked="checked"' : '' ?>
                                                                                     onClick='update_page();'>
        <td class="c" colspan="1"><?php echo($lang['HOME_SIMU_CLASS_COLLECT']); ?> <input type='checkbox'
                                                                                     id='c_class_collect' <?php print ($class_collect == 1) ? 'checked="checked"' : '' ?>
                                                                                     onClick='update_page();'>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1) - 6; ?>">&nbsp;</td>
    </tr>

    <tr>
        <th><a><?php echo($lang['HOME_SIMU_SOLARPLANT_SHORT']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $CES = $user_building[$i]["CES"];
            echo "\t" . "<th><input type='text' id='CES_" . $i . "' size='2' maxlength='2' value='" . $CES . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='CES_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['CES_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_FUSIONPLANT_SHORT']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $CEF = $user_building[$i]["CEF"];
            echo "\t" . "<th><input type='text' id='CEF_" . $i . "' size='2' maxlength='2' value='" . $CEF . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='CEF_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['CEF_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_SATELLITES']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $Sat = $user_building[$i]["Sat"];
            echo "\t" . "<th><input type='text' id='Sat_" . $i . "' size='5' maxlength='5' value='" . $Sat . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='Sat_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['Sat_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr style='font-style:italic;'>
        <th><a><?php echo($lang['HOME_SIMU_BOOSTERNRJ']); ?></a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'>";
            echo "<select id='E_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 80; $j >= 0; $j = $j - 20) {
                if (!isset($user_building[$i]["booster_tab"]['booster_e_val']))
                {
                    $user_building[$i]["booster_tab"]['booster_e_val'] = 0;
                }
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_building[$i]["booster_tab"]['booster_e_val'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_ENERGY']); ?></a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='NRJ_" . $i . "'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="E_NRJ">-</div>
        </th>
    </tr>
    <!--
    Foreuse
    -->
    <tr>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_CRAWLER']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_CRAWLER']); ?></a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $FOR = $user_building[$i]["FOR"];
            echo "\t" . "<th><input type='text' id='For_" . $i . "' size='4' maxlength='4' value='" . $FOR . "' onchange='update_page();'>";
            echo "<span style=\"color:lime;\"> / <div id='FOR_" . $i . "_max'>-</div></span></th>" . "\n";
            echo "\t" . "<th>";
            echo "<select id='For_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 150; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['FOR_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='FOR_" . $i . "_conso'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="FOR_conso">-</div>
        </th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='FOR_" . $i . "_prod'>-</div></span></th>" . "\n";
        }
        ?>
    </tr>

    <!--
    Métal
    -->

    <tr>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_METAL']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_LEVEL']); ?></a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $M = $user_building[$i]["M"];
            echo "\t" . "<th><input type='text' id='M_" . $i . "' size='2' maxlength='2' value='" . $M . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='M_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['M_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr style='font-style:italic;'>
        <th><a><?php echo($lang['HOME_SIMU_BOOSTERMETAL']); ?></a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'>";
            echo "<select id='M_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 40; $j >= 0; $j = $j - 10) {
                if (!isset($user_building[$i]["booster_tab"]['booster_m_val']))
                {
                    $user_building[$i]["booster_tab"]['booster_m_val'] = 0;
                }
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_building[$i]["booster_tab"]['booster_m_val'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='M_" . $i . "_conso'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="M_conso">-</div>
        </th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='M_" . $i . "_prod'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="M_prod">-</div>
        </th>
    </tr>

    <!--
    Cristal
    -->

    <tr>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_CRYSTAL']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_LEVEL']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $C = $user_building[$i]["C"];
            echo "\t" . "<th><input type='text' id='C_" . $i . "' size='2' maxlength='2' value='" . $C . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='C_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['C_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr style='font-style:italic;'>
        <th><a><?php echo($lang['HOME_SIMU_BOOSTERCRYSTAL']); ?></a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'>";
            echo "<select id='C_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 40; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";

                if (!isset($user_building[$i]["booster_tab"]['booster_c_val']))
                {
                    $user_building[$i]["booster_tab"]['booster_c_val'] = 0;
                }
                if ($user_building[$i]["booster_tab"]['booster_c_val'] == $j) echo " selected='selected'";

                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='C_" . $i . "_conso'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="C_conso">-</div>
        </th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='C_" . $i . "_prod'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="C_prod">-</div>
        </th>
    </tr>

    <!--
    Deutérium
    -->

    <tr>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_DEUT']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_LEVEL']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $D = $user_building[$i]["D"];
            echo "\t" . "<th><input type='text' id='D_" . $i . "' size='2' maxlength='2' value='" . $D . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='D_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['D_percentage'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr style='font-style:italic;'>
        <th><a><?php echo($lang['HOME_SIMU_BOOSTERDEUT']); ?></a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'>";
            echo "<select id='D_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 40; $j >= 0; $j = $j - 10) {
                if (!isset($user_building[$i]["booster_tab"]['booster_d_val']))
                {
                    $user_building[$i]["booster_tab"]['booster_d_val'] = 0;
                }
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_building[$i]["booster_tab"]['booster_d_val'] == $j) {
                    echo " selected='selected'";
                }
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_ENERGY_USAGE']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='D_" . $i . "_conso'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="D_conso">-</div>
        </th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_PRODUCTION']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='D_" . $i . "_prod'>-</div></span></th>" . "\n";
        }
        ?>
        <th>
            <div id="D_prod">-</div>
        </th>
    </tr>
    <tr>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>"><?php echo($lang['HOME_SIMU_POINTSBYPLANET']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_BUILDINGS']); ?></a></th>
        <?php
        $lab_max = 0;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='building_pts_" . $i . "'>-</div></span>" . "\n";
            echo "\t<input type='hidden' id='building_" . $i . "' value='" . implode(array_slice($user_building[$i], 21, -3, true), "<>") . "' /></th>";

            if ($lab_max < $user_building[$i]["Lab"]) {
                $lab_max = $user_building[$i]["Lab"];
            }
        }
        ?>
        <th><span style="color: white; "><span id='total_b_pts'>-</span></span></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_DEFENCES']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<th colspan='2'><span style=\"color: lime; \"><div id='defence_pts_" . $i . "'>-</div></span>" . "\n";
            echo "\t<input type='hidden' id='defence_" . $i . "' value='" . implode($user_defence[$i], "<>") . "' /></th>";
        }
        ?>
        <th><span style="color: white; "><span id='total_d_pts'>-</span></span></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_MOON']); ?></a></th>
        <?php
        for ($i = 201; $i <= 200 + $nb_planete; $i++) {
            echo "\t<th colspan='2'><span style=\"color: lime; \"><div id='lune_pts_" . $i . "'>-</div></span>" . "\n";
            if ($user_building[$i]) {
                echo "\t<input type='hidden' id='lune_b_" . $i . "' value='" . implode(array_slice($user_building[$i], 23, -3, true), "<>") . "' />";
                //print_r($user_building[$i]);
            } else {
                echo "\t<input type='hidden' id='lune_b_" . $i . "' value='0' />";
            }
            echo "\t<input type='hidden' id='lune_d_" . $i . "' value='" . implode($user_defence[$i], "<>") . "' /></th>";
        }
        ?>
        <th><span style="color: white; "><span id='total_lune_pts'>-</span></span></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_SATS']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='sat_pts_" . $i . "'>-</div></span>" . "\n";
            echo "\t<input type='hidden' id='sat_lune_" . $i . "' value=" . ($user_building[$i + 100]["Sat"] != "" ? $user_building[$i + 100]["Sat"] : 0) . " /></th>";
        }
        ?>
        <th>
            <div id="total_sat_pts">-</div>
        </th>
    </tr>
    <tr>
        <th><a><?php echo($lang['HOME_SIMU_TECHNOLOGIES']); ?></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            if ($user_empire["technology"] != NULL && $user_building[$i]["Lab"] == $lab_max) {
                echo "\t" . "<th colspan='2'><span style=\"color:lime;\"><div id='techno_pts'>-</div></span>" . "\n";
                echo "\t<input type='hidden' id='techno' value='" . implode($user_empire["technology"], "<>") . "' /></th>";
            } else {
                echo "<th colspan='2'><span style=\"color: lime; \">-</span></th>";
            }
        }
        ?>
        <th>-</th>
    </tr>
    <tr>
        <th><a><span style="color: yellow; "><?php echo($lang['HOME_SIMU_TOTALS']); ?></span></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><span style=\"color:white;\"><div id='total_pts_" . $i . "'>-</div></span></th>" . "\n";
        }
        ?>
        <th><span style="color: white; "><span id='total_pts'>-</span></span></th>
    </tr>
    <tr>
        <td class="c">&nbsp;</td>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $name = $user_building[$i]["planet_name"];
            if ($name == "") {
                $name = "xxx";
            }

            echo "\t" . "<td class='c' colspan='2'><a>" . $name . "</a></td>" . "\n";
        }
        ?>
        <td class='c'><?php echo($lang['HOME_SIMU_TOTALS']); ?></td>
    </tr>
</table>

<script type="text/javascript">
    update_page();
</script>
