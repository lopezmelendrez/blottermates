<?php
include '../config.php'; // Include your database configuration

$activityLogQuery = "

    (SELECT 'execution_notice' AS source, en.incident_case_number, en.timestamp AS formatted_timestamp, CONCAT('User has validated the agreement for execution for Case #', en.incident_case_number) AS activity, NULL AS submitter_first_name, NULL AS submitter_last_name
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
    ORDER BY formatted_timestamp DESC
";

$result = mysqli_query($conn, $activityLogQuery);

if ($result && mysqli_num_rows($result) > 0) {
    $currentDate = null;

    echo '
    <style>
        
        .activity-history{
            margin-top: 3%;
            width: 1190px;
            font-size: 16px;
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
    </style>
</head>
<body>
    <div class="container">

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
    echo 'No recent activity found.';
}
?>