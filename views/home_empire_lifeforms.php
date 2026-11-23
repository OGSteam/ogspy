<?php

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

$lifeform_labels = [
    'buildings' => $lang['HOME_EMPIRE_LIFEFORM_BUILDINGS'] ?? 'Life-form buildings',
    'research' => $lang['HOME_EMPIRE_LIFEFORM_RESEARCH'] ?? 'Life-form research',
    'no_data' => $lang['HOME_EMPIRE_LIFEFORM_NO_DATA'] ?? 'No life-form data received',
];

$lifeform_categories = [
    'buildings' => lifeform_technology_ids($player_lifeforms, 'buildings'),
    'research' => lifeform_technology_ids($player_lifeforms, 'research'),
];

foreach ($lifeform_categories as $lifeform_category => $lifeform_technology_ids): ?>
<thead>
<tr>
    <th scope="colgroup" colspan="<?php print $colspan_planets ?>">
        <?php echo htmlspecialchars($lifeform_labels[$lifeform_category], ENT_QUOTES, 'UTF-8'); ?>
    </th>
</tr>
</thead>
<tbody>
<?php if ($lifeform_technology_ids === []): ?>
<tr>
    <td class="tdname"><?php echo htmlspecialchars($lifeform_labels['no_data'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td colspan="<?php print $colspan_planets_nine ?>" class="tdcontent">-</td>
</tr>
<?php else: ?>
    <?php foreach ($lifeform_technology_ids as $technology_id): ?>
        <?php
        $species_code = lifeform_species_code($technology_id);
        $species_label = $lang['HOME_EMPIRE_LIFEFORM_SPECIES_' . $species_code]
            ?? ($lang['HOME_EMPIRE_LIFEFORM_SPECIES_UNKNOWN'] ?? 'Unknown species');
        $technology_label = $lang['HOME_EMPIRE_LIFEFORM_TECHNOLOGY_' . $technology_id]
            ?? $species_label;
        ?>
<tr>
    <td class="tdname">
        <?php echo htmlspecialchars($technology_label, ENT_QUOTES, 'UTF-8'); ?>
    </td>
    <?php foreach (array_keys($player_planets) as $planet_id): ?>
        <td class="tdcontent">
            <?php if (isset($player_lifeforms[$planet_id][$lifeform_category][$technology_id])): ?>
                <?php echo (int)$player_lifeforms[$planet_id][$lifeform_category][$technology_id]; ?>
            <?php else: ?>
                <span class="og-alert">-</span>
            <?php endif; ?>
        </td>
    <?php endforeach; ?>
</tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>
<?php endforeach; ?>
