<?php

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

/**
 * Reads and removes planet-specific life-form levels.
 */
class Player_Lifeform_Model extends Model_Abstract
{
    /**
     * @return array<int, array{buildings: array<int, int>, research: array<int, int>}>
     */
    public function select_player_lifeforms(int $playerId): array
    {
        $lifeforms = [];
        $this->appendCategory($lifeforms, $playerId, TABLE_GAME_LIFEFORM_BUILDING, 'buildings');
        $this->appendCategory($lifeforms, $playerId, TABLE_GAME_LIFEFORM_RESEARCH, 'research');

        return $lifeforms;
    }

    public function delete_astro_object(int $astroObjectId): void
    {
        foreach ([TABLE_GAME_LIFEFORM_BUILDING, TABLE_GAME_LIFEFORM_RESEARCH] as $table) {
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
}
