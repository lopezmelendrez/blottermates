<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

function displayPage($conn, $incident_case_number)
{
    // Check if the incident case number is found in the hearing table with hearing_type_status "filed to court action"
    $check_hearing_query = "SELECT * FROM hearing WHERE incident_case_number = '$incident_case_number'";
    $check_hearing_result = mysqli_query($conn, $check_hearing_query);

    if ($check_hearing_result && mysqli_num_rows($check_hearing_result) > 0) {
        // If incident_case_number is found in the hearing table with hearing_type_status "filed to court action", check if it's not found in amicable_settlement and court_action

        // Check if incident_case_number is not found in the amicable_settlement table
        $check_amicable_query = "SELECT * FROM amicable_settlement WHERE incident_case_number = '$incident_case_number'";
        $check_amicable_result = mysqli_query($conn, $check_amicable_query);

        // Check if incident_case_number is not found in the court_action table
        $check_court_action_query = "SELECT * FROM court_action WHERE incident_case_number = '$incident_case_number'";
        $check_court_action_result = mysqli_query($conn, $check_court_action_query);

        // Check if incident_case_number is not found in amicable_settlement and court_action
        if ($check_amicable_result && mysqli_num_rows($check_amicable_result) == 0 &&
            $check_court_action_result && mysqli_num_rows($check_court_action_result) == 0) {
            // If all conditions are met, return true
            return true;
        }
    }

    return false;
}

$incident_case_number = $_GET['incident_case_number'];

