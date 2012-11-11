<?php
/**
* autoupdate.php Page maitresse du mod (fait les mises à jours des mods et affiche les pages demandées)
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 27/10/2006
* modified	: 18/01/2007
* $Id: autoupdate.php 7754 2012-11-07 21:23:06Z darknoon $
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

require_once("views/page_header.php");
if ( !function_exists('json_decode')) die("Autoupdate ne peut fonctionner correctement sans la librairie JSON, Merci de mettre à jour PHP(>= 5.2)");
require_once("mod/autoupdate/functions.php");
require_once("mod/autoupdate/lang_main.php");

/**
* Défini où se trouve le fichier qui contient les dernières versions des mods.
* Différent suivant si allow_url_fopen est activé ou non. S'il n'est pas activé, on va chercher le fichier en local après téléchargement.
*/
if(mod_get_option("DOWNJSON")) {
	DEFINE("JSON_FILE","http://update.ogsteam.fr/update.json");
} else {
	DEFINE("JSON_FILE","parameters/modupdate.json");
}

if (!isset($pub_sub)) {
	$sub = "overview";
	$pub_sub = "overview";
} else $sub = $pub_sub;

if ($user_data["user_admin"] == 1 OR $user_data["user_coadmin"] == 1) {
	if ($sub != "overview") {
		$bouton1 = "\t\t"."<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=overview';\">";

		$bouton1 .= "<font color='lime'>".$lang['autoupdate_autoupdate_table']."</font>";
		$bouton1 .= "</td>\n";
	} else {
		$bouton1 = "\t\t"."<th width='150'>";
		$bouton1 .= "<font color=\"#5CCCE8\">".$lang['autoupdate_autoupdate_table']."</font>";
		$bouton1 .= "</th>\n";
	}
	if ($sub != "down") {
		$bouton2 = "\t\t"."<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=down';\">";
		$bouton2 .= "<font color='lime'>".$lang['autoupdate_autoupdate_down']."</font>";
		$bouton2 .= "</td>\n";
	} else {
		$bouton2 = "\t\t"."<th width='150'>";
		$bouton2 .= "<font color=\"#5CCCE8\">".$lang['autoupdate_autoupdate_down']."</font>";
		$bouton2 .= "</th>\n";
	}
} else {
	$bouton1 = "";
	$bouton2 = "";
}
if ($user_data["user_admin"] == 1) {
	if ($sub != "admin") {
		$bouton3 = "\t\t"."<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=admin';\">";
		$bouton3 .= "<font color='lime'>".$lang['autoupdate_autoupdate_admin']."</font>";
		$bouton3 .= "</td>\n";
	} else {
		$bouton3 = "\t\t"."<th width='150'>";
		$bouton3 .= "<font color=\"#5CCCE8\">".$lang['autoupdate_autoupdate_admin']."</font>";
		$bouton3 .= "</th>\n";
	}
} else {
	$bouton3 = "";
}


echo "\n<table>\n";
echo "\t<tr>\n";
	echo $bouton1.$bouton2.$bouton3;
echo "\t</tr><br />\n";
echo "</table>\n<br />\n";

if (!isset($pub_sub)) $sub = 'overview'; else $sub = htmlentities($pub_sub);
 switch($sub)
{
case 'overview': include ('overview.php');break;
case 'mod_upgrade': include ('mod_upgrade.php');break;
case 'tool_upgrade': include ('tool_upgrade.php');break;
case 'down': include ('down.php');break;
case 'admin': include ('admin.php');break;
}
?>
