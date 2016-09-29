<?php
/** $Id: about.php 7508 2012-01-30 21:59:01Z darknoon $ **/
/**
 * Panneau d'affichage About
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 17/01/2006
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy;

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
                    if (!isset($pub_subaction)) $subaction = "ogsteam";
                    else $subaction = $pub_subaction;

                    if ($subaction != "ogsteam") {
                        echo "\t\t\t" . "<td class='c' width='150' onclick=\"window.location = 'index.php?action=about&amp;subaction=ogsteam';\">";
                        echo "<a style='cursor:pointer'><span style=\"color: lime; \">" .$lang['ABOUT_TEAM']. "</span></a>";
                        echo "</td>";
                    } else {
                        echo "\t\t\t" . "<th width='150'>";
                        echo "<a>".$lang['ABOUT_TEAM']."</a>";
                        echo "</th>";
                    }

                    if ($subaction != "changelog") {
                        echo "\t\t\t" . "<td class='c' width='150' onclick=\"window.location = 'index.php?action=about&amp;subaction=changelog';\">";
                        echo "<a style='cursor:pointer'><span style=\"color: lime; \">" .$lang['ABOUT_CHANGELOG']. "</span></a>";
                        echo "</td>";
                    } else {
                        echo "\t\t\t" . "<th width='150'>";
                        echo "<a>".$lang['ABOUT_CHANGELOG']."</a>";
                        echo "</th>";
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
                case "ogsteam" :
                    require_once("about_ogsteam.php");
                    break;

                case "changelog" :
                    require_once("about_changelog.php");
                    break;
            }
            ?>
        </td>
    </tr>
</table>
<?php
require_once("views/page_tail.php");
?>
