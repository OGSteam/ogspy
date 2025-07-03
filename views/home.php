<?php global $lang;
/**
 * Affichage Empire
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("views/page_header.php");
?>

<?php
if (!isset($pub_subaction) || $pub_subaction == 'home') {
    $pub_subaction = "empire";
}




// place le tag active au besoin
$tagactive = "active";
$tagactiveempire = "";
$tagactivesimulation = "";
$tagactivespy = "";
$tagactivestat = "";

switch ($pub_subaction) {
    case "empire":
        $tagactiveempire = $tagactive;
        break;
    case "simulation":
        $tagactivesimulation = $tagactive;
        break;
    case "spy":
        $tagactivespy = $tagactive;
        break;
    case "stat":
        $tagactivestat = $tagactive;
        break;
    default:
        break;
}


//
?>
<div class="page_home">

    <div class="nav-page-menu">
        <div class="nav-page-menu-item nav-page-menu-item-home-infoserver <?php echo $tagactiveempire; ?>">
            <a class="nav-page-menu-link" href="index.php?action=home&amp;subaction=empire">
                <?php echo $lang['HOME_EMPIRE_TITLE']; ?>
            </a>
        </div>
        <div class="nav-page-menu-item nav-page-menu-item-home-simulation <?php echo $tagactivesimulation; ?>">
            <a class="nav-page-menu-link" href="index.php?action=home&amp;subaction=simulation">
                <?php echo $lang['HOME_SIMULATION_TITLE']; ?>
            </a>
        </div>
        <div class="nav-page-menu-item nav-page-menu-item-home-spy <?php echo $tagactivespy; ?>">
            <a class="nav-page-menu-link" href="index.php?action=home&amp;subaction=spy">
                <?php echo $lang['HOME_REPORTS_TITLE']; ?>
            </a>
        </div>
        <div class="nav-page-menu-item nav-page-menu-item-home-stat <?php echo $tagactivestat; ?>">
            <a class="nav-page-menu-link" href="index.php?action=home&amp;subaction=stat">
                <?php echo $lang['HOME_STATISTICS_TITLE']; ?>
            </a>
        </div>
    </div>


                <?php
                switch ($pub_subaction) {
                    case "empire":
                        require_once("home_empire.php");
                        break;

                    case "simulation":
                        require_once("home_simulation.php");
                        break;

                    case "stat":
                        require_once("home_stat.php");
                        break;

                    case "spy":
                        require_once("home_spy.php");
                        break;

                    default:
                        require_once("home_empire.php");
                        break;
                }
                ?>

</div><!-- fin div class="page_home" -->
<?php
require_once("views/page_tail.php");
?>
