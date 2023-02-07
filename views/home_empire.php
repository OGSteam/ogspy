<?php

/**
 * Affichage Empire - Page Planetes
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("includes/ogame.php");

global $server_config, $console;

$user_empire = user_get_empire($user_data['user_id']);

$user_building   = $user_empire['building'];
$user_defence    = $user_empire['defence'];
$user_technology = $user_empire['technology'];
$nb_planete      = find_nb_planete_user($user_data['user_id']);


if (!isset($pub_view)) {
    $pub_view = 'planets';
}
switch ($pub_view) {
    case 'moons':
        $view = $pub_view;
        $start = 201;
        break;
    case 'planets':    //no break
    default:
        $view = 'planets';
        $start = 101;
}

/* Restes du Lang Empire :-) */
$technology_requirement["Esp"] = array(3);
$technology_requirement["Ordi"] = array(1);
$technology_requirement["Armes"] = array(4);
$technology_requirement["Bouclier"] = array(6, "NRJ" => 3);
$technology_requirement["Protection"] = array(2);
$technology_requirement["NRJ"] = array(1);
$technology_requirement["Hyp"] = array(1, "NRJ" => 3, "Bouclier" => 5);
$technology_requirement["RC"] = array(1, "NRJ" => 1);
$technology_requirement["RI"] = array(2, "NRJ" => 1);
$technology_requirement["PH"] = array(7, "HYP" => 3);
$technology_requirement["Laser"] = array(1, "NRJ" => 2);
$technology_requirement["Ions"] = array(4, "Laser" => 5, "NRJ" => 4);
$technology_requirement["Plasma"] = array(4, "NRJ" => 8, "Laser" => 10, "Ions" => 5);
$technology_requirement["RRI"] = array(10, "Ordi" => 8, "Hyp" => 8);
$technology_requirement["Graviton"] = array(12);
$technology_requirement["Astrophysique"] = array(3, "Esp" => 4, "RI" => 3);

$name = $coordinates = $fields = $temperature_min = $temperature_max = $satellite = "";
//Planète
for ($i = 101; $i <= $nb_planete + 100; $i++) {
    $name            .= "'" . $user_building[$i]['planet_name'] . "', ";
    $coordinates     .= "'" . $user_building[$i]['coordinates'] . "', ";
    $fields          .= "'" . $user_building[$i]['fields'] . "', ";
    $temperature_min .= "'" . $user_building[$i]['temperature_min'] . "', ";
    $temperature_max .= "'" . $user_building[$i]['temperature_max'] . "', ";
    $satellite       .= "'" . $user_building[$i]['Sat'] . "', ";
}

