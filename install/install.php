<?php
/**
 * Fichier d'installation d'ogspy : ROOT/install/install.php 
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @version 3.04
 * @since 3.04 - 26 sept. 07
 */
?>
<html>
<head>
<title>Installation OGSpy</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="fr" />
<link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
</head>
<body>

<?php
define("IN_SPYOGAME", true);
define("INSTALL_IN_PROGRESS", true);

@chmod("../parameters",0777);
@chmod("../journal",0777);
@chmod("../mod",0777);
@chmod("../mod/autoupdate/tmp",0777);

if(!(version_compare(PHP_VERSION, "5.0.0") >= 0)){
	echo "<br /><br />";
	echo "<table align='center'><tr><th colspan ='2'><font color='red'>Installation impossible :</font></th><tr/>";
	echo "<tr><td colspan='2'>Pour pouvoir effectuer une installation complète d'OGSpy,";
	echo "<br/>votre hébergement doit être doté au minimum de la version 5 de PHP !";
	echo "<br/><br/>Vous disposez actuellement de la version : " . PHP_VERSION;
	echo "<tr align='center'><td colspan='2'><a href='install.php'>Rafraichir</a></td></tr>";
	echo "</table>";
	exit();	
}

/**
* Affiche une boite d'erreur de permission
*/
$error = "";
$alerte = FALSE;
if (is_writable("../parameters")) {
	$error .= "<tr><td width=\"250\">- \"parameters\" : </td><td><font color='green'>Ecriture autorisé</font></td></tr>";
} else {
	$error .= "<tr><td width=\"250\">- \"parameters\" : </td><td><font color='red'>Ecriture impossible</font></td></tr>";
	$alerte = TRUE;
}

if (is_writable("../journal")) {
	$error .= "<tr><td width=\"250\">- \"journal\" : </td><td><font color='green'>Ecriture autorisé</font></td></tr>";
} else {
	$error .= "<tr><td width=\"250\">- \"journal\" : </td><td><font color='red'>Ecriture impossible</font></td></tr>";
	$alerte = TRUE;
}

$error2 = "";
if (is_writable("../mod")) {
	$error2 .= "<tr><td width=\"250\">- \"mod\" : </td><td><font color='green'>Ecriture autorisé</font></td></tr>";
} else {
	$error2 .= "<tr><td width=\"250\">- \"mod\" : </td><td><font color='red'>Ecriture impossible</font></td></tr>";
}


if ($alerte) {
	echo "<br /><br />";
	echo "<table align='center'><tr><th colspan ='2'><font color='red'>Installation impossible :</font></th><tr/>";
	echo "<tr><td colspan='2'>Pour le bon fonctionnement et une installation complète de OGSpy,<br />vous devez";
	echo " autoriser ces dossiers en écriture";
	echo $error;
	echo "<tr><th colspan='2'><font color='red'>Erreur optionelle :</font></th><tr/>";
	echo "<tr><td colspan='2'>Ces dossiers servent à l'installation et à la mise à jour des modules OGSpy.<br>";
	echo "<font color='red'><b>Leurs dossiers et fichiers doivent être accessibles en écriture.</b></font>";
	echo $error2;
	echo "<tr align='center'><td colspan='2'><a href='install.php'>Rafraichir</a></td></tr>";
	echo "</table>";
	exit();
}

require_once("../common.php");
require_once("version.php");

/**
* Affiche une boite d'erreur d'installation et quitte le script
* @var string $message Message d'erreur
*/
function error_sql($message) {
	echo "<h3 align='center'><font color='red'>Erreur durant la procédure d'installation du serveur OGSpy</font></h3>";
	echo "<center><b>- ".$message."</b></center>";
	exit();
}

