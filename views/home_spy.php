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
    die('Hacking attempt');
}

$favorites = user_getfavorites_spy();

if (!isset($sort2)) {
    $sort2 = 0;
} else {
    $sort2 = $sort2 != 0 ? 0 : 1;
}
?>

<table>


    <tbody>
    <tr>
        <th >
            <a href="index.php?action=home&amp;subaction=spy&amp;sort=1&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_POSITIONS']); ?></a>
        </th>
        <th >
            <a href="index.php?action=home&amp;subaction=spy&amp;sort=2&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_ALLIANCES']); ?></a>
        </th>
        <th >
            <a href="index.php?action=home&amp;subaction=spy&amp;sort=3&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_PLAYERS']); ?></a>
        </th>
        <th >
            <a href="index.php?action=home&amp;subaction=spy&amp;sort=4&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_MOON']); ?></a></th>
        <th >&nbsp;</th>
        <th  >
            <a href="index.php?action=home&amp;subaction=spy&amp;sort=5&amp;sort2=<?php echo $sort2; ?>"><?php echo($lang['HOME_SPY_UPDATE']); ?></a></th>
        <th  >&nbsp;</th>
        <th  >&nbsp;</th>
    </tr>


    <?php
    foreach ($favorites as $v) {
        $spy_id    = $v['spy_id'];
        $galaxy    = $v['spy_galaxy'];
        $system    = $v['spy_system'];
        $row       = $v['spy_row'];
        $player    = $v['player'];
        $ally      = $v['ally'];
        $moon      = $v['moon'];
        $status    = $v['status'];
        $timestamp = $v['datadate'];

        if ($timestamp != 0) {
            $timestamp = strftime('%d %b %Y %H:%M', $timestamp);
            $poster = $timestamp . ' - ' . $v['poster'];
        }

        if ($ally == '') {
            $ally = '&nbsp;';
        } else {
            $ally = '<a href="index.php?action=search&amp;type_search=ally&amp;string_search=' . $ally . '&strict=on">' . $ally . '</a>';
        }

        if ($player == '') {
            $player = '&nbsp;';
        } else {
            $player = '<a href="index.php?action=search&amp;type_search=player&amp;string_search=' . $player . '&strict=on">' . $player . '</a>';
        }

        if ($status == '') {
            $status = " &nbsp;";
        }

        if ($moon == 1) {
            $moon = ' M';
        } else {
            $moon = '&nbsp;';
        }

        echo '<tr>';
        echo "<td>$galaxy:$system:$row</td>";
        echo "<td>$ally</td>";
        echo "<td>$player</td>";
        echo "<td>$moon</td>";
        echo "<td>$status</td>";
        echo "<td>$poster</td>";
        $coords = explode(':', $row);
        echo '<td><input type="button"  value="' . $lang['HOME_SPY_SEE']       . "\" onclick=\"window.open('index.php?action=show_reportspy&amp;galaxy=$galaxy&amp;system=$system&amp;row=$row&amp;spy_id=$spy_id','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)\" /></td>";
        echo '<td><input type="button" class="warning"  value="' . $lang['HOME_SPY_FAVDELETE'] . "\" onclick=\"window.location = 'index.php?action=del_favorite_spy&amp;spy_id=$spy_id&amp;info=1';\" /></td>";
        echo "</tr>\n";
    }
    ?>
    </tbody>

</table>