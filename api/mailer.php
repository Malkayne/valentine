<?php
// ================================================
// MAILER HELPER — Mailtrap SMTP via PHPMailer
// ================================================

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email via Mailtrap SMTP.
 *
 * @param string $toEmail    Recipient email address
 * @param string $toName     Recipient name
 * @param string $subject    Email subject
 * @param string $htmlBody   HTML body
 * @return bool              True on success
 */
function sendMail($toEmail, $toName, $subject, $htmlBody) {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = false;
        $mail->Port       = MAIL_PORT;

        // Disable SSL verification for Mailtrap sandbox
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        // Sender
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

        // Recipient
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject  = $subject;
        $mail->Body     = $htmlBody;
        $mail->AltBody  = strip_tags(str_replace('<br>', "\n", $htmlBody));
        $mail->CharSet  = 'UTF-8';

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
