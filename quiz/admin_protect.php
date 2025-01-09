<?php
if ($_SESSION['user']['role'] == 'participant') {
    $location = 'participant_dashboard.php';
    header("Location: " . $location);
}
?>