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

<script type="text/javascript">
    var date = new Date;
    var delta = Math.round((<?php echo (time() * 1000);?> -date.getTime()) / 1000);
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
                if (go_visibility[a] != "visible")
                    go_visibility[a] = "visible";
                else
                    go_visibility[a] = "hidden";
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

<table border="0" cellpadding="0" cellspacing="0">
    <tr align="center">
        <td>
            <b><?php echo($lang['MENU_SERVER_TIME']); ?></b><br/>
            <span id="datetime"><blink><?php echo($lang['MENU_WAITING']); ?></blink></span>
        </td>
    </tr>

    <tr>
        <td>
            <div><a href="index.php" class="menu"><img src="./theme/default_skin/transpa.gif" width="166" height="65" border="0"/></a></div>
        </td>
    </tr>

    <?php

    if ($server_config["server_active"] == 0) {
        echo "<tr>\n";
        echo "\t" . "<td><div align='center'><font color='red'><b><blink>".$lang['MENU_SERVER_OFFLINE']."</blink></b></font></div></td>\n";
        echo "</tr>\n";
    }

    ?>
<tr>
    <td><div style="text-align='left';">

        <ul style="width:100px;" class= "menu" id="menu">
            <?php
            if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) {
                echo "<li><a href='index.php?action=administration' class='menu_items'>".$lang['MENU_ADMIN']."</a></li>";
            }
            ?>
            <li><a href='index.php?action=profile' class='menu_items'><?php echo($lang['MENU_PROFILE']); ?></a></li>
            <img src="./theme/default_skin/transpa.gif" width="166" height="19">
            <li><a href='index.php?action=home' class='menu_items'><?php echo($lang['MENU_HOME']); ?></a></li>
            <li><a href='index.php?action=galaxy' class='menu_items'><?php echo($lang['MENU_GALAXY']); ?></a></li>
            <li><a href='index.php?action=cartography' class='menu_items'><?php echo($lang['MENU_ALLIANCES']); ?></a></li>
            <li><a href='index.php?action=search' class='menu_items'><?php echo($lang['MENU_RESEARCH']); ?></a></li>
            <li><a href='index.php?action=ranking' class='menu_items'><?php echo($lang['MENU_RANKINGS']); ?></a></li>
            <img src="./theme/default_skin/transpa.gif" width="166" height="19">
            <li><a href='index.php?action=statistic' class='menu_items'><?php echo($lang['MENU_UPDATE_STATUS']); ?></a></li>
            <li><p class='menu_items'><?php echo($lang['MENU_MODULES']); ?></p>
                <ul class='menu_mods'>
<?php
             $request = "SELECT action, menu FROM " . TABLE_MOD . " WHERE active = 1 AND `admin_only` = '0' order by position, title";
             $result = $db->sql_query($request);

             if ($db->sql_numrows($result)) {
                 while ($val = $db->sql_fetch_assoc($result)) {
                     echo '<span>&nbsp;&nbsp;- <a class=\'menu_mods\' href="index.php?action=' . $val['action'] . '">' . $val['menu'] . '</a></span>'.'<br>';
                 }
             }

            if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                $request = "SELECT action, menu FROM " . TABLE_MOD . " WHERE active = 1 and `admin_only` = '1' order by position, title";
                $result = $db->sql_query($request);

                if ($db->sql_numrows($result)) {
                    echo '<img src="./theme/default_skin/transpa.gif" width="166" height="19">';

                    while ($val = $db->sql_fetch_assoc($result)) {
                        echo '<span>&nbsp;&nbsp;- <a class="menu_mods" href="index.php?action=' . $val['action'] . '">' . $val['menu'] . '</a></span>' . '<br>';
                    }
                }
            }

?>
                </ul>
            </li>
            <img src="./theme/default_skin/transpa.gif" width="166" height="19">
            <?php
            if ($server_config["url_forum"] != "") {
                echo "<li><a href='" . $server_config["url_forum"] . "' class='menu_items'>".$lang['MENU_FORUM']."</a></li>";
            }
            ?>
            <img src="./theme/default_skin/transpa.gif" width="166" height="19">
            <li><a href="index.php?action=about" class='menu_items'><?php echo($lang['MENU_ABOUT']); ?></a></li>
            <img src="./theme/default_skin/transpa.gif" width="166" height="19">
            <li><a href='index.php?action=logout' class='menu_items'><?php echo($lang['MENU_LOGOUT']); ?></a></li>
        </ul>

        </div><!---->
    </td>


</tr>

</table>

<!--<script>$( "#menu" ).menu();</script> (Encore pas mal de travail pour mettre ce menu en place) -->