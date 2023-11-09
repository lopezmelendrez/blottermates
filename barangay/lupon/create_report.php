<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (!isset($email)) {
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
    $other_incident_case_type = $_POST['other_incident_case_type'];

    $result = mysqli_query($conn, "SELECT incident_case_number FROM incident_report WHERE incident_case_number = '$incident_case_number'");
    if (mysqli_num_rows($result) > 0) {
        die('Error: Incident case number already exists.');
    } else {
        $select_submitter = mysqli_query($conn, "SELECT * FROM lupon_accounts WHERE email_address = '$email'");
        if (mysqli_num_rows($select_submitter) > 0) {
            $submitter_data = mysqli_fetch_assoc($select_submitter);
            $lupon_id = $submitter_data['lupon_id'];
            $submitter_first_name = $submitter_data['first_name'];
            $submitter_last_name = $submitter_data['last_name'];

            mysqli_query($conn, "INSERT INTO `incident_report` (complainant_last_name, complainant_first_name, complainant_middle_name, complainant_cellphone_number, complainant_house_address, respondent_last_name, respondent_first_name, respondent_middle_name, respondent_cellphone_number, respondent_house_address, incident_case_number, incident_case_type, other_incident_case_type, incident_date, description_of_violation, created_at, submitter_first_name, submitter_last_name, lupon_id) VALUES ('$complainant_last_name', '$complainant_first_name', '$complainant_middle_name', '$complainant_cellphone_number', '$complainant_house_address', '$respondent_last_name', '$respondent_first_name', '$respondent_middle_name', '$respondent_cellphone_number', '$respondent_house_address', '$incident_case_number', '$incident_case_type', '$other_incident_case_type', '$incident_date', '$description_of_violation', NULL, '$submitter_first_name', '$submitter_last_name', '$lupon_id')") or die('query failed');
            header("location: incomplete_notices.php");
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
                
            </div>
        </div>

    </nav>


    <section class="home">
        <div class="container" style="margin-left: 15%; margin-top: 25px;">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <header class="card-title" style="font-size: 22px;">Create Incident Report Form</header>
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
                                <input type="text" name="complainant_cellphone_number" id="complainant_cellphone_number" placeholder="" required>

                            </div>
                            <div class="input-field-1" style="width: 35rem;">
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
                                <input type="text" name="respondent_cellphone_number" id="respondent_cellphone_number" placeholder="">
                            </div>
                            <div class="input-field-1" style="width: 35rem;">
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
                            <div class="input-field-1" style="width: 35rem;">
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
                            
                            <div class="input-field" style="width: 17rem;">
                                <label class="required-label">Incident Date</label>
                                <input type="text" name="incident_date" id="datepicker" placeholder="" required readonly>
                            </div>

                            <div id="otherIncident" style="display: none;">
                                <div class="input-field" style="width: 53.7rem;">
                                <input type="text" id="otherIncidentType" placeholder="Other Incident Case Type" name="other_incident_case_type">
                                </div>
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
                            
                            <button class="pop-up" style="margin-right: 1px;">
                            <span class="btnText" style="background: transparent; border: none; font-weight: 600; color: #fff; cursor: pointer;">CREATE</span>
                            </button>
                        </div>
                    </div> 
                </div>

                <div class="modal-overlay" id="confirmationModal">
                    <div class="modal">
                    <h3 class="title-text" style="font-size: 23px; text-align: center;">CONFIRM INCIDENT CASE #<?php echo $incident_case_number; ?> DETAILS</h3>
                <hr style="border: 1px solid #ccc; margin: 10px 0;">
                <p style="font-weight: 600; text-align: left; margin-top: 5px; margin-bottom: 15px;">COMPLAINANT DETAILS</p>
                <div class="details-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="inputfield">
                                <label class="label">Last Name</label>
                                <div class="text-box">
                                    <p id="complainantLastName" style="padding: 10px 0;"></p></div>
                </div>
                <div class="inputfield">
                                <label class="label">First Name</label>
                                <div class="text-box">
                                    <p id="complainantFirstName" style="padding: 10px 0"></p></div>
                </div>
                <div class="inputfield">
                                <label class="label">Middle Name</label>
                                <div class="text-box">
                                    <p id="complainantMiddleName" style="padding: 10px 0"></p></div>
                </div>
                </div>     
                <div class="details-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-top: 6%;">
                <div class="inputfield">
                                <label class="label">Cellphone Number</label>
                                <div class="text-box">
                                    <p id="complainantCellphone" style="padding: 10px 0;"></p></div>
                </div>
                <div class="inputfield">
                                <label class="label">House Address</label>
                                <div class="text-box" style="width: 500px;">
                                    <p id="complainantAddress" style="padding: 10px 0"></p></div>
                </div>
                <div class="inputfield">
                </div>
                </div>
                <p style="font-weight: 600; text-align: left; margin-top: 5%; margin-bottom: 15px;">RESPONDENT DETAILS</p>
                <div class="details-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="inputfield">
                                <label class="label">Last Name</label>
                                <div class="text-box">
                                    <p id="respondentLastName" style="padding: 10px 0;"></p></div>
                </div>
                <div class="inputfield">
                                <label class="label">First Name</label>
                                <div class="text-box">
                                    <p id="respondentFirstName" style="padding: 10px 0"></p></div>
                </div>
                <div class="inputfield">
                                <label class="label">Middle Name</label>
                                <div class="text-box">
                                    <p id="respondentMiddleName" style="padding: 10px 0"></p></div>
                </div>
                </div>     
                <div class="details-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-top: 6%;">
                <div class="inputfield">
                                <label class="label">Cellphone Number</label>
                                <div class="text-box">
                                    <p id="respondentCellphone" style="padding: 10px 0;"></p></div>
                </div>
                <div class="inputfield">
                                <label class="label">House Address</label>
                                <div class="text-box" style="width: 500px;">
                                    <p id="respondentAddress" style="padding: 10px 0"></p></div>
                </div>
                <div class="inputfield">
                </div>
                </div>
                <p style="font-weight: 600; text-align: left; margin-top: 5%; margin-bottom: 5px;">INCIDENT DETAILS</p>
                <div class="details-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-top: 2%;">
                <div class="inputfield">
                                <label class="label">Incident Date</label>
                                <div class="text-box">
                                    <p id="incidentDate" style="padding: 10px 0"></p></div>
                </div>
                <div class="inputfield">
                                <label class="label">Incident Case Type</label>
                                <div class="text-box" style="width: 500px;">
                                    <p id="incidentCaseType" style="padding: 10px 0"></p></div>
                </div>
                <div class="inputfield">
                </div>
                </div>
                <div class="details-container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-top: 6%;">
                <div class="inputfield">
                                <label class="label">Description of VIolation</label>
                                <div class="text-box" style="width: 762px; height: 60px;">
                                    <p id="descriptionOfViolation" style="padding: 10px 0;"></p></div>
                </div>
                <div class="inputfield">
                </div>
                <div class="inputfield">
                </div>
                </div>        
        
                        <div id="popup" class="popup">
            
        <div class="modal-buttons" style="display: flex; align-items: center; margin-top: 7.5%; margin-right: 1px;">
                    <div class="backBtn" id="modalCancelBtn" style="padding: 12px 12px; width: 100px; border: 1px solid #bc1823; background: #fff; color: #bc1823; margin-left: 60%;">
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

    <div id="customAlertModal" class="modal-overlay">
    <div class="close-icon" id="customAlertClose">
                <i class='bx bxs-x-circle' ></i> <!-- Replace with the desired close icon -->
    </div>
    <center>
            <div class="modal-content">
            <h3 class="modal-title" style="font-size: 18px; text-align:center; color: #bc1823; font-size: 30px;">
            <i class="fa-solid fa-triangle-exclamation" style="color: #bc1823; font-size: 45px;"></i>
            INVALID
            </h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 14px; text-align: center; font-weight: 500; text-transform: uppercase; letter-spacing: 2px; margin-top: 12%;">Please input a valid Cellphone number for the: </p>
            <p id="customAlertMessage" style="font-size: 20px; text-align: center; margin-top: 3%; font-weight: 600;"></p>
            </div>
    <center>
</div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

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

$(function() {
            $("#datepicker").datepicker({
                minDate: new Date(2019, 0, 1), // January 1, 2019
                maxDate: new Date() // Current date
            });
        });

        $(function() {
            $("#incident_case_type").change(function() {
                if ($(this).val() === "Other") {
                    $("#otherIncident").show();
                } else {
                    $("#otherIncident").hide();
                }
            });
        });

const respondentCellphoneInput = document.getElementById('respondent_cellphone_number');
        respondentCellphoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });

        const complainantCellphoneInput = document.getElementById('complainant_cellphone_number');
        complainantCellphoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+]/g, '');
        });


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

