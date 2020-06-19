<?php
/**
 * Panneau administration des options Modules
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Aeris
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$mod_list = mod_list();
$modsactived = $mod_list["actived"];
$modsdisabled = $mod_list["disabled"];
$modsinstall = $mod_list["install"];
$modswrong = $mod_list["wrong"];
?>
<div class="page_adminmod">
    <table>
        <thead>
        <tr>
            <th colspan="6"><?php echo($lang['ADMIN_MOD_LIST']); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th><?php echo($lang['ADMIN_MOD_USER']); ?></th>
            <th colspan="4"></th>
            <th><?php echo($lang['ADMIN_MOD_MENUVIEW']); ?></th>
        </tr>

        <!-- mods normaux -->
        <?php foreach ($modsactived as $mod): ?>
            <?php if ($mod["admin_only"] == 0)  : ?>
                <tr>
                    <td>
                        <?php echo $mod["title"]; ?> (<?php echo $mod["version"]; ?>)
                    </td>
                    <td>
                        <a href='index.php?action=mod_up&amp;mod_id=<?php echo $mod['id']; ?>'><img src='images/asc.png'
                                                                                                    alt='Monter'
                                                                                                    title='Monter'></a>
                        &nbsp;
                        <a href='index.php?action=mod_down&amp;mod_id=<?php echo $mod['id']; ?>'><img
                                    src='images/desc.png' alt='Descendre' title='Descendre'></a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_disable&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_DISABLE']; ?></a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_uninstall&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_REMOVE']; ?></a>
                    </td>
                    <td>
                        <?php if (!$mod["up_to_date"]) : ?>
                            <a href='index.php?action=mod_update&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_UPDATE']; ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href='index.php?action=mod_admin&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_NORMAL']; ?></a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <!-- mods admins -->
        <tr>
            <th><?php echo $lang['ADMIN_MOD_ADMIN']; ?></th>
            <th colspan="4"></th>
            <th><?php echo($lang['ADMIN_MOD_MENUVIEW']); ?></th>
        </tr>
        <?php foreach ($modsactived as $mod): ?>
            <?php if ($mod["admin_only"] == 1)  : ?>
                <tr>
                    <td>
                        <?php echo $mod["title"]; ?> (<?php echo $mod["version"]; ?>)
                    </td>
                    <td>
                        <a href='index.php?action=mod_up&amp;mod_id=<?php echo $mod['id']; ?>'><img src='images/asc.png'
                                                                                                    alt='Monter'
                                                                                                    title='Monter'></a>
                        &nbsp;
                        <a href='index.php?action=mod_down&amp;mod_id=<?php echo $mod['id']; ?>'><img
                                    src='images/desc.png' alt='Descendre' title='Descendre'></a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_disable&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_DISABLE']; ?></a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_uninstall&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_REMOVE']; ?></a>
                    </td>
                    <td>
                        <?php if (!$mod["up_to_date"]) : ?>
                            <a href='index.php?action=mod_update&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_UPDATE']; ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href='index.php?action=mod_normal&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_ADMIN']; ?></a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (count($modsdisabled) != 0) :  //affichage si disponible?>
        <table>
            <thead>
            <!-- mods inactifs -->
            <tr>
                <th colspan="6"><?php echo($lang['ADMIN_MOD_LIST_INACTIVE']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($modsdisabled as $mod): ?>
                <tr>
                    <td>
                        <?php echo $mod["title"]; ?> (<?php echo $mod["version"]; ?>)
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        <a href='index.php?action=mod_active&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_ENABLE']; ?></a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_uninstall&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_REMOVE']; ?></a>
                    </td>
                    <td>
                        <?php if (!$mod["up_to_date"]) : ?>
                            <a href='index.php?action=mod_update&amp;mod_id=<?php echo $mod['id']; ?>'><?php echo $lang['ADMIN_MOD_UPDATE']; ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- mods a installer  -->
    <?php if (count($modsinstall) != 0) :  //affichage si disponible?>
        <table>
            <thead>
            <tr>
                <th colspan="6"><?php echo($lang['ADMIN_MOD_NOT_INSTALLED']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($modsinstall as $mod) : ?>
                <tr>
                    <td>
                        <?php echo $mod["title"]; ?>
                    </td>
                    <td colspan="5">
                        <a href='index.php?action=mod_install&amp;directory=<?php echo $mod['directory']; ?>'><?php echo $lang['ADMIN_MOD_INSTALL']; ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- mods en erreur  -->
    <?php if (count($modswrong) != 0) :  //affichage si disponible?>
        <table>
            <thead>
            <tr>
                <th colspan="6"><?php echo($lang['ADMIN_MOD_INVALID']); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($modswrong as $mod) : ?>
                <tr>
                    <td>
                        <?php echo $mod["title"]; ?>
                    </td>
                    <td colspan="5">
                        <a href='index.php?action=mod_uninstall&amp;directory=<?php echo $mod['directory']; ?>'><?php echo $lang['ADMIN_MOD_REMOVE']; ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    <?php endif; ?>

</div>