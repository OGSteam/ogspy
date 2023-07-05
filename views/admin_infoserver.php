<?php
/**
 * Panneau administration des options server
 * @package OGSpy
 * @version 3.04b ($Rev: 7508 $)
 * @subpackage views
 * @author Kyzer
 * @created 07/04/2007
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

use Ogsteam\Ogspy\Model\Statistics_Model;

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
$key = 'unknow';
$paths = 'unknow';
$since = 0;
$nb_users = 0;
$og_uni = 'unknow';
$og_pays = 'unknow';
$nb_mail = (isset($server_config['count_mail'])) ? $server_config['count_mail'] : "0";

$stats = (new Statistics_Model())->find();
//fix affichage info OGS
//todo a supprimer, info insert xtense uniquement à la place ....
$stats["spyexport_ogs"] = (isset($stats["spyexport_ogs"])) ? $stats["spyexport_ogs"] : 0;
$stats["spyimport_ogs"] = (isset($stats["spyimport_ogs"])) ? $stats["spyimport_ogs"] : 0;
$stats["rankexport_ogs"] = (isset($stats["rankexport_ogs"])) ? $stats["rankexport_ogs"] : 0;
$stats["rankimport_ogs"] = (isset($stats["rankimport_ogs"])) ? $stats["rankimport_ogs"] : 0;
$stats["planetexport_ogs"] = (isset($stats["planetexport_ogs"])) ? $stats["planetexport_ogs"] : 0;
$stats["planetimport_ogs"] = (isset($stats["planetimport_ogs"])) ? $stats["planetimport_ogs"] : 0;

//on compte le nombre de personnes en ligne
$online = session_whois_online(); //Personne en ligne
$connectes = count($online); //Nombre personne en ligne
// Derniere Version OGSpy
$current_ogspy_version = github_get_latest_release('ogspy');
if (version_compare($current_ogspy_version['release'], $server_config['version'], '>')) {
    $ogspy_version_message = "<span style=\"color:red\">" . $current_ogspy_version['release'] . " : " . $lang['ADMIN_SERVER_NEWVERSION'] . "</span><br><br><span>" . $current_ogspy_version['description'] . "</span>";
} else {
    $ogspy_version_message = $current_ogspy_version['release'] . "<br><span>" . $current_ogspy_version['description'] . "</span><br><br>" . " (<a href='https://github.com/ogsteam/ogspy/releases' target='_blank' rel='noopener'>" . $lang['ADMIN_SERVER_RELEASENOTE'] . "</a>)";
}
?>
<table class="og-table og-full-table">
    <thead>
        <tr>
            <th><?php echo ($lang['ADMIN_SERVER_STATS']); ?></th>
            <th><?php echo ($lang['ADMIN_SERVER_STATS_VALUE']); ?></th>
            <th><?php echo ($lang['ADMIN_SERVER_STATS']); ?></th>
            <th><?php echo ($lang['ADMIN_SERVER_STATS_VALUE']); ?></th>
        </tr>  
    </thead>
    <tbody>
        <tr>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_MEMBERS']); ?></td>
            <td class="tdvalue"><?php echo $users_info; ?></td>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_FREEPLANETS']); ?></td>
            <td class="tdvalue"><?php echo formate_number($galaxy_statistic["nb_planets_free"]); ?></td>
        </tr>
        <tr>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_NBPLANETS']); ?></td>
            <td class="tdvalue"><?php echo formate_number($galaxy_statistic["nb_planets"]); ?></td>
            <td class="tdstat" rowspan="2"><?php echo ($lang['ADMIN_SERVER_DB_SIZE']); ?></td>
            <td class="tdvalue"><?php echo $db_size_info; ?></td>
        </tr>
        <tr>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_LOG_SIZE']); ?></td>
            <td class="tdvalue"><?php echo $log_size_info; ?></td>
            <td class="tdvalue"><a href="index.php?action=db_optimize"><i><?php echo ($lang['ADMIN_SERVER_DB_OPTIMIZE']); ?></i></a></td>
        </tr>
        <tr>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_SESSIONS']); ?></td>
            <td class="tdvalue"><?php echo $connectes; ?><a href="index.php?action=drop_sessions"> (<?php echo ($lang['ADMIN_SERVER_SESSIONS_CLEAN']); ?> <?php
                    echo
                    help("drop_sessions");
                    ?>)</td>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_TOTAL_MAILS']); ?></td>
            <td class="tdvalue"><?php echo ($nb_mail); ?></td>
        </tr>
        <tr>
            <td colspan='4'></td>
        </tr>
        <tr>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_CONNEXIONS']); ?></td>
            <td class="tdvalue"><?php echo formate_number($stats["connection_server"]); ?></th>

            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_PLANETS']); ?></td>
            <td class="tdvalue"><?php echo formate_number($stats["planetimport_ogs"]); ?> <?php echo ($lang['ADMIN_SERVER_ALL_IMPORT']); ?>
                - <?php echo formate_number($stats["planetexport_ogs"]); ?> <?php echo ($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
            </td>
        </tr>
        <tr>
            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_SPYREPORTS']); ?></td>
            <td class="tdvalue"><?php echo formate_number($stats["spyimport_ogs"]); ?> <?php echo ($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($stats["spyexport_ogs"]); ?>
                <?php echo ($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
            </td>

            <td class="tdstat"><?php echo ($lang['ADMIN_SERVER_RANKINGS']); ?></td>
            <td class="tdvalue"><?php echo formate_number($stats["rankimport_ogs"]); ?> <?php echo ($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($stats["rankexport_ogs"]); ?>
                <?php echo ($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
            </td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><a href="php/phpinfo.php" target="_blank" rel="noopener"><?php echo ($lang['ADMIN_SERVER_PHPINFO']); ?></a></td>
            <td colspan="2"><a href="php/phpmodules.php" target="_blank" rel="noopener"><?php echo ($lang['ADMIN_SERVER_PHPMODULES']); ?></a></td>
        </tr>  
    </tbody>
</table>



<table class="og-table og-full-table">
    <thead>
        <tr>
            <th><?php echo ($lang['ADMIN_SERVER_INFOVERSION']); ?></th>
        </tr>   

    </thead>
    <tbody>
        <tr>
            <td class="tdvalue"><?php echo ($ogspy_version_message); ?></rd>
        </tr>
    </tbody>
</table>


<table class="og-table og-full-table">
    <thead>
        <tr>
            <th><?php echo ($lang['ADMIN_SERVER_MEMBERNAME']); ?></th>
            <th><?php echo ($lang['ADMIN_SERVER_MEMBERCONNECTED']); ?></th>
            <th><?php echo ($lang['ADMIN_SERVER_MEMBERLASTACTIVITY']); ?></th>
            <th><?php echo ($lang['ADMIN_SERVER_MEMBERIP']); ?></th>
        </tr>   

    </thead>
    <tbody>
        <?php
        foreach ($online as $v) {
            $user = $v["user"];
            if ($v['time_start'] == 0) {
                $v['time_start'] = $v["time_lastactivity"];
            }
            $time_start = date("d F o G:i:s", $v["time_start"]);
            $time_lastactivity = date("d F o G:i:s", $v["time_lastactivity"]);
            $ip = $v["ip"];
            $ogs = $v["ogs"] == 1 ? "(OGS)" : "";

            echo "<tr>";
            echo "\t" . "<td>" . $user . " " . $ogs . "</td>";
            echo "\t" . "<td>" . $time_start . "</td>";
            echo "\t" . "<td>" . $time_lastactivity . "</td>";
            echo "\t" . "<td>" . $ip . "</td>";
            echo "</tr>";
        }
        ?>   


    </tbody>

</table>
