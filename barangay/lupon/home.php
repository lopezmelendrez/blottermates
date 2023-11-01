<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

// Get the lupon_id for the logged-in user using their email address
$selectLuponId = mysqli_query($conn, "SELECT lupon_id FROM `lupon_accounts` WHERE email_address = '$email'");
if (!$selectLuponId) {
    die('Failed to fetch lupon_id: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($selectLuponId);
$lupon_id = $row['lupon_id'];

// Use the obtained lupon_id to filter the hearing records based on the incident_case_number
$selectHearing = mysqli_query($conn, "
SELECT hearing.date_of_hearing, hearing.time_of_hearing, hearing.incident_case_number
FROM `hearing`
LEFT JOIN `incident_report` ON hearing.incident_case_number = incident_report.incident_case_number
WHERE incident_report.lupon_id = $lupon_id
") or die('query failed');

$events = [];

while ($fetchHearing = mysqli_fetch_assoc($selectHearing)) {
    $dateOfHearing = $fetchHearing['date_of_hearing'];
    $timeOfHearing = $fetchHearing['time_of_hearing']; 
    $startDatetime = $dateOfHearing . ' ' . $timeOfHearing;
    
    $event = [
        'title' => 'CASE NO. #' . $fetchHearing['incident_case_number'],
        'start' => $startDatetime, 
    ];

    array_push($events, $event);
}

$hasEvents = !empty($events);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/lupon_home.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.js"></script>
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Home</title>

</head>
<body>
    
<?php include 'navbar.php';?>

    <section class="home">
        
        <div class="datetime-container" style="display: flex;">
            <div class="datetime mb-3">
                <div class="time" id="time"></div>
                <div class="date" style="font-size: 23px;"></div>
            </div>

            <a href="../lupon_register.php" style="text-decoration: none; margin-left: 1%;"><div class="add-account" style="display: flex; margin-top: 20%; width: 69%;">
                <i class='bx bx-download'></i>
                <p>Generate Monthly Report</p>
            </div></a>
            
        </div>

        <div class="incident-case-table" style="display: flex;">
            <div class="head-text">
                <p class="incident-case">Recent Incident Cases</p>
                <p class="notice-records">* Needs Notice Records</p>
                <div class="box">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <a href="#">
                        <i class="bx bx-search"></i>
                    </a>
                </div>

        <div class="table-container"  style="max-height: 310px; overflow-y: hidden">
        <hr style="border: 1px solid #949494; margin: 20px 0; width: 80%; margin-top: 5%;">
        <table class="incident-table" style="width: 650px;">
            <thead>
                <tr>
                    <th>Case No</th>
                    <th>Case Title</th>
                    <th>Date Reported</th>
                </tr>
            </thead>
            <tbody id="tableBody" style="overflow-y: hidden;">
            <?php
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

$selectLuponId = mysqli_query($conn, "SELECT lupon_id FROM `lupon_accounts` WHERE email_address = '$email'");
if (!$selectLuponId) {
    die('Failed to fetch lupon_id: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($selectLuponId);
$lupon_id = $row['lupon_id'];
$select = mysqli_query($conn, "
SELECT incident_report.incident_case_number AS incident_case_number,
incident_report.complainant_last_name AS complainant_last_name,
incident_report.respondent_last_name AS respondent_last_name,
incident_report.created_at AS created_at
FROM `incident_report`
LEFT JOIN `notify_residents` ON incident_report.incident_case_number = notify_residents.incident_case_number
LEFT JOIN `amicable_settlement` ON incident_report.incident_case_number = amicable_settlement.incident_case_number
WHERE (generate_summon = 'not generated' OR generate_hearing = 'not generated' OR generate_pangkat = 'not generated' OR generate_summon IS NULL OR generate_hearing IS NULL OR generate_pangkat IS NULL)
AND amicable_settlement.incident_case_number IS NULL
AND incident_report.lupon_id = $lupon_id
") or die('query failed');

if (mysqli_num_rows($select) === 0) {
    echo '<tr><td colspan="3" style="font-size: 25px; font-weight: 600; text-transform: capitalize;">no ongoing incident cases yet</td></tr>';
} else {
    while ($fetchCases = mysqli_fetch_assoc($select)) {
        echo '<tr>';
        echo '<td>' . $fetchCases['incident_case_number'] . '</td>';
        echo '<td>' . $fetchCases['complainant_last_name'] . ' vs. ' . $fetchCases['respondent_last_name'] . '</td>';
        echo '<td>' . date("M, d, Y", strtotime($fetchCases['created_at'])) . '</td>';
        echo '</tr>';
    }
}

?>


<tbody id="noResults" style="display: none;">
    <tr>
        <td colspan="3" style="padding-top: 13%; font-size: 23px; font-weight: 400; text-transform: uppercase; padding-left: 18%;">No Incident Cases Found</td>
    </tr>
</tbody>

            </tbody>
        </table>
        </div>
    </div>

    <div class="calendar-container" style="display: flex;">
        <div id="calendar" style="width: 600px;"></div>
    </div>


    </section>

  
    <script>


        const timeElement = document.querySelector(".time");
const dateElement = document.querySelector(".date");

/**
 * @param {Date} date
 */
 function formatTime(date) {
  const hours = date.getHours();
  const hours12 = hours % 12 || 12;
  const minutes = date.getMinutes();
  const isAm = hours < 12;

  // Use conditional (ternary) operator to format hours without leading zero if it's a single digit
  const formattedHours = hours12 < 10 ? hours12.toString() : hours12;

  return `${formattedHours}:${minutes.toString().padStart(2, "0")} ${isAm ? "AM" : "PM"}`;
}

// Example usage:
const now = new Date();
console.log(formatTime(now)); // Outputs: "7:02 AM" for 07:02 and "12:15 PM" for 12:15


/**
* @param {Date} date
*/
function formatDate(date) {
const DAYS = [
"Sunday",
"Monday",
"Tuesday",
"Wednesday",
"Thursday",
"Friday",
"Saturday"
];
const MONTHS = [
"January",
"February",
"March",
"April",
"May",
"June",
"July",
"August",
"September",
"October",
"November",
"December"
];

return `${DAYS[date.getDay()]} - ${
MONTHS[date.getMonth()]
} ${date.getDate()}, ${date.getFullYear()}`;
}

setInterval(() => {
const now = new Date();

timeElement.textContent = formatTime(now);
dateElement.textContent = formatDate(now);
}, 200);


document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'listMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'listMonth'
        },
        events: <?php echo json_encode($events); ?>,
        views: {
            listMonth: {
                buttonText: 'HEARINGS',
            }
        },
        // Use the 'noEventsContent' callback to customize the "No events to display" text
        noEventsContent: function() {
            return "No Scheduled Hearings Yet";
        }
    });

    calendar.render();
});



const searchInput = document.getElementById("searchInput");
const tableBody = document.getElementById("tableBody");
const noResults = document.getElementById("noResults");

searchInput.addEventListener("input", function() {
    const searchText = searchInput.value.toLowerCase();
    const tableRows = tableBody.getElementsByTagName("tr");
    let resultsFound = false;

    for (const row of tableRows) {
        const caseNumber = row.cells[0].textContent.toLowerCase();
        const caseTitle = row.cells[1].textContent.toLowerCase();

        if (caseNumber.includes(searchText) || caseTitle.includes(searchText)) {
            row.style.display = "";
            resultsFound = true;
        } else {
            row.style.display = "none";
        }
    }

    // Show or hide "No incident cases found" message
    if (resultsFound) {
        noResults.style.display = "none";
    } else {
        noResults.style.display = "table-row-group";
    }
});

    </script>

    <style>
    .home .table-container table{
    width: 100%;
    border-collapse: collapse;
}

table th{
    padding-bottom: 12px;
    padding-right: 20px;
    font-size: 15px;
    text-align: left;
}

.home .table-container table td{
    padding-bottom: 20px;
    font-size: 13px;
}

.home{
    position: absolute;
    top: 0;
    top: 0;
    left: 250px;
    height: 100vh;
    width: calc(100% - 78px);
    background-color: var(--body-color);
    transition: var(--tran-05);
}

.sidebar.close ~ .home{
    left: 78px;
    height: 100vh;
    width: calc(100% - 78px);
}



    </style>

</body>
</html>