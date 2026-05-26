<?php

use Ogsteam\Ogspy\Model\Player_Model;

global $lang, $user_data;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$player_data = (new Player_Model())->get_player_data($user_data['player_id']);
if (empty($player_data)) {
    echo '<div class="og-msg og-msg-warning ">' .
        '<h3 class="og-title">' . $lang['MSG_SYSTEM'] . '</h3>' .
        '<p class="og-content">' . $lang['MSG_EMPIRE_DATA_FAILURE'] . '</p>' .
        '</div>';
    return;
}

$reports = user_getempire_combat_reports();
if (!isset($sort2)) {
    $sort2 = 0;
} else {
    $sort2 = $sort2 != 0 ? 0 : 1;
}

$filter_galaxy         = isset($pub_rc_filter_galaxy) ? (string)$pub_rc_filter_galaxy : '';
$filter_system         = isset($pub_rc_filter_system) ? (string)$pub_rc_filter_system : '';
$filter_row            = isset($pub_rc_filter_row) ? (string)$pub_rc_filter_row : '';
$filter_date_from      = isset($pub_rc_filter_date_from) ? (string)$pub_rc_filter_date_from : '';
$filter_date_to        = isset($pub_rc_filter_date_to) ? (string)$pub_rc_filter_date_to : '';
$filter_gains_min      = isset($pub_rc_filter_gains_min) ? (string)$pub_rc_filter_gains_min : '';
$filter_gains_max      = isset($pub_rc_filter_gains_max) ? (string)$pub_rc_filter_gains_max : '';
$filter_losses_min     = isset($pub_rc_filter_losses_min) ? (string)$pub_rc_filter_losses_min : '';
$filter_losses_max     = isset($pub_rc_filter_losses_max) ? (string)$pub_rc_filter_losses_max : '';
$filter_debris_min     = isset($pub_rc_filter_debris_min) ? (string)$pub_rc_filter_debris_min : '';
$filter_debris_max     = isset($pub_rc_filter_debris_max) ? (string)$pub_rc_filter_debris_max : '';
$filter_rounds_min     = isset($pub_rc_filter_rounds_min) ? (string)$pub_rc_filter_rounds_min : '';
$filter_rounds_max     = isset($pub_rc_filter_rounds_max) ? (string)$pub_rc_filter_rounds_max : '';
$filter_hide_one_round = isset($pub_rc_filter_hide_one_round) ? (string)$pub_rc_filter_hide_one_round : '';

$has_active_filter = ($filter_galaxy !== '' || $filter_system !== '' || $filter_row !== ''
    || $filter_date_from !== '' || $filter_date_to !== ''
    || $filter_gains_min !== '' || $filter_gains_max !== ''
    || $filter_losses_min !== '' || $filter_losses_max !== ''
    || $filter_debris_min !== '' || $filter_debris_max !== ''
    || $filter_rounds_min !== '' || $filter_rounds_max !== ''
    || $filter_hide_one_round === '1');

$filter_qs = '';
foreach ([
    'rc_filter_galaxy' => $filter_galaxy,
    'rc_filter_system' => $filter_system,
    'rc_filter_row' => $filter_row,
    'rc_filter_date_from' => $filter_date_from,
    'rc_filter_date_to' => $filter_date_to,
    'rc_filter_gains_min' => $filter_gains_min,
    'rc_filter_gains_max' => $filter_gains_max,
    'rc_filter_losses_min' => $filter_losses_min,
    'rc_filter_losses_max' => $filter_losses_max,
    'rc_filter_debris_min' => $filter_debris_min,
    'rc_filter_debris_max' => $filter_debris_max,
    'rc_filter_rounds_min' => $filter_rounds_min,
    'rc_filter_rounds_max' => $filter_rounds_max,
    'rc_filter_hide_one_round' => $filter_hide_one_round,
] as $key => $val) {
    if ($val !== '') {
        $filter_qs .= '&amp;' . $key . '=' . urlencode($val);
    }
}
?>

