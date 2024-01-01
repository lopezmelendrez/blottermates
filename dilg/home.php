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

$currentMonth = date("F");

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
    <title>DILG Santa Rosa Dashboard</title>
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

    <section class="home" style="margin-top: -1%;">
        
        <div class="datetime-container" style="display: flex;">
        <div class="datetime mb-3" style="width: 26rem;">
                <div class="time" id="time" style="padding-left: 15px;"></div>
                <div class="date" style="font-size: 21px; width: 24rem; padding-left: 15px;"></div>
            </div>

            <a href="add-barangay-account.php" style="text-decoration: none; margin-left: 1%;"><div class="add-account" style="display: flex; margin-top: 25%;">
                <i class='bx bx-folder-plus'></i>
                <p>Add Barangay Account</p>
            </div></a>

            <?php

$query = "SELECT COUNT(*) AS total_ongoing_cases
FROM `incident_report` AS ir
WHERE NOT EXISTS (
    SELECT 1
    FROM `amicable_settlement` AS amicable
    WHERE ir.incident_case_number = amicable.incident_case_number
)";


        $result = mysqli_query($conn, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $totalOngoingCases = $row['total_ongoing_cases'];
        } else {
            $totalOngoingCases = 0; // Handle the case where the query fails.
        }

        ?>

