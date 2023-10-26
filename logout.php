<?php
include 'config.php';

session_start();

// Check if a user is logged in
if (isset($_SESSION['email_address'])) {
    $email = $_SESSION['email_address'];
    
    // Update login status to 'inactive' in the lupon_accounts table
    $updateQuery = "UPDATE lupon_accounts SET login_status = 'inactive' WHERE email_address = '$email'";
    mysqli_query($conn, $updateQuery);
}

// Unset and destroy the session
session_unset();
session_destroy();

header('location:index.php');
?>
