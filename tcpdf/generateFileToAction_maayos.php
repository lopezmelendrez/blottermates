<?php


// Include the main TCPDF library (search for installation path).
require_once('tcpdf.php');
require_once('tcpdf_autoconfig.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PUP Santa Rosa Branch');
$pdf->SetTitle('BLOTTER INCIDENT FORM');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


// set default header data
$pdf->setFooterData(array(0,64,0), array(0,64,128));


// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}


// ---------------------------------------------------------


// set default font subsetting mode
$pdf->setFontSubsetting(true);


// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('times', '', 4, '', true);


// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();


// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
/*
// Data Retrieval
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve form data
  $province = $_POST['province'];
  $city = $_POST['city'];
  $baranggay = $_POST['baranggay'];
  $complainant = $_POST['complainant'];
  $defendant = $_POST['defendant'];
  $caseNo = $_POST['caseNo'];
  $caseType = $_POST['caseType'];
  $month = date('F');
  $date = date('d');
  $year = date('Y');
  $pangkat_secretary = $_POST['pangkat_secretary'];
  $attested = $_POST['attested'];
  $signature_image_path = "../images/sign1.jpg";

  // Connect to the database (Update with your actual database credentials)
 



  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  // Insert data into the table
  $sql = "INSERT INTO blotter_forms (province, city, baranggay, complainant, defendant, caseNo, caseType, month, date, year, pangkat_secretary, attested, signature_image_path)
  VALUES ('$province', '$city', '$baranggay', '$complainant', '$defendant', '$caseNo', '$caseType', '$month', '$date', '$year', '$pangkat_secretary', '$attested', '$signature_image_path')";

  if ($conn->query($sql) === TRUE) {
      // Data inserted successfully
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
} else {
  // Connect to the database (Update with your actual database credentials)
  $servername = "localhost";
  $username = "your_db_username";
  $password = "your_db_password";
  $dbname = "my_database";

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  // Retrieve data from the database
  $sql = "SELECT * FROM blotter_forms ORDER BY id DESC LIMIT 1";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $province = $row['province'];
      $city = $row['city'];
      $baranggay = $row['baranggay'];
      $complainant = $row['complainant'];
      $defendant = $row['defendant'];
      $caseNo = $row['caseNo'];
      $caseType = $row['caseType'];
      $month = $row['month'];
      $date = $row['date'];
      $year = $row['year'];
      $pangkat_secretary = $row['pangkat_secretary'];
      $attested = $row['attested'];
      $signature_image_path = $row['signature_image_path'];
  }

  $conn->close();
}
*/

    // Set default values or handle the case when the form is not submitted
    $province = "Laguna";
    $city = "City of Santa Rosa";
    $baranggay = "Ibaba";
    $complainant = "Complainant 1 <br>Complainant 2";
    $defendant = "Defendant 1<br>Defendant 2";
    $caseNo = "12345";
    $caseType = "Utang";
    $month = date('F');
    $date = date ('d'); 
    $year = date('Y'); 
    $pangkat_secretary = "Jaimie Lopez";
    $attested  = "Mary Anne Matos";
    $pangkat_chairman = "Anna Michaella Mangahis";
    $signature_image_path = "../images/sign1.jpg";
    $logo_image_path1 = "../images/ibaba.jpg";
    $logo_image_path2 = "../images/ibaba.jpg";
                             

// Set some content to print
$html = <<<EOD
<style>
  body {
    
  .header {
    font-size: 100px;
    text-align: center;
    margin-bottom: 10px;
}

.content {
  font-size: 12px;
  line-height: 1.5;
}

.content-header {
  font-size: 14px;
  font-weight: bold;
  margin-top: 10px;
  text-align: center;
}

.content-header1 {
  font-size: 14px;
  margin-top: 10px;
  text-align: right;
}

.section-header {
  font-size: 14px;
  font-weight: bold;
  margin-top: 10px;
}

.section-header1 {
  font-size: 14px;
  font-weight: bold;
  margin-top: 10px;
  text-align: center;
}

.signature-container1 {
  display: flex;
  align-items: center;
}

.signature-container2 {
  display: flex;
  align-items: right;
}

.signature-img {
  width: 100px; /* Adjust the width of the image as needed */
  
}

.logo-container {
  display: flex;
  align-items: left;
  margin-bottom: 10px;
  font-size: 14px;
  font-weight: bold;
  margin-top: 10px;
  text-align: center;
}

.logo-img {
  width: 100px; /* Adjust the width of the logo as needed */
  margin-right: 20px; /* Add some space between the logo and content */
}


</style>

<div class="logo-container">
<img class="logo-img" src="$logo_image_path1" >
<div class="logo-container">
  Republic of the Philippines
  <br>Province of $province
  <br>$city
  <br>Barangay $baranggay
  <br><br><br>OFFICE OF THE LUPONG TAGAPAMAYAPA
</div>
</div>


<div class="content-header1"><br>
<strong>Barangay Case No. </strong> <u>$caseNo</u> <br>
<strong>For:</strong> <u>$caseType</u>
</div>


<div class="content">
<div class="section-header">Complainant/s</div>
<u>$complainant</u>
<br>
<br> ---against---
<br>
<div class="section-header">Defendant/s</div>
<u>$defendant</u>
<br>
<div class="section-header1" >CERTIFICATION TO FILE ACTION</div>
This is to certify that:
<ol>
  <li>There has been a personal confrontation between the parties before the Punong Barangay but mediation failed;</li>
  <li>The Pangkat ng Tagapagkasundo was constituted but the personal confrontation before the Pangkat likewise did not result in a settlement; and</li>
  <li>Therefore, the corresponding complaint for the dispute may now be filed in court/government office.</li>
</ol>
This day <u> $date </u> month of <u>$month</u> year <u>$year</u>.
</div>



<div class="content">
  <div class="section-header">Pangkat Secretary:</div>
  <div class="signature-container">
  <img class="signature-img" src="$signature_image_path">
  <br><u>$pangkat_secretary</u>
</div>
<br>
  <div class="section-header">Attested by:</div>
  <div class="signature-container">
    <img class="signature-img" src="$signature_image_path">
    <br><u>$attested</u>
  </div>
</div>


  </div>
</div>

</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('FiletoAction.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

