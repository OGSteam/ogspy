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
    redirection("index.php?action=message&id_message=forbidden&info");
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
                    <label for="reset_confirm_input"><?= $lang['ADMIN_RESET_TYPE_LABEL'] ?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" id="reset_confirm_input" name="reset_confirm"
                           placeholder="<?= htmlspecialchars($lang['ADMIN_RESET_TYPE_PLACEHOLDER'], ENT_QUOTES, 'UTF-8') ?>"
                           autocomplete="off"
                           oninput="document.getElementById('reset_submit_btn').disabled = (this.value !== '<?= htmlspecialchars($lang['ADMIN_RESET_TYPE_KEYWORD'], ENT_QUOTES, 'UTF-8') ?>');">
                </td>
            </tr>
            <tr>
                <td>
                    <input id="reset_submit_btn" class="og-button og-button-warning" type="submit"
                           value="<?= $lang['ADMIN_RESET_BUTTON'] ?>" disabled>
                </td>
            </tr>
        </tbody>
    </table>
</form>
