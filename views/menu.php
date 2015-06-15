<?php
/***************************************************************************
*	filename	: menu.php
*	desc.		:
*	Author		: Kyser - http://ogsteam.fr/
*	created		: 08/12/2005
*	modified	: 22/08/2006 00:00:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

$items = array();
if (($user_auth["server_set_system"] == 1 && $user_auth["server_set_spy"] == 1) || $user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
	$items[] = array("basic", "S. Solaire, R. Espionnage");
}
elseif ($user_auth["server_set_system"] == 1) {
	$items[] = array("basic", "S. solaire");
}
elseif ($user_auth["server_set_spy"] == 1) {
	$items[] = array("basic", "R. Espionnage");
}
if ($user_auth["server_set_rc"] == 1 || $user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
	$items[] = array("combat_report", "R. Combat");
}

if ($user_auth["server_set_ranking"] == 1 || $user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
	$items[] = array("none", "");
	$items[] = array("none", "----Classement joueur----");
	$items[] = array("general_player", "-> Général");
	$items[] = array("fleet_player", "-> Flotte");
	$items[] = array("research_player", "-> Recherche");
	$items[] = array("none", "");
	$items[] = array("none", "---Classement alliance---");
	$items[] = array("general_ally", "-> Général");
	$items[] = array("fleet_ally", "-> Flotte");
	$items[] = array("research_ally", "-> Recherche");
}

?>

<script type="text/javascript">
var date = new Date;
var delta = Math.round((<?php echo (time() * 1000);?> - date.getTime()) / 1000);
function Timer() {
	var days = new Array ("Dim","Lun","Mar","Mer","Jeu","Ven","Sam");
	var months = new Array ("Jan","Fév","Mar","Avr","Mai","Jui","Jui","Aoû","Sep","oct","nov","déc");

	date = new Date;
	date.setTime(date.getTime() + delta*1000);
	var hour = date.getHours();
	var min = date.getMinutes();
	var sec = date.getSeconds();
	var day = days[date.getDay()];
	var day_number = date.getDate();
	var month = months[date.getMonth()];
	if (sec < 10) sec = "0" + sec;
	if (min < 10) min = "0" + min;
	if (hour < 10) hour = "0" + hour;

	var datetime = day + " " + day_number + " " + month + " " + hour + ":" + min + ":" + sec;

	if (document.getElementById) {
		document.getElementById("datetime").innerHTML = datetime;
	}
}

go_visibility = new Array;
function goblink() {
	if(document.getElementById && document.all)
	{
		blink_tab = document.getElementsByTagName('blink');
		for(a=0;a<blink_tab.length;a++) {
			if(go_visibility[a] != "visible")
			go_visibility[a] = "visible";
			else
			go_visibility[a] = "hidden";
			blink_tab[a].style.visibility=go_visibility[a];
		}
	}
}

function clear_box() {
	if (document.post.data.value == "Système solaire & Rapport espionnage & Classement") {
		document.post.data.value = "";
	}
}

function Biper() {
	Timer();
	goblink();

	setTimeout("Biper()", 1000);
}

window.onload = Biper;
</script>

<table border="0" cellpadding="0" cellspacing="0">
	<tr align="center">
		<td>
			<b>Heure serveur</b><br />
			<span id="datetime"><blink>En attente</blink></span>
		</td>
	</tr>
	
	<tr>
		<td>
			<div><a href="index.php" class="menu"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="65" border="0"/></a></div>
		</td>
	</tr>
	
	<?php
	
	if ($server_config["server_active"] == 0) {
		echo "<tr>\n";
		echo "\t"."<td><div align='center'><font color='red'><b><blink>Serveur hors-ligne</blink></b></font></div></td>\n";
		echo "</tr>\n";
	}
	
	if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) {
		echo "<tr>";
		echo "<td><div align='center'><a href='index.php?action=administration' class='menu0'><img src='".$link_css."/transpa.gif' width='166' height='19'></a></div></td>";
		echo "</tr>";
	}
	
	?>
	
	<tr>
		<td><div align="center"><a href="index.php?action=profile" class="menu1"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=home" class="menu2"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><img src="<?php echo $link_css;?>/transpa.gif" width="0" height="17"></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=galaxy" class="menu3"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=cartography" class="menu4"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=search" class="menu5"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=ranking" class="menu6"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><img src="<?php echo $link_css;?>/transpa.gif" width="0" height="17"></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=statistic" class="menu7"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=galaxy_obsolete" class="menu8"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><img src="<?php echo $link_css;?>/transpa.gif" width="0" height="17"></div></td>
	</tr>
	<tr>
		<td><div align="center"><img src="<?php echo $link_css;?>/menu/mods.png" width="166" height="19"></div></td>
	</tr>
	
	<!-- Emplacement mod /-->
	
	<?php
	
	if (ratio_is_ok()) {
		$request = "select action, menu from ".TABLE_MOD." where active = 1 and `admin_only` = '0' order by position, title";
		$result = $db->sql_query($request);
		
		if ($db->sql_numrows($result)) {
			while ($val = $db->sql_fetch_assoc($result)) {
				echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a class="menu_mods" href="index.php?action='.$val['action'].'">'.$val['menu'].'</a></td></tr>'."\n";
			}
		}
	}
	else {
		echo '<tr><td>- <font color="red">Mods<br />inaccessibles&nbsp;'.help("ratio_block").'</font></td></tr>'."\n";
	}
	
	?>
	
	<!-- Fin des mods /-->
	
	<!-- Emplacement mod  admin/-->
	
	<?php
	
	if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
		$request = "select action, menu from ".TABLE_MOD." where active = 1 and `admin_only` = '1' order by position, title";
		$result = $db->sql_query($request);
		
		if ($db->sql_numrows($result)) {
			echo '<tr><td><div align="center"><img src="'.$link_css.'transpa.gif" width="110" height="12"></div></td></tr>'."\n";
			
			while ($val = $db->sql_fetch_assoc($result)) {
				echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <a class="menu_mods" href="index.php?action='.$val['action'].'">'.$val['menu'].'</a></td></tr>'."\n";
			}
		}
	}
	
	?>
	
	<!-- Fin des mods  admin/-->
	
	<tr>
		<td><div align="center"><img src="<?php echo $link_css;?>/transpa.gif" width="0" height="17"></div></td>
	</tr>
	<tr>
		<td><div align="center"><a href="index.php?action=logout" class="menu10"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	<tr>
		<td><div align="center"><img src="<?php echo $link_css;?>/transpa.gif" width="0" height="17"></div></td>
	</tr>
	
	<?php
	
	if ($server_config["url_forum"] != "") {
	
	?>
	
	<tr>
		<td><div align="center"><a href="<?php echo $server_config["url_forum"];?>" target="_blank" class="menu11"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
	
	<?php
	
	}
	
	?>
	
	<tr>
		<td><div align="center"><a href="index.php?action=about" class="menu12"><img src="<?php echo $link_css;?>/transpa.gif" width="166" height="19"></a></div></td>
	</tr>
</table>
