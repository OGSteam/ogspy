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

function writeLog($texte){
	$filename = 'log_message.txt';
	$date = date("d-m-Y");
	$heure = date("G:i");

	// Assurons nous que le fichier est accessible en écriture
	if (is_writable($filename)) {
		// Dans notre exemple, nous ouvrons le fichier $filename en mode d'ajout
		// Le pointeur de fichier est placé à la fin du fichier
		// c'est là que le texte sera placé
		if (!$handle = fopen($filename, 'a')) {
			//echo "Impossible d'ouvrir le fichier ($filename)";
			exit;
		}
		// Ecrivons quelque chose dans notre fichier.
		if (fwrite($handle, $date ." - " . $heure . " : " . $texte . "\n") === FALSE) {
			//echo "Impossible d'écrire dans le fichier ($filename)";
			exit;
		}
		//echo "L'écriture de ($texte) dans le fichier ($filename) a réussi";
		fclose($handle);
	} else {
		//echo "Le fichier $filename n'est pas accessible en écriture.";
	}
}

if (isset($regId) && isset($message)) {    
	// Inclusion de fichiers
	require_once("../parameters/id.php");
	require_once("../includes/config.php");
	require_once("../includes/functions.php");	
	require_once("../includes/gcm/gcm.php");
	
    $gcm = new GCM();
 
    $registatoin_ids = array($regId);
    $messageArray = array("message" => $message, "sender" => "Admin OGSpy", "messagetype" => "message");
 
    $result = $gcm->send_notification($registatoin_ids, $messageArray);
 
    writeLog($result);
    echo $result; 
    return $result;
} else {
	
}
?>