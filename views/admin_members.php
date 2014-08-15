<?php
/***************************************************************************
*	filename	: admin_members.php
*	desc.		:
*	Author		: Kyser - http://ogsteam.fr/
*	created		: 16/12/2005
*	modified	: 30/07/2006 00:00:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] != 1) {
	redirection("index.php?action=message&id_message=forbidden&info");
}

$user_info = user_get();
$usergroup_list = usergroup_get();
?>

<table width="200">
<tr>
	<td class="c" colspan="3">Création d'un nouveau compte</td>
</tr>
<tr>
	<th width="100"><input type="button" value="Créer nouveau membre" onclick="document.getElementById('new_member').style.visibility = 'visible';"></th>
</tr>
</table>
<br />
<table>
<tr>
	<td class="c" width="120">Joueur</td>
	<td class="c" width="120">Inscrit le</td>
	<td class="c" width="120">Compte actif</td>
<?php
if ($user_data["user_admin"] == 1) {?>
	<td class="c" width="120">Co-administrateur</td>
<?php }
if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {?>
	<td class="c" width="120">Gestion des membres&nbsp;<?php echo help("admin_member_manager");?></td>
<?php }?>
	<td class="c" width="120">Gestion des classements&nbsp;<?php echo help("admin_ranking_manager");?></td>
	<td class="c" width="120">Dernière connexion</td>
	<td class="c" colspan="3">&nbsp;</td>
</tr>
<?php
foreach ($user_info as $v) {
	$user_id = $v["user_id"];
	if (($user_data["user_admin"] != 1 && $v["user_admin"] == 1) ||
	($user_data["user_admin"] != 1 && $user_data["user_coadmin"] == 1 && $v["user_coadmin"] == 1) ||
	($user_data["user_admin"] != 1 && ($user_data["user_coadmin"] != 1 && $user_data["management_user"] == 1) && ($v["user_coadmin"] == 1 || $v["management_user"] == 1))) {
		continue;
	}

	$YesNo = array("<font color=\"red\">Non</font>", "<font color=\"lime\">Oui</font>");
	$user_auth = user_get_auth($user_id);

	$auth = "<table width=\"100%\">";
	$auth .= "<tr><td class=\"c\" colspan=\"2\">Droits sur le serveur</td></tr>";
	$auth .= "<tr><th>Ajout de systèmes solaires</th><th>".$YesNo[$user_auth["server_set_system"]]."</th></tr>";
	$auth .= "<tr><th>Ajout de rapports d\'espionnage</th><th>".$YesNo[$user_auth["server_set_spy"]]."</th></tr>";
	$auth .= "<tr><th>Ajout de classements</th><th>".$YesNo[$user_auth["server_set_ranking"]]."</th></tr>";
	$auth .= "<tr><th>Affichage des positions protégées</th><th>".$YesNo[$user_auth["server_show_positionhided"]]."</th></tr>";

	$auth .= "<tr><td class=\"c\" colspan=\"2\">Droits clients externes</td></tr>";
	$auth .= "<tr><th>Connexion au serveur</th><th>".$YesNo[$user_auth["ogs_connection"]]."</th></tr>";
	$auth .= "<tr><th>Importation de systèmes solaires</th><th>".$YesNo[$user_auth["ogs_set_system"]]."</th></tr>";
	$auth .= "<tr><th>Exportation de systèmes solaires</th><th>".$YesNo[$user_auth["ogs_get_system"]]."</th></tr>";
	$auth .= "<tr><th>Importation de rapports d\'espionnage</th><th>".$YesNo[$user_auth["ogs_set_spy"]]."</th></tr>";
	$auth .= "<tr><th>Exportation de rapports d\'espionnage</th><th>".$YesNo[$user_auth["ogs_get_spy"]]."</th></tr>";
	$auth .= "<tr><th>Importation de classements</th><th>".$YesNo[$user_auth["ogs_set_ranking"]]."</th></tr>";
	$auth .= "<tr><th>Exportation de classements</th><th>".$YesNo[$user_auth["ogs_get_ranking"]]."</th></tr>";
	$auth .= "</table>";

	$auth = htmlentities($auth);

	$name = $v["user_name"];

	$reg_date =  strftime("%d %b %Y %H:%M", $v["user_regdate"]);

	$active_off = !$v["user_active"] ? " selected" : "";
	$user_coadmin_off = (!$v["user_coadmin"]&&!$v["user_admin"]) ? " selected" : "";
	$management_user_off = (!$v["management_user"]&&!$v["user_admin"]) ? " selected" : "";
	$management_ranking_off = (!$v["management_ranking"]&&!$v["user_admin"]) ? " selected" : "";

	if ($v["user_lastvisit"] != 0) {
		$last_visit =  strftime("%d %b %Y %H:%M", $v["user_lastvisit"]);
	}
	else {
		$last_visit = "--";
	}

	echo "<tr>"."\n";

	echo "<form method='POST' action='index.php?action=admin_modify_member&user_id=".$user_id."'>"."\n";
	echo "\t"."<th><a onmouseover=\"this.T_WIDTH=260;this.T_TEMP=15000;return escape('".$auth."')\">".$name."</a></th>"."\n";
	echo "\t"."<th>".$reg_date."</th>"."\n";
	echo "\t"."<th><select name='active'><option value='1'>Oui</option><option value='0'$active_off>Non</option></select></th>"."\n";
	if ($user_data["user_admin"] == 1) {
		echo "\t"."<th><select name='user_coadmin'><option value='1'>Oui</option><option value='0'$user_coadmin_off>Non</option></select></th>"."\n";
	}
	if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
		echo "\t"."<th><select name='management_user'><option value='1'>Oui</option><option value='0'$management_user_off>Non</option></select></th>"."\n";
	}
		echo "\t"."<th><select name='management_ranking'><option value='1'>Oui</option><option value='0'$management_ranking_off>Non</option></select></th>"."\n";
		echo "\t"."<th>".$last_visit."</th>"."\n";
	echo "\t"."<th><input type='image' src='images/usercheck.png' title='Valider les paramètres de ".$name."'></th>"."\n";
	echo "</form>"."\n";

	echo "<form method='POST' action='index.php?action=delete_member&user_id=".$user_id."' onsubmit=\"return confirm('Etes-vous sûr de vouloir supprimer ".$name."');\">"."\n";
	echo "\t"."<th><input type='image' src='images/userdrop.png' title='Supprimer le compte de ".$name."'></th>"."\n";
	echo "</form>"."\n";

	echo "<form method='POST' action='index.php?action=new_password&user_id=".$user_id."' id=".$user_id.">"."\n";
	echo "\t"."<th><img style=\"cursor:pointer\" src='images/userpwd.png' title='Changer le mot de passe de ".$name."' onclick=\"if(confirm('Etes-vous sûr de vouloir changer le mot de passe de ".$name."')){document.all.pass_name.value='".$name."';document.all.pass_id.value='".$user_id."';document.getElementById('pass_new').value = '';document.getElementById('new_pass').style.visibility = 'visible';}\"><input type=\"hidden\" id=\"".$name."\" name=\"pass_".$user_id."\" value=\"\"></th>"."\n"; 
	echo "</form>"."\n";
	echo "</tr>"."\n";
}
?>
</table>
<div id="new_pass" name="new_pass" style="visibility:hidden;position: fixed;    top: 300px;     left: 500;z-index: 100;"> 
	<table width="200" style="border:1px #003399 solid;" cellpadding="3"><tr><td align="center" class="c">Nouveau mot de passe</td></tr><tr><th align="center"> 
	Laissez vide pour un mot de passe aléatoire<br> 
		<input type="hidden" name="pass_name" value=""><br> 
		<input type="hidden" name="pass_id" value=""> 
		<input type="text" name="pass" id="pass_new" value=""><br><br> 
		<input type="button" value="ok" onclick="document.getElementById(document.all.pass_name.value).value = document.getElementById('pass_new').value;document.getElementById(document.all.pass_id.value).submit();"> 
		<input type="button" value="annuler" onclick="document.getElementById('new_pass').style.visibility = 'hidden';"> 
	</th></tr></table> 
</div> 
<div id="new_member" name="new_member" style="visibility:hidden;position: fixed; top: 200px; left: 500;z-index: 100;"> 
	<form method="POST" action="index.php?action=newaccount"> 
	<table><tr><td> 
		<table width="400" style="border:1px #003399 solid;background-color:#000000" cellpadding="3"> 
			<tr> 
				<td align="center" class="c" colspan="2">Création d'un nouveau compte</td> 
			</tr><tr> 
				<th align="center">Nom :</th> 
				<th align="center"><input name="pseudo" type="text" maxlength="15" size="20"></th> 
			</tr><tr> 
				<th align="center">Mot de passe :</th> 
				<th align="center"><input name="pass" type="text" maxlength="15" size="20"></th> 
			</tr><tr> 
				<th align="center">Droits :</th> 
				<th align="center"> 
				<?php 
				if ($user_data["user_admin"] == 1) { 
					echo "\t"."co-administrateur : <select name='user_coadmin'><option value='1'>Oui</option><option value='0'selected='selected' $user_coadmin_off>Non</option></select><br>"."\n"; 
				} 
				if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) { 
					echo "\t"."Gestion des membres ".help('admin_member_manager')." : <select name='management_user'><option value='1'>Oui</option><option value='0'$management_user_off>Non</option></select><br>"."\n"; 
				} 
					echo "\t"."Gestion des classements ".help('admin_ranking_manager')." : <select name='management_ranking'><option value='1'>Oui</option><option value='0'$management_ranking_off>Non</option></select><br>"."\n";?> 
				</th> 
			</tr><tr> 
				<th align="center">Groupe : </th> 
				<th align="center"> 
					<select name="group_id"><?php 
						foreach ($usergroup_list as $value) { 
							echo "\t\t\t\t"."<option value='".$value["group_id"]."'>".$value["group_name"]."</option>"; 
						}?> 
					</select>
				</th> 
			</tr><tr> 
				<th align="center" colspan="2"> 
					<input type="submit" value=" &nbsp; &nbsp; ok &nbsp; &nbsp; "> 
					<input type="button" value="annuler" onclick="document.getElementById('new_member').style.visibility = 'hidden';"> 
				</th> 
			</tr> 
		</table> 
		</td></tr></table> 
	</form> 
</div> 
