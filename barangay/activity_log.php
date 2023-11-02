<?php
include '../config.php'; // Include your database configuration

$activityLogQuery = "SELECT * FROM execution_notice ORDER BY timestamp DESC LIMIT 10"; 
$result = mysqli_query($conn, $activityLogQuery);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo 'User has validated the Execution of Agreement for ' . $row['incident_case_number'] . ' on ' . $row['timestamp'] . '<br>';
    }
} else {
    echo 'No recent activity found.';
}
?>
