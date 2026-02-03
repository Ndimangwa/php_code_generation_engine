<?php
class PatientAdmissionQueue
{
    public function getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename = null, $showHeaderAndFooter = true, $showCreationAndUpdationTime = true, $addNewPage = true, $settings = null, $shapingFunction = null)
    {
        $settings = is_null($settings) ? array() : $settings;
        //Get Queue
        
        return $pdf1;
    }
    public function _getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename = null, $showHeaderAndFooter = true, $showCreationAndUpdationTime = true, $addNewPage = true, $settings = null, $shapingFunction = null)
    {
        $settings = is_null($settings) ? array() : $settings;
        //Step 1: Get drugQueue Reference
        $patientAdmissionQueue1 = $this;
        $conn = $this->conn;
        //Step 2: Working with Headers and Footers if any
        if ($showHeaderAndFooter) {
            $headerTitle = isset($settings['header-title']) ? $settings['header-title'] : "Default Title";
            $footerTitle = isset($settings['footer-title']) ? $settings['footer-title'] : "Default Title";
            PDFLayout::createPageHeader($pdf1, $headerTitle, $logofilename, $settings);
            PDFLayout::createPageFooter($pdf1, $footerTitle, $settings);
        }
        //Step 3: Adding a New Page if any
        if ($addNewPage) {
            $pdf1->AddPage();
        }
        //Write Invoice Information
        $tableTitle = isset($settings['table-title']) ? $settings['table-title'] : "Admission & Operation";
        PDFLayout::writeBlock($pdf1, array(("<b><h3>" . $tableTitle . "</h3></b>")), 'L', $settings);
        //Pulling Invoice Information
        $listOfInvoices = PatientInvoice::filterRecords($conn, array(
            "temporaryObjectHolder" => ($patientAdmissionQueue1->getObjectReferenceString())
        ));
        //Step 4 -- Temp: Preparing Invoices 
        if (!is_null($listOfInvoices)) {
            $headerArray1 = array("S/N", "Currency", "Invoice Number", "Invoiced Amount", "Total Paid");
            $generalFormat = array("align:0" => "R", "align:1" => "L", "align:2" => "L", "align:3" => "R", "align:4" => "R");
            $widthRatio = array(1, 2, 4, 4, 4);
            PDFLayout::writeBlock($pdf1, array(("<b><h3>Invoices Summary</h3></b>")), 'L', $settings);
            $dataArray1 = array();
            foreach ($listOfInvoices as $index => $invoice1) {
                $sn = $index + 1;
                $code = $invoice1->getCurrency()->getCode();
                $amount = number_format($invoice1->getAmount(), 2);
                $invoiceNumber = $invoice1->getInvoiceNumber();
                $paid = number_format($invoice1->getTotalPaid(), 2);
                $dataArray1[sizeof($dataArray1)] = array(
                    'data' => array($sn, $code, $invoiceNumber, $amount, $paid),
                    'format' => $generalFormat
                );
            }
            PDFLayout::writeTable($pdf1, $dataArray1, $headerArray1, $widthRatio);
        }

        //Step 4: Preparing General Items like 
        $headerArray1 = array("S/N", "Drug Name", "Quantity", "Usage", "Status");
        $generalFormat = array("align:0" => "R", "align:1" => "L", "align:2" => "R", "align:3" => "L", "align:4" => "L");
        $widthRatio = array(1, 4, 2, 3, 2);
        //$dataArray1 = array();

        //PDFLayout::writeTable($pdf1, $dataArray1, $headerArray1, $widthRatio);
        //Finalizing time
        if ($showCreationAndUpdationTime) {
            //Do it later
        }
        return $pdf1;
    }
}
