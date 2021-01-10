<?php
/**
 * Fichier d'installation d'OGSpy : Script Installation
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.4
 */
?>
<html>
<head>
    <title>Installation OGSpy</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="language" content="fr"/>
    <link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="icon" type="image/icon" href="../favicon.ico">
</head>
<body>

<?php
define("IN_SPYOGAME", true);
define("INSTALL_IN_PROGRESS", true);

@chmod("../parameters", 0777);
@chmod("../journal", 0777);
@chmod("../mod", 0777);
@chmod("../mod/autoupdate/tmp", 0777);

require_once("../common.php");
require_once("version.php");

/**
 * Affiche une boite d'erreur de permission
 */
$error = "";
$alerte = FALSE;
if (is_writeable("../parameters")) {
    $error .= "<tr><td width=\"250\">- \"parameters\" : </td><td><font color='green'>" . $lang['INSTALL_WRITE_ALLOWED'] . "</font></td></tr>";
} else {
    $error .= "<tr><td width=\"250\">- \"parameters\" : </td><td><font color='red'>" . $lang['INSTALL_WRITE_DENIED'] . "</font></td></tr>";
    $alerte = TRUE;
}

if (is_writeable("../journal")) {
    $error .= "<tr><td width=\"250\">- \"journal\" : </td><td><font color='green'>" . $lang['INSTALL_WRITE_ALLOWED'] . "</font></td></tr>";
} else {
    $error .= "<tr><td width=\"250\">- \"journal\" : </td><td><font color='red'>" . $lang['INSTALL_WRITE_DENIED'] . "</font></td></tr>";
    $alerte = TRUE;
}

$error2 = "";
if (is_writeable("../mod")) {
    $error2 .= "<tr><td width=\"250\">- \"mod\" : </td><td><font color='green'>" . $lang['INSTALL_WRITE_ALLOWED'] . "</font></td></tr>";
} else {
    $error2 .= "<tr><td width=\"250\">- \"mod\" : </td><td><font color='red'>" . $lang['INSTALL_WRITE_DENIED'] . "</font></td></tr>";
}


if ($alerte) {
    echo "<br><br>";
    echo "<table align='center'><tr><th colspan ='2'><span style=\"color: red; \">" . $lang['INSTALL_NOT_POSSIBLE_TITLE'] . "</span></th><tr/>";
    echo "<tr><td colspan='2'>" . $lang['INSTALL_NOT_POSSIBLE_LINE_1'] . "</td></tr>";
    echo $error;
    echo "<tr><th colspan='2'><span style=\"color: red; \">" . $lang['INSTALL_NOT_POSSIBLE_OPTIONAL'] . "</span></th><tr/>";
    echo "<tr><td colspan='2'>" . $lang['INSTALL_NOT_POSSIBLE_LINE_2'] . "<br>";
    echo "<span style=\"color: red; \"><b>" . $lang['INSTALL_NOT_POSSIBLE_LINE_3'] . "</b></span></td></tr>";
    echo $error2;
    echo "<tr align='center'><td colspan='2'><a href='install.php'>" . $lang['INSTALL_NOT_POSSIBLE_REFRESH'] . "</a></td></tr>";
    echo "</table>";
    exit();
}

/**
 * Affiche une boite d'erreur d'installation et quitte le script
 * @var string $message Message d'erreur
 */
function error_sql($message)
{
    global $lang;
    echo "<h3 align='center'><span style=\"color: red; \">" . $lang['INSTALL_SQL_ERROR'] . "</span></h3>";
    echo "<div style=\"text-align: center;\"><b>- " . $message . "</b></div>";
    exit();
}

/**
 * Création de la structure de la base de donnée
 *
 * @param string $sgbd_server Serveur MySql (localhost)
 * @param        $sgbd_dbname Nom de la base de données
 * @param string $sgbd_username Utilisateur Base de donné
 * @param string $sgbd_password Mot de passe Base de donné
 * @param string $sgbd_tableprefix Préfixe à utiliser pour les tables ogspy
 * @param string $admin_username Nom de l'Administrateur OGSpy
 * @param string $admin_password Mot de passe Administrateur OGSpy
 * @param int    $num_of_galaxies Nombre de galaxies dans l'univers OGame de cet OGSp
 * @param int    $num_of_systems Nombre de systèmes dans l'univers OGame de cet OGSpy
 * @param        $ui_lang Langue
 */

