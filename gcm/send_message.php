<?php
/**
* This file used to send pushnotification to android device by making a request to GCM server.
* @package OGSpy
* @subpackage Common
* @author Jedinight
* @copyright Copyright &copy; 2013, http://www.ogsteam.fr/
* @version 1.0.0
*/
define('IN_SPYOGAME', true); // permet d'inclure les fichiers ogspy
define('IN_REGISTERING_GCM', true);

$regId = $_GET["regId"];
$message = $_GET["message"];

if(!isset($regId)) {
	$regId = $_POST["regId"];
}
if(!isset($message)) {
	$message = $_POST["message"];
}

if (isset($regId) && isset($message)) {    
	// Inclusion de fichiers
	require_once("../parameters/id.php");
	require_once("../includes/config.php");
	require_once("../includes/functions.php");	
	require_once("../includes/gcm/gcm.php");
	
    $gcm = new GCM();
 
    $registatoin_ids = array($regId);
    $message = array("message" => $message);
 
    $result = $gcm->send_notification($registatoin_ids, $message);
 
    echo $result;
 
    return $result;
} else {
	
}
?>