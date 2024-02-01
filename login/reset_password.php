<?php
include '../config.php';
if(isset($_REQUEST['submit']))
{
    $email = $_REQUEST['email_address'];
    $password = $_REQUEST['password'];
    $confirm_password = $_REQUEST['confirm_password'];

    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update password in lupon_accounts
        $reset_pwd_lupon = mysqli_query($conn, "UPDATE `lupon_accounts` SET password='$hashed_password' WHERE email_address='$email'");

        // Update password in pb_accounts
        $reset_pwd_pb = mysqli_query($conn, "UPDATE `pb_accounts` SET password='$hashed_password' WHERE email_address='$email'");

        if ($reset_pwd_lupon > 0 || $reset_pwd_pb > 0) {
            $success_msg[] = 'Password updated.';
        } else {
            $message[] = "Error while updating password.";
        }
    } else {
        $message[] = 'Passwords do not match.';
    }
}

if($_GET['secret'])
{
  $email = base64_decode($_GET['secret']);
  $check_details_lupon = mysqli_query($conn, "SELECT * FROM `lupon_accounts` WHERE email_address = '$email'");
  $check_details_pb = mysqli_query($conn, "SELECT * FROM `pb_accounts` WHERE email_address = '$email'");

  $res_lupon = mysqli_num_rows($check_details_lupon);
  $res_pb = mysqli_num_rows($check_details_pb);
  if ($res_lupon > 0 || $res_pb > 0)
    { ?>

<?php } } ?>
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
                            <span class="header-text-1">Reset Your Password</span>
                            <hr style="border: 1px solid #949494; margin: 20px 0;">

                            <p style="font-size: 15px; margin-top: 3%; text-align: center; font-weight: 500;">Please Enter your New Password.</p>

                            <?php if (isset($msg_error) && !empty($msg_error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msg_error; ?></span>
            <i class="fas fa-times" style="margin-left: 35%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

    <?php if (isset($msgerror) && !empty($msgerror)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 16px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msgerror; ?></span>
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

    <?php
                                        if(isset($success_msg)){
                                            foreach($success_msg as $success_msg){
                                                echo '
                                                <div class="message d-flex" style="background: #EDF1D6; border: none; border-radius: 5px; width: 100%; margin-top: 2%; padding: 2px 2px;">
                                                <i class="fa-solid fa-circle-check" style="margin-left: 3%; margin-top: 1%; font-size: 20px; color: #40513B;"></i>
                                                <span style="margin-left: 15%; font-size: 18px; color: #40513B; font-weight: 600;">'.$success_msg.'</span>
                                                <i class="fas fa-times" style="margin-left: 15%; margin-top: 4px; color: #40513B; font-size: 24px;" onclick="this.parentElement.remove();"></i>
                                                </div>
                                    ';
                                }
                            }
                        ?>
            
    <form action="" method="post">

<input type="hidden" name="email_address" value="<?php echo $email; ?>"/>

<div class="custom-search mb-2" style="margin-top: 6%;">
<input type="text" class="custom-search-input" id="password" name="password" placeholder="Password" style="font-size: 18px; cursor: auto;" required>
<button onclick="createPassword()" class="custom-search-button" type="submit">Generate Password</button>
</div>
        
<div class="custom-search mb-3" style="margin-top: 6%;">
<input type="password" class="custom-search-input" name="confirm_password" placeholder="Confirm Password" required class="box" style="font-size: 18px;">
<button class="custom-search-button" style="width: 120px;" type="submit" name="submit">Update Password</button>
</div>


<!--<a href="../index.php" style="text-decoration: none;">
<input type="button" value="Back" class="btn btn-secondary back-btn fs-4" style="width: 30%; margin-left: 360px; margin-top: 7%;">
</a>-->
<!--<div class="input-group1" style="width: 50%; margin-left: 23%; margin-top: 30%;">
    <input type="submit" name="submit" value="Update" class="button">
    
</div>-->
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

        const passwordBox = document.getElementById("password");
        const lenght = 11;

        const upperCase = "QWERTYUIOPASDFGHJKLZXCVBNM";
        const lowerCase = "mlpoknjiuhbvgytfcxdreszawq";
        const number = "2468013579";
        const symbol = "-#";

        const allChars = upperCase + lowerCase + number + symbol;

        function createPassword(){
            let password = "";
            password += upperCase[Math.floor(Math.random() * upperCase.length)];
            password += lowerCase[Math.floor(Math.random() * lowerCase.length)];
            password += number[Math.floor(Math.random() * number.length)];
            password += symbol[Math.floor(Math.random() * symbol.length)];

            while(lenght > password.length){
                password += allChars[Math.floor(Math.random() * allChars.length)];
            }

            passwordBox.value = password;
        }
    </script>

    <style>
        .header-text-1{
            font-size: 37.5px; margin-left: -120px;
        }
    
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

@media screen and (min-width: 1536px) and (min-height: 730px){
    .header-text-1{
        margin-left: -180px;
    }        
    .header-text-1::after{
                width: 66%;
                margin-left: -180px;
            }
        }

        @media screen and (min-width: 2133px) and (min-height: 1058px){
    footer{
        margin-top: -2%;
    }
}

@media screen and (min-width: 1460px) and (max-width: 1500px) and (min-height: 691px) and (max-height: 730px){
 .header-text-1{
        margin-left: -180px;
    }        
    .header-text-1::after{
                width: 66%;
                margin-left: -180px;
            }
        }
}

    </style>

</html>
