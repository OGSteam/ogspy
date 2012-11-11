<?php
/**
 * @package Xtense 2
 * @author Unibozu
 * @version 1.0
 */

if (!defined('IN_SPYOGAME')) die("Hacking Attemp!");

list($version, $root) = $db->sql_fetch_row($db->sql_query("SELECT version, root FROM ".TABLE_MOD." WHERE action = 'xtense'"));

require_once("mod/{$root}/includes/config.php");
require_once("mod/{$root}/includes/functions.php");
require_once("mod/{$root}/includes/Check.php");
$log_dir = "mod/{$root}/log/";

$page = 'infos';
if (isset($pub_page)) {
	// Pages publiques
	if ($pub_page == 'about') $page = $pub_page;
	
	// Pages admin
	if ($user_data['user_admin'] == 1 || ($user_data['user_coadmin'] == 1 && $server_config['xtense_strict_admin'] == 0)) {
		if ($pub_page == 'config' || $pub_page == 'group' || $pub_page == 'mods' || $pub_page == 'infos' || $pub_page == 'log') $page = $pub_page;
	}
}

if ($page == 'infos') {
	$plugin_url = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')+1).
	($server_config['xtense_plugin_root'] == 1 ? '' : "mod/{$root}/") .'xtense.php';
}

if ($page == 'config') {
	$checkboxes = array(
		'allow_connections',
		'strict_admin',
		'log_reverse',
		'plugin_root',
		'log_empire',
		'log_system',
		'log_spy',
		'log_ranking',
		'log_ally_list',
		'log_messages',
		'log_ogspy',
		'spy_autodelete'
	);

	if (isset($pub_universe, $pub_keep_log)) {
		$universe = Check::universe($pub_universe);
		if($universe===false)
			$universe = 'http://uni0.ogame.fr';
		
		$keep_log = (int)$pub_keep_log;
		$keep_log = ($keep_log > 360 ? 360 : $keep_log);
		
		$replace = '';
		foreach ($checkboxes as $name) {
			$server_config['xtense_'.$name] = (isset($_POST[$name]) ? 1 : 0);
			$replace .= ',("xtense_'.$name.'", "'.$server_config['xtense_'.$name].'")';
		}
		
		$db->sql_query('REPLACE INTO '.TABLE_CONFIG.' (config_name, config_value) VALUES ("xtense_universe", "'.$universe.'"), ("xtense_keep_log", "'.$keep_log.'")'.$replace);
		generate_config_cache();
		$server_config['xtense_keep_log'] = $keep_log;
		$server_config['xtense_universe'] = $universe;
		
		$update = true;
	}
	
	if (isset($pub_do)) {
		
		if ($pub_do == 'repair') {
			$db->sql_query('DELETE FROM '.TABLE_USER_BUILDING.' WHERE planet_id < 1');
			$db->sql_query('DELETE FROM '.TABLE_USER_DEFENCE.' WHERE planet_id < 1');
			$action = 'repair';
		}
		
		if ($pub_do == 'install_callbacks') {
			require_once('includes/check_callbacks.php');
			$installed_callbacks = count($callInstall['success']);
			$total_callbacks = count($callInstall['success'])+count($callInstall['errors']);
			$action = 'install_callbacks';
		}
	}
}

if ($page == 'group') {
	if (isset($pub_groups_id)) {
		$ids = explode('-', (string)$pub_groups_id);
		$groups = array();
		
		foreach ($ids as $group_id) {
			$system = (isset($_POST['system_'.$group_id]) ? 1 : 0);
			$ranking = (isset($_POST['ranking_'.$group_id]) ? 1 : 0);
			$empire = (isset($_POST['empire_'.$group_id]) ? 1 : 0);
			$messages = (isset($_POST['messages_'.$group_id]) ? 1 : 0);
			
			$db->sql_query('REPLACE INTO '.TABLE_XTENSE_GROUPS.' (group_id,  system, ranking, empire, messages) VALUES ('.$group_id.', '.$system.', 	'.$ranking.', '.$empire.', '.$messages.')');
		}
		
		$update = true;
	}
	
	
	$query = $db->sql_query('SELECT g.group_id, group_name,  system, ranking, empire, messages FROM '.TABLE_GROUP.' g LEFT JOIN '.TABLE_XTENSE_GROUPS.' x ON x.group_id = g.group_id ORDER BY g.group_name ASC');
	$groups = array();
	$groups_id = array();
	
	while ($data = $db->sql_fetch_assoc($query)) {
		if ($data['system'] == NULL) {
			$data['system'] = $data['spy'] = $data['ranking'] = $data['empire'] = $data['messages'] = 0;
		}
		
		$groups[] = $data;
		$groups_id[] = $data['group_id'];
	}
}

