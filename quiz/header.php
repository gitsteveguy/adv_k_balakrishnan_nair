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


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quiz - Adv. K. Balakrishnan Nair</title>

    <meta property="og:title" content="Adv. K. Balakrishnan Nair" />
    <meta
        property="og:description"
        content="In memory of the legacy of Adv. K. Balakrishnan Nair." />
    <meta property="og:url" content="https://advkbalakrishnannair.com" />
    <meta
        property="og:image"
        content="https://advkbalakrishnannair.com/gallery/seo_advkbn.jpg" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" />
    <link rel="manifest" href="/site.webmanifest" />

    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="quiz.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<header>
    <div class="hmigtext">
        <a href="index.html" class="hlink"><img src="../adv_bkn_logo.webp" alt="Logo" id="logo" /></a>
        <div>
            <h3>Adv. K. Balakrishnan Nair</h3>
            <h3>BA , LLB</h3>
        </div>
    </div>
    <div class="off-screen-menu">
        <div id="mob-close-btn" onclick="togglemenu()">
            <span id="mob-close-icon" class="material-symbols-rounded">close</span>
        </div>
        <menu>
            <li><a href="<?php echo $Globals['domain'] ?>">Home</a></li>
            <li><a href="<?php echo $Globals['domain'] ?>/organising_committee.html">About</a></li>
            <li><a class="active" href="<?php echo $Globals['domain'] ?>/quiz">Online Quiz</a></li>
            <li><a href="<?php echo $Globals['domain'] ?>/contact.html">Contact Us</a></li>
            <?php
            if (isset($_SESSION['user'])) {
            ?>
                <li><a href="logout.php">Logout</a></li>
            <?php
            }
            ?>
        </menu>
    </div>
    <nav>
        <div onclick="togglemenu()">
            <span id="menuicon" class="material-symbols-rounded">
                menu
            </span>
            <menu id="desktop-menu">
                <li><a href="<?php echo $Globals['domain'] ?>">Home</a></li>
                <li><a href="<?php echo $Globals['domain'] ?>/organising_committee.html">About</a></li>
                <li><a class="active" href="<?php echo $Globals['domain'] ?>/quiz">Online Quiz</a></li>
                <li><a href="<?php echo $Globals['domain'] ?>/contact.html">Contact Us</a></li>
                <?php
                if (isset($_SESSION['user'])) {
                ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php
                }
                ?>
            </menu>
    </nav>
</header>