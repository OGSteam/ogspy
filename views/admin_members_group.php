<?php global $user_data, $lang;
/**
 * Panneau administration des options de groupes de Membres
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

if ($user_data["admin"] != 1 && $user_data["coadmin"] != 1 && $user_data["management_user"] != 1) {
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
    <table class="og-table og-little-table">
        <thead>
            <tr>
                <th  colspan="3"><?= ($lang['ADMIN_GROUP_CREATE']) ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="tdstat">
                    <?= ($lang['ADMIN_GROUP_NAME']) ?>
                </td>
                <td>
                    <input name="groupname" type="text" maxlength="15" size="20">
                </td>
                <td>
                    <input class="og-button"  type="submit" value="<?= ($lang['ADMIN_GROUP_CREATENEW']) ?>">
                </td>
            </tr>
        </tbody>
    </table>
</form>
<table>


    <table class="og-table og-little-table">
        <form method="POST" action="index.php?action=administration&subaction=group">
            <thead>
                <tr>
                    <th  colspan="2"><?= ($lang['ADMIN_GROUP_RIGHTS']) ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="group_id">
                            <option><?= ($lang['ADMIN_GROUP_SELECT']) ?></option>
                            <?php foreach ($usergroup_list as $usergroup) : ?>
                                <option value='<?= $usergroup["id"] ?>'>
                                    <?= $usergroup["name"] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input class="og-button"  type="submit" value="<?= ($lang['ADMIN_GROUP_SEEPERMISSIONS']) ?>">
                    </td>
                </tr>
            </tbody>
        </form>
    </table>
    <?php if ($usergroup_info !== false) : ?>
        <?php $usergroup_member = usergroup_member($group_id); ?>
        <table class="og-table og-medium-table">
            <thead>
                <tr>
                    <th colspan="8"><?= ($lang['ADMIN_GROUP_MEMBERS']) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (sizeof($usergroup_member) > 0) : ?>
                    <?php $index = 0; ?>
                    <tr>
                        <?php foreach ($usergroup_member as $user) : ?>
                            <?php if ($index == 4): ?>
                                <?php $index = 0; ?>
                            </tr><!-- retour ligne -->
                            <tr>
                            <?php endif; ?>
                    <form method='POST' action='index.php?action=usergroup_delmember&amp;user_id=<?= $user["id"] ?>&group_id=<?= $group_id ?>' onsubmit="return confirm("<?= $lang['ADMIN_GROUP_DELETE_CONFIRMATION'] . $user["name"] ?>?");>
                        <td>
                            <?= $user["name"] ?>
                        </td>
                        <td>
                            <input class="og-button og-button-image  og-button-danger"  type="image" src='images/userdrop.png' title="<?= $lang['ADMIN_GROUP_DELETE'] . $user["name"] ?> ">
                        </td>
                    </form>
                    <?php $index++; ?>
                <?php endforeach; ?>
                <?php for ($index; $index < 4; $index++): ?>
                    <td>&nbsp;</td><td>&nbsp;</td>
                <?php endfor; ?>
                </tr>
            <?php endif; ?>
            <?php $user_list = user_get(); ?>
            <form method='POST' action='index.php?action=usergroup_newmember'>
                <input type='hidden' name='group_id' value='<?= $group_id ?>'>
                <tr>
                    <td colspan='2'>
                        <select name='user_id'>
                            <option><?= $lang['ADMIN_GROUP_MEMBERLIST'] ?></option>";
                            <?php foreach ($user_list as $user): ?>
                                <option value='<?= $user["id"] ?>'><?= $user["name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td colspan='2'>
                        <input  class="og-button" type='submit' value='<?= $lang['ADMIN_GROUP_ADD'] ?>'>
                    </td>
                    <td colspan='4'>
                        <input class="og-button" type='submit' name='add_all' value='<?= $lang['ADMIN_GROUP_ADDALL'] ?>'>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>


        <table class="og-table og-little-table">
            <thead>
                <tr>
                    <th colspan="2"><?= $usergroup_info["name"] ?></th>
                </tr>
            </thead>

            <form method="POST" action="index.php?action=usergroup_setauth">
                <input type="hidden" name="group_id" value="<?= $usergroup_info["id"] ?>">
                <tbody>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_NAME']) ?>
                        </td>
                        <td class="tdvalue">
                            <input type="text" name="group_name" value="<?= $usergroup_info["name"] ?>">
                        </td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th><?= ($lang['ADMIN_GROUP_SERVERRIGHTS']) ?></th>
                        <th><?= ($lang['ADMIN_GROUP_RIGHTS']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_RIGHTS_ADDSYSTEMS']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="server_set_system" type="checkbox" value="1" <?= ($usergroup_info["server_set_system"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_RIGHTS_ADDCOMBAT']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="server_set_rc" type="checkbox" value="1" <?= ($usergroup_info["server_set_rc"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_RIGHTS_ADDSPY']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="server_set_spy" type="checkbox" value="1" <?= ($usergroup_info["server_set_spy"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_RIGHTS_ADDRANK']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="server_set_ranking" type="checkbox" value="1" <?= ($usergroup_info["server_set_ranking"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_RIGHTS_PROTECTEDALLY']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="server_show_positionhided" type="checkbox" value="1" <?= ($usergroup_info["server_show_positionhided"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th><?= ($lang['ADMIN_GROUP_RIGHTS_EXTCLIENTS']) ?></th>
                        <th><?= ($lang['ADMIN_GROUP_RIGHTS']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_RIGHTS_CONNECT']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="ogs_connection" type="checkbox" value="1" <?= ($usergroup_info["ogs_connection"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_EXTERNAL_ADDSYSTEM']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="ogs_set_system" type="checkbox" value="1" <?= ($usergroup_info["ogs_set_system"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_EXTERNAL_GETSYSTEM']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="ogs_get_system" type="checkbox" value="1" <?= ($usergroup_info["ogs_get_system"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_EXTERNAL_ADDREPORT']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="ogs_set_spy" type="checkbox" value="1" <?= ($usergroup_info["ogs_set_spy"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_EXTERNAL_GETREPORT']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="ogs_get_spy" type="checkbox" value="1" <?= ($usergroup_info["ogs_get_spy"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_EXTERNAL_ADDRANK']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="ogs_set_ranking" type="checkbox" value="1" <?= ($usergroup_info["ogs_set_ranking"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdstat">
                            <?= ($lang['ADMIN_GROUP_EXTERNAL_GETRANK']) ?>
                        </td>
                        <td class="tdvalue">
                            <input name="ogs_get_ranking" type="checkbox" value="1" <?= ($usergroup_info["ogs_get_ranking"]) ? "checked" : "" ?>>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input class="og-button" type="submit" value="<?= ($lang['ADMIN_GROUP_EXTERNAL_VALIDATE']) ?>">
                        </td>
                    </tr>
                </tbody>
            </form>
            <?php if ($group_id != 1) : ?>
                <tbody>
                <form method="POST" action="index.php?action=usergroup_delete" onsubmit="return confirm('<?= ($lang['ADMIN_GROUP_DELETE']) ?>');">
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="group_id" value="<?= $group_id ?>">
                            <input class="og-button og-button-danger" type="submit" value="<?= ($lang['ADMIN_GROUP_DELETE_BUTTON']) ?>">
                        </td>
                    </tr>
                </form>
                </tbody>
            <?php endif; ?>

        </table>


    <?php endif; ?>



