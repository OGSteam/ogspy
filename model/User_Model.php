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
use Ogsteam\Ogspy;


class User_Model
{

    /* Fonctions concerning user account */
    /**
     * @param $login
     * @param $password
     * @return bool|mixed|mysqli_result
     */
    public function select_user_login($login, $password, $salt = false)
    {
        global $db;
        if ($salt === false) {
            $password = Ogspy\crypto($password);
        }

        $request = "SELECT `user_id`, `user_active` FROM " . TABLE_USER . " WHERE `user_name` = '" . $db->sql_escape_string($login) . "' AND `user_password` = '" . $password . "'";
        $result = $db->sql_query($request);

        return $result;
    }

    /**
     * @param $username
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function select_user_name($username)
    {
        global $db;
        $request = "SELECT * FROM " . TABLE_USER . " WHERE `user_name` = '" . $username . "'";

        $request = $db->sql_escape_string($request);
        $result = $db->sql_query($request);

        return $result;
    }

    public function select_user_list() {

        global $db;
        $request = "SELECT `user_name` FROM " . TABLE_USER;

        $request = $db->sql_escape_string($request);
        $result = $db->sql_query($request);
        list($user_name) = $db->sql_fetch_row($result);

        return $user_name;
    }

    public function select_userid_list() {

        global $db;
        $request = "SELECT `user_id` FROM " . TABLE_USER;

        $request = $db->sql_escape_string($request);
        $result = $db->sql_query($request);
        list($user_id) = $db->sql_fetch_row($result);

        return $user_id;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function select_last_visit($user_id)
    {
        global $db;
        $request = "SELECT `user_lastvisit` FROM " . TABLE_USER;
        $request .= " WHERE `user_id` = '" . $user_id . "'";
        $result = $db->sql_query($request);
        list($lastvisit) = $db->sql_fetch_row($result);

        return $lastvisit;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function select_user_data($user_id)
    {
        global $db;
        $request = "SELECT `user_id`, `user_name`, `user_password`, `user_email`, `user_active`, `user_regdate`, `user_lastvisit`," .
            " `user_galaxy`, `user_system`, `user_admin`, `user_coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`" .
            " FROM " . TABLE_USER;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " ORDER BY `user_name`";
        $result = $db->sql_query($request);

        $info_users = array();
        while ($row = $db->sql_fetch_assoc($result)) {
            $info_users[] = $row;
        }

        if (count($info_users) == 0) {
            return false;
        }
        return $info_users;
    }

    /**
     * @return mixed
     */
    public function select_all_user_data()
    {
        global $db;
        $request = "SELECT `user_id`, `user_name`, `user_password`, `user_email`, `user_active`, `user_regdate`, `user_lastvisit`," .
            " `user_galaxy`, `user_system`, `user_admin`, `user_coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`" .
            " FROM " . TABLE_USER;

        $request .= " ORDER BY `user_name`";
        $result = $db->sql_query($request);

        $info_users = array();
        while ($row = $db->sql_fetch_assoc($result)) {
            $info_users[] = $row;
        }

        if (count($info_users) == 0) {
            return false;
        }
        return $info_users;
    }

