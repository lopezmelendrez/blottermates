<?php

include '../config.php';

$pbId = isset($_GET['pb_id']) ? urldecode($_GET['pb_id']) : null;


// Include the main TCPDF library (search for installation path).
require_once('tcpdf.php');
require_once('tcpdf_autoconfig.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PUP Santa Rosa Branch');
$pdf->SetTitle('KP # 28 :  MONTHLY TRANSMITTAL OF FINAL REPORTS');




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

    $month = date('m');  // Get the current month

    $select = mysqli_query($conn, "
SELECT 
    ir.incident_case_number, 
    ir.complainant_last_name, 
    ir.respondent_last_name, 
    ir.description_of_violation, 
    ir.incident_case_type, 
    ir.incident_date, 
    ir.submitter_first_name, 
    ir.submitter_last_name, 
    ir.created_at, 
    amicable_settlement.date_agreed, 
    amicable_settlement.agreement_description,
    court_action.lupon_signature,
    execution_notice.compliance_status,
    monthly_reports.timestamp AS monthly_report_timestamp
FROM `incident_report` AS ir
INNER JOIN `hearing` AS h ON ir.incident_case_number = h.incident_case_number
LEFT JOIN `amicable_settlement` AS amicable_settlement ON h.hearing_id = amicable_settlement.hearing_id
LEFT JOIN `court_action` AS court_action ON h.hearing_id = court_action.hearing_id
LEFT JOIN `execution_notice` AS execution_notice ON ir.incident_case_number = execution_notice.incident_case_number
LEFT JOIN `monthly_reports` AS monthly_reports ON ir.pb_id = monthly_reports.pb_id
WHERE h.date_of_hearing IS NOT NULL 
    AND h.time_of_hearing IS NOT NULL 
    AND (
        amicable_settlement.agreement_description IS NOT NULL 
        OR court_action.lupon_signature IS NOT NULL
    ) 
    AND ir.pb_id = $pbId
    AND amicable_settlement.date_agreed <= monthly_reports.timestamp  -- Adjust the column names accordingly
ORDER BY ir.created_at DESC
") or die('query failed');
    

$tbodyContent = '';

// Check if there are rows returned from the query
if (mysqli_num_rows($select) > 0) {
    while ($row = mysqli_fetch_assoc($select)) {
        // ... Your existing code for processing each row ...
        $incidentCaseNumber = $row['incident_case_number'];
        $complainant_last_name = $row['complainant_last_name'];
        $respondent_last_name = $row['respondent_last_name'];

        $tbodyContent .= <<<EOD
            <tr>
                <td style="font-size: 12px; text-align: center; border: 1.5px solid black;">$incidentCaseNumber</td>
                <td style="font-size: 12px; text-align: center; border: 1.5px solid black;">$complainant_last_name vs. $respondent_last_name</td>
            </tr>
        EOD;
    }
} else {
    // No settled incident cases, display a message
    $tbodyContent .= <<<EOD
            <tr>
                <td colspan="2" style="font-size: 12px; text-align: center; border: 1.5px solid black;">NO SETTLED INCIDENT CASES</td>
            </tr>
        EOD;
}

    $date = date('F j, Y');

    $selectTimestamp = mysqli_query($conn, "
    SELECT timestamp
    FROM `monthly_reports`
    WHERE pb_id = $pbId
") or die('query failed');

// Check if there are rows returned from the query
if ($rowTimestamp = mysqli_fetch_assoc($selectTimestamp)) {
    $timestamp = strtotime($rowTimestamp['timestamp']);  // Convert the timestamp to a Unix timestamp
    $formattedDate = date('F j, Y', $timestamp);  // Format the date as desired
} else {
    $formattedDate = '';  // Default value if timestamp is not found
}

    
// Set some content to print
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

table {
    width: 100%; 
}

th {
    border: 2px solid black;
    font-size: 12px;
    font-weight: bold;
    padding: 8px;
    text-align: center;
}
</style>

<div class="header">
    Republic of the Philippines
    <br>Province of 
    <br>CITY/MUNICIPALITY OF
    <br>Barangay 
    <br>OFFICE OF LUPONG TAGAPAMAYAPA
    <br><br>OFFICE OF THE BARANGAY CAPTAIN
</div>

<div class="content-one">
<p><u>$formattedDate</u></p>
<p>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
</div>

  <div class="content" style="text-align: center; font-weight: bold;">
  <br>MONTHLY TRANSMITTAL OF FINAL REPORTS
</div>

  <div class="content-complainants">
    <br> To : City/Municipality Judge
    <p>___________________________</p>
    <p>City/Municipality Judge</p>
  </div>

<br><br><br>


<div class="content" style="text-align: justify">
<br>Enclosed herewith are the final reports of settlement of disputes and
arbitration awards made by the Barangay Captain/ Pangkat
Tagapagkasundo in the following cases: </br>
</div>
<br><br>

<table>
        <thead>
            <tr>
                <th>Barangay Case No.</th>
                <th>TITLE</th>
            </tr>
        </thead>
        <tbody>
        $tbodyContent
        </tbody>
</table>


<div class="content-one">
<p>_______________________</p>
<p>(Clerk of Court)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
</div>

<div class="content" style="text-align: justify">
<br>IMPORTANT: Lupon/Pangkat Secretary shall transmit not later than
the first five days of each month the final reports for preceding month. </br>
</div>



</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('KP#28.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>