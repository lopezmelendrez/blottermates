<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

function displayPage($conn, $incident_case_number)
{
    // Check if the incident case number exists in the amicable_settlement table
    $check_query = "SELECT * FROM amicable_settlement WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        // If the incident case number is found in the amicable_settlement table, redirect to settled_cases.php
        header('location: settled_cases.php');
        exit(); // Ensure that the script stops executing after the redirection
    }

    // Continue with the rest of your existing code for the hearing table
    // Construct a SELECT query to fetch information from the 'hearing' table
    $select_query = "SELECT hearing_type_status, date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'";

    // Execute the SELECT query using the provided database connection ($conn)
    $result = mysqli_query($conn, $select_query);

    // Check if the query was successful and if there is at least one row in the result set
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the first row from the result set as an associative array
        $row = mysqli_fetch_assoc($result);

        // Extract values from the associative array
        $hearing_type_status = $row['hearing_type_status'];
        $date_of_hearing = $row['date_of_hearing'];

        // Get the current date in 'Y-m-d' format
        $current_date = date('Y-m-d');

        if ($hearing_type_status === 'conciliation' && strtotime($date_of_hearing) <= strtotime($current_date)) {
            // If the conditions are met, return true
            return true;
        }
    }

    // If the conditions are not met or there was an issue with the query, return false
    return false;
}


$incident_case_number = $_GET['incident_case_number'];

