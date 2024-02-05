<?php
include '../config.php'; // Include your database configuration

$query = "SELECT 'pb_accounts' AS source, pb.account_id, pb.created_at AS formatted_timestamp, CONCAT('PB Account with ID #', pb.account_id, ' has been created by ', pb.first_name, ' ', pb.last_name) AS activity, pb.first_name AS submitter_first_name, pb.last_name AS submitter_last_name
FROM pb_accounts pb
WHERE pb.account_id = $account_id";

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