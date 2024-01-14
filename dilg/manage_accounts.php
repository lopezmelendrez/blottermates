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

$query = "SELECT * FROM pb_accounts ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dilg.css">
    <link rel="stylesheet" href="../css/lupon_home.css">
    <title>Barangay Accounts</title>
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

    <h1 style="margin-left: 1%; margin-top: -2.3%; display: flex; font-size: 48px;">ACCOUNT MANAGEMENT</h1>

        <div class="container" style="margin-left: -1%; display: flex;">

            <a href="add-barangay-account.php" style="text-decoration: none; margin-left: 1%;"><div class="add-account">
                <i class='bx bx-folder-plus'></i>
                <p>Add Barangay Account</p>
            </div></a>
            </div>


        <?php
                // Check if there are no rows in the result
                if (mysqli_num_rows($result) == 0) {
                    echo '<div class="text-box" style="margin-left: 30%;
                    margin-top: 15%;
                    background: #b9bbb6;
                    border-radius: 5px;
                    color: #fff;
                    font-size: 35px;
                    width: 500px;
                    padding: 13px 13px;
                    text-align: center;
                    letter-spacing: 1;
                    text-transform: uppercase;">No Registered Barangay Accounts Found</div>';
                } else {
                    echo '<table>';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th style="padding: 14px;">Barangay Captain</th>';
                    echo '<th style="padding: 14px;">Email Address</th>';
                    echo '<th style="padding: 13px;">Status</th>';
                    echo '<th style="padding: 14px;">Created At</th>';
                    echo '<th style="padding: 14px;">Actions</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    // Loop through the fetched records and display them in the table
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $row['barangay_captain'] . '</td>';
                        echo '<td>' . $row['email_address'] . '</td>';
                        $status = $row['account_status'];
                        $displayStatus = ($status == 'active') ? '<span style="color: green; font-weight: 600;">ACTIVE</span>' : (($status == 'disabled') ? '<span style="color: #bc1823; font-weight: 600;">DISABLED</span>' : '<span style="color: black; font-weight: 600;">OFFLINE</span>');
                        echo '<td>' . $displayStatus . '</td>';
                        echo '<td>' . (new DateTime($row['created_at']))->format('d M Y') . '</td>';
                        echo '<td class="actions">';
                        echo '</form>';
                
                if ($status == 'disabled') {
                    echo '<button class="btn activate" onclick="showActivatePopup(' . $row['pb_id'] . ')">Activate</button>';
                } else {
                    echo '<button class="btn disable" onclick="showDisablePopup(' . $row['pb_id'] . ')">Disable</button>';
                }
                        echo '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                }
                ?>




        </div>

        <div id="disable_popup" class="popup">
    <center>
        <div class="modal" style="display: block;">
            <div class="modal-title" style="font-size: 28px; font-weight: 500; text-align: center;">DISABLE ACCOUNT</div>
            <hr style="border: 1px solid #828282; margin: 10px 0; margin-bottom: 3%;">
            <form action="disable_user.php" method="post" class="disable-form">
                <label style="font-size: 20px; margin-top: 6%; margin-bottom: 6%; letter-spacing: 1; text-transform: uppercase;">Are you sure you want to disable this user account?</label>
                <hr style="border: 1px solid #828282; margin: 10px 0; margin-bottom: 3%;">
                <div class="disable-buttons" style="display: flex;">
                    <input type="hidden" id="disable_pbId" name="pbId">
                    <button type="button" class="backBtn" onclick="closeDisablePopup()" style="margin-top: 8%; width: 120px; padding: 6px 6px; font-weight: 600; background: #fff; border: 1px solid #bc1823; border-radius: 5px; color: #bc1823; margin-left: 210px;">CANCEL</button>
                    <button type="submit" class="backBtn" style="margin-top: 8%; width: 180px; padding: 6px 6px; font-weight: 600; background: #bc1823; border: none; border-radius: 5px; color: #fff; margin-left: 5px;">CONFIRM</button>
                </div>
            </form>
        </div>
    </center>
</div>


<div id="activate_popup" class="popup">
    <center>
        <div class="modal" style="display: block;">
            <div class="modal-title" style="font-size: 28px; font-weight: 500;">ACTIVATE ACCOUNT</div>
            <hr style="border: 1px solid #828282; margin: 10px 0; margin-bottom: 3%;">
            <form action="activate_user.php" method="post" class="activate-form">
                <label style="font-size: 20px; margin-top: 6%; margin-bottom: 6%; letter-spacing: 1; text-transform: uppercase;">Are you sure you want to activate this user account?</label>
                <hr style="border: 1px solid #828282; margin: 10px 0; margin-bottom: 3%;">
                <div class="activate-buttons" style="display: flex;">
                    <button type="button" class="backBtn" onclick="closeActivatePopup()" style="margin-top: 8%; width: 120px; padding: 6px 6px; font-weight: 600; background: #fff; border: 1px solid #bc1823; border-radius: 5px; color: #bc1823; margin-left: 210px;">CANCEL</button>
                    <input type="hidden" id="activate_pbId" name="pbId" value="">
                    <button type="submit" class="backBtn" style="margin-top: 8%; width: 180px; padding: 6px 6px; font-weight: 600; background: #bc1823; border: none; border-radius: 5px; color: #fff; margin-left: 5px;">CONFIRM</button>
                </div>
            </form>
        </div>
    </center>
</div>


    </section>

    <script src="search_bar.js"></script>
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

        const timeElement = document.querySelector(".time");
const dateElement = document.querySelector(".date");

/**
 * @param {Date} date
 */
 function formatTime(date) {
  const hours = date.getHours();
  const hours12 = hours % 12 || 12;
  const minutes = date.getMinutes();
  const isAm = hours < 12;

  // Use conditional (ternary) operator to format hours without leading zero if it's a single digit
  const formattedHours = hours12 < 10 ? hours12.toString() : hours12;

  return `${formattedHours}:${minutes.toString().padStart(2, "0")} ${isAm ? "AM" : "PM"}`;
}

// Example usage:
const now = new Date();
console.log(formatTime(now)); // Outputs: "7:02 AM" for 07:02 and "12:15 PM" for 12:15


/**
* @param {Date} date
*/
function formatDate(date) {
const DAYS = [
"Sunday",
"Monday",
"Tuesday",
"Wednesday",
"Thursday",
"Friday",
"Saturday"
];
const MONTHS = [
"January",
"February",
"March",
"April",
"May",
"June",
"July",
"August",
"September",
"October",
"November",
"December"
];

return `${DAYS[date.getDay()]} - ${
MONTHS[date.getMonth()]
} ${date.getDate()}, ${date.getFullYear()}`;
}

setInterval(() => {
const now = new Date();

timeElement.textContent = formatTime(now);
dateElement.textContent = formatDate(now);
}, 200);


function confirmDisable() {
    return confirm("Are you sure you want to temporarily disable this user account?");
    }

    function confirmActivate() {
    return confirm("The user will now be authorized to use the account.");
    }

    function showDisablePopup(pbId) {
        // Display the disable popup
        var popup = document.getElementById("disable_popup");
        popup.style.display = "flex";

        // Set the luponId in the hidden input field of the popup form
        document.getElementById("disable_pbId").value = pbId;
    }

    function closeDisablePopup() {
        // Close the disable popup
        var popup = document.getElementById("disable_popup");
        popup.style.display = "none";
    }

    function showActivatePopup(pbId) {
        // Display the activation popup
        var popup = document.getElementById("activate_popup");
        popup.style.display = "flex";

        // Set the luponId in the hidden input field of the popup form
        document.getElementById("activate_pbId").value = pbId;
    }

    function closeActivatePopup() {
        // Close the activation popup
        var popup = document.getElementById("activate_popup");
        popup.style.display = "none";
}

    </script>

</body>
<style>

body{
    overflow-y: auto;
}
table {
            width: 1120px;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: -1%;
            margin-left: 1%;
            border-radius: 5px;
            margin-top: 0.5%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 12px;
            text-align: center;

        }

        th {
            background-color: #f2f2f2;
        }

        td{
            background-color: #fff;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 8px 12px;
            cursor: pointer;
        }

        .view {
            background-color: #4caf50;
            color: white;
        }

        .remove {
            background-color: #f44336;
            color: white;
        }

        .disable {
            background-color: #2196f3;
            color: white;
        }

        .activate {
            background-color: #fee12b;
            color: white;
        }

        .activate:hover {
        background-color: #fcd12a; 
        color: white;/* Adjust the color to a darker shade */
    }

        .view:hover {
    background-color: #388e3c; 
    color: #fff;
}

.remove:hover {
    background-color: #d32f2f;
    color: #fff;
}

.disable:hover {
    background-color: #1565c0; 
    color: #fff;
}

.add-account{
    margin-top: -65px; margin-left: 240%; margin-bottom: 30px; width: 255px;
}

.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    justify-content: center;
    align-items: center;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

.modal {
    display: flex;
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    margin-top: 180px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 520px;
    height: 350px;
    overflow-y: hidden;
    margin-left: 30%;
}

@media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .add-account{
                margin-left: 245%;
            }
            table{
                margin-left: 17%;
            }
            .modal{
                margin-left: 38%;
                margin-top: 16%;
            }
        }

@media screen and (min-width: 1536px) and (min-height: 730px){
    table{
        margin-left: 7.9%;
    }
    .modal{
        margin-left: 35%;
    }
} 

@media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
    .modal{
        margin-top: 10%;
    }
}

</style>
</html>