if (!displayPage($conn, $incident_case_number)) {
    header('location: ongoing_cases.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arbitration_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $hearing_type_status = 'arbitration';

    // Update the `hearing` table and set `arbitration_timestamp` to the current timestamp
    $update_query = "UPDATE `hearing` SET `hearing_type_status` = '$hearing_type_status', `date_of_hearing` = NULL, `time_of_hearing` = NULL, `arbitration_timestamp` = NOW() WHERE incident_case_number = '$incident_case_number'";
    $result = mysqli_query($conn, $update_query);

    if ($result) {
        if (mysqli_affected_rows($conn) > 0) {
            header("Location: arbitration_hearings.php");
            exit;
        } else {
            // The row did not exist; you might want to handle this case or ignore it
            echo "Row not found.";
            exit;
        }
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['court_action_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $hearing_type_status = 'filed to court action';

    $update_query = "UPDATE `hearing` SET `hearing_type_status` = '$hearing_type_status' WHERE incident_case_number = '$incident_case_number'";
    $result = mysqli_query($conn, $update_query);

    if ($result) {
        if (mysqli_affected_rows($conn) > 0) {
            header("Location: file_court_action.php?incident_case_number=$incident_case_number");
            exit;
        } else {
            // The row did not exist, you might want to handle this case or ignore it
            echo "Row not found.";
            exit;
        }
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agreement_description = $_POST['agreement_description'];
    $incident_case_number = $_POST['incident_case_number'];

    $select_hearing_id_query = "SELECT hearing_id FROM hearing WHERE incident_case_number = '$incident_case_number'";
    $select_hearing_id_result = mysqli_query($conn, $select_hearing_id_query);

    if ($select_hearing_id_result && mysqli_num_rows($select_hearing_id_result) > 0) {
        $fetch_hearing = mysqli_fetch_assoc($select_hearing_id_result);
        $hearing_id = $fetch_hearing['hearing_id'];

        // Update your INSERT query to include the `timestamp` column and set it to the current timestamp
        $insert_query = "INSERT INTO `amicable_settlement` (`agreement_description`, `hearing_id`, `incident_case_number`, `timestamp`)
        VALUES ('$agreement_description', '$hearing_id', '$incident_case_number', NOW())";

        $insert_result = mysqli_query($conn, $insert_query);

        if ($insert_result) {
            header("Location: settled_cases.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
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
    <title>Conciliation Hearing Record</title>
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
            <header>Conciliation Hearing Record for Case <?php echo htmlspecialchars(substr($fetch_cases['incident_case_number'], 0, 9)); ?></header>
            <form action="#" method="post">
            <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
            <span class="title" style="font-style: italic; font-size: 23px; text-align: center;"><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?> </span>
                        <div class="fields">
                            <div class="input-field-1">
                                <label>Complainant</label>
                                <input type="text"  onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['complainant_last_name']; ?>, <?php echo $fetch_cases['complainant_first_name']; ?> <?php echo substr($fetch_cases['complainant_middle_name'], 0, 1); ?>." disabled readonly>
                            </div>
                            <div class="input-field-1">
                                <label>Respondent</label>
                                <input type="text"  onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['respondent_last_name']; ?>, <?php echo $fetch_cases['respondent_first_name']; ?> <?php echo substr($fetch_cases['respondent_middle_name'], 0, 1); ?>." disabled readonly>
                            </div>
                            <!--<div class="input-field">
                                <label>Witness</label>
                                <input type="text" onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['witness_last_name'] ? ($fetch_cases['witness_last_name'] . ', ' . $fetch_cases['witness_first_name'] . ' ' . $fetch_cases['witness_middle_name']) : 'NO WITNESS'; ?>" required readonly>
                            </div>-->
                            <div class="input-field-1" style="width: 54rem;">
                                <label class="required-label">Amicable Settlement Agreement</label>
                                <input type="text" style="height: 5rem;" name="agreement_description" placeholder="" required>
                            </div>
                        </div>
                        <div class="proceed-spans">
                            <span class="arbitration" onclick="showArbitrationPopup()" style="cursor:pointer;">Proceed to Arbitration</span>
                            <span class="filecourt-action" onclick="showCourtActionPopup()" style="cursor: pointer;">File Court Action</span>
                        </div>
                        <button class="submit">
                                <input type="submit" value="Create Agreement" class="btnText" style="font-size: 17px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">
                        </button>
            </form>
        </div>

    </section>

    <div id="arbitration_popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">ARE YOU SURE?</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 18px; text-align: center; margin-top: 3%;">By clicking the "Confirm" button, you will initiate the progression of the CONCILIATION record into the ARBITRATION process.</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <div class="button-container" style="display: flex;">
            <button class="backBtn" onclick="closeArbitrationPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 170px;">CANCEL</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="arbitration_submit" value="CONFIRM" class="backBtn" style="width: 310px; padding: 12px 12px; font-weight: 600; margin-left: -5px;"></button>
                </form>
            </div>
            </div>
            </center>
    </div>

    <div id="courtaction_popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">ARE YOU SURE?</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 18px; text-align: center; margin-top: 3%;">By clicking the "Confirm" button, you will initiate the progression of the CONCILIATION record to "File Court Action".</p>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <div class="button-container" style="display: flex;">
            <button class="backBtn" onclick="closeCourtActionPopup()" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 180px;">NO</button>
                <form action="" method="post">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                <input type="submit" name="court_action_submit" value="CONFIRM" class="backBtn" style="width: 310px; padding: 12px 12px; font-weight: 600; margin-left: -5px;"></button>
                </form>
            </div>
            </div>
            </center>
    </div>

    <div id="autoPopup" class="popup-1">
            <center>
            <div class="modal" style="width: 600px; height: 270px;">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">CONCILIATION HEARING</h3>
            <hr style="border: 1px solid #ccc; margin: 25px 0;">
            <p style="font-size: 17px; text-align: center; margin-top: 5%; margin-bottom: 5%; letter-spacing: 1; text-transform: uppercase;">Would you like to proceed or reschedule the hearing?</p>
            <hr style="border: 1px solid #ccc; margin: 25px 0;">
            <div class="button-container" style="display: flex; margin-top: -3%;">
            <a href="change_schedule.php?incident_case_number=<?php echo $incident_case_number; ?>" style="text-decoration: none;">
            <button class="backBtn" style="width: 150px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 120px; font-size: 19px;">RESCHEDULE</button>
            </a>    
            <input type="submit" onclick="closeAutoPopup()" value="PROCEED" class="backBtn" style="width: 310px; padding: 5px 5px; font-weight: 600; margin-left: -5px; font-size: 19px;"></button>
            </div>
            </div>
            </center>
    </div>


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

    function showArbitrationPopup() {
        // Get the popup element
        var popup = document.getElementById("arbitration_popup");

        // Display the popup
        popup.style.display = "block";


    }

    function closeArbitrationPopup() {
        var popup = document.getElementById("arbitration_popup");
        popup.style.display = "none";
    }

    function showCourtActionPopup() {
        // Get the popup element
        var popup = document.getElementById("courtaction_popup");

        // Display the popup
        popup.style.display = "block";


    }

    function closeCourtActionPopup() {
        var popup = document.getElementById("courtaction_popup");
        popup.style.display = "none";
    }

    function closeAutoPopup() {
        var popup = document.getElementById("autoPopup");
        popup.style.display = "none";
    }


    
    </script>
    <script src="../script.js"></script>

    <style>
        .container{
            margin-left: 15%; margin-top: 8%; height: 29rem;
        }

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

    .popup-1 {
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
    width: 488px;
    border-radius: 8px;
    background-color: #F5BE1D;
}

.backBtn:hover{
    background-color: #bc1823;
    }

    @media screen and (min-width: 1360px) and (min-height: 768px){
        .container{
            margin-top: 12%;
        }
    }

    @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
        .container{
            margin-left: 25%;
            margin-top: 16%;
        }

        .modal{
            margin-top: 19.5%;
        }
    }

    @media screen and (min-width: 1340px) and (max-width: 1360px){
            .container{
                width: 60%;
                margin-left: 20%;
                margin-top: 6.5%;
            }
}

    @media screen and (min-width: 1536px) and (min-height: 730px){
        .container{
            margin-left: 20%;
            margin-top: 10%;
        }

        .modal{
            margin-top: 14.5%;
            margin-left: 5%;
        }
    }

    @media screen and (min-width: 1366px) and (min-height: 617px){
        .container{
            width: 60%;
            margin-top: 5.8%;
            margin-left: 20%;
        }
        .modal{
            margin-left: 5%;
            margin-top: 12.5%;
        }
    }

    @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
        .container{
            margin-top: 4.5%;
            margin-left: 18%;
            width: 65%;
        }
        .modal{
            margin-top: 11.5%;
            margin-left: 4.5%;
        }
    }

    @media screen and (min-width: 1360px) and (min-height: 681px){
        .container{
            width: 70%;
        }
    }

    @media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
        .container{
            width: 60%;
            margin-top: 9%;
        }
        .modal{
            margin-top: 14%;
        }
    }

    @media screen and (max-width: 2133px) and (min-height: 1055px) and (max-height: 1058px){
        .container{
            width: 40%;
            margin-left: 30.5%;
            margin-top: 12%;
        }
        .modal{
            margin-top: 15.3%;
        }
    }


    </style>

</body>
</html>
