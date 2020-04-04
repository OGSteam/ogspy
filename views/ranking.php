<?php
/**
 * Rankings
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

require_once('views/page_header.php');
?>
    <table style="width:100%">
        <tr>
            <td>
                <table>
                    <tr>
<?php
    if (!isset($pub_subaction)) {
        $subaction = 'player';
    } else {
        $subaction = $pub_subaction;
    }

    if ($subaction != 'player') {
        echo "\t\t\t" . '<td class="c" style="width:150px" onclick="window.location = \'index.php?action=ranking&amp;subaction=player\';">';
        echo "<a style='cursor:pointer'><span style=\"color: lime; \">" . $lang['RANK_PLAYERS'] . "</span></a>";
        echo "</td>\n";
    } else {
        echo "\t\t\t" . '<th style="width:150px">';
        echo '<a>' . $lang['RANK_PLAYERS'] . '</a>';
        echo "</th>\n";
    }

    if ($subaction != 'ally') {
        echo "\t\t\t" . '<td class="c" style="width:150px" onclick="window.location = \'index.php?action=ranking&amp;subaction=ally\';">';
        echo '<a style="cursor:pointer"><span style="color: lime;">' . $lang['RANK_ALLIANCES'] . '</span></a>';
        echo "</td>\n";
    } else {
        echo "\t\t\t" . '<th style="width:150px">';
        echo '<a>' . $lang['RANK_ALLIANCES'] . '</a>';
        echo "</th>\n";
    }
?>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
<?php
    switch ($subaction) {
        case 'player' :
            require_once('ranking_player.php');
            break;

        case 'ally' :
            require_once('ranking_ally.php');
            break;
    }
?>
            </td>
        </tr>
    </table>
<?php
    require_once('views/page_tail.php');
?>