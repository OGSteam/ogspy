<?php



namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Spy_Model extends Model_Abstract
{
    /**
     * Retrieves a list of favorite spy reports for a specific user.
     *
     * @param int $user_id The ID of the user whose favorite spy reports are to be retrieved.
     * @param int $sort Optional parameter to specify the sorting criterion.
     *                  Values:
     *                  1 = coordinates
     *                  2 = ally
     *                  3 = player
     *                  4 = moon
     *                  Default: 5 (dateRE).
     * @param int $sort2 Optional parameter to specify the sorting order.
     *                   Values:
     *                   0 = descending (default).
     *                   1 = ascending.
     * @return array An associative array of favorite spy reports, where each report contains details such as
     *               spy ID, galaxy, system, row, player, ally, moon, status, date, and poster name.
     */
    public function get_favoriteSpyList(int $user_id, int $sort = 5, int $sort2 = 0)
    {
        $order = $sort2 === 0 ? " desc" : " asc";

        $ordered_by = match ($sort) {
                1 => "coordinates",
                2 => "ally",
                3 => "player",
                4 => "moon",
                default => "dateRE"
            } . $order;


        $favorite = [];

        $request = "SELECT `id_spy`, astro.`galaxy`, astro.`system`, astro.`row`, `dateRE`, `user`.`name`, `astro`.`type`, `player`.`ally_id`, `astro`.`player_id`, `player`.`status`";
        $request .= " FROM " . TABLE_PARSEDSPY. " `pspy`";
        $request .= " INNER JOIN " . TABLE_USER_BUILDING . " `astro` ON `pspy`.`coordinates` = CONCAT(`astro`.`galaxy`,':',`astro`.`system`,':',`astro`.`row`)";
        $request .= " INNER JOIN " . TABLE_GAME_PLAYER . " `player` ON `astro`.`player_id` = `player`.`id`";
        $request .= " INNER JOIN " . TABLE_USER . " `user` ON `user`.`id` = `pspy`.`sender_id`";
        $request .= " WHERE `pspy`.`sender_id`=$user_id ";
        $request .= " ORDER BY " . $ordered_by;
        $result = $this->db->sql_query($request);

        while (list($spy_id, $galaxy, $system, $row, $datadate, $sender_name, $moon, $ally, $player, $status) = $this->db->sql_fetch_row($result)) {

            $favorite[$spy_id] = array(
                "spy_id" => $spy_id, "spy_galaxy" => $galaxy
                , "spy_system" =>$system, "spy_row" => $row, "player" => $player,
                "ally" => $ally, "moon" => $moon, "status" => $status, "datadate" => $datadate,
                "poster" => $sender_name
            );
        }
        return $favorite;
    }


    /**
     * Retrieves the spy report details for the given spy ID.
     *
     * @param int $id_RE The ID of the spy report to retrieve.
     * @return array An associative array containing the details of the spy report, including:
     *               'planet_name', 'coordinates', 'metal', 'cristal', 'deuterium', 'energie',
     *               'activite', 'M', 'C', 'D', 'CES', 'CEF', 'UdR', 'UdN', 'CSp', 'HM', 'HC',
     *               'HD', 'Lab', 'Ter', 'Silo', 'Dock', 'DdR', 'BaLu', 'Pha', 'PoSa', 'LM',
     *               'LLE', 'LLO', 'CG', 'AI', 'LP', 'PB', 'GB', 'MIC', 'MIP', 'PT', 'GT', 'CLE',
     *               'CLO', 'CR', 'VB', 'VC', 'REC', 'SE', 'BMD', 'DST', 'EDLM', 'SAT', 'TRA',
     *               'FOR', 'FAU', 'ECL', 'Esp', 'Ordi', 'Armes', 'Bouclier', 'Protection', 'NRJ',
     *               'Hyp', 'RC', 'RI', 'PH', 'Laser', 'Ions', 'Plasma', 'RRI', 'Graviton',
     *               'Astrophysique', 'dateRE', 'proba'.
     */
    public function get_spy_Id($id_RE)
    {
        $id_RE = (int)$id_RE;

        $query = "SELECT `planet_name`, `coordinates`, `metal`, `cristal`, `deuterium`, `energie`, `activite`, `M`, `C`, `D`, `CES`, `CEF`, `UdR`, `UdN`, `CSp`, `HM`, `HC`,
        `HD`, `Lab`, `Ter`, `Silo`, `Dock`, `DdR`, `BaLu`, `Pha`, `PoSa`, `LM`, `LLE`, `LLO`, `CG`, `AI`, `LP`, `PB`, `GB`, `MIC`, `MIP`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`,
        `DST`, `EDLM`, `SAT`, `TRA`, `FOR`, `FAU`, `ECL`, `Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`,
        `dateRE`, `proba` FROM " . TABLE_PARSEDSPY . " WHERE `id_spy`=$id_RE";
        $result = $this->db->sql_query($query);

        $row = $this->db->sql_fetch_assoc($result);
        return $row;
    }

    /**
     * Retrieves all spy reports associated with the provided coordinates.
     *
     * @param string $coord The coordinates to filter spy reports by.
     *                      The format is expected to follow the galaxy:system:row pattern.
     * @return array An array of associative arrays, where each entry contains details about a spy report,
     *               including 'planet_name', 'coordinates', 'metal', 'cristal', 'deuterium', 'energie',
     *               'activite', 'M', 'C', 'D', 'CES', 'CEF', 'UdR', 'UdN', 'CSp', 'HM', 'HC', 'HD', 'Lab',
     *               'Ter', 'Silo', 'Dock', 'DdR', 'BaLu', 'Pha', 'PoSa', 'LM', 'LLE', 'LLO', 'CG', 'AI',
     *               'LP', 'PB', 'GB', 'MIC', 'MIP', 'PT', 'GT', 'CLE', 'CLO', 'CR', 'VB', 'VC', 'REC', 'SE',
     *               'BMD', 'DST', 'EDLM', 'SAT', 'TRA', 'FOR', 'FAU', 'ECL', 'Esp', 'Ordi', 'Armes',
     *               'Bouclier', 'Protection', 'NRJ', 'Hyp', 'RC', 'RI', 'PH', 'Laser', 'Ions', 'Plasma',
     *               'RRI', 'Graviton', 'Astrophysique', 'dateRE', 'proba', and other report details.
     */
    public function get_all_spy_coordinates($coord)
    {
        $coord = $this->db->sql_escape_string($coord);

        $query = "SELECT `planet_name`, `coordinates`, `metal`, `cristal`, `deuterium`, `energie`, `activite`, `M`, `C`, `D`, `CES`, `CEF`, `UdR`, `UdN`, `CSp`, `HM`, `HC`,
        `HD`, `Lab`, `Ter`, `Silo`, `Dock`, `DdR`, `BaLu`, `Pha`, `PoSa`, `LM`, `LLE`, `LLO`, `CG`, `AI`, `LP`, `PB`, `GB`, `MIC`, `MIP`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`,
        `DST`, `EDLM`, `SAT`, `TRA`, `FOR`, `FAU`, `ECL`, `Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`,
        `dateRE`, `proba` FROM " . TABLE_PARSEDSPY . " WHERE `coordinates`='$coord' ORDER BY `dateRE` DESC ";
        $result = $this->db->sql_query($query);

        $tResult = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = $row;
        }
        return $tResult;
    }

    /**
     * Retrieves the number of active spy reports associated with a specific planet based on its galaxy, system, and row coordinates.
     *
     * @param int $galaxy The galaxy number of the planet.
     * @param int $system The system number of the planet.
     * @param int $row The row number of the planet.
     * @return int The count of active spy reports for the specified planet.
     */
    public function get_nb_spy_by_planet(int $galaxy, int $system, int $row)
    {
        $request = "SELECT COUNT(pspy.id) as spy_count
                FROM " . TABLE_PARSEDSPY . " pspy
                INNER JOIN " . TABLE_USER_BUILDING . " astro ON pspy.astro_object_id = astro.id
                AND astro.galaxy = " . $galaxy . "
                AND astro.system = " . $system . "
                AND astro.row = " . $row;

        $result = $this->db->sql_query($request);
        $data = $this->db->sql_fetch_assoc($result);
        $nb_spy = $data ? (int)$data['spy_count'] : 0;

        return $nb_spy;;
    }

    /**
     * Retrieves a list of spy report IDs associated with a specific planet identified by its galaxy, system, and row.
     *
     * @param int $galaxy The galaxy coordinate of the planet.
     * @param int $system The system coordinate of the planet.
     * @param int $row The row coordinate of the planet.
     * @return array An array of associative arrays where each element contains:
     *               'id_spy' (int): The ID of the spy report,
     *               'user_name' (string): The name of the user who created the report,
     *               'dateRE' (string): The date the report was created.
     */
    public function get_spy_id_list_by_planet($galaxy, $system, $row)
    {
        $galaxy = (int)$galaxy;
        $system = (int)$system;
        $row = (int)$row;

        $request = "SELECT `id_spy`, `user_name`, `dateRE` "; //, `is_moon`";
        $request .= " FROM " . TABLE_PARSEDSPY . " LEFT JOIN " . TABLE_USER . " ON `user_id` = `sender_id`";
        $request .= " WHERE `active` = '1'  AND `coordinates` = '" . $galaxy . ":" . $system . ":" . $row . "'";
        $request .= " ORDER BY `dateRE` DESC";
        $result = $this->db->sql_query($request);
        $tResult = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = array(
                'id_spy' => $row['id_spy'],
                'user_name' => $row['user_name'],
                'dateRE' => $row['dateRE']
            );
            //   ,"is_moon" => $row['is_moon']);
        }
        return $tResult;
    }

    /**
     * Deletes a spy report from the database based on the provided spy report ID.
     *
     * @param int $spy_id The ID of the spy report to be deleted.
     * @return void
     */
    public function delete_spy($spy_id)
    {
        $spy_id = (int)$spy_id;

        $request = "DELETE FROM " . TABLE_PARSEDSPY . " WHERE `id_spy` = '" . $spy_id . "'";
        $this->db->sql_query($request);

        //todo prevoir suppression favorite ...


    }

    /**
     * Deletes a spy report from the database based on the given spy ID and sender ID.
     *
     * @param int $spy_id The unique identifier of the spy report to be deleted.
     * @param int $user_id The ID of the sender associated with the spy report.
     * @return void This method does not return any value.
     */
    public function delete_spy_by_senderId($spy_id, $user_id)
    {
        $spy_id = (int)$spy_id;
        $user_id = (int)$user_id;

        $request = "DELETE FROM " . TABLE_PARSEDSPY . " WHERE `id_spy` = '" . $spy_id . "' and  `sender_id` = '" . $user_id . "'";
        $this->db->sql_query($request);
    }


    /**
     * Deletes expired spy reports from the database based on the provided time limit or inactive status.
     *
     * @param int $limit_time The timestamp used to determine expiration. Spy reports with a date earlier than this value, or marked as inactive, will be deleted.
     * @return void
     */
    public function delete_expired_spies($limit_time)
    {
        $limit_time = (int)$limit_time;

        $request = "DELETE FROM " . TABLE_PARSEDSPY . " WHERE `active` = '0' OR `dateRE` < " . $limit_time;
        $this->db->sql_query($request);
    }
}