function installation_db($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $admin_username, $admin_password, $num_of_galaxies, $num_of_systems, $uni_speed, $ui_lang)
{
    global $lang, $install_version;
    $db = sql_db::getInstance($sgbd_server, $sgbd_username, $sgbd_password, $sgbd_dbname);
    if (!$db->db_connect_id) {
        error_sql($lang['INSTALL_SQL_CONNECTION_ERROR']);
    }


    //Création de la structure de la base de données
    $sql_query = @fread(@fopen("schemas/ogspy_structure.sql", 'r'), @filesize("schemas/ogspy_structure.sql")) or die("<h1>SQL structure file has not been found</h1>");

    if (trim($sgbd_tableprefix) != "") {
        $sql_query = preg_replace("#ogspy_#", $sgbd_tableprefix, $sql_query);
    }

    $sql_query = explode(";", $sql_query);
    $sql_query[] = "INSERT INTO " . $sgbd_tableprefix . "config (config_name, config_value) VALUES ('num_of_galaxies','$num_of_galaxies')";
    $sql_query[] = "INSERT INTO " . $sgbd_tableprefix . "config (config_name, config_value) VALUES ('num_of_systems','$num_of_systems')";
    $sql_query[] = "INSERT INTO " . $sgbd_tableprefix . "config (config_name, config_value) VALUES ('speed_uni','$uni_speed')";
    $sql_query[] = "INSERT INTO " . $sgbd_tableprefix . "config (config_name, config_value) VALUES ('ddr','true')";
    $sql_query[] = "INSERT INTO " . $sgbd_tableprefix . "config (config_name, config_value) VALUES ('astro_strict','1')";
    $sql_query[] = "INSERT INTO " . $sgbd_tableprefix . "config (config_name, config_value) VALUES ('version','$install_version')";
    $sql_query[] = "ALTER DATABASE " . $sgbd_dbname . " charset=utf8"; /*Passage de interclassement en utf8*/

    foreach ($sql_query as $request) {
        if (trim($request) != "") {
            if (!($result = $db->sql_query($request, false, false))) {
                $error = $db->sql_error($result);
                print $request;
                error_sql($error['message']);
            }
        }
    }

    $request = "insert into " . $sgbd_tableprefix . "user (user_id, user_name, user_password_s , user_regdate, user_active, user_admin)" .
        " values (1, '" . mysqli_real_escape_string($db->db_connect_id, $admin_username) . "', '" . password_hash($admin_password,PASSWORD_DEFAULT ) . "', " . time() . ", '1', '1')";
    if (!($result = $db->sql_query($request, false, false))) {
        $error = $db->sql_error($result);
        print $request;
        error_sql($error['message']);
    }

    $request = "insert into " . $sgbd_tableprefix . "user_group (group_id, user_id) values (1, 1)";
    if (!($result = $db->sql_query($request, false, false))) {
        $error = $db->sql_error($result);
        print $request;
        error_sql($error['message']);
    }

    // Ajout du mod_Xtense et du mod AutoUpdate
    define('TABLE_MOD', $sgbd_tableprefix . 'mod');
    define('TABLE_MOD_CFG', $sgbd_tableprefix . 'mod_config');
    define('TABLE_MOD_CONFIG', $sgbd_tableprefix . 'mod_config');
    define('TABLE_CONFIG', $sgbd_tableprefix . 'config');

    generate_id($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $ui_lang);

    echo "<h3 align='center'><span style=\"color: yellow; \">" . $lang['INSTALL_SUCCESS'] . "</span></h3>";
    echo "<div style=\"text-align: center;\">";
    echo "<b>" . $lang['INSTALL_SUCCESS_REMOVE_FOLDER'] . "</b><br>";
    echo "<a href='../index.php'>" . $lang['INSTALL_SUCCESS_BACK'] . "</a>";
    echo "</div>";
    exit();
}

/**
 * Création du fichier de configuration id.php et quitte le script
 * 
 * @param string $sgbd_server Serveur MySql (localhost)
 * @param string $sgbd_username Utilisateur Base de donnée
 * @param string $sgbd_password Mot de passe Base de donnée
 * @param string $sgbd_tableprefix Préfixe à utiliser pour les tables ogspy
 * @param string $sgbd_server
 * @param string $sgbd_username
 * @param string $sgbd_password
 * @param string $sgbd_tableprefix
 */
