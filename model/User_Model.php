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
    /* User account related methods */
    /**
     * Fetch login data for a user by username.
     *
     * @param string $login
     * @param string $password
     * @return array<int, mixed>|false
     */
    public function select_user_login($login, $password)
    {
        $login = $this->db->sql_escape_string($login);
        $password = $this->db->sql_escape_string($password);

        $request = "SELECT `id`, `active`, `password_s` FROM " . TABLE_USER . " WHERE `name` = '$login'";
        $result = $this->db->sql_query($request);
        // si pas de retour, user_password_s non encore initialisé
        if (!$this->db->sql_numrows($result)) {
            return false;
        }
        /// autrement faire retour
        $userLoginData = $this->db->sql_fetch_row($result);
        return $userLoginData;
    }

    /**
     * Check whether a username already exists.
     *
     * @param string $username
     * @return bool
     */
    public function select_is_user_name($username)
    {
        $username = mb_strtolower($username, 'UTF-8');
        $username = $this->db->sql_escape_string($username);

        $request = "SELECT * FROM " . TABLE_USER . " WHERE LOWER(`name`) = '$username'";
        $result = $this->db->sql_query($request);
        if ($result !== false && $result->num_rows !== 0) {
            return true;
        }
        return false;
    }

    /**
     * Check whether a username exists for another user ID.
     *
     * @param string $username
     * @param int $user_id
     * @return bool
     */
    public function select_is_other_user_name($username, $user_id)
    {
        $username = mb_strtolower($username, 'UTF-8');
        $username = $this->db->sql_escape_string($username);
        $user_id  = (int) $user_id;

        $request = "SELECT * FROM " . TABLE_USER . " WHERE LOWER(`name`) = '" . $username . "' AND `id` <> " . $user_id;
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) != 0) {
            return true;
        }
        return false;
    }

    /**
     * Return the list of all usernames.
     *
     * @return array<int, string>
     */
    public function select_user_list()
    {
        $request = "SELECT `name` FROM " . TABLE_USER;
        $list_user_name = array();

        $result = $this->db->sql_query($request);
        while (($row = $this->db->sql_fetch_row($result)) !== false && $row !== null) {
            list($user_name) = $row;
            $list_user_name[] = $user_name;
        }
        return $list_user_name;
    }

    /**
     * Return the list of all user IDs.
     *
     * @return array<int, int|string>
     */
    public function select_userid_list()
    {
        $request = "SELECT `id` FROM " . TABLE_USER;
        $list_user_id = array();

        $result = $this->db->sql_query($request);
        while (($row = $this->db->sql_fetch_row($result)) !== false && $row !== null) {
            list($user_id) = $row;
            $list_user_id[] = $user_id;
        }
        return $list_user_id;
    }

    /**
     * Get the last visit timestamp for a user.
     *
     * @param int $user_id
     * @return int|string|null
     */
    public function select_last_visit($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `lastvisit` FROM " . TABLE_USER;
        $request .= " WHERE `id` = '" . $user_id . "'";
        $result = $this->db->sql_query($request);
        $row = $this->db->sql_fetch_row($result);
        $lastvisit = (is_array($row) && isset($row[0])) ? $row[0] : null;

        return $lastvisit;
    }

    /**
     * Fetch profile data for one user.
     *
     * @param int $user_id
     * @return array<int, array<string, mixed>>|false
     */
    public function select_user_data($user_id)
    {
        $user_id = (int)$user_id;

        $request = "SELECT `id`, `name`, `email`, `active`, `regdate`, `lastvisit`," .
            " `default_galaxy`, `default_system`, `admin`, `coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `pwd_change`, `email_valid` " .
            " FROM " . TABLE_USER;
        $request .= " WHERE `id` = " . $user_id;
        $request .= " ORDER BY `name`";
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
     * Fetch profile data for all users.
     *
     * @return array<int, array<string, mixed>>|false
     */
    public function select_all_user_data()
    {
        $request = "SELECT `id`, `name`, `email`, `active`, `regdate`, `lastvisit`," .
            " `default_galaxy`, `default_system`, `admin`, `coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `pwd_change`, `email_valid` " .
            " FROM " . TABLE_USER;

        $request .= " ORDER BY `name`";
        $result = $this->db->sql_query($request);

        $info_users = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $info_users[] = $row;
        }

        if (empty($info_users)) {
            return false;
        }
        return $info_users;
    }

    /**
     * Fetch statistics data for all users.
     *
     * @return array<int, array<string, mixed>>
     */
    public function select_all_user_stats_data()
    {
        $request = "SELECT `id`, `name`, `planet_imports`, `search`, `spy_imports`, `rank_imports`, `xtense_type`, `xtense_version`, `active`, `admin`";
        $request .= " FROM " . TABLE_USER . " ORDER BY `planet_imports` DESC";
        $result = $this->db->sql_query($request);
        $retour = array();
        while ($row = $this->db->sql_fetch_assoc($result)) {
            $retour[] = $row;
        }
        return $retour;
    }

    /**
     * Fetch statistics data for one user.
     *
     * @param int $user_id
     * @return array{planet_imports:int|string,search:int|string,spy_imports:int|string,rank_imports:int|string}
     */
    public function select_user_stats_data($user_id)
    {
        $user_id = (int)$user_id;
        //todo requete 3.4
        //voir pour modifier bdd et ctualiser fn appelante et vue
        // $request = "SELECT `user_id`, `user_name`, `planet_added_xtense`, `search`, `spy_added_xtense`, `rank_added_xtense`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";

        $request = "SELECT `id`, `name`, `planet_imports`, `search`, `spy_imports`, `rank_imports`, `xtense_type`, `xtense_version`, `active`, `admin`";
        $request .= " FROM " . TABLE_USER;
        $request .= " WHERE `id`='" . $user_id . "'";
        $result = $this->db->sql_query($request);

        $row = $this->db->sql_fetch_row($result);
        $planet_imports = (is_array($row) && isset($row[0])) ? $row[0] : 0;
        $search = (is_array($row) && isset($row[1])) ? $row[1] : 0;
        $spy_imports = (is_array($row) && isset($row[2])) ? $row[2] : 0;
        $rank_imports = (is_array($row) && isset($row[3])) ? $row[3] : 0;

        return array("planet_imports" => $planet_imports, "search" => $search, "spy_imports" => $spy_imports, "rank_imports" => $rank_imports);
    }

    /**
     * Compute aggregated import and search counters across all users.
     *
     * @return array{planetimporttotal:int|string,spyimporttotal:int|string,rankimporttotal:int|string,searchtotal:int|string}
     */
    public function select_user_stats_sum()
    {
        $request = "SELECT SUM(planet_imports), SUM(spy_imports), SUM(rank_imports), SUM(search)";
        $request .= "FROM " . TABLE_USER;
        $resultat = $this->db->sql_query($request);

        $row = $this->db->sql_fetch_row($resultat);
        $planetimporttotal = (is_array($row) && isset($row[0])) ? $row[0] : 0;
        $spyimporttotal = (is_array($row) && isset($row[1])) ? $row[1] : 0;
        $rankimporttotal = (is_array($row) && isset($row[2])) ? $row[2] : 0;
        $searchtotal = (is_array($row) && isset($row[3])) ? $row[3] : 0;

        return array(
            "planetimporttotal" => $planetimporttotal,
            "spyimporttotal" => $spyimporttotal,
            "rankimporttotal" => $rankimporttotal,
            "searchtotal" => $searchtotal
        );
    }

    /**
     * Fetch effective rights for a user through group membership.
     *
     * @param int $user_id
     * @return array<string, int|string>
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
     * Update the last visit timestamp for a user.
     *
     * @param int $user_id
     * @return void
     */
    public function update_lastvisit_time($user_id)
    {
        $user_id = (int)$user_id;

        $request = "UPDATE " . TABLE_USER . " SET `lastvisit` = " . time() . " WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Update a user's display name.
     *
     * @param int $user_id
     * @param string $user_name
     * @return void
     */
    public function set_user_pseudo($user_id, $user_name)
    {
        $user_id = (int)$user_id;
        $user_name = $this->db->sql_escape_string($user_name);

        $request = "UPDATE " . TABLE_USER . " SET `name` = '" . $user_name . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Update a user's password hash.
     *
     * @param int $user_id
     * @param string $encrypted_password
     * @param int $user_pwd_change
     * @return void
     */
    public function set_user_password($user_id, $encrypted_password, $user_pwd_change = 1)
    {
        $encrypted_password = $this->db->sql_escape_string($encrypted_password);
        $user_id = (int)$user_id;

        $request = "UPDATE " . TABLE_USER . "
            SET `password_s` = '" . $encrypted_password . "',
                `pwd_change` = '" . $user_pwd_change . "'
            WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Update a user's email and reset validation state.
     *
     * @param int $user_id
     * @param string $user_email
     * @return void
     */
    public function set_user_email($user_id, $user_email)
    {
        $user_id = (int)$user_id;
        $user_email = $this->db->sql_escape_string($user_email);

        $request = "UPDATE " . TABLE_USER . "
            SET `email` = '" . $user_email . "',
                `email_valid` = '0'
            WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Update a user's default galaxy.
     *
     * @param int $user_id
     * @param int $default_galaxy
     * @return void
     */
    public function set_user_default_galaxy($user_id, $default_galaxy)
    {
        $request = "UPDATE " . TABLE_USER . " SET `default_galaxy` = '" . $default_galaxy . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
        //Nettoyage Préventif
        //coquille ????  $new_num_of_galaxies ?
        //$request = $this->db->sql_query("UPDATE " . TABLE_USER . " SET user_galaxy=1 WHERE user_galaxy > $new_num_of_galaxies");
        //$this->db->sql_query($request);
    }

    /**
     * Reset default galaxy when universe size is reduced.
     *
     * @param int $nb_galaxy
     * @return void
     */
    public function set_default_galaxy_after_resize($nb_galaxy)
    {
        $nb_galaxy = (int)$nb_galaxy;
        $request = "UPDATE " . TABLE_USER . " SET `default_galaxy` = 1 WHERE `default_galaxy` > $nb_galaxy";
        $this->db->sql_query($request);
    }

    /**
     * Update a user's default system.
     *
     * @param int $user_id
     * @param int $default_system
     * @return void
     */
    public function set_user_default_system($user_id, $default_system)
    {
        $user_id = (int)$user_id;
        $default_system = (int)$default_system;

        $request = "UPDATE " . TABLE_USER . " SET `default_system` = '" . $default_system . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Reset default system when universe size is reduced.
     *
     * @param int $nb_systems
     * @return void
     */
    public function set_default_system_after_resize($nb_systems)
    {
        $nb_systems = (int)$nb_systems;

        $request = $this->db->sql_query("UPDATE " . TABLE_USER . " SET `default_system` = 1 WHERE `default_system` > $nb_systems");
        $this->db->sql_query($request);
    }

    /**
     * Enable or disable IP checks for a user.
     *
     * @param int $user_id
     * @param int $disable_ip_check
     * @return void
     */
    public function set_user_ip_check($user_id, $disable_ip_check)
    {
        $user_id = (int)$user_id;
        $disable_ip_check = (int)$disable_ip_check;


        $request = "UPDATE " . TABLE_USER . " SET `disable_ip_check` = '" . $disable_ip_check . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }


    /**
     * Set user active status.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function set_user_active($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `active` = '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Set user co-admin status.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function set_user_coadmin($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;


        $request = "UPDATE " . TABLE_USER . " SET `coadmin` = '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Set user management permission.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function set_user_management_user($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `management_user` = '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Set user ranking management permission.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function set_user_management_ranking($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `management_ranking` = '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Increment user planet import counter.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function add_stat_planet_inserted($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `planet_imports` = `planet_imports` + '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Increment user spy import counter.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function add_stat_spy_inserted($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `spy_imports` = `spy_imports` + '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Increment user ranking import counter.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function add_stat_rank_inserted($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `rank_imports` = rank_imports + '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Increment user search counter.
     *
     * @param int $user_id
     * @param int $value
     * @return void
     */
    public function add_stat_search_made($user_id, $value)
    {
        $user_id = (int)$user_id;
        $value = (int)$value;

        $request = "UPDATE " . TABLE_USER . " SET `search` = search + '" . $value . "' WHERE `id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Reset search counters for all users.
     *
     * @return void
     */
    public function all_raz_ratio_search()
    {
        $request = "UPDATE " . TABLE_USER . " SET `search`='0'";
        $this->db->sql_query($request);
    }

    /**
     * Count active users.
     *
     * @return int
     */
    public function get_nb_active_users()
    {
        $request = "SELECT `id` FROM " . TABLE_USER . " WHERE `active` = '1'";
        $this->db->sql_query($request);
        return $this->db->sql_numrows();
    }

    /**
     * @return int
     */
    public function get_nb_users()
    {
        $result = $this->db->sql_query("SELECT COUNT(*) FROM " . TABLE_USER);
        $row = $this->db->sql_fetch_row($result);
        $count = (is_array($row) && isset($row[0])) ? (int)$row[0] : 0;
        return $count;
    }

    /**
     * Create a new user and assign the default group.
     *
     * @param string $pseudo
     * @param string $password
     * @return int
     */
    public function add_new_user($pseudo, $password)
    {
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
        $request = "INSERT INTO " . TABLE_USER . " (`name`, `password_s`, `regdate`, `active`)"
            . " VALUES ('" . $pseudo . "', '" . $encrypted_password . "', " . time() . ", '1')";
        $this->db->sql_query($request);

        $user_id = $this->db->sql_insertid();

        // Assigner automatiquement l'utilisateur au groupe par défaut (groupe ID 1)
        if ($user_id) {
            $this->add_user_to_group($user_id, 1);
        }

        return $user_id;
    }

    /**
     * Check whether a user belongs to a specific group.
     *
     * @param int $user_id
     * @param int $group_id
     * @return bool
     */
    public function is_user_in_group($user_id, $group_id)
    {
        $user_id = (int)$user_id;
        $group_id = (int)$group_id;

        $request = "SELECT COUNT(*) FROM " . TABLE_USER_GROUP . " WHERE `group_id` = " . $group_id . " AND `user_id` = " . $user_id;
        $result = $this->db->sql_query($request);
        $row = $this->db->sql_fetch_row($result);
        $count = (is_array($row) && isset($row[0])) ? (int)$row[0] : 0;
        return $count > 0;
    }

    /**
     * Add a user to a group if not already linked.
     *
     * @param int $user_id
     * @param int $group_id
     * @return void
     */
    public function add_user_to_group($user_id, $group_id)
    {
        $user_id = (int)$user_id;
        $group_id = (int)$group_id;

        // Vérifier si l'utilisateur est déjà dans le groupe pour éviter les doublons
        if (!$this->is_user_in_group($user_id, $group_id)) {
            $request = "INSERT INTO " . TABLE_USER_GROUP . " (`group_id`, `user_id`) VALUES (" . $group_id . ", " . $user_id . ")";
            $this->db->sql_query($request);
        }
    }

    /**
     * Delete a user and linked records.
     *
     * @param int $user_id
     * @return void
     */
    public function delete_user($user_id)
    {
        $user_id = (int)$user_id;

        $requests = array();

        $requests[] = "DELETE FROM " . TABLE_USER . " WHERE `id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_GROUP . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_FAVORITE . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_USER_SPY . " WHERE `user_id` = " . $user_id;
        $requests[] = "DELETE FROM " . TABLE_MOD_USER_CFG . " WHERE `user_id` = " . $user_id;

        foreach ($requests as $request) {
            $this->db->sql_query($request);
        }
    }
}
