<?php

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
 * @param string $sgbd_dbname Nom de la base de données
 * @param string $sgbd_username Utilisateur Base de donné
 * @param string $sgbd_password Mot de passe Base de donné
 * @param string $sgbd_tableprefix Préfixe à utiliser pour les tables ogspy
 * @param string $admin_username Nom de l'Administrateur OGSpy
 * @param string $admin_password Mot de passe Administrateur OGSpy
 * @param int    $num_of_galaxies Nombre de galaxies dans l'univers OGame de cet OGSp
 * @param int    $num_of_systems Nombre de systèmes dans l'univers OGame de cet OGSpy
 * @param string $ui_lang Langue
 */

function installation_db($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $admin_username, $admin_password, $num_of_galaxies, $num_of_systems, $uni_speed, $ui_lang)
{
    global $lang, $ogspy_version;
    $db = sql_db::getInstance($sgbd_server, $sgbd_username, $sgbd_password, $sgbd_dbname);
    if (!$db->db_connect_id) {
        error_sql($lang['INSTALL_SQL_CONNECTION_ERROR']);
    }


    //Création de la structure de la base de données
    if (is_file("schemas/ogspy_structure.sql")) {
        $sql_query = @fread(@fopen("schemas/ogspy_structure.sql", 'r'), @filesize("schemas/ogspy_structure.sql"));
    } else {
        exit("<h1>SQL structure file has not been found</h1>");
    }

    if (trim($sgbd_tableprefix) != "") {
        $sql_query = str_replace("ogspy_", $sgbd_tableprefix, $sql_query);
    }

    $sql_query = explode(";", $sql_query);
    $sql_query[] = "INSERT INTO `" . $sgbd_tableprefix . "config` (`config_name`, `config_value`) VALUES ('num_of_galaxies','$num_of_galaxies')";
    $sql_query[] = "INSERT INTO `" . $sgbd_tableprefix . "config` (`config_name`, `config_value`) VALUES ('num_of_systems','$num_of_systems')";
    $sql_query[] = "INSERT INTO `" . $sgbd_tableprefix . "config` (`config_name`, `config_value`) VALUES ('speed_uni','$uni_speed')";
    $sql_query[] = "INSERT INTO `" . $sgbd_tableprefix . "config` (`config_name`, `config_value`) VALUES ('version','$ogspy_version')";

    foreach ($sql_query as $request) {
        if (trim($request) != "") {
            if (!($result = $db->sql_query($request))) {
                $error = $db->sql_error($result);
                echo $request;
                error_sql($error['message']);
            }
        }
    }

    $request = "INSERT INTO " . $sgbd_tableprefix .
        "user (user_id, user_name, user_password_s , user_regdate, user_active, user_admin, user_pwd_change)" .
        " values (1, '" . mysqli_real_escape_string($db->db_connect_id, $admin_username) . "', '" .
        password_hash($admin_password, PASSWORD_DEFAULT) . "', " . time() . ", '1', '1', '0')";
    if (!($result = $db->sql_query($request))) {
        $error = $db->sql_error($result);
        echo $request;
        error_sql($error['message']);
    }

    $request = "INSERT INTO " . $sgbd_tableprefix . "user_group (group_id, user_id) values (1, 1)";
    if (!($result = $db->sql_query($request))) {
        $error = $db->sql_error($result);
        echo $request;
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
 * @param string $sgbd_server Serveur MySql
 * @param string $sgbd_username Utilisateur Base de donnée
 * @param string $sgbd_password Mot de passe Base de donnée
 * @param string $sgbd_tableprefix Préfixe à utiliser pour les tables ogspy
 * @throws FileAccessException
 */
function generate_id($sgbd_server, $sgbd_dbname, $sgbd_username, $sgbd_password, $sgbd_tableprefix, $install_ui_lang)
{
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
