<?php
/**
* lang_main.php D�finit les $lang
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 13/11/2006
* modified	: 18/01/2007
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
$lang['autoupdate_autoupdate_table'] = "Tableau de mise � jour";
$lang['autoupdate_autoupdate_admin'] = "Administration";
$lang['autoupdate_autoupdate_down'] = "T�l�chargement des mods";
$lang['autoupdate_tableau_info'] = "Lorsque les mods sont mis � jour, les fichiers pr�sents dans le zip �crasent les anciens.";
$lang['autoupdate_tableau_error'] = "Erreur lors de l'acc�s au fichier";
$lang['autoupdate_tableau_error1'] = "R�cup�ration des informations de version impossible !";
$lang['autoupdate_tableau_error2'] = "R�cup�ration du fichier modupdate.json impossible !";
$lang['autoupdate_tableau_error3'] = "Le t�l�chargement du fichier modupdate.json n'a pas eu lieu";
$lang['autoupdate_tableau_namemod'] = "Nom du module";
$lang['autoupdate_tableau_nametool'] = "Nom de l'outil";
$lang['autoupdate_tableau_description'] = "Description du module";
$lang['autoupdate_tableau_modinstall'] = "Modules install�s - Recherche des mises � jour sur OGSteam.fr";
$lang['autoupdate_tableau_toolinstall'] = "Outils install�s - Recherche des mises � jour sur OGSteam.fr";
$lang['autoupdate_tableau_modnoinstall'] = "Modules non install�s - Recherche des mods sur OGSteam.fr";
$lang['autoupdate_tableau_autoMaJ'] = "- Lancez la procedure de mise � jour";
$lang['autoupdate_tableau_version'] = "Version install�e";
$lang['autoupdate_tableau_versionSVN'] = "Derni�re version disponible";
$lang['autoupdate_tableau_versionTrunk'] = "Derni�re version sur Trunk";
$lang['autoupdate_tableau_action'] = "Action";
$lang['autoupdate_tableau_uptodate'] = "Mettre � jour";
$lang['autoupdate_tableau_norefered'] = "Non r�f�renc�";
$lang['autoupdate_tableau_link'] = "Liens";
$lang['autoupdate_tableau_pageadmin'] = "Page d'administration des modules OGSpy";
$lang['autoupdate_tableau_ok'] = "T�l�chargement de modupdate.json r�ussi";
$lang['autoupdate_tableau_ok1'] = "Forcer le t�l�chargement";
$lang['autoupdate_tableau_uptodateok'] = "Mise � jour effectu�e";
$lang['autoupdate_tableau_installok'] = "Installation effectu�e";
$lang['autoupdate_tableau_back'] = "<a href=index.php?action=autoupdate>Revenir au tableau des mises � jour</a> ";
$lang['autoupdate_tableau_uptodateoff'] = "Mise � jour impossible, passez par la page d'administration";
$lang['autoupdate_tableau_installoff'] = "Installation impossible, passez par la page d'administration";
$lang['autoupdate_MaJ_file'] = "Fichier";
$lang['autoupdate_MaJ_condition'] = "Etat";
$lang['autoupdate_MaJ_error'] = "Erreur � la mise � jour du fichier.";
$lang['autoupdate_MaJ_uptodateok'] = "Mise � jour r�ussie !!";
$lang['autoupdate_MaJ_downok'] = "T�l�chargement r�ussi.";
$lang['autoupdate_MaJ_unzipok'] = "D�compression r�ussie.";
$lang['autoupdate_MaJ_statistic'] = "Statistiques de mise � jour";
$lang['autoupdate_MaJ_number'] = "Nombre de fichiers et dossiers mis � jour";
$lang['autoupdate_MaJ_numbermod'] = "Nombre de mods mis � jour.";
$lang['autoupdate_MaJ_rights'] = "Vous n'avez pas les droits n�cessaire.";
$lang['autoupdate_MaJ_wantupdate'] = "Voulez vous finir de mettre<br />� jour le mod ???";
$lang['autoupdate_MaJ_wantupdatemod'] = "Voulez vous finir de mettre<br />� jour les mods ???";
$lang['autoupdate_MaJ_wantinstall'] = "Voulez vous installer<br />le mod ???";
$lang['autoupdate_MaJ_linkupdate'] = "Oui";
$lang['autoupdate_admin_time'] = "Heure serveur";
$lang['autoupdate_admin_writeerror'] = "Echec, impossible d'�crire dans le fichier \"parameters.php\"";
$lang['autoupdate_admin_valid'] = "Valider les param�tres";
$lang['autoupdate_admin_define'] = "Mise � jour des param�tres impossible :";
$lang['autoupdate_admin_iswritable'] = "Veuillez autoriser l'�criture sur le fichier \"parameters.php\" !!!";
$lang['autoupdate_admin_isnotwritable'] = "- Le fichier \"parameters.php\" n'est pas autoris� en �criture";
$lang['autoupdate_admin_generated'] = "Le fichier parameters a bien �t� g�n�r�";
$lang['autoupdate_admin_option'] = "Option";
$lang['autoupdate_admin_value'] = "Valeur";
$lang['autoupdate_admin_value1'] = "(oui | non)";
$lang['autoupdate_admin_MaJ'] = "Mise � jour et t�l�chargement de<br />nouveaux mods autoris� pour les co-admins";
$lang['autoupdate_admin_AUTOMaJ'] = "Possibilit� de mise � jour global";
$lang['autoupdate_admin_down'] = "T�l�chargement du fichier modupdate.json";
$lang['autoupdate_admin_down1'] = "A chaque acc�s au [MOD] AutoUpdate";
$lang['autoupdate_admin_banlist'] = "Utiliser la liste des Mods Bannis ?";
$lang['autoupdate_admin_banlist1'] = "(sql, mplogger, naqOgsPlugin, ogsfox, quiMObserve, packMod, modUpdate, market)";
$lang['autoupdate_admin_trunk'] = "Proposer la mise � jour depuis le Trunk ?";
$lang['autoupdate_admin_trunk1'] = "(Versions de d�veloppement)";
$lang['autoupdate_admin_frequency'] = "Choisir la fr�quence de mise � jour(Heures)";
$lang['autoupdate_admin_list'] = "Liste des fonctions";
$lang['autoupdate_admin_define'] = "D�finie";
$lang['autoupdate_admin_off'] = "Non d�finie";
$lang['autoupdate_createdby'] = "Cr�� par";
$lang['autoupdate_and'] = "et";
$lang['autoupdate_version'] = "version";
$lang['autoupdate_error'] = "ERREUR";
?>
