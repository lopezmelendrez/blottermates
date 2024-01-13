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

$activityLogQuery = "

    (SELECT 'execution_notice' AS source, en.incident_case_number, en.timestamp AS formatted_timestamp, CONCAT('User has validated the Agreement for Execution for Case #', en.incident_case_number) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
    FROM execution_notice en
    INNER JOIN hearing h ON en.incident_case_number = h.incident_case_number
    INNER JOIN (
        SELECT DISTINCT incident_case_number, lupon_id
        FROM incident_report
        WHERE lupon_id IN (
            SELECT lupon_id
            FROM lupon_accounts
            WHERE pb_id = $pb_id
        )
    ) subquery ON h.incident_case_number = subquery.incident_case_number
    INNER JOIN lupon_accounts la ON subquery.lupon_id = la.lupon_id
    WHERE la.pb_id = $pb_id)

    UNION

    (
        SELECT 'hearing' AS source, h.incident_case_number, h.timestamp AS formatted_timestamp, CONCAT('Incident Case #', h.incident_case_number, ' has been scheduled for <b>', UPPER(h.hearing_type_status), '</b>') AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM hearing h
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON h.incident_case_number = subquery.incident_case_number
    )

    UNION

    (
        SELECT 'hearing' AS source, h.incident_case_number, h.schedule_change_timestamp AS formatted_timestamp, 
        CONCAT('Hearing Schedule for Incident Case #', h.incident_case_number, ' has been changed') AS activity, 
        NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM hearing h
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON h.incident_case_number = subquery.incident_case_number
        WHERE h.schedule_change_timestamp IS NOT NULL
    )
    
    
    UNION

    (
        SELECT 'hearing' AS source, h.incident_case_number, h.conciliation_timestamp AS formatted_timestamp, 
        CONCAT('Hearing for Incident Case #', h.incident_case_number, ' has been changed to <b>', UPPER(h.hearing_type_status), '</b>') AS activity, 
        NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM hearing h
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON h.incident_case_number = subquery.incident_case_number
        where h.conciliation_timestamp IS NOT NULL
    )    
    
    
    UNION

    (
        SELECT 'hearing' AS source, h.incident_case_number, h.arbitration_timestamp AS formatted_timestamp, 
        CONCAT('Hearing for Incident Case #', h.incident_case_number, ' has been changed to <b>', UPPER(h.hearing_type_status), '</b>') AS activity, 
        NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM hearing h
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON h.incident_case_number = subquery.incident_case_number
        where h.arbitration_timestamp IS NOT NULL
    )    
    
    
    UNION

    (
        SELECT 'notify_residents' AS source, nr.incident_case_number, nr.generated_hearing_timestamp AS formatted_timestamp, CONCAT('Hearing Notice Form has been generated for Incident Case #', nr.incident_case_number) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM notify_residents nr
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON nr.incident_case_number = subquery.incident_case_number
        WHERE nr.generated_hearing_timestamp IS NOT NULL
    )
    
    UNION

    (
        SELECT 'notify_residents' AS source, nr.incident_case_number, nr.generated_summon_timestamp AS formatted_timestamp, CONCAT('Summon for the Respondent Form has been generated for Incident Case #', nr.incident_case_number) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM notify_residents nr
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON nr.incident_case_number = subquery.incident_case_number
        WHERE nr.generated_summon_timestamp IS NOT NULL
    )

    UNION

    (
        SELECT 'notify_residents' AS source, nr.incident_case_number, nr.generated_pangkat_timestamp AS formatted_timestamp, CONCAT('Notice for Constitution of Pangkat generated for Incident Case #', nr.incident_case_number) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM notify_residents nr
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON nr.incident_case_number = subquery.incident_case_number
        WHERE nr.generated_summon_timestamp IS NOT NULL
    )

    UNION

    (
        SELECT 'notify_residents' AS source, nr.incident_case_number, nr.hearing_notified AS formatted_timestamp, CONCAT('The Complainant of Incident Case #', nr.incident_case_number, ' has been notified of their Hearing') AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM notify_residents nr
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON nr.incident_case_number = subquery.incident_case_number
        WHERE nr.hearing_notified IS NOT NULL
    )

    UNION

    (
        SELECT 'notify_residents' AS source, nr.incident_case_number, nr.summon_notified AS formatted_timestamp, CONCAT('The Respondent of Incident Case #', nr.incident_case_number, ' has been notified of their Hearing') AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM notify_residents nr
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON nr.incident_case_number = subquery.incident_case_number
        WHERE nr.summon_notified IS NOT NULL
    )

    UNION

    (
        SELECT 'notify_residents' AS source, nr.incident_case_number, nr.pangkat_notified AS formatted_timestamp, CONCAT('The Respondent of Incident Case #', nr.incident_case_number, ' has been notified of the Pangkat Constitution Notice') AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
        FROM notify_residents nr
        INNER JOIN (
            SELECT DISTINCT incident_case_number, lupon_id
            FROM incident_report
            WHERE lupon_id IN (
                SELECT lupon_id
                FROM lupon_accounts
                WHERE pb_id = $pb_id
            )
        ) subquery ON nr.incident_case_number = subquery.incident_case_number
        WHERE nr.pangkat_notified IS NOT NULL
    )
                  

    UNION

    (SELECT 'incident_report' AS source, ir.incident_case_number, ir.created_at AS formatted_timestamp, CONCAT('Incident Case #', ir.incident_case_number, ' has been created by ', ir.submitter_first_name, ' ', ir.submitter_last_name) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
    FROM incident_report ir
    INNER JOIN lupon_accounts la ON ir.lupon_id = la.lupon_id
    WHERE la.pb_id = $pb_id)
    ORDER BY formatted_timestamp DESC LIMIT 10
