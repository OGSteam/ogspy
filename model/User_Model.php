<?php

/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class User_Model extends Model_Abstract
{
    /* Fonctions concerning user account */
    /**
     * @param $login
     * @param $password
     * @return array|bool hash or not
     */
    public function select_user_login($login, $password)
    {
        $login = $this->db->sql_escape_string($login);
        $password = $this->db->sql_escape_string($password);

        // quand tous les password seront mirgés, utilisation de password directement ici a prevoir
        $request = "SELECT `user_id`, `user_active`, `user_password_s` FROM " . TABLE_USER .
            " WHERE `user_name` = '" . $login . "' AND NOT `user_password_s` = ''";
        $result = $this->db->sql_query($request);
        // si pas de retour, user_password_s non encore initialisé
        if (!$this->db->sql_numrows($result)) {
            return false;
        }
        /// autrement faire retour
        $tlogin = $this->db->sql_fetch_row($result);
        return $tlogin;
    }

    /**
     * @param $username
     * @return bool
     */
    public function select_is_user_name($username)
    {
        $username = $this->db->sql_escape_string($username);

        $request = "SELECT * FROM " . TABLE_USER . " WHERE `user_name` = '" . $username . "'";
        $result = $this->db->sql_query($request);
        if ($result !== false && $result->num_rows !== 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $username
     * @return bool
     */
    public function select_is_other_user_name($username, $user_id)
    {
        $username = $this->db->sql_escape_string($username);
        $user_id  = (int) $user_id;

        $request = "SELECT * FROM " . TABLE_USER . " WHERE `user_name` = '" . $username . "' AND `user_id` <> " . $user_id;
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) != 0) {
            return true;
        }
        return false;
    }

    /**
     *  @return array
     */
    public function select_user_list()
    {
        $request = "SELECT `user_name` FROM " . TABLE_USER;
        $list_user_name = array();

        $result = $this->db->sql_query($request);
        while (list($user_name) = $this->db->sql_fetch_row($result)) {
            $list_user_name[] = $user_name;
        }
        return $list_user_name;
    }

    /**
     *  @return array
     */
    public function select_userid_list()
    {
        $request = "SELECT `user_id` FROM " . TABLE_USER;
        $list_user_id = array();

        $result = $this->db->sql_query($request);
        while (list($user_id) = $this->db->sql_fetch_row($result)) {
            $list_user_id[] = $user_id;
        }
        return $list_user_id;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function select_last_visit($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `user_lastvisit` FROM " . TABLE_USER;
        $request .= " WHERE `user_id` = '" . $user_id . "'";
        $result = $this->db->sql_query($request);
        list($lastvisit) = $this->db->sql_fetch_row($result);

        return $lastvisit;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function select_user_data($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `user_id`, `user_name`, `user_password`, `user_email`, `user_active`, `user_regdate`, `user_lastvisit`," .
            " `user_galaxy`, `user_system`, `user_admin`, `user_coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate` , `user_class`, `user_pwd_change`, `user_email_valid` " .
            " FROM " . TABLE_USER;
        $request .= " WHERE `user_id` = " . $user_id;
        $request .= " ORDER BY `user_name`";
        $result = $this->db->sql_query($request);

        $info_users = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
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
        $request = "SELECT `user_id`, `user_name`, `user_password`, `user_email`, `user_active`, `user_regdate`, `user_lastvisit`," .
            " `user_galaxy`, `user_system`, `user_admin`, `user_coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate` , `user_class`, `user_pwd_change`, `user_email_valid` " .
            " FROM " . TABLE_USER;

        $request .= " ORDER BY `user_name`";
        $result = $this->db->sql_query($request);

        $info_users = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
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
        $request = "SELECT `user_id`, `user_name`, `planet_added_ogs`, `search`, `spy_added_ogs`, `rank_added_ogs`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";
        $request .= " FROM " . TABLE_USER . " ORDER BY `planet_added_ogs` DESC";
        $result = $this->db->sql_query($request);
        $retour = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $retour[] = $row;
        }
        return $retour;
    }

    /**
     * @return mixed
     */
    public function select_user_stats_data($user_id)
    {
        $user_id = (int)$user_id;
        //todo requete 3.4
        //voir pour modifier bdd et ctualiser fn appelante et vue
        // $request = "SELECT `user_id`, `user_name`, `planet_added_xtense`, `search`, `spy_added_xtense`, `rank_added_xtense`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";

        $request = "SELECT `user_id`, `user_name`, `planet_added_ogs`, `search`, `spy_added_ogs`, `rank_added_ogs`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";
        $request .= " FROM " . TABLE_USER;
        $request .= " WHERE `user_id`='" . $user_id . "'";
        $result = $this->db->sql_query($request);

        list($planet_added_ogs, $search, $spy_added_ogs, $rank_added_ogs) = $this->db->sql_fetch_row($result);

        return array("planet_added_ogs" => $planet_added_ogs, "search" => $search, "spy_added_ogs" => $spy_added_ogs, "rank_added_ogs" => $rank_added_ogs);
    }

    public function select_user_stats_sum()
    {
        $request = "SELECT SUM(planet_added_xtense), SUM(spy_added_xtense), SUM(rank_added_xtense), SUM(search)";
        $request .= "FROM " . TABLE_USER;
        $resultat = $this->db->sql_query($request);

        list($planetimporttotal, $spyimporttotal, $rankimporttotal, $searchtotal) = $this->db->sql_fetch_row($resultat);

        return array(
            "planetimporttotal" => $planetimporttotal,
            "spyimporttotal" => $spyimporttotal,
            "rankimporttotal" => $rankimporttotal,
            "searchtotal" => $searchtotal
        );
    }

    /**
     * @param $user_id
     * @return array
     */
    public function select_user_rights($user_id)
    {
        $user_id = (int)$user_id;

        $user_auth = array("server_set_system" => 0, "server_set_spy" => 0, "server_set_rc" => 0, "server_set_ranking" => 0, "server_show_positionhided" => 0, "ogs_connection" => 0, "ogs_set_system" => 0, "ogs_get_system" => 0, "ogs_set_spy" => 0, "ogs_get_spy" => 0, "ogs_set_ranking" => 0, "ogs_get_ranking" => 0);


        $request = "SELECT `server_set_system`, `server_set_spy`, `server_set_rc`, `server_set_ranking`, `server_show_positionhided`,";
        $request .= " `ogs_connection`, `ogs_set_system`, `ogs_get_system`, `ogs_set_spy`, `ogs_get_spy`, `ogs_set_ranking`, `ogs_get_ranking`";
        $request .= " FROM " . TABLE_GROUP . " g, " . TABLE_USER_GROUP . " u";
        $request .= " WHERE g.`group_id` = u.`group_id`";
        $request .= " and `user_id` = " . $user_id;
        $result = $this->db->sql_query($request);

        if ($this->db->sql_numrows($result) == 1) { //Un seul retour possible ici
            while ($row = $this->db->sql_fetch_assoc($result)) {
                $user_auth = array(
                    "server_set_system" => $row['server_set_system'],
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
                    "ogs_get_ranking" => $row['ogs_get_ranking']
                );
            }
        }

        return $user_auth;
    }


    /**
     * @param $user_id
     */
    public function update_lastvisit_time($user_id)
    {
        $user_id = (int)$user_id;

        $request = "UPDATE " . TABLE_USER . " SET `user_lastvisit` = " . time() . " WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_name
     */
    public function set_user_pseudo($user_id, $user_name)
    {
        $user_id = (int)$user_id;
        $user_name = $this->db->sql_escape_string($user_name);

        $request = "UPDATE " . TABLE_USER . " SET `user_name` = '" . $user_name . "' WHERE `user_id` = " . $user_id;
        //$request = $this->db->sql_escape_string($request);
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_password
     */
    public function set_user_password($user_id, $encrypted_password, $user_pwd_change = 1)
    {
        $encrypted_password = $this->db->sql_escape_string($encrypted_password);
        $user_id = (int)$user_id;

        $request = "UPDATE " . TABLE_USER . " SET `user_password_s` = '" . $encrypted_password . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
        $request = "UPDATE " . TABLE_USER . " SET `user_pwd_change` = '" . $user_pwd_change . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request); //TODO : SQL simplification en 1 seule requête ??
    }

    /**
     * @param $user_id
     * @param $user_email
     */
    public function set_user_email($user_id, $user_email)
    {
        $user_id = (int)$user_id;
        $user_email = $this->db->sql_escape_string($user_email);

        $request = "UPDATE " . TABLE_USER . " SET `user_email` = '" . $user_email . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
        $request = "UPDATE " . TABLE_USER . " SET `user_email_valid` = '0' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $default_galaxy
     */
    public function set_user_default_galaxy($user_id, $default_galaxy)
    {
        $request = "UPDATE " . TABLE_USER . " SET `user_galaxy` = '" . $default_galaxy . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
        //Nettoyage Préventif
        //coquille ????  $new_num_of_galaxies ?
        //$request = $this->db->sql_query("UPDATE " . TABLE_USER . " SET user_galaxy=1 WHERE user_galaxy > $new_num_of_galaxies");
        //$this->db->sql_query($request);
    }

    /**
     * Utilisé après un redimensionement de l'univers
     * @param $int $nb_galaxy
     */
    public function set_default_galaxy_after_resize($nb_galaxy)
    {
        $nb_galaxy = (int)$nb_galaxy;
        $request = "UPDATE " . TABLE_USER . " SET `user_galaxy` = 1 WHERE `user_galaxy` > $nb_galaxy";
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $default_system
     */
    public function set_user_default_system($user_id, $default_system)
    {
        $user_id = (int)$user_id;
        $default_system = (int)$default_system;

        $request = "UPDATE " . TABLE_USER . " SET `user_system` = '" . $default_system . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Utilisé après un redimensionement de l'univers
     * @param $int $nb_systems
     */
    public function set_default_system_after_resize($nb_systems)
    {
        $nb_systems = (int)$nb_systems;

        $request = $this->db->sql_query("UPDATE " . TABLE_USER . " SET `usersystem` = 1 WHERE `user_system` > $nb_systems");
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $disable_ip_check
     */
    public function set_user_ip_check($user_id, $disable_ip_check)
    {
        $user_id = (int)$user_id;
        $disable_ip_check = (int)$disable_ip_check;


        $request = "UPDATE " . TABLE_USER . " SET `disable_ip_check` = '" . $disable_ip_check . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_active boolean 1/0
     */
    public function set_user_active($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `user_active` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_coadmin($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;


        $request = "UPDATE " . TABLE_USER . " SET `user_coadmin` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_user($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `management_user` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_ranking($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `management_ranking` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_planet_inserted($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `planet_added_ogs` = `planet_added_ogs` + '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
        //todo a implementer ( en remplacement dOGS )
        //$request = "UPDATE " . TABLE_USER . " SET `planet_added_xtense` = planet_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        //$this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_spy_inserted($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;


        $request = "UPDATE " . TABLE_USER . " SET `spy_added_ogs` = `spy_added_ogs` + '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
        //todo a implementer ( en remplacement dOGS )
        //$request = "UPDATE " . TABLE_USER . " SET `spy_added_xtense` = spy_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        //$this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_rank_inserted($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;


        $request = "UPDATE " . TABLE_USER . " SET `rank_added_ogs` = rank_added_ogs + '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
        //todo a implementer ( en remplacement dOGS )
        //$request = "UPDATE " . TABLE_USER . " SET `rank_added_xtense` = rank_added_xtense + '" . $value . "' WHERE `user_id` = " . $user_id;
        //$this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value int
     */
    public function add_stat_search_made($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `search` = search + '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     */
    public function all_raz_ratio_search()
    {
        $request = "UPDATE " . TABLE_USER . " SET `search`='0'";
        $this->db->sql_query($request);
    }

    /**
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_active_users()
    {
        $request = "SELECT `user_id` FROM " . TABLE_USER . " WHERE `user_active` = '1'";
        $this->db->sql_query($request);
        return $this->db->sql_numrows();
    }

    /**
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_users()
    {
        $request = "SELECT `user_id` FROM " . TABLE_USER;
        $this->db->sql_query($request);
        return $this->db->sql_numrows();
    }

    /**
     * @param $pseudo
     * @param $password
     * @return \Ogsteam\Ogspy\Returs
     */
    public function add_new_user($pseudo, $password)
    {
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
        $request = "INSERT INTO " . TABLE_USER . " (`user_name`, `user_password_s`, `user_regdate`, `user_active`)"
            . " VALUES ('" . $pseudo . "', '" . $encrypted_password . "', " . time() . ", '1')";
        $this->db->sql_query($request);

        return $this->db->sql_insertid();
    }

    /**
     * @param $user_id
     * @param $group_id
     */
    public function add_user_to_group($user_id, $group_id)
    {
        $user_id = (int)$user_id;
        $group_id = (int)$group_id;

        $request = "INSERT INTO " . TABLE_USER_GROUP . " (`group_id`, `user_id`) VALUES (" . $group_id . ", " . $user_id . ")";
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     */
    public function delete_user($user_id)
    {
        $user_id = (int)$user_id;

        $requests = array();

        $requests[] = "DELETE FROM " . TABLE_USER . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_GROUP . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_BUILDING . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_FAVORITE . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_DEFENCE . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_SPY . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_TECHNOLOGY . " WHERE `user_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_POINTS . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_ECO . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_TECHNOLOGY . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_MILITARY . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_MILITARY_BUILT . " SET `sender_id`= 0 WHERE `sender_id`= " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_MILITARY_LOOSE . " SET `sender_id`= 0 WHERE `sender_id`= " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_MILITARY_DESTRUCT . " SET `sender_id`= 0 WHERE `sender_id`= " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_PLAYER_HONOR . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_POINTS . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_ECO . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_TECHNOLOGY . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_MILITARY . " SET `sender_id` = 0 WHERE `sender_id` = " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_MILITARY_BUILT . " SET `sender_id`= 0 WHERE `sender_id`= " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_MILITARY_LOOSE . " SET `sender_id`= 0 WHERE `sender_id`= " . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " SET `sender_id`=0 WHERE `sender_id`=" . $user_id;
        $requests[] = "UPDATE " . TABLE_RANK_ALLY_HONOR . " SET `sender_id`=0 WHERE `sender_id`=" . $user_id;
        $requests[] = "UPDATE " . TABLE_UNIVERSE . " SET `last_update_user_id` = 0 WHERE `last_update_user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_MOD_USER_CFG . " WHERE `user_id` = " . $user_id;

        foreach ($requests as $request) {
            $this->db->sql_query($request);
        }
    }

    /* Fonctions concerning game account */

    /**
     * A quoi sert donc cette fonction ? :p
     * Reponse elle sert a mettre a jour le pseudo ingame afin d afficher les stats users dans son espace perso
     *
     * @param $user_id
     * @param $user_stat_name
     */
    public function set_game_account_name($user_id, $user_stat_name)
    {
        $user_id = (int)$user_id;
        $user_stat_name = $this->db->sql_escape_string($user_stat_name);

        $request = "UPDATE " . TABLE_USER . " SET `user_stat_name` = '$user_stat_name' WHERE `user_id` = $user_id";
        $this->db->sql_query($request);
    }

    /**
     *
     * @param user_class
     */
    public function set_game_class_type($user_id, $user_class)
    {
        $user_id = (int)$user_id;
        $user_class = $this->db->sql_escape_string($user_class);

        $request = "UPDATE " . TABLE_USER . " SET `user_class`  = '" . $user_class . "' WHERE `user_id` = " . $user_id;

        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $officer
     * @param $value
     */
    public function set_player_officer($user_id, $officer, $value)
    {
        $officer = $this->db->sql_escape_string($officer);
        $value = (int)$value;

        $allowedOfficers = [
            'off_commandant',
            'off_amiral',
            'off_ingenieur',
            'off_geologue',
            'off_technocrate',
        ];

        if (in_array($officer, $allowedOfficers)) {
            $request = "UPDATE " . TABLE_USER . " SET `" . $officer . "` = " . $value . " WHERE `user_id` = " . (int)$user_id;
            $this->db->sql_query($request);
        }
    }
}
