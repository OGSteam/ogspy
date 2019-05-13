<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Spy_Model extends Model_Abstract
{
    /**
     * @param $user_id
     * @param int $sort
     * @param int $sort2
     * @return array
     */
    public function get_favoriteSpyList($user_id, $sort = 5, $sort2 = 0) {

        

        switch ($sort2) {
            case 0:
                $order = " desc";
                break;
            case 1:
                $order = " asc";
                break;
            default:
                $order = " asc";
        }

        switch ($sort) {
            case 1:
                $ordered_by = "coordinates" . $order . "";
                break;
            case 2:
                $ordered_by = "ally " . $order;
                break;
            case 3:
                $ordered_by = "player " . $order;
                break;
            case 4:
                $ordered_by = "moon " . $order;
                break;
            case 5:
                $ordered_by = "dateRE " . $order;
                break;
            default:
                $ordered_by = "dateRE " . $order;
        }


        $favorite = array();

        $request = "select " . TABLE_PARSEDSPY .
            ".id_spy, coordinates, dateRE, sender_id, " . TABLE_UNIVERSE . ".moon, " . TABLE_UNIVERSE . ".ally, " . TABLE_UNIVERSE . ".player, " . TABLE_UNIVERSE . ".status";
        $request .= " from " . TABLE_PARSEDSPY . ", " . TABLE_UNIVERSE;
        $request .= " where " . TABLE_PARSEDSPY . ".sender_id = " . $user_id . " and CONCAT(" . TABLE_UNIVERSE . ".galaxy,':'," . TABLE_UNIVERSE . ".system,':'," . TABLE_UNIVERSE . ".row)=coordinates";
        $request .= " order by " . $ordered_by;
        $result = $this->db->sql_query($request);

        while (list($spy_id, $coordinates, $datadate, $sender_id, $moon, $ally, $player, $status) = $this->db->sql_fetch_row($result)) {
            $request = "select user_name from " . TABLE_USER;
            $request .= " where user_id=" . $sender_id;
            $result_2 = $this->db->sql_query($request);
            list($user_name) = $this->db->sql_fetch_row($result_2);

            $favorite[$spy_id] = array("spy_id" => $spy_id, "spy_galaxy" => substr($coordinates,
                0, strpos($coordinates, ':')), "spy_system" => substr($coordinates, strpos($coordinates,
                    ':') + 1, strrpos($coordinates, ':') - strpos($coordinates, ':') - 1), "spy_row" =>
                substr($coordinates, strrpos($coordinates, ':') + 1), "player" => $player,
                "ally" => $ally, "moon" => $moon, "status" => $status, "datadate" => $datadate,
                "poster" => $user_name);
        }
        return $favorite;
    }


    public function get_spy_Id($id_RE)
    {
        $query = 'SELECT planet_name, coordinates, metal, cristal, deuterium, energie, activite, M, C, D, CES, CEF, UdR, UdN, CSp, HM, HC, 
        HD, Lab, Ter, Silo, Dock, DdR, BaLu, Pha, PoSa, LM, LLE, LLO, CG, AI, LP, PB, GB, MIC, MIP, PT, GT, CLE, CLO, CR, VB, VC, REC, SE, BMD, 
        DST, EDLM, SAT, TRA, Esp, Ordi, Armes, Bouclier, Protection, NRJ, Hyp, RC, RI, PH, Laser, Ions, Plasma, RRI, Graviton, Astrophysique, 
        dateRE, proba FROM ' . TABLE_PARSEDSPY . ' WHERE id_spy=' . $id_RE;
        $result = $this->db->sql_query($query);

        $row = $this->db->sql_fetch_assoc($result);
        return $row;
    }

    public function get_all_spy_coordinates($coord)
    {
        $query = "SELECT planet_name, coordinates, metal, cristal, deuterium, energie, activite, M, C, D, CES, CEF, UdR, UdN, CSp, HM, HC, 
        HD, Lab, Ter, Silo, Dock, DdR, BaLu, Pha, PoSa, LM, LLE, LLO, CG, AI, LP, PB, GB, MIC, MIP, PT, GT, CLE, CLO, CR, VB, VC, REC, SE, BMD, 
        DST, EDLM, SAT, TRA, Esp, Ordi, Armes, Bouclier, Protection, NRJ, Hyp, RC, RI, PH, Laser, Ions, Plasma, RRI, Graviton, Astrophysique, 
        dateRE, proba FROM " . TABLE_PARSEDSPY . " WHERE coordinates='" . $coord. "' ORDER BY dateRE DESC ";
        $result = $this->db->sql_query($query);

        $tResult = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = $row;
        }
        return $tResult;
    }

    /**
     * @param int $galaxy
     * @param int $system
     * @param int $row
     * @return int $nb_spy
     */
    public function get_nb_spy_by_planet($galaxy, $system, $row) {
        

        $request = "SELECT * FROM " . TABLE_PARSEDSPY . " WHERE `active` = '1' AND `coordinates` = '" . $galaxy . ":" . $system . ":" . $row . "'";
        $result = $this->db->sql_query($request);
        $nb_spy = $this->db->sql_numrows($result);

        return $nb_spy;
    }

    /**
     * @param int $galaxy
     * @param int $system
     * @param int $row
     * @return array $tResult
     */
    public function get_spy_id_list_by_planet($galaxy, $system, $row) {
        

        $request = "SELECT `id_spy`, `user_name`, `dateRE` ";//, `is_moon`";
        $request .= " FROM " . TABLE_PARSEDSPY . " LEFT JOIN " . TABLE_USER . " ON `user_id` = `sender_id`";
        $request .= " WHERE `active` = '1'  AND `coordinates` = '" . $galaxy . ":" . $system . ":" . $row . "'";
        $request .= " ORDER BY `dateRE` DESC";
        $result = $this->db->sql_query($request);
        $tResult = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $tResult[] = array("id_spy" => $row['id_spy'],
                "user_name" => $row['user_name'],
                "dateRE" => $row['dateRE']);
             //   ,"is_moon" => $row['is_moon']);
        }
        return $tResult;
    }

    /**
     * @param $spy_id
     */
    public function delete_spy($spy_id) {


        $request = "DELETE FROM " . TABLE_PARSEDSPY . " WHERE `spy_id` = '" . $spy_id . "'";
        $this->db->sql_query($request);
    }

    /**
     * @param $spy_id
     * @param $user_id
     */
    public function delete_spy_by_senderId($spy_id,$user_id) {
        $request = "DELETE FROM " . TABLE_PARSEDSPY . " WHERE `spy_id` = '" . $spy_id . "' and  `sender_id` = '" . $user_id . "'";
        $this->db->sql_query($request);
    }



    /**
     * This function deletes expired Spy records from the database
     * @param $limit_time
     */
    public function delete_expired_spies($limit_time) {
        

        $request = "DELETE FROM " . TABLE_PARSEDSPY . " WHERE `active` = '0' OR `dateRE` < " . $limit_time;
        $this->db->sql_query($request);
    }

}