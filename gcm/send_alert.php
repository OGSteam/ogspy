<?php
/**
 * This file used to send pushnotification to android device by making a request to GCM server.
 * @package OGSpy
 * @subpackage Common
 * @author Jedinight
 * @copyright Copyright &copy; 2013, http://www.ogsteam.fr/
 * @version 1.0.0
 */
define('IN_SPYOGAME', true); // permet d'inclure les fichiers ogspy
define('IN_REGISTERING_GCM', true);

$regId = $_GET["regId"];
$message = $_GET["message"];
$username = $_GET["username"];


if (!isset($regId)) {
    $regId = $_POST["regId"];
}
if (!isset($message)) {
    $message = $_POST["message"];
}
if (!isset($username)) {
    $username = $_POST["username"];
}

/**
 * @param $texte
 */
function writeLog($texte)
{
    $filename = 'log_alert.txt';
    $date = date("d-m-Y");
    $heure = date("G:i");

    // Assurons nous que le fichier est accessible en écriture
    if (is_writeable($filename)) {
        // Dans notre exemple, nous ouvrons le fichier $filename en mode d'ajout
        // Le pointeur de fichier est placé à la fin du fichier
        // c'est là que le texte sera placé
        if (!$handle = fopen($filename, 'a')) {
            //echo "Impossible d'ouvrir le fichier ($filename)";
            exit;
        }
        // Ecrivons quelque chose dans notre fichier.
        if (fwrite($handle, $date . " - " . $heure . " : " . $texte . "\n") === FALSE) {
            //echo "Impossible d'écrire dans le fichier ($filename)";
            exit;
        }
        //echo "L'écriture de ($texte) dans le fichier ($filename) a réussi";
        fclose($handle);
    } else {
        //echo "Le fichier $filename n'est pas accessible en écriture.";
    }
}

if (isset($regId) && isset($message) && isset($username)) {
    // Inclusion de fichiers
    require_once("../parameters/id.php");
    require_once("../includes/config.php");
    require_once("../includes/functions.php");

    require_once("../includes/gcm/gcm.php");

    require_once("../includes/mysql.php");
    // attention penser a la supp 2 ouvertures faites
    //appel co sql natif
    $db = sql_db::getInstance($db_host, $db_user, $db_password, $db_database);
    if (!$db->db_connect_id) {
        die("Impossible de se connecter à la base de données : '" . $db_host . "'");
    }
    $gcm = new GCM();

    $users = getAllGCMUsersExceptMe($regId);
    $registatoin_ids = array();
    while ($row = $db->sql_fetch_row($users)) {
        $gcmRegid = $row[0];
        $registatoin_ids[] = $gcmRegid;
        //$registatoin_ids = array($gcmRegid);
        //$messageArray = array("message" => "Alerte de " . $username . " : " . $message);

        //$result = $gcm->send_notification($registatoin_ids, $messageArray);
        //writeLog("$username envoie le message **$message** a $gcmRegid");
        //writeLog("Résultat : $result");
    }
    writeLog("$username envoie un message ($message) a " . sizeof($registatoin_ids) . " membre(s) de la communauté.");

    //$registatoin_ids = array($regsIds);
    $messageArray = array("message" => $message, "sender" => $username, "messagetype" => "message");

    $result = $gcm->send_notification($registatoin_ids, $messageArray);
    writeLog("Resultat : $result");

    echo $result;
    //return $result;
} else {
    echo "hack";
}
