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
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <title>DILG Dashboard</title>
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="../images/logo.png">
                </span>

                <div class="text logo-text">
                    <span class="name"><?php echo $first_name ?> <?php echo $last_name ?></span>
                    <span class="profession"><?php echo $account_role ?></span>
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
                        <a href="#">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">Notifications</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Analytics</span>
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
                    <a href="../logout.php">
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

    <section class="home" style="margin-top: -1%;">
        
        <div class="datetime-container" style="display: flex;">
            <div class="datetime mb-3" style="margin-top: 2%">
                <div class="time" id="time"></div>
                <div class="date" style="font-size: 23px;"></div>
            </div>

            <a href="add-barangay-account.php" style="text-decoration: none; margin-left: 1%;"><div class="add-account" style="display: flex;">
                <i class='bx bx-folder-plus'></i>
                <p>Add Barangay Account</p>
            </div></a>

            <?php

        $query = "SELECT COUNT(*) AS total_ongoing_cases
                FROM `incident_report` AS ir
                INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
                WHERE h.date_of_hearing IS NOT NULL AND h.time_of_hearing IS NOT NULL
                AND NOT EXISTS (SELECT 1 FROM `amicable_settlement` AS amicable WHERE h.hearing_id = amicable.hearing_id)";

        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $totalOngoingCases = $row['total_ongoing_cases'];
        } else {
            $totalOngoingCases = 0; // Handle the case where the query fails.
        }

        ?>

        <div class="ongoing-cases-container" style="margin-left: 866.5px; position: fixed; width: 380px; height: 105px; margin-top: 14px;">
            <div class="text-box">
                <p style="padding-top: 8px; font-weight: 400; text-transform: uppercase; text-align: center;">Total Ongoing Incident Cases</p>
                <p style="padding-bottom: -6px; font-weight: 600; font-size: 50px; margin-top: -30px; text-align: center;"><?php echo $totalOngoingCases; ?></p>
            </div>
        </div>
        </div>


        <div class="incident-case-table" style="display: flex; height: 350px; width: 535px;">
    <div class="head-text">
        <p class="incident-case" style="font-size: 22px;">Top Barangays with Ongoing Incident Cases</p>
        <div class="table-container" style="max-height: 283px; overflow-y: hidden; margin-top: -6%;">
            <hr style="border: 1px solid #ebecf0; margin: 3px 0; width: 100%; margin-top: 3%">
            <table class="incident-table" style="width: 530px; margin-top: 3%;">
            <?php
        $select = mysqli_query($conn, "SELECT pb.barangay AS barangay, COUNT(ir.incident_case_number) AS total_cases
            FROM `incident_report` AS ir
            INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
            INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
            INNER JOIN `pb_accounts` AS pb ON la.pb_id = pb.pb_id
            WHERE h.date_of_hearing IS NOT NULL
                AND h.time_of_hearing IS NOT NULL
                AND NOT EXISTS (
                    SELECT 1
                    FROM `amicable_settlement` AS amicable
                    WHERE h.hearing_id = amicable.hearing_id
                )
            GROUP BY pb.barangay
        ")
        or die('query failed');
        
        while ($row = mysqli_fetch_assoc($select)) {
            echo "<tr>";
            echo "<td>" . $row['barangay'] . "</td>";
            
            echo "<td>" . $row['total_cases'] . "</td>";
            echo "</tr>";
        }
        ?>
            </table>
        </div>
    </div>
</div>

<div class="incident-case-table" style="display: flex; height: 350px; width: 650px; margin-top: -27.1%; margin-left: 44%;">
    <div class="head-text">
        <p class="incident-case" style="font-size: 22px;">Monthly Transmittal Reports</p>
        <div class="table-container" style="max-height: 283px; overflow-y: hidden; margin-top: -6%;">
            <hr style="border: 1px solid #ebecf0; margin: 3px 0; width: 100%; margin-top: 3%">
            <table class="incident-table" style="width: 530px; margin-top: 3%;">
            <p>habol ko 'to wait lang po babygirl mode muna</p>
            </table>
        </div>
    </div>
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

        modeSwitch.addEventListener("click" , () =>{
            body.classList.toggle("dark");
            
            if(body.classList.contains("dark")){
                modeText.innerText = "Light mode";
            }else{
                modeText.innerText = "Dark mode";
                
            }
        });

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

    </script>

</body>
</html>

<style>
    .ongoing-cases-container{
        background: white;
        border-radius: 5px;
        position: fixed;
        border: 2px solid #2E5895;
    }

    .incident-case-table{
    background: white;
    width: 45%;
    height: 400px;
    margin-top: 2%;
    border-radius: 8px;
    padding: 16px 24px;
    }

    .head-text .incident-case{
        font-size: 25px;
        font-weight: 500;
        display: flex;
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

    
</style>