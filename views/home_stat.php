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

$user_empire = player_get_empire($player_data['id']);
$player_building = $user_empire["building"];
$player_defense = $user_empire["defense"];
$user_technology = $user_empire["technology"];

if (!isset($pub_zoom) || !isset($pub_player_comp)) {
    $pub_player_comp = "";
    $pub_zoom = "";
}
if (!check_var($pub_zoom, "Char") || !check_var($pub_player_comp, "Text")) {
    redirection("index.php?action=message&amp;id_message=errordata&amp;info");
}

$zoom = $pub_zoom;
$player_comp = $pub_player_comp;

if (!isset($zoom)) {
    $zoom = "true";
}
if (isset($pub_zoom_change_y) && isset($pub_zoom_change_x)) {
    $zoom = ($zoom == "true" ? "false" : "true");
}
if (!isset($player_comp)) {
    $player_comp = "";
}

$individual_ranking = galaxy_show_ranking_unique_player($player_data["id"]);

ksort($individual_ranking);


$individual_ranking_2 = [];
if (!empty($player_comp)) {
    $playerId = (new Player_Model())->getPlayerId($player_comp);
    $individual_ranking_2 = galaxy_show_ranking_unique_player($playerId);
}

$dates = array_keys($individual_ranking);
$dates2 = array_keys($individual_ranking_2);
$dates = sizeof($dates) > sizeof($dates2) ? $dates : $dates2;


if (!empty($dates)) {
    $max_date = max($dates);
    $min_date = min($dates);

    if (isset($pub_start_date, $pub_end_date)) {
        // Valider et créer les objets DateTime à partir du format d/m/Y
        $start_dt = DateTime::createFromFormat('d/m/Y', trim($pub_start_date));
        $end_dt = DateTime::createFromFormat('d/m/Y', trim($pub_end_date));

        // Vérifier si les dates sont valides
        if ($start_dt && $end_dt) {
            // Modifier les dates de manière lisible
            $start_dt->setTime(22, 0, 0)->modify('-1 day');
            $end_dt->setTime(18, 0, 0);

            $min_ts = $start_dt->getTimestamp();
            $max_ts = $end_dt->getTimestamp();

            // Mettre à jour les dates si l'intervalle est valide
            if ($max_ts > $min_ts) {
                $max_date = $max_ts;
                $min_date = $min_ts;
            }
        }
    }
} else {
    $max_date = time();
    $min_date = time();
}


?>
<form method="get" action="index.php">
    <input type="hidden" name="action" value="home"/>
    <input type="hidden" name="subaction" value="stat"/>
    <input type="hidden" name="zoom" value="<?= $zoom; ?>"/>
    <table class="og-table og-little-table">
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
                <input type="text" name="user_stat_name" value="<?= $player_data["name"] ?>" readonly/>
            </td>
            <td>
                <input class="og-button og-button-little" type="submit" value="<?= $lang['HOME_STATS_GETSTATS'] ?>"/>
            </td>
        </tr>

        <tr>
            <td class="tdstat">
                <?= $lang['HOME_STATS_COMPARE'] ?>
            </td>
            <td>
                <input type="text" name="player_comp" value="<?= $player_comp ?>"/>
            </td>
            <td>
                <input class="og-button og-button-little" type="submit" value="<?= $lang['HOME_STATS_COMPARE'] ?>"/>
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
                <input type="text" maxlength="10" name="start_date"
                       value="<?= date("Y-m-d", $min_date + 60 * 60 * 2) ?>"/>
            </td>
            <td rowspan="2">
                <input class="og-button og-button-little" type="submit" value="<?= ($lang['HOME_STATS_SEND']) ?>"/>
            </td>

        </tr>
        <tr>
            <td class="tdstat">
                <?= $lang['HOME_STATS_TO'] ?>
            </td>
            <td>
                <input type="text" maxlength="10" name="end_date" value="<?= date("Y-m-d", $max_date) ?>"/>
            </td>
            <td class="tdstat">
            </td>
        </tr>
        </tbody>
        <thead>
        <tr>
            <th colspan='3'><?= $lang['HOME_STATS_OPTIONS'] ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tdstat">
                <?= ($lang['HOME_STATS_ZOOM']) ?>
            </td>
            <td></td>
            <td>
                <input class="og-button og-button-image og-button-image-large" type="image" name="zoom_change"
                       src="images/<?= ($zoom == "true" ? "zoom_in.png" : "zoom_out.png") ?>" alt="zoom"/>
            </td>

        </tr>
        </tbody>
    </table>
