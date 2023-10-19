<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if(!isset($email)){
header('location: ../../index.php');
}

$query = "SELECT i.*, h.incident_case_number AS hearing_incident_case_number,
                 h.date_of_hearing, h.time_of_hearing
          FROM incident_report i
          LEFT JOIN hearing h ON i.incident_case_number = h.incident_case_number";
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
    
<?php include 'navbar.php';?>

    <section class="home">

        <h1 style="margin-left: 4%; margin-top: 1%; display: flex; font-size: 48px;">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account" style="margin-top: -5%; margin-left: 518px; width: 250px;">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex; margin-left: 5%; width: 80%;">
            <a href="ongoing_cases.php" class="ongoing-cases" style="height: 40px; text-decoration: none;">
                <p>Mediation Hearings</p>
            </a>
            <a href="settled_cases.php" class="settled-cases" style="height: 40px; margin-left: 5px; text-decoration: none;">
                <p>Conciliation Hearings</p>
            </a>
            <a href="incomplete_notices.php" style="text-decoration: none;">
            <div class="incomplete-cases" style="height:40px; width: 135%;" >
                <p>Arbitration Hearings</p>
            </div></a>
        </div>

        <div class="search-container">
        <button class="case-button" style="padding: 0px 12px;">CASE NO.</button>
        <input type="text" id="searchInput" class="search-input" placeholder="Search...">
        <button class="search-button" style="padding: 0px 12px;">Search</button>
    </div>


        <?php
        // Loop through the results and display each row in a container
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="container">';
            echo '<div class="top-text" style="display: flex;">';
            echo '<h3 class="case-no-text" style="font-size: 20px;">Case No. #' . $row['incident_case_number'] . '</h3>';
            echo '</div>';
            echo '<div class="top-text" style="display: flex;">';
            $date_of_hearing = $row['date_of_hearing'];
    $formatted_date = date('l, F j, Y', strtotime($date_of_hearing));
    $time_of_hearing = $row['time_of_hearing'];
    $formatted_time = date('h:i A', strtotime($time_of_hearing));
    $schedule_status = isset($row['date_of_hearing']) && isset($row['time_of_hearing']) ?
        date('l, F j, Y - h:i A', strtotime($row['date_of_hearing'] . ' ' . $row['time_of_hearing'])) :
        "NO SCHEDULE YET";

    echo '<h3 class="case-no-text" style="font-size: 15px; font-weight: 500; font-style: italic; width: 20%;">' . $row['complainant_last_name'] . ' vs. ' . $row['respondent_last_name'] . '</h3>';
    
    if ($schedule_status === "NO SCHEDULE YET") {
        echo '<h3 class="hearing-text" style="font-size: 15px; font-weight: 500; margin-left: 45%;"><b>Hearing Schedule: </b>NO HEARING SCHEDULE YET</h3>';
    } else {
        echo '<h3 class="hearing-text" style="font-size: 15px; font-weight: 500; margin-left: 30%;"><b>Hearing Schedule: </b>' . $schedule_status . '</h3>';
    }
            echo '</div>';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Complainant</th>';
            echo '<th>Respondent</th>';
            echo '<th>Status</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            echo '<tr>';
            echo '<td>' . $row['complainant_last_name'] . ' ' . $row['complainant_first_name'] . ' ' . $row['complainant_middle_name'] . '</td>';
            echo '<td>' . $row['respondent_last_name'] . ' ' . $row['respondent_first_name'] . ' ' . $row['respondent_middle_name'] . '</td>';
            echo '<td></td>';
            echo '<td><button class="schedule" style="width: 90%; margin-left: 5%;"">Set To Notified</button></td>';
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';
            //echo '<td><button class="schedule" style="width: 15%; font-size: 14px; margin-left: 86.5%; top: 20px;"">Manage Notices</button></td>';
            echo '</div>';
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

        modeSwitch.addEventListener("click" , () =>{
            body.classList.toggle("dark");
            
            if(body.classList.contains("dark")){
                modeText.innerText = "Light mode";
            }else{
                modeText.innerText = "Dark mode";
                
            }
        });
    
        function loadContent() {
            // Get the value of the selected option
            const selectedOption = document.getElementById('transactionTableSelect').value;

            // Perform redirection or load content based on the selected option
            if (selectedOption === 'ongoing') {
                window.location.href = 'ongoingcases.php#ongoing';
            } else if (selectedOption === 'settled') {
                window.location.href = 'settledcases.php#settled';
            }
            else if (selectedOption === 'incomplete') {
                window.location.href = 'incompletenotices.php#incomplete';
            }
        }



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
            margin-left: 9%;
            margin-top: 3%;
            width: 1018px;
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


    </style>

</body>
</html>