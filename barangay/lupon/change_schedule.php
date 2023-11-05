<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (!isset($email)) {
    header('location: ../../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Assuming you have already established the database connection
    $date_of_hearing = $_POST['date_of_hearing'];
    $time_of_hearing = $_POST['time_of_hearing'];
    $incident_case_number = $_POST['incident_case_number'];

    // Update the hearing schedule with the current timestamp
    $update_query = "UPDATE `hearing` SET `date_of_hearing`='$date_of_hearing', `time_of_hearing`='$time_of_hearing', `schedule_change_timestamp`=NOW() WHERE `incident_case_number`='$incident_case_number'";
    $result = mysqli_query($conn, $update_query);

    if ($result) {
        // Data updated successfully, redirect to a success page or perform other actions
        header("Location: notice_forms.php?incident_case_number=" . $incident_case_number);
        exit;
    } else {
        // Error occurred while updating data, handle the error or redirect to an error page
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
    <title>Change Hearing Schedule</title>
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
        echo '<span class="image"><img src="../../images/ibaba_logo.jpg"></span>';
    } else {
        echo '<span class="image"><img src="../../images/logo.png"></span>';
    }
    ?>
                <div class="text logo-text">
                
                    <span class="name"><?php echo $fetch['first_name'] . ' ' . $fetch['last_name']; ?></span>
                    <?php
    if ($fetch['barangay']) {
        echo '<span class="profession">Barangay ' . $fetch['barangay'] . '</span>';
    } else {
        echo '<span class="profession">Not specified</span>'; // Or handle this case as needed
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
                    <input type="text" placeholder="Search...">
                </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="incident_reports.html">
                            <i class='bx bx-file icon' ></i>
                            <span class="text nav-text">Incident Reports</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-calendar-event icon' ></i>
                            <span class="text nav-text">Hearings</span>
                        </a>
                    </li>


            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="#">
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

                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Dark mode</span>

                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
                
            </div>
        </div>

    </nav>    

    <section class="home">
        <div class="container" style="margin-left: 15%; margin-top: 45px;">
        <?php
        $incident_case_number = $_GET['incident_case_number'];
        $select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
        $fetch_cases = mysqli_fetch_assoc($select);

        $hearing_query = "SELECT date_of_hearing, time_of_hearing FROM `hearing` WHERE `incident_case_number` = '$incident_case_number'";
        $hearing_result = mysqli_query($conn, $hearing_query);
        $hearing_data = mysqli_fetch_assoc($hearing_result);
        ?>

        <header style="font-size: 22px;">SCHEDULE FOR <?php echo $fetch_cases['incident_case_number']; ?></header>
        <form action="" method="post">
            <div class="form first">
                <div class="details personal">
                    <span class="title" style="font-style: italic;"><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></span>
                    <div class="fields">
                        <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">

                        <div class="input-field-1" style="width: 43rem;">
                            <label class="">Complainant</label>
                            <input type="text" value="<?php echo $fetch_cases['complainant_last_name']; ?>, <?php echo $fetch_cases['complainant_first_name']; ?> <?php echo $fetch_cases['complainant_middle_name']; ?>" disabled>
                        </div>
                        <div class="input-field-1" style="width: 43rem;">
                            <label class="">Respondent</label>
                            <input type="text" placeholder="<?php echo $fetch_cases['respondent_last_name']; ?>, <?php echo $fetch_cases['respondent_first_name']; ?> <?php echo $fetch_cases['respondent_middle_name']; ?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="details ID">
                    <span class="title"></span>
                    <div class="fields">
                        <div class="input-field-1" style="width: 43rem;">
                            <label class="required-label">Select Hearing Schedule</label>
                            <input type="date" name="date_of_hearing" placeholder="" value="<?php echo $hearing_data['date_of_hearing']; ?>" required>
                        </div>
                        <div class="input-field-1" style="width: 43rem;">
                            <label class="required-label">Hearing Time</label>
                            <input type="time" name="time_of_hearing" id="time_of_hearing" placeholder="" value="<?php echo $hearing_data['time_of_hearing']; ?>" required>
                        </div>
                    </div>
                    <button class="submit">
                        <input type="submit" name="submit" value="Update Schedule" class="btnText" style="font-size: 14px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">
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

    </style>
    

</body>
</html>