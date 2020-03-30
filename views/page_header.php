<?php
/**
 * HTML Header
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
use Ogsteam\Ogspy\Helper\ToolTip_Helper;

require_once("views/page_head.php");
require_once("views/page_menu.php");

global $classcontenttag;


?>

<?php if (isset($classcontenttag)) : ?>
    <div class="content <?php echo $classcontenttag?>">
<?php else : ?>
    <div class="content">
<?php endif ; ?>




