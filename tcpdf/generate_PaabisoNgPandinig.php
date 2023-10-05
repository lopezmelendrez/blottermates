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



// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE , PDF_HEADER_STRING, array(255,255,255), array(255,255,255));
$pdf->setFooterData(array(255,255,255), array(255,255,255));



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
    
    $complainant = "Nagrereklamo 1<br>Nagrereklamo 2";
    $defendant = "Inerereklamo 1<br>Inerereklamo 2";
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
    $petsa = date('m-d-Y');
    $araw = date('l');
    $space = "  ";
    $time = "12:00 PM";

    
switch ($araw) {
    case 'Sunday':
        $araw = 'Linggo';
        break;
    case 'Monday':
        $araw = 'Lunes';
        break;
    case 'Tuesday':
        $araw = 'Martes';
        break;
    case 'Wednesday':
        $araw = 'Miyerkules';
        break;
    case 'Thursday':
        $araw = 'Huwebes';
        break;
    case 'Friday':
        $araw = 'Biyernes';
        break;
    case 'Saturday':
        $araw = 'Sabado';
        break;
    default:
        $araw = 'Hindi matukoy';
        break;
}
                             
switch ($month) {
    case 'January':
        $month = 'Enero';
        break;
    case 'February':
        $month = 'Pebrero';
        break;
    case 'March':
        $month = 'Marso';
        break;
    case 'April':
        $month = 'Abril';
        break;
    case 'May':
        $month = 'Mayo';
        break;
    case 'June':
        $month = 'Hunyo';
        break;
    case 'July':
        $month = 'Hulyo';
        break;
    case 'August':
        $month = 'Agosto';
        break;
    case 'September':
        $month = 'Setyembre';
        break;
    case 'October':
        $month = 'Oktubre';
        break;
    case 'November':
        $month = 'Nobyembre';
        break;
    case 'December':
        $month = 'Disyembre';
        break;
    default:
        $month = 'Hindi matukoy';
        break;
}


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

.content1 {
    font-size: 12px;
    line-height: 1.5;
    font-weight: bold;
  }

.date {
    font-size: 12px;
    margin-top: 10px;
    text-align: right;
  }
.content-header {
  font-size: 12px;
  font-weight: bold;
  margin-top: 10px;
  text-align: center;
}

.content-KP{
  font-size: 12px;
  font-weight: bold;
  margin-top: 10px;
  text-align: left;
}

.content-header1 {
  font-size: 12px;
  margin-top: 10px;
  text-align: right;
}

.content2 {
    font-size: 12px;
    margin-top: 10px;
    text-align: center;
  }

  .content3 {
    font-size: 12px;
    margin-top: 10px;
    text-align: right;
  }

.section-header {
  font-size: 12px;
  font-weight: bold;
  margin-top: 10px;
  text-align : right;
}

.section-header1 {
  font-size: 12px;
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
  width: 100px; 
  align-item : right;


  .logo-container {
    display: flex;
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: bold;
    margin-top: 10px;
    text-align: center;
  }
  
  .logo-img {
    width: 50px; 
    margin-right: 20px; 
    text-align: center;
  }
  
  
  </style>

  <div class="content-KP">
  <br> KP Polmularyo Blg. 8
</div>
  
  <div class="content-header">
  <img class="logo-img" src="$logo_image_path1" >
   </div>
  

  <div class="content-header">
    Republika ng Pilipinas
    <br>Lalawigan ng Laguna
    <br>Lungsod ng Santa Rosa
    <br>Barangay Ibaba
    <br><br><br>TANGGAPAN NG PUNONG BARANGGAY
  </div>
  </div>
}


<div class="content-header" style="color: red;">
  <br>#############################################################################################
</div>

<div class="content-header">  
  TANGGAPAN NG LUPONG TAGAPAGPAMAYAPA
</div>


<div class="date">
<u>$petsa</u>
<div class="section-header">Petsa $space</div>
</div>


<div class="content1"> Para kay/kina: </div>
<div class="content"> <u>$complainant</u> </div>
<div class="content"> (Mga) Nagrerelamo</div>

<br><br><br><br><br><br><br><br>
<div class="section-header1" >
  Ikaw ay hinihilingang humarap sa akin sa ika- <u> $date </u> ng <u> $month </u> araw ng  <u> $araw </u>,<u>$year</u>, sa ganap 
  <br>na ika - <u> $time </u> para sa pandinig ng iyong reklamo.
<br>


<div class="content-header" >
Ngayong ika- <u> $date </u> ng <u> $month </u> araw ng  <u> $araw </u>,<u>$year</u>.
</div>



  <br><br>
  <div class="section-header">      
  <br><img class="signature-img" src="$signature_image_path">
   <br>    Kgg. RELLY M. MEDINA      
  <br>Punong Barangay/TagaPangulo ng Lupon</div>
  

  <div class="content-header" ><br><br><br><br>
  Ipinagbigay alam ngayong  ika- <u> $date </u> ng <u> $month </u> araw ng  <u> $araw </u>,<u>$year</u>.
  </div>

<br><br>
<div class="content3"> (Mga) Nagrerelamo : <br><u>$complainant</u>
    
  </div>
</div>

</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('AmicableSettlement.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

