<?php
/**
 * HTML footer
 */
// deprécié
if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

global $ogspy_phperror;

if(is_array($ogspy_phperror) && count($ogspy_phperror)) {
    echo "\t<tr><td>\n\t<table><tr><th>" . $lang['FOOTER_PHPERRORS'] . "</th></tr>";
    foreach ($ogspy_phperror as $line) {
        echo "\n<tr><td>$line</td></tr>";
    }
    echo "</table>\n\t</td>\n</tr>";
}
?>


</div>
<footer id="footbarre">
    <p class="toologs">
        <a href="https://www.ogsteam.fr/" target="_blank">OGSpy</a><span class="version"> <?php echo $server_config['version']; ?> </span><?php echo $lang['FOOTER_OGSPY']; ?> OGSteam &copy; 2005-2020
    </p>
</footer>
</div>
</body>
</html>