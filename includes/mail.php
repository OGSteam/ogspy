<?php
/**
 * Fonctions relatives aux mail  (via PHPMailer)
 *
 * @package OGSpy
 * @subpackage mail
 * @author machine
 * @copyright Copyright &copy; 2007, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

//-----accés PHPMailer------\\\
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require("includes/PHPMailer/PHPMailer.php");
require("includes/PHPMailer/SMTP.php");
require("includes/PHPMailer/Exception.php");
//-----fin accés PHPMailer---\\\

/**
 * @param $dest string/array tableau de destinataire
 * @param $subject string sujet du message
 * @param $HTMLBody string contenu du message en HTML
 */
function SendMail($dest, $subject, $HTMLBody)
{
    //verifiaction avant envoit de mail
    global $server_config;

    //usage du mail possible
    if ($server_config["mail_use"] != '1') {
        log_("debug", "Tentative de mailing config.mail_use = " . $server_config["mail_use"]);
        return false;
    }

    // le serveur à un mail defini
    if (!check_var($server_config["mail_smtp_username"], "Email")) {
        log_("Mail", "Erreur de format mail sender " . $server_config["mail_smtp_username"]);
        return false;
    }


    // le serveur à un sujet de type string
    if (!is_string($subject)) {
        log_("Mail", "Erreur de format sujet");
        return false;
    }

    // le serveur à un corps de message de type string
    if (!is_string($HTMLBody)) {
        log_("Mail", "Erreur de format body");
        return false;
    }


    // configuration de l envoi
    $mail = new PHPMailer();
    $mail->setFrom($server_config["mail_smtp_username"], $server_config["servername"]);
    $mail->Subject = $subject;
    $mail->msgHTML($HTMLBody);

    //gestion et verification des destinataires
    $is_dest = false;
    if (is_array($dest)) {
        foreach ($dest as $maildest) {
            if (!check_var($maildest, "Email")) {
                log_("Mail", "Erreur de format mail destinataire " . $maildest);
            } else {
                $mail->addAddress($maildest, $maildest);
                $is_dest = true;
            }
        }
    } else {
        if (!check_var($dest, "Email")) {
            log_("Mail", "Erreur de format mail destinataire " . $dest);
        } else {
            $is_dest = true;
            $mail->addAddress($dest, $dest);
        }

    }
    if (!$is_dest) {
        log_("Mail", "Aucun destinataire valide");
        return false;
    }
    //fin gestion et verification des destinataires


    //si mailing natif server
    if ($server_config["mail_smtp_use"] != 1) {
        if (!$mail->send()) {
            log_("Mail", "Erreur MAIL 1  " . $mail->ErrorInfo);
        } else {
            return true;
        }
    }

    //sinon SMTP
    $mail->IsSMTP();
    // securisé ?
    if ($server_config["mail_smtp_secure"] != 0) {
        $mail->SMTPSecure = "tsl";
    }


    $mail->Host = $server_config["mail_smtp_host"];      // sets YAHOO as the SMTP server
    $mail->Port = (int)$server_config["mail_smtp_port"]; // set the SMTP port for the yahoo server

    if ($server_config["Password"] != "") {
        $mail->Username = $server_config["mail_smtp_username"];  // yahoo username
        $mail->Password = $server_config["mail_smtp_username"];            // yahoo password
    }

    if (!$mail->Send()) {
        log_("Mail", " Erreur MAIL 2  " . $mail->ErrorInfo);
    } else {
        return true;
    }

}



