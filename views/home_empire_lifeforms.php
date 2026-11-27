<?php

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

$lifeform_labels = [
    'resources' => $lang['HOME_EMPIRE_LIFEFORM_RESOURCES'] ?? 'Life-form resources',
    'species' => $lang['HOME_EMPIRE_LIFEFORM_SPECIES'] ?? 'Species',
    'population' => $lang['HOME_EMPIRE_LIFEFORM_POPULATION'] ?? 'Population',
    'food' => $lang['HOME_EMPIRE_LIFEFORM_FOOD'] ?? 'Food',
    'last_update' => $lang['HOME_EMPIRE_LIFEFORM_LAST_UPDATE'] ?? 'Last observation',
    'buildings' => $lang['HOME_EMPIRE_LIFEFORM_BUILDINGS'] ?? 'Life-form buildings',
    'research' => $lang['HOME_EMPIRE_LIFEFORM_RESEARCH'] ?? 'Life-form research',
    'no_data' => $lang['HOME_EMPIRE_LIFEFORM_NO_DATA'] ?? 'No life-form data received',
];

$has_lifeform_resources = false;
foreach ($player_lifeforms as $planet_lifeforms) {
    if (isset($planet_lifeforms['resources'])) {
        $has_lifeform_resources = true;
        break;
    }
}
?>
<thead>
<tr>
    <th scope="colgroup" colspan="<?php print $colspan_planets ?>">
        <?php echo htmlspecialchars($lifeform_labels['resources'], ENT_QUOTES, 'UTF-8'); ?>
    </th>
</tr>
</thead>
<tbody>
<?php if (!$has_lifeform_resources): ?>
<tr>
    <td class="tdname"><?php echo htmlspecialchars($lifeform_labels['no_data'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td colspan="<?php print $colspan_planets_nine ?>" class="tdcontent">-</td>
</tr>
<?php else: ?>
    <?php foreach (['species', 'population', 'food', 'last_update'] as $resource_name): ?>
<tr>
    <td class="tdname"><?php echo htmlspecialchars($lifeform_labels[$resource_name], ENT_QUOTES, 'UTF-8'); ?></td>
        <?php foreach (array_keys($player_planets) as $planet_id): ?>
            <?php $resources = $player_lifeforms[$planet_id]['resources'] ?? null; ?>
        <td class="tdcontent">
            <?php if ($resources === null): ?>
                <span class="og-alert">-</span>
            <?php elseif ($resource_name === 'species'): ?>
                <?php
                $species_code = $resources['species_code'] ?? 'UNKNOWN';
                $species_label = $lang['HOME_EMPIRE_LIFEFORM_SPECIES_' . $species_code]
                    ?? ($lang['HOME_EMPIRE_LIFEFORM_SPECIES_UNKNOWN'] ?? 'Unknown species');
                echo htmlspecialchars($species_label, ENT_QUOTES, 'UTF-8');
                ?>
            <?php elseif ($resource_name === 'last_update'): ?>
                <?php echo ($resources['last_update'] ?? 0) > 0
                    ? date('d/m/Y H:i', (int)$resources['last_update'])
                    : '-'; ?>
            <?php else: ?>
                <?php echo lifeformFormatAmount($resources[$resource_name] ?? null); ?>
            <?php endif; ?>
        </td>
        <?php endforeach; ?>
</tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>
<?php

$lifeform_categories = [
    'buildings' => lifeform_technology_groups($player_lifeforms, 'buildings'),
    'research' => lifeform_technology_groups($player_lifeforms, 'research'),
];

foreach ($lifeform_categories as $lifeform_category => $lifeform_species_groups): ?>
<thead>
<tr>
    <th scope="colgroup" colspan="<?php print $colspan_planets ?>">
        <?php echo htmlspecialchars($lifeform_labels[$lifeform_category], ENT_QUOTES, 'UTF-8'); ?>
    </th>
</tr>
</thead>
<?php if ($lifeform_species_groups === []): ?>
<tbody>
<tr>
    <td class="tdname"><?php echo htmlspecialchars($lifeform_labels['no_data'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td colspan="<?php print $colspan_planets_nine ?>" class="tdcontent">-</td>
</tr>
</tbody>
<?php else: ?>
    <?php $show_species_groups = count($lifeform_species_groups) > 1; ?>
    <?php foreach ($lifeform_species_groups as $species_code => $lifeform_technology_ids): ?>
        <?php
        $species_label = $lang['HOME_EMPIRE_LIFEFORM_SPECIES_' . $species_code]
            ?? ($lang['HOME_EMPIRE_LIFEFORM_SPECIES_UNKNOWN'] ?? 'Unknown species');
        ?>
<tbody>
        <?php if ($show_species_groups): ?>
<tr class="lifeform-species-group">
    <th scope="rowgroup" colspan="<?php print $colspan_planets ?>">
        <?php echo htmlspecialchars($species_label, ENT_QUOTES, 'UTF-8'); ?>
    </th>
</tr>
        <?php endif; ?>
        <?php foreach ($lifeform_technology_ids as $technology_id): ?>
            <?php
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
</tbody>
    <?php endforeach; ?>
<?php endif; ?>
<?php endforeach; ?>
