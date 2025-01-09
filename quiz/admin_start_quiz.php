<?php
require_once("./header.php");
require_once("./admin_protect.php");

if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid']; // Fetch the quiz ID from the GET parameter

    // Correct SQL query with valid syntax
    $sql = "UPDATE quizzes SET is_running=1, start_time=NOW() WHERE quiz_id=?";
    $start_stmt = $con->prepare($sql);

    if (!$start_stmt) {
        // Debugging the prepare statement error
        echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        exit;
    }

    // Bind the correct variable
    $start_stmt->bind_param('i', $quiz_id);

    if ($start_stmt->execute()) {
        // Redirect on success
        header("Location: " . $Globals['domain'] . "/quiz/admin_view_quiz.php");
        exit;
    } else {
        // Debugging the execution error
        echo "Execute failed: (" . $start_stmt->errno . ") " . $start_stmt->error;
    }

    $start_stmt->close();
} else {
    // Redirect if 'qid' is not set
    header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
    exit;
}
