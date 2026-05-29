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

        $request = "SELECT pspy.`id`, astro.`galaxy`, astro.`system`, astro.`row`, `dateRE`, `user`.`name`, `astro`.`type`, `player`.`ally_id`, `astro`.`player_id`, `player`.`status`";
        $request .= " FROM " . TABLE_PARSEDSPY. " `pspy`";
        $request .= " INNER JOIN " . TABLE_USER_BUILDING . " `astro` ON `pspy`.`astro_object_id` = `astro`.`id`";
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
     * Retrieves detailed information about a specific spy report.
     *
     * @param int $id_RE The unique identifier of the spy report to retrieve.
     * @return array An associative array containing detailed information about the spy report,
     *               including resources, activities, structures, fleet, technologies, probabilities,
     *               and report date.
     */
    public function get_spy_Id(int $id_RE)
    {
        $query = "SELECT `astro_object_id`, `metal`, `crystal`, `deuterium`, `energie`, `activite`, `M`, `C`, `D`, `CES`, `CEF`, `UdR`, `UdN`, `CSp`, `HM`, `HC`,
        `HD`, `Lab`, `Ter`, `Silo`, `Dock`, `DdR`, `BaLu`, `Pha`, `PoSa`, `LM`, `LLE`, `LLO`, `CG`, `AI`, `LP`, `PB`, `GB`, `MIC`, `MIP`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`,
        `DST`, `EDLM`, `SAT`, `TRA`, `FOR`, `FAU`, `ECL`, `Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`,
        `dateRE`, `proba` FROM " . TABLE_PARSEDSPY . " WHERE `id`= {$id_RE}";
        $result = $this->db->sql_query($query);

        return $this->db->sql_fetch_assoc($result);
    }

    /**
     * Retrieves spy data for a specific astronomical object.
     *
     * @param int $astro_object_id The ID of the astronomical object for which the spy data is to be retrieved.
     * @return array An array of associative arrays, where each entry contains detailed spy report information
     *               such as planet name, resources (metal, crystal, deuterium, energy), activity, building levels,
     *               defenses, fleet composition, technology levels, report date, and probability.
     */
    public function get_spy_data(int $astro_object_id)
    {
        $query = "SELECT `planet_name`, `astro_object_id`, `metal`, `crystal`, `deuterium`, `energie`, `activite`, `M`, `C`, `D`, `CES`, `CEF`, `UdR`, `UdN`, `CSp`, `HM`, `HC`,
        `HD`, `Lab`, `Ter`, `Silo`, `Dock`, `DdR`, `BaLu`, `Pha`, `PoSa`, `LM`, `LLE`, `LLO`, `CG`, `AI`, `LP`, `PB`, `GB`, `MIC`, `MIP`, `PT`, `GT`, `CLE`, `CLO`, `CR`, `VB`, `VC`, `REC`, `SE`, `BMD`,
        `DST`, `EDLM`, `SAT`, `TRA`, `FOR`, `FAU`, `ECL`, `Esp`, `Ordi`, `Armes`, `Bouclier`, `Protection`, `NRJ`, `Hyp`, `RC`, `RI`, `PH`, `Laser`, `Ions`, `Plasma`, `RRI`, `Graviton`, `Astrophysique`,
        `dateRE`, `proba` FROM " . TABLE_PARSEDSPY . " WHERE `astro_object_id`='$astro_object_id' ORDER BY `dateRE` DESC ";
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

        return $nb_spy;
    }

    /**
     * Retrieves a list of active spy reports for a specific astro object.
     *
     * @param int $astroObjectId The ID of the astro object for which active spy reports are to be retrieved.
     * @return array An array of spy reports, where each report contains the spy ID, user name of the sender,
     *               and the date of the report.
     */
    public function get_spy_id_list_by_planet(int $astroObjectId)
    {
        $request = "SELECT spy.`id`, u.`name`, spy.`dateRE` "; //, `is_moon`";
        $request .= " FROM " . TABLE_PARSEDSPY . " spy LEFT JOIN " . TABLE_USER . " u ON u.`id` = spy.`sender_id`";
        $request .= " WHERE spy.`active` = '1'  AND spy.`astro_object_id` = '$astroObjectId'";
        $request .= " ORDER BY spy.`dateRE` DESC";
        $result = $this->db->sql_query($request);
        $tResult = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = array(
                'spy_id' => $row['id'],
                'user_name' => $row['name'],
                'dateRE' => $row['dateRE']
            );
            //   ,"is_moon" => $row['is_moon']);
        }
        return  $tResult;
    }

    /**
     * Deletes a specific spy report based on its ID.
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
