<?php
/**
 * Panneau administration des options Modules
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Aeris
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$mod_list = mod_list();
?>
<!--Mod activé/installé -->
<table class="og-table og-medium-table">
    <thead>
        <tr>
            <th  colspan="6" ><?php echo ($lang['ADMIN_MOD_LIST']); ?></th>
        </tr>
        <tr>
            <th  colspan="5" ><?php echo ($lang['ADMIN_MOD_USER']); ?></th>
            <th><?php echo ($lang['ADMIN_MOD_MENUVIEW']); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $mods = $mod_list["actived"]; ?>
        <?php foreach ($mods as $mod) : ?>
            <?php if ($mod["admin_only"] == 0) : ?>
                <tr>
                    <td>
                        <?php echo "(" . $mod['position'] . ') ' . $mod["title"] . " (" . $mod["version"] . ")"; ?>
                    </td>
                    <td>
                        <a href='index.php?action=mod_up&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <img src='images/asc.png' title='Monter'>
                        </a>
                        &nbsp;
                        <a href='index.php?action=mod_down&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <img src='images/desc.png' title='Descendre'>
                        </a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_disable&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_DISABLE']; ?>
                        </a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_uninstall&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_REMOVE']; ?>
                        </a>
                    </td>

                    <td>
                        <?php if (!$mod["up_to_date"]) : ?>
                            <a href='index.php?action=mod_update&amp;mod_id=<?php echo $mod['id']; ?>'>
                                <?php echo $lang['ADMIN_MOD_UPDATE']; ?>
                            </a>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href='index.php?action=mod_admin&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_NORMAL']; ?>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
    <thead>

        <tr>
            <th  colspan="5" ><?php echo ($lang['ADMIN_MOD_ADMIN']); ?></th>
            <th><?php echo ($lang['ADMIN_MOD_MENUVIEW']); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mods as $mod) : ?>
            <?php if ($mod["admin_only"] == 1) : ?>
                <tr>
                    <td>
                        <?php echo "(" . $mod['position'] . ') ' . $mod["title"] . " (" . $mod["version"] . ")"; ?>
                    </td>
                    <td>
                        <a href='index.php?action=mod_up&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <img src='images/asc.png' title='Monter'>
                        </a>
                        &nbsp;
                        <a href='index.php?action=mod_down&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <img src='images/desc.png' title='Descendre'>
                        </a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_disable&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_DISABLE']; ?>
                        </a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_uninstall&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_REMOVE']; ?>
                        </a>
                    </td>

                    <td>
                        <?php if (!$mod["up_to_date"]) : ?>
                            <a href='index.php?action=mod_update&amp;mod_id=<?php echo $mod['id']; ?>'>
                                <?php echo $lang['ADMIN_MOD_UPDATE']; ?>
                            </a>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href='index.php?action=mod_admin&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_ADMIN']; ?>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<!--Mod desactivé/installé -->
<?php $mods = $mod_list["disabled"]; ?>
<?php if (count($mods) != 0): ?>
    <table class="og-table og-medium-table">
        <thead>
            <tr>
                <th  colspan="4" ><?php echo ($lang['ADMIN_MOD_LIST_INACTIVE']); ?></th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($mods as $mod) : ?>
                <tr>
                    <td>
                        <?php echo $mod["title"] . " (" . $mod["version"] . ")"; ?>
                    </td>
                    <td>
                        <a href='index.php?action=mod_active&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_ENABLE']; ?>
                        </a>
                    </td>
                    <td>
                        <a href='index.php?action=mod_uninstall&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_REMOVE']; ?>
                        </a>
                    </td>
                    <td>
                        <?php if (!$mod["up_to_date"]) : ?>
                            <a href='index.php?action=mod_update&amp;mod_id=<?php echo $mod['id']; ?>'>
                                <?php echo $lang['ADMIN_MOD_UPDATE']; ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!--Mod non installé -->
<?php $mods = $mod_list["install"]; ?>
<?php if (count($mods) != 0): ?>
    <table class="og-table og-medium-table">
        <thead>
            <tr>
                <th  colspan="2" ><?php echo ($lang['ADMIN_MOD_NOT_INSTALLED']); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mods as $mod) : ?>
                <tr>
                    <td>
                        <?php echo $mod["title"]; ?>
                    </td>
                    <td>
                        <a href='index.php?action=mod_install&amp;directory=<?php echo $mod['directory']; ?>'>
                            <?php echo $lang['ADMIN_MOD_INSTALL']; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<!--Mod non valide -->
<?php $mods = $mod_list["wrong"]; ?>
<?php if (count($mods) != 0): ?>
    <table class="og-table og-medium-table">
        <thead>
            <tr>
                <th  colspan="2" ><?php echo ($lang['ADMIN_MOD_INVALID']); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mods as $mod) : ?>
                <tr>
                    <td>
                        <?php echo $mod["title"]; ?>
                    </td>
                    <td>
                        <a href='index.php?action=mod_uninstall&amp;mod_id=<?php echo $mod['id']; ?>'>
                            <?php echo $lang['ADMIN_MOD_REMOVE']; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
