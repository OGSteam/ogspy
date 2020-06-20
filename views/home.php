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
    die('Hacking attempt');
}
use Ogsteam\Ogspy\Helper\html_ogspy_Helper;


require_once('views/page_header.php');
?>

<?php
    //menu
    if (!isset($pub_subaction)) {
        $pub_subaction = 'empire';
    }
    $menuLinks = array();

// EMPIRE
$empire = array();
$empire[ "tag"] = "empire actif";
$empire["content"] = $lang['HOME_EMPIRE_TITLE'];
// si page deja affichée, on met le lien qui va bien
if ($pub_subaction != "empire") {
    $empire["tag"] = "empire";
    $empire["url"]= "index.php?action=home&amp;subaction=empire";
}
$menuLinks[]=$empire;


// SIMULATION
$simulation = array();
$simulation[ "tag"] = "simulation actif";
$simulation["content"] = $lang['HOME_SIMULATION_TITLE'];
// si page deja affichée, on met le lien qui va bien
if ($pub_subaction != "simulation") {
    $simulation["tag"] = "simulation";
    $simulation["url"]= "index.php?action=home&amp;subaction=simulation";
}
$menuLinks[]=$simulation;

// SPY
$spy = array();
$spy[ "tag"] = "spy actif";
$spy["content"] = $lang['HOME_REPORTS_TITLE'];
// si page deja affichée, on met le lien qui va bien
if ($pub_subaction != "spy") {
    $spy["tag"] = "spy";
    $spy["url"]= "index.php?action=home&amp;subaction=spy";
}
$menuLinks[]=$spy;


// SPY
$stat = array();
$stat[ "tag"] = "stat actif";
$stat["content"] = $lang['HOME_STATISTICS_TITLE'];
// si page deja affichée, on met le lien qui va bien
if ($pub_subaction != "stat") {
    $stat["tag"] = "stat";
    $stat["url"]= "index.php?action=home&amp;subaction=stat";
}
$menuLinks[]=$stat;

echo (new html_ogspy_Helper())->navbarreMenu("home" ,$menuLinks );

?>

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

<?php
require_once('views/page_tail.php');
?>