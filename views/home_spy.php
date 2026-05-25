<?php global $lang;

/**
 * Affichage Empire - Pages Espionnages favoris
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

$favorites = user_getfavorites_spy();
if (!isset($sort2)) {
    $sort2 = 0;
} else {
    $sort2 = $sort2 != 0 ? 0 : 1;
}

// Retrieve active filter values (set via extract from GET in common.php)
$filter_galaxy        = isset($pub_filter_galaxy)        ? (string)$pub_filter_galaxy        : '';
$filter_system        = isset($pub_filter_system)        ? (string)$pub_filter_system        : '';
$filter_row           = isset($pub_filter_row)           ? (string)$pub_filter_row           : '';
$filter_date_from     = isset($pub_filter_date_from)     ? (string)$pub_filter_date_from     : '';
$filter_date_to       = isset($pub_filter_date_to)       ? (string)$pub_filter_date_to       : '';
$filter_resources_min = isset($pub_filter_resources_min) ? (string)$pub_filter_resources_min : '';
$filter_defense       = isset($pub_filter_defense)       ? (string)$pub_filter_defense       : '';
$filter_incomplete    = isset($pub_filter_incomplete)    ? (string)$pub_filter_incomplete    : '';

$has_active_filter = ($filter_galaxy !== '' || $filter_system !== '' || $filter_row !== ''
    || $filter_date_from !== '' || $filter_date_to !== ''
    || $filter_resources_min !== ''
    || ($filter_defense !== '' && $filter_defense !== 'all')
    || $filter_incomplete === '1');

// Build a query-string fragment to preserve active filters in sort links
$filter_qs = '';
foreach ([
    'filter_galaxy'        => $filter_galaxy,
    'filter_system'        => $filter_system,
    'filter_row'           => $filter_row,
    'filter_date_from'     => $filter_date_from,
    'filter_date_to'       => $filter_date_to,
    'filter_resources_min' => $filter_resources_min,
    'filter_defense'       => $filter_defense,
    'filter_incomplete'    => $filter_incomplete,
] as $key => $val) {
    if ($val !== '') {
        $filter_qs .= '&amp;' . $key . '=' . urlencode($val);
    }
}
?>

<div class="og-spy-layout">

    <!-- ============================================================ -->
    <!-- Filter sidebar                                               -->
    <!-- ============================================================ -->
    <aside class="og-spy-filters">
        <h3 class="og-spy-filters-title"><?= $lang['HOME_SPY_FILTER_TITLE'] ?></h3>

        <form method="get" action="index.php" class="og-spy-filter-form">
            <input type="hidden" name="action" value="home">
            <input type="hidden" name="subaction" value="spy">

            <!-- Coordinates -->
            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_SPY_FILTER_COORDS'] ?></legend>
                <div class="og-spy-filter-row">
                    <label for="spyf_galaxy"><?= $lang['HOME_SPY_FILTER_GALAXY'] ?></label>
                    <input id="spyf_galaxy" type="number" name="filter_galaxy" min="1"
                           value="<?= htmlspecialchars($filter_galaxy) ?>" placeholder="–">
                </div>
                <div class="og-spy-filter-row">
                    <label for="spyf_system"><?= $lang['HOME_SPY_FILTER_SYSTEM'] ?></label>
                    <input id="spyf_system" type="number" name="filter_system" min="1"
                           value="<?= htmlspecialchars($filter_system) ?>" placeholder="–">
                </div>
                <div class="og-spy-filter-row">
                    <label for="spyf_row"><?= $lang['HOME_SPY_FILTER_ROW'] ?></label>
                    <input id="spyf_row" type="number" name="filter_row" min="1" max="15"
                           value="<?= htmlspecialchars($filter_row) ?>" placeholder="–">
                </div>
            </fieldset>

            <!-- Date range -->
            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_SPY_FILTER_DATE'] ?></legend>
                <div class="og-spy-filter-row">
                    <label for="spyf_date_from"><?= $lang['HOME_SPY_FILTER_DATE_FROM'] ?></label>
                    <input id="spyf_date_from" type="date" name="filter_date_from"
                           value="<?= htmlspecialchars($filter_date_from) ?>">
                </div>
                <div class="og-spy-filter-row">
                    <label for="spyf_date_to"><?= $lang['HOME_SPY_FILTER_DATE_TO'] ?></label>
                    <input id="spyf_date_to" type="date" name="filter_date_to"
                           value="<?= htmlspecialchars($filter_date_to) ?>">
                </div>
            </fieldset>

            <!-- Resources -->
            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_SPY_FILTER_RESOURCES'] ?></legend>
                <div class="og-spy-filter-row">
                    <label for="spyf_res_min"><?= $lang['HOME_SPY_FILTER_RESOURCES_MIN'] ?></label>
                    <input id="spyf_res_min" type="number" name="filter_resources_min" min="0"
                           value="<?= htmlspecialchars($filter_resources_min) ?>" placeholder="0">
                </div>
            </fieldset>

            <!-- Defense -->
            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_SPY_FILTER_DEFENSE'] ?></legend>
                <div class="og-spy-filter-row">
                    <label>
                        <input type="radio" name="filter_defense" value="all"
                            <?= ($filter_defense === '' || $filter_defense === 'all') ? 'checked' : '' ?>>
                        <?= $lang['HOME_SPY_FILTER_ALL'] ?>
                    </label>
                </div>
                <div class="og-spy-filter-row">
                    <label>
                        <input type="radio" name="filter_defense" value="yes"
                            <?= ($filter_defense === 'yes') ? 'checked' : '' ?>>
                        <?= $lang['HOME_SPY_FILTER_DEFENSE_YES'] ?>
                    </label>
                </div>
                <div class="og-spy-filter-row">
                    <label>
                        <input type="radio" name="filter_defense" value="no"
                            <?= ($filter_defense === 'no') ? 'checked' : '' ?>>
                        <?= $lang['HOME_SPY_FILTER_DEFENSE_NO'] ?>
                    </label>
                </div>
                <div class="og-spy-filter-row">
                    <label>
                        <input type="radio" name="filter_defense" value="unknown"
                            <?= ($filter_defense === 'unknown') ? 'checked' : '' ?>>
                        <?= $lang['HOME_SPY_FILTER_DEFENSE_UNKNOWN'] ?>
                    </label>
                </div>
            </fieldset>

            <!-- Incomplete reports -->
            <fieldset class="og-spy-filter-group">
                <legend><?= $lang['HOME_SPY_FILTER_REPORT'] ?></legend>
                <div class="og-spy-filter-row">
                    <label>
                        <input type="checkbox" name="filter_incomplete" value="1"
                            <?= ($filter_incomplete === '1') ? 'checked' : '' ?>>
                        <?= $lang['HOME_SPY_FILTER_INCOMPLETE'] ?>
                    </label>
                </div>
            </fieldset>

            <div class="og-spy-filter-actions">
                <button type="submit" class="og-button"><?= $lang['HOME_SPY_FILTER_APPLY'] ?></button>
                <a href="index.php?action=home&amp;subaction=spy" class="og-button og-button-warning"><?= $lang['HOME_SPY_FILTER_RESET'] ?></a>
            </div>
        </form>
    </aside>

    <!-- ============================================================ -->
    <!-- Reports table                                                -->
    <!-- ============================================================ -->
    <div class="og-spy-content">
        <?php if ($has_active_filter) : ?>
            <p class="og-spy-filter-active"><?= $lang['HOME_SPY_FILTER_ACTIVE'] ?> – <a href="index.php?action=home&amp;subaction=spy"><?= $lang['HOME_SPY_FILTER_RESET'] ?></a></p>
        <?php endif; ?>

        <table class="og-table og-medium-table">
            <thead>
            <tr>
                <th>
                    <a href="index.php?action=home&amp;subaction=spy&amp;sort=1&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_SPY_POSITIONS'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=spy&amp;sort=2&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_SPY_ALLIANCES'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=spy&amp;sort=3&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_SPY_PLAYERS'] ?>
                    </a>
                </th>
                <th>
                    <a href="index.php?action=home&amp;subaction=spy&amp;sort=4&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_SPY_MOON'] ?>
                    </a>
                </th>
                <th>&nbsp;</th>
                <th>
                    <a href="index.php?action=home&amp;subaction=spy&amp;sort=5&amp;sort2=<?= $sort2 ?><?= $filter_qs ?>">
                        <?= $lang['HOME_SPY_UPDATE'] ?>
                    </a>
                </th>
                <th><?= $lang['HOME_SPY_RESOURCES_TOTAL'] ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($favorites as $v) : ?>
                <tr>
                    <td>
                        <?= $v["spy_galaxy"] . ":" . $v["spy_system"] . ":" . $v["spy_row"] ?>
                    </td>
                    <td>
                        <?php if ($v["ally"] == "") : ?>
                            &nbsp;
                        <?php else : ?>
                            <a href='index.php?action=search&amp;type_search=ally&amp;string_search=<?= $v["ally"] ?>&strict=on'>
                                <?= $v["ally"] ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($v["player"] == "") : ?>
                            &nbsp;
                        <?php else : ?>
                            <a href='index.php?action=search&amp;type_search=player&amp;string_search=<?= $v["player"] ?>&strict=on'>
                                <?= $v["player"] ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($v["moon"] == "") : ?>
                            &nbsp;
                        <?php else : ?>
                            <span class="ogame-icon-moon">M</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($v["status"] == "") : ?>
                            &nbsp;
                        <?php else : ?>
                            <span class="ogame-status-<?= $v["status"] ?>">
                                <?= $v["status"] ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($v["datadate"] != 0) : ?>
                            <?= date("d M Y H:i", $v["datadate"]) . " - " . $v["poster"] ?>
                        <?php else : ?>
                            <?= $v["poster"] ?>
                        <?php endif; ?>
                    </td>
                    <td class="og-spy-resources">
                        <?php if ($v["total_resources"] >= 0) : ?>
                            <?= number_format($v["total_resources"], 0, ',', '.') ?>
                        <?php else : ?>
                            &ndash;
                        <?php endif; ?>
                    </td>
                    <td>
                        <input class="og-button og-button-little" type='button' value='<?= $lang['HOME_SPY_SEE'] ?>'
                               onclick="window.open('index.php?action=show_reportspy&amp;galaxy=<?= $v['spy_galaxy'] ?>&amp;system=<?= $v['spy_system'] ?>&amp;row=<?= $v['spy_row'] ?>&amp;spy_id=<?= $v['spy_id'] ?>','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)"/>
                    </td>
                    <td>
                        <!-- todo del_favorite_spy ne semble pas fonctionner -->
                        <input class="og-button og-button-danger og-button-little" type='button'
                               value='<?= $lang['HOME_SPY_FAVDELETE'] ?>'
                               onclick="window.location = 'index.php?action=del_favorite_spy&amp;spy_id=<?= $v['spy_id'] ?>&amp;info=1';"/>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div><!-- .og-spy-content -->

</div><!-- .og-spy-layout -->
