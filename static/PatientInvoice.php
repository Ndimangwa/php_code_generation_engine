<?php
class PatientInvoice
{
    public static function getPDFPrintOut($pdf1, $invoice1, $profile1, $login1, $logofilename = null)
    {
        //Setting Up Header
        PDFLayout::createPageHeader($pdf1, $profile1->getProfileName(), $logofilename);
        PDFLayout::createPageFooter($pdf1, $profile1->getProfileName());
        //Start A New Page
        $pdf1->AddPage();
        //Document Title 
        PDFLayout::writeBlock($pdf1, array(
            "<b>Invoice</b>",
            (PDFLayout::$__NEW_LINE)
        ), 'R');
        //Invoice information
        $city = $profile1->getCity();
        $region = $profile1->getRegion();
        $payload = array(
            "<b>Date: </b>" . ($invoice1->getTimeOfCreation()->getDateAndTimeString()),
            "<b>Invoice Number: </b>" . ($invoice1->getInvoiceNumber()),
            (PDFLayout::$__NEW_LINE),
            "<b>Address: </b>" . ($profile1->getAddress()),
            "<b>City: </b>: $city , <b>Region: </b>$region",
            "<b>Country: </b>" . ($profile1->getCountry()->getCountryName()),
            (PDFLayout::$__NEW_LINE)
        );
        PDFLayout::writeBlock($pdf1, $payload, 'L');

        //Billing Information
        PDFLayout::writeBlock($pdf1, array( 
            "<b>BILL TO:</b>",
            $invoice1->getChargeTo(),
            (PDFLayout::$__NEW_LINE)
        ), 'R');

        //Currency 
        $currency1 = $invoice1->getCurrency();
        $currency = ($currency1->getCurrencyName())." (".($currency1->getCode()).")";
        PDFLayout::writeBlock($pdf1, array(
            "<b>Currency : $currency</b>",
            (PDFLayout::$__NEW_LINE)
        ), 'C');
        //Now Preparing Data
        $payload = array();
        $count = 0;
        foreach ($invoice1->getListOfLogSequence() as $sequence1)  {
            $index = sizeof($payload);
            $payload[$index] = array();
            $payload[$index]['format'] = array('align:0' => 'C', 'align:1' => 'L', 'align:2' => 'R', 'align:3' => 'R', 'align:4' => 'R');
            $payload[$index]['data'] = array($count + 1, $sequence1->getServiceCaption(), $sequence1->getUnitCost(), $sequence1->getQuantity(), $sequence1->getAmount());
            $count++;
        }
        //Subtotal
        /*$index = sizeof($payload);
        $payload[$index] = (PDFLayout::$__NEW_LINE);*/
        $index = sizeof($payload);
        $payload[$index] = array(
            "format" => array('align:0' => 'R', 'align:4' => 'R', 'colspan:0' => 4),
            "data" => array('SUB-TOTAL', '', '', '', ($invoice1->getAmountBeforeDiscount()))
        );
        //Discount
        /*$index = sizeof($payload);
        $payload[$index] = (PDFLayout::$__NEW_LINE);*/
        $index = sizeof($payload);
        $payload[$index] = array(
            "format" => array('align:0' => 'R', 'align:4' => 'R', 'colspan:0' => 4),
            "data" => array('DISCOUNT', '', '', '', ($invoice1->getDiscount()))
        );
        //Total
        /*$index = sizeof($payload);
        $payload[$index] = (PDFLayout::$__NEW_LINE);*/
        $index = sizeof($payload);
        $payload[$index] = array(
            "format" => array('align:0' => 'R', 'align:4' => 'R', 'colspan:0' => 4),
            "data" => array('TOTAL', '', '', '', ($invoice1->getAmount()))
        );
        PDFLayout::writeTable($pdf1, $payload, array('S/N', 'Description', 'Unit Cost', 'Quantity', 'Amount'), array(1, 5, 2, 2, 2));
        //Amount in words 
        $patientName = $invoice1->getPatient()->getPatientName();
        $word_amount = Number::convertToWord($invoice1->getAmount());
        PDFLayout::writeBlock($pdf1, array(
            (PDFLayout::$__NEW_LINE),
            "<i><b>Amount in Words: </b><u>$word_amount</u></i>",
            /*(PDFLayout::$__NEW_LINE),*/
            "(Patient : <i><u>$patientName</u></i>)"
        ), 'C');
        //Invoice full paid, remaining amount 
        PDFLayout::writeBlock($pdf1, array(
            (PDFLayout::$__NEW_LINE),
            "<b>Remaining Amount : </b>".($invoice1->getBalance()),
            "<b>Paid Amount      : </b>".($invoice1->getTotalPaid())
        ), 'R');
        //Prepared By
        PDFLayout::writeBlock($pdf1, array(
            (PDFLayout::$__NEW_LINE),
            "Prepared By: ".($invoice1->getPreparedBy()->getLoginName()),
            (PDFLayout::$__NEW_LINE),
            "Printed By: ".($login1->getLoginName()),
            "Printed Time: ".(DateAndTime::getCurrentDateAndTime($profile1)->getDateAndTimeString())
        ), 'L');
        return $invoice1;
    }
    public function getUnpaidAmount()
    {
        return ($this->amount - $this->getPaidAmount());
    }
    public function getPaidAmount()
    {
        $invoiceId = $this->invoiceId;
        $query = "SELECT SUM(amount) AS totalPaidAmount FROM _patient_receipt WHERE invoiceId = '$invoiceId'";
        $jresults = SQLEngine::rawSelectQueryExecute($query, $this->conn);
        if (is_null($jresults)) throw new Exception("[ Invoice ] : Could not extract amount paid");
        $jArray1 = json_decode($jresults, true);
        if (is_null($jArray1)) throw new Exception("[ Invoice ] : Malformed results");
        $message = $jArray1['message'];
        if ($jArray1['code'] != 0) throw new Exception("[ Invoice ] : $message");
        if ($jArray1['count'] != 1) return 0;
        $row = $jArray1['rows'][0];
        return $row['totalPaidAmount'];
    }
}