function generate_id($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $install_ui_lang)
{
    global $lang;

    $id_php[] = '<?php';
    $id_php[] = '/*****************************************************************';
    $id_php[] = '*	filename	: id.php';
    $id_php[] = '*	generated	: ' . date("d/M/Y H:i:s");
    $id_php[] = '*****************************************************************/';
    $id_php[] = '';
    $id_php[] = 'if (!defined("IN_SPYOGAME")) die("Hacking attempt");';
    $id_php[] = '';
    $id_php[] = '$table_prefix = "' . $sgbd_tableprefix . '";';
    $id_php[] = '';
    $id_php[] = '//Paramètres de connexion à la base de données';
    $id_php[] = '$db_host = "' . $sgbd_server . '";';
    $id_php[] = '$db_user = "' . $sgbd_username . '";';
    $id_php[] = '$db_password = "' . $sgbd_password . '";';
    $id_php[] = '$db_database = "' . $sgbd_dbname . '";';
    $id_php[] = '';
    $id_php[] = '//OGSpy Language';
    $id_php[] = '$ui_lang = "' . $install_ui_lang . '";';
    $id_php[] = '';
    $id_php[] = 'define("OGSPY_INSTALLED", TRUE);';

    if (!write_file("../parameters/id.php", "w", $id_php)) {
        die($lang['INSTALL_IDFILE_ERROR']);
    }
}

