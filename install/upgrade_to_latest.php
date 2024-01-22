<?php

/**
 * OGSpy installation : Script Upgrade
 * @package OGSpy
 * @subpackage install
 * @author Kyser
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.04
 */

if (!defined('IN_SPYOGAME')) {
    define("IN_SPYOGAME", true);
}

if (!defined('UPGRADE_IN_PROGRESS')) {
    define("UPGRADE_IN_PROGRESS", true);
}

require("../common.php");

if (!isset($pub_verbose)) {
    $pub_verbose = true;
}

if (!isset($ogspy_version)) {
    require_once("./version.php");
}

if ($pub_verbose) {
?>

    <html lang="fr">

    <head>
        <title>Mise à jour OGSpy</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="../skin/OGSpy_skin/formate.css" />
    </head>

    <body>

    <?php
}

// on réinitialise la sequense config
// evite d utiliser le cache ( qui sera périmé ))
$request = "SELECT * FROM " . TABLE_CONFIG;
$result = $db->sql_query($request);
while (list($name, $value) = $db->sql_fetch_row($result)) {
    $server_config[$name] = stripslashes($value);
}


$request = "SELECT `config_value` FROM " . TABLE_CONFIG . " WHERE config_name = 'version'";
$result = $db->sql_query($request);
list($ogsversion) = $db->sql_fetch_row($result);

