<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (!isset($email)) {
    header('location: ../../index.php');
}

// Get pb_id using prepared statement
$selectLuponId = mysqli_prepare($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = ?");
mysqli_stmt_bind_param($selectLuponId, "s", $email);
mysqli_stmt_execute($selectLuponId);
mysqli_stmt_store_result($selectLuponId);

if (mysqli_stmt_num_rows($selectLuponId) > 0) {
    mysqli_stmt_bind_result($selectLuponId, $pb_id);
    mysqli_stmt_fetch($selectLuponId);
} else {
    die('Failed to fetch lupon_id');
}

mysqli_stmt_close($selectLuponId);

// Get hearings using prepared statement
$selectHearing = mysqli_prepare($conn, "
    SELECT hearing.date_of_hearing, hearing.time_of_hearing, hearing.incident_case_number
    FROM `hearing`
    LEFT JOIN `incident_report` ON hearing.incident_case_number = incident_report.incident_case_number
    LEFT JOIN `amicable_settlement` ON hearing.incident_case_number = amicable_settlement.incident_case_number
    LEFT JOIN `court_action` ON hearing.incident_case_number = court_action.incident_case_number
    WHERE incident_report.pb_id = ?
    AND court_action.incident_case_number IS NULL
    AND amicable_settlement.incident_case_number IS NULL
") or die('Query failed');

mysqli_stmt_bind_param($selectHearing, "i", $pb_id);
mysqli_stmt_execute($selectHearing);
mysqli_stmt_store_result($selectHearing);

$events = [];

mysqli_stmt_bind_result($selectHearing, $dateOfHearing, $timeOfHearing, $incident_case_number);

while (mysqli_stmt_fetch($selectHearing)) {
    $startDatetime = $dateOfHearing . ' ' . $timeOfHearing;

    $event = [
        'title' => 'CASE NO. #' . htmlspecialchars(substr($incident_case_number, 0, 9)),
        'start' => $startDatetime,
    ];

    array_push($events, $event);
}

mysqli_stmt_close($selectHearing);

$hasEvents = !empty($events);

if (isset($_POST['submit'])) {
    $generate_report = strtolower(date('F')) . '_report';

    // Get submitter data using prepared statement
    $selectSubmitter = mysqli_prepare($conn, "SELECT lupon_id, first_name, last_name, pb_id FROM lupon_accounts WHERE email_address = ?");
    mysqli_stmt_bind_param($selectSubmitter, "s", $email);
    mysqli_stmt_execute($selectSubmitter);
    mysqli_stmt_store_result($selectSubmitter);

    if (mysqli_stmt_num_rows($selectSubmitter) > 0) {
        mysqli_stmt_bind_result($selectSubmitter, $lupon_id, $submitter_first_name, $submitter_last_name, $pb_id);
        mysqli_stmt_fetch($selectSubmitter);

        // Insert into monthly_reports using prepared statement
        $insertMonthlyReport = mysqli_prepare($conn, "INSERT INTO `monthly_reports` (generate_report, timestamp, lupon_id, submitter_first_name, submitter_last_name, pb_id) VALUES (?, NULL, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($insertMonthlyReport, "sissi", $generate_report, $lupon_id, $submitter_first_name, $submitter_last_name, $pb_id);
        mysqli_stmt_execute($insertMonthlyReport);

        mysqli_stmt_close($insertMonthlyReport);

        echo '<script>';
        echo 'window.open("http://localhost/barangay%20justice%20management%20system%2001/tcpdf/monthly_transmittal_report.php?luponId=' . $lupon_id . '", "_blank");';
        echo 'window.location.href = window.location.href;';
        echo '</script>';
    }

    mysqli_stmt_close($selectSubmitter);
}


$currentDate = date('Y-m-d');

$isEndOfMonth = (date('d', strtotime($currentDate)) <= 5) || (date('d', strtotime($currentDate)) == 1 && date('m', strtotime($currentDate)) == 1);

if ($isEndOfMonth) {
    $selectLuponData = mysqli_prepare($conn, "SELECT lupon_id FROM `lupon_accounts` WHERE email_address = ?");
    mysqli_stmt_bind_param($selectLuponData, "s", $email);
    mysqli_stmt_execute($selectLuponData);
    mysqli_stmt_store_result($selectLuponData);

    if (mysqli_stmt_num_rows($selectLuponData) > 0) {
        mysqli_stmt_bind_result($selectLuponData, $lupon_id);
        mysqli_stmt_fetch($selectLuponData);

        $downloadLink = "../../tcpdf/transmittal_report.php?lupon_id={$lupon_id}";
    $modalContent = '
    <h3 class="modal-title" style="font-size: 18px; text-align:center;">GENERATE MONTHLY TRANSMITTAL REPORT</h3>
    <hr style="border: 1px solid #ccc; margin: 10px 0;">
    <p style="font-size: 15px; text-align: justify; font-weight: 600; margin-top: 20px;">By clicking the generate button, the report will be automatically submitted to the DILG.</p>
    <p style="font-size: 14px; text-align: center;">To obtain the report for the previous month, click <a href="' . $downloadLink . '" target="_blank" class="click-here">here</a>.</p>
    <div class="button-container" style="margin-top: 3%;">
        <form action="" method="post">
            <input type="submit" name="submit" value="GENERATE REPORT" class="backBtn" style="width: 310px; padding: 12px 12px; font-weight: 600; margin-left: -5px; background: #bc1823; color: #fff; border: none;">
        </form>
    </div>';
    }

} else {
    $selectLuponData = mysqli_prepare($conn, "SELECT lupon_id FROM `lupon_accounts` WHERE email_address = ?");
    mysqli_stmt_bind_param($selectLuponData, "s", $email);
    mysqli_stmt_execute($selectLuponData);
    mysqli_stmt_store_result($selectLuponData);

    if (mysqli_stmt_num_rows($selectLuponData) > 0) {
        mysqli_stmt_bind_result($selectLuponData, $lupon_id);
        mysqli_stmt_fetch($selectLuponData);

        $downloadLink = "../../tcpdf/transmittal_report.php?lupon_id={$lupon_id}";
        $modalContent = '
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">GENERATE MONTHLY TRANSMITTAL REPORT</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 15px; text-align: justify; font-weight: 600;">Report generation is only available during the first week of the month. Please try again later.</p>
            <p style="font-size: 14px; text-align: center;">To obtain the report for the previous month, kindly click the button below:</p>
            <div class="button-container" style="margin-top: 3%;">
                <a href="' . $downloadLink . '" class="backBtn" style="width: 310px; padding: 12px 12px; font-weight: 600; margin-left: 15px; background: #bc1823; color: #fff; text-decoration: none;" target="_blank">DOWNLOAD LAST MONTH REPORT</a>
            </div>';
    }

    mysqli_stmt_close($selectLuponData);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            <div class="date" style="font-size: 21px; width: 24rem;"></div>
        </div>

        <div class="add-account" onclick="showMonthlyReportPopup()">
            <i class='bx bx-download'></i>
            <p>Generate Monthly Report</p>
        </div>

        <?php

        $selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
        if (!$selectLuponId) {
            die('Failed to fetch lupon_id: ' . mysqli_error($conn));
        }
        $row = mysqli_fetch_assoc($selectLuponId);
        $pb_id = $row['pb_id'];

        $currentTimestamp = date('Y-m-d H:i:s'); // Get the current timestamp in the format 'YYYY-MM-DD H:i:s'

        $select = mysqli_query($conn, "
            SELECT execution_notice.incident_case_number AS incident_case_number
            FROM `execution_notice`
            WHERE execution_notice.pb_id = $pb_id
              AND execution_notice.timestamp >= DATE_SUB('$currentTimestamp', INTERVAL 2 DAY)
        ") or die('query failed');

        $notifications = array();
        while ($row = mysqli_fetch_assoc($select)) {
            $incident_case_number = $row['incident_case_number'];

            // Create a link to case_reportPage2.php with the incident_case_number as a parameter
            $link = 'case_reportPage2.php?incident_case_number=' . $incident_case_number;

            // Create the notification message with the link
            $notification =
                'Case <a href="' . $link . '" style="font-size: 11px;">#' . substr($incident_case_number, 0, 9) . '</a> Motion for Execution has been validated.';

            // Add the notification to the array
            $notifications[] = $notification;
        }

        // Display notifications in the HTML structure
        echo '<div class="notification-box" style="cursor: default;">';
        echo '<div class="online" style="display: flex; margin-top: -5px;">';
        echo '<i class="fa-solid fa-bell" style="font-size: 25px;"></i>';
        echo '<p style="margin-top: -2px; margin-left: 10px;">NOTIFICATIONS</p>';
        echo '<div class="dropdown">';
        echo '<p id="notificationCount" style="margin-left: 35px; margin-top: -1px; font-weight: 600; font-size: 20px; cursor: pointer;">(' . count($notifications) . ')</p>';
        // Check if there are notifications before displaying the dropdown content
        if (!empty($notifications)) {
            echo '<div class="dropdown-content">';
            foreach ($notifications as $notification) {
                echo '<div class="notification-container" style="z-index: 1003;">';
                echo '<p style="font-size: 13px; color: #3d3d3d; margin-top: 2%; margin-bottom: 3%;">' . $notification . '</p>';
                echo '</div>';

            }
            echo '</div>';
        }

        echo '</div>';

        echo '</div>';
        echo '</div>';
        ?>

    </div>

    <div class="home-container" style="display: flex;">

        <div class="calendar-container">
            <div id="calendar" class="calendar"></div>
        </div>

        <div class="incident-case-table">
            <div class="head-text">
                <p class="incident-case">Recent Incident Cases</p>
                <p class="notice-records">* Needs Notice Records</p>

                <div class="table-container">
                    <hr style="border: 1px solid #949494; margin: 10px 0; width: 100%; margin-top: 0.2%;">
                    <table class="incident-table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Case Title</th>
                                <th>Requirement</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" style="overflow-y: hidden;">
                            <?php
                            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

                            $selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
                            if (!$selectLuponId) {
                                die('Failed to fetch lupon_id: ' . mysqli_error($conn));
                            }
                            $row = mysqli_fetch_assoc($selectLuponId);
                            $pb_id = $row['pb_id'];

                            $select = mysqli_query($conn, "
                                SELECT
                                    incident_report.incident_case_number AS incident_case_number,
                                    incident_report.complainant_last_name AS complainant_last_name,
                                    incident_report.respondent_last_name AS respondent_last_name,
                                    incident_report.created_at AS created_at,
                                    incident_report.submitter_first_name AS submitter_first_name,
                                    incident_report.submitter_last_name AS submitter_last_name,
                                    notify_residents.generate_summon AS generate_summon,
                                    notify_residents.generate_hearing AS generate_hearing
                                FROM `incident_report`
                                LEFT JOIN `notify_residents` ON incident_report.incident_case_number = notify_residents.incident_case_number
                                LEFT JOIN `amicable_settlement` ON incident_report.incident_case_number = amicable_settlement.incident_case_number
                                WHERE (notify_residents.generate_summon = 'not generated' OR notify_residents.generate_hearing = 'not generated' OR notify_residents.generate_summon IS NULL OR notify_residents.generate_hearing IS NULL)
                                    AND incident_report.pb_id = $pb_id
                                    AND amicable_settlement.incident_case_number IS NULL
                                ORDER BY incident_report.created_at DESC
                            ") or die('query failed');

                            if (mysqli_num_rows($select) === 0) {
                                echo '<tr><td colspan="3" style="font-size: 22px; font-weight: 600; text-transform: capitalize;">No Incident Cases with Incomplete Notice</td></tr>';
                            } else {
                                while ($fetchCases = mysqli_fetch_assoc($select)) {
                                    $incident_case_number = $fetchCases['incident_case_number'];

                                    $checkHearingTable = mysqli_query($conn, "SELECT * FROM `hearing` WHERE incident_case_number = '$incident_case_number'");

                                    if (mysqli_num_rows($checkHearingTable) === 0) {
                                        echo '<tr>';
                                        echo '<td><a href="hearing_schedule.php?incident_case_number=' . $incident_case_number . '" target="_blank">' . htmlspecialchars(substr($incident_case_number, 0, 9)) . '</a></td>';
                                        echo '<td>' . $fetchCases['complainant_last_name'] . ' vs. ' . $fetchCases['respondent_last_name'] . '</td>';
                                        echo '<td>'; // Start the column

                                        if ($fetchCases['generate_summon'] === 'not generated' && $fetchCases['generate_hearing'] === 'form generated') {
                                            echo '<span class="to-notify">NEEDS KP FORM #9</span>';
                                        } elseif ($fetchCases['generate_hearing'] === 'not generated' && $fetchCases['generate_summon'] === 'form generated') {
                                            echo '<span class="to-notify">NEEDS KP FORM #8</span>';
                                        } else {
                                            echo '<span style="font-weight: 900;">SET HEARING SCHEDULE</span>';
                                        }
                                        echo '</tr>';
                                    } else {
                                        echo '<tr>';
                                        echo '<td><a href="notice_forms.php?incident_case_number=' . $incident_case_number . '" target="_blank">' . htmlspecialchars(substr($incident_case_number, 0, 9)) . '</a></td>';
                                        echo '<td>' . $fetchCases['complainant_last_name'] . ' vs. ' . $fetchCases['respondent_last_name'] . '</td>';
                                        echo '<td>'; // Start the column

        if ($fetchCases['generate_summon'] === 'not generated' && $fetchCases['generate_hearing'] === 'form generated') {
            echo '<span class="to-notify">NEEDS KP FORM #9</span>';
        } elseif ($fetchCases['generate_hearing'] === 'not generated' && $fetchCases['generate_summon'] === 'form generated') {
            echo '<span class="to-notify">NEEDS KP FORM #8</span>';
        } else {
            echo '<span class="to-notify">NEED KP FORM #8 AND #9</span>';
        }

        echo '</td>'; // E
                                        echo '</tr>';
                                    }
                                }
                            }
                            ?>
                        </tbody>

                        <tbody id="noResults" style="display: none;">
                            <tr>
                                <td colspan="3" style="padding-top: 13%; font-size: 23px; font-weight: 400; text-transform: uppercase; padding-left: 18%;">No Incident Cases Found</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>

        <div id="monthly_report" class="popup">
            <div class="close-icon" onclick="closeMonthlyReportPopup()">
                <i class='bx bxs-x-circle'></i> <!-- Replace with the desired close icon -->
            </div>
            <center>
                <div class="modal">
                    <?php echo $modalContent; ?>
                </div>
            </center>
        </div>

    </div>

</section>

<script>

const searchIcon = document.querySelector('.search-box .icon');
const searchInput1 = document.getElementById('searchInput1');

searchIcon.addEventListener('click', function () {
    const searchTerm = searchInput1.value.trim().toLowerCase();

    if (searchTerm !== '') {
        handleSearch(searchTerm);
    }
});

searchInput1.addEventListener('keyup', function (e) {
    if (e.key === 'Enter') {
        const searchTerm = searchInput1.value.trim().toLowerCase();

        if (searchTerm !== '') {
            handleSearch(searchTerm);
        }
    }
});

function handleSearch(searchTerm) {
    const lowerCaseSearchTerm = searchTerm.trim().toLowerCase();

    if (lowerCaseSearchTerm.startsWith('mediation') || lowerCaseSearchTerm.endsWith('mediation')) {
        window.location.href = 'mediation_hearings.php';
    } else if (lowerCaseSearchTerm === 'hearing' || lowerCaseSearchTerm === 'hearings') {
        window.location.href = 'hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('incident')) {
        window.location.href = 'incident_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('conciliation') || lowerCaseSearchTerm.endsWith('conciliation')) {
        window.location.href = 'conciliation_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('arbitration') || lowerCaseSearchTerm.endsWith('arbitration')) {
        window.location.href = 'arbitration_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('create') || lowerCaseSearchTerm.endsWith('create')) {
        window.location.href = 'create_report.php';
    } else if (lowerCaseSearchTerm.startsWith('ongoing') || lowerCaseSearchTerm.endsWith('ongoing')) {
        window.location.href = 'ongoing_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('settled') || lowerCaseSearchTerm.endsWith('settled')) {
        window.location.href = 'settled_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('incomplete') || lowerCaseSearchTerm.endsWith('incomplete')) {
        window.location.href = 'incomplete_notices.php';
    } else if (lowerCaseSearchTerm.startsWith('home') || lowerCaseSearchTerm.endsWith('home')) {
        window.location.href = 'home.php';
    } else if (lowerCaseSearchTerm.startsWith('account') || lowerCaseSearchTerm.endsWith('account')) {
        window.location.href = 'my_account.php';
    } else if (lowerCaseSearchTerm.startsWith('profile') || lowerCaseSearchTerm.endsWith('profile')) {
        window.location.href = 'my_account.php';
    } else {
        searchInput1.value = `'${searchTerm.charAt(0).toUpperCase() + searchTerm.slice(1)}' was not found`;
    }
}

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

    return `${DAYS[date.getDay()]} - ${MONTHS[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
}

setInterval(() => {
    const now = new Date();

    timeElement.textContent = formatTime(now);
    dateElement.textContent = formatDate(now);
}, 200);

document.addEventListener('DOMContentLoaded', function () {
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
        noEventsContent: function () {
            return "No Scheduled Hearings Yet";
        },
    });

    calendar.render();
});

const searchInput = document.getElementById("searchInput");
const tableBody = document.getElementById("tableBody");
const noResults = document.getElementById("noResults");

searchInput.addEventListener("input", function () {
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
    .home .table-container table {
        border-collapse: collapse;
        width: 570px; padding: 10px 10px; margin-left: 9px;
    }

    .table-container{
        max-height: 310px; overflow-y: hidden; overflow-x: hidden; margin-top: -1%;
    }

    .add-account{
        display: flex; 
        margin-top: 6%; 
        width: 22%;
    }

    table th {
        padding-bottom: 12px;
        padding-right: 20px;
        font-size: 15px;
        text-align: left;
    }

    .home .table-container table td {
        padding-bottom: 20px;
        font-size: 13px;
    }

    .home {
        position: absolute;
        top: 0;
        top: 0;
        left: 250px;
        height: 100vh;
        width: calc(100% - 78px);
        background-color: var(--body-color);
        transition: var(--tran-05);
    }

    .sidebar.close ~ .home {
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
        display: flex;
        flex-direction: column;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        margin-top: 180px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 500px;
        height: 280px;
        overflow-y: hidden;
        position: relative;
        z-index: 1001;
    }

    .close-icon {
        position: absolute;
        top: 155px;
        left: 895px;
        cursor: pointer;
        font-size: 50px;
        color: #bc1823;
        z-index: 1002;
    }

    .click-here {
        font-style: italic;
    }

    .click-here:hover {
        font-weight: 500;
        transition: .5s linear;
        letter-spacing: 1;
    }

    .notification-box {
        width: 290px;
        height: 45px;
        padding: 12px 12px;
        background: #fff;
        border: 2px solid #Fada5f;
        border-radius: 5px;
        text-align: center;
        margin: 10px;
        position: fixed;
        right: -10px;
        top: -10px;
    }

    .notification-box p,
    .notification-box i {
        font-size: 22px;
        color: #F5be1d;
        font-weight: 600;
        text-transform: uppercase;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: relative;
    }

    .dropdown:hover .dropdown-content {
        display: block;
        width: 290px;
        padding: 12px 12px;
        text-align: center;
        margin: 10px;
        position: fixed;
        right: -10px;
        top: 28px;
    }

    .notification-container {
        border: 1px solid #F5be1d;
        background: white;
        padding-top: 8px;
        padding-bottom: 4px;
        margin-bottom: 10px;
    }

    .incident-case-table {
        width: 600px;
        height: 470px;
    }

    .to-notify{
        font-weight: 900;
        color: #bc1823;
        letter-spacing: 2;
        }
    
    .calendar-container{
        display: flex; height: 470px; width: 530px; margin-left: 3%; margin-top: 0.5%
    }

    .calendar{
        width: 500px;
    }

    @media screen and (min-width: 1310px) {
        .close-icon {
            left: 875px;
        }

        .incident-case-table {
            margin-top: 0.5%;
        }
    }

    @media screen and (max-width: 1310px) and (max-height: 570px) {
        .close-icon {
            left: 870px;
        }

        .incident-case-table {
            margin-top: 0.5%;
        }
    }

    @media screen and (min-width: 1331px) {
        .close-icon {
            left: 895px;
        }

        .incident-case-table {
            margin-top: 0.5%;
        }

        .add-account{
            width: 21.2%;
        }
    }

    @media screen and (min-width: 1710px) {
        .close-icon {
            left: 62.3%;
        }

    }

    @media screen and (max-width: 1352px) and (max-height: 616px) {
        .incident-case-table {
            margin-top: 0.5%;
        }
    }

    @media screen and (min-width: 1360px) and (min-height: 768px) {
        .incident-case-table {
            margin-top: 3.5%;
        }
        .calendar-container{
            margin-top: 3.5%;
        }
        .datetime-container{
            margin-top: 1.5%;
        }
    }

    @media screen and (min-width: 1920px) and (min-height: 1080px){
        .home{
            margin-left: 1.5%;
        }
        .datetime-container{
            margin-top: -20px;
            height: 15rem;
        }
        .datetime-container .datetime{
            padding: 33px;
        }
        .add-account{
            width: 15%;
        }
        .calendar-container{
            width: 950px;
            height: 40rem;
        }
        .calendar{
            width: 950px;
        }
        .incident-case-table{
            width: 600px;
            height: 40rem;
        }
        .incident-case-table .head-text .notice-records{
            margin-top: -1.5%;
        }
        .incident-case-table table{
            width: 800px;
            padding: 20px 20px;
        }
    }


</style>

</body>
</html>