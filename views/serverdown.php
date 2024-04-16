<?php

/**
 * Server Down Page
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

$reason = $server_config["reason"];
require_once("views/page_header.php");
?>



<div class="page_message">
    <div class="og-msg og-msg-danger">
        <h3 class="og-title"><?php  echo ($lang['SERVERDOWN_TITLE']);?></h3>
        <p class="og-content"><?php echo $reason; ?></p>
    </div>
</div>


<?php
require_once("views/page_tail.php");
