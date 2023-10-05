<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

// Fetch the hearing_id based on incident_case_number
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agreement_description = $_POST['agreement_description'];
    

    // Assuming incident_case_number is the unique identifier for the hearing record
    $incident_case_number = $_POST['incident_case_number'];

    $select_hearing_id_query = "SELECT hearing_id FROM hearing WHERE incident_case_number = '$incident_case_number'";
    $select_hearing_id_result = mysqli_query($conn, $select_hearing_id_query);

    if ($select_hearing_id_result && mysqli_num_rows($select_hearing_id_result) > 0) {
        $fetch_hearing = mysqli_fetch_assoc($select_hearing_id_result);
        $hearing_id = $fetch_hearing['hearing_id'];

        // Insert the agreement_description and hearing_id into the amicable_settlement table
        $insert_query = "INSERT INTO `amicable_settlement` (`agreement_description`, `hearing_id`, `incident_case_number`)
        VALUES ('$agreement_description', '$hearing_id', '$incident_case_number')";


        $insert_result = mysqli_query($conn, $insert_query);

        if ($insert_result) {
            // Insertion successful, redirect back to the dashboard or any other desired page
            header("Location: settled_cases.php");
            exit;
        } else {
            // Insertion failed, handle the error accordingly
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Handle the case where the agreement is not found, you can redirect back or show an error message.
        echo "Error: Hearing record not found for the incident case number.";
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
    <title>Mediation Hearing Record</title>
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
        <div class="container" style="margin-left: 15%; margin-top: 8%; height: 23rem;">
        <?php
        // Assuming you have already established the database connection
        $incident_case_number = $_GET['incident_case_number'];
        $select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
        $fetch_cases = mysqli_fetch_assoc($select);
        ?>
            <header>Mediation Hearing Record for Case <?php echo $fetch_cases['incident_case_number']; ?></header>
            <form action="#" method="post">
            <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
            <span class="title" style="font-style: italic;"><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?> </span>
                        <div class="fields">
                            <div class="input-field-1">
                                <label>Complainant</label>
                                <input type="text"  onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['complainant_last_name']; ?>, <?php echo $fetch_cases['complainant_first_name']; ?> <?php echo $fetch_cases['complainant_middle_name']; ?>" disabled readonly>
                            </div>
                            <div class="input-field-1">
                                <label>Respondent</label>
                                <input type="text"  onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['respondent_last_name']; ?>, <?php echo $fetch_cases['respondent_first_name']; ?> <?php echo $fetch_cases['respondent_middle_name']; ?>" disabled readonly>
                            </div>
                            <!--<div class="input-field">
                                <label>Witness</label>
                                <input type="text" onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['witness_last_name'] ? ($fetch_cases['witness_last_name'] . ', ' . $fetch_cases['witness_first_name'] . ' ' . $fetch_cases['witness_middle_name']) : 'NO WITNESS'; ?>" required readonly>
                            </div>-->
                            <div class="input-field-1" style="width: 43rem;">
                                <label class="required-label">Amicable Settlement Agreement</label>
                                <input type="text" style="height: 5rem;" name="agreement_description" placeholder="" required>
                            </div>
                        </div>
                        <div class="proceed-spans">
                            <span class="conciliation" onclick="showPopup()">Proceed to Conciliation</span>
                            <span class="arbitration">Proceed to Arbitration</span>
                            <span class="filecourt-action">File Court Action</span>
                        </div>
                        <button class="submit">
                                <input type="submit" name="submit" value="Create Settlement Agreement" class="btnText" style="font-size: 12px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">
                        </button>
            </form>
        </div>

    </section>

    <div id="popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">ARE YOU SURE?</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 18px; text-align: center; margin-top: 10%;">By clicking the "Confirm" button, you will initiate the progression of the mediation record into the conciliation process.</p>
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closePopup()" style="width: 300px; padding: 12px 12px; font-weight: 600;">BACK</button>
                <button class="backBtn" onclick="submitForm()" style="width: 300px; margin-left: 270px; padding: 12px 12px; font-weight: 600;"">CONFIRM</button>
            </div>
            </div>
            </center>
</div>


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

modeSwitch.addEventListener("click" , () =>{
    body.classList.toggle("dark");
    
    if(body.classList.contains("dark")){
        modeText.innerText = "Light mode";
    }else{
        modeText.innerText = "Dark mode";
        
    }
});

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


function validateName(event) {
            var keyCode = event.keyCode;

            // Allow letters (uppercase and lowercase)
            if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122)) {
                return true;
            }

            // Allow space, period, hyphen, and tab key
            if (keyCode === 32 || keyCode === 46 || keyCode === 45 || keyCode === 9) {
                return true;
            }

            event.preventDefault();
            return false;
        }

        const modal = document.getElementById('confirmationModal');

        function showPopup() {
        // Get the popup element
        var popup = document.getElementById("popup");

        // Display the popup
        popup.style.display = "block";


    }

    function closePopup() {
        var popup = document.getElementById("popup");
        popup.style.display = "none";
    }
    
    </script>
    <script src="../script.js"></script>

    <style>
        .title::before{
            content: "";
            background: transparent;
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


.container form{
    position: relative;
    margin-top: 16px;
    min-height: 390px;
    background-color: #fff;
    overflow: hidden;
}

.container header::before{
    content: "";
    position: absolute;
    left: 0;
    bottom: -2px;
    height: 3px;
    width: 450px;
    border-radius: 8px;
    background-color: #F5BE1D;
}

    </style>

</body>
</html>
