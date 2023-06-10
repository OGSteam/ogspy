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
    <!--<?php echo ($lang['MENU_SERVER_TIME']); ?><br /> // Limite la place utilisé-->
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
            <!--<li class='menuitem-administration'>
                <a href='index.php?action=administration' ><?php echo $lang['MENU_ADMIN']; ?></a>
            </li>-->
            <li class='menuitem-admin-grp'>
                <a><?php echo $lang['MENU_ADMIN']; ?></a>
                <ul class="sub_menu_navigate">
                    <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                        <li class='menuitem-admin-infoserver'>
                            <a href='index.php?action=administration&amp;subaction=infoserver' ><?php echo ($lang['ADMIN_TITLE_GENERAL_INFO']); ?></a>
                        </li>
                        <li class='menuitem-admin-parameter'>
                            <a href='index.php?action=administration&amp;subaction=parameter' ><?php echo ($lang['ADMIN_TITLE_SERVER_CONF']); ?></a>
                        </li>
                        <li class='menuitem-admin-affichage'>
                            <a href='index.php?action=administration&amp;subaction=affichage' ><?php echo ($lang['ADMIN_TITLE_DISPLAY_CONF']); ?></a>
                        </li>
                    <?php endif; ?>
                    <li class='menuitem-admin-member'>
                        <a href='index.php?action=administration&amp;subaction=member' ><?php echo ($lang['ADMIN_TITLE_MEMBER_CONF']); ?></a>
                    </li>
                    <li class='menuitem-admin-group'>
                        <a href='index.php?action=administration&amp;subaction=group' ><?php echo ($lang['ADMIN_TITLE_GROUP_CONF']); ?></a>
                    </li>
                    <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                        <li class='menuitem-admin-viewer'>
                            <a href='index.php?action=administration&amp;subaction=viewer' ><?php echo ($lang['ADMIN_TITLE_LOGS_CONF']); ?></a>
                        </li>
                        <li class='menuitem-admin-helper'>
                            <a href='index.php?action=administration&amp;subaction=helper' ><?php echo ($lang['ADMIN_TITLE_HELPER_CONF']); ?></a>
                        </li>
                        <li class='menuitem-admin-mod'>
                            <a href='index.php?action=administration&amp;subaction=mod' ><?php echo ($lang['ADMIN_TITLE_MODS_CONF']); ?></a>
                        </li>
                    <?php endif; ?>

                </ul>            
            </li>
        <?php endif; ?>

        <li class='menuitem-profile'>
            <a href='index.php?action=profile' ><?php echo ($lang['MENU_PROFILE']); ?></a>
        </li>
         <li class='menuitem-home-grp'>
            <a><?php echo ($lang['MENU_HOME']); ?></a>
            <ul class="sub_menu_navigate">
                <li class='menuitem-home-empire'>
                    <a href='index.php?action=home&amp;subaction=empire' ><?php echo ($lang['HOME_EMPIRE_TITLE']); ?></a>
                </li>
                <li class='menuitem-home-simulation'>
                    <a href='index.php?action=home&amp;subaction=simulation'><?php echo ($lang['HOME_SIMULATION_TITLE']); ?></a>
                </li>
                <li class='menuitem-home-spy'>
                    <a href='index.php?action=home&amp;subaction=spy' ><?php echo ($lang['HOME_REPORTS_TITLE']); ?></a>
                </li>
                <li class='menuitem-home-stat'>
                    <a href='index.php?action=home&amp;subaction=stat' ><?php echo ($lang['HOME_STATISTICS_TITLE']); ?></a>
                </li>
            </ul>            
         </li>

        <li class='menuitem-ogspy-grp'>
            <a><!-- <?php echo ($lang['MENU_GALAXY']); ?>--> Ogspy </a>
            <ul class="sub_menu_navigate">
                <li class='menuitem-ogspy-galaxy'>
                    <a href='index.php?action=galaxy' ><?php echo ($lang['MENU_GALAXY']); ?></a>
                </li>
                <li class='menuitem-ogspy-cartography'>
                    <a href='index.php?action=cartography'><?php echo ($lang['MENU_ALLIANCES']); ?></a>
                </li>
                <li class='menuitem-ogspy-search'>
                    <a href='index.php?action=search' ><?php echo ($lang['MENU_RESEARCH']); ?></a>
                </li>
                <li class='menuitem-ogspy-ranking'>
                    <a href='index.php?action=ranking' ><?php echo ($lang['MENU_RANKINGS']); ?></a>
                </li>
                <li class='menuitem-ogspy-statistic'>
                    <a href='index.php?action=statistic' ><?php echo ($lang['MENU_UPDATE_STATUS']); ?></a>
                </li>
            </ul>            
        </li>

        <li class='menuitem-mod'>
            <a><?php echo ($lang['MENU_MODULES']); ?></a>
            <ul class="sub_menu_navigate">
                <?php
//todo sortir requete de la vue
                $mod_model = new \Ogsteam\Ogspy\Model\Mod_Model();
                $tMods = $mod_model->find_by(array("active" => "1"), array("position" => 'ASC', "title" => 'ASC'));
                ?>
                <!-- mod non admin -->
                <?php foreach ($tMods as $mod) : ?>
                    <?php if ($mod['admin_only'] == 0) : ?>
                        <li class='menusubitem-mod menusubitem-mod-<?php echo $mod['action']; ?>'>
                            <a href="index.php?action=<?php echo $mod['action']; ?>"><?php echo $mod['menu']; ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- mod admin -->
            </ul>            
            <ul class="sub_menu_navigate sub_menu_navigate_admin">
                <?php if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) : ?>
                    <?php foreach ($tMods as $mod) : ?>
                        <?php if ($mod['admin_only'] == 1) : ?>
                            <li class='menusubitem-mod-admin menusubitem-mod-<?php echo $mod['action']; ?>'>
                                <a href="index.php?action=<?php echo $mod['action']; ?>"><?php echo $mod['menu']; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </li>

        <?php if ($server_config["url_forum"] != ""): ?>
            <li class='menuitem-forum'>
                <a href='<?php echo $server_config["url_forum"]; ?>' ><?php echo $lang['MENU_FORUM']; ?></a>
            </li>
        <?php endif; ?>


        <li class='menuitem-about'>
            <a href="index.php?action=about"><?php echo $lang['MENU_ABOUT']; ?></a>
        </li>

        <li class='menuitem-logout'>
            <a href='index.php?action=logout'><?php echo $lang['MENU_LOGOUT']; ?></a>
        </li>

    </ul>
</div>    