/**
* Création de la structure de la base de donnée
* @var string $sgbd_server Serveur MySql (localhost)
* @var string $sgbd_username Utilisateur Base de donnée
* @var string $sgbd_password Mot de passe Base de donnée
* @var string $sgbd_tableprefix Préfixe à utiliser pour les tables ogspy
* @var string $admin_username Nom de l'Administrateur OGSpy
* @var string $admin_password Mot de passe Administrateur OGSpy
* @var string $admin_password2 Confirmation du Mot de passe Administrateur OGSpy
* @var int $num_of_galaxies Nombre de galaxies dans l'univers OGame de cet OGSpy
* @var int $num_of_systems Nombre de systèmes dans l'univers OGame de cet OGSpy
*/
function installation_db($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $admin_username, $admin_password, $admin_password2, $num_of_galaxies, $num_of_systems) {
	global $pub_directory;
	$db  = sql_db::getInstance($sgbd_server, $sgbd_username, $sgbd_password, $sgbd_dbname);
	if (!$db->db_connect_id) error_sql("Impossible de se connecter à la base de données");

    $db->sql_query("ALTER DATABASE ".$sgbd_dbname." charset=utf8");
    
    //Création de la structure de la base de données
	$sql_query = @fread(@fopen("schemas/ogspy_structure.sql", 'r'), @filesize("schemas/ogspy_structure.sql")) or die("<h1>Le script sql d'installation est introuvable</h1>");

	$sql_query = preg_replace("#ogspy_#", $sgbd_tableprefix, $sql_query);
	
	//Création de l'énumération des galaxies:
	$galaxies_db_str = 'galaxy enum(';
	for($i=1 ; $i<$num_of_galaxies ; $i++)
		$galaxies_db_str .= "'$i' , ";
	$galaxies_db_str .= "'$num_of_galaxies') NOT NULL default '1',";
	$sql_query = preg_replace("#GALAXY_ENUM#",  $galaxies_db_str, $sql_query);

	$sql_query = explode(";", $sql_query);
	$sql_query[] = "INSERT INTO ".$sgbd_tableprefix."config (config_name, config_value) VALUES ('num_of_galaxies','$num_of_galaxies')";
	$sql_query[] = "INSERT INTO ".$sgbd_tableprefix."config (config_name, config_value) VALUES ('num_of_systems','$num_of_systems')";
	$sql_query[] = "INSERT INTO ".$sgbd_tableprefix."config (config_name, config_value) VALUES ('speed_uni','1')";
	$sql_query[] = "INSERT INTO ".$sgbd_tableprefix."config (config_name, config_value) VALUES ('ddr','false')";
    $sql_query[] = "INSERT INTO ".$sgbd_tableprefix."config (config_name, config_value) VALUES ('astro_strict','1')";
    $sql_query[] = "INSERT INTO ".$sgbd_tableprefix."config (config_name, config_value) VALUES ('uni_arrondi_galaxy','0')";
    $sql_query[] = "INSERT INTO ".$sgbd_tableprefix."config (config_name, config_value) VALUES ('uni_arrondi_system','0')";

	foreach ($sql_query as $request) {
		if (trim($request) != "") {
			if (!($result = $db->sql_query($request, false, false))) {
				$error = $db->sql_error($result);
				print $request;
				error_sql($error['message']);
			}
		}
	}

	$request = "insert into ".$sgbd_tableprefix."user (user_id, user_name, user_password, user_regdate, user_active, user_admin)".
	" values (1, '".mysqli_real_escape_string($db->db_connect_id, $admin_username)."', '".md5(sha1($admin_password))."', ".time().", '1', '1')";
	if (!($result = $db->sql_query($request, false, false))) {
		$error = $db->sql_error($result);
		print $request;
		error_sql($error['message']);
	}

	$request = "insert into ".$sgbd_tableprefix."user_group (group_id, user_id) values (1, 1)";
	if (!($result = $db->sql_query($request, false, false))) {
		$error = $db->sql_error($result);
		print $request;
		error_sql($error['message']);
	}
	
	// Ajout du mod_Xtense et du mod AutoUpdate
	define ( 'TABLE_MOD', $sgbd_tableprefix . 'mod' );
	define ( 'TABLE_MOD_CFG', $sgbd_tableprefix . 'mod_config' );
	define ( 'TABLE_MOD_CONFIG', $sgbd_tableprefix . 'mod_config' );
	define ( 'TABLE_CONFIG', $sgbd_tableprefix . 'config' );
		
	generate_id($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $num_of_galaxies, $num_of_systems);

}

