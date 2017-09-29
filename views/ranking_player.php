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


?>

<br><br>
<table id='<?php echo $idHtmlTable;?>' class='display' width='100%'>
    <thead>
    <tr>
        <th rowspan="2" class="c">Position</th>
        <th rowspan="2" class="c">Joueur</th>
        <th rowspan="2" class="c">Alliance</th>
        <th colspan="2" class="c">Points</th>
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

<?php  echo $datatable->getHtml(); ?>

