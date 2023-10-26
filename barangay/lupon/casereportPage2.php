<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

$generate_execution = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execution_submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $generate_execution = 'form generated';

    $check_query = "SELECT * FROM `notify_residents` WHERE incident_case_number = '$incident_case_number'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE `notify_residents` SET `generate_execution` = '$generate_execution' WHERE incident_case_number = '$incident_case_number'";
        $result = mysqli_query($conn, $update_query);
    } else {
        $insert_query = "INSERT INTO `notify_residents` (`incident_case_number`, `generate_execution`)
                         VALUES ('$incident_case_number', '$generate_execution')";
        $result = mysqli_query($conn, $insert_query);
    }

    if ($result) {
        header("Location: incident_reports.php");
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
    <title>Create Incident Report Record</title>
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
                        <a href="hearings.php">
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
        <div class="container" style="margin-left: 15%; margin-top: 22px;">
        <?php
        $incident_case_number = $_GET['incident_case_number'];
        $select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
        $fetch_cases = mysqli_fetch_assoc($select);
        ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <header class="card-title" style="font-size: 18px;">Case Report Summary of Case #<?php echo $incident_case_number; ?></header>
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
                                    <p style="padding: 10px 0"><?php echo $hearing_status ?></p></div>
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
<div class="text-box">
    <p style="padding: 10px 0"><?php echo $agreement_description ?></p></div>
</div>
                            
                            <span class="title" style="width: 100%;">Execution of Agreement</span>
                            <div class="input-field-1">
                                <label class="label">Date of Agreement Execution</label>
                                <input type="date" name="agreement_execution" placeholder="" >
                            </div>
                            <div class="input-field-1">
                                <label class="label">Compliance Status</label>
                                <select name="compliance_status">
                                    <option disabled selected>Select...</option>
                                    <option>COMPLIANCE</option>
                                    <option>NON-COMPLIANCE</option>
                                </select>
                            </div>
                            <div class="input-field" style="width: 100%; position: relative;">
                                <label class="label">Remarks</label>
                                <input type="text" name="complainant_first_name" onkeypress="return validateName(event)" placeholder="" required>
                            </div>
                        </div>
                    </div>
                    <div class="details ID">
                        
                        <div class="buttons" style="margin-top: -2%;">
                            <a href="case_report.php?incident_case_number=<?php echo $incident_case_number ?>" style="text-decoration: none;">
                            <div class="backBtn-1" style="padding: 12px 12px; width: 100px; border: 1px solid #bc1823; background: #fff; color: #bc1823; margin-left: 550%;">
                                <span class="btnText" style="text-align: center;">Back</span>
                            </div></a>
                            <a href="try.html" style="text-decoration: none;">
                            <div class="backBtn-1" style="width: 600px; margin-left: 280%; padding: 12px 12px; ">
                                <span class="btnText">Generate Notice of Execution</span>
                            </div>
                            </a>
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
    Complainant's Form (KP #7)
    <a href="your-link-here" class="button" style="margin-left: 29%;">
      Generate
    </a>
    <span class="printer-icon">
      <i class='bx bxs-printer'></i>
    </span>
  </p>
  <hr style="border: 1px solid #ccc; margin: 10px 0;">
  <p style="font-size: 14px; text-align: left;">
    Hearing Notice for Complainant (KP #18)
    <a href="your-link-here" class="button" style="margin-left: 10%;">
      Generate
    </a>
    <span class="printer-icon">
      <i class='bx bxs-printer'></i>
    </span>
  </p>
  <hr style="border: 1px solid #ccc; margin: 10px 0;">
  <p style="font-size: 14px; text-align: left;">
    Hearing Notice for Respondent (KP #19)
    <a href="your-link-here" class="button" style="margin-left: 12%;">
      Generate
    </a>
    <span class="printer-icon">
      <i class='bx bxs-printer'></i>
    </span>
  </p>
  </form>
                            
            </center>
        </div>
</div>

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
      left: 900px;
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


</style>
</body>
</html>