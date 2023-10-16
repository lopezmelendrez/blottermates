<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

$current_year = date('Y');
$result = mysqli_query($conn, "SELECT MAX(incident_id) AS max_id FROM incident_report");
$row = mysqli_fetch_assoc($result);
$next_id = $row['max_id'] + 1;
$pad_length = 4;
$next_id_padded = str_pad($next_id, $pad_length, '0', STR_PAD_LEFT);
$incident_case_number = $current_year . '-' . $next_id_padded;

if (isset($_POST['submit'])) {
    $complainant_last_name = $_POST['complainant_last_name'];
    $complainant_first_name = $_POST['complainant_first_name'];
    $complainant_middle_name = $_POST['complainant_middle_name'];
    $complainant_cellphone_number = $_POST['complainant_cellphone_number'];
    $complainant_house_address = $_POST['complainant_house_address'];

    $respondent_last_name = $_POST['respondent_last_name'];
    $respondent_first_name = $_POST['respondent_first_name'];
    $respondent_middle_name = $_POST['respondent_middle_name'];
    $respondent_cellphone_number = $_POST['respondent_cellphone_number'];
    $respondent_house_address = $_POST['respondent_house_address'];

    $incident_case_type = $_POST['incident_case_type'];
    $incident_date = $_POST['incident_date'];
    $description_of_violation = $_POST['description_of_violation'];

    $result = mysqli_query($conn, "SELECT incident_case_number FROM incident_report WHERE incident_case_number = '$incident_case_number'");
    if (mysqli_num_rows($result) > 0) {
        die('Error: Incident case number already exists.');
    } else {
        $select_submitter = mysqli_query($conn, "SELECT * FROM lupon_accounts WHERE email_address = '$email'");
        if(mysqli_num_rows($select_submitter) > 0) {
            $submitter_data = mysqli_fetch_assoc($select_submitter);
            $lupon_id = $submitter_data['lupon_id'];
            $submitter_first_name = $submitter_data['first_name'];
            $submitter_last_name = $submitter_data['last_name'];


        mysqli_query($conn, "INSERT INTO `incident_report` (complainant_last_name, complainant_first_name, complainant_middle_name, complainant_cellphone_number, complainant_house_address, respondent_last_name, respondent_first_name, respondent_middle_name, respondent_cellphone_number, respondent_house_address, incident_case_number, incident_case_type, incident_date, description_of_violation, created_at, submitter_first_name, submitter_last_name, lupon_id) VALUES('$complainant_last_name', '$complainant_first_name', '$complainant_middle_name', '$complainant_cellphone_number', '$complainant_house_address', '$respondent_last_name', '$respondent_first_name', '$respondent_middle_name', '$respondent_cellphone_number', '$respondent_house_address', '$incident_case_number', '$incident_case_type', '$incident_date', '$description_of_violation', NULL, '$submitter_first_name', '$submitter_last_name', '$lupon_id')") or die('query failed');
        header("location: case_report.php?incident_case_number=<?php echo $incident_case_number ?>");
        }
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
        <div class="container" style="margin-left: 15%; margin-top: 10px;">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <header class="card-title" style="font-size: 18px;">Create Incident Report Form</header>
                    <hr style="border: 1px solid #ccc; margin: 20px 0;">
                    <div class="case-number-box" style="text-align: center; font-size: 28px;">
                        <span>INCIDENT CASE #<?php echo $incident_case_number; ?></span>
                    </div>
                </div>
            <form action="#" method="post">
                <div class="form first">
                    <div class="details personal">
                        <span class="title">Complainant Details</span>
                        <div class="fields">
                            <div class="input-field">
                                <label class="required-label">Last Name</label>
                                <input type="text" name="complainant_last_name" onkeypress="return validateName(event)" placeholder="" required>
                            </div>
                            <div class="input-field">
                                <label class="required-label">First Name</label>
                                <input type="text" name="complainant_first_name" onkeypress="return validateName(event)" placeholder="" required>
                            </div>
                            <div class="input-field">
                                <label>Middle Name</label>
                                <input type="text" name="complainant_middle_name" onkeypress="return validateName(event)" placeholder="">
                            </div>
                            <div class="input-field">
                                <label class="required-label">Cellphone Number</label>
                                <input type="text" name="complainant_cellphone_number" placeholder="" required>

                            </div>
                            <div class="input-field-1" style="width: 28rem;">
                                <label class="required-label">House Address</label>
                                <input type="text" name="complainant_house_address" placeholder="" required>
                            </div>
                        </div>
                    </div>
                    <div class="details ID">
                        <span class="title">Respondent Details</span>
                        <div class="fields">
                            <div class="input-field">
                                <label class="required-label">Last Name</label>
                                <input type="text" name="respondent_last_name" onkeypress="return validateName(event)" placeholder="" required>
                            </div>
                            <div class="input-field">
                                <label class="required-label">First Name</label>
                                <input type="text" name="respondent_first_name" onkeypress="return validateName(event)" placeholder="" required>
                            </div>
                            <div class="input-field">
                                <label>Middle Name</label>
                                <input type="text" name="respondent_middle_name" onkeypress="return validateName(event)" placeholder="">
                            </div>
                            <div class="input-field">
                            <label class="required-label">Cellphone Number</label>
                            <input type="text" name="respondent_cellphone_number" placeholder="" required>
                            </div>

                            <div class="input-field-1" style="width: 28rem;">
                                <label class="required-label">House Address</label>
                                <input type="text" name="respondent_house_address" placeholder="" required>
                            </div>
                        </div>
                        <button class="nextBtn" style="margin-top: 5px; margin-left: -20px;">
                            <span class="btnText">Next</span>
                        </button>
                    </div> 
                </div>
                <div class="form second">
                    <div class="details address">
                        <span class="title">Incident Details</span>
                        <div class="fields">
                            <div class="input-field-1" style="width: 28rem;">
                                <label class="required-label">Incident Case Type</label>
                                <select name="incident_case_type" id="incident_case_type" required>
                                    <option disabled selected>Select...</option>
                                    <option value="Article No. 154">Unlawful use of means of Publication and Unlawful Utterances</option>
                                    <option value="Article No. 155">Alarms and Scandals</option>
                                    <option value="Article No. 175">Using False Certificates</option>
                                    <option value="Article No. 178">Using Fictitious Names and Concealing True Names</option>
                                    <option value="Article No. 179">Illegal Use of Uniforms and Insignias</option>
                                    <option value="Article No. 252">Physical Injuries inflicted in a tumultuous affray</option>
                                    <option value="Article No. 253">Giving Assistance to consummated Suicide.</option>
                                    <option value="Article No. 260">Responsibility of participants in a duel if only physical injuries are infliced or no physical injuries have been inflicted</option>
                                    <option value="Article No. 265">Less serious physical injuries</option>
                                    <option value="Article No. 266">Slight physical injuries and maltreatment</option>
                                    <option value="Article No. 269">Unlawful Arrest</option>
                                    <option value="Article No. 271">Inducing a minor to abandon his/her home</option>
                                    <option value="Article No. 275">Abandonment of a person in danger and abandonment of one's own victim</option>
                                    <option value="Article No. 276">Abandoning a minor (A child under Seven[7] years old)</option>
                                    <option value="Article No. 277">Abandonment of a minor by persons entrusted with his/her custody; indifference of parents</option>
                                    <option value="Article No. 280">Qualified trespass to dwelling (Without the use of violence and intimidation)</option>
                                    <option value="Article No. 281">Other forms of trespass</option>
                                    <option value="Article No. 283">Light threats</option>
                                    <option value="Article No. 285">Other light threats</option>
                                    <option value="Article No. 286">Grave coercion</option>
                                    <option value="Article No. 287">Light coercion</option>
                                    <option value="Article No. 288">Other similar coercions (Compulsory purchase of merchandise and payment of wages by means of tokens)</option>
                                    <option value="Article No. 289">Formation, maintenance, and prohibition of combination of captial or labor through violence or threats</option>
                                    <option value="Article No. 290">Discovering secrets through seizure and correspondence</option>
                                    <option value="Article No. 291">Revealing secrets with abuse of authority.</option>
                                    <option value="Article No. 309">Theft(If the value of the property stolen does not exceed P50.00).</option>
                                    <option value="Article No. 310">Qualified Theft(If the amount does not exceed P500).</option>
                                    <option value="Article No. 312">Occupation of real property or usurpation of real rights in property.</option>
                                    <option value="Article No. 313">Altering boundaries or landmarks.</option>
                                    <option value="Article No. 315">Swindling or estafa(If the amount does not exceed P200.00).</option>
                                    <option value="Article No. 316">Other forms of swindling.</option>
                                    <option value="Article No. 317">Swindling a minor.</option>
                                    <option value="Article No. 318">Other deceits.</option>
                                    <option value="Article No. 319">Removal, sale, or pledge of mortgaged property.</option>
                                    <option value="Article No. 328">Special cases of malicious mischief(If the value of the damaged property does not exceed P1, 000.00).</option>
                                    <option value="Article No. 329">Other mischiefs(If the value of the damaged property does not exceed P1,000.00).</option>
                                    <option value="Article No. 338">Simple seduction.</option>
                                    <option value="Article No. 339">Acts of lasciviousness with the consent of the offended party.</option>
                                    <option value="Article No. 356">Threatening to publish and offer to prevent such publication for compensation.</option>
                                    <option value="Article No. 357">Prohibiting publication of acts referred to in the course of official proceedings.</option>
                                    <option value="Article No. 363">Incriminating innocent persons</option>
                                    <option value="Article No. 364">Intriguing against honor.</option>
                                    <option value="BP 22">Issuing checks without sufficient funds.</option>
                                    <option value="PD 1612">Fencing of stolen properties if the property involved is not more than P50.00.</option>
                                    <option value="Other">Others...</option>
                                </select>
                            </div>
                            
                            <div class="input-field">
                                <label class="required-label">Incident Date</label>
                                <input type="date" name="incident_date" placeholder="" required>
                            </div>
                            <div class="input-field" style="width: 100%; position: relative;">
                                <label class="required-label">Description of Violation</label>
                                <textarea style="width: 100%; height: 150px; padding: 10px 15px; border: 1px solid #aaa; outline: none; font-size: 14px; border-radius: 5px; font-weight: 400; margin-top: 8px; resize: vertical;" name="description_of_violation" id="description_input" required></textarea>
                                <p id="character_count" style="position: absolute; bottom: 8px; right: 15px; color: #333; font-size: 11px;">255 characters left</p>
                            </div>
                        </div>
                </div>

                    <div class="details family">
                        <div class="buttons">
                            <div class="backBtn">
                                <span class="btnText" style="margin-left: -20px;">Back</span>
                            </div>
                            
                            <button class="pop-up">
                            <span class="btnText" style="font-size: 11px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">Create Incident Report Record</span>
                            </button>
                        </div>
                    </div> 
                </div>

                <div class="modal-overlay" id="confirmationModal">
                    <div class="modal">
                        <h3 class="modal-title" style="font-size: 18px; text-align:center;">CONFIRM INCIDENT CASE #<?php echo $incident_case_number; ?></h3>
                        <hr style="border: 1px solid #ccc; margin: 10px 0;">
                        <h3 style="font-size: 18px;">COMPLAINANT DETAILS</h3>
                        <div class="name-section">
                        <p>Last Name: 
                            <div class="box" style="width: 90%;">
                            <span id="complainantLastName"></span>
                            </div>
                        </p>     
                        </div>
                        <div class="name-section" style="margin-left: 22px;">
                        <p style="margin-left: -25px;">First Name: 
                            <div class="box" style="width: 12.2rem; margin-left: -25px;">
                            <span id="complainantFirstName"></span>
                            </div>
                        </p>
                        </div>
                        <div class="name-section">
                        <p>Middle Name: 
                            <div class="box" style="width: 14rem;">
                            <span id="complainantMiddleName"></span>
                            </div>
                        </p>
                        </div>
                        <div class="name-section" style="display: inline-block;">
                        <p>Contact Number:
                            <div class="box" style="width: 90%;"> 
                            <span id="complainantCellphone"></span>
                            </div>
                            </p>
                        </div>
                        <div class="name-section-1" style="width: 27.5rem;">
                        <p>House Address: 
                            <div class="box">
                            <span id="complainantAddress"></span>
                            </div>
                        </p>
                        </div>
                    <h3 style="font-size: 18px; margin-top: 10px;">RESPONDENT DETAILS</h3>
                    <div class="name-section">
                        <p>Last Name: 
                            <div class="box" style="width: 90%;">
                            <span id="respondentLastName"></span>
                            </div>
                        </p>     
                        </div>
                        <div class="name-section" style="margin-left: 22px;">
                        <p style="margin-left: -25px;">First Name: 
                            <div class="box" style="width: 12.2rem; margin-left: -25px;">
                            <span id="respondentFirstName"></span>
                            </div>
                        </p>
                        </div>
                        <div class="name-section">
                        <p>Middle Name: 
                            <div class="box" style="width: 14rem;">
                            <span id="respondentMiddleName"></span>
                            </div>
                        </p>
                        </div>
                        <div class="name-section" style="display: inline-block;">
                        <p>Contact Number:
                            <div class="box" style="width: 90%;"> 
                            <span id="respondentCellphone"></span>
                            </div>
                            </p>
                        </div>
                        <div class="name-section-1" style="width: 27.5rem;">
                        <p>House Address: 
                            <div class="box">
                            <span id="respondentAddress"></span>
                            </div>
                        </p>
                        </div>
                    <h3 style="font-size: 18px; margin-top: 10px;">INCIDENT DETAILS</h3>
                    <div class="name-section" style="display: inline-block;">
                        <p>Date of Incident:
                            <div class="box" style="width: 90%;"> 
                            <span id="incidentDate"></span>
                            </div>
                            </p>
                        </div>
                        <div class="name-section-1" style="width: 27.5rem;">
                        <p>Incident Case Type: 
                            <div class="box">
                            <span id="incidentCaseType"></span>
                            </div>
                        </p>
                        </div>
                        <div class="name-section">
                        <p>Description of Violation: 
                            <div class="box" style="width: 40rem; height: 66px; white-space: normal;">
                            <span id="descriptionOfViolation"></span>
                            </div>
                        </p>
                        </div>
        
                        <div id="popup" class="popup">
            
        <div class="modal-buttons" style="display: flex; align-items: center; margin-top: -15px; margin-right: 22px;">
                    <div class="backBtn" id="modalCancelBtn">
                        <span class="btnText" style="margin-left: -20px;">Back</span>
                    </div>
            <button class="modal-confirm" id="modalConfirmBtn">
            <input type="submit" name="submit" value="Submit" class="btnText" style="font-size: 16px; background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">
            </button>
        </div>
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

        const maxLength = 255;
const inputElement = document.getElementById('description_input');
const characterCountElement = document.getElementById('character_count');

inputElement.addEventListener('input', function() {
    let inputText = inputElement.value;

    // Remove extra spaces from the beginning and end of the input text
    inputText = inputText.trim();

    // Remove consecutive spaces within the input text
    inputText = inputText.replace(/\s+/g, ' ');

    if (inputText.length > maxLength) {
        inputText = inputText.substring(0, maxLength);
        inputElement.value = inputText; // Update the input value
    }
    
    const remainingCharacters = maxLength - inputText.length;
    characterCountElement.textContent = `${remainingCharacters} character${remainingCharacters !== 1 ? 's' : ''} left`;

    // Check remaining characters and change text color accordingly
    if (remainingCharacters <= 20) {
        characterCountElement.style.color = '#F5BE1D';
    } else if (remainingCharacters <= 0) {
        characterCountElement.style.color = 'red';
    } else {
        characterCountElement.style.color = ''; // Reset color to default
    }
});

// JavaScript code for the modal and form submission confirmation
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
        document.getElementById("complainantLastName").textContent = document.querySelector('input[name="complainant_last_name"]').value;
        document.getElementById("complainantFirstName").textContent = document.querySelector('input[name="complainant_first_name"]').value;
        document.getElementById("complainantMiddleName").textContent = document.querySelector('input[name="complainant_middle_name"]').value;
        document.getElementById("complainantCellphone").textContent = document.querySelector('input[name="complainant_cellphone_number"]').value;
        document.getElementById("complainantAddress").textContent = document.querySelector('input[name="complainant_house_address"]').value;
        // Add more fields for complainant

        document.getElementById("respondentLastName").textContent = document.querySelector('input[name="respondent_last_name"]').value;
        document.getElementById("respondentFirstName").textContent = document.querySelector('input[name="respondent_first_name"]').value;
        document.getElementById("respondentMiddleName").textContent = document.querySelector('input[name="respondent_middle_name"]').value;
        document.getElementById("respondentCellphone").textContent = document.querySelector('input[name="respondent_cellphone_number"]').value;
        document.getElementById("respondentAddress").textContent = document.querySelector('input[name="respondent_house_address"]').value;
        // Add more fields for respondent

        document.getElementById("incidentCaseType").textContent = document.querySelector('select[name="incident_case_type"] option:checked').text;
        document.getElementById("incidentDate").textContent = document.querySelector('input[name="incident_date"]').value;
        document.getElementById("descriptionOfViolation").textContent = document.querySelector('textarea[name="description_of_violation"]').value;

        modalOverlay.style.display = "flex";
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

const phoneNumberInput = document.getElementById('phoneNumber');
const validationMessage = document.getElementById('validationMessage');
const warningSign = document.querySelector('.warning-sign');

// Regular expression to validate a Philippine cellphone number.
const regex = /^(09|\+639)\d{9}$/;

phoneNumberInput.addEventListener('input', function () {
  const phoneNumber = phoneNumberInput.value;

  if (regex.test(phoneNumber)) {
    validationMessage.textContent = '';
    phoneNumberInput.classList.remove('invalid-input');
    warningSign.style.visibility = 'hidden';
  } else {
    validationMessage.textContent = 'Invalid cellphone number. Please enter a valid Philippine cellphone number.';
    phoneNumberInput.classList.add('invalid-input');
    warningSign.style.visibility = 'visible';
  }
});

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

    .modal {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 860px;
        height: 600px;
        overflow-y: hidden;
    }

    .modal-title {
        font-size: 1.5em;
        margin-bottom: 10px;
    }

    .modal p{
        font-size: 15px;
    }

    .modal-message {
        font-size: 1.2em;
        margin-bottom: 20px;
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

    .name-section {
        display: inline-block;
        width: 30%;
        box-sizing: border-box;
        
    }

    .name-section-1 {
        display: inline-block;
        width: calc(100% / 2 - 15px);
        box-sizing: border-box;
        
    }

    .name-section-2{
        display: inline-block;
        width: 100%;
        box-sizing: border-box;
    }

    .name-section p, .name-section-1 p, .name-section-2 p{
        font-size: 13px;
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
        background-color: #f5f5f5;
    }



</style>
</body>
</html>