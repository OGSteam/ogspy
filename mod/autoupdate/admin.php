<?php
/** $Id: admin.php 7668 2012-07-15 22:16:03Z darknoon $ **/
/**
* admin.php Partie admin du mod
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 13/11/2006
* modified	: 18/01/2007
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$error = "";
if (isset($pub_valid)) {
	
    if(empty($pub_cycle)) $pub_cycle = 0;
	//$generated = generate_parameters($pub_coadmin, $pub_down, $pub_cycle, date("d"), date("H"), 0, $pub_autoMaJ, $pub_banlist);
     mod_set_option ( "CYCLEMAJ", $pub_cycle);
     mod_set_option ( "BAN_MODS", $pub_banlist);
     mod_set_option ( "DOWNJSON", $pub_down);
     mod_set_option ( "MAJ_TRUNK", $pub_majtrunk);
  
}

// Récupération des paramètres dans le fichier parameters.php

$arr = get_defined_functions();

foreach($arr as $zeile){
	sort($zeile);$s=0;
	foreach($zeile as $bzeile){
		$s = ($s) ? 0 : 1;
		if($bzeile == 'versionmod' OR $bzeile == 'generate_parameters' OR $bzeile == 'copymodupdate' OR $bzeile == 'json_decode') {
			if($bzeile == 'versionmod') {
				$versionmod = $bzeile;
			}
			if($bzeile == 'copymodupdate') {
				$copymodupdate = $bzeile;
			}
			if($bzeile == 'json_decode') {
				$json_on = $bzeile;
			}
		}
	}
}
?>
<table>
	<tr>
		<td class="c" colspan="2"><?php echo $lang['autoupdate_admin_list']; ?></a>
	</tr>
	<tr>
		<td class="c">Versionmod</th>
		<th><?php if(empty($versionmod)) echo $lang['autoupdate_admin_off']; else echo $lang['autoupdate_admin_define']; ?></th>
	</tr>
	<tr>
		<td class="c">Copymodupdate</td>
		<th><?php if(empty($copymodupdate)) echo $lang['autoupdate_admin_off']; else echo $lang['autoupdate_admin_define']; ?></th>
	</tr>
	<tr>
		<td class="c">JSON_Decode</td>
		<th><?php if(empty($json_on)) echo $lang['autoupdate_admin_off']; else echo $lang['autoupdate_admin_define']; ?></th>
	</tr>
</table>
<br />
<table>
	<tr>
		<td class="c"><?php echo $lang['autoupdate_admin_option']; ?></td>
		<td class="c" align="center"><?php echo $lang['autoupdate_admin_value']; ?><br /><?php echo $lang['autoupdate_admin_value1']; ?></td>
	</tr>
	<form action="index.php?action=autoupdate&sub=admin" method="post">
	<tr>
		<th><?php echo $lang['autoupdate_admin_down']; ?><br /><?php echo $lang['autoupdate_admin_down1']; ?></th>
		<th><input type="radio" name="down" <?php echo (mod_get_option("DOWNJSON") == 1) ? 'checked' : ''; ?> value="1"/> <font size="5">|</font> <input type="radio" name="down" <?php echo (mod_get_option("DOWNJSON") == 0) ? 'checked' : ''; ?> value="0"/></th>
	</tr>
	<tr>
		<th><?php echo $lang['autoupdate_admin_frequency']; ?></th>
		<th><input name="cycle" type="text" size="3" maxlength="2" value="<?php echo mod_get_option("CYCLEMAJ");?>">
		</th>
	</tr>
	<tr>
		<th><?php echo $lang['autoupdate_admin_banlist']; ?><br /><?php echo $lang['autoupdate_admin_banlist1']; ?></th>
		<th><input type="radio" name="banlist" <?php echo (mod_get_option("BAN_MODS") == 1) ? 'checked' : ''; ?> value="1"/> <font size="5">|</font> <input type="radio" name="banlist" <?php echo (mod_get_option("BAN_MODS") == 0) ? 'checked' : ''; ?> value="0"/></th>
	</tr>
	<tr>
		<th><?php echo $lang['autoupdate_admin_trunk']; ?><br /><?php echo $lang['autoupdate_admin_trunk1']; ?></th>
		<th><input type="radio" name="majtrunk" <?php echo (mod_get_option("MAJ_TRUNK") == 1) ? 'checked' : ''; ?> value="1"/> <font size="5">|</font> <input type="radio" name="majtrunk" <?php echo (mod_get_option("MAJ_TRUNK") == 0) ? 'checked' : ''; ?> value="0"/></th>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="valid" value="<?php echo $lang['autoupdate_admin_valid']; ?>"/></td>
	</tr>
	</form>
</table>
<?php
if(!empty($generated)) {
	if($generated == 'yes') {
		echo "<br />\n".$lang['autoupdate_admin_generated']."<br />\n";
	} else {
		echo "<br />\n".$lang['autoupdate_error']."<br />\n";
	}
}
echo "<br />\n";
echo 'AutoUpdate '.$lang['autoupdate_version'].' '.versionmod();
echo '<br />'."\n";
echo $lang['autoupdate_createdby'].' Jibus '.$lang['autoupdate_and'].' Bartheleway.</div>';
require_once("views/page_tail.php");
?>
