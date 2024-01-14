<?php

$configFile = file_get_contents('../barangays.json');
$config = json_decode($configFile, true);

include '../config.php';

session_start();

$pb_id = $_SESSION['pb_id'];
$barangay_captain = $_SESSION['barangay_captain'];

if(!isset($pb_id)){
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
    <link rel="stylesheet" href="../css/lupon_home.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <title>Punong Barangay Dashboard</title>
</head>
<body>
    <nav class="sidebar close">
        <header>
                    <div class="image-text">
                    <?php
                    $select = mysqli_query($conn, "SELECT * FROM `pb_accounts` WHERE pb_id = '$pb_id'") or die('Query failed');

                    if (mysqli_num_rows($select) > 0) {
                        $fetch = mysqli_fetch_assoc($select);
                    }

                    if (!empty($fetch['barangay'])) {
                        $barangay = $fetch['barangay'];

                        if (isset($config['barangayLogos'][$barangay])) {
                            echo '<span class="image"><img src="' . $config['barangayLogos'][$barangay] . '"></span>';
                        } else {
                            echo '<span class="image"><img src="../images/default_logo.png"></span>';
                        }
                    } else {
                        echo '<span class="image"><img src="../images/default_logo.png"></span>';
                    }
                    ?>

                    <div class="text logo-text">
                        <span class="name"><?php echo $barangay_captain ?></span>
                        <span class="profession"  style="font-size: 13px;">Punong Barangay</span>
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
                        <a href="incident_reports.php">
                            <i class='bx bx-receipt icon' ></i>
                            <span class="text nav-text">Incident Reports</span>
                        </a>
                    </li>


                    <li class="nav-link">
                        <a href="activity_history.php">
                            <i class='bx bx-history icon'></i>
                            <span class="text nav-text">Activity History</span>
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
                <a href="my_account.php">
                        <i class='bx bx-user-circle icon' ></i>
                        <span class="text nav-text">My Account</span>
                    </a>
                </li>
                
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
        
    <div class="datetime-container" style="display: flex; margin-top: -4.5%;">
        <div class="datetime mb-3">
                <div class="time" id="time" style="padding-left: 8px;"></div>
                <div class="date"></div>
            </div>

            <a href="add_lupon_account.php" style="text-decoration: none; margin-left: 1%;"><div class="add-account">
                <i class='bx bx-folder-plus'></i>
                <p>Add Lupon Account</p>
            </div></a>

            <?php
              $activeLuponCountQuery = "SELECT pa.barangay, COUNT(*) as activeLuponStaffs
              FROM `lupon_accounts` AS la
              INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
              WHERE pa.pb_id = '$pb_id' AND la.login_status = 'active';
              ";

                $result = mysqli_query($conn, $activeLuponCountQuery);

                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    $activeLuponCount = $row['activeLuponStaffs'];
                } else {
                    $activeLuponCount = "N/A";
                }

            ?>
                <div class="lupon-online-box">
                    <div class="online" style="display: flex; margin-top: -5px;">
                    <i class='bx bx-user-circle' style="font-size: 35px; font-weight: 500; margin-top: -4px; margin-left: -5px;"></i>
                    <p style="margin-top: 1px; margin-left: -0.5px; width: 17rem;">LUPON STAFF ONLINE</p>
                    <p style="margin-left: 22px; margin-top: -1px; font-weight: 600; font-size: 20px;">(<?php echo $activeLuponCount ?>)</p>
                    </div>
                </div>
        </div>

        <center>
        <div class="container">
        <div class="row">
            <div class="col-md-4">
            <?php
              $countQuery = "SELECT pa.barangay, COUNT(*) as caseCount
              FROM `incident_report` AS ir
              INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
              INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
              INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
              WHERE h.date_of_hearing IS NOT NULL
                  AND h.time_of_hearing IS NOT NULL
                  AND NOT EXISTS (SELECT 1 FROM `amicable_settlement` AS amicable WHERE h.hearing_id = amicable.hearing_id)
                  AND pa.pb_id = '$pb_id'";


                $result = mysqli_query($conn, $countQuery);

                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    $ongoingCasesCount = $row['caseCount'];
                } else {
                    $ongoingCasesCount = "N/A";
                }

            ?>

                <div class="ongoing-cases-box">
                    <p>Ongoing Cases</p>
                    <p class="count"><?php echo $ongoingCasesCount ?></p>
                </div>
            </div>
            
            <div class="col-md-4">
            
            <?php
              $settledCountQuery = "SELECT pa.barangay, COUNT(*) as settledCaseCount
              FROM `incident_report` AS ir
              INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
              INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
              INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
              LEFT JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
              WHERE h.date_of_hearing IS NOT NULL AND h.time_of_hearing IS NOT NULL AND amicable_settlement.agreement_description IS NOT NULL
              AND pa.pb_id = '$pb_id'";

                $result = mysqli_query($conn, $settledCountQuery);

                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    $settledCasesCount = $row['settledCaseCount'];
                } else {
                    $settledCasesCount = "N/A";
                }

            ?>
        
                <div class="settled-cases-box">
                    <p>Settled Cases</p>
                    <p class="count"><?php echo $settledCasesCount ?></p>
                </div>
            </div>

            <div class="col-md-3">
                            <?php
                $incompleteCountQuery = "SELECT pa.barangay, COUNT(*) as incompleteCaseCount
                FROM `incident_report` AS ir
                INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
                INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
                WHERE pa.pb_id = '$pb_id'
                AND NOT EXISTS (
                    SELECT 1
                    FROM `hearing` AS h
                    INNER JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
                    WHERE ir.incident_case_number = h.incident_case_number
                )
                GROUP BY pa.barangay;";

                $result = mysqli_query($conn, $incompleteCountQuery);

                if ($result) {
                    // Check if there are rows returned
                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $incompleteCasesCount = $row['incompleteCaseCount'];
                    } else {
                        $incompleteCasesCount = 0; // No results found
                    }
                } else {
                    $incompleteCasesCount = "N/A";
                }
                ?>

                <div class="incomplete-cases-box">
                    <p>Cases with Incomplete Notice</p>
                    <p class="count"><?php echo $incompleteCasesCount ?></p>
                </div>
            </div>
        </div>
            </center>
        
            <div class="incident-case-table">
            <div class="head-text">
                <p class="incident-case">Incident Report Cases</p>
                <p class="notice-records">* Validate File of Motion</p>


        <div class="table-container"  style="max-height: 310px; overflow-y: hidden; margin-top: -6%;">
        <hr style="border: 1px solid #949494; margin: 5px 0; width: 100%; margin-top: 5%;">
        <table class="incident-table">
            <thead>
                <tr>
                    <th>Case No</th>
                    <th>Case Title</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody id="tableBody" style="overflow-y: hidden;">
            <?php
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

            $select = mysqli_query($conn, "
            SELECT pa.barangay,
                incident_report.incident_case_number AS incident_case_number,
                incident_report.complainant_last_name AS complainant_last_name,
                incident_report.respondent_last_name AS respondent_last_name,
                incident_report.created_at AS created_at
            FROM `incident_report`
            INNER JOIN `lupon_accounts` AS la ON incident_report.lupon_id = la.lupon_id
            INNER JOIN `pb_accounts` AS pa ON la.pb_id = pa.pb_id
            LEFT JOIN `notify_residents` AS nr ON incident_report.incident_case_number = nr.incident_case_number
            LEFT JOIN `execution_notice` AS en ON incident_report.incident_case_number = en.incident_case_number
            WHERE pa.pb_id = '$pb_id'
            AND nr.generate_execution = 'form generated'
            AND en.incident_case_number IS NULL
            LIMIT 3
            ") or die('query failed');



            
            $rowCount = mysqli_num_rows($select);

            if ($rowCount === 0) {
                echo '<tr><td colspan="3" style="font-size: 16px; font-weight: 600; text-transform: uppercase; text-align: center; padding-top: 8%;">No incident cases require motion filing at this time</td></tr>';
            } else {
                while ($fetchCases = mysqli_fetch_assoc($select)) {
                    echo '<tr>';
                    echo '<td>' . substr($fetchCases['incident_case_number'], 0, 9) . '</td>';
                    echo '<td>' . $fetchCases['complainant_last_name'] . ' vs. ' . $fetchCases['respondent_last_name'] . '</td>';
                    echo '<td><a href="execution_notice.php?incident_case_number=' . $fetchCases['incident_case_number'] . '" style="text-decoration: none;"><span class="summon-record">Validate</span></a></td>';
                    echo '</tr>';
                }
            }
            ?>

<tbody id="noResults" style="display: none;">
    <tr>
        <td colspan="3" style="padding-top: 13%; font-size: 12x; font-weight: 400; text-transform: uppercase; padding-left: 18%;">No Cases Yet</td>
    </tr>
</tbody>
            </tbody>
        </table>
        </div>
        <?php
if ($rowCount >= 3) {
    echo '<a href="activity_history.php" style="text-decoration: none;"><span class="seeall-1">See All</span></a>';
}
?>
    </div>

    <div class="incident-case-table-1">
    <div class="head-text">
        <p class="incident-case">Recent Activity Log</p>

        <div class="table-container">
            <hr style="border: 1px solid #949494; margin: 5px 0; width: 100%; margin-top: 3%;">
            <table class="incident-table" style="width: 560px;">
                <tbody>
                                    <?php
                    include 'activity_log.php';
                    ?>


                </tbody>
            </table>
        </div>
        <a href="activity_history.php" style="text-decoration: none;">
        <span class="seeall">See All</span></a>
    </div>
</div>



    </section>

    <script src="searchbar.js"></script>
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

    <style>
        .home .datetime{
    font-size: 16px;
    width: 26rem;
    padding: 10px;
    padding-left: 2%;
    margin-left: 3%;
    margin-top: 3%;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
    border-radius: 4px;
    font-weight: 500;
    border-right: 10px #FADA5F solid;
    font-family: 'Oswald', sans-serif;
}

.home .datetime .date{
    margin-top: 3px;
    font-size: 21px; width: 24rem;
    color: var(--text-color);
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
            margin-left: -0.3%; margin-top: 3%;
        }

        .sidebar.close ~ .home{
            left: 78px;
            height: 100vh;
            width: calc(100% - 78px);
        }

        .container {
            display: flex;
            justify-content: space-around;
            margin-right: 20px;
            margin-left: -2%;
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
            border: 2px solid #C23B21;
            background: #fff;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
        }
        
        .incomplete-cases-box p{
            font-size: 18px;
            color: #C23B21;
            font-weight: 600;
            text-transform: capitalize;            
        }

        .lupon-online-box {
            width: 290px;
            height: 45px;
            padding: 12px 12px;
            background: #fff;
            border: 2px solid #5bc236;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
            position: fixed;
            right: -10px;
            top: -10px;
        }
        
        .lupon-online-box p, .lupon-online-box i{
            font-size: 17px;
            color: #5bc236;
            font-weight: 600;
            text-transform: uppercase;            
        }

        .summon-record{
            background: #fff;
        padding: 5px 5px;
        color: #2962ff;
        border: 1px solid #2962ff;
        text-transform: uppercase;
        text-align: center;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 70%; 
    }

    .summon-record:hover{
        background: #2962ff;
        color: #fff;
        transition: .5s;
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
        margin-left: 76%;
        text-align: center;
        text-decoration: none;
        margin-top: -3%;
    }

    .seeall:hover{
        background: #363636;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
    }

    .incident-case-table{
        display: flex; margin-top: 15px; height: 350px;
    }

    .incident-case-table-1{
        display: flex; margin-top: -16px; height: 350px; margin-left: 6%; width: 600px;
    background: white;
    border-radius: 8px;
    padding: 16px 24px;
}

.table-container{
    height: 240px; overflow-y: hidden; margin-top: -6%;
}

.add-account{
    display: flex; margin-top: 75px; width: 68%;
}

.ongoing-cases-box .count, .settled-cases-box .count, .incomplete-cases-box .count{
    font-size: 30px; margin-top: -8%; font-weight: 600;
}

.incident-table{
    width: 530px; margin-top: 10px;
}

    @media screen and (min-width: 1331px){
        .home{
            margin-left: 1.1%;
        }
    }

    @media screen and (min-width: 1360px) and (min-height: 768px){
    .incident-case-table, .incident-case-table-1{
        height: 450px;
    }

    .incident-case-table-1 .table-container{
        height: 360px;
    }

    @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
        .add-account{
            margin-top: 125px;
            height: 80px;
            width: 21rem;
        }
        .add-account i{
            font-size: 40px;
            margin-top: 4px;
        }
        .add-account p{
            font-size: 25px;
            width: 20rem;
            margin-top: 6px;
        }
        .container{
            margin-top: 4%;
        }
        .incident-case-table{
            margin-top: 4%;
            margin-left: 8%;
            width: 700px;
        }
        .incident-case-table-1{
            margin-left: 10%;
        }
        .incident-case-table .incident-table{
            width: 685px;
        }
        .datetime-container{
            margin-top: -20px;
            height: 15rem;
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
        .ongoing-cases-box, .settled-cases-box, .incomplete-cases-box{
            height: 8rem;
            width: 350px;
        }
        .ongoing-cases-box p,.settled-cases-box p,.incomplete-cases-box p{
            font-size: 20px;
        }
        .ongoing-cases-box .count, .settled-cases-box .count, .incomplete-cases-box .count{
            font-size: 50px;
        }
    }
   
}

@media screen and (min-width: 1536px) and (min-height: 730px){
    .container{
        margin-left: -0.5%;
        margin-bottom: 2%;
        margin-top: 0.5%;
    }
    .incident-case-table-1{
        margin-left: 12%;
        width: 650px;
    }
    .incident-case-table{
        width: 630px;
    }
    .incident-case-table .table-container{
        width: 580px;
    }
    .seeall{
        margin-left: 79%;
    }
    .seeall-1{
        margin-left: 79%;
        margin-top: -2%;
    }
    .summon-record{
        margin-left: -2%;
        width: 80%;
    }
}

@media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
    .incident-case-table-1, .incident-case-table{
        height: 21rem;
    }
    .incident-case-table-1{
        width: 670px;
    }
}


    </style>

</body>
</html>