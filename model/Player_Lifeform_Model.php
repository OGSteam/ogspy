<?php

namespace Ogsteam\Ogspy\Model;

use InvalidArgumentException;
use Ogsteam\Ogspy\Abstracts\Model_Abstract;

/**
 * Manages planet-specific life-form levels and resource observations.
 */
class Player_Lifeform_Model extends Model_Abstract
{
    /**
     * @return array<int, array{
     *     buildings: array<int, int>,
     *     research: array<int, int>,
     *     resources?: array{species_code: ?string, population: ?string, food: ?string, last_update: int}
     * }>
     */
    public function select_player_lifeforms(int $playerId): array
    {
        $lifeforms = [];
        $this->appendCategory($lifeforms, $playerId, TABLE_GAME_LIFEFORM_BUILDING, 'buildings');
        $this->appendCategory($lifeforms, $playerId, TABLE_GAME_LIFEFORM_RESEARCH, 'research');
        $this->appendResources($lifeforms, $playerId);

        return $lifeforms;
    }

    /**
     * Stores the latest observation and one daily UTC snapshot per planet.
     *
     * Missing fields preserve their current values. Explicit null values clear a
     * value, while zero is stored as an observed value.
     *
     * @param array{species?: ?string, population?: int|string|null, food?: int|string|null} $observation
     * @return bool Whether at least one supported field was stored.
     */
    public function storeResourceObservation(
        int $astroObjectId,
        array $observation,
        ?int $observedAt = null
    ): bool {
        if ($astroObjectId <= 0) {
            throw new InvalidArgumentException('The astronomical object ID must be positive.');
        }

        $values = [];
        if (array_key_exists('species', $observation)) {
            $values['species_code'] = $this->normalizeSpecies($observation['species']);
        }
        if (array_key_exists('population', $observation)) {
            $values['population'] = $this->normalizeAmount($observation['population'], 'population');
        }
        if (array_key_exists('food', $observation)) {
            $values['food'] = $this->normalizeAmount($observation['food'], 'food');
        }

        if ($values === []) {
            return false;
        }

        $timestamp = $observedAt ?? time();
        if ($timestamp <= 0) {
            throw new InvalidArgumentException('The observation timestamp must be positive.');
        }

        $this->upsertCurrentResources($astroObjectId, $values, $timestamp);
        $this->upsertDailyResources($astroObjectId, $timestamp);

        return true;
    }

    public function delete_astro_object(int $astroObjectId): void
    {
        foreach ([
            TABLE_GAME_LIFEFORM_BUILDING,
            TABLE_GAME_LIFEFORM_RESEARCH,
            TABLE_GAME_LIFEFORM_RESOURCE,
            TABLE_GAME_LIFEFORM_RESOURCE_HISTORY,
        ] as $table) {
            $this->db->sql_query(
                "DELETE FROM {$table} WHERE `astro_object_id` = {$astroObjectId}"
            );
        }
    }

    /**
     * @param array<int, array{buildings: array<int, int>, research: array<int, int>}> $lifeforms
     */
    private function appendCategory(array &$lifeforms, int $playerId, string $table, string $category): void
    {
        $query = "SELECT levels.`astro_object_id`, levels.`technology_id`, levels.`level`
            FROM {$table} levels
            INNER JOIN " . TABLE_USER_BUILDING . " astro ON astro.`id` = levels.`astro_object_id`
            WHERE astro.`player_id` = {$playerId}
            ORDER BY levels.`astro_object_id`, levels.`technology_id`";
        $result = $this->db->sql_query($query);

        while ($row = $this->db->sql_fetch_assoc($result)) {
            $astroObjectId = (int)$row['astro_object_id'];
            if (!isset($lifeforms[$astroObjectId])) {
                $lifeforms[$astroObjectId] = ['buildings' => [], 'research' => []];
            }
            $lifeforms[$astroObjectId][$category][(int)$row['technology_id']] = (int)$row['level'];
        }
    }

