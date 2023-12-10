<?php

include '../config.php';

$incident_case_number = $_GET['incident_case_number'];
$select = mysqli_query($conn, "SELECT * FROM `incident_report`
                                LEFT JOIN `notify_residents` ON `incident_report`.`incident_case_number` = `notify_residents`.`incident_case_number`
                                LEFT JOIN `hearing` ON `incident_report`.`incident_case_number` = `hearing`.`incident_case_number`
                                WHERE `incident_report`.`incident_case_number` = '$incident_case_number'") or die('query failed');
$fetch_cases = mysqli_fetch_assoc($select);


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
            <div class="track-case-container">
                <div class="hearing-text">
                <img src="../images/logo.png" class="image">
                <h4 class="case-id">Case ID: <?php echo htmlspecialchars(substr($fetch_cases['incident_case_number'], 0, 9)); ?></h4>
                </div>
                <hr style="border: 1px solid #b3b3b3; margin: 5px 0;">
                <h2 class="status">ONGOING</h2>
                <?php
            // Check conditions for displaying notifications
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
                            <i class="fa-solid fa-circle-check" style="color: #1db954;"></i>
                            <p>Summon Notified</p>
                        </div>
                        <p class="text">The Summon Notice for the Respondent is now in progress</p>
                    </div>';
            } elseif ($fetch_cases['notify_summon'] == 'notified' && $fetch_cases['notify_hearing'] == 'notified') {
                // Display the actual date_of_hearing and time_of_hearing if available
                $date_of_hearing = isset($fetch_cases['date_of_hearing']) ? date('jS \of F Y', strtotime($fetch_cases['date_of_hearing'])) : '2nd of December 2023';
                $time_of_hearing = isset($fetch_cases['time_of_hearing']) ? date('h:i A', strtotime($fetch_cases['time_of_hearing'])) : '11:00 AM';

                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-check" style="color: #1db954;"></i>
                            <p>Hearing Notified</p>
                        </div>
                        <p class="text">The Complainant and the Respondent have now been notified of their Hearing on ' . $date_of_hearing . ' - ' . $time_of_hearing . '</p>
                    </div>';
            } else {
                // Your existing code for displaying the notification
                echo '<div class="hearing-notice" style="color: white;">
                        <div class="hearing-text">
                            <i class="fa-solid fa-circle-check" style="color: #1db954;"></i>
                            <p>Hearing Notified</p>
                        </div>
                        <p class="text">The Complainant and the Respondent have now been notified of their Hearing on 02 Dec</p>
                    </div>';
            }
            ?>
                <div class="hearing-notice">
                    <div class="hearing-text">
                    <i class="fa-solid fa-circle-check"></i>
                    <p>Upcoming Hearing</p>
                    </div>
                    <p class="text">Your Incident Case will be held on 11 Dec 2023 - 11:00 AM</p>
                </div>
                <div class="hearing-notice">
                    <div class="hearing-text">
                    <i class="fa-solid fa-circle-check"></i>
                    <p>Decision Made</p>
                    </div>
                    <p class="text">Your Incident Case is settled through MEDIATION on  11 Dec (SUPPOSED SAMPLE TEXT ONLY)</p>
                </div>
            </div>

        </center>

        

    </body>

    <script>
        
    </script>

</html>
