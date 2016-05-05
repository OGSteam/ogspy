<?php

if (defined("INSTALL_IN_PROGRESS") || defined("UPGRADE_IN_PROGRESS")) {
	require_once ("../lang/".$ui_lang."/lang_install.php");
    require_once ("../lang/".$ui_lang."/lang_help.php");
}else
{
	require_once ("./lang/".$ui_lang."/lang_about.php");
	require_once ("./lang/".$ui_lang."/lang_admin.php");
    require_once ("./lang/".$ui_lang."/lang_cartography.php");
    require_once ("./lang/".$ui_lang."/lang_galaxy.php");
    require_once ("./lang/".$ui_lang."/lang_gcm.php");
    require_once ("./lang/".$ui_lang."/lang_header_tail.php");
    require_once ("./lang/".$ui_lang."/lang_help.php");
    require_once ("./lang/".$ui_lang."/lang_home.php");
    require_once ("./lang/".$ui_lang."/lang_login.php");
    require_once ("./lang/".$ui_lang."/lang_menu.php");
    require_once ("./lang/".$ui_lang."/lang_message.php");
    require_once ("./lang/".$ui_lang."/lang_profile.php");
    require_once ("./lang/".$ui_lang."/lang_ranking.php");
    require_once ("./lang/".$ui_lang."/lang_report.php");
    require_once ("./lang/".$ui_lang."/lang_search.php");
    require_once ("./lang/".$ui_lang."/lang_serverdown.php");
    require_once ("./lang/".$ui_lang."/lang_statistic.php");
}


