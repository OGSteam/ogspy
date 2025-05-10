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

//Définition de la date sélectionnée
if (!isset($pub_show)) {
    $show = date("y~m~d");
} else {
    $show = $pub_show;
}

if (sizeof(explode("~", $show)) != 3) {
    $show = date("y~m~d");
}
list($show_year, $show_month, $show_day) = explode("~", $show);
if (!checkdate($show_month, $show_day, $show_year)) {
    list($show_year, $show_month, $show_day) = explode("~", date("y~m~d"));
}
$show = $show_year . $show_month . $show_day;

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
    $file = $typelog . "_" . $show . ".log";
}
if ($typelog == "sql") {
    $file = $typelog . "_" . $show . ".sql";
}

//Récupération du log
$dir = $show;
$file = PATH_LOG . $dir . "/" . $file;

if (file_exists($file)) {
    $log = file($file);
} else {
    $log = array($lang['ADMIN_LOGS_NOLOGS']);
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
            <?php if (log_check_exist(date("ym", $date))) :?>
            <td>
                <span class="og-success"><?= date("F o", $date) ?></span>
            </td>
             <td>
                 <input class="og-button og-button-image  og-button-warning" type='image' src='images/save.png' onclick="window.location = 'index.php?action=extractor&amp;date=<?= date("Fo", $date)?>'" title='<?= $lang['ADMIN_LOGS_DOWNLOAD'] . date("F o", $date) ?>'>
                 <input  class="og-button og-button-image  og-button-danger" type='image' src='images/drop.png' onclick="window.location = 'index.php?action=remove&amp;date=<?php date("ym", $date) . $show_day?>&directory=TRUE'" title='<?= $lang['ADMIN_LOGS_DELETE'] . date("F o", $date) ?>'>
            <?php else : ?>
                <td colspan='2'>
                    <?= date("F o", $date) ?>
                </td>
            <?php endif ;?>
           </td>
        <?php else :?>
            <?php if (log_check_exist(date("ym", $date))) :?>
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
                    <input class="og-button og-button-image  og-button-danger" type='image' src='images/drop.png' onclick="window.location = 'index.php?action=remove&amp;date=<?= $show_year . $show_month . $day ?>'" title='<?= $lang['ADMIN_LOGS_DELETE'] . date("d F o", $date) ?>'>
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
                <td colspan='2' onclick="window.location='index.php?action=administration&amp;subaction=viewer&amp;show=<?= $show ?>&amp;typelog=<?= $typelog ?>';">
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
            end($log);
            while ($line = current($log)) {

                $line = trim(nl2br(htmlspecialchars($line)));
                $line = preg_replace("#/\*(.*)\*/#", "<span style=\"color: orange; \">$1 : </span>", $line);

                echo $line;
                prev($log);
            }
            ?>
            </td>
        </tr>
        </tbody>
</table>

