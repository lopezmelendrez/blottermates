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
    <title>Settled Incident Cases</title>
</head>
<body>

<?php include 'nav_bar.php'; ?>

    <section class="home">

    <h1 class="incident-reports">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex;">
            <a href="incident_reports.php" style="text-decoration: none;">
            <div class="back" style="width: 150%;">
            <p style="font-size: 18px; margin-top: 3px;">Back</p>
            </div></a>
            <div class="settled-cases" style="width: 80%; margin-left: 90px; height: 40px;">
                <p>Settled Cases</p>
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

    $rowsPerPage = 3;

    $selectCount = mysqli_query($conn, "SELECT COUNT(ir.incident_case_number) AS total_rows
        FROM `incident_report` AS ir
        INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
        LEFT JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
        LEFT JOIN `court_action` AS court_action ON h.hearing_id = court_action.hearing_id
        LEFT JOIN `execution_notice` AS execution_notice ON ir.incident_case_number = execution_notice.incident_case_number
        WHERE h.date_of_hearing IS NOT NULL 
            AND h.time_of_hearing IS NOT NULL 
            AND (
                amicable_settlement.agreement_description IS NOT NULL 
                OR court_action.lupon_signature IS NOT NULL
            ) 
            AND ir.pb_id = $pb_id");

    $rowCount = mysqli_fetch_assoc($selectCount);
    $num_rows = $rowCount['total_rows'];

    $totalPages = ceil($num_rows / $rowsPerPage);

    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

    echo '<div class="pages" style="display: flex; margin-left: 80%; margin-top: -4%;">';

    // Previous button
    if ($currentPage > 1) {
        // Adjust the styling for all pages except the first and last
        $prevButtonStyle = ($currentPage > 1 && $currentPage < $totalPages) ? 'margin-left: 5px;' : 'margin-left: 59px;';
        echo '<i class="bx bxs-left-arrow-square previous" onclick="navigatePage(' . ($currentPage - 1) . ')" style="font-size: 50px; color: #f5be1d; cursor: pointer; ' . $prevButtonStyle . '"></i>';
    }

    // Next button
    if ($currentPage < $totalPages) {
        // Adjust the styling for all pages except the first and last
        $nextButtonStyle = ($currentPage > 1 && $currentPage < $totalPages) ? 'margin-left: 4px;' : 'margin-left: 59px;';
        echo '<i class="bx bxs-right-arrow-square previous" onclick="navigatePage(' . ($currentPage + 1) . ')" style="font-size: 50px; color: #f5be1d; cursor: pointer; ' . $nextButtonStyle . '"></i>';
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
                    <th>Hearing</th>
                    <th>Date of Agreement</th>
                    <th>Agreement</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
$selectLuponId = mysqli_query($conn, "SELECT pb_id FROM `lupon_accounts` WHERE email_address = '$email'");
if (!$selectLuponId) {
    die('Failed to fetch pb_id: ' . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($selectLuponId);
$pb_id = $row['pb_id'];

$rowsPerPage = 3; // Adjust the number of rows per page as needed
                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $offset = ($page - 1) * $rowsPerPage;

$select = mysqli_query($conn, "
SELECT 
    ir.incident_case_number, 
    ir.complainant_last_name, 
    ir.respondent_last_name, 
    ir.description_of_violation, 
    ir.incident_case_type, 
    ir.incident_date, 
    ir.submitter_first_name, 
    ir.submitter_last_name, 
    ir.created_at, 
    amicable_settlement.date_agreed, 
    amicable_settlement.agreement_description,
    court_action.lupon_signature,
    execution_notice.compliance_status
FROM `incident_report` AS ir
INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
LEFT JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
LEFT JOIN `court_action` AS court_action ON h.hearing_id = court_action.hearing_id
LEFT JOIN `execution_notice` AS execution_notice ON ir.incident_case_number = execution_notice.incident_case_number -- Adjust join condition
WHERE h.date_of_hearing IS NOT NULL 
    AND h.time_of_hearing IS NOT NULL 
    AND (
        amicable_settlement.agreement_description IS NOT NULL 
        OR court_action.lupon_signature IS NOT NULL
    ) 
    AND ir.pb_id = $pb_id
ORDER BY ir.created_at DESC
LIMIT $offset, $rowsPerPage") or die('query failed');

$num_rows = mysqli_num_rows($select);

if ($num_rows === 0) {
echo '<tr><td colspan="8" style="font-size: 25px; font-weight: 600; text-transform: uppercase; text-align: center;">no SETTLED incident cases yet</td></tr>';
} else {
while ($fetch_cases = mysqli_fetch_assoc($select)) {
    $submitter_first_name = $fetch_cases['submitter_first_name'];
    $submitter_last_name = $fetch_cases['submitter_last_name'];
    $submitter_full_name = $submitter_first_name . ' ' . $submitter_last_name;
    $incident_case_number = $fetch_cases['incident_case_number'];
    $compliance_status = $fetch_cases['compliance_status'];
        ?>
    <tr>    
    <td style="width: 9rem;"><?php echo htmlspecialchars(substr($incident_case_number, 0, 9)); ?></td>
            <td><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></td>
            <?php
$incident_case_number = $fetch_cases['incident_case_number'];
$select_hearing = mysqli_query($conn, "SELECT hearing_type_status, date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

if (mysqli_num_rows($select_hearing) > 0) {
    $hearing_data = mysqli_fetch_assoc($select_hearing);
    $hearing_date = date("F j, Y", strtotime($hearing_data['date_of_hearing']));
    $hearing_type_status = $hearing_data['hearing_type_status'];
    $agreementDescription = $fetch_cases['agreement_description'];

    if ($hearing_type_status === 'mediation') {
        echo '<td><p class="mediation">Mediation</p></td>';
        echo '<td>' . date("F j, Y", strtotime($fetch_cases['date_agreed'])) . '</td>';
        if (strlen($agreementDescription) >= 40) {
            echo '<td><span class="ellipsis" onclick="showFullText(this)">' . substr($agreementDescription, 0, 40) . '...</span></td>';
        } else {
            echo '<td>' . $agreementDescription . '</td>';
        }
        echo '<td>' . ($compliance_status !== null ? $compliance_status : '—') . '</td>';
    } elseif ($hearing_type_status === 'conciliation') {
        echo '<td><p class="conciliation">Conciliation</p></td>';
        echo '<td>' . date("F j, Y", strtotime($fetch_cases['date_agreed'])) . '</td>';
        if (strlen($agreementDescription) >= 40) {
            echo '<td><span class="ellipsis" onclick="showFullText(this)">' . substr($agreementDescription, 0, 40) . '...</span></td>';
        } else {
            echo '<td>' . $agreementDescription . '</td>';
        }
        echo '<td>' . ($compliance_status !== null ? $compliance_status : '—') . '</td>';
    } elseif ($hearing_type_status === 'arbitration') {
        echo '<td><p class="arbitration">Arbitration</p></td>';
        echo '<td>' . date("F j, Y", strtotime($fetch_cases['date_agreed'])) . '</td>';
        if (strlen($agreementDescription) >= 40) {
            echo '<td><span class="ellipsis" onclick="showFullText(this)">' . substr($agreementDescription, 0, 40) . '...</span></td>';
        } else {
            echo '<td>' . $agreementDescription . '</td>';
        }
        echo '<td>' . ($compliance_status !== null ? $compliance_status : '—') . '</td>';
    }
    elseif ($hearing_type_status === 'filed to court action') {
        echo '<td colspan="4"><p class="court">Filed to Court Action</p></td>';
    } else {
        echo '<p class="unknown">Unknown</p>';
    }
}
?>
            <td>
            <?php if ($hearing_type_status === 'filed to court action'): ?>
        <a href="casereport_.php?incident_case_number=<?php echo $incident_case_number ?>" class="shownotices" style="width: 100%; padding: 4px 15px;">Details</a>
    <?php else: ?>
        <a href="case_report.php?incident_case_number=<?php echo $incident_case_number ?>" class="shownotices" style="width: 100%; padding: 4px 15px;">Details</a>
    <?php endif; ?>
            </td>
    </tr>
<?php
    }
}
?>

            </tbody>
        </table>

        
        
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
    
        function loadContent() {
            // Get the value of the selected option
            const selectedOption = document.getElementById('transactionTableSelect').value;

            // Perform redirection or load content based on the selected option
            if (selectedOption === 'ongoing') {
                window.location.href = 'ongoingcases.php#ongoing';
            } else if (selectedOption === 'settled') {
                window.location.href = 'settledcases.php#settled';
            }
            else if (selectedOption === 'incomplete') {
                window.location.href = 'incompletenotices.php#incomplete';
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

    .court {
        border-radius: 0.2rem;
        background-color: #bc1823;
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

.pagination {
            display: flex;
            padding-left: 27%;
            margin-top: 10px;
        }

        .pagination a {
            display: inline-block;
            padding: 4px 8px;
            margin: 0 3px;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .pagination a:hover {
            background-color: #ccc;
        }

        .pagination .active {
            background-color: #007bff;
            color: #fff;
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

        @media screen and (min-width: 1920px) and (min-height: 1080px){
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

        @media screen and (min-width: 1536px) and (min-height: 730px){
    .add-account{
        margin-top: -4.5%;
    }
    .pagination{
        margin-top: 1.5%;
        margin-bottom: 1%;
        margin-left: 5.1%;
    }
    table{
        width: 85.5%;
        margin-left: 9%;
    }
    .shownotices{
        margin-left: 1%;   
    }
}

@media screen and (min-width: 1360px) and (min-height: 645px){
    table{
        width: 84.3%;
    }
    .pagination{
        margin-top: 1%;
        margin-bottom: 1%;
    }
}

@media screen and (min-width: 1366px) and (max-width: 1500px) and (min-height: 617px){
    table{
        width: 84.3%;
    }
    .pagination{
        margin-top: 1%;
        margin-bottom: 1%;
    }
}

@media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
            .pagination{
                margin-left: -3%;
            }
        }

        @media screen and (max-width: 2133px) and (min-height: 1055px) and (max-height: 1058px){
        .add-account{
            margin-top: -3.1%;
            margin-left: 26%;
        }
        table{
            margin-left: 7.6%;
            width: 86.5%;
        }
        .shownotices{
            margin-left: 0%;
        }
        .pagination{
            margin-left: 18.2%;
            margin-top: 1.5%;
        }
    }

@media screen and (min-width: 1500px) and (max-width: 1670px) and (min-height: 700px) and (max-height: 760px){
        table{
            margin-left: 8.85%;
            width: 84.5%;
        }
        .pagination{
            margin-left: 5.1%;
            margin-top: 1.5%;
        }
        .add-account{
            margin-top: -4.2%;
        }
    }
    
    @media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
    
        .pagination{
            margin-left: 3.5%;
        }
        
        table{
        margin-left: 9%;
            
        }
    }


    </style>

</body>
</html>