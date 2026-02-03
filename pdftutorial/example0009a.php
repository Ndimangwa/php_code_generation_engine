<?php
require "../html/vendor/autoload.php";
require_once("../html/sys/__autoload__.php");
//============================================================+
// File name   : example_009.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 009 for TCPDF class
//               Test Image
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Test Image
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 009');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 009', PDF_HEADER_STRING);

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

// -------------------------------------------------------------------

// add a page
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);
// set JPEG quality
$pdf->setJPEGQuality(75);

// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Image example with resizing
//$pdf->Image('images/image_demo.jpg', 15, 140, 75, 113, 'JPG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, false, false, false);

$width = $pdf->getPageWidth();
$height = $pdf->getPageHeight();
$img_file = "images/image_demo.jpg";

//$pdf->Image('images/image_demo.jpg', 0, 0, $width, $height, 'JPG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, true, false, true);

// The following working, just saved
//$pdf->Image($img_file, 0, 0, $width, $height, '', '', '', false, 300, '', false, false, 0);
$pdf->Image($img_file, 0, 0, $width, $height, '', '', '', false, 300, '', false, false, 0);
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//try to add text on top of image
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->SetTextColor(255, 255, 255);
//Begin -- Adding Text
$html .= <<<EOD
<div>[{ width, height } = { $width , $height }]This is a great news <a href="google.com" class="btn btn-danger" style="cursor: pointer; font-size: 1.2em; text-decoration: none;">Click Here to Proceed Now 0001</a></div>
EOD;
//$pdf->writeHTML($html);
$pdf->writeHTMLCell($width / 3, $height / 3, $width / 3, $height / 3, $html, 0, 1, 0, true, '', true);
//End -- Adding Text
// test fitbox with all alignment combinations




//Close and output PDF document
$pdf->Output('example_009.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>