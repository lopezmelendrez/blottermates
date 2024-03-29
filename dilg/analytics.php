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
    <title>Analytics</title>
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

    <h1 style="margin-left: 3.5%; margin-top: -2%; display: flex; font-size: 48px;">ANALYTICS</h1>
    <p class="notice-records" id="dateNotice"><b></b></p>

    <center>
        <div class="container">
        <div class="row">
            <div class="col-md-4">
            <?php

$query = "SELECT COUNT(*) AS total_ongoing_cases
FROM `incident_report` AS ir
WHERE EXISTS (
    SELECT 1
    FROM `hearing` AS h
    WHERE ir.incident_case_number = h.incident_case_number
) AND NOT EXISTS (
    SELECT 1
    FROM `court_action` AS ca
    WHERE ir.incident_case_number = ca.incident_case_number
) AND NOT EXISTS (
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

                <div class="ongoing-cases-box">
                    <p>Ongoing Incident Cases</p>
                    <p class="count"><?php echo $totalOngoingCases ?></p>
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
        <p class="count"><?php echo $totalMonthlyReports; ?></p>
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
        <p class="count"><?php echo $totalRegisteredBarangays; ?></p>
    </div>
</div>


            </center>

            <div class="incident-case-table" style="display: flex;">
    <div class="head-text">
        <p class="incident-case">Incident Cases Overview</p>
        <div class="table-container">
        <hr class="border-1">
            <canvas id="barGraph" width="400" height="200"></canvas>
        </div>
        </div>
    </div>
</div>

<div class="incident-case-table-1">
<div class="head-text">
        <p class="incident-case">Top Incident Case Type</p>
        <div class="table-container">
            <hr style="border: 1px solid #3d3d3d; margin: 3px 0; width: 73%; margin-top: 3.7%; margin-bottom: 5%;">
            <?php
    $select = mysqli_query($conn, "SELECT incident_case_type, COUNT(*) AS total_cases
    FROM `incident_report`
    GROUP BY incident_case_type
    ORDER BY total_cases DESC
    LIMIT 10")
    or die('query failed');

    $data = array(); // Array to store data for the chart

    while ($row = mysqli_fetch_assoc($select)) {
        $description = isset($incidentTypeMap[$row['incident_case_type']]) ? $incidentTypeMap[$row['incident_case_type']] : $row['incident_case_type'];

        // Store data for the chart
        $data[] = array(
            "label" => $description,
            "value" => $row['total_cases']
        );
    }
?>

<div class="piechart">
    <canvas id="myPieChart"></canvas>
</div>


        </div>
        </div>
    </div>
</div>

    </section>

    <script src="search_bar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

// Function to format the current date and time
function formatDateTime() {
    const now = new Date();
    const formattedDateTime = now.toLocaleString('en-US', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      hour12: true
    });
    return formattedDateTime;
  }

  // Update the notice with the formatted date and time
  document.addEventListener('DOMContentLoaded', function() {
    const dateNotice = document.getElementById('dateNotice');
    if (dateNotice) {
      dateNotice.innerHTML = `* As of <b>${formatDateTime()}</b>`;
    }
  });

var ctx = document.getElementById('myPieChart').getContext('2d');
var myPieChart = new Chart(ctx, {
    type: 'doughnut', // Change the chart type to doughnut
    data: {
        labels: <?php echo json_encode(array_column($data, 'label')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_map(function($value) { return $value * 10; }, array_column($data, 'value'))); ?>,
            backgroundColor: [
                'rgba(246, 109, 68)',
                'rgba(254, 174, 101)',
                'rgba(230, 246, 157)',
                'rgba(170, 222, 167)',
                'rgba(100, 194, 166)',
                'rgba(45, 135, 187)',
                // Add more colors if needed
            ],
        }]
    },
    options: {
        cutoutPercentage: 30, // Adjust this value to control the size of the center hole
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    // Display only the first 5 labels
                    filter: function (legendItem, chartData) {
                        return chartData.labels.indexOf(legendItem.text) < 5;
                    },
                    // Customize the legend text color and font weight
                    color: 'black', // Change to the desired color
                    fontWeight: 600, // Change to the desired font weight
                },
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        var label = context.label || '';
                        var percentage = Math.round(context.parsed); // Round the percentage to a whole number
                        return label + ": " + percentage + "%";
                    },
                },
            },
        },
    },
});


    <?php
            $select = mysqli_query($conn, "
            SELECT pb.barangay AS barangay, COUNT(ir.incident_case_number) AS total_cases
            FROM `incident_report` AS ir
            INNER JOIN `lupon_accounts` AS la ON ir.lupon_id = la.lupon_id
            INNER JOIN `pb_accounts` AS pb ON la.pb_id = pb.pb_id
            LEFT JOIN `amicable_settlement` AS amicable ON ir.incident_case_number = amicable.incident_case_number
            LEFT JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
            LEFT JOIN `court_action` AS ca ON ir.incident_case_number = ca.incident_case_number
            WHERE amicable.hearing_id IS NULL
            AND h.incident_case_number IS NOT NULL
            AND ca.incident_case_number IS NULL
            GROUP BY pb.barangay
            ORDER BY total_cases DESC
            ") or die('query failed');

            $data = array();
while ($row = mysqli_fetch_assoc($select)) {
    $data[$row['barangay']] = round($row['total_cases']);
}

        ?>

        var labels = <?php echo json_encode(array_keys($data)); ?>;
        var data = <?php echo json_encode(array_values($data)); ?>;

        data = data.map(function(value) {
    return Math.round(value);
});

var ctx = document.getElementById('barGraph').getContext('2d');
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Cases',
            data: data,
            backgroundColor: [
                'rgba(246, 109, 68, 0.7)',
                'rgba(254, 174, 101, 0.7)',
                'rgba(230, 246, 157, 0.7)',
                'rgba(170, 222, 167, 0.7)',
                'rgba(100, 194, 166, 0.7)',
                'rgba(45, 135, 187, 0.7)',
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    callback: function (value) { return value.toFixed(0); }
                }
            }
        }
    }
});



    </script>

    </script>

