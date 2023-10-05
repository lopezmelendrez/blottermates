<!--<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if(!isset($email)){
header('location: ../../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['incident_case_number'])) {
    $incident_case_number = $_GET['incident_case_number'];
} else {
    header("Location: ../lupon/home.php");
    exit;
}

$select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE `incident_case_number` = '$incident_case_number'") or die('query failed');
$row_data = mysqli_fetch_assoc($select);

if (!$row_data) {
    // If the incident_case_number is not found, handle the error accordingly, e.g., redirect back to the dashboard.
    header("Location: ../lupon/incompletenotices.php");
    exit;
}

$complainant_last_name = $row_data['complainant_last_name'];
$complainant_first_name = $row_data['complainant_first_name'];
$complainant_middle_name = $row_data['complainant_middle_name'];
$complainant_cellphone_number = $row_data['complainant_cellphone_number'];
$respondent_last_name = $row_data['respondent_last_name'];
$respondent_first_name = $row_data['respondent_first_name'];
$respondent_middle_name = $row_data['respondent_middle_name'];

?>-->
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
    <title>Notice Management Page</title>
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
                    <a href="#">
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
        <div class="container" style="margin-left: 15%; margin-top: 45px;">
            <header>NOTICE OF CASE - <?php echo $incident_case_number; ?></header>
            <form action="#">
                <div class="form first">
                    <div class="details personal">
                        <span class="title" style="font-style: italic;"><?php echo $complainant_last_name; ?> vs. <?php echo $respondent_last_name; ?> </span>
                        <div class="fields">
                            <div class="input-field-1" style="width: 43rem;">
                            <?php
             $select = mysqli_query($conn, "SELECT * FROM `hearing` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
             if(mysqli_num_rows($select) > 0){
                $fetch = mysqli_fetch_assoc($select);
             }
             $alphabet_month_form = date('M j, Y', strtotime($fetch['date_of_hearing']));
             $time_in_12_hour_format = date('g:i A', strtotime($fetch['time_of_hearing']));
            ?>
                                <label class="">Hearing Schedule</label>
                                <input type="text" onkeypress="return validateName(event)" value="<?php echo $alphabet_month_form; ?> - <?php echo $time_in_12_hour_format; ?>" disabled>
                            </div>
                            <div class="change-schedule" style="margin-left: 86%; font-size: 12px; text-decoration: none;">
                                <a href="change_schedule.php?incident_case_number=<?php echo $incident_case_number; ?>" style="text-decoration: none; color: inherit;">Change Schedule?</a>
                            </div>
                        </div>
                    </div>

                    <hr style="border: 1px solid #ccc; margin: 20px 0;">

                    <div class="details ID">
                        <span class="title"></span>
                        <div class="fields">
                            <table class="notice-table" style="width: 100%; margin-left: 1%;">
                                <thead>
                                    <tr style="text-align: center;">
                                        <th>Type of Notice</th>
                                        <th>Resident Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Hearing Notice</td>
                                        <td><?php echo $complainant_last_name; ?>, <?php echo $complainant_first_name; ?> <?php echo $complainant_middle_name; ?></td>
                                        <td><span class="to-notify">To Notify</span></td>
                                        <td>
                                        <span class="notify" onclick="showPopup()">Set to Notified</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Summon Notice</td>
                                        <td><?php echo $respondent_last_name; ?>, <?php echo $respondent_first_name; ?> <?php echo $respondent_middle_name; ?>.</td>
                                        <td>-</td>
                                        <td>
                                        <a href="http://localhost/barangay%20justice%20management%20system%2001/tcpdf/generate_summonRecord.php?incident_case_number=<?php echo $incident_case_number; ?>" id="summonButton" class="summon-record" style="text-decoration:none;">Generate Summon Record</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pangkat Notice</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>
                                        <a href="http://localhost/barangay%20justice%20management%20system%2001/tcpdf/generate_kp10.php?incident_case_number=<?php echo $incident_case_number; ?>" class="summon-record" style="text-decoration:none;">Generate Pangkat Constituition Record</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr style="border: 1px solid #ccc; margin: 20px 0;">
                        
                    </div> 
                </div>
            </form>
        </div>

        <div id="popup" class="popup">
            <center>
            <div class="modal">
            <h3 class="modal-title" style="font-size: 18px; text-align:center;">NOTIFY COMPLAINANT</h3>
            <hr style="border: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 14px; text-align: justify;">The complainant's contact number will promptly receive their hearing notice details via message.</p>
            <p style="font-size: 12px; margin-left: -28%; margin-top: 5%; font-weight: 600;">Complainant's Contact Number: </p>
            <div class="box" id="phoneNumberBox">
                <span id="phoneNumberText"><?php echo $complainant_cellphone_number ?></span>
            </div>
            <div class="button-container" style="display: flex;">
                <button class="backBtn" onclick="closePopup()" style="width: 300px; padding: 12px 12px; font-weight: 600;">BACK</button>
                <button class="backBtn" onclick="submitForm()" style="width: 300px; margin-left: 290px; padding: 12px 12px; font-weight: 600;"">NOTIFY</button>
            </div>
            </div>
            </center>
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

// Add a variable to track whether the button has been clicked
let isSummonNotified = false;

// Function to handle the button click
function handleSummonButtonClick() {
    // Check if the button has not been clicked
    if (!isSummonNotified) {
        // Update the button text to "Set to Notified"
        const summonButton = document.getElementById('summonButton');
        summonButton.textContent = 'Set to Notified';

        // Disable the link to prevent further clicks
        summonButton.removeAttribute('href');

        // Set the button as notified
        isSummonNotified = true;
    }
}

// Add a click event listener to the button
document.getElementById('summonButton').addEventListener('click', handleSummonButtonClick);



</script>
<script src="../script.js"></script>
<style>
    .title::before{
        content: "";
        background: transparent;
    }

    form .fields .input-field-1{
    display: flex;
    width: calc(100% / 1 - 15px);
    flex-direction: column;
    margin: 4px 0;
        }

    .notify{
        background: #fff;
        padding: 4px 4px;
        color: #2962ff;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 70%;
        margin-left: 20%;
    }

    .notify:hover{
        background: #2962ff;
        color: #fff;
        transition: .5s;
    }

    .change-schedule:hover{
        font-weight: 600;
        transition: .5s linear;
    }

    .summon-record{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 70%;
        margin-left: 20%;
    }
    

    td{
        font-size: 12px;
        text-align: center;
        text-transform: uppercase;
        margin-top: 30px;
        padding: 10px;
    }

    .to-notify{
        font-weight: 900;
        color: #bc1823;
    }


    .backBtn:hover{
    background-color: #bc1823;
    }

    .summon-record:hover{
        background: #2e5984;
        transition: .5s;
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

</style>

</body>
</html>
