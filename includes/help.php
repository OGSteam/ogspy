<?php
/**
 * Functions which informs the user about an item using a pop-up.
 *
 * This function create a pop-up which informs the user about items in the user interface.
 * @package OGSpy
 * @subpackage Help
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @version 3.04b ($Rev: 7688 $)
 * @modified $Date: 2012-08-18 14:35:34 +0200 (Sat, 18 Aug 2012) $
 * @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/includes/help.php $
 * $Id: help.php 7688 2012-08-18 12:35:34Z darknoon $
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * help() creates a pop-up to display the help message on the mouse over.
 * @param string $key The Help message ID
 * @param string $value If the ID is not used, it is possible to use a custom message
 * @param string $prefixe Path to the OGSpy root (Not really used)
 * @return string The Html code to insert.
 */
function help($key, $value = "", $prefixe = "")
{
    global $help;

    if (isset($help[$key])) {
        $value = $help[$key];
    }else{
        $value = "Aide Introuvable";
    }

    $text = "<table width=\"200\">";
    $text .= '<tr><td class="c" style="text-align:center;">Aide</td></tr>';
    $text .= '<tr><th style="color:white; ">' . addslashes($value) . "</th></tr>";
    $text .= "</table>";

    if (version_compare(phpversion(), '5.4.0', '>=')) {
        $text = htmlentities($text, ENT_COMPAT | ENT_HTML401, "UTF-8");
    } else {
        $text = htmlentities($text, ENT_COMPAT, "UTF-8");
    }
    $text = "this.T_WIDTH=210;this.T_TEMP=0;return escape('" . $text . "')";

    return "<img style=\"cursor:pointer\" src=\"" . $prefixe . "images/help_2.png\" onmouseover=\"" . $text . "\">";
}
