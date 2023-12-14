<?php
include '../config.php';
if(isset($_REQUEST['reset']))
{
    $email_address = mysqli_real_escape_string($conn, $_REQUEST['email_address']);
     // Check email in lupon_accounts
     $check_email_lupon = mysqli_query($conn, "SELECT * FROM `lupon_accounts` WHERE email_address = '$email_address'");
     $res_lupon = mysqli_num_rows($check_email_lupon);
 
     // Check email in pb_accounts
     $check_email_pb = mysqli_query($conn, "SELECT * FROM `pb_accounts` WHERE email_address = '$email_address'");
     $res_pb = mysqli_num_rows($check_email_pb);
    
     if ($res_lupon > 0 || $res_pb > 0)
    {
    $message = '<html>
    <head>
        <style>
        body {
         margin: 0;
         padding: 0;
         background-color: #f2f2f2;
         font-family: Arial, sans-serif;
         font-size: 14px;
         line-height: 1.5;
     }
     /* Add styles for the email container */
     .email-container {
         max-width: 600px;
         margin: 0 auto;
         border: 1px solid #ccc;
         background-color: #fff;
         padding: 20px;
         
     }

     .header {
         display: flex;
         align-items: center;
     }
            
     .logo {
         width: 50px; 
         height: auto; 
         margin-right: 10px; 
     }
            
     .logo-text {
         font-weight: bold;
         font-size: 16px; 
         margin-top: 2%;
     }

     .button-container {
     text-align: center;
     margin-top: 20px;
 }

     .btn-primary {
     display: inline-block;
     text-decoration: none;
     border-radius: 4px;
     font-size: 16px;
     padding: 10px;
     border-radius: 5px;
     background: #E83422;
     border: none;
     color: #fff;
     font-weight: 600;
     letter-spacing: 1px;
 }

     .btn-primary:hover {
     background-color: #bc1823;
 }

     </style>
    </head>
    <body>
    <div class="email-container">
        <div class="header">
            <img src="cid:logo" alt="Logo" class="logo">
            <span class="logo-text">Barangay Justice Management System</span>
        </div>
        <p style="font-size: 35px; text-align: center;"><b>PASSWORD RESET<b><p>
        <p style="font-size: 16px; margin-left: 10px; text-align: justify;">We have received a request to reset your password for your account. To proceed with the password reset, please click on the button below:<p>
         <div class="button-container">
         <button class="btn btn-primary"><a href="http://localhost/barangay%20justice%20management%20system%2001/login/reset_password.php?secret='.base64_encode($email_address).'" style="text-decoration: none; color: #fff; font-weight: 600; text-transform: uppercase;">reset password</a></button>
         </div>
         <p style="font-size: 14px;">The password reset link expires in 15 minutes. If the link expires, request a new password reset email on our website.<p>
        <br>
        <p style="font-size: 12px; color: #C7C7BA; font-weight: 400;">If you did not request a password reset, please ignore this email. Rest assured that your account is still secure.</p>
        </body>
     <div>
     </html>';

include_once("../SMTP/class.phpmailer.php");
include_once("../SMTP/class.smtp.php");
$email = $email_address; 
$mail = new PHPMailer;
$mail->IsSMTP();
$mail->SMTPAuth = true;                 
$mail->SMTPSecure = "tls";      
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587; 
$mail->Username = "brgyblottermanagementsystem@gmail.com";  
$mail->Password = "qfuwnhtdpniedxfa";  
$mail->FromName = "Barangay Justice Management System";
$mail->AddAddress($_REQUEST['email_address']);
$mail->AddEmbeddedImage('../images/logo.png', 'logo');
$mail->Subject = "Reset Password";
$mail->isHTML( TRUE );
$mail->Body =$message;
if($mail->send())
{
  $success_msg[] = "We have e-mailed your password reset link!";
}
}
else
{
  $msg_error = "Email Address not found in our system.";
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
    <title>Reset Password | Barangay Blotter Management System</title>
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
                            <span class="header-text-1" style="font-size: 37.5px; margin-left: -120px;">Reset Your Password</span>
                            <hr style="border: 1px solid #949494; margin: 20px 0;">

                            <?php if (isset($msg_error) && !empty($msg_error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 14px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msg_error; ?></span>
            <i class="fas fa-times" style="margin-left: 32%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

    <?php if (isset($msgerror) && !empty($msgerror)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 14px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msgerror; ?></span>
            <i class="fas fa-times" style="margin-left: 47%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

                            <?php if (isset($error) && !empty($error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.5%;"><?php echo $error; ?></span>
            <i class="fas fa-times" style="margin-left: 60%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>
                    

    <p style="font-size: 15px; margin-top: 3%; text-align: justify;">Enter the email address associated with your account to change your password.</p>
            
    <?php
                            if(isset($success_msg)){
                                foreach($success_msg as $success_msg){
                                    echo '   
                                    <div class="message d-flex" style="background: #e0f19c; border: none; border-radius: 5px; width: 100%; margin-top: 3%;">
                                    <i class="error error-icon fas fa-circle-check" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #2a4c09;"></i>
                                    <div class="error error-txt" style="margin-left: 2%; font-size: 16px; margin-top: 0.5%; color: #2a4c09; font-weight: 600;">'.$success_msg.'</div>
                                    <i class="fas fa-times" style="margin-left: 15%; margin-top: 0.4%; font-size: 24px; color: #2a4c09;" onclick="this.parentElement.remove();"></i>
                                    </div>
                                    ';
                                }
                            }
                        ?>

    <form action="#" method="post">
                            <div class="custom-search" style="margin-top: 3%;">
                            <input type="email_address" id="email_address" class="custom-search-input" style="font-size: 15px;" name="email_address" placeholder="Email Address" required>
                            <button input type="submit" id="login" name="reset" class="custom-search-button">Send Password Reset Link</button>
                    </div>

                    <input type="button" value="Back" class="btn btn-secondary back-btn fs-4" style="width: 30%; margin-left: 360px; margin-top: 10%;" onclick="history.back()">
                
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
    
    .header-text-1::after{
    content: "";
    display: block;
    border-bottom: 3px solid #F9C918; /* Adjust the color and thickness as needed */
    width: 76%;
    border-radius: 25px;
    margin-left: -23%;
    }

    .custom-search {
    position: relative;
    width: 100%;
}

.custom-search-input {
    width: 100%;
    border: 2px solid #adadad;
    background: #fff;
    font-size: 13px;
    border-radius: 5px;
    padding: 10px 100px 10px 20px;
    line-height: 1;
    box-sizing: border-box;
    outline: none;
}

.custom-search-button {
    position: absolute;
    font-size: 10px;
    right: 3px;
    top: 3px;
    bottom: 3px;
    border: 0;
    background: #e83422;
    color: #fff;
    outline: none;
    margin: 0;
    padding: 0 10px;
    border-radius: 5px;
}

.custom-search-button:hover{
    background: #bc1823;
}

    </style>

</html>
