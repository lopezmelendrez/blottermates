<?php
include '../config.php'; // Include your database configuration

$query = "(SELECT 'pb_accounts' AS source, pb.account_id, pb.created_at AS formatted_timestamp, 
          CONCAT('Account for Barangay ', pb.barangay, ' has been registered with ', pb.barangay_captain, ' as the Punong Barangay') AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
          FROM pb_accounts pb
          WHERE pb.account_id = $account_id)
          UNION
          (SELECT 'lupon_accounts' AS source, la.lupon_id, la.timestamp AS formatted_timestamp, 
          CONCAT(la.first_name, ' ', la.last_name, ' has been registered as Lupon in Barangay ', pb.barangay) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
          FROM lupon_accounts la
          JOIN pb_accounts pb ON la.pb_id = pb.pb_id)
          UNION
          (SELECT 'incident_report' AS source, LEFT(ir.incident_case_number, 9) AS incident_case_number, ir.created_at AS formatted_timestamp, 
    CONCAT('Incident Case ', LEFT(ir.incident_case_number, 9), ' has been created by ', ir.submitter_first_name, ' ', ir.submitter_last_name, ' in Barangay ', pb.barangay) AS activity, 
    ir.submitter_first_name, ir.submitter_last_name
FROM incident_report ir
JOIN pb_accounts pb ON ir.pb_id = pb.pb_id)
          UNION
          (SELECT 'hearing' AS source, h.incident_case_number, h.timestamp AS formatted_timestamp, 
          CONCAT('Incident Case #', h.incident_case_number, ' has been scheduled for <b>', UPPER(h.hearing_type_status), '</b> in Barangay ', pb.barangay) AS activity, ir.submitter_first_name, ir.submitter_last_name
          FROM hearing h
          JOIN incident_report ir ON h.incident_case_number = ir.incident_case_number
          JOIN pb_accounts pb ON ir.pb_id = pb.pb_id)
          UNION
          (SELECT 'hearing' AS source, LEFT(h.incident_case_number, 9) AS incident_case_number, h.schedule_change_timestamp AS formatted_timestamp, 
    CONCAT('Hearing Schedule for Incident Case #', LEFT(h.incident_case_number, 9), ' has been changed in Barangay ', pb.barangay) AS activity, 
    NULL AS submitter_first_name, NULL AS submitter_last_name
FROM hearing h
INNER JOIN (
    SELECT DISTINCT LEFT(incident_case_number, 9) AS incident_case_number, lupon_id
    FROM incident_report
) subquery ON h.incident_case_number = subquery.incident_case_number
INNER JOIN pb_accounts pb ON pb.pb_id = (
    SELECT pb_id
    FROM lupon_accounts
    WHERE lupon_id = subquery.lupon_id
    LIMIT 1
)
WHERE h.schedule_change_timestamp IS NOT NULL)

UNION

(SELECT 'hearing' AS source, LEFT(h.incident_case_number, 9) AS incident_case_number, h.conciliation_timestamp AS formatted_timestamp, 
    CONCAT('Hearing for Incident Case #', LEFT(h.incident_case_number, 9), ' has been changed to <b>', UPPER(h.hearing_type_status), '</b> in Barangay ', pb.barangay) AS activity, 
    NULL AS submitter_first_name, NULL AS submitter_last_name
FROM hearing h
INNER JOIN (
    SELECT DISTINCT LEFT(incident_case_number, 9) AS incident_case_number, lupon_id
    FROM incident_report
) subquery ON h.incident_case_number = subquery.incident_case_number
INNER JOIN pb_accounts pb ON pb.pb_id = (
    SELECT pb_id
    FROM lupon_accounts
    WHERE lupon_id = subquery.lupon_id
    LIMIT 1
)
WHERE h.conciliation_timestamp IS NOT NULL)
          UNION
          (SELECT 'hearing' AS source, LEFT(h.incident_case_number, 9) AS incident_case_number, h.arbitration_timestamp AS formatted_timestamp, 
          CONCAT('Hearing for Incident Case #', LEFT(h.incident_case_number, 9), ' has been changed to <b>', UPPER(h.hearing_type_status), '</b> in Barangay ', pb.barangay) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
          FROM hearing h
          INNER JOIN (
              SELECT DISTINCT LEFT(incident_case_number, 9) AS incident_case_number, lupon_id
              FROM incident_report
              ) subquery ON h.incident_case_number = subquery.incident_case_number
          INNER JOIN pb_accounts pb ON pb.pb_id = (
              SELECT pb_id
              FROM lupon_accounts
              WHERE lupon_id = subquery.lupon_id
              LIMIT 1
          )
          WHERE h.arbitration_timestamp IS NOT NULL)
          UNION
          (SELECT 'execution_notice' AS source, LEFT(en.incident_case_number, 9) AS incident_case_number, en.timestamp AS formatted_timestamp, 
    CONCAT('Barangay ', pb.barangay, ' has validated the Agreement for Execution for Case #', LEFT(en.incident_case_number, 9)) AS activity, 
    NULL AS submitter_first_name, NULL AS submitter_last_name
FROM execution_notice en
INNER JOIN hearing h ON en.incident_case_number = h.incident_case_number
INNER JOIN pb_accounts pb ON en.pb_id = pb.pb_id
INNER JOIN (
    SELECT DISTINCT incident_case_number, lupon_id
    FROM incident_report
) subquery ON h.incident_case_number = subquery.incident_case_number
INNER JOIN lupon_accounts la ON subquery.lupon_id = la.lupon_id)

        ORDER BY formatted_timestamp ASC";



$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $currentDate = null;

    echo '
    <style>
        
        .activity-history{
            margin-top: 3%;
            font-size: 14px;
            margin-left: 16%;
            width: 880px;
        }

        .activity-date {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            font-weight: bold;
            font-size: 15px;
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

        .text-box{
            
        }

        .sort-filter-box {
            background-color: #F2F3F5;
            padding: 5px 4px;
            font-size: 15px;
            border-radius: 4px;
            margin-right: 10px;
            width: 100px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
            /* Add box shadow */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        

        .sort-container {
            margin-left: 60.6%;
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        #sort {
            height: 33px;
        }

        @media screen and (min-width: 1331px){
            .activity{
                margin-left: 30%;
            }
        }

    </style>
</head>
<body>
    
<div class="sort-container">
    <div class="sort-filter-box">Sort By:</div>
    <select id="sort" onchange="loadContent()">
        <option value="latest">From Latest to Oldest</option>
        <option value="oldest">From Oldest to Latest</option>
    </select>
</div>
    <div class="container" style="margin-top: -1%;">

    <div class="activity-history">';

    while ($row = mysqli_fetch_assoc($result)) {
        $formattedTimestamp = date('d M Y', strtotime($row['formatted_timestamp']));

        // Check if the date has changed
        if ($formattedTimestamp != $currentDate) {
            // Display the date as a header
            echo '<div class="activity-date">' . $formattedTimestamp . '</div>';
            $currentDate = $formattedTimestamp;
        }
        
        echo '<div class="activity">';
        echo '<div class="activity-time">' . date('h:i A', strtotime($row['formatted_timestamp'])) . '</div>';
        echo $row['activity'];
        echo '</div>';
    }

    echo '</div>
    </div>
</body>
</html>';
} else {
    echo '<div class="text-box" style="margin-left: 30%;
    margin-top: 15%;
    background: #b9bbb6;
    border-radius: 5px;
    color: #fff;
    font-size: 35px;
    width: 500px;
    padding: 13px 13px;
    text-align: center;
    letter-spacing: 1;
    text-transform: uppercase;">No Recent Activity Yet.</div>';
}
?>