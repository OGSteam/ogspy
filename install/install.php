<?php global $ogspy_version, $lang;

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
    const IN_SPYOGAME = true;
    define("INSTALL_IN_PROGRESS", true);



    require_once("../common.php");
    require_once "installFunctions.php";
    require_once("version.php");

    // Set permissions
    @chmod("../parameters", 0740);
    @chmod("../journal", 0740);
    @chmod("../mod", 0777);
    @chmod("../mod/autoupdate/tmp", 0777);

    /**
     * Affiche une boite d'erreur de permission
     */
    $error = "";
    $alerte = false;
    if (is_writeable("../parameters")) {
        $error .= "<tr><td width=\"250\">- \"parameters\" : </td><td><font color='green'>" . $lang['INSTALL_WRITE_ALLOWED'] . "</font></td></tr>";
    } else {
        $error .= "<tr><td width=\"250\">- \"parameters\" : </td><td><font color='red'>" . $lang['INSTALL_WRITE_DENIED'] . "</font></td></tr>";
        $alerte = true;
    }

    if (is_writeable("../journal")) {
        $error .= "<tr><td width=\"250\">- \"journal\" : </td><td><font color='green'>" . $lang['INSTALL_WRITE_ALLOWED'] . "</font></td></tr>";
    } else {
        $error .= "<tr><td width=\"250\">- \"journal\" : </td><td><font color='red'>" . $lang['INSTALL_WRITE_DENIED'] . "</font></td></tr>";
        $alerte = true;
    }

    $error2 = "";
    if (is_writeable("../mod")) {
        $error2 .= "<tr><td width=\"250\">- \"mod\" : </td><td><font color='green'>" . $lang['INSTALL_WRITE_ALLOWED'] . "</font></td></tr>";
    } else {
        $error2 .= "<tr><td width=\"250\">- \"mod\" : </td><td><font color='red'>" . $lang['INSTALL_WRITE_DENIED'] . "</font></td></tr>";
        $alerte = true;
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

    $sgbd_server = $pub_sgbd_server ?? "localhost";
    $sgbd_dbname = $pub_sgbd_dbname ?? "ogspy";
    $sgbd_username = $pub_sgbd_username ?? "";
    $sgbd_password = $pub_sgbd_password ?? "";
    $sgbd_tableprefix = $pub_sgbd_tableprefix ?? "ogspy_";
    $admin_username = $pub_admin_username ?? "admin";
    $admin_password = $pub_admin_password ?? "";
    $admin_password2 = $pub_admin_password2 ?? "";
    $num_of_galaxies = $pub_num_of_galaxies ?? 9;
    $num_of_systems = $pub_num_of_systems ?? 499;
    $uni_speed = $pub_uni_speed ?? 1;
    $directory = $pub_directory ?? "";


    if (
        isset($pub_sgbd_server) && isset($pub_sgbd_dbname) && isset($pub_sgbd_username) && isset($pub_sgbd_password) && isset($pub_sgbd_tableprefix) &&
        isset($pub_admin_username) && isset($pub_admin_password) && isset($pub_admin_password2) && isset($pub_num_of_galaxies) && isset($pub_num_of_systems) && isset($pub_uni_speed) && isset($pub_lang)
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
                            <th><input name="sgbd_server" type="text" value="<?php echo $pub_sgbd_server ?? "localhost"; ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBNAME']; ?></th>
                            <th><input name="sgbd_dbname" type="text" value="<?php echo $pub_sgbd_dbname ?? ""; ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBUSER']; ?></th>
                            <th><input name="sgbd_username" type="text" value="<?php echo $pub_sgbd_username ?? ""; ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBPWD']; ?></th>
                            <th><input name="sgbd_password" type="password"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBPREFIX']; ?></th>
                            <th><input name="sgbd_tableprefix" type="text" value="<?php echo $pub_sgbd_tableprefix ?? "ogspy_"; ?>">
                            </th>
                        </tr>
                        <tr>
                            <td class="c" colspan="2"><?php echo $lang['INSTALL_VIEW_DBUNI']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBUNIGAL']; ?><?php echo help("profile_galaxy", "", "../"); ?></th>
                            <th><input name="num_of_galaxies" type="text" value="<?php echo $pub_num_of_galaxies ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBUNISYS']; ?><?php echo help("profile_galaxy", "", "../"); ?></th>
                            <th><input name="num_of_systems" type="text" value="<?php echo $pub_num_of_systems ?>"></th>
                        </tr>
                        <tr>
                            <th><?php echo $lang['INSTALL_VIEW_DBUNISPEED']; ?><?php echo help("profile_speed", "", "../"); ?></th>
                            <th><input name="uni_speed" type="text" value="<?php echo isset($pub_uni_speed) ? $pub_uni_speed : "1"; ?>"></th>
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
                                <b>OGSteam Software</b> (c)2005-2024</i><br />v <?= $ogspy_version ?></span></div>
                </td>
            </tr>
        </table>
    </form>
</body>

</html>
