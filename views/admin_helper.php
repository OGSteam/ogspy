<?php
/**
 * Panneau administration des options Helpers
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
$thelper = get_Helpers();
?>
<div class="page_adminhelper">
    <table>
        <thead>
        <tr>
            <th colspan="3"><?php echo $lang['ADMIN_HELPER_HERE']; ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($thelper as $helper) : ?>
            <tr>
                <th>
                    <?php echo $helper["name"]; ?>
                </th>
                <td>
                    <?php echo $helper["version"]; ?>
                </td>
                <td>
                    <?php echo $helper["description"]; ?>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


