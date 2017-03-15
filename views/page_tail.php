<?php
/**
 * HTML Footer
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace Ogsteam\Ogspy;

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
    echo "\n<tr>\n\t<td><table><tr><th>".$lang['FOOTER_PHPERRORS']."</th></tr>";
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
                <a style="font-weight:bold;font-size: 12px" href="https://www.ogsteam.fr" target="_blank">OGSpy</a> <?php echo $server_config["version"]." ".$lang['FOOTER_OGSPY']; ?> OGSteam &copy; 2005-2017<br/>
            </td>
            <td style="width:34%;text-align:center;font-size:11px;font-style:italic;font-weight:bold;"><?php echo("<span id='nb_users'>" . $nb_users . "</span> ".$lang['FOOTER_CONTRIBUTORS']. (($nb_users > 1) ? "s" : "") . " ".$lang['FOOTER_ON_SITE'] ); ?></td>
            <td style="width:33%;text-align:right;font-size:11px;font-style:italic">
                 <?php echo $lang['FOOTER_RENDERING']." ".round($php_timing + $sql_timing, 3);?> sec (<span
                    style="font-weight:bold;">PHP</span> : <?php echo round($php_timing, 3);?> / <span
                    style="font-weight:bold;">SQL</span> : <?php echo round($sql_timing, 3);?>)
                [<?php echo ($nb_requete." ".$lang['FOOTER_QUERY'].(($nb_requete > 1) ? "s" : "")); ?>]
            </td>
        </tr>
    </table>

</div>
<script type="text/javascript" src="assets/js/wz_tooltip.js"></script>

</body>
</html>