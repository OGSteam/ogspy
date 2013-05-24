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
	
	// Variable de sécurité OGSPY
	define('IN_SPYOGAME', true); // permet d'inclure les fichiers ogspy
	define('IN_REGISTERING_GCM', true);
	
	// Inclusion de fichiers
	require_once("../parameters/id.php");
	require_once("../includes/config.php");
	require_once("../includes/functions.php");	
	require_once("../includes/gcm/gcm.php");
	
	// Co à la bdd
	require_once("../includes/mysql.php");
	
	// attention penser a la supp 2 ouvertures faites
	//appel co sql natif
	$db = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);
	if (!$db->db_connect_id) {
		die("Impossible de se connecter à la base de données : '" . $db_host . "'");
	}
	// fin co ( $db est dispo dans tout ce qui va suivre ... )
		
	// Remplacement des fonctions centrales car on ne passe pas par index.php
		
	// Récuperation des variables $__get et $__post	
	// Récupération des valeur GET, POST, COOKIE
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
	
	// Astuce pour recupérer les données du serveur ( pas d appel bdd on va dans le cache ...)
	// Load cached config
	$filename = '../cache/cache_config.php';
	if (file_exists($filename)) {
		include $filename;
		// var_dump($server_config);
	}
		
	// pour user_data va falloir faire la requete toi meme puisque ca sera ton propre login .
	// je pense qu il y a moyen de passer par la session native d ogspy ...
	// mais bon ... :p ( regarde dans le common.php et include/user.php)
		
	/**
	 * Registering a user device
	 * Store reg id in users table
	 */
	if (isset($pub_name) && isset($pub_regId)) {
	    $gcm = new GCM();
	 
	    $res = storeGCMUser($pub_name, $pub_regId);

		echo "Success=" . $res;

	    $registatoin_ids = array($pub_regId);
	    $message = array("product" => "ok");
	 
	    $result = $gcm->send_notification($registatoin_ids, $message);
	 
	    return $result;
	} else {
	    die("User details missing for registration !");
	}
?>