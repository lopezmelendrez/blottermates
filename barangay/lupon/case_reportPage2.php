<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (!isset($email)) {
    header('location: ../../index.php');
}

$generate_execution = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execution_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $generate_execution = 'form generated';

    $manilaTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $created_at = $manilaTime->format('Y-m-d H:i:s');

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE `notify_residents` SET `generate_execution` = '$generate_execution', `generated_execution_timestamp` = '$created_at'  WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `generate_execution`, `generated_execution_timestamp`)
                         VALUES ('$incident_case_number', '$generate_execution', '$created_at')";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        $incident_case_number = $_POST['incident_case_number'];
        echo '<script>';
        echo 'window.open("http://brgyblotter-src.online/tcpdf/motion_for_execution.php?incident_case_number=' . $incident_case_number . '", "_blank");';
        echo 'window.location.href = window.location.href;';
        echo '</script>';
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
    <link rel="stylesheet" href="bootstrap/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Case Report Summary</title>
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
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                <header class="card-title" style="font-size: 18px;">Case Report Summary of Case #<?php echo htmlspecialchars(substr($incident_case_number, 0, 9)); ?></header>
                    <span class="generate" onclick="showPDFPopup()" style="text-decoration: none;"><i class="fa-solid fa-file-pdf" style="margin-right: 5px;"></i>Generate PDF Forms</span>
                    <p style="font-size: 15px; font-style: italic; margin-top: -5px;"><?php echo $fetch_cases['complainant_last_name'] ?> vs. <?php echo $fetch_cases['respondent_last_name'] ?></p>
                    <hr style="border: 1px solid #ccc; margin: 20px 0;">
                </div>
            <form action="#">
                <div class="form first">
                    <div class="details personal">
                        <div class="fields">
                        <span class="title" style="width: 100%; margin-top: -5px;">Hearing Information</span>
                            <?php
             $select = mysqli_query($conn, "SELECT * FROM `hearing` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
             if(mysqli_num_rows($select) > 0){
                $fetch = mysqli_fetch_assoc($select);
             }
             $hearing_date = date('M j, Y', strtotime($fetch['date_of_hearing']));
             $formatted_time = date('g:i A', strtotime($fetch['time_of_hearing']));
             $hearing_status = $fetch['hearing_type_status'];
            ?>
                            <div class="input-field">
                                <label class="label">Hearing Status</label>
                                <div class="text-box">
                                    <p style="padding: 10px 0; text-transform: uppercase;"><?php echo $hearing_status ?></p></div>
                            </div>
                            <div class="input-field">
                                <label class="label">Date of Hearing</label>
                                <div class="text-box">
                                    <p style="padding: 10px 0"><?php echo $formatted_time ?> - <?php echo $hearing_date?></p></div>
                            </div>
                            <?php
$select = mysqli_query($conn, "SELECT * FROM `amicable_settlement` WHERE `incident_case_number` = '$incident_case_number'") or die('query failed');
if(mysqli_num_rows($select) > 0){
    $fetch = mysqli_fetch_assoc($select);
    $date_agreed = $fetch['date_agreed'];
    $agreed_date = date("F j, Y", strtotime($date_agreed));
    $agreement_description = $fetch['agreement_description'];
} else {
    $agreed_date = "NO SETTLEMENT YET";
    $agreement_description = "NO SETTLEMENT YET";
}
?><div class="input-field">
<label class="label">Date of Settlement</label>
<div class="text-box">
    <p style="padding: 10px 0"><?php echo $agreed_date ?></p></div>
</div>
<div class="input-field" style="width: 100%; position: relative;">
<label class="label">Final Agreement</label>
<div class="text-box" style="height: 5.5rem;">
<p style="padding: 10px 0"><?php echo ucfirst($agreement_description); ?></p></div>
</div>

<span class="title" style="width: 100%;">Execution of Agreement</span>

                            
<?php
// Assuming you have a connection to the database ($conn)
$generate_execution_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
$generate_execution_result = mysqli_query($conn, $generate_execution_query);

if ($generate_execution_result && mysqli_num_rows($generate_execution_result) > 0) {
    $generate_execution_data = mysqli_fetch_assoc($generate_execution_result);
    
    // Check the value of generate_execution
    $generate_execution_value = $generate_execution_data['generate_execution'];

    if ($generate_execution_value == '' || strtolower($generate_execution_value) == 'not generated') {
        // Display "PLEASE FILE A MOTION FIRST"
        echo '<p class="file" style="padding: 10px 0">PLEASE GENERATE A MOTION FOR EXECUTION</p>';
    } elseif ($generate_execution_value == 'form generated') {
        // Check if there is data in the execution_notice table
        $execution_query = "SELECT * FROM `execution_notice` WHERE incident_case_number = '$incident_case_number'";
        $execution_result = mysqli_query($conn, $execution_query);

        if ($execution_result && mysqli_num_rows($execution_result) > 0) {
            // Display the fields from execution_notice
            $execution_data = mysqli_fetch_assoc($execution_result);
            ?>
            
            <span class="generate" style="font-size: 14px; text-align: center; margin-top: -3%;"><a href="../../tcpdf/notice_of_execution.php?incident_case_number=<?php echo $incident_case_number; ?>" target="_blank" style="text-decoration: none; cursor: pointer; color: white;">Generate KP Form #27</a></span>
            <div class="input-field-1">
                <label class="label">Date of Agreement Execution</label>
                <div class="text-box">
                <p style="padding: 10px 0"><?php echo date('F j, Y', strtotime($execution_data['execution_date'])); ?></p>
                </div>
            </div>

            <div class="input-field-1">
                <label class="label">Compliance Status</label>
                <div class="text-box">
                    <p style="padding: 10px 0"><?php echo $execution_data['compliance_status']; ?></p>
                </div>
            </div>

            <div class="input-field" style="width: 100%; position: relative;">
                <label class="label">Remarks</label>
                <div class="text-box">
                <p style="padding: 10px 0"><?php echo ucfirst($execution_data['remarks']); ?></p>
                </div>
            </div>
            <?php
        } else {
            // Display "THE MOTION FOR EXECUTION IS SUBMITTED TO THE BARANGAY FOR VALIDATION"
            echo '<p class="pending" style="padding: 10px 0">THE MOTION FOR EXECUTION IS SUBMITTED TO THE PUNONG BARANGAY FOR VALIDATION</p>';
        }
    }
} else {
    // No else block needed to display default content
}
?>



                        </div>
                       
                        
                    </div> 
                </div>
                
                </form>

                <div id="pdf_popup" class="popup">
                <div class="close-icon" onclick="closePDFPopup()">
                <i class='bx bxs-x-circle' ></i> <!-- Replace with the desired close icon -->
    </div>
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">SELECT PDF TO GENERATE</h3>
            <hr style="border: 1px solid #ebecf0; margin: 10px 0;">
            <p style="font-size: 14px; text-align: left;">
    Amicable Settlement Form (KP #16)
    <a href="../../tcpdf/amicable_settlement_form.php?incident_case_number=<?php echo $incident_case_number; ?>" target="_blank" class="button" style="margin-left: 17.5%;">
      Generate
    </a>
    <span class="printer-icon">
      <i class='bx bxs-printer'></i>
    </span>
  </p>
  <hr style="border: 1px solid #ccc; margin: 10px 0;">
  <p style="font-size: 14px; text-align: left;">
    Certification to File Action (KP #20)
    <?php
    $check_hearing_query = "SELECT * FROM `court_action` WHERE incident_case_number = '$incident_case_number'";
    $check_hearing_result = mysqli_query($conn, $check_hearing_query);

    if ($check_hearing_result && mysqli_num_rows($check_hearing_result) > 0) {
        echo '
            <a href="../../tcpdf/certification_to_file_action_form.php?incident_case_number=' . $incident_case_number . '" class="button" style="margin-left: 18%;">
                Generate
            </a>
            <span class="printer-icon">
                <i class="bx bxs-printer"></i>
            </span>';
    } else {
        echo '<span class="button1" style="margin-left: 18%;">NOT APPLICABLE</span>';
    }
    ?>
</p>
  <hr style="border: 1px solid #ccc; margin: 10px 0;">
  <form action="" method="post">
  <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
  <p style="font-size: 14px; text-align: left;">
        Motion for Execution (KP #25)
        <?php
        $check_generation_query = "SELECT generate_execution FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
        $check_generation_result = mysqli_query($conn, $check_generation_query);

        if ($check_generation_result && mysqli_num_rows($check_generation_result) > 0) {
            $generation_data = mysqli_fetch_assoc($check_generation_result);

            // If generate_execution is "form generated," display "NOT APPLICABLE"
            if ($generation_data['generate_execution'] === 'form generated') {
                echo '<span class="button1" style="margin-left: 26%;">NOT APPLICABLE</span>';
            } else {
                // If generate_execution is not "form generated," display the "Generate" button
                echo '
                    <input type="submit" name="execution_submit" class="button" value="Generate" style="border: none; cursor: pointer; margin-left: 26%;">
                    <span class="printer-icon">
                        <i class="bx bxs-printer"></i>
                    </span>';
            }
        } else {
            echo '<span class="button1" style="margin-left: 26%;">NOT APPLICABLE</span>';
        }
        ?>
    </p>
  </form>
                            
            </center>
        </div>
</div>

        </div>

    </section>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

const searchIcon = document.querySelector('.search-box .icon');
    const searchInput1 = document.getElementById('searchInput1');

    searchIcon.addEventListener('click', function () {
    const searchTerm = searchInput1.value.trim().toLowerCase();

    if (searchTerm !== '') {
        handleSearch(searchTerm);
    }
    });

searchInput1.addEventListener('keyup', function (e) {
    if (e.key === 'Enter') {
        const searchTerm = searchInput1.value.trim().toLowerCase();

        if (searchTerm !== '') {
            handleSearch(searchTerm);
        }
    }
});

function handleSearch(searchTerm) {
    const lowerCaseSearchTerm = searchTerm.trim().toLowerCase();

    if (lowerCaseSearchTerm.startsWith('mediation') || lowerCaseSearchTerm.endsWith('mediation')) {
        window.location.href = 'mediation_hearings.php';
    } else if (lowerCaseSearchTerm === 'hearing' || lowerCaseSearchTerm === 'hearings') {
        window.location.href = 'hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('incident')) {
        window.location.href = 'incident_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('conciliation') || lowerCaseSearchTerm.endsWith('conciliation')) {
        window.location.href = 'conciliation_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('arbitration') || lowerCaseSearchTerm.endsWith('arbitration')) {
        window.location.href = 'arbitration_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('create') || lowerCaseSearchTerm.endsWith('create')) {
        window.location.href = 'create_report.php';
    } else if (lowerCaseSearchTerm.startsWith('ongoing') || lowerCaseSearchTerm.endsWith('ongoing')) {
        window.location.href = 'ongoing_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('settled') || lowerCaseSearchTerm.endsWith('settled')) {
        window.location.href = 'settled_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('incomplete') || lowerCaseSearchTerm.endsWith('incomplete')) {
        window.location.href = 'incomplete_notices.php';
    } else if (lowerCaseSearchTerm.startsWith('home') || lowerCaseSearchTerm.endsWith('home')) {
        window.location.href = 'home.php';
    } else if (lowerCaseSearchTerm.startsWith('account') || lowerCaseSearchTerm.endsWith('account')) {
        window.location.href = 'my_account.php';
    } else if (lowerCaseSearchTerm.startsWith('profile') || lowerCaseSearchTerm.endsWith('profile')) {
        window.location.href = 'my_account.php';
    }  else {
    searchInput1.value = `'${searchTerm.charAt(0).toUpperCase() + searchTerm.slice(1)}' was not found`;
    }
}

function restrictInput(input) {

// Remove special characters and numbers
input.value = input.value.replace(/[^a-zA-Z\s]/g, '');

// Restrict spacebar only if it's the first character
if (input.value.length > 0 && input.value[0] === ' ') {
  input.value = input.value.substring(1); // Remove the leading space
}
}

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


        function validateName(event) {
                    var keyCode = event.keyCode;

                    if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122)) {
                        return true;
                    }

                    if (keyCode === 32 || keyCode === 46 || keyCode === 45 || keyCode === 9) {
                        return true;
                    }

                    event.preventDefault();
                    return false;
                }
    
        const incidentDateInput = document.querySelector('input[name="incident_date"]');

        incidentDateInput.addEventListener('change', function(event) {
        const selectedDate = new Date(event.target.value);
        const currentDate = new Date();

    
        if (selectedDate > currentDate) {
        
        const formattedCurrentDate = currentDate.toISOString().slice(0, 10); 
        incidentDateInput.value = formattedCurrentDate;
        }

        });

        function showPDFPopup() {
        var popup = document.getElementById("pdf_popup");
        popup.style.display = "block";


    }

    function closePDFPopup() {
        var popup = document.getElementById("pdf_popup");
        popup.style.display = "none";
    }



    </script>
    <script src="../script.js"></script>
<style>
    .container{
        margin-left: 15%; margin-top: 22px;
    }

    .container form{
        position: relative;
        margin-top: 16px;
        min-height: 470px;
        background-color: #fff;
        overflow: hidden;
    }

    .container header::before{
        content: "";
        position: absolute;
        left: 0;
        bottom: -2px;
        height: 3px;
        width: 260px;
        border-radius: 8px;
        background-color: #F5BE1D;
    }

    .title::before{
        content: "";
        background: transparent;
    }

    .backBtn:hover{
        background-color: #bc1823;
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
    height: 250px;
    overflow-y: hidden;
}

    .box{
        outline: none;
        font-size: 14px;
        font-weight: 400;
        color: #333;
        border-radius: 5px;
        border: 1px solid #aaa;
        padding: 0 15px;
        width: 100%;
        height: 22px;
        margin: 5px 0;
        background-color: #f2f2f2;
    }

    .generate{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        font-size: 16px;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        width: 197px;
        margin-left: 77%;
        margin-top: -2%;
    }

    .generate:hover{
        background: #0d52bd;
        color: #fff;     
    }

    .backBtn-1{
        display: flex;
    align-items: center;
    justify-content: center;
    height: 45px;
    max-width: 200px;
    width: 100%;
    border: none;
    outline: none;
    color: #fff;
    border-radius: 5px;
    margin: 25px 0;
    background-color: #E83422;
    transition: all 0.3s linear;
    cursor: pointer;
    }

    .backBtn-1:hover{
        background-color: #bc1823;
    }

    .container header::before{
    content: "";
    position: absolute;
    left: 0;
    bottom: -2px;
    height: 3px;
    width: 395px;
    border-radius: 8px;
    background-color: #F5BE1D;
}

.button {
      display: inline-block;
      background-color: #007bff;
      color: #fff;
      padding: 8px;
      width: 90px;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      font-size: 14px;
    }

    .button1 {
      display: inline-block;
      background-color: #fff;
      border: 1px solid #007bff;
      color: #007bff;
      padding: 8px;
      width: 120px;
      text-decoration: none;
      border-radius: 5px;
      font-size: 12.5px;
      font-weight: 500;
      text-align: center;
      cursor: default;
    }

    /* Style the printer icon with a blue border and white background */
    .printer-icon {
      display: inline-block;
      background-color: #fff;
      border: 1px solid #007bff;
      border-radius: 5px;
      padding: 5px;
      margin-left: -12px;
    }

    .printer-icon i {
      font-size: 20px;
      margin-top: -2px;
      margin-bottom: 7px;
      color: #007bff;
    }

    .close-icon {
      position: absolute;
      top: 155px;
      left: 895px;
      cursor: pointer;
      font-size: 50px;
      color:#bc1823;
    }

    .text-box{
    outline: none;
    font-size: 14px;
    font-weight: 400;
    color: #333;
    border-radius: 5px;
    border: 1px solid #aaa;
    padding: 0 15px;
    height: 42px;
    margin: 8px 0;
}

.pending{
    background: #F5BE1D;
    width: 80%;
    text-align: center;
    border-radius: 5px;
    color: #fff;
    font-weight: 500;
    margin-left: 10%;
    margin-top: 4%;
    margin-bottom: 5%;
    font-size: 22px;
}

.file{
    background: #bc1823;
    width: 80%;
    text-align: center;
    border-radius: 5px;
    color: #fff;
    font-weight: 500;
    margin-left: 10%;
    margin-top: 4%;
    margin-bottom: 5%;
    font-size: 22px;
}

@media screen and (min-width: 1310px){
            .close-icon{
                left: 870px;
            }
            .container{
                margin-top: 3%;
            }
        }

        @media screen and (min-width: 1331px){
            .close-icon{
                left: 895px;
                top: 161px;
            }
            .container{
                margin-top: 1.7%;
            }
            
            .close-icon-1{
                left: 940px;
            }
        
        }

        @media screen and (min-width: 1360px) and (min-height: 768px) {
        .container{
            margin-top: 6%;
        }
    }

    @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
        .container{
            margin-left: 25%;
            margin-top: 13%;
        }

        .modal{
            margin-top: 20%;
            margin-left: 5%;
        }

        .close-icon{
            margin-left: 17%;
            margin-top: 10.3%;
        }
    }

    @media screen and (min-width: 1536px) and (min-height: 730px){
        .container{
            margin-top: 4.5%;
            margin-left: 20%;
        }
        .modal{
            margin-top: 15%;
        }
        .close-icon{
            margin-left: 5.5%;
            margin-top: 3%;
        }
    }

    @media screen and (min-width: 1366px) and (min-height: 617px){
        .container{
            margin-top: 0.05%;
            margin-left: 15%;
            width: 70%;
        }

        .container header::before{
            width: 403px;
        }
    }

    @media screen and (min-width: 1340px) and (max-width: 1360px){
            .container{
                width: 71%;
                margin-top: 1%;
            }
}

    @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
        .container{
            margin-top: -1.5%;
            margin-left: 13%;
            width: 75.5%;
        }
        .container header:before{
            width: 402px;
        }
        .close-icon{
            margin-left: -3.8%;
            margin-top: -1%;
        }
        .modal{
            margin-top: 13%;
        }
    }

    @media screen and (min-width: 1360px) and (min-height: 681px){
        .container{
            width: 70%;
            margin-top: 2.5%;
        }
        .container header:before{
            width: 403px;
        }
        .modal{
            margin-top: 15%;
        }
        .close-icon{
            margin-top: 1.8%;
        }
    }

    @media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
        .container{
            width: 62%;
            margin-top: 4.3%;
            margin-left: 20%;
        }
        .modal{
            margin-top: 15%;
        }
        .close-icon{
            margin-top: 3%;
        }
    }

    @media screen and (max-width: 2133px) and (min-height: 1055px) and (max-height: 1058px){
    .container{
        width: 43.8%;
        margin-top: 10%;
        margin-left: 28%;
    }
    .modal{
        margin-top: 16.5%;
        margin-left: 0%;
    }
    .close-icon{
        top: 27.5%;
        left: 54.5%;
    }
    }

@media screen and (min-width: 1500px) and (max-width: 1670px) and (min-height: 700px) and (max-height: 760px){
        .modal{
            position: absolute;
        top: 20%;
        left: 50%;
        transform: translate(-50%, -50%);
        }
        .close-icon{
            top: 24.5%;
            left: 63.5%;
            z-index: 1001;
        }
    }
    
        @media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
    .container{
    width: 64.8%;
    margin-left: 19%;
    margin-top: 2.8%;
    }
    .modal{
            position: absolute;
        top: 18%;
        left: 50%;
        transform: translate(-50%, -50%);
        }
        .close-icon{
            top: 24.5%;
            left: 64.3%;
            z-index: 1001;
        }
    }

</style>
</body>
</html>