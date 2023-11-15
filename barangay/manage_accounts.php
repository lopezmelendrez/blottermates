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
                    <input type="text" placeholder="Search...">
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

    <section class="home" style="margin-left: -0.3%; margin-top: 3%;">

        <h1 style="margin-left: 4%; margin-top: -2.3%; display: flex; font-size: 48px;">ACCOUNT MANAGEMENT</h1>
        
        <table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email Address</th>
            <th style="padding: 13px;">Status</th>
            <th style="padding: 14px;">Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Stella Salle Cruz</td>
            <td>johndoe@example.com</td>
            <td>Active</td>
            <td>2023-11-15</td>
            <td class="actions">
                <button class="btn view" onclick="showViewPopup()">View</button>
                <button class="btn remove" onclick="showRemovePopup()">Remove</button>
                <button class="btn disable" onclick="showDisablePopup()">Disable</button>
            </td>
        </tr>
        <!-- Add more rows as needed -->
    </tbody>
</table>

<div id="view_popup" class="popup">
            <center>
            <div class="modal">
            <button onclick="closeViewPopup()">close</button>
            </div>
            </center>
        </div>

        <div id="remove_popup" class="popup">
            <center>
            <div class="modal">
            <button onclick="closeRemovePopup()">close</button>
            </div>
            </center>
        </div>

        <div id="disable_popup" class="popup">
            <center>
            <div class="modal">
            <button onclick="closeDisablePopup()">close</button>
            </div>
            </center>
        </div>
        


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

        function showViewPopup() {
        // Get the popup element
        var popup = document.getElementById("view_popup");

        // Display the popup
        popup.style.display = "flex";


    }

    function closeViewPopup() {
        var popup = document.getElementById("view_popup");
        popup.style.display = "none";
    }

    function showRemovePopup() {
        // Get the popup element
        var popup = document.getElementById("remove_popup");

        // Display the popup
        popup.style.display = "flex";


    }

    function closeRemovePopup() {
        var popup = document.getElementById("remove_popup");
        popup.style.display = "none";
    }

    function showDisablePopup() {
        // Get the popup element
        var popup = document.getElementById("disable_popup");

        // Display the popup
        popup.style.display = "flex";


    }

    function closeDisablePopup() {
        var popup = document.getElementById("disable_popup");
        popup.style.display = "none";
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

  table {
            width: 1120px;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 3%;
            margin-left: 4%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 12px;
            text-align: center;

        }

        th {
            background-color: #f2f2f2;
        }

        td{
            background-color: #fff;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 8px 12px;
            cursor: pointer;
        }

        .view {
            background-color: #4caf50;
            color: white;
        }

        .remove {
            background-color: #f44336;
            color: white;
        }

        .disable {
            background-color: #2196f3;
            color: white;
        }

        .view:hover {
    background-color: #388e3c; 
    color: #fff;
}

.remove:hover {
    background-color: #d32f2f;
    color: #fff;
}

.disable:hover {
    background-color: #1565c0; 
    color: #fff;
}

.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    justify-content: center;
    align-items: center;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

.modal {
    display: flex; /* or display: none; depending on your initial state */
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    margin-top: 180px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 500px;
    height: 280px;
    overflow-y: hidden;
    margin-left: 30%;
}

.close-icon {
      position: absolute;
      top: 155px;
      left: 875px;
      cursor: pointer;
      font-size: 50px;
      color:#bc1823;
      z-index: 1002;
    }


    </style>

</body>
</html>