if ($page == 'mods') {
	if (isset($pub_toggle, $pub_state)) {
		$mod_id = (int)$pub_toggle;
		$state = $pub_state == 1 ? 1 : 0;
		$db->sql_query('UPDATE '.TABLE_XTENSE_CALLBACKS.' SET active = '.$state.' WHERE id = '.$mod_id);
		
		$update = true;
	}
	
	$query = $db->sql_query('SELECT c.id, c.type, c.active AS callback_active, m.title, m.active, m.version FROM '.TABLE_XTENSE_CALLBACKS.' c LEFT JOIN '.TABLE_MOD.' m ON m.id = c.mod_id ORDER BY m.title ASC');
	$callbacks = array();
	$calls_id  = array();
	
	$data_names = array(
		'spy' => 'Rapports d\'espionnage',
		'rc_cdr' => 'Rapports de recyclage',
		'msg' => 'Messages de joueurs',
		'ally_msg' => 'Messages d\'alliances',
		'expedition' => 'Rapports d\'expeditions',
		'trade' => 'Livraisons Amies',
		'trade_me' => 'Mes Livraisons',
		'overview' => 'Vue générale',
		'ennemy_spy' => 'Espionnages ennemis',
		'system' => 'Systèmes solaires',
		'ally_list' => 'Liste des joueurs d\'alliance',
		'buildings' => 'Bâtiments',
		'research' => 'Laboratoire',
		'fleet' => 'Flotte',
		'fleetSending' => 'Départ de flotte',
		'defense' => 'Défense',
		'rc' => 'Rapports de combat',
		'ranking_player_fleet' => 'Statistiques (flotte) des joueurs',
		'ranking_player_points' => 'Statistiques (points) des joueurs',
		'ranking_player_research' => 'Statistiques (recherches) des joueurs',
		'ranking_ally_fleet' => 'Statistiques (flotte) des alliances',
		'ranking_ally_points' => 'Statistiques (points) des alliances',
		'ranking_ally_research' => 'Statistiques (recherches) des alliances',
		'trade' => 'Livraisons Alliées',
		'trade_me' => 'Mes livraisons',
		'hostiles' => 'Flottes Hostiles'
	);
	
	while ($data = $db->sql_fetch_assoc($query)) {
		$data['type'] = $data_names[$data['type']];
		$callbacks[] = $data;
		$calls_id[] = $data['id'];
	}
}

if ($page == 'log') {
	if (!is_writable($log_dir)) $unwritable =  1;
	
	if (isset($pub_purge)) {
		$fp = @opendir($log_dir);
		while (($file = readdir($fp)) !== false) {
			if ($file != '.' && $file != '..' && !is_dir($log_dir.$file) && $file != 'index.html') {
				if (!@unlink($log_dir.$file))
					break;
			}
		}
		@closedir($fp);
		
		$purge = true;
	}
	
	$size = 0;
	$nb = 0;
	$availableLogs = array();
	
	$fp = @opendir($log_dir);
	while (($file = @readdir($fp)) !== false) {
		if ($file != '.' && $file != '..' && !is_dir($log_dir.$file) && $file != 'index.html') {
			$size += @filesize($log_dir.$file);
			$availableLogs[] = substr($file, -4);
			$nb++;
		}
	}
	closedir($fp);
	
	$date = date('d/m/Y');
	if (isset($pub_date) && preg_match('!^[0-3][0-9]/[01][0-9]/20[0-9]{2}$!Usi', $pub_date))
		$date = $pub_date;
	
	$exp = explode('/', $date);
	$file = $log_dir.substr($exp[2], 2).$exp[1].$exp[0].'.log';
	
	if (file_exists($file)) {
		if (!is_readable($file)) {
			$error = 'read';
		} else {
			$content = file($file);
			if (empty($content)) {
				$no_log = true;
			} else {
				if ($server_config['xtense_log_reverse']) $content = array_reverse($content);
				$content = implode('<br />', $content);
			}
		}
	} else {
		$no_log = true;
	}
	
	$log_size_moy = format_size(round($size / ($nb == 0 ? 1 : $nb) ,2));
	$log_size = format_size($size);
}