function openCustomAlert(message) {
    const modal = document.getElementById('customAlertModal');
    const messageElement = document.getElementById('customAlertMessage');

    messageElement.textContent = message;
    modal.style.display = 'block';

    // Close the modal when the close button is clicked
    const closeButton = document.getElementById('customAlertClose');
    closeButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close the modal when the user clicks outside the modal
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

function validatePhoneNumber(phoneNumber) {
  // Regular expression pattern for a valid Philippine phone number
  const phoneNumberPattern = /^(?:\+63|0)[0-9]{10}$/;

  return phoneNumberPattern.test(phoneNumber);
}

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
        const complainantPhoneNumber = document.querySelector('input[name="complainant_cellphone_number"]').value;
        const respondentPhoneNumber = document.querySelector('input[name="respondent_cellphone_number"]').value;

        if (!validatePhoneNumber(complainantPhoneNumber) || !validatePhoneNumber(respondentPhoneNumber)) {
    if (!validatePhoneNumber(complainantPhoneNumber) && !validatePhoneNumber(respondentPhoneNumber)) {
        openCustomAlert("COMPLAINANT and RESPONDENT");
    } else if (!validatePhoneNumber(complainantPhoneNumber)) {
        openCustomAlert("COMPLAINANT");
    } else {
        openCustomAlert("RESPONDENT");
    }
}  else {
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

        const incidentCaseType = document.querySelector('select[name="incident_case_type"] option:checked').text;
const otherCaseType = document.querySelector('input[name="other_incident_case_type"]').value;

if (otherCaseType) {
    document.getElementById("incidentCaseType").textContent = incidentCaseType + " — " + otherCaseType;
} else {
    document.getElementById("incidentCaseType").textContent = incidentCaseType;
}
        document.getElementById("incidentDate").textContent = document.querySelector('input[name="incident_date"]').value;
        document.getElementById("descriptionOfViolation").textContent = document.querySelector('textarea[name="description_of_violation"]').value;
        

        modalOverlay.style.display = "flex";
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

    </script>
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
        width: 312px;
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
        margin-top: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 800px;
        height: 635px;
        overflow-y: hidden;
    }

    .modal-title {
        font-size: 1.5em;
        margin-bottom: 10px;
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
    font-size: 14px;
    font-weight: 400;
    color: #333;
    border-radius: 5px;
    border: 1px solid #aaa;
    padding: 0 5px;
    height: 30px;
    margin: 8px 0;
    width: 239px;
    position: fixed;
}

.text-box p{
    text-align: left;
    margin-left: 5px;
    margin-top: -6px;
}

.details-container .inputfield{
    display: flex;
    width: calc(100% / 3 - 15px);
    flex-direction: column;
    margin: 4px 0;
}

.inputfield label, .inputfield2 label{
    font-size: 13px;
    font-weight: 500;
    color: #2e2e2e;
    text-align: left;
    margin-top: -15px;
}

.modal-content {
    background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        margin-top: 180px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 500px;
        height: 280px;
        overflow-y: hidden;
}

.close-icon {
      position: absolute;
      top: 155px;
      left: 875px;
      cursor: pointer;
      font-size: 50px;
      color:#bc1823;
    }
    



</style>
</body>
</html>