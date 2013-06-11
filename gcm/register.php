<?php
/**
* This file receives requests from android device and stores the user in the database.
* @package OGSpy
* @subpackage Common
* @author Jedinight
* @copyright Copyright &copy; 2013, http://www.ogsteam.fr/
* @version 1.0.0
*/
	// Response json
	$json = array();
	
	// Variable de s�curit� OGSPY
	define('IN_SPYOGAME', true); // permet d'inclure les fichiers ogspy
	define('IN_REGISTERING_GCM', true);
	
	// Inclusion de fichiers
	require_once("../parameters/id.php");
	require_once("../includes/config.php");
	require_once("../includes/functions.php");	
	require_once("../includes/gcm/gcm.php");
	
	// Co � la bdd
	require_once("../includes/mysql.php");
	
	// attention penser a la supp 2 ouvertures faites
	//appel co sql natif
	$db = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);
	if (!$db->db_connect_id) {
		die("Impossible de se connecter � la base de donn�es : '" . $db_host . "'");
	}
	// fin co ( $db est dispo dans tout ce qui va suivre ... )
		
	// Remplacement des fonctions centrales car on ne passe pas par index.php
		
	// R�cuperation des variables $__get et $__post	
	// R�cup�ration des valeur GET, POST, COOKIE
	extract($_GET,EXTR_PREFIX_ALL , "pub");
	extract($_POST,EXTR_PREFIX_ALL , "pub");
	extract($_COOKIE,EXTR_PREFIX_ALL , "pub");
	
	foreach ($_GET as $secvalue) {
		if (!check_getvalue($secvalue)) {
			die("I don't like you " . $secvalue ." ...");
		}
	}
	
	foreach ($_POST as $secvalue) {
		if (!check_postvalue($secvalue)) {
			Header("Location: index.php");
			die();
		}
	}
	// Utilisation possible de $pub_XXX
	
	// Astuce pour recup�rer les donn�es du serveur ( pas d appel bdd on va dans le cache ...)
	// Load cached config
	$filename = '../cache/cache_config.php';
	if (file_exists($filename)) {
		include $filename;
		// var_dump($server_config);
	}
		
	// pour user_data va falloir faire la requete toi meme puisque ca sera ton propre login .
	// je pense qu il y a moyen de passer par la session native d ogspy ...
	// mais bon ... :p ( regarde dans le common.php et include/user.php)
		
	function writeLog($texte){
		$filename = 'log.txt';

		// Assurons nous que le fichier est accessible en �criture
		if (is_writable($filename)) {
		    // Dans notre exemple, nous ouvrons le fichier $filename en mode d'ajout
		    // Le pointeur de fichier est plac� � la fin du fichier
		    // c'est l� que le texte sera plac�
		    if (!$handle = fopen($filename, 'a')) {
		         //echo "Impossible d'ouvrir le fichier ($filename)";
		         exit;
		    }
		    // Ecrivons quelque chose dans notre fichier.
		    if (fwrite($handle, $texte."\n") === FALSE) {
		        //echo "Impossible d'�crire dans le fichier ($filename)";
		        exit;
		    }
		    //echo "L'�criture de ($texte) dans le fichier ($filename) a r�ussi";		
		    fclose($handle);		
		} else {
		    //echo "Le fichier $filename n'est pas accessible en �criture.";
		}
	}
		
		
	/**
	 * Registering a user device
	 * Store reg id in users table
	 */
	if(isset($pub_unregister) && isset($pub_regId)){
		$gcm = new GCM();
		writeLog("Try to unregister User ($pub_name) | regId ($pub_regId)");
		$res = deleteGCMUser($pub_regId);
		writeLog("Successfull unregistering User ($pub_name) | regId ($pub_regId)");
		echo $res;
	} else {
		if (isset($pub_name) && isset($pub_regId)) {
			writeLog("Try to register User ($pub_name) | regId ($pub_regId)");
		    $gcm = new GCM();		 
		    $res = storeGCMUser($pub_name, $pub_regId);
	
			//echo "Resultat=" . $res;
			
			if ($res == 1){
				writeLog("Success !");
					
				$registatoin_ids = array($pub_regId);
				//$message = array("product" => "ok");
				$message = array("message" => "You are register on your OGSPY server !");
				
				$result = $gcm->send_notification($registatoin_ids, $message);
					
				//echo $result;
				writeLog("Notif envoy�e : " . $result);
				echo $result;
			} else if($res == 1) {
				writeLog("Echec !");
			} else if($res == -1) {
				writeLog("D�j� enregistr� !");
			}
			
		} else {
			writeLog("User details missing for registration !");
		    die("User details missing for registration !");
		}
	}
	
?>