$php_end = benchmark();
$php_timing = $php_end - $php_start;
$db->sql_close();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/2002/REC-xhtml1-20020801/DTD/xhtml1-transitional.dtd">
<script type="application/x-javascript">
<!--
function install (aEvent)
{
  var params = {
    "Xtense": { URL: aEvent.target.href,
             IconURL: aEvent.target.getAttribute("iconURL"),
             Hash: aEvent.target.getAttribute("hash"),
             toString: function () { return this.URL; }
    }
  };
  InstallTrigger.install(params);

  return false;
}

function toggle_callback_info() {
	block = document.getElementById('callback_info');
	if(block.style.display == 'block')
		block.style.display = 'none';
	else
		block.style.display = 'block';
}
-->
</script>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" >
<head>
	<title>Xtense <?php echo $version; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" media="all" type="text/css" href="mod/<?php echo $root; ?>/style.css" />
</head>
<body>
<h1>Administration de Xtense</h1>
<script language="Javascript" type="text/javascript" src="mod/<?php echo $root; ?>/js/config.js"></script>

<div id="wrapper">
	<ul id="menu">
		<li class="infos<?php if ($page == 'infos') echo ' active'; ?>">
			<div>
				<a href="index.php?action=xtense&amp;page=infos">Informations</a>
			</div>
		</li>
		
	<?php if ($user_data['user_admin'] == 1 || ($user_data['user_coadmin'] == 1 && $server_config['xtense_strict_admin'] == 0)) { ?>
		<li class="config<?php if ($page == 'config') echo ' active'; ?>">
			<div>
				<a href="index.php?action=xtense&amp;page=config">Configuration</a>
			</div>
		</li>
		<li class="user<?php if ($page == 'group') echo ' active'; ?>">
			<div>
				<a href="index.php?action=xtense&amp;page=group">Autorisations</a>
			</div>
		</li>
		<li class="mods<?php if ($page == 'mods') echo ' active'; ?>">
			<div>
				<a href="index.php?action=xtense&amp;page=mods">Mods</a>
			</div>
		</li>
		<li class="log<?php if ($page == 'log') echo ' active'; ?>">
			<div>
				<a href="index.php?action=xtense&amp;page=log">Journal</a>
			</div>
		</li>
	<?php } ?>
		<li class="about<?php if ($page == 'about') echo ' active'; ?>">
			<div>
				<a href="index.php?action=xtense&amp;page=about">A propos</a>
			</div>
		</li>
	</ul>

	<div id="content">
	