";

$result = mysqli_query($conn, $activityLogQuery);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
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
    <title>Activity History</title>
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

    <section class="home" style="margin-left: -0.3%; margin-top: 3%;">
        
        <h1 style="margin-left: 4%; margin-top: -2.3%; display: flex; font-size: 48px;">ACTIVITY HISTORY</h1>

        <div class="audit-box">
        <?php include 'audit_trail.php'; ?>
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

        function loadContent() {
    // Get the value of the selected option
    const selectedOption = document.getElementById("sort").value;

    // Perform redirection or load content based on the selected option
    if (selectedOption === "oldest") {
        window.location.href = "activityhistory.php#oldest";
    } else if (selectedOption === "latest") {
        window.location.href = "activity_history.php#latest";
    }
}

</script>

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

        .container {
            max-width: 800px;
            margin: 20px auto;
            display: inline-block;
            margin-left: 3%;
        }

        .activity-history {
            background: #fff; /* Set the background to white */
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
        }

        .activity-date {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            font-weight: bold;
        }

        .activity {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 10px 0;
            padding: 20px;
        }

        .activity-time {
            color: #777;
        }

        .title{
    font-size: 36px;
    font-weight: 500;
    position: relative;
    margin-top: -8px;
    margin-bottom: -20px;
    margin-left: 4.5%;
  }

 .title::before{
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    height: 3px;
    width: 273px;
    border-radius: 5px;
    background: #F5BE1D;
  }

  @media screen and (min-width: 1331px){
    .activity-history{
        margin-left: 19.2%;
    }
  }
  @media screen and (min-width: 1400px) and (max-width: 1920px) and (min-height: 1080px){
        .activity-history{
            margin-left: 50%;
            margin-top: 4%;
        }
    }

    @media screen and (min-width: 1536px) and (min-height: 730px){
        .activity-history{
            margin-left: 30%;
        }
        .sort-container{
          margin-left: 58.6%;  
        }
    }

    </style>

</body>
</html>