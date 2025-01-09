<?php
require_once("./header.php");
require_once("./participant_protect.php");
$quiz_id;
$quiz;
$quiz_questions;
$participant_id;
if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
} else {
    $location = 'participant_dashboard.php';
}
if (isset($_POST['submit_quiz'])) {
    $con->begin_transaction();
    try {
        $answers = $_POST['answers'];
        $isql = "INSERT INTO question_answer_submissions (question_id,participant_id,quiz_id,submitted_answer) VALUES(?,?,?,?)";
        $istmt = $con->prepare($isql);
        $score = 0;
        $istmt->bind_param('iiis', $qnid, $participant_id, $quiz_id, $isa);

        $sql = "SELECT correct_option FROM questions WHERE question_id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $qnid);

        foreach ($answers as $answer) {
            $qnid = $answer['question_id'];
            $participant_id = $_SESSION['user']['user_id'];
            $isa = $answer['participant_answer'];
            if (!$istmt->execute()) {
                // Capture and display the error message
                echo "Error: " . $istmt->error;
            }


            if (!$stmt->execute()) {
                // Capture and display the error message
                echo "Error: " . $stmt->error;
            }

            $ans = $stmt->get_result()->fetch_assoc();
            if ($answer['participant_answer'] == $ans['correct_option']) {
                $score++;
            }
        }
        $iqsql = "INSERT INTO quiz_submissions (quiz_id,participant_id,score) VALUES(?,?,?)";
        $iqstmt = $con->prepare($iqsql);
        $iqstmt->bind_param('iii', $quiz_id, $participant_id, $score);
        $iqstmt->execute();
        $con->commit();
        $qz_submission_id = $con->insert_id;

        header("Location: " . $Globals['domain'] . "/quiz/participant_quiz_submitted.php?qzsubid=" . $qz_submission_id);
    } catch (Exception $e) {
        $con->rollback();
        echo "Error: " . $e->getMessage();
    }
}

try {
    $sql = "SELECT * FROM quizzes WHERE quiz_id=? ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
if (!$quiz['allowed_entry']) {
    $location = 'participant_dashboard.php';
}
if ($quiz['start_time'] != null && $quiz['stop_time'] == null) {
    $sql = "SELECT * FROM questions WHERE quiz_id=? ORDER BY RAND()";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $quiz_questions = $stmt->get_result();
}
?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-form-section grid">
        <div class="quiz-form-container">
            <div class="form-container">
                <h2><?php echo $quiz['quiz_name']; ?></h2>
                <form class="quiz-form" method="post">
                    <div class="quiz-attributes">
                        <h3>Instructions : You are to be on zoom call while attempting the quiz.<br>The tab should not switched or minimized<br>Violation of the above rules will be met with disqualification</h3>
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
                    <input type="submit" value="Submit" name="submit_quiz">
                </form>
            </div>
        </div>
        </div>
    </section>
    <div id="timer-container">
        <div id="timer" class="floating-timer"></div>
    </div>
    <script src="./timer.js"></script>
    <script>
        startTimer(<?php echo $quiz['duration_in_minutes'] ?>);
    </script>
</body>
<?php
require_once("./footer.php");
?>