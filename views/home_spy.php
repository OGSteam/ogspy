<?php
/***************************************************************************
*	filename	: home_spy.php
*	desc.		:
*	Author		: Ben.12 - http://ogsteam.fr/
*	created		: 19/01/2006
*	modified	: 22/06/2006 00:13:20
*	modified	: 30/07/2006 00:00:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

//$nb_del_spy = user_del_old_favorite_spy();
$favorites = user_getfavorites_spy();

if (!isset($sort2)) $sort2 = 0;
else $sort2 = $sort2 != 0 ? 0 : 1;
?>

<table align="center"><tr>
<td class="c" width="75"><a href="index.php?action=home&subaction=spy&sort=1&sort2=<?php echo $sort2;?>">Positions</a></td>
<td class="c" width="120"><a href="index.php?action=home&subaction=spy&sort=2&sort2=<?php echo $sort2;?>">Alliances</a></td>
<td class="c" width="120"><a href="index.php?action=home&subaction=spy&sort=3&sort2=<?php echo $sort2;?>">Joueurs</a></td>
<td class="c" width="20"><a href="index.php?action=home&subaction=spy&sort=4&sort2=<?php echo $sort2;?>">Lune</a></td>
<td class="c" width="20">&nbsp;</td>
<td class="c" width="250"><a href="index.php?action=home&subaction=spy&sort=5&sort2=<?php echo $sort2;?>">Mises à jour</a></td>
<td class="c" width="40">&nbsp;</td>
<td class="c" width="120">&nbsp;</td></tr>
<?php
foreach ($favorites as $v) {
	$spy_id = $v["spy_id"];
	$galaxy = $v["spy_galaxy"];
	$system = $v["spy_system"];
	$row = $v["spy_row"];
	$player = $v["player"];
	$ally = $v["ally"];
	$moon = $v["moon"];
	$status = $v["status"];
	$timestamp = $v["datadate"];

	if ($timestamp != 0) {
		$timestamp = strftime("%d %b %Y %H:%M", $timestamp);
		$poster = $timestamp." - ".$v["poster"];
	}

	if ($ally == "") $ally = "&nbsp;";
	else $ally = "<a href='index.php?action=search&type_search=ally&string_search=".$ally."&strict=on'>".$ally."</a>";

	if ($player == "") $player = "&nbsp;";
	else $player = "<a href='index.php?action=search&type_search=player&string_search=".$player."&strict=on'>".$player."</a>";

	if ($status == "") $status = " &nbsp;";

	if ($moon == 1) $moon = " M";
	else $moon = "&nbsp;";

	echo "<tr>";
	echo "<th>$galaxy:$system:$row</th>";
	echo "<th>$ally</th>";
	echo "<th>$player</th>";
	echo "<th>$moon</th>";
	echo "<th>$status</th>";
	echo "<th>$poster</th>";
	$coords = explode(":",$row);
	echo "<th><input type='button' value='Voir' onclick=\"window.open('index.php?action=show_reportspy&galaxy=$galaxy&system=$system&row=$row&spy_id=$spy_id','_blank','width=640, height=480, toolbar=0, location=0, directories=0, status=0, scrollbars=1, resizable=1, copyhistory=0, menuBar=0');return(false)\" /></th>";
	echo "<th><input type='button' value='Supprimer des favoris' onclick=\"window.location = 'index.php?action=del_favorite_spy&spy_id=$spy_id&info=1';\" /></th>";
	echo "</tr>";
}
?>
</table>