if (isset($pub_sgbd_server) && isset($pub_sgbd_dbname) && isset($pub_sgbd_username) && isset($pub_sgbd_password) && isset($pub_sgbd_tableprefix) &&
    isset($pub_admin_username) && isset($pub_admin_password) && isset($pub_admin_password2) && isset($pub_num_of_galaxies) && isset($pub_num_of_systems) && isset($pub_uni_speed)&& isset($pub_lang)
) {

    if (isset($pub_complete)) {
        if (!empty($pub_sgbd_tableprefix) && !check_var($pub_sgbd_tableprefix, "Pseudo_Groupname", "", true)) {
            $pub_error = $lang['INSTALL_FORM_ERROR_PREFIX'];
        } elseif (!check_var($pub_admin_username, "Pseudo_Groupname", "", true) || !check_var($pub_admin_password, "Password", "", true)) {
            $pub_error = $lang['INSTALL_FORM_ERROR_USER'];
        } elseif (!check_var($pub_num_of_galaxies, "Galaxy", "", true) || !check_var($pub_num_of_systems, "Galaxy", "", true)) {
            $pub_error = $lang['INSTALL_FORM_ERROR_GALAXY'];
        } else {
            if ($pub_sgbd_server != "" && $pub_sgbd_dbname != "" && $pub_sgbd_username != "" && $pub_admin_username != "" && $pub_admin_password != "" && $pub_admin_password == $pub_admin_password2 && $pub_num_of_galaxies != "" && $pub_num_of_systems != "" && $pub_lang != "") {
                installation_db($pub_sgbd_server, $pub_sgbd_dbname, $pub_sgbd_username, $pub_sgbd_password, $pub_sgbd_tableprefix, $pub_admin_username, $pub_admin_password, $pub_num_of_galaxies, $pub_num_of_systems, $pub_uni_speed, $pub_lang);
            } else {
                $pub_error = $lang['INSTALL_FORM_ERROR_CONNECTION'];
            }
        }
    } elseif (isset($pub_file)) {
        if ($pub_sgbd_server != "" && $pub_sgbd_dbname != "" && $pub_sgbd_username != "" && $pub_lang != "") {
            generate_id($pub_sgbd_server, $pub_sgbd_dbname, $pub_sgbd_username, $pub_sgbd_password, $pub_sgbd_tableprefix, $pub_lang);
        } else {
            $pub_error = $lang['INSTALL_FORM_ERROR_CONNECTION_PARAMS'];
        }
    }

    $sgbd_server = $pub_sgbd_server;
    $sgbd_dbname = $pub_sgbd_dbname;
    $sgbd_username = $pub_sgbd_username;
    $sgbd_password = $pub_sgbd_password;
    $sgbd_tableprefix = $pub_sgbd_tableprefix;
    $admin_username = $pub_admin_username;
    $admin_password = $pub_admin_password;
    $admin_password2 = $pub_admin_password2;
    $num_of_galaxies = (isset($pub_num_of_galaxies) && !empty($pub_num_of_galaxies)) ? $pub_num_of_galaxies : 9;
    $num_of_systems = (isset($pub_num_of_systems) && !empty($pub_num_of_systems)) ? $pub_num_of_systems : 499;
    $uni_speed = (isset($pub_uni_speed) && !empty($pub_uni_speed)) ? $pub_uni_speed : 1;
    $directory = $pub_directory;
}
?>
<form method="POST" action="../install/install.php?lang=<?php echo $pub_lang; ?>">
    <table width="100%" align="center" cellpadding="20">
        <tr>
            <td height="70">
                <div align="center"><img src="../skin/OGSpy_skin/logos/logo.png"></div>
            </td>
        </tr>
        <tr>
            <td align="center">
                <table width="800">
                    <tr>
                        <td colspan="2" align="center"><span style="font-size: small; "><b><?php echo ($lang['INSTALL_VIEW_WELCOME'] . $install_version); ?></b></span></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><span
                                style="color: Red; "><b><?php echo isset($pub_error) ? $pub_error : ""; ?></b></span></td>
                    </tr>

                    <tr>
                        <td class="c" colspan="2"><?php echo $lang['INSTALL_VIEW_DBCONFIG']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_SERVERNAME']; ?></th>
                        <th><input name="sgbd_server" type="text"
                                   value="<?php echo isset($pub_sgbd_server) ? $pub_sgbd_server : "localhost"; ?>"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_DBNAME']; ?></th>
                        <th><input name="sgbd_dbname" type="text"
                                   value="<?php echo isset($pub_sgbd_dbname) ? $pub_sgbd_dbname : ""; ?>"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_DBUSER']; ?></th>
                        <th><input name="sgbd_username" type="text"
                                   value="<?php echo isset($pub_sgbd_username) ? $pub_sgbd_username : ""; ?>"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_DBPWD']; ?></th>
                        <th><input name="sgbd_password" type="password"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_DBPREFIX']; ?></th>
                        <th><input name="sgbd_tableprefix" type="text"
                                   value="<?php echo isset($pub_sgbd_tableprefix) ? $pub_sgbd_tableprefix : "ogspy_"; ?>">
                        </th>
                    </tr>
                    <tr>
                        <td class="c" colspan="2"><?php echo $lang['INSTALL_VIEW_DBUNI']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_DBUNIGAL']; ?><?php echo help("profile_galaxy", "", "../"); ?></th>
                        <th><input name="num_of_galaxies" type="text"
                                   value="<?php echo isset($pub_num_of_galaxies) ? $pub_num_of_galaxies : "9"; ?>"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_DBUNISYS']; ?><?php echo help("profile_galaxy", "", "../"); ?></th>
                        <th><input name="num_of_systems" type="text"
                                   value="<?php echo isset($pub_num_of_systems) ? $pub_num_of_systems : "499"; ?>"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_DBUNISPEED']; ?><?php echo help("profile_speed", "", "../"); ?></th>
                        <th><input name="uni_speed" type="text"
                                   value="<?php echo isset($pub_uni_speed) ? $pub_uni_speed : "1"; ?>"></th>
                    </tr>


                    <tr>
                        <td class="c" colspan="2"><?php echo $lang['INSTALL_VIEW_ADMIN']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_ADMINNAME']; ?><?php echo help("profile_login", "", "../"); ?></th>
                        <th><input name="admin_username" type="text"
                                   value="<?php echo isset($pub_admin_username) ? $pub_admin_username : ""; ?>"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_ADMINPWD']; ?><?php echo help("profile_password", "", "../"); ?></th>
                        <th><input name="admin_password" type="password"></th>
                    </tr>
                    <tr>
                        <th><?php echo $lang['INSTALL_VIEW_ADMINPWD2']; ?></th>
                        <th><input name="admin_password2" type="password"></th>
                    </tr>

                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <th colspan="2"><input name="complete" type="submit" value="<?php echo $lang['INSTALL_VIEW_INSTALLFULL']; ?>">&nbsp;/&nbsp;<input
                                name="file" type="submit" value="<?php echo $lang['INSTALL_VIEW_INSTALLCONFIG']; ?>"></th>
                    </tr>

                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <a target="_blank" href="https://forum.ogsteam.fr/">
                                <i><span style="color: orange; "><?php echo $lang['INSTALL_VIEW_INSTALLHELP']; ?></span></i>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr align="center">
            <td>
                <div style="text-align: center;"><span style="font-size: x-small; "><i><b>OGSpy</b> is an
                            <b>OGSteam Software</b> (c)2005-2020</i><br/>v <?php echo $install_version; ?></span></div>
            </td>
        </tr>
    </table>
</form>
</body>
</html>
