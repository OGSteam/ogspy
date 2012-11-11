<?php
/** $Id: update.php 7672 2012-08-05 21:33:46Z darknoon $ **/
/**
* update.php Met à jour le mod
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0a
* created	: 27/10/2006
* modified	: 19/01/2007
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
$mod_folder= "autoupdate";
$mod_name = "autoupdate";
update_mod($mod_folder,$mod_name);

if(file_exists("mod/autoupdate/modupdate.json")) {
	unlink("mod/autoupdate/modupdate.json");
	mod_set_option ( "CYCLEMAJ", "1");
	mod_set_option ( "BAN_MODS", "1");
	mod_set_option ( "DOWNJSON", "1");
	mod_set_option ( "MAJ_TRUNK", "0");
}
?>
