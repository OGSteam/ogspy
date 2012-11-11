<?php
/**
* tool_upgrade.php Met à jour OGSpy depuis le serveur
* @package [MOD] AutoUpdate
* @author DarkNoon
* @version 2.0
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

if($user_data['user_admin'] == 1) {
    
    $toolroot = mysql_real_escape_string($pub_tool);
    $version = mysql_real_escape_string($pub_tag);

	if ($pub_sub == "tool_upgrade" && $pub_confirmed == "yes") {
        
        //echo substr(sprintf('%o', fileperms('./install')), -4);
        
		if (!is_writable(".")) {
			die("Erreur: Le répertoire OGSpy doit etre accessible en écriture (755) ".__FILE__. "(Ligne: ".__LINE__.")");
		}
      
       //$modzip = "http://update.ogsteam.fr/".$toolroot."/download.php?download=".$toolroot."-".$version;
       $modzip = "http://darkcity.fr/ogspy311.zip";

       
		if(copy($modzip , './mod/autoupdate/tmp/'.$toolroot.'.zip')) {
                echo '<table align="center" style="width : 400px">'."\n";
                
			if ($zip->open('./mod/autoupdate/tmp/'.$toolroot.'.zip') === TRUE) {
				echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_downok'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
                
                $zip->extractTo(".");
                $zip->close();
                
                unlink("./mod/autoupdate/tmp/".$toolroot.".zip");
                
                if (!is_writable("./install")) {
                    die("Erreur: Le répertoire install OGSpy doit etre accessible en écriture (755) ".__FILE__. "(Ligne: ".__LINE__.")");
                }
                
                chdir('./install'); //Passage dans le répertoire d'installation
                $pub_verbose = false; //Paramétrage de la mise à jour
                echo "\t".'<tr>'."\n";
				require_once("upgrade_to_latest.php"); // Mise à jour...
				echo "\t".'</tr>'."\n";
                chdir('..');// Retour au répertoire par défaut.
				
				if(!rrmdir("./install")){
					die("Impossible de supprimer le répertoire d'installation");
				}

               
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_unzipok'].'</td>'."\n";
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
            echo "\t\t".'<th><a href="index.php?action=autoupdate&sub=tool_upgrade&confirmed=yes&tool='.$toolroot.'&tag='.$version.'">'.$lang['autoupdate_MaJ_linkupdate'].'</a></th>'."\n";
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
