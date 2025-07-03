<?php
global $lang;

/**
 *  Panneau d'affichage: A propos de l'OGsteam et des contributeurs à OGSpy
 * @package OGSpy
 * @version 3.04b ($Rev: 7665 $)
 * @subpackage views
 * @author Kyser
 * @created 17/01/2006
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("views/page_header.php");
?>
<div class="page_about">
    <h2 class="page_about_h2"><?php echo $lang['ABOUT_PROJECT_TEAM']; ?></h2>

    <main class="cards">
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Kyser Picture" title="Kyser">
            <div class="text">
                <h3>Kyser</h3>
                <p>Concepteur du serveur d'alliance OGSpy.</p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Rica Picture" title="Rica">
            <div class="text">
                <h3>Rica</h3>
                <p>Concepteur du client OGame Stratege (OGS)</p>
                <p> Concepteur de l'ancien serveur d'alliance OGame Stratege Serveur (OGSS).</p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/ogsteam_avatar_aeris.png" alt="Aéris Picture" title="Aéris">
            <div class="text">
                <h3>Aéris</h3>
                <p><?php echo $lang['ABOUT_PROJECT_AERIS']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Capi Picture" title="Capi">
            <div class="text">
                <h3>Capi</h3>
                <p><?php echo $lang['ABOUT_PROJECT_CAPI']; ?></p>
                <p class="og-highlight">Capi capi, capo...</p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Chris Alys Picture" title="Chris Alys">
            <div class="text">
                <h3>Chris Alys</h3>
                <p><?php echo $lang['ABOUT_PROJECT_CHRYS']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/ogsteam_avatar_darknoon.png" alt="DarkNoon Picture" title="DarkNoon">
            <div class="text">
                <h3>DarkNoon</h3>
                <p><?php echo $lang['ABOUT_PROJECT_DARKNOON']; ?></p>
                <p><?php echo $lang['ABOUT_PROJECT_DARKNOON1']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/ogsteam_avatar_jedinight.png" alt="miJedinightddle Picture" title="Jedinight">
            <div class="text">
                <h3>Jedinight</h3>
                <p><?php echo $lang['ABOUT_PROJECT_JEDINIGHT']; ?></p>
                <p><?php echo $lang['ABOUT_PROJECT_JEDINIGHT1']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/ogsteam_avatar_machine.jpg" alt="Machine Picture" title="Machine">
            <div class="text">
                <h3>Machine</h3>
                <p><?php echo $lang['ABOUT_PROJECT_MACHINE']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Ninety Picture" title="Ninety">
            <div class="text">
                <h3>Ninety</h3>
                <p><?php echo $lang['ABOUT_PROJECT_NINETY']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Shad Picture" title="Shad">
            <div class="text">
                <h3>Shad</h3>
                <p><?php echo $lang['ABOUT_PROJECT_SHAD']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Superbox Picture" title="Superbox">
            <div class="text">
                <h3>Superbox</h3>
                <p><?php echo $lang['ABOUT_PROJECT_SUPERBOX']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/ogsteam_avatar_xaviernuma.jpg" alt="Xaviernuma Picture" title="Xaviernuma">
            <div class="text">
                <h3>Xaviernuma</h3>
                <p><?php echo $lang['ABOUT_PROJECT_XAVIERNUMA']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/ogsteam_avatar_pitch314.jpg" alt="pitch314 Picture" title="pitch314">
            <div class="text">
                <h3>pitch314</h3>
                <p><?php echo $lang['ABOUT_PROJECT_PITCH314_1']; ?></p>
                <p><?php echo $lang['ABOUT_PROJECT_PITCH314_2']; ?></p>
            </div>
        </div>
        <div class="card">
            <img src="images/avatars/default_avatar.png" alt="Test Team Picture" title="Test Team">
            <div class="text">
                <h3><?php echo $lang['ABOUT_TEST_TEAM']; ?></h3>
                <ul>
                    <li>Skyline-ch</li>
                    <li>Anubys</li>
                    <li>Néo32</li>
                    <li>Lorenzo</li>
                    <li>EVH</li>
                    <li>Dardarmotus</li>
                </ul>
            </div>
        </div>
    </main>

</div> <!-- fin div class="page_about" -->
<?php
require_once("views/page_tail.php");
