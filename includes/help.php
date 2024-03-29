<?php

/**
 * Functions which informs the user about an item using a pop-up.
 * @package OGSpy
 * @subpackage Help
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04b ($Rev: 7688 $)
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Helper\ToolTip_Helper;

/**
 * help() creates a pop-up to display the help message on the mouse over.
 * @param string $key The Help message ID
 * @param string $value If the ID is not used, it is possible to use a custom message
 * @param string $prefixe Path to the OGSpy root (Not really used)
 * @return string The Html code to insert.
 */
function help($key, $value = null, $prefixe = "")
{
    global $lang;

    $tth = new ToolTip_Helper();
    $key = "help_" . $key;
    $value = ($value == null) ? "Aide Introuvable" : $value;
    $value = (isset($lang[$key])) ? $lang[$key] : $value; // On ecrase la variable si présente dans ogspy donc non custom

    $text  = '<table class="og-table og-mini-table">';
    $text .= '<thead><tr><th>Aide</th></tr></thead>';
    $text .= '<tbody><tr><td>' . ($value) . "</td></tr></tbody>";
    $text .= '</table>';

    $text = htmlentities($text);
    $tth->addTooltip($key, $text);
    return "<img style=\"cursor:pointer\" src=\"" . $prefixe . "images/help_2.png\" " . $tth->GetHTMLClassContent() . ">";
}
