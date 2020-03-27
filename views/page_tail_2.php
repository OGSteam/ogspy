<?php
/**
 * HTML footer
 */
// deprécié
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

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



<?php
// echo (new ToolTip_Helper())->GetHTMLHideContent();   //non utilisé pour tail 2
// echo (new ToolTip_Helper())->activateJs();
?>

</div>
</body>
</html>