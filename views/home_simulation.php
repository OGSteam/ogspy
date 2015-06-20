<?php
/***************************************************************************
 *    filename    : home_simulation.php
 *    desc.        :
 *    Author        : Kyser - http://ogsteam.fr/
 *    created        : 19/12/2005
 *    modified    : 06/08/2006 11:40:18
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
$user_empire = user_get_empire();
$user_building = $user_empire["building"];
$user_defence = $user_empire["defence"];
if ($user_empire["technology"]) $user_technology = $user_empire["technology"];
else $user_technology = '0';

$nb_planete = find_nb_planete_user();

// Recuperation des pourcentages
$planet = array("planet_id" => "", "M_percentage" => 0, "C_percentage" => 0, "D_percentage" => 0, "CES_percentage" => 100, "CEF_percentage" => 100, "Sat_percentage" => 100);
$quet = $db->sql_query("SELECT planet_id, M_percentage, C_percentage, D_percentage, CES_percentage, CEF_percentage, Sat_percentage FROM " . TABLE_USER_BUILDING . " WHERE user_id = " . $user_data["user_id"] . " AND planet_id < 199 ORDER BY planet_id");
$user_percentage = array_fill(101, $nb_planete, $planet);
while ($row = $db->sql_fetch_assoc($quet)) {
    $arr = $row;
    unset($arr["planet_id"]);
    $user_percentage[$row["planet_id"]] = $arr;
}

// ajout infos pour gestion js ...
$officier = $user_data['off_commandant'] + $user_data['off_amiral'] + $user_data['off_ingenieur']
    + $user_data['off_geologue'] + $user_data['off_technocrate'];
$off_full = ($officier == 5) ? '1' : '0';
echo "<input type='hidden' id='vitesse_uni' size='2' maxlength='5' value='" . $server_config['speed_uni'] . "'/>";
echo "<input type='hidden' id='off_ingenieur' value='" . $user_data["off_ingenieur"] . "'/>";
echo "<input type='hidden' id='off_geologue' value='" . $user_data["off_geologue"] . "'/>";
echo "<input type='hidden' id='off_full' value='" . $off_full . "'/>";

//Calcul et correction boosters :
for ($i = 101; $i <= $nb_planete + 100; $i++) {
    /*Boosters et extensions modification :*/
    $booster_tab[$i] = booster_decode($user_building[$i]["boosters"]);
    $user_building[$i]["fields"] += $booster_tab[$i]['extention_p'];
}
?>

<script src="js/ogame_formula.js" type="text/javascript"></script>

