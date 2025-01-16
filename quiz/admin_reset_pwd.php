<?php
require_once("./header.php");
require_once("./admin_protect.php");

require './phpmailer/Exception.php';
require './phpmailer/PHPMailer.php';
require './phpmailer/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once("./mail.php");

function logToFile($message)
{
    $logFile = 'email_status_log.txt';
    file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);
}

if (isset($_GET['to'])) {
    $to = $_GET['to'];

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = "sg2plzcpnl508745.prod.sin2.secureserver.net";
    $mail->SMTPAuth = true;
    $mail->Username = "lexathon@advkbalakrishnannair.com";
    $mail->Password = "LxTnAKBN@25";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->SMTPKeepAlive = true;
    $mail->setFrom('lexathon@advkbalakrishnannair.com', 'Lexathon');
    if ($to == 'all') {
        set_time_limit(0); //removes time limit
        function sendBatchCredentials($con, $mail)
        {
            // Fetch users from the database
            $result = $con->query("SELECT user_id, email, first_name, last_name FROM users WHERE role='participant'");

            if (!$result || $result->num_rows == 0) {
                logToFile("No users found to send credentials.");
                echo "No users found to send credentials.";
                return;
            }

            while ($row = $result->fetch_assoc()) {
                $userId = $row['user_id'];
                $userEmail = $row['email'];
                $userName = $row['first_name'] . " " . $row['last_name'];

                // Generate a random password
                $plainPassword = bin2hex(random_bytes(4));
                $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

                // Update the database with the hashed password
                $stmt = $con->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $hashedPassword, $userId);
                    if ($stmt->execute()) {
                        // Send the email
                        $subject = "Login Credentials for quiz competition";
                        $body = "Dear {$userName},<br><br>Your login credentials are:<br>
                           Email: {$userEmail}<br>
                           Password: {$plainPassword}<br><br>
                           Please log in with the same.";
                        $altBody = "Dear {$userName},\n\nYour login credentials are:\nEmail: {$userEmail}\nPassword: {$plainPassword}\n\nPlease log in with the same.";

                        if (!sendEmail($userEmail, $userName, $subject, $body, $altBody, $mail)) {
                            $message = "Failed to send email to {$userEmail} (ID: {$userId}).";
                            logToFile($message);
                        } else {
                            $message = "Email sent successfully to {$userEmail} (ID: {$userId}).";
                            logToFile($message);
                        }
                    } else {
                        $message = "Failed to update password for user ID {$userId}.";
                        logToFile($message);
                    }
                    $stmt->close();
                } else {
                    $message = "Failed to prepare statement for user ID {$userId}: " . $con->error;
                    logToFile($message);
                }

                // Optional: Delay to avoid overwhelming the email server
                sleep(1); // 1-second delay
            }
        }
        sendBatchCredentials($con, $mail);
    } else if (filter_var($to, FILTER_VALIDATE_INT) !== false) {
        function reset_pwd($con, $user_id, $mail)
        {

            $newPassword = bin2hex(random_bytes(6)); // 12-character password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            try {
                // Get user email from the database
                $stmt = $con->prepare("SELECT email, first_name, last_name FROM users WHERE user_id = ? AND role='participant'");
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    $message = "No user found with the provided ID {$user_id}.";
                    logToFile($message);
                    return;
                }

                $user = $result->fetch_assoc();
                $email = $user['email'];
                $name = $user['first_name'] . ' ' . $user['last_name'];

                // Update the password in the database
                $updateStmt = $con->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $updateStmt->bind_param('si', $hashedPassword, $user_id);
                $updateStmt->execute();

                if ($updateStmt->affected_rows > 0) {
                    // Send the email
                    $subject = "Login Credentials for quiz competition";
                    $body = "Dear {$name},<br><br>Your login credentials are:<br>
                           Email: {$email}<br>
                           Password: {$newPassword}<br><br>
                           Please log in with the same.";
                    $altBody = "Dear {$name},\n\nYour login credentials are:\nEmail: {$email}\nPassword: {$newPassword}\n\nPlease log in with the same.";

                    if (!sendEmail($email, $name, $subject, $body, $altBody, $mail)) {
                        $message = "Failed to send email to {$email} (ID: {$user_id}).";
                        logToFile($message);
                    } else {
                        $message = "Email sent successfully to {$email} (ID: {$user_id}).";
                        logToFile($message);
                    }
                } else {
                    $message = "Failed to update the password for user ID {$user_id}.";
                    logToFile($message);
                }
            } catch (Exception $e) {
                $message = "An error occurred: " . $e->getMessage();
                logToFile($message);
            }
        }
        reset_pwd($con, $to, $mail);
    }
    $mail->smtpClose();
}
