<?php
if ($_SESSION['user']['role'] == 'admin') {
    $location = 'admin_dashboard.php';
    header("Location: " . $location);
}
