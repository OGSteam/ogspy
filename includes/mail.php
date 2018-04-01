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

//-----accès PHPMailer------\\\
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once("includes/PHPMailer/PHPMailer.php");
require_once("includes/PHPMailer/SMTP.php");
require_once("includes/PHPMailer/Exception.php");
//-----fin accès PHPMailer---\\\

/**
 * @param $dest string/array tableau de destinataire
 * @param $subject string sujet du message
 * @param $HTMLBody string contenu du message en HTML
 * @return bool
 * @throws Exception
 */
function sendMail($dest, $subject, $HTMLBody)
{
    //verifiaction avant envoi de mail
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
            mailCounter();
            return true;
        }
        return false;
    }

    //sinon SMTP
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Timeout = 28;

    // securisé ?
    if ($server_config["mail_smtp_secure"] != 0) {
        $mail->SMTPSecure = "ssl";
    }


    $mail->Host = $server_config["mail_smtp_host"];      // sets YAHOO as the SMTP server

    $mail->Port = (int)$server_config["mail_smtp_port"]; // set the SMTP port for the yahoo server

    //get password
    include_once("parameters/mail.php"); //TODO : A stocker en base non ?
    if ($mail_smtp_password != "") {
        $mail->Username = $server_config["mail_smtp_username"];  // yahoo username
        $mail->Password = $mail_smtp_password;  // yahoo password
    }

    if (!$mail->Send()) {
        log_("Mail", " Erreur MAIL 2  " . $mail->ErrorInfo);
    } else {
        mailCounter();
        return true;
    }

}


//Incremente le compte de mail
function mailCounter()
{
    global $db,  $server_config;
    if (!isset($server_config['count_mail']))
    {
        $server_config['count_mail'] = 0;
    }
   $total =  $server_config['count_mail'] + 1;

    $request = "REPLACE INTO " . TABLE_CONFIG ." (config_name, config_value) VALUES ('count_mail','$total')";
    $db->sql_query($request);
    // mise a jour des caches avec les mofids
    generate_config_cache();

}

//passage du mdp en system de fichier (pas de mdp en clair dans variables sessions et BDD
function setMailSMTPPassword($password)
{
    $fh = @fopen('parameters/mail.php', 'wb');
    fwrite($fh, '<?php' . "\n\n" . 'if (!defined("IN_SPYOGAME")) die("Hacking attempt");' . "\n\n" . '$mail_smtp_password ="'.$password.'";' . "\n\n" . '?>');
    fclose($fh);
}






