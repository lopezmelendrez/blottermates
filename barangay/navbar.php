<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/lupon_home.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.js"></script>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">

</head>
<body>
<nav class="sidebar close">
        <header>
            <div class="image-text">
                    <?php
        $select = mysqli_query($conn, "SELECT * FROM `pb_accounts` WHERE pb_id = '$pb_id'") or die('Query failed');

        if(mysqli_num_rows($select) > 0){
            $fetch = mysqli_fetch_assoc($select);
        }

        if ($fetch['barangay'] == 'Ibaba') {
            echo '<span class="image"><img src="../images/ibaba_logo.png"></span>';
        } else {
            echo '<span class="image"><img src="../images/logo.png"></span>';
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
                        <a href="#">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">Notifications</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Analytics</span>
                        </a>
                    </li>

            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="../logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>

                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Dark mode</span>

                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
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

        modeSwitch.addEventListener("click" , () =>{
            body.classList.toggle("dark");
            
            if(body.classList.contains("dark")){
                modeText.innerText = "Light mode";
            }else{
                modeText.innerText = "Dark mode";
                
            }
        });

        const timeElement = document.querySelector(".time");
        const dateElement = document.querySelector(".date");

        /**
         * @param {Date} date
         */
        function formatTime(date) {
        const hours = date.getHours();
        const hours12 = hours % 12 || 12;
        const minutes = date.getMinutes();
        const isAm = hours < 12;

        // Use conditional (ternary) operator to format hours without leading zero if it's a single digit
        const formattedHours = hours12 < 10 ? hours12.toString() : hours12;

        return `${formattedHours}:${minutes.toString().padStart(2, "0")} ${isAm ? "AM" : "PM"}`;
        }

        // Example usage:
        const now = new Date();
        console.log(formatTime(now)); // Outputs: "7:02 AM" for 07:02 and "12:15 PM" for 12:15


        /**
        * @param {Date} date
        */
        function formatDate(date) {
        const DAYS = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday"
        ];
        const MONTHS = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
        ];

        return `${DAYS[date.getDay()]} - ${
        MONTHS[date.getMonth()]
        } ${date.getDate()}, ${date.getFullYear()}`;
        }

        setInterval(() => {
        const now = new Date();

        timeElement.textContent = formatTime(now);
        dateElement.textContent = formatDate(now);
        }, 200);

</script>
</body>
</html>