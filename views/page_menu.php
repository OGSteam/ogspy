<?php
/**
 * Main menu
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
?>
<div class="menu">
    <aside>
        <div class="timer">
            <!-- placer dans un fichier js ...-->
            <script>
                var date = new Date;
                var delta = Math.round((1584339590000 - date.getTime()) / 1000);

                function Timer() {
                    var days = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
                    var months = ["Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Jui", "Aoû", "Sep", "oct", "nov", "déc"];

                    date = new Date;
                    date.setTime(date.getTime() + delta * 1000);
                    var hour = date.getHours();
                    var min = date.getMinutes();
                    var sec = date.getSeconds();
                    var day = days[date.getDay()];
                    var day_number = date.getDate();
                    var month = months[date.getMonth()];
                    if (sec < 10) sec = "0" + sec;
                    if (min < 10) min = "0" + min;
                    if (hour < 10) hour = "0" + hour;

                    var datetime = day + " " + day_number + " " + month + " " + hour + ":" + min + ":" + sec;

                    if (document.getElementById) {
                        document.getElementById("datetime").innerHTML = datetime;
                    }
                }

                go_visibility = [];

                function goblink() {
                    if (document.getElementById && document.all) {
                        var blink_tab = document.getElementsByTagName('blink');
                        for (var a = 0; a < blink_tab.length; a++) {
                            if (go_visibility[a] !== "visible")
                                go_visibility[a] == "visible";
                            else
                                go_visibility[a] == "hidden";
                            blink_tab[a].style.visibility = go_visibility[a];
                        }
                    }
                }

                function Biper() {
                    Timer();
                    goblink();
                    setTimeout("Biper()", 1000);
                }

                window.onload = Biper;
            </script>
            Heure serveur <br>
            <span id="datetime">En attente</span>
        </div>
        <div class="logo_menu">
            <!-- logo ogsteam menu-->
        </div>
<?php if ($server_config["server_active"] == 0) : ?>
            <div class="offline">
<?php echo $lang['MENU_SERVER_OFFLINE']; ?>
            </div>
<?php endif; ?>
    </aside>
    <nav>
        <ul class="navmenu">
            <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) :?>
                <li>
                    <a href='index.php?action=administration' class='menu_items menu_items_admin'><?php echo  $lang['MENU_ADMIN'] ;?></a>
                </li>
            <?php endif; ?>
            <li>
                <a href='index.php?action=profile' class='menu_items menu_items_profil'>
                    <?php echo  $lang['MENU_PROFILE'] ;?>
                </a>
            </li>
            <li>
                <a href='index.php?action=home' class='menu_items menu_items_espperso'>
                    <?php echo  $lang['MENU_HOME'] ;?>
                </a>
            </li>
            <li>
                <a href='index.php?action=galaxy' class='menu_items menu_items_galaxie'>
                    <?php echo  $lang['MENU_GALAXY'] ;?>
                </a>
            </li>
            <li>
                <a href='index.php?action=cartography' class='menu_items menu_items_alliance'>
                    <?php echo  $lang['MENU_ALLIANCES'] ;?>
                </a>
            </li>
            <li>
                <a href='index.php?action=search' class='menu_items menu_items_recherche'>
                    <?php echo  $lang['MENU_RESEARCH'] ;?>
                </a>
            </li>
            <li>
                <a href='index.php?action=ranking' class='menu_items menu_items_classements'>
                    <?php echo  $lang['MENU_RANKINGS'] ;?>
                </a>
            </li>
            <li><a href='index.php?action=statistic' class='menu_items menu_items_etatcarto'>
                    <?php echo  $lang['MENU_UPDATE_STATUS'] ;?>
                </a></li>
            <li class='menu_items menu_items_modules'><?php echo($lang['MENU_MODULES']); ?>

                <?php
                //todo sortir requete de la vue
                $mod_model = new \Ogsteam\Ogspy\Model\Mod_Model();
                $tMods = $mod_model->find_by(array("active" => "1"), array("position" => 'ASC', "title" => 'ASC'));
                ?>

                <ul class='menu_mods menu_mods_user'><!-- mod non admin -->
                    <?php foreach ($tMods as $mod) : ?>
                        <?php if ($mod['admin_only'] == 0) : ?>
                            <li>
                                <a class="menu_mods menu_mod_name_<?php echo $mod['menu']; ?>" href="index.php?action=<?php echo $mod['action'];?>">
                                    <?php echo $mod['menu']; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <ul class='menu_mods menu_mods_admin'><!-- mod  admin -->

                    <?php foreach ($tMods as $mod) : ?>

                        <?php if ($mod['admin_only'] == 1) : ?>
                            <li>
                                <a class="menu_mods menu_mod_name_<?php echo $mod['title']; ?>" href="index.php?action=<?php echo $mod['action'];?>">
                                    <?php echo $mod['title']; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
             </li>
            <?PHP if ($server_config["url_forum"] != "") : ?>
                <li>
                    <a href='https://forum.ogsteam.fr/' class='menu_items menu_items_forum '>
                        <?php echo($lang['MENU_FORUM']); ?>
                    </a>
                </li>
            <?php endif ; ?>

            <li>
                <a href="index.php?action=about" class='menu_items menu_items_about'>
                    <?php echo($lang['MENU_ABOUT']); ?>
                </a>
            </li>
            <li>
                <a href='index.php?action=logout' class='menu_items menu_items_logout'>
                    <?php echo($lang['MENU_LOGOUT']); ?>
                </a>
            </li>
        </ul>
    </nav>
</div>
    <!--<script>$( "#menu" ).menu();</script> (Encore pas mal de travail pour mettre ce menu en place) -->