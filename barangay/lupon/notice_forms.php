<?php
include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (!isset($email)) {
    header('location: ../../index.php');
}

function displayPage($conn, $incident_case_number)
{
    // Check if the incident case number is found in the hearing table
    $check_hearing_query = "SELECT * FROM hearing WHERE incident_case_number = '$incident_case_number'";
    $check_hearing_result = mysqli_query($conn, $check_hearing_query);

    if ($check_hearing_result && mysqli_num_rows($check_hearing_result) > 0) {
        // If incident_case_number is found in the hearing table, check additional conditions

        $hearing_row = mysqli_fetch_assoc($check_hearing_result);

        // Check if incident_case_number is not found in the amicable_settlement table
        $check_amicable_query = "SELECT * FROM amicable_settlement WHERE incident_case_number = '$incident_case_number'";
        $check_amicable_result = mysqli_query($conn, $check_amicable_query);

        // Check if the hearing_type_status is not "filed to court action"
        if ($check_amicable_result && mysqli_num_rows($check_amicable_result) == 0 && $hearing_row['hearing_type_status'] != 'filed to court action') {
            // If all conditions are met, return true
            return true;
        }
    }

    // If the conditions are not met or there was an issue with the queries, return false
    return false;
}

$incident_case_number = $_GET['incident_case_number'];

if (!displayPage($conn, $incident_case_number)) {
    header('location: incident_reports.php');
}

$generate_hearing_value = '';
$generate_summon_value = '';
$notify_complainant_value = '';
$notify_summon_value = '';
$generate_pangkat = '';
$notify_pangkat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $generate_summon = 'form generated';

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE `notify_residents` SET `generate_summon` = '$generate_summon', `generated_summon_timestamp` = NOW() WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `generate_summon`, `generated_summon_timestamp`)
                         VALUES ('$incident_case_number', '$generate_summon', NOW())";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        $incident_case_number = $_POST['incident_case_number'];
        echo '<script>';
        echo 'window.open("http://localhost/barangay%20justice%20management%20system%2001/tcpdf/summon_for_the_respondent_form.php?incident_case_number=' . $incident_case_number . '", "_blank");';
        echo 'window.location.href = window.location.href;';
        echo '</script>';
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hearing_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $generate_hearing = 'form generated';

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE `notify_residents` SET `generate_hearing` = '$generate_hearing', `generated_hearing_timestamp` = NOW() WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `generate_hearing`, `generated_hearing_timestamp`)
                         VALUES ('$incident_case_number', '$generate_hearing', NOW())";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        $incident_case_number = $_POST['incident_case_number'];
        echo '<script>';
        echo 'window.open("http://localhost/barangay%20justice%20management%20system%2001/tcpdf/notice_of_hearing_form.php?incident_case_number=' . $incident_case_number . '", "_blank");';
        echo 'window.location.href = window.location.href;';
        echo '</script>';
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pangkat_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $generate_pangkat = 'form generated';

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE `notify_residents` SET `generate_pangkat` = '$generate_pangkat', `generated_pangkat_timestamp` = NOW() WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `generate_pangkat`, `generated_pangkat_timestamp`)
                         VALUES ('$incident_case_number', '$generate_pangkat', NOW())";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        $incident_case_number = $_POST['incident_case_number'];
        echo '<script>';
        echo 'window.open("http://localhost/barangay%20justice%20management%20system%2001/tcpdf/generate_kp10.php?incident_case_number=' . $incident_case_number . '", "_blank");';
        echo 'window.location.href = window.location.href;';
        echo '</script>';
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify_complainant_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $notify_hearing = 'notified';

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query =  "UPDATE `notify_residents` 
        SET `notify_hearing` = '$notify_hearing', `hearing_notified` = NOW() 
        WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `notify_hearing`, `hearing_notified`)
        VALUES ('$incident_case_number', '$notify_hearing', NOW())";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        // Fetch complainant_cellphone_number from incident_report table
        $complainant_query = "SELECT complainant_cellphone_number FROM `incident_report` WHERE incident_case_number = '$incident_case_number'";
        $complainant_result = mysqli_query($conn, $complainant_query);

        if ($complainant_result && mysqli_num_rows($complainant_result) > 0) {
            $complainant_row = mysqli_fetch_assoc($complainant_result);
            $complainant_cellphone_number = $complainant_row['complainant_cellphone_number'];

            // Check if the cellphone number already has the country code
            if (!startsWith($complainant_cellphone_number, '+63')) {
                // If not, prepend the country code
                $complainant_cellphone_number = '+63' . ltrim($complainant_cellphone_number, '0');
            }

            $send_data = [
                'sender_id' => 'PhilSMS',
                'recipient' => $complainant_cellphone_number, // Use complainant's cellphone number
                'message' => "Hello, this is to inform you that the Notice of Hearing for your Incident Case is now in progress. To track updates easily, use our online platform: http://localhost/barangay%20justice%20management%20system%2001/resident/track_case.php?incident_case_number=$incident_case_number 
For any questions, contact Barangay Ibaba, Santa Rosa Laguna. Thank you for your cooperation.",
            ];

            $token = "187|4Ix57qaMwfknN0suokLhfdNiM88e1LDdAxDoqOqI ";

            $parameters = json_encode($send_data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://app.philsms.com/api/v3/sms/send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer $token",
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $get_sms_status = curl_exec($ch);

            var_dump($get_sms_status);

            header("Location: " . $_SERVER['PHP_SELF'] . "?incident_case_number=" . $incident_case_number);
            exit;
        } else {
            echo "Error fetching complainant's cellphone number: " . mysqli_error($conn);
            exit;
        }
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}

