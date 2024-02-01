<?php
session_start();

include '../config.php';

$max_attempts = 3;
$lockout_duration = 900;

function getLoginAttempts($conn, $email)
{
    $query = "SELECT login_attempts FROM account WHERE email_address = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $attempts);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $attempts ? (int)$attempts : 0;
}

function updateLoginAttempts($conn, $email, $attempts)
{
    $query = "UPDATE account SET login_attempts = ? WHERE email_address = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $attempts, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getLastFailedAttemptTimestamp($conn, $email)
{
    $query = "SELECT last_failed_attempt FROM account WHERE email_address = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $timestamp);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $timestamp ? strtotime($timestamp) : 0;
}

function updateLastFailedAttemptTimestamp($conn, $email, $timestamp)
{
    $formattedTimestamp = date('Y-m-d H:i:s', $timestamp);
    $query = "UPDATE account SET last_failed_attempt = ? WHERE email_address = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $formattedTimestamp, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function resetLoginAttemptsIfNeeded($conn, $email, $lockout_duration)
{
    $lastFailedAttemptTimestamp = getLastFailedAttemptTimestamp($conn, $email);
    $currentTimestamp = time();
    $timeElapsed = $currentTimestamp - $lastFailedAttemptTimestamp;

    if ($timeElapsed >= $lockout_duration) {
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
            $msg_error = "Maximum Attempts Reached. Retry in " . $remainingTimeMinutes . " minutes.";
        }
    } else {
        $query = "SELECT * FROM account WHERE email_address = ? AND account_role = 'DILG'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                $_SESSION['account_id'] = $row['account_id'];
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['last_name'] = $row['last_name'];
                $_SESSION['account_role'] = $row['account_role'];

                header('location: ../dilg/home.php');
                exit();
            } else {
                $error = "Invalid Password";
                updateLoginAttempts($conn, $email, $attempts + 1);
                updateLastFailedAttemptTimestamp($conn, $email, time());
            }
        } else {
            $msgerror = "Email Address not Found";
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
    <script>
  window.onload = function() {
    if (/Mobi|Android/i.test(navigator.userAgent)) {
      window.location.href = 'error_page.php';
    }
  };
</script>

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
                            <span class="header-text-1">Login to start session</span>
                            <hr style="border: 1px solid #949494; margin: 20px 0;">

                            <?php if (isset($error) && !empty($error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.5%;"><?php echo $error; ?></span>
            <i class="fas fa-times" style="margin-left: 60%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

    <?php if (isset($msgerror) && !empty($msgerror)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msgerror; ?></span>
            <i class="fas fa-times" style="margin-left: 47%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

    <?php if (isset($msg_error) && !empty($msg_error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msgerror; ?></span>
            <i class="fas fa-times" style="margin-left: 7%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
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
                                       <!-- <small><a href="forgot_password.php" class="forgot-pass" style="margin-left: 75%;">Forgot Password?</a></small>-->
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

<style>

.header-text-1{
    font-size: 37.5px; 
    margin-left: -100px;
}

@media screen and (min-width: 1920px) and (min-height: 1080px){
    .header-text-1{
        margin-left: -160px;
    }

    .header-text-1::after{
        width: 68.5%;
        margin-left: -160px;
    }
}

@media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
            .header-text-1::after{
                width: 67.5%;
                margin-left: -25%;
            }
            .header-text-1{
                margin-left: -25%;
            }
            .input-group1{
                margin-left: 15%;
            }
        }

        @media screen and (min-width: 1280px) and (max-width: 1290px) and (min-height: 569px){
            .header-text, .header-text::after{
                margin-left: 4%;
            }
            .barangay-btn{
                margin-left: 7.5%;
            }
        }

        
@media screen and (min-width: 1331px) and (max-width: 1400px){
    .header-text-1, .header-text-1::after{
        margin-left: -15%;
    }
}

@media screen and (min-width: 2133px) and (min-height: 1058px){
    .header-text-1, .header-text-1::after{
        margin-left: -25%;
    }
    .header-text-1::after{
        width: 66.9%;
    }
    .input-group1{
        margin-left: 18%;
    }
    footer{
        margin-top: -2%;
    }
}

@media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
.header-text-1::after{
                width: 67.5%;
                margin-left: -25%;
            }
            .header-text-1{
                margin-left: -25%;
            }
            .input-group1{
                margin-left: 15%;
            }
}
</style>

</html>
