<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.js"></script>
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Mediation Hearings</title>
</head>
<body>

<?php include 'nav_bar.php'; ?>

    <section class="home">

    <h1 class="incident-reports">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex;">
            <a href="hearings.php" style="text-decoration: none;">
            <div class="back" style="width: 150%;">
                <p style="font-size: 18px; margin-top: 3px;">Back</p>
            </div></a>
            <div class="settled-cases" style="width: 80%; margin-left: 90px; height: 40px;">
                <p>Mediation Hearings</p>
            </div>
        </div>

        <div class="pagination">
            <?php
            $selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
            if (!$selectLuponId) {
                die('Failed to fetch lupon_id: ' . mysqli_error($conn));
            }
            $row = mysqli_fetch_assoc($selectLuponId);
            $pb_id = $row['pb_id'];

            $rowsPerPage = 4;

            $selectCount = mysqli_query($conn, "SELECT COUNT(*) AS total_rows
            FROM `incident_report` AS ir
            INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
            WHERE h.date_of_hearing IS NOT NULL
            AND ir.pb_id = $pb_id 
            AND h.time_of_hearing IS NOT NULL 
            AND NOT EXISTS (SELECT 1 FROM `amicable_settlement` AS amicable WHERE h.hearing_id = amicable.hearing_id)
            AND h.hearing_type_status = 'mediation'");

            $rowCount = mysqli_fetch_assoc($selectCount);
            $num_rows = $rowCount['total_rows'];

            $totalPages = ceil($num_rows / $rowsPerPage);

            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

            echo '<div class="pages" style="display: flex; margin-left: 80%; margin-top: -4%;">';

            // Previous button
            if ($currentPage > 1) {
                echo '<i class="bx bxs-left-arrow-square next" onclick="navigatePage(' . ($currentPage - 1) . ')" style="font-size: 50px; color: #0956bf; cursor: pointer;"></i>';
            }

            // Next button
            if ($currentPage == 1 && $num_rows > $rowsPerPage) {
                echo '<i class="bx bxs-right-arrow-square next" onclick="navigatePage(' . ($currentPage + 1) . ')" style="font-size: 50px; color: #0956bf; cursor: pointer; margin-left: 50px;"></i>';
            } elseif ($currentPage > 1) {
                echo '<i class="bx bxs-right-arrow-square next" onclick="navigatePage(' . ($currentPage + 1) . ')" style="font-size: 50px; color: #0956bf; cursor: pointer;"></i>';
            }

            echo '</div>';
            ?>

            <script>
                function navigatePage(page) {
                    window.location.href = '?page=' + page;
                }
            </script>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Case No.</th>
                    <th>Case Title</th>
                    <th>Complainant</th>
                    <th>Respondent</th>
                    <th>Hearing Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php

            $selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
            if (!$selectLuponId) {
                die('Failed to fetch lupon_id: ' . mysqli_error($conn));
            }
            $row = mysqli_fetch_assoc($selectLuponId);
            $pb_id = $row['pb_id'];

            $rowsPerPage = 4; // Adjust the number of rows per page as needed
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $rowsPerPage;


            $select = mysqli_query($conn, "SELECT ir.incident_case_number, ir.complainant_last_name, ir.complainant_first_name, ir.complainant_middle_name, ir.respondent_last_name, ir.respondent_first_name, ir.respondent_middle_name, ir.description_of_violation, ir.incident_date, ir.submitter_first_name, ir.submitter_last_name, ir.created_at 
                FROM `incident_report` AS ir
                INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
                WHERE h.date_of_hearing IS NOT NULL
                AND ir.pb_id = $pb_id 
                AND h.time_of_hearing IS NOT NULL 
                AND NOT EXISTS (SELECT 1 FROM `amicable_settlement` AS amicable WHERE h.hearing_id = amicable.hearing_id)
                AND h.hearing_type_status = 'mediation'
                ORDER BY ir.created_at DESC
                LIMIT $offset, $rowsPerPage")
                or die('query failed');

            $num_rows = mysqli_num_rows($select);

            if ($num_rows === 0) {
                echo '<tr><td colspan="8" style="font-size: 25px; font-weight: 600; text-transform: uppercase;">No Scheduled Mediation Hearings</td></tr>';
            } else {
                while ($fetch_cases = mysqli_fetch_assoc($select)) {
                    $submitter_first_name = $fetch_cases['submitter_first_name'];
                    $submitter_last_name = $fetch_cases['submitter_last_name'];
                    $submitter_full_name = $submitter_first_name . ' ' . $submitter_last_name;
                    ?>
            <tr>    
            <td style="width: 9rem;"><?php echo htmlspecialchars(substr($fetch_cases['incident_case_number'], 0, 9)); ?></td>
            <td><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></td>
            <td><?php echo $fetch_cases['complainant_first_name']; ?> <?php echo $fetch_cases['complainant_last_name'] ; ?></td>
            <td><?php echo $fetch_cases['respondent_first_name']; ?> <?php echo $fetch_cases['respondent_last_name'] ; ?></td>
            <td>
    <?php
    $incident_case_number = $fetch_cases['incident_case_number'];
    $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

    if (mysqli_num_rows($select_hearing) > 0) {
        $hearing_data = mysqli_fetch_assoc($select_hearing);
        $hearing_date = date("F j, Y", strtotime($hearing_data['date_of_hearing']));
        echo $hearing_date;
    }
    ?>
</td>
<td>
    <?php
    $incident_case_number = $fetch_cases['incident_case_number'];
    $select_hearing = mysqli_query($conn, "SELECT hearing_type_status, date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

    if (mysqli_num_rows($select_hearing) > 0) {
        $hearing_data = mysqli_fetch_assoc($select_hearing);
        $hearing_type_status = $hearing_data['hearing_type_status'];
        $date_of_hearing = strtotime($hearing_data['date_of_hearing']);
        $current_time = time();

        if ($current_time >= $date_of_hearing && $hearing_type_status == "mediation") {
            echo '<a href="settlement_page.php?incident_case_number=' . $incident_case_number . '" class="hearing-1" style="text-decoration: none; margin-left: 0%;">Hearing</a>';
        } else {
            // Display an appropriate text for upcoming mediation hearings
            echo '<div class="upcoming-hearing">Upcoming Hearing</div>';
        }

    }
    ?>
</td>

            </tr>
            <?php
    }
}
?>
            </tbody>
        </table>

        
        
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

        thead tr th{
            font-size: 13px;
            padding: 12px 12px;
        }

        .hearing{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        text-decoration: none;

    }

    .hearing:hover{
        color:#2962ff;
        background: #fff;
        border: 1px solid #2962ff;
        transition: .5s
    }

    .mediation {
        border-radius: 0.2rem;
        background-color: #0956BF;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
      
    }
    
    .arbitration {
        border-radius: 0.2rem;
        background-color: #379711;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    .conciliation {
        border-radius: 0.2rem;
        background-color: #ECD407;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    
    .compliance {
        border-radius: 0.2rem;
        background-color: black;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    .non-compliance{
        border-radius: 0.2rem;
        background-color: #fff;
        color: black;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    .details{
        background: #FFCDD2;
        padding: 10px 10px;
        color: #C62828;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
    }
    
    .details:hover{
        background: #C62828;
        color: #FFCDD2;
        transition: .5s;
    }

    table tbody tr:nth-child(even) {
        background-color: #f4f6fb; /* Light gray */
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

.back{
    background-color: white;
    margin-left: 35px;
    height: 40px;
    background: #F5BE1D;
    color: #F2F3F5;
    font-weight: 600;
    cursor: pointer;
    border-radius: 5px;
    text-align: center;
    padding: 3px 3px;
}

.hearing-1{
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
        margin-left: 8%;
    }

    .hearing-1:hover{
        background: #2962ff;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
    }

    .filecourt-action{
        background: #fff;
        padding: 4px 4px;
        color: #bc1823;
        border: 1px solid #bc1823;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        margin-left: 8%;
    }

    .filecourt-action:hover{
        background: #bc1823;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
    }
 

    .settled-cases{
    flex: 1; /* Distribute available space evenly among child elements */
    text-align: center; /* Center align the text */
    padding: 5px 5px;
    width: 10px; /* Add padding for better spacing */
    border: 1px solid #0956BF; /* Add a border for separation */
    border-radius: 5px; /* Add rounded corners to the divs */
    background-color: #F2F3F5; /* Background color for the divs */
    color: #0956BF;
    font-size: 18px;
    font-weight: 600;
    margin-left: 3%;
    cursor: default;
}

.settled-cases:hover{
    color: #0956BF;
    background: #F2F3F5;
}

.back{
    background: #0956BF;
    color: #F2F3F5;
}

.upcoming-hearing{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: default;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        margin-left: 0;
        text-decoration: none;
    }

    .pagination {
            display: flex;
            padding-left: 27%;
            margin-top: 10px;
        }

        .incident-reports{
            margin-left: 4%; 
            margin-top: 1%; 
            display: flex; 
            font-size: 48px;
        }

        .add-account{
            margin-top: -5%; margin-left: 518px; width: 250px;
        }

        table{
            margin-left: 118px; 
            width: 83.7%; 
            background: #fff; 
            text-align: center;
        }

        @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .add-account{
                margin-top: -3.4%;
                margin-left: 530px;
            }

            table{
                margin-left: 150px;
                width: 85.5%;
            }

            .pagination{
                margin-left: 300px;
            }

            .previous{
                margin-top: 20%;
                margin-bottom: 20%;
            }
        }

        @media screen and (min-width: 1536px) and (min-height: 730px){
    .add-account{
        margin-top: -4.5%;
    }
    .pagination{
        margin-top: 1.5%;
        margin-bottom: 1%;
        margin-left: 5.1%;
    }
    table{
        width: 85.5%;
        margin-left: 8.8%;
    }
    .upcoming-hearing{
        margin-left: 8%;   
    }
}

@media screen and (min-width: 1360px) and (min-height: 645px){
    table{
        width: 84.3%;
    }
    .pagination{
        margin-top: 1%;
        margin-bottom: 1%;
    }
}

@media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
    table{
        width: 84.3%;
    }
    .pagination{
        margin-top: 1%;
        margin-bottom: 1%;
    }
}

@media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
            .pagination{
                margin-left: -3%;
            }
        }

    </style>

</body>
</html>