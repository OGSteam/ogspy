<?php
/**
* lang_main.php Définit les $lang
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 13/11/2006
* modified	: 18/01/2007
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
$lang['autoupdate_autoupdate_table'] = "Tableau de mise à jour";
$lang['autoupdate_autoupdate_admin'] = "Administration";
$lang['autoupdate_autoupdate_down'] = "Téléchargement des mods";
$lang['autoupdate_tableau_info'] = "Lorsque les mods sont mis à jour, les fichiers présents dans le zip écrasent les anciens.";
$lang['autoupdate_tableau_error'] = "Erreur lors de l'accès au fichier";
$lang['autoupdate_tableau_error1'] = "Récupération des informations de version impossible !";
$lang['autoupdate_tableau_error2'] = "Récupération du fichier modupdate.json impossible !";
$lang['autoupdate_tableau_error3'] = "Le téléchargement du fichier modupdate.json n'a pas eu lieu";
$lang['autoupdate_tableau_namemod'] = "Nom du module";
$lang['autoupdate_tableau_nametool'] = "Nom de l'outil";
$lang['autoupdate_tableau_description'] = "Description du module";
$lang['autoupdate_tableau_modinstall'] = "Modules installés - Recherche des mises à jour sur OGSteam.fr";
$lang['autoupdate_tableau_toolinstall'] = "Outils installés - Recherche des mises à jour sur OGSteam.fr";
$lang['autoupdate_tableau_modnoinstall'] = "Modules non installés - Recherche des mods sur OGSteam.fr";
$lang['autoupdate_tableau_autoMaJ'] = "- Lancez la procedure de mise à jour";
$lang['autoupdate_tableau_version'] = "Version installée";
$lang['autoupdate_tableau_versionSVN'] = "Dernière version disponible";
$lang['autoupdate_tableau_versionTrunk'] = "Dernière version sur Trunk";
$lang['autoupdate_tableau_action'] = "Action";
$lang['autoupdate_tableau_uptodate'] = "Mettre à jour";
$lang['autoupdate_tableau_norefered'] = "Non référencé";
$lang['autoupdate_tableau_link'] = "Liens";
$lang['autoupdate_tableau_pageadmin'] = "Page d'administration des modules OGSpy";
$lang['autoupdate_tableau_ok'] = "Téléchargement de modupdate.json réussi";
$lang['autoupdate_tableau_ok1'] = "Forcer le téléchargement";
$lang['autoupdate_tableau_uptodateok'] = "Mise à jour effectuée";
$lang['autoupdate_tableau_installok'] = "Installation effectuée";
$lang['autoupdate_tableau_back'] = "<a href=index.php?action=autoupdate>Revenir au tableau des mises à jour</a> ";
$lang['autoupdate_tableau_uptodateoff'] = "Mise à jour impossible, passez par la page d'administration";
$lang['autoupdate_tableau_installoff'] = "Installation impossible, passez par la page d'administration";
$lang['autoupdate_MaJ_file'] = "Fichier";
$lang['autoupdate_MaJ_condition'] = "Etat";
$lang['autoupdate_MaJ_error'] = "Erreur à la mise à jour du fichier.";
$lang['autoupdate_MaJ_uptodateok'] = "Mise à jour réussie !!";
$lang['autoupdate_MaJ_downok'] = "Téléchargement réussi.";
$lang['autoupdate_MaJ_unzipok'] = "Décompression réussie.";
$lang['autoupdate_MaJ_statistic'] = "Statistiques de mise à jour";
$lang['autoupdate_MaJ_number'] = "Nombre de fichiers et dossiers mis à jour";
$lang['autoupdate_MaJ_numbermod'] = "Nombre de mods mis à jour.";
$lang['autoupdate_MaJ_rights'] = "Vous n'avez pas les droits nécessaire.";
$lang['autoupdate_MaJ_wantupdate'] = "Voulez vous finir de mettre<br />à jour le mod ???";
$lang['autoupdate_MaJ_wantupdatemod'] = "Voulez vous finir de mettre<br />à jour les mods ???";
$lang['autoupdate_MaJ_wantinstall'] = "Voulez vous installer<br />le mod ???";
$lang['autoupdate_MaJ_linkupdate'] = "Oui";
$lang['autoupdate_admin_time'] = "Heure serveur";
$lang['autoupdate_admin_writeerror'] = "Echec, impossible d'écrire dans le fichier \"parameters.php\"";
$lang['autoupdate_admin_valid'] = "Valider les paramètres";
$lang['autoupdate_admin_define'] = "Mise à jour des paramètres impossible :";
$lang['autoupdate_admin_iswritable'] = "Veuillez autoriser l'écriture sur le fichier \"parameters.php\" !!!";
$lang['autoupdate_admin_isnotwritable'] = "- Le fichier \"parameters.php\" n'est pas autorisé en écriture";
$lang['autoupdate_admin_generated'] = "Le fichier parameters a bien été généré";
$lang['autoupdate_admin_option'] = "Option";
$lang['autoupdate_admin_value'] = "Valeur";
$lang['autoupdate_admin_value1'] = "(oui | non)";
$lang['autoupdate_admin_MaJ'] = "Mise à jour et téléchargement de<br />nouveaux mods autorisé pour les co-admins";
$lang['autoupdate_admin_AUTOMaJ'] = "Possibilité de mise à jour global";
$lang['autoupdate_admin_down'] = "Téléchargement du fichier modupdate.json";
$lang['autoupdate_admin_down1'] = "A chaque accès au [MOD] AutoUpdate";
$lang['autoupdate_admin_banlist'] = "Utiliser la liste des Mods Bannis ?";
$lang['autoupdate_admin_banlist1'] = "(sql, mplogger, naqOgsPlugin, ogsfox, quiMObserve, packMod, modUpdate, market)";
$lang['autoupdate_admin_trunk'] = "Proposer la mise à jour depuis le Trunk ?";
$lang['autoupdate_admin_trunk1'] = "(Versions de développement)";
$lang['autoupdate_admin_frequency'] = "Choisir la fréquence de mise à jour(Heures)";
$lang['autoupdate_admin_list'] = "Liste des fonctions";
$lang['autoupdate_admin_define'] = "Définie";
$lang['autoupdate_admin_off'] = "Non définie";
$lang['autoupdate_createdby'] = "Créé par";
$lang['autoupdate_and'] = "et";
$lang['autoupdate_version'] = "version";
$lang['autoupdate_error'] = "ERREUR";
?>
