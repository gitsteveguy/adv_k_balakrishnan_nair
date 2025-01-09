<?php
require_once("./header.php");
require_once("./admin_protect.php");
if (isset($_GET['dqnid']) && isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
    $question_id = $_GET['dqnid'];
    $sql = "DELETE FROM questions WHERE question_id=?";
    $del_stmt = $con->prepare($sql);
    $del_stmt->bind_param('i', $question_id);
    if ($del_stmt->execute()) {
        header("Location: " . $Globals['domain'] . "/quiz/admin_edit_quiz.php?qid=5");
    }
    $del_stmt->close();
} else {
    header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
}
