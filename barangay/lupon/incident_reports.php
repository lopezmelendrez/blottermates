<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if(!isset($email)){
header('location: ../../index.php');
}

$selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
if (!$selectLuponId) {
    die('Failed to fetch lupon_id: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($selectLuponId);
$pb_id = $row['pb_id'];

if(isset($_POST['submit_search'])){
    $search_case = mysqli_real_escape_string($conn, $_POST['search_case']);
    $query = "SELECT i.*, h.incident_case_number AS hearing_incident_case_number,
                     h.date_of_hearing, h.time_of_hearing, h.hearing_type_status
              FROM incident_report i
              LEFT JOIN hearing h ON i.incident_case_number = h.incident_case_number
              LEFT JOIN court_action ca ON i.incident_case_number = ca.incident_case_number
              WHERE i.pb_id = $pb_id 
                    AND i.incident_case_number LIKE '%$search_case%'
                    AND ca.incident_case_number IS NULL
              ORDER BY i.created_at DESC";
} else {
    $query = "SELECT i.*, h.incident_case_number AS hearing_incident_case_number,
                     h.date_of_hearing, h.time_of_hearing, h.hearing_type_status
              FROM incident_report i
              LEFT JOIN hearing h ON i.incident_case_number = h.incident_case_number
              LEFT JOIN court_action ca ON i.incident_case_number = ca.incident_case_number
              WHERE i.pb_id = $pb_id
                    AND ca.incident_case_number IS NULL
              ORDER BY i.created_at DESC";
}


$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
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
    <link rel="stylesheet" href="../../css/lupon.css">
    <link rel="stylesheet" href="../../css/incidentform.css">
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Incident Reports</title>
</head>
<body>
    
<?php include 'nav_bar.php';?>

    <section class="home">

        <h1 style="margin-left: 4%; margin-top: 1%; display: flex; font-size: 48px;">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account" style="margin-top: -5%; margin-left: 518px; width: 250px;">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex; margin-left: 5%; width: 80%;">
            <a href="ongoing_cases.php" class="ongoing-cases" style="height: 40px; text-decoration: none;">
                <p>Ongoing Cases</p>
            </a>
            <a href="settled_cases.php" class="settled-cases" style="height: 40px; margin-left: 1%; text-decoration: none;">
                <p>Settled Cases</p>
            </a>
            <a href="incomplete_notices.php" style="text-decoration: none;">
            <div class="incomplete-cases" style="height:40px; width: 120%;" >
                <p>Cases with Incomplete Notices</p>
            </div></a>
        </div>

        <div class="search-container">
            <form action="" method="post">
                <button class="case-button" style="padding: 0px 12px;">CASE NO.</button>
                <input type="text" class="search-input" name="search_case" placeholder="Search...">
                <button type="submit" name="submit_search" class="search-button" style="padding: 0px 12px;">Search</button>
            </form>
        </div>

        <!--<div class="sort-container">
    <div class="sort-filter-box">Sort By:</div>
    <select id="sort" onchange="loadContent()">
        <option value="latest">From Latest to Oldest</option>
        <option value="oldest">From Oldest to Latest</option>
    </select>
</div>-->

    <?php

if (mysqli_num_rows($result) == 0) {
    echo '<div class="text-box">No Incident Cases found</div>';
} else {

    while ($row = mysqli_fetch_assoc($result)) {
    $incident_case_number = $row['incident_case_number'];
    
    $check_query = "SELECT COUNT(*) as count FROM amicable_settlement WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);
    $row_count = mysqli_fetch_assoc($check_result);
    $count = $row_count['count'];

    if ($count == 0) {
        $date_of_hearing = $row['date_of_hearing'];
        $formatted_date = date('l, F j, Y', strtotime($date_of_hearing));
        $time_of_hearing = $row['time_of_hearing'];
        $formatted_time = date('h:i A', strtotime($time_of_hearing));
        $schedule_status = isset($row['date_of_hearing']) && isset($row['time_of_hearing']) ?
            date('D, j F Y - h:i A', strtotime($row['date_of_hearing'] . ' ' . $row['time_of_hearing'])) :
            "NO SCHEDULE YET";
        echo '<div class="container">';
        echo '<div class="top-text" style="display: flex;">';
        echo '<h3 class="case-no-text" style="font-size: 20px;">Case No. #' . htmlspecialchars(substr($row['incident_case_number'], 0, 9)) . '</h3>';
        echo '</div>';
        if ($schedule_status !== "NO SCHEDULE YET") {
            echo '<div class="top-text" style="display: flex;">';
            echo '<h3 class="hearing-text" style="font-size: 15px; margin-top: -2.8%; margin-left: 58%; font-weight: 500;"><b>Hearing Type Status</b>: ' . strtoupper($row['hearing_type_status']) . '</h3>';
            echo '</div>';
        }
        echo '<div class="top-text" style="display: flex;">';
        echo '<h3 class="case-no-text" style="font-size: 15px; font-weight: 500; font-style: italic; width: 20%;">' . $row['complainant_last_name'] . ' vs. ' . $row['respondent_last_name'] . '</h3>';

        if ($schedule_status === "NO SCHEDULE YET") {
            echo '';
        } else {
            echo '<h3 class="hearing-text" style="font-size: 15px; font-weight: 500; margin-left: 38%;"><b>Schedule: </b>' . $schedule_status . '</h3>';
        }
        echo '</div>';

        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Type of Notice</th>';
        echo '<th>Resident Name</th>';
        echo '<th>Status</th>';
        echo '<th>Date Notified</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        if ($schedule_status === "NO SCHEDULE YET") {
            echo '<tr>';
            echo '<td colspan="5" style="text-align: center; font-size: 24px;">NO HEARING SCHEDULE YET</td>';
            echo '</tr>';
        } else {
            echo '<tr>';
            echo '<td>Hearing Notice</td>';
            echo '<td>' . $row['complainant_last_name'] . ' ' . $row['complainant_first_name'] . ' ' . substr($row['complainant_middle_name'], 0, 1) . '.</td>';
            echo '<td>';
            $check_query = "SELECT generate_hearing, notify_hearing, hearing_notified FROM notify_residents WHERE incident_case_number = '" . $row['incident_case_number'] . "'";
            $check_result = mysqli_query($conn, $check_query);

            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $row_notify = mysqli_fetch_assoc($check_result);
                $generate_hearing_value = $row_notify['generate_hearing'];
                $notify_complainant_value = $row_notify['notify_hearing'];
                $hearing_notified = $row_notify['hearing_notified'];
                $formatted_date = date("F j, Y", strtotime($hearing_notified));

                if (empty($generate_hearing_value) || $generate_hearing_value === 'not generated') {
                    echo '-';
                } elseif ($notify_complainant_value === 'notified') {
                    echo '<span class="notified">NOTIFIED</span>';
                } else {
                    echo '<span class="to-notify">TO NOTIFY</span>';
                }
            }
            echo '</td>';
            echo '<td>';
            if (empty($generate_hearing_value) || $generate_hearing_value === 'not generated') {
                echo '-';
            } elseif ($notify_complainant_value === 'notified') {
                echo $formatted_date;
            } else {
                echo '-';
            }
            echo '</td>';
            echo '<td>';
            if (empty($generate_hearing_value) || $generate_hearing_value === 'not generated') {
                echo '<span class="generate" onclick="showSummonPopup()">Generate KP Form #8</span>';
            } elseif ($notify_complainant_value === 'notified') {
                echo '-';
            } else {
                echo '<button class="notify" style="cursor: default;">Set To Notified</button>';
            }
            echo '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>Summon Notice</td>';
            echo '<td>' . $row['respondent_last_name'] . ' ' . $row['respondent_first_name'] . ' ' . substr($row['respondent_middle_name'], 0, 1) . '.' . '</td>';
            echo '<td>';
            $check_query = "SELECT generate_summon, notify_summon, summon_notified FROM notify_residents WHERE incident_case_number = '" . $row['incident_case_number'] . "'";
            $check_result = mysqli_query($conn, $check_query);

            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $row_notify = mysqli_fetch_assoc($check_result);
                $generate_summon_value = $row_notify['generate_summon'];
                $notify_summon_value = $row_notify['notify_summon'];
                $date_summon = $row_notify['summon_notified'];
                $summon_notified = date("F j, Y", strtotime($date_summon));

                if (empty($generate_summon_value) || $generate_summon_value === 'not generated') {
                    echo '-';
                } elseif ($notify_summon_value === 'notified') {
                    echo '<span class="notified">NOTIFIED</span>';
                } else {
                    echo '<span class="to-notify">TO NOTIFY</span>';
                }
            }
            echo '</td>';
            echo '<td>';
            if (empty($generate_summon_value) || $generate_summon_value === 'not generated') {
                echo '-';
            } elseif ($notify_summon_value === 'notified') {
                echo $summon_notified;
            } else {
                echo '-';
            }
            echo '</td>';
            echo '<td>';
            if (empty($generate_summon_value) || $generate_summon_value === 'not generated') {
                echo '<span class="generate" onclick="showSummonPopup()">Generate KP Form #9</span>';
            } elseif ($notify_summon_value === 'notified') {
                echo '-';
            } else {
                echo '<button class="notify" style="cursor: default;">Set To Notified</button>';
            }
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        if ($schedule_status === "NO SCHEDULE YET") {
            echo '<a href="schedule_hearing.php?incident_case_number=' . $row['incident_case_number'] . '" style="text-decoration: none;">';
            echo '<button class="schedule" style="width: 18%; font-size: 14px; margin-left: 82%; margin-top: 10px;">SET HEARING SCHEDULE</button>';
            echo '</a>';
        } else {
            echo '<a href="notice_forms.php?incident_case_number=' . $row['incident_case_number'] . '" style="text-decoration: none;">';
            echo '<button class="schedule" style="width: 15%; font-size: 14px; margin-left: 85%; margin-top: 10px;">Manage Notices</button>';
            echo '</a>';
        }
        echo '</div>';
    }
}
}
?>
        
    </section>

    <script>

        const body = document.querySelector('body'),
        sidebar = body.querySelector('nav'),
        toggle = body.querySelector(".toggle"),
        searchBtn = body.querySelector(".search-box"),
        modeSwitch = body.querySelector(".toggle-switch"),
        modeText = body.querySelector(".mode-text");


        toggle.addEventListener("click" , () =>{
            sidebar.classList.toggle("close");
        })

        searchBtn.addEventListener("click" , () =>{
            sidebar.classList.remove("close");
        })

        function loadContent() {
        const selectedOption = document.getElementById("sort").value;

        if (selectedOption === "oldest") {
            window.location.href = "incident_reports.php?sort=oldest";
        } else if (selectedOption === "latest") {
            window.location.href = "incident_reports.php?sort=latest";
        }
    }

    

    </script>

    <style>
    
        .search-container{
            margin-left: 9%;
        }

        .search-input{
            width: 790px;
            padding: 0 12px;
        }

        .case-button{
            background: #E83422;
            border: none;
            color: #fff;
            font-weight: 500;
        }

        .search-button{
            background: #E83422;
            color: #fff;
            border: none;
            font-weight: 600;
        }

        .search-button:hover{
            background: #bc1823;
            transition: .2s;
        }

        .container{
            background: #f2f3f5;
            margin-left: 9%;
            margin-top: 2%;
            width: 980px;
            padding: 20px;
        }

        .home{
            position: absolute;
            top: 0;
            top: 0;
            left: 250px;
            height: 200vh;
            width: calc(100% - 78px);
            background-color: var(--body-color);
            transition: var(--tran-05);
        }

        .sidebar.close ~ .home{
            left: 78px;
            height: 100vh;
            width: calc(100% - 78px);
        }

        .container table thead tr th{
            font-size: 17px;
            text-align: center;
        }

        .to-notify{
                font-weight: 900;
                color: #bc1823;
        }

        table tbody tr:nth-child(even) {
        background-color: white;
        }

        .generate{
        background: #fff;
        padding: 4px 4px;
        color: #2962ff;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        margin-left: 0;
        text-decoration: none;
        cursor: default;
    }

    .notified{
        font-weight: 900;
        color: #0b6623;
    }

    .notify{
        background: #fff;
        padding: 4px 4px;
        color: #363636;
        border: 1px solid #363636;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        text-decoration: none;
        cursor: default;
    }

    .text-box{
        margin-left: 30%;
        margin-top: 15%;
        background: #bc1823;
        border-radius: 5px;
        color: #fff;
        font-size: 35px;
        width: 500px;
        padding: 13px 13px;
        text-align: center;
        letter-spacing: 1;
        text-transform: uppercase;
    }

    .sort-filter-box {
            background-color: #ccc;
            padding: 4px;
            font-size: 15px;
            border-radius: 4px;
            margin-right: 10px;
            width: 100px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
        }

        .sort-container {
            margin-left: 63.6%;
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        #sort {
            height: 33px;
        }

        @media screen and (min-width: 1331px){
            .search-input{
                width: 825px;
            }
            .container{
                width: 1015px;
            }
        }

        @media screen and (min-width: 1352px){
            .search-input{
                width: 835px;
            }
            .container{
                width: 1025px;
            }
        }

    </style>

</body>
</html>