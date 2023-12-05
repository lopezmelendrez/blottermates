<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $date_of_hearing = $_POST['date_of_hearing'];
    $time_of_hearing = $_POST['time_of_hearing'];
    $incident_case_number = $_POST['incident_case_number'];
    $hearing_type_status = 'mediation'; 

    $insert_query = "INSERT INTO `hearing` (`incident_case_number`, `date_of_hearing`, `time_of_hearing`, `hearing_type_status`, `timestamp`)
                     VALUES ('$incident_case_number', '$date_of_hearing', '$time_of_hearing', '$hearing_type_status', NOW())";

    $result = mysqli_query($conn, $insert_query);
    if ($result) {
        header("Location: notice_forms.php?incident_case_number=" . $incident_case_number);
        exit;
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Set Hearing Schedule</title>
</head>
<body>
    
<?php include 'navbar.php'; ?>

    <section class="home">
        <div class="container" style="margin-left: 15%; margin-top: 80px; width: 950px; height: 475px;">
        <?php
        // Assuming you have already established the database connection
        $incident_case_number = $_GET['incident_case_number'];
        $select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
        $fetch_cases = mysqli_fetch_assoc($select);
        ?>

<header style="font-size: 22px;">SCHEDULE FOR CASE #<?php echo htmlspecialchars(substr($fetch_cases['incident_case_number'], 0, 9)); ?></header>
        <form action="" method="post">
            <div class="form first">
                <div class="details personal">
                    <span class="title" style="font-style: italic; font-size: 24px; text-align: center; margin-bottom: 20px;"><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></span>
                    <div class="fields">
                        <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">

                        <div class="input-field-1" style="width: 49%;">
                            <label class="">Complainant</label>
                            <input type="text" value="<?php echo $fetch_cases['complainant_last_name']; ?>, <?php echo $fetch_cases['complainant_first_name']; ?> <?php echo substr($fetch_cases['complainant_middle_name'], 0, 1); ?>." disabled>
                        </div>
                        <div class="input-field-1" style="width: 49%;">
                            <label class="">Respondent</label>
                            <input type="text" placeholder="<?php echo $fetch_cases['respondent_last_name']; ?>, <?php echo $fetch_cases['respondent_first_name']; ?> <?php echo substr($fetch_cases['respondent_middle_name'], 0, 1); ?>." disabled>
                        </div>
                    </div>
                </div>
                <div class="details ID">
                    <span class="title"></span>
                    <div class="fields">
                    <div class="input-field-1" style="width: 57.8rem; margin-top: 15px;">
                    <label class="required-label">Hearing Date</label>
                    <input type="text" name="date_of_hearing" id="datepicker" placeholder="" required readonly>
                    </div>
                    <div class="input-field-1" style="width: 57.8rem; margin-top: 15px;">
                        <label class="required-label">Hearing Time</label>
                        <select name="time_of_hearing" readonly>
                            <option disabled selected>Select Hearing Schedule...</option>
                        </select>
                    </div>
                    </div>

                    <button class="pop-up" style="margin-right: 1px;">
                            <span class="btnText" style="background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">APPOINT HEARING</span>
                    </button>
                    <!--<button class="submit">
                        <input type="submit" name="submit" value="Appoint Hearing" class="btnText" style="font-size: 14px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">
                    </button>-->
                </div>
            </div>

            <div class="modal-overlay" id="confirmationModal">
                    <div class="modal-content">
                    <h3 class="modal-title" style="font-size: 20px; text-align:center;">CONFIRM HEARING SCHEDULE</h3>
                    <hr style="border: 1px solid #ccc; margin: 10px 0;">
                    <div class="inputfield">
                    <label style="text-align: left;">Date of Hearing</label>
                    <div class="text-box">
                    <p id="dateOfHearing"></p>
                    </div>
                    <div class="inputfield" style="margin-top: 12%;">
                    </div><label>Time of Hearing</label>
                    <div class="text-box">
                    <p id="timeOfHearing"></p>
                    </div>
                    </div>
                        
                        <div id="popup" class="popup">
            
                            <div class="modal-buttons" style="display: flex; align-items: center; margin-top: 7.5%; margin-right: 1px;">
                            <div class="backBtn" id="modalCancelBtn" style="padding: 12px 12px; width: 100px; border: 1px solid #bc1823; background: #fff; color: #bc1823; margin-left: 33%;">
                                <span class="btnText" style="margin-left: -5px;">Back</span>
                            </div>
                            <button class="modal-confirm" id="modalConfirmBtn" name="submit">
                            <input type="submit" name="submit" value="Submit" class="btnText" style="font-size: 16px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">
                            </button>

                        </div>
                </div>
            </div>

        </form>
        </div>

    </section>

    <script>

$(function () {
    $("#datepicker").datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0,
        maxDate: '+3w',
        beforeShowDay: function (date) {
            var day = date.getDay();
            return [day !== 0 && day !== 6, ''];
        }
    });

    $('select[name="time_of_hearing"]').prop('disabled', true);

    $('input[name="date_of_hearing"]').change(function () {
        var selectedDate = $(this).val();

        if (selectedDate !== "") {
            // Fetch available times from the server
            $.ajax({
                type: 'POST',
                url: 'fetch_available_times.php',
                data: { selectedDate: selectedDate },
                success: function (response) {
                    // Enable the dropdown
                    var timeSelect = $('select[name="time_of_hearing"]');
                    timeSelect.prop('disabled', false);

                    // Parse the response as JSON
                    var availableTimes = JSON.parse(response);

                    // Clear existing options
                    timeSelect.empty();

                    // Populate the dropdown with options
                    if (availableTimes.length > 0) {
                        timeSelect.append('<option disabled selected>Available Timeslots</option>');

                        availableTimes.forEach(function (time) {
                            // Format the time as '3:00 PM' using JavaScript
    var formattedTime = formatTime(time);

// Append the formatted time to the dropdown
timeSelect.append('<option value="' + time + '">' + formattedTime + '</option>');
                        });
                    } else {
                        timeSelect.append('<option disabled selected>No available times</option>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        } else {
            // Disable the dropdown if no date is selected
            $('select[name="time_of_hearing"]').prop('disabled', true);
        }
    });
});

function formatTime(rawTime) {
    var timeParts = rawTime.split(':');
    var hours = parseInt(timeParts[0], 10);
    var minutes = timeParts[1];

    // Use the modulo operator to convert 24-hour time to 12-hour time
    var amPm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;

    // Pad single-digit minutes with a leading zero
    minutes = minutes.padStart(2, '0');

    // Construct the formatted time
    var formattedTime = hours + ':' + minutes + ' ' + amPm;
    
    return formattedTime;
}

document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const popUpButton = form.querySelector(".pop-up");
    const submitInput = form.querySelector("input[type='submit']");
    const modalOverlay = document.getElementById("confirmationModal");
    const modalCancelBtn = document.getElementById("modalCancelBtn");
    const modalConfirmBtn = document.getElementById("modalConfirmBtn");

    // Open modal when the pop-up button is clicked
    popUpButton.addEventListener("click", function(event) {
        event.preventDefault();

        // Check if date_of_hearing has a value
        const dateOfHearingInput = document.querySelector('input[name="date_of_hearing"]');
        if (dateOfHearingInput && dateOfHearingInput.value.trim() !== "") {
            var rawDate = dateOfHearingInput.value;
            var formattedDate = formatDate(rawDate);
            document.getElementById("dateOfHearing").textContent = formattedDate;
            document.getElementById("timeOfHearing").textContent = document.querySelector('select[name="time_of_hearing"] option:checked').text;

            modalOverlay.style.display = "flex";
        } else {
            // Do nothing if date_of_hearing has no value
        }
    });

    // Close modal when cancel button is clicked
    modalCancelBtn.addEventListener("click", function() {
        modalOverlay.style.display = "none";
    });

    // Submit the form when confirm button is clicked
    modalConfirmBtn.addEventListener("click", function() {
        submitInput.click(); // Trigger the submit input element's click event
    });
});



        function formatDate(rawDate) {
        var options = { year: 'numeric', month: 'long', day: 'numeric' };
        var formattedDate = new Date(rawDate).toLocaleDateString(undefined, options);
        return formattedDate;
    }




    </script>
    
    <style>
    .container form{
    position: relative;
    margin-top: 16px;
    min-height: 400px;
    background-color: #fff;
    overflow: hidden;
}

.container header::before{
    content: "";
    position: absolute;
    left: 0;
    bottom: -2px;
    height: 3px;
    width: 360px;
    border-radius: 8px;
    background-color: #F5BE1D;
}

.modal-overlay {
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

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        margin-top: 15px;
        margin-left: 10%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 500px;
        height: 300px;
        overflow-y: hidden;
    }

    .modal-buttons {
        position: relative;
        text-align: center;
        margin-top: 10px;
    }

    .modal-cancel,
    .modal-confirm {
        padding: 8px 20px;
        margin: 0 10px;
        border: none;
        cursor: pointer;
    }

    .text-box{
    outline: none;
    font-size: 18px;
    font-weight: 400;
    color: #333;
    border-radius: 5px;
    border: 1px solid #aaa;
    padding: 0 5px;
    height: 35px;
    margin: 8px 0;
    width: 460px;
    position: fixed;
}

.text-box p{
    text-align: left;
    margin-left: 5px;
    margin-top: 1px;
}

label{
    font-size: 16px;
    font-weight: 500;
    color: #2e2e2e;
    text-align: center;
    margin-top: -15px;
}

    </style>

</body>
</html>
