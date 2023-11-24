<?php

include '../config.php';

session_start();

$configFile = file_get_contents('incident_case_types.json'); // Adjust the file path accordingly
$incidentTypeMap = json_decode($configFile, true);

$account_id = $_SESSION['account_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$account_role = $_SESSION['account_role'];

if(!isset($account_id)){
header('location: ../index.php');
}

$query = "SELECT * FROM pb_accounts";
$result = mysqli_query($conn, $query);

date_default_timezone_set('Asia/Manila');


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
    <section class="home" style="margin-left: 1%;">

    <h1 style="margin-left: 3.5%; margin-top: -2%; display: flex; font-size: 48px;">ANALYTICS</h1>
    <p class="notice-records" style="margin-left: 25%; margin-top: -3.5%; font-size: 16px;">* As of <b><?php echo date('l, d F Y - h:i A'); ?></b></p>

    <center>
        <div class="container" style="margin-left: -2%;">
        <div class="row">
            <div class="col-md-4">
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

                <div class="ongoing-cases-box">
                    <p>Ongoing Cases</p>
                    <p style="font-size: 30px; margin-top: -8%; font-weight: 600;"><?php echo $totalOngoingCases ?></p>
                </div>
            </div>
            
            <div class="col-md-4">
    <?php
        $queryMonthlyReports = "SELECT COUNT(*) AS total_reports FROM monthly_reports";
        $resultMonthlyReports = mysqli_query($conn, $queryMonthlyReports);

        if ($resultMonthlyReports) {
            $rowMonthlyReports = mysqli_fetch_assoc($resultMonthlyReports);
            $totalMonthlyReports = $rowMonthlyReports['total_reports'];
        } else {
            $totalMonthlyReports = 0; // Handle the case where the query fails.
        }
    ?>
    <div class="settled-cases-box">
        <p>Monthly Transmittal Reports</p>
        <p style="font-size: 30px; margin-top: -8%; font-weight: 600;"><?php echo $totalMonthlyReports; ?></p>
    </div>
</div>


<div class="col-md-3">
    <?php
        $queryRegisteredBarangays = "SELECT COUNT(*) AS total_accounts FROM pb_accounts";
        $resultRegisteredBarangays = mysqli_query($conn, $queryRegisteredBarangays);

        if ($resultRegisteredBarangays) {
            $rowRegisteredBarangays = mysqli_fetch_assoc($resultRegisteredBarangays);
            $totalRegisteredBarangays = $rowRegisteredBarangays['total_accounts'];
        } else {
            $totalRegisteredBarangays = 0; // Handle the case where the query fails.
        }
    ?>
    <div class="incomplete-cases-box">
        <p>Registered Barangays</p>
        <p style="font-size: 30px; margin-top: -8%; font-weight: 600;"><?php echo $totalRegisteredBarangays; ?></p>
    </div>
</div>


            </center>

            <div class="incident-case-table" style="display: flex; height: 440px; width: 600px;">
    <div class="head-text">
        <p class="incident-case" style="font-size: 22px;">Ongoing Incident Cases</p>
        <div class="table-container" style="max-height: 283px; overflow-y: hidden; margin-top: -6%;">
            <hr style="border: 1px solid #3d3d3d; margin: 3px 0; width: 100%; margin-top: 5%">
            <table class="incident-table" style="width: 548px; margin-top: 0.5%;">
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
            echo "<td style='font-size: 14px; font-weight: 500; border-bottom: 2px solid #ebecf0; padding-bottom: 10px; width: 100%; padding-top: 10px;'>Barangay " . $row['barangay'] . "</td>";
            echo "<td style='position: fixed; margin-left: -4%; font-size: 18px; font-weight: 600; padding-top: 10px;'>" . $row['total_cases'] . "</td>";
            echo "</tr>";
        }
        ?>
            </table>
        </div>
        </div>
    </div>
</div>

<div class="incident-case-table" style="background-color: #fff; margin-top: -35.5%; width: 450px; margin-left: 55%; height: 440px; border-radius: 5px;">
<div class="head-text">
        <p class="incident-case" style="font-size: 22px;">Top Incident Case Type</p>
        <div class="table-container" style="max-height: 283px; overflow-y: hidden; margin-top: -6%;">
            <hr style="border: 1px solid #3d3d3d; margin: 3px 0; width: 98%; margin-top: 5%">
            <?php
    $select = mysqli_query($conn, "SELECT incident_case_type, COUNT(*) AS total_cases
        FROM `incident_report`
        GROUP BY incident_case_type
        ORDER BY total_cases DESC")
    or die('query failed');

    $rank = 1;

    echo "<table>";  // Start the table here

    while ($row = mysqli_fetch_assoc($select)) {
        $description = isset($incidentTypeMap[$row['incident_case_type']]) ? $incidentTypeMap[$row['incident_case_type']] : $row['incident_case_type'];

        echo "<tr>";
        echo "<td style='font-size: 15px; font-weight: 500; padding-right: 10px;'>" . $rank . ".</td>";
        echo "<td style='font-size: 18px; font-weight: 600;'>" . $description . "</td>";
        echo "</tr>";

        $rank++;
    }

    echo "</table>";  // End the table here
?>


        </div>
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
<style>

.container {
            display: flex;
            justify-content: space-around;
            margin-right: 20px;
        }

        .ongoing-cases-box {
            width: 305px;
            height: 90px;
            padding: 12px;
            border: 2px solid #2E5895;
            background: #fff;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
        }
        
        .ongoing-cases-box p{
            font-size: 18px;
            color: #2E5895;
            font-weight: 600;
            text-transform: capitalize;            
        }

        .settled-cases-box {
            width: 305px;
            height: 90px;
            padding: 12px;
            border: 2px solid #F5BE1D;
            background: #fff;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
        }
        
        .settled-cases-box p{
            font-size: 18px;
            color: #F5BE1D;
            font-weight: 600;
            text-transform: capitalize;            
        }

        .incomplete-cases-box {
            width: 305px;
            height: 90px;
            padding: 12px;
            border: 2px solid #388e3c;
            background: #fff;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
        }
        
        .incomplete-cases-box p{
            font-size: 18px;
            color: #388e3c;
            font-weight: 600;
            text-transform: capitalize;            
        }

        .cases-box{
            border-radius: 5px;
        }

        .notice-records{
            margin-top: -1%;
            font-style: italic;
            font-weight: 400;
            font-size: 13px;
            color: #c82333;
        }

</style>
</html>