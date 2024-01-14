<?php

$configFile = file_get_contents('../barangays.json');
$config = json_decode($configFile, true);

include '../config.php';

session_start();

$pb_id = $_SESSION['pb_id'];
$barangay_captain = $_SESSION['barangay_captain'];

if(!isset($pb_id)){
header('location: ../index.php');
}

if (isset($_POST['submit'])) {
    // Sanitize and validate user input
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $email_address = mysqli_real_escape_string($conn, $_POST["email_address"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST["confirmPassword"]);
    $signatureData = mysqli_real_escape_string($conn, $_POST["signatureData"]);

    // Check if passwords match
    if ($password != $confirmPassword) {
        $error = "Passwords do not Match";
    } else {
        // Validate email address format
        if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid Email Address";
        } else {
            // Check if the email address already exists in the lupon_accounts table
            $email_check_query = "SELECT * FROM lupon_accounts WHERE email_address='$email_address' LIMIT 1";
            $result = $conn->query($email_check_query);

            if ($result->num_rows > 0) {
                $msg_error = "Email Address already exists";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert data into the 'lupon_accounts' table
                $sql = "INSERT INTO lupon_accounts (first_name, last_name, email_address, password, pb_id, signature_image) VALUES ('$first_name', '$last_name', '$email_address', '$hashed_password', '$pb_id', '$signatureData')";

                if ($conn->query($sql) === TRUE) {
                    // Redirect to manage_accounts.php after successful account creation
                    header("Location: manage_accounts.php");
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
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
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dilg.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <title>Add Lupon Account</title>
</head>
<body>
<nav class="sidebar close">
        <header>
                    <div class="image-text">
                    <?php
                    $select = mysqli_query($conn, "SELECT * FROM `pb_accounts` WHERE pb_id = '$pb_id'") or die('Query failed');

                    if (mysqli_num_rows($select) > 0) {
                        $fetch = mysqli_fetch_assoc($select);
                    }

                    if (!empty($fetch['barangay'])) {
                        $barangay = $fetch['barangay'];

                        if (isset($config['barangayLogos'][$barangay])) {
                            echo '<span class="image"><img src="' . $config['barangayLogos'][$barangay] . '"></span>';
                        } else {
                            echo '<span class="image"><img src="../images/default_logo.png"></span>';
                        }
                    } else {
                        echo '<span class="image"><img src="../images/default_logo.png"></span>';
                    }
                    ?>

                    <div class="text logo-text">
                        <span class="name"><?php echo $barangay_captain ?></span>
                        <span class="profession"  style="font-size: 13px;">Punong Barangay</span>
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
                            <i class='bx bx-receipt icon' ></i>
                            <span class="text nav-text">Incident Reports</span>
                        </a>
                    </li>


                    <li class="nav-link">
                        <a href="activity_history.php">
                            <i class='bx bx-history icon'></i>
                            <span class="text nav-text">Activity History</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="analytics.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Analytics</span>
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
                <a href="manage_accounts.php">
                <i class="fa-solid fa-users-line icon"></i>
                        <span class="text nav-text">Manage Accounts</span>
                    </a>
                </li>

           
                <li class="">
                    <a href="../logout.php">
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
                <div class="header-text">Add Lupon Account</div>
                
                <form action="" method="post" style="height: 490px;" id="barangayForm">
                    <div class="fields">
                        <div class="input-field-1">
                            <label class="required-label">First Name</label>
                            <input type="text" onkeypress="return validateName(event)" name="first_name">
                        </div>
                        <div class="input-field-1">
                            <label class="required-label">Last Name</label>
                            <input type="text" onkeypress="return validateName(event)" name="last_name">
                        </div>
                        <div class="input-field" style="width: 100%;">
                            <label class="required-label">Email Address</label>
                            <input type="text" name="email_address" id="email_address" required>
                        </div>

                       
                            <div class="input-field-1" style="width: 100%;">
                                <label class="required-label">Password</label>
                                <i class="uil uil-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Password must contain: At least one lowercase letter (a-z), at least one uppercase letter (A-Z), at least one number, and at least one special character." style="right: 0; display: flex; margin-top: -2.3%; margin-left: 10%;"></i>
                                <div class="pw-meter" style="margin-top: 3px;">
                                <input type="password" name="password" id="password" placeholder="" required>
                                <div class="pw-display-toggle-btn" style="margin-top: 178px;">
                                    <i class='showHidePw' style="font-size: 22px; cursor: pointer; margin-left: -25px;"></i>
                                </div>

                            <div class="pw-strength" id="strength-meter">
                                <span></span>
                                <span></span>
                            </div>

                        </div>
                        
                                
                        </div>
                    
                        <div class="input-field-1" style="width: 100%;">
                            <label class="required-label">Confirm Password</label>
                            <input type="password" name="confirmPassword" placeholder="" required>
                        </div>

                        <?php if (isset($error) && !empty($error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.5%;"><?php echo $error; ?></span>
            <i class="fas fa-times" style="margin-left: 60%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

    <?php if (isset($msg_error) && !empty($msg_error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.5%;"><?php echo $msg_error; ?></span>
            <i class="fas fa-times" style="margin-left: 55%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

                    </div>
                
                    <!--<div class="signature-container" id="openModalBtn">
                        Place Signature Here
                    </div>-->
                    <div class="input-field" style="margin-top: -23px;">
                    <div id="copyMessage" class="clipboard" style="display: none; margin-top: 5px;">Copied to Clipboard!</div>
                    </div>

                    <div class="input-group1 d-flex" style="margin-top: 8%;">
                        <input type="button" value="Back" class="btn btn-secondary back-btn" style="width: 10%; margin-left: 470px;" onclick="history.back()">
                        <input type="button" id="openModalBtn" value="Create Account" class="btn btn-danger" style="width: 25%; margin-left: 10px;" disabled>
                    </div>

                    
                    <div id="signatureModal" class="modal">
                        <div class="modal-content">
                            <p style="text-align: justify; font-size: 13px;">By adding a digital signature, you are ensuring the authenticity and integrity of the account registration.</p>
                            <div class="signature-pad">
                                <canvas id="signatureCanvas" width="400" height="200"></canvas>
                            </div>
                            <div class="clear-signature" id="clearSignatureBtn">Clear Signature</div>
                            <div class="input-group1 d-flex" style="margin-top: 3%;">
                                <input type="button" value="Cancel" id="closeModalBtn" class="btn btn-secondary back-btn" style="width: 15%; margin-left: 60%; font-size: 17px; text-align: center; padding: 10px 10px;">
                                <input type="submit" name="submit" value="Confirm" class="btn btn-danger" id="saveSignatureBtn" style="width: 30%; margin-left: 15px; font-size: 17px; text-transform: uppercase;">
                            </div>
                        </div>
                        <input type="hidden" id="signatureData" name="signatureData">
                    </div>
                    
                    

                </form>
            </div>
            </center>

    </section>

    <script src="searchbar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script>
        function validateName(event) {
  var keyCode = event.keyCode;
  var inputValue = event.target.value;

  // Check if the length exceeds 20 characters
  if (inputValue.length >= 20 && keyCode !== 8) {
    event.preventDefault();
    return false;
  }

  if (keyCode === 32) {
    if (event.target.selectionStart === 0) {
      event.preventDefault();
      return false;
    }
  }

  // Capitalize the first character
  if (inputValue.length === 0 || inputValue.slice(0, event.target.selectionStart).trim() === '') {
    event.target.value = inputValue.charAt(0).toUpperCase() + inputValue.slice(1);
  }

  if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122)) {
    return true;
  }

  if (keyCode === 32 || keyCode === 46 || keyCode === 45 || keyCode === 9) {
    return true;
  }

  event.preventDefault();
  return false;
}

        document.addEventListener('DOMContentLoaded', function () {
    const barangayForm = document.getElementById('barangayForm');
    const createAccountBtn = document.getElementById('openModalBtn');

    // Function to check if all required fields are filled
    function isFormValid() {
        const requiredFields = ['first_name', 'last_name', 'email_address', 'password', 'confirmPassword'];
        return requiredFields.every(field => {
            const inputField = document.querySelector(`[name="${field}"]`);
            return inputField && inputField.value.trim() !== '';
        });
    }

    // Function to enable/disable the "Create Account" button based on form validity
    function updateCreateAccountButtonState() {
        createAccountBtn.disabled = !isFormValid();
    }

    // Add event listeners to form inputs to update the button state on input change
    barangayForm.addEventListener('input', updateCreateAccountButtonState);

    // Initial state check
    updateCreateAccountButtonState();

    // ... (rest of your script)
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



    const confirmPasswordInput = document.querySelector('input[name="confirmPassword"]');
const showHidePwIcon = document.querySelector('.showHidePw');

showHidePwIcon.addEventListener('click', () => {
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        showHidePwIcon.classList.remove('uil-eye-slash');
        showHidePwIcon.classList.add('uil-eye');
    } else {
        confirmPasswordInput.type = 'password';
        showHidePwIcon.classList.remove('uil-eye');
        showHidePwIcon.classList.add('uil-eye-slash');
    }
});

function getPasswordStrength(password){
    let s = 0;
        if(password.length > 6){
          s++;
        }

        if(password.length > 10){
          s++;
        }

        if(/[A-Z]/.test(password)){
          s++;
        }

        if(/[0-9]/.test(password)){
          s++;
        }

        if(/[^A-Za-z0-9]/.test(password)){
          s++;
        }

        return s;
}
      
const passwordInput = document.querySelector(".pw-meter #password");
const passwordStrengthMeter = document.querySelector(".pw-meter .pw-strength");

passwordInput.addEventListener("input", function () {
    // Check if there is input in the password field
    if (this.value.trim().length > 0) {
        passwordStrengthMeter.style.display = "block";
    } else {
        passwordStrengthMeter.style.display = "none";
    }
});

passwordInput.addEventListener("blur", function () {
    // Hide the meter when the field loses focus
    passwordStrengthMeter.style.display = "none";
});


      
document.querySelector(".pw-meter .pw-display-toggle-btn").addEventListener("click",function(){
    let el = document.querySelector(".pw-meter .pw-display-toggle-btn");
        
        if(el.classList.contains("active")){
            document.querySelector(".pw-meter #password").setAttribute("type","password");
            el.classList.remove("active");
        } else{
            document.querySelector(".pw-meter #password").setAttribute("type","text");
            el.classList.add("active");
        }
});
      
    document.querySelector(".pw-meter #password").addEventListener("keyup",function(e){
        let password = e.target.value;
        let strength = getPasswordStrength(password);
        let passwordStrengthSpans = document.querySelectorAll(".pw-meter .pw-strength span");
        strength = Math.max(strength,1);
        passwordStrengthSpans[1].style.width = strength*20 + "%";
        
        if(strength < 2){
          passwordStrengthSpans[0].innerText = "Weak";
          passwordStrengthSpans[0].style.color = "#111";
          passwordStrengthSpans[1].style.background = "#d13636";
        } else if(strength >= 2 && strength <= 4){
          passwordStrengthSpans[0].innerText = "Medium";
          passwordStrengthSpans[0].style.color = "#111";
          passwordStrengthSpans[1].style.background = "#e6da44";
        } else {
          passwordStrengthSpans[0].innerText = "Strong";
          passwordStrengthSpans[0].style.color = "#fff";
          passwordStrengthSpans[1].style.background = "#20a820";
        }
    });



</script>

<style>
    .add-account-container{
        height: 600px; width: 800px; margin-top: 2px;
    }

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

.pw-meter .form-element {
    position:absolute;
  }
  .pw-meter label {
    display:block;
  }
  .pw-meter input {
    padding:8px 30px 8px 10px;
    width:100%;
    outline:none;
  }
  .pw-meter .pw-display-toggle-btn {
    position:absolute;
    right:10px;
    top:45px;
    width:20px;
    height:20px;
    text-align:center;
    line-height:20px;
    cursor:pointer;
  }
  .pw-meter .pw-display-toggle-btn i.fa-eye-slash {
    display:none;
  }
  .pw-meter .pw-display-toggle-btn.active i.fa-eye-slash {
    display:block;
  }
  .pw-meter .pw-display-toggle-btn.active i.fa-eye {
    display:none;
  }
  .pw-meter .pw-strength {
    position:relative;
    width:100%;
    height:25px;
    margin-top:10px;
    text-align:center;
    background:#f2f2f2;
    display:none;
  }
  .pw-meter .pw-strength span:nth-child(1) {
    position:relative;
    font-size:14px;
    padding-bottom: 5px;
    text-transform: uppercase;
    color:#111;
    z-index:2;
    font-weight:600;
  }
  .pw-meter .pw-strength span:nth-child(2) {
    position:absolute;
    top:0px;
    left:0px;
    width:0%;
    height:100%;
    border-radius:5px;
    z-index:1;
    transition:all 300ms ease-in-out;
  }

  .required-label::after{
    content: '*';
    color: red;
    margin-left: 5px;
    }

    @media screen and (min-width: 1331px){
        .add-account-container{
            margin-top: -1.5%;
            margin-left: -3%;
        }
    }

    @media screen and (min-width: 1360px) and (min-height: 768px){
        .add-account-container{
            margin-top: 5%;
        }
    }

    @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .add-account-container{
                margin-top: 9%;
            }

            .modal-content{
                margin-top: 18%;
            }
        }

    @media screen and (min-width: 1536px) and (min-height: 730px){
        .add-account-container{
            margin-top: 1.8%;
        }

        .modal-content{
            margin-top: 12%;
        }
    }

    @media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
        .add-account-container{
            margin-top: -2%;
            margin-left: -3%;
        }
    }

    @media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
        .add-account-container{
            margin-top: -2.5%;
            margin-left: -7%;
        }
    }

    @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
        .add-account-container{
            margin-top: -3.5%;
            margin-left: -5%;
        }
        .modal-content{
            margin-left: 5.5%;
        }
    }

</style>
</body>
</html>