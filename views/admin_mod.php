<?php
/***************************************************************************
*	filename	: admin_mod.php
*	desc.		:
*	Author		: Aéris - http://ogsteam.fr/
*	created		:
*	modified	: 22/08/2006 00:00:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

$mod_list = mod_list();
?>
<table align="center">
	<tr><td>&nbsp;</td></tr>
	
	<tr><td class="c" colspan="6" width="550">Mods installés actifs</td></tr>
    	<tr><td>Mods Normaux</td><td colspan="4"></td><th>Vue du menu</th></tr>
<?php
$mods = $mod_list["actived"];
while ($mod = current($mods)) {
	if ($mod["admin_only"] == 0) {
    echo "\t"."<tr>";
    echo "<th width='200'>".$mod["title"]." (".$mod["version"].")</th>";
    echo "<th width='50'><a href='index.php?action=mod_up&mod_id=".$mod['id']."'><img src='images/asc.png' title='Monter'></a>&nbsp;<a href='index.php?action=mod_down&mod_id=".$mod['id']."'><img src='images/desc.png' title='Descendre'></a></th>";
    echo "<th width='100'><a href='index.php?action=mod_disable&mod_id=".$mod['id']."'>Désactiver</a></th>";
    echo "<th width='100'><a href='index.php?action=mod_uninstall&mod_id=".$mod['id']."'>Désinstaller</a></th>";
    echo "<th width='100'>";
    if (!$mod["up_to_date"]) {
        echo "<a href='index.php?action=mod_update&mod_id=".$mod['id']."'>Mettre à jour</a>";
    }
    echo "</th>";    
        echo "<th width='100'><a href='index.php?action=mod_admin&mod_id=".$mod['id']."'>Normal</a></th>";
    echo "</tr>";
    echo "\n";
	

	}
	next($mods);
}
	echo"<tr><td>Mods Admins</td><td colspan='5'></td></tr>";
$mods = $mod_list["actived"];
while ($mod = current($mods)) {
if ($mod["admin_only"] == 1) {
    echo "\t"."<tr>";
    echo "<th width='200'>".$mod["title"]." (".$mod["version"].")</th>";
    echo "<th width='50'><a href='index.php?action=mod_up&mod_id=".$mod['id']."'><img src='images/asc.png' title='Monter'></a>&nbsp;<a href='index.php?action=mod_down&mod_id=".$mod['id']."'><img src='images/desc.png' title='Descendre'></a></th>";
    echo "<th width='100'><a href='index.php?action=mod_disable&mod_id=".$mod['id']."'>Désactiver</a></th>";
    echo "<th width='100'><a href='index.php?action=mod_uninstall&mod_id=".$mod['id']."'>Désinstaller</a></th>";
    echo "<th width='100'>";
    if (!$mod["up_to_date"]) {
        echo "<a href='index.php?action=mod_update&mod_id=".$mod['id']."'>Mettre à jour</a>";
    }
    echo "</th>";    
        echo "<th width='100'><a href='index.php?action=mod_normal&mod_id=".$mod['id']."'>Admin</a></th>";
    echo "</tr>";
    echo "\n";

    
	}
	next($mods);
}
?>
	<tr><td>&nbsp;</td></tr>
	
	<tr><td class="c" colspan="6" width="550">Mods installés inactifs</td></tr>
<?php
$mods = $mod_list["disabled"];
while ($mod = current($mods)) {
	echo "\t"."<tr>";
	echo "<th width='250' colspan='2'>".$mod["title"]." (".$mod["version"].")</th>";
	echo "<th width='100'><a href='index.php?action=mod_active&mod_id=".$mod['id']."'>Activer</a></th>";
	echo "<th width='100'><a href='index.php?action=mod_uninstall&mod_id=".$mod['id']."'>Désinstaller</a></th>";
	if (!$mod["up_to_date"]) {
		echo "<th width='100'><a href='index.php?action=mod_update&mod_id=".$mod['id']."'>Mettre à jour</a></th>";
	}
	else echo "<th width='100'>&nbsp;</th>";
	echo "<th width='100'>&nbsp;</th>";
	echo "</tr>";
	echo "\n";

	next($mods);
}
?>
	<tr><td>&nbsp;</td></tr>
	
	<tr><td class="c" colspan="6" width="550">Mods non installés</td></tr>
<?php
$mods = $mod_list["install"];
while ($mod = current($mods)) {
	echo "\t"."<tr>";
	echo "<th width='200'>".$mod["title"]."</th>";
	echo "<th width='300' colspan='5'><a href='index.php?action=mod_install&directory=".$mod['directory']."'>Installer</a></th>";
	echo "</tr>";
	echo "\n";

	next($mods);
}
?>
	<tr><td>&nbsp;</td></tr>
	
	<tr><td class="c" colspan="6" width="550">Mods invalides</td></tr>
<?php
$mods = $mod_list["wrong"];
while ($mod = current($mods)) {
	echo "\t"."<tr>";
	echo "<th width='200'>".$mod["title"]."</th>";
	echo "<th width='300' colspan='5'><a href='index.php?action=mod_uninstall&mod_id=".$mod['id']."'>Désinstaller</a></th>";
	echo "</tr>";
	echo "\n";

	next($mods);
}
?>
</table>
