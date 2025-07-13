<?php global $user_data, $lang;

/**
 * Panneau d'Administration : Paramètres et affichage des Journaux
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["admin"] != 1 && $user_data["coadmin"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

// Niveaux de log Monolog avec leur valeur numérique
$monolog_levels = [
    'EMERGENCY' => 600,
    'ALERT'     => 550,
    'CRITICAL'  => 500,
    'ERROR'     => 400,
    'WARNING'   => 300,
    'NOTICE'    => 250,
    'INFO'      => 200,
    'DEBUG'     => 100,
];

//Définition du niveau de log sélectionné
$log_level_filter = isset($pub_level) && isset($monolog_levels[$pub_level]) ? $pub_level : 'INFO';

//Définition de la date sélectionnée
if (!isset($pub_show)) {
    $show = date("Y~m~d");
} else {
    $show = $pub_show;
}

if (sizeof(explode("~", $show)) != 3) {
    $show = date("Y~m~d");
}
list($show_year, $show_month, $show_day) = explode("~", $show);
if (!checkdate($show_month, $show_day, $show_year)) {
    list($show_year, $show_month, $show_day) = explode("~", date("Y~m~d"));
}
$show = "$show_year-$show_month-$show_day";

if (!isset($pub_typelog)) {
    $typelog = "log";
} else {
    if (check_var($pub_typelog, "Char")) {
        $typelog = $pub_typelog;
    } else {
        $typelog = "log";
    }
}
if ($typelog != "log" && $typelog != "sql") {
    $typelog = "log";
}
if ($typelog == "log") {
    $file = "OGSpy-" . $show . ".log";
}
if ($typelog == "sql") {
    $file = "OGSpy-sql-" . $show . ".log";
}

//Récupération du log
$dir = $show;
$file = PATH_LOG . "/" . $file;

if (file_exists($file)) {
    $logFileName = file($file);
} else {
    $logFileName = array($lang['ADMIN_LOGS_NOLOGS']);
}

echo "<!--<a>" . $lang['ADMIN_LOGS_SELECTED_DATE'] .  date("d F o", mktime(0, 0, 0, $show_month, $show_day, $show_year)) . "</a>-->";
?>

<table class="og-table og-full-table">
    <thead>
        <tr>
            <th  colspan="12">
                <?= ($lang['ADMIN_LOGS_SELECTED_DATE'] .  date("d F o", mktime(0, 0, 0, $show_month, $show_day, $show_year))) ?></th>

        </tr>
    </thead>
    <tbody>
         <?php $date = mktime(0, 0, 0, date("n"), 1) - 60 * 60 * 24 * 365; ?>
    <tr> <!-- Mois -->
    <?php for ($i = 0; $i < 12; $i++)  :?>
        <?php
        $date += 60 * 60 * 24 * 31;
        $show_month_loop = date("m", $date);
        $show_year_loop = date("Y", $date);
        $show_param = $show_year_loop . "~" . $show_month_loop . "~" . $show_day;

        $log_exists = log_check_exist(date("Ym", $date));
        $is_selected_month = ($show_year_loop == $show_year && $show_month_loop == $show_month);

        $cell_class = '';
        if ($log_exists) {
            $cell_class = 'og-success';
        }
        if ($is_selected_month) {
            $cell_class .= ' og-highlight';
        }
        ?>
        <td colspan="2" class="<?= $cell_class ?>" style="cursor: pointer;" onclick="window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=<?= $show_param ?>&amp;typelog=<?= $typelog ?>&level=<?= $log_level_filter ?>';">
            <?= date("F Y", $date) ?>
        </td>
        <?php if ($i == 5) :?>
            </tr>
            <tr>
        <?php endif ;?>
    <?php endfor;?>
    </tr>
    </tbody>
</table>

<table class="og-table og-full-table">
    <thead>
        <tr>
            <th colspan="10">
                <?= ($lang['ADMIN_LOGS_SELECT_DAY']) ?>
             </th>
        </tr>
    </thead>
    <tbody>
        <?php
        $max_day = date("t", mktime(0, 0, 0, $show_month, 1, $show_year));
        $current_day_in_loop = 1;
        ?>
    <tr> <!-- Jour -->
   <?php for ($i = 1; $i <= $max_day; $i++) :?>
        <?php
        $day = str_pad($i, 2, '0', STR_PAD_LEFT);
        $current_show_date_param = $show_year . "~" . $show_month . "~" . $day;
        $log_exists = log_check_exist($show_year . $show_month . $day);

        // La date sélectionnée via l'URL est au format Y~m~d
        $selected_date_from_url = $pub_show ?? date("Y~m~d");

        $is_selected_day = ($current_show_date_param == $selected_date_from_url);

        $cell_class = '';
        if ($log_exists) {
            $cell_class = 'og-success'; // Vert pour les jours avec logs
        }
        if ($is_selected_day) {
            $cell_class .= ' og-highlight'; // Jaune/Orange pour le jour sélectionné
        }
        ?>
        <td class="<?= $cell_class ?>" style="cursor: pointer;" onclick="window.location='index.php?action=administration&subaction=viewer&show=<?= $current_show_date_param ?>&typelog=<?= $typelog ?>&level=<?= $log_level_filter ?>';">
            <?= $day ?>
        </td>
        <?php if ($i % 10 == 0 && $i < $max_day) :?>
            </tr>
            <tr>
        <?php endif ;?>
    <?php endfor ;?>
    <?php
    // Complète la dernière ligne avec des cellules vides si nécessaire
    $remaining_cells = 10 - ($max_day % 10);
    if ($remaining_cells < 10) {
        for ($j = 0; $j < $remaining_cells; $j++) {
            echo "<td></td>";
        }
    }
    ?>
    </tr>
    </tbody>
</table>

<span class="og-alert"><?= ($lang['ADMIN_LOGS_SELECTTYPE']) ?></span>
<div class="nav-page-menu">

    <?php $activelog = ($typelog == "log") ? " active " : "";?>
    <?php $activelogsql = ($typelog != "log") ? " active " : "";?>

    <div class="nav-page-menu-item nav-page-menu-item-admin-infoserver <?= $activelog ?>">
        <a class="nav-page-menu-link" href='index.php?action=administration&amp;subaction=viewer&amp;show=<?= $show ?>&amp;typelog=log'>
            <?= $lang['ADMIN_LOGS_GENERAL'] ?>
        </a>
    </div>
    <div class="nav-page-menu-item nav-page-menu-item-admin-parameter <?= $activelogsql ?>">
        <a class="nav-page-menu-link" href='index.php?action=administration&amp;subaction=viewer&amp;show=<?= $show ?>&amp;typelog=sql'>
            <?= $lang['ADMIN_LOGS_SQL'] ?>
        </a>
    </div>

</div>

<div style="margin-bottom: 10px;">
    <form action="index.php" method="get" style="display: inline;">
        <input type="hidden" name="action" value="administration">
        <input type="hidden" name="subaction" value="viewer">
        <input type="hidden" name="show" value="<?= $show ?>">
        <input type="hidden" name="typelog" value="<?= $typelog ?>">
        <label for="level">Niveau de log minimum :</label>
        <select name="level" id="level" onchange="this.form.submit()">
            <?php foreach ($monolog_levels as $level_name => $level_value) : ?>
                <option value="<?= $level_name ?>" <?= ($log_level_filter == $level_name) ? 'selected' : '' ?>>
                    <?= $level_name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<span class="og-alert"><?= ($lang['ADMIN_LOGS_SEE_TRANSACTIONALS']) ?></span>

<table class="og-table og-full-table og-table-log">
    <thead>
        <tr>
            <th>
                <?= ($lang['ADMIN_LOGS_VIEWER']) ?>
                <span class="og-alert">
                    <?= $typelog == "log" ? $lang['ADMIN_LOGS_GENERAL'] : $lang['ADMIN_LOGS_SQL'] ?>
                </span>
            </th>
        </tr>
    </thead>
    <tbody>
          <tr>
              <td class="tdvalue" >
                  <?php
                  // Niveaux Monolog et leurs classes CSS associées
                  $log_levels = [
                      'EMERGENCY' => 'log-emergency',
                      'ALERT'     => 'log-alert',
                      'CRITICAL'  => 'log-critical',
                      'ERROR'     => 'log-error',
                      'WARNING'   => 'log-warning',
                      'NOTICE'    => 'log-notice',
                      'INFO'      => 'log-info',
                      'DEBUG'     => 'log-debug',
                  ];

                  $min_level_value = $monolog_levels[$log_level_filter];

                  foreach (array_reverse($logFileName) as $line) {
                      $line = rtrim($line, "\r\n");
                      if (empty($line)) {
                          continue;
                      }

                      $current_line_level = null;
                      foreach ($monolog_levels as $level_name => $level_value) {
                          if (preg_match('/OGSpy(SQL)?\.' . $level_name . ':/', $line)) {
                              $current_line_level = $level_value;
                              break;
                          }
                      }

                      // Si le niveau de log n'est pas trouvé ou est inférieur au filtre, on passe à la ligne suivante
                      if ($current_line_level === null || $current_line_level < $min_level_value) {
                          continue;
                      }

                      $applied_class = 'log-line'; // Classe par défaut

                      // Cherche le niveau de log dans l'entrée
                      foreach ($log_levels as $level => $class) {
                          if (preg_match('/OGSpy(SQL)?\.' . $level . ':/', $line)) {
                              $applied_class .= ' ' . $class;
                              break; // On a trouvé le niveau, on arrête la recherche
                          }
                      }

                      // Sécurise l'entrée pour l'affichage HTML
                      $formatted_entry = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');

                      // Affiche l'entrée formatée.
                      echo '<span class="' . $applied_class . '">' . $formatted_entry . '</span>';
                  }
                  ?>
              </td>
        </tr>
        </tbody>
</table>
