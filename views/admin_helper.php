<?php
/**
 * Panneau administration des options Helpers
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
$thelper = get_Helpers();
?>

<table align="center">
    <tr>
        <td class="c" colspan="3" ><?php echo $lang['ADMIN_HELPER_HERE'] ; ?></td>
    </tr>


    <?php foreach ($thelper as $helper) : ?>
        <tr>
            <th width="20%">
                <?php echo $helper["name"]; ?>
            </th>
            <th width="20%">
                <?php echo $helper["version"]; ?>
            </th>
            <th width="60%">
                <?php echo $helper["description"]; ?>
            </th>

        </tr>
    <?php endforeach; ?>


</table>