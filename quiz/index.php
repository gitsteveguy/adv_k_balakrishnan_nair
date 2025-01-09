<?php
require_once("./header.php");

$invalid_login = false;
if (isset($_POST['Login'])) {


  $email = $con->real_escape_string($_POST['email']);
  $password = $_POST['password'];

  $query = "SELECT * FROM users WHERE email = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

      // Login success
      session_regenerate_id(true);
      $_SESSION['user'] = $user;
      $location = $_SESSION['user']['role'] == 'admin' ? 'admin_dashboard.php' : 'participant_dashboard.php';
      header("Location: " . $location);
      exit();
    } else {
      $invalid_login = true;
    }
  } else {
    $invalid_login = true;
  }

  // Close the statement and connection
  $stmt->close();
}
?>

<body>
  <section class="quiz-launch">
    <div class="form-border-container">
      <div class="form-container">
        <h2>Quiz Login</h2>
        <?php
        if ($invalid_login) {
        ?>
          <h4>Invalid Email or Password</h4>
        <?php
        }
        ?>
        <form id="login-form" method="post">
          <input type="email" name="email" placeholder="Enter your Email" required>
          <input type="password" name="password" placeholder="Enter Your Password" required>
          <input type="submit" value="Login" name="Login">
        </form>
      </div>
    </div>
  </section>


</body>
<?php
require_once("./footer.php");
?>