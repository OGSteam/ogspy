<?php

/**
 * Model used by the Data API to manage connected-user tokens.
 * Réécriture of the modèle of session destinée à le remplacer.
 * @package OGSpy
 * @subpackage Model
 * @author DarkNoon
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy\Model;

use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Tokens_Model extends Model_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->delete_expired_tokens();
    }
    /**
    * Adds or updates a token in the database.
     * @param $token_id
     * @param $token_user_id
     * @param $token_expire
     * @param $token_type (PAT, .... )
     * @internal param $cookie_expire
     * @return mixed
     */
    public function add_token($token_id, $token_user_id, $token_expire, $token_type)
    {
        $token_id = $this->db->sql_escape_string($token_id);
        $token_user_id = (int)$token_user_id;
        $token_expire = (int)$token_expire;
        $token_type = $this->db->sql_escape_string($token_type);

        if ($this->get_token($token_user_id, $token_type) != false) {
            $request = "UPDATE " . TABLE_USER_TOKEN . " SET  `token` = '" . $token_id . "', `expiration_date` = '" . $token_expire . "' WHERE `user_id` = '" . $token_user_id . "' AND `name` =  '" . $token_type . "'";
            $this->db->sql_query($request, true, false);
        } else {
            $request = "INSERT INTO " . TABLE_USER_TOKEN . " (`id`, `user_id`, `name`, `token`, `expiration_date`) VALUES (NULL, '" . $token_user_id . "', '" . $token_type . "' , '" . $token_id . "', '" . $token_expire . "')";
            $this->db->sql_query($request);
        }
        return $this->get_token($token_user_id, $token_type);;
    }

    /**
    * Retrieves a user's token identifier.
     * @param $token_user_id
     * @return mixed
     */
    public function get_token($token_user_id, $token_type)
    {
        $token_user_id = (int)$token_user_id;
        $token_type = $this->db->sql_escape_string($token_type);

        $request = "SELECT `token` FROM " . TABLE_USER_TOKEN . " WHERE `user_id`= '" . $token_user_id . "' AND `name` =  '" . $token_type . "'";
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) > 0) {
            $row = $this->db->sql_fetch_row($result);
            $token_id = (is_array($row) && isset($row[0])) ? $row[0] : false;
            return $token_id;
        } else {
            return false;
        }
    }

    /**
    * Retrieves all tokens for a user.
     * @param $token_user_id
     * @return mixed
     */
    public function get_all_tokens($token_user_id)
    {
        $token_user_id = (int)$token_user_id;

        $request = "SELECT `token`, `name` ,`expiration_date`   FROM " . TABLE_USER_TOKEN . " WHERE `user_id`= '" . $token_user_id . "' ";

        $result = $this->db->sql_query($request);
        $tRetour = array();
        if ($this->db->sql_numrows($result) > 0) {
            while ($row = $this->db->sql_fetch_assoc($result)) {
                $tRetour[$row['name']] = $row;
            }
            return $tRetour;
        } else {
            return false;
        }
    }

    /**
    * Retrieves a user identifier from a token.
     * @param $token
     * @return boolean true False
     * @internal param $token_user_id
     */
    public function get_userid_from_token($token, $token_type)
    {
        $token = $this->db->sql_escape_string($token);
        $token_type = $this->db->sql_escape_string($token_type);

        $request = "SELECT `user_id` FROM " . TABLE_USER_TOKEN . " WHERE `token`= '" . $token . "' AND `name` =  '" . $token_type . "'";
        $result = $this->db->sql_query($request);
        if ($this->db->sql_numrows($result) > 0) {
            $row = $this->db->sql_fetch_row($result);
            $token_user_id = (is_array($row) && isset($row[0])) ? (int)$row[0] : false;
            return $token_user_id;
        } else {
            return false;
        }
    }


    /**
    * Deletes all tokens from the table.
     */
    public function delete_all_tokens()
    {
        $request = "DELETE FROM " . TABLE_USER_TOKEN;
        $this->db->sql_query($request);
    }


    /**
    * Deletes all tokens of a given type.
     */
    public function delete_all_tokens_by_type($token_type)
    {
        $token_type = $this->db->sql_escape_string($token_type);

        $request = "DELETE FROM " . TABLE_USER_TOKEN . " WHERE `name` =  '" . $token_type . "'";
        $this->db->sql_query($request);
    }


    /**
    * Deletes all expired tokens.
     */
    public function delete_expired_tokens()
    {
        $request = "DELETE FROM " . TABLE_USER_TOKEN . " WHERE expiration_date < " . time();
        $this->db->sql_query($request);
    }
}
