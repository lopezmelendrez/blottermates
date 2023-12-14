<?php

include '../config.php';

require_once('tcpdf.php');
require_once('tcpdf_autoconfig.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PUP Santa Rosa Branch');
$pdf->SetTitle('KP # 25 :  MOTION FOR EXECUTION');
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['incident_case_number'])) {
  $incident_case_number = $_GET['incident_case_number'];
} else {
  header("Location: ../lupon/home.php");
  exit;
}

$select_query = "SELECT * FROM `incident_report` WHERE `incident_case_number` = '$incident_case_number'";
$select_incident = mysqli_query($conn, $select_query) or die('Incident query failed');
$incident_data = mysqli_fetch_assoc($select_incident);

if (!$incident_data) {
  die("Incident data not found");
}

$amicable_query = "SELECT * FROM `amicable_settlement` WHERE `incident_case_number` = '$incident_case_number'";
$select_amicable = mysqli_query($conn, $amicable_query) or die('Amicable settlement query failed');
$amicable_data = mysqli_fetch_assoc($select_amicable);

if (!$amicable_data) {
  die("Amicable settlement data not found");
}

$complainant_last_name = $incident_data['complainant_last_name'];
$complainant_first_name = $incident_data['complainant_first_name'];
$complainant_middle_name = $incident_data['complainant_middle_name'];
$respondent_last_name = $incident_data['respondent_last_name'];
$respondent_first_name = $incident_data['respondent_first_name'];
$respondent_middle_name = $incident_data['respondent_middle_name'];
$caseNo = $incident_data['incident_case_number'];
$caseType = $incident_data['incident_case_type'];

$date_agreed = $amicable_data['date_agreed'];
$formatted_date_agreed = date('jS \of F Y', strtotime($date_agreed));
$dateAgreedObj = new DateTime($date_agreed);

function formatDay($day) {
$suffix = '';
if ($day > 10 && $day < 20) {
    $suffix = 'th';
} else {
    switch ($day % 10) {
        case 1:
            $suffix = 'st';
            break;
        case 2:
            $suffix = 'nd';
            break;
        case 3:
            $suffix = 'rd';
            break;
        default:
            $suffix = 'th';
            break;
    }
}
return $day . $suffix;
}

$day = formatDay($dateAgreedObj->format('d'));
$month = $dateAgreedObj->format('F');
$year = $dateAgreedObj->format('Y'); 
$agreement_description = $amicable_data['agreement_description'];

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

</style>

<div class="header">
    Republic of the Philippines
    <br>Province of 
    <br>CITY/MUNICIPALITY OF
    <br>Barangay 
    <br>OFFICE OF LUPONG TAGAPAMAYAPA
</div>

<div class="content-one">
<p>Barangay Case No. <u>$caseNo</u></p>
<p>For : <u>$caseType</u></p>
</div>

<div class="content-complainants">
    <u>$complainant_last_name, $complainant_first_name $complainant_middle_name</u>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Complainant</p>
    <p><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--against--</p>
    <u>$respondent_last_name, $respondent_first_name $respondent_middle_name</u>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Respondent</p>
  </div>

<br><br><br>
<div class="content" style="text-align: center; font-weight: bold;">
  <br>MOTION FOR EXECUTION
</div>



<div class="content" style="text-align: left">
<br>Complainant/s/Respondent/s state as follows:
<br>1. On <u>$formatted_date_agreed</u>, the parties in this case signed an amicable
<br>settlement/received the arbitration award rendered by the Lupon/Chairman/
<br>Pangkat ng Tagapagkasundo;
<br>2. The period of ten (10) days from the above-stated date has expired without
any of the parties filing a sworn statement of repudiation of the settlement
before the Lupon Chairman a petition for nullification of the arbitration award
in court; and
<br>3. The amicable settlement/arbitration award is now final and executory.
WHEREFORE, Complainant/s/Respondent/s request that the corresponding
writ of execution be issued by the Lupon Chairman in this case.
<br><br>_______________
<br>(Date)
<br><br>_______________________
<br>Complainant/s/Respondent/s
</div>  
</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('KP#25.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

