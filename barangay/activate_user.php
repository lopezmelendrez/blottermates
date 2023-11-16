<?php
// activate_user.php

include '../config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the lupon_id from the form submission
    $luponId = $_POST['luponId'];

    // Update the login_status to 'active' in the lupon_accounts table
    $updateQuery = "UPDATE lupon_accounts SET login_status = 'inactive' WHERE lupon_id = '$luponId'";
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