    /**
     * @return mixed
     */
    public function select_all_user_stats_data()
    {
        global $db;

        $request = "SELECT `user_id`, `user_name`, `planet_added_xtense`, `search`, `spy_added_xtense`, `rank_added_xtense`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";
        $request .= " FROM " . TABLE_USER . " ORDER BY `planet_added_xtense` DESC";
        $result = $db->sql_query($request);
        return $result;
    }
    /**
     * @return mixed
     */
    public function select_user_stats_data($user_id)
    {
        global $db;

        $request = "SELECT `user_id`, `user_name`, `planet_added_xtense`, `search`, `spy_added_xtense`, `rank_added_xtense`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";
        $request .= " FROM " . TABLE_USER;
        $request .= " WHERE user_id='" . $user_id . "'";
        $result = $db->sql_query($request);

        list($planet_added_xtense, $search, $spy_added_xtense, $rank_added_xtense) = $db->sql_fetch_row($result);

        return array("planet_added_xtense" => $planet_added_xtense, "search" => $search, "spy_added_xtense" => $spy_added_xtense, "rank_added_xtense" => $rank_added_xtense);
    }
    public function select_user_stats_sum()
    {
        global $db;
        $request = "SELECT SUM(planet_added_xtense), SUM(spy_added_xtense), SUM(rank_added_xtense), SUM(search)";
        $request .= "FROM " . TABLE_USER;
        $resultat = $db->sql_query($request);

        list($planetimporttotal, $spyimporttotal, $rankimporttotal, $searchtotal) = $db->sql_fetch_row($resultat);

        return array("planetimporttotal" => $planetimporttotal,
            "spyimporttotal" => $spyimporttotal,
            "rankimporttotal" => $rankimporttotal,
            "searchtotal" => $searchtotal);
    }
    /**
     * @param $user_id
     * @return array
     */
    public function select_user_rights($user_id)
    {
        global $db;

        $user_auth = array("server_set_system" => 0, "server_set_spy" => 0, "server_set_rc" => 0, "server_set_ranking" => 0, "server_show_positionhided" => 0, "ogs_connection" => 0, "ogs_set_system" => 0, "ogs_get_system" => 0, "ogs_set_spy" => 0, "ogs_get_spy" => 0, "ogs_set_ranking" => 0, "ogs_get_ranking" => 0);


        $request = "SELECT `server_set_system`, `server_set_spy`, `server_set_rc`, `server_set_ranking`, `server_show_positionhided`,";
        $request .= " ogs_connection, ogs_set_system, ogs_get_system, ogs_set_spy, ogs_get_spy, ogs_set_ranking, ogs_get_ranking";
        $request .= " from " . TABLE_GROUP . " g, " . TABLE_USER_GROUP . " u";
        $request .= " where g.group_id = u.group_id";
        $request .= " and user_id = " . $user_id;
        $result = $db->sql_query($request);

        if ($db->sql_numrows($result) == 1) { //Un seul retour possible ici
            while ($row = $db->sql_fetch_assoc($result)) {
                $user_auth = array("server_set_system" => $row['server_set_system'],
                    "server_set_spy" => $row['server_set_spy'],
                    "server_set_rc" => $row['server_set_rc'],
                    "server_set_ranking" => $row['server_set_ranking'],
                    "server_show_positionhided" => $row['server_show_positionhided'],
                    "ogs_connection" => $row['ogs_connection'],
                    "ogs_set_system" => $row['ogs_set_system'],
                    "ogs_get_system" => $row['ogs_get_system'],
                    "ogs_set_spy" => $row['ogs_set_spy'],
                    "ogs_get_spy" => $row['ogs_get_spy'],
                    "ogs_set_ranking" => $row['ogs_set_ranking'],
                    "ogs_get_ranking" => $row['ogs_get_ranking']);
            }
        }

        return $user_auth;
    }


    /**
     * @param $user_id
     */
    public function update_lastvisit_time($user_id)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_lastvisit` = " . time() . " WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_name
     */
    public function set_user_pseudo($user_id, $user_name)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_name` = '" . $user_name . "' WHERE `user_id` = " . $user_id;
        $request = $db->sql_escape_string($request);
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $user_password
     */
    public function set_user_password($user_id, $user_password)
    {
        global $db;
        $encrypted_password = crypto($user_password);
        $request = "UPDATE " . TABLE_USER . " SET `user_password` = '" . $encrypted_password . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $user_email
     */
    public function set_user_email($user_id, $user_email)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_email` = '" . $user_email . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $default_galaxy
     */
    public function set_user_default_galaxy($user_id, $default_galaxy)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_galaxy` = '" . $default_galaxy . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
        //Nettoyage Préventif
        $request = $db->sql_query("UPDATE " . TABLE_USER . " SET user_galaxy=1 WHERE user_galaxy > $new_num_of_galaxies");
        $db->sql_query($request);
    }

    /**
     * Utilisé après un redimensionement de l'univers
     * @param $int $nb_galaxy
     */
    public function set_default_galaxy_after_resize($nb_galaxy)
    {
        global $db;
        $request = $db->sql_query("UPDATE " . TABLE_USER . " SET `user_galaxy` = 1 WHERE `user_galaxy` > $nb_galaxy");
        $db->sql_query($request);
    }