</body>
<style>
    .incident-case, .incident-case-table-1 .head-text .incident-case{
        font-size: 22px;
    }


    .piechart{
        width: 400px; height: 335px;
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
            margin-left: 2.6%;
            margin-top: 3%;
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
            border: 2px solid #388e3c;
            background: #fff;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
        }

        .incident-case-table{
            height: 440px; width: 600px;
        }

        .incident-case-table-1{
            background-color: #fff; 
            margin-top: -35.5%; 
            width: 450px; 
            margin-left: 55%; 
            height: 440px; 
            border-radius: 5px;
            padding: 16px 24px;
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

        .border-1{
            border: 1px solid #3d3d3d; margin: 3px 0; width: 100%; margin-top: 5%; margin-bottom: 40px;
        }

        .notice-records{
            margin-top: -1%;
            font-style: italic;
            font-weight: 400;
            font-size: 13px;
            color: #c82333;
        }

        #myPieChart {
        width: 500px;
        height: 360px; 
        margin-top: -1%;
        margin-left: 11%;
        margin-bottom: 3%;
    }

    .notice-records{
        margin-left: 25%; margin-top: -3.5%; font-size: 16px;
    }

    .incident-case-table-1 .table-container{
        height: 380px; overflow-y: hidden; margin-top: -6%;
    }

    .table-container{
        height: 400px; overflow-y: hidden; margin-top: -6%; width: 545px;
    }

    .ongoing-cases-box .count, .settled-cases-box .count, .incomplete-cases-box .count{
    font-size: 30px; margin-top: -8%; font-weight: 600;
}

        @media screen and (min-width: 1355px) and (min-height: 616px){
            .incident-case-table-1{
                margin-top: -34.1%;
            }
        }

        @media screen and (min-width: 1331px) {
            .home{
                margin-top: 2.8%;
                margin-left: 1%;
            }
            .incident-case-table-1{
                height: 65%;
                width: 35%;
                margin-left: 61%;
                margin-top: -32.5%;
            }

            .incident-case-table{
                height: 64%;
                width: 700px;
            }

            .incident-case-table .table-container{
                width: 650px;
                height: 400px;
            }

            .border-1{
                margin-bottom: 10px;
            }

            #myPieChart{
                margin-top: -3.5%;
            }
        }

        @media screen and (min-width: 1360px) and (min-height: 768px) {
            .incident-case-table-1{
                height: 56%;
                margin-top: -33.5%;
            }

            .incident-case-table{
                height: 56%;
            }

        }

        @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .notice-records{
                margin-top: -2.5%;
                margin-left: 19%;
            }
            .container{
                margin-top: 3%;
                margin-bottom: 3%;
            }
            .incident-case-table{
                height: 51%;
                margin-left: 8%;
            }

            .incident-case-table-1{
                height: 51%;
                width: 35%;
                margin-top: -30%;
                margin-left: 51%;
            }
            .incident-case-table-1 .table-container{
                height: 600px;
                width: 810px;
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
    
        .piechart{
            width: 500px;
            height: 450px;
            margin-left: 5%;
        }

        .incident-case-table .table-container{
            height: 600px;
        }

        .incident-case-table canvas{
            margin-top: 10%;
        } 

        }

        @media screen and (min-width: 1536px) and (min-height: 730px){
            .notice-records{
                margin-left: 22.5%;
                margin-top: -2.8%;
            }
            .incident-case-table{
                margin-left: 3.7%;
            }
            .incident-case-table-1{
                margin-top: -32%;
                margin-left: 56%;
                width: 510px;
            }
            .incident-case-table-1 .table-container{
                height: 700px;
                width: 630px;
            }
            .piechart{
                height: 480px;
                margin-top:-2%;
                margin-left: 5%;
            }
            #myPieChart{
                margin-top: 1%;
                margin-left: 7%;
            }
            .incident-case-table canvas{
                margin-top: 6%;
            }
            .container{
                margin-left: 5%;
            }
        }

        @media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
            .incident-case-table{
                height: 26rem;
                margin-top: .2%;
            }
            .incident-case-table-1{
                height: 26rem;
                margin-top: -32.3%;
                margin-left: 60%;
            }
        }

        @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
            .notice-records{
                margin-left: 26%;
            }
            .incident-case-table{
                margin-top: .2%;
                height: 24rem;
            }
            .incident-case-table-1{
                height: 24rem;
                margin-top: -32%;
            }
            .piechart{
                height: 300px;
            }
            #myPieChart{
                margin-top: -3.5%;
                margin-left: 14.5%;
            }
            .incident-case-table canvas{
                margin-top:-4%;
            }
            .container{
                margin-top: -.5%;
            }
            .ongoing-cases-box, .settled-cases-box, .incomplete-cases-box{
            height: 5rem;
        }
        .ongoing-cases-box p,.settled-cases-box p,.incomplete-cases-box p{
            font-size: 15px;
        }
        .ongoing-cases-box .count, .settled-cases-box .count, .incomplete-cases-box .count{
            font-size: 30px;
        }
        }

        @media screen and (min-width: 1360px) and (min-height: 681px){
        .incident-case-table-1{
            margin-top: -34%;
        }
        #myPieChart{
            margin-top: -1%;
        }
    }

    @media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
        .incident-case-table-1{
            margin-top: -32%;
            height: 29rem;
        }
        #myPieChart{
            margin-top: -4%;
        }
        .incident-case-table {
            margin-left: 5%;
        }
    }

