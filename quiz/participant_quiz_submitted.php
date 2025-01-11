<?php
require_once("./header.php");
require_once("./participant_protect.php");
if (isset($_POST['score']) && isset($_POST['total_possible_score']) && isset($_POST['quiz_id'])) {
    $score = $_POST['score'];
    $total_possible_score = $_POST['total_possible_score'];
    $quiz_id = $_POST['quiz_id'];
} else {
    header("Location: " . $Globals['domain'] . "/quiz");
}
?>

<body>
    <h2>Quiz Submitted Successfully</h2>
    <section class="quiz_submission_result_container">
        <div class="white-card">
            <h3>Your Score : <?php echo $score ?></h3>
            <h3>Total Score : <?php echo $total_possible_score ?></h3>
        </div>
        <a href="<?php echo $Globals['domain'] ?>/quiz/participant_view_quiz_result.php?qid=" <?php echo $quiz_id ?>>View Detailed Quiz Result</a>
        <a href="<?php echo $Globals['domain'] ?>/quiz" <?php echo $quiz_id ?>>Back to Dashboard</a>
    </section>

    <?php
    require_once("./footer.php");
    ?>