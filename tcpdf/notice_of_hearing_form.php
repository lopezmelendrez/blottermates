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
$pdf->SetTitle('KP # 8 : NOTICE OF HEARING');



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

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['incident_case_number'])) {
        $incident_case_number = $_GET['incident_case_number'];
      } else {
        header("Location: http://localhost/blottermates-master/barangay/lupon/home.php");
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
  </div>
}

<div class="header" style="font-weight: bold;">  
  <br>NOTICE OF HEARING
  <br>(MEDIATION PROCEEDINGS)
</div>


<div class="content-complainants">  
  <br>TO: <u>{$complainant_last_name}, {$complainant_first_name} {$complainant_middle_name}.</u>
  <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Complainant
</div>



<br><br><br><br><br><br><br><br>
<div class="content">
You are required to appear before me on
  <u>{$month_name} {$day}, {$year}</u> 
  at <u>{$time}</u> for the hearing of your complaint.


This _________ day of __________, 19___.


<br><img class="signature-img" src="$signature_image_path">
<br><br><br><u>Kgg. RELLY M. MEDINA</u>
<br>Punong Barangay/Lupon Chairman


<br><br>Notified this _________ day of ________, 19____.

<br><br><br>Complainant/s
<br>_________________
<br>_________________

</div>


</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('KP#8.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

