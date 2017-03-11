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

?>
<br><br>
<table id='player_rankings' class='display' width='100%'>
    <thead>
    <tr>
        <th>Position</th>
        <th>Joueur</th>
        <th>Alliance</th>
        <th>Points</th>
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
        $('#player_rankings').DataTable({
            "language": {
                "url": "./assets/js/dataTables.french.lang.json"
            }


        });
    } );
</script>




