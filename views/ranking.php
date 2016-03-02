<?php
/***************************************************************************
 *    filename    : ranking.php
 *    desc.        :
 *    Author        : Kyser - http://ogsteam.fr/
 *    created        : 06/05/2006
 *    modified    : 30/07/2006 00:00:00
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("views/page_header.php");
?>

    <table width="100%">
        <tr>
            <td>
                <table>
                    <tr align="center">
                        <?php
                        if (!isset($pub_subaction)) $subaction = "player";
                        else $subaction = $pub_subaction;

                        if ($subaction != "player") {
                            echo "\t\t\t" . "<td class='c' width='150' onclick=\"window.location = 'index.php?action=ranking&amp;subaction=player';\">";
                            echo "<a style='cursor:pointer'><span style=\"color: lime; \">".$lang['RANK_PLAYERS']."</span></a>";
                            echo "</td>";
                        } else {
                            echo "\t\t\t" . "<th width='150'>";
                            echo "<a>".$lang['RANK_PLAYERS']."</a>";
                            echo "</th>";
                        }

                        if ($subaction != "ally") {
                            echo "\t\t\t" . "<td class='c' width='150' onclick=\"window.location = 'index.php?action=ranking&amp;subaction=ally';\">";
                            echo "<a style='cursor:pointer'><span style=\"color: lime; \">".$lang['RANK_ALLIANCES']."</span></a>";
                            echo "</td>";
                        } else {
                            echo "\t\t\t" . "<th width='150'>";
                            echo "<a>".$lang['RANK_ALLIANCES']."</a>";
                            echo "</th>";
                        }
                        ?>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center">
                <?php
                switch ($subaction) {
                    case "player" :
                        require_once("ranking_player.php");
                        break;

                    case "ally" :
                        require_once("ranking_ally.php");
                        break;
                }
                ?>
            </td>
        </tr>
    </table>

<?php
require_once("views/page_tail.php");
?>