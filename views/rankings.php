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



$idHtmlTable = "rankings";
$datatable = new \Ogsteam\Ogspy\datatable_js($idHtmlTable);

// activation ou desactivation des options
$datatable->enableFeatures(array("AutoWidth","Info", "ScrollX","ScrollY","LengthChange","Ordering","Paging","Searching"));
$datatable->disableFeatures(array());
$datatable->setFormatNumber(true);
$datatable->toggleVisibility("datatable-toggle-vis","data-column");
$datatable->setPaginate(array(100, 500, 1000, -1));


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
<?php if($is_player) :?>
    <a class="datatable-toggle-vis" data-column="2"<?php echo  $lang['RANK_ALLY']?>></a> |
    <a class="datatable-toggle-vis" data-column="3|4"><?php echo  $lang['RANK_GENERAL']?></a> |
    <a class="datatable-toggle-vis" data-column="5|6"><?php echo  $lang['RANK_ECONOMY']?></a> |
    <a class="datatable-toggle-vis" data-column="7|8"><?php echo  $lang['RANK_RESEARCH']?></a> |
    <a class="datatable-toggle-vis" data-column="9|10"><?php echo  $lang['RANK_MILITARY']?></a> |
    <a class="datatable-toggle-vis" data-column="11|12"><?php echo  $lang['RANK_MILITARY_BUILT']?></a> |
    <a class="datatable-toggle-vis" data-column="13|14"><?php echo  $lang['RANK_MILITARY_LOST']?></a> |
    <a class="datatable-toggle-vis" data-column="15|16"><?php echo  $lang['RANK_MILITARY_DESTROYED']?></a> |
    <a class="datatable-toggle-vis" data-column="17|18"><?php echo  $lang['RANK_MILITARY_HONOR']?></a> <br />
<?php else : ?>
    <a class="datatable-toggle-vis" data-column="2"><?php echo  $lang['RANK_MEMBER']?></a> |
    <a class="datatable-toggle-vis" data-column="3|4|5"><?php echo  $lang['RANK_GENERAL']?></a> |
    <a class="datatable-toggle-vis" data-column="6|7|8"><?php echo  $lang['RANK_ECONOMY']?> </a> |
    <a class="datatable-toggle-vis" data-column="9|10|11"><?php echo  $lang['RANK_RESEARCH']?> </a> |
    <a class="datatable-toggle-vis" data-column="12|13|14"><?php echo  $lang['RANK_MILITARY']?></a> |
    <a class="datatable-toggle-vis" data-column="15|16|17"><?php echo  $lang['RANK_MILITARY_BUILT']?> </a> |
    <a class="datatable-toggle-vis" data-column="18|19|20"><?php echo  $lang['RANK_MILITARY_LOST']?> </a> |
    <a class="datatable-toggle-vis" data-column="20|21|22"><?php echo  $lang['RANK_MILITARY_DESTROYED']?> </a> |
    <a class="datatable-toggle-vis" data-column="23|24|25"><?php echo  $lang['RANK_MILITARY_HONOR']?></a> <br />
<?php endif ; ?>
<!-- fin vicsibilité colonne ou non -->
</div>

<table id='<?php echo $idHtmlTable;?>' class='display' width='100%'>
    <thead>
    <tr>
        <th rowspan="2" class="c"><?php echo  $lang['RANK_ID']?></th>
        <?php if($is_player) :?>
            <?php $colspan = 2;?>
            <th rowspan="2" class="c"><?php echo  $lang['RANK_PLAYER']?></th>
            <th rowspan="2" class="c"><?php echo  $lang['RANK_ALLY']?></th>
        <?php else : ?>
            <?php $colspan = 3;?>
            <th rowspan="2" class="c"><?php echo  $lang['RANK_ALLY']?></th>
            <th rowspan="2" class="c"><?php echo  $lang['RANK_MEMBER']?></th>
        <?php endif ; ?>
        <th colspan="<?php echo $colspan;?>"  class="c"><?php echo  $lang['RANK_GENERAL']?></th>
        <th colspan="<?php echo $colspan;?>" class="c"><?php echo  $lang['RANK_ECONOMY']?></th>
        <th colspan="<?php echo $colspan;?>" class="c"><?php echo  $lang['RANK_RESEARCH']?></th>
        <th colspan="<?php echo $colspan;?>"class="c"><?php echo  $lang['RANK_MILITARY']?></th>
        <th colspan="<?php echo $colspan;?>" class="c"><?php echo  $lang['RANK_MILITARY_BUILT']?></th>
        <th colspan="<?php echo $colspan;?>" class="c"><?php echo  $lang['RANK_MILITARY_LOST']?></th>
        <th colspan="<?php echo $colspan;?>"class="c"><?php echo  $lang['RANK_MILITARY_DESTROYED']?></th>
        <th colspan="<?php echo $colspan;?>"class="c"><?php echo  $lang['RANK_MILITARY_HONOR']?></th>
    </tr>
    <tr>
        <?php  for( $i= 0 ; $i < 8 ; $i++ ) : ?>
            <th class="c">Rank</th>
            <th class="c">Points</th>
            <?php if(!$is_player) :?>
                <th class="c"> </th>
            <?php endif ; ?>
        <?php endfor; ?>
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


