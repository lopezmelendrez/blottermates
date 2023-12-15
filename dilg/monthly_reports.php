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

if (isset($_GET['pb_id'])) {
    $pbId = $_GET['pb_id'];

}

$barangayQuery = "SELECT barangay FROM pb_accounts WHERE pb_id = '$pbId'";
$barangayResult = mysqli_query($conn, $barangayQuery);

if ($barangayResult) {
    $barangayRow = mysqli_fetch_assoc($barangayResult);
    $barangayName = $barangayRow['barangay'];
} else {
    // Handle the error or set a default value
    $barangayName = "Unknown Barangay";
}

if (isset($_POST['submit_search'])) {
    $search_case = mysqli_real_escape_string($conn, $_POST['search_case']);
    $query = "SELECT pb.barangay AS barangay, 
        mr.timestamp AS date_submitted, 
        mr.generate_report AS report,
        mr.lupon_id AS lupon_id  -- Add this line to include LUPON ID
        FROM `monthly_reports` AS mr
        INNER JOIN `pb_accounts` AS pb ON mr.pb_id = pb.pb_id
        WHERE pb.pb_id = '$pbId' 
        AND mr.generate_report LIKE '%$search_case%'
        ORDER BY mr.timestamp DESC"; // Order by TIMESTAMP in ascending order
} else {
    $query = "SELECT pb.barangay AS barangay, 
        mr.timestamp AS date_submitted, 
        mr.generate_report AS report,
        mr.lupon_id AS lupon_id  -- Add this line to include LUPON ID
        FROM `monthly_reports` AS mr
        INNER JOIN `pb_accounts` AS pb ON mr.pb_id = pb.pb_id
        WHERE pb.pb_id = '$pbId'
        ORDER BY mr.timestamp DESC"; // Order by TIMESTAMP in descending order
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
    <title>BRGY. <?php echo strtoupper($barangayName); ?>: Monthly Transmittal Report</title>
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

    <h1 style="margin-left: 1%; margin-top: -2.3%; display: flex; font-size: 48px;">MONTHLY TRANSMITTAL REPORTS</h1>

    <div class="cases-container" style="margin-left: -31%; width: 100%; margin-top: -2%; ">
            <a href="incomplete_notices.php" style="text-decoration: none;">
            <div class="validate-cases" style="height:40px; width: 965px; margin-left: 33.5%;" >
                <p style="text-transform: uppercase;">BRGY. <?php echo $barangayName ?></p>
            </div></a>
        </div>

    <div class="search-container" style="margin-top: -0.5%;">
            <form action="" method="post">
                <button class="case-button" style="padding: 0px 12px;">MONTH</button>
                <input type="text" class="search-input" name="search_case" placeholder="Search...">
                <button type="submit" name="submit_search" class="search-button" style="padding: 0px 12px;">Search</button>
            </form>
        </div>


<?php
if (mysqli_num_rows($result) == 0) {
    echo '<div class="text-box">No Monthly Transmittal Reports Found</div>';
} else {

    echo '<div class="sort-container">';
    echo '<div class="sort-filter-box">Sort By:</div>';
    echo '<select id="sort" onchange="loadContent()">';
    echo '<option value="latest">From Latest to Oldest</option>';
    echo '<option value="oldest">From Oldest to Latest</option>';
    echo '</select>';
    echo '</div>';

        while ($row = mysqli_fetch_assoc($result)) {
            $barangay = $row['barangay'];
            $dateSubmitted = date('M d, Y', strtotime($row['date_submitted']));
            $transmittalReport = $row['report'];

            echo '<div class="container" style="margin-top: 1%;">';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>DATE SUBMITTED</th>';
            echo '<th>TRANSMITTAL REPORT</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            echo '<tr>';
            echo '<td>' . $dateSubmitted . '</td>';
    if (!empty($transmittalReport)) {
        echo '<td><a href="../tcpdf/monthly_report.php?pb_id=' . urlencode($pbId) . '" style="text-decoration: none;"><span class="generate">VIEW</span></a></td>';
    } else {
        echo '<td></td>';
    }
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
    }
        ?>


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

//function loadContent() {
    //const selectedOption = document.getElementById("sort").value;

    //if (selectedOption === "oldest") {
        //window.location.href = "monthlyreports.php#oldest";
    //} else if (selectedOption === "latest") {
       // window.location.href = "monthly_reports.php#latest";
    //}
//}

    </script>

</body>
<style>
    body{
        overflow-y: scroll;
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
        margin-left: 30%;
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

.sort-filter-box {
            background-color: #ccc;
            padding: 4px;
            font-size: 15px;
            border-radius: 4px;
            margin-right: 10px;
            width: 100px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
        }

        .sort-container {
            margin-left: 57.2%;
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        #sort {
            height: 33px;
        }

</style>
</html>