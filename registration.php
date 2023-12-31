<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {

    // Sanitize and validate user input
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $email_address = mysqli_real_escape_string($conn, $_POST["email_address"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $account_role = mysqli_real_escape_string($conn, $_POST["account_role"]);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the 'account' table
    $sql = "INSERT INTO account (first_name, last_name, email_address, password, account_role) VALUES ('$first_name', '$last_name', '$email_address', '$hashed_password', '$account_role')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
        <link rel="stylesheet" href="css/registration.css">
        <link rel="icon" type="image/x-icon" href="images/favicon.ico">
        <title>Account Registration</title>
    </head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="row border p-3 bg-white shadow box-area">

            <div class="col-md-6 justify-content-center align-items-center flex-column left-box" style="background: #F9C918; border-radius: 10px; height: 525px;"> 
                <div class="featured-image mb-4" style="margin-left: 14%; margin-top: 65px;">
                    <img src="images/logo.png" class="img-fluid" style="width: 350px;">
                </div>
                <p class="text-center fs-5" style="font-weight: 600;margin-top: 25px;">Barangay Justice Management System</p>
            </div>

            <div class="col-md-6 right-box">
                <div class="forms">
                    <div class="form-box">
                        <span class="fs-4 header-text">Account Registration</span>
                                                <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 100%; margin-top: 2%; padding: 3px 3px;">
                                                    <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 1%; font-size: 23px; color: #D52826;"></i>
                                                    <span style="margin-left: 15%; font-size: 20px; color: #D52826; font-weight: 600;">'.$msg_error.'</span>
                                                    <i class="fas fa-times" style="margin-left: 15%; margin-top: 1%; color: #D52826; font-size: 25px;" onclick="this.parentElement.remove();"></i>
                                                </div>
                            

                        <form action="" method="post">

                            <div class="input-field">
                                <input type="text" onkeypress="return validateName(event)" name="first_name" required>
                                <span></span>
                                <label>First Name</label>
                            </div>

                            <div class="input-field mb-1">
                                <input type="text" onkeypress="return validateName(event)" name="last_name" required> 
                                <span></span>
                                <label>Last Name</label>
                            </div>

                            <div class="input-field mb-1">
                                <input type="text" name="email_address" required>
                                <span></span>
                                <label>Email Address</label>
                            </div>

                            <div class="pw-meter">

                                <div class="input-field mb-0">
                                    <input type="password"  name="password" class="password" id="password" onkeydown="return validatePassword(event)" onfocus="showStrengthMeter()" onblur="hideStrengthMeter()" required>
                                    <label>Password</label>
                                    <i class="uil uil-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Password must contain: At least one lowercase letter (a-z), at least one uppercase letter (A-Z), at least one number, and at least one special character." style="right: 0;"></i>
                                    <div class="pw-display-toggle-btn">
                                        <i class="uil uil-eye-slash showHidePw" style="cursor: pointer;"></i>
                                    </div>
                                </div>

                                <div class="pw-strength" id="strength-meter">
                                    <span>Weak</span>
                                    <span></span>
                                </div>

                            </div>   

                            <div class="input-field">
                                <input type="password" onkeydown="return validatePassword(event)" name="confirmPassword"required>
                                <label>Confirm Password</label>
                            </div>

                           <div class="input-group d-flex">
                                <p>Personnel Position: </p>
                                <select name="account_role" required class="box">
                                <option value="Select" disabled selected hidden>Select</option>
                                <option value="DILG">Lupon</option>
                                <option value="Barangay">Brgy. Captain</option>
                            </div>

                            <div class="input-group1">
                                <input type="submit" name="submit" value="Register Account" class="button">
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

function getPasswordStrength(password){
    let s = 0;
        if(password.length > 6){
          s++;
        }

        if(password.length > 10){
          s++;
        }

        if(/[A-Z]/.test(password)){
          s++;
        }

        if(/[0-9]/.test(password)){
          s++;
        }

        if(/[^A-Za-z0-9]/.test(password)){
          s++;
        }

        return s;
}
      
document.querySelector(".pw-meter #password").addEventListener("focus",function(){
    document.querySelector(".pw-meter .pw-strength").style.display = "block";
});

document.querySelector(".pw-meter #password").addEventListener("blur", function() {
    document.querySelector(".pw-meter .pw-strength").style.display = "none";
});
      
document.querySelector(".pw-meter .pw-display-toggle-btn").addEventListener("click",function(){
    let el = document.querySelector(".pw-meter .pw-display-toggle-btn");
        
        if(el.classList.contains("active")){
            document.querySelector(".pw-meter #password").setAttribute("type","password");
            el.classList.remove("active");
        } else{
            document.querySelector(".pw-meter #password").setAttribute("type","text");
            el.classList.add("active");
        }
});
      
    document.querySelector(".pw-meter #password").addEventListener("keyup",function(e){
        let password = e.target.value;
        let strength = getPasswordStrength(password);
        let passwordStrengthSpans = document.querySelectorAll(".pw-meter .pw-strength span");
        strength = Math.max(strength,1);
        passwordStrengthSpans[1].style.width = strength*20 + "%";
        
        if(strength < 2){
          passwordStrengthSpans[0].innerText = "Weak";
          passwordStrengthSpans[0].style.color = "#111";
          passwordStrengthSpans[1].style.background = "#d13636";
        } else if(strength >= 2 && strength <= 4){
          passwordStrengthSpans[0].innerText = "Medium";
          passwordStrengthSpans[0].style.color = "#111";
          passwordStrengthSpans[1].style.background = "#e6da44";
        } else {
          passwordStrengthSpans[0].innerText = "Strong";
          passwordStrengthSpans[0].style.color = "#fff";
          passwordStrengthSpans[1].style.background = "#20a820";
        }
    });



function validateName(event){
    var keyCode = event.keyCode;
        
        if (keyCode === 32) {
            return true;
        }
        
        if (keyCode >= 48 && keyCode <= 57) {
            event.preventDefault();
            return false;
        }
        
        return true;
    }

function validatePassword(event){
    var keyCode = event.keyCode;
        
        if (keyCode === 32) {
            event.preventDefault();
            return false;
        }
        
        return true;
}

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

document.querySelector(".pw-meter #password").addEventListener("focus",function(){
    document.querySelector(".pw-meter .pw-strength").style.display = "block";
});

document.querySelector(".pw-meter #password").addEventListener("blur", function() {
    document.querySelector(".pw-meter .pw-strength").style.display = "none";
});
    </script>
</body>
</html>