<?php
/***************************************************************************
 *    filename    : home_stat.php
 *    desc.        :
 *    Author        : Ben.12 - http://ogsteam.fr/
 *    created        : 22/01/2006
 *    modified    : 22/08/2006 00:00:00
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
//integré dasn le common.php
//require "includes/ogame.php";

if (!isset($pub_zoom) || !isset($pub_user_stat_name) || !isset($pub_player_comp) ||
    !isset($pub_user_stat_name)
) {
    $pub_user_stat_name = "";
    $pub_player_comp = "";
    $pub_user_stat_name = "";
    $pub_zoom = "";
}
if (!check_var($pub_zoom, "Char") || !check_var($pub_player_comp, "Text") || !
    check_var($pub_user_stat_name, "Text")
) {
    redirection("index.php?action=message&amp;id_message=errordata&amp;info");
}

$zoom = $pub_zoom;
$player_comp = $pub_player_comp;
$user_stat_name = $pub_user_stat_name;

if (!isset($zoom))
    $zoom = "true";
if (isset($pub_zoom_change_y) && isset($pub_zoom_change_x))
    $zoom = ($zoom == "true" ? "false" : "true");
if (!isset($player_comp))
    $player_comp = "";
if (isset($user_stat_name) && $user_stat_name != "" && $user_stat_name != $user_data["user_stat_name"]) {
    user_set_stat_name($user_stat_name);
    redirection("index.php?action=home&amp;subaction=stat&amp;zoom=" . $zoom .
        "&player_comp=" . $player_comp);
}

$individual_ranking = galaxy_show_ranking_unique_player($user_data["user_stat_name"]);

ksort($individual_ranking);

$individual_ranking_2 = galaxy_show_ranking_unique_player($player_comp);

$dates = array_keys($individual_ranking);
$dates2 = array_keys($individual_ranking_2);
$dates = sizeof($dates) > sizeof($dates2) ? $dates : $dates2;

if (sizeof($dates) > 0) {
    $max_date = max($dates);
    $min_date = min($dates);

    if (isset($pub_start_date) && isset($pub_end_date) && preg_match("/^(3[01]|[0-2][0-9]|[1-9])\/([1-9]|0[1-9]|1[012])\/(2[[:digit:]]{3})$/",
            trim($pub_start_date)) && preg_match("/^(3[01]|[0-2][0-9]|[1-9])\/([1-9]|0[1-9]|1[012])\/(2[[:digit:]]{3})$/",
            trim($pub_end_date))
    ) {
        $min = explode("/", trim($pub_start_date));
        $min = mktime(22, 0, 0, $min[1], $min[0] - 1, $min[2]);
        $max = explode("/", trim($pub_end_date));
        $max = mktime(18, 0, 0, $max[1], $max[0], $max[2]);
        if ($max > $min) {
            $max_date = $max;
            $min_date = $min;
        }
    }
} else {
    $max_date = time();
    $min_date = time();
}
?>
<center>
    <form method="get" action="index.php">
        <input type="hidden" name="action" value="home"/> <input
            type="hidden" name="subaction" value="stat"/> <input type="hidden"
                                                                 name="zoom" value="<?php echo $zoom; ?>"/>
        <table>
            <tr>
                <td class='c'>Les statistiques de :</td>
                <td class='c' colspan='2'>Options :</td>
            </tr>
            <tr>
                <th><input type="text" name="user_stat_name"
                           value="<?php echo $user_data["user_stat_name"]; ?>"/> <input
                        type="submit" value="obtenir les statistiques"/>
                </th>
                <th rowspan="2"><span style="text-decoration: underline;">intervalle d'étude</span> : du <input type="text"
                                                                      size="10" maxlength="10" name="start_date"
                                                                      value="<?php echo strftime("%d/%m/%Y", $min_date + 60 * 60 * 2); ?>"/>
                    au <input type="text" size="10" maxlength="10" name="end_date"
                              value="<?php echo strftime("%d/%m/%Y", $max_date); ?>"/> <input
                        type="submit" value="envoyer"/></th>
                <th rowspan="2">zoom : <input type="image" align="absmiddle"
                                              name="zoom_change"
                                              src="images/<?php echo($zoom == "true" ? "zoom_in.png" : "zoom_out.png"); ?>"
                                              alt="zoom"/>
                </th>
            </tr>
            <tr>
                <th><input type="text" name="player_comp"
                           value="<?php echo $player_comp; ?>"/> <input type="submit"
                                                                        value="comparer avec"/></th>
            </tr>
        </table>
    </form>





    <?php
    $first = array("general_pts" => -1, "eco_pts" => -1, "techno_pts" => -1,
        "military_pts" => -1, "military_b_pts" => -1, "military_l_pts" => -1,
        "military_d_pts" => -1, "honnor_pts" => -1);
    $last = array("general_pts" => 0, "eco_pts" => 0, "techno_pts" => 0,
        "military_pts" => 0, "military_b_pts" => 0, "military_l_pts" => 0,
        "military_d_pts" => 0, "honnor_pts" => 0, "general_rank" => 0, "eco_rank" => 0,
        "techno_rank" => 0, "military_rank" => 0, "military_b_rank" => 0,
        "military_l_rank" => 0, "military_d_rank" => 0, "honnor_rank" => 0);
    $tab_rank = "";


    while ($ranking = current($individual_ranking)) {

        $v = key($individual_ranking);

        if ($v < $min_date || $v > $max_date) {
            next($individual_ranking);
            continue;
        }

        if ($first["general_pts"] == -1 && isset($ranking["general"])) {
            $first["general_pts"] = $ranking["general"]["points"];
            $first["general_rank"] = $ranking["general"]["rank"];
            $first_date["general"] = $v;
        }

        if ($first["eco_pts"] == -1 && isset($ranking["eco"])) {
            $first["eco_pts"] = $ranking["eco"]["points"];
            $first["eco_rank"] = $ranking["eco"]["rank"];
            $first_date["eco"] = $v;
        }

        if ($first["techno_pts"] == -1 && isset($ranking["techno"])) {
            $first["techno_pts"] = $ranking["techno"]["points"];
            $first["techno_rank"] = $ranking["techno"]["rank"];
            $first_date["techno"] = $v;
        }
        if ($first["military_pts"] == -1 && isset($ranking["military"])) {
            $first["military_pts"] = $ranking["military"]["points"];
            $first["military_rank"] = $ranking["military"]["rank"];
            $first_date["military"] = $v;
        }
        if ($first["military_b_pts"] == -1 && isset($ranking["military_b"])) {
            $first["military_b_pts"] = $ranking["military_b"]["points"];
            $first["military_b_rank"] = $ranking["military_b"]["rank"];
            $first_date["military_b"] = $v;
        }
        if ($first["military_l_pts"] == -1 && isset($ranking["military_l"])) {
            $first["military_l_pts"] = $ranking["military_l"]["points"];
            $first["military_l_rank"] = $ranking["military_l"]["rank"];
            $first_date["military_l"] = $v;
        }
        if ($first["military_d_pts"] == -1 && isset($ranking["military_d"])) {
            $first["military_d_pts"] = $ranking["military_d"]["points"];
            $first["military_d_rank"] = $ranking["military_d"]["rank"];
            $first_date["military_d"] = $v;
        }
        if ($first["honnor_pts"] == -1 && isset($ranking["honnor"])) {
            $first["honnor_pts"] = $ranking["honnor"]["points"];
            $first["honnor_rank"] = $ranking["honnor"]["rank"];
            $first_date["honnor"] = $v;
        }

        if (isset($ranking["general"])) {
            $last["general_pts"] = $ranking["general"]["points"];
            $last["general_rank"] = $ranking["general"]["rank"];
            $last_date["general"] = $v;
        }

        if (isset($ranking["eco"])) {
            $last["eco_pts"] = $ranking["eco"]["points"];
            $last["eco_rank"] = $ranking["eco"]["rank"];
            $last_date["eco"] = $v;
        }

        if (isset($ranking["techno"])) {
            $last["techno_pts"] = $ranking["techno"]["points"];
            $last["techno_rank"] = $ranking["techno"]["rank"];
            $last_date["techno"] = $v;
        }

        if (isset($ranking["military"])) {
            $last["military_pts"] = $ranking["military"]["points"];
            $last["military_rank"] = $ranking["military"]["rank"];
            $last_date["military"] = $v;
        }
        if (isset($ranking["military_b"])) {
            $last["military_b_pts"] = $ranking["military_b"]["points"];
            $last["military_b_rank"] = $ranking["military_b"]["rank"];
            $last_date["military_b"] = $v;
        }
        if (isset($ranking["military_l"])) {
            $last["military_l_pts"] = $ranking["military_l"]["points"];
            $last["military_l_rank"] = $ranking["military_l"]["rank"];
            $last_date["military_l"] = $v;
        }

        if (isset($ranking["military_d"])) {
            $last["military_d_pts"] = $ranking["military_d"]["points"];
            $last["military_d_rank"] = $ranking["military_d"]["rank"];
            $last_date["military_d"] = $v;
        }

        if (isset($ranking["honnor"])) {
            $last["honnor_pts"] = $ranking["honnor"]["points"];
            $last["honnor_rank"] = $ranking["honnor"]["rank"];
            $last_date["honnor"] = $v;
        }


        $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) :
            "&nbsp;";
        $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) :
            "&nbsp;";
        $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) :
            "&nbsp;";
        $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) :
            "&nbsp;";
        $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) :
            "&nbsp;";
        $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) :
            "&nbsp;";
        $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) :
            "&nbsp;";
        $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) :
            "&nbsp;";
        $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) :
            "&nbsp;";
        $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) :
            "&nbsp;";
        $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) :
            "&nbsp;";
        $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) :
            "&nbsp;";
        $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) :
            "&nbsp;";
        $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) :
            "&nbsp;";
        $honnor_rank = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["rank"]) :
            "&nbsp;";
        $honnor_points = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["points"]) :
            "&nbsp;";


        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $honnor_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $honnor_points . "</th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $military_d_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $military_d_points . "</th>" . "\n" .
            $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $military_l_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $military_l_points . "</th>" . "\n" .
            $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $military_b_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $military_b_points . "</th>" . "\n" .
            $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $military_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $military_points . "</th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $techno_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $techno_points . "</th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $eco_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $eco_points . "</th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='40'><font color='lime'><i>" . $general_rank .
            "</i></font></th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='70'>" . $general_points . "</th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t\t" . "<th width='180'>" . strftime("%d %b %Y %H:%M", $v) .
            "</th>" . "\n" . $tab_rank;
        $tab_rank = "\t\t" . "<tr>" . "\n" . $tab_rank;

        next($individual_ranking);
    }

    echo "<p><b><u style='font-size:14px;'>Les statistiques de " . $user_data["user_stat_name"] .
        "</u></b></p>";

    echo "<table width='1000'>";

if ($player_comp != "" && isset($player_comp)): ?>
        <tr>
            <td class='c' colspan='2'>Comparaison</td>
        </tr>
    <?php else : ?>
        <tr>
            <td class='c' colspan='2'>Classements</td>
        </tr>
    <?php endif;
    // affichage du tableau avec conteur div
    ?>
    <tr>
        <th colspan='2'>
            <div id="<?php echo "points"; ?>">pas de graphique disponible</div>
        </th>
    </tr>
    <tr>
        <th colspan='2'>
            <div id="<?php echo "rank"; ?>">pas de graphique disponible</div>
        </th>
    </tr>




    <?php
    // on fabrique toutes les courbes ici
    global $zoom;
    $zoom = 'false';
    $curve = create_curves($user_data["user_stat_name"], $min_date, $max_date,
        $player_comp);

    $title = "Basé sur vos données dans \"Empire\"";
    if (!empty($user_data["user_stat_name"])) {
        $title .= " et les stats de " . $user_data["user_stat_name"];
        if (!empty($last_date["general"]))
            $title .= " du " . strftime("%d %b %Y %H:%M", $last_date["general"]);
    }
    echo "<tr><td class='c' colspan='2'>Divers " . help(null, $title) . "</td></tr>";

    $user_empire = user_get_empire($user_data["user_id"]);
    $user_building = $user_empire["building"];
    $user_defence = $user_empire["defence"];
    $user_technology = $user_empire["technology"];

    $nb_planete = find_nb_planete_user($user_data["user_id"]);

    $b = round(all_building_cumulate(array_slice($user_building, 0, $nb_planete)) /
        1000);
    $d = round(all_defence_cumulate(array_slice($user_defence, 0, $nb_planete)) /
        1000);
    $l = round(all_lune_cumulate(array_slice($user_building, $nb_planete, $nb_planete),
            array_slice($user_defence, $nb_planete, $nb_planete)) / 1000);
    $t = round(all_technology_cumulate($user_technology) / 1000);
    $f = $last["general_pts"] - $b - $d - $l - $t;
    if ($f < 0)
        $f = 0;


    // affichage premier camembert
    $pie_point = "";
    echo "<th align='center' width='400'>";
    echo "<div id='pie_point'>";
    // pas d info
    if ($b == 0 && $d == 0 && $l == 0 && $t == 0)
        echo "Pas de données dans l'empire";

// calcul impossible ( non connaissance du classement)
    elseif ($last["general_pts"] == 0)
        echo "Pas de données sur le total de points";

// autrement on affiche rien : on prepare juste l affichage du script
    else {
        $pie_point = create_pie($b . "_x_" . $d . "_x_" . $l . "_x_" . $f . "_x_" . $t,
            "Batiments_x_Défenses_x_Lunes_x_Flotte_x_Technologies",
            "Dernière répartition des points connue", "pie_point");
    }
    echo "</div>";
    echo "</th>\n";


    $planet = array();
    $planet_name = array();
    for ($i = 1; $i <= $nb_planete; $i++) {
        $b = round(all_building_cumulate(array_slice($user_building, $i - 1, 1)) / 1000);
        $d = round(all_defence_cumulate(array_slice($user_defence, $i - 1, 1)) / 1000);
        $l = round(all_lune_cumulate(array_slice($user_building, $i + $nb_planete - 1, 1),
                array_slice($user_defence, $i + $nb_planete - 1, 1)) / 1000);
        if ($b != 0 || $d != 0 || $l != 0) {
            $planet[] = $b + $d + $l;
            $planet_name[] = $user_building[$i + 100]['planet_name'];
        }
    }

    // affichage second camembert
    $pie_empire = "";
    echo "<th align='center' width='400'>";
    echo "<div id='pie_empire'  width='400'>";
    // pas d info
    if ($b == 0 && $d == 0 && $l == 0 && $t == 0)
        echo "Pas de données dans l'empire";

// autrement on affiche rien : on prepare juste l affichage du script
    else {
        $pie_empire = create_pie(implode($planet, "_x_"), implode($planet_name, "_x_"),
            "Proportion des planètes - lunes comprises", "pie_empire");
    }

    echo "</div>";
    echo "</th></tr></table>";

    ?>



    <br/>


    <table>
        <tr>
            <td class="c" colspan="17">Classement de <a><?php echo $user_data["user_stat_name"]; ?></a></td>
        </tr>
        <tr>
            <td class="c" width="140">Date</td>
            <td class="c_classement_points" colspan="2">Pts Général</td>
            <td class="c" colspan="2">Pts Economique</td>
            <td class="c_classement_recherche" colspan="2">Pts Recherche</td>
            <td class="c_classement_flotte" colspan="2">Pts Militaire</td>
            <td class="c_classement_flotte" colspan="2">Pts Militaire Construits</td>
            <td class="c_classement_flotte" colspan="2">Pts Perte militaire</td>
            <td class="c_classement_flotte" colspan="2">Pts destruction</td>
            <td class="c" colspan="2">Pts honorifique</td>

        </tr>
        <?php

        echo $tab_rank;
        echo "\t\t" . "<tr>" . "\n";
        echo "\t\t\t" . "<th width='150' style='border-color:#FF0000'><font color='yellow'>Progression moyenne par jour :</font></th>" .
            "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["general_pts"] ==
                -1 || $last_date["general"] == $first_date["general"]) ? "-" : round(($last["general_pts"] -
                    $first["general_pts"]) * 60 * 60 * 24 / ($last_date["general"] - $first_date["general"]),
                2)) . "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["general_pts"] ==
                -1 || $last_date["general"] == $first_date["general"]) ? "-" : round(($last["general_rank"] -
                        $first["general_rank"]) * 60 * 60 * 24 / ($last_date["general"] - $first_date["general"]),
                    2) * (-1)) . "</i></font></th>" . "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["eco_pts"] ==
                -1 || $last_date["eco"] == $first_date["eco"]) ? "-" : round(($last["eco_pts"] -
                    $first["eco_pts"]) * 60 * 60 * 24 / ($last_date["eco"] - $first_date["eco"]), 2)) .
            "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["eco_pts"] ==
                -1 || $last_date["eco"] == $first_date["eco"]) ? "-" : round(($last["eco_rank"] -
                        $first["eco_rank"]) * 60 * 60 * 24 / ($last_date["eco"] - $first_date["eco"]), 2) *
                (-1)) . "</i></font></th>" . "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["techno_pts"] ==
                -1 || $last_date["techno"] == $first_date["techno"]) ? "-" : round(($last["techno_pts"] -
                    $first["techno_pts"]) * 60 * 60 * 24 / ($last_date["techno"] - $first_date["techno"]),
                2)) . "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["techno_pts"] ==
                -1 || $last_date["techno"] == $first_date["techno"]) ? "-" : round(($last["techno_rank"] -
                        $first["techno_rank"]) * 60 * 60 * 24 / ($last_date["techno"] - $first_date["techno"]),
                    2) * (-1)) . "</i></font></th>" . "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["military_pts"] ==
                -1 || $last_date["military"] == $first_date["military"]) ? "-" : round(($last["military_pts"] -
                    $first["military_pts"]) * 60 * 60 * 24 / ($last_date["military"] - $first_date["military"]),
                2)) . "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["military_pts"] ==
                -1 || $last_date["military"] == $first_date["military"]) ? "-" : round(($last["military_rank"] -
                        $first["military_rank"]) * 60 * 60 * 24 / ($last_date["military"] - $first_date["military"]),
                    2) * (-1)) . "</i></font></th>" . "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["military_b_pts"] ==
                -1 || $last_date["military_b"] == $first_date["military_b"]) ? "-" : round(($last["military_b_pts"] -
                    $first["military_b_pts"]) * 60 * 60 * 24 / ($last_date["military_b"] - $first_date["military_b"]),
                2)) . "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["military_b_pts"] ==
                -1 || $last_date["military_b"] == $first_date["military_b"]) ? "-" : round(($last["military_b_rank"] -
                        $first["military_b_rank"]) * 60 * 60 * 24 / ($last_date["military_b"] - $first_date["military_b"]),
                    2) * (-1)) . "</i></font></th>" . "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["military_l_pts"] ==
                -1 || $last_date["military_l"] == $first_date["military_l"]) ? "-" : round(($last["military_l_pts"] -
                    $first["military_l_pts"]) * 60 * 60 * 24 / ($last_date["military_l"] - $first_date["military_l"]),
                2)) . "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["military_l_pts"] ==
                -1 || $last_date["military_l"] == $first_date["military_l"]) ? "-" : round(($last["military_l_rank"] -
                        $first["military_l_rank"]) * 60 * 60 * 24 / ($last_date["military_l"] - $first_date["military_l"]),
                    2) * (-1)) . "</i></font></th>" . "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["military_d_pts"] ==
                -1 || $last_date["military_d"] == $first_date["military_d"]) ? "-" : round(($last["military_d_pts"] -
                    $first["military_d_pts"]) * 60 * 60 * 24 / ($last_date["military_d"] - $first_date["military_d"]),
                2)) . "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["military_d_pts"] ==
                -1 || $last_date["military_d"] == $first_date["military_d"]) ? "-" : round(($last["military_d_rank"] -
                        $first["military_d_rank"]) * 60 * 60 * 24 / ($last_date["military_d"] - $first_date["military_d"]),
                    2) * (-1)) . "</i></font></th>" . "\n";
        echo "\t\t\t" . "<th width='70' style='border-color:#FF0000'>" . (($first["honnor_pts"] ==
                -1 || $last_date["honnor"] == $first_date["honnor"]) ? "-" : round(($last["honnor_pts"] -
                    $first["honnor_pts"]) * 60 * 60 * 24 / ($last_date["honnor"] - $first_date["honnor"]),
                2)) . "</th>" . "\n";
        echo "\t\t\t" . "<th width='40' style='border-color:#FF0000'><font color='lime'><i>" . (($first["honnor_pts"] ==
                -1 || $last_date["honnor"] == $first_date["honnor"]) ? "-" : round(($last["honnor_rank"] -
                        $first["honnor_rank"]) * 60 * 60 * 24 / ($last_date["honnor"] - $first_date["honnor"]),
                    2) * (-1)) . "</i></font></th>" . "\n</tr>";


        ?>

    </table>
</center>

<?php
/// affichage des script de création graph
// camembert
echo $pie_point;
echo $pie_empire;


echo $curve;
?>


