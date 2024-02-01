<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (isset($_POST['submit'])) {

    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $email_address = mysqli_real_escape_string($conn, $_POST["email_address"]);
    $old_password = mysqli_real_escape_string($conn, $_POST["old_password"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST["confirmPassword"]);

    $check_password_query = "SELECT password FROM lupon_accounts WHERE email_address = '$email'";
    $result = $conn->query($check_password_query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $old_password_hash = $row['password'];

        if (password_verify($old_password, $old_password_hash)) {

            if (!empty($password) && !empty($confirmPassword)) {
                if ($password != $confirmPassword) {
                    $error = "New and Confirm Password do not Match";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    // Include the new password in the update query
                    $update_query = "UPDATE lupon_accounts SET 
                        first_name = '$first_name',
                        last_name = '$last_name',
                        email_address = '$email_address',
                        password = '$hashed_password'
                        WHERE email_address = '$email'";
                }
            } else {
                // Do not update the password in the query
                $update_query = "UPDATE lupon_accounts SET 
                    first_name = '$first_name',
                    last_name = '$last_name',
                    email_address = '$email_address'
                    WHERE email_address = '$email'";
            }
    
            // Execute the update query
            if (!empty($update_query) && $conn->query($update_query) === TRUE) {
                // Redirect to the manage_accounts.php page after a successful update
                header("Location: my_account.php");
                exit();
            } else {
            }
        } else {
            $msg_error = "Old Password is Incorrect";
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
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/dilg.css">
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>My Account</title>
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
                <div class="header-text">MY ACCOUNT</div>

                <?php
             $select = mysqli_query($conn, "SELECT * FROM `lupon_accounts` WHERE email_address = '$email'") or die('query failed');
             if(mysqli_num_rows($select) > 0){
                $fetch = mysqli_fetch_assoc($select);
             }
             
            ?>
                
                <form action="" method="post" style="height: 490px; width: 750px;">
                    <div class="fields">
                        <div class="input-field-1">
                            <label>First Name</label>
                            <input type="text" name="first_name" onkeypress="return validateName(event)" value="<?php echo $fetch['first_name']; ?>">
                        </div>
                        <div class="input-field-1">
                            <label>Last Name</label>
                            <input type="text" name="last_name" onkeypress="return validateName(event)" value="<?php echo $fetch['last_name']; ?>">
                        </div>
                        <div class="input-field-1">
                            <label>Email Address</label>
                            <input type="text" name="email_address" value="<?php echo $fetch['email_address']; ?>">
                        </div>
                        <div class="input-field-1">
                            <label class="required-label">Old Password</label>
                            <input type="password" name="old_password" onkeydown="return preventSpace(event)" placeholder="" required>
                        </div>
                        <div class="pw-display-toggle-btn" style="margin-top: -9.4%; margin-left: 95%;">
                                <i class='uil uil-eye-slash showHidePw' style="font-size: 22px; cursor: pointer;"></i>
                            </div>

                        <?php if (isset($msg_error) && !empty($msg_error)) { ?>
                            <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 50%; margin-top: -1%; padding: 2px 2px; margin-left: 380px;">
                                <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 1%; font-size: 20px; color: #D52826;"></i>
                                <span style="margin-left: 5%; font-size: 15.5px; color: #D52826; font-weight: 600; margin-top: 1%;"><?php echo $msg_error; ?></span>
                                <i class="fas fa-times" style="margin-left: 21%; margin-top: 0.5%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
                            </div>
                        <?php } ?>

                       
                            <div class="input-field-1" style="width: 100%;">
                                <label>New Password</label>
                                <i class="uil uil-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Password must contain: At least one lowercase letter (a-z), at least one uppercase letter (A-Z), at least one number, and at least one special character." style="right: 0; display: flex; margin-top: -2.3%; margin-left: 13%;"></i>
                                <div class="pw-meter" style="margin-top: 3px;">
                                <input type="password" name="password" onkeydown="return preventSpace(event)" id="password" placeholder="">
                                <div class="pw-display-toggle-btn" style="margin-top: 23.8%; margin-right: 1.7%;">
                                <i class='uil uil-eye-slash showHidePw2' style="font-size: 22px; cursor: pointer;"></i>
                            </div>

                            <div class="pw-strength" id="strength-meter">
                                <span></span>
                                <span></span>
                            </div>

                        </div>
                        
                                
                        </div>
                        
                    
                        <div class="input-field-1" style="width: 100%;">
                            <label>Confirm Password</label>
                            <input type="password" onkeydown="return preventSpace(event)" name="confirmPassword" placeholder="">
                        </div>

                        <?php if (isset($error) && !empty($error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.5%;"><?php echo $error; ?></span>
            <i class="fas fa-times" style="margin-left: 42%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

                    </div>
                
                    <div class="input-field" style="margin-top: -23px;">
                    <div id="copyMessage" class="clipboard" style="display: none; margin-top: 5px;">Copied to Clipboard!</div>
                    </div>

                    <div class="input-group1 d-flex" style="margin-top: 7%;">
                        <input type="button" value="Back" class="btn btn-secondary back-btn" style="width: 10%; margin-left: 430px; font-size: 20px;" onclick="history.back()">
                        <input type="button" id="openModalBtn" value="Update Account" class="btn btn-danger" style="width: 30%; margin-left: 10px; font-size: 20px;" disabled>
                    </div>

                    
                    <div id="signatureModal" class="modal">
                        <div class="modal-content">
                        <p style="text-align: justify; font-size: 13px;">By updating your digital signature, you are ensuring the ongoing authenticity and integrity of your account information.</p>
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

    <script src="search_bar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script>

function preventSpace(event) {
  // Check if the pressed key is a space and if it's the first character
  if (event.key === ' ' && event.target.selectionStart === 0) {
    // Prevent the space from being added to the input
    event.preventDefault();
    return false;
  }
  // Allow other keypress events
  return true;
}

function validateName(event) {
  var keyCode = event.keyCode;
  
  // Check if the pressed key is a space
  if (keyCode === 32) {
    // Check if it's the first character
    if (event.target.selectionStart === 0) {
      // Prevent the space from being added to the input
      event.preventDefault();
      return false;
    }
  }

  // Your existing validation logic
  if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122)) {
    return true;
  }

  if (keyCode === 32 || keyCode === 46 || keyCode === 45 || keyCode === 9) {
    return true;
  }

  // Prevent other characters if not allowed
  event.preventDefault();
  return false;
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

        document.addEventListener("DOMContentLoaded", function () {
    // Get references to the input fields
    const oldPasswordInput = document.querySelector("input[name='old_password']");
    const newPasswordInput = document.querySelector("input[name='password']");
    const confirmPasswordInput = document.querySelector("input[name='confirmPassword']");
    const firstNameInput = document.querySelector("input[name='first_name']");
    const lastNameInput = document.querySelector("input[name='last_name']");
    const emailInput = document.querySelector("input[name='email_address']");
    const updateAccountBtn = document.getElementById("openModalBtn");

    // Function to check if the conditions for enabling the button are met
    function checkConditions() {
        const oldPasswordNotEmpty = oldPasswordInput.value.trim() !== "";
        const newPasswordNotEmpty = newPasswordInput.value.trim() !== "";
        const confirmPasswordNotEmpty = confirmPasswordInput.value.trim() !== "";
        const firstNameNotEmpty = firstNameInput.value.trim() !== "";
        const lastNameNotEmpty = lastNameInput.value.trim() !== "";
        const emailNotEmpty = emailInput.value.trim() !== "";

        // Enable the button if the conditions are met, otherwise disable it
        updateAccountBtn.disabled = !(oldPasswordNotEmpty && ((newPasswordNotEmpty && confirmPasswordNotEmpty) || (!newPasswordNotEmpty && !confirmPasswordNotEmpty)) && (firstNameNotEmpty || lastNameNotEmpty || emailNotEmpty));
    }

    // Attach event listeners to input fields to check conditions on input
    oldPasswordInput.addEventListener("input", checkConditions);
    newPasswordInput.addEventListener("input", checkConditions);
    confirmPasswordInput.addEventListener("input", checkConditions);
    firstNameInput.addEventListener("input", checkConditions);
    lastNameInput.addEventListener("input", checkConditions);
    emailInput.addEventListener("input", checkConditions);
});
        

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
    // Check if the old password field is not empty
    const oldPasswordInput = document.querySelector("input[name='old_password']");
    const passwordInput = document.querySelector("input[name='password']");
    const confirmPasswordInput = document.querySelector("input[name='confirmPassword']");

    if (oldPasswordInput.value.trim() === "") {
        // Display the alert message for empty old password
        alert("Please enter your old password.");
    } else {
        // Open the modal if the old password is not empty and passwords match
        modal.style.display = "block";
    }
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
    
    const confirmPasswordInput = document.querySelector('input[name="old_password"]');
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

    const confirmPasswordInput2 = document.querySelector('input[name="password"]');
    const showHidePwIcon2 = document.querySelector('.showHidePw2');

    showHidePwIcon2.addEventListener('click', () => {
        if (confirmPasswordInput2.type === 'password') {
            confirmPasswordInput2.type = 'text';
            showHidePwIcon2.classList.remove('uil-eye-slash');
            showHidePwIcon2.classList.add('uil-eye');
        } else {
            confirmPasswordInput2.type = 'password';
            showHidePwIcon2.classList.remove('uil-eye');
            showHidePwIcon2.classList.add('uil-eye-slash');
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

    .required-label::after{
    content: '*';
    color: red;
    margin-left: 5px;
    }

    .add-account-container{
        height: 600px; width: 800px; margin-top: 2px
    }

    @media screen and (min-width: 1331px){
        .add-account-container{
            margin-top: -1.6%;
            margin-left: -5%;
        }
    }

    @media screen and (min-width: 1360px) and (min-height: 768px) {
        .add-account-container{
            margin-top: 3.5%;
        }
    }

    @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .add-account-container{
                margin-top: 8%;
            }

            .modal-content{
                margin-top: 18%;
            }
        }
    
    @media screen and (min-width: 1536px) and (min-height: 730px){
        .add-account-container{
            margin-top: 2.2%;
            margin-left: -2%;
        }

        .modal-content{
            margin-top: 10.5%;
            margin-left: 5%;
        }
    }

    @media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
        .add-account-container{
            margin-left: -8%;
            margin-top: -2.6%;
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

    @media screen and (min-width: 1360px) and (min-height: 681px){
           .add-account-container{
            margin-top: 0%;
           }
           .modal-content{
            margin-top: 12%;
            margin-left: 6%;
           }
        }
    
        @media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
        .add-account-container{
            margin-top: 1.3%;
            margin-left: -6%;
        }
    }

    @media screen and (max-width: 2133px) and (min-height: 1055px) and (max-height: 1058px){
        .add-account-container{
            margin-top: 7.3%;
            margin-left: -5%;
        }
        .modal-content{
            margin-top: 15%;
            margin-left: 2%;
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
            margin-top: 2%;
            margin-left: -2%;
        }
    }
    
    @media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
     .modal-content{
            position: absolute;
        top: 25%;
        left: 45%;
        transform: translate(-50%, -50%);
        }
        .add-account-container{
            margin-top: 0.5%;
            margin-left: -2%;
        }
    }




</style>
</body>
</html>