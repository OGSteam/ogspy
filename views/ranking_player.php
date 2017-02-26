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

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$data_general = galaxy_show_ranking('player_points_rank');
$data_economy = galaxy_show_ranking('player_eco_rank');
$data_research = galaxy_show_ranking('player_techno_rank');
$data_military = galaxy_show_ranking('player_military_rank');
$data_military_b = galaxy_show_ranking('player_military_built_rank');
$data_military_l = galaxy_show_ranking('player_military_lost_rank');
$data_military_d = galaxy_show_ranking('player_military_destroyed_rank');
$data_military_h = galaxy_show_ranking('player_honor_rank');

?>

<table id='player_rankings' class='display' width='100%'>
    <thead>
    <tr>
        <th>Date</th>
        <th>Position</th>
        <th>Joueur</th>
        <th>Alliance</th>
        <th>Points</th>
        <th>Sender</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th>Date</th>
        <th>Position</th>
        <th>Joueur</th>
        <th>Alliance</th>
        <th>Points</th>
        <th>Sender</th>
    </tr>
    </tfoot>
    <tbody>

<?php
    foreach($data_general as $v){
    echo "<tr>";
        foreach($v as $vv){
        echo "<td>{$vv}</td>";
        }
        echo "</tr>";
        }
        ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#player_rankings').DataTable();
    } );
</script>




