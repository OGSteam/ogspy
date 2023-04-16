<?php

/**
 * Panneau d'Administration : Paramètres et affichage des Journaux
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

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
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

echo "<a>" . $lang['ADMIN_LOGS_SELECTED_DATE'] .  date("d F o", mktime(0, 0, 0, $show_month, $show_day, $show_year)) . "</a>";
?>

<table width="100%">
    <th>
    <td class="c" colspan="12"><?php echo ($lang['ADMIN_LOGS_SELECTED_DATE']); ?></td>
    </th>
    <?php
    $date = mktime(0, 0, 0, date("n"), 1) - 60 * 60 * 24 * 365;
    echo "<tr>";
    for ($i = 0; $i < 12; $i++) {
        $date += 60 * 60 * 24 * 31;
        $show = date("y~m", $date) . "~" . $show_day;

        if ($show == $show_year . "~" . $show_month . "~" . $show_day) {
            if (log_check_exist(date("ym", $date))) {
                echo "\t" . "<th><a>" . date("F o", $date) . "</a></th>" . "\n";
                echo "\t" . "<th width='40px'><input type='image' src='images/save.png' onclick=\"window.location = 'index.php?action=extractor&amp;date=" . date("Fo", $date) . "'\" title='" . $lang['ADMIN_LOGS_DOWNLOAD'] . date("F o", $date) . "'><input type='image' src='images/drop.png' onclick=\"window.location = 'index.php?action=remove&amp;date=" . date("ym", $date) . $show_day . "&directory=TRUE'\" title='" . $lang['ADMIN_LOGS_DELETE'] . date("F o", $date) . "'>";
            } else {
                echo "\t" . "<th colspan='2'><a>" . date("F o", $date) . "</a></th>" . "\n";
            }
            echo "</th>" . "\n";
        } else {
            if (log_check_exist(date("ym", $date))) {
                echo "\t" . "<th onclick=\"window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=" . $typelog . "';\">";
                echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . date("F o", $date) . "</span></a></th>" . "\n";
                echo "\t" . "<th width='16px'><input type='image' src='images/save.png' onclick=\"window.location = 'index.php?action=extractor&amp;date=" . date("ym", $date) . "'\" title='" . $lang['ADMIN_LOGS_DOWNLOAD'] . date("F o", $date) . "'></th>";
            } else {
                echo "\t" . "<th colspan='2' onclick=\"window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=" . $typelog . "';\">";
                echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . date("F o", $date) . "</span></a></th>" . "\n";
            }
        }
        if ($i == 5) {
            echo "</tr>" . "\n" . "<tr>";
        }
    }
    echo "</tr>";
    ?>
</table>

<br />
<table width="100%">
    <tr>
        <td class="c" colspan="20"><?php echo ($lang['ADMIN_LOGS_SELECT_DAY']); ?></td>
    </tr>
    <?php
    $max_day = date("d");
    if (intval($show_month) != date("m")) {
        $max_day = date("t", mktime(0, 0, 0, $show_month, 1, $show_year));
    }

    echo "<tr>";
    for ($i = 1; $i <= $max_day; $i++) {
        $day = $i;
        if ($i < 10) {
            $day = "0" . $i;
        }
        $show = $show_year . "~" . $show_month . "~" . $day;
        $date = mktime(0, 0, 0, $show_month, $day, $show_year);

        if ($show == $show_year . "~" . $show_month . "~" . $show_day) {

            if (log_check_exist($show_year . $show_month . $day)) {
                echo "\t" . "<th><a>" . $day . "</a></th>";
                echo "\t" . "<th width='40px'><input type='image' src='images/save.png' onclick=\"window.location = 'index.php?action=extractor&amp;date=" . $show_year . $show_month . $day . "'\" title='" . $lang['ADMIN_LOGS_DOWNLOAD'] . date("d F o", $date) . "'><input type='image' src='images/drop.png' onclick=\"window.location = 'index.php?action=remove&amp;date=" . $show_year . $show_month . $day . "'\" title='" . $lang['ADMIN_LOGS_DELETE'] . date("d F o", $date) . "'>";
            } else {
                echo "\t" . "<th colspan='2'><a>" . $day . "</a></th>";
            }
            echo "</th>" . "\n";
        } else {
            if (log_check_exist($show_year . $show_month . $day)) {
                echo "\t" . "<th onclick=\"window.location='index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=" . $typelog . "';\">";
                echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . $day . "</span></a></th>";
                echo "\t" . "<th width='16px'><input type='image' src='images/save.png' onclick=\"window.location = 'index.php?action=extractor&amp;date=" . $show_year . $show_month . $day . "'\" title='" . $lang['ADMIN_LOGS_DOWNLOAD'] . date("d F o G:i", $date) . "'>";
            } else {
                echo "\t" . "<th colspan='2' onclick=\"window.location='index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=" . $typelog . "';\">";
                echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . $day . "</span></a></th>";
            }
        }
        if ($i % 10 == 0) {
            echo "</tr>" . "\n" . "<tr>";
        }
    }
    $j = 1;
    while (($i - 1) % 10 != 0) {
        echo "<th colspan='2'></th>";
        $i++;
    }
    echo "</tr>";
    ?>
</table>

<br />
<table width="100%">
    <tr>
        <td class="c" colspan="3"><?php echo ($lang['ADMIN_LOGS_SELECTTYPE']); ?></td>
    </tr>
    <?php
    $show = $show_year . "~" . $show_month . "~" . $show_day;
    echo "<tr>";
    if ($typelog == "log") {
        echo "\t" . "<th width='50%'><a>" . $lang['ADMIN_LOGS_GENERAL'] . "</a></th>";
        echo "\t" . "<th width='50%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=sql';\"><a style='cursor:pointer'><span style=\"color: lime; \">" . $lang['ADMIN_LOGS_SQL'] . "</span></a></td>";
    } else {
        echo "\t" . "<th width='50%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=log';\"><a style='cursor:pointer'><span style=\"color: lime; \">" . $lang['ADMIN_LOGS_GENERAL'] . "</span></a></td>";
        echo "\t" . "<th width='50%'><a>" . $lang['ADMIN_LOGS_SQL'] . "</a></td>";
    }
    echo "</tr>";
    ?>
    <tr>
        <td colspan='2'><span style="color: Red; "><i><?php echo ($lang['ADMIN_LOGS_SEE_TRANSACTIONALS']); ?></i></span></td>
    </tr>
</table>

<br />
<table width="100%">
    <tr>
        <td class="l" colspan="3"><b><?php echo ($lang['ADMIN_LOGS_VIEWER']); ?></b> <i><span style="color: red; "><b><?php echo $typelog == "log" ? $lang['ADMIN_LOGS_GENERAL'] : $lang['ADMIN_LOGS_SQL']; ?></b></span></i><br>
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
</table>