/**
* Création du fichier de configuration id.php et quitte le script
* @var string $sgbd_server Serveur MySql (localhost)
* @var string $sgbd_username Utilisateur Base de donnée
* @var string $sgbd_password Mot de passe Base de donnée
* @var string $sgbd_tableprefix Préfixe à utiliser pour les tables ogspy
* @var int $num_of_galaxies Nombre de galaxies dans l'univers OGame de cet OGSpy
* @var int $num_of_systems Nombre de systèmes dans l'univers OGame de cet OGSpy
*/
function generate_id($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $sgbd_num_of_galaxies, $sgbd_num_of_systems) {
	$id_php[] = '<?php';
	$id_php[] = '/***************************************************************************';
	$id_php[] = '*	filename	: id.php';
	$id_php[] = '*	generated	: '.date("d/M/Y H:i:s");
	$id_php[] = '***************************************************************************/';
	$id_php[] = '';
	$id_php[] = 'if (!defined("IN_SPYOGAME")) die("Hacking attempt");';
	$id_php[] = '';
	$id_php[] = '$table_prefix = "'.$sgbd_tableprefix.'";';
	$id_php[] = '';
	$id_php[] = '//Paramètres de connexion à la base de données';
	$id_php[] = '$db_host = "'.$sgbd_server.'";';
	$id_php[] = '$db_user = "'.$sgbd_username.'";';
	$id_php[] = '$db_password = "'.$sgbd_password.'";';
	$id_php[] = '$db_database = "'.$sgbd_dbname.'";';
	$id_php[] = '';
	$id_php[] = 'define("OGSPY_INSTALLED", TRUE);';
	$id_php[] = '?>';
	if (!write_file("../parameters/id.php", "w", $id_php)) {
		die("Echec installation, impossible de générer le fichier 'parameters/id.php'");
	}

	echo "<h3 align='center'><font color='yellow'>Installation du serveur OGSpy effectuée avec succès !</font></h3>";
	echo "<center>";
	echo "<b>Pensez à supprimer le dossier 'install'</b><br />";
	echo "<a href='../index.php'>Retour</a>";
	echo "</center>";
	exit();
}

if (isset($pub_sgbd_server) && isset($pub_sgbd_dbname) && isset($pub_sgbd_username) && isset($pub_sgbd_password) && isset($pub_sgbd_tableprefix) &&
isset($pub_admin_username) && isset($pub_admin_password) && isset($pub_admin_password2) && isset($pub_num_of_galaxies) && isset($pub_num_of_systems)) {

	if (isset($pub_complete)) {
		if (!empty($pub_sgbd_tableprefix) && !check_var($pub_sgbd_tableprefix , "Pseudo_Groupname", "", true) ) {
			$pub_error = "Des caractères utilisés pour le préfixe de la base de donnée sont incorrect.";
		}
		elseif (!check_var($pub_admin_username, "Pseudo_Groupname", "", true) || !check_var($pub_admin_password, "Password", "", true)) {
			$pub_error = "Des caractères utilisés pour le nom d'utilisateur ou le mot de passe ne sont pas corrects";
		}
		elseif (!check_var($pub_num_of_galaxies, "Galaxy","", true) || !check_var($pub_num_of_systems, "Galaxy","", true)){
			$pub_error = "Vous n'avez pas rentrez des valeurs correcte pour le nombres de galaxies et (ou) de systemes";
		}
		else {
			if ($pub_sgbd_server != "" && $pub_sgbd_dbname != "" && $pub_sgbd_username != "" && $pub_admin_username != "" && $pub_admin_password != "" && $pub_admin_password == $pub_admin_password2) {
				installation_db($pub_sgbd_server, $pub_sgbd_dbname, $pub_sgbd_username, $pub_sgbd_password, $pub_sgbd_tableprefix, $pub_admin_username, $pub_admin_password, $pub_admin_password2, $pub_num_of_galaxies, $pub_num_of_systems);
			}
			else {
				$pub_error = "Saisissez correctement les champs de connexion à la base de données et du compte administrateur";
			}
		}
	}
	elseif (isset($pub_file)) {
		if ($pub_sgbd_server != "" && $pub_sgbd_dbname != "" && $pub_sgbd_username != "") {
			generate_id($pub_sgbd_server, $pub_sgbd_dbname, $pub_sgbd_username, $pub_sgbd_password, $pub_sgbd_tableprefix, $pub_num_of_galaxies, $pub_num_of_systems);
		}
		else {
			$pub_error = "Saisissez correctement les champs de connexion à la base de données";
		}
	}

	$sgbd_server = $pub_sgbd_server;
	$sgbd_dbname = $pub_sgbd_dbname;
	$sgbd_username = $pub_sgbd_username;
	$sgbd_password = $pub_sgbd_password;
	$sgbd_tableprefix = $pub_sgbd_tableprefix;
	$admin_username = $pub_admin_username;
	$admin_password = $pub_admin_password;
	$admin_password2 = $pub_admin_password2;
	$num_of_galaxies = (isset($pub_num_of_galaxies) && !empty($pub_num_of_galaxies))?$pub_num_of_galaxies:9;
	$num_of_systems = (isset($pub_num_of_systems) && !empty($pub_num_of_systems))?$pub_num_of_systems:9;
	$directory = $pub_directory;
}
?>
<form method="POST" action="../install/install.php">
<table width="100%" align="center" cellpadding="20">
<tr>
	<td height="70"><div align="center"><img src="../images/OgameSpy2.jpg"></div></td>
