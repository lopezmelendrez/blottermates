<?php
// disable_user.php

include '../config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the pbId from the form submission
    $pbId = $_POST['pbId'];

    $checkLastDisableQuery = "SELECT * FROM pb_accounts WHERE pb_id = '$pbId' AND account_status = 'disabled' AND DATE_SUB(CONVERT_TZ(NOW(), 'UTC', 'Asia/Manila'), INTERVAL 1 MONTH) <= last_disable_date";
    $checkResult = mysqli_query($conn, $checkLastDisableQuery);

    if (mysqli_num_rows($checkResult) > 0) {

        echo "Account can only be disabled once a month.";
    } else {
            $manilaTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $timestamp = $manilaTime->format('Y-m-d H:i:s');
    
        $updateQuery = "UPDATE pb_accounts SET account_status = 'disabled', last_disable_date = '$timestamp' WHERE pb_id = '$pbId'";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            // Handle the case where the update query fails
            die('Update query failed: ' . mysqli_error($conn));
        }
    }
} else {
    // Redirect if accessed directly without a form submission
    header('Location: ../index.php');
    exit;
}
?>
