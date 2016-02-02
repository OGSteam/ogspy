<?php
/**
* This file used to send push notification requests to GCM server.
* @package OGSpy
* @subpackage Common
* @author Jedinight
* @copyright Copyright &copy; 2013, http://www.ogsteam.fr/
* @version 1.0.0
*/
class GCM {
 
    // Code here
    function __construct() {
 
    }

    /**
     * Sending Push Notification
     * @param $registatoin_ids
     * @param $message
     * @return mixed
     */
    function send_notification($registatoin_ids, $message) {
        // include config
        //include_once './config.php';
 
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send'; 
        $fields = array('registration_ids' => $registatoin_ids,'data' => $message); 
        $headers = array('Authorization: key=' . GOOGLE_API_KEY, 'Content-Type: application/json');
        
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
        return $result;
    }
}
?>