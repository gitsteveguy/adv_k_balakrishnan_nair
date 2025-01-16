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
      <style>
        .pwd-container {
          display: flex;
          position: relative;
        }

        #togglePassword {
          position: absolute;
          padding: 0.25rem;
          right: 0;
          top: 9px;
          background: transparent;
          border: none
        }

        #togglePassword:hover {
          border: none !important;
          outline: none;
        }

        #pwdicon {
          color: var(--primary-color);
        }
      </style>
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
          <div class="pwd-container">
            <input type="password" id="password" name="password" placeholder="Enter Your Password" required>
            <button type="button" id="togglePassword"><span class="material-symbols-rounded" id="pwdicon">
                visibility
              </span></button>
          </div>
          <input type="submit" value="Login" name="Login">
        </form>
      </div>
    </div>
  </section>
  <script>
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');

    togglePasswordButton.addEventListener('click', () => {
      // Toggle the type attribute
      let itype = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = itype;

      // Update button text
      pwdicon.innerText = passwordInput.type === 'password' ? 'visibility' : 'visibility_off';
    });
  </script>


</body>
<?php
require_once("./footer.php");
?>