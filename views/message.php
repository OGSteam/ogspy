<?php
/***************************************************************************
*	filename	: message.php
*	desc.		:
*	Author		: Kyser - http://ogsteam.fr/
*	created		: 09/12/2005
*	modified	: 22/06/2006 00:13:20
*	modified	: 13/04/2007 03:34:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}
if(!isset($pub_id_message) || !isset($pub_info)) {
	redirection("index.php");
}

if(!check_var($pub_id_message, "Char") || !check_var($pub_info, "Special", "#^[\sa-zA-Z0-9~¤_.\-\:\[\]]+$#")) {
	redirection("index.php");
}

$action = "";
$message = "<b>Message Système</b><br /><br />";

switch ($pub_id_message) {
	//
	case "forbidden" :
	$message .= "<font color='red'><b>Vous ne disposez pas des droits nécessaires pour effectuer cette action</b></font>";
	break;

	//
	case "errorfatal" :
	$message .= "<font color='red'><b>Interruption suite à une erreur fatale</b></font>";
	break;

	//
	case "errordata" :
	$message .= "<font color='red'><b>Les données transmises sont incorrectes</b></font>";
	break;

	//
	case "createuser_success" :
	list($user_id, $password) = explode(":", $pub_info);
	$user_info = user_get($user_id);
	$message .= "<font color='lime'><b>Création du compte de <a>".$user_info[0]["user_name"]."</a> réussie</b></font><br />";
	$message .= "Transmettez lui ces informations :<br /><br />";
	$message .= "- URL du serveur :<br /><a>http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."</a><br /><br />";
	$message .= "- Mot de passe :<br /><a>".$password."</a><br /><br />";
	$message .= "- URL Xtense :<br /><a>http://".str_replace('index.php', '', $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'mod/xtense/xtense.php')."</a><br /><br />";
	$message .= "<font color='lime'><b>BBCode pour <a>".$user_info[0]["user_name"]."</a></b></font><br /><br />";
	$message .= "[b]Utilisateur:[/b] [i]".$user_info[0]["user_name"]."[/i]<br />
				[b]Lien vers les serveur:[/b] [url]http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."[/url]<br />
				[b]Lien Xtense:[/b] [url]http://".str_replace('index.php', '', $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'mod/xtense/xtense.php')."[/url]<br />
				[b]Mot de passe :[/b] [i]".$password."[/i]";
	$action = "action=administration&subaction=member";
	break;

	//
	case "regeneratepwd_success" :
	list($user_id, $password) = explode(":", $pub_info);
	$user_info = user_get($user_id);
	$message .= "<font color='lime'><b>Génération du nouveau mot de passe de <a>".$user_info[0]["user_name"]."</a> réussie</b></font><br />";
	$message .= "Transmettez lui son mot de passe : <a>".$password."</a>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "regeneratepwd_failed" :
	$message .= "<font color='red'><b>Génération du nouveau mot de passe échouée</b></font>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "createuser_failed_pseudolocked" :
	$message .= "<font color='red'><b>Création du compte de <a>".$pub_info."</a> échouée</b></font><br />";
	$message .= "<i>Le pseudo est déjà utilisé</i>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "createuser_failed_pseudo" :
	$message .= "<font color='red'><b>Création du compte de <a>".$pub_info."</a> échouée</b></font><br />";
	$message .= "<i>Le pseudo doit contenir entre 3 et 15 caractères standards</i></a>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "createuser_failed_password" :
	$message .= "<font color='red'><b>Création du compte de <a>".$pub_info."</a> échouée</b></font><br />";
	$message .= "<i>Le mot de passe doit contenir entre 6 et 15 caractères</i>";
	$action = "action=administration&subaction=member";
	break;	
	
	//
	case "createuser_failed_general" :
	$message .= "<font color='red'><b>Création du compte de <a>".$pub_info."</a> échouée</a></b></font><br />";
	$message .= "<i>Le pseudo est incorrect</i></a>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "admin_modifyuser_success" :
	$user_info = user_get($pub_info);
	$message .= "<font color='lime'><b>Modification du profil de <a>".$user_info[0]["user_name"]."</a> réussie</b></font>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "admin_modifyuser_failed" :
	$message .= "<font color='red'><b>Modification du profil échouée</b></font>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "member_modifyuser_success" :
	$message .= "<font color='lime'><b>Modification de votre profil réussie</b></font>";
	$action = "action=profile";
	break;

	//
	case "member_modifyuser_failed" :
	$message .= "<font color='red'><b>Modification de votre profil échouée</b></font>";
	$action = "action=profile";
	break;

	//
	case "member_modifyuser_failed_passwordcheck" :
	$message .= "<font color='red'><b>Modification de votre profil échouée</b></font><br />";
	$message .= "Saisissez correctement votre ancien mot de passe et deux fois le nouveau";
	$action = "action=profile";
	break;

	//
	case "member_modifyuser_failed_password" :
	$message .= "<font color='red'><b>Modification de votre profil échouée</a></b></font><br />";
	$message .= "Le mot de passe doit contenir entre 6 et 15 caractères standards";
	$action = "action=profile";
	break;

	//
	case "member_modifyuser_failed_pseudolocked" :
	$message .= "<font color='red'><b>Modification de votre profil échouée</b></font><br />";
	$message .= "Le pseudo est déjà utilisé par un autre membre";
	$action = "action=profile";
	break;

	//
	case "member_modifyuser_failed_pseudo" :
	$message .= "<font color='red'><b>Modification de votre profil échouée</b></font><br />";
	$message .= "Le pseudo saisi doit contenir entre 3 et 15 caractères standards";
	$action = "action=profile";
	break;

	//
	case "deleteuser_success" :
	$message .= "<font color='lime'><b>Suppression du membre <a>".$pub_info."</a> réussie</b></font>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "deleteuser_failed" :
	$message .= "<font color='red'><b>Suppression du membre échouée</b></font>";
	$action = "action=administration&subaction=member";
	break;

	//
	case "login_wrong" :
	$message .= "<font color='red'><b>Identifiants de connexion incorrects</b></font>";
	break;

	//
	case "account_lock" :
	$message .= "<font color='red'><b>Votre compte n'est pas activé</b></font><br />";
	$message .= "Contactez un responsable";	
	break;

	//
	case "max_favorites" :
	$message .= "<font color='orange'><b>Vous avez atteint le nombre maximal de favoris (".$server_config["max_favorites"].")</b></font>";
	
	break;

	//
	case "setting_serverconfig_failed" :
	$message .= "<font color='red'><b>La configuration des paramètres serveur a échouée</b></font>";
	$action = "action=administration&subaction=parameter";
	break;

	//
	case "setting_server_view_success" :
	$message .= "<font color='lime'><b>Configuration des paramètres d'affichage achevée avec succès</b></font>";
	$action = "action=administration&subaction=affichage";
	break;

	//
	case "setting_server_view_failed" :
	$message .= "<font color='red'><b>La configuration des paramètres d'affichage à echouée</b></font>";
	$action = "action=administration&subaction=affichage";
	break;

	//
	case "setting_serverconfig_success" :
	$message .= "<font color='lime'><b>Configuration des paramètres serveur achevée avec succès</b></font>";
	$action = "action=administration&subaction=parameter";
	break;

	//
	case "log_missing" :
	$message .= "<font color='orange'><b>Il n'y a pas de fichiers logs à cette période</b></font>";
	$action = "action=administration&subaction=viewer";
	break;

	//
	case "log_remove" :
	$message .= "<font color='lime'><b>Le fichier log séléctionné a bien été supprimer</></font>";
	$action = "action=administration&subaction=viewer";
	break;
	
	//
	case "set_building_failed_planet_id" :
	$message .= "<font color='orange'><b>Veuillez préciser la planète concernée</b></font>";
	$action = "action=home&subaction=empire";
	break;

	//
	case "install_directory" :
	$message .= "<font color='red'><b>Veuillez supprimer le dossier 'install'</b></font>";	
	break;

	//
	case "spy_added" :
	$reports = explode("¤", $pub_info);
	$message .= "<font color='lime'><b>Chargement des rapports d'espionnage terminé</b></font><br />";
	foreach ($reports as $v) {
		list($added, $coordinates, $timestamp) = explode("~", $v);
		list($galaxy, $system, $row) = explode(":", str_replace(array("[", "]"), "", $coordinates));
		$message .= "<br />Rapport d'espionnage de la planète [<a href='index.php?galaxy=".$galaxy."&system=".$system."'><font color='lime'>".$coordinates."</font></a>] : ";
		$message .= $added ? "<font color='lime'>Chargé" : "<font color='orange'>Ignoré";
		$message .= "</font>";
	}
	$action = "action=galaxy";
	break;

	//
	case "createusergroup_success" :
	$message .= "<font color='lime'><b>Création du groupe <a>".$pub_info."</a> réussie</b></font><br />";
	$action = "action=administration&subaction=group";
	break;

	//
	case "createusergroup_failed_groupnamelocked" :
	$message .= "<font color='red'><b>Création du groupe <a>".$pub_info."</a> échouée</b></font><br />";
	$message .= "Le nom est déjà utilisé";
	$action = "action=administration&subaction=group";
	break;

	//
	case "createusergroup_failed_groupname" :
	$message .= "<font color='red'><b>Création du groupe <a>".$pub_info."</a> échouée</b></font><br />";
	$message .= "Le nom doit contenir entre 3 et 15 caractères standards</a>";
	$action = "action=administration&subaction=group";
	break;

	//
	case "createusergroup_failed_general" :
	$message .= "<font color='red'><b>Création du groupe <a>".$pub_info."</a> échouée</a></b></font><br />";
	$message .= "Le nom est incorrect</a>";
	$action = "action=administration&subaction=group";
	break;

	//
	case "db_optimize" :
	list($dbSize_before, $dbSize_after) = explode("¤", $pub_info);
	$message .= "<font color='lime'><b>Optimisation terminée</b></font><br />";
	$message .= "Espace occupé avant optimisation : ".$dbSize_before."<br />";
	$message .= "Espace occupé après optimisation : ".$dbSize_after."<br /><br />";
	$action = "action=administration&subaction=infoserver";
	break;

	//
	case "set_empire_failed_data" :
	$message .= "<font color='red'><b>Un problème est survenu durant l'acquisition de votre empire</b></font>";
	$action = "action=home&subaction=empire";
	break;
  
  	//
	case "raz_ratio" :
	$message .= "<font color='lime'><b>Remise à zéro des recherches effectuée.</b></font><br />";
	$action = "action=statistic";
	break;
	
	//
	default:
	redirection("index.php");
	break;
}

$action = $action != "" ? "?".$action : "";
$message .="<br /><br /><a href='index.php".$action."'>Retour</a>";

require_once("views/page_header_2.php");?>

<table align="center">
<tr>
	<td class="c"><div align="center"><?php echo $message;?></div></td>
</tr>
</table>

<?php
require_once("views/page_tail_2.php");
?>
