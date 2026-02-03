<?php
class PatientReceipt
{
    public static function getPDFPrintOut($pdf1, $receipt1, $profile1, $login1, $logofilename = null)
    {
        //Setting Up Header
        PDFLayout::createPageHeader($pdf1, $profile1->getProfileName(), $logofilename);
        PDFLayout::createPageFooter($pdf1, $profile1->getProfileName());
        //Start A New Page
        $pdf1->AddPage();
        //Document Title 
        PDFLayout::writeBlock($pdf1, array(
            "<b>Receipt</b>",
            (PDFLayout::$__NEW_LINE)
        ), 'R');
        //Invoice information
        $city = $profile1->getCity();
        $region = $profile1->getRegion();
        $payload = array(
            "<b>Date: </b>" . ($receipt1->getTimeOfCreation()->getDateAndTimeString()),
            "<b>Receipt Number: </b>" . ($receipt1->getReceiptNumber()),
            (PDFLayout::$__NEW_LINE),
            "<b>Address: </b>" . ($profile1->getAddress()),
            "<b>City: </b>: $city , <b>Region: </b>$region",
            "<b>Country: </b>" . ($profile1->getCountry()->getCountryName()),
            (PDFLayout::$__NEW_LINE)
        );
        PDFLayout::writeBlock($pdf1, $payload, 'L');
        
        //Payment Information
        $amount = $receipt1->getAmount();
        $amountInWords = Number::convertToWord($amount);
        $invoice1 = $receipt1->getInvoice();
        $currency1 = $invoice1->getCurrency();
        $currencyName = $currency1->getCurrencyName();
        $currencyCode = $currency1->getCode();
        $patientName = $invoice1->getPatient()->getPatientName();
        $payerName = $receipt1->getPayerName();
        
        PDFLayout::writeBlock($pdf1, array(
            "<b>Received Amount : </b><i>$amountInWords ($amount), $currencyName ($currencyCode) only</i>",
            (PDFLayout::$__NEW_LINE),
            "<b>Received From : </b><i>$payerName</i>",
            "(Patient: <u><i>$patientName</i></u>)",
            (PDFLayout::$__NEW_LINE)
        ), 'C');

        
        //Invoice full paid, remaining amount 
        PDFLayout::writeBlock($pdf1, array(
            (PDFLayout::$__NEW_LINE),
            "<b>Reference Invoice : </b>".($invoice1->getInvoiceNumber())
        ), 'R');
        //Prepared By
        PDFLayout::writeBlock($pdf1, array(
            (PDFLayout::$__NEW_LINE),
            "Prepared By: ".($receipt1->getPreparedBy()->getLoginName()),
            (PDFLayout::$__NEW_LINE),
            "Printed By: ".($login1->getLoginName()),
            "Printed Time: ".(DateAndTime::getCurrentDateAndTime($profile1)->getDateAndTimeString())
        ), 'L');
        return $receipt1;
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