<div class="og-spy-layout">
    <aside class="og-spy-filters">
        <h3 class="og-spy-filters-title">
            <?= $lang['HOME_SPY_FILTER_TITLE'] ?>
            <?php if ($has_active_filter) : ?>
                <span class="og-spy-filter-badge">ON</span>
            <?php endif; ?>
        </h3>

        <form method="get" action="index.php" class="og-spy-filter-form">
            <input type="hidden" name="action" value="home">
            <input type="hidden" name="subaction" value="combat">

            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_SPY_FILTER_COORDS'] ?></legend>
                <div class="og-spy-filter-row">
                    <label for="rcf_galaxy"><?= $lang['HOME_SPY_FILTER_GALAXY'] ?></label>
                    <input id="rcf_galaxy" type="number" name="rc_filter_galaxy" min="1"
                           value="<?= htmlspecialchars($filter_galaxy) ?>" placeholder="–">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_system"><?= $lang['HOME_SPY_FILTER_SYSTEM'] ?></label>
                    <input id="rcf_system" type="number" name="rc_filter_system" min="1"
                           value="<?= htmlspecialchars($filter_system) ?>" placeholder="–">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_row"><?= $lang['HOME_SPY_FILTER_ROW'] ?></label>
                    <input id="rcf_row" type="number" name="rc_filter_row" min="1" max="15"
                           value="<?= htmlspecialchars($filter_row) ?>" placeholder="–">
                </div>
            </fieldset>

            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_SPY_FILTER_DATE'] ?></legend>
                <div class="og-spy-filter-row">
                    <label for="rcf_date_from"><?= $lang['HOME_SPY_FILTER_DATE_FROM'] ?></label>
                    <input id="rcf_date_from" type="date" name="rc_filter_date_from"
                           value="<?= htmlspecialchars($filter_date_from) ?>">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_date_to"><?= $lang['HOME_SPY_FILTER_DATE_TO'] ?></label>
                    <input id="rcf_date_to" type="date" name="rc_filter_date_to"
                           value="<?= htmlspecialchars($filter_date_to) ?>">
                </div>
            </fieldset>

            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_COMBAT_FILTER_VALUES'] ?></legend>
                <div class="og-spy-filter-row">
                    <label for="rcf_gains_min"><?= $lang['HOME_COMBAT_FILTER_GAINS_MIN'] ?></label>
                    <input id="rcf_gains_min" type="number" name="rc_filter_gains_min" min="0"
                           value="<?= htmlspecialchars($filter_gains_min) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_gains_max"><?= $lang['HOME_COMBAT_FILTER_GAINS_MAX'] ?></label>
                    <input id="rcf_gains_max" type="number" name="rc_filter_gains_max" min="0"
                           value="<?= htmlspecialchars($filter_gains_max) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_losses_min"><?= $lang['HOME_COMBAT_FILTER_LOSSES_MIN'] ?></label>
                    <input id="rcf_losses_min" type="number" name="rc_filter_losses_min" min="0"
                           value="<?= htmlspecialchars($filter_losses_min) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_losses_max"><?= $lang['HOME_COMBAT_FILTER_LOSSES_MAX'] ?></label>
                    <input id="rcf_losses_max" type="number" name="rc_filter_losses_max" min="0"
                           value="<?= htmlspecialchars($filter_losses_max) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_debris_min"><?= $lang['HOME_COMBAT_FILTER_DEBRIS_MIN'] ?></label>
                    <input id="rcf_debris_min" type="number" name="rc_filter_debris_min" min="0"
                           value="<?= htmlspecialchars($filter_debris_min) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_debris_max"><?= $lang['HOME_COMBAT_FILTER_DEBRIS_MAX'] ?></label>
                    <input id="rcf_debris_max" type="number" name="rc_filter_debris_max" min="0"
                           value="<?= htmlspecialchars($filter_debris_max) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_rounds_min"><?= $lang['HOME_COMBAT_FILTER_ROUNDS_MIN'] ?></label>
                    <input id="rcf_rounds_min" type="number" name="rc_filter_rounds_min" min="0"
                           value="<?= htmlspecialchars($filter_rounds_min) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label for="rcf_rounds_max"><?= $lang['HOME_COMBAT_FILTER_ROUNDS_MAX'] ?></label>
                    <input id="rcf_rounds_max" type="number" name="rc_filter_rounds_max" min="0"
                           value="<?= htmlspecialchars($filter_rounds_max) ?>" placeholder="0">
                </div>
                <div class="og-spy-filter-row">
                    <label>
                        <input type="checkbox" name="rc_filter_hide_one_round" value="1"
                            <?= ($filter_hide_one_round === '1') ? 'checked' : '' ?>>
                        <?= $lang['HOME_COMBAT_FILTER_HIDE_ONE_ROUND'] ?>
                    </label>
                </div>
            </fieldset>

            <div class="og-spy-filter-actions">
                <button type="submit" class="og-button"><?= $lang['HOME_SPY_FILTER_APPLY'] ?></button>
                <a href="index.php?action=home&amp;subaction=combat" class="og-button og-button-warning"><?= $lang['HOME_SPY_FILTER_RESET'] ?></a>
            </div>
        </form>
    </aside>

    <div class="og-spy-content">
        <?php if ($has_active_filter) : ?>
            <p class="og-spy-filter-active"><?= $lang['HOME_SPY_FILTER_ACTIVE'] ?><a href="index.php?action=home&amp;subaction=combat"><?= $lang['HOME_SPY_FILTER_RESET'] ?></a></p>
        <?php endif; ?>

        <table class="og-table og-medium-table">
            <thead>
            <tr>
                <th>
                    <a href="index.php?action=home&amp;subaction=combat&amp;sort=1&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_SPY_POSITIONS'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=combat&amp;sort=2&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_COMBAT_DATE'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=combat&amp;sort=3&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_COMBAT_GAINS'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=combat&amp;sort=4&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_COMBAT_LOSSES'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=combat&amp;sort=5&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_COMBAT_DEBRIS'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=combat&amp;sort=6&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_COMBAT_ROUNDS'] ?>
                    </a>
                </th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php if (sizeof($reports) == 0) : ?>
                <tr>
                    <td colspan="7"><?= $lang['HOME_COMBAT_NOREPORTS'] ?></td>
                </tr>
            <?php endif; ?>
            <?php foreach ($reports as $report) : ?>
                <tr>
                    <td><?= $report['coordinates'] ?></td>
                    <td><?= date("d M Y H:i", (int)$report['dateRC']) ?></td>
                    <td><?= ((int)$report['total_gain'] >= 0) ? number_format((int)$report['total_gain'], 0, ',', '.') : '&ndash;' ?></td>
                    <td><?= number_format((int)$report['total_losses'], 0, ',', '.') ?></td>
                    <td><?= ((int)$report['total_debris'] >= 0) ? number_format((int)$report['total_debris'], 0, ',', '.') : '&ndash;' ?></td>
                    <td><?= (int)$report['nb_rounds'] ?></td>
                    <td>
                        <input class="og-button og-button-little" type="button" value="<?= $lang['HOME_COMBAT_SEE'] ?>"
                               onclick="window.open('index.php?action=show_reportrc&amp;galaxy=<?= (int)$report['galaxy'] ?>&amp;system=<?= (int)$report['system'] ?>&amp;row=<?= (int)$report['row'] ?>&amp;rc_id=<?= (int)$report['id_rc'] ?>','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
