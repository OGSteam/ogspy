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
<link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
</head>
<body>

<?php
}

// on réinitialise la sequense config
// evite d utiliser le cache ( qui sera périmé ))
$request = "select * from " . TABLE_CONFIG;
$result = mysql_query($request);
 while (list($name, $value) = mysql_fetch_row($result)) {
        $server_config[$name] = stripslashes($value);
    }
    

$request = "SELECT config_value FROM ".TABLE_CONFIG." WHERE config_name = 'version'";
$result = $db->sql_query($request);
list($ogsversion) = $db->sql_fetch_row($result);

$requests = array();
$up_to_date = false;
switch ($ogsversion) {
	case '3.06':
		$requests[] = "ALTER TABLE ".TABLE_USER_TECHNOLOGY." CHANGE Expeditions Astrophysique SMALLINT(2) NOT NULL default '0'";
		$requests[] = "ALTER TABLE ".TABLE_PARSEDSPY." CHANGE Expeditions Astrophysique SMALLINT(2) NOT NULL default '0'";
		$requests[] = "ALTER TABLE ".TABLE_MOD." MODIFY version VARCHAR(10)";
		$requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.0.7' WHERE config_name = 'version'";
		$requests[] = "INSERT IGNORE INTO ".TABLE_CONFIG." (config_name, config_value) VALUES ('astro_strict','1')";
        $requests[] = "UPDATE ".TABLE_USER_BUILDING." SET planet_id = (planet_id + 100) WHERE planet_id < 10";
        $requests[] = "UPDATE ".TABLE_USER_BUILDING." SET planet_id = (planet_id + 191) WHERE planet_id > 9 and planet_id < 19 ";
        $requests[] = "UPDATE ".TABLE_USER_DEFENCE." SET planet_id = (planet_id + 100) WHERE planet_id < 10";
        $requests[] = "UPDATE ".TABLE_USER_DEFENCE." SET planet_id = (planet_id + 191) WHERE planet_id > 9 and planet_id < 19 ";
        $ogsversion = '3.0.7';
		$up_to_date = true;
		break;
		
	case '3.0.7':
		$requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.0.8' WHERE config_name = 'version'";
		$requests[] = "INSERT IGNORE INTO ".TABLE_CONFIG." (config_name, config_value) VALUES ('config_cache', '3600')";
		$requests[] = "INSERT IGNORE INTO ".TABLE_CONFIG." (config_name, config_value) VALUES ('mod_cache', '604800')";
        $ogsversion = '3.0.8';
		$up_to_date = true;
		break;
		
	case '3.0.8':
        // modif building
	   $requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.1.0' WHERE config_name = 'version'";
       $requests[] = "ALTER TABLE `".TABLE_USER_BUILDING."` ADD `CD` SMALLINT(2) NOT NULL default '0' AFTER `HD`"; // cache deut
	   $requests[] = "ALTER TABLE `".TABLE_USER_BUILDING."` ADD `CC` SMALLINT(2) NOT NULL default '0' AFTER `HD`"; // cache cristal
	   $requests[] = "ALTER TABLE `".TABLE_USER_BUILDING."` ADD `CM` SMALLINT(2) NOT NULL default '0' AFTER `HD`"; // cache metal
	   $requests[] = "ALTER TABLE `".TABLE_PARSEDSPY."` ADD `CD` SMALLINT(2) NOT NULL default '-1' AFTER `HD`"; // cache deut
	   $requests[] = "ALTER TABLE `".TABLE_PARSEDSPY."` ADD `CC` SMALLINT(2) NOT NULL default '-1' AFTER `HD`"; // cache cristal
	   $requests[] = "ALTER TABLE `".TABLE_PARSEDSPY."` ADD `CM` SMALLINT(2) NOT NULL default '-1' AFTER `HD`"; // cache metal
        // fin modif building
        
        // ajout classement alliance //
        // economique
        $requests[] = "CREATE TABLE ".TABLE_RANK_ALLY_ECO." (".
	        " datadate int(11) NOT NULL default '0',".
	        " rank int(11) NOT NULL default '0',".
	        " ally varchar(30) NOT NULL,".
        	" number_member int(11) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,ally),".
        	" KEY ally (ally)".
        	" )";
        
      // recherche
        $requests[] = "CREATE TABLE ".TABLE_RANK_ALLY_TECHNOLOGY." (".
	         " datadate int(11) NOT NULL default '0',".
	         " rank int(11) NOT NULL default '0',".
	        " ally varchar(30) NOT NULL,".
        	" number_member int(11) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
         	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,ally),".
        	" KEY ally (ally)".
        	" )";
        
     // militaire
        $requests[] = "CREATE TABLE ".TABLE_RANK_ALLY_MILITARY." (".
	         " datadate int(11) NOT NULL default '0',".
	         " rank int(11) NOT NULL default '0',".
	        " ally varchar(30) NOT NULL,".
        	" number_member int(11) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,ally),".
        	" KEY ally (ally)".
        	" )";
        // militaire construit
        $requests[] = "CREATE TABLE ".TABLE_RANK_ALLY_MILITARY_BUILT." (".
        	         " datadate int(11) NOT NULL default '0',".
        	         " rank int(11) NOT NULL default '0',".
        	        " ally varchar(30) NOT NULL,".
                	" number_member int(11) NOT NULL,".
                	" points int(11) NOT NULL default '0',".
                	" sender_id int(11) NOT NULL default '0',".
                	" PRIMARY KEY  (rank,datadate),".
                	" KEY datadate (datadate,ally),".
                	" KEY ally (ally)".
                	" )";
    
     // militaire perdu
        $requests[] = "CREATE TABLE ".TABLE_RANK_ALLY_MILITARY_LOOSE." (".
	         " datadate int(11) NOT NULL default '0',".
	         " rank int(11) NOT NULL default '0',".
	        " ally varchar(30) NOT NULL,".
        	" number_member int(11) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,ally),".
        	" KEY ally (ally)".
        	" )";
        
     // militaire detruit
        $requests[] = "CREATE TABLE ".TABLE_RANK_ALLY_MILITARY_DESTRUCT." (".
	         " datadate int(11) NOT NULL default '0',".
	         " rank int(11) NOT NULL default '0',".
	        " ally varchar(30) NOT NULL,".
        	" number_member int(11) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,ally),".
        	" KEY ally (ally)".
        	" )";
        
    // point honneur
        $requests[] = "CREATE TABLE ".TABLE_RANK_ALLY_HONOR." (".
	         " datadate int(11) NOT NULL default '0',".
	         " rank int(11) NOT NULL default '0',".
	        " ally varchar(30) NOT NULL,".
        	" number_member int(11) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,ally),".
        	" KEY ally (ally)".
        	" )";
        
    // fin classement alliance
    
   /// classement joueur
            // economique
        $requests[] = "CREATE TABLE ".TABLE_RANK_PLAYER_ECO." (".
	        " datadate int(11) NOT NULL default '0',".
	        " rank int(11) NOT NULL default '0',".
	        " player varchar(30) NOT NULL,".
        	" ally varchar(100) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,player),".
        	" KEY player (player)".
        	" )";
   
   
   // technologie
        $requests[] = "CREATE TABLE ".TABLE_RANK_PLAYER_TECHNOLOGY." (".
	        " datadate int(11) NOT NULL default '0',".
	        " rank int(11) NOT NULL default '0',".
	        " player varchar(30) NOT NULL,".
        	" ally varchar(100) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,player),".
        	" KEY player (player)".
        	" )";
   
   
