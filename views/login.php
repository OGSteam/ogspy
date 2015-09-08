<?php

/***************************************************************************
 * filename : login.php
 * desc.    :
 * Author   : Kyser - http://ogsteam.fr/
 * created  : 15/11/2005
 * modified : 22/08/2006 00:00:00
 ***************************************************************************/

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
        <input type='hidden' name='goto' value='<?php echo $goto; ?>'/></p>

    <table style="margin:0 auto; padding:0; border-collapse:separate; border-spacing:1px">
        <tr>
            <td class="c" colspan="2" style="text-align:left">Paramètres de connexion</td>
        </tr>
        <tr>
            <th style="width:150px">Login :</th>
            <th style="width:150px"><input type='text' name='login'/></th>
        </tr>
        <tr>
            <th style="width:150px">Mot de passe :</th>
            <th style="width:150px"><input type='password' name='password'/></th>
        </tr>
        <tr>
            <th colspan='2'><input type='submit' value='Connexion'/></th>
        </tr>
        <?php

        if ($enable_register_view == 1) {

            ?>
            <tr>
                <td class="c" colspan="2" style="text-align:left">Demande de compte OGSpy</td>
            </tr>
            <tr>
                <th colspan='2'>Si vous ne disposez pas d'un compte, il faut <span
                        style="color:red">obligatoirement</span> en demander un sur le forum
                    de <?php echo $server_config['register_alliance']; ?>.
                </th>
            </tr>
            <tr>
                <th colspan='2'><input type="button" value="Demander un compte"
                                       onclick="window.open('<?php echo $server_config['register_forum']; ?>');"/></th>
            </tr>
            <?php

        }

        ?>
    </table>
</form>

<p style="text-align:right;">
    <a href="http://validator.w3.org/check?uri=referer">
        <img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88"/>
    </a>
</p>
<?php require_once("views/page_tail_2.php"); ?>
