<?php
/**
 * Rankings
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

$tagactive = "active";
$tagactiveplayers = "";
$tagactiveallys = "";



// place le tag active au besoin
if (!isset($pub_view) || $pub_view == 'player') {
    $subaction = "player";
    $tagactiveplayers = $tagactive;
} else {
    $subaction = "ally";
    $tagactiveallys = $tagactive;
}
?>

<div class="page_ranking"> 
    <div class="nav-page-menu">
        <div class="nav-page-menu-item nav-page-menu-item-admin-infoserver <?php echo $tagactiveplayers; ?>">
            <a class="nav-page-menu-link" href="index.php?action=ranking&amp;view=player">
                <?php echo $lang['RANK_PLAYERS']; ?> 
            </a>
        </div>
        <div class="nav-page-menu-item nav-page-menu-item-admin-parameter <?php echo $tagactiveallys; ?>">
            <a class="nav-page-menu-link" href="index.php?action=ranking&amp;view=ally">
                <?php echo $lang['RANK_ALLIANCES']; ?>
            </a>
        </div>
    </div>



    <?php
    switch ($subaction) {
        case "player":
            require_once("ranking_player.php");
            break;

        case "ally":
            require_once("ranking_ally.php");
            break;
    }
    ?>

</div> <!-- fin div  class="page_ranking" --> 
<?php
require_once("views/page_tail.php");
?>
