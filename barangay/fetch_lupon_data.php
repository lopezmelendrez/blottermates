<?php
// fetch_lupon_data.php

// Assuming you have a database connection
include '../config.php';

// Get lupon_id from the GET parameters
$luponId = $_GET['lupon_id'];

// Perform a database query to fetch data based on the lupon_id
$query = "SELECT * FROM incident_report WHERE lupon_id = $luponId";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

// Fetch data from the result (adjust as needed)
$data = mysqli_fetch_assoc($result);

// Count the number of incident cases for the given lupon_id
$incidentCasesQuery = "SELECT COUNT(*) AS num_cases FROM incident_report WHERE lupon_id = $luponId";
$incidentCasesResult = mysqli_query($conn, $incidentCasesQuery);

if (!$incidentCasesResult) {
    die('Incident Cases Query failed: ' . mysqli_error($conn));
}

$incidentCasesData = mysqli_fetch_assoc($incidentCasesResult);
$numIncidentCases = $incidentCasesData['num_cases'];

// Add the incident cases count to the data
$data['num_incident_cases'] = $numIncidentCases;

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
