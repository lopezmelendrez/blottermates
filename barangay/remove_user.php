<?php
// remove_user.php

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $luponId = $_POST['luponId'];

    $deleteQuery = "DELETE FROM lupon_accounts WHERE lupon_id = '$luponId'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        // Redirect back to the original page after successful deletion
        header('Location: manage_accounts.php');
        exit();
    } else {
        // Handle the error (you can redirect or show an error message)
        echo 'Error: ' . mysqli_error($conn);
    }
} else {
    // Invalid request method
    echo 'Invalid request';
}
?>