if (!displayPage($conn, $incident_case_number)) {
    header('location: incident_reports.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $incident_case_number = $_POST['incident_case_number'];
    $signatureData = mysqli_real_escape_string($conn, $_POST["lupon_signature"]);

    $select_hearing_id_query = "SELECT hearing_id FROM hearing WHERE incident_case_number = '$incident_case_number'";
    $select_hearing_id_result = mysqli_query($conn, $select_hearing_id_query);

    if ($select_hearing_id_result && mysqli_num_rows($select_hearing_id_result) > 0) {
        $fetch_hearing = mysqli_fetch_assoc($select_hearing_id_result);
        $hearing_id = $fetch_hearing['hearing_id'];

        // Update your INSERT query to include the `timestamp` column and set it to the current timestamp
        $insert_query = "INSERT INTO `court_action` (`lupon_signature`, `hearing_id`, `incident_case_number`, `timestamp`)
        VALUES ('$signatureData', '$hearing_id', '$incident_case_number', NOW())";

        $insert_result = mysqli_query($conn, $insert_query);

        if ($insert_result) {
            $incident_case_number = $_POST['incident_case_number'];
            echo '<script>';
            echo 'window.open("http://brgyblotter-src.online/tcpdf/certification_to_file_action_form.php?incident_case_number=' . $incident_case_number . '", "_blank");';
            echo 'window.location.href = "settled_cases.php";';
            echo '</script>';
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
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/dilg.css">
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>File Court Action</title>
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

    <center>
            <div class="add-account-container">
            <?php
$incident_case_number = $_GET['incident_case_number'];

// Fetch incident report details
$select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
$fetch_cases = mysqli_fetch_assoc($select);

// Fetch hearing details
$select_hearing = mysqli_query($conn, "SELECT * FROM `hearing` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
$fetch_hearing = mysqli_fetch_assoc($select_hearing);
?>
                <div class="header-text">File Court Action for Case <?php echo htmlspecialchars(substr($fetch_cases['incident_case_number'], 0, 9)); ?></div>
                
                <form action="" method="post" style="height: 425px;">
                <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
            <span class="title" style="font-style: italic; margin-top: -5px; font-size: 23px; text-transform: uppercase;"><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?> </span>
            <span style="display: block;">HEARING: <strong><?php echo strtoupper($fetch_hearing['hearing_type_status']); ?></strong> ON 
    <?php
        // Format date
        $date_of_hearing = date('F j, Y', strtotime($fetch_hearing['date_of_hearing']));
        // Format time
        $time_of_hearing = date('g:i A', strtotime($fetch_hearing['time_of_hearing']));
        echo $date_of_hearing . ', ' . $time_of_hearing;
    ?></span>
            <div class="fields">
                        <div class="input-field-1">
                            <label>Complainant</label>
                            <input type="text"  onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['complainant_last_name']; ?>, <?php echo $fetch_cases['complainant_first_name']; ?> <?php echo substr($fetch_cases['complainant_middle_name'], 0, 1); ?>." disabled readonly>
                        </div>
                        <div class="input-field-1">
                            <label>Respondent</label>
                            <input type="text"  onkeypress="return validateName(event)" placeholder="" value="<?php echo $fetch_cases['respondent_last_name']; ?>, <?php echo $fetch_cases['respondent_first_name']; ?> <?php echo substr($fetch_cases['respondent_middle_name'], 0, 1); ?>." disabled readonly>
                        </div>
                        <div class="input-field" style="width: 100%;">
                        <small style="font-size: 19px; font-weight: 500; text-align: justify;">THIS IS TO CERTIFY THAT:</small>
                        </div>

                            <div class="input-field-1" style="width: 100%;">
                        <p style="font-size: 14px; text-align: left;">1. There was a personal confrontation between the parties before the Punong
Barangay but mediation failed;</p>
                        <p style="font-size: 14px; text-align: left;">2. The Punong Barangay set the meeting of the parties for the constitution of
the Pangkat;.</p>
                        <p style="font-size: 14px; text-align: left;">3.  The respondent willfully failed or refused to appear without justifiable
reason at the conciliation proceedings before the Pangkat; and</p>
<p style="font-size: 15px; text-align: left;">4. Therefore, the corresponding complaint for the dispute may now be filed in
court/government office.</p>
                                
                        </div>
                    

                    </div>
                
                    <div class="input-group1 d-flex" style="margin-top: 1%; margin-left: 22%;">
                        <input type="button" value="Back" class="btn btn-secondary back-btn" style="width: 10%; margin-left: 430px;" onclick="history.back()">
                        <input type="button" id="openModalBtn" value="FILE COURT ACTION" class="btn btn-danger" style="width: 30%; margin-left: 10px;">
                    </div>

                    
                    <div id="signatureModal" class="modal">
                        <div class="modal-content">
                            <p style="text-align: justify; font-size: 13px;">By adding a digital signature, you are ensuring the authenticity and integrity of the motion to File Court Action.</p>
                            <div class="signature-pad">
                                <canvas id="signatureCanvas" width="400" height="200"></canvas>
                            </div>
                            <div class="clear-signature" id="clearSignatureBtn">Clear Signature</div>
                            <div class="input-group1 d-flex" style="margin-top: 3%;">
                                <input type="button" value="Cancel" id="closeModalBtn" class="btn btn-secondary back-btn" style="width: 15%; margin-left: 60%; font-size: 17px; text-align: center; padding: 10px 10px;">
                                <input type="submit" name="submit" value="Confirm" class="btn btn-danger" id="saveSignatureBtn" style="width: 30%; margin-left: 15px; font-size: 17px; text-transform: uppercase;">
                            </div>
                        </div>
                        <input type="hidden" id="signatureData" name="lupon_signature">
                    </div>
                    
                    
                </form>
            </div>
            </center>

    </section>

    <script src="search_bar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
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

        

// Get the modal and button elements
const modal = document.getElementById("signatureModal");
const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const signatureCanvas = document.getElementById("signatureCanvas");
const clearSignatureBtn = document.getElementById("clearSignatureBtn");
const saveSignatureBtn = document.getElementById('saveSignatureBtn');
const signatureDataInput = document.getElementById('signatureData');

const signaturePad = new SignaturePad(signatureCanvas);

// Open the modal when the button is clicked
openModalBtn.addEventListener("click", () => {
    modal.style.display = "block";
});

// Close the modal when the close button is clicked
closeModalBtn.addEventListener("click", () => {
    modal.style.display = "none";
    // Clear the signature when the modal is closed
    signaturePad.clear();
});

// Clear the signature when the clear button is clicked
clearSignatureBtn.addEventListener("click", () => {
    signaturePad.clear();
});

saveSignatureBtn.addEventListener('click', () => {
    // Get the signature data as an image (PNG format)
    const signatureData = signaturePad.toDataURL('image/png');

    // Store the signature data in the hidden input field
    signatureDataInput.value = signatureData;

    // Optionally, you can also send the signatureData to your server for storage here
    // Example: sendToServer(signatureData);
});

// Close the modal if the user clicks outside of it
window.addEventListener("click", (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
        // Clear the signature when the modal is closed
        signaturePad.clear();
    }
});


</script>

<style>
    select[name="barangay"] option:disabled {
        color: #707070;
        background-color: #DDD;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); /* Apply shadow to the text */
    }

    .clipboard{
        background-color: #DDD;
        color: #707070;
        font-weight: 600;
        width: 15%;
        border-radius: 5px;
        font-size: 10px;
        margin-left: 16%;
        margin-top: -80%;
        padding: 5px 6px;
    }

    .home{
    position: absolute;
    top: 0;
    top: 0;
    left: 250px;
    height: 100vh;
    width: calc(100% - 78px);
    background-color: var(--body-color);
    transition: var(--tran-05);
}

.sidebar.close ~ .home{
    left: 78px;
    height: 100vh;
    width: calc(100% - 78px);
}

.add-account-container{
    height: 535px; width: 880x; margin-top: 20px; margin-left: -50px;
}

@media screen and (min-width: 1310px){
    .add-account-container{
        margin-top: 3.3%;
    }
}

@media screen and (min-width: 1331px){
    .add-account-container{
        margin-top: 1.1%;
    }
}

@media screen and (min-width: 1340px) and (max-width: 1360px){
    .modal-content{
        margin-top: 10%;
    }
    }


@media screen and (min-width: 1360px) and (min-height: 768px){
    .add-account-container{
        margin-top: 6%;
        margin-left: -5%;
    }
}

@media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .add-account-container{
                margin-top: 13%;
                margin-left: -1%;
            }

            .modal-content{
                margin-top: 18%;
            }
        }

@media screen and (min-width: 1536px) and (min-height: 730px){
    .add-account-container{
        margin-top: 4.5%;
        margin-left: -1.5%;
    }

    .modal-content{
        margin-top: 13%;
        margin-left: 5%;
    }
}

@media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
        .add-account-container{
            margin-left: -5%;
            margin-top: 0%;
        }       
    }


@media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
        .add-account-container{
            margin-top: -1.8%;
            margin-left: -5.5%;
        }
    }

@media screen and (min-width: 1360px) and (min-height: 681px){
    .add-account-container{
       margin-top: 2.5%;
       margin-left: -5%; 
    }
    .modal-content{
            margin-top: 12%;
            margin-left: 5%;
        }
}

@media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
        .add-account-container{
            margin-top: 3.5%;
            margin-left: -5%;
        }
    }

    @media screen and (max-width: 2133px) and (min-height: 1055px) and (max-height: 1058px){
        .add-account-container{
            margin-top: 9%;
        }
        .modal-content{
            margin-top: 15%;
            margin-left: 2.5%;
        }
    }

@media screen and (min-width: 1500px) and (max-width: 1670px) and (min-height: 700px) and (max-height: 760px){
        .modal-content{
            position: absolute;
        top: 25%;
        left: 45%;
        transform: translate(-50%, -50%);
        }
        .add-account-container{
            margin-top: 4.9%;
            margin-left: -3%;
        }
    }

@media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
        .add-account-container{
            margin-top: 3.5%;
            margin-left: -1%;
        }
}

</style>
</body>
</html>