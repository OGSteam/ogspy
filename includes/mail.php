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

// Pour configuration a inserer dans BDD
//mail_active => activation ou non des mails
//mail_server   sender
// si smtp
//mail_smtp_use
//mail_smtp_secure
//mail_smtp_host
//mail_smtp_port
//mail_smtp_username
//mail_smtp_password









//sample smtp yahoo
//$mail = new PHPMailer();
//$body  = "<h1>hello, world!</h1>";
//$mail->IsSMTP(); // telling the class to use SMTP
//$mail->SMTPAuth   = true;                  // enable SMTP authentication
//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
//$mail->Host       = "smtp.mail.yahoo.com";      // sets YAHOO as the SMTP server
//$mail->Port       = 465;                   // set the SMTP port for the yahoo server
//$mail->Username   = "@yahoo.fr";  // yahoo username
//$mail->Password   = "mdp";            // yahoo password
//$mail->SetFrom('@yahoo.fr', 'First Last');
//$mail->AddReplyTo("@yahoo.fr","First Last");
//$mail->Subject    = "PHPMailer Test Subject via smtp (yahoo), basic";
//$mail->MsgHTML($body);
//$address = "@yahoo.fr";
//$mail->AddAddress($address, "John Doe");
//
//https://github.com/PHPMailer/PHPMailer/blob/master/examples/mail.phps
//sample  without smtp
//$mail = new PHPMailer;
//$mail->setFrom('from@example.com', 'First Last');
//$mail->addReplyTo('replyto@example.com', 'First Last');
//$mail->addAddress('whoto@example.com', 'John Doe');
//$mail->Subject = 'PHPMailer mail() test';
//$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
//$mail->AltBody = 'This is a plain-text message body';
//$mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
//if (!$mail->send()) {
//    echo "Mailer Error: " . $mail->ErrorInfo;
//} else {
//    echo "Message sent!";
//}
