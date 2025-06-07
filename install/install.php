<?php global $lang;

/**
 * Fichier d'installation d'OGSpy : Script Installation
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.3.4
 */
?>
<html>

<head>
    <title>Installation OGSpy</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="language" content="fr" />
    <link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
    <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico">
    <link rel="icon" type="image/icon" href="../favicon.ico">
</head>

<body>

    <?php
    define("IN_SPYOGAME", true);
    define("INSTALL_IN_PROGRESS", true);

    @chmod("../parameters", 0740);
    @chmod("../journal", 0740);
    @chmod("../mod", 0777);
    @chmod("../mod/autoupdate/tmp", 0777);

    require_once("../common.php");
    require_once("version.php");

    /**
     * Affiche une boite d'erreur de permission
     */
    $error = "";
    $alerte = false;
    if (is_writeable("../parameters")) {
        $error .= " : </td><td><span style=\"color: green; \">" . $lang['INSTALL_WRITE_ALLOWED'] . "</span></td></tr>";
    } else {
        $error .= " : </td><td><span style=\"color: red; \">" . $lang['INSTALL_WRITE_DENIED'] . "</span></td></tr>";
        $alerte = true;
    }

    if (is_writeable("../journal")) {
        $error .= " : </td><td><span style=\"color: green; \">" . $lang['INSTALL_WRITE_ALLOWED'] . "</span></td></tr>";
    } else {
        $error .= " : </td><td><span style=\"color: red; \">" . $lang['INSTALL_WRITE_DENIED'] . "</span></td></tr>";
        $alerte = true;
    }

    $error2 = "";
    if (is_writeable("../mod")) {
        $error2 .= " : </td><td><span style=\"color: green; \">" . $lang['INSTALL_WRITE_ALLOWED'] . "</span></td></tr>";
    } else {
        $error2 .= " : </td><td><span style=\"color: red; \">" . $lang['INSTALL_WRITE_DENIED'] . "</span></td></tr>";
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
        echo "<h3 style=\"text-align: center;\"><span style=\"color: red; \">" . $lang['INSTALL_SQL_ERROR'] . "</span></h3>";
        // Le message est maintenant échappé avec htmlspecialchars pour la sécurité et l'affichage correct.
        echo "<div style=\"text-align: center;\"><b>- " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</b></div>";
        exit();
    }

    /**
     * Installs and initializes the database structure and configuration for the application.
     *
     * @param string $sgbd_server The database server address.
     * @param string $sgbd_dbname The name of the database.
     * @param string $sgbd_username The username for database authentication.
     * @param string $sgbd_password The password for database authentication.
     * @param string $sgbd_tableprefix The prefix to use for database table names.
     * @param string $admin_username The username for the default admin account.
     * @param string $admin_password The password for the default admin account.
     * @param int $num_of_galaxies The number of galaxies to configure in the database.
     * @param int $num_of_systems The number of systems to configure in the database.
     * @param string $ui_lang The language for the user interface.
     *
     * @return void This method does not return a value, it either completes the installation or terminates with an error.
     */

    function installation_db($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $admin_username, $admin_password, $num_of_galaxies, $num_of_systems, $ui_lang)
    {
        global $lang, $ogspy_version;
        $db = sql_db::getInstance($sgbd_server, $sgbd_username, $sgbd_password, $sgbd_dbname);
        if (!$db->db_connect_id) {
            error_sql($lang['INSTALL_SQL_CONNECTION_ERROR']);
        }


        //Création de la structure de la base de données
        if (is_file("schemas/ogspy_structure.sql")) {
            $sql_query = @fread(@fopen("schemas/ogspy_structure.sql", 'r'), @filesize("schemas/ogspy_structure.sql"));
            $sql_query_data = @fread(@fopen("schemas/ogspy_init-data.sql", 'r'), @filesize("schemas/ogspy_init-data.sql"));
        } else {
            exit("<h1>SQL structure file has not been found</h1>");
        }

        $sgbd_tableprefix = $sgbd_tableprefix ?? "ogspy_";

        if (isset($sgbd_tableprefix) && trim($sgbd_tableprefix) != "ogspy_") {
            $sql_query = str_replace("#ogspy_#", $sgbd_tableprefix, $sql_query);
        }

        $sql_query = array_merge(
            explode(";", $sql_query),
            explode(";", $sql_query_data),
            [
                "UPDATE `{$sgbd_tableprefix}config` (`config_value`) VALUES ('$num_of_galaxies') WHERE `config_name` = 'num_of_galaxies'",
                "UPDATE `{$sgbd_tableprefix}config` (`config_value`) VALUES ('$num_of_systems') WHERE `config_name` = 'num_of_systems'",
                "UPDATE `{$sgbd_tableprefix}config` (`config_value`) VALUES ('1') WHERE `config_name` = 'speed_uni'",
                "UPDATE `{$sgbd_tableprefix}config` (`config_value`) VALUES ('$ogspy_version') WHERE `config_name` = 'version'",
            ]
        );

        foreach ($sql_query as $request) {
            if (!empty(trim($request))) {
                $result = $db->sql_query($request);
                if (!$result) {
                    $error = $db->sql_error($result);
                    print $request;
                    error_sql($error['message']);
                }
            }
        }

        $request = "UPDATE " . $sgbd_tableprefix . "user SET " .
            "name = '" . mysqli_real_escape_string($db->db_connect_id, $admin_username) . "', " .
            "password_s = '" . password_hash($admin_password, PASSWORD_DEFAULT) . "', " .
            "regdate = " . time() . ", " .
            "active = '1', " .
            "admin = '1', " .
            "pwd_change = '0' " .
            "WHERE id = 1";

        if (!($result = $db->sql_query($request))) {
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
        $id_php[] = '*  filename    : id.php';
        $id_php[] = '*  generated   : ' . date("d/M/Y H:i:s");
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

        write_file("../parameters/id.php", "w", $id_php);
    }

    if (
        isset($pub_sgbd_server) && isset($pub_sgbd_dbname) && isset($pub_sgbd_username) && isset($pub_sgbd_password) && isset($pub_sgbd_tableprefix) &&
        isset($pub_admin_username) && isset($pub_admin_password) && isset($pub_admin_password2) && isset($pub_num_of_galaxies) && isset($pub_num_of_systems) && isset($pub_lang)
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
                    installation_db($pub_sgbd_server, $pub_sgbd_dbname, $pub_sgbd_username, $pub_sgbd_password, $pub_sgbd_tableprefix, $pub_admin_username, $pub_admin_password, $pub_num_of_galaxies, $pub_num_of_systems, $pub_lang);
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
                            <td colspan="2" align="center"><span style="font-size: small; "><b><?php echo ($lang['INSTALL_VIEW_WELCOME'] . $ogspy_version); ?></b></span></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center"><span style="color: Red; "><b><?php echo isset($pub_error) ? $pub_error : ""; ?></b></span></td>
                        </tr>

                        <tr>
                            <td class="c" colspan="2"><?php echo $lang['INSTALL_VIEW_DBCONFIG']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_SERVERNAME']; ?></th>
                            <th><input name="sgbd_server" type="text" value="<?php echo isset($pub_sgbd_server) ? $pub_sgbd_server : "localhost"; ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBNAME']; ?></th>
                            <th><input name="sgbd_dbname" type="text" value="<?php echo isset($pub_sgbd_dbname) ? $pub_sgbd_dbname : ""; ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBUSER']; ?></th>
                            <th><input name="sgbd_username" type="text" value="<?php echo isset($pub_sgbd_username) ? $pub_sgbd_username : ""; ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBPWD']; ?></th>
                            <th><input name="sgbd_password" type="password"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBPREFIX']; ?></th>
                            <th><input name="sgbd_tableprefix" type="text" value="<?php echo isset($pub_sgbd_tableprefix) ? $pub_sgbd_tableprefix : "ogspy_"; ?>">
                            </th>
                        </tr>
                        <tr>
                            <td class="c" colspan="2"><?php echo $lang['INSTALL_VIEW_DBUNI']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBUNIGAL']; ?><?php echo help("profile_galaxy", "", "../"); ?></th>
                            <th><input name="num_of_galaxies" type="text" value="<?php echo isset($pub_num_of_galaxies) ? $pub_num_of_galaxies : "9"; ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBUNISYS']; ?><?php echo help("profile_galaxy", "", "../"); ?></th>
                            <th><input name="num_of_systems" type="text" value="<?php echo isset($pub_num_of_systems) ? $pub_num_of_systems : "499"; ?>"></th>
                        </tr>

                        <tr>
                            <td class="c" colspan="2"><?php echo $lang['INSTALL_VIEW_ADMIN']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_ADMINNAME']; ?><?php echo help("profile_login", "", "../"); ?></th>
                            <th><input name="admin_username" type="text" value="<?php echo isset($pub_admin_username) ? $pub_admin_username : ""; ?>"></th>
                        </tr>
                        <tr>
                            <th><?= $lang['INSTALL_VIEW_ADMINPWD'] ?><?= help("profile_password", "", "../") ?></th>
                            <th><input name="admin_password" type="password"></th>
                        </tr>
                        <tr>
                            <th><?= $lang['INSTALL_VIEW_ADMINPWD2'] ?></th>
                            <th><input name="admin_password2" type="password"></th>
                        </tr>

                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <th colspan="2"><input name="complete" type="submit" value="<?php echo $lang['INSTALL_VIEW_INSTALLFULL']; ?>">&nbsp;/&nbsp;<input name="file" type="submit" value="<?php echo $lang['INSTALL_VIEW_INSTALLCONFIG']; ?>"></th>
                        </tr>

                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <a target="_blank" href="https://forum.ogsteam.eu/">
                                    <i><span style="color: orange; "><?= $lang['INSTALL_VIEW_INSTALLHELP'] ?></span></i>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr align="center">
                <td>
                    <div style="text-align: center;"><span style="font-size: x-small; "><i><b>OGSpy</b> is an
                    <div style="text-align: center;"><span style="font-size: x-small; "><i><b>OGSpy</b> is an
                                <b>OGSteam Software</b> (c)2005-2025</i><br />v <?= $ogspy_version ?></span></div>
                </td>
            </tr>
        </table>
    </form>
</body>

</html>