</form>

<?php
$first = [
    "general_pts" => null, "eco_pts" => null, "eco_rank" => null, "techno_pts" => null, "techno_rank" => null, "military_pts" => null, "military_rank" => null,
    "military_b_pts" => null, "military_b_rank" => null, "military_l_pts" => null, "military_l_rank" => null, "military_d_pts" => null, "military_d_rank" => null, "honor_pts" => null, "honor_rank" => null
];
$first_date = [];
$last_date = [];

$last = [
    "general_pts" => 0, "eco_pts" => 0, "techno_pts" => 0, "military_pts" => 0,
    "military_b_pts" => 0, "military_l_pts" => 0, "military_d_pts" => 0, "honor_pts" => 0,
    "general_rank" => 0, "eco_rank" => 0, "techno_rank" => 0, "military_rank" => 0,
    "military_b_rank" => 0, "military_l_rank" => 0, "military_d_rank" => 0, "honor_rank" => 0
];

$pie_point = false;
$pie_empire = false;

$tab_rank = "";
$rank_row = [];
$i = 0;
//Array ( [0] => Array ( [date] => 07 Jun 2025 16:00 [general_rank] =>   [general_points] =>   [eco_rank] =>   [eco_points] =>   [techno_rank] =>   [techno_points] =>   [military_rank] =>   [military_points] =>   [military_b_rank] =>   [military_b_points] =>   [military_l_rank] =>   [military_l_points] =>   [military_d_rank] =>   [military_d_points] =>   [honor_rank] =>   [honor_points] =>   ) [1] => Array ( [date] => 08 Jun 2025 16:00 [general_rank] =>   [general_points] =>   [eco_rank] =>   [eco_points] =>   [techno_rank] =>   [techno_points] =>   [military_rank] =>   [military_points] =>   [military_b_rank] =>   [military_b_points] =>   [military_l_rank] =>   [military_l_points] =>   [military_d_rank] =>   [military_d_points] =>   [honor_rank] =>   [honor_points] =>   ) [2] => Array ( [date] => 09 Jun 2025 16:00 [general_rank] =>   [general_points] =>   [eco_rank] =>   [eco_points] =>   [techno_rank] =>   [techno_points] =>   [military_rank] =>   [military_points] =>   [military_b_rank] =>   [military_b_points] =>   [military_l_rank] =>   [military_l_points] =>   [military_d_rank] =>   [military_d_points] =>   [honor_rank] =>   [honor_points] =>   ) [3] => Array ( [date] => 24 Jun 2025 16:00 [general_rank] =>   [general_points] =>   [eco_rank] =>   [eco_points] =>   [techno_rank] =>   [techno_points] =>   [military_rank] =>   [military_points] =>   [military_b_rank] =>   [military_b_points] =>   [military_l_rank] =>   [military_l_points] =>   [military_d_rank] =>   [military_d_points] =>   [honor_rank] =>   [honor_points] =>   ) )

$rankings = ["general", "eco", "techno", "military", "military_b", "military_l", "military_d", "honor"];

