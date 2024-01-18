<?php
include '../config.php'; // Include your database configuration

$activityLogQuery = "
    (
        SELECT 'execution_notice' AS source,
        SUBSTRING(en.incident_case_number, 1, 9) AS incident_case_number,
        en.timestamp AS formatted_timestamp,
        CONCAT('User has validated the agreement for execution for Case #', SUBSTRING(en.incident_case_number, 1, 9)) AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        WHERE la.pb_id = $pb_id
    )

    UNION

    (
        SELECT 'hearing' AS source,
        SUBSTRING(h.incident_case_number, 1, 9) AS incident_case_number,
        h.timestamp AS formatted_timestamp,
        CONCAT('Incident Case #', SUBSTRING(h.incident_case_number, 1, 9), ' has been scheduled for <b>', UPPER(h.hearing_type_status), '</b>') AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        SELECT 'hearing' AS source,
        SUBSTRING(h.incident_case_number, 1, 9) AS incident_case_number,
        h.schedule_change_timestamp AS formatted_timestamp,
        CONCAT('Hearing Schedule for Incident Case #', SUBSTRING(h.incident_case_number, 1, 9), ' has been changed') AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        SELECT 'hearing' AS source,
        SUBSTRING(h.incident_case_number, 1, 9) AS incident_case_number,
        h.conciliation_timestamp AS formatted_timestamp,
        CONCAT('Hearing for Incident Case #', SUBSTRING(h.incident_case_number, 1, 9), ' has been changed to <b>', UPPER(h.hearing_type_status), '</b>') AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        WHERE h.conciliation_timestamp IS NOT NULL
    )

    UNION

    (
        SELECT 'hearing' AS source,
        SUBSTRING(h.incident_case_number, 1, 9) AS incident_case_number,
        h.arbitration_timestamp AS formatted_timestamp,
        CONCAT('Hearing for Incident Case #', SUBSTRING(h.incident_case_number, 1, 9), ' has been changed to <b>', UPPER(h.hearing_type_status), '</b>') AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        WHERE h.arbitration_timestamp IS NOT NULL
    )

    UNION

    (
        SELECT 'notify_residents' AS source,
        SUBSTRING(nr.incident_case_number, 1, 9) AS incident_case_number,
        nr.generated_hearing_timestamp AS formatted_timestamp,
        CONCAT('Hearing Notice Form has been generated for Incident Case #', SUBSTRING(nr.incident_case_number, 1, 9)) AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        SELECT 'notify_residents' AS source,
        SUBSTRING(nr.incident_case_number, 1, 9) AS incident_case_number,
        nr.generated_summon_timestamp AS formatted_timestamp,
        CONCAT('Summon for the Respondent Form has been generated for Incident Case #', SUBSTRING(nr.incident_case_number, 1, 9)) AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        SELECT 'notify_residents' AS source,
        SUBSTRING(nr.incident_case_number, 1, 9) AS incident_case_number,
        nr.generated_pangkat_timestamp AS formatted_timestamp,
        CONCAT('Notice for Constitution of Pangkat generated for Incident Case #', SUBSTRING(nr.incident_case_number, 1, 9)) AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        WHERE nr.generated_pangkat_timestamp IS NOT NULL
    )

    UNION

    (
        SELECT 'notify_residents' AS source,
        SUBSTRING(nr.incident_case_number, 1, 9) AS incident_case_number,
        nr.hearing_notified AS formatted_timestamp,
        CONCAT('The Complainant of Incident Case #', SUBSTRING(nr.incident_case_number, 1, 9), ' has been notified of their Hearing') AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        SELECT 'notify_residents' AS source,
        SUBSTRING(nr.incident_case_number, 1, 9) AS incident_case_number,
        nr.summon_notified AS formatted_timestamp,
        CONCAT('The Respondent of Incident Case #', SUBSTRING(nr.incident_case_number, 1, 9), ' has been notified of their Hearing') AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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
        SELECT 'notify_residents' AS source,
        SUBSTRING(nr.incident_case_number, 1, 9) AS incident_case_number,
        nr.pangkat_notified AS formatted_timestamp,
        CONCAT('The Respondent of Incident Case #', SUBSTRING(nr.incident_case_number, 1, 9), ' has been notified of the Pangkat Constitution Notice') AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
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

    (
        SELECT 'incident_report' AS source,
        SUBSTRING(ir.incident_case_number, 1, 9) AS incident_case_number,
        ir.created_at AS formatted_timestamp,
        CONCAT('Incident Case #', SUBSTRING(ir.incident_case_number, 1, 9), ' has been created by ', ir.submitter_first_name, ' ', ir.submitter_last_name) AS activity,
        NULL AS submitter_first_name,
        NULL AS submitter_last_name
        FROM incident_report ir
        INNER JOIN lupon_accounts la ON ir.lupon_id = la.lupon_id
        WHERE la.pb_id = $pb_id
    )

    UNION

    (
        SELECT 
            'lupon_accounts' AS source, 
            la.lupon_id, 
            la.timestamp AS formatted_timestamp, 
            CONCAT(la.first_name, ' ', la.last_name, ' has been registered as Lupon') AS activity, 
            NULL AS submitter_first_name, 
            NULL AS submitter_last_name
        FROM lupon_accounts la
        WHERE la.pb_id = $pb_id
    )

    UNION

(
    SELECT 'amicable_settlement' AS source,
    SUBSTRING(ams.incident_case_number, 1, 9) AS incident_case_number,
    ams.timestamp AS formatted_timestamp,
    CONCAT(
        'Incident Case #', SUBSTRING(ams.incident_case_number, 1, 9),
        ' has been settled through <b>', UPPER(h.hearing_type_status), '</b>'
    ) AS activity,
    NULL AS submitter_first_name,
    NULL AS submitter_last_name
    FROM amicable_settlement ams
    INNER JOIN hearing h ON ams.incident_case_number = h.incident_case_number
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

    
    ORDER BY formatted_timestamp DESC
";

$result = mysqli_query($conn, $activityLogQuery);

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