<?php

/**
 * Affichage Empire - Pages Espionnages favoris
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Ben.12
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$favorites = user_getfavorites_spy();
// todo : verifier fonctionnement $sort et $sort2 
if (!isset($sort2)) {
    $sort2 = 0;
} else {
    $sort2 = $sort2 != 0 ? 0 : 1;
}
?>

<table class="og-table og-medium-table">
    <thead>
        <tr>
            <th>
                <a href="index.php?action=home&amp;subaction=spy&amp;sort=1&amp;sort2=<?php echo $sort2; ?>">
                    <?php echo ($lang['HOME_SPY_POSITIONS']); ?>
                </a>
            </th>
            <th>
                <a href="index.php?action=home&amp;subaction=spy&amp;sort=2&amp;sort2=<?php echo $sort2; ?>">
                    <?php echo ($lang['HOME_SPY_ALLIANCES']); ?>
                </a>
            </th>
            <th>
                <a href="index.php?action=home&amp;subaction=spy&amp;sort=3&amp;sort2=<?php echo $sort2; ?>">
                    <?php echo ($lang['HOME_SPY_PLAYERS']); ?>
                </a>
            </th>
            <th>
                <a href="index.php?action=home&amp;subaction=spy&amp;sort=4&amp;sort2=<?php echo $sort2; ?>">
                    <?php echo ($lang['HOME_SPY_MOON']); ?>
                </a>
            </th>
            <th>
                &nbsp;
            </th>
            <th>
                <a href="index.php?action=home&amp;subaction=spy&amp;sort=5&amp;sort2=<?php echo $sort2; ?>">
                    <?php echo ($lang['HOME_SPY_UPDATE']); ?>
                </a>
            </th>
            <th>
                &nbsp;
            </th>
            <th>
                &nbsp;
            </th>
    </thead>
    <tbody>

        <?php foreach ($favorites as $v) : ?>
            <tr>
                <td>
                    <?php echo $v["spy_galaxy"] . ":" . $v["spy_system"] . ":" . $v["spy_row"]; ?>
                </td>
                <td>
                    <?php if ($v["ally"] == "") : ?>
                        &nbsp;
                    <?php else : ?>
                        <a href='index.php?action=search&amp;type_search=ally&amp;string_search=<?php echo $v["ally"]; ?>&strict=on'>
                            <?php echo $v["ally"]; ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($v["player"] == "") : ?>
                        &nbsp;
                    <?php else : ?>
                        <a href='index.php?action=search&amp;type_search=player&amp;string_search=<?php echo $v["player"]; ?>&strict=on'>
                            <?php echo  $v["player"]; ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($v["moon"] == "") : ?>
                        &nbsp;
                    <?php else : ?>
                        <span class="ogame-icon-moon">
                         M
                        </span>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($v["status"] == "") : ?>
                        &nbsp;
                    <?php else : ?>
                        <span class="ogame-status-<?php echo $v["status"]; ?>">
                            <?php echo $v["status"]; ?>
                        </span>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($v["datadate"]!= 0) : ?>
                        <?php echo date("d M Y H:i", $v["datadate"]) . " - " . $v["poster"]; ?>

                    <?php else : ?>
                        <?php echo $v["poster"]; ?>
                    <?php endif; ?>
                </td>

                <td>
                    <input class="og-button og-button-little" type='button' value='<?php echo $lang['HOME_SPY_SEE'];?>' onclick="window.open('index.php?action=show_reportspy&amp;galaxy=<?php echo $v['spy_galaxy'];?>&amp;system=<?php echo $v['spy_system'];?>&amp;row=<?php echo $v['spy_row'];?>&amp;spy_id=<?php echo $v['spy_id'];?>','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)" />
                </td>
                <td>
                    <!-- todo del_favorite_spy ne semble pas fonctionner -->
                    <input class="og-button og-button-danger og-button-little"type='button' value='<?php echo $lang['HOME_SPY_FAVDELETE'];?>' onclick="window.location = 'index.php?action=del_favorite_spy&amp;spy_id=<?php echo $v['spy_id'];?>&amp;info=1';" />
                </td>

                
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

