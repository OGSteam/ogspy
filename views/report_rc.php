<?php
/**
 * Combat Report Rendering
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

$reports = galaxy_reportrc_show();
$galaxy = $pub_galaxy;
$system = $pub_system;
$row = $pub_row;

if ($reports === false) {
    redirection("index.php?action=message&amp;id_message=errorfatal&amp;info");
}

require_once("views/page_header_2.php");
if (sizeof($reports) == 0) {
    echo "<p>" . $lang['REPORT_NOREPORTAVAILABLE'] . "</p>";
    echo '<script language="javascript">window.opener.location.href=window.opener.location.href;</script>';
} else {
    foreach ($reports as $v) {
        echo "<table align='center'>" . "\n";
        echo "<tr><td class='c'>" . nl2br($v) . "</td></tr>" . "\n";
        echo "</table>";
        echo "<br>";
    }
}
require_once("views/page_tail_2.php");
