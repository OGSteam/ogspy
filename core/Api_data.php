<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 24/09/2016
 * Time: 19:50
 */

namespace Ogsteam\Ogspy\Api;
use Ogsteam\Ogspy\Model\Config_Model;
use Ogsteam\Ogspy\Model\Statistics_Model;
use Ogsteam\Ogspy\Model\Tokens_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Sinergi\Token\StringGenerator;
use Sabre\HTTP;


class Api_data
{
    private $authenticated_token = false;
    private $user_id_token = false;

    /**
     * @param $login
     * @param $password
     * @return bool
     */
    public function authenticate_by_user($login, $password)
    {

        global $db;
        $data_user = new User_Model();
        $result = $data_user->select_user_login($login, $password, true);

        if (list($user_id, $user_active) = $db->sql_fetch_row($result)) {
            if ($user_active == 1) {

                $generated_token = $this->generate_token();
                $data_token = new Tokens_Model();
                $private_token = $data_token->add_token($generated_token, $user_id, time() + 3600, "android");
                $this->authenticated_token = $private_token;
                $this->send_response(array('status' => 'ok', 'api_token' => $private_token));
            }else
                exit(); //On ne retourne rien pour masquer API
        }
    }

    /**
     * @param $token
     * @return bool
     */
    public function authenticate_by_token($token){

        $data_token = new Tokens_Model();
        $user_id = $data_token->get_userid_from_token($token);
        if($user_id !== false){
            $this->authenticated_token = $token;
            $this->user_id_token = $user_id;
            return true;
        }else{
            $feedback = array('status' => 'error_auth');
            $this->send_response($feedback);
        }
            return false;
    }


    /**
     * Entry point for API commands
     * This function will call the required and private function to get the requested data
     * @param $data
     */
    public function api_treat_command($data){

        $data_decoded = json_decode($data);
        //print $data_decoded;
        switch ($data_decoded['cmd']) {
            case "ogspy_server_details" :
                $this->api_send_ogspy_server_details();
            break;
            case "ogspy_ally_details" :
                $this->api_send_ogspy_ally_details();
                break;
            case "ogspy_user_details" :
                $this->api_send_ogspy_player_details();
                break;
            default:
                break;
        }
        //Cas envoyer liste paramètres utilisateurs.
        //$this->api_send_user_list();

    }

    /**
     * Fonction test envoi de données
     */
    private function api_send_ogspy_server_details(){

        if($this->authenticated_token != null){

            $data_config = new Config_Model();
            $data_config = $data_config->find_by(array("servername","register_alliance","allied","url_forum"));
            $data =  array('status' => 'error_auth', 'content' =>$data_config);
            $this->send_response($data);
        }
    }

    /**
     * Fonction test envoi de données
     */
    private function api_send_ogspy_ally_details(){

        if($this->authenticated_token != null){

            //$data_ally = new Statistics_Model();
            //$data_config
            $data =  array('status' => 'not implemented', 'content' => null);
            $this->send_response($data);
        }
    }

    /**
     * Fonction test envoi de données
     */
    private function api_send_ogspy_player_details(){

        if($this->authenticated_token != null){

            $data =  array('status' => 'not implemented', 'content' => null);
            $this->send_response($data);
        }
    }

    /**
     * Function to send the http response
     * @param $data
     */
    private function send_response($data){

        $answer_data = json_encode($data);

        $response = new HTTP\Response();
        $response->setStatus(201); // created !
        $response->setHeader('Content-type', 'application/json');
        $response->setBody($answer_data);

        HTTP\Sapi::sendResponse($response);
    }

    /**
     * Use the String Generator Lib to generate a token
     * @return string
     */
    private function generate_token(){

        return StringGenerator::randomAlnum(128);

    }
}