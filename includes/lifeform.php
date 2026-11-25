<?php

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

/**
 * Returns the language suffix for the species owning an OGame life-form technology.
 */
function lifeform_species_code(int $technologyId): string
{
    return match (intdiv($technologyId, 1000)) {
        11 => 'HUMANS',
        12 => 'ROCKTAL',
        13 => 'MECHAS',
        14 => 'KAELESH',
        default => 'UNKNOWN',
    };
}

/**
 * Collects and numerically sorts the real OGame IDs present across all planets.
 *
 * @param array<int, array{buildings?: array<int, int>, research?: array<int, int>}> $lifeforms
 * @return int[]
 */
function lifeform_technology_ids(array $lifeforms, string $category): array
{
    if (!in_array($category, ['buildings', 'research'], true)) {
        return [];
    }

    $technologyIds = [];
    foreach ($lifeforms as $planetLifeforms) {
        foreach ($planetLifeforms[$category] ?? [] as $technologyId => $_level) {
            $technologyId = (int)$technologyId;
            if ($technologyId > 0) {
                $technologyIds[$technologyId] = true;
            }
        }
    }

    $technologyIds = array_keys($technologyIds);
    sort($technologyIds, SORT_NUMERIC);

    return $technologyIds;
}

/**
 * Groups the real OGame technology IDs by their owning species.
 *
 * Research from several species may be selected on the same planet, so the
 * technology ID determines the group rather than the planet's active species.
 *
 * @param array<int, array{buildings?: array<int, int>, research?: array<int, int>}> $lifeforms
 * @return array<string, int[]>
 */
function lifeform_technology_groups(array $lifeforms, string $category): array
{
    $groups = [];
    foreach (lifeform_technology_ids($lifeforms, $category) as $technologyId) {
        $groups[lifeform_species_code($technologyId)][] = $technologyId;
    }

    $orderedGroups = [];
    foreach (['HUMANS', 'ROCKTAL', 'MECHAS', 'KAELESH', 'UNKNOWN'] as $speciesCode) {
        if (isset($groups[$speciesCode])) {
            $orderedGroups[$speciesCode] = $groups[$speciesCode];
        }
    }

    return $orderedGroups;
}

/**
 * Formats an arbitrarily large non-negative integer without losing precision.
 */
function lifeformFormatAmount(?string $amount): string
{
    if ($amount === null || !preg_match('/^\d+$/', $amount)) {
        return '-';
    }

    $amount = ltrim($amount, '0');
    if ($amount === '') {
        return '0';
    }

    return preg_replace('/\B(?=(\d{3})+(?!\d))/', ' ', $amount) ?? $amount;
}
