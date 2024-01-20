<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatinble" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <title>Barangay Blotter Management System</title>
    </head>
    <body>
    <script>
  window.onload = function() {
    if (/Mobi|Android/i.test(navigator.userAgent)) {
      window.location.href = 'error_page.php';
    }
  };
</script>


        <div class="container d-flex justify-content-center align-items-center min-vh-100">

            <div class="row border rounded-4 p-3 bg-white shadow box-area" style="width: 100%;">

                <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box;" style="background: #F9C918; border-radius: 10px;">
                    <div class="featured-image mb-3" style="margin-top: 20px; text-align: center;">
                        <img src="images/logo.png" class="img-fluid" style="width: 255px;">
                    </div>
                        <p style="font-size: 23px; font-weight: 700; text-align: center; color: #010203; text-transform: uppercase;">Barangay Blotter Management System</p>
                </div>

                <div class="col-md-6 right-box">
                    <span class="header-text" style="font-size: 34px;">Start Your Session As</span>
                    <hr style="border: 1px solid #949494; margin: 20px 0;">
                    <center>
                    <div class="row" style="margin-top: 11%;">
                        <div class="col-md-6">
                            <a href="login/barangay.php" style="text-decoration: none;">
                            <button class="btn btn-danger d-flex flex-column align-items-center barangay-btn" style="height: 150px; width: 240px; font-size: 22px;">
                                <i class="fa-solid fa-users fa-3x mb-2" style="margin-top: 8%;"></i>
                                <hr style="border: 1px solid white; width: 100%; margin: 5px 0;">Barangay
                            </button></a>
                        </div>
                        <div class="col-md-6">
                            <a href="login/dilg.php" style="text-decoration: none;">
                            <button class="btn btn-danger d-flex flex-column align-items-center dilg-btn" style="height: 150px; width: 240px; font-size: 22px;">
                                <i class="fa-solid fa-circle-user fa-3x mb-2" style="margin-top: 8%;"></i>
                                <hr style="border: 1px solid white; width: 100%; margin: 5px 0;">DILG
                            </button></a>
                        </div>
                    </div>
                    </center>
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

    <style>

        @media screen and (min-width: 1920px) and (min-height: 1080px){
            .header-text{
                margin-left: 20px;
            }

            .header-text::after{
                width: 68.5%;
                margin-left: 18px;
            }
        }

        @media screen and (min-width: 1500px) and (max-width: 1536px) and (min-height: 730px){
            .header-text::after{
                width: 67.5%;
                margin-left: 4%;
            }
            .header-text{
                margin-left: 4%;
            }
            .barangay-btn{
                margin-left: 10%;
                margin-top: -5.5%;
            }
            .dilg-btn{
                margin-top: -5.5%;
                margin-left: -18%;
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
    .header-text{
        margin-left: 3%;
    }
    .header-text::after{
        margin-left: 3%;
        width: 79%;
    }
    .barangay-btn{
        margin-left: 6.9%;
    }
}

@media screen and (min-width: 2133px) and (min-height: 1058px){
    .header-text, .header-text::after{
        margin-left: 5%;
    }
    .header-text::after{
        width: 67.9%;
    }
    .barangay-btn{
        margin-left: 19%;
        margin-top: -3%;
    }
    .dilg-btn{
        margin-left: -15%;
        margin-top: -3%;
    }
    footer{
        margin-top: -2%;
    }
}
    </style>

    </style>
    
</html>
