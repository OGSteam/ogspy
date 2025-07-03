<?php global $lang;
/**
 * Page Login
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

if (!isset($goto)) {
    $goto = "";
}
$enable_register_view = isset($server_config['enable_register_view']) ? $server_config['enable_register_view'] : 0;

require_once 'views/page_header_2.php';
?>

<div class="page_login">

    <div class="login_panel">
        <div class="og-logo">
            <img alt="Logo OGSpy" src="./skin/OGSpy_skin/logos/logo.png">
        </div>
        <div class="og-login-header">
            <h2><?php echo $lang['LOGIN_CONNEXION_PARAMETERS'] ?></h2>
        </div>
        <form method='post' action='' >
            <input name="action" type="hidden" value="member_modify_member">

            <p><input type='hidden' name='action' value='login_web' />
                <input type='hidden' name='token' value='<?php echo token::staticGetToken(600, "login"); ?>' />
                <input type='hidden' name='goto' value='<?php echo $goto; ?>' />
            </p>
            <div class="og-login-group">
                <label for="login" class="control-label"><?php echo $lang['LOGIN_USER']; ?>:</label>
                <input type='text'  name='login' />
            </div>
            <div class="og-login-group">
                <label for="password" class="control-label"><?php echo $lang['LOGIN_PASSWORD']; ?>:</label>
                <input type='password' name='password' />
            </div>

            <input class="og-button"  type='submit' value='<?= $lang['LOGIN_CONNEXION_BUTTON'] ?>' />

        </form>
        <hr/>
        <div class="og-login-header">
            <h2><?php echo $lang['LOGIN_ACCOUNT_REQUEST'] ?></h2>
        </div>
        <div class="og-login-group">
                  <p>
            <?php echo $lang['LOGIN_ACCOUNT_REQUEST_DESC']; ?>
        </p>
        </div>


        <input class="og-button"  type="button" value="<?= $lang['LOGIN_ACCOUNT_REQUEST_BUTTON'] ?>" onclick="window.open('<?= $server_config['register_forum'] ?>');" />

    </div>

<?php require_once 'views/page_tail_2.php'; ?>
