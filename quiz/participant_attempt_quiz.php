<?php
require_once("./header.php");
require_once("./participant_protect.php");
$quiz_id;
$quiz;
$quiz_questions;
$participant_id;
$quiz_started = false;
$allow_attempt = true;
$attempt_reject_message = "";
$qz_submission_id = 0;
$duration = 0;
if (!isset($_SESSION['user'])) {
    header("Location: " . $Globals['domain'] . "/quiz");
}

if ($_SESSION['user']['disqualified'] == 1) {
    $allow_attempt = false;
    $attempt_reject_message = $_SESSION['user']['disqualification_reason'];
    header("Location: " . $Globals['domain'] . "/quiz/participant_reject_attempt.php?message=" . urlencode($attempt_reject_message));
    exit();
}
if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
    $_SESSION['quiz_id'] = $quiz_id;
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
        $total_possible_score = 0;
        $istmt->bind_param('iiis', $qnid, $participant_id, $quiz_id, $isa);

        $sql = "SELECT correct_option FROM questions WHERE question_id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $qnid);

        $score_sql = "SELECT COUNT(question_id) AS total_questions FROM questions WHERE quiz_id=?";
        $score_stmt = $con->prepare($score_sql);  // Correct variable name
        $score_stmt->bind_param('i', $quiz_id);   // Use quiz_id instead of qnid
        $score_stmt->execute();

        $score_result = $score_stmt->get_result();
        $score_row = $score_result->fetch_assoc();
        $total_possible_score = $score_row['total_questions'];
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
        $current_time_sbmt = new DateTime();
        $current_time_sbmt_formatted = $current_time_sbmt->format('Y-m-d H:i:s');
        $iqsql = "UPDATE quiz_submissions SET quiz_submission_time = ?, score = ? WHERE quiz_id=? AND participant_id=?";
        $iqstmt = $con->prepare($iqsql);
        $iqstmt->bind_param('siii', $current_time_sbmt_formatted, $score, $quiz_id, $participant_id);
        $iqstmt->execute();
        $con->commit();


        $redirect_url = $Globals['domain'] . "/quiz/participant_quiz_submitted.php";
        echo '
    <form id="redirectForm" action="' . $redirect_url . '" method="POST">
        <input type="hidden" name="score" value="' . $score . '">
        <input type="hidden" name="total_possible_score" value="' . $total_possible_score . '">
         <input type="hidden" name="quiz_id" value="' . $quiz_id . '">
    </form>
    <script type="text/javascript">
        // Automatically submit the form
        document.getElementById("redirectForm").submit();
    </script>
