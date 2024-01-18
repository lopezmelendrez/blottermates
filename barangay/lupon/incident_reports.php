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
<script>
    function redirectToSortedPage() {
        // Get the selected option value
        var sortValue = document.getElementById("sort").value;

        // Redirect based on the selected option
        if (sortValue === "latest") {
            window.location.href = "incident_reports.php";
        } else if (sortValue === "oldest") {
            window.location.href = "incidentreports.php";
        }
    }
</script>
    
<?php include 'nav_bar.php';?>

    <section class="home">

        <h1 class="incident-reports">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex;">
            <a href="ongoing_cases.php" class="ongoing-cases" style="height: 40px; text-decoration: none;">
                <p>Ongoing Cases</p>
            </a>
            <a href="settled_cases.php" class="settled-cases" style="height: 40px; margin-left: 1%; text-decoration: none;">
                <p>Settled Cases</p>
            </a>
            <a href="incomplete_notices.php" style="text-decoration: none;">
            <div class="incomplete-cases" style="height:40px;">
                <p>Cases with Incomplete Notices</p>
            </div></a>
        </div>

        <div class="search-container">
            <form action="" method="post">
            <span class="case-button" style="padding: 0px 12px; cursor: default">CASE NO.</span>
                <input type="text" class="search-input" name="search_case" placeholder="Search...">
                <button type="submit" name="submit_search" class="search-button" style="padding: 0px 12px;">Search</button>
            </form>
        </div>

        <div class="sort-container">
    <div class="sort-filter-box">Sort By:</div>
    <form id="sortForm" action="" method="get" onchange="redirectToSortedPage()">
        <select id="sort" name="sort">
            <option value="latest">From Latest to Oldest</option>
            <option value="oldest">From Oldest to Latest</option>
        </select>
    </form>
</div>
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
        echo '<div class="container" style="margin-top: 10px;">';
        echo '<div class="top-text" style="display: flex;">';
        echo '<h3 class="case-no-text" style="font-size: 20px;">Case No. #' . htmlspecialchars(substr($row['incident_case_number'], 0, 9)) . '</h3>';
        echo '</div>';
        if ($schedule_status !== "NO SCHEDULE YET") {
            echo '<div class="top-text-1" style="display: flex;">';
            echo '<h3 class="hearing-text" style="font-size: 15px; margin-top: -2.8%; margin-left: 58%; font-weight: 500;"><b>Hearing Type Status</b>: ' . strtoupper($row['hearing_type_status']) . '</h3>';
            echo '</div>';
        }
        echo '<div class="top-text" style="display: flex;">';
        echo '<h3 class="case-no-text" style="font-size: 15px; font-weight: 500; font-style: italic; width: 20%;">' . $row['complainant_last_name'] . ' vs. ' . $row['respondent_last_name'] . '</h3>';

        if ($schedule_status === "NO SCHEDULE YET") {
            echo '';
        } else {
            echo '<h3 class="hearing-text"><b>Schedule: </b>' . $schedule_status . '</h3>';
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

        
    

    </script>

    <style>
    
        .search-container{
            margin-left: 9%;
        }

        .search-input{
            width: 71%;
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
            width: 80%;
            padding: 20px;
        }

        .cases-container{
            margin-left: 5%; width: 80%;
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

    .incomplete-cases{
        width: 120%;
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

    .hearing-text{
        font-size: 15px; font-weight: 500; margin-left: 38%;
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
    background-color: #F2F3F5;
    padding: 5px 4px;
    font-size: 15px;
    border-radius: 4px;
    margin-right: 10px;
    width: 100px;
    font-weight: 600;
    text-transform: uppercase;
    text-align: center;
    /* Add box shadow */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

        .sort-container {
            margin-left: 64%;
            margin-top: 10px;
            display: flex;
            align-items: center;
        }

        #sort {
            height: 33px;
        }

        .incident-reports{
            margin-left: 4%; margin-top: 1%; display: flex; font-size: 48px;
        }

        .add-account{
            margin-top: -5%; margin-left: 518px; width: 250px;
        }

        @media screen and (min-width: 1331px) and (max-width: 1400px){
            .search-input{
                width: 75%;
            }
            .container{
                width: 1000px;
            }
        }

        @media screen and (min-width: 1340px) and (max-width: 1360px){
            .cases-container{
                width: 81.5%;
            }
            .sort-container{
                margin-left: 65%;
            }
            .container{
                width: 71%;
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

        @media screen and (min-width: 1536px) and (min-height: 730px){
            .add-account{
                margin-top: -4%;
            }
            .search-input{
                width: 72.6%;
            }
            .sort-container{
                margin-left: 66.5%;
            }
            .container{
                width: 79%;
            }
            .top-text-1{
                margin-left: 21.8%;
            }
            .hearing-text{
                margin-left: 47%;
            }
            .generate{
                margin-left: 10%;
            }
        }

        @media screen and (min-width: 1366px) and (min-height: 617px){
            .add-account{
                margin-top: -5%;
            }
            .search-container{
                margin-left: 11%;
            }
            .search-input{
                width: 72%;
            }
            .sort-container{
                margin-left: 65.6%;
            }
            .container{
                width: 79%;
                margin-bottom: 3%;
                margin-left: 11%;
            }
            .cases-container{
                width: 80%;
                margin-left: 7%;
            }
            .incomplete-cases{
                width: 118%;
            }
        }

        @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
            .sort-container{
                margin-left: 65%;
            }
            .cases-container{
                width: 84%;
                margin-left: 6%;
            }
            .search-container{
                margin-left: 10.2%;
            }
            .search-input{
                width: 72.7%;
            }
            .incomplete-cases{
                width: 110%;
            }
            .container{
                margin-left: 10.2%;
                width: 81%;
            }
        }

        
    
        @media screen and (min-width: 1360px) and (min-height: 681px){
            .search-input{
                width: 71.3%;
            }
            .sort-container{
                margin-left: 64.5%;
            }
            .container{
                width: 79.4%;
                margin-bottom: 2%;
            }
        }

        @media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
            .add-account{
                margin-top: -4.42%;
            }
            .search-input{
                width: 73.7%;
            }
            .search-container{
                margin-left: 10.8%;
            }
            .sort-container{
                margin-left: 68%;
            }
            .container{
                width: 78.5%;
            }
            .notify{
                margin-left: 7%;
            }
        }

        @media screen and (max-width: 2133px) and (min-height: 1055px) and (max-height: 1058px){
            .home{
                margin-left: 0%;
            }
            .add-account{
                margin-top: -3%;
                margin-left: 26.5%;
            }
            .cases-container{
                width: 59.5%;
                margin-left: 13%;
            }
            .incomplete-cases{
                width: 157.5%;
            }
            .search-input{
                width: 65.3%;
            }
            .search-container{
                margin-left: 16%;
            }
            .sort-container{
                margin-left: 65%;
                margin-bottom: 1%;
            }
            .container{
                margin-bottom: 2%;
                margin-left: 16%;
                width: 95%;

            }
            .generate{
                margin-left: 15%;
            }
            .notify{
                margin-left: 18%;
            }
        }


    </style>

</body>
</html>