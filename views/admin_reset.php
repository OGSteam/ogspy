<?php global $lang, $user_data;
/**
 * Panneau administration : remise à zéro des données jeu
 * @package OGSpy
 * @subpackage views
 * @author OGSteam
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["admin"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}
?>

<table class="og-table og-full-table">
    <thead>
        <tr>
            <th colspan="2"><?= $lang['ADMIN_RESET_TITLE'] ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2">
                <p><?= $lang['ADMIN_RESET_WARNING'] ?></p>
                <ul>
                    <li><?= $lang['ADMIN_RESET_ITEM_MODS'] ?></li>
                    <li><?= $lang['ADMIN_RESET_ITEM_GAMEDATA'] ?></li>
                    <li><?= $lang['ADMIN_RESET_ITEM_STATS'] ?></li>
                </ul>
                <p><?= $lang['ADMIN_RESET_PRESERVED'] ?></p>
            </td>
        </tr>
    </tbody>
</table>

<form method="post" action="index.php?action=admin_reset"
      onsubmit="return confirm('<?= htmlspecialchars($lang['ADMIN_RESET_CONFIRM'], ENT_QUOTES, 'UTF-8') ?>')">
    <table class="og-table og-full-table">
        <tbody>
            <tr>
                <td>
                    <input class="og-button og-button-warning" type="submit"
                           value="<?= $lang['ADMIN_RESET_BUTTON'] ?>">
                </td>
            </tr>
        </tbody>
    </table>
</form>