';
        exit();
    } catch (Exception $e) {
        $con->rollback();
        echo "Error: " . $e->getMessage();
        exit();
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
    header("Location: " . $location);
}
if ($quiz['start_time'] != null && $quiz['stop_time'] != null) {
    $quiz_started = true;
    $user_id = $_SESSION['user']['user_id'];
    $ini_chk_sql = "SELECT * FROM quiz_submissions WHERE participant_id = ? AND quiz_id = ?";
    $ini_chk_stmt = $con->prepare($ini_chk_sql);
    $ini_chk_stmt->bind_param('ii', $user_id, $quiz_id);
    $ini_chk_stmt->execute();

    $ini_chk_result = $ini_chk_stmt->get_result();

    if ($ini_chk_result->num_rows > 0) {
        $allow_attempt = false;
        $attempt_reject_message = 'Already Attempted the Quiz';
        $ini_chk_res = $ini_chk_result->fetch_assoc();
        if ($ini_chk_res['disqualified_submission'] == 0) {
            if ($ini_chk_res['quiz_submission_time'] == null) {
                $attempt_reject_message = "Exited during the quiz.";
                $dis_sql = "UPDATE quiz_submissions SET disqualified_submission = 1, disqualification_reason= ? WHERE quiz_submission_id = ?";
                $dis_stmt = $con->prepare($dis_sql);
                $dis_stmt->bind_param('si', $attempt_reject_message, $ini_chk_res['quiz_submission_id']);
                $dis_stmt->execute();
                header("Location: " . $Globals['domain'] . "/quiz/participant_reject_attempt.php?message=" . urlencode($attempt_reject_message));
                exit();
            } else {
                $attempt_reject_message = 'Already Attempted the Quiz';
                header("Location: " . $Globals['domain'] . "/quiz/participant_reject_attempt.php?message=" . urlencode($attempt_reject_message));
                exit();
            }
        } else if ($ini_chk_res['disqualified_submission'] == 1) {
            $attempt_reject_message = $ini_chk_res['disqualification_reason'];
            header("Location: " . $Globals['domain'] . "/quiz/participant_reject_attempt.php?message=" . urlencode($attempt_reject_message));
            exit();
        }
        header("Location: " . $Globals['domain'] . "/quiz/participant_reject_attempt.php?message=" . urlencode($attempt_reject_message));
        exit();
    }

    $current_time = new DateTime();
    $duration = $quiz['duration_in_minutes'];
    $stop_time = new DateTime($quiz['stop_time']);
    $start_time = new DateTime($quiz['start_time']);
    $calculated_duration = $stop_time->getTimestamp() - $current_time->getTimestamp();
    $calculated_duration = floor($calculated_duration / 60) - 8;
    $duration = min($calculated_duration, $quiz['duration_in_minutes']);
    if ($duration <= 5) {
        $allow_attempt = false;
        $attempt_reject_message = "Less than 5 minutes remaining in exam";
    }
    if (!$allow_attempt) {
        header("Location: " . $Globals['domain'] . "/quiz/participant_reject_attempt.php?message=" . urlencode($attempt_reject_message));
        exit();
    }

    $ini_sbmt_qz = "INSERT INTO quiz_submissions (quiz_id,participant_id,quiz_start_time) VALUES (?,?,?)";
    $ini_sbmt_stmt = $con->prepare($ini_sbmt_qz);
    $current_time_formatted = $current_time->format('Y-m-d H:i:s');
    $ini_sbmt_stmt->bind_param('iis', $quiz_id, $user_id, $current_time_formatted);
    $ini_sbmt_stmt->execute();
    $qz_submission_id = $con->insert_id;

    $sql = "SELECT * FROM questions WHERE quiz_id=? ORDER BY RAND()";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $quiz_questions = $stmt->get_result();
}
?>

<body>
    <style>
        header {
            justify-content: center;
        }

        #header-nav {
            display: none;
        }
    </style>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-form-section grid">
        <?php
        if ($quiz_started) {
        ?>
            <div class="quiz-form-container">
                <div class="form-container">
                    <h2><?php echo $quiz['quiz_name']; ?></h2>
                    <form class="quiz-form" method="post" id="quiz-form">
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
        <?php
        } else {
        ?>
            <div class="waiting-message-container">
                <h3>Quiz has not started yet.</h3>
                <h3>Please wait for Quiz Master's Instruction.</h3>
                <h3>NB: Any attempt at malpractice, including opening other applications during the quiz, will result in immediate disqualification.</h3>
                <h4 id="instruction_text"></h4>
                <button id="exam_btn" onclick="location.reload()">Load Quiz</button>
            </div>
        <?php
        }
        ?>
    </section>
    <?php
    if ($quiz_started) {
    ?>
        <div id="timer-container">
            <div id="timer" class="floating-timer"></div>
        </div>
    <?php
    }
    ?>
    <script src="./timer.js"></script>
    <script>
        <?php
        if ($quiz_started) {
        ?>
            window.onblur = function() {
                // Create a form dynamically
                let form = document.createElement("form");
                form.method = "POST";
                form.action = "<?php echo $Globals['domain'] ?>/quiz/participant_cheat_report.php";

                // Add a hidden input field for the 'cheated' parameter
                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "cheated";
                input.value = "1";
                form.appendChild(input);

                let rinput = document.createElement("input");
                rinput.type = "hidden";
                rinput.name = "cheat_reason";
                rinput.value = "Detected Potential attempt to access other application.";
                form.appendChild(rinput);

                // Append the form to the body and submit it
                document.body.appendChild(form);
                form.submit();
            };
            document.body.addEventListener("contextmenu", function(e) {
                e.preventDefault();
            });
            startTimer(<?php echo $duration ?>);
        <?php
        } else {
        ?>
            window.onblur = function() {
                window.close();
            };
            document.body.addEventListener("contextmenu", function(e) {
                e.preventDefault();
            });
        <?php
        }
        ?>
    </script>
</body>
<?php
require_once("./footer.php");
?>