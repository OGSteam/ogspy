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
    foreach ($ogspy_phperror as $line) {
        echo "\n<tr><td>$line</td></tr>";
    }
    echo "</table>\n\t</td>\n</tr>";
}
?>
</table>
<table style="height:30px;">
    <tr>
        <td></td>
    </tr>
</table> <!-- Place pour bas de pages -->
<div id='barre'>
    <table style="width:100%">
        <tr>
            <td></td>
        </tr>
        <tr>
            <td style="width:33%;text-align:left;font-size:11px;font-style:italic">
                <a style="font-weight:bold;" href="http://www.ogsteam.fr"
                   target="_blank">OGSpy</a> <?php echo $server_config["version"];?> est un <span
                    style="font-weight:bold;">OGSteam Software</span> &copy; 2005-2016<br/>
            </td>
            <td style="width:34%;text-align:center;font-size:11px;font-style:italic;font-weight:bold;"><?php echo("<span id='nb_users'>" . $nb_users . "</span> contributeur" . (($nb_users > 1) ? "s" : "") . " sur le site"); ?></td>
            <td style="width:33%;text-align:right;font-size:11px;font-style:italic">
                Temps de génération <?php echo round($php_timing + $sql_timing, 3);?> sec (<span
                    style="font-weight:bold;">PHP</span> : <?php echo round($php_timing, 3);?> / <span
                    style="font-weight:bold;">SQL</span> : <?php echo round($sql_timing, 3);?>)
                [<?php echo $nb_requete;?> requête<?php echo(($nb_requete > 1) ? "s" : ""); ?>]
            </td>
        </tr>
    </table>

</div>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/wz_tooltip.js"></script>

</body>
</html>