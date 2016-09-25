<?php
/**
 * Model class used by the API Data to manage connected user tokens
 *  This is a rewriting of the Session Model and will replace it in the future
 * User: DarkNoon
 * Date: 24/09/2016
 * Time: 20:37
 */

namespace Ogsteam\Ogspy\Model;


class Tokens_Model
{
    function __construct() {
        $this->delete_expired_tokens();
    }
    /**
     * This function will add or update a token into the database
     * @param $token_id
     * @param $token_user_id
     * @param $token_expire
     * @param $token_type (Token Android, Ios,... )
     * @internal param $cookie_expire
     * @return mixed
     */
    public function add_token($token_id, $token_user_id, $token_expire, $token_type){
        global $db;

        if($this->get_token($token_user_id) != null){

            $request = "UPDATE " . TABLE_TOKENS . " SET `token_expire` = " . $token_expire . " WHERE `token_user_id` = '" . $token_user_id . "'";
            $db->sql_query($request, true, false);

        }else{
            $request = "INSERT INTO " . TABLE_TOKENS . " (`token_id`, `token_user_id`, `token_expire`, `token_type`) VALUES ('" . $token_id . "', '". $token_user_id . "', '" . $token_expire . "', '". $token_type ."')";
            $db->sql_query($request);
        }

        return $this->get_token($token_user_id);;
    }

    /**
     * THis function will retrieve the token id from the user
     * @param $token_user_id
     * @return mixed
     */
    public function get_token($token_user_id){
        global $db;
        $request = "SELECT `token_id` FROM " . TABLE_TOKENS . " WHERE `token_user_id`= ". $token_user_id;
        $result = $db->sql_query($request);

        if($db->sql_numrows($result) > 0)
            list($token_id) = $db->sql_fetch_row($result);
        else
            $token_id = null;

        return $token_id;
    }

    /**
     * THis function will retrieve the token id from the user
     * @param $token
     * @return boolean true False
     * @internal param $token_user_id
     */
    public function get_userid_from_token($token){
        global $db;
        $request = "SELECT `token_user_id` FROM " . TABLE_TOKENS . " WHERE `token_id`= '". $token ."'";
        $result = $db->sql_query($request);

        if($db->sql_numrows($result) > 0){
            list($token_user_id) = $db->sql_fetch_row($result);
            return $token_user_id;
        }
        else
            return false;
    }

    /**
     *  This function removes all tokens from the Table
     */
    public function delete_all_tokens(){
        global $db;
        $request = "DELETE FROM " . TABLE_TOKENS;
        $db->sql_query($request);
    }

    /**
     * This function clean all expired tokens
     */
    public function delete_expired_tokens(){
        global $db;
        $request = "DELETE FROM " . TABLE_TOKENS . " WHERE token_expire < " . time();
        $db->sql_query($request);
    }

}