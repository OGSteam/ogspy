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
$nb_users = user_get_nb_active_users();
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
</table>
<div id='barre'>
    <table style="width:100%">
        <tr><td></td></tr></td></tr></td></tr> <!-- Au cas où...-->
        <tr>
            <td style="width:33%;text-align:left;font-size: 11px;">
                <i><b><a href="http://www.ogsteam.fr" target="_blank">OGSpy</a></b> <?php echo $server_config["version"];?> est un <b>logiciel OGSteam</b> &copy; 2005-2015</i><br />
            </td>
             <td style="width:34%;text-align:center;font-size:11px;font-style:italic;font-weight:bold;"><?php echo("<span id='nb_users'>".$nb_users."</span> contributeur".(($nb_users>1) ? "s":"")." sur le site"); ?></td>
             <td style="width:33%;text-align:right;font-size:11px;">
				<i>Temps de génération <?php echo round($php_timing+$sql_timing, 3);?> sec (<b>PHP</b> : <?php echo round($php_timing, 3);?> / <b>SQL</b> : <?php echo round($sql_timing, 3);?>) [<?php echo $nb_requete;?> requête(s)]</i>
            </td>
        </tr>
    </table>

</div>
<script src="js/jquery.js" type="text/javascript"> </script>
<script language="JavaScript" src="js/wz_tooltip.js"></script>

</body>
</html>