$requests = array();
switch ($ogsversion) {
    case '3.3.6':

        $requests[] = "ALTER TABLE " . TABLE_PARSEDSPY . " MODIFY `metal` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDSPY . " MODIFY `cristal` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDSPY . " MODIFY `deuterium` BIGINT";

        //table player
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `user_class`  ENUM ('none', 'COL', 'GEN', 'EXP') NOT NULL DEFAULT 'none' AFTER `user_stat_name`";
        //table spy
        $requests[] = "ALTER TABLE `" . TABLE_PARSEDSPY . "` ADD `ECL` INT(11) NOT NULL DEFAULT  '-1'  AFTER `TRA`";
        $requests[] = "ALTER TABLE `" . TABLE_PARSEDSPY . "` ADD `FAU` INT(11) NOT NULL DEFAULT  '-1'  AFTER `TRA`";
        $requests[] = "ALTER TABLE `" . TABLE_PARSEDSPY . "` ADD `FOR` INT(11) NOT NULL DEFAULT  '-1'  AFTER `TRA`";
        //table building
        $requests[] = "ALTER TABLE `" . TABLE_USER_BUILDING . "` ADD `FOR` SMALLINT(5) NOT NULL DEFAULT '-1' AFTER `Sat_percentage`";
        $requests[] = "ALTER TABLE `" . TABLE_USER_BUILDING . "` ADD `FOR_percentage` SMALLINT(3) NOT NULL DEFAULT '100' AFTER `FOR`";
        //table Round
        $requests[] = "ALTER TABLE `" . TABLE_ROUND_ATTACK . "` ADD `FAU` SMALLINT(2) NOT NULL DEFAULT '-1' AFTER `TRA`";
        $requests[] = "ALTER TABLE `" . TABLE_ROUND_ATTACK . "` ADD `ECL` SMALLINT(2) NOT NULL DEFAULT '-1' AFTER `TRA`";
        $requests[] = "ALTER TABLE `" . TABLE_ROUND_DEFENSE . "` ADD `FAU` SMALLINT(2) NOT NULL DEFAULT '-1' AFTER `TRA`";
        $requests[] = "ALTER TABLE `" . TABLE_ROUND_DEFENSE . "` ADD `ECL` SMALLINT(2) NOT NULL DEFAULT '-1' AFTER `TRA`";
        $requests[] = "ALTER TABLE `" . TABLE_ROUND_DEFENSE . "` ADD `FOR` SMALLINT(2) NOT NULL DEFAULT '-1' AFTER `SAT`";

    case '3.3.7':
        // New Table
        if (!defined('TABLE_MOD_USER_CFG')) {
            define("TABLE_MOD_USER_CFG", $table_prefix . "mod_user_config");
        }
        $requests[] = "CREATE TABLE IF NOT EXISTS `" . TABLE_MOD_USER_CFG . "` (
                        `mod`     VARCHAR(50) NOT NULL,
                        `user_id` INT(11) NOT NULL,
                        `config`  VARCHAR(255) NOT NULL,
                        `value`   VARCHAR(255) NOT NULL,
                        PRIMARY KEY (`mod`, `user_id`, `config`),
                        UNIQUE KEY `config` (`config`)
                    ) DEFAULT CHARSET = UTF8;";

        $requests[] = "INSERT INTO " . TABLE_CONFIG . " VALUES ('donutSystem','1')";
        $requests[] = "INSERT INTO " . TABLE_CONFIG . " VALUES ('donutGalaxy','1')";
        $requests[] = "INSERT INTO " . TABLE_CONFIG . " VALUES ('speed_fleet_peaceful','1')";
        $requests[] = "INSERT INTO " . TABLE_CONFIG . " VALUES ('speed_fleet_war','1')";
        $requests[] = "INSERT INTO " . TABLE_CONFIG . " VALUES ('speed_fleet_holding','1')";
        $requests[] = "INSERT INTO " . TABLE_CONFIG . " VALUES ('speed_research_divisor','1')";


        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `user_pwd_change` TINYINT(1) NOT NULL DEFAULT '1' AFTER `user_password_s`";
        $requests[] = "UPDATE " . TABLE_USER . " SET `user_pwd_change` = '0' WHERE `user_pwd_change` = '1'";    //Ne pas impacter les users existant
        $requests[] = "ALTER TABLE `" . TABLE_USER . "` ADD `user_email_valid` TINYINT(1) NOT NULL DEFAULT '0' AFTER `user_email`";
        $requests[] = "ALTER TABLE " . TABLE_USER . " ADD `ally_class` ENUM ('none', 'MAR', 'WAR', 'RES') NOT NULL DEFAULT 'none' AFTER `user_class`";

        $requests[] = "ALTER TABLE " . TABLE_RANK_PLAYER_MILITARY . " MODIFY `nb_spacecraft` BIGINT";

        $requests[] = "ALTER TABLE " . TABLE_PARSEDRCROUND . " MODIFY `attaque_tir` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRCROUND . " MODIFY `attaque_puissance` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRCROUND . " MODIFY `defense_bouclier` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRCROUND . " MODIFY `attaque_bouclier` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRCROUND . " MODIFY `defense_tir` BIGINT";
        $requests[] = "ALTER TABLE " . TABLE_PARSEDRCROUND . " MODIFY `defense_puissance` BIGINT";

        $requests[] = "ALTER TABLE " . TABLE_GAME_ALLY . " ADD `class` ENUM ('none', 'MAR', 'WAR', 'RES') NOT NULL DEFAULT 'none' AFTER `tag`";
        $requests[] = "ALTER TABLE " . TABLE_GAME_PLAYER . " ADD `class` ENUM ('none', 'COL', 'GEN', 'EXP') NOT NULL DEFAULT 'none' AFTER `status`";


        $requests[] = "ALTER TABLE " . TABLE_MOD . " MODIFY `version` VARCHAR(100) NOT NULL";
        $requests[] = "ALTER TABLE " . TABLE_USER_BUILDING . " MODIFY `boosters` VARCHAR(64) NOT NULL DEFAULT 'm:0:0_c:0:0_d:0:0_e:0:0_p:0_m:0'";
        //no break pour faire toutes les mises à jour d'un coup !
        case '3.3.8':
        break;
    default:
        echo "Aucune mise à jour de base de données trouvée";
}

$requests[] = "UPDATE " . TABLE_CONFIG . " SET `config_value` = '$ogspy_version' WHERE `config_name` = 'version'";

foreach ($requests as $request) {
    $db->sql_query($request);
}

// on supprime tous les fichiers du cache
// pour prendre en compte toutes les modifications
$files = glob('../cache/*.php');
if (count($files) > 0) {
    foreach ($files as $filename) {
        unlink($filename);
    }
}
if ($pub_verbose) { //Silent Upgrade
    ?>
        <h3 align='center'><span style="color: yellow; ">Mise à jour du serveur OGSpy vers la version <?= $ogspy_version ?> réussie</span></h3>
        <div style="text-align: center;">
            <br>
            <b><i>Voulez-vous supprimer le dossier 'install' ?</i></b><br>
            <br><a href='../index.php'>Oui</a>
        </div>
    </body>

    </html>
<?php
}
?>
