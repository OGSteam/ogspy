<?php
/** $Id: about_changelog.php 7665 2012-07-09 14:44:26Z luke_skywalker $ **/
/**
* Affichage du Changelog d'OGSpy : Changements version apr�s version
* @package OGSpy
* @version 3.04b ($Rev: 7665 $)
* @subpackage views
* @author Kyser
* @created 17/01/2006
* @copyright Copyright &copy; 2007, http://ogsteam.fr/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @modified $Date: 2012-07-09 16:44:26 +0200 (Mon, 09 Jul 2012) $
* @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/views/about_changelog.php $
*/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}
?>

<table width="70%" style="text-align:center;">
	<tr>
		<td align="center" class="c" colspan="2"><font color="Yellow">Notes de version</font></td>
	</tr>
	<tr>
		<td class="c" width="50">Version</td>
		<td class="c">Description</td>
	</tr>
	<tr>
		<th>3.1.1</th>
		<th style="text-align:left">
			<ul>
				<li>Compatibilit� OGame 4.X</li>
				<li>Correction de la maintenance automatique</li>
				<li>Mise � jour de l'�quipe OGSteam</li>
			</ul>
			
	  	</th>
	</tr>
	<tr>
		<th>3.1.0</th>
		<th style="text-align:left">
	- Compatibilit� OGame 3.X:<br />
	- Nouveaux classements militaires<br />
	- Nouveaux b�timents.<br />
	- Support IPv6.<br />
	- Nouveau skin.<br />
	  </th>
	</tr>
	<tr>
		<th>3.0.8</th>
		<th style="text-align:left">
	- Affichage RE vue galaxie : affichage de 2 RE : 1 de plan�te, et 1 de lune, si il(s) existe(nt)<br />
	- Modification acc�s � la base de donn�e.<br />
	- Mise en place d'un systeme de mise en cache.<br />
	- Attribution d'un identifiant unique pour chaque installation.<br />
	- Suppression de tous les appels directs � la base de donn�e.<br />
	- Supression des fichiers obsol�tes.<br />
	- Mise en conformit� des pseudos ingame
	- Correctifs divers
	  </th>
	</tr>
	<tr>
		<th>3.0.7</th>
		<th style="text-align:left">
	    - Remplacement de la technologie Exp�ditions par Astrophysique<br />
		  - Support d'un nombre de plan�tes sup�rieur � 9(D�sormais li� � la Technologie Astrophysique)<br />
	    - D�sactivation de l'import par copier - coller<br />
		  - Remise � jour des Liens vers les sites de l'OGSteam<br />
		  - Nouvelle Gestion des Id Plan�tes <br />
	        - Mise a jour des diverses formules de calcul <br />
	        - Mise en conformit� r�glement ogame v1 <br />
	  </th>
	</tr>
	<tr>
		<th>3.0.6</th>
		<th style="text-align:left">
	    - Non Publi�e<br />
		</th>
	</tr>
	<tr>
		<th>3.0.5</th>
		<th style="text-align:left">
	    - Compatibilit� avec OGame 0.78c<br />
	    - Depots de ravitaillement (optionnel)<br />
	    - Vitesse de l'univers param�trable<br />
	    - Ajout des exp�ditions<br />
	    - RC directement pars� dans OGSpy<br />
	    - Changement de la structure de la base de donn�e (optimisation ++++)<br />
	    - Affichage des RC enregistr�s directement sur la vue galaxie<br />
		</th>
	</tr>
	<tr>
		<th>3.04b</th>
		<th style="text-align:left">
			- Suppression du fond transparent pour l'ajout des membres (admin)<br />
			- Ajout de flag admin param�trables sur les mods <br />
			- Ajout d'une option de journalisation des erreurs php<br />
			- autoupdate: descriptions des mods, plus d'infos sur les droits d'ecritures<br />
			- Ajout d'une table de configuration pour les mods avec les fonctions appropri�s<br />
			- Correction bug sur recherche "stricte" <br />
			- Correction du bug de droits insuffisants pour copier/coller les infos<br />
			- Am�lioration securit�<br />
			- Correction bug "Illegal mix of collations" <br />
			- Correction bug d'ajout de membres <br />
		</th>
	</tr>
	<tr>
		<th>3.04</th>
		<th style="text-align:left">
			- Ajout du mod_Xtense � la base d'OGSpy<br />
			- Ajout du mod_autoupdate � la base d'OGSpy<br />
			- Ajout d'une fonction "Ajouter tout les membres" pour les groupes<br />
			- Correction de bugs li� au passage d'Ogame en version 0.77b<br />
		</th>
	</th>
	<tr> 
		<th>3.03</th> 
		<th style="text-align:left"> 
			- Mise en place du choix de galaxies et de syt�mes par galaxies<br/>
		</th>
	</tr>
	<tr> 
		<th>3.02c</th> 
		<th style="text-align:left"> 
			- Ordonnancement des mods dans l'administration<br /> 
			- Assouplissement des contr�les sur l'injection de syst�mes solaires et rapport d'espionnage<br /> 
			- Modifications mineures de l'interface de l'administration<br /> 
			- Possibilit� de d�sactiver le contr�le des adresses IP provoquant des d�connexions intempestives (AOL, Proxy, etc)<br /> 
			- Correction d'anomalies diverses<br /> 
		</th>
	</tr>
	<tr>
		<th>3.02b</th>
		<th style="text-align:left">
			- Correction de bugs mineurs<br />
		</th>
	</tr>
	<tr>
		<th>3.02</th>
		<th style="text-align:left">
			- Gestion des utilisateurs par groupe<br />
			- Cartographie alliance<br />
			- Am�lioration de l'interface par l'utilisation de tooltips<br />
			- Prise en compte des phalanges et portes spatiales<br />
			- Affichage des syst�mes solaires et lunes obsol�tes<br />
			- M�morisation de rapport d'espionnage dans l'espace personnel<br />
			- Optimisation du code pour de meilleurs d�lais de r�ponse<br />
			- Espace personnel enrichi avec affichage de graphiques<br />
			- Calcul de la participation des membres dans la section statistiques<br />
			- Gestionnaire d'int�gration de mods<br />
			- Correction de bugs mineurs<br />
			<i>- Incompatibilit� avec les versions d'OGS ant�rieures � la 2.0</i><br />
		</th>
	</tr>
	<tr>
		<th>0.301b</th>
		<th style="text-align:left">
			- Correction mauvais affichage des joueurs absents<br />
			- Correction du bug emp�chant de rentrer le classement dans la p�riode 16h-24h<br />
			- Bug javascript emp�chant de faire des simulations avec Internet Explorer corrig�<br />
			- Correction de bugs mineurs<br />
		</th>
	</tr>
	<tr>
		<th>0.301</th>
		<th style="text-align:left">
			- Disponibilit� du script de migration des bases de donn�es OGSS -> OGSpy<br />
			- Nombre de satellites pass� � 5 chiffres dans l'espace personnel<br />
			- Ajout d'un nouveau crit�re de recherche selon les rapports d'espionnage (Merci ben.12)<br />
			- Possibilit� de visualiser plusieurs syst�mes sur une m�me page par l'interm�diaire de la page statistiques<br />
			- Optimisation de l'affichage du classement joueur<br />
			- Affichage des syst�mes mis � jour dans la section statistiques par secteur<br />
			- Correction bug exportation des rapports d'espionnage par syst�me qui envoyait tous les rapports connus vers OGS au lieu du syst�me demand�<br />
			- Purge automatique des classements et des rapports d'espionnage selon l'anciennet� ou le nombre maximal autoris�. (Param�trable dans l'administration)<br />
			- Possibilit� de supprimer les classements au cas par cas<br />
			- Importation du classement directement sur le serveur<br />
			- Possibilit� d'avoir de nombreuses statistiques par le biais de BBClone<br />
			- Faille de s�curit� concernant les sessions corrig�es
		</th>
	</tr>
	<tr>
		<th>0.300f</th>
		<th style="text-align:left">
			- Les rapports d'espionnage sont affich�s du plus r�cent au plus ancien<br />
			- Message dans le journal lorsque l'on envoie le classement<br />
			- Exportation de rapports d'espionnage selon une date<br />
			- Correction du bug d'affichage classement<br />
			- R�sum� apr�s envoi de rapports d'espionnage<br />
			- Correction du bug de recherche qui emp�chait les pages suivantes avec comme un crit�re diff�rent des coordonn�es<br />
			- Correction bug dans l'espace personnel, calcul de la production d'�nergie et de deut�rium fauss�e
		</th>
	</tr>
	<tr>
		<th>0.300e</th>
		<th style="text-align:left">
			- Correction du bug de recherche qui n'affichait pas les pages avec IE<br />
			- Correction du bug de non compatibilit� de requetes SQL avec certains serveurs MySQL<br />
			- Affichage PHPInfo - Modules PHP dans l'administration<br />
			- Correction bug gestion empire (apparition des plan�tes d'autres joueurs apr�s modification)<br />
			- Possibilit� de param�trer le lien du forum affich� sur le menu par l'administration<br />
			- Correction du bug d'importation de certains rapports d'espionnage<br />
			- Possibilit� de contr�ler que le serveur soit � jour dans l'administration
		</th>
	</tr>
	<tr>
		<th>0.300d</th>
		<th style="text-align:left">
			- Correction du bug du panneau d'administration et de connexion avec OGS li� � un champ manquant dans la base de donn�es<br />
			- Correction bug de recherche des joueurs sans ally<br />
			- Correction du bug dans l'espace personnel au sujet du nombre de cases utilis�es par plan�te
		</th>
	</tr>
	<tr>
		<th>0.300c</th>
		<th style="text-align:left">
			- Correction du bug d'importation des rapports d'espionnage<br />
			- Correction bug emp�chant de modifier les param�tres serveur selon la configuration d'installation employ�e pour OGSpy<br />
			- Correction de bugs mineurs
		</th>
	</tr>
	<tr>
		<th>0.300b</th>
		<th style="text-align:left">
			- Modification des requ�tes incompatibles avec MySQL 4.0
		</th>
	</tr>
	<tr>
		<th>0.300</th>
		<th style="text-align:left">
			- Restructuration int�grale du code<br />
			- Nouvelle interface utilisateur<br />
		</th>
	</tr>
</table>
