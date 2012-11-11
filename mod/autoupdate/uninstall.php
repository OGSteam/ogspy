<?php
/** $Id: uninstall.php 7672 2012-08-05 21:33:46Z darknoon $ **/
/**
* uninstall.php Désinstall le mod
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 27/10/2006
* modified	: 19/01/2007
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$mod_uninstall_name = "autoupdate";
uninstall_mod($mod_unistall_name,$mod_uninstall_table);

if(file_exists("mod/autoupdate/modupdate.json")) {
	unlink("mod/autoupdate/modupdate.json");
}
if(file_exists("parameters/modupdate.json")) {
	unlink("parameters/modupdate.json");
}

mod_del_all_option ();
?>
