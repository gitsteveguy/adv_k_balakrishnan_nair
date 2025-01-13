<?php
require_once("./header.php");
require_once("./participant_protect.php");

$message;
if (isset($_GET['message'])) {
    $message = $_GET['message'];
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