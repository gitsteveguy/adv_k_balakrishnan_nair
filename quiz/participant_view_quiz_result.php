<?php
require_once("./header.php");
require_once("./participant_protect.php");
$quiz_id;
$quiz;
$quiz_questions;
$participant_id;
if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
    $participant_id = $_SESSION['user']['user_id'];
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

    // Fetch quiz questions
    $qn_sql = "SELECT * FROM questions WHERE quiz_id=? ORDER BY question_id";
    $qn_stmt = $con->prepare($qn_sql);
    $qn_stmt->bind_param('i', $quiz_id);
    $qn_stmt->execute();
    $quiz_questions = $qn_stmt->get_result();
    $qn_stmt->close(); // Close the statement

    $qn_sb_sql = "SELECT * FROM quiz_submissions WHERE quiz_id=? AND participant_id = ? LIMIT 1";
    $qn_sb_stmt = $con->prepare($qn_sb_sql);
    $qn_sb_stmt->bind_param('i', $quiz_id);
    $qn_sb_stmt->execute();
    $quiz_submission = $qn_sb_stmt->get_result()->fetch_assoc();
    $qn_sb_stmt->close(); // Close the statement

    // Fetch submitted answers
    $subm_sql = "SELECT * FROM question_answer_submissions 
             WHERE quiz_id = ? AND participant_id = ? 
             ORDER BY question_id";
    $subm_stmt = $con->prepare($subm_sql);

    if (!$subm_stmt) {
        die("Prepare failed for submissions: " . $con->error);
    }
    $subm_stmt->bind_param('ii', $quiz_id, $participant_id);
    $subm_stmt->execute();
    $subm_answers = $subm_stmt->get_result();
    $subm_stmt->close(); // Close the statement
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<body>
    <h2>Quiz Result </h2>
    <section class="dashboard-form-section grid">
        <div class="quiz-form-container">
            <div class="form-container">
                <h2><?php echo $quiz['quiz_name']; ?></h2>
                <form class="quiz-form" method="post" id="quiz-form" inert>
                    <div class="quiz-attributes">
                        <h3>Your scored <?php echo $quiz['score'] . ' / ' . $quiz['total_marks'] ?></h3>
                    </div>
                    <div class="questions-container">
                        <?php
                        $index = 1;
                        while ($quiz_question = $quiz_questions->fetch_assoc()) {
                        ?>
                            <div class="question-block">
                                <h3><?php echo $index . ". " . $quiz_question['question'] ?></h3>
                                <div class="question-options">
                                    <div class="option-container">
                                        <input type="hidden" value="<?php echo $quiz_question['question_id'] ?>" name="answers[<?php echo $index - 1 ?>][question_id]">
                                        <input type="radio" class="quiz-radio" value="no answer" checked style="display: none;" name="answers[<?php echo $index - 1 ?>][participant_answer]">
                                        <input type="radio" class="quiz-radio" value="a" name="answers[<?php echo $index - 1 ?>][participant_answer]">
                                        <label for=" aopt"><?php echo $quiz_question['option_a'] ?></label>
                                    </div>
                                    <div class="option-container">
                                        <input type="radio" class="quiz-radio" value="b" name="answers[<?php echo $index - 1 ?>][participant_answer]">
                                        <label for="aopt"><?php echo $quiz_question['option_b'] ?></label>
                                    </div>
                                    <div class="option-container">
                                        <input type="radio" class="quiz-radio" value="c" name="answers[<?php echo $index - 1 ?>][participant_answer]">
                                        <label for="aopt"><?php echo $quiz_question['option_c'] ?></label>
                                    </div>
                                    <div class="option-container">
                                        <input type="radio" class="quiz-radio" value="d" name="answers[<?php echo $index - 1 ?>][participant_answer]">
                                        <label for="aopt"><?php echo $quiz_question['option_d'] ?></label>
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