<?php

include '../config.php';

$incident_case_number = $_GET['incident_case_number'];

if (!isset($incident_case_number) || empty($incident_case_number)) {
    header('Location: error_page.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $incident_case_number = isset($_POST['incident_case_number']) ? $_POST['incident_case_number'] : '';

    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];

    $allowed_extensions = array('jpg', 'jpeg', 'png');
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        die('Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
    }

    $upload_directory = "images/";
    $destination_path = $upload_directory . $file_name;

    if (!move_uploaded_file($file_tmp, $destination_path)) {
        die('Failed to move the uploaded file.');
    }

    // OCR processing
    $tesseract_path = '"C:\\Program Files\\Tesseract-OCR\\tesseract"';
    $image_path = '"' . $destination_path . '"';
    $output_path = '"' . $upload_directory . 'out"';

    $command = $tesseract_path . ' ' . $image_path . ' ' . $output_path;
    shell_exec($command);

    $output_file = $upload_directory . 'out.txt';
    $ocr_results = file_get_contents($output_file);


    $query = "SELECT complainant_first_name, complainant_last_name FROM incident_report WHERE incident_case_number = '$incident_case_number'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $first_name = $row['complainant_first_name'];
            $last_name = $row['complainant_last_name'];

            if (stripos($ocr_results, $first_name) !== false && stripos($ocr_results, $last_name) !== false) {
                echo "<p>OCR results contain the names: $first_name $last_name</p>";

                $redirect_url = "track_case_status.php?incident_case_number=" . urlencode($incident_case_number);
    header("Location: $redirect_url");
    exit(); 
            } else {
                $msg_error = "These credentials do not match our records.";
            }
        } else {
            echo "<p>No records found for the incident_case_number: $incident_case_number</p>";
        }

        mysqli_free_result($result);
    } else {
        echo "<p>Error executing query: " . mysqli_error($conn) . "</p>";
    }

    echo "</pre>";
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
    <title>Track Your Case</title>
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
                            <span class="header-text-1" style="font-size: 36px; margin-left: -30px;">Track Incident Case Status</span>
                            <hr style="border: 1px solid #949494; margin: 20px 0;">

                               
    <p style="font-size: 20px; margin-top: 8%; text-align: center; font-weight: 500;">VERIFY YOUR IDENTITY</p>
    <p style="font-size: 15px; margin-top: -2%; text-align: justify; font-weight: 400; padding: 0px 23px;">Prior to accessing the status of your incident case, kindly upload a valid identification to confirm your identity.</p>
    
    <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="image" id="imageInput" style="border: 1px solid #ccc;" accept="image/*" required>
    <input type="hidden" name="incident_case_number" value="<?php echo htmlspecialchars($incident_case_number); ?>">
    <input class="submit" type="submit" value="Authenticate">
    </form>

    <?php if (isset($msg_error) && !empty($msg_error)) { ?>
        <div class="message d-flex" style="background: #F5E2D1; border: none; border-radius: 5px; width: 91%; margin-top: -1%; padding: 2px 2px; margin-left: 0;">
            <i class="fa-solid fa-circle-exclamation" style="margin-left: 3%; margin-top: 0.6%; font-size: 20px; color: #D52826;"></i>
            <span style="margin-left: 2%; font-size: 14px; color: #D52826; font-weight: 600; margin-top: 0.3%;"><?php echo $msg_error; ?></span>
            <i class="fas fa-times" style="margin-left: 18%; margin-top: 0.4%; color: #D52826; font-size: 24px;" onclick="this.parentElement.remove();"></i>
        </div>
    <?php } ?>

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

document.getElementById("uploadForm").addEventListener("submit", function (event) {
        var fileInput = document.getElementById("imageInput");

        // Check if a file is chosen
        if (fileInput.files.length === 0) {
            alert("Please choose a file before submitting.");
            event.preventDefault(); // Prevent form submission
        }
    });

    </script>

    <style>
    
    .header-text-1::after{
    content: "";
    display: block;
    border-bottom: 3px solid #F9C918; /* Adjust the color and thickness as needed */
    width: 93%;
    border-radius: 25px;
    margin-left: -6%;
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

.submit{
    background-color: #e83422;
    color: #fff;
    border-radius: 5px;
    border: none;
    padding: 5px 10px;
}

.submit:hover{
    background-color: #bc1823;
}

input[type="file"]::file-selector-button {
  width: 136px;
  color: #bc1823;
  background-color: white;
  font-weight: 500;
  border: 1px solid #e83422;
  padding: 5px 10px;
}

input[type="file"]::file-selector-button:hover{
    border: 2px solid #bc1823;
    font-weight: 600;
    cursor: pointer;
}

    </style>

</html>

