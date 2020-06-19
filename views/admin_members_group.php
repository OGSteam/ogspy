<?php
/**
 * Panneau administration des options de groupes de Membres
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

$usergroup_list = usergroup_get();
$usergroup_info = false;
if (isset($pub_group_id)) {
    if (check_var($pub_group_id, "Num")) {
        $group_id = $pub_group_id;
        $usergroup_info = usergroup_get($group_id);
    }
}
?>

<form method="POST" action="index.php?action=usergroup_create">
    <p class="legend"><?php echo($lang['ADMIN_GROUP_CREATE']); ?></p>
    <div>
        <label for="groupname"><?php echo($lang['ADMIN_GROUP_NAME']); ?></label>
        <input name="groupname" id="groupname" type="text" maxlength="15" size="20">
    </div>
    <div>
        <label> </label>
        <input type="submit" class="button" value="<?php echo($lang['ADMIN_GROUP_CREATENEW']); ?>">
    </div>
</form>


<form method="POST" action="index.php?action=administration&subaction=group">
    <p class="legend"><?php echo($lang['ADMIN_GROUP_RIGHTS']); ?></p>
    <div>
        <label for="group_id"><?php echo($lang['ADMIN_GROUP_RIGHTS']); ?></label>
        <select id="group_id" name="group_id">
            <option><?php echo($lang['ADMIN_GROUP_SELECT']); ?></option>
            <?php
            foreach ($usergroup_list as $value) {
                echo "\t\t\t\t" . "<option value='" . $value["group_id"] . "'>" . $value["group_name"] . "</option>";
            }
            ?>
        </select>
    </div>
    <div>
        <label> </label>
        <input type="submit" class="button" value="<?php echo($lang['ADMIN_GROUP_SEEPERMISSIONS']); ?>">
    </div>
</form>



<?php if ($usergroup_info !== false)  : ?>
<?php   $usergroup_member = usergroup_member($group_id); ?>
    <?php if (sizeof($usergroup_member) > 0) :?>
    <table>
        <thead>
        <tr>
            <th colspan="2">
                <?php echo($lang['ADMIN_GROUP_MEMBERS']." ".$usergroup_info["group_name"]); ?>
            </th>
        </tr>
        </thead>
        <tbody>

            <?php foreach ($usergroup_member as $user) :?>
                <tr>
                    <td><?php echo $user["user_name"] ;?></td>
                    <td><a href="index.php?action=usergroup_delmember&amp;user_id=<?php echo $user["user_id"];?>&group_id=<?php echo $group_id;?>"><img src='images/userdrop.png' title='<?php echo $lang['ADMIN_GROUP_DELETE'] . $user["user_name"];?>'></a></td>
                </tr>
            <?php endforeach ;?>
        </tbody>
    </table>
    <?php endif ;?>

    <form method='POST' action='index.php?action=usergroup_newmember'>
       <input type='hidden' name='group_id' value='<?php echo $group_id;?>'>
        <div>
            <label>
                <?php echo $lang['ADMIN_GROUP_MEMBERLIST'];?>
            </label>
            <?php      $user_list = user_get(); ; ?>
            <select name='user_id'>
                <option><?php echo $lang['ADMIN_GROUP_MEMBERLIST'];?></option>
                <?php foreach ($user_list as $user) : ?>
                    <option value="<?php echo $user["user_id"]; ?>">
                        <?php  echo $user["user_name"];?>
                    </option>
                <?php  endforeach;; ?>
            </select>
        </div>
        <div>
            <label></label>
            <input class="button" type='submit' value='<?php echo $lang['ADMIN_GROUP_ADD']; ?>'>
        </div>
        <div>
            <label></label>
            <input class="button else" type='submit'  name='add_all'  value='<?php echo $lang['ADMIN_GROUP_ADDALL']; ?>'>
        </div>

    

    </form>

    <?php if ($group_id != 1) :?>

        <form method="POST" action="index.php?action=usergroup_delete"
              onsubmit="return confirm('<?php echo($lang['ADMIN_GROUP_DELETE']); ?>');">
            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
            <div>
                <label>

                </label>
                <input type="submit" class="button warning" value="<?php echo($lang['ADMIN_GROUP_DELETE_BUTTON']); ?>">
            </div>

        </form>


    <?php endif ; ?>

<form method="POST" action="index.php?action=usergroup_setauth">
    <input type="hidden" name="group_id" value="<?php echo $usergroup_info["group_id"]; ?>">
    <div>
        <label><?php echo($lang['ADMIN_GROUP_NAME']); ?></label>
        <input type="text" name="group_name" value="<?php echo $usergroup_info["group_name"]; ?>">
    </div>
    <p class="legend"><?php echo($lang['ADMIN_GROUP_SERVERRIGHTS']); ?> (<?php echo($lang['ADMIN_GROUP_RIGHTS']); ?>)</p>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_RIGHTS_ADDSYSTEMS']); ?></label>
        <input name="server_set_system" type="checkbox" value="1" <?php echo ($usergroup_info["server_set_system"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_RIGHTS_ADDCOMBAT']); ?></label>
        <input name="server_set_rc" type="checkbox" value="1" <?php echo ($usergroup_info["server_set_rc"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_RIGHTS_ADDSPY']); ?></label>
        <input name="server_set_spy" type="checkbox" value="1" <?php echo ($usergroup_info["server_set_spy"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_RIGHTS_ADDRANK']); ?></label>
        <input name="server_set_ranking" type="checkbox" value="1" <?php echo ($usergroup_info["server_set_ranking"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_RIGHTS_PROTECTEDALLY']); ?></label>
        <input name="server_show_positionhided" type="checkbox" value="1" <?php echo ($usergroup_info["server_show_positionhided"]) ? "checked" : "" ?>>
    </div>

    <p class="legend"><?php echo($lang['ADMIN_GROUP_RIGHTS_EXTCLIENTS']); ?> (<?php echo($lang['ADMIN_GROUP_RIGHTS']); ?>)</p>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_RIGHTS_CONNECT']); ?></label>
        <input name="ogs_connection" type="checkbox" value="1" <?php echo ($usergroup_info["ogs_connection"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_EXTERNAL_ADDSYSTEM']); ?></label>
        <input name="ogs_set_system" type="checkbox" value="1" <?php echo ($usergroup_info["ogs_set_system"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_EXTERNAL_GETSYSTEM']); ?></label>
        <input name="ogs_get_system" type="checkbox" value="1" <?php echo ($usergroup_info["ogs_get_system"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_EXTERNAL_ADDREPORT']); ?></label>
        <input name="ogs_set_spy" type="checkbox" value="1" <?php echo ($usergroup_info["ogs_set_spy"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_EXTERNAL_GETREPORT']); ?></label>
        <input name="ogs_get_spy" type="checkbox" value="1" <?php echo ($usergroup_info["ogs_get_spy"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_EXTERNAL_ADDRANK']); ?></label>
        <input name="ogs_set_ranking" type="checkbox" value="1" <?php echo ($usergroup_info["ogs_set_ranking"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label><?php echo($lang['ADMIN_GROUP_EXTERNAL_GETRANK']); ?></label>
        <input name="ogs_get_ranking" type="checkbox" value="1" <?php echo ($usergroup_info["ogs_get_ranking"]) ? "checked" : "" ?>>
    </div>
    <div>
        <label></label>
        <input type="submit" class=""button" value="<?php echo($lang['ADMIN_GROUP_EXTERNAL_VALIDATE']); ?>">
    </div>

</form>

<?php endif; ?>
