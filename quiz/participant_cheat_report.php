<?php
require_once("./header.php");
require_once("./participant_protect.php");

$cheat = 0;
$message;
$user_id;
$quiz_id;
if (isset($_POST['cheated']) && isset($_POST['cheat_reason']) && isset($_SESSION['quiz_id'])) {
    if ($_POST['cheated'] == 1) {
        $user_id = $_SESSION['user']['user_id'];
        $quiz_id = $_SESSION['quiz_id'];
        $cheat = $_POST['cheated'];
        $message = $_POST['cheat_reason'];
        $sql = 'UPDATE quiz_submissions SET disqualified_submission = 1, disqualification_reason = ? WHERE participant_id = ? AND quiz_id = ?';
        $stmt = $con->prepare($sql);
        $stmt->bind_param('sii', $message, $user_id, $quiz_id);
        $stmt->execute();
        $_SESSION['user']['disqualified'] = 1;
        $_SESSION['user']['disqualification_reason'] = $message;
    } else {
        header("Location: " . $Globals['domain'] . "/quiz");
    }
} else {
    header("Location: " . $Globals['domain'] . "/quiz");
}
?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-home-section grid" style="display: flex; flex-direction:column; align-items:center;">
        <h2>Attempt Rejected</h2>
        <h2>Reason : <?php echo $message; ?></h2>
        <button onclick="window.close()">Close Quiz</button>
    </section>


</body>
<?php
require_once("./footer.php");
?>