<?php
include '../config.php'; // Include your database configuration

$activityLogQuery = "

(SELECT 'lupon_accounts' AS source, la.timestamp AS formatted_timestamp, CONCAT(la.first_name, ' ', la.last_name, ' has registered as Lupon.') AS activity, NULL AS incident_case_number, NULL AS submitter_first_name, NULL AS submitter_last_name
FROM lupon_accounts la
WHERE la.pb_Id = $pb_id)

UNION

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
    ORDER BY formatted_timestamp DESC LIMIT 10
";



$result = mysqli_query($conn, $activityLogQuery);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">';
        echo '<div style="font-size: 14px; positiion: fixed; width: 17rem;">' . $row['activity'] . '</div>';       
        $formattedTimestamp = date('M, d, Y - h:i A', strtotime($row['formatted_timestamp']));
        echo '<div style="color: gray; font-style: italic; font-size: 11px;">• ' . $formattedTimestamp . '</div>';
        echo '</div>';
        echo '<hr style="border: 1px solid #bfbfbf; margin: 5px 0; width: 100%; margin-top: 2%;">';
    }
} else {
    echo 'No recent activity found.';
}

?>
