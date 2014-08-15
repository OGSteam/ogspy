<?php
/***************************************************************************
*	filename	: admin_infoserver.php
*	desc.		:
*	Author		: Kyser - http://ogsteam.fr/
*	created		: 16/12/2005
*	modified	: 02/11/2011 02:45:00
***************************************************************************/

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
    redirection("index.php?action=message&id_message=forbidden&info");
}

//Statistiques concernant la base de données
$db_size_info = db_size_info();
if ($db_size_info["Server"] == $db_size_info["Total"]) {
    $db_size_info = $db_size_info["Server"];
} else {
    $db_size_info = $db_size_info["Server"] . " sur " . $db_size_info["Total"];
}

//Statistiques concernant les fichiers journal
$log_size_info = log_size_info();
$log_size_info = $log_size_info["size"] . " " . $log_size_info["type"];

//Statistisques concernant l'univers
$galaxy_statistic = galaxy_statistic();

//Statistics concernant les membres
$users_info = sizeof(user_statistic());

//Statistiques du serveur
$connection_server = 0;
$planetimport_ogs = 0;
$planetexport_ogs = 0;
$spyimport_ogs = 0;
$spyexport_ogs = 0;
$rankimport_ogs = 0;
$rankexport_ogs = 0;
$key = 'unknow';
$paths = 'unknow';
$since = 0;
$nb_users = 0;
$og_uni = 'unknow';
$og_pays = 'unknow';

// recuperation du pays et de l univers du serveur
if (isset($server_config["xtense_universe"])) {
    //pattern de recherche
    $pattern = "#http://uni([0-9]{1,3}+)\.ogame\.([\w]{2,3})#";
    if (preg_match($pattern, $server_config["xtense_universe"], $retour)) {
        $og_pays = $retour[2]; // seconde capture
        $og_uni = $retour[1]; // premiere capture
    }
}

if (defined("OGSPY_KEY")) {
    if (check_var($serveur_key, 'Text')) {
        $key = $serveur_key;
    }

    $paths = 'http://' . $_SERVER["SERVER_NAME"];
    if (check_var($serveur_date, 'Num')) {
        $since = $serveur_date;
    }
} else {
    echo 'Fichier key.php introuvable !!! ', log_("key");
}

$request = "select statistic_name, statistic_value from " . TABLE_STATISTIC;
$result = $db->sql_query($request);

while (list($statistic_name, $statistic_value) = $db->sql_fetch_row($result)) {

	switch ($statistic_name) {

		case "connection_server":
			$connection_server = $statistic_value;
			break;

		case "planetimport_ogs":
            $planetimport_ogs = $statistic_value;
            break;

        case "planetexport_ogs":
            $planetexport_ogs = $statistic_value;
            break;

        case "spyimport_ogs":
            $spyimport_ogs = $statistic_value;
            break;

        case "spyexport_ogs":
            $spyexport_ogs = $statistic_value;
            break;

        case "rankimport_ogs":
            $rankimport_ogs = $statistic_value;
            break;

        case "rankexport_ogs":
            $rankexport_ogs = $statistic_value;
            break;
    }
}

//on compte le nombre de personnes en ligne
$connectes_req = $db->sql_query("SELECT COUNT(session_ip) FROM " .
    TABLE_SESSIONS);
list($connectes) = $db->sql_fetch_row($connectes_req);

//Personne en ligne
$online = session_whois_online();


//Vérification version installée et envoi de statistiques
preg_match("#([0-9]+)\.([0-9]+)\.([0-9]+)(\-[a-z]*)?#", $server_config["version"],
    $current_version);
@list($current_version, $head_revision, $minor_revision, $extension_revision) =
    $current_version;

$errno = 0;
$errstr = $version_info = '';

//proxy patch by Cerber
//based on http://us2.php.net/manual/en/function.fsockopen.php#58196
//vrai si on doit passer par un proxy, faux sinon
$proxy_use = false;
//Nom du serveur de proxy
$proxy_name = 'proxy';
//port utilisé par le serveur proxy
$proxy_port = 3128;
//utilisateur du proxy
$proxy_user = '';
//mot de passe de l'utilisateur
$proxy_pass = '';

//Adresse du serveur a contacter
$url_server = "update.ogsteam.fr";
//port du serveur spécifié en hard dans le code

