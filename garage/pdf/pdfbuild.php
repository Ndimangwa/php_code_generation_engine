<?php 
require "../../html/vendor/autoload.php";
require_once("../../html/sys/__autoload__.php");

//$pdf = new CustomPdfGenerator(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf1 = PDFEngine::getADefaultEngine();
//Setting Up Header
PDFLayout::createPageHeader($pdf1, "My Header Text", "mbwambo.png");
PDFLayout::createPageFooter($pdf1, "My Default Footer");
//Start A New Page
$pdf1->AddPage();


//date and invoice Number 
PDFLayout::writeBlock($pdf1, array(
    (PDFLayout::$__NEW_LINE),
    "<b>DATE:</b>25/12/2021",
    "<b>INVOICE:</b>#25",
    (PDFLayout::$__NEW_LINE),
    "98 Norton Street",
    "Soweto Avenue",
    "No 05, Shivajnagar",
    (PDFLayout::$__NEW_LINE)
), 'L');

//Bill To 
PDFLayout::writeBlock($pdf1, array(
    "<b>BILL TO :</b>",
    "22 North Molle Boulevard",
    "DYY001, 4564",
    (PDFLayout::$__NEW_LINE)
), 'R');

//Invoice Table starts here 
/*$header = array('DESCRIPTION', 'UNITS', 'RATE $', 'AMOUNT');
$data = array(
    array('Item #1', '1', '100', '100'),
    array('Item #2', '2', '200', '400')
);
$pdf1->printTable($header, $data);*/
PDFLayout::writeTable($pdf1, array(
        array('format' => array(), 'data' => array(1, 'Pencil', 10, 100, 1000)),
        array('format' => array('align:2' => 'C'), 'data' => array(2, 'Kalamu za njano', 20, 200, 4000)),
        array('format' => array('align:3' => 'R'), 'data' => array(3, 'Kamba za blue', 30, 300, 9000)),
        PDFLayout::$__NEW_LINE,
        array('format' => array('number_format:4'), 'data' => array('', 'SUB-TOTAL', '', '', 13000)),
        PDFLayout::$__NEW_LINE,
        array('format' => array('align:4' => 'R', 'colspan:1' => 3), 'data' => array('', 'TOTAL', '','', '270,000'))
), array(
    "S/N",
    "Description",
    "Units",
    "Rates $",
    "Amount"
));
$pdf1->Ln();

// comments
$pdf1->SetFont('', '', 12);
PDFLayout::writeBlock($pdf1, array(
    "<b>OTHER COMMENTS:</b>",
    "Method of payment: <i>PAYPAL</i>",
    "PayPal ID: <i>ndimangwa@gmail.com</i>",
    (PDFLayout::$__NEW_LINE),
    (PDFLayout::$__NEW_LINE),
    (PDFLayout::$__NEW_LINE)
), 'L');
PDFLayout::writeBlock($pdf1, array(
    "If you have any questions about this invoice, please contact:",
    "Ndimangwa Fadhili Ngoya, (07) 4050 2235, ndimangwa@gmail.com"
), 'C');

//Output
//$pdf1->Output(__DIR__ . '/invoice_12.pdf');
$pdf1->print();
