<?php

include '../../config.php';

// Assuming you have a table named 'availability' with columns 'date' and 'time'
// Adjust the table and column names according to your database structure

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedDate'])) {
    $selectedDate = $_POST['selectedDate'];

    // Fetch existing times from the database based on the selected date
    $query = "SELECT DISTINCT TIME_FORMAT(time_of_hearing, '%H:%i:%s') AS formatted_time FROM hearing WHERE date_of_hearing = '$selectedDate'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $existingTimes = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $existingTimes[] = $row['formatted_time'];
        }

        // Default timeslots
        $defaultTimeslots = array('8:00:00', '9:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00');

        // Exclude existing times from default timeslots
        $availableTimes = array_diff($defaultTimeslots, $existingTimes);

        // Return the available times as a JSON array
        echo json_encode(array_values($availableTimes));
    } else {
        // Handle the query error
        echo json_encode(array());
    }
}

?>
