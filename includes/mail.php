<?php

/**
 * Fonctions relatives aux mail  (via PHPMailer)
 *
 * @package OGSpy
 * @subpackage mail
 * @author machine
 * @copyright Copyright &copy; 2007, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$folder = "";
if (defined("INSTALL_IN_PROGRESS") || defined("UPGRADE_IN_PROGRESS")) {

    $folder = "../";
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Ogsteam\Ogspy\Model\Config_Model;

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
    global $server_config, $log;

    $log->info("Starting email sending process", [
        'recipient_count' => is_array($dest) ? count($dest) : 1,
        'subject' => $subject,
        'body_length' => strlen($HTMLBody),
        'mail_use_enabled' => $server_config["mail_use"] ?? 'undefined'
    ]);

    //usage du mail possible
    if ($server_config["mail_use"] != '1') {
        $log->warning("Email sending disabled in configuration", [
            'mail_use' => $server_config["mail_use"],
            'subject' => $subject
        ]);
        return false;
    }

    // le serveur à un mail defini
    if (!check_var($server_config["mail_smtp_username"], "Email")) {
        $log->error("Invalid sender email configuration", [
            'sender_email' => $server_config["mail_smtp_username"] ?? 'undefined',
            'subject' => $subject
        ]);
        return false;
    }

    // le serveur à un sujet de type string
    if (!is_string($subject)) {
        $log->error("Invalid subject format", [
            'subject_type' => gettype($subject),
            'subject_value' => $subject
        ]);
        return false;
    }

    // le serveur à un corps de message de type string
    if (!is_string($HTMLBody)) {
        $log->error("Invalid email body format", [
            'body_type' => gettype($HTMLBody),
            'body_length' => is_string($HTMLBody) ? strlen($HTMLBody) : 'N/A'
        ]);
        return false;
    }

    $log->debug("Email configuration validation passed", [
        'sender' => $server_config["mail_smtp_username"],
        'subject' => $subject,
        'body_length' => strlen($HTMLBody)
    ]);

    // configuration de l envoi
    try {
        $mail = new PHPMailer();
        $mail->setFrom($server_config["mail_smtp_username"], $server_config["servername"]);
        $mail->Subject = $subject;
        $mail->msgHTML($HTMLBody);

        $log->debug("PHPMailer instance created and configured", [
            'sender' => $server_config["mail_smtp_username"],
            'server_name' => $server_config["servername"]
        ]);
    } catch (Exception $e) {
        $log->error("Failed to create PHPMailer instance", [
            'error' => $e->getMessage(),
            'subject' => $subject
        ]);
        return false;
    }

    //gestion et verification des destinataires
    $is_dest = false;
    $valid_recipients = 0;
    $invalid_recipients = 0;

    if (is_array($dest)) {
        $log->debug("Processing multiple recipients", ['total_count' => count($dest)]);

        foreach ($dest as $maildest) {
            if (!check_var($maildest, "Email")) {
                $log->warning("Invalid recipient email format", ['email' => $maildest]);
                $invalid_recipients++;
            } else {
                try {
                    $mail->addAddress($maildest, $maildest);
                    $is_dest = true;
                    $valid_recipients++;
                    $log->debug("Added valid recipient", ['email' => $maildest]);
                } catch (Exception $e) {
                    $log->error("Failed to add recipient", ['email' => $maildest, 'error' => $e->getMessage()]);
                    $invalid_recipients++;
                }
            }
        }
    } else {
        $log->debug("Processing single recipient", ['email' => $dest]);

        if (!check_var($dest, "Email")) {
            $log->error("Invalid single recipient email format", ['email' => $dest]);
            $invalid_recipients++;
        } else {
            try {
                $is_dest = true;
                $mail->addAddress($dest, $dest);
                $valid_recipients++;
                $log->debug("Added single valid recipient", ['email' => $dest]);
            } catch (Exception $e) {
                $log->error("Failed to add single recipient", ['email' => $dest, 'error' => $e->getMessage()]);
                $invalid_recipients++;
            }
        }
    }

    if (!$is_dest) {
        $log->error("No valid recipients found", [
            'valid_recipients' => $valid_recipients,
            'invalid_recipients' => $invalid_recipients,
            'subject' => $subject
        ]);
        return false;
    }

    $log->info("Recipients processed", [
        'valid_recipients' => $valid_recipients,
        'invalid_recipients' => $invalid_recipients,
        'subject' => $subject
    ]);
    //fin gestion et verification des destinataires

    //si mailing natif server
    if ($server_config["mail_smtp_use"] != 1) {
        $log->debug("Using native mail server", ['smtp_use' => $server_config["mail_smtp_use"]]);

        try {
            if (!$mail->send()) {
                $log->error("Native mail sending failed", [
                    'error' => $mail->ErrorInfo,
                    'subject' => $subject,
                    'recipients' => $valid_recipients
                ]);
                return false;
            } else {
                $log->info("Email sent successfully via native mail", [
                    'subject' => $subject,
                    'recipients' => $valid_recipients
                ]);
                mailCounter();
                return true;
            }
        } catch (Exception $e) {
            $log->error("Exception during native mail sending", [
                'error' => $e->getMessage(),
                'subject' => $subject
            ]);
            return false;
        }
    }

    //sinon SMTP
    $log->debug("Using SMTP configuration", [
        'host' => $server_config["mail_smtp_host"] ?? 'undefined',
        'port' => $server_config["mail_smtp_port"] ?? 'undefined',
        'secure' => $server_config["mail_smtp_secure"] ?? 'undefined'
    ]);

    try {
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Timeout = 28;

        // securisé ?
        if ($server_config["mail_smtp_secure"] != 0) {
            $mail->SMTPSecure = "ssl";
            $log->debug("SMTP SSL/TLS enabled");
        }

        $mail->Host = $server_config["mail_smtp_host"];      // sets YAHOO as the SMTP server
        $mail->Port = (int)$server_config["mail_smtp_port"]; // set the SMTP port for the yahoo server

        //get password
        if (isset($mail_smtp_password) && $mail_smtp_password != "") {
            $mail->Username = $server_config["mail_smtp_username"];  // yahoo username
            $mail->Password = $mail_smtp_password;  // yahoo password
            $log->debug("SMTP authentication configured", ['username' => $server_config["mail_smtp_username"]]);
        } else {
            $log->warning("SMTP password not found or empty");
        }

        if (!$mail->Send()) {
            $log->error("SMTP mail sending failed", [
                'error' => $mail->ErrorInfo,
                'host' => $server_config["mail_smtp_host"],
                'port' => $server_config["mail_smtp_port"],
                'subject' => $subject,
                'recipients' => $valid_recipients
            ]);
            return false;
        } else {
            $log->info("Email sent successfully via SMTP", [
                'host' => $server_config["mail_smtp_host"],
                'port' => $server_config["mail_smtp_port"],
                'subject' => $subject,
                'recipients' => $valid_recipients
            ]);
            mailCounter();
            return true;
        }
    } catch (Exception $e) {
        $log->error("Exception during SMTP mail sending", [
            'error' => $e->getMessage(),
            'host' => $server_config["mail_smtp_host"] ?? 'undefined',
            'subject' => $subject
        ]);
        return false;
    }
}


//Incremente le compte de mail
function mailCounter()
{
    global $server_config, $log;

    $log->debug("Starting mail counter increment", ['current_count' => $server_config['count_mail'] ?? 'undefined']);

    if (!isset($server_config['count_mail'])) {
        $server_config['count_mail'] = 0;
        $log->debug("Mail counter initialized to 0");
    }

    $total = $server_config['count_mail'] + 1;

    try {
        (new Config_Model())->update_one($total, "count_mail");
        $log->info("Mail counter incremented successfully", [
            'previous_count' => $server_config['count_mail'],
            'new_count' => $total
        ]);
    } catch (Exception $e) {
        $log->error("Failed to update mail counter in database", [
            'current_count' => $server_config['count_mail'],
            'attempted_count' => $total,
            'error' => $e->getMessage()
        ]);
        return;
    }

    // mise a jour des caches avec les modifications
    try {
        generate_config_cache();
        $log->debug("Configuration cache regenerated after mail counter update");
    } catch (Exception $e) {
        $log->warning("Failed to regenerate config cache after mail counter update", [
            'error' => $e->getMessage(),
            'mail_count' => $total
        ]);
    }
}

//passage du mdp en system de fichier (pas de mdp en clair dans variables sessions et BDD
function setMailSMTPPassword($password)
{
    global $log;

    $log->info("Starting SMTP password configuration", ['password_length' => strlen($password)]);

    // Validation du mot de passe
    if (!is_string($password)) {
        $log->error("Invalid password type provided", ['password_type' => gettype($password)]);
        return false;
    }

    if (empty($password)) {
        $log->warning("Empty password provided for SMTP configuration");
    }

    //TODO : Save password in config table

    return true;
}
