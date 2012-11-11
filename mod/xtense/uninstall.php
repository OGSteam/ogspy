<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @version 1.0
 * @licence GNU
 */

if (!defined('IN_SPYOGAME') && !defined('IN_UNISPY2')) {
    die("Hacking attempt");
}

global $de,$table_prefix;
$mod_uninstall_name = "xtense";
$mod_uninstall_table = $table_prefix."xtense_groups".','.$table_prefix."xtense_callbacks";
uninstall_mod ($mod_uninstall_name, $mod_uninstall_table);

require_once("mod/{$root}/includes/config.php");

$db->sql_query('DELETE FROM '.TABLE_CONFIG.' WHERE config_name LIKE "xtense_%"');
generate_config_cache();

?>