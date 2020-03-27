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
$nb_mail= (isset($server_config['count_mail'])) ? $server_config['count_mail']     : "0";

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
$online = session_whois_online();//Personne en ligne
$connectes = count($online);//Nombre personne en ligne

// Derniere Version OGSpy
$current_ogspy_version = github_get_latest_release('ogspy');
if(version_compare($current_ogspy_version['release'],$server_config['version'],'>')){
    $ogspy_version_message = "<span style=\"color:red\">".$current_ogspy_version['release']." : " . $lang['ADMIN_SERVER_NEWVERSION'] . "</span><br><br><span>".$current_ogspy_version['description']."</span>";
}
else{
    $ogspy_version_message = $current_ogspy_version['release']."<br><span>".$current_ogspy_version['description']."</span><br><br>". " (<a href='https://github.com/ogsteam/ogspy/releases' target='_blank'>".$lang['ADMIN_SERVER_RELEASENOTE']."</a>)" ;

}
?>


<table width="40% ">
    <thead>
    <tr>
        <th class="" ><?php echo($lang['ADMIN_SERVER_STATS']); ?></th>
        <th class="" ><?php echo($lang['ADMIN_SERVER_STATS_VALUE']); ?></th>
    </tr>
    </thead>

    <tbody>

    <tr>
        <th><?php echo($lang['ADMIN_SERVER_MEMBERS']); ?></th>
        <td><?php echo $users_info; ?></td>
    </tr>

    </tbody>

</table>



<table width="40%">
    <tr>
        <td class="c_ogspy"><?php echo($lang['ADMIN_SERVER_INFOVERSION']); ?></td>
    </tr>
    <tr>
        <th style="text-align:left"><?php echo($ogspy_version_message); ?></th>
    </tr>
</table>


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
            if ($v['time_start'] == 0) {
                $v['time_start'] = $v["time_lastactivity"];
            }
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




<table width="40% ">
    <thead>
        <tr>
            <th class="" ><?php echo($lang['ADMIN_SERVER_STATS']); ?></th>
            <th class="" ><?php echo($lang['ADMIN_SERVER_STATS_VALUE']); ?></th>
            <th class="" ><?php echo($lang['ADMIN_SERVER_STATS']); ?></th>
            <th class="" ><?php echo($lang['ADMIN_SERVER_STATS_VALUE']); ?></th>
        </tr>
     </thead>

    <tbody>

    <tr>
        <th><?php echo($lang['ADMIN_SERVER_MEMBERS']); ?></th>
        <td><?php echo $users_info; ?></td>
        <th><?php echo($lang['ADMIN_SERVER_FREEPLANETS']); ?></th>
        <td><?php echo
            formate_number($galaxy_statistic["nb_planets_free"]); ?></td>
    </tr>
    <tr>
        <th><?php echo($lang['ADMIN_SERVER_NBPLANETS']); ?></th>
        <td><?php echo formate_number($galaxy_statistic["nb_planets"]); ?></td>
        <th rowspan="2"><?php echo($lang['ADMIN_SERVER_DB_SIZE']); ?></th>
        <td><?php echo $dbsize_info; ?>??</td>
    </tr>
    <tr>
        <th><?php echo($lang['ADMIN_SERVER_LOG_SIZE']); ?></th>
        <th><?php echo $log_size_info; ?></th>
        <th><a href="index.php?action=db_optimize"><i><?php echo($lang['ADMIN_SERVER_DB_OPTIMIZE']); ?></i></a></th>
    </tr>
    <tr>
        <th><?php echo($lang['ADMIN_SERVER_SESSIONS']); ?></th>
        <td><?php echo $connectes; ?><a href="index.php?action=drop_sessions"> (<?php echo($lang['ADMIN_SERVER_SESSIONS_CLEAN']); ?> <?php echo
                help("drop_sessions"); ?>)</td>
        <th><?php echo($lang['ADMIN_SERVER_TOTAL_MAILS']); ?></th>
        <td><?php echo($nb_mail); ?></td>
    </tr>
    <tr>
        <th colspan='4'>&nbsp;</th>
    </tr>
    <tr>
        <th><?php echo($lang['ADMIN_SERVER_CONNEXIONS']); ?></th>
        <td><?php echo formate_number($stats["connection_server"]); ?></td>

        <th><?php echo($lang['ADMIN_SERVER_PLANETS']); ?></th>
        <td><?php echo formate_number($stats["planetimport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?>
            - <?php echo formate_number($stats["planetexport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </td>
    </tr>
    <tr>
        <th><?php echo($lang['ADMIN_SERVER_SPYREPORTS']); ?></th>
        <td><?php echo formate_number($stats["spyimport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($stats["spyexport_ogs"]); ?>
            <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </td>

        <th><?php echo($lang['ADMIN_SERVER_RANKINGS']); ?></th>
        <td><?php echo formate_number($stats["rankimport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($stats["rankexport_ogs"]); ?>
            <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </td>
    </tr>
    </tbody>
    <thead>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
    </thead>
    <tbody>
    <tr>
        <th colspan="2"><a href="php/phpinfo.php" target="_blank"><?php echo($lang['ADMIN_SERVER_PHPINFO']); ?></a></th>
        <th colspan="2"><a href="php/phpmodules.php" target="_blank"><?php echo($lang['ADMIN_SERVER_PHPMODULES']); ?></a></th>
    </tr>


    </tbody>

</table>
<br/>



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
        <th><a><?php echo($lang['ADMIN_SERVER_TOTAL_MAILS']); ?></a></th>
        <th><?php echo($nb_mail); ?></th>
    </tr>
    <tr>
        <th colspan='4'></th>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_CONNEXIONS']); ?></a></th>
        <th><?php echo formate_number($stats["connection_server"]); ?></th>

        <th><a><?php echo($lang['ADMIN_SERVER_PLANETS']); ?></a></th>
        <th><?php echo formate_number($stats["planetimport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?>
            - <?php echo formate_number($stats["planetexport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </th>
    </tr>
    <tr>
        <th><a><?php echo($lang['ADMIN_SERVER_SPYREPORTS']); ?></a></th>
        <th><?php echo formate_number($stats["spyimport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($stats["spyexport_ogs"]); ?>
            <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </th>

        <th><a><?php echo($lang['ADMIN_SERVER_RANKINGS']); ?></a></th>
        <th><?php echo formate_number($stats["rankimport_ogs"]); ?> <?php echo($lang['ADMIN_SERVER_ALL_IMPORT']); ?> - <?php echo formate_number($stats["rankexport_ogs"]); ?>
            <?php echo($lang['ADMIN_SERVER_ALL_EXPORT']); ?>
        </th>
    </tr>
    <tr>
        <td class="c" colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <th colspan="2"><a href="php/phpinfo.php" target="_blank"><?php echo($lang['ADMIN_SERVER_PHPINFO']); ?></a></th>
        <th colspan="2"><a href="php/phpmodules.php" target="_blank"><?php echo($lang['ADMIN_SERVER_PHPMODULES']); ?></a></th>
    </tr>
</table>
<br/>
<table width="100%">
    <tr>
        <td class="c_ogspy"><?php echo($lang['ADMIN_SERVER_INFOVERSION']); ?></td>
    </tr>
    <tr>
        <th style="text-align:left"><?php echo($ogspy_version_message); ?></th>
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
        if ($v['time_start'] == 0) {
                    $v['time_start'] = $v["time_lastactivity"];
        }
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
