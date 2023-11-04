<?php

/**
 * Spy Report Rendering
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

$reports = galaxy_reportspy_show();
$galaxy = $pub_galaxy;
$system = $pub_system;
$row = $pub_row;

if ($reports === false) {
    redirection('index.php?action=message&amp;id_message=errorfatal&amp;info');
}

$favorites = user_getfavorites_spy();

require_once('views/page_header_2.php');
?>
<div class="page_cartography">

    <?php if (sizeof($reports) == 0) : ?>
        <p><?php echo  $lang['REPORT_NOREPORTAVAILABLE']; ?></p>
        <script>
            window.opener.location.href = window.opener.location.href;
        </script>
    <?php else : ?>
        <?php foreach ($reports as $v) : ?>
            <?php $spy_id = $v['spy_id']; ?>
            <?php $sender = $v['sender']; ?>
            <?php if (sizeof($favorites) < $server_config['max_favorites_spy']) : ?>
                <?php $string_addfavorites = "window.location = 'index.php?action=add_favorite_spy&amp;spy_id=" . $spy_id . "&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "&amp;row=" . $row . "';"; ?>
            <?php else : ?>
                <?php $string_addfavorites = "alert('" . $lang['REPORT_MAXFAVORITES'] . " (" . $server_config['max_favorites_spy'] . ")')"; ?>
            <?php endif; ?>
            <?php $string_delfavorites = "window.location = 'index.php?action=del_favorite_spy&amp;spy_id=" . $spy_id . "&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "&amp;row=" . $row . "&amp;info=2';"; ?>
            <?php $string_delspy = "window.location.href = 'index.php?action=del_spy&amp;spy_id=" . $spy_id . "&amp;galaxy=" . $galaxy . "&amp;system=" . $system . "&amp;row=" . $row . "&amp;info=2';"; ?>
            <p><?php echo  $lang['REPORT_RESENTBY']; ?>
                <span class="og-highlight"><?php echo $sender; ?></span> <?php echo date($lang['REPORT_DATEFORMAT'], $v['dateRE']); ?>
            </p>
            <?php if (!isset($favorites[$spy_id])) : ?>
                <input class="og-button" type='button' value='<?php echo  $lang['REPORT_ADDTOFAV']; ?>' onclick="<?php echo $string_addfavorites; ?>">
            <?php else : ?>
                <input class="og-button og-button-warning" type='button' value='<?php echo $lang['REPORT_REMOVEFROMFAV']; ?>' onclick="<?php echo $string_delfavorites ?>">
            <?php endif; ?>
            <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) : ?>
                <input class="og-button og-button-danger" type='button' value='<?php echo $lang['REPORT_DELETE']; ?>' onclick="<?php echo $string_delspy; ?>">
            <?php endif; ?>
            <?php echo $v['data']; ?><br /><br />
            <hr />
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
require_once("views/page_tail_2.php");
