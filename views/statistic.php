<?php
/***************************************************************************
 *    filename    : statistic.php
 *    desc.        :
 *    Author        : Kyser - http://ogsteam.fr/
 *    created        : 08/12/2005
 *    modified    : 13/04/2007 03:40:00
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    exit('Hacking attempt');
}

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

<table>
    <tr>
        <td class="c" colspan="<?php echo $galaxy_step * 2 + 2; ?>" align="center">État de la cartographie</td>
    </tr>

    <?php

    do {
        $galaxy_up = $galaxy_down + $galaxy_step;

        ?>

        <tr>
            <td class="c" width="45"></td>

            <?php

            if ($galaxy > intval($server_config['num_of_galaxies'])) {
                $galaxy_up = intval($server_config['num_of_galaxies']);
            }

            for ($i = $galaxy_down; $i < $galaxy_up; $i++) {
                echo '<td class="c" width="60" colspan="2">';

                if ($i <= intval($server_config['num_of_galaxies'])) {
                    echo "G$i";
                }

                echo '</td>';
            }

            ?>

            <td class="c" width="45"></td>
        </tr>

        <?php

        for ($system = 1; $system <= intval($server_config['num_of_systems']); $system = $system + $step) {

            $up = $system + $step - 1;

            if ($up > intval($server_config['num_of_systems'])) {
                $up = intval($server_config['num_of_systems']);
            }

            echo '<tr>';
            echo '<td class="c" align="center">' . $system . ' - ' . $up . '</td>';

            for ($galaxy = $galaxy_down; $galaxy < $galaxy_up; $galaxy++) {

                $link_colonized = '';
                $colonized = '-';
                $link_free = '';
                $free = '-';

                if ($galaxy > intval($server_config['num_of_galaxies'])) {
                    echo '<th></th><th></th>';
                    continue;
                }

                if ($galaxy_statistic[$galaxy][$system]['planet'] > 0) {

                    $link_colonized = 'onclick="window.location = \'index.php?action=galaxy_sector&amp;';
                    $link_colonized .= 'galaxy=' . $galaxy . '&amp;';
                    $link_colonized .= 'system_down=' . $system . '&amp;system_up=' . $up;
                    $link_colonized .= '\';"';

                    if ($galaxy_statistic[$galaxy][$system]['new']) {
                        $colonized = '<a style="cursor: pointer; text-decoration: blink; color: lime;">' . $galaxy_statistic[$galaxy][$system]['planet'] . '</a>';
                    } else {
                        $colonized = '<a style="cursor: pointer; color: lime;">' . $galaxy_statistic[$galaxy][$system]['planet'] . '</a>';
                    }
                }

                if ($galaxy_statistic[$galaxy][$system]['free'] > 0) {

                    $link_free = 'onclick="window.location = \'index.php?action=search&amp;type_search=colonization&amp;';
                    $link_free .= 'galaxy_down=' . $galaxy . '&galaxy_up=' . $galaxy . '&amp;';
                    $link_free .= 'system_down=' . $system . '&system_up=' . $up . '&amp;';
                    $link_free .= 'row_down&row_up';
                    $link_free .= '\';"';

                    $free = '<a style="cursor:pointer; color: orange;">' . $galaxy_statistic[$galaxy][$system]['free'] . '</a>';
                }

                echo '<th width="30" ' . $link_colonized . '>' . $colonized . '</th>';
                echo '<th width="30" ' . $link_free . '>' . $free . '</th>';
            }

            echo '<td class="c" align="center">' . $system . ' - ' . $up . '</td>';
            echo '</tr>';
        }

        $galaxy_down = $galaxy_up;
    } while ($galaxy_up < intval($server_config['num_of_galaxies']));

    $legend = '<table width="225">';
    $legend .= '<tr><td class="c" colspan="2" align="center" width="150">Légende</td></tr>';
    $legend .= '<tr><td class="c">Planètes répertoriées</td><th><font color="lime">xx</font></th></tr>';
    $legend .= '<tr><td class="c">Planètes colonisables</td><th><font color="orange"><b>xx</b></font></th></tr>';
    $legend .= '<tr><td class="c">Planètes mises à jour récemment</td><th style="color: lime; text-decoration: blink;"><b>xx</b></th></tr>';
    $legend .= '</table>';

    if (version_compare(phpversion(), '5.4.0', '>=')) {
        $legend = htmlentities($legend, ENT_COMPAT | ENT_HTML401, "UTF-8");
    } else {
        $legend = htmlentities($legend, ENT_COMPAT, "UTF-8");
    }

    ?>

    <tr>
        <td class="c" colspan="<?php echo $galaxy_step * 2 + 2; ?>" align="center">
            <a style="cursor:pointer"
               onmouseover="this.T_WIDTH=210;this.T_TEMP=0;return escape('<?php echo $legend; ?>')">Légende</a>
        </td>
    </tr>
</table>

<br/>

<table>
    <?php

    if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1 || $user_data['management_user'] == 1) {
        echo '<tr align="right">';
        echo '<td colspan="7"><a href="index.php?action=raz_ratio">Remise à zéro</a></td>';
        echo '</tr>';
    }

    ?>

    <tr align="center">
        <td class="c" width="100">Pseudos</td>
        <td class="c" width="100">Planètes</td>
        <td class="c" width="100">Rapports d'espionnage</td>
        <td class="c" width="100">Classement (lignes)</td>
        <td class="c" width="100">Recherches<br/>effectuées</td>
        <td class="c" width="100">Ratio</td>
        <td class="c" width="100">Xtense</td>
    </tr>

    <?php

    // Statistiques participation des membres actifs
    $query = 'SELECT
				SUM(planet_added_web + planet_added_ogs),
				SUM(spy_added_web + spy_added_ogs),
				SUM(rank_added_web + rank_added_ogs),
				SUM(search)
			  FROM ' . TABLE_USER;

    $result = $db->sql_query($query);
    list($planetimport, $spyimport, $rankimport, $search) = $db->sql_fetch_row($result);

    if ($planetimport == 0) $planetimport = 1;
    if ($spyimport == 0) $spyimport = 1;
    if ($rankimport == 0) $rankimport = 1;
    if ($search == 0) $search = 1;

    foreach ($user_statistic as $v) {

        $ratio_planet = ($v['planet_added_web'] + $v['planet_added_ogs']) / $planetimport;
        $ratio_spy = ($v['spy_added_web'] + $v['spy_added_ogs']) / $spyimport;
        $ratio_rank = ($v['rank_added_web'] + $v['rank_added_ogs']) / $rankimport;
        $ratio = (3 * $ratio_planet + 2 * $ratio_spy + $ratio_rank) / 6;

        $ratio_planet_penality = ($v['planet_added_web'] + $v['planet_added_ogs'] - $v['planet_exported']) / $planetimport;
        $ratio_spy_penality = (($v['spy_added_web'] + $v['spy_added_ogs']) - $v['spy_exported']) / $spyimport;
        $ratio_rank_penality = (($v['rank_added_web'] + $v['rank_added_ogs']) - $v['rank_exported']) / $rankimport;
        $ratio_penality = (3 * $ratio_planet_penality + 2 * $ratio_spy_penality + $ratio_rank_penality) / 6;

        $ratio_search = $v['search'] / $search;
        $ratio_searchpenality = ($ratio - $ratio_search);

        $couleur = $ratio_penality > 0 ? 'lime' : 'red';

        $result = ($ratio + $ratio_penality + $ratio_searchpenality) * 1000;

        if ($result < 0) {
            $color = 'red';
        } else if ($result == 0) {
            $color = 'white';
        } else if ($result < 100) {
            $color = 'orange';
        } else {
            $color = 'lime';
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

            if ($v['user_active'] == "1" && $v['user_admin'] == "0") {
                echo '<tr>';
                echo '<th style="color: ' . $color . '">' . $v['user_name'] . (($enable_members_view || $user_data['user_admin'] || $user_data['user_coadmin']) ? ' ' . $v['here'] : '') . '</th>';
                echo '<th>' . formate_number($v['planet_added_ogs']) . '</th>';
                echo '<th>' . formate_number($v['spy_added_ogs']) . '</th>';
                echo '<th>' . formate_number($v['rank_added_ogs']) . '</th>';
                echo '<th>' . formate_number($v['search']) . '</th>';
                echo '<th style="color: ' . $couleur . '">' . formate_number($result) . '</th>';
                echo '<th>' . $xtense_type . '</th>';
                echo '</tr>';
            }
        }
    }

    if (sizeof($user_statistic) > 10) {

        ?>

        <tr align="center">
            <td class="c">Pseudos</td>
            <td class="c">Planètes</td>
            <td class="c">Rapports d'espionnage</td>
            <td class="c">Classement (lignes)</td>
            <td class="c">Recherches effectuées</td>
            <td class="c">Ratio</td>
            <td class="c">Xtense</td>
        </tr>
        <?php
    }
    if ($enable_members_view || $user_data['user_admin'] || $user_data['user_coadmin']) {
        ?>
        <tr>
            <td colspan="7">(*) connecté sur le serveur<br/>(**) connecté avec Xtense ou Xtense Chrome Plugin</td>
        </tr>
        <?php
    }
    ?>

</table>
<?php require_once 'views/page_tail.php'; ?>