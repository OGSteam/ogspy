<?php
/**
 * Page Login
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die('Hacking attempt');
}

require_once('views/page_header_2.php');

if (!isset($goto)) {
    $goto = "";
}
$enable_register_view = isset ($server_config['enable_register_view']) ? $server_config['enable_register_view'] : 0;
?>

    <form method='post' class="login littlebox">
        <p class="legend"><?php echo($lang['LOGIN_CONNEXION_PARAMETERS']); ?></p>
        <input type='hidden' name='action' value='login_web'/>
        <input type='hidden' name='token' value='<?php echo token::staticGetToken(600,'login');?>'/>
        <input type='hidden' name='goto' value='<?php echo $goto; ?>'/>
        <div>
            <label for="login"><?php echo($lang['LOGIN_USER']); ?></label>
            <input type='text' name='login' id='login'/>
        </div>
        <div>
            <label for="password"><?php echo($lang['LOGIN_PASSWORD']); ?></label>
            <input type='password' id='password' name='password'/>
        </div>
        <div>
            <label></label>
            <input class="button" type='submit' value='<?php echo($lang['LOGIN_CONNEXION_BUTTON']); ?>'/>
        </div>

        <?php if ($enable_register_view != 1) : ?>
            <div class="sep"></div>
                <p><?php echo($lang['LOGIN_ACCOUNT_REQUEST']); ?></p>
                <p>
                    <?php echo($lang['LOGIN_ACCOUNT_REQUEST_DESC']); ?>
                </p>
                <button type="button" class="button else" onclick="window.open('<?php echo $server_config['register_forum']; ?>');">
                    <?php echo($lang['LOGIN_ACCOUNT_REQUEST_BUTTON']); ?>
                </button>
        <?php endif; ?>
        <div>
        </div>
    </form>




<?php require_once('views/page_tail_2.php'); ?>