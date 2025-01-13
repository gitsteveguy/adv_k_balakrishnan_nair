<?php
require_once("./header.php");
require_once("./admin_protect.php");
$quiz_id;
$quiz;
$quiz_questions;
$participant_id;
$participant;
if (isset($_GET['qid']) && isset($_GET['uid'])) {
    $quiz_id = $_GET['qid'];
    $participant_id = $_GET['uid'];
    $psql = "SELECT first_name,last_name FROM users WHERE user_id = ?";
    $pstmt = $con->prepare($psql);
    $pstmt->bind_param('i', $participant_id);
    $pstmt->execute();
    $presult = $pstmt->get_result();
    $participant = $presult->fetch_assoc();
    $pstmt->close();
} else {
    $location = 'participant_dashboard.php';
}

try {
    $sql = "SELECT * FROM quizzes WHERE quiz_id=?";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        die("Prepare failed for quiz details: " . $con->error);
    }
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();
    $stmt->close(); // Close the statement

    $qz_sb_sql = "SELECT * FROM quiz_submissions WHERE quiz_id=? AND participant_id = ? LIMIT 1";
    $qz_sb_stmt = $con->prepare($qz_sb_sql);
    $qz_sb_stmt->bind_param('ii', $quiz_id, $participant_id);
    $qz_sb_stmt->execute();
    $qz_sb_stmt_res = $qz_sb_stmt->get_result();
    if ($qz_sb_stmt_res->num_rows < 1) {
        header("Location: " . $Globals['domain'] . '/quiz');
        exit();
    }
    $quiz_submission = $qz_sb_stmt_res->fetch_assoc();
    $qz_sb_stmt->close(); // Close the statement

    // Fetch quiz questions and submitted answers
    $qn_sb_sql = "
SELECT 
    q.question_id,
    q.quiz_id,
    q.question,
    q.option_a,
    q.option_b,
    q.option_c,
    q.option_d,
    q.correct_option,
    s.question_answer_submission_id,
    s.submitted_answer
FROM 
    questions q
LEFT JOIN 
    question_answer_submissions s
    ON q.question_id = s.question_id 
    AND s.quiz_id = q.quiz_id
    AND s.participant_id = ?
WHERE 
    q.quiz_id = ?
ORDER BY 
    q.question_id";
    $qn_sb_stmt = $con->prepare($qn_sb_sql);
    $qn_sb_stmt->bind_param('ii', $participant_id, $quiz_id);
    $qn_sb_stmt->execute();
    $quiz_questions = $qn_sb_stmt->get_result();
    $qn_sb_stmt->close(); // Close the statement


} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<body>
    <div class="heading-container">
        <h2>Quiz Result </h2>
        <a onclick="history.back()"><span class="material-symbols-rounded">
                arrow_back_ios
            </span> Back</a>
    </div>
    <section class="dashboard-form-section grid">
        <div class="quiz-form-container">
            <div class="form-container">
                <h2><?php echo $quiz['quiz_name']; ?></h2>
                <form class="quiz-form" method="post" id="quiz-form" inert>
                    <div class="quiz-attributes">
                        <h3><?php echo $participant['first_name'] . ' ' . $participant['last_name']; ?> scored : <?php echo $quiz_submission['score'] . ' / ' . $quiz['total_marks'] ?></h3>
                    </div>
                    <div class="questions-container">
                        <?php
                        $index = 1;
                        while ($quiz_question = $quiz_questions->fetch_assoc()) {
                            $is_correct = ($quiz_question['correct_option'] == $quiz_question['submitted_answer']);
                        ?>
                            <div class="question-block">
                                <div class="question-text-container">
                                    <h3><?php echo $index . ". " . $quiz_question['question'] ?></h3>
                                    <h3 class="<?php echo $is_correct ? 'correct' : 'wrong'; ?>"><?php echo $is_correct ? '+1 mark' : '0 mark' ?></h3>
                                </div>

                                <div class="question-options">
                                    <div class="option-container">
                                        <input type="hidden" value="<?php echo $quiz_question['question_id'] ?>" name="answers[<?php echo $index - 1 ?>][question_id]">
                                        <input type="radio" class="quiz-radio" value="a" name="answers[<?php echo $index - 1 ?>][participant_answer]" <?php echo ($quiz_question['submitted_answer'] == 'a') ? 'checked' : '' ?>>
                                        <label for="answers[<?php echo $index - 1 ?>][participant_answer]" class="<?php echo ($quiz_question['correct_option'] == 'a') ? 'correct' : '' ?>  <?php echo (!$is_correct && ($quiz_question['submitted_answer'] == 'a')) ? 'wrong' : '' ?>"><?php echo $quiz_question['option_a'] ?></label>
                                    </div>
                                    <div class=" option-container">
                                        <input type="radio" class="quiz-radio" value="b" name="answers[<?php echo $index - 1 ?>][participant_answer]" <?php echo ($quiz_question['submitted_answer'] == 'b') ? 'checked' : '' ?>>
                                        <label for="answers[<?php echo $index - 1 ?>][participant_answer]" class="<?php echo ($quiz_question['correct_option'] == 'b') ? 'correct' : '' ?>  <?php echo (!$is_correct && ($quiz_question['submitted_answer'] == 'b')) ? 'wrong' : '' ?>"><?php echo $quiz_question['option_b'] ?></label>
                                    </div>
                                    <div class=" option-container">
                                        <input type="radio" class="quiz-radio" value="c" name="answers[<?php echo $index - 1 ?>][participant_answer]" <?php echo ($quiz_question['submitted_answer'] == 'c') ? 'checked' : '' ?>>
                                        <label for="answers[<?php echo $index - 1 ?>][participant_answer]" class="<?php echo ($quiz_question['correct_option'] == 'c') ? 'correct' : '' ?> <?php echo (!$is_correct && ($quiz_question['submitted_answer'] == 'c')) ? 'wrong' : '' ?>"><?php echo $quiz_question['option_c'] ?></label>
                                    </div>
                                    <div class=" option-container">
                                        <input type="radio" class="quiz-radio" value="d" name="answers[<?php echo $index - 1 ?>][participant_answer]" <?php echo ($quiz_question['submitted_answer'] == 'd') ? 'checked' : '' ?>>
                                        <label for="answers[<?php echo $index - 1 ?>][participant_answer]" class="<?php echo ($quiz_question['correct_option'] == 'd') ? 'correct' : '' ?> <?php echo (!$is_correct && ($quiz_question['submitted_answer'] == 'd')) ? 'wrong' : '' ?>"><?php echo $quiz_question['option_d'] ?></label>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $index++;
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>
<?php
require_once("./footer.php");
?>