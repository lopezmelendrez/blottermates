<?php

include '../../config.php';

session_start();

$email = $_SESSION['email_address'];


if(!isset($email)){
header('location: ../../index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/lupon.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.js"></script>
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Arbitration Hearings</title>
</head>
<body>

<nav class="sidebar close">
        <header>
            <div class="image-text">
            <?php
                    $select = mysqli_query($conn, "SELECT l.*, pb.barangay 
                                                FROM `lupon_accounts` l
                                                LEFT JOIN `pb_accounts` pb ON l.pb_id = pb.pb_id
                                                WHERE l.email_address = '$email'") or die('query failed');
                    if(mysqli_num_rows($select) > 0){
                        $fetch = mysqli_fetch_assoc($select);
                    }
                ?>
                              <?php
if ($fetch['barangay'] == 'Ibaba') {
    echo '<span class="image"><img src="../../images/ibaba_logo.png"></span>';
} elseif ($fetch['barangay'] == 'Other') {
    echo '<span class="image"><img src="../../images/logo.png"></span>';
} elseif ($fetch['barangay'] == 'Labas') {
    echo '<span class="image"><img src="../../images/labas.png"></span>';
} elseif ($fetch['barangay'] == 'Tagapo') {
    echo '<span class="image"><img src="../../images/tagapo.png"></span>';
} elseif ($fetch['barangay'] == 'Malusak') {
    echo '<span class="image"><img src="../../images/malusak.png"></span>';
} elseif ($fetch['barangay'] == 'Balibago') {
    echo '<span class="image"><img src="../../images/balibago.png"></span>';
} elseif ($fetch['barangay'] == 'Caingin') {
    echo '<span class="image"><img src="../../images/caingin.png"></span>';
} elseif ($fetch['barangay'] == 'Pook') {
    echo '<span class="image"><img src="../../images/pooc.png"></span>';
} elseif ($fetch['barangay'] == 'Aplaya') {
    echo '<span class="image"><img src="../../images/aplaya.png"></span>';
} elseif ($fetch['barangay'] == 'Kanluran') {
    echo '<span class="image"><img src="../../images/kanluran.png"></span>';
} else {
    // Default image if the barangay is not matched
    echo '<span class="image"><img src="../../images/logo.png"></span>';
}
?>
                <div class="text logo-text">
                
                    <span class="name"><?php echo $fetch['first_name'] . ' ' . $fetch['last_name']; ?></span>
                    <?php
    if ($fetch['barangay']) {
        echo '<span class="profession">Barangay ' . $fetch['barangay'] . '</span>';
    } else {
        echo '<span class="profession">Not specified</span>'; 
    }
    ?>
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
                            <i class='bx bx-file icon' ></i>
                            <span class="text nav-text">Incident Reports</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="hearings.php">
                            <i class='bx bx-calendar-event icon' ></i>
                            <span class="text nav-text">Hearings</span>
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
                    <a href="../../logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
                
            </div>
        </div>

    </nav>

    <section class="home">

    <h1 class="incident-reports">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex;">
            <a href="hearings.php" style="text-decoration: none;">
            <div class="back" style="width: 150%;">
            <p style="font-size: 18px; margin-top: 3px;">Back</p>
            </div></a>
            <div class="settled-cases" style="width: 80%; margin-left: 90px; height: 40px;">
                <p>Arbitration Hearings</p>
            </div>
        </div>

        <div class="pagination">
            <?php
            $selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
            if (!$selectLuponId) {
                die('Failed to fetch lupon_id: ' . mysqli_error($conn));
            }
            $row = mysqli_fetch_assoc($selectLuponId);
            $pb_id = $row['pb_id'];

            $rowsPerPage = 4;

            $selectCount = mysqli_query($conn, "SELECT COUNT(*) AS total_rows
            FROM `incident_report` AS ir
            INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
            WHERE ir.pb_id = $pb_id
            AND NOT EXISTS (SELECT 1 FROM `amicable_settlement` AS amicable WHERE h.hearing_id = amicable.hearing_id)
            AND h.hearing_type_status = 'arbitration'");

            $rowCount = mysqli_fetch_assoc($selectCount);
            $num_rows = $rowCount['total_rows'];

            $totalPages = ceil($num_rows / $rowsPerPage);

            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

            echo '<div class="pages" style="display: flex; margin-left: 80%; margin-top: -4%;">';

            // Previous button
            if ($currentPage > 1) {
                echo '<i class="bx bxs-left-arrow-square next" onclick="navigatePage(' . ($currentPage - 1) . ')" style="font-size: 50px; color: #379711; cursor: pointer;"></i>';
            }

            // Next button
            if ($currentPage == 1 && $num_rows > $rowsPerPage) {
                echo '<i class="bx bxs-right-arrow-square next" onclick="navigatePage(' . ($currentPage + 1) . ')" style="font-size: 50px; color: #379711; cursor: pointer; margin-left: 50px;"></i>';
            } elseif ($currentPage > 1) {
                echo '<i class="bx bxs-right-arrow-square next" onclick="navigatePage(' . ($currentPage + 1) . ')" style="font-size: 50px; color: #379711; cursor: pointer;"></i>';
            }

            echo '</div>';
            ?>

            <script>
                function navigatePage(page) {
                    window.location.href = '?page=' + page;
                }
            </script>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Case No.</th>
                    <th>Case Title</th>
                    <th>Complainant</th>
                    <th>Respondent</th>
                    <th>Hearing Date</th>
                    <th>Arbitration Requirement</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php

            $selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
            if (!$selectLuponId) {
                die('Failed to fetch lupon_id: ' . mysqli_error($conn));
            }
            $row = mysqli_fetch_assoc($selectLuponId);
            $pb_id = $row['pb_id'];

            $rowsPerPage = 4; // Adjust the number of rows per page as needed
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $rowsPerPage;

            $select = mysqli_query($conn, "SELECT ir.incident_case_number, ir.complainant_last_name, ir.complainant_first_name, ir.complainant_middle_name, ir.respondent_last_name, ir.respondent_first_name, ir.respondent_middle_name, ir.description_of_violation, ir.incident_date, ir.submitter_first_name, ir.submitter_last_name, ir.created_at 
                FROM `incident_report` AS ir
                INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
                AND ir.pb_id = $pb_id
                AND NOT EXISTS (SELECT 1 FROM `amicable_settlement` AS amicable WHERE h.hearing_id = amicable.hearing_id)
                AND h.hearing_type_status = 'arbitration'
                ORDER BY ir.created_at DESC
                LIMIT $offset, $rowsPerPage")
                or die('query failed');

            $num_rows = mysqli_num_rows($select);

            if ($num_rows === 0) {
                echo '<tr><td colspan="8" style="font-size: 25px; font-weight: 600; text-transform: uppercase;">No Scheduled Arbitration Hearings</td></tr>';
            } else {
                while ($fetch_cases = mysqli_fetch_assoc($select)) {
                    $submitter_first_name = $fetch_cases['submitter_first_name'];
                    $submitter_last_name = $fetch_cases['submitter_last_name'];
                    $submitter_full_name = $submitter_first_name . ' ' . $submitter_last_name;
                    ?>
            <tr>    
            <td style="width: 9rem;"><?php echo htmlspecialchars(substr($fetch_cases['incident_case_number'], 0, 9)); ?></td>
            <td><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></td>
            <td><?php echo $fetch_cases['complainant_first_name']; ?> <?php echo $fetch_cases['complainant_last_name'] ; ?></td>
            <td><?php echo $fetch_cases['respondent_first_name']; ?> <?php echo $fetch_cases['respondent_last_name'] ; ?></td>
            <td>
    <?php
    $incident_case_number = $fetch_cases['incident_case_number'];
    $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

    if (mysqli_num_rows($select_hearing) > 0) {
        $hearing_data = mysqli_fetch_assoc($select_hearing);
        
        if ($hearing_data['date_of_hearing'] !== null) {
            $hearing_date = date("F j, Y", strtotime($hearing_data['date_of_hearing']));
            echo $hearing_date;
        } else {
            echo '<span style="font-weight: 600;">NO HEARING SCHEDULE YET</span>';
        }
    } else {
        echo '<span style="font-weight: 600;">NO HEARING SCHEDULE YET</span>';
    }
    ?>
</td>

<td>            <?php
$incident_case_number = $fetch_cases['incident_case_number'];
$select_arbitration_agreement = mysqli_query($conn, "SELECT 1 FROM arbitration_agreement WHERE incident_case_number = '$incident_case_number' LIMIT 1");
if (mysqli_num_rows($select_arbitration_agreement) > 0) {
    echo '-';
    
} else {
    echo '<span class="to-notify">NEEDS ARBITRATION AGREEMENT</span>';
}
?></td>
            <td>
            <?php
$incident_case_number = $fetch_cases['incident_case_number'];

// Check if arbitration agreement exists
$select_arbitration_agreement = mysqli_query($conn, "SELECT 1 FROM arbitration_agreement WHERE incident_case_number = '$incident_case_number' LIMIT 1");

if (mysqli_num_rows($select_arbitration_agreement) > 0) {
    // Arbitration agreement exists, check for hearing schedule
    $hearing_data = mysqli_query($conn, "SELECT hearing_type_status, date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number' LIMIT 1");

    if (mysqli_num_rows($hearing_data) > 0) {
        $hearing_row = mysqli_fetch_assoc($hearing_data);
        $hearing_type_status = $hearing_row['hearing_type_status'];
        $date_of_hearing = strtotime($hearing_row['date_of_hearing']);
        $current_time = time();

        if ($current_time >= $date_of_hearing && $hearing_type_status == "arbitration") {
            echo '<a href="arbitration_settlement_page.php?incident_case_number=' . $incident_case_number . '" class="hearing-1" style="text-decoration: none; margin-left: 0%;">Hearing</a>';
        } else {
            // Display message for upcoming hearing
            echo '<div class="upcoming-hearing">Upcoming Hearing</div>';
        }
    } else {
        echo '<div class="no-hearing-schedule">No hearing schedule set</div>';
    }
} else {
    $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

    if ($select_hearing) {
        $hearing_data = mysqli_fetch_assoc($select_hearing);

        if ($hearing_data['date_of_hearing'] === NULL) {
            echo '<a href="hearingschedule.php?incident_case_number=' . $incident_case_number . '" class="schedule">SET HEARING SCHEDULE</a>';
        } else {
            $hearing_date = date("F j, Y", strtotime($hearing_data['date_of_hearing']));
            echo '<a href="arbitration_agreement.php?incident_case_number=' . $incident_case_number . '" class="hearing-1" style="text-decoration: none; margin-left: -10px;">CREATE ARBITRATION AGREEMENT</a>';
        }
    } else {
        // Handle the case when the query fails
        die('hearing query failed');
    }
}
?>
            </td>
            </tr>
            <?php
    }
}
?>
            </tbody>
        </table>

        
        
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

        const searchIcon = document.querySelector('.search-box .icon');
        const searchInput1 = document.getElementById('searchInput1');

        searchIcon.addEventListener('click', function () {
        const searchTerm = searchInput1.value.trim().toLowerCase();

    if (searchTerm !== '') {
        handleSearch(searchTerm);
    }
    });

searchInput1.addEventListener('keyup', function (e) {
    if (e.key === 'Enter') {
        const searchTerm = searchInput1.value.trim().toLowerCase();

        if (searchTerm !== '') {
            handleSearch(searchTerm);
        }
    }
});

function handleSearch(searchTerm) {
    const lowerCaseSearchTerm = searchTerm.trim().toLowerCase();

    if (lowerCaseSearchTerm.startsWith('mediation') || lowerCaseSearchTerm.endsWith('mediation')) {
        window.location.href = 'mediation_hearings.php';
    } else if (lowerCaseSearchTerm === 'hearing' || lowerCaseSearchTerm === 'hearings') {
        window.location.href = 'hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('incident')) {
        window.location.href = 'incident_reports.php';
    } else if (lowerCaseSearchTerm.startsWith('conciliation') || lowerCaseSearchTerm.endsWith('conciliation')) {
        window.location.href = 'conciliation_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('arbitration') || lowerCaseSearchTerm.endsWith('arbitration')) {
        window.location.href = 'arbitration_hearings.php';
    } else if (lowerCaseSearchTerm.startsWith('create') || lowerCaseSearchTerm.endsWith('create')) {
        window.location.href = 'create_report.php';
    } else if (lowerCaseSearchTerm.startsWith('ongoing') || lowerCaseSearchTerm.endsWith('ongoing')) {
        window.location.href = 'ongoing_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('settled') || lowerCaseSearchTerm.endsWith('settled')) {
        window.location.href = 'settled_cases.php';
    } else if (lowerCaseSearchTerm.startsWith('incomplete') || lowerCaseSearchTerm.endsWith('incomplete')) {
        window.location.href = 'incomplete_notices.php';
    } else if (lowerCaseSearchTerm.startsWith('home') || lowerCaseSearchTerm.endsWith('home')) {
        window.location.href = 'home.php';
    } else if (lowerCaseSearchTerm.startsWith('account') || lowerCaseSearchTerm.endsWith('account')) {
        window.location.href = 'my_account.php';
    } else if (lowerCaseSearchTerm.startsWith('profile') || lowerCaseSearchTerm.endsWith('profile')) {
        window.location.href = 'my_account.php';
    }  else {
    searchInput1.value = `'${searchTerm.charAt(0).toUpperCase() + searchTerm.slice(1)}' was not found`;
    }
}

function restrictInput(input) {

// Remove special characters and numbers
input.value = input.value.replace(/[^a-zA-Z\s]/g, '');

// Restrict spacebar only if it's the first character
if (input.value.length > 0 && input.value[0] === ' ') {
  input.value = input.value.substring(1); // Remove the leading space
}
}

    </script>

    <style>

        thead tr th{
            font-size: 13px;
            padding: 12px 12px;
        }

        .hearing{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        text-decoration: none;

    }

    .hearing:hover{
        color:#2962ff;
        background: #fff;
        border: 1px solid #2962ff;
        transition: .5s
    }

    .mediation {
        border-radius: 0.2rem;
        background-color: #0956BF;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
      
    }
    
    .arbitration {
        border-radius: 0.2rem;
        background-color: #379711;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    .conciliation {
        border-radius: 0.2rem;
        background-color: #ECD407;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    
    .compliance {
        border-radius: 0.2rem;
        background-color: black;
        color: #fff;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    .non-compliance{
        border-radius: 0.2rem;
        background-color: #fff;
        color: black;
        padding: 5px 5px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 600;
    }

    .details{
        background: #FFCDD2;
        padding: 10px 10px;
        color: #C62828;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
    }
    
    .details:hover{
        background: #C62828;
        color: #FFCDD2;
        transition: .5s;
    }

    table tbody tr:nth-child(even) {
        background-color: #f4f6fb; /* Light gray */
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

.back{
    background-color: white;
    margin-left: 35px;
    height: 40px;
    background: #F5BE1D;
    color: #F2F3F5;
    font-weight: 600;
    cursor: pointer;
    border-radius: 5px;
    text-align: center;
    padding: 3px 3px;
}

.hearing-1{
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
        margin-left: 8%;
    }

    .hearing-1:hover{
        background: #2962ff;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
    }

    .filecourt-action{
        background: #fff;
        padding: 4px 4px;
        color: #bc1823;
        border: 1px solid #bc1823;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: pointer;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        margin-left: 8%;
    }

    .filecourt-action:hover{
        background: #bc1823;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
    }
 

    .settled-cases{
    flex: 1; /* Distribute available space evenly among child elements */
    text-align: center; /* Center align the text */
    padding: 5px 5px;
    width: 10px; /* Add padding for better spacing */
    border: 1px solid #379711; /* Add a border for separation */
    border-radius: 5px; /* Add rounded corners to the divs */
    background-color: #F2F3F5; /* Background color for the divs */
    color: #379711;
    font-size: 18px;
    font-weight: 600;
    margin-left: 3%;
    cursor: default;
}

    .settled-cases:hover{
        color: #379711;
        background: #F2F3F5;
    }

.back{
    background: #379711;
    color: #F2F3F5;
}

.to-notify{
        font-weight: 900;
        color: #bc1823;
    }

    .upcoming-hearing{
        background: #2962ff;
        padding: 4px 4px;
        color: #fff;
        text-transform: uppercase;
        border-radius: 0.2rem;
        cursor: default;
        display: block;
        margin-bottom: 5px;
        width: 10rem;
        margin-left: 0;
        text-decoration: none;
    }

    .pagination {
            display: flex;
            padding-left: 27%;
            margin-top: 10px;
        }
    
        .incident-reports{
            margin-left: 4%; 
            margin-top: 1%; 
            display: flex; 
            font-size: 48px;
        }

        .add-account{
            margin-top: -5%; margin-left: 518px; width: 250px;
        }

        table{
            margin-left: 118px; 
            width: 83.7%; 
            background: #fff; 
            text-align: center;
        }

        @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
            .add-account{
                margin-top: -3.4%;
                margin-left: 530px;
            }

            table{
                margin-left: 150px;
                width: 85.5%;
            }

            .pagination{
                margin-left: 300px;
            }

            .previous{
                margin-top: 20%;
                margin-bottom: 20%;
            }
        }


    </style>

</body>
</html>