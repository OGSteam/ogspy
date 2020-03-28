<?php
/**
 * HTML footer
 */
// deprécié
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
use Ogsteam\Ogspy\Helper\ToolTip_Helper;
?>





<?php
$php_end = benchmark();
$php_timing = $php_end - $php_start - $sql_timing;
$nb_requete = $db->nb_requete;
$nb_users = user_get_nb_active_users();
$db->sql_close(); // fermeture de la connexion à la base de données
?>


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


<!-- Place pour bas de pages -->



<div id="footbarre">
    <div class="toologs">
        <p>
            <a href="https://www.ogsteam.fr" target="_blank">OGSpy</a> <span class="version"><?php echo $server_config["version"] ?></span><?php echo $lang['FOOTER_OGSPY'] ?> OGSteam &copy; 2005-2020
        </p>
    </div>
    <div class="online">
        <p>
            <span class="nb_users"><?php echo $nb_users ?></span> <?php echo $lang['FOOTER_CONTRIBUTORS'] . (($nb_users > 1) ? "s" : "") . " " . $lang['FOOTER_ON_SITE'] ?>
        </p>
    </div>
    <div class="bench">
        <p>
            <?php echo $lang['FOOTER_RENDERING'] ;?> <span class="value"><?php echo round($php_timing + $sql_timing, 3); ?></span> sec (<span class="language">PHP</span> : <span class="value"><?php echo round($php_timing, 3); ?></span> / <span class=language">SQL</span> : <span class="value"><?php echo round($sql_timing, 3); ?></span>)
            [ <span class="value"><?php echo $nb_requete;?></span> <?php echo $lang['FOOTER_QUERY'] . (($nb_requete > 1) ? "s" : ""); ?>]
        </p>
    </div>


</div>


<?php echo (new ToolTip_Helper())->GetHTMLHideContent(); ?>
<?php echo (new ToolTip_Helper())->activateJs(); ?>

</div>
</div>
</body>
</html>