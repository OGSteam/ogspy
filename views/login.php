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
    die("Hacking attempt");
}

require_once("views/page_header_2.php");

if (!isset($goto)) {
    $goto = "";
}
$enable_register_view = isset ($server_config['enable_register_view']) ? $server_config['enable_register_view'] : 0;
?>

<form style="margin-bottom:40px;" method='post' action=''>
    <p><input type='hidden' name='action' value='login_web'/>
       <input type='hidden' name='token' value='<?php echo token::staticGetToken(600,"login");?>'/>
       <input type='hidden' name='goto' value='<?php echo $goto; ?>'/></p>

    <table style="margin:0 auto; padding:0; border-collapse:separate; border-spacing:1px">
        <tr>
            <td class="c" colspan="2" style="text-align:left"><?php echo($lang['LOGIN_CONNEXION_PARAMETERS']); ?></td>
        </tr>
        <tr>
            <th style="width:150px"><?php echo($lang['LOGIN_USER']); ?></th>
            <th style="width:150px"><input type='text' name='login'/></th>
        </tr>
        <tr>
            <th style="width:150px"><?php echo($lang['LOGIN_PASSWORD']); ?></th>
            <th style="width:150px"><input type='password' name='password'/></th>
        </tr>
        <tr>
            <th colspan='2'><input type='submit' value='<?php echo($lang['LOGIN_CONNEXION_BUTTON']); ?>'/></th>
        </tr>
        <?php

        if ($enable_register_view == 1) {

            ?>
            <tr>
                <td class="c" colspan="2" style="text-align:left"><?php echo($lang['LOGIN_ACCOUNT_REQUEST']); ?></td>
            </tr>
            <tr>
                <th colspan='2'><?php echo($lang['LOGIN_ACCOUNT_REQUEST_DESC']); ?>
                </th>
            </tr>
            <tr>
                <th colspan='2'><input type="button" value="<?php echo($lang['LOGIN_ACCOUNT_REQUEST_BUTTON']); ?>"
                                       onclick="window.open('<?php echo $server_config['register_forum']; ?>');"/></th>
            </tr>
            <?php

        }

        ?>
    </table>
</form>
<?php require_once("views/page_tail_2.php"); ?>
