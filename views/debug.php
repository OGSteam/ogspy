<?php
/***************************************************************************
 *    filename    : debug.php
 *    desc.        :
 *    Author        : Kyser - http://ogsteam.fr/
 *    created        : 21/11/2005
 *    modified    : 22/06/2006 00:13:20
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

// Liste des Variables de session
echo '<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=1 WIDTH=600 BGCOLOR="#000000" ALIGN="CENTER">';
echo '<tr bgcolor="#9999CC"><td class="c" colspan="2">Variables de session</td></tr>';
if (isset($HTTP_SESSION_VARS))
    foreach ($HTTP_SESSION_VARS as $key => $value) {
        if (is_array($value)) {
            echo "<tr bgcolor=\"#CCCCCC\"><td nowrap bgcolor=\"#CCCCFF\">$key</td><td>";
            foreach ($value as $inckey => $incval)
                echo "[$inckey]=>$incval<br />";
            echo '</td></tr>';
        } else
            echo "<tr bgcolor=\"#CCCCCC\"><td nowrap bgcolor=\"#CCCCFF\">$key</td><td>$value</td></tr>";
    }

// Liste des variables passées dans l'URL. NB : Il n'y a pas de gestion des tableaux dans ce cas
echo '<tr  bgcolor="#9999CC"><td class="c" colspan="2">Variables passées en URL</td></tr>';
if (isset($HTTP_GET_VARS))
    foreach ($HTTP_GET_VARS as $key => $value) {
        echo "<tr><th nowrap>$key</th><th>$value</td></th>";
    }

// Liste des variables transmises par formulaire
echo '<tr  bgcolor="#9999CC"><td class="c" colspan="2">Variables passées par formulaire</td></tr>';
if (isset($HTTP_POST_VARS))
    foreach ($HTTP_POST_VARS as $key => $value) {
        if (is_array($value)) {
            echo "<tr><th nowrap>$key</th><th>";
            foreach ($value as $inckey => $incval)
                echo "[$inckey]=>$incval<br />";
            echo '</th></tr>';
        } else
            echo "<tr><th nowrap>$key</td><th>$value</th></tr>";
    }
echo '</table>';
?>