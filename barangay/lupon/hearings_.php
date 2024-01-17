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
              ORDER BY i.created_at ASC";
} else {
    $query = "SELECT i.*, h.incident_case_number AS hearing_incident_case_number,
                     h.date_of_hearing, h.time_of_hearing, h.hearing_type_status
              FROM incident_report i
              LEFT JOIN hearing h ON i.incident_case_number = h.incident_case_number
              LEFT JOIN court_action ca ON i.incident_case_number = ca.incident_case_number
              WHERE i.pb_id = $pb_id
                    AND ca.incident_case_number IS NULL
              ORDER BY i.created_at ASC";
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
    <title>Hearings</title>
</head>
<body>
<script>
    function redirectToSortedPage() {
        // Get the selected option value
        var sortValue = document.getElementById("sort").value;

        // Redirect based on the selected option
        if (sortValue === "latest") {
            window.location.href = "hearings.php";
        } else if (sortValue === "oldest") {
            window.location.href = "hearings_.php";
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
            <a href="mediation_hearings.php" class="ongoing-cases" style="height: 40px; text-decoration: none;">
                <p>Mediation Hearings</p>
            </a>
            <a href="conciliation_hearings.php" class="settled-cases" style="height: 40px; margin-left: 5px; text-decoration: none;">
                <p>Conciliation Hearings</p>
            </a>
            <a href="arbitration_hearings.php" style="text-decoration: none;">
            <div class="incomplete-cases" style="height:40px;" >
                <p>Arbitration Hearings</p>
            </div></a>
        </div>

        <div class="search-container">
            <form action="" method="post">
                <span class="case-button" style="padding: 0px 12px; cursor: default;">CASE NO.</span>
                <input type="text" class="search-input" name="search_case" placeholder="Search...">
                <button type="submit" name="submit_search" class="search-button" style="padding: 0px 12px;">Search</button>
            </form>
        </div>

        <div class="sort-container">
    <div class="sort-filter-box">Sort By:</div>
    <form id="sortForm" action="" method="get" onchange="redirectToSortedPage()">
        <select id="sort" name="sort">
        <option value="oldest">From Oldest to Latest</option>
            <option value="latest">From Latest to Oldest</option>
        </select>
    </form>
</div>


    <?php

if (mysqli_num_rows($result) == 0) {
    echo '<div class="text-box">No Incident Cases found</div>';
} else {

while ($row = mysqli_fetch_assoc($result)) {
    $incident_case_number = $row['incident_case_number'];
    // Check if the data is found in the amicable_settlement table
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
            echo '<h3 class="hearing-text"><b>Hearing Schedule: </b>' . $schedule_status . '</h3>';
        }
        echo '</div>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Complainant</th>';
        echo '<th>Respondent</th>';
        echo '<th>Requirement</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        if ($schedule_status === "NO SCHEDULE YET") {
            // Display "NO HEARING SCHEDULE" inside the table when there is no schedule
            echo '<tr>';
            echo '<td colspan="5" style="text-align: center; font-size: 24px;">NO HEARING SCHEDULE YET</td>';
            echo '</tr>';
        } else {
            echo '<tr>';
            echo '<td>' . $row['complainant_last_name'] . ' ' . $row['complainant_first_name'] . ' ' . substr($row['complainant_middle_name'], 0, 1) . '.</td>';
            echo '<td>' . $row['respondent_last_name'] . ' ' . $row['respondent_first_name'] . ' ' . substr($row['respondent_middle_name'], 0, 1) . '.</td>';
            echo '<td>';
            $check_query = "SELECT nr.generate_pangkat, nr.generate_summon, nr.generate_hearing, h.hearing_type_status, aa.incident_case_number
                FROM notify_residents nr
                LEFT JOIN hearing h ON nr.incident_case_number = h.incident_case_number
                LEFT JOIN arbitration_agreement aa ON nr.incident_case_number = aa.incident_case_number
                WHERE nr.incident_case_number = '" . $row['incident_case_number'] . "'";
            
            $check_result = mysqli_query($conn, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $row_notify = mysqli_fetch_assoc($check_result);
                $generate_pangkat = $row_notify['generate_pangkat'];
                $hearing_type_status = $row_notify['hearing_type_status'];
            
                if ($hearing_type_status === 'conciliation') {
                    $check_query = "SELECT generate_pangkat FROM notify_residents WHERE incident_case_number = '" . $row['incident_case_number'] . "'";
                    $check_result = mysqli_query($conn, $check_query);

                    if ($check_result && mysqli_num_rows($check_result) > 0) {
                        $row_notify = mysqli_fetch_assoc($check_result);
                        $generate_pangkat = $row_notify['generate_pangkat'];

                        if (empty($generate_pangkat) || $generate_pangkat === 'not generated') { // Check for the specific value
                            echo '<span class="to-notify">NEEDS KP FORM #10</span>';
                        } else {
                            echo '-';
                        }
                    }
                } elseif ($hearing_type_status === 'arbitration') {
                    $select_arbitration_agreement = mysqli_query($conn, "SELECT 1 FROM arbitration_agreement WHERE incident_case_number = '{$row['incident_case_number']}' LIMIT 1");

                    if ($select_arbitration_agreement && mysqli_num_rows($select_arbitration_agreement) > 0) {
                        echo '-';
                    } else {
                        echo '<span class="to-notify">NEEDS KP FORM #14</span>';
                    }
                }
                elseif ($hearing_type_status === 'mediation') {
                    $check_query_mediation = "SELECT generate_summon, generate_hearing FROM notify_residents WHERE incident_case_number = '" . $row['incident_case_number'] . "'";
                    $check_result_mediation = mysqli_query($conn, $check_query_mediation);
                
                    if ($check_result_mediation && mysqli_num_rows($check_result_mediation) > 0) {
                        $row_notify_mediation = mysqli_fetch_assoc($check_result_mediation);
                        $generate_summon_mediation = $row_notify_mediation['generate_summon'];
                        $generate_hearing_mediation = $row_notify_mediation['generate_hearing'];
                
                        if (empty($generate_summon_mediation) || $generate_summon_mediation === 'not generated') {
                            echo '<span class="to-notify">NEEDS KP FORM #9</span>';
                        } elseif (empty($generate_hearing_mediation) || $generate_hearing_mediation === 'not generated') {
                            echo '<span class="to-notify">NEEDS KP FORM #8</span>';
                        } else {
                            echo '-';
                        }
                    }
                    else {
                        echo '<span class="to-notify">NEEDS KP FORM #8 AND #9</span>';
                    }
                }                
            }

            echo '</td>';
            echo '<td>';
            $check_query = "SELECT nr.generate_pangkat, nr.generate_summon, nr.generate_hearing, h.hearing_type_status, aa.incident_case_number, h.date_of_hearing
                FROM notify_residents nr
                LEFT JOIN hearing h ON nr.incident_case_number = h.incident_case_number
                LEFT JOIN arbitration_agreement aa ON nr.incident_case_number = aa.incident_case_number
                WHERE nr.incident_case_number = '" . $row['incident_case_number'] . "'";
            
                $check_result = mysqli_query($conn, $check_query);
            
                if ($check_result && mysqli_num_rows($check_result) > 0) {
                    $row_notify = mysqli_fetch_assoc($check_result);
                    $generate_pangkat = $row_notify['generate_pangkat'];
                    $hearing_type_status = $row_notify['hearing_type_status'];
                    $date_of_hearing = strtotime($row_notify['date_of_hearing']);
                    $current_time = time();

    if ($hearing_type_status === 'conciliation') {
        $check_query = "SELECT generate_pangkat FROM notify_residents WHERE incident_case_number = '" . $row['incident_case_number'] . "'";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $row_notify = mysqli_fetch_assoc($check_result);
            $generate_pangkat = $row_notify['generate_pangkat'];

            if (empty($generate_pangkat) || $generate_pangkat === 'not generated') { // Check for the specific value
                echo '<span class="forms" style="text-decoration: none; width: 90%; margin-left: 6%; cursor: default">Create Notice Form(s)</span>';
            } else {
                if (date('Y-m-d', $current_time) == date('Y-m-d', $date_of_hearing)) {
                    echo '<a href="conciliation_settlement_page.php?incident_case_number=' . $row['incident_case_number'] . '" class="shownotices" style="text-decoration: none; width: 90%; margin-left: 6%;">Go To Hearing</a>';
                } else {
                    echo '<span class="forms" style="text-decoration: none; width: 90%; margin-left: 6%; cursor: default;">Upcoming Hearing</span>';
                }
            }
            
        }
    } elseif ($hearing_type_status === 'arbitration') {
        $select_arbitration_agreement = mysqli_query($conn, "SELECT 1 FROM arbitration_agreement WHERE incident_case_number = '{$row['incident_case_number']}' LIMIT 1");
    
        if ($select_arbitration_agreement && mysqli_num_rows($select_arbitration_agreement) > 0) {
            // Check if the arbitration date is in the future
            if (date('Y-m-d', $current_time) < date('Y-m-d', $date_of_hearing)) {
                echo '<span class="forms" style="text-decoration: none; width: 90%; margin-left: 6%; cursor: default;">Upcoming Hearing</span>';
            } else {
                echo '<a href="arbitration_settlement_page.php?incident_case_number=' . $row['incident_case_number'] . '" class="shownotices" style="text-decoration: none;">Go to Hearing</a>';
            }
        } else {
            echo '<a href="arbitration_agreement.php?incident_case_number=' . $row['incident_case_number'] . '" class="shownotices" style="text-decoration: none;">Create Arbitration Agreement</a>';
        }
    }
    elseif ($hearing_type_status === 'mediation') {
        $check_query_mediation = "SELECT generate_summon, generate_hearing FROM notify_residents WHERE incident_case_number = '" . $row['incident_case_number'] . "'";
        $check_result_mediation = mysqli_query($conn, $check_query_mediation);
    
        if ($check_result_mediation && mysqli_num_rows($check_result_mediation) > 0) {
            $row_notify_mediation = mysqli_fetch_assoc($check_result_mediation);
            $generate_summon_mediation = $row_notify_mediation['generate_summon'];
            $generate_hearing_mediation = $row_notify_mediation['generate_hearing'];
    
            if (empty($generate_summon_mediation) || $generate_summon_mediation === 'not generated' || empty($generate_hearing_mediation) || $generate_hearing_mediation === 'not generated') {
                echo '<span class="forms" style="text-decoration: none; width: 90%; margin-left: 6%; cursor: default">Create Notice Form(s)</span>';
            } else {
                if (date('Y-m-d', $current_time) == date('Y-m-d', $date_of_hearing)) {
                    echo '<a href="settlement_page.php?incident_case_number=' . $row['incident_case_number'] . '" class="shownotices" style="text-decoration: none; width: 90%; margin-left: 6%;">Go To Hearing</a>';
                } else {
                    echo '<span class="forms" style="text-decoration: none; width: 90%; margin-left: 6%; cursor: default;">Upcoming Hearing</span>';
                }
            }
        } else {
            echo '<span class="forms" style="text-decoration: none; width: 90%; margin-left: 6%; cursor: default">Create Notice Form(s)</span>';
        }
    }
    
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

    <script src="search_bar.js"></script>
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

        modeSwitch.addEventListener("click" , () =>{
            body.classList.toggle("dark");
            
            if(body.classList.contains("dark")){
                modeText.innerText = "Light mode";
            }else{
                modeText.innerText = "Dark mode";
                
            }
        });
    
        
    </script>

    <style>
        .hearing-text{
            font-size: 15px; font-weight: 500; margin-left: 38%;
        }
    
        .search-container{
            margin-left: 9%;
        }

        .search-input{
            width: 795px;
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
            margin-top: 3%;
            width: 1018px;
            margin-bottom: 2%;
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

        .ongoing-cases{
        border: 1px solid #0956bf; 
        background-color: #F2F3F5; 
        color: #0956bf;
        }

        .ongoing-cases:hover{
            background: #0956bf;
            color: #F2F3F5;
        }

        .settled-cases{
        border: 1px solid #ecd407; 
        background-color: #F2F3F5; 
        color: #ecd407;
        }

        .settled-cases:hover{
            background: #ecd407;
            color: #F2F3F5;
        }

        .incomplete-cases{
        border: 1px solid #379711; 
        background-color: #F2F3F5; 
        color: #379711;
        width: 135%;
        }

        .incomplete-cases:hover{
            background: #379711;
            color: #F2F3F5;
        }

        .to-notify{
        font-weight: 900;
        color: #bc1823;
        }

        .cases-container{
            margin-left: 5%; width: 80%;
        }

        .forms{
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
    }

    .shownotices:hover{
        background: #2962ff;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
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
            margin-left: 63.6%;
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

    @media screen and (min-width: 1352px){
            .search-input{
                width: 843px;
            }
            .container{
                width: 1035px;
                padding: 16px 16px;
            }
        }

    @media screen and (min-width: 1310px){
            .container{
                width: 985px;
            }
    }

    @media screen and (min-width: 1331px){
            .search-input{
                width: 825px;
            }
            .container{
                width: 1015px;
            }
        }
    
        @media screen and (min-width: 1360px) and (min-height: 645px){
        .sort-container{
            margin-left: 64.5%;
        }
    }

        @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .add-account{
                margin-top: -3.4%;
                margin-left: 530px;
            }
            .search-input{
                width: 75%;
            }
            .sort-container{
                margin-left: 70.3%;
                margin-top: 1%;
                margin-bottom: 1%;
            }
            .container{
                margin-left: 21.7%;
            }
        }

        @media screen and (min-width: 1536px) and (min-height: 730px){
            .add-account{
                margin-top: -4.2%;
            }
            .search-input{
                width: 73.3%;
            }
            .sort-container{
                margin-left: 67.3%;
            }
            .container{
              width: 80%;
              margin-bottom: 3%;   
            }
            .top-text-1{
                margin-left: 10.5%;
            }
            .hearing-text{
                margin-left: 42.5%;
            }
        }

        @media screen and (min-width: 1366px) and (min-height: 617px){
            .add-account{
                margin-top: -4.8%;
            }
            .search-container{
                margin-left: 10.5%;
            }
            .search-input{
                width: 73.1%;
            }
            .sort-container{
                margin-left: 66.1%;
            }
            .container{
                width: 80%;
                margin-bottom: 3%;
                margin-left: 10.5%;
            }
            .cases-container{
                width: 77%;
                margin-left: 6.5%;
            }
            .incomplete-cases{
                width: 155%;
            }
        }

        @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
            .sort-container{
                margin-left: 63%;
            }
            .cases-container{
                width: 75%;
                margin-left: 8%;
            }
            .search-container{
                margin-left: 11.9%;
            }
            .search-input{
                width: 69.8%;
            }
            .incomplete-cases{
                width: 145%;
            }
            .container{
                margin-left: 11.9%;
                width: 77.3%;
            }
        }

        @media screen and (min-width: 1360px) and (min-height: 681px){
            .search-input{
                width: 72%;
            }
            .sort-container{
                margin-left: 65%;
            }
            .container{
                width: 80%;
                margin-bottom: 3%;
            }
        }
        
        @media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
        .incomplete-cases{
            width: 195%;
        }
        .cases-container{
            margin-left: 7.5%;
            width: 70%;
        }
        .search-container{
            margin-left: 11%;
        }
        .search-input{
            width: 73.5%;
        }
        .sort-container{
            margin-left: 67.7%;
        }
        .container{
            width: 78.5%;
            margin-left: 11%;
        }
        .add-account{
            margin-top: -4.4%;
        }
    }



        

    </style>

</body>
</html>