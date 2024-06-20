<?php global $user_data, $lang;
/** $Id: admin.php 7596 2012-03-25 16:10:55Z ninety $ * */
/**
 * Fonctions d'administrations
 * @package OGSpy
 * @version 3.04b ($Rev: 7596 $)
 * @subpackage admin
 * @author Kyser
 * @created 16/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
// Verification des droits admins
if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

require_once("views/page_header.php");

//determine page par defaut si non defini
if (!isset($pub_subaction)) {
    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
        $pub_subaction = "infoserver";
    } else {
        $pub_subaction = "member";
    }
}
// place le tag active au besoin
$tagactive = "active";
$tagactiveinfoserver = "";
$tagactiveparameter = "";
$tagactiveaffichage = "";
$tagactivemember = "";
$tagactivegroup = "";
$tagactiveviewer = "";
$tagactivehelper = "";
$tagactivemod = "";

switch ($pub_subaction) {
    case "infoserver":
        $tagactiveinfoserver = $tagactive;
        break;
    case "parameter":
        $tagactiveparameter = $tagactive;
        break;
    case "affichage":
        $tagactiveaffichage = $tagactive;
        break;
    case "member":
        $tagactivemember = $tagactive;
        break;
    case "group":
        $tagactivegroup = $tagactive;
        break;
    case "viewer":
        $tagactiveviewer = $tagactive;
        break;
    case "helper":
        $tagactivehelper = $tagactive;
        break;
    case "mod":
        $tagactivemod = $tagactive;
        break;
    default:
        break;
}


//
?>


<div class="page_administration">

    <div class="nav-page-menu">
        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : //admin/co only ?>
            <div class="nav-page-menu-item nav-page-menu-item-admin-infoserver <?php echo $tagactiveinfoserver; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=infoserver">
                    <?php echo $lang['ADMIN_TITLE_GENERAL_INFO']; ?>
                </a>
            </div>
            <div class="nav-page-menu-item nav-page-menu-item-admin-parameter <?php echo $tagactiveparameter; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=parameter">
                    <?php echo $lang['ADMIN_TITLE_SERVER_CONF']; ?>
                </a>
            </div>
            <div class="nav-page-menu-item nav-page-menu-item-admin-affichage <?php echo $tagactiveaffichage; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=affichage">
                    <?php echo $lang['ADMIN_TITLE_DISPLAY_CONF']; ?>
                </a>
            </div>
        <?php endif; ?>
        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1): //admin/co + gestion membre only ?>
            <div class="nav-page-menu-item nav-page-menu-item-admin-member <?php echo $tagactivemember; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=member">
                    <?php echo $lang['ADMIN_TITLE_MEMBER_CONF']; ?>
                </a>
            </div>
            <div class="nav-page-menu-item nav-page-menu-item-admin-group <?php echo $tagactivegroup; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=group">
                    <?php echo $lang['ADMIN_TITLE_GROUP_CONF']; ?>
                </a>
            </div>
        <?php endif; ?>
        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : //admin/co only ?>
            <div class="nav-page-menu-item nav-page-menu-item-admin-viewer <?php echo $tagactiveviewer; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=viewer">
                    <?php echo $lang['ADMIN_TITLE_LOGS_CONF']; ?>
                </a>
            </div>
            <div class="nav-page-menu-item nav-page-menu-item-admin-helper <?php echo $tagactivehelper; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=helper">
                    <?php echo $lang['ADMIN_TITLE_HELPER_CONF']; ?>
                </a>
            </div>
            <div class="nav-page-menu-item nav-page-menu-item-admin-mod <?php echo $tagactivemod; ?>">
                <a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=mod">
                    <?php echo $lang['ADMIN_TITLE_MODS_CONF']; ?>
                </a>
            </div>
        <?php endif; ?>
    </div>


    <?php
    switch ($pub_subaction) {
        case "member":
            require_once("admin_members.php");
            break;

        case "group":
            require_once("admin_members_group.php");
            break;

        case "parameter":
            require_once("admin_parameters.php");
            break;

        case "affichage":
            require_once("admin_affichage.php");
            break;

        case "viewer":
            require_once("admin_viewer.php");
            break;

        case "helper":
            require_once("admin_helper.php");
            break;

        case "mod":
            require_once("admin_mod.php");
            break;

        default:
            require_once("admin_infoserver.php");
            break;
    }
    ?>
</div> <!-- fin class="page_administration"  -->
<?php
require_once("views/page_tail.php");
?>
