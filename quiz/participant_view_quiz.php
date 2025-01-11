<?php
require_once("./header.php");
require_once("./participant_protect.php");

$user_id = $_SESSION['user']['user_id'];
$sql = "
    SELECT 
        q.quiz_id, 
        q.quiz_name, 
        IF(qs.quiz_id IS NOT NULL, TRUE, FALSE) AS is_submitted
    FROM 
        quizzes q
    LEFT JOIN 
        quiz_submissions qs ON q.quiz_id = qs.quiz_id AND qs.participant_id = $user_id
    WHERE 
        q.allowed_entry = 1
";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();

?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <h3>Available Quizes</h3>
    <section class="dashboard-home-section grid">
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Loop through and display the data
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="card btn <?php echo $row['is_submitted'] ? 'greyscale' : ''; ?>"
                    <?php if (!$row['is_submitted']) { ?>
                    onclick="window.location.href='<?php echo $Globals['domain'] . "/quiz/participant_attempt_quiz.php?qid=" . $row['quiz_id']; ?>'"
                    <?php } ?>>
                    <h2><?php echo htmlspecialchars($row['quiz_name']); ?></h2>
                    <?php if ($row['is_submitted']) { ?>
                        <h3>Attempted</h3>
                        <a href="<?php echo $Globals['domain'] . "/quiz/participant_view_quiz_result.php?qid=" . $row['quiz_id']; ?>">View Result</a>
                    <?php } ?>
                </div>
            <?php
            }
        } else {
            ?>
            <h3>No Quizzes are currently running</h3>
        <?php
        }
        ?>
        </div>
    </section>


</body>
<?php
require_once("./footer.php");
?>