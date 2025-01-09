<?php
require_once("./header.php");
require_once("./participant_protect.php");
?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-home-section grid">
        <div class="card btn" onclick="window.location.href='<?php echo $Globals['domain'] ?>/quiz/participant_view_quiz.php'">
            <h2>Quizes</h2>
        </div>
        <div class="card btn">
            <h2>View Results</h2>
        </div>
    </section>


</body>
<?php
require_once("./footer.php");
?>