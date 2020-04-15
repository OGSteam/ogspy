<?php
/**
 * Panneau d'Administration : Paramètres et affichage des Journaux
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Helper\html_ogspy_Helper;

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

?>

<div class="page_adminviewer">

<h1><?php echo  $lang['ADMIN_LOGS_SELECTED_DATE'] . strftime("%d %b %Y", mktime(0, 0, 0, $show_month, $show_day, $show_year)) ;?></h1>


<table>
    <thead>
        <tr>
            <th colspan="12"><?php echo($lang['ADMIN_LOGS_SELECTED_DATE']); ?></th>

        </tr>
    </thead>
    <tbody>
     <?php $date = mktime(0, 0, 0, date("n"), 1) - 60 * 60 * 24 * 365; ?>
        <tr>
            <?php for ($i = 0; $i < 12; $i++) :?>
                <?php $date += 60 * 60 * 24 * 31; ;?>
                <?php $show = date("y~m", $date) . "~" . $show_day;?>
                <?php if ($show == $show_year . "~" . $show_month . "~" . $show_day) :?>
                    <?php if (log_check_exist(date("ym", $date))) :?>
                        <td>
                            <a> <?php echo strftime("%B %Y", $date) ;?></a>
                        </td>
                        <td>
                            <input type='image' src='images/save.png' onclick="window.location = 'index.php?action=extractor&amp;date=<?php echo date("ym", $date) ;?>'" title='<?php echo $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%B %Y", $date)?>' alt='<?php echo $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%B %Y", $date)?>'>
                            <input type='image' src='images/drop.png' onclick="window.location = 'index.php?action=remove&amp;date=<?php echo date("ym", $date) . $show_day?>&directory=TRUE'" title='<?php echo  $lang['ADMIN_LOGS_DELETE'] . strftime("%B %Y", $date)?>' alt='<?php echo  $lang['ADMIN_LOGS_DELETE'] . strftime("%B %Y", $date)?>'>
                        </td>
                    <?php else : ?>
                        <td colspan='2'>
                            <a><?php echo  strftime("%B %Y", $date);?></a>
                        </td>
                    <?php endif ;?>
                <?php else : ?>
                    <?php if (log_check_exist(date("ym", $date))) : ?>
                        <td onclick="window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=<?php echo $show ;?>&amp;typelog=<?php echo $typelog ;?>'">
                                <a>
                                    <?php echo strftime("%B %Y", $date) ;?>
                                </a>
                        </td>
                        <td>
                                <input type='image' src='images/save.png' onclick="window.location = 'index.php?action=extractor&amp;date=<?php echo date("ym", $date) ;?>'" title='<?php echo $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%B %Y", $date) ;?>' alt='<?php echo $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%B %Y", $date) ;?>'>
                        </td>
                    <?php else : ?>
                        <td colspan='2' onclick="window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=<?php echo $show ;?>&amp;typelog=<?php echo $typelog ;?>'">
                            <a><?php echo strftime("%B %Y", $date) ;?></a>
                        </td>
                    <?php  endif; ?>
                <?php endif;?>
                <?php if ($i == 5) :?>
                        </tr>
                        <tr>
                <?php endif ; ?>
            <?php endfor; ?>
        </tr>
    </tbody>

</table>

<br/>
<table >
    <thead>
        <tr>
            <th colspan="20"><?php echo($lang['ADMIN_LOGS_SELECT_DAY']); ?></th>
        </tr>
    </thead>
    <tbody>
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
                    echo "\t" . "<td><a>" . $day . "</a></td>";
                    echo "\t" . "<td ><input type='image' src='images/save.png' onclick=\"window.location = 'index.php?action=extractor&amp;date=" . $show_year . $show_month . $day . "'\" title='" . $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%d %B %Y", $date) . "' alt='" . $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%d %B %Y", $date) . "'><input type='image' src='images/drop.png' onclick=\"window.location = 'index.php?action=remove&amp;date=" . $show_year . $show_month . $day . "'\" title='" . $lang['ADMIN_LOGS_DELETE'] . strftime("%d %B %Y", $date) . "' alt='" . $lang['ADMIN_LOGS_DELETE'] . strftime("%d %B %Y", $date) . "'>";
                } else {
                    echo "\t" . "<td colspan='2'><a>" . $day . "</a></td>";
                }
                echo "</td>" . "\n";
            } else {
                if (log_check_exist($show_year . $show_month . $day)) {
                    echo "\t" . "<td onclick=\"window.location='index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=" . $typelog . "';\">";
                    echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . $day . "</span></a></td>";
                    echo "\t" . "<td><input type='image' src='images/save.png' onclick=\"window.location = 'index.php?action=extractor&amp;date=" . $show_year . $show_month . $day . "'\" title='" . $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%d %B %Y", $date) . "' alt='" . $lang['ADMIN_LOGS_DOWNLOAD'] . strftime("%d %B %Y", $date) . "'>";
                } else {
                    echo "\t" . "<td colspan='2' onclick=\"window.location='index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=" . $typelog . "';\">";
                    echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . $day . "</span></a></td>";
                }
            }
            if ($i % 10 == 0) {
                echo "</tr>" . "\n" . "<tr>";
            }
        }
        $j = 1;
        while (($i - 1) % 10 != 0) {
            echo "<td colspan='2'></td>";
            $i++;
        }
        echo "</tr>";
        ?>


    </tbody>


</table>


<br/>
<table >
    <thead>
    <tr>
        <th colspan="2"><?php echo($lang['ADMIN_LOGS_SELECTTYPE']); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $show = $show_year . "~" . $show_month . "~" . $show_day;
    echo "<tr>";
    if ($typelog == "log") {
        echo "\t" . "<td>" . $lang['ADMIN_LOGS_GENERAL'] . "</td>";
        echo "\t" . "<td onclick=\"window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=sql';\"><a>" . $lang['ADMIN_LOGS_SQL'] . "</a></td>";
    } else {
        echo "\t" . "<td onclick=\"window.location = 'index.php?action=administration&amp;subaction=viewer&amp;show=" . $show . "&amp;typelog=log';\"><a>" . $lang['ADMIN_LOGS_GENERAL'] . "</a></td>";
        echo "\t" . "<td >" . $lang['ADMIN_LOGS_SQL'] . "</td>";
    }
    echo "</tr>";
    ?>

    <tr>
        <td colspan="2" class="warning"><?php echo($lang['ADMIN_LOGS_SEE_TRANSACTIONALS']); ?></td>
    </tr>
    </tbody>

</table>

<br/>



<?php
$boxversiontitle =  $typelog == "log" ?  $lang['ADMIN_LOGS_VIEWER']  . " <span class=\"warning\">" . $lang['ADMIN_LOGS_GENERAL'] ."</span>":  $lang['ADMIN_LOGS_VIEWER']  . " <span class=\"warning\">" . $lang['ADMIN_LOGS_SQL'] ."</span>";
$boxversionclosebutton=false;
$boxversionstyle = " default";
$boxversioncontent= "";
end($log);
while ($line = current($log)) {
    if (version_compare(phpversion(), '5.4.0', '>=')) {
        $line = trim(nl2br(htmlspecialchars($line, ENT_COMPAT | ENT_HTML401, "UTF-8")));
    } else {
        $line = trim(nl2br(htmlspecialchars($line, ENT_COMPAT, "UTF-8")));
    }
    $line = preg_replace("#/\*(.*)\*/#", "<span class=\"notice\">$1 : </span>", $line);

    $boxversioncontent .= $line;
    prev($log);
}


$box = (new html_ogspy_Helper())->msgBox($boxversiontitle, $boxversioncontent, $boxversionclosebutton, $boxversionstyle );
echo $box;
?>

</div>
