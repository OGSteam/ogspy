<?php
/**
 * Created by IntelliJ IDEA.
 * User: Anthony
 * Date: 24/09/2016
 * Time: 19:50
 */

namespace Ogsteam\Ogspy\Api;
use Ogsteam\Ogspy\Model\Tokens_Model;
use Ogsteam\Ogspy\Model\User_Model;
use Sinergi\Token\StringGenerator;
use Sabre\HTTP;


class Api_data
{
    private $authenticated_token = false;
    private $user_id_token = false;

    public function authenticate_by_user($login,$password)
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
                $this->send_response(json_encode(array('api_token' => $private_token)));
            }

        }
    }

    public function authenticate_by_token($token){

        $data_token = new Tokens_Model();
        $user_id = $data_token->get_userid_from_token($token);
        if($user_id !== false){
            $this->authenticated_token = $token;
            $this->user_id_token = $user_id;
            return true;
        }else
            return false;

    }


    public function api_treat_command($data){

        $data_decoded = json_decode($data);

        //Cas envoyer liste paramètres utilisateurs.
        $this->api_send_user_list();

    }

    private function api_send_user_list(){

        if($this->authenticated_token != null){

            $data_user = new User_Model();
            $data = $data_user->select_all_user_data();
            $this->send_response($data);
        }


    }

    private function send_response($data){

        $answer_data = json_encode($data);

        $response = new HTTP\Response();
        $response->setStatus(201); // created !
        $response->setHeader('Content-type', 'application/json');
        $response->setBody($answer_data);

        HTTP\Sapi::sendResponse($response);
    }

    private function generate_token(){

        return StringGenerator::randomAlnum(128);

    }



}