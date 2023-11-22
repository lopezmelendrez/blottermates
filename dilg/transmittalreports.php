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

if (isset($_POST['submit_search'])) {
    $search_case = mysqli_real_escape_string($conn, $_POST['search_case']);
    $query = "SELECT pb.barangay AS barangay, 
    mr.timestamp AS date_submitted, 
    mr.generate_report AS report,
    mr.pb_id as pbId
    FROM `monthly_reports` AS mr
    INNER JOIN `pb_accounts` AS pb ON mr.pb_id = pb.pb_id
    WHERE (MONTH(mr.timestamp) = MONTH(CURRENT_DATE()) AND YEAR(mr.timestamp) = YEAR(CURRENT_DATE()))
    AND pb.barangay LIKE '%$search_case%'
    ORDER BY mr.timestamp ASC";  // Added ORDER BY clause
} else {
    $query = "SELECT pb.barangay AS barangay, 
        mr.timestamp AS date_submitted, 
        mr.generate_report AS report,
        mr.pb_id as pbId
    FROM `monthly_reports` AS mr
    INNER JOIN `pb_accounts` AS pb ON mr.pb_id = pb.pb_id
    WHERE MONTH(mr.timestamp) = MONTH(CURRENT_DATE())
    AND YEAR(mr.timestamp) = YEAR(CURRENT_DATE())
    ORDER BY mr.timestamp ASC";  // Added ORDER BY clause
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
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
    <link rel="stylesheet" href="../css/lupon_home.css">
    <link rel="stylesheet" href="../css/incidentform.css">
    <link rel="stylesheet" href="../css/lupon.css">
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

    <h1 style="margin-left: 1%; margin-top: -2.3%; display: flex; font-size: 48px;">MONTHLY TRANSMITTAL REPORTS</h1>

    <div class="search-container">
            <form action="" method="post">
                <button class="case-button" style="padding: 0px 12px;">BARANGAY</button>
                <input type="text" class="search-input" name="search_case" placeholder="Search...">
                <button type="submit" name="submit_search" class="search-button" style="padding: 0px 12px;">Search</button>
            </form>
    </div>


    <?php
    if (mysqli_num_rows($result) == 0) {
        echo '<div class="text-box">No Monthly Transmittal Reports Found</div>';
    } else {

        echo '<div class="sort-container" style="margin-left: 65.5%; margin-top: 20px;">';
        echo '<select id="sort" style="height: 30px;" onchange="loadContent()">';
        echo '<option disabled selected>Sort By...</option>';
        echo '<option value="latest">From Latest to Oldest</option>';
        echo '<option value="oldest">From Oldest to Latest</option>';
        echo '</select>';
        echo '</div>';
        

            while ($row = mysqli_fetch_assoc($result)) {
                $barangay = $row['barangay'];
                $dateSubmitted = date('M d, Y', strtotime($row['date_submitted']));
                $transmittalReport = $row['report'];
                $pbId = $row['pbId'];

                echo '<div class="container" style="margin-top: 1%;">';
                echo '<table>';
                echo '<thead>';
                echo '<tr>';
                echo '<th>BARANGAY</th>';
                echo '<th>DATE SUBMITTED</th>';
                echo '<th>TRANSMITTAL REPORT</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                echo '<tr>';
                echo '<td>' . $barangay . '</td>';
                echo '<td>' . $dateSubmitted . '</td>';
        if (!empty($transmittalReport)) {
            echo '<td><span class="generate">VIEW</span></td>';
        } else {
            echo '<td></td>';
        }
                echo '</tr>';
                echo '</tbody>';
                echo '</table>';
                echo '<a href="monthly_reports.php?pb_id=' . urlencode($pbId) . '" style="text-decoration: none;">';
                echo '<button class="schedule" style="width: 18%; font-size: 14px; margin-left: 82%; margin-top: 10px;">SEE ALL</button>';
                echo '</a>';
                echo '</div>';
            }
        }
            ?>


        

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

function loadContent() {
    const selectedOption = document.getElementById("sort").value;

    if (selectedOption === "oldest") {
        window.location.href = "transmittalreports.php#oldest";
    } else if (selectedOption === "latest") {
        window.location.href = "transmittal_reports.php#latest";
    }
}

    </script>

</body>
<style>
    body{
        overflow-y: scroll;
    }
.search-container{
            margin-left: 6%;
            margin-top: 1%;
        }

        .search-input{
            width: 795px;
            padding: 0 12px;
        }

        .case-button{
            background: #E83422;
            border: none;
            color: #fff;
            font-weight: 500;
        }

        .search-button{
            background: #E83422;
            color: #fff;
            border: none;
            font-weight: 600;
        }

        .search-button:hover{
            background: #bc1823;
            transition: .2s;
        }

        .container{
            background: #f2f3f5;
            margin-left: 9%;
            margin-top: 3%;
            width: 1018px;
        }

        .container table thead tr th{
            font-size: 17px;
            text-align: center;
        }

        .to-notify{
                font-weight: 900;
                color: #bc1823;
        }

        table tbody tr:nth-child(even) {
        background-color: white;
        }

        .generate{
        background: #fff;
        padding: 4px 4px;
        color: #2962ff;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        margin-left: 20%;
        text-decoration: none;
        cursor: pointer;
    }

    .generate:hover{
        background: #2962ff;
        color: #fff;
        transition: .5s;
    }

    .notified{
        font-weight: 900;
        color: #0b6623;
    }

    .notify{
        background: #fff;
        padding: 4px 4px;
        color: #363636;
        border: 1px solid #363636;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        text-decoration: none;
        cursor: default;
    }

    .text-box{
        margin-left: 30%;
        margin-top: 15%;
        background: #bc1823;
        border-radius: 5px;
        color: #fff;
        font-size: 35px;
        width: 500px;
        padding: 13px 13px;
        text-align: center;
        letter-spacing: 1;
        text-transform: uppercase;
    }

    .validate-cases{
    flex: 1; /* Distribute available space evenly among child elements */
    text-align: center; /* Center align the text */
    padding: 5px 5px;
    width: 10px; /* Add padding for better spacing */
    border: 1px solid #C23B21; /* Add a border for separation */
    border-radius: 5px; /* Add rounded corners to the divs */
    background-color: #F2F3F5; /* Background color for the divs */
    color: #C23B21;
    font-size: 18px;
    font-weight: 600;
    margin-left: 3%;
    cursor: default;
}

</style>
</html>