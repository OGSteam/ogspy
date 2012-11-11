<?php
/** $Id: admin_parameters.php 7508 2012-01-30 21:59:01Z darknoon $ **/
/**
* Panneau d'Administration : paramètres et options du serveur 
* @package OGSpy
* @version 3.04b ($Rev: 7508 $)
* @subpackage views
* @author Kyser
* @created 15/12/2005
* @copyright Copyright &copy; 2007, http://ogsteam.fr/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @modified $Date: 2012-01-30 22:59:01 +0100 (Mon, 30 Jan 2012) $
* @link $HeadURL: http://svn.ogsteam.fr/trunk/ogspy/views/admin_parameters.php $
*/

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
	redirection("index.php?action=message&id_message=forbidden&info");
}

$max_battlereport = $server_config['max_battlereport'];
$max_favorites = $server_config['max_favorites'];
$max_spyreport = $server_config['max_spyreport'];
$server_active = $server_config['server_active'] == 1 ? "checked" : "";
$session_time = $server_config['session_time'];
$max_keeplog = $server_config['max_keeplog'];
$default_skin = $server_config['default_skin'];
$debug_log = $server_config['debug_log'] == 1 ? "checked" : "";
$log_phperror = $server_config['log_phperror'] == 1 ? "checked" : "";
$reason = $server_config['reason'];
$ally_protection = $server_config['ally_protection'];
$allied = $server_config['allied'];
$url_forum = $server_config['url_forum'];
$max_keeprank = $server_config['max_keeprank'];
$keeprank_criterion = $server_config['keeprank_criterion'];
$max_keepspyreport = $server_config['max_keepspyreport'];
$servername = $server_config['servername'];
$max_favorites_spy = $server_config['max_favorites_spy'];
$disable_ip_check = $server_config['disable_ip_check'] == 1 ? "checked" : "";
$num_of_galaxies = ( isset ( $pub_num_of_galaxies ) ) ? $pub_num_of_galaxies:$server_config['num_of_galaxies'];
$num_of_systems = ( isset ( $pub_num_of_systems ) ) ? $pub_num_of_systems:$server_config['num_of_systems'];
$block_ratio = $server_config['block_ratio'] == 1 ? "checked" : "";
$ratio_limit = $server_config['ratio_limit'];
$speed_uni = $server_config['speed_uni'];
$ddr = $server_config['ddr'];
$astro_strict = $server_config['astro_strict'];
$config_cache = $server_config['config_cache'];
$mod_cache = $server_config['mod_cache'];
?>

<table width="100%">
<form method="POST" action="index.php">
<input type="hidden" name="action" value="set_serverconfig">
<input name="max_battlereport" type="hidden" size="5" value="10">
<tr>
	<td class="c_ogspy" colspan="2">Options générales du serveur</td>
</tr>
<tr>
	<th width="60%">Nom du serveur</th>
	<th><input type="text" name="servername" size="60" value="<?php echo $servername;?>"></th>
</tr>
<tr>
	<th width="60%">Activer le serveur&nbsp;<?php echo help("admin_server_status");?></th>
	<th><input name="server_active" type="checkbox" value="1" <?php echo $server_active;?>></th>
</tr>
<tr>
	<th width="60%">Motif fermeture&nbsp;<?php echo help("admin_server_status_message");?></th>
	<th><input type="text" name="reason" size="60" value="<?php echo $reason;?>"></th>
</tr>
<tr>
	<td class="c" colspan="2">Options des membres</td>
</tr>
<tr>
	<th>Autoriser la désactivation du contrôle des adresses ip&nbsp;<?php echo help("admin_check_ip");?></th>
	<th><input name="disable_ip_check" type="checkbox" value="1" <?php echo $disable_ip_check;?>></th>
</tr>
<tr>
	<th>Skin par défaut<br /><div class="z"><i>ex: http://80.237.203.201/download/use/epicblue/</i></div></th>
	<th><input name="default_skin" type="text" size="60" value="<?php echo $default_skin;?>"></th>
</tr>
<tr>
	<th>Nombre maximum de systèmes favoris autorisé <a>[0-99]</a></th>
	<th><input name="max_favorites" type="text" size="5" maxlength="2" value="<?php echo $max_favorites;?>"></th>
