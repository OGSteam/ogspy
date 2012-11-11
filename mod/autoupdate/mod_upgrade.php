<?php
/**
* mod_upgrade.php Met à jour les mods depuis le serveur
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0a
* created	: 27/10/2006
* modified	: 18/01/2007
* $Id: MaJ.php 7668 2012-07-15 22:16:03Z darknoon $
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");
/**
*Récupère les fonctions zip
*/
$zip = new ZipArchive;

require_once("views/page_header.php");

if(!isset($pub_confirmed)) $pub_confirmed = "no";

if($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
    
    $modroot = mysql_real_escape_string($pub_mod);
    $version = mysql_real_escape_string($pub_tag);

	if ($pub_sub == "mod_upgrade" && $pub_confirmed == "yes") {

		$modzip = "http://update.ogsteam.fr/mods/download.php?download=".$modroot."-".$version;

		if (!is_writable("./mod/autoupdate/tmp/")) {
			die("Erreur: Le repertoire /mod/autoupdate/tmp/ doit etre accessible en écriture (777) ".__FILE__. "(Ligne: ".__LINE__.")");
		}
        
		if(copy($modzip , './mod/autoupdate/tmp/'.$modroot.'.zip')) {
                echo '<table align="center" style="width : 400px">'."\n";
			if ($zip->open('./mod/autoupdate/tmp/'.$modroot.'.zip') === TRUE) {
				echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_downok'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
                
                $zip->extractTo("./mod/".$modroot."/");
                $zip->close();
                unlink("./mod/autoupdate/tmp/".$modroot.".zip");
                
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_unzipok'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.upgrade_ogspy_mod($modroot).'</td>'."\n";
                echo "\t".'</tr>'."\n";
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_tableau_back'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
				echo '</table>'."\n";
				echo '<br />'."\n";
			}
		}
    }else{
            echo '<table>'."\n";
            echo "\t".'<tr>'."\n";
            echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_wantupdate'].'</td>'."\n";
            echo "\t\t".'<th><a href="index.php?action=autoupdate&sub=mod_upgrade&confirmed=yes&mod='.$modroot.'&tag='.$version.'">'.$lang['autoupdate_MaJ_linkupdate'].'</a></th>'."\n";
            echo "\t".'</tr>'."\n";
            echo '</table>'."\n";
	}

} else {
	echo $lang['autoupdate_MaJ_rights'];
}
echo '<br />'."\n";
echo 'AutoUpdate '.$lang['autoupdate_version'].' '.versionmod();
echo '<br />'."\n";
echo $lang['autoupdate_createdby'].' Jibus '.$lang['autoupdate_and'].' Bartheleway.</div>';
require_once("views/page_tail.php");
?>
