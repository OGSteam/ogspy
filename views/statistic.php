<?php global $user_data, $server_config, $lang;

/**
 * User statistic Page
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */


if (!defined('IN_SPYOGAME')) {
    exit('Hacking attempt');
}

use Ogsteam\Ogspy\Helper\ToolTip_Helper;

$ToolTip_Helper = new ToolTip_Helper();

$galaxy_step = $server_config['galaxy_by_line_stat'];
$galaxy_down = 1;
$galaxy = 1;
$step = $server_config['system_by_line_stat'];

$enable_stat_view = $server_config['enable_stat_view'];
$enable_members_view = $server_config['enable_members_view'];

$user_statistic = user_statistic();

$galaxy_statistic = galaxy_statistic($step);
$galaxy_statistic = $galaxy_statistic['map'];

require_once 'views/page_header.php';

?>
<div class="page_statistic">
    <table class="og-table og-medium-table og-table-galaxystatistic ">
        <thead>
            <tr>
                <th colspan="<?php echo($galaxy_step * 2 + 2); ?>" class="og-legend">
                    <?= $lang['STATS_TITLE']; ?>
                </th>
            </tr>
        </thead>
        <?php

        do {
            $galaxy_up = $galaxy_down + $galaxy_step; //todo passer sur une boucle for

        ?>
            <thead>
                <tr>
                    <th></th>
                    <?php $galaxy_up = ($galaxy > intval($server_config['num_of_galaxies'])) ?  intval($server_config['num_of_galaxies']) : $galaxy_up; ?>
                    <?php for ($i = $galaxy_down; $i < $galaxy_up; $i++) : ?>
                        <th colspan="2">
                            <?php if ($i <= intval($server_config['num_of_galaxies'])) : ?>
                                <?php echo   "G$i"; ?>
                            <?php endif; ?>
                        </th>
                    <?php endfor; ?>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php for ($system = 1; $system <= intval($server_config['num_of_systems']); $system = $system + $step) : ?>
                    <?php $up = $system + $step - 1; ?>
                    <?php $up = ($up > intval($server_config['num_of_systems'])) ? intval($server_config['num_of_systems']) : $up; ?>
                    <tr>
                        <td class="tdcontent">
                            <?php echo $system . ' - ' . $up; ?>
                        </td>

                        <?php for ($galaxy = $galaxy_down; $galaxy < $galaxy_up; $galaxy++) : ?>
                            <?php $link_colonized = ''; ?>
                            <?php $colonized = '-'; ?>
                            <?php $link_free = ''; ?>
                            <?php $free = '-'; ?>
                            <?php $tagclass = ''; ?>

                            <?php if ($galaxy > intval($server_config['num_of_galaxies'])) : ?>
                                <td class="tdcontent"></td>
                                <td class="tdcontent"></td>
                                <?php continue; ?>
                            <?php endif; ?>

                            <?php if ($galaxy_statistic[$galaxy][$system]['planet'] > 0) : ?>
                                <?php $tagclass .= " og-success "; ?>
                                <?php if ($galaxy_statistic[$galaxy][$system]['new']) : ?>
                                    <?php $tagclass .= " og-highlight "; ?>
                                <?php endif; ?>


                                <?php $link_colonized = 'onclick="window.location = \'index.php?action=galaxy_sector&amp;'; ?>
                                <?php $link_colonized .= 'galaxy=' . $galaxy . '&amp;'; ?>
                                <?php $link_colonized .= 'system_down=' . $system . '&amp;system_up=' . $up; ?>
                                <?php $link_colonized .= '\';"'; ?>

                            <?php endif; ?>
                            <td class="tdcontent" <?php echo $link_colonized ?>>
                                <a class="<?php echo $tagclass; ?>"><?php echo  $galaxy_statistic[$galaxy][$system]['planet']; ?></a>
                            </td>
                            <?php if ($galaxy_statistic[$galaxy][$system]['free'] > 0) : ?>
                                <?php $link_free = 'onclick="window.location = \'index.php?action=search&amp;type_search=colonization&amp;'; ?>
                                <?php $link_free .= 'galaxy_down=' . $galaxy . '&galaxy_up=' . $galaxy . '&amp;'; ?>
                                <?php $link_free .= 'system_down=' . $system . '&system_up=' . $up . '&amp;'; ?>
                                <?php $link_free .= 'row_down&row_up'; ?>
                                <?php $link_free .= '\';"'; ?>
                            <?php endif; ?>
                            <td class="tdcontent" <?php echo  $link_free; ?>>
                                <a class="og-warning"><?php echo $galaxy_statistic[$galaxy][$system]['free']; ?></a>
                            </td>
                        <?php endfor; ?>
                        <td class="tdcontent">
                            <?php echo  $system . ' - ' . $up; ?>
                        </td>

                    </tr>

                <?php endfor; ?>
            </tbody>
        <?php

            $galaxy_down = $galaxy_up;
        } while ($galaxy_up < intval($server_config['num_of_galaxies'])); //todo fin passer sur une boucle for

        ?>
        <?php
        $legend = '<table class="og-table og-small-table ">';
        $legend .= '<thead>';
        $legend .= '<tr><th colspan="2">' . $lang['STATS_LEGEND'] . '</th></tr>';
        $legend .= '</thead>';
        $legend .= '<tbody>';
        $legend .= '<tr><td>' . $lang['STATS_KNOWN_PLANETS'] . '</td><td class="tdcontent"><span class="og-success">xxx</span></td></tr>';
        $legend .= '<tr><td>' . $lang['STATS_FREE_PLANETS'] . '</td><td class="tdcontent"><span class="og-warning">xxx</span></td></tr>';
        $legend .= '<tr><td>' . $lang['STATS_UPDATED_PLANETS'] . '</td><td class="tdcontent"><span class="og-highlight">xxx</td></tr>';
        $legend .= '</tbody>';
        $legend .= '</table>';

        $legend = htmlentities($legend);

        //------------  ajout Tooltip ----------------
        $ToolTip_Helper->addTooltip("legende",  $legend);

        ?>
        <thead>
            <tr>
                <th class="og-legend" colspan="<?php echo $galaxy_step * 2 + 2; ?>">
                    <a <?php echo  $ToolTip_Helper->GetHTMLClassContent(); ?>><?php echo ($lang['STATS_LEGEND']); ?></a>
                </th>
            </tr>
        </thead>
    </table>



    <br />


    <table class="og-table og-medium-table og-table-connectstatistic ">
        <thead>
            <tr>
                <th><?php echo ($lang['STATS_USERNAME']); ?></th>
                <th><?php echo ($lang['STATS_PLANETS']); ?></th>
                <th><?php echo ($lang['STATS_SPY_REPORTS']); ?></th>
                <th><?php echo ($lang['STATS_RANKINGS']); ?></th>
                <th><?php echo ($lang['STATS_SEARCHINGS']); ?></th>
                <th><?php echo ($lang['STATS_RATIO']); ?></th>
                <th><?php echo ($lang['STATS_XTENSE']); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php

            // Statistiques participation des membres actifs
            //todo sortir de la vue
            $sumStats = (new \Ogsteam\Ogspy\Model\Statistics_Model())->get_users_stat_sum();
            $planetimport = ($sumStats["planetimport"] != 0) ? $sumStats["planetimport"] : 1;
            $spyimport = ($sumStats["spyimport"] != 0) ? $sumStats["spyimport"] : 1;
            $rankimport = ($sumStats["rankimport"] != 0) ? $sumStats["rankimport"] : 1;
            $search = ($sumStats["search"] != 0) ? $sumStats["search"] : 1;

            foreach ($user_statistic as $v) {
                $ratio_planet = ($v['planet_added_ogs']) / $planetimport;
                $ratio_spy = ($v['spy_added_ogs']) / $spyimport;
                $ratio_rank = ($v['rank_added_ogs']) / $rankimport;
                $ratio = (3 * $ratio_planet + 2 * $ratio_spy + $ratio_rank) / 6;

                $ratio_planet_penality = ($v['planet_added_ogs']) / $planetimport;
                $ratio_spy_penality = ($v['spy_added_ogs']) / $spyimport;
                $ratio_rank_penality = ($v['rank_added_ogs']) / $rankimport;
                $ratio_penality = (3 * $ratio_planet_penality + 2 * $ratio_spy_penality + $ratio_rank_penality) / 6;

                $ratio_search = $v['search'] / $search;
                $ratio_searchpenality = ($ratio - $ratio_search);

                $couleur = $ratio_penality > 0 ? 'lime' : 'red';

                $result = ($ratio + $ratio_penality + $ratio_searchpenality) * 1000;

                $classtag = "";

                if ($result < 0) {
                    $classtag = " og-danger ";
                } elseif ($result == 0) {
                    $classtag = "";
                } elseif ($result < 100) {
                    $classtag = " og-warning ";
                } else {
                    $classtag = " og-success ";
                }

                if ($enable_stat_view || ($v['user_name'] == $user_data['user_name']) || $user_data['user_admin'] || $user_data['user_coadmin']) {

                    switch ($v['xtense_type']) {
                        case 'FF':
                            $xtense_type = 'Firefox (' . $v['xtense_version'] . ')';
                            break;
                        case 'GM-FF':
                            $xtense_type = 'GreaseMonkey Firefox (' . $v['xtense_version'] . ')';
                            break;
                        case 'GM-GC':
                            $xtense_type = 'GreaseMonkey Google Chrome (' . $v['xtense_version'] . ')';
                            break;
                        case 'GM-OP':
                            $xtense_type = 'GreaseMonkey Opéra (' . $v['xtense_version'] . ')';
                            break;
                        default:
                            $xtense_type = 'N/A (' . $v['xtense_type'] . ')';
                    }

                    //todo voir si seulement admin on le visuel ...
                    if ($v['user_active'] == "1" || $v['user_admin'] == "1") {
                        echo '<tr>';
                        echo '<td><span class="' . $classtag . '">' . $v['user_name'] . (($enable_members_view || $user_data['user_admin'] || $user_data['user_coadmin']) ? ' ' . $v['here'] : '') . '</span></td>';
                        echo '<td>' . formate_number($v['planet_added_ogs']) . '</td>';
                        echo '<td>' . formate_number($v['spy_added_ogs']) . '</td>';
                        echo '<td>' . formate_number($v['rank_added_ogs']) . '</td>';
                        echo '<td>' . formate_number($v['search']) . '</td>';
                        echo '<td><span class="' . $classtag . '">' . formate_number($result) . '</span></td>';
                        echo '<td>' . $xtense_type . '</td>';
                        echo '</tr>';
                    }
                }
            }





            ?>
        </tbody>

        <?php if ($enable_members_view || $user_data['user_admin'] || $user_data['user_coadmin']) : ?>
            <?php
            $legend = '<table class="og-table og-small-table ">';
            $legend .= '<thead>';
            $legend .= '<tr><th colspan="2">' . $lang['STATS_LEGEND'] . '</th></tr>';
            $legend .= '</thead>';
            $legend .= '<tbody>';
            $legend .= '<tr><td>(*) ' . $lang['STATS_CONNECTED'] . '</td></tr>';
            $legend .= '<tr><td>(**) ' . $lang['STATS_CONNECTED_XTENSE'] . '</td></tr>';
            $legend .= '</tbody>';
            $legend .= '</table>';

            $legend = htmlentities($legend);

            //------------  ajout Tooltip ----------------
            $ToolTip_Helper->addTooltip("legende2",  $legend);

            ?>
            <thead>
                <tr>
                    <th class="og-legend" colspan="7">
                        <a <?php echo  $ToolTip_Helper->GetHTMLClassContent(); ?>><?php echo ($lang['STATS_LEGEND']); ?></a>
                    </th>
                </tr>
            </thead>
        <?php endif; ?>
    </table>

    <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1 || $user_data['management_user'] == 1) : ?>
        <table>
            <tr>
                <td>
                    <a class="og-button og-button-warning" href="index.php?action=raz_ratio"><?php echo $lang['STATS_RAZ']; ?></a>
                </td>
            </tr>
        </table>
    <?php endif; ?>

</div> <!-- fin div class="page_statistic" -->
<?php require_once 'views/page_tail.php'; ?>