// militaire
        $requests[] = "CREATE TABLE ".TABLE_RANK_PLAYER_MILITARY." (".
	        " datadate int(11) NOT NULL default '0',".
	        " rank int(11) NOT NULL default '0',".
	        " player varchar(30) NOT NULL,".
        	" ally varchar(100) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".        	
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,player),".
        	" KEY player (player)".
        	" )";
        
// militaire constuit
        $requests[] = "CREATE TABLE ".TABLE_RANK_PLAYER_MILITARY_BUILT." (".
        	        " datadate int(11) NOT NULL default '0',".
        	        " rank int(11) NOT NULL default '0',".
        	        " player varchar(30) NOT NULL,".
                	" ally varchar(100) NOT NULL,".
                	" points int(11) NOT NULL default '0',".
                	" sender_id int(11) NOT NULL default '0',".
                	" PRIMARY KEY  (rank,datadate),".
                	" KEY datadate (datadate,player),".
                	" KEY player (player)".
                	" )";
        
   
// militaire perdu
        $requests[] = "CREATE TABLE ".TABLE_RANK_PLAYER_MILITARY_LOOSE." (".
	        " datadate int(11) NOT NULL default '0',".
	        " rank int(11) NOT NULL default '0',".
	        " player varchar(30) NOT NULL,".
        	" ally varchar(100) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,player),".
        	" KEY player (player)".
        	" )";
   
   // militaire detruit
        $requests[] = "CREATE TABLE ".TABLE_RANK_PLAYER_MILITARY_DESTRUCT." (".
	        " datadate int(11) NOT NULL default '0',".
	        " rank int(11) NOT NULL default '0',".
	        " player varchar(30) NOT NULL,".
        	" ally varchar(100) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,player),".
        	" KEY player (player)".
        	" )";
      

    // militaire honneur
        $requests[] = "CREATE TABLE ".TABLE_RANK_PLAYER_HONOR." (".
	        " datadate int(11) NOT NULL default '0',".
	        " rank int(11) NOT NULL default '0',".
	        " player varchar(30) NOT NULL,".
        	" ally varchar(100) NOT NULL,".
        	" points int(11) NOT NULL default '0',".
        	" sender_id int(11) NOT NULL default '0',".
        	" PRIMARY KEY  (rank,datadate),".
        	" KEY datadate (datadate,player),".
        	" KEY player (player)".
        	" )";

    
        $ogsversion = '3.1.0';
		$up_to_date = true;
		break;
		
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
	case '3.1.2':
		$requests[] = "ALTER TABLE `".TABLE_USER_BUILDING."` MODIFY `coordinates` VARCHAR(10)";
		$requests[] = "ALTER TABLE `".TABLE_UNIVERSE."` MODIFY `phalanx` tinyint(1) NOT NULL default '0'";
		$requests[] = "ALTER TABLE `".TABLE_USER."` MODIFY `xtense_type` enum('FF','GM-FF','GM-GC','GM-OP','ANDROID')";
		$requests[] = "ALTER TABLE `".TABLE_USER."` ADD `user_email` VARCHAR(50) NOT NULL default '' AFTER `user_password`";
		$requests[] = "ALTER TABLE `".TABLE_USER."` ADD `off_commandant` enum('0','1') NOT NULL default '0', AFTER `disable_ip_check`";
		$requests[] = "UPDATE ".TABLE_CONFIG." SET config_value = '3.1.3' WHERE config_name = 'version'";
		$ogsversion = '3.1.3';
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
	<h3 align='center'><font color='yellow'>Mise à jour du serveur OGSpy vers la version <?php echo $ogsversion;?> effectuée avec succès</font></h3>
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
