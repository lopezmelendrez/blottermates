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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $execution_date = $_POST['execution_date'];
    $compliance_status = $_POST['compliance_status'];
    $remarks = $_POST['remarks'];
    $incident_case_number = $_POST['incident_case_number'];
    

    $insert_query = "INSERT INTO `execution_notice` (`pb_id`, `execution_date`, `compliance_status`, `remarks`, `incident_case_number`)
                 VALUES ('$pb_id', '$execution_date', '$compliance_status', '$remarks', '$incident_case_number')";


    $result = mysqli_query($conn, $insert_query);
    if ($result) {
        header("Location: incident_reports.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="../css/dilg.css">
    <link rel="stylesheet" href="../css/lupon_home.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
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

            </div>

            <div class="bottom-content">
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

    <section class="home" style="margin-left: -0.3%;">
        
    <center>
            <div class="add-account-container" style="height: 485px; width: 800px; margin-top: 5%; margin-left: 3%;">
            <?php
            $incident_case_number = $_GET['incident_case_number'];
            $select = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE incident_case_number = '$incident_case_number'") or die('query failed');
            $fetch_cases = mysqli_fetch_assoc($select);
            ?>
                <div class="header-text" style="font-size: 25px;">AGREEMENT OF EXECUTION FOR CASE #<?php echo substr($incident_case_number, 0, 9); ?></div>
                
                <form action="" method="post" style="height: 380px; width: 750px;">
                    <div class="fields">
                    <input type="hidden" name="incident_case_number" value="<?php echo $incident_case_number; ?>">
                        <div class="input-field-1">
                            <label class="required-label">Date of Agreement Execution</label>
                            <input type="text" name="execution_date" id="datepicker" placeholder="" required readonly>
                        </div>
                        <div class="input-field-1">
                            <label class="required-label">Compliance Status</label>
                            <select name="compliance status">
                                <option>COMPLIANCE</option>
                                <option>NON-COMPLIANCE</option>
                            </select>
                        </div>
                        <div class="input-field" style="width: 100%;">
                            <label class="required-label">Remarks</label>
                            <textarea style="width: 100%; height: 150px; padding: 10px 15px; border: 1px solid #aaa; outline: none; font-size: 14px; border-radius: 5px; font-weight: 400; margin-top: 8px; resize: vertical;" name="remarks" required></textarea>
                        </div>    
                                
                        
                    <div class="input-group1 d-flex" style="margin-top: 4%;">
                        <a href="home.php" style="text-decoration: none;"><input type="button" value="Back" class="btn btn-secondary back-btn" style="width: 15%; margin-left: 460px;" onclick="history.back()"></a>
                        <input type="submit" name="submit" value="Execute Agreement" class="btn btn-danger" style="width: 25%; margin-left: 25px;">
                    </div>


                </form>
            </div>
            </center>

    </section>


    <script src="search_bar.js"></script>
    <script>
    
    $(function () {
    $("#datepicker").datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0,
        beforeShowDay: function (date) {
            var day = date.getDay();
            return [day != 0 && day != 6, ''];
        },
        beforeShow: function (input, inst) {
            var currentDate = new Date();
            var weekdaysToAdd = 5;
            var endDate = new Date(currentDate);

            while (weekdaysToAdd > 0) {
                endDate.setDate(endDate.getDate() + 1);
                if (endDate.getDay() !== 0 && endDate.getDay() !== 6) {
                    weekdaysToAdd--;
                }
            }

            $("#datepicker").datepicker("option", "maxDate", endDate);
        }
    });
});


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

        const incidentDateInput = document.querySelector('input[name="execution_date"]');

        incidentDateInput.addEventListener('change', function(event) {
            const selectedDate = new Date(event.target.value);
            const currentDate = new Date();

            if (selectedDate < currentDate) {
                const formattedCurrentDate = currentDate.toISOString().slice(0, 10);
                incidentDateInput.value = formattedCurrentDate;
            }
        });

        

    </script>

    <style>

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

    .required-label::after{
    content: '*';
    color: red;
    margin-left: 5px;
    }

    </style>

</body>
</html>