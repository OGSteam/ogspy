<?php global $benchogspy, $db, $lang, $server_config;

/**
 * HTML Footer
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
$benchogspy->stop("fin"); // Arret calcul temps

$ogspy_timing = $benchogspy->getAllElapsed(); // temps total
$sql_timing = $benchSQL->getAllElapsed(); //temps sql
$php_timing = $ogspy_timing - $sql_timing; // delta => temps php

?>
</section><!-- Fin Contenu principal compat legacy -->

<footer id='barre'>
    <div class="footerbarre-version">
        <a href="https://www.ogsteam.eu" target="_blank" rel="noopener">OGSpy</a> <span class="og-highlight"><?php echo $server_config["version"] . "</span> " . $lang['FOOTER_OGSPY']; ?> OGSteam &copy; 2005-2025
    </div>
    <div class="footerbarre-countuser">
        <?php echo ("<span class='og-highlight ' id='nb_users '>" . $nb_users . "</span> " . $lang['FOOTER_CONTRIBUTORS'] . (($nb_users > 1) ? "s" : "") . " " . $lang['FOOTER_ON_SITE']); ?>
    </div>
    <div class="footerbarre-bench">
        <?php echo $lang['FOOTER_RENDERING'] . " <span class=\"og-highlight\">" . round($php_timing + $sql_timing, 3); ?></span> sec <span class="notviewlittle">(PHP : <span class="og-highlight"><?php echo round($php_timing, 3); ?></span> / SQL : <span class="og-highlight"><?php echo round($sql_timing, 3); ?></span>)
        [<?php echo ($nb_requete . " " . $lang['FOOTER_QUERY'] . (($nb_requete > 1) ? "s" : "")); ?>]</span>
    </div>
</footer>
<!-- fin pied de page footer html -->

<?php echo (new ToolTip_Helper())->GetHTMLHideContent(); ?>
</body>

</html>

<?php ob_end_flush(); ?>