<table id="simu" width="100%" title="<?php echo $nb_planete; ?>">
    <tr>
        <td class="c"></td>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $name = $user_building[$i]["planet_name"];
            if ($name == "") $name = "xxx";

            echo "\t" . "<td class='c' colspan='2'><a>" . $name . "</a></td>" . "\n";
        }
        ?>
        <td class="c">Totaux</td>
    </tr>
    <tr>
        <th><a>Coordonnées</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $coordinates = $user_building[$i]["coordinates"];
            if ($coordinates == "") $coordinates = "&nbsp;";
            else $coordinates = "[" . $coordinates . "]";

            echo "\t" . "<th colspan='2'>" . $coordinates . "</th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a>Cases</a></th>
        <?php
        $sum_field = 0;
        $sum_filed_used = 0;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $fields = $user_building[$i]["fields"];
            if ($fields == "0") $fields = "?";
            $fields_used = $user_building[$i]["fields_used"];
            if ($fields_used >= 0) {
                $fields = $fields_used . " / " . $fields;
            } else $fields = "&nbsp;";

            echo "\t" . "<th colspan='2'>" . $fields . "</th>" . "\n";

            if (is_numeric($user_building[$i]["fields"])) $sum_field += $user_building[$i]["fields"];
            if (is_numeric($user_building[$i]["fields_used"])) $sum_filed_used += $user_building[$i]["fields_used"];
        }
        echo "\t<th><div id='T_cases'>" . $sum_filed_used . "/" . $sum_field . "</div></th>";
        ?>
    </tr>
    <tr>
        <th><a>Température Min.</a></th>
        <?php
        $t_min = $user_building[101]["temperature_min"];
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $temperature_min = $user_building[$i]["temperature_min"];
            if ($temperature_min == "") $temperature_min = "&nbsp;";
            echo "\t" . "<th colspan='2'>" . $temperature_min . "<input id='temperature_min_" . $i . "' type='hidden' value='" . $temperature_min . "'></th>" . "\n";

            if (is_numeric($user_building[$i]["temperature_min"]) && $user_building[$i]["temperature_min"] < $t_min)
                $t_min = $user_building[$i]["temperature_min"];
        }
        echo "\t<th><div id='T_min'>" . $t_min . "</div></th>";
        ?>
    </tr>
    <tr>
        <th><a>Température Max.</a></th>
        <?php
        $t_max = $user_building[101]["temperature_max"];
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $temperature_max = $user_building[$i]["temperature_max"];
            if ($temperature_max == "") $temperature_max = "&nbsp;";
            echo "\t" . "<th colspan='2'>" . $temperature_max . "<input id='temperature_max_" . $i . "' type='hidden' value='" . $temperature_max . "'></th>" . "\n";

            if (is_numeric($user_building[$i]["temperature_max"]) && $user_building[$i]["temperature_max"] > $t_max)
                $t_max = $user_building[$i]["temperature_max"];
        }
        echo "\t<th><div id='T_max'>" . $t_max . "</div></th>";
        ?>
    </tr>
    <tr style='font-style:italic;'>
        <th><a>Extension</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $booster = "&nbsp;";

            $booster = $booster_tab[$i]['extention_p']; // La vue Lune n'existe pas sur la page simulation

            echo "\t" . "<th colspan='2'>" . $booster . "<input id='extension" . $i . "' type='hidden' value='" . $booster . "'></th>" . "</th>" . "\n";
        }
        ?>
    </tr>

    <!--
    Energie
    -->

    <tr>
        <td class="c">Énergies</td>
        <td class="c" colspan="4">Technologie Énergie <input type="text" id="NRJ" size="2" maxlength="2"
                                                             value="<?php print $user_technology['NRJ'] ?>"
                                                             onchange='update_page();'></td>
        <td class="c" colspan="4">Technologie Plasma <input type="text" id="Plasma" size="2" maxlength="2"
                                                            value="<?php print $user_technology['Plasma'] ?>"
                                                            onchange='update_page();'></td>
        <td class="c" colspan="2">Officier ingénieur <input type='checkbox'
                                                            id='c_off_ingenieur' <?php print ($user_data["off_ingenieur"] == 1) ? 'checked="checked"' : '' ?>
                                                            onClick='javascript:update_page();'>
        <td class="c" colspan="2">Officier géologue <input type='checkbox'
                                                           id='c_off_geologue' <?php print ($user_data["off_geologue"] == 1) ? 'checked="checked"' : '' ?>
                                                           onClick='javascript:update_page();'>
        <td class="c" colspan="2">Full officier <input type='checkbox'
                                                       id='c_off_full' <?php print ($off_full == 1) ? 'checked="checked"' : '' ?>
                                                       onClick='javascript:update_page();'>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1) - 8; ?>">&nbsp;</td>
    </tr>
    <tr>
        <th><a>CES</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $CES = $user_building[$i]["CES"];
            echo "\t" . "<th><input type='text' id='CES_" . $i . "' size='2' maxlength='2' value='" . $CES . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='CES_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['CES_percentage'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a>CEF</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $CEF = $user_building[$i]["CEF"];
            echo "\t" . "<th><input type='text' id='CEF_" . $i . "' size='2' maxlength='2' value='" . $CEF . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='CEF_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['CEF_percentage'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a>Satellites</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $Sat = $user_building[$i]["Sat"];
            echo "\t" . "<th><input type='text' id='Sat_" . $i . "' size='2' maxlength='5' value='" . $Sat . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='Sat_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['Sat_percentage'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a>Energie</a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='NRJ_" . $i . "'>-</div></font></th>" . "\n";
        }
        ?>
        <th>
            <div id="E_NRJ">-</div>
        </th>
    </tr>

    <!--
    Métal
    -->

    <tr>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>">Métal</td>
    </tr>
    <tr>
        <th><a>Niveau</a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $M = $user_building[$i]["M"];
            echo "\t" . "<th><input type='text' id='M_" . $i . "' size='2' maxlength='2' value='" . $M . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='M_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['M_percentage'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr style='font-style:italic;'>
        <th><a>Booster métal</a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'>";
            echo "<select id='M_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 30; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($booster_tab[$i]['booster_m_val'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a>Consommation Energie</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='M_" . $i . "_conso'>-</div></font></th>" . "\n";
        }
        ?>
        <th>
            <div id="M_conso">-</div>
        </th>
    </tr>
    <tr>
        <th><a>Production</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='M_" . $i . "_prod'>-</div></font></th>" . "\n";
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
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>">Cristal</td>
    </tr>
    <tr>
        <th><a>Niveau</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $C = $user_building[$i]["C"];
            echo "\t" . "<th><input type='text' id='C_" . $i . "' size='2' maxlength='2' value='" . $C . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='C_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['C_percentage'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr style='font-style:italic;'>
        <th><a>Booster cristal</a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'>";
            echo "<select id='C_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 30; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($booster_tab[$i]['booster_c_val'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a>Consommation Energie</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='C_" . $i . "_conso'>-</div></font></th>" . "\n";
        }
        ?>
        <th>
            <div id="C_conso">-</div>
        </th>
    </tr>
    <tr>
        <th><a>Production</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='C_" . $i . "_prod'>-</div></font></th>" . "\n";
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
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>">Deutérium</td>
    </tr>
    <tr>
        <th><a>Niveau</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $D = $user_building[$i]["D"];
            echo "\t" . "<th><input type='text' id='D_" . $i . "' size='2' maxlength='2' value='" . $D . "' onchange='update_page();'></th>" . "\n";

            echo "\t" . "<th>";
            echo "<select id='D_" . $i . "_percentage' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 100; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($user_percentage[$i]['D_percentage'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr style='font-style:italic;'>
        <th><a>Booster deutérium</a></th>
        <?php

        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'>";
            echo "<select id='D_" . $i . "_booster' onchange='update_page();' onKeyUp='update_page();'>" . "\n";
            for ($j = 30; $j >= 0; $j = $j - 10) {
                echo "\t\t" . "<option value='" . $j . "'";
                if ($booster_tab[$i]['booster_d_val'] == $j) echo " selected='selected'";
                echo ">" . $j . "%</option>" . "\n";
            }
            echo "</select></th>" . "\n";
        }
        ?>
        <th></th>
    </tr>
    <tr>
        <th><a>Consommation Energie</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='D_" . $i . "_conso'>-</div></font></th>" . "\n";
        }
        ?>
        <th>
            <div id="D_conso">-</div>
        </th>
    </tr>
    <tr>
        <th><a>Production</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='D_" . $i . "_prod'>-</div></font></th>" . "\n";
        }
        ?>
        <th>
            <div id="D_prod">-</div>
        </th>
    </tr>
    <tr>
        <td class="c" colspan="<?php echo 2 * ($nb_planete + 1); ?>">Poids en points de chaque planète</td>
    </tr>
    <tr>
        <th><a>Bâtiments</a></th>
        <?php
        $lab_max = 0;
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='building_pts_" . $i . "'>-</div></font>" . "\n";
            echo "\t<input type='hidden' id='building_" . $i . "' value='" . implode(array_slice($user_building[$i], 12, -2), "<>") . "' /></th>";
            if ($lab_max < $user_building[$i]["Lab"]) $lab_max = $user_building[$i]["Lab"];
        }
        ?>
        <th><font color='white'><span id='total_b_pts'>-</span></font></th>
    </tr>
    <tr>
        <th><a>Défenses</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t<th colspan='2'><font color='lime'><div id='defence_pts_" . $i . "'>-</div></font>" . "\n";
            echo "\t<input type='hidden' id='defence_" . $i . "' value='" . implode($user_defence[$i], "<>") . "' /></th>";
        }
        ?>
        <th><font color='white'><span id='total_d_pts'>-</span></font></th>
    </tr>
    <tr>
        <th><a>Lunes</a></th>
        <?php
        for ($i = 201; $i <= 200 + $nb_planete; $i++) {
            echo "\t<th colspan='2'><font color='lime'><div id='lune_pts_" . $i . "'>-</div></font>" . "\n";
            if ($user_building[$i]) {
                echo "\t<input type='hidden' id='lune_b_" . $i . "' value='" . implode(array_slice($user_building[$i], 12, -2, true), "<>") . "' />";
            } else {
                echo "\t<input type='hidden' id='lune_b_" . $i100 . "' value='0' />";
            }
            echo "\t<input type='hidden' id='lune_d_" . $i . "' value='" . implode($user_defence[$i], "<>") . "' /></th>";
        }
        ?>
        <th><font color='white'><span id='total_lune_pts'>-</span></font></th>
    </tr>
    <tr>
        <th><a>Satellites</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='lime'><div id='sat_pts_" . $i . "'>-</div></font>" . "\n";
            echo "\t<input type='hidden' id='sat_lune_" . $i . "' value=" . ($user_building[$i + 100]["Sat"] != "" ? $user_building[$i + 100]["Sat"] : 0) . " /></th>";
        }
        ?>
        <th>
            <div id="total_sat_pts">-</div>
        </th>
    </tr>
    <tr>
        <th><a>Technologies</a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            if ($user_empire["technology"] != NULL && $user_building[$i]["Lab"] == $lab_max) {
                echo "\t" . "<th colspan='2'><font color='lime'><div id='techno_pts'>-</div></font>" . "\n";
                echo "\t<input type='hidden' id='techno' value='" . implode($user_empire["technology"], "<>") . "' /></th>";
            } else echo "<th colspan='2'><font color='lime'>-</font></th>";
        }
        ?>
        <th>-</th>
    </tr>
    <tr>
        <th><a><font color='yellow'>Totaux</font></a></th>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            echo "\t" . "<th colspan='2'><font color='white'><div id='total_pts_" . $i . "'>-</div></font></th>" . "\n";
        }
        ?>
        <th><font color='white'><span id='total_pts'>-</span></font></th>
    </tr>
    <tr>
        <td class="c">&nbsp;</td>
        <?php
        for ($i = 101; $i <= $nb_planete + 100; $i++) {
            $name = $user_building[$i]["planet_name"];
            if ($name == "") $name = "xxx";

            echo "\t" . "<td class='c' colspan='2'><a>" . $name . "</a></td>" . "\n";
        }
        ?>
        <td class='c'>Totaux</td>
    </tr>
</table>

<script type="text/javascript">
    update_page();
</script>