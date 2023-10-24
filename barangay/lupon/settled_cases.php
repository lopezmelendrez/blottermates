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

<?php include 'navbar.php'; ?>

    <section class="home">

    <h1 style="margin-left: 4%; margin-top: 1%; display: flex; font-size: 48px;">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
        <div class="add-account" style="margin-top: -5%; margin-left: 518px; width: 250px;">
        <i class='bx bx-book-add'></i>
        <p style="margin-left: 10px;">Create Incident Report</p>
        </div></a>

        <div class="cases-container" style="display: flex;">
            <a href="incident_reports.php" style="text-decoration: none;">
            <div class="back" style="width: 150%;">
                <p>Back</p>
            </div></a>
            <div class="settled-cases" style="width: 80%; margin-left: 90px; height: 40px;">
                <p>Settled Cases</p>
            </div>
        </div>

        <table style="margin-left: 120px; width: 84%; background: #fff; text-align: center;">
            <thead>
                <tr>
                    <th>Case No.</th>
                    <th>Case Title</th>
                    <th>Case Title</th>
                    <th>Hearing/Action</th>
                    <th>Date of Agreement</th>
                    <th>Date of Execution</th>
                    <th>Agreement</th>
<!--                        <th class="agreement-status">Agreement Status</th>-->
                    <th>Status of Compliance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
$select = mysqli_query($conn, "SELECT ir.incident_case_number, ir.complainant_last_name, ir.respondent_last_name, ir.description_of_violation, ir.incident_case_type, ir.incident_date, ir.submitter_first_name, ir.submitter_last_name, ir.created_at, amicable_settlement.date_agreed, amicable_settlement.agreement_description
FROM `incident_report` AS ir
INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
LEFT JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
WHERE h.date_of_hearing IS NOT NULL AND h.time_of_hearing IS NOT NULL AND amicable_settlement.agreement_description IS NOT NULL")
or die('query failed');
$num_rows = mysqli_num_rows($select);

if ($num_rows === 0) {
echo '<tr><td colspan="8" style="font-size: 25px; font-weight: 600; text-transform: uppercase; text-align: center;">no SETTLED incident cases yet</td></tr>';
} else {
while ($fetch_cases = mysqli_fetch_assoc($select)) {
    $submitter_first_name = $fetch_cases['submitter_first_name'];
    $submitter_last_name = $fetch_cases['submitter_last_name'];
    $submitter_full_name = $submitter_first_name . ' ' . $submitter_last_name;
    $incident_case_number = $fetch_cases['incident_case_number'];
        ?>
    <tr>    
            <td><?php echo $incident_case_number; ?></td>
            <td><?php echo $fetch_cases['complainant_last_name']; ?> vs. <?php echo $fetch_cases['respondent_last_name']; ?></td>
            <td><?php echo $fetch_cases['incident_case_type']; ?></td>
            <td>
            <?php
$incident_case_number = $fetch_cases['incident_case_number'];
$select_hearing = mysqli_query($conn, "SELECT hearing_type_status, date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

if (mysqli_num_rows($select_hearing) > 0) {
    $hearing_data = mysqli_fetch_assoc($select_hearing);
    $hearing_date = date("F j, Y", strtotime($hearing_data['date_of_hearing']));
    $hearing_type_status = $hearing_data['hearing_type_status'];

    if ($hearing_type_status === 'mediation') {
        echo '<p class="mediation">Mediation</p>';
    } elseif ($hearing_type_status === 'conciliation') {
        echo '<p class="conciliation">Conciliation</p>';
    } elseif ($hearing_type_status === 'arbitration') {
        echo '<p class="arbitration">Arbitration</p>';
    } else {
        // Handle the case when hearing_type_status is not one of the specified values
        // You may choose to display a default message or handle the error accordingly.
        echo '<p class="unknown">Unknown</p>';
    }
}
?>
            </td>
            <td><?php echo date("F j, Y", strtotime($fetch_cases['date_agreed'])); ?></td>
            <td>-</td>
            <td><?php echo $fetch_cases['agreement_description']; ?></td>
            <td>-</td>
            <td><a href="case_report.php?incident_case_number=<?php echo $incident_case_number ?>" class="shownotices" style="width: 100%; padding: 4px 15px;">Details</a></td>
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


    </style>

</body>
</html>