<?php
include 'config.php';


if (isset($_POST["verify_email"])) {
    $email = $_POST["email"];
    $verification_code = $_POST["verification_code"];

            // Proceed with verification
            mysqli_query($conn, "UPDATE lupon_accounts SET verification_date = NOW() WHERE email = '" . $email . "' AND verification_code = '" . $verification_code . "'");

            if (mysqli_affected_rows($conn) == 0) {
                $msg_error[] = "Incorrect Code.";

            } else {
                $success_msg[] = "Your Account is now verified.";
            }
        }
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="bootstrap/bootstrap-5.0.2-dist/css/bootstrap.min.css"> 
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
        <link rel="stylesheet" href="./css/account.css">
        <link rel="icon" type="image/x-icon" href="images/favicon.ico">
        <title>Account Registration</title>
    </head>
<body>

    <center>
    <div class="verification-container">
        <img src="images/logo.png" style="width: 110px; margin-top: -10%;">
        <p style="font-weight: 600; text-transform: uppercase; font-size: 28px; margin-top: 3%;">Verify your Account</p>
        <hr style="border: 1px solid #949494; margin: 10px 0; width: 88%;">
        <p style="text-align: justify; font-size: 13px; padding: 15px 15px;">To complete the account verification process, please enter the verification code that we've sent to your email address. This code ensures the security of your account and helps us verify your identity.</p>
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>" style="margin-top: 10%;" required>
            <div class="verification-box" style="padding: 5px 5px;">
            <input type="text" name="verification_code" style="margin-top: 3%; margin-bottom: 5%; border-radius: 3px; border: 2px solid #999; width: 60%;" placeholder="Enter verification code" required>
            <input type="submit" name="verify_email" value="VERIFY" style="background: #E83422; border: none; border-radius: 2px; font-weight: 600; color: #fff;" class="verifyButton" id="verifyButton">
            </div>
        </form>
        <?php
                                        if(isset($msg_error)){
                                            foreach($msg_error as $msg_error){
                                                echo '
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 366px; height: 25px; margin-top: -3%; margin-left: -5px;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; color: #D52826; margin-top: 3px;"></i>
            <span style="margin-left: 5%; font-size: 12px; color: #D52826; font-weight: 600; margin-top: 3px;">'.$msg_error.'</span>
            <i class="fas fa-times" style="margin-left: 55%; color: #D52826; margin-top: 3px;" onclick="this.parentElement.remove();"></i>
            </div>
            ';
        }
    }
?>

<?php
                                        if(isset($success_msg)){
                                            foreach($success_msg as $success_msg){
                                                echo '
                                    <div class="message d-flex" style="background: #e0f19c; border: none; border-radius: 5px; width: 366px; height: 25px; margin-top: -3%; margin-left: -5px;">
                                    <i class="fa-solid fa-circle-check" style="margin-left: 3%; color: #2a4c09; margin-top: 3px"></i>
                                    <div class="error error-txt" style="margin-left: 5%; font-size: 12px; color: #2a4c09; font-weight: 600; margin-top: 3px;">'.$success_msg.'</div>
                                    <i class="fas fa-times" style="margin-left: 13%; color: #2a4c09; margin-top: 3px;" onclick="this.parentElement.remove();"></i>
                                    </div>
                                    <a href="login/barangay.php" class="back" style="text-decoration: none; font-size: 13px; margin-top: 3%; width: 30%; border-radius: 5px; text-transform: uppercase;">Login Now</a>
                                    ';
                                }
                            }
                        ?>
    </div>
    </center>


</body>
</html>