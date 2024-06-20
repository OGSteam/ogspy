<?php global $server_config, $lang;

/**
 * Panneau d'Administration : Paramètres et affichage des Journaux
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

use Ogsteam\Ogspy\Helper\ToolTip_Helper;

$ToolTip_Helper = new ToolTip_Helper();
$TtlCounter = 0;

$galaxy_step = $server_config['galaxy_by_line_ally'];
$galaxy_down = 1;
$galaxy = 1;
$step = $server_config['system_by_line_ally'];
$nb_colonnes_ally = $server_config['nb_colonnes_ally'];
$color_ally_n = $server_config['color_ally'];
$color_ally = explode("_", $color_ally_n);

$galaxy_ally_position = galaxy_ally_position($step);
$position = array_keys($galaxy_ally_position);

for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
    $options_[$i] = "<option></option>" . "\n";
}

$ally_list = galaxy_ally_listing();
foreach ($ally_list as $ally_name) {
    for ($i = 1; $i <= $nb_colonnes_ally; $i++) {
        $selected_[$i] = "";
        if (isset($pub_ally_[$i]) && ($ally_name == $pub_ally_[$i])) {
            $selected_[$i] = "selected";
        }
        $options_[$i] .= "<option " . $selected_[$i] . ">" . $ally_name . "</option>" . "\n";
    }
}

require_once("views/page_header.php");
?>
<div class="page_cartography">
    <form method="POST" action="index.php?action=cartography">
        <table class="og-table og-medium-table og-table-cartographyform ">
            <thead>
                <tr>
                    <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) : ?>
                        <th class="og-legend" colspan='2'>
                            <font color='<?php echo $color_ally[$i - 1]; ?>'>
                                <?php echo  $lang['CARTO_ALLY'] . $i; ?>
                            </font>
                        </th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) : ?>
                        <td>
                            <input type='text' name='select_ally_[<?php echo  $i; ?>]' onkeyup="autoComplete(this,this.form.elements['ally_[<?php echo  $i; ?>]'],'text',false)">
                        </td>
                        <td>
                            <select name='ally_[<?php echo $i; ?>]'>
                                <?php echo  $options_[$i]; ?>
                            </select>
                        </td>
                    <?php endfor; ?>
                </tr>

                <tr>
                    <td colspan="<?php echo $nb_colonnes_ally * 2; ?>">
                        <input type="submit" class="og-button" value="<?php echo ($lang['CARTO_DISPLAYPOSITIONS']); ?>">
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <br />

    <table class="og-table og-medium-table og-table-cartography ">
        <?php
        do {
            $galaxy_up = $galaxy_down + $galaxy_step; // todo remplacer do
        ?>
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <?php $galaxy_up = ($galaxy > intval($server_config['num_of_galaxies']))  ? intval($server_config['num_of_galaxies']) :   $galaxy_up; ?>
                    <?php for ($i = $galaxy_down; $i < $galaxy_up; $i++) : ?>
                        <th colspan="<?php echo  $nb_colonnes_ally; ?>">
                            <?php if ($i <= intval($server_config['num_of_galaxies'])) : ?>
                                <?php echo  "G$i"; ?>
                            <?php endif; ?>
                        </th>
                    <?php endfor; ?>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>

                <?php for ($system = 1; $system <= intval($server_config['num_of_systems']); $system = $system + $step) : ?>
                    <?php $up = $system + $step - 1; ?>
                    <?php $up = ($up > intval($server_config['num_of_systems'])) ? intval($server_config['num_of_systems']) : $up; ?>
                    <tr>
                        <td>
                            <?php echo $system . " - " . $up; ?>
                        </td>

                        <?php for ($galaxy = $galaxy_down; $galaxy < $galaxy_up; $galaxy++) : ?>
                            <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) : ?>
                                <?php $nb_player[$i - 1] = "&nbsp;"; ?>
                                <?php $tooltip[$i - 1] = ""; ?>
                            <?php endfor; ?>
                            <?php
                            $i = 0;
                            ?>
                            <?php foreach ($position as $ally_name) : ?>
                                <?php if ($galaxy_ally_position[$ally_name][$galaxy][$system]["planet"] > 0) : ?>
                                    <?php $tooltip[$i]  = '<table class="og-table og-small-table ">'; ?>
                                    <?php $tooltip[$i] .= "<thead>"; ?>
                                    <?php $tooltip[$i] .= "<tr><th colspan='2'>" . $lang['CARTO_PLAYER_POSITIONS'] . "</th></tr>"; ?>
                                    <?php $tooltip[$i] .= "</thead>"; ?>
                                    <?php $tooltip[$i] .= "<tbody>"; ?>
                                    <?php $last_player = ""; ?>
                                    <?php foreach ($galaxy_ally_position[$ally_name][$galaxy][$system]["population"] as $value) : ?>
                                        <?php $player = ""; ?>
                                        <?php if ($last_player != $value["player"]) : ?>
                                            <?php $player = "<a href=\"index.php?action=search&amp;type_search=player&amp;string_search=" . $value["player"] . "&strict=on\">" . $value["player"] . "</a>"; ?>
                                        <?php endif; ?>
                                        <?php $row = "<a href=\"index.php?action=galaxy&amp;galaxy=" . $value["galaxy"] . "&system=" . $value["system"] . "\">" . $value["galaxy"] . ":" . $value["system"] . ":" . $value["row"] . "</a>"; ?>
                                        <?php $tooltip[$i] .= "<tr><td  class=\'tdcontent\' >" . $player . "</td><td  class=\'tdcontent\' >" . $row . "</td></tr>"; ?>
                                        <?php $last_player = $value["player"]; ?>
                                    <?php endforeach; ?>
                                    <?php $tooltip[$i] .= "</tbody>"; ?>
                                    <?php $tooltip[$i] .= "</table>";; ?>

                                    <?php $ToolTip_Helper->addTooltip("ttp_cartographie_" . $value["player"] . "_" . $TtlCounter,  htmlentities($tooltip[$i])); ?>
                                    <?php $tooltip[$i] = $ToolTip_Helper->GetHTMLClassContent(); ?>
                                    <?php $TtlCounter++; ?>

                                    <?php $nb_player[$i] = $galaxy_ally_position[$ally_name][$galaxy][$system]["planet"]; ?>
                                <?php endif; ?>
                                <?php $i++; ?>

                            <?php endforeach; ?>
                            <?php for ($i = 1; $i <= $nb_colonnes_ally; $i++) : ?>
                                <?php $tagclass =  "td" . $i; ?>
                                <td class="<?php echo $tagclass; ?>">
                                    <a <?php echo $tooltip[$i - 1]; ?>>
                                        <font color='<?php echo  $color_ally[$i - 1]; ?>'>
                                            <?php echo  $nb_player[$i - 1]; ?>
                                        </font>
                                    </a>
                                </td>
                            <?php endfor; ?>
                        <?php endfor; ?>

                        <td><?php echo $system . " - " . $up; ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        <?php
            $galaxy_down = $galaxy_up;
        } while ($galaxy_up < intval($server_config['num_of_galaxies']));  // todo fin remplacer do
        ?>

        <thead>
            <tr>
            </tr>
        </thead>
    </table>





</div> <!-- fin div class="page_cartography" -->
<?php
require_once("views/page_tail.php");
?>