for ($i = 201; $i <= $nb_planete + 200; $i++) {
    $name            .= "'" . $user_building[$i]['planet_name'] . " (lune)', ";
    $coordinates     .= "'" . $user_building[$i]['coordinates'] . "', ";
    $fields          .= "'" . $user_building[$i]['fields'] . "', ";
    $temperature_min .= "'" . $user_building[$i]["temperature_min"] . "', ";
    $temperature_max .= "'" . $user_building[$i]["temperature_max"] . "', ";
    $satellite       .= "'" . $user_building[$i]["Sat"] . "', ";
}
?>
<table width="100%">
    <tr>
        <?php
        $colspan = ($nb_planete + 1) / 2;
        $colspan_planete = floor($colspan);
        $colspan_lune = ceil($colspan);

        if ($view == 'planets') {
            echo "<th colspan='$colspan_planete'><a>" . $lang['HOME_EMPIRE_PLANET'] . "</a></th>\n";
            echo "<td class='c' align='center' colspan='$colspan_lune' onClick=\"window.location = 'index.php?action=home&amp;view=moons';\"><a style='cursor:pointer'><font color='lime'>" . $lang['HOME_EMPIRE_MOON'] . "</font></a></td>\n";
        } else {
            echo "<th colspan='$colspan_lune'><a>" . $lang['HOME_EMPIRE_MOON'] . "</a></th>\n";
            echo "<td class='c' align='center' colspan='$colspan_planete' onClick=\"window.location = 'index.php?action=home&amp;view=planets';\"><a style='cursor:pointer'><font color='lime'>" . $lang['HOME_EMPIRE_PLANET'] . "</font></a></td>\n";
        }
        ?>
    </tr>
    <?php
    // vérification de compte de planete/lune avec la technologie astro
    if (!isset($user_technology['Astrophysique']) || $user_technology['Astrophysique'] == '') {
        $user_technology['Astrophysique'] = 0;
    }
    $astro = astro_max_planete($user_technology['Astrophysique']);

    if (((find_nb_planete_user($user_data['user_id']) > $astro) || (find_nb_moon_user($user_data['user_id']) > $astro)) && ($user_technology != false)) {
        echo '<tr>';
        echo '<td class="c" colspan="' . ($nb_planete < 10 ? '10' : $nb_planete + 1) . '">';
        echo $lang['HOME_EMPIRE_ERROR'] . ' ';
        echo (find_nb_planete_user($user_data['user_id']) > $astro) ? $lang['HOME_EMPIRE_ERROR_PLANET'] . '<br>' : '';
        echo (find_nb_moon_user($user_data['user_id']) > $astro) ? $lang['HOME_EMPIRE_ERROR_MOON'] . '<br>' : '';
        echo "</td></tr>\n";
    }
    ?>
    <tr>
        <td class="c" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>"><?php echo ($lang['HOME_EMPIRE_SUMMARY']); ?></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {

            $user_production[$i] = ogame_production_planet($user_building[$i], $user_technology, $user_data, $server_config);
            echo "<th>";
            if (!isset($pub_view) || $pub_view == "planets") {
                // echo "<input type='image' title='" . $lang['HOME_EMPIRE_MOVELEFT'] . " " . $user_building[$i]["planet_name"] . "' src='images/previous.png' onclick=\"window.location = 'index.php?action=move_planet&amp;planet_id=" . $i . "&amp;view=" . $view . "&amp;left';\">&nbsp;&nbsp;";
                echo "<input type='image' title='" . $lang['HOME_EMPIRE_MOVELEFT'] . " " . $user_building[$i]["planet_name"] . "' src='images/previous.png'>&nbsp;&nbsp;";
                echo "<input type='image' title='" . $lang['HOME_EMPIRE_DELETE_PLANET'] . " " . $user_building[$i]["planet_name"] . "' src='images/drop.png' onclick=\"window.location = 'index.php?action=del_planet&amp;planet_id=" . $i . "&amp;view=" . $view . "';\">&nbsp;&nbsp;";
                // echo "<input type='image' title='" . $lang['HOME_EMPIRE_MOVERIGHT'] . " " . $user_building[$i]["planet_name"] . "' src='images/next.png' onclick=\"window.location = 'index.php?action=move_planet&amp;planet_id=" . $i . "&amp;view=" . $view . "&amp;right';\">";
                echo "<input type='image' title='" . $lang['HOME_EMPIRE_MOVERIGHT'] . " " . $user_building[$i]["planet_name"] . "' src='images/next.png'>";
            } else {
                echo "<input type='image' title='" . $lang['HOME_EMPIRE_DELETE_MOON'] . " " . $user_building[$i]["planet_name"] . "' src='images/drop.png' onclick=\"window.location = 'index.php?action=del_planet&amp;planet_id=" . $i . "&amp;view=" . $view . "';\">&nbsp;&nbsp;";
            }
            echo "</th>\n";
        }
        $console->log($user_production);
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_NAME']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $name = $user_building[$i]["planet_name"];
            if ($name == "") {
                $name = "&nbsp;";
            }
            echo "\t" . "<th><a>" . $name . "</a></th>" . "\n";
        }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_COORD']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $coordinates = $user_building[$i]["coordinates"];
            if ($coordinates == "" || ($user_building[$i]["planet_name"] == "" && $view == "moons")) {
                $coordinates = "&nbsp;";
            } else {
                $coordinates = "[" . $coordinates . "]";
            }

            echo "\t" . "<th>" . $coordinates . "</th>" . "\n";
        }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_FIELDS']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $fields = $user_building[$i]["fields"];
            if ($fields == "0") {
                $fields = 0;
            }
            $fields_used = $user_building[$i]["fields_used"];

            echo "\t" . "<th>" . $fields_used . " / " . ($fields != 0 ? $fields : "") . "</th>" . "\n";
        }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_MINTEMP']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $temperature_min = $user_building[$i]["temperature_min"];
            if ($temperature_min == "") {
                $temperature_min = "&nbsp;";
            }

            echo "\t" . "<th>" . $temperature_min . "</th>" . "\n";
        }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_MAXTEMP']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $temperature_max = $user_building[$i]["temperature_max"];
            if ($temperature_max == "") {
                $temperature_max = "&nbsp;";
            }

            echo "\t" . "<th>" . $temperature_max . "</th>" . "\n";
        }
        ?>
    </tr>
    <tr style='font-style:italic;'>
        <th><a><?php echo ($lang['HOME_EMPIRE_EXTENSION']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $booster = "&nbsp;";
            $booster_tab = booster_decode($user_building[$i]["boosters"]);

            if ($view == "planets") {
                $booster = $booster_tab['extention_p'];
            } else {
                $booster = $booster_tab['extention_m'];
            }

            echo "\t" . "<th>" . $booster . "</th>" . "\n";
        }

        if ($view == "planets") {
        ?>
    </tr>
    <tr>
        <td class="c" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>"><?php echo ($lang['HOME_EMPIRE_PRODUCTION_EXPECTED']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_METAL']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $M = $user_building[$i]['M'];
                if ($M != "") {
                    echo "\t" . "<th>" . $user_production[$i]['prod_theorique']['M'] . "</th>" . "\n";
                } else {
                    echo "\t" . "<th>&nbsp;</th>" . "\n";
                }
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_CRYSTAL']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $C = $user_building[$i]['C'];
                if ($C != "") {
                    echo "\t" . "<th>" . $user_production[$i]['prod_theorique']['C'] . "</th>" . "\n";
                } else {
                    echo "\t" . "<th>&nbsp;</th>" . "\n";
                }
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_DEUT']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $D = $user_building[$i]['D'];
                if ($D != "") {
                    echo "\t" . "<th>" . $user_production[$i]['prod_theorique']['D'] . "</th>" . "\n";
                } else {
                    echo "\t" . "<th>&nbsp;</th>" . "\n";
                }
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_ENERGY']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                if (!isset($user_production[$i]['NRJ'])) {
                    $user_production[$i]['NRJ'] = 0;
                }
                echo "\t" . "<th>" . number_format($user_production[$i]['NRJ'], 0, ',', ' ') . "</th>" . "\n";
            }

        ?>
    <tr>
        <td class="c" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>"><?php echo ($lang['HOME_EMPIRE_PRODUCTION_REAL']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_RATIO']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                if (!isset($user_production[$i]['ratio'])) {
                    $user_production[$i]['ratio'] = 0;
                }
                echo "\t" . "<th style='font-weight:bold; color:";
                if ($user_production[$i]['ratio'] != 1) {
                    echo "red";
                } else {
                    echo "green";
                }
                echo ";'>";
                echo number_format(round($user_production[$i]['ratio'], 3), 3, ',', ' ');
                echo "</th>" . "\n";
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_METAL']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                if ($user_building[$i]['M'] != "") {
                    echo "\t" . "<th>" . number_format(floor($user_production[$i]['prod_reel']['M']), 0, ',', ' ') . "</th>" . "\n";
                } else {
                    echo "\t" . "<th>&nbsp;</th>" . "\n";
                }
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_CRYSTAL']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                if ($user_building[$i]['C'] != "") {
                    echo "\t" . "<th>" . number_format(floor($user_production[$i]['prod_reel']['C']), 0, ',', ' ') . "</th>" . "\n";
                } else {
                    echo "\t" . "<th>&nbsp;</th>" . "\n";
                }
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_DEUT']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                if ($user_building[$i]['D'] != "") {
                    echo "\t" . "<th>" . number_format(floor($user_production[$i]['prod_reel']['D']), 0, ',', ' ') . "</th>" . "\n";
                } else {
                    echo "\t" . "<th>&nbsp;</th>" . "\n";
                }
            }
        ?>
    </tr>
    <tr style='font-style:italic;'>
        <th><a><?php echo ($lang['HOME_EMPIRE_BOOSTER']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $booster_tab = booster_decode($user_building[$i]["boosters"]);
                echo "\t" . "<th>m:" . $booster_tab['booster_m_val'] . '%, c:' . $booster_tab['booster_c_val'] . '%, d:' . $booster_tab['booster_d_val'] . '%, e:' . $booster_tab['booster_e_val'] . "%</th>" . "\n";
            }
        ?>
    </tr>
    <tr>
        <td class="c_batiments" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>">
            <?php echo ($lang['HOME_EMPIRE_BUILDINGS']); ?>
        </td>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_MINE_METAL']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $M = $user_building[$i]["M"];
                if ($M == "") {
                    $M = "&nbsp;";
                }

                echo "\t" . "<th><span  id='15" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $M . "</span></th>" . "\n";
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_MINE_CRYSTAL']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $C = $user_building[$i]["C"];
                if ($C == "") {
                    $C = "&nbsp;";
                }

                echo "\t" . "<th><span  id='16" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $C . "</span></th>" . "\n";
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_MINE_DEUT']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $D = $user_building[$i]["D"];
                if ($D == "") {
                    $D = "&nbsp;";
                }

                echo "\t" . "<th><span  id='17" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $D . "</span></th>" . "\n";
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_SOLAR_PLANT']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $CES = $user_building[$i]["CES"];
                if ($CES == "") {
                    $CES = "&nbsp;";
                }

                echo "\t" . "<th><span  id='20" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $CES . "</span></th>" . "\n";
            }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_FUSION_PLANT']); ?></a></th>
    <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $CEF = $user_building[$i]["CEF"];
                if ($CEF == "") {
                    $CEF = "&nbsp;";
                }

                echo "\t" . "<th><span  id='21" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $CEF . "</span></th>" . "\n";
            }
        } // fin de si view="planets"
        else {
            echo '</tr><tr> <td class="c" colspan="';
            print ($nb_planete < 10) ? '10' : $nb_planete + 1;
            echo '">' . $lang['HOME_EMPIRE_BUILDINGS'] . '</td>';
        }
    ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_ROBOTS_PLANT']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $UdR = $user_building[$i]["UdR"];
            if ($UdR == "") {
                $UdR = "&nbsp;";
            }

            echo "\t" . "<th><span  id='1" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $UdR . "</span></th>" . "\n";
        }

        if ($view == "planets") {
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_NANITES_PLANT']); ?></a></th>
    <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $UdN = $user_building[$i]["UdN"];
                if ($UdN == "") {
                    $UdN = "&nbsp;";
                }

                echo "\t" . "<th><span  id='22" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $UdN . "</span></th>" . "\n";
            }
        } // fin de si view="planets"
    ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_SHIPYARD']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $CSp = $user_building[$i]["CSp"];
            if ($CSp == "") {
                $CSp = "&nbsp;";
            }

            echo "\t" . "<th><span  id='2" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $CSp . "</span></th>" . "\n";
        }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_METALSTORAGE']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $HM = $user_building[$i]["HM"];
            if ($HM == "") {
                $HM = "&nbsp;";
            }

            echo "\t" . "<th><span  id='3" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $HM . "</span></th>" . "\n";
        }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_CRYSTALSTORAGE']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $HC = $user_building[$i]["HC"];
            if ($HC == "") {
                $HC = "&nbsp;";
            }

            echo "\t" . "<th><span  id='4" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $HC . "</span></th>" . "\n";
        }
        ?>
    </tr>
    <tr>
        <th><a><?php echo ($lang['HOME_EMPIRE_DEUTSTORAGE']); ?></a></th>
        <?php
        for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
            $HD = $user_building[$i]["HD"];
            if ($HD == "") {
                $HD = "&nbsp;";
            }

            echo "\t" . "<th><span  id='5" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $HD . "</span></th>" . "\n";
        }
        ?>
    </tr>
    <?php
    if ($view == "planets") { ?>

        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_RESEARCHLAB']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $Lab = $user_building[$i]["Lab"];
                if ($Lab == "") {
                    $Lab = "&nbsp;";
                }

                echo "\t" . "<th><span  id='23" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Lab . "</span></th>" . "\n";
            }
            if ($server_config['ddr'] == 1) {
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_ALLIANCEDEPOT']); ?></a></th>
        <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $DdR = $user_building[$i]["DdR"];
                    if ($DdR == "") {
                        $DdR = "&nbsp;";
                    }

                    echo "\t" . "<th><span  id='42" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $DdR . "</span></th>" . "\n";
                }
            } //Fin de si $server_config['ddr']
        ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TERRAFORMER']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $Ter = $user_building[$i]["Ter"];
                if ($Ter == "") {
                    $Ter = "&nbsp;";
                }

                echo "\t" . "<th><span  id='24" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Ter . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_MISSILESSILO']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $Silo = $user_building[$i]["Silo"];
                if ($Silo == "") {
                    $Silo = "&nbsp;";
                }

                echo "\t" . "<th><span  id='25" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Silo . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_DOCK']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $Dock = $user_building[$i]["Dock"];
                if ($Dock == "") {
                    $Dock = "&nbsp;";
                }

                echo "\t" . "<th><span  id='25" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Dock . "</span></th>" . "\n";
            }
        } else {
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_LUNARSTATION']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $BaLu = $user_building[$i]["BaLu"];
                if ($BaLu == "") {
                    $BaLu = "&nbsp;";
                }

                echo "\t" . "<th><span  id='15" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $BaLu . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_LUNARPHALANX']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $Pha = $user_building[$i]["Pha"];
                if ($Pha == "") {
                    $Pha = "&nbsp;";
                }

                echo "\t" . "<th><span  id='16" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Pha . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_LUNARJUMPGATE']); ?></a></th>
        <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $PoSa = $user_building[$i]["PoSa"];
                if ($PoSa == "") {
                    $PoSa = "&nbsp;";
                }

                echo "\t" . "<th><span  id='17" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $PoSa . "</span></th>" . "\n";
            }
        } // fin de sinon view="planets"
        ?>
        </tr>
        <tr>
            <td class="c_satellite" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1; ?>"><?php echo ($lang['HOME_EMPIRE_OTHERS']); ?></td>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_SATELLITES']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $Sat = $user_building[$i]["Sat"];
                if ($Sat == "") {
                    $Sat = "&nbsp;";
                } else {
                    $Sat = number_format($Sat, 0, ',', ' ');
                }

                echo "\t" . "<th><span  id='6" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Sat . "</span></th>" . "\n";
            }

            if ($view == "planets") {
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_CRAWLER']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $For = $user_building[$i]["FOR"];
                    $class_collect = ($user_data['user_class'] === 'COL') ? '1' : '0';
                    $nb_max = foreuse_max($user_building[$i]['M'], $user_building[$i]['C'], $user_building[$i]['D'], $user_data['off_geologue'], $class_collect);
                    if ($For == "") {
                        $For = "&nbsp;";
                    } else {
                        $For = number_format($For, 0, ',', ' ');
                    }

                    echo "\t" . "<th><span  id='43" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $For . " / " . $nb_max . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <td class="c_classement_recherche" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>"><?php echo ($lang['HOME_EMPIRE_TECHNOS']); ?></td>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_SPY']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Esp = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Esp = $user_technology["Esp"] != "" ? $user_technology["Esp"] : "0";
                        $requirement = $technology_requirement["Esp"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Esp = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Esp = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='26" . (($i + 1 - $start)) . "' style=\"color: lime; \">" . $Esp . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_COMPUTER']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Ordi = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Ordi = $user_technology["Ordi"] != "" ? $user_technology["Ordi"] : "0";
                        $requirement = $technology_requirement["Ordi"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Ordi = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Ordi = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='27" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Ordi . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_WEAPONS']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Armes = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Armes = $user_technology["Armes"] != "" ? $user_technology["Armes"] : "0";
                        $requirement = $technology_requirement["Armes"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Armes = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Armes = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='28" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Armes . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_SHIELD']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Bouclier = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Bouclier = $user_technology["Bouclier"] != "" ? $user_technology["Bouclier"] : "0";
                        $requirement = $technology_requirement["Bouclier"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Bouclier = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Bouclier = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='29" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Bouclier . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_PROTECTION']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Protection = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Protection = $user_technology["Protection"] != "" ? $user_technology["Protection"] : "0";
                        $requirement = $technology_requirement["Protection"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Protection = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Protection = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='30" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Protection . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_ENERGY']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $NRJ = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $NRJ = $user_technology["NRJ"] != "" ? $user_technology["NRJ"] : "0";
                        $requirement = $technology_requirement["NRJ"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $NRJ = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $NRJ = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='31" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $NRJ . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_HYPERSPACE']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Hyp = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Hyp = $user_technology["Hyp"] != "" ? $user_technology["Hyp"] : "0";
                        $requirement = $technology_requirement["Hyp"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Hyp = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Hyp = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='32" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Hyp . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_COMBUSTION_DRIVE']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $RC = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $RC = $user_technology["RC"] != "" ? $user_technology["RC"] : "0";
                        $requirement = $technology_requirement["RC"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $RC = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $RC = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='33" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $RC . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_IMPULSE_DRIVE']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $RI = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $RI = $user_technology["RI"] != "" ? $user_technology["RI"] : "0";
                        $requirement = $technology_requirement["RI"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $RI = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $RI = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='34" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $RI . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_HYPER_DRIVE']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $PH = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $PH = $user_technology["PH"] != "" ? $user_technology["PH"] : "0";
                        $requirement = $technology_requirement["PH"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $PH = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $PH = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='35" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $PH . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_LASER']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Laser = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Laser = $user_technology["Laser"] != "" ? $user_technology["Laser"] : "0";
                        $requirement = $technology_requirement["Laser"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Laser = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Laser = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='36" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Laser . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_IONS']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Ions = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Ions = $user_technology["Ions"] != "" ? $user_technology["Ions"] : "0";
                        $requirement = $technology_requirement["Ions"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Ions = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Ions = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='37" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Ions . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_PLASMA']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Plasma = "&nbsp;";
                    if ($user_building[$i][0] == true) {
                        $Plasma = $user_technology["Plasma"] != "" ? $user_technology["Plasma"] : "0";
                        $requirement = $technology_requirement["Plasma"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key === 0) {
                                if ($Lab < $value) {
                                    $Plasma = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Plasma = "-";
                            }
                            next($requirement);
                        }
                    }
                    echo "\t" . "<th><span  id='38" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Plasma . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_RESEARCH_NETWORK']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $RRI = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $RRI = $user_technology["RRI"] != "" ? $user_technology["RRI"] : "0";
                        $requirement = $technology_requirement["RRI"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $RRI = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $RRI = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='39" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $RRI . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_ASTRO']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Astrophysique = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Astrophysique = $user_technology["Astrophysique"] != "" ? $user_technology["Astrophysique"] : "0";
                        $requirement = $technology_requirement["Astrophysique"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Astrophysique = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Astrophysique = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='41" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Astrophysique . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS_GRAVITY']); ?></a></th>
        <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $Lab = $user_building[$i]["Lab"];
                    $Graviton = "&nbsp;";

                    if ($user_building[$i][0] == true) {
                        $Graviton = $user_technology["Graviton"] != "" ? $user_technology["Graviton"] : "0";
                        $requirement = $technology_requirement["Graviton"];

                        while ($value = current($requirement)) {
                            $key = key($requirement);
                            if ($key == 0) {
                                if ($Lab < $value) {
                                    $Graviton = "-";
                                }
                            } elseif ($user_technology[$key] < $value) {
                                $Graviton = "-";
                            }
                            next($requirement);
                        }
                    }

                    echo "\t" . "<th><span  id='40" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $Graviton . "</span></th>" . "\n";
                }
            } // fin de si view="planets"
        ?>
        </tr>
        <tr>
            <td class="c_defense" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>"><?php echo ($lang['HOME_EMPIRE_WEAPONS_TITLE']); ?></td>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_MISSILES']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $LM = $user_defence[$i]["LM"];
                if ($LM == "") {
                    $LM = "&nbsp;";
                } else {
                    $LM = number_format($LM, 0, ',', ' ');
                }

                echo "\t" . "<th><span  id='7" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $LM . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_LLASERS']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $LLE = $user_defence[$i]["LLE"];
                if ($LLE == "") {
                    $LLE = "&nbsp;";
                } else {
                    $LLE = number_format($LLE, 0, ',', ' ');
                }

                echo "\t" . "<th><span  id='8" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $LLE . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_HLASERS']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $LLO = $user_defence[$i]["LLO"];
                if ($LLO == "") {
                    $LLO = "&nbsp;";
                } else {
                    $LLO = number_format($LLO, 0, ',', ' ');
                }

                echo "\t" . "<th><span  id='9" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $LLO . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_GAUSS']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $CG = $user_defence[$i]["CG"];
                if ($CG == "") {
                    $CG = "&nbsp;";
                } else {
                    $CG = number_format($CG, 0, ',', ' ');
                }

                echo "\t" . "<th><span  id='10" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $CG . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_IONS']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $AI = $user_defence[$i]["AI"];
                if ($AI == "") {
                    $AI = "&nbsp;";
                } else {
                    $AI = number_format($AI, 0, ',', ' ');
                }

                echo "\t" . "<th><span  id='11" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $AI . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_PLASMA']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $LP = $user_defence[$i]["LP"];
                if ($LP == "") {
                    $LP = "&nbsp;";
                } else {
                    $LP = number_format($LP, 0, ',', ' ');
                }

                echo "\t" . "<th><span  id='12" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $LP . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_SMALLSHIELD']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $PB = $user_defence[$i]["PB"];
                if ($PB == "") {
                    $PB = "&nbsp;";
                }

                echo "\t" . "<th><span  id='13" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $PB . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_LARGESHIELD']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $GB = $user_defence[$i]["GB"];
                if ($GB == "") {
                    $GB = "&nbsp;";
                }

                echo "\t" . "<th><span  id='14" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $GB . "</span></th>" . "\n";
            }

            if ($view == "planets") {
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_ANTI']); ?></a></th>
            <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $MIC = $user_defence[$i]["MIC"];
                    if ($MIC == "") {
                        $MIC = "&nbsp;";
                    } else {
                        $MIC = number_format($MIC, 0, ',', ' ');
                    }

                    echo "\t" . "<th><span  id='19" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $MIC . "</span></th>" . "\n";
                }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_INTER']); ?></a></th>
        <?php
                for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                    $MIP = $user_defence[$i]["MIP"];
                    if ($MIP == "") {
                        $MIP = "&nbsp;";
                    } else {
                        $MIP = number_format($MIP, 0, ',', ' ');
                    }

                    echo "\t" . "<th><span  id='18" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $MIP . "</span></th>" . "\n";
                }
            } // fin de si view="planets"
        ?>
        </tr>
        <tr>
            <td class="c" colspan="<?php print ($nb_planete < 10) ? '10' : $nb_planete + 1 ?>"><?php echo ($lang['HOME_EMPIRE_POINTS_TITLE']); ?></td>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_BUILDINGS']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $point = all_building_cumulate(array(1 => $user_building[$i]));
                $point = round($point / 1000);
                $point = number_format($point, 0, ',', ' ');

                echo "\t" . "<th><span  id='19" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $point . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_WEAPONS_TITLE']); ?></a></th>
            <?php
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $point = all_defence_cumulate(array(1 => $user_defence[$i]));
                $point = round($point / 1000);
                $point = number_format($point, 0, ',', ' ');

                echo "\t" . "<th><span  id='20" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $point . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_FLEET_TITLE']); ?></a></th>
            <?php
            // Pour le moment seulement les FOR et les SAT !!
            for ($i = $start; $i <= $start + $nb_planete - 1; $i++) {
                $point = all_fleet_cumulate(array(1 => $user_building[$i])); //FOR et Sat
                $point = round($point / 1000);
                $point = number_format($point, 0, ',', ' ');

                echo "\t" . "<th><span  id='20" . ($i + 1 - $start) . "' style=\"color: lime; \">" . $point . "</span></th>" . "\n";
            }
            ?>
        </tr>
        <tr>
            <th><a><?php echo ($lang['HOME_EMPIRE_TECHNOS']); ?></a></th>
            <th colspan="<?php print ($nb_planete < 10) ? '9' : $nb_planete ?>"><span id='21' style="color: lime;">
                    <?php
                    $point = all_technology_cumulate($user_technology);
                    $point = round($point / 1000);
                    $point = number_format($point, 0, ',', ' ');
                    echo $point;
                    ?></span>
            </th>
        </tr>
</table>

<?php
/**
 * @param $txt
 * @param $nb_planete
 * @return string
 */
function read_th($txt, $nb_planete)
{
    $retour = "";
    if ($nb_planete > 9) {
        for ($i = 10; $i <= $nb_planete; $i++) {
            $retour = $retour . $txt;
        }
    }
    return $retour;
}
