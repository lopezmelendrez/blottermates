<?php
// Include your database connection logic here
include '../config.php';

// Get the selected option value
$sortValue = $_GET['sort'];

// Perform your database query based on the $sortValue
// Replace this with your actual query to fetch updated data

// Example: Fetch data for the last 7 days
if ($sortValue === 'seven-days') {
    $startDate = date('Y-m-d', strtotime('-7 days'));
    $endDate = date('Y-m-d');
    $query = "SELECT DATE(ir.created_at) as date, COUNT(*) as total_cases
              FROM `incident_report` AS ir
              INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
              INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
              WHERE pa.pb_id = '$pb_id'
              AND DATE(ir.created_at) BETWEEN '$startDate' AND '$endDate'
              GROUP BY date
              ORDER BY date";
} elseif ($sortValue === 'thirty-days') {
    $startDate = date('Y-m-d', strtotime('-30 days'));
    $endDate = date('Y-m-d');
    $query = "SELECT DATE(ir.created_at) as date, COUNT(*) as total_cases
              FROM `incident_report` AS ir
              INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
              INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
              WHERE pa.pb_id = '$pb_id'
              AND DATE(ir.created_at) BETWEEN '$startDate' AND '$endDate'
              GROUP BY date
              ORDER BY date";
}
// Add other cases for different options if needed

$result = mysqli_query($conn, $query);

// Process the result and format it as needed
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $formattedDate = date('F j', strtotime($row['date'])); // Format the date as "January 12"
    $data[$formattedDate] = round($row['total_cases']);
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode(array('labels' => array_keys($data), 'data' => array_values($data)));
?>
