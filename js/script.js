const passwordInput = document.getElementById("password");
const showHideButton = document.getElementById("showHidePw");

showHideButton.addEventListener("click", function () {
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        showHideButton.classList.remove("fa-lock");
        showHideButton.classList.add("fa-unlock");
    } else {
        passwordInput.type = "password";
        showHideButton.classList.remove("fa-unlock");
        showHideButton.classList.add("fa-lock");
    }
});

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