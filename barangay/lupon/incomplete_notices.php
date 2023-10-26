<!--<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if(!isset($email)){
header('location: ../../index.php');
}

?>-->

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
    <title>Incident Cases with Incomplete Notices</title>
</head>
<body>

<?php include 'navbar.php'; ?>

    <section class="home">

    <h1 style="margin-left: 4%; margin-top: 1%; display: flex; font-size: 48px;">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account" style="margin-top: -5%; margin-left: 518px; width: 250px;">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex;">
            <a href="incident_reports.php" style="text-decoration: none;">
            <div class="back" style="width: 150%;">
                <p>Back</p>
            </div></a>
            <div class="incomplete-cases" style="width: 80%; margin-left: 90px; height: 40px;">
                <p>Cases with Incomplete Notices</p>
            </div>
        </div>

        <table style="margin-left: 120px; width: 84%; background: #fff; text-align: center;">
            <thead>
                <tr>
                    <th>Case No.</th>
                    <th>Case Title</th>
                    <th>Date Reported</th>
                    <th>Processed By</th>
                    <th>Date of Hearing</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
    $itemsPerPage = 4;
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $select = mysqli_query($conn, "SELECT * FROM `incident_report` AS ir
    WHERE NOT EXISTS (
        SELECT 1 FROM `hearing` AS h
        INNER JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
        WHERE ir.incident_case_number = h.incident_case_number
    )") or die('query failed');
    
    $num_rows = mysqli_num_rows($select);
    $numPages = ceil($num_rows / $itemsPerPage);

    if ($num_rows === 0) {
        echo '<tr><td colspan="6" style="font-size: 25px; font-weight: 600; text-transform: uppercase;">no ongoing incident cases yet</td></tr>';
    } else {
        // Adjust the query to include LIMIT
        $select = mysqli_query($conn, "SELECT * FROM `incident_report` AS ir
            WHERE NOT EXISTS (
                SELECT 1 FROM `hearing` AS h
                INNER JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
                WHERE ir.incident_case_number = h.incident_case_number
            ) LIMIT $offset, $itemsPerPage") or die('query failed');
        while ($fetch_cases = mysqli_fetch_assoc($select)) {
            $submitter_first_name = $fetch_cases['submitter_first_name'];
            $submitter_last_name = $fetch_cases['submitter_last_name'];
            $submitter_full_name = $submitter_first_name . ' ' . $submitter_last_name;
            ?>
            <tr>
                <td><?php echo $fetch_cases['incident_case_number']; ?></td>
                <td><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></td>
                <td><?php echo date("F j, Y", strtotime($fetch_cases['created_at'])); ?></td>
                <td><?php echo $submitter_full_name; ?></td>
                <td>
                <?php
                $incident_case_number = $fetch_cases['incident_case_number'];
                $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

                if (mysqli_num_rows($select_hearing) > 0) {
                    $hearing_data = mysqli_fetch_assoc($select_hearing);
                    echo date("F j, Y", strtotime($hearing_data['date_of_hearing']));
                } else {
                    echo '<span style="font-weight: 600;">NO HEARING SCHEDULE YET</span>';
                }
                ?>
                </td>
                <td>
                <?php
                $incident_case_number = $fetch_cases['incident_case_number'];
                $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

                if (mysqli_num_rows($select_hearing) > 0) {
                    $hearing_data = mysqli_fetch_assoc($select_hearing);
                    $hearing_date = date("F j, Y", strtotime($hearing_data['date_of_hearing']));
                    echo '<a href="../../barangay/lupon/notice_forms.php?incident_case_number=' . $incident_case_number . '" class="shownotices">Create Notice Form(s)</a>';
                } else {
                    // No hearing date, display "NO HEARING SCHEDULE YET"
                    echo '<a href="../../barangay/lupon/hearing_schedule.php?incident_case_number=' . $incident_case_number . '" class="schedule">Set Hearing Schedule</a>';
                    echo '<a href="../../tcpdf/generate_kp7.php?incident_case_number=' . $incident_case_number . '" class="shownotices"><i class="bx bx-printer" style="margin-right: 5px;"></i>Generate KPL Form 7</a>';
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

        <div class="pagination" style="margin-left: 15%;">
    <?php
    $numPages = ceil($num_rows / $itemsPerPage);

    // Check if there's more than one page before displaying pagination
    if ($numPages > 1) {
        if ($currentPage > 1) {
            echo '<a href="?page=' . ($currentPage - 1) . '">Previous</a>';
        }

        for ($i = 1; $i <= $numPages; $i++) {
            if ($i == $currentPage) {
                echo '<a class="active" href="?page=' . $i . '">' . $i . '</a>';
            } else {
                echo '<a href="?page=' . $i . '">' . $i . '</a>';
            }
        }

        if ($currentPage < $numPages) {
            echo '<a href="?page=' . ($currentPage + 1) . '">Next</a>';
        }
    }
    ?>
</div>


        
        
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

        thead tr th{
            font-size: 13px;
            padding: 12px 12px;
        }

        .pagination {
    display: flex;
    padding-left: 27%;
    margin-top: 10px;
}

.pagination a {
    display: inline-block;
    padding: 4px 8px;
    margin: 0 3px;
    background-color: #f2f2f2;
    border: 1px solid #ddd;
    border-radius: 3px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
}

.pagination a:hover {
    background-color: #ccc;
}

.pagination .active {
    background-color: #007bff;
    color: #fff;
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
    background-color: #C23B21;
    color: #F2F3F5;
    font-weight: 600;
    cursor: pointer;
    border-radius: 5px;
    text-align: center;
    padding: 3px 3px;
}


    </style>

</body>
</html>