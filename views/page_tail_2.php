<?php

/**
 * HTML Footer Light
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


use Ogsteam\Ogspy\Helper\ToolTip_Helper;
use Ogsteam\Ogspy\Helper\Benchmark_Helper;

$nb_requete = $db->nb_requete;
$nb_users = user_get_nb_active_users();
$db->sql_close(); // fermeture de la connexion à la base de données
$benchogspy->stop("fin");// Arret calcul temps

$ogspy_timing = $benchogspy->getAllElapsed(); // temps total
$sql_timing = $benchSQL->getAllElapsed(); //temps sql
$php_timing = $ogspy_timing - $sql_timing ; // delta => temps php



?>
</section><!-- Fin Contenu principal compat legacy -->
<?php
global $ogspy_phperror;
if (is_array($ogspy_phperror) && count($ogspy_phperror)) {
    echo "\n<tr>\n\t<td><table><tr><th>" . $lang['FOOTER_PHPERRORS'] . "</th></tr>";
    foreach ($ogspy_phperror as $line) {
        echo "\n<tr><td>$line</td></tr>";
    }
    echo "</table>\n\t</td>\n</tr>";
}
?>

<!--todo revoir footer de ce type de page -->
<footer id='barre'>
    <div class="footerbarre-version">
        <a href="https://www.ogsteam.eu" target="_blank" rel="noopener">OGSpy</a> <span class="og-highlight"><?php echo $server_config["version"] . "</span> " . $lang['FOOTER_OGSPY']; ?> OGSteam &copy; 2005-2023
    </div>
    <div class="footerbarre-countuser">
     </div>
    <div class="footerbarre-bench">
        <?php echo $lang['FOOTER_RENDERING'] . " <span class=\"og-highlight\">" . round($php_timing + $sql_timing, 3); ?></span> sec <span class="notviewlittle">(PHP : <span class="og-highlight"><?php echo round($php_timing, 3); ?></span> / SQL : <span class="og-highlight"><?php echo round($sql_timing, 3); ?></span>)
        [<?php echo ($nb_requete . " " . $lang['FOOTER_QUERY'] . (($nb_requete > 1) ? "s" : "")); ?>]</span>
    </div>
</footer>  <!-- fin pied de page footer html -->
<?php echo (new ToolTip_Helper())->GetHTMLHideContent(); ?>
</body>

</html>