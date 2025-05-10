<?php global $user_data, $lang;

/**
 * Affichage Empire - Page Statistiques
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Ben.12
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
//integré dasn le common.php
//require "includes/ogame.php";

if (
    !isset($pub_zoom) || !isset($pub_user_stat_name) || !isset($pub_player_comp) ||
    !isset($pub_user_stat_name)
) {
    $pub_user_stat_name = "";
    $pub_player_comp = "";
    $pub_user_stat_name = "";
    $pub_zoom = "";
}
if (
    !check_var($pub_zoom, "Char") || !check_var($pub_player_comp, "Text") || !check_var($pub_user_stat_name, "Text")
) {
    redirection("index.php?action=message&amp;id_message=errordata&amp;info");
}

$zoom = $pub_zoom;
$player_comp = $pub_player_comp;
$player_stat_name = $pub_player_stat_name;

if (!isset($zoom)) {
    $zoom = "true";
}
if (isset($pub_zoom_change_y) && isset($pub_zoom_change_x)) {
    $zoom = ($zoom == "true" ? "false" : "true");
}
if (!isset($player_comp)) {
    $player_comp = "";
}

$individual_ranking = galaxy_show_ranking_unique_player($player_data["user_stat_name"]);

ksort($individual_ranking);

$individual_ranking_2 = galaxy_show_ranking_unique_player($player_comp);

$dates = array_keys($individual_ranking);
$dates2 = array_keys($individual_ranking_2);
$dates = sizeof($dates) > sizeof($dates2) ? $dates : $dates2;

if (sizeof($dates) > 0) {
    $max_date = max($dates);
    $min_date = min($dates);

    if (
        isset($pub_start_date) && isset($pub_end_date) && preg_match(
            "/^(3[01]|[0-2][0-9]|[1-9])\/([1-9]|0[1-9]|1[012])\/(2[[:digit:]]{3})$/",
            trim($pub_start_date)
        ) && preg_match(
            "/^(3[01]|[0-2][0-9]|[1-9])\/([1-9]|0[1-9]|1[012])\/(2[[:digit:]]{3})$/",
            trim($pub_end_date)
        )
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

<table class="og-table og-little-table">
    <form method="get" action="index.php">
        <input type="hidden" name="action" value="home" />
        <input type="hidden" name="subaction" value="stat" />
        <input type="hidden" name="zoom" value="<?= $zoom; ?>" />
        <thead>
            <tr>
                <th colspan='3'><?= $lang['HOME_STATS_STATISTICS'] ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat">
                    <?= $lang['HOME_STATS_STATISTICS'] ?>
                </td>
                <td>
                    <input type="text" name="user_stat_name" value="<?= $player_data["name"] ?>" />
                </td>
                <td>
                    <input class="og-button og-button-little" type="submit" value="<?= $lang['HOME_STATS_GETSTATS'] ?>" />
                </td>
            </tr>

            <tr>
                <td class="tdstat">
                    <?= $lang['HOME_STATS_COMPARE'] ?>
                </td>
                <td>
                    <input type="text" name="player_comp" value="<?= $player_comp ?>" />
                </td>
                <td>
                    <input class="og-button og-button-little" type="submit" value="<?= $lang['HOME_STATS_COMPARE'] ?>" />
                </td>
            </tr>

        </tbody>
        <thead>
            <th colspan='3'><?= $lang['HOME_STATS_INTERVAL'] ?></th>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat">
                    <?= $lang['HOME_STATS_FROM'] ?>
                </td>
                <td>
                    <input type="text" maxlength="10" name="start_date" value="<?= date("Y-m-d", $min_date + 60 * 60 * 2) ?>" />
                </td>
                <td rowspan="2">
                    <input class="og-button og-button-little" type="submit" value="<?= ($lang['HOME_STATS_SEND']) ?>" />
                </td>

            </tr>
            <tr>
                <td class="tdstat">
                    <?= $lang['HOME_STATS_TO'] ?>
                </td>
                <td>
                    <input type="text" maxlength="10" name="end_date" value="<?= date("Y-m-d", $max_date) ?>" />
                </td>
            </tr>
        </tbody>
        <thead>
            <th colspan='3'><?= $lang['HOME_STATS_OPTIONS'] ?></th>
        </thead>
        <tbod>
            <tr>
                <td class="tdstat">
                    <?= ($lang['HOME_STATS_ZOOM']) ?>
                </td>
                <td></td>
                <td>
                    <input class="og-button og-button-image og-button-image-large" type="image" name="zoom_change" src="images/<?= ($zoom == "true" ? "zoom_in.png" : "zoom_out.png") ?>" alt="zoom" />
                </td>

            </tr>
        </tbod>
    </form>
</table>

<!--
<div style="display:flex; flex-direction: column; justify-content: center;">
    <form method="get" action="index.php">
        <input type="hidden" name="action" value="home" />
        <input type="hidden" name="subaction" value="stat" />
        <input type="hidden" name="zoom" value="<?= $zoom; ?>" />
        <table>
            <tr>
                <td class='c'><?= $lang['HOME_STATS_STATISTICS'] ?></td>
                <td class='c' colspan='2'><?= $lang['HOME_STATS_OPTIONS']; ?></td>
            </tr>
            <tr>
                <th><input type="text" name="user_stat_name" value="<?= $player_data["name"] ?>" />
                    <input type="submit" value="<?= $lang['HOME_STATS_GETSTATS'] ?>" />
                </th>
                <th rowspan="2"><span style="text-decoration: underline;"><?= $lang['HOME_STATS_INTERVAL'] ?></span> : <?= $lang['HOME_STATS_FROM'] ?>
                    <input type="text" size="10" maxlength="10" name="start_date" value="<?= date("Y-m-d", $min_date + 60 * 60 * 2) ?>" />
                    <?= ($lang['HOME_STATS_TO']) ?>
                    <input type="text" size="10" maxlength="10" name="end_date" value="<?= date("Y-m-d", $max_date) ?>" />
                    <input type="submit" value="<?= ($lang['HOME_STATS_SEND']) ?>" />
                </th>
                <th rowspan="2"><?= ($lang['HOME_STATS_ZOOM']) ?> : <input type="image" align="absmiddle" name="zoom_change" src="images/<?= ($zoom == "true" ? "zoom_in.png" : "zoom_out.png") ?>" alt="zoom" />
                </th>
            </tr>
            <tr>
                <th><input type="text" name="player_comp" value="<?= $player_comp ?>" />
                    <input type="submit" value="<?= ($lang['HOME_STATS_COMPARE']) ?>" />
                </th>
            </tr>
        </table>
    </form>
-->
<?php
$first = [
    "general_pts" => -1, "eco_pts" => -1, "techno_pts" => -1, "military_pts" => -1,
    "military_b_pts" => -1, "military_l_pts" => -1, "military_d_pts" => -1, "honnor_pts" => -1
];
$last = [
    "general_pts" => 0, "eco_pts" => 0, "techno_pts" => 0, "military_pts" => 0,
    "military_b_pts" => 0, "military_l_pts" => 0, "military_d_pts" => 0, "honnor_pts" => 0,
    "general_rank" => 0, "eco_rank" => 0, "techno_rank" => 0, "military_rank" => 0,
    "military_b_rank" => 0, "military_l_rank" => 0, "military_d_rank" => 0, "honnor_rank" => 0
];
$tab_rank = "";
$rank_row = [];
$i = 0;



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

    $rank_row[$i]["date"] = date("d M Y H:i", $v);
    $rankings = ["general", "eco", "techno", "military", "military_b", "military_l", "military_d", "honnor"];
    foreach ($rankings as $ranking) {
        $rank_row[$i][$ranking . "_rank"] = isset($ranking[$ranking]) ? formate_number($ranking[$ranking]["rank"]) : "&nbsp;";
        $rank_row[$i][$ranking . "_points"] = isset($ranking[$ranking]) ? formate_number($ranking[$ranking]["points"]) : "&nbsp;";
    }
    $i++;
    next($individual_ranking);
}
?>
<h2>
    <?= $lang['HOME_STATS_PALYERSTATS'] . " " . $player_data["name"] ?>
</h2>

<?php
// on fabrique toutes les courbes ici
//
global $zoom;
$zoom = 'false';
$curve = create_curves($player_data["name"], $min_date, $max_date, $player_comp);

$title = $lang['HOME_STATS_GRAPHIC_TITLE'];
if (!empty($player_data["name"])) {
    $title .= " " . $lang['HOME_STATS_GRAPHIC_TITLE2'] . " " . $player_data["name"];
    if (!empty($last_date["general"])) {
        $title .= " " . $lang['HOME_STATS_GRAPHIC_FROM'] . " " . date("d M Y H:i", $last_date["general"]);
    }
}

$user_empire = user_get_empire($user_data["id"]);
$user_building = $user_empire["building"];
$user_defence = $user_empire["defence"];
$user_technology = $user_empire["technology"];

$nb_planete = find_nb_planete_user($user_data["id"]);

$b = round(all_building_cumulate(array_slice($user_building, 0, $nb_planete)) / 1000);
$d = round(all_defence_cumulate(array_slice($user_defence, 0, $nb_planete)) / 1000);
$l = round(all_lune_cumulate(array_slice($user_building, $nb_planete, $nb_planete), array_slice($user_defence, $nb_planete, $nb_planete)) / 1000);
$t = round(all_technology_cumulate($user_technology) / 1000);
$f = $last["general_pts"] - $b - $d - $l - $t;
if ($f < 0) {
    $f = 0;
}


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
<!-- positionnement des graphiques -->

<table class="og-table og-full-table og-table-ranking">
    <thead>
        <tr>
            <th colspan="2">
                <?php if ($player_comp != "" && isset($player_comp)) : ?>
                    <?= ($lang['HOME_STATS_COMP']) ?> <?= $player_data["name"] ?>
                <?php else : ?>
                    <?= ($lang['HOME_STATS_RANKINGS']) ?> <?= $player_data["name"] ?>
                <?php endif; ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan='2'>

                <div id="<?= "points" ?>">
                    <?= ($lang['HOME_STATS_NOGRAPHIC']) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <div id="<?= "rank" ?>">
                    <?= ($lang['HOME_STATS_NOGRAPHIC']) ?>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<table class="og-table og-full-table og-table-ranking">
    <thead>
        <tr>
            <th colspan="2">
                <?= $lang['HOME_STATS_GRAPHIC_DIVERS'] . " " . help(null, $title) ?>
            </th>
       </tr>
    </thead>
    <tr>
        <td >
            <div id="pie_point">
                <?php if ($b == 0 && $d == 0 && $l == 0 && $t == 0) : ?>
                    <?php // calcul impossible ( non connaissance du classement)
                    echo $lang['HOME_STATS_GRAPHIC_NOEMPIREDATA']; ?>

                <?php else : ?>
                    <?php if ($last["general_pts"] == 0) : ?>
                        <?= $lang['HOME_STATS_GRAPHIC_NOSTATSDATA'] . "<br>" ?>
                        <?php $f = round(all_fleet_cumulate($user_building) / 1000); ?> <!-- only FOR et Sat, pour le moment-->
                    <?php endif; ?>
                    <?php $pie_point = create_pie($b . "_x_" . $d . "_x_" . $l . "_x_" . $f . "_x_" . $t, "Batiments_x_Défenses_x_Lunes_x_Flotte_x_Technologies", $lang['HOME_STATS_GRAPHIC_LASTREPARTITION'], "pie_point"); ?>

                <?php endif; ?>
            </div>
        </td>
        <td >
            <div id="pie_empire">
                <?php if ($b == 0 && $d == 0 && $l == 0 && $t == 0) : ?>
                    <?php // pas d info
                    ?>
                    <?= $lang['HOME_STATS_GRAPHIC_NOEMPIREDATA'];; ?>
                <?php else : ?>
                    <?php // autrement on affiche rien : on prepare juste l affichage du script
                    ?>
                    <?php $pie_empire = create_pie(
                        implode('_x_', $planet),
                        implode('_x_', $planet_name),
                        $lang['HOME_STATS_GRAPHIC_REPARTITION'],
                        "pie_empire"
                    ); ?>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>



<table class="og-table og-full-table og-table-ranking">
    <thead>
        <tr>
            <th colspan="17">
                <?= ($lang['HOME_STATS_RANKING']) ?> <span class=og-highlight><?= $player_data["name"] ?></span>
            </th>
        </tr>
        <tr>
            <th><?= $lang['HOME_STATS_DATE'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_GENERAL'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_ECO'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_RESEARCH'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_MILITARY'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_MILITARYBUILT'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_MILITARYLOST'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_MILITARYDEST'] ?></th>
            <th colspan="2"><?= $lang['HOME_STATS_PTS_HONOR'] ?></th>
        </tr>
    </thead>
    <tbody>
        <!-- affichage classement -->
        <?php foreach ($rank_row as $row) : ?>
            <tr>
                <td>
                    <?= $row["date"] ?>
                </td>
                <td>
                    <?= $row["general_points"] ?>
                </td>
                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["general_rank"] ?>
                    </span>
                </td>
                <td>
                    <?= $row["eco_points"] ?>
                </td>
                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["eco_rank"] ?>
                    </span>
                </td>
                <td>
                    <?= $row["techno_points"] ?>
                </td>
                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["techno_rank"] ?>
                    </span>
                </td>
                <td>
                    <?= $row["military_points"] ?>
                </td>

                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["military_rank"] ?>
                    </span>
                </td>

                <td>
                    <?= $row["military_b_points"] ?>
                </td>
                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["military_b_rank"] ?>
                    </span>
                </td>
                <td>
                    <?= $row["military_l_points"] ?>
                </td>
                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">

                        <?= $row["military_l_rank"] ?>
                    </span>
                </td>
                <td>
                    <?= $row["military_d_points"] ?>
                </td>
                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["military_d_rank"] ?>
                    </span>
                </td>
                <td>
                    <?= $row["honnor_points"] ?>
                </td>
                <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["honnor_rank"] ?>
                    </span>
                </td>

            </tr>
        <?php endforeach; ?>
        <!-- affichage Progression -->
        <tr>
            <td>
                <span class="og-highlight">
                    <?= $lang['HOME_STATS_PROGRESS_RATE'] ?>
                </span>
            </td>
            <td>
                <?php if ($first["general_pts"] == -1 ||  $last_date["general"] == $first_date["general"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["general_pts"] - $first["general_pts"]) * 60 * 60 * 24 / ($last_date["general"] - $first_date["general"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["general_rank"] == -1 ||  $last_date["general"] == $first_date["general"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["general_rank"] - $first["general_rank"]) * 60 * 60 * 24 / ($last_date["general"] - $first_date["general"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>

            </td>
            <td>
                <?php if ($first["eco_pts"] == -1 ||  $last_date["eco"] == $first_date["eco"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["eco_pts"] - $first["eco_pts"]) * 60 * 60 * 24 / ($last_date["eco"] - $first_date["eco"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["eco_rank"] == -1 ||  $last_date["eco"] == $first_date["eco"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["eco_rank"] - $first["eco_rank"]) * 60 * 60 * 24 / ($last_date["eco"] - $first_date["eco"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["techno_pts"] == -1 ||  $last_date["techno"] == $first_date["techno"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["techno_pts"] - $first["techno_pts"]) * 60 * 60 * 24 / ($last_date["techno"] - $first_date["techno"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["techno_rank"] == -1 ||  $last_date["techno"] == $first_date["techno"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["techno_rank"] - $first["techno_rank"]) * 60 * 60 * 24 / ($last_date["techno"] - $first_date["techno"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["military_pts"] == -1 ||  $last_date["military"] == $first_date["military"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_pts"] - $first["military_pts"]) * 60 * 60 * 24 / ($last_date["military"] - $first_date["military"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["military_rank"] == -1 ||  $last_date["military"] == $first_date["military"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_rank"] - $first["military_rank"]) * 60 * 60 * 24 / ($last_date["military"] - $first_date["military"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>

            <td>
                <?php if ($first["military_b_pts"] == -1 ||  $last_date["military_b"] == $first_date["military_b"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_b_pts"] - $first["military_b_pts"]) * 60 * 60 * 24 / ($last_date["military_b"] - $first_date["military_b"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["military_b_rank"] == -1 ||  $last_date["military_b"] == $first_date["military_b"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_b_rank"] - $first["military_b_rank"]) * 60 * 60 * 24 / ($last_date["military_b"] - $first_date["military_b"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["military_l_pts"] == -1 ||  $last_date["military_l"] == $first_date["military_l"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_l_pts"] - $first["military_l_pts"]) * 60 * 60 * 24 / ($last_date["military_l"] - $first_date["military_l"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["military_l_rank"] == -1 ||  $last_date["military_l"] == $first_date["military_l"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_l_rank"] - $first["military_l_rank"]) * 60 * 60 * 24 / ($last_date["military_l"] - $first_date["military_l"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["military_d_pts"] == -1 ||  $last_date["military_d"] == $first_date["military_d"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_d_pts"] - $first["military_d_pts"]) * 60 * 60 * 24 / ($last_date["military_d"] - $first_date["military_d"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["military_d_rank"] == -1 ||  $last_date["military_d"] == $first_date["military_d"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["military_d_rank"] - $first["military_d_rank"]) * 60 * 60 * 24 / ($last_date["military_d"] - $first_date["military_d"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["honnor_pts"] == -1 ||  $last_date["honnor"] == $first_date["honnor"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["honnor_pts"] - $first["honnor_pts"]) * 60 * 60 * 24 / ($last_date["honnor"] - $first_date["honnor"]); ?>
                    <?php if ($prog < 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($first["honnor_rank"] == -1 ||  $last_date["honnor"] == $first_date["honnor"]) : ?>
                    -
                <?php else : ?>
                    <?php $prog = ($last["honnor_rank"] - $first["honnor_rank"]) * 60 * 60 * 24 / ($last_date["honnor"] - $first_date["honnor"]); ?>
                    <?php if ($prog > 0) : ?>
                        <span class="og-alert">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php else : ?>
                        <span class="og-success">
                            <?= round($prog, 2) ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>



        </tr>
    </tbody>




</table>


<?php
/// affichage des script de création graph
// camembert
echo $pie_point;
echo $pie_empire;


echo $curve;
?>
