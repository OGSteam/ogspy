<?php
/**
 * Panneau administration des options server
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyzer
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

if ($user_data["user_admin"] != 1 && $user_data["user_coadmin"] != 1) {
    redirection("index.php?action=message&amp;id_message=forbidden&amp;info");
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

?>

<table width="100%">
    <tr>
        <td class="c_stats" width="25%"><?php echo($lang['ADMIN_SERVER_STATS']); ?></td>
        <td class="c" width="25%"><?php echo($lang['ADMIN_SERVER_STATS_VALUE']); ?></td>
        <td class="c_stats" width="25%"><?php echo($lang['ADMIN_SERVER_STATS']); ?></td>
        <td class="c" width="25%"><?php echo($lang['ADMIN_SERVER_STATS_VALUE']); ?></td>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_MEMBERS']); ?></a></th>
        <th><?php echo $users_info; ?></th>
        <th><a><?php echo($lang['ADMIN_SERVER_FREEPLANETS']); ?></a></th>
        <th><?php echo
            formate_number($galaxy_statistic["nb_planets_free"]); ?></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_NBPLANETS']); ?></a></th>
        <th><?php echo formate_number($galaxy_statistic["nb_planets"]); ?></th>
        <th rowspan="2"><a><?php echo($lang['ADMIN_SERVER_DB_SIZE']); ?></a></th>
        <th><?php echo
            $db_size_info; ?></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_LOG_SIZE']); ?></a></th>
        <th><?php echo $log_size_info; ?></th>
        <th><a href="index.php?action=db_optimize"><i><?php echo($lang['ADMIN_SERVER_DB_OPTIMIZE']); ?></i></a></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_SESSIONS']); ?></a></th>
        <th><?php echo $connectes; ?><a href="index.php?action=drop_sessions"> (<?php echo($lang['ADMIN_SERVER_SESSIONS_CLEAN']); ?> <?php echo
                help("drop_sessions"); ?>)</th>
        <th colspan='2'></th>
    </tr>
    <tr>
        <th colspan='4'></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_CONNEXIONS']); ?></a></th>
        <th><?php echo formate_number($connection_server); ?></th>

        <th><a><?php echo($lang['ADMIN_SERVER_PLANETS']); ?></a></th>
        <th><?php echo formate_number($planetimport_ogs); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?>
            - <?php echo formate_number($planetexport_ogs); ?> <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </th>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_SPYREPORTS']); ?></a></th>
        <th><?php echo formate_number($spyimport_ogs); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($spyexport_ogs); ?>
            <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </th>

        <th><a><?php echo($lang['ADMIN_SERVER_RANKINGS']); ?></a></th>
        <th><?php echo formate_number($rankimport_ogs); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($rankexport_ogs); ?>
            <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </th>
    </tr>
    <tr>
        <td class="c" colspan="4">&nbsp;</td>
    </tr>
</table>
<br/>
<table width="100%">
    <tr>
        <td class="c_ogspy"><?php echo($lang['ADMIN_SERVER_INFOVERSION']); ?></td>
    </tr>
    <tr>
        <th style="text-align:left"><?php echo($lang['ADMIN_SERVER_INFOVERSION_DESC']); ?></th>
    </tr>
</table>
<br/>
<table width="100%">
    <tr>
        <td class="c_user"><?php echo($lang['ADMIN_SERVER_MEMBERNAME']); ?></td>
        <td class="c"><?php echo($lang['ADMIN_SERVER_MEMBERCONNECTED']); ?></td>
        <td class="c"><?php echo($lang['ADMIN_SERVER_MEMBERLASTACTIVITY']); ?></td>
        <td class="c_tech"><?php echo($lang['ADMIN_SERVER_MEMBERIP']); ?></td>
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
