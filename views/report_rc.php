<?php

/**
 * Combat Report Rendering
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

$reports = galaxy_reportrc_show();
$galaxy  = $pub_galaxy;
$system  = $pub_system;
$row     = $pub_row;

if ($reports === false) {
    redirection('index.php?action=message&amp;id_message=errorfatal&amp;info');
}

require_once('views/page_header_2.php');
?>
<div class="page_report_rc">

    <?php if (sizeof($reports) == 0) : ?>
        <p><?php echo  $lang['REPORT_NOREPORTAVAILABLE']; ?></p>
        <script>
            window.opener.location.href = window.opener.location.href;
        </script>
    <?php else : ?>
        <?php foreach ($reports as $v) : ?>
            <?php echo nl2br($v); ?>
            <?php //echo $v; ?>
            <hr />
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once('views/page_tail_2.php');
