<?php
/**
 * Affichage Empire - Page Statistiques
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Ben.12
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

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
if (!check_var($pub_zoom, "Char") || !check_var($pub_player_comp, "Text") || !check_var($pub_user_stat_name, "Text")
) {
    redirection("index.php?action=message&amp;id_message=errordata&amp;info");
}

$zoom = $pub_zoom;
$player_comp = $pub_player_comp;
$user_stat_name = $pub_user_stat_name;

if (!isset($zoom)) {
    $zoom = "true";
}
if (isset($pub_zoom_change_y) && isset($pub_zoom_change_x)) {
    $zoom = ($zoom == "true" ? "false" : "true");
}
if (!isset($player_comp)) {
    $player_comp = "";
}
if (isset($user_stat_name) && $user_stat_name != "" && $user_stat_name != $user_data["user_stat_name"]) {
    user_set_stat_name($user_stat_name);
    redirection("index.php?action=home&amp;subaction=stat&amp;zoom=" . $zoom . "&amp;player_comp=" . $player_comp);
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
<div>
    <form method="get" action="index.php">
        <input type="hidden" name="action" value="home"/>
        <input type="hidden" name="subaction" value="stat"/>
        <input type="hidden" name="zoom" value="<?php echo $zoom; ?>"/>
        <p class="legend"><?php echo($lang['HOME_STATS_STATISTICS']); ?></p>
        <div>
            <input type="text" name="user_stat_name" value="<?php echo $user_data["user_stat_name"]; ?>"/>
            <input  class="button"  type="submit" value="<?php echo($lang['HOME_STATS_GETSTATS']); ?>"/>
        </div>
        <div>
            <input type="text" name="player_comp" value="<?php echo $player_comp; ?>"/>
            <input class="button" type="submit" value="<?php echo($lang['HOME_STATS_COMPARE']); ?>"/>
        </div>
        <p class="legend"><?php echo($lang['HOME_STATS_INTERVAL']); ?></p>
        <div>
            <label for="start_date"><?php echo($lang['HOME_STATS_FROM']); ?></label>
            <input type="text" size="10" maxlength="10" name="start_date"  id="start_date" value="<?php echo strftime("%d/%m/%Y", $min_date + 60 * 60 * 2); ?>"/>
        </div>
        <div>
            <label for="end_date"><?php echo($lang['HOME_STATS_TO']); ?></label>
            <input type="text" size="10" maxlength="10" name="end_date" id="end_date" value="<?php echo strftime("%d/%m/%Y", $max_date); ?>"/>
        </div>
        <div>
            <input type="image" name="zoom_change" src="images/<?php echo($zoom == "true" ? "zoom_in.png" : "zoom_out.png"); ?>" alt="zoom"/>
            <input type="submit" class="button" value="<?php echo($lang['HOME_STATS_SEND']); ?>"/>
        </div>
    </form>

    <?php
    $first = array("general_pts" => -1, "eco_pts" => -1, "techno_pts" => -1, "military_pts" => -1, "military_b_pts" => -1, "military_l_pts" => -1,
                   "military_d_pts" => -1, "honnor_pts" => -1);
    $last = array("general_pts" => 0, "eco_pts" => 0, "techno_pts" => 0, "military_pts" => 0, "military_b_pts" => 0, "military_l_pts" => 0,
                  "military_d_pts" => 0, "honnor_pts" => 0, "general_rank" => 0, "eco_rank" => 0, "techno_rank" => 0, "military_rank" => 0,
                  "military_b_rank" => 0, "military_l_rank" => 0, "military_d_rank" => 0, "honnor_rank" => 0);
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


        $general_rank = isset($ranking["general"]) ? formate_number($ranking["general"]["rank"]) : "&nbsp;";
        $general_points = isset($ranking["general"]) ? formate_number($ranking["general"]["points"]) : "&nbsp;";
        $eco_rank = isset($ranking["eco"]) ? formate_number($ranking["eco"]["rank"]) : "&nbsp;";
        $eco_points = isset($ranking["eco"]) ? formate_number($ranking["eco"]["points"]) : "&nbsp;";
        $techno_rank = isset($ranking["techno"]) ? formate_number($ranking["techno"]["rank"]) : "&nbsp;";
        $techno_points = isset($ranking["techno"]) ? formate_number($ranking["techno"]["points"]) : "&nbsp;";
        $military_rank = isset($ranking["military"]) ? formate_number($ranking["military"]["rank"]) : "&nbsp;";
        $military_points = isset($ranking["military"]) ? formate_number($ranking["military"]["points"]) : "&nbsp;";
        $military_b_rank = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["rank"]) : "&nbsp;";
        $military_b_points = isset($ranking["military_b"]) ? formate_number($ranking["military_b"]["points"]) : "&nbsp;";
        $military_l_rank = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["rank"]) : "&nbsp;";
        $military_l_points = isset($ranking["military_l"]) ? formate_number($ranking["military_l"]["points"]) : "&nbsp;";
        $military_d_rank = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["rank"]) : "&nbsp;";
        $military_d_points = isset($ranking["military_d"]) ? formate_number($ranking["military_d"]["points"]) : "&nbsp;";
        $honnor_rank = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["rank"]) : "&nbsp;";
        $honnor_points = isset($ranking["honnor"]) ? formate_number($ranking["honnor"]["points"]) : "&nbsp;";


        $tab_rank = "\t\t\t<td>" . $honnor_rank . "</span></td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td >" . $honnor_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $military_d_rank . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td >" . $military_d_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $military_l_rank . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td >" . $military_l_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $military_b_rank . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $military_b_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $military_rank . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td >" . $military_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $techno_rank . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td >" . $techno_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $eco_rank . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td >" . $eco_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . $general_rank . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td >" . $general_points . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t\t<td>" . strftime('%d %b %Y %H:%M', $v) . "</td>\n" . $tab_rank;
        $tab_rank = "\t\t<tr>\n" . $tab_rank;

        next($individual_ranking);
    }

    ?>

    <div class="msgbox msgboxdefault">
    <p class="msgboxtitle"><?php echo $lang['HOME_STATS_PALYERSTATS'] . " " . $user_data["user_stat_name"]  ;?></p>
        <div class="msgcontent">
            <?php if ($player_comp != "" && isset($player_comp)): ?>
            <h1>
                <?php echo($lang['HOME_STATS_COMP']); ?>
            </h1>
            <?php else : ?>
            <h1>
                <?php echo($lang['HOME_STATS_RANKINGS']); ?></td>
            </h1>
            <?php endif;            ?>
            <div id="<?php echo "points"; ?>"><?php echo($lang['HOME_STATS_NOGRAPHIC']); ?></div>
            <div id="<?php echo "rank"; ?>"><?php echo($lang['HOME_STATS_NOGRAPHIC']); ?></div>
            </div>
    </div>

    <?php




    // on fabrique toutes les courbes ici
    global $zoom;
    $zoom = 'false';
    $curve = create_curves($user_data["user_stat_name"], $min_date, $max_date,
        $player_comp);

    $title = $lang['HOME_STATS_GRAPHIC_TITLE'];
    if (!empty($user_data["user_stat_name"])) {
        $title .= " " . $lang['HOME_STATS_GRAPHIC_TITLE2'] . " " . $user_data["user_stat_name"];
        if (!empty($last_date["general"])) {
                    $title .= " " . $lang['HOME_STATS_GRAPHIC_FROM'] . " " . strftime("%d %b %Y %H:%M", $last_date["general"]);
        }
    }

    $user_empire = user_get_empire($user_data["user_id"]);
    $user_building = $user_empire["building"];
    $user_defence = $user_empire["defence"];
    $user_technology = $user_empire["technology"];

    $nb_planete = find_nb_planete_user($user_data["user_id"]);

    $b = round(all_building_cumulate(array_slice($user_building, 0, $nb_planete)) / 1000);
    $d = round(all_defence_cumulate(array_slice($user_defence, 0, $nb_planete)) / 1000);
    $l = round(all_lune_cumulate(array_slice($user_building, $nb_planete, $nb_planete), array_slice($user_defence, $nb_planete, $nb_planete)) / 1000);
    $t = round(all_technology_cumulate($user_technology) / 1000);
    $f = $last["general_pts"] - $b - $d - $l - $t;
    if ($f < 0) {
            $f = 0;
    }
?>


<?php
    $planet = array();
    $planet_name = array();
    for ($i = 1; $i <= $nb_planete; $i++) {
        $b = round(all_building_cumulate(array_slice($user_building, $i - 1, 1)) / 1000);
        $d = round(all_defence_cumulate(array_slice($user_defence, $i - 1, 1)) / 1000);
        $l = round(all_lune_cumulate(array_slice($user_building, $i + $nb_planete - 1, 1), array_slice($user_defence, $i + $nb_planete - 1, 1)) / 1000);
        if ($b != 0 || $d != 0 || $l != 0) {
            $planet[] = $b + $d + $l;
            $planet_name[] = $user_building[$i + 100]['planet_name'];
        }
    }
    ?>

    <div class="msgbox msgboxdefault">
        <p class="msgboxtitle"><?php  echo $lang['HOME_STATS_GRAPHIC_DIVERS'] . " " . help(null, $title)   ;?></p>
        <div class="msgcontent">
             <!-- graph 1 -->
            <div id="pie_point">
                <?php if ($b == 0 && $d == 0 && $l == 0 && $t == 0) :    // pas d info ?>
                    <?php echo $lang['HOME_STATS_GRAPHIC_NOEMPIREDATA']; ?>
                <?php ELSEIF ($b == 0 && $d == 0 && $l == 0 && $t == 0) :    // calcul impossible ( non connaissance du classement) ?>
                    <?php echo $lang['HOME_STATS_GRAPHIC_NOSTATSDATA']; ?>
                <?php else :?>
                    <?php  $pie_point = create_pie($b . "_x_" . $d . "_x_" . $l . "_x_" . $f . "_x_" . $t,
                        "Batiments_x_Défenses_x_Lunes_x_Flotte_x_Technologies",
                        $lang['HOME_STATS_GRAPHIC_LASTREPARTITION'], "pie_point");?>

                <?php endif;?>
            </div>
            <!-- graph 2 -->
            <?php  $pie_empire = "";?>
            <div id="pie_empire">
                <?php if ($b == 0 && $d == 0 && $l == 0 && $t == 0)  :?>
                    <?php echo $lang['HOME_STATS_GRAPHIC_NOEMPIREDATA']; ?>
                <?php else: ?>}
                    <?php $pie_empire = create_pie(implode($planet, "_x_"), implode($planet_name, "_x_"),
                    $lang['HOME_STATS_GRAPHIC_REPARTITION'], "pie_empire");?>
                <?php endif ;?>

            </div>
            </div>
    </div>


    <br/>
    <table>
        <thead>
        <tr>
            <th colspan="17"><?php echo($lang['HOME_STATS_RANKING']); ?> <?php echo $user_data["user_stat_name"]; ?></th>
        </tr>
        </thead>
        <tbody>


        <tr>
            <th  style="width:140px"><?php echo($lang['HOME_STATS_DATE']); ?></th>
            <th  colspan="2"><?php echo($lang['HOME_STATS_PTS_GENERAL']); ?></th>
            <th  colspan="2"><?php echo($lang['HOME_STATS_PTS_ECO']); ?></th>
            <th colspan="2"><?php echo($lang['HOME_STATS_PTS_RESEARCH']); ?></th>
            <th  colspan="2"><?php echo($lang['HOME_STATS_PTS_MILITARY']); ?></th>
            <th  colspan="2"><?php echo($lang['HOME_STATS_PTS_MILITARYBUILT']); ?></th>
            <th  colspan="2"><?php echo($lang['HOME_STATS_PTS_MILITARYLOST']); ?></th>
            <th  colspan="2"><?php echo($lang['HOME_STATS_PTS_MILITARYDEST']); ?></th>
            <th  colspan="2"><?php echo($lang['HOME_STATS_PTS_HONOR']); ?></th>
        </tr>
<?php
        echo $tab_rank;
        echo "\t\t<tr>\n";
        echo "\t\t\t<td>" . $lang['HOME_STATS_PROGRESS_RATE'] . " :</td>\n";
        echo "\t\t\t<td>" . (($first['general_pts'] == -1 ||
                $last_date['general'] == $first_date['general']) ? '-' : round(($last['general_pts'] - $first['general_pts']) * 60 * 60 * 24 / ($last_date['general'] - $first_date['general']),2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['general_pts'] == -1 ||
                $last_date['general'] == $first_date['general']) ? '-' : round(($last['general_rank'] - $first['general_rank']) * 60 * 60 * 24 / ($last_date['general'] - $first_date['general']), 2) * (-1)) . "</td>\n";

        echo "\t\t\t<td>" . (($first['eco_pts'] == -1 ||
                $last_date['eco'] == $first_date['eco']) ? '-' : round(($last['eco_pts'] - $first['eco_pts']) * 60 * 60 * 24 / ($last_date['eco'] - $first_date['eco']), 2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['eco_pts'] == -1 ||
                $last_date['eco'] == $first_date['eco']) ? '-' : round(($last['eco_rank'] - $first['eco_rank']) * 60 * 60 * 24 / ($last_date['eco'] - $first_date['eco']), 2) * (-1)) . "</td>\n";

        echo "\t\t\t<td>" . (($first['techno_pts'] == -1 ||
                $last_date['techno'] == $first_date['techno']) ? '-' : round(($last['techno_pts'] - $first['techno_pts']) * 60 * 60 * 24 / ($last_date['techno'] - $first_date['techno']),2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['techno_pts'] == -1 ||
                $last_date['techno'] == $first_date['techno']) ? '-' : round(($last['techno_rank'] - $first['techno_rank']) * 60 * 60 * 24 / ($last_date['techno'] - $first_date['techno']),2) * (-1)) . "</td>\n";

        echo "\t\t\t<td>" . (($first['military_pts'] == -1 ||
                $last_date['military'] == $first_date['military']) ? '-' : round(($last['military_pts'] - $first['military_pts']) * 60 * 60 * 24 / ($last_date['military'] - $first_date['military']),2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['military_pts'] == -1 ||
                $last_date['military'] == $first_date['military']) ? '-' : round(($last['military_rank'] - $first['military_rank']) * 60 * 60 * 24 / ($last_date['military'] - $first_date['military']),2) * (-1)) . "</td>\n";

        echo "\t\t\t<td>" . (($first['military_b_pts'] == -1 ||
                $last_date['military_b'] == $first_date['military_b']) ? '-' : round(($last['military_b_pts'] - $first['military_b_pts']) * 60 * 60 * 24 / ($last_date['military_b'] - $first_date['military_b']),2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['military_b_pts'] == -1 ||
                $last_date['military_b'] == $first_date['military_b']) ? '-' : round(($last['military_b_rank'] - $first['military_b_rank']) * 60 * 60 * 24 / ($last_date['military_b'] - $first_date['military_b']),2) * (-1)) . "</td>\n";

        echo "\t\t\t<td>" . (($first['military_l_pts'] == -1 ||
                $last_date['military_l'] == $first_date['military_l']) ? '-' : round(($last['military_l_pts'] - $first['military_l_pts']) * 60 * 60 * 24 / ($last_date['military_l'] - $first_date['military_l']),2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['military_l_pts'] == -1 ||
                $last_date['military_l'] == $first_date['military_l']) ? '-' : round(($last['military_l_rank'] - $first['military_l_rank']) * 60 * 60 * 24 / ($last_date['military_l'] - $first_date['military_l']),2) * (-1)) . "</td>\n";

        echo "\t\t\t<td>" . (($first['military_d_pts'] == -1 ||
                $last_date['military_d'] == $first_date['military_d']) ? '-' : round(($last['military_d_pts'] - $first['military_d_pts']) * 60 * 60 * 24 / ($last_date['military_d'] - $first_date['military_d']),2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['military_d_pts'] == -1 ||
                $last_date['military_d'] == $first_date['military_d']) ? '-' : round(($last['military_d_rank'] - $first['military_d_rank']) * 60 * 60 * 24 / ($last_date['military_d'] - $first_date['military_d']),2) * (-1)) . "</td>\n";

        echo "\t\t\t<td>" . (($first['honnor_pts'] == -1 ||
                $last_date['honnor'] == $first_date['honnor']) ? '-' : round(($last['honnor_pts'] - $first['honnor_pts']) * 60 * 60 * 24 / ($last_date['honnor'] - $first_date['honnor']),2)) . "</td>\n";

        echo "\t\t\t<td >" . (($first['honnor_pts'] == -1 ||
                $last_date['honnor'] == $first_date['honnor']) ? '-' : round(($last['honnor_rank'] - $first['honnor_rank']) * 60 * 60 * 24 / ($last_date['honnor'] - $first_date['honnor']),2) * (-1)) . "</td>\n";
        echo "</tr>\n";
?>
        </tbody>
    </table>
</div>

<?php
/// affichage des script de création graph
// camembert
echo $pie_point;
echo $pie_empire;

echo $curve;
?>


