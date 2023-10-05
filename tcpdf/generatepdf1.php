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
$pdf->SetHeaderData(PDF_HEADER_LOGO,  PDF_HEADER_LOGO_WIDTH, 'Barangay Ibaba', 'Santa Rosa, Laguna', array(0,64,255), array(0,64,128));
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
$pdf->SetFont('helvetica', '', 4, '', true);


// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();


// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

/*
// Data Retrieval
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data
        $name = $_POST['name'];
        $studentNumber = $_POST['studentNumber'];
        $courseYrSec = $_POST ['courseYrSec'];
        $date = $_POST ['date'];
        $pupBranch = $_POST ['pupBranch'];
        $reason = $_POST ['reason'];


        //$courseCode = $_POST ['courseCode'];
        //$description = $_POST ['description'];
        //$units = $_POST ['units'];
        //$enrollment = isset($_POST['enrollment']) ? $_POST['enrollment'] : array();
        // $enrollment will be an array containing the selected enrollment options  
        //$acadyear = $_POST ['acadyear'];
    } else {
*/
    // Set default values or handle the case when the form is not submitted
        $name = '';
        $studentNumber = '';
        $courseYrSec ='';
        $date ='';
        $pupBranch ='';
        $reason ='';
                                                


// Set some content to print
$html = <<<EOD
<style>
  body {
    font-family: Arial, sans-serif;

  }


  h1 {
    color: #333;
    text-align: center;
  }


  table {
    max-width: 100%;
    margin: 0 auto;
    background-color: #fff;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }


  th {
    padding: 10px;
    border: 1px solid black;
    text-align: center;
    font-size: 10px;
    background-color: #f2f2f2;
    width: 20%;
  }


  td {
    padding: 10px;
    border: 1px solid black;
    text-align: left;
    width: 85%;
  }


  .tdname{
    width: 32%
  }


  .tdname2{
    width: 33%
  }


  .tdname3{
    width: 32%
  }


  .tdname4{
    width: 33%
    font-family: "Arial Unicode MS", "Segoe UI Symbol", Arial, sans-serif;
    font-size: 10px;
  }


  .thname2{
    background-color: #ffffff;
    font-size: 10px;
  }


  .tablehead{
    padding: 1px;
  }


  .steps{
    font-size: 11px;
  }


  .instructions{
    font-size: 15px;
    line-height: -5%;
  }


  .thclass{
    font-size:9px;
    width: 11.7%;
  }


  .thclass1{
    width:52.65%;
    text-align: left;
  }


  .thclass2{
    width: 52.65%;
    text-align: center;
  }
  
  .tdclass{
    width:11.7%
    font-size:9px;
  }


  .p11{
    color: red;
    font-size: 10px;
    text-align: left;
  }


  .p3{
    font-size: 12px;
    text-align: center;
  }
  .p12 {
    font-size: 32px;
    text-align: center;
    margin-top: 0;
    margin-bottom: 0;
  }
  


</style>
<body>

<p class="p12">INCIDENT RECORD FORM</p>
  <table style="margin: 0 auto;">
    <tr>
      <th>CASE ID NUMBER:</th>
      <td class="tdname">$studentNumber</td>
      <th>DATE:</th>
      <td class="tdname2">$date</td>
    </tr>
  </table>

  <p class="p11">[1] COMPLAINANT'S DETAILS</p>
  <table style="margin: 0 auto;">
    <tr>
      <th>FIRST NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>MIDDLE NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>LAST NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>CONTACT NUMBER:</th>
      <td class="tdname">$studentNumber</td>
      <th>ADDRESS:</th>
      <td class="tdname2">$date</td>
    </tr>
  </table>

  <p class="p11">[2] DEFENDANT'S DETAILS</p>
  <table style="margin: 0 auto;">
    <tr>
      <th>FIRST NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>MIDDLE NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>LAST NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>CONTACT NUMBER:</th>
      <td class="tdname">$studentNumber</td>
      <th>ADDRESS:</th>
      <td class="tdname2">$date</td>
    </tr>
  </table>

  <p class="p11">[3] FAMILY DETAILS</p>
  <table style="margin: 0 auto;">
    <tr>
      <th>FIRST NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>MIDDLE NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>LAST NAME:</th>
      <td>$pupBranch</td>
    </tr>
    <tr>
      <th>CONTACT NUMBER:</th>
      <td class="tdname">$studentNumber</td>
      <th>ADDRESS:</th>
      <td class="tdname2">$date</td>
    </tr>
  </table>
  <br><br><br>
  <p class="p11">[4] INCIDENT DETAILS DETAILS</p>
  <table style="margin: 0 auto;">
    <tr>
      <th>DATE OF INCIDENT:</th>
      <td class="tdname">$studentNumber</td>
      <th>LOCATION OF INCIDENT:</th>
      <td class="tdname2">$date</td>
    </tr>
    <tr>
      <th>INCIDENT CASE:</th>
      <td>$pupBranch</td>
    </tr>
  </table>


  <p class="p11"></p> 
  <table>
  <tr>
  <th class="thclass1">STATUS:</th>
  <th class="thclass2">
    ASSIGNED OFFICER:<br><br>
    <span class="assigned-officer">___________________________</span><br>
    <span class="officer-signature">SIGNATURE</span>
  </th>
</tr>
</table>


<p class="p3">This form is confidential and will only be used for blotter record purposes.</p>




</body>




EOD;


// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// ---------------------------------------------------------


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Blotter-Record-Form.pdf', 'I');


//============================================================+
// END OF FILE
//============================================================+


?>

