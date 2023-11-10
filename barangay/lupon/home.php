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

if (isset($_POST['submit'])) {
    $generate_report = strtolower(date('F')) . '_report';

        $select_submitter = mysqli_query($conn, "SELECT * FROM lupon_accounts WHERE email_address = '$email'");
        if (mysqli_num_rows($select_submitter) > 0) {
            $submitter_data = mysqli_fetch_assoc($select_submitter);
            $lupon_id = $submitter_data['lupon_id'];
            $submitter_first_name = $submitter_data['first_name'];
            $submitter_last_name = $submitter_data['last_name'];
            $pb_id = $submitter_data['pb_id'];

            mysqli_query($conn, "INSERT INTO `monthly_reports` (generate_report, timestamp, lupon_id, submitter_first_name, submitter_last_name, pb_id) VALUES ('$generate_report', NULL, '$lupon_id', '$submitter_first_name', '$submitter_last_name', '$pb_id')") or die('query failed');
            header("location: home.php");
        }
    }

// Get the current date
$currentDate = date('Y-m-d');

// Check if it's the end of the month
$isEndOfMonth = date('Y-m-t', strtotime($currentDate)) === $currentDate;

// Define the content based on the condition
if ($isEndOfMonth) {
    // Default content if it's the end of the month
    $modalContent = '
    <h3 class="modal-title" style="font-size: 18px; text-align:center;">GENERATE MONTHLY TRANSMITTAL REPORTS</h3>
    <hr style="border: 1px solid #ccc; margin: 10px 0;">
    <p style="font-size: 15px; text-align: justify; font-weight: 600;">By clicking the generate button, the report will be automatically submitted to the DILG.</p>
    <p style="font-size: 14px; text-align: center;">To obtain the report for the previous month, click <a href="link.html" class="click-here">here</a>.</p>
    <div class="button-container" style="margin-top: 3%;">
        <form action="" method="post">
        <input type="submit" name="submit" value="GENERATE REPORT" class="backBtn" style="width: 310px; padding: 12px 12px; font-weight: 600; margin-left: -5px; background: #bc1823; color: #fff; border: none;"></button>
        </form>
    </div>';
} else {
    // Alternative content if it's not the end of the month
    $modalContent = '
    <h3 class="modal-title" style="font-size: 18px; text-align:center;">GENERATE MONTHLY TRANSMITTAL REPORT</h3>
    <hr style="border: 1px solid #ccc; margin: 10px 0;">
    <p style="font-size: 15px; text-align: justify; font-weight: 600;">Report generation is only available at the end of the month. Please try again later.</p>
    <p style="font-size: 14px; text-align: center;">To obtain the report for the previous month, kindly click the button below:</p>
    <div class="button-container" style="margin-top: 3%;">
    <a href="download_last_month_report.php" download="Last_Month_Report.pdf" class="backBtn" style="width: 310px; padding: 12px 12px; font-weight: 600; margin-left: 15px; background: #bc1823; color: #fff; text-decoration: none;" target="_blank">DOWNLOAD LAST MONTH REPORT</a>
    </div>';
}

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

    <section class="home" style="margin-top: -10px;">
        
        <div class="datetime-container" style="display: flex;">
            <div class="datetime mb-3" style="width: 26rem;">
                <div class="time" id="time"></div>
                <div class="date" style="font-size: 24px; width: 24rem;"></div>
            </div>

            <div class="add-account" onclick="showMonthlyReportPopup()" style="display: flex; margin-top: 6%; width: 21.7%;">
                <i class='bx bx-download'></i>
                <p>Generate Monthly Report</p>
            </div>
            
        </div>

        <div class="incident-case-table" style="display: flex; width: 615px;">
            <div class="head-text">
                <p class="incident-case">Recent Incident Cases</p>
                <p class="notice-records">* Needs Notice Records</p>
                <div class="box" style="margin-left: 360px; margin-top: -60px;">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <a href="#">
                        <i class="bx bx-search" style="font-size: 20px; margin-top: 5px;"></i>
                    </a>
                </div>

        <div class="table-container"  style="max-height: 310px; overflow-y: hidden">
        <hr style="border: 1px solid #949494; margin: 20px 0; width: 80%; margin-top: 4%;">
        <table class="incident-table" style="width: 710px;">
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
    echo '<tr><td colspan="3" style="font-size: 22px; font-weight: 600; text-transform: capitalize;">No Incident Cases with Incomplete Notice</td></tr>';
} else {
    while ($fetchCases = mysqli_fetch_assoc($select)) {
        echo '<tr>';
        echo '<td><a href="notice_forms.php?incident_case_number=' . $fetchCases['incident_case_number'] . '" target="_blank">' . $fetchCases['incident_case_number'] . '</a></td>';
        echo '<td>' . $fetchCases['complainant_last_name'] . ' vs. ' . $fetchCases['respondent_last_name'] . '</td>';
        echo '<td>' . date("M d, Y", strtotime($fetchCases['created_at'])) . '</td>';
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

    <div class="calendar-container" style="display: flex; margin-left: -17%;">
        <div id="calendar" style="width: 500px;"></div>
    </div>

    <div id="monthly_report" class="popup">
    <div class="close-icon" onclick="closeMonthlyReportPopup()">
                <i class='bx bxs-x-circle' ></i> <!-- Replace with the desired close icon -->
    </div>
            <center>
            <div class="modal">
            <?php echo $modalContent; ?>
            </div>
            </center>
    </div>


    </section>

    <footer>
            <p>Department of the Interior and Local Government</p>
            <p>F. Gomez St., Brgy.Kanluran, Old Municipal Hall (Gusaling Museo) 4026, Santa Rosa, 4026 Laguna</p>
            <p>&copy; 2023 DILG All Rights Reserved.</p>
        </footer>

  
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
            right: 'prev,next', // Only show prev and next buttons
            center: '', // Remove the title
            left: 'listMonth'
        },
        events: <?php echo json_encode($events); ?>,
        views: {
            listMonth: {
                buttonText: 'SCHEDULED HEARINGS',
            }
        },
        noEventsContent: function() {
            return "No Scheduled Hearings Yet";
        },
        eventClick: function(arg) {
            // Handle the click event and navigate to hearings.php
            window.location.href = 'hearings.php'; // Change this URL to your desired page
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

    function showMonthlyReportPopup() {
        var popup = document.getElementById("monthly_report");
        popup.style.display = "block";


    }

    function closeMonthlyReportPopup() {
        var popup = document.getElementById("monthly_report");
        popup.style.display = "none";
    }

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

.popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal {
    display: flex; /* Add this line to make the modal visible */
    flex-direction: column; /* Adjust to your needs */
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    margin-top: 180px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 500px;
    height: 280px;
    overflow-y: hidden;
    position: relative;
    z-index: 1001; /* Make sure the modal is on top of the overlay */
}

.close-icon {
      position: absolute;
      top: 155px;
      left: 875px;
      cursor: pointer;
      font-size: 50px;
      color:#bc1823;
      z-index: 1002;
    }

.click-here{
    font-style: italic;
}

.click-here:hover{
    font-weight: 500;
    transition: .5s linear;
    letter-spacing: 1;
}
    


    </style>

</body>
</html>