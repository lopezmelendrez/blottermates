<?php

$configFile = file_get_contents('../barangays.json');
$config = json_decode($configFile, true);

include '../config.php';

session_start();

$pb_id = $_SESSION['pb_id'];
$barangay_captain = $_SESSION['barangay_captain'];

if(!isset($pb_id)){
header('location: ../index.php');
}

if (isset($_POST['submit_search'])) {
    $search_case = mysqli_real_escape_string($conn, $_POST['search_case']);
    $query = "SELECT pa.barangay,
                     incident_report.incident_case_number AS incident_case_number,
                     incident_report.complainant_first_name AS complainant_first_name,
                     incident_report.complainant_last_name AS complainant_last_name,
                     incident_report.respondent_first_name as respondent_first_name,
                     incident_report.respondent_last_name AS respondent_last_name,
                     incident_report.created_at AS created_at,
                     en.timestamp AS execution_date,
                     amicable_settlement.date_agreed AS date_agreed
              FROM `incident_report`
              INNER JOIN `lupon_accounts` AS la ON incident_report.lupon_id = la.lupon_id
              INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
              LEFT JOIN `notify_residents` AS nr ON incident_report.incident_case_number = nr.incident_case_number
              LEFT JOIN `execution_notice` AS en ON incident_report.incident_case_number = en.incident_case_number
              LEFT JOIN `amicable_settlement` AS amicable_settlement ON incident_report.incident_case_number = amicable_settlement.incident_case_number
              WHERE pa.pb_id = '$pb_id'
                    AND nr.generate_execution = 'form generated'
                    AND incident_report.incident_case_number LIKE '%$search_case%'
              ORDER BY en.timestamp ASC";
} else {
    $query = "SELECT pa.barangay,
                     incident_report.incident_case_number AS incident_case_number,
                     incident_report.complainant_first_name AS complainant_first_name,
                     incident_report.complainant_last_name AS complainant_last_name,
                     incident_report.respondent_first_name as respondent_first_name,
                     incident_report.respondent_last_name AS respondent_last_name,
                     incident_report.created_at AS created_at,
                     en.timestamp AS execution_date, -- Include the execution_date from execution_notice
                     amicable_settlement.date_agreed AS date_agreed
              FROM `incident_report`
              INNER JOIN `lupon_accounts` AS la ON incident_report.lupon_id = la.lupon_id
              INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
              LEFT JOIN `notify_residents` AS nr ON incident_report.incident_case_number = nr.incident_case_number
              LEFT JOIN `execution_notice` AS en ON incident_report.incident_case_number = en.incident_case_number
              LEFT JOIN `amicable_settlement` AS amicable_settlement ON incident_report.incident_case_number = amicable_settlement.incident_case_number
              WHERE pa.pb_id = '$pb_id'
                    AND nr.generate_execution = 'form generated'
              ORDER BY en.timestamp ASC";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/lupon.css">
    <link rel="stylesheet" href="../css/incidentform.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <title>Incident Reports</title>
</head>
<body>
    
<nav class="sidebar close">
        <header>
                    <div class="image-text">
                    <?php
                    $select = mysqli_query($conn, "SELECT * FROM `pb_accounts` WHERE pb_id = '$pb_id'") or die('Query failed');

                    if (mysqli_num_rows($select) > 0) {
                        $fetch = mysqli_fetch_assoc($select);
                    }

                    if (!empty($fetch['barangay'])) {
                        $barangay = $fetch['barangay'];

                        if (isset($config['barangayLogos'][$barangay])) {
                            echo '<span class="image"><img src="' . $config['barangayLogos'][$barangay] . '"></span>';
                        } else {
                            echo '<span class="image"><img src="../images/default_logo.png"></span>';
                        }
                    } else {
                        echo '<span class="image"><img src="../images/default_logo.png"></span>';
                    }
                    ?>

                    <div class="text logo-text">
                        <span class="name"><?php echo $barangay_captain ?></span>
                        <span class="profession"  style="font-size: 13px;">Punong Barangay</span>
                    </div>
                    </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

            <li class="search-box">
                <i class='bx bx-search icon'></i>
                <input type="text" id="searchInput1" placeholder="Search..." oninput="restrictInput(this)">
            </li>

                    <li class="nav-link">
                        <a href="home.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="incident_reports.php">
                            <i class='bx bx-receipt icon' ></i>
                            <span class="text nav-text">Incident Reports</span>
                        </a>
                    </li>


                    <li class="nav-link">
                        <a href="activity_history.php">
                            <i class='bx bx-history icon'></i>
                            <span class="text nav-text">Activity History</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="analytics.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Analytics</span>
                        </a>
                    </li>

                    
            </div>

            <div class="bottom-content">
            <li class="">
                <a href="my_account.php">
                        <i class='bx bx-user-circle icon' ></i>
                        <span class="text nav-text">My Account</span>
                    </a>
                </li>
                
                <li class="">
                <a href="manage_accounts.php">
                <i class="fa-solid fa-users-line icon"></i>
                        <span class="text nav-text">Manage Accounts</span>
                    </a>
                </li>

           
                <li class="">
                    <a href="../logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>

                
                
            </div>
        </div>

    </nav>

    <section class="home">

        <h1 style="margin-left: 4%; margin-top: 1%; display: flex; font-size: 48px;">INCIDENT REPORTS</h1>

        <div class="cases-container">
            <a href="" style="text-decoration: none;">
            <div class="validate-cases" style="height:40px; width: 460%;" >
                <p>Validate File of Motion</p>
            </div></a>
        </div>

        <div class="search-container">
            <form action="" method="post">
                <button class="case-button" style="padding: 0px 12px;">CASE NO.</button>
                <input type="text" class="search-input" name="search_case" placeholder="Search...">
                <button type="submit" name="submit_search" class="search-button" style="padding: 0px 12px;">Search</button>
            </form>
        </div>

        <div class="container-box">
        <?php
if (mysqli_num_rows($result) == 0) {
    echo '<div class="text-box">No Incident Cases found</div>';
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $incident_case_number = $row['incident_case_number'];
        $complainant_last_name = $row['complainant_last_name'];
        $respondent_last_name = $row['respondent_last_name'];
        $complainant_first_name = $row['complainant_first_name'];
        $respondent_first_name = $row['respondent_first_name'];
        $date_agreed = !empty($row['execution_date']) ? date('D, d M Y', strtotime($row['execution_date'])) : '';

        $notifyQuery = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
        $notifyResult = mysqli_query($conn, $notifyQuery);
        $notifyRow = mysqli_fetch_assoc($notifyResult);

        $executionQuery = "SELECT * FROM `execution_notice` WHERE incident_case_number = '$incident_case_number'";
        $executionResult = mysqli_query($conn, $executionQuery);
        $executionRow = mysqli_fetch_assoc($executionResult);

        echo '<div class="container">';
        echo '<div class="top-text" style="display: flex;">';
        echo '<h3 class="case-no-text" style="font-size: 20px;">Case No. #' . substr($incident_case_number, 0, 9) . '</h3>';
        echo '</div>';

        echo '<div class="top-text" style="display: flex;">';
        echo '<h3 class="case-no-text" style="font-size: 15px; font-weight: 500; font-style: italic; width: 24%;">';
        echo $complainant_last_name . ' vs. ' . $respondent_last_name;
        

        if (!empty($executionRow['execution_date'])) {
            echo '<h3 class="hearing-text" style="font-size: 15px; font-weight: 500; margin-left: 48%;"><b>Execution Date</b>: ';
            echo date('D, d M Y', strtotime($executionRow['execution_date']));
            echo '</h3>';
        } else {
            echo '<h3 class="hearing-text" style="font-size: 15px; font-weight: 500; margin-left: 43%;"><b>Execution Date</b>: ';
            echo 'NO EXECUTION DATE YET';
            echo '</h3>';
        }

        
        echo '</div>';

        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>COMPLAINANT</th>';
        echo '<th>RESPONDENT</th>';
        echo '<th>STATUS</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        echo '<tr>';
        echo '<td>' . $complainant_first_name . ' ' . $complainant_last_name . '</td>';
        echo '<td>' . $respondent_first_name . ' ' . $respondent_last_name . '</td>';
        echo '<td>';
        if ($notifyRow && $executionRow === null) {
            echo '<p>-</p>';
        } elseif ($notifyRow && $executionRow !== null) {
            echo '<span style="font-weight: 900; color: #0b6623;">
            VALIDATED</span>';
        } else {
            echo '-';
        }
        echo '</td>';
        echo '<td>';
        if ($notifyRow && $executionRow === null) {
            echo '<a href="execution_notice.php?incident_case_number=' . $incident_case_number . '" class="generate">VALIDATE</a>';
        } elseif ($notifyRow && $executionRow !== null) {
            echo '<a href="../tcpdf/notice_of_execution.php?incident_case_number=' . $incident_case_number . '" target="_blank" class="generate">GENERATE KP FORM #27</a>';
        } else {
            echo '-';
        }
        echo '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}
?>
        </div>

    </section>

    <script src="searchbar.js"></script>
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
            margin-top: 3%;
            width: 900px; margin-left: 13%;
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
        margin-left: 12%;
        text-decoration: none;
        cursor: pointer;
    }

    .generate:hover{
        background: #2962ff;
        color: #fff;
        transition: .5s;
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

    .validate-cases{
    flex: 1; /* Distribute available space evenly among child elements */
    text-align: center; /* Center align the text */
    padding: 5px 5px;
    width: 10px; /* Add padding for better spacing */
    border: 1px solid #C23B21; /* Add a border for separation */
    border-radius: 5px; /* Add rounded corners to the divs */
    background-color: #F2F3F5; /* Background color for the divs */
    color: #C23B21;
    font-size: 18px;
    font-weight: 600;
    margin-left: 3%;
    cursor: default;
}

.cases-container{
    margin-left: -33%; 
    width: 100%; 
    margin-top: -2%;
}

@media screen and (min-width: 1331px){
    .home{
        margin-left: 1.3%;
        margin-top: 0%;
    }
}

@media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
        .cases-container{
            margin-top: -0.5%;
            margin-left: -25%;
        }
        .search-container{
            margin-left: 19.5%;
        }
        .container-box{
            margin-left: 10%;
        }
    }

@media screen and (min-width: 1536px) and (min-height: 730px){
    .cases-container{
        margin-top: -1%;
        margin-left: -27%;
    }
    .search-container{
        margin-left: 16.2%;
    }
    .container{
        width: 900px;
        margin-left: 18.8%;
    }
}

@media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
    .container{
        margin-left: 11.5%;
        width: 77%;
    }
    .cases-container{
        margin-left: -31%;
    }
    .search-container{
        margin-left: 11.5%;
    }
    .search-input{
        width: 70%;
    }
}

@media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
    .container{
        margin-left: 9.6%;
        width: 82.5%;
    }
    .cases-container{
        margin-left: -32%;
    }
    .search-container{
        margin-left: 9.6%;
    }
    .search-input{
        width: 73.8%;
    }
}

@media screen and (min-width: 1360px) and (min-height: 681px){
    .cases-container{
        margin-left: -30.5%;
    }
    .search-container{
        margin-left: 11.5%;
    }
    .container{
        width: 76.5%;
        margin-left: 11.5%;
    }
}

@media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
        .cases-container{
            margin-left: -28%;
        }
        .search-container{
            margin-left: 15.2%;
        }
        .search-input{
            width: 64.5%;
        }
        .container{
            margin-left: 15.2%;
            width: 68%;
        }
    }

@media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
    .cases-container{
            margin-left: -28%;
        }
        .search-container{
            margin-left: 14.9%;
        }
        .search-input{
            width: 67%;
        }
        .container{
            margin-left: 14.9%;
            width: 70.9%;
        }
}




    </style>

</body>
</html>