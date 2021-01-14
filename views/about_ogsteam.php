<?php
/** $Id: about_ogsteam.php 7665 2012-07-09 14:44:26Z luke_skywalker $ **/
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
        <td align="center" class="c" colspan="2"><span
                style="color: Yellow; font-size: small; "><?php echo($lang['ABOUT_PROJECT_TEAM']); ?></span></td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/default_avatar.svg" width="40" title="Kyser">
        </td>
        <td class="l">
            &nbsp;<a><span style="color: Yellow; font-size: x-small; ">Kyser</span></a><br>
            <div style="text-align: center;"><b>Concepteur du serveur d'alliance OGSpy</b><br><br></div>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/default_avatar.svg" width="40" title="Rica">
        </td>
        <td class="l">
            &nbsp;<a><span style="color: Yellow; font-size: x-small; ">Rica</span></a><br>
            <div style="text-align: center;"><b>Concepteur du client OGame Stratege (OGS)<br>
            Concepteur de l'ancien serveur d'alliance OGame Stratege Serveur (OGSS)</b><br></div>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/ogsteam_avatar_aeris.png" width="80" title="Aeris">
        </td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">Aéris</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_AERIS']); ?></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/default_avatar.svg" width="40" title="Capi">
        </td>
        <td class="l">
            &nbsp;<a><span style="color: Yellow; font-size: x-small; ">Capi</span></a><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_CAPI']); ?><br/>
                    <span style="color: orange; ">Capi capi, capo...</span></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/default_avatar.svg" width="40" title="Chris Alys">
        </td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">Chris Alys</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_CHRYS']); ?></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/ogsteam_avatar_darknoon.png" width="80" title="DarkNoon">
        </td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">DarkNoon</span></b>
            <br/>
            <ul>
                <li><b><?php echo($lang['ABOUT_PROJECT_DARKNOON']); ?></b></li>
                <li><b><?php echo($lang['ABOUT_PROJECT_DARKNOON1']); ?></b></li>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/ogsteam_avatar_jedinight.png" width="80" title="Jedinight">
        </td>
        <td class="l">
            <b><span style="color: Yellow; font-size: x-small; ">Jedinight</span></b>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_JEDINIGHT']); ?></b></div>
            <br/>
            <ul>
                <li><b><?php echo($lang['ABOUT_PROJECT_JEDINIGHT1']); ?></b></li>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/ogsteam_avatar_machine.jpg" width="80" title="Machine">
        </td>
        <td class="l">
            <b><span style="color: Yellow; font-size: x-small; ">Machine</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_MACHINE']); ?></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/default_avatar.svg" width="40" title="Ninety"></td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">Ninety</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_NINETY']); ?></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/default_avatar.svg" width="40" title="Shad">
        </td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">Shad</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_SHAD']); ?></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/default_avatar.svg" width="40" title="Superbox">
        </td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">Superbox</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_SUPERBOX']); ?></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/ogsteam_avatar_xaviernuma.jpg" width="80" title="Xaviernuma">
        </td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">Xaviernuma</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_XAVIERNUMA']); ?></b></div>
            <br/>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img src="images/avatars/ogsteam_avatar_pitch314.jpg" width="80" title="pitch314">
        </td>
        <td class="l">
            &nbsp;<b><span style="color: Yellow; font-size: x-small; ">Pitch314</span></b><br/>
            <div style="text-align: center;"><b><?php echo($lang['ABOUT_PROJECT_PITCH314']); ?></b></div>
            <ul>
                <li><b><?php echo($lang['ABOUT_PROJECT_PITCH314_1']); ?></b></li>
                <li><b><?php echo($lang['ABOUT_PROJECT_PITCH314_2']); ?></b></li>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="c" width="100" height="100" align="center" valign="middle"><img
                src="skin/OGSpy_skin/gfx/ogame-produktion.jpg" width="80" title="Testeurs"></td>
        <td class="l">
            <b><span style="color: Yellow; font-size: x-small; "><?php echo($lang['ABOUT_TEST_TEAM']); ?></span></b>
            <br/>
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