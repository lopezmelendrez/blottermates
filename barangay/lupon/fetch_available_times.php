<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedDate'])) {
    $selectedDate = $_POST['selectedDate'];

     $selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
    
     if (!$selectLuponId) {
         die('Failed to fetch pb_id: ' . mysqli_error($conn));
     }
 
     $row = mysqli_fetch_assoc($selectLuponId);
     $pb_id = $row['pb_id'];

     $query = "SELECT DISTINCT TIME_FORMAT(time_of_hearing, '%H:%i:%s') AS formatted_time FROM hearing WHERE date_of_hearing = '$selectedDate' AND pb_id = '$pb_id'";
     $result = mysqli_query($conn, $query);

    if ($result) {
        $existingTimes = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $existingTimes[] = $row['formatted_time'];
        }

        $defaultTimeslots = array('08:00:00', '09:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00');

        $availableTimes = array_diff($defaultTimeslots, $existingTimes);

        echo json_encode(array_values($availableTimes));
    } else {
        echo json_encode(array());
    }
}

?>
