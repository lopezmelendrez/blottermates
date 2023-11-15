<?php

include '../config.php';

// Include the main TCPDF library (search for installation path).
require_once('tcpdf.php');
require_once('tcpdf_autoconfig.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PUP Santa Rosa Branch');
$pdf->SetTitle('KP No. 9: Summon for the Respondent');


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

   
    $complainant = "   Complainant    ";
    $defendant = "   Defendant    ";
    $caseNo = "2020-0024";
    $caseType = "Utang";
    $month = date('F');
    $date = date ('d'); 
    $year = date('Y'); 
    $pangkat_secretary = "Jaimie Lopez";
    $attested  = "Mary Anne Matos";
    $pangkat_chairman = "Anna Michaella Mangahis";
    $signature_image_path = "sign1.jpg";
    $logo_image_path1 = "ibaba.jpg";
    $petsa = date('m-d-Y');
    $araw = date('l');
    $space = "  ";
    $spaces = "      ";

    
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['incident_case_number'])) {
  $incident_case_number = $_GET['incident_case_number'];
} else {
  header("Location: ../lupon/home.php");
  exit;
}

$select_incident = mysqli_query($conn, "SELECT * FROM `incident_report` WHERE `incident_case_number` = '$incident_case_number'") or die('query failed');
$incident_data = mysqli_fetch_assoc($select_incident);

// Check if data was fetched successfully
if ($incident_data) {
    $complainant_last_name = $incident_data['complainant_last_name'];
    $complainant_first_name = $incident_data['complainant_first_name'];
    $complainant_middle_name = $incident_data['complainant_middle_name'];
    $respondent_last_name = $incident_data['respondent_last_name'];
    $respondent_first_name = $incident_data['respondent_first_name'];
    $respondent_middle_name = $incident_data['respondent_middle_name'];
    $caseNo = $incident_data['incident_case_number'];
    $caseType = $incident_data['incident_case_type'];
    // Add other fields as needed
} else {
    // Handle the case when incident data is not found, e.g., redirect back to the dashboard or show an error message.
    // Replace the following line with your preferred error handling code.
    die("Incident data not found");
}

$select_hearing = mysqli_query($conn, "SELECT * FROM `hearing` WHERE `incident_case_number` = '$incident_case_number'") or die('query failed');
$hearing_data = mysqli_fetch_assoc($select_hearing);

// Check if data was fetched successfully
if ($hearing_data) {
    $date_of_hearing = $hearing_data['date_of_hearing'];

    $dateTime = new DateTime($date_of_hearing);

    setlocale(LC_ALL, 'fil_PH.utf8');

    $day_of_week = strftime('%A', $dateTime->getTimestamp());
    $month_name = $dateTime->format('F');
    $month_name_tagalog = getTagalogMonthName($month_name);
    
    
    // Split the date using date_parse function
    $parsed_date = date_parse($date_of_hearing);
    
    // Extract day, month, and year
    $day = $parsed_date['day'];
    $month = $parsed_date['month'];
    $year = $parsed_date['year'];
    
    // Convert month number to month name
    $month_name = date('F', mktime(0, 0, 0, $month, 1));
    
    // Add other fields as needed
} else {
    // Handle the case when hearing data is not found, e.g., redirect back to the dashboard or show an error message.
    // Replace the following line with your preferred error handling code.
    die("Hearing data not found");
}

function getTagalogMonthName($month_name) {
  $tagalog_months = array(
      'January'   => 'Enero',
      'February'  => 'Pebrero',
      'March'     => 'Marso',
      'April'     => 'Abril',
      'May'       => 'Mayo',
      'June'      => 'Hunyo',
      'July'      => 'Hulyo',
      'August'    => 'Agosto',
      'September' => 'Setyembre',
      'October'   => 'Oktubre',
      'November'  => 'Nobyembre',
      'December'  => 'Disyembre'
  );
  
  return $tagalog_months[$month_name];
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
  line-height: 1;
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
}

.section-header1 {
  font-size: 12px;
  font-weight: bold;
  margin-top: 10px;
  text-align: center;
  line-height: 2;
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
  <br> KP Polmularyo Blg. 9
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
<div class="section-header">Petsa</div>
</div>

<div class="content-header1"><br>
<strong>Usapin ng Barangay Bldg.</strong> <u>$caseNo</u> <br>
<strong>Para sa:</strong> <u>$caseType</u>
</div>


<div class="content">
  <u>$complainant_last_name, $complainant_first_name $complainant_middle_name</u>
  <div class="section-header">Nagrereklamo</div>
  <br> -Laban kay/kina-
  <br><br>
  <u>$respondent_last_name, $respondent_first_name $respondent_middle_name</u>
  <div class="section-header">Inereklamo</div>


  <div class="section-header1">PATAWAG</div>
  <br> Para kay/kina: <u>$respondent_last_name, $respondent_first_name $respondent_middle_name</u> <br>
                    
<div class="section-header1">Inereklamo</div> 

  <blockquote>Ikaw ay ipinatawg upang humarap sa akin nang personal kasama ang iyong mga testigo, sa ika- <u> $day </u> ng <u> $month_name_tagalog </u> </blockquote>
  <br>araw ng <u> $day_of_week </u>,<u>$year</u>, sa ganap na ika - ___  ng _____ upang sagutin ang reklamong laban sa iyo na idinulog sa 
  <br><br>harap ko; kakabit nito ang kopya, para sa pamamagitan/pag-aayos ng inyong mga sigalot ng 
  <br><br>nagrereklamo/mga nagrerelamo.
  <br><br>
  <blockquote> Binabalaan ka na nag iyong pangtanggi o sadyang hindi pagharap upang sundin ang pagpapatawg na ito ay </blockquote>
  maghahadlang sa iyo na makapaghain ng ganting sumbong na magmumula sa reklamong ito.
  <br><br>
  <blockquote> Kung sakaling mabigo ka, haharap ka sa kaparusahang paglapastangan sa hukuman. 
  <br><br>
</div>







<div class="content2" >
Tinanggap at inihain ngayong ika- <u> $date </u> ng <u> $month </u> araw ng <u> $araw </u>,<u>$year</u>.
</div>

<div class="content3" >
<img class="signature-img" src="$signature_image_path">

  <div class="section-header">      
       Kgg. RELLY M. MEDINA      
  <br>Punong Barangay/TagaPangulo ng Lupon</div>
  <div class="signature-container">
  <br>
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



