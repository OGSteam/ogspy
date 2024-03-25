<?php
/**
 * Panneau administration des options Membres
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyzer
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
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

<!--tableau de creation d'un nouvel utilisateur-->
<div id="creatingNewPlayer">

    <table class="og-table og-little-table">
        <thead>
            <tr>
                <th  colspan="3"><?php echo ($lang['ADMIN_MEMBERS_NEWACCOUNT']); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <input class="og-button" type="button" value="<?php echo ($lang['ADMIN_MEMBERS_NEWACCOUNT_BUTTON']); ?>" onclick=ogspy_beginCreateUser()>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<!-- formulaire de creation nouveau joueur-->
<div id="createNewPlayer" style="display: none;visibility: hidden" ;>
    <form method="POST" action="index.php?action=newaccount">
        <table class="og-table og-little-table">
            <thead>
                <tr>
                    <th colspan="2">
                        <?php echo ($lang['ADMIN_MEMBERS_POPUP_NEWACCOUNT']); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['ADMIN_MEMBERS_POPUP_NAME'] . help("profile_login")); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="pseudo" type="text" maxlength="15" size="20">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['ADMIN_MEMBERS_POPUP_PASSWORD'] . help("profile_password")); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="pass" type="text" maxlength="64" size="20">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        <?php echo ($lang['ADMIN_MEMBERS_POPUP_EMAIL']); ?>
                    </td>
                    <td class="tdvalue">
                        <input name="email" type="text" maxlength="50" size="35">
                    </td>
                </tr>
                <tr>
                    <td class="tdstat" rowspan="3">
                        <?php echo ($lang['ADMIN_MEMBERS_POPUP_RIGHTS']); ?>
                    </td>
                    <td class="tdvalue">
                        <!-- gestion des membres-->
                        <?php if ($user_data["user_admin"] == 1) : ?>
                            <?php echo $lang['ADMIN_MEMBERS_POPUP_MGTMEMBERS'] . " : "; ?>
                            <select name='user_coadmin'>"?>
                                <option value='1'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                                <option value='0' selected='selected'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
                            </select><br>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>       
                    <td class="tdvalue"><!-- gestion des Classements-->
                        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                            <?php echo $lang['ADMIN_MEMBERS_POPUP_MGTRANKS'] . help('admin_member_manager') . " : "; ?>
                            <select name='management_user'>"?>
                                <option value='1' selected='selected'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                                <option value='0'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
                            </select><br>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>   
                    <td class="tdvalue"><!--creation d un nouveau compte-->
                        <?php echo $lang['ADMIN_MEMBERS_POPUP_GROUP'] . help('admin_ranking_manager') . " : "; ?>
                        <select name='management_ranking'>"?>
                            <option value='1'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                            <option value='0'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tdstat">
                        Groupe :
                    </td>
                    <td class="tdvalue">
                        <select name="group_id">
                            <?php foreach ($usergroup_list as $value) : ?>
                                <option value="<?php echo $value["group_id"]; ?>">
                                    <?php echo $value["group_name"]; ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input class="og-button" type="submit" value="<?php echo ($lang['ADMIN_MEMBERS_PASSWORDOK']); ?>">
                        <input class="og-button og-button-warning " type="button" value="<?php echo ($lang['ADMIN_MEMBERS_PASSWORDCANCEL']); ?>" onclick=ogspy_endCreateUser()>
                    </td>
                </tr>   
            </tbody>
        </table>
        </td>
        </tr>
        </table>
    </form>
</div>
<!-- Fin formulaire de creation nouveau joueur-->


<!-- Liste des joueurs-->
<div id="listPlayer">
    <table class="og-table og-full-table">
        <thead>
            <tr>
                <th>
                    <?php echo ($lang['ADMIN_MEMBERS_PLAYER']); ?>
                </th>
                <th>
                    <?php echo ($lang['ADMIN_MEMBERS_EMAIL']); ?>
                </th>
                <th>
                    <?php echo ($lang['ADMIN_MEMBERS_REGISTERED']); ?>
                </th>
                <th>
                    <?php echo ($lang['ADMIN_MEMBERS_ENABLED']); ?>
                </th>
                <?php if ($user_data["user_admin"] == 1) : ?>
                    <th>
                        <?php echo ($lang['ADMIN_MEMBERS_COADMIN']); ?>
                    </th>
                <?php endif; ?>
                <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                    <th>
                        <?php echo ($lang['ADMIN_MEMBERS_MGMEMBERS']); ?>
                        <?php echo help("admin_member_manager"); ?>
                    </th>
                <?php endif; ?>
                <th>
                    <?php echo ($lang['ADMIN_MEMBERS_MGRANKS']); ?>
                    <?php echo help("admin_ranking_manager"); ?>
                </th>
                <th>
                    <?php echo ($lang['ADMIN_MEMBERS_LASTCONNECT']); ?>
                </th>
                <th colspan="4">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($user_info as $v) : ?>
                <tr>
                    <?php $user_id = $v["user_id"]; ?>
                    <?php $name = $v["user_name"]; ?>
                    <?php $email = $v["user_email"]; ?>
                    <?php $reg_date = date("d F o G:i", $v["user_regdate"]); ?>
                    <?php $active_off = !$v["user_active"] ? " selected" : ""; ?>
                    <?php $user_coadmin_off = (!$v["user_coadmin"] && !$v["user_admin"]) ? " selected" : ""; ?>
                    <?php $management_user_off = (!$v["management_user"] && !$v["user_admin"]) ? " selected" : ""; ?>
                    <?php $management_ranking_off = (!$v["management_ranking"] && !$v["user_admin"]) ? " selected" : ""; ?>
                    <?php if ($v["user_lastvisit"] != 0) : ?>
                        <?php $last_visit = date("d F o G:i", $v["user_lastvisit"]); ?>
                    <?php else : ?>
                        <?php $last_visit = "--"; ?>
                    <?php endif; ?>
            <form method='POST' action='index.php?action=admin_modify_member&amp;user_id=<?php echo $user_id; ?>'>
                <td>
                    <?php echo $name; ?>
                    <!--todo voir pour popUp-->
                </td>
                <td>
                    <?php echo $email; ?>
                </td>
                <td>
                    <?php echo $reg_date; ?>
                </td>
                <td>
                    <!--compte actif-->
                    <select name='active'>
                        <option value='1'>
                            <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                        </option>
                        <option value='0' <?php echo $active_off; ?>>
                            <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                        </option>
                    </select>
                </td>

                <?php if ($user_data["user_admin"] == 1) : ?>
                    <td>
                        <select name='user_coadmin'>
                            <option value='1'>
                                <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                            </option>
                            <option value='0' <?php echo $user_coadmin_off; ?>>
                                <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                            </option>
                        </select>
                    </td>
                <?php endif; //si l'utilisateur est co admin que ce passe t-il  ?'
                ?>

                <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                    <td>
                        <!--gestion membres-->
                        <select name='management_user'>
                            <option value='1'>
                                <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                            </option>
                            <option value='0' <?php echo $management_user_off; ?>>
                                <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                            </option>
                        </select>
                    </td>
                <?php endif; //section visible que des admin verif pertinente ???'
                ?>
                <td>
                    <select name='management_ranking'>
                        <option value='1'>
                            <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                        </option>
                        <option value='0' <?php echo $management_ranking_off; ?>>
                            <?php echo $lang['ADMIN_MEMBERS_NO'] ?>
                        </option>
                    </select>

                </td>
                <td>
                    <?php echo $last_visit; ?>
                </td>
                <td>
                    <input class="og-button og-button-image  og-button-success"  type='image' src='images/usercheck.png' title='<?php echo $lang['ADMIN_MEMBERS_VALIDATE'] . $name ?>'>
                </td>
            </form>
            <form method='POST' action='index.php?action=delete_member&amp;user_id=<?php echo $user_id; ?>' onsubmit="return  confirm('<?php echo $lang['ADMIN_MEMBERS_DELETE_TITLE'] . $name ?>');">
                <td>
                    <input class="og-button og-button-image  og-button-danger"  type='image' src='images/userdrop.png' title='<?php echo $lang['ADMIN_MEMBERS_DELETE'] . $name; ?>'>
                </td>
            </form>
            <form method='POST' action='index.php?action=new_password&amp;user_id=<?php echo $user_id; ?>'>
                <td>
                    <input class="og-button og-button-image og-button-warning"  type='image' src='images/userpwd.png'  title='<?php echo $lang['ADMIN_MEMBERS_PWDCHANGE_TITLE'] . $name; ?>'>
                </td>
            </form>
            <td>
                <!-- Todo ajouter ici l'envoi direct du nouveau mdp via mail -->
            </td>
            </tr>
        <?php endforeach; ?>

        </tbody>   
    </table>
</div>
