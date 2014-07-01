<?php
/**
* Functions which informs the user about an item using a pop-up.
* 
* This function create a pop-up which informs the user about items in the user interface.
* @package OGSpy
* @subpackage Help
* @author Kyser
* @copyright Copyright &copy; 2007, http://ogsteam.fr/
* @version 3.04b ($Rev: 7688 $)
* @modified $Date: 2012-08-18 14:35:34 +0200 (Sat, 18 Aug 2012) $
* @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/help.php $
* $Id: help.php 7688 2012-08-18 12:35:34Z darknoon $
*/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

$help["admin_server_status"] = "Lorsque le serveur est d�sactiv�, seul les membres avec le statut d'administrateur ont acc�s aux fonctionnalit�s du serveur";
$help["admin_server_status_message"] = "Le message sera affich� aux membres \"de base\" lorsque le serveur sera d�sactiv�";
$help["admin_save_transaction"] = "Les transactions correspondent aux :<br />- Syst�mes solaires<br />- Rapports d'espionnage<br />- Classements joueurs et alliances";
$help["admin_member_manager"] = "Autorise la cr�ation, la mise � jour et la suppression des utilisateurs";
$help["admin_ranking_manager"] = "Autorise la suppression des classements joueurs et alliances";
$help["admin_check_ip"] = "Certains utilisateurs subissent des d�connexions intempestives (AOL, Proxy, etc).<br />Activez cette option afin qu'ils puissent d�sactiver la v�rification dans leur profil";
$help["admin_session_infini"] = "Si vous choisissez des sessions ind�finies dans le temps, plusieurs individus ne pourront plus utiliser le m�me compte en m�me temps";
$help["drop_sessions"] = "Vide la table des sessions, cela all�ge l'administration mais oblige tout les utilisateurs � se reconnecter.";

$help["search_strict"] = "<font color=orange>Joueur recherch� :</font><br /><i>Liquid snake</i><br /><font color=orange>Crit�re de recherche :</font><br /><i>snake</i><br /><br />=> <font color=lime>R�sultat positif</font> si l'option \"strict\" est d�sactiv�e<br />=> <font color=red>R�sultat n�gatif</font> si l'option \"strict\" est activ�e";

$help["home_commandant"] = "Page empire du compte commandant";

$help["profile_login"] = "Doit contenir entre 3 et 15 caract�res (les caract�res sp�ciaux ne sont pas accept�s)";
$help["profile_pseudo_email"] = "Si rempli, vous recevrez des mails de la part de certains mods";
$help["profile_skin"] = "OGSpy utilise les m�mes skins qu'OGame";
$help["profile_main_planet"] = "La vue Galaxie sera ouverte directement sur ce syst�me solaire";
$help["profile_password"] = "Doit contenir entre 6 et 15 caract�res (les caract�res sp�ciaux ne sont pas accept�s)";
$help["profile_galaxy"] = "Doit contenir un nombre<br /> de 1 � 999";
$help["profile_disable_ip_check"] = "La v�rification de l'adresse IP permet de vous prot�ger contre le vol de session.<br /><br />";
$help["profile_disable_ip_check"] .= "Si vous �tes d�connect� r�guli�rement (AOL, Proxy, etc), d�sactivez la v�rification.<br /><br />";
$help["profile_disable_ip_check"] .= "<i>L'option est disponible uniquement si l'administrateur l'a activ�e</i>";

$help["galaxy_phalanx"] = "Chargez des rapports d'espionnage pour afficher les phalanges hostiles";

$help["ratio_block"] = "Vous avez un ratio inferieur au seuil, vous ne pouvez pas acc�der aux mods";
$help["profile_speed_uni"] = "Indiquez le multiplicateur de vitesse de votre univers (1 par d�faut)";
$help["profile_ddr"] = "Cocher si le d�p�t de ravitaillement est pr�sent dans votre univers";
$help["astro_strict"] = "Cocher si l'univers est ancien. Cela permet d'avoir 9 planetes sans avoir la technologie astrophysique correspondante.";
$help["config_cache"] = "Dur�e de vie du cache config en seconde.";
$help["mod_cache"] = "Dur�e de vie du cache mod en seconde..";

/**
* help() creates a pop-up to display the help message on the mouse over.
* @param string $key The Help message ID
* @param string $value If the ID is not used, it is possible to use a custom message
* @param string $prefixe Path to the OGSpy root (Not really used)
* @return string The Html code to insert.
*/
function help($key, $value = "", $prefixe = "") {
	global $help;

	if (!isset($help[$key]) && !(is_null($key) && $value <> "")) {
		return;
	}

	if (isset($help[$key])) {
		$value = $help[$key];
	}
	
	$text = "<table width=\"200\">";
	$text .= "<tr><td align=\"center\" class=\"c\">Aide</td></tr>";
	$text .= "<tr><th align=\"center\">".addslashes($value)."</th></tr>";
	$text .= "</table>";

	$text = htmlentities($text);
	$text = "this.T_WIDTH=210;this.T_TEMP=0;return escape('".$text."')";

	return "<img style=\"cursor:pointer\" src=\"".$prefixe."images/help_2.png\" onmouseover=\"".$text."\">";
}
?>
