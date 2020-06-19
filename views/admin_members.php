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
        <thead>
        <tr>
            <th class="c" colspan="3">
                <?php echo($lang['ADMIN_MEMBERS_NEWACCOUNT']); ?>
            </th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <th>
                    <input type="button"  class="button" value="<?php echo($lang['ADMIN_MEMBERS_NEWACCOUNT_BUTTON']); ?>"
                           onclick=beginCreateUser()>
                </th>
            </tr>
        </tbody>

    </table>

</div>


<!-- formulaire de creation nouveau joueur-->
<div id="createNewPlayer" style="display: none;visibility: hidden" ;>
<form method="POST"  action="index.php?action=newaccount" >
    <fieldset>
        <p class="legend"><?php echo($lang['ADMIN_MEMBERS_POPUP_NEWACCOUNT']); ?></p>

        <div>
            <label for="pseudo"><?php echo($lang['ADMIN_MEMBERS_POPUP_NAME']); ?></label>
            <input type="text" maxlength="64" size="20"  name='pseudo' id='pseudo'/>
        </diV>
        <div>
        <label for="pass"><?php echo($lang['ADMIN_MEMBERS_POPUP_PASSWORD']); ?></label>
        <input type="password" maxlength="15" size="20"  name='pass' id='pass'/>
        </div>
        <div>
        <label for="email"><?php echo($lang['ADMIN_MEMBERS_POPUP_EMAIL']); ?></label>
        <input type="email" maxlength="50" size="35" name='email' id='email'/>
        </div>
    </fieldset>

    <fieldset>
        <p class="legend"><?php echo($lang['ADMIN_MEMBERS_POPUP_RIGHTS']); ?></p>
        <?php if ($user_data["user_admin"] == 1) : ?>
            <!-- gestion des membres-->
        <div>
            <label for="user_coadmin"><?php echo($lang['ADMIN_MEMBERS_POPUP_MGTMEMBERS']); ?></label>
            <select name='user_coadmin' id='user_coadmin' ">
                <option value='1'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                <option value='0' selected='selected'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
            </select>
        </div>

        <?php  endif ; ?>
        <!-- gestion des Classements-->
        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1)  : ?>
        <div>
            <label for="management_user"><?php echo($lang['ADMIN_MEMBERS_POPUP_MGTMEMBERS']); ?> <?php echo help('admin_member_manager') ; ?></label>
            <select name='management_user' id='management_user' ">
                <option value='1'  selected='selected'><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
                <option value='0'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
            </select>
        </div>
        <?php  endif ; ?>
        <!--creation d un nouveau compte-->
        <div>
        <label for="management_ranking"><?php echo($lang['ADMIN_MEMBERS_POPUP_GROUP']); ?> <?php echo help('admin_ranking_manager') ; ?></label>
        <select name='management_ranking' id='management_ranking' ">
        <option value='1' ><?php echo $lang['ADMIN_MEMBERS_YES']; ?></option>
        <option value='0'  selected='selected'> <?php echo $lang['ADMIN_MEMBERS_NO']; ?></option>
        </select>
        </div>
        <div>
        <label for="group_id">Groupe : </label>
        <select name="group_id" id="group_id">
            <?php foreach ($usergroup_list as $value) : ?>
                <option value="<?php echo $value["group_id"]; ?>">
                    <?php echo $value["group_name"]; ?>
                </option>
            <?php endforeach ?>
        </select>
        </div>

    </fieldset>
    <div class="sep">
    </div>
    <div>
            <input type="submit" class="button" value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDOK']); ?>">
            <input type="button "class="button else"  value="<?php echo($lang['ADMIN_MEMBERS_PASSWORDCANCEL']); ?>" onclick=endCreateUser()>
    </div>




</div>

<!-- Fin formulaire de creation nouveau joueur-->


<!-- Liste des joueurs-->
<div id="listPlayer">
    <table>
        <thead>
        <tr>
            <th>
                <?php echo($lang['ADMIN_MEMBERS_PLAYER']); ?>
            </th>
            <th>
                <?php echo($lang['ADMIN_MEMBERS_EMAIL']); ?>
            </th>
            <th>
                <?php echo($lang['ADMIN_MEMBERS_REGISTERED']); ?>
            </th>
            <th>
                <?php echo($lang['ADMIN_MEMBERS_ENABLED']); ?>
            </th>
            <?php if ($user_data["user_admin"] == 1): ?>
                <th>
                    <?php echo($lang['ADMIN_MEMBERS_COADMIN']); ?>
                </th>
            <?php endif; ?>
            <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1): ?>
                <th>
                    <?php echo($lang['ADMIN_MEMBERS_MGMEMBERS']); ?>
                    <?php echo help("admin_member_manager"); ?>
                </th>
            <?php endif; ?>
            <th>
                <?php echo($lang['ADMIN_MEMBERS_MGRANKS']); ?>
                <?php echo help("admin_ranking_manager"); ?>
            </th>
            <th>
                <?php echo($lang['ADMIN_MEMBERS_LASTCONNECT']); ?>
            </th>
            <th colspan="4">&nbsp;</th>
        </tr>


        </thead>
<tbody>
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
            <td>
                <a><?php echo $name; ?></a><!--todo voir pour popUp-->
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
            <?php endif; //si l'utilisateur est co admin que ce passe t-il  ?'?>

            <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1): ?>
                <td> <!--gestion membres-->
                    <select name='management_user'>
                        <option value='1'>
                            <?php echo $lang['ADMIN_MEMBERS_YES']; ?>
                        </option>
                        <option value='0' <?php echo $management_user_off; ?>>
                            <?php echo $lang['ADMIN_MEMBERS_NO']; ?>
                        </option>
                    </select>
                </td>
            <?php endif; //section visible que des admin verif pertinente ???'?>
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
                <input type='image' src='images/usercheck.png' title='<?php echo $lang['ADMIN_MEMBERS_VALIDATE'] . $name ?>'>
            </td>
        </form>
        <form method='POST' action='index.php?action=delete_member&amp;user_id=<?php echo $user_id ;?>' onsubmit="return  confirm('<?php echo $lang['ADMIN_MEMBERS_DELETE_TITLE'] . $name?>');">
            <td>
                <input type='image' src='images/userdrop.png' title='<?php echo $lang['ADMIN_MEMBERS_DELETE'] . $name;?>'>
            </td>
        </form>
        <form method='POST' action='index.php?action=new_password&amp;user_id=<?php echo $user_id;?>'>
            <td>
                <input type='image' src='images/userpwd.png' title='<?php echo $lang['ADMIN_MEMBERS_PWDCHANGE_TITLE'] . $name;?>'>
            </td>
        </form>
        <th>
            <!-- Todo ajouter ici l'envoi direct du nouveau mdp via mail -->
        </th>
    </tr>
<?php endforeach; ?>

</tbody>

    </table>
</div>



