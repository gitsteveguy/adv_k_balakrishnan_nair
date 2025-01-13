<?php
require_once("./header.php");
require_once("./admin_protect.php");

$quiz_id;
if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
}
// Define the number of records per page
$records_per_page = 10;

// Get the current page number (default to 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_POST['search']) ? $_POST['search'] : '';

// SQL query to fetch participants with search
// Define the base query with placeholders
$sql = "SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            q.total_marks,
            qs.score,
            TIMESTAMPDIFF(SECOND, qs.quiz_start_time, qs.quiz_submission_time) AS time_taken
        FROM 
            users u
        JOIN 
            quiz_submissions qs ON u.user_id = qs.participant_id
        JOIN 
            quizzes q ON qs.quiz_id = q.quiz_id
        WHERE 
            qs.quiz_id = ?
            AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.user_id = ?)
        ORDER BY 
            qs.score DESC, time_taken ASC
        LIMIT ?, ?";

// Prepare the statement
$stmt = $con->prepare($sql);

// Add wildcards to the search term
$search_term = "%$search%";

// Bind parameters to the statement
$stmt->bind_param('issiii', $quiz_id, $search_term, $search_term, $search_term, $start_from, $records_per_page);

// Execute the prepared statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();



// Fetch the total count
$total_records = $result->num_rows;
$total_pages = ceil($total_records / $records_per_page);
?>

<body>
    <div class="heading-container">
        <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
        <a onclick="history.back()"><span class="material-symbols-rounded">
                arrow_back_ios
            </span> Back</a>
    </div>
    <section class="dashboard-home-section grid participant-section">
        <h3>Quizzes</h3>
        <form method="post" class="search-form">
            <input type="text" name="search" placeholder="Search by First Name, Last Name, User ID" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" value="Search"><span class="material-symbols-rounded">
                    search
                </span></button>
        </form>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Score</th>
                        <th>Total Marks</th>
                        <th>Time Taken to Finish (mm:ss)</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <div>
                    <?php
                    // Check if there are any results
                    if ($result->num_rows > 0) {
                        // Loop through and display the data
                        while ($row = $result->fetch_assoc()) {
                            // Convert time_to_finish (in seconds) into minutes and seconds
                            $time_in_seconds = $row['time_taken'];
                            $minutes = floor($time_in_seconds / 60);
                            $seconds = $time_in_seconds % 60;

                            // Format the time as mm:ss
                            $formatted_time = sprintf("%02d:%02d", $minutes, $seconds);
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['score']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['total_marks']) . "</td>";
                            echo "<td>" . htmlspecialchars($formatted_time) . "</td>";
                            echo '<td>';
                            echo '<div class="action-col-div">';
                    ?>
                            <a class="hlink" href="<?php echo $Globals['domain'] ?>/quiz/admin_view_quiz_result.php?qid=<?php echo $quiz_id ?>&uid=<?php echo $row['user_id'] ?>" class="hlink">
                                <div class="tbl-icon-container secondary">
                                    <span class="material-symbols-rounded">
                                        description
                                    </span>
                                </div>
                            </a>
                </div>
                </td>
        <?php
                        }
                        echo "</tr>";
                    } else {
                        echo "<tr><td colspan='14'>No Quizzes found</td></tr>";
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