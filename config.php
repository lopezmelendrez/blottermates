<?php

// Define database constants
define('DB_NAME', 'u327593433_srcbrgyblotter');
define('DB_USER', 'u327593433_blottersrc');
define('DB_PASSWORD', 'BlotterSRC1217!');
define('DB_HOST', 'localhost');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}


?>