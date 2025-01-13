<?php
require_once("./header.php");
require_once("./participant_protect.php");

$user_id = $_SESSION['user']['user_id'];
$current_time = (new DateTime())->format('Y-m-d H:i:s');
$sql = "
    SELECT 
        q.quiz_id, 
        q.quiz_name, 
        IF(qs.quiz_submission_time IS NOT NULL, TRUE, FALSE) AS is_submitted,
        qs.quiz_start_time,qs.quiz_submission_time,qs.disqualified_submission,qs.disqualification_reason
    FROM 
        quizzes q
    LEFT JOIN 
        quiz_submissions qs ON q.quiz_id = qs.quiz_id AND qs.participant_id = $user_id 
    WHERE 
        q.allowed_entry = 1 AND (q.stop_time IS NULL OR '$current_time' < q.stop_time)
";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();

?>

<body>
    <div class="heading-container">
        <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
        <a onclick="history.back()"><span class="material-symbols-rounded">
                arrow_back_ios
            </span> Back</a>
        <h3>Available Quizes</h3>
    </div>

    <section class="dashboard-home-section grid">
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Loop through and display the data
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="card btn <?php echo $row['is_submitted'] || $row['disqualified_submission'] ? 'greyscale' : ''; ?>"
                    <?php if (!$row['is_submitted'] && !$row['disqualified_submission']) { ?>
                    onclick="openQuiz('<?php echo $Globals['domain'] . '/quiz/participant_attempt_quiz.php?qid=' . $row['quiz_id']; ?>')"
                    <?php } ?>>
                    <h2><?php echo htmlspecialchars($row['quiz_name']); ?></h2>
                    <?php if ($row['is_submitted']) { ?>
                        <h3>Attempted</h3>
                        <a href="<?php echo $Globals['domain'] . "/quiz/participant_view_quiz_result.php?qid=" . $row['quiz_id']; ?>">View Result</a>
                    <?php } else if ($row['disqualified_submission']) {
                    ?>
                        <h3>Disqualified</h3>
                        <h4>Reason : <?php echo $row['disqualification_reason']; ?></h4>
                    <?php
                    }
                    ?>
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
    <script>
        var myWindow;

        function openQuiz(url) {
            const screenWidth = screen.width;
            const screenHeight = screen.height;

            // Open a new window with specified dimensions
            const quizWindow = window.open(
                url,
                "quizWindow",
                `width=${screenWidth},height=${screenHeight},left=0,top=0`
            );

            // Ensure the window stays fullscreen by listening to resize events
            if (quizWindow) {
                quizWindow.addEventListener("resize", function() {

                    quizWindow.resizeTo(screenWidth, screenHeight);
                    quizWindow.moveTo(0, 0);
                });

                // Reposition the window if it is moved
                var oldX = quizWindow.screenX,
                    oldY = quizWindow.screenY;

                var interval = setInterval(function() {
                    if (oldX != quizWindow.screenX || oldY != quizWindow.screenY) {
                        quizWindow.moveTo(0, 0);

                    } else {

                    }

                    oldX = quizWindow.screenX;
                    oldY = quizWindow.screenY;
                }, 200);

                // Ensure the window is immediately resized and repositioned
                quizWindow.resizeTo(screenWidth, screenHeight);
                quizWindow.moveTo(0, 0);

                // Disable right-click within the quiz window
            } else {
                alert("Popup blocked! Please allow popups for this site to proceed.");
            }
        }
    </script>

</body>
<?php
require_once("./footer.php");
?>