while ($ranking = current($individual_ranking)) {
    $v = key($individual_ranking);

    if ($v < $min_date || $v > $max_date) {
        next($individual_ranking);
        continue;
    }

    foreach ($rankings as $type) {
        // On ne traite que les classements qui ont des points non vides
        if (isset($ranking[$type]) && !empty(trim($ranking[$type]['points']))) {
            // Si c'est la première valeur valide qu'on trouve pour ce type
            if ($first[$type . "_pts"] === null) {
                $first[$type . "_pts"] = $ranking[$type]["points"];
                $first[$type . "_rank"] = $ranking[$type]["rank"];
                $first_date[$type] = $v;
            }

            // C'est la dernière valeur valide en date
            $last[$type . "_pts"] = $ranking[$type]["points"];
            $last[$type . "_rank"] = $ranking[$type]["rank"];
            $last_date[$type] = $v;
        }
    }

    $rank_row[$i]["date"] = date("d M Y H:i", $v);
    foreach ($rankings as $type) {
        $rank_row[$i][$type . "_rank"] = isset($ranking[$type]) ? formate_number($ranking[$type]["rank"]) : "&nbsp;";
        $rank_row[$i][$type . "_points"] = isset($ranking[$type]) ? formate_number($ranking[$type]["points"]) : "&nbsp;";
    }

    $i++;
    next($individual_ranking);
}

// on fabrique toutes les courbes ici
$curve_points = create_curves($player_data["name"], 'points', $min_date, $max_date, $player_comp);
$curve_rank = create_curves($player_data["name"], 'rank', $min_date, $max_date, $player_comp);

$title = $lang['HOME_STATS_GRAPHIC_TITLE'];
if (!empty($player_data["name"])) {
    $title .= " " . $lang['HOME_STATS_GRAPHIC_TITLE2'] . " " . $player_data["name"];
    if (!empty($last_date["general"])) {
        $title .= " " . $lang['HOME_STATS_GRAPHIC_FROM'] . " " . date("d M Y H:i", $last_date["general"]);
    }
}

$nb_planete = getPlanetCountForPlayer($player_data["id"]);

$buildings_total = round(all_building_cumulate(array_slice($player_building, 0, $nb_planete)) / 1000);
$defenses_total = round(all_defense_cumulate(array_slice($player_defense, 0, $nb_planete)) / 1000);
$moons_total = round(all_lune_cumulate(array_slice($player_building, $nb_planete, $nb_planete), array_slice($player_defense, $nb_planete, $nb_planete)) / 1000);
$technologies = round(all_technology_cumulate($user_technology) / 1000);
$fleets = $last["general_pts"] - $buildings_total - $defenses_total - $moons_total - $technologies; // on calcule la flotte restante :-)
if ($fleets < 0) {
    $fleets = 0;
}

if ($buildings_total != 0 || $defenses_total != 0 || $moons_total != 0 || $technologies != 0) {
    if ($last["general_pts"] == 0) {
        $fleets = round(all_fleet_cumulate($player_building) / 1000); // only FOR et Sat, pour le moment
    }
    $pie_point = create_pie($buildings_total . "_x_" . $defenses_total . "_x_" . $moons_total . "_x_" . $fleets . "_x_" . $technologies, "Batiments_x_Défenses_x_Lunes_x_Flotte_x_Technologies", $lang['HOME_STATS_GRAPHIC_LASTREPARTITION'], "pie_point");
}

$planet_values = [];
$planet_names = [];
foreach (array_slice($player_building, 0, $nb_planete) as $index => $planet_building) {
    $buildings = round(all_building_cumulate([$planet_building]) / 1000);
    $defenses = 0;
    if (isset($player_defense[$index])) {
        $defenses = round(all_defense_cumulate([$player_defense[$index]]) / 1000);
    }
    $moons = 0;
    if (isset($player_building[$index + $nb_planete]) && isset($player_defense[$index + $nb_planete])) {
        $moons = round(all_lune_cumulate([$player_building[$index + $nb_planete]], [$player_defense[$index + $nb_planete]]) / 1000);
    }
    if ($buildings != 0 || $defenses != 0 || $moons != 0) {
        $planet_values[] = $buildings + $defenses + $moons;
        $planet_names[] = $planet_building['name'];
    }
}

if (!empty($planet_values)) {
    $pie_empire = create_pie(
        implode('_x_', $planet_values),
        implode('_x_', $planet_names),
        $lang['HOME_STATS_GRAPHIC_REPARTITION'],
        "pie_empire"
    );
}

