<?php
/***************************************************************************
 *    filename    : admin_members.php
 *    desc.        :
 *    Author        : Kyser - http://ogsteam.fr/
 *    created        : 16/12/2005
 *    modified    : 30/07/2006 00:00:00
 ***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

$user_info = user_get();
$usergroup_list = usergroup_get();
?>

<table width="200">
    <tr>
        <td class="c" colspan="3"><?php echo($lang['ADMIN_MEMBERS_NEWACCOUNT']); ?></td>
    </tr>
    <tr>
        <th width="100"><input type="button" value="<?php echo($lang['ADMIN_MEMBERS_NEWACCOUNT_BUTTON']); ?>"
                               onclick="document.getElementById('new_member').style.visibility = 'visible';"></th>
    </tr>
</table>
<br/>
<table>
    <tr>
        <td class="c" width="120"><?php echo($lang['ADMIN_MEMBERS_PLAYER']); ?></td>
        <td class="c" width="120"><?php echo($lang['ADMIN_MEMBERS_REGISTERED']); ?></td>
        <td class="c" width="120"><?php echo($lang['ADMIN_MEMBERS_ENABLED']); ?></td>
        <?php
        if ($user_data["user_admin"] == 1) { ?>
            <td class="c" width="120"><?php echo($lang['ADMIN_MEMBERS_COADMIN']); ?></td>
        <?php }
        if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) { ?>
            <td class="c" width="120"><?php echo($lang['ADMIN_MEMBERS_MGMEMBERS']); ?><?php echo help("admin_member_manager");?></td>
        <?php }?>
        <td class="c" width="120"><?php echo($lang['ADMIN_MEMBERS_MGRANKS']); ?><?php echo help("admin_ranking_manager");?></td>
        <td class="c" width="120"><?php echo($lang['ADMIN_MEMBERS_LASTCONNECT']); ?></td>
        <td class="c" colspan="3">&nbsp;</td>
    </tr>
    <?php
    foreach ($user_info as $v) {
        $user_id = $v["user_id"];
        if (($user_data["user_admin"] != 1 && $v["user_admin"] == 1) ||
            ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] == 1 && $v["user_coadmin"] == 1) ||
            ($user_data["user_admin"] != 1 && ($user_data["user_coadmin"] != 1 && $user_data["management_user"] == 1) && ($v["user_coadmin"] == 1 || $v["management_user"] == 1))
        ) {
            continue;
        }

        $YesNo = array("<font color=\"red\">".$lang['ADMIN_MEMBERS_NO']."</font>", "<font color=\"lime\">".$lang['ADMIN_MEMBERS_YES']."</font>");
        $user_auth = user_get_auth($user_id);

        $auth = "<table width=\"100%\" style=\"color:white;\">";
        $auth .= "<tr><td class=\"c\" colspan=\"2\">".$lang['ADMIN_MEMBERS_SERVER_RIGHTS']."</td></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_SERVER_ADDSYSTEM']."</th><th>" . $YesNo[$user_auth["server_set_system"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_SERVER_ADDREPORT']."</th><th>" . $YesNo[$user_auth["server_set_spy"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_SERVER_ADDRANK']."</th><th>" . $YesNo[$user_auth["server_set_ranking"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_SERVER_ADDHIDDEN']."</th><th>" . $YesNo[$user_auth["server_show_positionhided"]] . "</th></tr>";

        $auth .= "<tr><td class=\"c\" colspan=\"2\">".$lang['ADMIN_MEMBERS_EXTERNAL_RIGHTS']."</td></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_EXTERNAL_CONNECT']."</th><th>" . $YesNo[$user_auth["ogs_connection"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_EXTERNAL_ADDSYSTEM']."</th><th>" . $YesNo[$user_auth["ogs_set_system"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_EXTERNAL_GETSYSTEM']."</th><th>" . $YesNo[$user_auth["ogs_get_system"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_EXTERNAL_ADDREPORT']."</th><th>" . $YesNo[$user_auth["ogs_set_spy"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_EXTERNAL_GETREPORT']."</th><th>" . $YesNo[$user_auth["ogs_get_spy"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_EXTERNAL_ADDRANK']."</th><th>" . $YesNo[$user_auth["ogs_set_ranking"]] . "</th></tr>";
        $auth .= "<tr><th>".$lang['ADMIN_MEMBERS_EXTERNAL_GETRANK']."</th><th>" . $YesNo[$user_auth["ogs_get_ranking"]] . "</th></tr>";
        $auth .= "</table>";

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $auth = htmlentities($auth, ENT_COMPAT | ENT_HTML401, "UTF-8");
        } else {
            $auth = htmlentities($auth, ENT_COMPAT, "UTF-8");
        }

        $name = $v["user_name"];

        $reg_date = strftime("%d %b %Y %H:%M", $v["user_regdate"]);

        $active_off = !$v["user_active"] ? " selected" : "";
        $user_coadmin_off = (!$v["user_coadmin"] && !$v["user_admin"]) ? " selected" : "";
        $management_user_off = (!$v["management_user"] && !$v["user_admin"]) ? " selected" : "";
        $management_ranking_off = (!$v["management_ranking"] && !$v["user_admin"]) ? " selected" : "";

        if ($v["user_lastvisit"] != 0) {
            $last_visit = strftime("%d %b %Y %H:%M", $v["user_lastvisit"]);
        } else {
            $last_visit = "--";
        }

        echo "<tr>" . "\n";

        echo "<form method='POST' action='index.php?action=admin_modify_member&amp;user_id=" . $user_id . "'>" . "\n";
        echo "\t" . "<th><a onmouseover=\"this.T_WIDTH=260;this.T_TEMP=15000;return escape('" . $auth . "')\">" . $name . "</a></th>" . "\n";
        echo "\t" . "<th>" . $reg_date . "</th>" . "\n";
        echo "\t" . "<th><select name='active'><option value='1'>".$lang['ADMIN_MEMBERS_YES']."</option><option value='0'".$active_off.">".$lang['ADMIN_MEMBERS_NO']."</option></select></th>" . "\n";
        if ($user_data["user_admin"] == 1) {
            echo "\t" . "<th><select name='user_coadmin'><option value='1'>".$lang['ADMIN_MEMBERS_YES']."</option><option value='0'".$user_coadmin_off.">".$lang['ADMIN_MEMBERS_NO']."</option></select></th>" . "\n";
        }
        if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
            echo "\t" . "<th><select name='management_user'><option value='1'>".$lang['ADMIN_MEMBERS_YES']."</option><option value='0'".$management_user_off.">".$lang['ADMIN_MEMBERS_NO']."</option></select></th>" . "\n";
        }
        echo "\t" . "<th><select name='management_ranking'><option value='1'>".$lang['ADMIN_MEMBERS_YES']."</option><option value='0'".$management_ranking_off.">".$lang['ADMIN_MEMBERS_NO']."</option></select></th>" . "\n";
        echo "\t" . "<th>" . $last_visit . "</th>" . "\n";
        echo "\t" . "<th><input type='image' src='images/usercheck.png' title='".$lang['ADMIN_MEMBERS_VALIDATE']. $name . "'></th>" . "\n";
        echo "</form>" . "\n";

        echo "<form method='POST' action='index.php?action=delete_member&amp;user_id=" . $user_id . "' onsubmit=\"return confirm('".$lang['ADMIN_MEMBERS_DELETE_TITLE'] . $name . "');\">" . "\n";
        echo "\t" . "<th><input type='image' src='images/userdrop.png' title='".$lang['ADMIN_MEMBERS_DELETE'] . $name . "'></th>" . "\n";
        echo "</form>" . "\n";

        echo "<form method='POST' action='index.php?action=new_password&amp;user_id=" . $user_id . "' id=" . $user_id . ">" . "\n";
        echo "\t" . "<th><img style=\"cursor:pointer\" src='images/userpwd.png' title='".$lang['ADMIN_MEMBERS_PWDCHANGE_TITLE'] . $name . "' onclick=\"if(confirm('".$lang['ADMIN_MEMBERS_PWDCHANGE'] . $name . "')){document.all.pass_name.value='" . $name . "';document.all.pass_id.value='" . $user_id . "';document.getElementById('pass_new').value = '';document.getElementById('new_pass').style.visibility = 'visible';}\"><input type=\"hidden\" id=\"" . $name . "\" name=\"pass_" . $user_id . "\" value=\"\"></th>" . "\n";
        echo "</form>" . "\n";
        echo "</tr>" . "\n";
    }
    ?>
</table>
<div id="new_pass" style="visibility:hidden;position: fixed;    top: 300px;     left: 500px;z-index: 100;">
    <table width="200" style="border:1px #003399 solid;" cellpadding="3">
        <tr>
            <td align="center" class="c"><?php echo($lang['ADMIN_MEMBERS_NEWPASSWORD']); ?></td>
        </tr>
        <tr>
            <th align="center">
                <?php echo($lang['ADMIN_MEMBERS_PASSWORDGENERATE']); ?><br>
                <input type="hidden" name="pass_name" value=""><br>
                <input type="hidden" name="pass_id" value="">
                <input type="text" name="pass" id="pass_new" value=""><br><br>
                <input type="button" value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDOK']); ?>"
                       onclick="document.getElementById(document.all.pass_name.value).value = document.getElementById('pass_new').value;document.getElementById(document.all.pass_id.value).submit();">
                <input type="button" value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDCANCEL']); ?>"
                       onclick="document.getElementById('new_pass').style.visibility = 'hidden';">
            </th>
        </tr>
    </table>
</div>
<div id="new_member" style="visibility:hidden;position: fixed; top: 200px; left: 500px;z-index: 100;">
    <form method="POST" action="index.php?action=newaccount">
        <table>
            <tr>
                <td>
                    <table width="400" style="border:1px #003399 solid;background-color:#000000" cellpadding="3">
                        <tr>
                            <td align="center" class="c" colspan="2"><?php echo($lang['ADMIN_MEMBERS_POPUP_NEWACCOUNT']); ?></td>
                        </tr>
                        <tr>
                            <th align="center"><?php echo($lang['ADMIN_MEMBERS_POPUP_NAME']); ?></th>
                            <th align="center"><input name="pseudo" type="text" maxlength="15" size="20"></th>
                        </tr>
                        <tr>
                            <th align="center"><?php echo($lang['ADMIN_MEMBERS_POPUP_PASSWORD']); ?></th>
                            <th align="center"><input name="pass" type="text" maxlength="15" size="20"></th>
                        </tr>
                        <tr>
                            <th align="center"><?php echo($lang['ADMIN_MEMBERS_POPUP_RIGHTS']); ?></th>
                            <th align="center">
                                <?php
                                if ($user_data["user_admin"] == 1) {
                                    echo "\t" . $lang['ADMIN_MEMBERS_POPUP_MGTMEMBERS']." : <select name='user_coadmin'><option value='1'>".$lang['ADMIN_MEMBERS_YES']."</option><option value='0'selected='selected' ".$user_coadmin_off.">".$lang['ADMIN_MEMBERS_NO']."</option></select><br>" . "\n";
                                }
                                if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
                                    echo "\t" . $lang['ADMIN_MEMBERS_POPUP_MGTRANKS'] . help('admin_member_manager') . " : <select name='management_user'><option value='1'>".$lang['ADMIN_MEMBERS_YES']."</option><option value='0'".$management_user_off.">".$lang['ADMIN_MEMBERS_NO']."</option></select><br>" . "\n";
                                }
                                echo "\t" . $lang['ADMIN_MEMBERS_POPUP_GROUP'] . help('admin_ranking_manager') . " : <select name='management_ranking'><option value='1'>".$lang['ADMIN_MEMBERS_YES']."</option><option value='0'".$management_ranking_off.">".$lang['ADMIN_MEMBERS_NO']."</option></select><br>" . "\n";?>
                            </th>
                        </tr>
                        <tr>
                            <th align="center">Groupe :</th>
                            <th align="center">
                                <select name="group_id"><?php
                                    foreach ($usergroup_list as $value) {
                                        echo "\t\t\t\t" . "<option value='" . $value["group_id"] . "'>" . $value["group_name"] . "</option>";
                                    }?>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th align="center" colspan="2">
                                <input type="submit" value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDOK']); ?>">
                                <input type="button" value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDCANCEL']); ?>"
                                       onclick="document.getElementById('new_member').style.visibility = 'hidden';">
                            </th>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div> 
