<?php
require_once("./header.php");
require_once("./admin_protect.php");

// Define the number of records per page
$records_per_page = 10;

// Get the current page number (default to 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_POST['search']) ? $_POST['search'] : '';

// SQL query to fetch participants with search
// Define the base query with placeholders
$sql = "SELECT * FROM users 
        WHERE role = ? 
        AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR college LIKE ?) 
        LIMIT ?, ?";

// Prepare the statement
$stmt = $con->prepare($sql);

// Add wildcards to the search term
$search_term = "%$search%";

// Bind parameters to the statement
$stmt->bind_param('sssssii', $role, $search_term, $search_term, $search_term, $search_term, $start_from, $records_per_page);

// Define the values for the placeholders
$role = 'participant';

// Execute the prepared statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Query to get the total number of participants for pagination
// Define the query with placeholders
$total_sql = "SELECT COUNT(*) AS total 
              FROM users 
              WHERE role = ? 
              AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR college LIKE ?)";

// Prepare the statement
$total_stmt = $con->prepare($total_sql);

// Add wildcards to the search term
$search_term = "%$search%";

// Bind parameters to the prepared statement
$total_stmt->bind_param('sssss', $role, $search_term, $search_term, $search_term, $search_term);

// Define the value for the role
$role = 'participant';

// Execute the prepared statement
$total_stmt->execute();

// Get the result
$total_result = $total_stmt->get_result();

// Fetch the total count
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-home-section grid participant-section">
        <h3>Participants</h3>
        <form method="post" class="search-form">
            <input type="text" name="search" placeholder="Search by Name, Email, college or city" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" value="Search"><span class="material-symbols-rounded">
                    search
                </span></button>
        </form>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone Number</th>
                        <th>College</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>Pincode</th>
                        <th>Graduation Year</th>
                        <th>College/University Admission No.</th>
                        <th>Year of Joining</th>
                        <th>Programme</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are any results
                    if ($result->num_rows > 0) {
                        // Loop through and display the data
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['phone_no']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['college']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['city']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['state']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['pincode']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['graduation_year']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['ini_col_code']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['year_of_joining']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['programme']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                    ?>
                            <td><a class="hlink" href="<?php echo $Globals['domain'] ?>/quiz/admin_edit_participant.php?pid=<?php echo $row['user_id'] ?>" class="hlink">
                                    <div class="tbl-icon-container">
                                        <span class="material-symbols-rounded">
                                            edit
                                        </span>
                                    </div>
                                </a>
                            </td>
                    <?php
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='14'>No participants found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="pagination">
            <?php
            // Previous page link
            if ($page > 1) {
                echo "<a href='?page=" . ($page - 1) . "&search=" . urlencode($search) . "'>Previous</a>";
            }

            // Page numbers
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<span id='active-page'><strong>$i</strong></span>";
                } else {
                    echo "<a href='?page=$i&search=" . urlencode($search) . "'>$i</a>";
                }
            }

            // Next page link
            if ($page < $total_pages) {
                echo "<a href='?page=" . ($page + 1) . "&search=" . urlencode($search) . "'>Next</a>";
            }
            ?>
    </section>


</body>
<?php
require_once("./footer.php");
?>