$title = $lang['HOME_STATS_GRAPHIC_TITLE'];
if (!empty($player_data["name"])) {
    $title .= " " . $lang['HOME_STATS_GRAPHIC_TITLE2'] . " " . $player_data["name"];
    if (!empty($last_date["general"])) {
        $title .= " " . $lang['HOME_STATS_GRAPHIC_FROM'] . " " . date("d M Y H:i", $last_date["general"]);
    }
}

?>
<h2>
    <?= $lang['HOME_STATS_PALYERSTATS'] . " " . $player_data["name"] ?>
</h2>

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
                <?= $lang['HOME_STATS_NOGRAPHIC'] ?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <div id="<?= "rank" ?>">
                <?= $lang['HOME_STATS_NOGRAPHIC'] ?>
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
    <tbody>
    <tr>
        <td style="width: 50%;">
            <div id="pie_point">
                <?= $lang['HOME_STATS_GRAPHIC_NOEMPIREDATA'] ?>
            </div>
        </td>
        <td style="width: 50%;">
            <div id="pie_empire">
                <?= $lang['HOME_STATS_GRAPHIC_NOEMPIREDATA'] ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>

<table class="og-table og-full-table og-table-ranking">
    <thead>
    <tr>
        <th colspan="17">
            <?= $lang['HOME_STATS_RANKING'] ?> <span class=og-highlight><?= $player_data["name"] ?></span>
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
                <?= $row["honor_points"] ?>
            </td>
            <td class="table-ranking-td-subrank">
                    <span class="ranking-subrank-number">
                        <?= $row["honor_rank"] ?>
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
            <?php if ($first["general_pts"] == null || $last_date["general"] == $first_date["general"]) : ?>
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
            <?php if ($first["general_rank"] == null || $last_date["general"] == $first_date["general"]) : ?>
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
            <?php if ($first["eco_pts"] == null || $last_date["eco"] == $first_date["eco"]) : ?>
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
            <?php if ($first["eco_rank"] == null || $last_date["eco"] == $first_date["eco"]) : ?>
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
            <?php if ($first["techno_pts"] == null || $last_date["techno"] == $first_date["techno"]) : ?>
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
            <?php if ($first["techno_rank"] == null || $last_date["techno"] == $first_date["techno"]) : ?>
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
            <?php if ($first["military_pts"] == null || $last_date["military"] == $first_date["military"]) : ?>
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
            <?php if ($first["military_rank"] == null || $last_date["military"] == $first_date["military"]) : ?>
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
            <?php if ($first["military_b_pts"] == null || $last_date["military_b"] == $first_date["military_b"]) : ?>
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
            <?php if ($first["military_b_rank"] == null || $last_date["military_b"] == $first_date["military_b"]) : ?>
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
            <?php if ($first["military_l_pts"] == null || $last_date["military_l"] == $first_date["military_l"]) : ?>
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
            <?php if ($first["military_l_rank"] == null || $last_date["military_l"] == $first_date["military_l"]) : ?>
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
            <?php if ($first["military_d_pts"] == null || $last_date["military_d"] == $first_date["military_d"]) : ?>
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
            <?php if ($first["military_d_rank"] == null || $last_date["military_d"] == $first_date["military_d"]) : ?>
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
            <?php if ($first["honor_pts"] == null || $last_date["honor"] == $first_date["honor"]) : ?>
                -
            <?php else : ?>
                <?php $prog = ($last["honor_pts"] - $first["honor_pts"]) * 60 * 60 * 24 / ($last_date["honor"] - $first_date["honor"]); ?>
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
            <?php if ($first["honor_rank"] == null || $last_date["honor"] == $first_date["honor"]) : ?>
                -
            <?php else : ?>
                <?php $prog = ($last["honor_rank"] - $first["honor_rank"]) * 60 * 60 * 24 / ($last_date["honor"] - $first_date["honor"]); ?>
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
// On affiche les graphiques en incluant les scripts nécessaires
echo $curve_points ?? '';
echo $curve_rank ?? '';
echo $pie_point ?? '';
echo $pie_empire ?? '';
?>
