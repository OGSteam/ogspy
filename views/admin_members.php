<?php
/**
 * Panneau administration des options Membres
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyzer
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1 && $user_data["management_user"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
}

$user_info = user_get();
$usergroup_list = usergroup_get();


?>
<!--Logicque javascript-->
<script language="JavaScript">
    function visible(byId) {
        document.getElementById(byId).style.visibility = 'visible';
        document.getElementById(byId).style.display = 'block';

    }

    function unvisible(byId) {
        document.getElementById(byId).style.visibility = 'hidden';
        document.getElementById(byId).style.display = 'none';

    }

    function beginCreateUser() {
        visible("createNewPlayer");
        unvisible("creatingNewPlayer");


    }

    function endCreateUser() {
        visible("creatingNewPlayer");
        unvisible("createNewPlayer");
    }
</script>


<!--tableau de creation d'un nouvel utilisateur-->
<div id="creatingNewPlayer">
    <table>
        <tr>
            <td class="c" colspan="3">
                <?php echo($lang['ADMIN_MEMBERS_NEWACCOUNT']); ?>
            </td>
        </tr>
        <tr>
            <th>
                <input type="button" value="<?php echo($lang['ADMIN_MEMBERS_NEWACCOUNT_BUTTON']); ?>"
                       onclick=beginCreateUser()>
            </th>
        </tr>
    </table>

</div>


<!-- formulaire de creation nouveau joueur-->
<div id="createNewPlayer" style="display: none;visibility: hidden" ;>
<form method="POST"  action="index.php?action=newaccount" >
    <fieldset>
        <legend><?php echo($lang['ADMIN_MEMBERS_POPUP_NEWACCOUNT']); ?></legend>
        <label for="pseudo"><?php echo($lang['ADMIN_MEMBERS_POPUP_NAME']); ?></label>
        <input type="text" maxlength="64" size="20"  name='pseudo' id='pseudo'/>
        <label for="pass"><?php echo($lang['ADMIN_MEMBERS_POPUP_PASSWORD']); ?></label>
        <input type="password" maxlength="15" size="20"  name='pass' id='pass'/>
        <label for="email"><?php echo($lang['ADMIN_MEMBERS_POPUP_EMAIL']); ?></label>
        <input type="email" maxlength="50" size="35" name='email' id='email'/>
    </fieldset>

    <fieldset>
        <legend><?php echo($lang['ADMIN_MEMBERS_POPUP_RIGHTS']); ?></legend>
        <?php if ($user_data["user_admin"] == 1) : ?>
            <!-- gestion des membres-->
            <label for="user_coadmin"><?php echo($lang['ADMIN_MEMBERS_POPUP_MGTMEMBERS']); ?></label>
            <select name='user_coadmin' id='user_coadmin' ">
                <option value='1'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                <option value='0' selected='selected'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
            </select>
        <?php  endif ; ?>
        <!-- gestion des Classements-->
        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1)  : ?>
            <label for="management_user"><?php echo($lang['ADMIN_MEMBERS_POPUP_MGTMEMBERS']); ?> <?php echo help('admin_member_manager') ; ?></label>
            <select name='management_user' id='management_user' ">
                <option value='1'  selected='selected'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                <option value='0'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
            </select>
        <?php  endif ; ?>
        <!--creation d un nouveau compte-->
        <label for="management_ranking"><?php echo($lang['ADMIN_MEMBERS_POPUP_GROUP']); ?> <?php echo help('admin_ranking_manager') ; ?></label>
        <select name='management_ranking' id='management_ranking' ">
            <option value='1' ><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
            <option value='0'  selected='selected'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
        </select>


        <label for="enable_stat_view"><?php echo($lang['ADMIN_DISPLAY_STATS_MEMBER']); ?><?php echo help("member_stats"); ?></label>
        <input type="checkbox" value="1" <?php echo $enable_stat_view; ?> onClick="if (view.enable_stat_view.checked == false)view.enable_members_view.checked=false;" name='enable_stat_view' id='enable_stat_view'/>
        <label for="enable_members_view"><?php echo($lang['ADMIN_DISPLAY_STATS_CONNECTED']); ?><?php echo help("member_connected"); ?></label>
        <input type="checkbox" value="1" <?php echo $enable_members_view; ?> onClick="if (view.enable_stat_view.checked == false)view.enable_members_view.checked=false;" name='enable_members_view' id='enable_members_view'/>
        <label for="galaxy_by_line_stat"><?php echo($lang['ADMIN_DISPLAY_STATS_GVIEW']); ?></label>
        <input type="text" size="5" maxlength="3" value="<?php echo $galaxy_by_line_stat; ?>"  name='galaxy_by_line_stat' id='galaxy_by_line_stat'/>
        <label for="system_by_line_stat"><?php echo($lang['ADMIN_DISPLAY_STATS_SVIEW']); ?></label>
        <input type="text" size="5" maxlength="3" value="<?php echo $system_by_line_stat; ?>"  name='system_by_line_stat' id='system_by_line_stat'/>
    </fieldset>
</div>



<div>
    <form method="POST" action="index.php?action=newaccount">
        <table>
            <tr>
                <td>
                    <table >
                        <tr>
                            <td colspan="2">
                                <?php echo($lang['ADMIN_MEMBERS_POPUP_NEWACCOUNT']); ?>
                            </td>
                        </tr>
                        <tr>
                            <th >
                                <?php echo($lang['ADMIN_MEMBERS_POPUP_NAME']); ?>
                            </th>
                            <th >
                                <input name="pseudo" type="text" maxlength="15" size="20">
                            </th>
                        </tr>
                        <tr>
                            <th>
                                <?php echo($lang['ADMIN_MEMBERS_POPUP_PASSWORD']); ?>
                            </th>
                            <th>
                                <input name="pass" type="text" maxlength="64" size="20">
                            </th>
                        </tr>
                        <tr>
                            <th >
                                <?php echo($lang['ADMIN_MEMBERS_POPUP_EMAIL']); ?>
                            </th>
                            <th align="center">
                                <input name="email" type="text" maxlength="50" size="35">
                            </th>
                        </tr>
                        <tr>
                            <th>
                                <?php echo($lang['ADMIN_MEMBERS_POPUP_RIGHTS']); ?>
                            </th>
                            <th >
                                <!-- gestion des membres-->
                                <?php if ($user_data["user_admin"] == 1) : ?>
                                    <?php echo "\t" . $lang['ADMIN_MEMBERS_POPUP_MGTMEMBERS'] . " : "; ?>
                                    <select name='user_coadmin'>"?>
                                        <option value='1'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                                        <option value='0'
                                                selected='selected'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
                                    </select><br>
                                <?php endif; ?>
                                <!-- gestion des Classements-->
                                <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                                    <?php echo "\t" . $lang['ADMIN_MEMBERS_POPUP_MGTRANKS'] . help('admin_member_manager') . " : "; ?>
                                    <select name='management_user'>"?>
                                        <option value='1'  selected='selected'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                                        <option value='0'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
                                    </select><br>
                                <?php endif; ?>
                                <!--creation d un nouveau compte-->
                                <?php echo "\t" . $lang['ADMIN_MEMBERS_POPUP_GROUP'] . help('admin_ranking_manager') . " : "; ?>
                                <select name='management_ranking'>"?>
                                    <option value='1' ><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                                    <option value='0' > <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
                                </select><br>
                            </th>
                        </tr>
                        <tr>
                            <th >
                                Groupe :
                            </th>
                            <th ">
                                <select name="group_id">
                                    <?php foreach ($usergroup_list as $value) : ?>
                                        <option value="<?php echo $value["group_id"]; ?>">
                                            <?php echo $value["group_name"]; ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th align="center" colspan="2">
                                <input type="submit" value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDOK']); ?>">
                                <input type="button" value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDCANCEL']); ?>"
                                       onclick=endCreateUser()>
                            </th>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- Fin formulaire de creation nouveau joueur-->


<!-- Liste des joueurs-->
<div id="listPlayer">
    <table>
        <tr>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_PLAYER']); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_EMAIL']); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_REGISTERED']); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_ENABLED']); ?>
            </td>
            <?php if ($user_data["user_admin"] == 1): ?>
                <td class="c" width="120">
                    <?php echo($lang['ADMIN_MEMBERS_COADMIN']); ?>
                </td>
            <?php endif; ?>
            <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1): ?>
                <td class="c" width="120">
                    <?php echo($lang['ADMIN_MEMBERS_MGMEMBERS']); ?>
                    <?php echo help("admin_member_manager"); ?>
                </td>
            <?php endif; ?>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_MGRANKS']); ?>
                <?php echo help("admin_ranking_manager"); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_LASTCONNECT']); ?>
            </td>
            <td class="c" colspan="4">&nbsp;</td>
        </tr>
        <?php foreach ($user_info as $v) : ?>
            <tr>
                <?php $user_id = $v["user_id"];?>
                <?php $name = $v["user_name"]; ?>
                <?php $email = $v["user_email"]; ?>
                <?php $reg_date = strftime("%d %b %Y %H:%M", $v["user_regdate"]); ?>
                <?php $active_off = !$v["user_active"] ? " selected" : ""; ?>
                <?php $user_coadmin_off = (!$v["user_coadmin"] && !$v["user_admin"]) ? " selected" : ""; ?>
                <?php $management_user_off = (!$v["management_user"] && !$v["user_admin"]) ? " selected" : ""; ?>
                <?php $management_ranking_off = (!$v["management_ranking"] && !$v["user_admin"]) ? " selected" : ""; ?>
                <?php if ($v["user_lastvisit"] != 0): ?>
                    <?php $last_visit = strftime("%d %b %Y %H:%M", $v["user_lastvisit"]); ?>
                <?php else : ?>
                    <?php $last_visit = "--"; ?>
                <?php endif; ?>
                <form method='POST' action='index.php?action=admin_modify_member&amp;user_id=<?php echo $user_id;?>'>
                    <th>
                        <a><?php echo $name; ?></a><!--todo voir pour popUp-->
                    </th>
                    <th>
                        <?php echo $email; ?>
                    </th>
                    <th>
                        <?php echo $reg_date; ?>
                    </th>
                    <th>
                        <!--compte actif-->
                        <select name='active'>
                            <option value='1'>
                                <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                            </option>
                            <option value='0' <?php echo $active_off; ?>>
                                <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                            </option>
                        </select>
                    </th>

                    <?php if ($user_data["user_admin"] == 1) : ?>
                        <th>
                            <select name='user_coadmin'>
                                <option value='1'>
                                    <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                                </option>
                                <option value='0' <?php echo $user_coadmin_off; ?>>
                                    <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                                </option>
                            </select>
                        </th>
                    <?php endif; //si l'utilisateur est co admin que ce passe t-il  ?'?>

                    <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1): ?>
                        <th> <!--gestion membres-->
                            <select name='management_user'>
                                <option value='1'>
                                    <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                                </option>
                                <option value='0' <?php echo $management_user_off; ?>>
                                    <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                                </option>
                            </select>
                        </th>
                    <?php endif; //section visible que des admin verif pertinente ???'?>
                    <th>
                        <select name='management_ranking'>
                            <option value='1'>
                                <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                            </option>
                            <option value='0' <?php echo $management_ranking_off; ?>>
                                <?php echo $lang['ADMIN_MEMBERS_NO'] ?>
                            </option>
                        </select>

                    </th>
                    <th>
                        <?php echo $last_visit; ?>
                    </th>
                    <th>
                        <input type='image' src='images/usercheck.png' title='<?php echo $lang['ADMIN_MEMBERS_VALIDATE'] . $name ?>'>
                    </th>
                </form>
                <form method='POST' action='index.php?action=delete_member&amp;user_id=<?php echo $user_id ;?>' onsubmit="return  confirm('<?php echo $lang['ADMIN_MEMBERS_DELETE_TITLE'] . $name?>');">
                    <th>
                        <input type='image' src='images/userdrop.png' title='<?php echo $lang['ADMIN_MEMBERS_DELETE'] . $name;?>'>
                    </th>
                </form>
                <form method='POST' action='index.php?action=new_password&amp;user_id=<?php echo $user_id;?>'>
                    <th>
                        <input type='image' src='images/userpwd.png' title='<?php echo $lang['ADMIN_MEMBERS_PWDCHANGE_TITLE'] . $name;?>'>
                    </th>
                </form>
                <th>
                    <!-- Todo ajouter ici l'envoi direct du nouveau mdp via mail -->
                </th>
            </tr>
        <?php endforeach; ?>
    </table>
</div>


<!-- Liste des joueurs-->
<div id="listPlayer2">
    <table>
        <tr>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_PLAYER']); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_EMAIL']); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_REGISTERED']); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_ENABLED']); ?>
            </td>
            <?php if ($user_data["user_admin"] == 1): ?>
                <td class="c" width="120">
                    <?php echo($lang['ADMIN_MEMBERS_COADMIN']); ?>
                </td>
            <?php endif; ?>
            <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1): ?>
                <td class="c" width="120">
                    <?php echo($lang['ADMIN_MEMBERS_MGMEMBERS']); ?>
                    <?php echo help("admin_member_manager"); ?>
                </td>
            <?php endif; ?>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_MGRANKS']); ?>
                <?php echo help("admin_ranking_manager"); ?>
            </td>
            <td class="c" width="120">
                <?php echo($lang['ADMIN_MEMBERS_LASTCONNECT']); ?>
            </td>
            <td class="c" colspan="4">&nbsp;</td>
        </tr>
        <?php foreach ($user_info as $v) : ?>
            <tr>
                <?php $user_id = $v["user_id"];?>
                <?php $name = $v["user_name"]; ?>
                <?php $email = $v["user_email"]; ?>
                <?php $reg_date = strftime("%d %b %Y %H:%M", $v["user_regdate"]); ?>
                <?php $active_off = !$v["user_active"] ? " selected" : ""; ?>
                <?php $user_coadmin_off = (!$v["user_coadmin"] && !$v["user_admin"]) ? " selected" : ""; ?>
                <?php $management_user_off = (!$v["management_user"] && !$v["user_admin"]) ? " selected" : ""; ?>
                <?php $management_ranking_off = (!$v["management_ranking"] && !$v["user_admin"]) ? " selected" : ""; ?>
                <?php if ($v["user_lastvisit"] != 0): ?>
                    <?php $last_visit = strftime("%d %b %Y %H:%M", $v["user_lastvisit"]); ?>
                <?php else : ?>
                    <?php $last_visit = "--"; ?>
                <?php endif; ?>
                <form method='POST' action='index.php?action=admin_modify_member&amp;user_id=<?php echo $user_id;?>'>
                    <th>
                        <a><?php echo $name; ?></a><!--todo voir pour popUp-->
                    </th>
                    <th>
                        <?php echo $email; ?>
                    </th>
                    <th>
                        <?php echo $reg_date; ?>
                    </th>
                    <th>
                        <!--compte actif-->
                        <select name='active'>
                            <option value='1'>
                                <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                            </option>
                            <option value='0' <?php echo $active_off; ?>>
                                <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                            </option>
                        </select>
                    </th>

                    <?php if ($user_data["user_admin"] == 1) : ?>
                        <th>
                            <select name='user_coadmin'>
                                <option value='1'>
                                    <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                                </option>
                                <option value='0' <?php echo $user_coadmin_off; ?>>
                                    <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                                </option>
                            </select>
                        </th>
                    <?php endif; //si l'utilisateur est co admin que ce passe t-il  ?'?>

                    <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1): ?>
                        <th> <!--gestion membres-->
                            <select name='management_user'>
                                <option value='1'>
                                    <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                                </option>
                                <option value='0' <?php echo $management_user_off; ?>>
                                    <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                                </option>
                            </select>
                        </th>
                    <?php endif; //section visible que des admin verif pertinente ???'?>
                    <th>
                        <select name='management_ranking'>
                            <option value='1'>
                                <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                            </option>
                            <option value='0' <?php echo $management_ranking_off; ?>>
                                <?php echo $lang['ADMIN_MEMBERS_NO'] ?>
                            </option>
                        </select>

                    </th>
                    <th>
                        <?php echo $last_visit; ?>
                    </th>
                    <th>
                        <input type='image' src='images/usercheck.png' title='<?php echo $lang['ADMIN_MEMBERS_VALIDATE'] . $name ?>'>
                    </th>
                </form>
                <form method='POST' action='index.php?action=delete_member&amp;user_id=<?php echo $user_id ;?>' onsubmit="return  confirm('<?php echo $lang['ADMIN_MEMBERS_DELETE_TITLE'] . $name?>');">
                    <th>
                        <input type='image' src='images/userdrop.png' title='<?php echo $lang['ADMIN_MEMBERS_DELETE'] . $name;?>'>
                    </th>
                </form>
                <form method='POST' action='index.php?action=new_password&amp;user_id=<?php echo $user_id;?>'>
                    <th>
                        <input type='image' src='images/userpwd.png' title='<?php echo $lang['ADMIN_MEMBERS_PWDCHANGE_TITLE'] . $name;?>'>
                    </th>
                </form>
                <th>
                    <!-- Todo ajouter ici l'envoi direct du nouveau mdp via mail -->
                </th>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