function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify_respondent_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $notify_summon = 'notified';

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE `notify_residents` 
        SET `notify_summon` = '$notify_summon', `summon_notified` = NOW() 
        WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `notify_summon`, `summon_notified`)
        VALUES ('$incident_case_number', '$notify_summon', NOW())";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        // Fetch respondent_cellphone_number from incident_report table
        $respondent_query = "SELECT respondent_cellphone_number FROM `incident_report` WHERE incident_case_number = '$incident_case_number'";
        $respondent_result = mysqli_query($conn, $respondent_query);

        if ($respondent_result && mysqli_num_rows($respondent_result) > 0) {
            $respondent_row = mysqli_fetch_assoc($respondent_result);
            $respondent_cellphone_number = $respondent_row['respondent_cellphone_number'];

            // Check if the cellphone number already has the country code
            if (!startsWith($respondent_cellphone_number, '+63')) {
                // If not, prepend the country code
                $respondent_cellphone_number = '+63' . ltrim($respondent_cellphone_number, '0');
            }

            $send_data = [
                'sender_id' => 'PhilSMS',
                'recipient' => $respondent_cellphone_number, // Use complainant's cellphone number
                'message' => "Hello, this is to inform you that the Notice of Hearing for your Incident Case is now in progress. To track updates easily, use our online platform: http://localhost/barangay%20justice%20management%20system%2001/resident/track_case.php?incident_case_number=$incident_case_number 
For any questions, contact Barangay Ibaba, Santa Rosa Laguna. Thank you for your cooperation.",
            ];

            $token = "187|4Ix57qaMwfknN0suokLhfdNiM88e1LDdAxDoqOqI ";

            $parameters = json_encode($send_data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://app.philsms.com/api/v3/sms/send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer $token",
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $get_sms_status = curl_exec($ch);

            var_dump($get_sms_status);

            header("Location: " . $_SERVER['PHP_SELF'] . "?incident_case_number=" . $incident_case_number);
            exit;
        } else {
            echo "Error fetching respondent's cellphone number: " . mysqli_error($conn);
            exit;
        }
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify_pangkat_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $notify_pangkat = 'notified';

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE `notify_residents` 
        SET `notify_pangkat` = '$notify_pangkat', `pangkat_notified` = NOW() 
        WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `notify_pangkat`, `pangkat_notified`)
        VALUES ('$incident_case_number', '$notify_pangkat', NOW())";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        // Fetch respondent_cellphone_number from incident_report table
        $respondent_query = "SELECT respondent_cellphone_number FROM `incident_report` WHERE incident_case_number = '$incident_case_number'";
        $respondent_result = mysqli_query($conn, $respondent_query);

        if ($respondent_result && mysqli_num_rows($respondent_result) > 0) {
            $respondent_row = mysqli_fetch_assoc($respondent_result);
            $respondent_cellphone_number = $respondent_row['respondent_cellphone_number'];

            // Check if the cellphone number already has the country code
            if (!startsWith($respondent_cellphone_number, '+63')) {
                // If not, prepend the country code
                $respondent_cellphone_number = '+63' . ltrim($respondent_cellphone_number, '0');
            }

            $send_data = [
                'sender_id' => 'PhilSMS',
                'recipient' => $respondent_cellphone_number, // Use complainant's cellphone number
                'message' => "Hello, this is to inform you that the Notice of Hearing for your Incident Case is now in progress. To track updates easily, use our online platform: http://localhost/barangay%20justice%20management%20system%2001/resident/track_case.php?incident_case_number=$incident_case_number 
For any questions, contact Barangay Ibaba, Santa Rosa Laguna. Thank you for your cooperation.",
            ];

            $token = "187|4Ix57qaMwfknN0suokLhfdNiM88e1LDdAxDoqOqI ";

            $parameters = json_encode($send_data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://app.philsms.com/api/v3/sms/send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer $token",
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $get_sms_status = curl_exec($ch);

            var_dump($get_sms_status);

            header("Location: " . $_SERVER['PHP_SELF'] . "?incident_case_number=" . $incident_case_number);
            exit;
        } else {
            echo "Error fetching respondent's cellphone number: " . mysqli_error($conn);
            exit;
        } 
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/incidentform.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Notice Management</title>
</head>
<body>

<nav class="sidebar close">
        <header>
            <div class="image-text">
            <?php
                    $select = mysqli_query($conn, "SELECT l.*, pb.barangay 
                                                FROM `lupon_accounts` l
                                                LEFT JOIN `pb_accounts` pb ON l.pb_id = pb.pb_id
                                                WHERE l.email_address = '$email'") or die('query failed');
                    if(mysqli_num_rows($select) > 0){
                        $fetch = mysqli_fetch_assoc($select);
                    }
                ?>
                              <?php
if ($fetch['barangay'] == 'Ibaba') {
    echo '<span class="image"><img src="../../images/ibaba_logo.png"></span>';
} elseif ($fetch['barangay'] == 'Other') {
    echo '<span class="image"><img src="../../images/logo.png"></span>';
} elseif ($fetch['barangay'] == 'Labas') {
    echo '<span class="image"><img src="../../images/labas.png"></span>';
} elseif ($fetch['barangay'] == 'Tagapo') {
    echo '<span class="image"><img src="../../images/tagapo.png"></span>';
} elseif ($fetch['barangay'] == 'Malusak') {
    echo '<span class="image"><img src="../../images/malusak.png"></span>';
} elseif ($fetch['barangay'] == 'Balibago') {
    echo '<span class="image"><img src="../../images/balibago.png"></span>';
} elseif ($fetch['barangay'] == 'Caingin') {
    echo '<span class="image"><img src="../../images/caingin.png"></span>';
} elseif ($fetch['barangay'] == 'Pook') {
    echo '<span class="image"><img src="../../images/pooc.png"></span>';
} elseif ($fetch['barangay'] == 'Aplaya') {
    echo '<span class="image"><img src="../../images/aplaya.png"></span>';
} elseif ($fetch['barangay'] == 'Kanluran') {
    echo '<span class="image"><img src="../../images/kanluran.png"></span>';
} else {
    // Default image if the barangay is not matched
    echo '<span class="image"><img src="../../images/logo.png"></span>';
}
?>
                <div class="text logo-text">
                
                    <span class="name"><?php echo $fetch['first_name'] . ' ' . $fetch['last_name']; ?></span>
                    <?php
    if ($fetch['barangay']) {
        echo '<span class="profession">Barangay ' . $fetch['barangay'] . '</span>';
    } else {
        echo '<span class="profession">Not specified</span>'; 
    }
    ?>
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
                            <i class='bx bx-file icon' ></i>
                            <span class="text nav-text">Incident Reports</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="hearings.php">
                            <i class='bx bx-calendar-event icon' ></i>
                            <span class="text nav-text">Hearings</span>
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
                    <a href="../../logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
                
            </div>
        </div>

    </nav>

    
    <section class="home">
        <div class="container">
        <?php
        $incident_case_number = $_GET['incident_case_number'];
        $select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
        $fetch_cases = mysqli_fetch_assoc($select);
        ?>
            <header>NOTICE OF CASE #<?php echo htmlspecialchars(substr($incident_case_number, 0, 9)); ?></header>
            <form action="#">
                <div class="form first">
                    <div class="details personal">
                        <span class="title" style="font-style: italic; margin-top: -5px; text-align: center; font-size: 22px;"><?php echo $fetch_cases['complainant_last_name'] ?> vs. <?php echo $fetch_cases['respondent_last_name'] ?> </span>
                        <hr style="border: 1px solid #ccc; margin: 20px 0; width: 860px;">
                    </div>
                        <div class="fields">
                            <div class="input-field-1" style="width: 53.5rem;">
                            <?php
             $select = mysqli_query($conn, "SELECT * FROM `hearing` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
             if(mysqli_num_rows($select) > 0){
                $fetch = mysqli_fetch_assoc($select);
             }
             $alphabet_month_form = date('M j, Y', strtotime($fetch['date_of_hearing']));
             $time_in_12_hour_format = date('g:i A', strtotime($fetch['time_of_hearing']));
            ?>
                                <label class="">Hearing Schedule</label>
                                <input type="text" onkeypress="return validateName(event)" value="<?php echo $alphabet_month_form; ?> - <?php echo $time_in_12_hour_format; ?>" disabled>
                            </div>
                            <div class="change-schedule" style="margin-left: 86%; font-size: 12px; text-decoration: none;">
                                <a href="change_schedule.php?incident_case_number=<?php echo $incident_case_number; ?>" style="text-decoration: none; color: inherit;">Change Schedule?</a>
                            </div>
                            
                        </div>
            </div>

                    <hr style="border: 1px solid #ccc; margin: 20px 0; margin-top: 25%;">

                    <div class="details ID">
                        <span class="title"></span>
                        <div class="fields">
                            <table class="notice-table" style="width: 100%; margin-left: 1%; margin-top: 1%;">
                                
                                <thead>
                                    <tr style="text-align: center;">
                                        <th>Type of Notice</th>
                                        <th>Resident Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Hearing Notice</td>
                                        <td><?php echo $fetch_cases['complainant_last_name'] ?>, <?php echo $fetch_cases['complainant_first_name'] ?> <?php echo substr($fetch_cases['complainant_middle_name'], 0, 1) ?>.</td>
                                        <td><?php
                                        $check_query = "SELECT generate_hearing, notify_hearing FROM notify_residents WHERE incident_case_number = '$incident_case_number'";
                                        $check_result = mysqli_query($conn, $check_query);

                                        if ($check_result && mysqli_num_rows($check_result) > 0) {
                                            $row = mysqli_fetch_assoc($check_result);
                                            $generate_hearing_value = $row['generate_hearing'];
                                            $notify_complainant_value = $row['notify_hearing'];

                                            if (empty($generate_hearing_value) || $generate_hearing_value === 'not generated') { // Check for the specific value
                                                echo '-';
                                            } elseif ($notify_complainant_value === 'notified') {
                                                echo '<span class="notified">NOTIFIED</span>';
                                            } else {
                                                echo '<span class="to-notify">TO NOTIFY</span>';
                                            }
                                            
                                        } 
                                        ?>
                                        </td>
                                        <td>
                                        <?php
                                        if (empty($generate_hearing_value) || $generate_hearing_value === 'not generated') {
                                            echo '<span class="summon-record" onclick="showHearingPopup()">Generate KP Form #8</span>';
                                        } elseif ($notify_complainant_value === 'notified') {
                                            echo '-';
                                        }
                                        else {
                                            echo '<span class="notify" onclick="showNotifyComplainantPopup()">Set To Notified</span>';
                                        }
                                        ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                            <td>Summon Notice</td>
                                            <td><?php echo $fetch_cases['respondent_last_name'] ?>, <?php echo $fetch_cases['respondent_first_name'] ?> <?php echo substr($fetch_cases['respondent_middle_name'], 0, 1) ?>.</td>
                                            <td>
                                            <?php
                                        $check_query = "SELECT generate_summon, notify_summon FROM notify_residents WHERE incident_case_number = '$incident_case_number'";
                                        $check_result = mysqli_query($conn, $check_query);

                                        if ($check_result && mysqli_num_rows($check_result) > 0) {
                                            $row = mysqli_fetch_assoc($check_result);
                                            $generate_summon_value = $row['generate_summon'];
                                            $notify_summon_value = $row['notify_summon'];

                                            if (empty($generate_summon_value) || $generate_summon_value === 'not generated') { // Check for the specific value
                                                echo '-';
                                            } elseif ($notify_summon_value === 'notified') {
                                                echo '<span class="notified">NOTIFIED</span>';
                                            } else {
                                                echo '<span class="to-notify">TO NOTIFY</span>';
                                            }
                                            
                                        } 
                                        ?>
                                        </td>
<td>
<?php
if (empty($generate_summon_value) || $generate_summon_value === 'not generated') {
    echo '<span class="summon-record" onclick="showSummonPopup()">Generate KP Form #9</span>';
} elseif ($notify_summon_value === 'notified') {
    echo '-';
}
else {
    echo '<span class="notify" onclick="showNotifyRespondentPopup()">Set To Notified</span>';
}
?>

    </td>
</tr>
                                    <tr>
                                        <td>Pangkat Notice</td>
                                        <td>-</td>
                                        <td>
    <?php
    $check_query = "SELECT generate_pangkat, notify_pangkat FROM notify_residents WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $row = mysqli_fetch_assoc($check_result);
        $generate_pangkat = $row['generate_pangkat'];
        $notify_pangkat = $row['notify_pangkat'];

        if (empty($generate_pangkat) || $generate_pangkat === 'not generated') {
            echo '-';
        } elseif ($notify_pangkat === 'notified') {
            echo '<span class="notified">NOTIFIED</span>';
        } else {
            echo '<span class="to-notify">TO NOTIFY</span>';
        }
    }
    ?>
</td>

<td>
<?php
$check_query = "SELECT hearing_type_status FROM hearing WHERE incident_case_number = '$incident_case_number'";
$check_result = mysqli_query($conn, $check_query);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    $row = mysqli_fetch_assoc($check_result);
    $hearing_type_status = $row['hearing_type_status'];

    if (empty($generate_pangkat) || strtolower($generate_pangkat) === 'not generated') {
        if (strtolower($hearing_type_status) === 'conciliation') {
            echo '<span class="summon-record" onclick="showPangkatPopup()">Generate Pangkat Constitution Record</span>';
        } else {
            echo 'CONCILIATION HEARING IS NOT SCHEDULED';
        }
    } elseif ($notify_pangkat === 'notified') {
        echo '-';
    } else {
        echo '<span class="notify" onclick="showNotifyPangkatPopup()">Set To Notified</span>';
    }
}
?>

</td>


                                    </tr>
                                    <!--<tr>
                                        <td>Subpoena Notice</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>
                                        <div class="summon-record" onclick="showWitnessPopup()" style="text-decoration:none;">Add Witness</div>
                                        </td>
                                    </tr>-->
                                </tbody>
                            </table>
                        </div>

                        <hr style="border: 1px solid #ccc; margin: 20px 0;">
                        
                    </div> 
                </div>
            </form>
        </div>


        <div id="summon_popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">GENERATE SUMMON RECORD CONFIRMATION</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 16px; letter-spacing: 1px; text-align: center; margin-top: 10%; margin-bottom: 10%;">Are you sure you want to generate the form?</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closeSummonPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 180px;">NO</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="submit" value="YES" class="backBtn" style="width: 310px; padding: 8px 8px; font-size: 20px; font-weight: 600; margin-left: -5px;"></button>
                </form>
            </div>
            </div>
            </center>
        </div>

        <div id="hearing_popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">GENERATE HEARING NOTICE CONFIRMATION</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 16px; letter-spacing: 1px; text-align: center; margin-top: 10%; margin-bottom: 10%;">Are you sure you want to generate the form?</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closeHearingPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 180px;">NO</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="hearing_submit" value="YES" class="backBtn" style="width: 310px; padding: 8px 8px; font-weight: 600; margin-left: -5px; font-size: 20px;"></button>
                </form>
            </div>
            </div>
            </center>
        </div>

        <div id="pangkat_popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">GENERATE PANGKAT RECORD CONFIRMATION</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 16px; letter-spacing: 1px; text-align: center; margin-top: 10%; margin-bottom: 10%;">Are you sure you want to generate the form?</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closePangkatPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 180px;">NO</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="pangkat_submit" value="YES" class="backBtn" style="width: 310px; padding: 8px 8px; font-size: 20px; font-weight: 600; margin-left: -5px;"></button>
                </form>
            </div>
            </div>
            </center>
        </div>

        <div id="notify_complainant" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">NOTIFY COMPLAINANT</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 15px; letter-spacing: 1px; text-align: center; margin-top: 8%; margin-bottom: 8%;">The complainant's contact number will promptly receive their hearing notice details via message.</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closeNotifyComplainantPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 180px;">NO</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="notify_complainant_submit" value="YES" class="backBtn" style="width: 310px; padding: 8px 8px; font-size: 20px; font-weight: 600; margin-left: -5px;"></button>
                </form>
            </div>
            </div>
            </center>
        </div>

        <div id="notify_respondent" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">NOTIFY RESPONDENT</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 15px; letter-spacing: 1px; text-align: center; margin-top: 8%; margin-bottom: 8%;">The respondent's contact number will promptly receive their hearing notice details via message.</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closeNotifyRespondentPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 180px;">NO</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="notify_respondent_submit" value="YES" class="backBtn" style="width: 310px; padding: 8px 8px; font-size: 20px; font-weight: 600; margin-left: -5px;"></button>
                </form>
            </div>
            </div>
            </center>
        </div>

        <div id="notify_pangkat" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">NOTIFY RESPONDENT FOR PANGKAT</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 15px; letter-spacing: 1px; text-align: center; margin-top: 8%; margin-bottom: 8%;">The respondent's contact number will promptly receive their hearing notice details via message.</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closeNotifyPangkatPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 180px;">NO</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="notify_pangkat_submit" value="YES" class="backBtn" style="width: 310px; padding: 8px 8px; font-size: 20px; font-weight: 600; margin-left: -5px;"></button>
                </form>
            </div>
            </div>
            </center>
        </div>


        <!--<div id="popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">NOTIFY COMPLAINANT</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 14px; text-align: justify;">The complainant's contact number will promptly receive their hearing notice details via message.</p>
            <p style="font-size: 12px; margin-left: -28%; margin-top: 5%; font-weight: 600;">Complainant's Contact Number: </p>
            <div class="box" id="phoneNumberBox">
                <span id="phoneNumberText"></span>
            </div>
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closeNotifyPopup()" style="width: 300px; padding: 12px 12px; font-weight: 600;">BACK</button>
                <button class="backBtn" onclick="submitForm()" style="width: 300px; margin-left: 290px; padding: 12px 12px; font-weight: 600;"">NOTIFY</button>
            </div>
            </div>
            </center>
        </div>-->


    </section>
    
    <script src="search_bar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

function showPopup() {
        // Get the popup element
        var popup = document.getElementById("popup");

        // Display the popup
        popup.style.display = "block";


    }

    function closeNotifyPopup() {
        var popup = document.getElementById("popup");
        popup.style.display = "none";
    }

    function showWitnessPopup() {
        // Get the popup element
        var popup = document.getElementById("witness-popup");

        // Display the popup
        popup.style.display = "block";


    }

    function closePopup() {
        var popup = document.getElementById("witness-popup");
        popup.style.display = "none";
    }

    
    function showSummonPopup() {
        var popup = document.getElementById("summon_popup");
        popup.style.display = "block";


    }

    function closeSummonPopup() {
        var popup = document.getElementById("summon_popup");
        popup.style.display = "none";
    }

    function showHearingPopup() {
        var popup = document.getElementById("hearing_popup");
        popup.style.display = "block";


    }

    function closeHearingPopup() {
        var popup = document.getElementById("hearing_popup");
        popup.style.display = "none";
    }

    function showPangkatPopup() {
        var popup = document.getElementById("pangkat_popup");
        popup.style.display = "block";


    }

    function closePangkatPopup() {
        var popup = document.getElementById("pangkat_popup");
        popup.style.display = "none";
    }

    function showNotifyComplainantPopup() {
        var popup = document.getElementById("notify_complainant");
        popup.style.display = "block";


    }

    function closeNotifyComplainantPopup() {
        var popup = document.getElementById("notify_complainant");
        popup.style.display = "none";
    }

    function showNotifyRespondentPopup() {
        var popup = document.getElementById("notify_respondent");
        popup.style.display = "block";


    }

    function closeNotifyRespondentPopup() {
        var popup = document.getElementById("notify_respondent");
        popup.style.display = "none";
    }

    function showNotifyPangkatPopup() {
        var popup = document.getElementById("notify_pangkat");
        popup.style.display = "block";


    }

    function closeNotifyPangkatPopup() {
        var popup = document.getElementById("notify_pangkat");
        popup.style.display = "none";
    }





</script>
<script src="../script.js"></script>
<style>
    .container{
        margin-left: 15%; margin-top: 45px;
    }
    .title::before{
        content: "";
        background: transparent;
    }

    form .fields .input-field-1{
    display: flex;
    width: calc(100% / 1 - 15px);
    flex-direction: column;
    margin: 4px 0;
        }

    .notify{
        background: #fff;
        padding: 4px 4px;
        color: #2962ff;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 70%;
        margin-left: 15%;
    }

    .notify:hover{
        background: #2962ff;
        color: #fff;
        transition: .5s;
    }

    .change-schedule:hover{
        font-weight: 600;
        transition: .5s linear;
    }

    .summon-record{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 70%;
        margin-left: 15%;
    }
    

    td{
        font-size: 12px;
        text-align: center;
        text-transform: uppercase;
        margin-top: 30px;
        padding: 10px;
    }

    .to-notify{
        font-weight: 900;
        color: #bc1823;
    }

    .notified{
        font-weight: 900;
        color: #0b6623;
    }


    .backBtn:hover{
    background-color: #bc1823;
    }

    .summon-record:hover{
        background: #2e5984;
        transition: .5s;
    }

    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    margin-top: 180px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 500px;
    height: 280px;
    overflow-y: hidden;
}

