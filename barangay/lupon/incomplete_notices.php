<?php
include '../../config.php';

session_start();

$email = $_SESSION['email_address'];

if (!isset($email)) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
    <link rel="icon" type="image/x-icon" href="../../images/favicon.ico">
    <title>Incident Cases with Incomplete Notices</title>
</head>

<body>

    <?php include 'nav_bar.php'; ?>

    <section class="home">

        <h1 class="incident-reports">INCIDENT REPORTS</h1>
        <a href="create_report.php" style="text-decoration: none;">
            <div class="add-account">
                <i class='bx bx-book-add'></i>
                <p style="margin-left: 10px;">Create Incident Report</p>
            </div>
        </a>

        <div class="cases-container" style="display: flex;">
            <a href="incident_reports.php" style="text-decoration: none;">
                <div class="back" style="width: 150%;">
                    <p style="font-size: 18px; margin-top: 3px;">Back</p>
                </div>
            </a>
            <div class="incomplete-cases" style="width: 80%; margin-left: 90px; height: 40px;">
                <p>Cases with Incomplete Notices</p>
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

            $selectCount = mysqli_query($conn, "
            SELECT COUNT(*) AS total_rows
            FROM `incident_report`
            LEFT JOIN `notify_residents` ON incident_report.incident_case_number = notify_residents.incident_case_number
            LEFT JOIN `amicable_settlement` ON incident_report.incident_case_number = amicable_settlement.incident_case_number
            WHERE (generate_summon = 'not generated' OR generate_hearing = 'not generated' OR generate_summon IS NULL OR generate_hearing IS NULL)
            AND incident_report.pb_id = $pb_id
        ") or die('count query failed');

            $rowCount = mysqli_fetch_assoc($selectCount);
            $num_rows = $rowCount['total_rows'];

            $totalPages = ceil($num_rows / $rowsPerPage);

            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

            echo '<div class="pages" style="display: flex; margin-left: 80%; margin-top: -4%;">';

            // Previous button
            if ($currentPage > 1) {
                // Adjust the styling for all pages except the first and last
                $prevButtonStyle = ($currentPage > 1 && $currentPage < $totalPages) ? 'margin-left: 5px;' : 'margin-left: 59px;';
                echo '<i class="bx bxs-left-arrow-square previous" onclick="navigatePage(' . ($currentPage - 1) . ')" style="font-size: 50px; color: #C23B21; cursor: pointer; ' . $prevButtonStyle . '"></i>';
            }
        
            // Next button
            if ($currentPage < $totalPages) {
                // Adjust the styling for all pages except the first and last
                $nextButtonStyle = ($currentPage > 1 && $currentPage < $totalPages) ? 'margin-left: 4px;' : 'margin-left: 59px;';
                echo '<i class="bx bxs-right-arrow-square previous" onclick="navigatePage(' . ($currentPage + 1) . ')" style="font-size: 50px; color: #C23B21; cursor: pointer; ' . $nextButtonStyle . '"></i>';
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
                    <th>Date Reported</th>
                    <th>Processed By</th>
                    <th>Date of Hearing</th>
                    <th>Actions</th>
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

                $rowsPerPage = 3; // Declare the variable here
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($page - 1) * $rowsPerPage;

                $select = mysqli_query($conn, "
    SELECT incident_report.incident_case_number AS incident_case_number,
    incident_report.complainant_last_name AS complainant_last_name,
    incident_report.respondent_last_name AS respondent_last_name,
    incident_report.created_at AS created_at,
    incident_report.submitter_first_name as submitter_first_name,
    incident_report.submitter_last_name as submitter_last_name
    FROM `incident_report`
    LEFT JOIN `notify_residents` ON incident_report.incident_case_number = notify_residents.incident_case_number
    LEFT JOIN `amicable_settlement` ON incident_report.incident_case_number = amicable_settlement.incident_case_number
    WHERE (generate_summon = 'not generated' OR generate_hearing = 'not generated' OR generate_summon IS NULL OR generate_hearing IS NULL)
    AND incident_report.pb_id = $pb_id
    AND amicable_settlement.incident_case_number IS NULL  -- Exclude if found in amicable_settlement
    ORDER BY incident_report.created_at DESC
    LIMIT $offset, $rowsPerPage
") or die('query failed');


                $num_rows = mysqli_num_rows($select);

                if ($num_rows === 0) {
                    echo '<tr><td colspan="6" style="font-size: 25px; font-weight: 600; text-transform: uppercase;">no cases with incomplete notice</td></tr>';
                } else {
                    while ($fetch_cases = mysqli_fetch_assoc($select)) {
                        $submitter_first_name = $fetch_cases['submitter_first_name'];
                        $submitter_last_name = $fetch_cases['submitter_last_name'];
                        $submitter_full_name = $submitter_first_name . ' ' . $submitter_last_name;
                        ?>
                        <tr>
                            <td style="width: 9rem;"><?php echo htmlspecialchars(substr($fetch_cases['incident_case_number'], 0, 9)); ?></td>
                            <td><?php echo ucwords($fetch_cases['complainant_last_name']); ?> vs. <?php echo ucwords($fetch_cases['respondent_last_name']); ?></td>
                            <td><?php echo date("F j, Y", strtotime($fetch_cases['created_at'])); ?></td>
                            <td><?php echo $submitter_full_name; ?></td>
                            <td>
                                <?php
                                $incident_case_number = $fetch_cases['incident_case_number'];
                                $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

                                if (mysqli_num_rows($select_hearing) > 0) {
                                    $hearing_data = mysqli_fetch_assoc($select_hearing);
                                    $hearing_date = $hearing_data['date_of_hearing'];

                                    if ($hearing_date !== NULL) {
                                        echo date("F j, Y", strtotime($hearing_date));
                                    } else {
                                        echo '<span style="font-weight: 600;">NO HEARING SCHEDULE YET</span>';
                                    }
                                } else {
                                    echo '<span style="font-weight: 600;">NO HEARING SCHEDULE YET</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $incident_case_number = $fetch_cases['incident_case_number'];
                                $select_hearing = mysqli_query($conn, "SELECT date_of_hearing FROM hearing WHERE incident_case_number = '$incident_case_number'") or die('hearing query failed');

                                if (mysqli_num_rows($select_hearing) > 0) {
                                    $hearing_data = mysqli_fetch_assoc($select_hearing);
                                    $hearing_date = $hearing_data['date_of_hearing'];
                                    if (empty($hearing_date)) {
                                        echo '<div id="popup" class="popup">';
        echo '<center>';
        echo '<div class="modal">';
        echo '<h3 class="modal-title" style="font-size: 18px; text-align:center;">CONFIRMATION</h3>';
        echo '<hr style="border: 1px solid #ccc; margin: 10px 0;">';
        echo '<p style="font-size: 16px; letter-spacing: 1px; text-align: center; margin-top: 10%; margin-bottom: 10%;">Are you sure you want to delete Incident Case #' . htmlspecialchars(substr($incident_case_number, 0, 9)) . '?</p>';
        echo '<hr style="border: 1px solid #ccc; margin: 10px 0;">';
        echo '<div class="button-container" style="display: flex; margin-top: -4%; margin-left: 6%;">';
        echo '<button class="backBtn" onclick="closeConfirmation()" style="width: 100px; padding: 12px 12px; font-weight: 600; background: #fff; border: 1px solid #bc1823; color: #bc1823; margin-left: 190px;">CANCEL</button>';
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="incident_case_number" value="' . $incident_case_number . '">';
        echo '<input type="submit" name="submit" value="YES" class="backBtn" style="width: 150px; padding: 8px 8px; font-size: 20px; font-weight: 600; margin-left: 10px;"></button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</center>';
        echo '</div>';
                                        echo '<a href="../../barangay/lupon/hearing_schedule.php?incident_case_number=' . $incident_case_number . '" class="schedule">Set Hearing Schedule</a>';
                                        echo '<a href="../../tcpdf/generate_kp7.php?incident_case_number=' . $incident_case_number . '" class="shownotices" target="_blank"><i class="bx bx-printer" style="margin-right: 5px;"></i>Generate KP Form #7</a>';
                                    } else {
                                        $hearing_date = date("F j, Y", strtotime($hearing_date));
                                        echo '<a href="../../barangay/lupon/notice_forms.php?incident_case_number=' . $incident_case_number . '" class="shownotices">Create Notice Form(s)</a>';
                                    }
                                } else {
                                    echo '<a href="../../barangay/lupon/hearing_schedule.php?incident_case_number=' . $incident_case_number . '" class="schedule">Set Hearing Schedule</a>';
                                    echo '<a href="../../tcpdf/complainants_form.php?incident_case_number=' . $incident_case_number . '" class="shownotices" target="_blank"><i class="bx bx-printer" style="margin-right: 5px;"></i>Generate KP Form #7</a>';
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

    <script src="search_bar.js"></script>
    <script>
        const body = document.querySelector('body'),
            sidebar = body.querySelector('nav'),
            toggle = body.querySelector(".toggle"),
            searchBtn = body.querySelector(".search-box"),
            modeSwitch = body.querySelector(".toggle-switch"),
            modeText = body.querySelector(".mode-text");


        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        })

        searchBtn.addEventListener("click", () => {
            sidebar.classList.remove("close");
        })



    </script>

    <style>
        .close-icon{
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
        margin-left: 0;
        text-decoration: none;
        font-size: 13px;
        }

        .close-icon:hover{
        background: #bc1823;
        color: #fff;
        border: 1px solid #fff;
        transition: .5s;
    }

        thead tr th {
            font-size: 13px;
            padding: 12px 12px;
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

        .home {
            position: absolute;
            top: 0;
            top: 0;
            left: 250px;
            height: 100vh;
            width: calc(100% - 78px);
            background-color: var(--body-color);
            transition: var(--tran-05);
        }

        .sidebar.close~.home {
            left: 78px;
            height: 100vh;
            width: calc(100% - 78px);
        }

        .back {
            background-color: white;
            margin-left: 35px;
            height: 40px;
            background-color: #C23B21;
            color: #F2F3F5;
            font-weight: 600;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            padding: 3px 3px;
        }

        .previous:hover {
            color: #bc1823;
            cursor: pointer;
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
            .shownotices{
                margin-left: 18%;
                width: 15rem;
            }
        }

        .backBtn{
            display: flex;
    align-items: center;
    justify-content: center;
    height: 45px;
    max-width: 200px;
    border: none;
    outline: none;
    color: #fff;
    border-radius: 5px;
    margin: 25px 0;
    background-color: #E83422;
    transition: all 0.3s linear;
    cursor: pointer;
        }

        .backBtn:hover{
    background-color: #bc1823;
    }

        .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal {
        display: block;
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    margin-top: 180px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 500px;
    height: 280px;
    overflow-y: hidden;
    margin-left: 35%;
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
        margin-left: 8.8%;
    }
    .schedule{
        margin-left: 6%;
    }
    .shownotices{
        margin-left: 6%;   
    }
    .close-icon{
        margin-left: 6%;
    }
    .modal{
        margin-top: 15%;
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
        .schedule,.shownotices, .close-icon{
            margin-left: 23%;
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
        .schedule,.shownotices, .close-icon{
            margin-left: 10%;
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
