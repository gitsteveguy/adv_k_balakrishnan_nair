<?php
require_once("./header.php");
require_once("./admin_protect.php");

if (isset($_GET['qid'])) {
    $quiz_id = intval($_GET['qid']); // Fetch the quiz ID from the GET parameter and ensure it's an integer

    // Fetch the quiz details to get duration_in_minutes
    $sql_fetch = "SELECT duration_in_minutes FROM quizzes WHERE quiz_id = ?";
    $fetch_stmt = $con->prepare($sql_fetch);

    if (!$fetch_stmt) {
        echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        exit;
    }

    $fetch_stmt->bind_param('i', $quiz_id);
    $fetch_stmt->execute();
    $fetch_result = $fetch_stmt->get_result();
    $quiz = $fetch_result->fetch_assoc();
    $fetch_stmt->close();

    if (!$quiz) {
        echo "Quiz not found!";
        exit;
    }

    $duration_in_minutes = $quiz['duration_in_minutes'];

    // Calculate start_time and stop_time
    $current_time = new DateTime(); // PHP equivalent of NOW()
    $stop_time = clone $current_time;
    $stop_time->modify('+' . ($duration_in_minutes + 10) . ' minutes');

    // Format times for MySQL
    $start_time_formatted = $current_time->format('Y-m-d H:i:s');
    $stop_time_formatted = $stop_time->format('Y-m-d H:i:s');

    // Update the quiz with the new times
    $sql_update = "UPDATE quizzes 
                   SET 
                       start_time = ?, 
                       stop_time = ? 
                   WHERE quiz_id = ?";
    $update_stmt = $con->prepare($sql_update);

    if (!$update_stmt) {
        echo "Prepare failed: (" . $con->errno . ") " . $con->error;
        exit;
    }

    $update_stmt->bind_param('ssi', $start_time_formatted, $stop_time_formatted, $quiz_id);

    if ($update_stmt->execute()) {
        // Redirect on success
        header("Location: " . $Globals['domain'] . "/quiz/admin_view_quiz.php");
        exit;
    } else {
        // Debugging the execution error
        echo "Execute failed: (" . $update_stmt->errno . ") " . $update_stmt->error;
    }

    $update_stmt->close();
} else {
    // Redirect if 'qid' is not set
    header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
    exit;
}