.box{
    outline: none;
    font-size: 18px;
    font-weight: 400;
    color: #333;
    border-radius: 5px;
    border: 1px solid #aaa;
    padding: 0 40px;
    width: 70%;
    height: 30px;
    margin: 5px 0;
    background-color: #f2f2f2;
}

.generate{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        font-size: 13px;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        width: 198px;
        height: 30px;
        margin-left: 2%;
    }

    .generate:hover{
        background: #0d52bd;
        color: #fff;     
    }

    @media screen and (min-width: 1310px){
    .container{
        margin-left: 14.5%;
        margin-top: 5%;
    }
}

@media screen and (min-width: 1331px){
    .container{
        margin-left: 14.5%;
        margin-top: 3.2%;
    }
}

    @media screen and (min-width: 1360px) and (min-height: 768px) {
        .container{
            margin-top: 7%;
        }
    }

    @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
        .container{
            margin-left: 27%;
            margin-top: 13.5%;
        }
        .modal{
            margin-top: 18%;
        }
    }

    @media screen and (min-width: 1536px) and (min-height: 730px){
        .container{
            margin-top: 6%;
            margin-left: 20%;
        }
        .modal{
            margin-top: 15%;
        }
    }

    @media screen and (min-width: 1366px) and (min-height: 617px){
        .container{
            margin-top: 1.5%;
            margin-left: 15%;
        }
    }


</style>

</body>
</html>
