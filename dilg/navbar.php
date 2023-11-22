<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/lupon_home.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">

</head>
<body>
<nav class="sidebar close">
            <header>
                <div class="image-text">
                    <span class="image">
                        <img src="../images/logo.png">
                    </span>

                    <div class="text logo-text">
                        <span class="name"><?php echo $first_name ?> </span>
                        <span class="profession"><?php echo $last_name ?></span>
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
                                <span class="text nav-text">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-link">
                            <a href="transmittal_reports.php">
                            <i class="fa-solid fa-receipt icon"></i>
                                <span class="text nav-text" style="font-size: 16px;">Transmittal Reports</span>
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

</script>
</body>
</html>