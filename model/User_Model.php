<?php
/**
 * Database Model
 *
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class User_Model extends Model_Abstract
{
    /* Fonctions concerning user account */
    /**
     * @param $login
     * @param $password
     * @return array
     */
    public function select_user_login($login, $password)
    {
        // quand tous les password seront mirgés, utilisation de password directement ici a prevoir
        $request = "SELECT user_id, user_active, user_password_s FROM " . TABLE_USER .
            " WHERE user_name = '" . $login . "' AND NOT user_password_s = ''";
        $result = $this->db->sql_query($request);
        // si pas de retour, user_password_s non encore initialisé
        if (!$this->db->sql_numrows($result)) {
            return false;
        }
        /// autrement faire retour
        $tlogin = $this->db->sql_fetch_row($result);
        return $tlogin;
    }

    /* Fonctions concerning user account */
    /**
     * Permet la connexion avec ancien system de login et migre vers le nouveau
     * @param $login
     * @param $password
     * @return array
     */
    public function select_user_login_legacy($login, $password)
    {
        $request = "SELECT user_id, user_active FROM " . TABLE_USER .
            " WHERE user_name = '" . $login .
            "' AND user_password = '" . md5(sha1($password)) . "'";
        $result = $this->db->sql_query($request);

        // si reponse, password non migré / si rien erreur de login
        if (!$this->db-- > sql_numrows($result)) {
            return false;
        }

        $tlogin = $this->db->sql_fetch_row($result);

        //Ajout du nouveau mot de passe et supression ancien
        $request = "UPDATE " . TABLE_USER . " SET `user_password_s` = '" . password_hash($password, PASSWORD_DEFAULT) . "' WHERE `user_id` = " . $tlogin['user_id'];
        $this->db->sql_query($request);
        $request = "UPDATE " . TABLE_USER . " SET `user_password` = '' WHERE `user_id` = " . $tlogin['user_id'];
        $this->db->sql_query($request);

        return $tlogin;
    }

    /**
     * @param $username
     * @return bool|mixed|\Ogsteam\Ogspy\mysqli_result
     */
    public function select_user_name($username)
    {

        $request = "SELECT * FROM " . TABLE_USER . " WHERE `user_name` = '" . $username . "'";

        $request = $this->db->sql_escape_string($request);
        $result = $this->db->sql_query($request);

        return $result;
    }

    public function select_user_list()
    {


        $request = "SELECT `user_name` FROM " . TABLE_USER;

        $request = $this->db->sql_escape_string($request);
        $result = $this->db->sql_query($request);
        list($user_name) = $this->db->sql_fetch_row($result);

        return $user_name;
    }

    public function select_userid_list()
    {


        $request = "SELECT `user_id` FROM " . TABLE_USER;

        $request = $this->db->sql_escape_string($request);
        $result = $this->db->sql_query($request);
        $list_user_id = array();
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

        $request = "SELECT `user_id`, `user_name`, `user_password`, `user_email`, `user_active`, `user_regdate`, `user_lastvisit`," .
            " `user_galaxy`, `user_system`, `user_admin`, `user_coadmin`, `management_user`, `management_ranking`, `disable_ip_check`," .
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`" .
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
            " `off_commandant`, `off_amiral`, `off_ingenieur`, `off_geologue`, `off_technocrate`" .
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
        //todo ancciennement :   $request = "select user_id, user_name, planet_added_web, planet_added_ogs, search, spy_added_web, spy_added_ogs, rank_added_web, rank_added_ogs, planet_exported, spy_exported, rank_exported, xtense_type, xtense_version, user_active, user_admin";
        // penser a spprimer champs

        //todo dans 3.4 voci requete
        //modifier les OGS par Xtense
        //$request = "SELECT `user_id`, `user_name`, `planet_added_xtense`, `search`, `spy_added_xtense`, `rank_added_xtense`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";
        //planet_added_ogs=>planet_added_xtense
        //spy_added_ogs=>spy_added_xtense
        //planet_added_ogs=>planet_added_xtense
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
        //todo requete 3.4
        //voir pour modifier bdd et ctualiser fn appelante et vue
        // $request = "SELECT `user_id`, `user_name`, `planet_added_xtense`, `search`, `spy_added_xtense`, `rank_added_xtense`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";

        $request = "SELECT `user_id`, `user_name`, `planet_added_ogs`, `search`, `spy_added_ogs`, `rank_added_ogs`, `xtense_type`, `xtense_version`, `user_active`, `user_admin`";
        $request .= " FROM " . TABLE_USER;
        $request .= " WHERE user_id='" . $user_id . "'";
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


        $user_auth = array("server_set_system" => 0, "server_set_spy" => 0, "server_set_rc" => 0, "server_set_ranking" => 0, "server_show_positionhided" => 0, "ogs_connection" => 0, "ogs_set_system" => 0, "ogs_get_system" => 0, "ogs_set_spy" => 0, "ogs_get_spy" => 0, "ogs_set_ranking" => 0, "ogs_get_ranking" => 0);


        $request = "SELECT `server_set_system`, `server_set_spy`, `server_set_rc`, `server_set_ranking`, `server_show_positionhided`,";
        $request .= " ogs_connection, ogs_set_system, ogs_get_system, ogs_set_spy, ogs_get_spy, ogs_set_ranking, ogs_get_ranking";
        $request .= " from " . TABLE_GROUP . " g, " . TABLE_USER_GROUP . " u";
        $request .= " where g.group_id = u.group_id";
        $request .= " and user_id = " . $user_id;
        $result = $this->db->sql_query($request);

        if ($this->db->sql_numrows($result) == 1) { //Un seul retour possible ici
            while ($row = $this->db->sql_fetch_assoc($result)) {
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

        $request = "UPDATE " . TABLE_USER . " SET `user_lastvisit` = " . time() . " WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_name
     */
    public function set_user_pseudo($user_id, $user_name)
    {

        $request = "UPDATE " . TABLE_USER . " SET `user_name` = '" . $user_name . "' WHERE `user_id` = " . $user_id;
        $request = $this->db->sql_escape_string($request);
        $this->db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $user_password
     */
    public function set_user_password($user_id, $user_password)
    {

        $encrypted_password = crypto($user_password);
        $request = "UPDATE " . TABLE_USER . " SET `user_password` = '" . $encrypted_password . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);

    }

    /**
     * @param $user_id
     * @param $user_email
     */
    public function set_user_email($user_id, $user_email)
    {

        $request = "UPDATE " . TABLE_USER . " SET `user_email` = '" . $user_email . "' WHERE `user_id` = " . $user_id;
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

        $request = $this->db->sql_query("UPDATE " . TABLE_USER . " SET `user_galaxy` = 1 WHERE `user_galaxy` > $nb_galaxy");
        $this->db->sql_query($request);
    }


    /**
     * @param $user_id
     * @param $default_system
     */
    public function set_user_default_system($user_id, $default_system)
    {

        $request = "UPDATE " . TABLE_USER . " SET `user_system` = '" . $default_system . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * Utilisé après un redimensionement de l'univers
     * @param $int $nb_systems
     */
    public function set_default_system_after_resize($nb_systems)
    {

        $request = $this->db->sql_query("UPDATE " . TABLE_USER . " SET `usersystem` = 1 WHERE `user_system` > $nb_systems");
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $disable_ip_check
     */
    public function set_user_ip_check($user_id, $disable_ip_check)
    {

        $request = "UPDATE " . TABLE_USER . " SET `disable_ip_check` = '" . $disable_ip_check . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $user_active boolean 1/0
     */
    public function set_user_active($user_id, $value)
    {

        $request = "UPDATE " . TABLE_USER . " SET `user_active` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_coadmin($user_id, $value)
    {

        $request = "UPDATE " . TABLE_USER . " SET `user_coadmin` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_user($user_id, $value)
    {

        $request = "UPDATE " . TABLE_USER . " SET `management_user` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function set_user_management_ranking($user_id, $value)
    {

        $request = "UPDATE " . TABLE_USER . " SET `management_ranking` = '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $value boolean 1/0
     */
    public function add_stat_planet_inserted($user_id, $value)
    {
        $request = "UPDATE " . TABLE_USER . " SET `planet_added_ogs` = planet_added_ogs + '" . $value . "' WHERE `user_id` = " . $user_id;
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
        $request = "UPDATE " . TABLE_USER . " SET `spy_added_ogs` = spy_added_ogs + '" . $value . "' WHERE `user_id` = " . $user_id;
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

        $request = "UPDATE " . TABLE_USER . " SET `search` = search + '" . $value . "' WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     */
    public function all_raz_ratio_search()
    {
        $request = "UPDATE " . TABLE_USER . " set search='0'";
        $this->db->sql_query($request);
    }

    /**
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_active_users()
    {


        $request = "SELECT `user_id` FROM " . TABLE_USER . " WHERE `user_active` = '1'";
        $result = $this->db->sql_query($request);
        return $number = $this->db->sql_numrows();
    }

    /**
     * @return \Ogsteam\Ogspy\the
     */
    public function get_nb_users()
    {


        $request = "SELECT `user_id` FROM " . TABLE_USER;
        $this->db->sql_query($request);
        return $number = $this->db->sql_numrows();
    }

    /**
     * @param $pseudo
     * @param $password
     * @return \Ogsteam\Ogspy\Returs
     */
    public function add_new_user($pseudo, $password)
    {


        $encrypted_password = Ogspy\crypto($password);
        $request = "INSERT INTO " . TABLE_USER . " (user_name, user_password, user_regdate, user_active)"
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


        $request = "INSERT INTO " . TABLE_USER_GROUP . " (group_id, user_id) VALUES (" . $group_id . ", " . $user_id . ")";
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     */
    public function delete_user($user_id)
    {


        $request = "DELETE FROM " . TABLE_USER . " WHERE `user_id` = " . $user_id;
        $this->db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_GROUP . " where user_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_BUILDING . " where user_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_FAVORITE . " where user_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_DEFENCE . " where user_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_SPY . " where user_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "DELETE FROM " . TABLE_USER_TECHNOLOGY . " where user_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_POINTS . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_ECO . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_TECHNOLOGY . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY_BUILT . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY_LOOSE . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_MILITARY_DESTRUCT . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_PLAYER_HONOR . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_POINTS . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_ECO . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_TECHNOLOGY . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY_BUILT . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY_LOOSE . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_MILITARY_DESTRUCT . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_RANK_ALLY_HONOR . " set sender_id = 0 where sender_id = " . $user_id;
        $this->db->sql_query($request);

        $request = "update " . TABLE_UNIVERSE . " set last_update_user_id = 0 where last_update_user_id = " . $user_id;
        $this->db->sql_query($request);


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
        $request = "update " . TABLE_USER . " set user_stat_name = '" . $user_stat_name . "' where user_id = " . $user_id;
        $this->db->sql_query($request);
    }

    /**
     * @param $user_id
     * @param $officer
     * @param $value
     */
    public function set_player_officer($user_id, $officer, $value)
    {

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
        $this->db->sql_query($request);
    }

}