@media screen and (min-width: 1500px) and (max-width: 1670px) and (min-height: 700px) and (max-height: 760px){
        .notice-records{
            margin-left: 22.5%;
            margin-top: -43px;
        }
        .ongoing-cases-box, .settled-cases-box, .incomplete-cases-box{
            height: 7rem;
            width: 355px;
        }
        .ongoing-cases-box p,.settled-cases-box p,.incomplete-cases-box p{
            font-size: 20px;
        }
        .ongoing-cases-box .count, .settled-cases-box .count, .incomplete-cases-box .count{
            font-size: 50px;
        }
        .incident-case-table-1{
            width: 30%;
            margin-left: 58%;
            margin-top: -32.7%;
            height: 29.5rem;
        }
        .incident-case-table .table-container canvas{
            margin-top: 6.5%;
        }
        .incident-case-table-1 .table-container{
            height: 700px;
        }
        .incident-case-table{
            margin-left: 9%;
        }
        .piechart{
            height: 340px;
        }
        #myPieChart{
            margin-left: 5%;
            margin-top: 8%;
        }
    }
    
     @media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
    .notice-records{
        margin-left: 23.5%;
        margin-top: -43px;
    }
    .container{
        margin-left: 0%;
    }
     .ongoing-cases-box, .settled-cases-box, .incomplete-cases-box{
            height: 7rem;
            width: 355px;
        }
        .ongoing-cases-box p,.settled-cases-box p,.incomplete-cases-box p{
            font-size: 20px;
        }
        .ongoing-cases-box .count, .settled-cases-box .count, .incomplete-cases-box .count{
            font-size: 50px;
        }
        .incident-case-table-1{
            width: 30.7%;
            margin-left: 60%;
            margin-top: -34%;
            height: 29.5rem;
        }
        .incident-case-table .table-container canvas{
            margin-top: 6.5%;
        }
        .incident-case-table-1 .table-container{
            height: 700px;
        }
        .incident-case-table-1 .table-container .border1{
            width: 70%;
        }
        .incident-case-table{
            margin-left: 7.6%;
            height: 29.5rem;
            width: 50%;
        }
        .piechart{
            height: 355px;
        }
        #myPieChart{
            margin-left: 5%;
            margin-top: 5%;
        }
}

</style>
</html>