</tr>
<tr>
	<th>Nombre maximum de rapports d'espionnage favoris autorisé <a>[0-99]</a></th>
	<th><input name="max_favorites_spy" type="text" size="5" maxlength="2" value="<?php echo $max_favorites_spy;?>"></th>
</tr>
<tr>
	<td class="c_tech" colspan="2">Gestion des sessions</td>
</tr>
<tr>
	<th>Durée des sessions <a>[5-180 minutes]</a> <a>[0=durée indéterminée&nbsp;<?php echo help("admin_session_infini");?>]</a></th>
	<th><input name="session_time" type="text" size="5" maxlength="3" value="<?php echo $session_time;?>"></th>
</tr>
<tr>
	<td class="c" colspan="2">Protection alliance</td>
</tr>
<tr>
	<th width="60%">Liste des alliances à ne pas afficher<br /><div class="z"><i>Séparez les alliances avec des virgules</i></div></th>
	<th><input type="text" size="60" name="ally_protection" value="<?php echo $ally_protection;?>"></th>
</tr>
<tr>
	<th width="60%">Liste des alliances amies<br /><div class="z"><i>Séparez les alliances avec des virgules</i></div></th>
	<th><input type="text" size="60" name="allied" value="<?php echo $allied;?>"></th>
</tr>
<tr>
	<td class="c" colspan="2">Paramètres divers</td>
</tr>
<tr>
	<th width="60%">Lien du forum de l'alliance</th>
	<th><input type="text" size="60" name="url_forum" value="<?php echo $url_forum;?>"></th>
</tr>
<tr>
	<th>Journaliser les transactions et requêtes SQL&nbsp;<?php echo help("admin_save_transaction");?><br /><div class="z"><i>Risque de dégradation des performances du serveur</i></div></th>
	<th><input name="debug_log" type="checkbox" value="1" <?php echo $debug_log;?>></th>
</tr>
<tr>
	<th>Blocage des mods par Ratio</th>
	<th><input name="block_ratio" type="checkbox" value="1" <?php echo $block_ratio;?>></th>
</tr>
<tr>
	<th>Limite du blocage du Ratio</th>
	<th><input name="ratio_limit" type="text" size="10" maxlength="9" value="<?php echo $ratio_limit;?>"></th>
</tr>
<tr>
	<td class="c_tech" colspan="2">Maintenance</td>
</tr>
<tr>
	<th width="60%">Durée de conservation des classements <a>[1-50 jours ou nombre]</a></th>
	<th><input type="text" name="max_keeprank" maxlength="4" size="5" value="<?php echo $max_keeprank;?>">&nbsp;<select name="keeprank_criterion"><option value="quantity" <?php echo $keeprank_criterion == "quantity" ? "selected" : "";?>>Nombre</option><option value="day" <?php echo $keeprank_criterion == "day" ? "selected" : "";?>>Jours</option></th>
</tr>
<tr>
	<th width="60%">Nombre maximal de rapports d'espionnage par planète <a>[1-10]</a></th>
	<th><input type="text" name="max_spyreport" maxlength="4" size="5" value="<?php echo $max_spyreport;?>"></th>
</tr>
<tr>
	<th width="60%">Durée de conservation des rapports d'espionnage <a>[1-90 jours]</a></th>
	<th><input type="text" name="max_keepspyreport" maxlength="4" size="5" value="<?php echo $max_keepspyreport;?>"></th>
</tr>
<tr>
	<th width="60%">Durée de conservation des fichiers logs <a>[0-365 jours]</a></th>
	<th><input name="max_keeplog" type="text" size="5" maxlength="3" value="<?php echo $max_keeplog;?>"></th>
