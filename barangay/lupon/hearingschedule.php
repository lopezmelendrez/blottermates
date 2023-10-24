<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (!isset($email)) {
    header('location: ../../index.php');
    exit; // You should exit after redirecting to prevent further execution
}

// Assuming you have already established the database connection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $date_of_hearing = $_POST['date_of_hearing'];
    $time_of_hearing = $_POST['time_of_hearing'];
    $incident_case_number = $_POST['incident_case_number'];
    $hearing_type_status = 'mediation';

    // Check if a hearing schedule already exists for the incident case number
    $check_query = "SELECT * FROM `hearing` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result) {
        if (mysqli_num_rows($check_result) > 0) {
            // Hearing schedule exists, so update it
            $update_query = "UPDATE `hearing` SET `date_of_hearing` = '$date_of_hearing', `time_of_hearing` = '$time_of_hearing' WHERE incident_case_number = '$incident_case_number'";
            $result = mysqli_query($conn, $update_query);

            if ($result) {
                // Data updated successfully, redirect to a success page or perform other actions
                header("Location: hearings.php");
                exit;
            } else {
                // Error occurred while updating data, handle the error or redirect to an error page
                echo "Error: " . mysqli_error($conn);
                exit;
            }
        } else {
            // Hearing schedule doesn't exist, so insert a new one
            $insert_query = "INSERT INTO `hearing` (`date_of_hearing`, `time_of_hearing`, `incident_case_number`, `hearing_type_status`) VALUES ('$date_of_hearing', '$time_of_hearing', '$incident_case_number', '$hearing_type_status')";
            $result = mysqli_query($conn, $insert_query);

            if ($result) {
                // Data inserted successfully, redirect to a success page or perform other actions
                header("Location: hearings.php");
                exit;
            } else {
                // Error occurred while inserting data, handle the error or redirect to an error page
                echo "Error: " . mysqli_error($conn);
                exit;
            }
        }
    } else {
        // Error occurred while checking for existing data, handle the error or redirect to an error page
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
    <link rel="stylesheet" href="bootstrap/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Create Hearing Schedule</title>
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

        <header style="font-size: 22px;">SCHEDULE FOR CASE #<?php echo $fetch_cases['incident_case_number']; ?></header>
        <form action="" method="post">
            <div class="form first">
                <div class="details personal">
                    <span class="title" style="font-style: italic;"><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></span>
                    <div class="fields">
                        <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">

                        <div class="input-field-1" style="width: 50%;">
                            <label class="">Complainant</label>
                            <input type="text" value="<?php echo $fetch_cases['complainant_last_name']; ?>, <?php echo $fetch_cases['complainant_first_name']; ?> <?php echo $fetch_cases['complainant_middle_name']; ?>." disabled>
                        </div>
                        <div class="input-field-1" style="width: 48%; margin-left: 3px;">
                            <label class="">Respondent</label>
                            <input type="text" placeholder="<?php echo $fetch_cases['respondent_last_name']; ?>, <?php echo $fetch_cases['respondent_first_name']; ?> <?php echo $fetch_cases['respondent_middle_name']; ?>." disabled>
                        </div>
                    </div>
                </div>
                <div class="details ID">
                    <span class="title"></span>
                    <div class="fields">
                        <div class="input-field-1" style="width: 46rem;">
                            <label class="required-label">Select Hearing Schedule</label>
                            <input type="date" name="date_of_hearing" placeholder="" required>
                        </div>
                        <div class="input-field-1" style="width: 46rem;">
                            <label class="required-label">Hearing Time</label>
                            <input type="time" name="time_of_hearing" id="time_of_hearing" placeholder="" required>
                        </div>
                    </div>
                    <button class="submit">
                        <input type="submit" name="submit" value="Appoint Hearing" class="btnText" style="font-size: 14px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">
                        <!--<span class="btnText" style="font-size: 12px;">Create Incident Report Record</span>-->
                    </button>
                </div>
            </div>
        </form>
        </div>

    </section>
    

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

const form = document.querySelector("form"),
        nextBtn = form.querySelector(".nextBtn"),
        backBtn = form.querySelector(".backBtn"),
        allInput = form.querySelectorAll(".first input");


nextBtn.addEventListener("click", ()=> {
    allInput.forEach(input => {
        if(input.value != ""){
            form.classList.add('secActive');
        }else{
            form.classList.remove('secActive');
        }
    })
})

backBtn.addEventListener("click", () => form.classList.remove('secActive'));

// Get the input element by its name
const timeInput = document.getElementById('time_of_hearing');

// Function to check if the time is within the allowed range
function isTimeValid(timeStr) {
    const timeParts = timeStr.split(':');
    const hours = parseInt(timeParts[0]);
    const minutes = parseInt(timeParts[1]);

    // Check if the time is between 8:00 AM (08:00) and 5:00 PM (17:00)
    return (hours > 7 && hours < 17) || (hours === 17 && minutes === 0);
}

// Function to handle time input change
function handleTimeInput() {
    const inputTime = timeInput.value;
    if (!isTimeValid(inputTime)) {
        // If the time is not valid, reset the value to the default (08:00 AM)
        timeInput.value = '08:00';
    }
}

// Attach an event listener to the time input
timeInput.addEventListener('input', handleTimeInput);


    </script>
    <script src="../script.js"></script>
    
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

    </style>

</body>
</html>

