<?php
/***************************************************************************
*	filename	: home.php
*	desc.		:
*	Author		: Kyser - http://ogsteam.fr/
*	created		: 17/12/2005
*	modified	: 30/07/2006 00:00:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

require_once("views/page_header.php");
?>

<table width="100%">
<tr>
	<td>
		<table width="100%">
		<tr align="center">
<?php
if (!isset($pub_subaction)) $pub_subaction = "empire";

if ($pub_subaction != "empire") {
	echo "\t\t\t"."<td class='c' width='25%' onclick=\"window.location = 'index.php?action=home&subaction=empire';\">";
	echo "<a style='cursor:pointer'><font color='lime'>Empire</font></a>";
	echo "</td>";
}
else {
	echo "\t\t\t"."<th width='25%'>";
	echo "<a>Empire</a>";
	echo "</th>";
}

if ($pub_subaction != "simulation") {
	echo "\t\t\t"."<td class='c' width='25%' onclick=\"window.location = 'index.php?action=home&subaction=simulation';\">";
	echo "<a style='cursor:pointer'><font color='lime'>Simulation</font></a>";
	echo "</td>";
}
else {
	echo "\t\t\t"."<th width='150'>";
	echo "<a>Simulation</a>";
	echo "</th>";
}

if ($pub_subaction != "spy") {
	echo "\t\t\t"."<td class='c' width='25%' onclick=\"window.location = 'index.php?action=home&subaction=spy';\">";
	echo "<a style='cursor:pointer'><font color='lime'>Rapports d'espionnage</font></a>";
	echo "</td>";
}
else {
	echo "\t\t\t"."<th width='25%'>";
	echo "<a>Rapports d'espionnage</a>";
	echo "</th>";
}

if ($pub_subaction != "stat") {
	echo "\t\t\t"."<td class='c_stats' width='25%' onclick=\"window.location = 'index.php?action=home&subaction=stat';\">";
	echo "<a style='cursor:pointer'><font color='lime'>Statistiques</font></a>";
	echo "</td>";
}
else {
	echo "\t\t\t"."<th width='25%'>";
	echo "<a>Statistiques</a>";
	echo "</th>";
}
?>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
<?php
switch ($pub_subaction) {
	case "empire" :
	require_once("home_empire.php");
	break;

	case "simulation" :
	require_once("home_simulation.php");
	break;

	case "stat" :
	require_once("home_stat.php");
	break;
	
	case "spy" :
	require_once("home_spy.php");
	break;

	default:
	require_once("home_empire.php");
	break;
}
?>
	</td>
</tr>
</table>

<?php
require_once("views/page_tail.php");
?>