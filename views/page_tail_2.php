<?php
/**
 * HTML Footer Light
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$php_end = benchmark();
$php_timing = $php_end - $php_start - $sql_timing;
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

<tr>
    <td style="color: #ECFF00; text-align:center; font-size:13px; font-style:italic">
        <a style="font-weight:bold;" href="http://www.ogsteam.fr">OGSpy</a> is an <span style="font-weight:bold;">OGSteam Software</span> &copy;
        2005-2016<br/>
        <span style="font-style:normal;">v <?php echo $server_config["version"];?></span><br/>
        <?php echo($lang['FOOTER_RENDERING']); ?> <?php echo round($php_timing + $sql_timing, 3);?> sec (<span
            style="font-weight:bold;">PHP</span> : <?php echo round($php_timing, 3);?> / <span
            style="font-weight:bold;">SQL</span> : <?php echo round($sql_timing, 3);?>)<br/>
    </td>
</tr>
</table>
</body>
</html>
