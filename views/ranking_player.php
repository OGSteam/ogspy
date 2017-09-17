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

$idHtmlTable = "player_rankings";
$data_general = galaxy_show_ranking();
$datatable = new \Ogsteam\Ogspy\datatable_js($idHtmlTable)


?>
<br><br>
<table id='<?php echo $idHtmlTable;?>' class='display' width='100%'>
    <thead>
    <tr>
        <th class="c">Position</th>
        <th class="c">Joueur</th>
        <th class="c">Alliance</th>
        <th class="c">Points</th>
        <th class="c">Points Eco.</th>
        <th class="c">Points Tech.</th>
        <th class="c">Points Mil.</th>
        <th class="c">Points Mil. Construits</th>
        <th class="c">Points Mil. Perdus</th>
        <th class="c">Points Mil. Détruits</th>
        <th class="c">Points Honneur</th>
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

    <?php foreach ($data_general as $v) : ?>
        <tr>
            <?php foreach ($v as $vv):?>
                <td>
                    <?php echo $vv;?>
                </td>
            <?php endforeach ;?>
        </tr>
    <?php endforeach ; ?>


<?php echo $datatable->getHtml(); ?>





