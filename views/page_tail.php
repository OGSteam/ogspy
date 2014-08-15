<?php
/**
* page_tail.php :  Footer des pages OGSpy
* @author Kyser - http://ogsteam.fr/
* @created  08/12/2005
* @package OGSpy
* @subpackage main
*/
if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

$php_end = benchmark();
$php_timing = $php_end - $php_start - $sql_timing;
$nb_requete = $db->nb_requete;
$db->sql_close(); // fermeture de la connexion à la base de données 
?>
	</td>
</tr>
<?php
global $ogspy_phperror;
if (is_array($ogspy_phperror) && count($ogspy_phperror)) {
	echo "\n<tr>\n\t<td><table><tr><th>Erreurs php</th></tr>";
	foreach($ogspy_phperror as $line) {
		echo "\n<tr><td>$line</td></tr>";
	}
	echo "</table>\n\t</td>\n</tr>";
}
?>
<tr>
	<td>
		<center>
			<font size="2">
				<i><b><a href="http://www.ogsteam.fr" target="_blank">OGSpy</a></b> is an <b>OGSteam Software</b> &copy; 2005-2013</i><br />v <?php echo $server_config["version"];?><br />
			</font>
			<font size="1">
				<i>Temps de génération <?php echo round($php_timing+$sql_timing, 3);?> sec (<b>PHP</b> : <?php echo round($php_timing, 3);?> / <b>SQL</b> : <?php echo round($sql_timing, 3);?>) [<?php echo $nb_requete;?> requéte(s)]</i>
			</font>
		</center>
	</td>
</tr>
</table>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"> </script>
<script language="JavaScript" src="js/wz_tooltip.js"></script>

</body>
</html>
