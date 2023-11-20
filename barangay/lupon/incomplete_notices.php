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
            <p style="font-size: 18px; margin-top: 3px;">Back</p>
            </div></a>
            <div class="incomplete-cases" style="width: 80%; margin-left: 90px; height: 40px;">
                <p>Cases with Incomplete Notices</p>
            </div>
        </div>

        <table style="margin-left: 118px; width: 83.7%; background: #fff; text-align: center;">
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


$selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
if (!$selectLuponId) {
    die('Failed to fetch lupon_id: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($selectLuponId);
$pb_id = $row['pb_id'];


$select = mysqli_query($conn, "
SELECT incident_report.incident_case_number AS incident_case_number,
incident_report.complainant_last_name AS complainant_last_name,
incident_report.respondent_last_name AS respondent_last_name,
incident_report.created_at AS created_at,
incident_report.submitter_first_name as submitter_first_name,
incident_report.submitter_last_name as submitter_last_name
FROM `incident_report`
LEFT JOIN `notify_residents` ON incident_report.incident_case_number = notify_residents.incident_case_number
LEFT JOIN `amicable_settlement` ON incident_report.incident_case_number = amicable_settlement.incident_case_number
WHERE (generate_summon = 'not generated' OR generate_hearing = 'not generated' OR generate_pangkat = 'not generated' OR generate_summon IS NULL OR generate_hearing IS NULL OR generate_pangkat IS NULL)
AND incident_report.pb_id = $pb_id
AND NOT EXISTS (
    SELECT 1 FROM `hearing` AS h
    WHERE incident_report.incident_case_number = h.incident_case_number
)
ORDER BY incident_report.created_at DESC
") or die('query failed');

$num_rows = mysqli_num_rows($select);

if ($num_rows === 0) {
    echo '<tr><td colspan="6" style="font-size: 25px; font-weight: 600; text-transform: uppercase;">no cases with incomplete notice</td></tr>';
} else {
    while ($fetch_cases = mysqli_fetch_assoc($select)) {
        $submitter_first_name = $fetch_cases['submitter_first_name'];
        $submitter_last_name = $fetch_cases['submitter_last_name'];
        $submitter_full_name = $submitter_first_name . ' ' . $submitter_last_name;
        ?>
        <tr>
            <td style="width: 9rem;"><?php echo $fetch_cases['incident_case_number']; ?></td>
            <td><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></td>
            <td><?php echo date("F j, Y", strtotime($fetch_cases['created_at'])); ?></td>
            <td><?php echo $submitter_full_name; ?></td>
            <td>
            <?php
            $incident_case_number = $fetch_cases['incident_case_number'];
            $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');
            
            if (mysqli_num_rows($select_hearing) > 0) {
                $hearing_data = mysqli_fetch_assoc($select_hearing);
                $hearing_date = $hearing_data['date_of_hearing'];
            
                if ($hearing_date !== NULL) {
                    echo date("F j, Y", strtotime($hearing_date));
                } else {
                    echo '<span style="font-weight: 600;">NO HEARING SCHEDULE YET</span>';
                }
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
                $hearing_date = $hearing_data['date_of_hearing'];
                if (empty($hearing_date)) {
                    echo '<a href="../../barangay/lupon/hearing_schedule.php?incident_case_number=' . $incident_case_number . '" class="schedule">Set Hearing Schedule</a>';
                    echo '<a href="../../tcpdf/generate_kp7.php?incident_case_number=' . $incident_case_number . '" class="shownotices" target="_blank"><i class="bx bx-printer" style="margin-right: 5px;"></i>Generate KPL Form 7</a>';
                } else {
                    $hearing_date = date("F j, Y", strtotime($hearing_date));
                    echo '<a href="../../barangay/lupon/notice_forms.php?incident_case_number=' . $incident_case_number . '" class="shownotices">Create Notice Form(s)</a>';
                }
            } else {
                echo '<a href="../../barangay/lupon/hearing_schedule.php?incident_case_number=' . $incident_case_number . '" class="schedule">Set Hearing Schedule</a>';
                echo '<a href="../../tcpdf/complainants_form.php?incident_case_number=' . $incident_case_number . '" class="shownotices" target="_blank"><i class="bx bx-printer" style="margin-right: 5px;"></i>Generate KPL Form 7</a>';
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