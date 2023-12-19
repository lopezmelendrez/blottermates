<?php

include '../config.php';

require_once('tcpdf.php');
require_once('tcpdf_autoconfig.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PUP Santa Rosa Branch');
$pdf->SetTitle('KP # 7 : COMPLAINANTS FORM');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE , PDF_HEADER_STRING, array(255,255,255), array(255,255,255));
$pdf->setFooterData(array(255,255,255), array(255,255,255));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$pdf->setFontSubsetting(true);
$pdf->SetFont('times', '', 4, '', true);
$pdf->AddPage();

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
    $description_of_violation = $incident_data['description_of_violation'];
    $incident_date = $incident_data['incident_date'];

    // Create a DateTime object from the incident date
    $dateObj = new DateTime($incident_date);
    
    // Extract day, month, and year
    $day = $dateObj->format('d');
    $month = $dateObj->format('F');
    $year = $dateObj->format('Y');
    
    $formattedDate = "Made this $day day of $month, $year";

   
    // Add other fields as needed
} else {
    // Handle the case when incident data is not found, e.g., redirect back to the dashboard or show an error message.
    // Replace the following line with your preferred error handling code.
    die("Incident data not found");
}
  
  //New
if ($incident_data['incident_case_type'] === "Other") {
  $caseType = $incident_data['other_incident_case_type'];
}
$pb_id = $incident_data ['pb_id'];
// New code to fetch barangay from pb_account table
$barangay_query = "SELECT * FROM `pb_accounts` WHERE `pb_id` = '$pb_id'";
$select_barangay = mysqli_query($conn, $barangay_query) or die('Barangay query failed');
$barangay_data = mysqli_fetch_assoc($select_barangay);

if (!$barangay_data) {
  die("Barangay data not found for pb_id: $pb_id");
}

$barangay = $barangay_data['barangay'];
$barangay_captain = $barangay_data['barangay_captain'];


// New code to fetch signature of the barangay captain from lupon_accounts table
$captainSign_query = "SELECT * FROM `lupon_accounts` WHERE `pb_id` = '$pb_id'";
$select_captainSign = mysqli_query($conn, $captainSign_query) or die('Signature query failed');
$captain_data = mysqli_fetch_assoc($select_captainSign);

if (!$captain_data) {
  die("Signature data not found for pb_id: $pb_id");
}

$captainSign = $captain_data ['signature_image'];


$html = <<<EOD
<style>
 
.header{
  font-size: 12px;
  margin-top: 10px;
  text-align: center;
}

.content{
  font-size: 12px;
  line-height: 1.5;
}
.content-one{
    font-size: 12px;
    margin-top: 10px;
    text-align: right;
  }
.content-complainants{
  font-size: 12px;
  margin-top: 10px;
  text-align: left;
}
td {
  font-size: 12px;
  text-align: center;
  text-indent: -0.5em;
}

</style>

<body>
<div class="header">
    Republic of the Philippines
    <br>Province of Laguna
    <br>CITY/MUNICIPALITY OF SANTA ROSA
    <br>Barangay $barangay
    <br>OFFICE OF LUPONG TAGAPAMAYAPA
</div>

  <div class="content-one">
    <p>Barangay Case No. <u>$caseNo</u></p>
    <p>For : <u>$caseType</u></p>
  </div>

  <table style="width:30%;">
  <tbody>
      <tr>
          <td>
              <u>$complainant_last_name, $complainant_first_name $complainant_middle_name</u>
              <p>Complainant</p>
              <p><b>--against--</b></p>
              <u>$respondent_last_name, $respondent_first_name $respondent_middle_name</u>
              <p>Respondent</p>
          </td>
      </tr>
  </tbody>
</table>

<br><br><br>
<div class="content" style="text-align: center; font-weight: bold;">
  <br>C O M P L A I N T
</div>

<br>
<div class="content" style="text-align: justify;">
    <br>I/WE hereby complain against above named respondent/s for violating my/our
    rights and interests in the following manner:<u> $description_of_violation </u>

    <br><br>THEREFORE, I/WE pray that the following relief/s be granted to me/us in
    accordance with law and/or equity:

    <br><br>Made this this <u>$day</u> day of <u>$month</u>, <u>$year</u>.
    
    <br><br><u>$complainant_last_name, $complainant_first_name $complainant_middle_name</u>
    <br>Complainant/s
    
    <br><br>Received and filed this <u>$day</u> day of <u>$month</u>, <u>$year</u>.
    
    <br><br><u>$barangay_captain</u>
    <br>Punong Barangay/Lupon Chairman
    

</div>

</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('KP#7.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

