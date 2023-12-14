<?php

include '../config.php';

$incident_case_number = $_GET['incident_case_number'];

if (!isset($incident_case_number) || empty($incident_case_number)) {
    header('Location: error_page.php');
    exit();
}

$referrer = $_SERVER['HTTP_REFERER'];
$expected_referrer = 'http://localhost/barangay%20justice%20management%20system%2001/resident/track_case.php'; // Change this to your actual domain and path

if (strpos($referrer, $expected_referrer) === false) {
    header('Location: error_page.php');
    exit();
}

$select = mysqli_query($conn, "SELECT *
                                FROM `incident_report`
                                LEFT JOIN `notify_residents` ON `incident_report`.`incident_case_number` = `notify_residents`.`incident_case_number`
                                LEFT JOIN `hearing` ON `incident_report`.`incident_case_number` = `hearing`.`incident_case_number`
                                LEFT JOIN `amicable_settlement` ON `incident_report`.`incident_case_number` = `amicable_settlement`.`incident_case_number`
                                LEFT JOIN `pb_accounts` ON `incident_report`.`pb_id` = `pb_accounts`.`pb_id`
                                LEFT JOIN `execution_notice` ON `incident_report`.`incident_case_number` = `execution_notice`.`incident_case_number`
                                WHERE `incident_report`.`incident_case_number` = '$incident_case_number'") or die('query failed');


$fetch_cases = mysqli_fetch_assoc($select);

if (!isset($fetch_cases['date_of_hearing']) || empty($fetch_cases['date_of_hearing'])) {
    header('Location: error_page.php');
    exit();
}

$barangay = $fetch_cases['barangay'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatinble" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/track_case.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <title>Track Your Case</title>
</head>
<body>
    <center>
        <div class="track-case-container" style="margin-top: 11.5%;">
            <div class="hearing-text">
                <img src="../images/logo.png" class="image">
                <h4 class="case-id" style="margin-top: 0px; font-size: 11px;">
                    Case ID: <?php echo htmlspecialchars(substr($incident_case_number, 0, 9)); ?>
                </h4>
            </div>
            <h3 class="case-id" style="margin-left: 43px; margin-top: -25px;">Brgy. <?php echo $barangay ?></h3>
            <hr style="border: 1px solid #b3b3b3; margin: 20px 0;">
            <?php
            if (!empty($fetch_cases['date_agreed'])) {
                echo '<h2 class="status" style="margin-top: -10px;">SETTLED</h2>';
            } else {
                echo '<h2 class="status" style="margin-top: -10px;">ONGOING</h2>';
            }
            ?>

            <?php
            if ($fetch_cases['notify_summon'] == 'not notified' && $fetch_cases['notify_hearing'] == 'not notified') {
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-notch" style="color: #eed202;"></i>
                            <p>In Progress</p>
                        </div>
                        <p class="text">The Hearing Notice forms for the Complainant and the Respondent are still being processed.</p>
                    </div>';
            } elseif ($fetch_cases['notify_summon'] == 'not notified') {
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-notch" style="color: #eed202;"></i>
                            <p>Summon Notified</p>
                        </div>
                        <p class="text">The Summon Notice for the Respondent is now in progress</p>
                    </div>';
            } elseif ($fetch_cases['notify_hearing'] == 'not notified') {
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-notch" style="color: #eed202;"></i>
                            <p>Summon Notified</p>
                        </div>
                        <p class="text">The Hearing Notice for the Complainant is now in progress.</p>
                    </div>';
            } elseif ($fetch_cases['notify_summon'] == 'notified' && $fetch_cases['notify_hearing'] == 'notified') {
                $date_of_hearing = isset($fetch_cases['date_of_hearing']) ? date('jS \of F Y', strtotime($fetch_cases['date_of_hearing'])) : '2nd of December 2023';
                $time_of_hearing = isset($fetch_cases['time_of_hearing']) ? date('h:i A', strtotime($fetch_cases['time_of_hearing'])) : '11:00 AM';

                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-check" style="color: #1db954;"></i>
                            <p>Hearing Notified</p>
                        </div>
                        <p class="text">The Complainant and the Respondent have now been notified of their Hearing.</p>
                    </div>';
            } else {
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-check" style="color: #1db954;"></i>
                            <p>Hearing Notified</p>
                        </div>
                        <p class="text">The Complainant and the Respondent have now been notified of their Hearing on 02 Dec</p> 
                    </div>';
            }
            ?>

            <?php
            $date_of_hearing = isset($fetch_cases['date_of_hearing']) ? strtotime($fetch_cases['date_of_hearing']) : null;
            $hearing_type_status = $fetch_cases['hearing_type_status'];

            if (!empty($fetch_cases['date_agreed'])) {
                $date = isset($fetch_cases['date_of_hearing']) ? date('jS \of F Y', strtotime($fetch_cases['date_of_hearing'])) : '2nd of December 2023';
                $time = isset($fetch_cases['time_of_hearing']) ? date('h:i A', strtotime($fetch_cases['time_of_hearing'])) : '11:00 AM';

                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-check" style="color: #1db954;"></i>
                            <p>Hearing</p>
                        </div>';
                echo '<p class="text">Your Incident Case was held on ' . $date . ' - ' . $time . ' </p>';
                echo '</div>';
            } elseif (empty($fetch_cases['date_agreed']) && $date_of_hearing && ($date_of_hearing == strtotime(date('Y-m-d')) || $date_of_hearing < strtotime(date('Y-m-d')))) {
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-notch" style="color: #eed202;"></i>
                            <p>Hearing In Progress</p>
                        </div>';
                echo '<p class="text">' . strtoupper($hearing_type_status) . ' Hearing for this Case is currently Ongoing. Please wait for the decision.</p>';
                echo '</div>';
            } elseif ($fetch_cases['notify_summon'] == 'notified' && $fetch_cases['notify_hearing'] == 'notified') {
                $date = isset($fetch_cases['date_of_hearing']) ? date('jS \of F Y', strtotime($fetch_cases['date_of_hearing'])) : '2nd of December 2023';
                $time = isset($fetch_cases['time_of_hearing']) ? date('h:i A', strtotime($fetch_cases['time_of_hearing'])) : '11:00 AM';
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-notch" style="color: #eed202;"></i>
                            <p>Upcoming Hearing</p>
                        </div>';
                echo '<p class="text">Your Incident Case will be held on ' . $date . ' - ' . $time . ' </p>';
                echo '</div>';
            } else {
                $formatted_date_of_hearing = isset($fetch_cases['date_of_hearing']) ? date('j M Y - h:i A', $date_of_hearing) : '11 Dec 2023 - 11:00 AM';
                echo '<div class="hearing-notice">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-check"></i>
                            <p>Upcoming Hearing</p>
                        </div>';
                echo '<p class="text">Your Incident Case will be held on ' . $formatted_date_of_hearing . '</p>';
                echo '</div>';
            }
            ?>

            <?php
            if (!empty($fetch_cases['date_agreed'])) {
                $formatted_date = date('jS \of F Y \— h:i A', strtotime($fetch_cases['date_agreed']));
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-check" style="color: #1db954;"></i>
                            <p>Decision Made</p>
                        </div>
                        <p class="text">Settled through MEDIATION on ' . $formatted_date . '. Please wait for the Filing of Motion for Execution.</p>
                    </div>';
            } else {
                echo '<div class="hearing-notice">
                        <div class="hearing-text">
                        </div>
                        <p class="text"></p>
                    </div>';
            }
            ?>
            <?php
            if (!empty($fetch_cases['execution_date'])) {
                echo '<span class="button">Notice of Execution has now been processed</span>';
            } else {
        
            }
            ?>
        </div>
    </center>
</body>
<style>

.button{
    background: #1db954;
    padding: 8px 25px;
    color: white;
    text-transform: uppercase;
    font-weight: 500;
    letter-spacing: 1;
    font-size: 14px;
}
</style>
</html>
