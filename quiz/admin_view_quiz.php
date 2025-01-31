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
$sql = "SELECT quiz_id, quiz_name,  duration_in_minutes,total_marks,start_time,stop_time,allowed_entry
FROM quizzes WHERE quiz_name LIKE ? ORDER BY start_time DESC
        LIMIT ?,? ";

// Prepare the statement
$stmt = $con->prepare($sql);

// Add wildcards to the search term
$search_term = "%$search%";

// Bind parameters to the statement
$stmt->bind_param('sii', $search_term, $start_from, $records_per_page);

// Execute the prepared statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Query to get the total number of participants for pagination
// Define the query with placeholders
$total_sql = "SELECT COUNT(*) AS total 
              FROM quizzes 
              WHERE quiz_name LIKE ? ";

// Prepare the statement
$total_stmt = $con->prepare($total_sql);

// Add wildcards to the search term
$search_term = "%$search%";
// Bind parameters to the prepared statement
$total_stmt->bind_param('s', $search_term);


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
    <div class="heading-container">
        <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
        <a onclick="history.back()"><span class="material-symbols-rounded">
                arrow_back_ios
            </span> Back</a>
    </div>
    <section class="dashboard-home-section grid participant-section">
        <h3>Quizzes</h3>
        <form method="post" class="search-form">
            <input type="text" name="search" placeholder="Search by Quiz Name" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" value="Search"><span class="material-symbols-rounded">
                    search
                </span></button>
        </form>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Quiz Name</th>
                        <th>Is Running ?</th>
                        <th>Duration in Mins</th>
                        <th>Total Marks</th>
                        <th>Start Time</th>
                        <th>Stop Time</th>
                        <th>Allowed Entry</th>
                        <th>Already Conducted ?</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <div>
                    <?php
                    // Check if there are any results
                    if ($result->num_rows > 0) {
                        // Loop through and display the data
                        while ($row = $result->fetch_assoc()) {
                            $start_time = $row['start_time'] == null ? 'Not Started' : $row['start_time'];
                            $stop_time = $row['stop_time'] == null ? 'Not Started' : $row['stop_time'];
                            $is_running = false;
                            $is_conducted = false;

                            if ($row['stop_time'] != null) {  // Explicitly check for NOT NULL

                                $stop_time_dt = new DateTime($row['stop_time']);
                                $stop_timespamp = $stop_time_dt->getTimestamp();
                                $current_time = new DateTime();
                                $current_timestamp = $current_time->getTimestamp();
                                if ($current_timestamp <= $stop_timespamp) {
                                    $is_running = true;
                                }
                                if ($current_timestamp >= $stop_timespamp) {
                                    $is_conducted = true;
                                }
                            }
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['quiz_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($is_running ? 'Yes' : 'No') . "</td>";
                            echo "<td>" . htmlspecialchars($row['duration_in_minutes']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['total_marks']) . "</td>";
                            echo "<td>" . htmlspecialchars($start_time) . "</td>";
                            echo "<td>" . htmlspecialchars($stop_time) . "</td>";
                            echo "<td>" . htmlspecialchars(!$row['allowed_entry'] ? 'No' : 'Yes') . "</td>";
                            echo "<td>" . htmlspecialchars($is_conducted ? 'Yes' : 'No') . "</td>";
                            echo '<td>';
                            echo '<div class="action-col-div">';
                            if (!$is_running && !$is_conducted) {
                    ?>

                                <a class="hlink" href="<?php echo $Globals['domain'] ?>/quiz/admin_edit_quiz.php?qid=<?php echo $row['quiz_id'] ?>" class="hlink">
                                    <div class="tbl-icon-container secondary">
                                        <span class="material-symbols-rounded">
                                            edit
                                        </span>
                                    </div>
                                </a>
                            <?php
                            }
                            if (!$row['allowed_entry'] && !$is_running && $row['start_time'] == null) {
                            ?>
                                <a class="hlink" href="<?php echo $Globals['domain'] ?>/quiz/admin_allow_entry.php?qid=<?php echo $row['quiz_id'] ?>" class="hlink">
                                    <div class="tbl-icon-container secondary">
                                        <span class="material-symbols-rounded">
                                            login
                                        </span>
                                    </div>
                                </a>
                            <?php
                            }


                            if ($row['start_time'] == null && !$is_running && $row['allowed_entry']) {
                            ?>
                                <a class="hlink" href="<?php echo $Globals['domain'] ?>/quiz/admin_start_quiz.php?qid=<?php echo $row['quiz_id'] ?>" class="hlink">
                                    <div class="tbl-icon-container secondary">
                                        <span class="material-symbols-rounded">
                                            play_arrow
                                        </span>
                                    </div>
                                </a>
                </div>
                </td>
            <?php
                            }
                            if ($row['start_time'] != null && $is_running && $row['allowed_entry'] || $is_conducted) {
            ?>
                <a class="hlink" href="<?php echo $Globals['domain'] ?>/quiz/admin_view_results.php?qid=<?php echo $row['quiz_id'] ?>" class="hlink">
                    <div class="tbl-icon-container secondary">
                        <span class="material-symbols-rounded">
                            description
                        </span>
                    </div>
                </a>
    <?php
                            }
                            echo "</tr>";
                        }
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