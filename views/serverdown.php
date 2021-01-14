<?php
/**
 * Server Down Page
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */


if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$reason = $server_config["reason"];
require_once("views/page_header.php");
?>

    <table width="500" align="center">
        <tr>
            <td class="c"><?php echo($lang['SERVERDOWN_TITLE']); ?></td>
        </tr>
        <tr>
            <th><span style="color: red; "><?php echo $reason; ?></span></th>
        </tr>
    </table>

<?php
require_once("views/page_tail.php");
?>