<div class="lupon-online-box">
                    <div class="online" style="display: flex; margin-top: -5px;">
                    <i class='bx bx-notepad' style="font-size: 35px; font-weight: 500; margin-top: -4px; margin-left: -5px;"></i>
                    <p style="margin-top: 1.5px; margin-left: -20px; width: 17rem;">ONGOING CASES</p>
                    <p style="margin-left: 22px; margin-top: -1px; font-weight: 600; font-size: 20px;">(<?php echo $totalOngoingCases ?>)</p>
                    </div>
                </div>
        </div>

        <div class="home-container" style="display: flex;">
        <div class="incident-case-table" style="display: flex; height: 450px; width: 535px;">
    <div class="head-text">
        <p class="incident-case" style="font-size: 22px;">Incident Cases</p>
        <p class="notice-records">* Barangays with the Most Number of Ongoing Incident Cases</p>
        <div class="table-container" style="max-height: 370px; overflow-y: hidden; margin-top: -6%;">
            <hr style="border: 1px solid #3d3d3d; margin: 3px 0; width: 90%; margin-top: 5%">
            <table class="incident-table" style="width: 530px; margin-top: 0.5%;">
            <?php
       $select = mysqli_query($conn, "
       SELECT pb.barangay AS barangay, COUNT(ir.incident_case_number) AS total_cases
       FROM `incident_report` AS ir
       INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
       INNER JOIN `pb_accounts` AS pb ON la.pb_id = pb.pb_id
       LEFT JOIN `amicable_settlement` AS amicable ON ir.incident_case_number = amicable.incident_case_number
       WHERE amicable.hearing_id IS NULL
       GROUP BY pb.barangay
       ORDER BY total_cases DESC
   ") or die('query failed');
        
        while ($row = mysqli_fetch_assoc($select)) {
            echo "<tr>";
            echo "<td style='font-size: 18px; font-weight: 500; border-bottom: 2px solid #ebecf0; padding-bottom: 10px; width: 90%; padding-top: 10px;'>" . $row['barangay'] . "</td>";
            echo "<td style='position: fixed; margin-left: -4%; font-size: 22px; font-weight: 600; padding-top: 10px;'>" . $row['total_cases'] . "</td>";
            echo "</tr>";
        }
        ?>
            </table>
            <a href="analytics.php" style="text-decoration: none;">
        <span class="seeall" style="margin-left: 66%;">See All</span></a>
        </div>
        </div>
    </div>
</div>

<?php
$queryMonthlyReports = "SELECT pb.pb_id, pb.barangay AS barangay, 
mr.timestamp AS date_submitted, 
mr.generate_report AS report
FROM `monthly_reports` AS mr
INNER JOIN `pb_accounts` AS pb ON mr.pb_id = pb.pb_id
WHERE MONTH(mr.timestamp) = MONTH(CURRENT_DATE())
AND YEAR(mr.timestamp) = YEAR(CURRENT_DATE())
ORDER BY mr.timestamp DESC
LIMIT 6";



$resultMonthlyReports = mysqli_query($conn, $queryMonthlyReports);

?>

<div class="incident-case-table-1">
    <div class="head-text">
        <p class="incident-case" style="font-size: 22px;">Monthly Transmittal Reports</p>
        <p class="notice-records">* For the Month of <em><?php echo $currentMonth; ?></em></p>
        <div class="table-container" style="height: 800px;">
            <hr style="border: 1px solid #3d3d3d; margin: 10px 0; width: 100%; margin-top: 5%">
            <table class="incident-table" style="width: 570px; height: 240px; margin-top: 2%;">
                
                <?php
                // Check if there are any reports
                if (mysqli_num_rows($resultMonthlyReports) > 0) {
                    echo "<tr>";
                        echo "<th style='font-weight: 500; font-size: 14px; text-transform: uppercase;'>Barangay</th>";
                        echo "<th style='font-weight: 500; font-size: 14px; text-transform: uppercase;'>Date Submitted</th>";
                        echo "<th style='font-weight: 500; font-size: 14px; text-transform: uppercase;'>Report</th>";
                        echo "</tr>";
                        echo "<tr>";
                        while ($row = mysqli_fetch_assoc($resultMonthlyReports)) {
                            echo "<td style='font-size: 18px; margin-top: 5px;'>" . $row['barangay'] . "</td>";
                            echo "<td style='font-size: 18px;'>" . date('M d, Y', strtotime($row['date_submitted'])) . "</td>";
                        
                            // Check if 'pb_id' exists in the current row
                            $pb_id = isset($row['pb_id']) ? $row['pb_id'] : '';
                        
                            echo '<td style="font-size: 14px;"><a href="../tcpdf/report.php?pb_id=' . $pb_id . '" style="text-decoration: none;"><span class="summon-record">View</span></a></td>';
                            echo "</tr>";
                        }
                        
                } else {
                    echo "<tr>";
                    echo "<td style='margin-left: 30%;
                    background: #b9bbb6;
                    border-radius: 5px;
                    color: #fff;
                    font-size: 20px;
                    width: 300px;
                    padding: 5px 5px;
                    text-align: center;
                    letter-spacing: 1;
                    margin-top: 20%;
                    text-transform: uppercase;'>No Submitted Transmittal Reports Yet</td>";
                    echo "</tr>";
                }
                ?>

            </table>

        </div>
        <a href="transmittal_reports.php" style="text-decoration: none;">
            <span class="seeall" style="margin-top: 10%;">See All</span></a>
    </div>
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

    </script>

</body>
</html>

<style>
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

    .incident-case-table-1{
        height: 450px; 
        width: 625px; 
        margin-left: 44%;
        background: white;
        border-radius: 8px;
        padding: 16px 24px;
        margin-top: -35%;
    }

    .head-text .incident-case{
        font-size: 25px;
        font-weight: 500;
        display: flex;
    }

    .lupon-online-box {
            width: 290px;
            height: 45px;
            padding: 12px 12px;
            background: #fff;
            border: 2px solid #051094;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
            position: fixed;
            right: -10px;
            top: -10px;
        }
        
        .lupon-online-box p, .lupon-online-box i{
            font-size: 17px;
            color: #051094;
            font-weight: 600;
            text-transform: uppercase;            
        }

        .head-text .notice-records{
    margin-top: -3%;
    font-style: italic;
    font-weight: 400;
    font-size: 14px;
    color: #c82333;

}

.incident-case-table-1 .table-container{
    max-height: 283px; 
    overflow-y: hidden; 
    margin-top: -6%; 
    width: 570px;
}

.seeall{
        font-size: 16px;
        background: #fff;
        padding: 4px 4px;
        color: #363636;
        border: 1px solid #363636;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        width: 8rem;
        margin-left: 77%;
        text-align: center;
        text-decoration: none;
        margin-top: 2%;
    }

    .seeall:hover{
        background: #363636;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
    }

    .summon-record{
            background: #fff;
        padding: 2px 2px;
        color: #2962ff;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        text-align: center;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-left: -10%;
        margin-bottom: 5px;
        width: 65%; 
    }

    .summon-record:hover{
        background: #2962ff;
        color: #fff;
        transition: .5s;
    }

    @media screen and (max-width: 1325px){
        .incident-case-table-1{
            margin-top: -36.3%;
        }
    }
    

</style>