</tr>
<tr>
	<td align="center">
		<table width="800">
		<tr>
			<td colspan="2" align="center"><font size="3"><b>Bienvenue à l'installation d'OGSpy version <?php echo $install_version; ?></b></font></td>
		</tr>
		<tr>
			<td colspan="2" align="center">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><font color="Red"><b><?php echo isset($pub_error) ? $pub_error : "";?></b></font></td>
		</tr>
		
		<tr>
			<td class="c" colspan="2">Configuration de la base de données</td>
		</tr>
		<tr>
			<th>Nom du Serveur de Base de données / SGBD</th>
			<th><input name="sgbd_server" type="text" value="<?php echo isset($pub_sgbd_server) ? $pub_sgbd_server : "localhost";?>"></th>
		</tr>
		<tr>
			<th>Nom de votre base de données</th>
			<th><input name="sgbd_dbname" type="text" value="<?php echo isset($pub_sgbd_dbname) ? $pub_sgbd_dbname : "";?>"></th>
		</tr>
		<tr>
			<th>Nom d'utilisateur</th>
			<th><input name="sgbd_username" type="text" value="<?php echo isset($pub_sgbd_username) ? $pub_sgbd_username : "";?>"></th>
		</tr>
		<tr>
			<th>Mot de passe</th>
			<th><input name="sgbd_password" type="password"></th>
		</tr>
		<tr>
			<th>Préfixe des tables</th>
			<th><input name="sgbd_tableprefix" type="text" value="<?php echo isset($pub_sgbd_tableprefix) ? $pub_sgbd_tableprefix : "ogspy_";?>"></th>
		</tr>
		<tr>
			<td class="c" colspan="2">Configuration de l'univers</td>
		</tr>
		<tr>
			<th>Nombre de galaxies&nbsp;<?php echo help("profile_galaxy", "", "../");?></th>
			<th><input name="num_of_galaxies" type="text" value="<?php echo isset($pub_num_of_galaxies) ? $pub_num_of_galaxies : "9";?>"></th>
		</tr>
		<tr>
			<th>Nombre de systèmes par galaxies&nbsp;<?php echo help("profile_galaxy", "", "../");?></th>
			<th><input name="num_of_systems" type="text" value="<?php echo isset($pub_num_of_systems) ? $pub_num_of_systems : "499";?>"></th>
		</tr>
		
		<tr>
			<td class="c" colspan="2">Configuration du compte administrateur</td>
		</tr>
		<tr>
			<th>Nom d'utilisateur&nbsp;<?php echo help("profile_login", "", "../");?></th>
			<th><input name="admin_username" type="text" value="<?php echo isset($pub_admin_username) ? $pub_admin_username : "";?>"></th>
		</tr>
		<tr>
			<th>Mot de passe&nbsp;<?php echo help("profile_password", "", "../");?></th>
			<th><input name="admin_password" type="password"></th>
		</tr>
		<tr>
			<th>Mot de passe [Confirmer]</th>
			<th><input name="admin_password2" type="password"></th>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th colspan="2"><input name="complete" type="submit" value="Démarrer l'installation complète">&nbsp;ou&nbsp;<input name="file" type="submit" value="Générer le fichier 'id.php'"></th>
		</tr>
		
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2" align="center"><a target="_blank" href="http://www.ogsteam.fr/"><i><font color="orange">Besoin d'assistance ?</font></i></a></td>
		</tr>
		</table>
	</td>
</tr>
<tr align="center">
	<td>
		<center><font size="2"><i><b>OGSpy</b> is an <b>OGSteam Software</b> (c) 2005-2013</i><br />v <?php echo $install_version ;?></font></center>
	</td>
</tr>
</table>
</form>
</body>
<script language="JavaScript" src="../js/wz_tooltip.js"></script>
</html>
