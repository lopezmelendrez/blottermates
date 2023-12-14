<?php

include '../config.php';

require_once('tcpdf.php');
require_once('tcpdf_autoconfig.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PUP Santa Rosa Branch');
$pdf->SetTitle('KP # 27 :  NOTICE OF EXECUTION');
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
  <br>NOTICE OF EXECUTION
</div>



<div class="content" style="text-align: justify">

<br>WHEREAS, on <u>$formatted_date_agreed</u>, an amicable settlement was signed by
the parties in the above-entitled case [or an arbitration award was rendered by
the Punong Barangay/Pangkat ng Tagapagkasundo];
WHEREAS, the terms and conditions of the settlement, the dispositive portion
of the award. read:
<br><u>$agreement_description</u>
_______________________________________________________________________________________________________________________________________________________________________________________________________________________________________

<br>The said settlement/award is now final and executory;

<br>WHEREAS, the party obliged ________________ (name) has not complied
voluntarily with the aforestated amicable settlement/arbitration award, within
the period of five (5) days from the date of hearing on the motion for
execution;
<br>NOW, THEREFORE, in behalf of the Lupong Tagapamayapa and by virtue of
the powers vested in me and the Lupon by the Katarungang Pambarangay Law
and Rules, I shall cause to be realized from the goods and personal property of
__________________ (name of party obliged) the sum of
_________________ (state amount of settlement or award) upon in the said
amicable settlement [or adjudged in the said arbitration award], unless
voluntary compliance of said settlement or award shall have been made upon
receipt hereof.
<br><br>Signed this _________ day of ___________, 19____.
<br><br>___________________
<br>Punong Barangay

<br><br>Copy furnished:

<br>______________________&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
______________________
<br>______________________&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
______________________
<br>Complainant/s &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

Respondent/s
</div>

 
</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('KP#27.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

