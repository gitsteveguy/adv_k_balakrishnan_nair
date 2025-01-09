<?php
require_once("./header.php");
require_once("./participant_protect.php");

$sql = "SELECT * FROM quizzes 
        WHERE allowed_entry = 1";

$stmt = $con->prepare($sql);

$stmt->execute();

$result = $stmt->get_result();

?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <h3>Participants</h3>
    <section class="dashboard-home-section grid">
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Loop through and display the data
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="card btn" onclick="window.location.href='<?php echo $Globals['domain'] ?>/quiz/participant_attempt_quiz.php?qid=<?php echo $row['quiz_id'] ?>'">
                    <h2><?php echo $row['quiz_name'] ?></h2>
                </div>
        <?php
            }
        }
        ?>
        </div>
    </section>


</body>
<?php
require_once("./footer.php");
?>