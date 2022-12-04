<?php

/**
 *  Panneau d'affichage: A propos de l'OGsteam et des contributeurs à OGSpy
 * @package OGSpy
 * @version 3.04b ($Rev: 7665 $)
 * @subpackage views
 * @author Kyser
 * @created 17/01/2006
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("views/page_header.php");
?>


<table width="70%" border="1" cellpadding="0" cellspacing="5" align="center">
    <tr>
        <th align="center" class="c" colspan="5"><span style="color: Yellow; font-size: small; "><?php echo $lang['ABOUT_PROJECT_TEAM']; ?></span></th>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/default_avatar.svg" alt="Kyser Picture" width="40" title="Kyser">
        </td>
        <td class="l">
            &nbsp;<a><span style="color: Yellow; font-size: x-small; ">Kyser</span></a><br>
            <div style="text-align: center;"><strong>Concepteur du serveur d'alliance OGSpy</strong><br><br></div>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/default_avatar.svg" alt="Rica Picture" width="40" title="Rica">
        </td>
        <td class="l">
            &nbsp;<a><span style="color: Yellow; font-size: x-small; ">Rica</span></a><br>
            <div style="text-align: center;"><strong>Concepteur du client OGame Stratege (OGS)<br>
                    Concepteur de l'ancien serveur d'alliance OGame Stratege Serveur (OGSS)</strong><br></div>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/ogsteam_avatar_aeris.png" alt="Aeris Picture" width="80" title="Aeris">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">Aéris</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_AERIS']; ?></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/default_avatar.svg" alt="Capi Picture" width="40" title="Capi">
        </td>
        <td class="l">
            &nbsp;<a><span style="color: Yellow; font-size: x-small; ">Capi</span></a><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_CAPI']; ?><br />
                    <span style="color: orange; ">Capi capi, capo...</span></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/default_avatar.svg" alt="Chris Alys Picture" width="40" title="Chris Alys">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">Chris Alys</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_CHRYS']; ?></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/ogsteam_avatar_darknoon.png" alt="DarkNoon Picture" width="80" title="DarkNoon">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">DarkNoon</span></strong>
            <br />
            <ul>
                <li><strong><?php echo $lang['ABOUT_PROJECT_DARKNOON']; ?></strong></li>
                <li><strong><?php echo $lang['ABOUT_PROJECT_DARKNOON1']; ?></strong></li>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/ogsteam_avatar_jedinight.png" alt="Jedinight Picture" width="80" title="Jedinight">
        </td>
        <td class="l">
            <strong><span style="color: Yellow; font-size: x-small; ">Jedinight</span></strong>
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_JEDINIGHT']; ?></strong></div>
            <br />
            <ul>
                <li><strong><?php echo $lang['ABOUT_PROJECT_JEDINIGHT1']; ?></strong></li>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/ogsteam_avatar_machine.jpg" alt="Machine Picture" width="80" title="Machine">
        </td>
        <td class="l">
            <strong><span style="color: Yellow; font-size: x-small; ">Machine</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_MACHINE']; ?></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/default_avatar.svg" alt="Ninety Picture" width="40" title="Ninety">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">Ninety</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_NINETY']; ?></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/default_avatar.svg" alt="Shad Picture" width="40" title="Shad">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">Shad</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_SHAD']; ?></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/default_avatar.svg" alt="SuperBox Picture" width="40" title="Superbox">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">Superbox</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_SUPERBOX']; ?></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/ogsteam_avatar_xaviernuma.jpg" alt="XNurma Picture" width="80" title="Xaviernuma">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">Xaviernuma</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_XAVIERNUMA']; ?></strong></div>
            <br />
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="images/avatars/ogsteam_avatar_pitch314.jpg" alt="pitch314 Picture" width="80" title="pitch314">
        </td>
        <td class="l">
            &nbsp;<strong><span style="color: Yellow; font-size: x-small; ">Pitch314</span></strong><br />
            <div style="text-align: center;"><strong><?php echo $lang['ABOUT_PROJECT_PITCH314']; ?></strong></div>
            <ul>
                <li><strong><?php echo $lang['ABOUT_PROJECT_PITCH314_1']; ?></strong></li>
                <li><strong><?php echo $lang['ABOUT_PROJECT_PITCH314_2']; ?></strong></li>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle">
            <img src="skin/OGSpy_skin/gfx/ogame-produktion.jpg" alt="Test Team Picture" width="80" title="Testeurs">
        </td>
        <td class="l">
            <strong>
                <span style="color: Yellow; font-size: x-small; "><?php echo $lang['ABOUT_TEST_TEAM']; ?></span>
            </strong>
            <br />
            <ul>
                <li>Skyline-ch</li>
                <li>Anubys</li>
                <li>Néo32</li>
                <li>Lorenzo</li>
            </ul>
        </td>
    </tr>
</table>

<?php
require_once("views/page_tail.php");
?>
