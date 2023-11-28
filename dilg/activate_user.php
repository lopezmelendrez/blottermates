<?php
// activate_user.php

include '../config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the lupon_id from the form submission
    $pbId = $_POST['pbId'];

    // Update the login_status to 'active' in the lupon_accounts table
    $updateQuery = "UPDATE pb_accounts SET account_status = 'active' WHERE pb_id = '$pbId'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        // Redirect to the page where the user was initially
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } else {
        // Handle the case where the update query fails
        die('Update query failed: ' . mysqli_error($conn));
    }
} else {
    // Redirect if accessed directly without a form submission
    header('Location: ../index.php');
    exit;
}
?>