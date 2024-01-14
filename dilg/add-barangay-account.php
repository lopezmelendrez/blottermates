<?php

include '../config.php';

session_start();

$account_id = $_SESSION['account_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$account_role = $_SESSION['account_role'];

if(!isset($account_id)){
header('location: ../index.php');
}

function barangayExists($barangayName, $conn)
{
    $barangayName = mysqli_real_escape_string($conn, $barangayName);
    $sql = "SELECT COUNT(*) as count FROM pb_accounts WHERE barangay = '$barangayName'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    return false;
}

if (isset($_POST['submit'])) {

    // Sanitize and validate user input
    $barangay = mysqli_real_escape_string($conn, $_POST["barangay"]);
    $barangay_captain = mysqli_real_escape_string($conn, $_POST["barangay_captain"]);
    $email_address = mysqli_real_escape_string($conn, $_POST["email_address"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST["confirmPassword"]);
    $signatureData = mysqli_real_escape_string($conn, $_POST["signatureData"]);

    // Check if passwords match
    if ($password != $confirmPassword) {
        // Handle password mismatch error
        echo "Passwords do not match.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the 'pb_accounts' table
        $sql = "INSERT INTO pb_accounts (account_id, barangay, barangay_captain, email_address, password, signature_image) VALUES ('$account_id', '$barangay', '$barangay_captain', '$email_address', '$hashed_password', '$signatureData')";


        if ($conn->query($sql) === TRUE) {
            // Redirect to manage_accounts.php after successful account creation
            header("Location: manage_accounts.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();
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
    <title>Add Barangay Account</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
</head>
<body>
<nav class="sidebar close">
            <header>
                <div class="image-text">
                    <span class="image">
                        <img src="../images/logo.png">
                    </span>

                    <div class="text logo-text">
                        <span class="name"><?php echo $first_name ?> </span>
                        <span class="profession"><?php echo $last_name ?></span>
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
                            <a href="transmittal_reports.php">
                            <i class="fa-solid fa-receipt icon"></i>
                                <span class="text nav-text" style="font-size: 16px;">Transmittal Reports</span>
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
            <div class="add-account-container" style="margin-left: -5%;">
                <div class="header-text">Add Barangay Account</div>
                
                <form action="" method="post" id="barangayForm">
                    <div class="fields">
                        <div class="input-field-1">
                            <label>Barangay</label>
                            <select name="barangay" required>
                                <option value="DoNotDisplayEmail" disabled selected>Select...</option>
                                <option <?php echo barangayExists('Aplaya', $conn) ? 'disabled' : ''; ?>>Aplaya</option>
                                <option <?php echo barangayExists('Balibago', $conn) ? 'disabled' : ''; ?>>Balibago</option>
                                <option <?php echo barangayExists('Caingin', $conn) ? 'disabled' : ''; ?>>Caingin</option>
                                <option <?php echo barangayExists('Dila', $conn) ? 'disabled' : ''; ?>>Dila</option>
                                <option <?php echo barangayExists('Dita', $conn) ? 'disabled' : ''; ?>>Dita</option>
                                <option <?php echo barangayExists('Don Jose', $conn) ? 'disabled' : ''; ?>>Don Jose</option>
                                <option <?php echo barangayExists('Ibaba', $conn) ? 'disabled' : ''; ?>>Ibaba</option>
                                <option <?php echo barangayExists('Kanluran', $conn) ? 'disabled' : ''; ?>>Kanluran</option>
                                <option <?php echo barangayExists('Labas', $conn) ? 'disabled' : ''; ?>>Labas</option>
                                <option <?php echo barangayExists('Macabling', $conn) ? 'disabled' : ''; ?>>Macabling</option>
                                <option <?php echo barangayExists('Malitlit', $conn) ? 'disabled' : ''; ?>>Malitlit</option>
                                <option <?php echo barangayExists('Malusak', $conn) ? 'disabled' : ''; ?>>Malusak</option>
                                <option <?php echo barangayExists('Market Area', $conn) ? 'disabled' : ''; ?>>Market Area</option>
                                <option <?php echo barangayExists('Pook', $conn) ? 'disabled' : ''; ?>>Pook</option>
                                <option <?php echo barangayExists('Pulong Santa Cruz', $conn) ? 'disabled' : ''; ?>>Pulong Santa Cruz</option>
                                <option <?php echo barangayExists('Santo Domingo', $conn) ? 'disabled' : ''; ?>>Santo Domingo</option>
                                <option <?php echo barangayExists('Sinalhan', $conn) ? 'disabled' : ''; ?>>Sinalhan</option>
                                <option <?php echo barangayExists('Tagapo', $conn) ? 'disabled' : ''; ?>>Tagapo</option>
                            </select>
                        </div>
                        <div class="input-field-1">
                            <label>Punong Barangay</label>
                            <input type="text" name="barangay_captain">
                        </div>
                        <div class="input-field">
                            <label>Email Address</label>
                            <input type="text" name="email_address" id="email_address" required>
                        </div>
                        <div class="input-field" style="margin-top: -2px;">
                            <label>Password</label>
                            <input type="text" name="password" id="passwordField" placeholder="" required>
                            <div class="pw-display-toggle-btn" style="margin-top: -40px; margin-left: 85%;">
                                    <i class='bx bxs-copy' id="copyIcon" style="font-size: 22px;"></i>
                            </div>
                                
                        </div>
                    
                        <div class="input-field">
                            <label>Confirm Password</label>
                            <input type="password" name="confirmPassword" placeholder="" required>
                            <div class="pw-display-toggle-btn" style="margin-top: -40px; margin-left: 85%;">
                                <i class='uil uil-eye-slash showHidePw' style="font-size: 22px; cursor: pointer;"></i>
                            </div>
                        </div>

                    </div>
                
                    <!--<div class="signature-container" id="openModalBtn">
                        Place Signature Here
                    </div>-->
                    <div class="input-field" style="margin-top: -23px;">
                    <div id="copyMessage" class="clipboard" style="display: none; margin-top: 5px;">Copied to Clipboard!</div>
                    </div>

                    <div class="input-group1 d-flex" style="margin-top: 8%;">
                        <input type="button" value="Back" class="btn btn-secondary back-btn" style="width: 10%; margin-left: 660px;" onclick="history.back()">
                        <input type="button" id="openModalBtn" value="Create Account" class="btn btn-danger" style="width: 20%; margin-left: 10px;" disabled>
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

    <script src="search_bar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
        const barangayForm = document.getElementById('barangayForm');
        const createAccountBtn = document.getElementById('openModalBtn');

        // Function to check if all required fields are filled
        function isFormValid() {
            const requiredFields = ['barangay', 'barangay_captain', 'email_address', 'password', 'confirmPassword'];
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

// Get references to the select element and email input field
const barangaySelect = document.querySelector('select[name="barangay"]');
const emailInput = document.querySelector('#email_address');
const passwordInput = document.querySelector('input[name="password"]');

function generatePassword(selectedBarangay) {
        const consonants = 'bcdfghjklmnpqrstvwxyz';
        const passwordLength = 10; // You can adjust the length as needed
        let password = '';

        // Include "Admin_" in the password
        password += 'PB_';

        // Include consonants from the selected Barangay
        for (const char of selectedBarangay) {
            if (consonants.includes(char)) {
                password += char;
            }
        }

        // Generate 3 random numbers
        for (let i = 0; i < 3; i++) {
            password += Math.floor(Math.random() * 10); // Random digit (0-9)
        }

        // Add a special character (e.g., !)
        password += '!';

        // Add "Barangay" to the password
        password += 'Barangay';

        return password;
    }


    // Define a function to update the email input field
    function updateEmail() {
        const selectedBarangay = barangaySelect.value;
        const barangayCaptainInput = document.querySelector('input[name="barangay_captain"]');
        if (selectedBarangay === 'DoNotDisplayEmail') {
            // Hide the email input field
            emailInput.style.display = 'block';
            emailInput.value = "";
            passwordInput.value = '';
        } else {
            // Show the email input field and set its value
            emailInput.style.display = 'block';
            const emailSuffix = `barangay_${selectedBarangay.toLowerCase()}@admin.com`;
            emailInput.value = emailSuffix;
            const generatedPassword = generatePassword(selectedBarangay);
            passwordInput.value = generatedPassword;
        }

        const captains = {
        'Aplaya': "Fe B. Villanueva",
        'Balibago': "Ariel D. Gomez",
        'Caingin': "Christopher B. Dictado",
        'Dila': "Jose C. Cartaño",
        'Dita': "Godofredo Z. Dela Rosa",
        'Don Jose': "Irineo L. Aala Jr.",
        'Ibaba': "Relly M. Medina",
        'Labas': "Ronald Ian A. De Guzman",
        'Macabling': "Jejomar Jojo Alvin B. Banal",
        'Malitlit': "Cesar C. Hernandez",
        'Malusak': "Ramon E. Dia",
        'Market Area': "Michael I. Ambata",
        'Kanluran': "Ronald A. Daroy",
        'Pook': "Alvin R. Cartaño",
        'Pulong Santa Cruz': "Constancia L. Dones",
        'Santo Domingo': "Lily P. Ortega",
        'Sinalhan': "Soledad C. De Leon",
        'Tagapo': "Arturo S. Catindig"
    };

    if (captains.hasOwnProperty(selectedBarangay)) {
        // Set the captain for the selected barangay
        barangayCaptainInput.value = captains[selectedBarangay];
    } else {
        // Clear the captain field if not found
        barangayCaptainInput.value = "";
    }

    }

    

    // Add an event listener to the select element
    barangaySelect.addEventListener('change', updateEmail);

    // Call the function initially to set the initial email value and visibility
    updateEmail();

    const passwordField = document.getElementById('passwordField');
    const copyIcon = document.getElementById('copyIcon');
    const copyMessage = document.getElementById('copyMessage');

    // Add a click event listener to the copy icon
    copyIcon.addEventListener('click', () => {
        // Select the text in the password input field
        passwordField.select();
        passwordField.setSelectionRange(0, 99999); // For mobile devices

        // Copy the selected text to the clipboard
        document.execCommand('copy');

        // Display the "Copied to clipboard" message
        copyMessage.style.display = 'block';

        // Hide the message after a short delay
        setTimeout(() => {
            copyMessage.style.display = 'none';
        }, 1000); // Display for 2 seconds
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

@media screen and (min-width: 1536px) and (min-height: 730px){
    .add-account-container{
        margin-top: 7%;
    }

    .modal-content{
        margin-top: 11.5%;
    }
}


</style>


</body>
</html>