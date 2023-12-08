<?php
session_start();

include '../config.php';

$max_attempts = 3; 
$lockout_duration = 900; 

function getLoginAttempts($conn, $email) {
    $query = "SELECT login_attempts FROM lupon_accounts WHERE email_address = '" . $email . "'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        return (int) $row['login_attempts'];
    }

    $query = "SELECT login_attempts FROM pb_accounts WHERE email_address = '" . $email . "'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    return $row ? (int) $row['login_attempts'] : 0;
}

function updateLoginAttempts($conn, $email, $attempts) {
    $query = "UPDATE lupon_accounts SET login_attempts = " . $attempts . " WHERE email_address = '" . $email . "'";
    mysqli_query($conn, $query);

    $query = "UPDATE pb_accounts SET login_attempts = " . $attempts . " WHERE email_address = '" . $email . "'";
    mysqli_query($conn, $query);
}

function getLastFailedAttemptTimestamp($conn, $email) {
    $query = "SELECT last_failed_attempt FROM lupon_accounts WHERE email_address = '" . $email . "'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        return strtotime($row['last_failed_attempt']);
    }

    $query = "SELECT last_failed_attempt FROM pb_accounts WHERE email_address = '" . $email . "'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    return $row ? strtotime($row['last_failed_attempt']) : 0;
}

function updateLastFailedAttemptTimestamp($conn, $email, $timestamp) {
    $formattedTimestamp = date('Y-m-d H:i:s', $timestamp);
    $query = "UPDATE lupon_accounts SET last_failed_attempt = '" . $formattedTimestamp . "' WHERE email_address = '" . $email . "'";
    mysqli_query($conn, $query);

    $query = "UPDATE pb_accounts SET last_failed_attempt = '" . $formattedTimestamp . "' WHERE email_address = '" . $email . "'";
    mysqli_query($conn, $query);
}

function resetLoginAttemptsIfNeeded($conn, $email, $lockout_duration) {
    $lastFailedAttemptTimestamp = getLastFailedAttemptTimestamp($conn, $email);
    $currentTimestamp = time();
    $timeElapsed = $currentTimestamp - $lastFailedAttemptTimestamp;

    if ($timeElapsed >= $lockout_duration) {
        updateLoginAttempts($conn, $email, 0);
    }

    $lastFailedAttemptTimestampPB = getLastFailedAttemptTimestamp($conn, $email);
    $timeElapsedPB = $currentTimestamp - $lastFailedAttemptTimestampPB;

    if ($timeElapsedPB >= $lockout_duration) {
        updateLoginAttempts($conn, $email, 0);
    }
}


