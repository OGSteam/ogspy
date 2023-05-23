<?php
/**
 * Main menu
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyser
 * @created 15/12/2005
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
?>
<div id="menu_timer">
    <?php echo ($lang['MENU_SERVER_TIME']); ?><br />
    <span id="datetime"><?php echo ($lang['MENU_WAITING']); ?></span>
</div>

<?php if ($server_config["server_active"] == 0) : ?>
    <div id="menu_offline">
        <?php echo $lang['MENU_SERVER_OFFLINE']; ?>
    </div>
<?php endif; ?>

<div id="menu_navigate_id">
    <ul class="menu_navigate">
        <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1 || $user_data["management_user"] == 1) : ?>
            <li>
                <a href='index.php?action=administration' ><?php echo $lang['MENU_ADMIN']; ?></a>
            </li>
        <?php endif; ?>
        <li>
            <a href='index.php?action=profile' ><?php echo ($lang['MENU_PROFILE']); ?></a>
        </li>
        <li class='menu-separator'>
            <a href='index.php?action=home'><?php echo ($lang['MENU_HOME']); ?></a>
        </li>
        <li>
            <a href='index.php?action=galaxy' ><?php echo ($lang['MENU_GALAXY']); ?></a>
        </li>
        <li>
            <a href='index.php?action=cartography'><?php echo ($lang['MENU_ALLIANCES']); ?></a>
        </li>
        <li>
            <a href='index.php?action=search' ><?php echo ($lang['MENU_RESEARCH']); ?></a>
        </li>
        <li>
            <a href='index.php?action=ranking' ><?php echo ($lang['MENU_RANKINGS']); ?></a>
        </li>
        <li class='menu-separator'>
            <a href='index.php?action=statistic' ><?php echo ($lang['MENU_UPDATE_STATUS']); ?></a>
        </li>
        <li class='menu-separator'>
            <?php echo ($lang['MENU_MODULES']); ?>
            <ul class="sub_menu_navigate">
                <?php
//todo sortir requete de la vue
                $mod_model = new \Ogsteam\Ogspy\Model\Mod_Model();
                $tMods = $mod_model->find_by(array("active" => "1"), array("position" => 'ASC', "title" => 'ASC'));
                ?>
                <li class='menu-separator'></li>
                <!-- mod non admin -->
                <?php foreach ($tMods as $mod) : ?>
                    <?php if ($mod['admin_only'] == 0) : ?>
                        <li class="menu_mod_normal">
                            <a href="index.php?action=<?php echo $mod['action']; ?>"><?php echo $mod['menu']; ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- mod admin -->
                <li class='menu-separator'></li>
                <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                    <?php foreach ($tMods as $mod) : ?>
                        <?php if ($mod['admin_only'] == 1) : ?>
                            <li class="menu_mod_admin">
                                <a href="index.php?action=<?php echo $mod['action']; ?>"><?php echo $mod['menu']; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </li>

        <?php if ($server_config["url_forum"] != ""): ?>
            <li class='menu-separator'>
                <a href='<?php echo $server_config["url_forum"]; ?>' class='menu_items'><?php echo $lang['MENU_FORUM']; ?></a>
            </li>
        <?php endif; ?>


        <li class='menu-separator'>
            <a href="index.php?action=about"><?php echo $lang['MENU_ABOUT']; ?></a>
        </li>
        
        <li class='menu-separator'>
            <a href='index.php?action=logout'><?php echo $lang['MENU_LOGOUT']; ?></a>
        </li>

    </ul>
</div>    



