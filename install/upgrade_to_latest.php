<?php
/**
* Mise à jour d'OGSpy : update_to_latest.php
* @package OGSpy
* @subpackage install
* @created 28/11/2005
* @modified 30/09/2007
* @version 3.0.7
*/

define("IN_SPYOGAME", true);
define("UPGRADE_IN_PROGRESS", true);

require_once("../common.php");

if(!isset($pub_verbose)) $pub_verbose = true;


if($pub_verbose == true){
?>

<html>
<head>
<title>Mise à jour OGSpy</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="fr" />
<link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
</head>
<body>

<?php
}

// on réinitialise la sequense config
// evite d utiliser le cache ( qui sera périmé ))
$request = "select * from " . TABLE_CONFIG;
$result = $db->sql_query($request);
 while (list($name, $value) = $db->sql_fetch_row($result)) {
        $server_config[$name] = stripslashes($value);
    }
    

$request = "SELECT config_value FROM ".TABLE_CONFIG." WHERE config_name = 'version'";
$result = $db->sql_query($request);
list($ogsversion) = $db->sql_fetch_row($result);

$requests = array();
$up_to_date = false;
switch ($ogsversion) {
	case '3.1.0':
		$requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.1.1' WHERE config_name = 'version'";
        // MODIF TABLE_USER
        $requests[] = "ALTER TABLE `".TABLE_USER."` ADD `xtense_type` enum('FF','GM-FF','GM-GC','GM-OP') AFTER `rank_added_ogs`"; // Type de barre utilisée par le user
		$requests[] = "ALTER TABLE `".TABLE_USER."` ADD `xtense_version` VARCHAR(10) AFTER `xtense_type`"; // Type de barre utilisée par le user
		
		// MODIF TABLE_RANK_PLAYER_MILITARY
		$requests[] = "ALTER TABLE `".TABLE_RANK_PLAYER_MILITARY."` ADD `nb_spacecraft` int(11) NOT NULL default '0' AFTER `sender_id`"; // Ajout nombre de vaisseaux au classement militaire joueur
		                                          
		// SUPPRESSIONS ANCIENS CLASSEMENTS : TABLE_RANK_PLAYER_FLEET, TABLE_RANK_PLAYER_RESEARCH, TABLE_RANK_ALLY_FLEET & TABLE_RANK_ALLY_RESEARCH
		$requests[] = "DROP TABLE `".TABLE_RANK_PLAYER_FLEET."`"; 	// ancien classement flotte
		$requests[] = "DROP TABLE `".TABLE_RANK_PLAYER_RESEARCH."`";// ancien classement recherche
		$requests[] = "DROP TABLE `".TABLE_RANK_ALLY_FLEET."`";		// ancien classement flotte
		$requests[] = "DROP TABLE `".TABLE_RANK_ALLY_RESEARCH."`";	// ancien classement recherche
		$requests[] = "DROP TABLE `".TABLE_SPY."`";					// ancienne table des RE
		$requests[] = "DROP TABLE `".TABLE_UNIVERSE_TEMPORARY."`";	// ancienne table temporaire univers		
		
		$ogsversion = '3.1.1';
		$up_to_date = true;
		//Pas de break pour faire toutes les mises à jour d'un coup !
	case '3.1.1':
		$requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.1.2' WHERE config_name = 'version'";
		$ogsversion = '3.1.2';
		$up_to_date = true;
		//Pas de break pour faire toutes les mises à jour d'un coup !
	case '3.1.2':
		$requests[] = "ALTER TABLE `".TABLE_USER_BUILDING."` MODIFY `coordinates` VARCHAR(10)";
		$requests[] = "ALTER TABLE `".TABLE_UNIVERSE."` MODIFY `phalanx` tinyint(1) NOT NULL default '0'";
		$requests[] = "ALTER TABLE `".TABLE_USER."` MODIFY `xtense_type` enum('FF','GM-FF','GM-GC','GM-OP','ANDROID')";
		$requests[] = "ALTER TABLE `".TABLE_USER."` ADD `user_email` VARCHAR(50) NOT NULL default '' AFTER `user_password`";
		$requests[] = "ALTER TABLE `".TABLE_USER."` ADD `off_commandant` enum('0','1') NOT NULL default '0' AFTER `disable_ip_check`";
		$requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.1.3' WHERE config_name = 'version'";
		$ogsversion = '3.1.3';
		$up_to_date = true;
	case '3.1.3':
		$requests[] = "CREATE TABLE IF NOT EXISTS `".TABLE_GCM_USERS."` ( ".
  					  "`user_id` int(11) NOT NULL default '0',".
  					  "`gcm_regid` varchar(255) NOT NULL, ".
  					  "`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, ".
					  "`version_android` varchar(50), ".
		  			  "`version_ogspy` varchar(50), ".
					  "`device` varchar(50), ".
  					  "PRIMARY KEY (`gcm_regid`) ".
  					  ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";					  
					  
		//Passage des tables en UTF-8
		$requests[] = "ALTER TABLE ".TABLE_CONFIG." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_GROUP." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_SESSIONS." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_STATISTIC." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_UNIVERSE." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_USER." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_USER_BUILDING." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_USER_DEFENCE." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_USER_FAVORITE." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_USER_GROUP." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_USER_SPY." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_USER_TECHNOLOGY." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_MOD." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_MOD_CFG." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_PARSEDSPY." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_PARSEDRC." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_PARSEDRCROUND." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_ROUND_ATTACK." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_ROUND_DEFENSE." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_POINTS." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_ECO." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_TECHNOLOGY." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_MILITARY." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_MILITARY_BUILT." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_MILITARY_LOOSE." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_MILITARY_DESTRUCT." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_PLAYER_HONOR." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_POINTS." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_ECO." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_TECHNOLOGY." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_MILITARY." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_MILITARY_BUILT." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_MILITARY_LOOSE." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_MILITARY_DESTRUCT." CONVERT TO CHARACTER SET utf8";
		$requests[] = "ALTER TABLE ".TABLE_RANK_ALLY_HONOR." CONVERT TO CHARACTER SET utf8";
        
        $requests[] = "INSERT INTO ".TABLE_CONFIG." (config_name, config_value) VALUES ('uni_arrondi_galaxy','0')";
        $requests[] = "INSERT INTO ".TABLE_CONFIG." (config_name, config_value) VALUES ('uni_arrondi_system','0')";
        $requests[] = "ALTER TABLE ".TABLE_USER_BUILDING." ADD `boosters` VARCHAR(64) NOT NULL default 'm:0:0_c:0:0_d:0:0_p:0_m:0' AFTER `fields`";
        $requests[] = "ALTER TABLE ".TABLE_PARSEDRC." MODIFY `pertes_A` BIGINT";
        $requests[] = "ALTER TABLE ".TABLE_PARSEDRC." MODIFY `pertes_D` BIGINT";
        $requests[] = "ALTER TABLE ".TABLE_PARSEDRC." MODIFY `gain_M` BIGINT";
        $requests[] = "ALTER TABLE ".TABLE_PARSEDRC." MODIFY `gain_C` BIGINT";
        $requests[] = "ALTER TABLE ".TABLE_PARSEDRC." MODIFY `gain_D` BIGINT";
        $requests[] = "ALTER TABLE ".TABLE_PARSEDRC." MODIFY `debris_M` BIGINT";
        $requests[] = "ALTER TABLE ".TABLE_PARSEDRC." MODIFY `debris_C` BIGINT";
		        
		$requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.2.0' WHERE config_name = 'version'";
		$ogsversion = '3.2.0';
		$up_to_date = true;
		break;
	default:
	die("Aucune mise … jour n'est disponible");
}


foreach ($requests as $request) {
	$db->sql_query($request);
}

if ( $ogsversion == '3.1.0' && function_exists ( 'import_RE' ) ) {
    import_RE(); 
    }
  
// on supprime tous les fichiers du cache
// pour prendre en compte toutes les modifications
$files = glob('../cache/*.php');
if (count($files) > 0) {
	foreach ($files as $filename){unlink($filename);} 
}
  
?>
	<h3 align='center'><span style="color: yellow; ">Mise à jour du serveur OGSpy vers la version <?php echo $ogsversion;?> effectuée avec succès</span></h3>
	<center>
	<br />
<?php
if($pub_verbose == true){
if ($up_to_date) {
	echo "\t"."<b><i>Pensez à supprimer le dossier 'install'</i></b><br />"."\n";
	echo "\t"."<br /><a href='../index.php'>Retour</a>"."\n";
}
else {
	echo "\t"."<br><font color='orange'><b>Cette version n'est pas la dernière en date, veuillez réexécuter le script</font><br />"."\n";
	echo "\t"."<a href=''>Recommencer l'opération</a>"."\n";
}
?>
	</center>
</body>
</html>
<?php } ?>
