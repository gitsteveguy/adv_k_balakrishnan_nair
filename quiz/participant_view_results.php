<?php
require_once("./header.php");
require_once("./participant_protect.php");
$participant_id = $_SESSION['user']['user_id'];
$sql = "SELECT 
    q.quiz_id,
    q.quiz_name,
    q.total_marks,
    qs.score
FROM 
    quizzes q
JOIN 
    quiz_submissions qs
    ON q.quiz_id = qs.quiz_id
WHERE 
    qs.participant_id = ? 
    AND qs.quiz_submission_time IS NOT NULL";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $participant_id);
$stmt->execute();
$quizzes = $stmt->get_result();
$stmt->close();
?>

<body>
    <div class="heading-container">
        <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
        <a onclick="history.back()"><span class="material-symbols-rounded">
                arrow_back_ios
            </span> Back</a>
        <h3>View Results</h3>
    </div>
    <section class="dashboard-home-section grid">
        <?php
        while ($quiz = $quizzes->fetch_assoc()) {
        ?>
            <div class="card btn">
                <h2><?php echo $quiz['quiz_name'] ?></h2>
                <h3>Score : <?php echo $quiz['score'] ?> / <?php echo $quiz['total_marks'] ?></h3>
                <a href="<?php echo $Globals['domain'] . "/quiz/participant_view_quiz_result.php?qid=" . $quiz['quiz_id'] ?>">View Result</a>
                <a href="<?php echo $Globals['domain'] . "/quiz//participant_download_certificate.php?qid=" . $quiz['quiz_id'] ?>">Download Participation Certificate</a>
            </div>
        <?php
        }
        ?>
    </section>


</body>
<?php
require_once("./footer.php");
?>