<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once('connect.php');
session_start();

$current_filename = basename($_SERVER['SCRIPT_FILENAME']);
$Globals = [];
$query = "SELECT name, value FROM globals";
$result = $con->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $Globals[$row['name']] = $row['value'];
    }
}

// Error handling function
function handleError($errorMessage)
{
    global $Globals;
    error_log($errorMessage); // Log the error for debugging purposes
    header("Location: " . $Globals['domain'] . "/quiz/");
    exit; // Prevent further script execution
}

if (isset($_SESSION['user']) && $current_filename == 'index.php') {
    $location = $_SESSION['user']['role'] == 'admin' ? 'admin_dashboard.php' : 'participant_dashboard.php';
    header("Location: " . $location);
    exit;
}

if (!isset($_SESSION['user']) && $current_filename != 'index.php') {
    header("Location: index.php");
    exit;
}

require_once("./participant_protect.php");

$quiz_id = $_GET['qid'] ?? null;

if (!$quiz_id) {
    handleError("Quiz ID not provided.");
}

function addOrdinalNumberSuffix($num)
{
    if (!in_array(($num % 100), array(11, 12, 13))) {
        switch ($num % 10) {
            case 1:
                return $num . 'st';
            case 2:
                return $num . 'nd';
            case 3:
                return $num . 'rd';
        }
    }
    return $num . 'th';
}

// Ensure session user is available
if (!isset($_SESSION['user'])) {
    handleError("User session is not set. Please log in.");
}

$name = $_SESSION['user']['first_name'] . " " . $_SESSION['user']['last_name'];
$user_id = $_SESSION['user']['user_id'];

$sql = "SELECT YEAR(quiz_submission_time) AS quiz_year 
        FROM quiz_submissions 
        WHERE quiz_id = ? AND participant_id = ? LIMIT 1";

$stmt = $con->prepare($sql);
if (!$stmt) {
    handleError("Failed to prepare statement: " . $con->error);
}

$stmt->bind_param('ii', $quiz_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc();
    if ($quiz['quiz_year'] != null) {
        $akbn_death_year = 2023;
        $year = $quiz['quiz_year'];
        $anniversery_number = $year - $akbn_death_year;
        $anniversery_number_ordinal = addOrdinalNumberSuffix($anniversery_number);

        // Certificate generation
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="Certificate.png"');

        $templatePath = './assets/certificates/Lexathon_Certificate_Template.png';
        $fontPath = './assets/certificates/Arial.ttf';
        $boldFontPath = './assets/certificates/Arial_Bold.ttf';

        if (!file_exists($templatePath) || !file_exists($fontPath) || !file_exists($boldFontPath)) {
            handleError("Certificate template or font file is missing.");
        }

        $image = imagecreatefrompng($templatePath);

        $fontColor = imagecolorallocate($image, 155, 4, 4);
        $year_logo_color
            = imagecolorallocate($image, 6, 31, 106);
        $angle = 0;
        $nameFontSize = 70;
        $logo_year_size = 40;

        // Center alignment for name
        $imageWidth = imagesx($image);
        $nameBox = imagettfbbox($nameFontSize, $angle, $fontPath, $name);
        $nameWidth = $nameBox[2] - $nameBox[0];
        $nameX = ($imageWidth - $nameWidth) / 2;
        $nameY = 720;

        $yearX = 1050; // Adjust based on design
        $yearY = 160;

        imagettftext($image, $nameFontSize, $angle, $nameX, $nameY, $fontColor, $fontPath, $name);
        imagettftext($image, $logo_year_size, $angle, $yearX, $yearY, $year_logo_color, $boldFontPath, $year);

        imagepng($image);
        imagedestroy($image);
    } else {
        handleError("Quiz year not found for this submission.");
    }
} else {
    handleError("No quiz submission found for this user.");
}