</tr>
<?php
	if ($user_data["user_admin"] == 1) {
?>
<tr>
	<td class="c_ogame" colspan="2">Options de l'univers</td>
</tr>
<tr>
	<th width="60%">Nombre de galaxies&nbsp;<?php echo help("profile_galaxy");?></th>
	<th><input name="num_of_galaxies" id="galaxies" type="text" size="5" maxlength="3" value="<?php echo $num_of_galaxies;?>" onChange="if (!confirm('Etes vous sur de vouloir modifier le nombre de galaxies?\nsi vous réduisez ce nombre\nLes membres qui sont définit comme étant dans l\'une des galaxies supprimé ce verront mis dans la galaxie 1, système 1\n et leur favoris supprimé')){document.getElementById('galaxies').value='<?php echo $num_of_galaxies;?>';}" readonly="readonly"> &nbsp; &nbsp; active le champs(<input name="enable_input_num_galaxies" type="checkbox" onClick="(this.checked)? document.getElementById('galaxies').readOnly=false : document.getElementById('galaxies').readOnly=true;">)</th>
</tr>
<tr>
	<th width="60%">Nombre de systèmes par galaxies&nbsp;<?php echo help("profile_galaxy");?></th>
	<th><input name="num_of_systems" id="systems" type="text" size="5" maxlength="3" value="<?php echo $num_of_systems;?>" onChange="if (!confirm('Etes vous sur de vouloir modifier le nombre de systèmes?\nsi vous réduisez ce nombre\nLes membres qui sont définit comme étant dans l\'un des systèmes supprimé ce verront mis dans la galaxie 1, système 1\n et leur favoris supprimé')){document.getElementById('systems').value='<?php echo $num_of_systems;?>';}" readonly="readonly"> &nbsp; &nbsp; active le champs(<input name="enable_input_num_systems" type="checkbox" onClick="(this.checked)? document.getElementById('systems').readOnly=false : document.getElementById('systems').readOnly=true;">)</th>
</tr>
<tr>
	<th width="60%">Vitesse de l'univers&nbsp;<?php echo help("profile_speed_uni");?></th>
	<th><input name="speed_uni" id="speed_uni" type="text" size="5" maxlength="2" value="<?php echo $speed_uni;?>" onChange="if (!confirm('Etes vous sur de vouloir modifier la vitesse du jeu?\n')){document.getElementById('speed_uni').value='<?php echo $speed_uni;?>';}" readonly="readonly"> &nbsp; &nbsp; active le champs(<input name="enable_input_speed_uni" type="checkbox" onClick="(this.checked)? document.getElementById('speed_uni').readOnly=false : document.getElementById('speed_uni').readOnly=true;">)</th>
</tr>
<tr>
	<th width="60%">D&eacute;p&ocirc;t de ravitaillement&nbsp;<?php echo help("profile_ddr");?></th>
	<th><input name="ddr" value="1" type="checkbox"<?php print ($ddr==1)? ' checked':'' ?>></th>
</tr>
<tr>
</tr>
<tr>
	<th width="60%">Technologie astrophysique stricte<?php echo help("astro_strict");?></th>
	<th><input name="astro_strict" value="1" type="checkbox"<?php print ($astro_strict==1)? ' checked':'' ?>></th>
</tr>
<tr>
<?php
}
?>
<tr>
	<td class="c_tech" colspan="2">Options de cache</td>
</tr>
<tr>	
    <th>régénérer tous les fichiers caches<br /></th>
	<th><input name="regenere_cache" type="checkbox" value="0" /></th>
</tr>
<tr>
	<th width="60%">Durée de conservation du cache "config" <?php echo help("config_cache");?> <br /><div class="z"><i>(Attention, peut impacter la mise a jour des statitistiques du serveur et le ratio .. )</i></div></a></th>
	<th><input type="text" name="config_cache" maxlength="10" size="10" value="<?php echo $config_cache;?>"></th>
</tr>
<tr>
	<th width="60%">Durée de conservation du cache "mod" <?php echo help("mod_cache");?></a></th>
	<th><input type="text" name="mod_cache" maxlength="10" size="10" value="<?php echo $mod_cache;?>"></th>
</tr>

<tr>
	<td class="c_tech" colspan="2">Options de debuggage et de journalisation</td>
</tr>
<tr>
    <th>Enregistrement des erreurs php<br /><div class="z"><i>(Surveillez vos journaux.. peut prendre beaucoup de place)</i></div></th>
	<th><input name="log_phperror" type="checkbox" value="1" <?php echo $log_phperror;?>></th>

</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<th colspan="2"><input type="submit" value="Valider">&nbsp;<input type="reset" value="Réinitialiser"></th>
</tr>
</form>
</table>
