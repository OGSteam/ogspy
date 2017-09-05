<?php
/**
 * Affichage Empire - Pages Espionnages favoris
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

$favorites = user_getfavorites_spy();

if (!isset($sort2)) {
    $sort2 = 0;
} else {
    $sort2 = $sort2 != 0 ? 0 : 1;
}
?>

<table align="center">
    <tr>
        <td class="c" width="75"><a
                href="index.php?action=home&amp;subaction=spy&amp;sort=1&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_POSITIONS']); ?></a>
        </td>
        <td class="c" width="120"><a
                href="index.php?action=home&amp;subaction=spy&amp;sort=2&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_ALLIANCES']); ?></a>
        </td>
        <td class="c" width="120"><a
                href="index.php?action=home&amp;subaction=spy&amp;sort=3&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_PLAYERS']); ?></a>
        </td>
        <td class="c" width="20"><a
                href="index.php?action=home&amp;subaction=spy&amp;sort=4&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_MOON']); ?></a></td>
        <td class="c" width="20">&nbsp;</td>
        <td class="c" width="250"><a
                href="index.php?action=home&amp;subaction=spy&amp;sort=5&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_UPDATE']); ?></a></td>
        <td class="c" width="40">&nbsp;</td>
        <td class="c" width="120">&nbsp;</td>
    </tr>
    <?php
    foreach ($favorites as $v) {
        $spy_id = $v["spy_id"];
        $galaxy = $v["spy_galaxy"];
        $system = $v["spy_system"];
        $row = $v["spy_row"];
        $player = $v["player"];
        $ally = $v["ally"];
        $moon = $v["moon"];
        $status = $v["status"];
        $timestamp = $v["datadate"];

        if ($timestamp != 0) {
            $timestamp = strftime("%d %b %Y %H:%M", $timestamp);
            $poster = $timestamp . " - " . $v["poster"];
        }

        if ($ally == "") {
            $ally = "&nbsp;";
        } else {
            $ally = "<a href='index.php?action=search&amp;type_search=ally&amp;string_search=" . $ally . "&strict=on'>" . $ally . "</a>";
        }

        if ($player == "") {
            $player = "&nbsp;";
        } else {
            $player = "<a href='index.php?action=search&amp;type_search=player&amp;string_search=" . $player . "&strict=on'>" . $player . "</a>";
        }

        if ($status == "") {
            $status = " &nbsp;";
        }

        if ($moon == 1) {
            $moon = " M";
        } else {
            $moon = "&nbsp;";
        }

        echo "<tr>";
        echo "<th>$galaxy:$system:$row</th>";
        echo "<th>$ally</th>";
        echo "<th>$player</th>";
        echo "<th>$moon</th>";
        echo "<th>$status</th>";
        echo "<th>$poster</th>";
        $coords = explode(":", $row);
        echo "<th><input type='button' value='" . $lang['HOME_SPY_SEE'] . "' onclick=\"window.open('index.php?action=show_reportspy&amp;galaxy=$galaxy&amp;system=$system&amp;row=$row&amp;spy_id=$spy_id','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)\" /></th>";
        echo "<th><input type='button' value='" . $lang['HOME_SPY_FAVDELETE'] . "' onclick=\"window.location = 'index.php?action=del_favorite_spy&amp;spy_id=$spy_id&amp;info=1';\" /></th>";
        echo "</tr>";
    }
    ?>
</table>