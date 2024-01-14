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

    <section class="home">
        
        <div class="datetime-container" style="display: flex;">
        <div class="datetime mb-3">
                <div class="time" id="time"></div>
                <div class="date"></div>
            </div>

            <a href="add-barangay-account.php" style="text-decoration: none; margin-left: 1%;"><div class="add-account">
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
                    <i class='bx bx-notepad notepad'></i>
                    <p class="ongoingcases">ONGOING CASES</p>
                    <p class="total">(<?php echo $totalOngoingCases ?>)</p>
                    </div>
                </div>
        </div>

        <div class="home-container" style="display: flex;">
        <div class="incident-case-table">
    <div class="head-text">
        <p class="incident-case">Incident Cases</p>
        <p class="notice-records">* Barangays with the Most Number of Ongoing Incident Cases</p>
        <div class="table-container">
            <hr style="border: 1px solid #3d3d3d; margin: 3px 0; width: 90%; margin-top: 5%">
            <table class="incident-table">
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
        <span class="seeall">See All</span></a>
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
        <p class="incident-case">Monthly Transmittal Reports</p>
        <p class="notice-records">* For the Month of <em><?php echo $currentMonth; ?></em></p>
        <div class="table-container" style="height: 800px;">
            <hr style="border: 1px solid #3d3d3d; margin: 10px 0; width: 100%; margin-top: 5%">
            <table class="incident-table-1">
                
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
                            $dateSubmitted = $row['date_submitted'];
                            $formattedDate = date("F d, Y", strtotime($dateSubmitted));
                            $transmittalReport = $row['report'];
                
                            echo "<td style='font-size: 18px; margin-top: 5px;'>" . $row['barangay'] . "</td>";
                            echo "<td style='font-size: 18px;'>" . date('M d, Y', strtotime($row['date_submitted'])) . "</td>";
                        
                            
                            $pbId = isset($row['pb_id']) ? $row['pb_id'] : '';
                        
                            echo '<td style="font-size: 14px;"><a href="../tcpdf/monthly_report.php?pb_id=' . urlencode($pbId) . '&date_submitted=' . urlencode($dateSubmitted) . '" style="text-decoration: none;" target="_blank"><span class="summon-record">View</span></a></td>';
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
            <span class="seeall-1">See All</span></a>
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
    .lupon-online-box .notepad{
        font-size: 35px; font-weight: 500; margin-top: -4px; margin-left: -5px;
    }
    .lupon-online-box .ongoingcases{
        margin-top: 1.5px; margin-left: -20px; width: 17rem;
    }
    .lupon-online-box .total{
        margin-left: 22px; margin-top: -1px; font-weight: 600; font-size: 20px;
    }
    .incident-case{
        font-size: 22px;
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
            margin-top: -1%;
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
    margin-top: 2%;
    border-radius: 8px;
    padding: 16px 24px;
    display: flex; height: 450px; width: 535px;
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

.incident-case-table .table-container{
   height: 370px; overflow-y: hidden; margin-top: -6%;
}

.incident-case-table-1 .table-container{
    max-height: 283px; 
    overflow-y: hidden; 
    margin-top: -6%; 
    width: 570px;
}

.incident-table{
    width: 530px; margin-top: 0.5%;
}

.incident-table-1{
    width: 570px; height: 240px; margin-top: 2%;
}

.add-account{
    display: flex; margin-top: 25%;
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
        margin-left: 66%;
        text-align: center;
        text-decoration: none;
        margin-top: 2%;
    }

    .seeall-1{
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
        margin-top: 10%;
    }

    .seeall:hover, .seeall-1:hover{
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

    .home .datetime{
    font-size: 16px;
    width: 26rem;
    padding: 10px;
    padding-left: 1%;
    margin-top: 3%;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    border-radius: 4px;
    font-weight: 500;
    border-right: 10px #FADA5F solid;
    font-family: 'Oswald', sans-serif;
}


.home .datetime .time{
    font-size: 40px;
    width: 20rem;
    color: #F5BE1D;
    padding-left: 15px;
}

.home .datetime .date{
    margin-top: 3px;
    color: var(--text-color);
    font-size: 21px; width: 24rem; padding-left: 15px;
}


    @media screen and (max-width: 1325px){
        .incident-case-table-1{
            margin-top: -36.3%;
        }
    }

    @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
        .incident-case-table{
            margin-top: 3%;
            width: 800px;
            height: 550px;
        }    
        .incident-case-table-1{
                margin-top: -30.5%;
                margin-left: 48%;
                width: 800px;
                height: 550px;
            }
        .add-account{
            margin-top: 30%;
            height: 80px;
            width: 26rem;
        }
        .add-account i{
            font-size: 45px;
            margin-top: 4px;
        }
        .add-account p{
            font-size: 27px;
            width: 20rem;
            margin-top: 5px;
        }
        .home .datetime{
            width: 37rem;
            height: 215px;
        }
        .home .datetime .time{
            font-size: 85px;
            width: 29rem;
        }
        .home .datetime .date{
            font-size: 30px;
            width: 32rem;
            padding-bottom: 15px
        }
        .lupon-online-box{
            width: 22rem;
            height: 65px;
        }
        .lupon-online-box .notepad{
            font-size: 45px;
            margin-top: 2px;
        }
        .lupon-online-box .ongoingcases{
            font-size: 24px;
            margin-top: 6px;
            margin-left: -4px;
        }

        .lupon-online-box .total{
            font-size: 30px;
        }

        .incident-case-table .incident-case{
            font-size: 37px;
        }
        .incident-case-table .notice-records{
            font-size: 20px;
            margin-top: -20px;
            margin-bottom: 25px;
        }
        .incident-case-table .table-container{
            width: 830px;
            height: 600px;
        }
        .incident-table{
            width: 800px;
        }
        .incident-case-table-1 .incident-case{
            font-size: 37px;
        }
        .incident-case-table-1 .notice-records{
            font-size: 20px;
            margin-top: -20px;
            margin-bottom: 25px;
        }
        .incident-case-table-1 .table-container{
            width: 745px;
            height: 900px;
        }
        .incident-case-table-1 .incident-table-1{
            width: 730px;
            margin-left: 2%;
        }
        }

        @media screen and (min-width: 1536px) and (min-height: 730px){
            .home{
                margin-left: 5%;
            }
            .incident-case-table-1{
                margin-top: -33%;
                height: 30rem;
                margin-left: 50%;
            }
            .incident-case-table-1 table{
                width: 570px;
            }
            .incident-case-table{
                width: 680px;
                height: 30rem;
            }
            .incident-case-table .table-container{
                width: 690px;
                height: 800px;
            }
            .incident-case-table table{
                width: 690px;
            }
            .seeall{
                margin-left: 72%;
            }
            .summon-record{
                width: 100%;
                margin-left: -30%;
            }
            .add-account{
                margin-top: 27%;
            }
        }

        @media screen and (min-width: 1366px) and (max-width:1500px) and (min-height: 617px){
            .incident-case-table{
                margin-top: -0.2%;
            }
        }

        @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
            .home{
                margin-top: -1.8%;
            }
            .home .datetime{
            width: 23rem;
            height: 100px;
        }
        .home .datetime .time{
            margin-top: -5.5px;
            font-size: 40px;
            width: 29rem;
        }
        .home .datetime .date{
            font-size: 20px;
            margin-top: -8px;
            width: 32rem;
            padding-bottom: 15px
        }
        .incident-case-table{
            height: 27rem;
            margin-top: -0.5%;
        }

        .incident-case-table .incident-case{
            font-size: 21px;
        }
        .incident-case-table .notice-records{
            font-size: 14px;
            margin-top: -18px;
            margin-bottom: 15px;
        }

        .incident-case-table-1{
            height: 27rem;
            width: 570px;
            margin-top: -35.95%;
            margin-left: 46%;
        }

        .incident-case-table-1 .table-container{
            width: 520px;
            height: 700px;
        }

        .incident-case-table-1 .table-container table{
            width: 520px;
        }

        .seeall-1{
            margin-top: 9%;
            margin-left: 74%;
        }
        }

    

</style>