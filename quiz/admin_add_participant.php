<?php
require_once("./header.php");
require_once("./admin_protect.php");
if (isset($_POST['add_participant'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_no = $_POST['phone_no'];
    $college = $_POST['college'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $pincode = $_POST['pincode'];
    $graduation_year = $_POST['graduation_year'];
    $ini_col_code = $_POST['ini_col_code'];
    $year_of_joining = $_POST['year_of_joining'];
    $programme = $_POST['programme'];

    // Hash the password (to store it securely)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement with placeholders
    $stmt = $con->prepare("INSERT INTO users (email, password, first_name, last_name, phone_no, college, city, state, country, pincode, graduation_year, ini_col_code, year_of_joining, programme, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters to the prepared statement
    $role = 'participant';  // The role is hardcoded as 'participant'
    $stmt->bind_param("sssssssssssssss", $email, $hashed_password, $first_name, $last_name, $phone_no, $college, $city, $state, $country, $pincode, $graduation_year, $ini_col_code, $year_of_joining, $programme, $role);

    // Execute the prepared statement
    if ($stmt->execute()) {
        header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php?status=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}
?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-form-section grid">
        <div class="form-border-container">
            <div class="form-container">
                <h2>Create Participant</h2>
                <form id="add-participant-form" method="post">
                    <div class="grid">
                        <input type="email" maxlength="50" name="email" placeholder="Email" required>
                        <input type="text" maxlength="50" name="password" placeholder="Password" required>
                        <input type="text" maxlength="100" name="first_name" placeholder="First Name" required>
                        <input type="text" maxlength="50" name="last_name" placeholder="Last Name" required>
                        <input type="tel" maxlength="15" name="phone_no" placeholder="Phone no" required>
                        <input type="text" maxlength="100" name="college" placeholder="College Name" required>
                        <input type="text" maxlength="100" name="city" placeholder=" City" required>
                        <input type="text" maxlength="100" name="state" placeholder="State" required>
                        <input type="text" maxlength="100" name="country" placeholder="Country" required>
                        <input type="text" maxlength="100" name="pincode" placeholder="Postal Code" required>
                        <input type="tel" maxlength="4" name="graduation_year" placeholder="Graduation Year (YYYY)" required>
                        <input type="text" maxlength="50" name="ini_col_code" placeholder="College or University Admission no:" required>
                        <input type="tel" maxlength="4" name="year_of_joining" placeholder="Year of Joining Programme (YYYY)" required>
                        <input type="text" maxlength="100" name="programme" placeholder="Programme" required>
                    </div>
                    <input type="submit" value="Create Participant" name="add_participant">
                </form>
            </div>
        </div>
        </div>
    </section>

</body>
<?php
require_once("./footer.php");
?>