$fsock = false;
if ($proxy_use) {
    //on utlise le proxy
    $fsock = @fsockopen($proxy_name, $proxy_port, $errno, $errstr, 3);
} else {
    //pas de proxy
    $fsock = @fsockopen($url_server, 80, $errno, $errstr, 3);
}

if ($fsock) {
    //paramètres de la requete
    $link = "/ogspy/latest2.php";
    $link .= "?version=" . $server_config["version"];

    $link .= "&nb_users=" . $users_info;

    $link .= "&connection_server=" . $connection_server;

    $link .= "&planetimport_ogs=" . $planetimport_ogs;
    $link .= "&planetexport_ogs=" . $planetexport_ogs;

    $link .= "&spyimport_ogs=" . $spyimport_ogs;
    $link .= "&spyexport_ogs=" . $spyexport_ogs;

    $link .= "&rankimport_ogs=" . $rankimport_ogs;
    $link .= "&rankexport_ogs=" . $rankexport_ogs;

    // clef unique
    $link .= "&server_paths=" . $paths;
    $link .= "&server_since=" . $since;
    $link .= "&server_key=" . $key;

    // recuperation pays et univers du serveur
    $link .= "&og_uni=" . $og_uni;
    $link .= "&og_pays=" . $og_pays;

    if ($proxy_use) {
        //si on passe par le proxy ==> requête sauce proxy

        //création de l'url réellement recherchée
        $request_url = "http://$url_server:80$link";
        //appel de l'url via le proxy
        @fputs($fsock, "GET $request_url HTTP/1.0\r\nHost: $proxy_name\r\n");
        if (isset($proxy_user) && $proxy_user != '') {
            //ajout du login + pass si dispo
            @fputs($fsock, "Proxy-Authorization: Basic " . base64_encode("$proxy_user:$proxy_pass") .
                "\r\n");
        }
        //demande de cloture de connexion
        @fputs($fsock, "Connection: close\r\n\r\n");
    } else {
        //pas de proxy ==> code standard
        @fputs($fsock, "GET " . $link . " HTTP/1.1\r\n");
        @fputs($fsock, "HOST: " . $url_server . "\r\n");
        @fputs($fsock, "Connection: close\r\n\r\n");
    }

    $get_info = false;
    while (!@feof($fsock)) {
        if ($get_info) {
            $version_info .= @fread($fsock, 1024);
        } else {
            if (@fgets($fsock, 1024) == "\r\n") {
                $get_info = true;
            }
        }
    }

    @fclose($fsock);
    if (preg_match("#([0-9]+)\.([0-9]+)\.([0-9]+)(\-[a-z]*){0,1}#", $version_info, $version_info)) {

        @list($latest_version, $latest_head_revision, $latest_minor_revision, $latest_extension_revision) =
            $version_info;
        $version_info = $latest_version;

        if (version_compare($latest_head_revision . '.' . $latest_minor_revision . '.' .
            $latest_extension_revision, $head_revision . '.' . $minor_revision . '.' . $extension_revision,
            '<=')) {
            $version_info = "<font color='lime'><b>Votre serveur OGSpy est à jour.</b></font>";
            /*$version_info .='Latest_Head: '.$latest_head_revision.' Minor: '.$latest_minor_revision.' Ext: '.$latest_extension_revision.' vs Head:'.$head_revision.' Minor: '.$minor_revision.' Ext: '.$extension_revision;*/
        } else {
            $version_info = "<blink><b><font color='red'>Votre serveur OGSpy n'est pas à jour.</font></blink>";
            $version_info .= "<br />Rendez vous sur le  <a href='http://www.ogsteam.fr' target='_blank'>forum</a> dédié au support d'OGSpy pour récupérer la dernière version : <font color='red'>" .
                $latest_version . "</b>";
        }
    } else {
        $version_info = "<blink><b><font color='orange'>Une incohérence a été rencontrée avec le serveur de contrôle de version.</font></blink>";
        $version_info .= "<br />Consulter le <a href='http://www.ogsteam.fr' target='_blank'>forum</a> dédié au support d'OGSpy pour en connaître la raison.</b>";
    }
} else {
    $version_info = "<blink><b><font color='orange'>Impossible de récupérer le numéro de la dernière version car le lien n'a pas pu être établie avec le serveur de contrôle.</font></blink>";
    $version_info .= "<br />Il se peut que ce soit votre hébergeur qui n'autorise pas cette action.";
    $version_info .= "<br />Il vous faudra consulter régulièrement le <a href='http://board.ogsteam.fr' target='_blank'>forum</a> dédié au support d'OGSpy pour prendre connaissance des nouvelles versions.</b>";
}
?>

