<?php
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors on the screen
ini_set('display_startup_errors', 1); // Display startup errors

require_once('connect.php');
session_start();
$current_filename = basename($_SERVER['SCRIPT_FILENAME']);
$Globals = [];
$query = "SELECT name, value FROM globals";
$result = $con->query($query);

if ($result->num_rows > 0) {
    // Fetch rows as an associative array
    while ($row = $result->fetch_assoc()) {
        $Globals[$row['name']] = $row['value'];
    }
}

if (isset($_SESSION['user']) && $current_filename == 'index.php') {
    $location = $_SESSION['user']['role'] == 'admin' ? 'admin_dashboard.php' : 'participant_dashboard.php';

    header("Location: " . $location);
}
if (!isset($_SESSION['user']) && !$current_filename == 'index.php') {
    header("Location: index.php");
}
require_once("./participant_protect.php");

$quiz_id;
$quiz;
if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
} else {
    header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
}
function addOrdinalNumberSuffix($num)
{
    if (!in_array(($num % 100), array(11, 12, 13))) {
        switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
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

// Dynamic content
$name = $_SESSION['user']['first_name'] . " " . $_SESSION['user']['last_name'];
$user_id = $_SESSION['user']['user_id'];
$sql = "SELECT 
        YEAR(quiz_submission_time) AS quiz_year
        FROM 
        quiz_submissions WHERE quiz_id = ? AND participant_id = ? LIMIT 1";
$stmt = $con->prepare($sql);
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

        // Set headers to download the file
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="Certificate.png"');

        // Load the certificate template
        $templatePath = './assets/certificates/Lexathon_Certificate_Template.png';
        $image = imagecreatefrompng($templatePath);

        // Set font properties
        $fontColor = imagecolorallocate($image, 0, 0, 0); // Black color (RGB)
        $nameFontSize = 5; // Font size (1-5 for built-in fonts)



        // Coordinates for text placement
        $nameX = 500;
        $nameY = 350;
        $yearX = 700;
        $yearY = 450;

        $anniversery_number_ordinalX = 800;
        $anniversery_number_ordinalY = 500;

        // Add text using built-in fonts
        imagestring($image, $nameFontSize, $nameX, $nameY, $name, $fontColor);
        imagestring($image, $nameFontSize, $yearX, $yearY, $year, $fontColor);
        imagestring($image, $nameFontSize, $anniversery_number_ordinalX, $anniversery_number_ordinalY, $anniversery_number_ordinal, $fontColor);

        // Output the image as a PNG
        imagepng($image);

        // Free memory
        imagedestroy($image);
    }
}
