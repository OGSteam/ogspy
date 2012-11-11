<?php
/** $Id: functions.php 7754 2012-11-07 21:23:06Z darknoon $ **/
/**
* functions.php Défini les fonctions du mod
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
*	created		: 20/11/2006
*	modified	: 17/01/2007
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("mod/autoupdate/ban_list.php");

/**
*Récupère la version du mod
*/
function versionmod() {
	global $db, $pub_action;
	$sql = "SELECT version FROM ".TABLE_MOD." WHERE action = '".$pub_action."' LIMIT 1";
	$query = $db->sql_query($sql);
	$fetch = $db->sql_fetch_assoc($query);
	return $fetch['version'];
}
function upgrade_ogspy_mod($mod){
	global $db, $lang;
    // On vérifie si le mod est déjà installé
    $check = "SELECT title FROM " . TABLE_MOD . " WHERE root='" . $mod .
        "'";
    $query_check = $db->sql_query($check);
    $result_check = $db->sql_numrows($query_check);

    if ($result_check != 0) { 
    // Si le mod existe, on fait une mise à jour
        if (file_exists("mod/".$mod."/update.php"))
        {
            require_once("mod/".$mod."/update.php");
            generate_all_cache();
            log_("mod_update", $mod);
            $maj = $lang['autoupdate_tableau_uptodateok']."<br />\n<br />\n";
        } else{
            $maj = $lang['autoupdate_tableau_uptodateoff']."<br />\n<br />\n";
        }
        return $maj;
        
    }else{
        // Si le mod n'existe pas, on fait une installation
        if (file_exists("mod/".$mod."/install.php"))
        {
            require_once("mod/".$mod."/install.php");
            generate_all_cache();
            log_("mod_install", $mod);
            $maj = $lang['autoupdate_tableau_installok']."<br />\n<br />\n";
        } else{
            $maj = $lang['autoupdate_tableau_installoff']."<br />\n<br />\n";
        }
        return $maj;
                
    }
}
function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}

/**
*Copie le fichier modupdate.json dans mod/modupdate.json
*/
function copymodupdate() {
global $lang;

    if(time() > (mod_get_option('LAST_MODLIST_UPDATE') + mod_get_option('CYCLEMAJ') * 3600)){
        if (!copy("http://update.ogsteam.fr/mods/latest.php", "parameters/modupdate.json")) {
            $affiche = "<br />\n".$lang['autoupdate_tableau_error2'];
        } else {
            $affiche = "<br />\n".$lang['autoupdate_tableau_ok'];
            mod_set_option('LAST_MODLIST_UPDATE', time());
        }

        return $affiche;
    }   
}

/**
* Affiche sous forme de tableau table à 2 colonne les fichiers du zip et son état.
*/
function tableau($tableau, $type = "maj") {
	global $lang;
	while(list($key,$valeur) = each($tableau)) {
		$fichier = explode("/", $key);
		$nom = "";
		for($i = 1; $i < count($fichier); $i++) {
			if (count($fichier) >= 3 AND count($fichier) != $i AND $i > 1) {
				$slash = "/";
			} else {
				$slash = "";
			}
			$nom .= $slash.$fichier[$i];
		}
		$explode = explode(".", $key);
		if ($nom != "" AND $explode[0] != $key) {
			if ($type == "maj") {
				$etat = $lang['autoupdate_MaJ_uptodateok'];
			} else if ($type == "down") {
				$etat = $lang['autoupdate_MaJ_downok'];
			}
			echo "\t".'<tr>'."\n";
			echo "\t\t".'<td class="a">'.$nom.'</td>'."\n";
			echo "\t\t".'<td class="a">'.$etat.'</td>'."\n";
			echo "\t".'</tr>'."\n";
		}
	}
}

function getmodlist(){
	global $ban_mod;
	// Récupérer la liste des dernières versions dans le fichier JSON
    if(!file_exists("parameters/modupdate.json")) {
	//Retry once to not overload the server.
		if (!copy("http://update.ogsteam.fr/mods/latest.php", "parameters/modupdate.json")){
			die ("Fichier JSON Introuvable !");
		}
	}
	$contents = file_get_contents("parameters/modupdate.json");	
	$results = utf8_encode($contents);
	$data = json_decode($results, true);
	//Suppresion des Mods interdits
	if( mod_get_option("BAN_MODS") == 1){
		foreach( $ban_mod as $to_ban)
		{
			unset($data[$to_ban]);
		}
	}
	return $data;	
}	
?>