if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email_address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    resetLoginAttemptsIfNeeded($conn, $email, $lockout_duration);

    $attempts = getLoginAttempts($conn, $email);

    if ($attempts >= $max_attempts) {
        $lastFailedAttemptTimestamp = getLastFailedAttemptTimestamp($conn, $email);
        $currentTimestamp = time();
        $timeElapsed = $currentTimestamp - $lastFailedAttemptTimestamp;
        $remainingTimeMinutes = ceil(($lockout_duration - $timeElapsed) / 60);

        if ($remainingTimeMinutes > 0) {
            $msgerror = "Maximum Attempts Reached. Retry in " . $remainingTimeMinutes . " minutes.";
        }
    } else {
        $query = "SELECT * FROM pb_accounts WHERE email_address = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                $_SESSION['pb_id'] = $row['pb_id'];
                $_SESSION['barangay_captain'] = $row['barangay_captain'];

                header('location: ../barangay/home.php');
                exit();
            } else {
                $error = "Invalid Password";
                updateLoginAttempts($conn, $email, $attempts + 1);
                updateLastFailedAttemptTimestamp($conn, $email, time());
            }
        } else {
            $query = "SELECT * FROM lupon_accounts WHERE email_address = '$email'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);

                if ($row['login_status'] == 'disabled') {
                    $msg_error = 'Account is temporarily disabled';
                } elseif (password_verify($password, $row['password'])) {
                    $updateQuery = "UPDATE lupon_accounts SET login_status = 'active' WHERE email_address = '$email'";
                    mysqli_query($conn, $updateQuery);

                    $_SESSION['email_address'] = $email;
                    $_SESSION['lupon_id'] = $row['lupon_id']; 
                    $_SESSION['pb_id'] = $row['pb_id']; 
                    header('location: ../barangay/lupon/home.php');
                    exit();
                } else {
                    $error = "Invalid Password";
                    updateLoginAttempts($conn, $email, $attempts + 1);
                    updateLastFailedAttemptTimestamp($conn, $email, time());
                }
            } else {
                $msg_error = "Email Address not Found";
            }
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatinble" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <title>Login | Barangay Blotter Management System</title>
    </head>
    <body>

        <center>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">

            <div class="row border rounded-4 p-3 bg-white shadow box-area" style="width: 100%;">

                <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box;" style="background: #F9C918; border-radius: 10px;">
                    <div class="featured-image mb-3" style="margin-top: 20px; text-align: center;">
                        <img src="../images/logo.png" class="img-fluid" style="width: 255px;">
                    </div>
                        <p style="font-size: 23px; font-weight: 700; text-align: center; color: #010203; text-transform: uppercase;">Barangay Blotter Management System</p>
                </div>

                <div class="col-md-6 right-box">
                            <span class="header-text-1" style="font-size: 37.5px; margin-left: -100px;">Login to start session</span>
                            <hr style="border: 1px solid #949494; margin: 20px 0;">

                            <?php if (isset($msg_error) && !empty($msg_error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msg_error; ?></span>
            <i class="fas fa-times" style="margin-left: 46%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

    <?php if (isset($msgerror) && !empty($msgerror)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msgerror; ?></span>
            <i class="fas fa-times" style="margin-left: 7%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

                            <?php if (isset($error) && !empty($error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.5%;"><?php echo $error; ?></span>
            <i class="fas fa-times" style="margin-left: 60%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>
                               
                            <form action="" method="post">
                                <div class="input-field" style="margin-top: 8%;">
                                    <input type="text" name="email_address" required>
                                    <span></span>
                                    <label style="font-size: 17px;">Email Address</label>
                                </div>
    
                                <div class="input-field mb-2">
                                    <input type="password" name="password" class="password" id="password" required>
                                    <label style="font-size: 17px;">Password</label>
                                      <div class="pw-display-toggle-btn">
                                        <i class="uil uil-eye-slash showHidePw" style="right: 10px;"></i>
                                      </div>
                                </div>

                                <div class="input-group1" style="margin-bottom: 8%;">
                                        <small><a href="forgot_password.php" class="forgot-pass" style="margin-left: 75%;">Forgot Password?</a></small>
                                </div>
    
                                <div class="input-group1 d-flex">
                                    <input type="button" value="Back" class="btn btn-secondary back-btn fs-4" style="width: 20%; margin-left: 95px;" onclick="history.back()">
                                    <input type="submit" name="submit" value="Login" class="btn btn-danger fs-4" style="width: 60%; margin-left: 10px;">
                                </div>
                            </form>
                </div>

            </div>

        </div>
        </center>

        <footer>
            <p>Department of the Interior and Local Government</p>
            <p>F. Gomez St., Brgy.Kanluran, Old Municipal Hall (Gusaling Museo) 4026, Santa Rosa, 4026 Laguna</p>
            <p>&copy; 2023 DILG All Rights Reserved.</p>
        </footer>

    </body>

    <script>
        const container = document.querySelector(".right-box"),
        pwShowHide = document.querySelectorAll(".showHidePw"),
        pwFields = document.querySelectorAll(".password");

        pwShowHide.forEach(eyeIcon =>{
            eyeIcon.addEventListener("click", ()=>{
                pwFields.forEach(pwField =>{
                    if(pwField.type ==="password"){
                        pwField.type = "text";
                        pwShowHide.forEach(icon =>{
                            icon.classList.replace("uil-eye-slash", "uil-eye");
                        })
                    }else{
                        pwField.type = "password";
                        pwShowHide.forEach(icon =>{
                            icon.classList.replace("uil-eye", "uil-eye-slash");
                        })
                    }
                }) 
            })
        })
    </script>

</html>
