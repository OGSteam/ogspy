<?php
/**
 * Panneau d'Administration : Paramètres et affichage des Journaux
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$galaxy_step = $server_config['galaxy_by_line_ally'];
$galaxy_down = 1;
$galaxy = 1;
$step = $server_config['system_by_line_ally'];
$nb_colonnes_ally = $server_config['nb_colonnes_ally'];
$color_ally_n = $server_config['color_ally'];
$color_ally = explode("_", $color_ally_n);

$galaxy_ally_position = galaxy_ally_position($step);
$position = array_keys($galaxy_ally_position);
$ally = "";
for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
    $ally .= $pub_ally_[$i];
    $options_[$i] = "<option></option>" . "\n";

}

$ally_list = galaxy_ally_listing();
foreach ($ally_list as $ally_name) {
    for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
        $selected_[$i] = "";
        if ($ally_name == $pub_ally_[$i]) {
            $selected_[$i] = "selected";
        }
        $options_[$i] .= "<option " . $selected_[$i] . ">" . $ally_name . "</option>" . "\n";
    }
}

require_once("views/page_header.php");
?>
<script language="JavaScript" src="js/autocomplete.js"></script>

<form method="POST" action="index.php?action=cartography">
    <table>
        <tr>
            <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
                echo "<td class='c' align='center' colspan='2' width='300'><font color='" . $color_ally[$i - 1] . "'>" . $lang['CARTO_ALLY'] . $i . "</font></td>";
            }
            ?>
        </tr>
        <tr>
            <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
                echo "<th width='125'><input type='text' name='select_ally_[" . $i . "]' onkeyup=\"autoComplete(this,this.form.elements['ally_[" . $i . "]'],'text',false)\"></th>";
                echo "<th width='175'><select name='ally_[" . $i . "]'>" . $options_[$i] . "</select></th>";
            }
            ?>
        </tr>
        <tr>
            <td class="c" colspan="<?php echo $nb_colonnes_ally * 2; ?>" align="center"><input type="submit"
                                                                                               value="<?php echo($lang['CARTO_DISPLAYPOSITIONS']); ?>">
            </td>
        </tr>
    </table>
</form>
<br/>
<table border='1'>
    <?php
    do {
        $galaxy_up = $galaxy_down + $galaxy_step;
        ?>
        <tr>
            <td class="c" width="45">&nbsp;</td>

            <?php
            if ($galaxy > intval($server_config['num_of_galaxies'])) {
                            $galaxy_up = intval($server_config['num_of_galaxies']);
            }
            for ($i = $galaxy_down; $i < $galaxy_up; $i++) {
                echo "<td class='c' width='60' colspan=" . $nb_colonnes_ally . ">";
                if ($i <= intval($server_config['num_of_galaxies'])) {
                                    echo "G$i";
                }
                echo "</td>";
            }
            ?>

            <td class="c" width="45">&nbsp;</td>
        </tr>
        <?php
        for ($system = 1; $system <= intval($server_config['num_of_systems']); $system = $system + $step) {
            $up = $system + $step - 1;
            if ($up > intval($server_config['num_of_systems'])) {
                $up = intval($server_config['num_of_systems']);
            }

            echo "<tr>" . "\n";
            echo "\t" . "<td class='c' align='center' nowrap>" . $system . " - " . $up . "</td>";
            for ($galaxy = $galaxy_down; $galaxy < $galaxy_up; $galaxy++) {
                for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
                    $nb_player[$i - 1] = "&nbsp;";
                    $tooltip[$i - 1] = "";
                }
                $i = 0;
                foreach ($position as $ally_name) {
                    if ($galaxy_ally_position[$ally_name][$galaxy][$system]["planet"] > 0) {
                        $tooltip[$i] = "<table width=\'200\'>";
                        $tooltip[$i] .= "<tr><td class=\'c\' colspan=\'2\' align=\'center\'>" . $lang['CARTO_PLAYER_POSITIONS'] . "</td></tr>";
                        $last_player = "";
                        foreach ($galaxy_ally_position[$ally_name][$galaxy][$system]["population"] as $value) {
                            $player = "";
                            if ($last_player != $value["player"]) {
                                $player = "<a href=\"index.php?action=search&amp;type_search=player&amp;string_search=" . $value["player"] . "&strict=on\">" . $value["player"] . "</a>";
                            }
                            $row = "<a href=\"index.php?action=galaxy&amp;galaxy=" . $value["galaxy"] . "&system=" . $value["system"] . "\">" . $value["galaxy"] . ":" . $value["system"] . ":" . $value["row"] . "</a>";

                            $tooltip[$i] .= "<tr><td class=\'c\' align=\'center\'>" . $player . "</td><th>" . $row . "</th></tr>";
                            $last_player = $value["player"];
                        }
                        $tooltip[$i] .= "</table>";
                        if (version_compare(phpversion(), '5.4.0', '>=')) {
                            $tooltip[$i] = " onmouseover=\"this.T_WIDTH=210;this.T_TEMP=15000;return escape('" . htmlentities($tooltip[$i], ENT_COMPAT | ENT_HTML401, "UTF-8") . "')\"";
                        } else {
                            $tooltip[$i] = " onmouseover=\"this.T_WIDTH=210;this.T_TEMP=15000;return escape('" . htmlentities($tooltip[$i], ENT_COMPAT, "UTF-8") . "')\"";
                        }

                        $nb_player[$i] = $galaxy_ally_position[$ally_name][$galaxy][$system]["planet"];
                    }
                    $i++;
                }
                for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
                    echo "\t" . "<th width='20'><a style='cursor:pointer'" . $tooltip[$i - 1] . "><font color='" . $color_ally[$i - 1] . "'>" . $nb_player[$i - 1] . "</font></a></th>" . "\n";
                }
            }
            echo "\t" . "<td class='c' align='center' nowrap>" . $system . " - " . $up . "</td>";
            echo "</tr>" . "\n";
        }
        $galaxy_down = $galaxy_up;
    } while ($galaxy_up < intval($server_config['num_of_galaxies']));
    ?>
    <tr>
        <td class="c" colspan="<?php echo $galaxy_step * $nb_colonnes_ally + 2; ?>">&nbsp;</td>
    </tr>
</table>
<?php
require_once("views/page_tail.php");
?>
