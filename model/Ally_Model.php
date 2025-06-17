<?php

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;
class Ally_Model extends Model_Abstract
{
    /**
     * Retrieves detailed data of the ally corresponding to the given ally ID.
     *
     * @param int $allyId The unique identifier of the ally.
     * @return array|false An associative array containing the ally's data if found, or false if no ally is found.
     */
    public function get_player_data(int $allyId)
    {
        $request = "SELECT `id`, `name`, `tag`, `class` " .
                    " FROM " . TABLE_GAME_ALLY;
        $request .= " WHERE `id` = " . $allyId;
        $request .= " ORDER BY `id`";
        $result = $this->db->sql_query($request);

        $allyData = $this->db->sql_fetch_assoc($result);

        if (empty($allyData)) {
            return false;
        }

        return $allyData;
    }

    /**
     * Retrieves the name of the ally corresponding to the given ally ID.
     *
     * @param int $allyId The unique identifier of the ally.
     * @return string|false The name of the ally if found, or false if no ally is found.
     */
    public function get_ally_name(int $allyId)
    {
        $request = "SELECT `name`".
            " FROM " . TABLE_GAME_ALLY;
        $request .= " WHERE `id` = " . $allyId;
        $result = $this->db->sql_query($request);

        $allyName = $this->db->sql_fetch_row($result);

        if (empty($allyName)) {
            return false;
        }

        return $allyName;
    }
}
