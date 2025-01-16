<?php
require_once("./admin_protect.php");

function sendEmail($to, $toName, $subject, $body, $altBody, $mail)
{



    try {
        $mail->addAddress($to, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Email error: " . $mail->ErrorInfo, 3, "/var/log/email_errors.log";
        return false;
    }
}



// Include the PHPMailer fi
