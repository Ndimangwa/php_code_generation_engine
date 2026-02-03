<?php
class PatientExaminationQueue
{
    public function getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename = null, $showHeaderAndFooter = true, $showCreationAndUpdationTime = true, $addNewPage = true, $settings = null, $shapingFunction = null)  {
        $settings = is_null($settings) ? array() : $settings;
        //Step 1: Get examinationQueue Reference
        $examinationQueue1 = $this;
        $conn = $this->conn;
        //Step 2: Working with Headers and Footers if any
        if ($showHeaderAndFooter)   {
            $headerTitle = isset($settings['header-title']) ? $settings['header-title'] : "Default Title";
            $footerTitle = isset($settings['footer-title']) ? $settings['footer-title'] : "Default Title";
            PDFLayout::createPageHeader($pdf1, $headerTitle, $logofilename, $settings);
            PDFLayout::createPageFooter($pdf1, $footerTitle, $settings);
        }
        //Step 3: Adding a New Page if any
        if ($addNewPage)    {
            $pdf1->AddPage();
        }
        //Step 5: Get Service List 
        $listOfServices = $examinationQueue1->getListOfServices();
        if (is_null($listOfServices)) return $pdf1;
        //Step 6: Traversing through list of services
        //Write Invoice Information
        $tableTitle = isset($settings['table-title']) ? $settings['table-title'] : "Patient Examination Report";
        PDFLayout::writeBlock($pdf1, array(("<b><h3>" . $tableTitle . "</h3></b>")), 'L', $settings);
        //Pulling Invoice Information
        $listOfInvoices = PatientInvoice::filterRecords($conn, array(
            "temporaryObjectHolder" => ( $examinationQueue1->getObjectReferenceString() )
        ));
        //Step 4 -- Temp: Preparing Invoices 
        if (! is_null($listOfInvoices)) {
            $headerArray1 = array("S/N", "Currency", "Invoice Number", "Invoiced Amount", "Total Paid");
            $generalFormat = array("align:0" => "R", "align:1" => "L", "align:2" => "L", "align:3" => "R", "align:4" => "R");
            $widthRatio = array(1, 2, 4, 4, 4);
            PDFLayout::writeBlock($pdf1, array(("<b><h3>Invoices Summary</h3></b>")), 'L', $settings);
            $dataArray1 = array();
            foreach ($listOfInvoices as $index => $invoice1)  {
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
        $headerArray1 = array("S/N", "Name of Test", "Results", "Comments");
        $generalFormat = array("align:0" => "R", "align:1" => "L", "align:2" => "L", "align:3" => "L");
        $widthRatio = array(1, 6, 6, 4);
        foreach ($listOfServices as $index => $service1)  {
            //Write Title
            $sn = $index + 1; 
            $serviceName = $service1->getServiceName();
            $titleText = "<b><span>$sn : </span><span>$serviceName</span></b>";
            PDFLayout::writeBlock($pdf1, array($titleText), 'L', $settings);
            //Get Results for this service 
            $listOfResults = PatientExaminationResults::getListOfResultsForService($conn, $examinationQueue1->getQueueId(), $service1->getServiceId());
            if (is_null($listOfResults))    {
                $errorText = "---Results Not Yet---";
                PDFLayout::writeBlock($pdf1, array($errorText), 'L', $settings);
            } else {
                //Now you have results
                $dataArray1 = array();
                foreach ($listOfResults as $index => $results1) {
                    $standard1 = $results1->getExaminationStandard();
                    $sn = $index + 1;
                    $test = $standard1->getNameOfTest();
                    $results = $results1->getGeneralValue();
                    $comment = ( $results1->isSafeValue() ) ? "OK" : "Needs Attention";
                    //Now Build 
                    $dataArray1[sizeof($dataArray1)] = array(
                        'data' => array($sn, $test, $results, $comment),
                        'format' => $generalFormat
                    );
                }
                //Build Table
                PDFLayout::writeTable($pdf1, $dataArray1, $headerArray1, $widthRatio);
            }
        }
        //Finalizing time
        if ($showCreationAndUpdationTime)   {
            //Do it later
        }

        return $pdf1;
    }
    public static function getExaminationQueuesForMedicalConsultationQueue($conn, $medicalConsultationQueueId)
    {
        $list = array();
        try {
            $records = __data__::getSelectedRecords($conn, "SELECT queueId FROM _patient_examination_queue WHERE consultationQueue = '$medicalConsultationQueueId'", false);
            foreach ($records['column'] as $row) {
                $list[sizeof($list)] = new PatientExaminationQueue("Delta", $row['queueId'], $conn);
            }
        } catch (Exception $e) {
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
    public function isQueueCompleted()
    {
        $queue1 = $this;
        if ($queue1->isCompleted()) return true;
        $listOfServices = $queue1->getListOfServices();
        $listOfAttendedServices = $queue1->getListOfAttendedServices();
        if (is_null($listOfAttendedServices)) return false;
        if (is_null($listOfServices)) return true;
        $listOfServices = explode(",", __data__::convertListObjectsToCommaSeparatedValues($listOfServices));
        $listOfAttendedServices = explode(",", __data__::convertListObjectsToCommaSeparatedValues($listOfAttendedServices));
        $completed = true;
        foreach ($listOfServices as $serviceId) {
            $completed = in_array($serviceId, $listOfAttendedServices);
            if (!$completed) break;
        }
        return $completed;
    }
}