    /**
     * @param $user_id
     * @param $default_system
     */
    public function set_user_default_system($user_id, $default_system)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_system` = '" . $default_system . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * Utilisé après un redimensionement de l'univers
     * @param $int $nb_systems
     */
    public function set_default_system_after_resize($nb_systems)
    {
        global $db;
        $request = $db->sql_query("UPDATE " . TABLE_USER . " SET `usersystem` = 1 WHERE `user_system` > $nb_systems");
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $disable_ip_check
     */
    public function set_user_ip_check($user_id, $disable_ip_check)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `disable_ip_check` = '" . $disable_ip_check . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_active boolean 1/0
     */
    public function set_user_active($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_active` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_coadmin($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `user_coadmin` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_user($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `management_user` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_ranking($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `management_ranking` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_planet_inserted($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `planet_added_xtense` = planet_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_spy_inserted($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `spy_added_xtense` = spy_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }
    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_rank_inserted($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `rank_added_xtense` = rank_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value int
     */
    public function add_stat_search_made($user_id, $value)
    {
        global $db;
        $request = "UPDATE " . TABLE_USER . " SET `search` = search + '" . $value . "' WHERE `user_id` = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_active_users() {

        global $db;
        $request = "SELECT `user_id` FROM " . TABLE_USER . " WHERE `user_active` = '1'";
        $result = $db->sql_query($request);
        return $number = $db->sql_numrows();
    }

    /**
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_users() {

        global $db;
        $request = "SELECT `user_id` FROM " . TABLE_USER;
        $db->sql_query($request);
        return $number = $db->sql_numrows();
    }

    /**
     * @param $pseudo
     * @param $password
     * @return \Ogsteam\Ogspy\Returs
     */
    public function add_new_user($pseudo, $password) {

        global $db;
        $encrypted_password = Ogspy\crypto($password);
        $request = "INSERT INTO " . TABLE_USER . " (user_name, user_password, user_regdate, user_active)"
            . " VALUES ('" . $pseudo . "', '" . $encrypted_password . "', " . time() . ", '1')";
        $db->sql_query($request);

        return $db->sql_insertid();
    }

    /**
     * @param $user_id
     * @param $group_id
     */
    public function add_user_to_group($user_id, $group_id) {

        global $db;
        $request = "INSERT INTO " . TABLE_USER_GROUP . " (group_id, user_id) VALUES (" . $group_id . ", " . $user_id . ")";
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     */
    public function delete_user($user_id)
    {
        global $db;
        
        $request = "DELETE FROM " . TABLE_USER . " WHERE `user_id` = " . $user_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_GROUP . " where user_id = " . $user_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_BUILDING . " where user_id = " . $user_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_FAVORITE . " where user_id = " . $user_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_DEFENCE . " where user_id = " . $user_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_SPY . " where user_id = " . $user_id;
        $db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_TECHNOLOGY . " where user_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_POINTS . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_ECO . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_TECHNOLOGY . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY_BUILT . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY_LOOSE . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY_DESTRUCT . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_HONOR . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_POINTS . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_ECO . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_TECHNOLOGY . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY_BUILT . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY_LOOSE . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_HONOR . " set sender_id = 0 where sender_id = " . $user_id;
        $db->sql_query($request);

        $request = "update " . TABLE_UNIVERSE . " set last_update_user_id = 0 where last_update_user_id = " . $user_id;
        $db->sql_query($request);
        
        
    }

    /* Fonctions concerning game account */

    /**
     * A quoi sert donc cette fonction ? :p
     * Reponse elle sert a mettre a jour le pseudo ingame afin d afficher les stats users dans son espace perso
     *
     * @param $user_stat_name
     */
    public function set_game_account_name($user_id, $user_stat_name)
    {
        global $db;

        $request = "update " . TABLE_USER . " set user_stat_name = '" . $user_stat_name . "' where user_id = " . $user_id;
        $db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $officer
     * @param $value
     */
    public function set_player_officer($user_id, $officer, $value)
    {
        global $db;
        switch ($officer) {
            case 'off_commandant':
                $request = "UPDATE " . TABLE_USER . " SET `off_commandant` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_amiral':
                $request = "UPDATE " . TABLE_USER . " SET `off_amiral` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_ingenieur':
                $request = "UPDATE " . TABLE_USER . " SET `off_ingenieur` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_geologue':
                $request = "UPDATE " . TABLE_USER . " SET `off_geologue` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            case 'off_technocrate':
                $request = "UPDATE " . TABLE_USER . " SET `off_technocrate` = '" . $value . "' WHERE `user_id` = " . $user_id;
                break;
            default:
                $request = "";
        }
        $db->sql_query($request);
    }

}