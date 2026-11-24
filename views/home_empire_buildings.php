<?php

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

$planet_building_rows = [
    ['code' => 'M', 'label' => 'HOME_EMPIRE_MINE_METAL', 'id' => '15'],
    ['code' => 'C', 'label' => 'HOME_EMPIRE_MINE_CRYSTAL', 'id' => '16'],
    ['code' => 'D', 'label' => 'HOME_EMPIRE_MINE_DEUT', 'id' => '17'],
    ['code' => 'HM', 'label' => 'HOME_EMPIRE_METALSTORAGE', 'id' => '3'],
    ['code' => 'HC', 'label' => 'HOME_EMPIRE_CRYSTALSTORAGE', 'id' => '4'],
    ['code' => 'HD', 'label' => 'HOME_EMPIRE_DEUTSTORAGE', 'id' => '5'],
    ['code' => 'CES', 'label' => 'HOME_EMPIRE_SOLAR_PLANT', 'id' => '20'],
    ['code' => 'CEF', 'label' => 'HOME_EMPIRE_FUSION_PLANT', 'id' => '21'],
    ['code' => 'UdR', 'label' => 'HOME_EMPIRE_ROBOTS_PLANT', 'id' => '1'],
    ['code' => 'UdN', 'label' => 'HOME_EMPIRE_NANITES_PLANT', 'id' => '22'],
    ['code' => 'CSp', 'label' => 'HOME_EMPIRE_SHIPYARD', 'id' => '2'],
    ['code' => 'Lab', 'label' => 'HOME_EMPIRE_RESEARCHLAB', 'id' => '23'],
    ['code' => 'DdR', 'label' => 'HOME_EMPIRE_ALLIANCEDEPOT', 'id' => '42', 'enabled' => $server_config['ddr'] == 1],
    ['code' => 'Ter', 'label' => 'HOME_EMPIRE_TERRAFORMER', 'id' => '24'],
    ['code' => 'Silo', 'label' => 'HOME_EMPIRE_MISSILESSILO', 'id' => '25'],
    ['code' => 'Dock', 'label' => 'HOME_EMPIRE_DOCK', 'id' => 'dock'],
    ['code' => 'Scanner', 'label' => 'HOME_EMPIRE_ANOMALY_SCANNER', 'id' => 'scanner'],
];
?>
<thead>
<tr>
    <th colspan="<?php echo (int)$colspan_planets; ?>">
        <?php echo $lang['HOME_EMPIRE_BUILDINGS']; ?>
    </th>
</tr>
</thead>
<tbody>
<?php foreach ($planet_building_rows as $building_row): ?>
    <?php if ($building_row['enabled'] ?? true): ?>
<tr>
    <td class="tdname"><?php echo $lang[$building_row['label']]; ?></td>
    <?php foreach ($player_planets as $planet_id => $planet): ?>
        <?php $level = ($planet[$building_row['code']] ?? '') === '' ? '&nbsp;' : $planet[$building_row['code']]; ?>
    <td class="tdcontent">
        <span id="<?php echo $building_row['id'] . '_' . $planet_id; ?>"><?php echo $level; ?></span>
    </td>
    <?php endforeach; ?>
</tr>
    <?php endif; ?>
<?php endforeach; ?>
</tbody>