<?php if ($page == 'infos') { ?>
	<h2>T&eacute;l&eacute;chargement de la barre</h2>
		<p>Version Firefox (Récupérez la dernière version et ouvrez le fichier avec Firefox): <a href="http://update.ogsteam.fr/xtense/download.php" target="_blank">Module Xtense</a></p>
		<p>Version Chrome et Firefox : <a href="http://userscripts.org/scripts/show/112690" target="_blank">Module Xtense Grease Monkey</a></p>
	<h2>Informations</h2>
	
	<p>Voici les informations que vous devez rentrer dans le plugin Xtense pour vous connecter &agrave; ce serveur :</p>
	<p><label for="url"><strong>URL de l&#039;univers</strong></label></p>
	<p class="c">
		<input type="text" class="infos" id="url" name="url" value="<?php echo $server_config['xtense_universe']; ?>" onclick="this.select();" readonly />
	</p>
	<p><label for="plugin"><strong>URL plugin OGSpy</strong></label></p>
	<p class="c">
		<input type="text" class="infos" id="plugin" name="plugin" value="<?php echo $plugin_url; ?>" onclick="this.select();" readonly />
	</p>
	<p>Vous devez &eacute;galement mettre votre pseudo et votre mot de passe de connexion &agrave; OGSpy</p>
	
<?php } elseif ($page == 'config') { ?>
	
	<?php if (isset($update)) { ?>
		<p class="success">Mise &agrave; jour effectu&eacute;e</p>
	<?php } ?>
	
	<?php if (isset($action)) { ?>
			<?php if ($action == 'repair') { ?>
				<p class="success">L&#039;espace personnel a &eacute;t&eacute; correctement r&eacute;par&eacute;</p>
			<?php } elseif ($action == 'install_callbacks') { ?>
				<p class="success" name="callback_sumary">Les appels ont &eacute;t&eacute; install&eacute;s. <?php echo $installed_callbacks; ?> appel(s) install&eacute;(s) pour un total de <?php echo $total_callbacks; ?> appels disponibles.
			<?php if(!empty($callInstall['errors'])) { ?>
				<label for="callback_sumary">
					<button type="button" onclick="toggle_callback_info();" id="callback_button">D&eacute;tails des erreurs</button>
				</label>
				<span id="callback_info">
					<h2>Liste des liens</h2>
					<?php if (!empty($callInstall['success'])) { ?>
						<p><em>Voici la liste des liens correctement install&eacute;s</em></p>
						<ul>
							<?php foreach ($callInstall['success'] as $reason) { ?>
								<li><?php echo $reason; ?></li>
							<?php } ?>
						</ul>
					<?php } ?>
					<?php if (!empty($callInstall['errors'])) { ?>
						<p><em>Certains liens n&#039;ont pas p&ucirc; &ecirc;tre automatiquement install&eacute;s</em></p>
						<ul>
						<?php foreach ($callInstall['errors'] as $reason) { ?>
							<li><?php echo $reason; ?></li>
						<?php } ?>
						</ul>
					<?php } ?>
				</span>
				<?php } ?>
				</p>
			<?php } ?>
	<?php } ?>

	<form action="?action=xtense&amp;page=config" method="post" name="form" id="form">
		<div class="col">
			<p>
				<span class="chk"><input type="checkbox" id="allow_connections" name="allow_connections"<?php echo ($server_config['xtense_allow_connections'] == 1 ? ' checked="checked"' : '');?> /></span>
				<label for="allow_connections">Autoriser les connexions au plugin</label>
			</p>
			<p>
				<span class="chk"><input type="checkbox" id="strict_admin" name="strict_admin"<?php echo ($server_config['xtense_strict_admin'] == 1 ? ' checked="checked"' : '');?> onclick="if (this.checked && <?php echo (int)($user_data['user_coadmin'] && !$user_data['user_admin']);?>) alert('Vous &ecirc;tes co-admin, si vous cochez cette option vous ne pourrez plus acceder &agrave; l&#039;administration de Xtense');" /></span>
				<label for="strict_admin">Limiter l&#039;administration &agrave; l&#039;admin (et non aux co-admins)</label>
			</p>
			<p>
				<span class="chk"><input type="text" size="2" maxlength="2" id="keep_log" name="keep_log" value="<?php echo $server_config['xtense_keep_log']; ?>" /></span>
				<label for="keep_log">Dur&eacute;e de conservation des fichiers logs de Xtense (en jours, 0 pour aucune suppression).</label>
			</p>
			<p>
				<span class="chk"><input type="checkbox" id="log_reverse" name="log_reverse"<?php echo ($server_config['xtense_log_reverse'] == 1 ? ' checked="checked"' : '');?> /></span>
				<label for="log_reverse">Afficher les actions les plus r&eacute;centes en haut dans le journal.</label>
			</p>
			<p>
				<span class="chk"><input type="checkbox" id="spy_autodelete" name="spy_autodelete"<?php echo ($server_config['xtense_spy_autodelete'] == 1 ? ' checked="checked"' : '');?> /></span>
				<label for="spy_autodelete">Effacement automatique des RE trop vieux (configurable depuis l&#039;admin de OGSpy).</label>
			</p>
			<p>
				<span class="chk"><input type="text" size="30" maxlength="30" id="universe" name="universe" value="<?php echo $server_config['xtense_universe']; ?>" /></span>
				<label for="universe">Serveur de jeu</label>
			</p>
		</div>
		
		<div>
			<fieldset>
				<legend>Journaliser les requ&ecirc;tes</legend>
				
				<p>
					<span class="chk"><input type="checkbox" id="log_system" name="log_system"<?php echo ($server_config['xtense_log_system'] == 1 ? ' checked="checked"' : '');?> /></span>
					<label for="log_system">Syst&egrave;mes solaires</label>
				</p>
				<p>
					<span class="chk"><input type="checkbox" id="log_spy" name="log_spy"<?php echo ($server_config['xtense_log_spy'] == 1 ? ' checked="checked"' : '');?> /></span>
					<label for="log_spy">Rapports d&#039;espionnage</label>
				</p>
				<p>
					<span class="chk"><input type="checkbox" id="log_empire" name="log_empire"<?php echo ($server_config['xtense_log_empire'] == 1 ? ' checked="checked"' : '');?> onclick='if (this.checked) {if (!confirm("Attention ! La journalisation des pages des empires des joueurs n&#039;est pas forcement necessaire. Elle prend rapidement beaucoup de place dans les logs !<br>Etes-vous s&ucirc;r de vouloir l&#039;activer ?")) this.checked = false;}' /></span>
					<label for="log_empire">Empire (Pages Empire, Batiments, Recherche...)</label>
				</p>
				<p>
					<span class="chk"><input type="checkbox" id="log_ranking" name="log_ranking"<?php echo ($server_config['xtense_log_ranking'] == 1 ? ' checked="checked"' : '');?> /></span>
					<label for="log_ranking">Classements</label>
				</p>
				<p>
					<span class="chk"><input type="checkbox" id="log_ally_list" name="log_ally_list"<?php echo ($server_config['xtense_log_ally_list'] == 1 ? ' checked="checked"' : '');?> /></span>
					<label for="log_ally_list">Liste des joueurs d&#039;alliance</label>
				</p>
				<p>
					<span class="chk"><input type="checkbox" id="log_messages" name="log_messages"<?php echo ($server_config['xtense_log_messages'] == 1 ? ' checked="checked"' : '');?> /></span>
					<label for="log_messages">Messages</label>
				</p>
				<hr size="1" />
				<p>
					<span class="chk"><input type="checkbox" id="log_ogspy" name="log_ogspy"<?php echo ($server_config['xtense_log_ogspy'] == 1 ? ' checked="checked"' : '');?> /></span>
					<label for="log_ogspy">Utiliser le journal OGSpy</label>
				</p>
			</fieldset>
		</div>
		<div class="clear sep"></div>
		<div id="actions">
			<h2>Actions</h2>
			<p>
				<a href="?action=xtense&amp;page=config&amp;do=repair" class="action" title="Effectuer cette action">&nbsp;</a>
				R&eacute;parer les espaces personnels (en cas de probl&egrave;mes avec un espace personnel plein)
			</p>
			
			<p>
				<a href="?action=xtense&amp;page=config&amp;do=install_callbacks" class="action" title="Effectuer cette action">&nbsp;</a>
				Installer les appels de tous les mods install&eacute;s et activ&eacute;s
			</p>
		</div>
		<div class="sep"></div>
		
		<p class="center"><button type="submit" class="submit">Envoyer</button> <button type="reset" class="reset">Annuler</button></p>
		
		</form>
		
<?php } elseif ($page == 'group') { ?>


<script language="Javascript" type="text/javascript">
	var groups_id = [<?php echo implode(', ', $groups_id); ?>];
</script>

	<p>Vous pouvez d&eacute;finir pour chaque groupe de OGSpy les acc&egrave;s qu&#039;ont les utilisateurs &agrave; Xtense.</p>
	
	<?php if (isset($update)) { ?>
		<p class="success">Mise &agrave; jour effectu&eacute;e</p>
	<?php } ?>
	
	<p style="text-align:right;" class="p10"><span onclick="set_all(true);" style="cursor:pointer;">Tout cocher</span> / <span onclick="set_all(false);" style="cursor:pointer;">Tout decocher</span></p>
	
	<form action="?action=xtense&amp;page=group" method="post" name="form" id="form">
	<input type="hidden" name="groups_id" id="groups_id" value="<?php echo implode('-', $groups_id); ?>" />
	<table width="100%">
		<tr>
			<th>Nom</th>
			<th width="12%" class="c">Syst&egrave;mes</th>
			<th width="12%" class="c">Classement</th>
			<th width="12%" class="c"><acronym title="Pages Batiments, Recherches, Defenses.. et Empire">Empire</acronym></th>
			<th width="12%" class="c">Messages</th>
			<th width="20" class="c"></th>
		</tr>
	<?php foreach ($groups as $l) { ?>
		<tr>
			<td><?php echo $l['group_name']; ?></td>
			
			<td class="c"><input type="checkbox" name="system_<?php echo $l['group_id']; ?>" id="system_<?php echo $l['group_id']; ?>" <?php if ($l['system'] == 1) echo 'checked="checked"'; ?> /></td>
			<td class="c"><input type="checkbox" name="ranking_<?php echo $l['group_id']; ?>" id="ranking_<?php echo $l['group_id']; ?>" <?php if ($l['ranking'] == 1) echo 'checked="checked"'; ?> /></td>
			<td class="c"><input type="checkbox" name="empire_<?php echo $l['group_id']; ?>" id="empire_<?php echo $l['group_id']; ?>" <?php if ($l['empire'] == 1) echo 'checked="checked"'; ?> /></td>
			<td class="c"><input type="checkbox" name="messages_<?php echo $l['group_id']; ?>" id="messages_<?php echo $l['group_id']; ?>" <?php if ($l['messages'] == 1) echo 'checked="checked"'; ?> /></td>
			<td><input type="checkbox" onclick="check_row(<?php echo $l['group_id']; ?>, this);" /></td>
		</tr>
	<?php } ?>
	
		<tr class="bottom">
			<th></th>
			<th class="c"><input type="checkbox" onclick="check_col('system', this);" /></th>
			<th class="c"><input type="checkbox" onclick="check_col('ranking', this);" /></th>
			<th class="c"><input type="checkbox" onclick="check_col('empire', this);" /></th>
			<th class="c"><input type="checkbox" onclick="check_col('messages', this);" /></th>
			<th></th>
		</tr>
	</table>
	
	<div class="sep"></div>
	<p class="center"><button class="submit" type="submit">Envoyer</button> <button class="reset" type="reset">Annuler</button></p>
	</form>
	
<?php } elseif ($page == 'mods') { ?>

	<p>Liste des mods li&eacute;s au plugin Xtense. Ces liens permettent aux mods de r&eacute;cuperer les donn&eacute;es envoy&eacute;es par Xtense 2. Vous pouvez ici activer ou desactiver ces liaisons.</p><br/>
	<?php if (isset($update)) { ?>
		<p class="success">Mise &agrave; jour effectu&eacute;e</p>
	<?php } ?>
	
	<form action="?action=xtense&amp;page=mods" method="post" name="form" id="form">
	<input type="hidden" name="calls_id" id="calls_id" value="<?php echo implode('-', $calls_id); ?>" />
	<table width="100%">
		<tr>
			<th class="c">#</th>
			<th>Nom/version du Mod</th>
			<th width="40%">Type de donn&eacute;es</th>
			<th width="17%" class="c">Status du mod</th>
			<th width="17%" class="c">Status du lien</th>
			<th class="c" width="10"></th>
		</tr>
	<?php if (empty($callbacks)) { ?>
		<tr>
			<td class="c" colspan="5"><em>Aucun lien enregistr&eacute; dans la base de donn&eacute;es</em></td>
		</tr>
	<?php } ?>
	
	<?php foreach ($callbacks as $l) { ?>
		<tr>
			<td><?php echo $l['id']; ?></td>
			<td><?php echo $l['title']; ?> (<?php echo $l['version']; ?>)</td>
			<td><?php echo $l['type']; ?></td>
			<td class="c"><?php echo ($l['active'] == 1 ? 'Activ&eacute;' : 'D&eacute;sactiv&eacute;'); ?></td>
			<td class="c"><?php echo ($l['callback_active'] == 1 ? 'Activ&eacute;' : 'D&eacute;sactiv&eacute;'); ?></td>
			<td><a href="index.php?action=xtense&amp;page=mods&amp;toggle=<?php echo $l['id']; ?>&amp;state=<?php echo $l['callback_active']==1?0:1; ?>" title="<?php echo ($l['callback_active'] == 1 ? 'D&eacute;sactiver' : 'Activer'); ?> l'appel"><?php icon($l['callback_active'] == 1 ? 'reset' : 'valid'); ?></a></td>
		</tr>
	<?php } ?>
	</table>
	<br/>
	
<?php } elseif ($page == 'log') { ?>
<style type="text/css">@import url(mod/<?php echo $root; ?>/js/calendar/theme.css);</style>
<script type="text/javascript" src="mod/<?php echo $root; ?>/js/calendar/calendar.js" /></script>
<script type="text/javascript" src="mod/<?php echo $root; ?>/js/calendar/calendar-fr.js" /></script>
<script type="text/javascript" src="mod/<?php echo $root; ?>/js/calendar/calendar-setup.js" /></script>
	
	<?php if (isset($unwritable)) { ?>
		<p class="error">Le dossier log/ du plugin Xtense n&#039;est pas accessible en &eacute;criture ! Les journaux ne seront pas sauvegard&eacute;s</p>
	<?php } ?>

	<?php if (isset($purge)) { ?>
		<?php if ($purge == 0) { ?>
			<p class="error">Une erreur a eu lieue lors de la suppression des fichiers. Impossible de continuer.</p>
		<?php } else { ?>
			<p class="success">Fichiers supprim&eacute;s</p>
		<?php } ?>
	<?php } ?>
	
	<p>
		Taille actuellement occup&eacute;e par les fichiers de journalisation : <strong><?php echo $log_size;?></strong> <em>(<?php echo $log_size_moy;?> pour <?php echo $nb;?> fichiers)</em> -
		<a href="?action=xtense&amp;page=log&amp;purge" onclick="return confirm('Etes-vous s&ucirc;r de vouloir supprimer tous les journaux ?');">Tout supprimer</a>
	</p>
	
	<div class="sep"></div>
	<form action="?action=xtense&amp;page=log" method="post" name="form" id="form">
		<label>Date du journal &agrave; visionner : <input type="text" id="date" name="date" size="10" maxlength="10" readonly value="<?php echo $date; ?>" /></label>
		<button class="submit" type="submit">Voir</button>
	</form>
	
<script type="text/javascript">
var availableLogs = [<?php if (!empty($availableLogs)) echo "'".implode(',', $availableLogs)."'"; ?>];

Calendar.setup({
	inputField  : 'date',
	ifFormat    : '%d/%m/%Y',
	button      : 'date',
	showOthers  : true,
	range		: [2012, 2099],
	weekNumbers : false
});
</script>
<?php if (isset($error)) { ?>
	<p class="error">Impossible de lire le fichier du journal : <?php echo $file; ?></p>
<?php } ?>

	<div class="sep"></div>
	<p><strong>Historique du <?php echo $date; ?> :</strong></p>
	<p id="log">
		<?php if (isset($no_log)) { 
			echo "Historique vide";
		} else { 
			echo $content; 
		} ?>
	</p>
<?php } elseif ($page == 'about') { ?>
	<p>Xtense par Unibozu</a></p>
	<p>Forum de support de l'OGSteam : <a href="http://www.ogsteam.fr/" onclick="return winOpen(this);" target="_blank">Xtense</a></p>
	<p>Set d'ic&ocirc;nes "Silk icons" par <a href="http://www.famfamfam.com/lab/icons/silk/">FamFamFam</a></p>
	
	<div class="sep"></div>
	<h2>Changelog</h2>
	<dl class="changelog">
    		<dt>Mai 2012</dt>
			<dd>			
				<div class="version">Module OGSpy 2.4.2</div>
				<p>
					<em>Ajouts : </em><br />
					&nbsp;* Support OGame 4.0
				</p>
			</dd>
		<dt>Janvier 2012</dt>
			<dd>			
				<div class="version">Module OGSpy 2.4.0</div>
				<p>
					<em>Ajouts : </em><br />
					&nbsp;* Support OGame 3.0
				</p>
			</dd>
		<dt>14 janvier 2009</dt>
			<dd>			
				<div class="version">Module OGSpy 2.2</div>
				<p>
					<em>Fix : </em><br />
					&nbsp;*  L'id de l'utilisateur ayant transmis un RE est d&eacute;sormais enregistr&eacute;e correctement.
				</p>
				<p>
					<em>Ajouts : </em><br />
					&nbsp;* Compatibilit&eacute; UniSpy et E-Univers partielle (galaxie, RE, classements)
				</p>
			</dd>
		<dt>09 novembre 2008</dt>
		<dd>
			<div class="version">Module OGSpy 2.1a</div>
			<p>
				<em>Fix : </em><br />
				&nbsp;* 
			</p>
			<p>
				<em>Ajouts : </em><br />
				&nbsp;* Le module n&eacute;cessite php5.<br />
				&nbsp;* A l'installation, le module v&eacute;rifie ses fichiers (checksum)<br />
				&nbsp;* A l'installation, le module propose de saisir l'adresse de l'univers de jeu (compatible avec les serveurs autres qu'ogame.fr).<br />
				&nbsp;* A l'installation, le module peut d&eacute;placer le fichier xtense.php &agrave; la racine de l'OGSpy.<br />
				&nbsp;- le module peut d&eacute;placer le fichier xtense.php &agrave; la racine de l'OGSpy.<br />
				&nbsp;* Ajout d'un bouton r&eacute;initialiser dans l'onglet Mods. Il permet de mettre &agrave; jour les appels &agrave; Xtense des autres modules sans avoir besoin les r&eacute;installer.<br />
			</p>
		</dd>
		<dt>Mercredi 1 janvier 2007</dt>
		<dd>
			<div class="version">Extension Firefox 2.0&beta;7</div>
			
			<p>
				<em>Fix :</em><br />
				&nbsp;* Lunes ayant pour nom "lune" qui &eacute;taient reconnues comme plan&egrave;tes<br />
				&nbsp;* Noms de plan&egrave;tes comportant des points<br />
				&nbsp;* Envoi de MIPS dans la galaxie<br />
			</p>
			<p>
				<em>Ajout :</em><br />
				&nbsp;* Bouton pour copier le texte de debug (retour du plugin)<br />
				&nbsp;* Mise en place des pr&eacute;f&eacute;rences par d&eacute;faut<br />
				&nbsp;* Activation des serveurs OGSpy<br />
				&nbsp;* Remise en forme des fen&ecirc;tres d'erreurs qui sont plus claires<br />
				&nbsp;* Noms des colonies absents des syst&egrave;mes solaires vus depuis une lune avec la r&eacute;duction de la galaxie par Foxgame
			</p>
			
			<div class="version">Module OGSpy 2.0&beta;6</div>	
			<p>
				<em>Fix : </em><br />
				&nbsp;* Erreur lors de la d&eacute;sactivation d'un OGSpy<br />
				&nbsp;* Parsing des RE sous OGSpy 3.05<br />
				&nbsp;* Effacement des RE<br />
				&nbsp;* Synchronisation des codes d'envois de Xtense qui sont maintenant comme dans la DB<br />
			</p>
			<p>
				<em>Ajouts : </em><br />
				&nbsp;* Option permettant d'activer ou non l'effacement automatique des RE<br />
			</p>
			
			
		</dd>
		
	</dl>
<?php } ?>
	</div>
</div>

<div id="foot"><?php echo round($php_timing, 2); ?> ms - Cr&eacute;&eacute; par Unibozu - <a href="http://www.ogsteam.fr/" onclick="return winOpen(this);" target="_blank">Support</a></div>


</body>
</html>