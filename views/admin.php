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
use Ogsteam\Ogspy\Helper\html_ogspy_Helper;


// Verification des droits admins
if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

require_once("views/page_header.php");
?>

<?php
if (!isset($pub_subaction)) {
    if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
        $pub_subaction = "infoserver";
    } else {
        $pub_subaction = "member";
    }
}


$menuLinks = array();

//utilisateur admin ou coadmin
if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {

    // INFOSERVEUR
    $infoserver = array();
    $infoserver[ "tag"] = "infoserver actif";
    $infoserver["content"] = $lang['ADMIN_TITLE_GENERAL_INFO'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "infoserver") {
        $infoserver["tag"] = "infoserver";
        $infoserver["url"]= "index.php?action=administration&amp;subaction=infoserver";
     }
    $menuLinks[]=$infoserver;

    //PARAMETER
    $parameter =array();
    $parameter[ "tag"] = "parameter actif";
    $parameter["content"] = $lang['ADMIN_TITLE_SERVER_CONF'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "parameter") {
        $parameter["tag"] = "parameter";
        $parameter["url"]= "index.php?action=administration&amp;subaction=parameter";
    }
    $menuLinks[]=$parameter;
    //AFFICHAGE
    $affichage =array();
    $affichage[ "tag"] = "affichage actif";
    $affichage["content"] = $lang['ADMIN_TITLE_DISPLAY_CONF'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "affichage") {
        $affichage["tag"] = "affichage";
        $affichage["url"]= "index.php?action=administration&amp;subaction=affichage";
    }
    $menuLinks[]=$affichage;

}

//utilisateur admin ou coadmin ou geston membre
if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) {
    //MEMBER
    $member =array();
    $member[ "tag"] = "member actif";
    $member["content"] = $lang['ADMIN_TITLE_MEMBER_CONF'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "member") {
        $member["tag"] = "member";
        $member["url"]= "index.php?action=administration&amp;subaction=member";
    }
    $menuLinks[]=$member;
    //GROUP
    $group =array();
    $group[ "tag"] = "group actif";
    $group["content"] = $lang['ADMIN_TITLE_GROUP_CONF'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "group") {
        $group["tag"] = "group";
        $group["url"]= "index.php?action=administration&amp;subaction=group";
    }
    $menuLinks[]=$group;
}

//A nouveau utilisateur admin ou coadmin
if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
    //VIEWER
    $viewer =array();
    $viewer[ "tag"] = "viewer actif";
    $viewer["content"] = $lang['ADMIN_TITLE_GROUP_CONF'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "viewer") {
        $viewer["tag"] = "viewer";
        $viewer["url"]= "index.php?action=administration&amp;subaction=viewer";
    }
    $menuLinks[]=$viewer;
    //HELPER
    $helper =array();
    $helper[ "tag"] = "helper actif";
    $helper["content"] = $lang['ADMIN_TITLE_HELPER_CONF'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "helper") {
        $helper["tag"] = "helper";
        $helper["url"]= "index.php?action=administration&amp;subaction=helper";
    }
    $menuLinks[]=$helper;
    //mod
    $mod =array();
    $mod[ "tag"] = "mod actif";
    $mod["content"] = $lang['ADMIN_TITLE_MODS_CONF'];
    // si page deja affichée, on met le lien qui va bien
    if ($pub_subaction != "mod") {
        $mod["tag"] = "mod";
        $mod["url"]= "index.php?action=administration&amp;subaction=mod";
    }
    $menuLinks[]=$mod;



}



echo (new html_ogspy_Helper())->navbarreMenu("admin" ,$menuLinks );


?>


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
<?php
require_once("views/page_tail.php");
?>
