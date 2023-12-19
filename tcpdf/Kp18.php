<?php

include '../config.php';

require_once('tcpdf.php');
require_once('tcpdf_autoconfig.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PUP Santa Rosa Branch');
$pdf->SetTitle('KP # 18 : NOTICE OF HEARING FOR COMPLAINANT');
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
if ($incident_data['incident_case_type'] === "Other") {
  $caseType = $incident_data['other_incident_case_type'];
}
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

// New code to fetch hearing data of the from hearing table
$hearing_query = "SELECT * FROM `hearing` WHERE `incident_case_number` = '$incident_case_number'";
$select_hearing = mysqli_query($conn, $hearing_query) or die('Signature query failed');
$hearing_data = mysqli_fetch_assoc($select_hearing);

if (!$hearing_data) {
  die("Signature data not found");
}


$date_of_hearing = $hearing_data["date_of_hearing"];
$time_of_hearing = $hearing_data["time_of_hearing"];
$hearing_type_status = $hearing_data["hearing_type_status"];
$timestamp = $hearing_data["timestamp"];
$schedule_change_timestamp = $hearing_data["schedule_change_timestamp"];
$conciliation_timestamp = $hearing_data["conciliation_timestamp"];
$arbitration_timestamp = $hearing_data["arbitration_timestamp"];

$newdateObj = new DateTime($date_of_hearing);
    
// Extract day, month, and year
$day = $dateObj->format('d');
$month = $dateObj->format('F');
$year = $dateObj->format('Y');

$formattedDate = "Made this $day day of $month, $year";



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
  <br>NOTICE OF HEARING
  <br>(RE: FAILURE TO APPEAR)
</div>

<div class="content-complainants">  
  <br>TO:<u>$complainant_last_name, $complainant_first_name $complainant_middle_name</u>
  <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Complainant
</div>
  
<div class="content" style="text-align: justify;">
<br>You are hereby required to appear before me/the Pangkat on the ___ day
of ____, ____, at ________ o’clock in the morning/afternoon to
explain why you failed to appear for mediation/conciliation scheduled on
____ day of ______, ______ and why your complaint should not be dismissed, a
certificate to bar the filing of your action on court/government office should
not be issued, and contempt proceedings should not be initiated in court for
willful failure or refusal to appear before the Punong Barangay/Pangkat ng
Tagapagkasundo.

</div>

<div class="content" style="text-align: left">
<br>This ________ day of ___________, 19____.
<br><br><u>$barangay_captain</u>
<br>Punong Barangay/Pangkat Chairman
<br>(Cross out whichever is not applicable)
<br><br>Notified this _________ day of ________, 19_____.
<br><br>Complainant
<br><u>$complainant_last_name, $complainant_first_name $complainant_middle_name</u>
<br><br>Respondent
<br><u>$respondent_last_name, $respondent_first_name $respondent_middle_name.</u>

</div>

</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('KP#18.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

