<?php
session_start();

include '../config.php'; 

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email_address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    
    $query = "SELECT * FROM account WHERE email_address = '$email' AND account_role = 'DILG'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        
        
        if(password_verify($password, $row['password'])){
            $_SESSION['account_id'] = $row['account_id']; 
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['account_role'] = $row['account_role']; 
            
            
            header('location: ../dilg/home.php');
            exit();
        } else {
            echo 'Invalid password. Please try again.';
        }
    } else {
        echo 'Invalid email address or account role. Please try again.';
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
