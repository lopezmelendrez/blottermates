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

$query = "SELECT * FROM pb_accounts";
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
                    <input type="text" placeholder="Search...">
                </li>

                    <li class="nav-link">
                        <a href="home.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
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

            <a href="add-barangay-account.php" style="text-decoration: none; margin-left: 1%;"><div class="add-account" style="margin-top: -65px; margin-left: 240%; margin-bottom: 30px; width: 255px;">
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
                    echo '<th>Barangay Captain</th>';
                    echo '<th>Email Address</th>';
                    echo '<th style="padding: 13px;">Status</th>';
                    echo '<th style="padding: 14px;">Created At</th>';
                    echo '<th>Actions</th>';
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
                    // Display Activate button for disabled users
                    echo '<form action="activate_user.php" method="post" class="activate-form" onsubmit="return confirmActivate()">';
                    echo '<input type="hidden" name="pbId" value="' . $row['pb_id'] . '">';
                    echo '<button type="submit" class="btn activate">Activate</button>';
                    echo '</form>';
                } else {
                    // Display Disable button for active users
                    echo '<form action="disable_user.php" method="post" class="disable-form" onsubmit="return confirmDisable()">';
                    echo '<input type="hidden" name="pbId" value="' . $row['pb_id'] . '">';
                    echo '<button type="submit" class="btn disable">Disable</button>';
                    echo '</form>';
                }
                        echo '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                }
                ?>




        </div>

    </section>

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

    </script>

</body>
<style>
table {
            width: 1120px;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 3%;
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

</style>
</html>