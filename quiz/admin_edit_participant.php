<?php
require_once("./header.php");
require_once("./admin_protect.php");

$participant = null;
$pid = null;

if (isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $stmt = $con->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
    $stmt->bind_param('i', $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows < 1) {
        header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
    }
    $participant = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
}

if (isset($_POST['update_participant'])) {
    $email = $_POST['email'];
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

    // Check if password is being updated
    if (isset($_POST['password']) && trim($_POST['password']) != '') {
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $con->prepare("UPDATE users 
            SET email = ?, 
                password = ?, 
                first_name = ?, 
                last_name = ?, 
                phone_no = ?, 
                college = ?, 
                city = ?, 
                state = ?, 
                country = ?, 
                pincode = ?, 
                graduation_year = ?, 
                ini_col_code = ?, 
                year_of_joining = ?, 
                programme = ? 
            WHERE user_id = ?");
        $stmt->bind_param("ssssssssssssssi", $email, $hashed_password, $first_name, $last_name, $phone_no, $college, $city, $state, $country, $pincode, $graduation_year, $ini_col_code, $year_of_joining, $programme, $pid);
    } else {
        print_r($_POST);
        $stmt = $con->prepare("UPDATE users 
         SET email = ?, 
             first_name = ?, 
             last_name = ?, 
             phone_no = ?, 
             college = ?, 
             city = ?, 
             state = ?, 
             country = ?, 
             pincode = ?, 
             graduation_year = ?, 
             ini_col_code = ?, 
             year_of_joining = ?, 
             programme = ? 
         WHERE user_id = ?");
        $stmt->bind_param("sssssssssssssi", $email, $first_name, $last_name, $phone_no, $college, $city, $state, $country, $pincode, $graduation_year, $ini_col_code, $year_of_joining, $programme, $pid);
    }

    // Execute the query
    if ($stmt->execute()) {
        header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php?status=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-form-section grid">
        <div class="form-border-container">
            <div class="form-container">
                <h2>Edit Participant</h2>
                <form id="add-participant-form" method="POST">
                    <div class="grid">
                        <input type="email" maxlength="50" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($participant['email']); ?>">
                        <input type="text" maxlength="50" name="password" placeholder="Password">
                        <input type="text" maxlength="100" name="first_name" placeholder="First Name" required value="<?php echo htmlspecialchars($participant['first_name']); ?>">
                        <input type="text" maxlength="50" name="last_name" placeholder="Last Name" required value="<?php echo htmlspecialchars($participant['last_name']); ?>">
                        <input type="tel" maxlength="15" name="phone_no" placeholder="Phone no" required value="<?php echo htmlspecialchars($participant['phone_no']); ?>">
                        <input type="text" maxlength="100" name="college" placeholder="College Name" required value="<?php echo htmlspecialchars($participant['college']); ?>">
                        <input type="text" maxlength="100" name="city" placeholder=" City" required value="<?php echo htmlspecialchars($participant['city']); ?>">
                        <input type="text" maxlength="100" name="state" placeholder="State" required value="<?php echo htmlspecialchars($participant['state']); ?>">
                        <input type="text" maxlength="100" name="country" placeholder="Country" required value="<?php echo htmlspecialchars($participant['country']); ?>">
                        <input type="text" maxlength="100" name="pincode" placeholder="Postal Code" required value="<?php echo htmlspecialchars($participant['pincode']); ?>">
                        <input type="tel" maxlength="4" name="graduation_year" placeholder="Graduation Year (YYYY)" required value="<?php echo htmlspecialchars($participant['graduation_year']); ?>">
                        <input type="text" maxlength="50" name="ini_col_code" placeholder="College or University Admission no:" required value="<?php echo htmlspecialchars($participant['ini_col_code']); ?>">
                        <input type="tel" maxlength="4" name="year_of_joining" placeholder="Year of Joining Programme (YYYY)" required value="<?php echo htmlspecialchars($participant['year_of_joining']); ?>">
                        <input type="text" maxlength="100" name="programme" placeholder="Programme" required value="<?php echo htmlspecialchars($participant['programme']); ?>">
                    </div>
                    <input type="submit" value="Update Participant" name="update_participant">
                </form>
            </div>
        </div>
    </section>
</body>
<?php
require_once("./footer.php");
?>