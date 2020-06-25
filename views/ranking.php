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
use Ogsteam\Ogspy\Helper\html_ogspy_Helper;

require_once('views/page_header.php');
?>
<?php
$menuLinks = array();
if (!isset($pub_subaction)) {
    $subaction = 'player';
} else {
    $subaction = $pub_subaction;
}

//creation de la barre de menu
    // Player
    $player = array();
    $player[ "tag"] = "rank_player actif";
    $player["content"] =  $lang['RANK_PLAYERS'];
    // si page deja affichée, on met le lien qui va bien
    if ($subaction != "player") {
        $player["tag"] = "rank_player";
        $player["url"]= "index.php?action=ranking&amp;subaction=player";
    }
    $menuLinks[]=$player;


    // ally
    $ally = array();
    $ally[ "tag"] = "rank_ally actif";
    $ally["content"] = $lang['RANK_ALLIANCES'];
    // si page deja affichée, on met le lien qui va bien
    if ($subaction != "ally") {
        $ally["tag"] = "rank_ally";
        $ally["url"]= "index.php?action=ranking&amp;subaction=ally";
    }
    $menuLinks[]=$ally;

    echo (new html_ogspy_Helper())->navbarreMenu("ranking" ,$menuLinks );
?>

<?php
//preparation formulaire
$order_by = '';
$interval = '';
if(isset($pub_order_by))
{
    $order_by=$pub_order_by;
}
if(isset($pub_interval)) {
    $interval = $pub_interval;
}

if ( $subaction == 'player') {
    list($order, $ranking, $ranking_available, $maxrank) = galaxy_show_ranking_player();
    $valuetype='player';
} else {
    list($order, $ranking, $ranking_available, $maxrank) = galaxy_show_ranking_ally();
        $valuetype='ally';
}







?>


    <form method="POST" action="index.php">
        <input type="hidden" name="action" value="ranking">
        <input type="hidden" name="subaction" value="<?php echo $valuetype;?>">
        <input type="hidden" name="order_by" value="<?php echo $order_by; ?>">
        <div>
            <select name="date" onchange="this.form.submit();">
                <?php
                $date_selected = '';
                $datadate = 0;
                foreach ($ranking_available as $v) {
                    $selected = '';
                    if (!isset($pub_date_selected) && !isset($datadate)) {
                        $datadate = $v;
                        $date_selected = strftime('%d %b %Y %Hh', $v);
                    }
                    if ($pub_date == $v) {
                        $selected = 'selected';
                        $datadate = $v;
                        $date_selected = strftime('%d %b %Y %Hh', $v);
                    }
                    $string_date = strftime('%d %b %Y %Hh', $v);
                    echo "\t\t\t" . '<option value="' . $v . '" ' . $selected . '>' . $string_date . "</option>\n";
                }
                ?>
            </select>

            <select name="interval" onchange="this.form.submit();">
                <?php
                if (sizeof($ranking_available) > 0) {
                    for ($i = 1; $i <= $maxrank; $i = $i + 100) {
                        $selected = '';
                        if ($i == $interval) {
                            $selected = 'selected';
                        }
                        echo "\t\t\t" . '<option value="' . $i . '" ' . $selected . '>' . $i . ' - ' . ($i + 99) . "</option>\n";
                    }
                }
                ?>
            </select>
        </div>


    </form>
<?php
if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1 || $user_data['management_ranking'] == 1) {
    ?>
    <form method="POST" action="index.php" onsubmit="return confirm('<?php echo($lang['RANK_DELETE_CONFIRMATION']); ?>');">
        <input type="hidden" name="action" value="drop_ranking">
        <input type="hidden" name="subaction" value="<?php echo $valuetype;?>">
        <input type="hidden" name="datadate" value="<?php echo $datadate; ?>">
        <input class="button warning" alt="DELETE" type="button"   value="<?php echo $lang['RANK_DELETE'] . " " . $date_selected; ?>">
    </form>
    <?php
}
?>




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


<?php
    require_once('views/page_tail.php');
?>