<table width="100%">
<tr>
	<td class="c_stats" width="25%">Statistiques</td><td class="c" width="25%">Valeur</td>
	<td class="c_stats" width="25%">Statistiques</td><td class="c" width="25%">Valeur</td>
</tr>
<tr>
	<th><a>Nombre de membres</a></th><th><?php echo $users_info; ?></th>
	<th><a>Nombre de planètes répertoriées libres</a></th><th><?php echo
formate_number($galaxy_statistic["nb_planets_free"]); ?></th>
</tr>
<tr>
	<th><a>Nombre de planètes répertoriées</a></th><th><?php echo formate_number($galaxy_statistic["nb_planets"]); ?></th>
	<th rowspan="2"><a>Espace occupé par la base de données</a></th><th><?php echo
$db_size_info; ?></th>
</tr>
<tr>
	<th><a>Espace occupé par les logs</a></th><th><?php echo $log_size_info; ?></th>
	<th><a href="index.php?action=db_optimize"><i>Optimiser la base de données</i></a></th>
</tr>
<tr>
	<th><a>Nombre de session ouvertes</a></th><th><?php echo $connectes; ?><a href="index.php?action=drop_sessions"> (vider <?php echo
help("drop_sessions"); ?>)</th>
	<th colspan='2'>&nbsp;</th>
</tr>
<tr>
	<th colspan='4'>&nbsp;</th>
</tr>
<tr>
	<th><a>Connexions au serveur</a></th>
	<th><?php echo formate_number($connection_server); ?></th>
	
	<th><a>Planètes</a></th>
	<th><?php echo formate_number($planetimport_ogs); ?> importations - <?php echo formate_number($planetexport_ogs); ?> exportations</th>
</tr>
<tr>
	<th><a>Rapports d'espionnage</a></th>
	<th><?php echo formate_number($spyimport_ogs); ?> importations - <?php echo formate_number($spyexport_ogs); ?> exportations</th>
	
	<th><a>Classement (nombre de lignes)</a></th>
	<th><?php echo formate_number($rankimport_ogs); ?> importations - <?php echo formate_number($rankexport_ogs); ?> exportations</th>
</tr>
<!--<tr>
	<th><a>Rapports de combats [Serveur]</a></th><th>x importations</th>
	<th><a>Rapports de combats [OGS]</a></th><th>x importations - x exportations</th>
</tr>-->
<tr>
	<td class="c" colspan="4">&nbsp;</th>
</tr>
<tr>
	<th colspan="2"><a href="php/phpinfo.php" target="_blank"a>PHPInfo</a></th>
	<th colspan="2"><a href="php/phpmodules.php" target="_blank"a>Modules PHP</a></th>
</tr>
</table>
<br />
<table width="100%">
<tr>
	<td class="c_ogspy">Information de version</td>
</tr>
<tr>
	<th style="text-align:left">Merci de consulter le Forum de l'OGSteam pour connaitre la dernière version d'OGSpy: <a href='http://www.ogsteam.fr' target='_blank'>Forum</a></th>
</tr>
</table>
<br />
<table width="100%">
<tr>
	<td class="c_user">Nom de membre</td>
	<td class="c">Connexion</td>
	<td class="c">Dernière activité</td>
	<td class="c_tech">Adresse IP</td>
</tr>
<?php
foreach ($online as $v) {
    $user = $v["user"];
    if ($v['time_start'] == 0)
        $v['time_start'] = $v["time_lastactivity"];
    $time_start = strftime("%d %b %Y %H:%M:%S", $v["time_start"]);
    $time_lastactivity = strftime("%d %b %Y %H:%M:%S", $v["time_lastactivity"]);
    $ip = $v["ip"];
    $ogs = $v["ogs"] == 1 ? "(OGS)" : "";

    echo "<tr>";
    echo "\t" . "<th width='25%'>" . $user . " " . $ogs . "</th>";
    echo "\t" . "<th width='25%'>" . $time_start . "</th>";
    echo "\t" . "<th width='25%'>" . $time_lastactivity . "</th>";
    echo "\t" . "<th width='25%'>" . $ip . "</th>";
    echo "</tr>";
}
?>
</table>