    /**
     * @param array<int, array<string, mixed>> $lifeforms
     */
    private function appendResources(array &$lifeforms, int $playerId): void
    {
        $query = "SELECT resources.`astro_object_id`, resources.`species_code`,
                resources.`population`, resources.`food`, resources.`last_update`
            FROM " . TABLE_GAME_LIFEFORM_RESOURCE . " resources
            INNER JOIN " . TABLE_USER_BUILDING . " astro ON astro.`id` = resources.`astro_object_id`
            WHERE astro.`player_id` = {$playerId}
            ORDER BY resources.`astro_object_id`";
        $result = $this->db->sql_query($query);

        while ($row = $this->db->sql_fetch_assoc($result)) {
            $astroObjectId = (int)$row['astro_object_id'];
            if (!isset($lifeforms[$astroObjectId])) {
                $lifeforms[$astroObjectId] = ['buildings' => [], 'research' => []];
            }
            $lifeforms[$astroObjectId]['resources'] = [
                'species_code' => $row['species_code'] ?? null,
                'population' => isset($row['population']) ? (string)$row['population'] : null,
                'food' => isset($row['food']) ? (string)$row['food'] : null,
                'last_update' => (int)$row['last_update'],
            ];
        }
    }

    /**
     * @param array<string, ?string> $values
     */
    private function upsertCurrentResources(int $astroObjectId, array $values, int $timestamp): void
    {
        $columns = ['astro_object_id'];
        $sqlValues = [(string)$astroObjectId];
        $updates = [];

        foreach ($values as $column => $value) {
            $columns[] = $column;
            $sqlValue = $this->sqlNullableValue($value);
            $sqlValues[] = $sqlValue;
            $updates[] = "`{$column}` = {$sqlValue}";
        }

        $columns[] = 'last_update';
        $sqlValues[] = (string)$timestamp;
        $updates[] = "`last_update` = {$timestamp}";

        $quotedColumns = '`' . implode('`, `', $columns) . '`';
        $query = 'INSERT INTO ' . TABLE_GAME_LIFEFORM_RESOURCE
            . " ({$quotedColumns}) VALUES (" . implode(', ', $sqlValues) . ')'
            . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);
        $this->db->sql_query($query);
    }

    private function upsertDailyResources(int $astroObjectId, int $timestamp): void
    {
        $observationDay = gmdate('Y-m-d', $timestamp);
        $query = 'INSERT INTO ' . TABLE_GAME_LIFEFORM_RESOURCE_HISTORY
            . ' (`astro_object_id`, `observation_day`, `species_code`, `population`, `food`, `observed_at`)'
            . " SELECT `astro_object_id`, '{$observationDay}', `species_code`, `population`, `food`, {$timestamp}"
            . ' FROM ' . TABLE_GAME_LIFEFORM_RESOURCE
            . " WHERE `astro_object_id` = {$astroObjectId}"
            . ' ON DUPLICATE KEY UPDATE'
            . ' `species_code` = VALUES(`species_code`),'
            . ' `population` = VALUES(`population`),'
            . ' `food` = VALUES(`food`),'
            . ' `observed_at` = VALUES(`observed_at`)';
        $this->db->sql_query($query);
    }

    private function normalizeSpecies(?string $species): ?string
    {
        if ($species === null) {
            return null;
        }

        $species = strtoupper(trim($species));
        if (!in_array($species, ['HUMANS', 'ROCKTAL', 'MECHAS', 'KAELESH'], true)) {
            throw new InvalidArgumentException("Unsupported life-form species: {$species}");
        }

        return $species;
    }

    private function normalizeAmount(int|string|null $amount, string $field): ?string
    {
        if ($amount === null) {
            return null;
        }

        $amount = (string)$amount;
        if (!preg_match('/^\d+$/', $amount)) {
            throw new InvalidArgumentException("The {$field} value must be a non-negative integer.");
        }

        $amount = ltrim($amount, '0');
        return $amount === '' ? '0' : $amount;
    }

    private function sqlNullableValue(?string $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return "'" . $this->db->sql_escape_string($value) . "'";
    }
}
