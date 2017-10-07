<?php
/**
 * Rankings - Player Page
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy;

use Ogsteam\Ogspy\datatable_js;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$data_general = galaxy_show_ranking();


$idHtmlTable = "player_rankings";
$datatable = new \Ogsteam\Ogspy\datatable_js($idHtmlTable);

// activation ou desactivation des options
$datatable->enableFeatures(array("AutoWidth","Info", "LengthChange","Ordering","Paging","Searching"));
$datatable->disableFeatures(array("ScrollX","ScrollY"));
$datatable->setFormatNumber(true);
$datatable->toggleVisibility("datatable-toggle-vis","data-column");
$datatable->setPaginate(array(100, 500, 1000, -1));

$availableDatatade = get_all_player_distinct_date_ranktable();

?>

<br><br>
<form method="POST" action="index.php">
    <input type="hidden" name="action" value="ranking">
    <input type="hidden" name="subaction" value="player">
    <select name="date" onchange="this.form.submit();">
        <?php $date_selected = "";  ?>
        <?php $datadate = 0; ?>
        <?php foreach ($availableDatatade as $date) : ?>
            <?php $selected = "";?>
            <?php if (!isset($pub_date_selected) && !isset($datadate)) :?>
                <?php $datadate = $date; ?>
                <?php $date_selected = strftime("%d %b %Y %Hh", $date); ?>
            <?php endif; ?>
            <?php if ($pub_date == $date):?>
                <?php $selected = " selected "; ?>
                <?php $datadate = $date; ?>
                <?php $date_selected = strftime("%d %b %Y %Hh", $date); ?>
            <?php endif; ?>
            <option value="<?php echo $date;?>" <?php echo $selected ;?>>
                <?php echo strftime("%d %b %Y %Hh", $date);?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
<!-- vicsibilité colonne ou non ($datatable->toggleVisibility) -->
<a class="datatable-toggle-vis" data-column="2">Alliance</a> |
<a class="datatable-toggle-vis" data-column="3|4">Points</a> |
<a class="datatable-toggle-vis" data-column="5|6">Points Eco</a> |
<a class="datatable-toggle-vis" data-column="7|8">Points Tech.</a> |
<a class="datatable-toggle-vis" data-column="9|10">Points Mil.</a> |
<a class="datatable-toggle-vis" data-column="11|12">Points Mil. Construits</a> |
<a class="datatable-toggle-vis" data-column="13|14">Points Mil. Perdus</a> |
<a class="datatable-toggle-vis" data-column="15|16">Points Mil. Détruits</a> |
<a class="datatable-toggle-vis" data-column="17|18">Points Honneur</a> <br />
<!-- fin vicsibilité colonne ou non -->
</div>

<table id='<?php echo $idHtmlTable;?>' class='display' width='100%'>
    <thead>
    <tr>
        <th rowspan="2" class="c">Position</th>
        <th rowspan="2" class="c">Joueur</th>
        <th rowspan="2" class="c">Alliance</th>
        <th colspan="2"  class="c">Points</th>
        <th colspan="2" class="c">Points Eco.</th>
        <th colspan="2" class="c">Points Tech.</th>
        <th colspan="2"class="c">Points Mil.</th>
        <th colspan="2" class="c">Points Mil. Construits</th>
        <th colspan="2" class="c">Points Mil. Perdus</th>
        <th colspan="2"class="c">Points Mil. Détruits</th>
        <th colspan="2"class="c">Points Honneur</th>
    </tr>
    <tr>
        <th class="c">Rank</th>
        <th class="c">Points</th>
        <th class="c">Rank </th>
        <th class="c">Points</th>
        <th class="c">Rank</th>
        <th class="c">Points</th>
        <th class="c">Rank</th>
        <th class="c">Points</th>
        <th class="c">Rank</th>
        <th class="c">Points</th>
        <th class="c">Rank</th>
        <th class="c">Points</th>
        <th class="c">Rank</th>
        <th class="c">Points</th>
        <th class="c">Rank</th>
        <th class="c">Points</th>
    </tr>
    </thead>
    <!--<tfoot>
    <tr>
        <th>Position</th>
        <th>Joueur</th>
        <th>Alliance</th>
        <th>Points</th>
    </tr>
    </tfoot> -->
    <tbody>
    <?php foreach ($data_general as $row) : ?>
        <tr>
            <?php foreach ($row as $cell):?>
                <td>
                    <?php echo $cell;?>
                </td>
                 <?php endforeach ;?>
        </tr>
    <?php endforeach ; ?>

<?php echo $datatable->getHtml(); ?>


