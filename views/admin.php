<?php
/** $Id: admin.php 7596 2012-03-25 16:10:55Z ninety $ **/
/**
 * Fonctions d'administrations
 * @package OGSpy
 * @version 3.04b ($Rev: 7596 $)
 * @subpackage admin
 * @author Kyser
 * @created 16/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
// Verification des droits admins
if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

require_once("views/page_header.php");
?>

<table width="100%">
    <tr>
        <td>
            <table border="1" width="100%">
                <tr align="center">
                    <?php
                    if (!isset($pub_subaction)) {
                        if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                            $pub_subaction = "infoserver";
                        } else {
                            $pub_subaction = "member";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                        if ($pub_subaction != "infoserver") {
                            echo "\t\t\t" . "<td class='c' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=infoserver';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_GENERAL_INFO'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_GENERAL_INFO'] . "</a>";
                            echo "</th>" . "\n";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                        if ($pub_subaction != "parameter") {
                            echo "\t\t\t" . "<td class='c_tech' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=parameter';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_SERVER_CONF'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_SERVER_CONF'] . "</a>";
                            echo "</th>" . "\n";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                        if ($pub_subaction != "affichage") {
                            echo "\t\t\t" . "<td class='c' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=affichage';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_DISPLAY_CONF'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_DISPLAY_CONF'] . "</a>";
                            echo "</th>" . "\n";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) {
                        if ($pub_subaction != "member") {
                            echo "\t\t\t" . "<td class='c' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=member';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_MEMBER_CONF'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_MEMBER_CONF'] . "</a>";
                            echo "</th>" . "\n";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) {
                        if ($pub_subaction != "group") {
                            echo "\t\t\t" . "<td class='c' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=group';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_GROUP_CONF'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_GROUP_CONF'] . "</a>";
                            echo "</th>" . "\n";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                        if ($pub_subaction != "viewer") {
                            echo "\t\t\t" . "<td class='c' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=viewer';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_LOGS_CONF'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_LOGS_CONF'] . "</a>";
                            echo "</th>" . "\n";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                        if ($pub_subaction != "helper") {
                            echo "\t\t\t" . "<td class='c' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=helper';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_HELPER_CONF'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_HELPER_CONF'] . "</a>";
                            echo "</th>" . "\n";
                        }
                    }

                    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                        if ($pub_subaction != "mod") {
                            echo "\t\t\t" . "<td class='c' width='12%' onclick=\"window.location = 'index.php?action=administration&amp;subaction=mod';\">";
                            echo "<a style='cursor:pointer;color: lime;'>" . $lang['ADMIN_TITLE_MODS_CONF'] . "</a>";
                            echo "</td>" . "\n";
                        } else {
                            echo "\t\t\t" . "<th width='12%'>";
                            echo "<a>" . $lang['ADMIN_TITLE_MODS_CONF'] . "</a>";
                            echo "</th>" . "\n";
                        }
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
                case "member" :
                    require_once("admin_members.php");
                    break;

                case "group" :
                    require_once("admin_members_group.php");
                    break;

                case "parameter" :
                    require_once("admin_parameters.php");
                    break;

                case "affichage" :
                    require_once("admin_affichage.php");
                    break;

                case "viewer" :
                    require_once("admin_viewer.php");
                    break;

                case "helper" :
                    require_once("admin_helper.php");
                    break;

                case "mod" :
                    require_once("admin_mod.php");
                    break;

                default:
                    require_once("admin_infoserver.php");
                    break;
            }
            ?>
        </td>
    </tr>
</table>

<?php
require_once("views/page_tail.php");
?>
