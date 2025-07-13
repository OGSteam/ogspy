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
    $file = "OGSpy-sql-" . $show . ".sql";
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
        <?php $date += 60 * 60 * 24 * 31;?>
        <?php $show = date("y~m", $date) . "~" . $show_day;?>

        <?php if ($show == $show_year . "~" . $show_month . "~" . $show_day) :  ?>
            <?php if (log_check_exist(date("Ym", $date))) :?>
            <td>
                <span class="og-success"><?= date("F o", $date) ?></span>
            </td>
             <td>
                 <input class="og-button og-button-image  og-button-warning" type='image' src='images/save.png' onclick="window.location = 'index.php?action=extractor&amp;date=<?= date("Fo", $date)?>'" title='<?= $lang['ADMIN_LOGS_DOWNLOAD'] . date("F o", $date) ?>'>
            <?php else : ?>
                <td colspan='2'>
                    <?= date("F o", $date) ?>
                </td>
            <?php endif ;?>
           </td>
        <?php else :?>
            <?php if (log_check_exist(date("Ym", $date))) :?>
                <td onclick="window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=<?= $show ?>&amp;typelog=<?= $typelog ?>';">
                    <?= date("F o", $date) ?>
                </td>
                <td>
                    <input class="og-button og-button-image  og-button-warning" type='image' src='images/save.png' onclick="window.location = 'index.php?action=extractor&amp;date=<?= date("ym", $date) ?>'" title='<?= $lang['ADMIN_LOGS_DOWNLOAD'] . date("F o", $date) ?>'>
                </td>
            <?php else : ?>
               <td colspan='2' onclick="window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=<?php $show . "&amp;typelog=" . $typelog;?>';">
                   <?= date("F o", $date) ?>
               </td>
            <?php endif ;?>
        <?php endif;?>
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
            <th  colspan="20">
                <?= ($lang['ADMIN_LOGS_SELECT_DAY']) ?>
             </th>
        </tr>
    </thead>
    <tbody>
         <?php $max_day = (intval($show_month) != date("m")) ? date("t", mktime(0, 0, 0, $show_month, 1, $show_year)) : date("d") ;?>
    <tr> <!-- Jour -->
   <?php  for ($i = 1; $i <= $max_day; $i++) :?>
        <?php $day = $i;?>
        <?php if ($i < 10) :?>
            <?php $day = "0" . $i;?>
        <?php endif;?>
        <?php $show = $show_year . "~" . $show_month . "~" . $day;?>
        <?php $date = mktime(0, 0, 0, $show_month, $day, $show_year);?>

        <?php if ($show == $show_year . "~" . $show_month . "~" . $show_day) :?>
            <?php if (log_check_exist($show_year . $show_month . $day)): ?>
        <td>
            <span class="og-success"><?= $day ?></span>
        </td>
                <td>
                    <input class="og-button og-button-image  og-button-warning" type='image' src='images/save.png' onclick="window.location = 'index.php?action=extractor&amp;date=<?= $show_year . $show_month . $day ?>'" title='<?= $lang['ADMIN_LOGS_DOWNLOAD'] . date("d F o", $date) ?>'>
                </td>
            <?php else : ?>
                <td colspan='2'>
                    <?= $day ?>
                </td>
            <?php endif; ?>

        <?php else :?>
            <?php if (log_check_exist($show_year . $show_month . $day)) :?>
                <td onclick="window.location='index.php?action=administration&amp;subaction=viewer&amp;show=<?= $show ?>&amp;typelog=<?= $typelog ?>';">
                    <?= $day ?>
                 </td>
                 <td>
                     <input class="og-button og-button-image  og-button-warning" type='image' src='images/save.png' onclick="window.location = 'index.php?action=extractor&amp;date=<?= $show_year . $show_month . $day ?>'" title='<?= $lang['ADMIN_LOGS_DOWNLOAD'] . date("d F o G:i", $date)?>'>
            <?php else : ?>
                <td colspan='2' onclick="window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=<?= $show ?>&amp;typelog=<?= $typelog ?>';">
                <?= $day ?>
                </td>
            <?php endif ;?>
        <?php endif ;?>
        <?php if ($i % 10 == 0) :?>
            </tr>
            <tr>
        <?php endif ;?>
    <?php endfor ;?>
    <?php $j = 1; /* $j ?*/?>
    <?php while (($i - 1) % 10 != 0) :?>
       <td colspan='2'></td>
        <?php $i++;?>
    <?php endwhile;?>

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
                          if (strpos($line, 'OGSpy.' . $level_name . ':') !== false) {
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
                          if (strpos($line, 'OGSpy.' . $level . ':') !== false) {
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
