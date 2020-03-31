<?php
/**
 * Affichage Empire
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

require_once("views/page_header.php");
?>

    <table style="width:100%">
        <tr>
            <td>
                <table style="width:100%">
                    <tr>
<?php
    if (!isset($pub_subaction)) {
        $pub_subaction = 'empire';
    }

    if ($pub_subaction != 'empire') {
        echo "\t\t\t" . '<td class="c" style="width:25%" onclick="window.location = \'index.php?action=home&amp;subaction=empire\';">';
        echo '<a style="cursor:pointer"><span style="color: lime;">' . $lang['HOME_EMPIRE_TITLE'] . '</span></a>';
        echo '</td>';
    } else {
        echo "\t\t\t" . '<th style="width:25%">';
        echo '<a>' . $lang['HOME_EMPIRE_TITLE'] . '</a>';
        echo '</th>';
    }

    if ($pub_subaction != 'simulation') {
        echo "\t\t\t" . '<td class="c" style="width:25%" onclick="window.location = \'index.php?action=home&amp;subaction=simulation\';">';
        echo '<a style="cursor:pointer"><span style="color: lime;">' . $lang['HOME_SIMULATION_TITLE'] . '</span></a>';
        echo '</td>';
    } else {
        echo "\t\t\t" . '<th style="width:150px">';
        echo '<a>' . $lang['HOME_SIMULATION_TITLE'] . '</a>';
        echo '</th>';
    }

    if ($pub_subaction != 'spy') {
        echo "\t\t\t" . '<td class="c" style="width:25%" onclick="window.location = \'index.php?action=home&amp;subaction=spy\';">';
        echo '<a style="cursor:pointer"><span style="color: lime;">' . $lang['HOME_REPORTS_TITLE'] . '</span></a>';
        echo '</td>';
    } else {
        echo "\t\t\t" . '<th style="width:25%">';
        echo '<a>' . $lang['HOME_REPORTS_TITLE'] . '</a>';
        echo '</th>';
    }

    if ($pub_subaction != "stat") {
        echo "\t\t\t" . '<td class="c_stats" style="width:25%" onclick="window.location = \'index.php?action=home&amp;subaction=stat\';">';
        echo '<a style="cursor:pointer"><span style="color: lime;">' . $lang['HOME_STATISTICS_TITLE'] . '</span></a>';
        echo '</td>';
    } else {
        echo "\t\t\t" . '<th style="width:25%">';
        echo '<a>' . $lang['HOME_STATISTICS_TITLE'] . '</a>';
        echo '</th>';
    }
?>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
<?php
    switch ($pub_subaction) {
        case 'simulation' :
            require_once('home_simulation.php');
            break;

        case 'stat' :
            require_once('home_stat.php');
            break;

        case 'spy' :
            require_once('home_spy.php');
            break;

        case 'empire' : //no break
        default:
            require_once('home_empire.php');
            break;
    }
?>
            </td>
        </tr>
    </table>

<?php
require_once("views/page_tail.php");
?>