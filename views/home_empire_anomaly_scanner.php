<?php

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}
?>
<tr>
    <td class="tdname">
        <?php echo $lang['HOME_EMPIRE_ANOMALY_SCANNER']; ?>
    </td>
    <?php foreach ($player_planets as $planet_id => $planet): ?>
        <td class="tdcontent">
            <span id="scanner_<?php echo (int)$planet_id; ?>">
                <?php echo (int)($planet['Scanner'] ?? 0); ?>
            </span>
        </td>
    <?php endforeach; ?>
</tr>
