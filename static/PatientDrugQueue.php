<?php
class PatientDrugQueue {
    public function getPDFTabularSummary($pdf1, $profile1, $login1, $logofilename = null, $showHeaderAndFooter = true, $showCreationAndUpdationTime = true, $addNewPage = true, $settings = null, $shapingFunction = null)  {
        $settings = is_null($settings) ? array() : $settings;
        //Step 1: Get drugQueue Reference
        $patientDrugQueue1 = $this;
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
        //Step 5: Get DrugManagementList
        $listOfDrugManagement = $patientDrugQueue1->getListOfDrugManagements();
        if (is_null($listOfDrugManagement)) return $pdf1;
        //Step 6: Traversing through list of services
        //Write Invoice Information
        $tableTitle = isset($settings['table-title']) ? $settings['table-title'] : "Patient Drugs Report";
        PDFLayout::writeBlock($pdf1, array(("<b><h3>" . $tableTitle . "</h3></b>")), 'L', $settings);
        //Pulling Invoice Information
        $listOfInvoices = PatientInvoice::filterRecords($conn, array(
            "temporaryObjectHolder" => ( $patientDrugQueue1->getObjectReferenceString() )
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
        $headerArray1 = array("S/N", "Drug Name", "Quantity", "Usage", "Status");
        $generalFormat = array("align:0" => "R", "align:1" => "L", "align:2" => "R", "align:3" => "L", "align:4" => "L");
        $widthRatio = array(1,4,2,3,2);
        $dataArray1 = array();
        foreach ($listOfDrugManagement as $index => $management1) {
            $sn = $index + 1;
            $pharmaceuticalDrug1 = $management1->getPharmaceuticalDrug();
            $drugName = $pharmaceuticalDrug1->getDrugName();
            $quantity = "";
            $usage = "";
            $status = "Requested";
            $dispensedPatientDrug1 = self::getDispensedPatientDrugForManagement($conn, $management1->getManagementId());
            if (! is_null($dispensedPatientDrug1))  {
                $quantity = $dispensedPatientDrug1->getQuantity();
                $usage = $dispensedPatientDrug1->getUsage();
                $status = "Dispensed";
                $unit = $pharmaceuticalDrug1->getUnitOfMeasurement()->getUnitName();
                //Build quantity
                $quantity = "$quantity $unit";
            }
            $dataArray1[sizeof($dataArray1)] = array(
                'data' => array($sn, $drugName, $quantity, $usage, $status),
                'format' => $generalFormat
            );
        }
        PDFLayout::writeTable($pdf1, $dataArray1, $headerArray1, $widthRatio);
        //Finalizing time
        if ($showCreationAndUpdationTime)   {
            //Do it later
        }
        return $pdf1;
    }
    public static function getDispensedPatientDrugForManagement($conn, $managementId)   {
        $classname = "DispensedPatientDrug";
        $tablename = Registry::getTablename($classname);
        if (is_null($tablename)) return null;
        $query = "SELECT dispensedId FROM $tablename WHERE drugManagement='$managementId'";
        $dispensedPatientDrug1 = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, true);
            $dispensedPatientDrug1 = Registry::getObjectReference("Delta", $conn, $classname, $records['column'][0]['dispensedId']);
        } catch (Exception $e)  {
            $dispensedPatientDrug1 = null;
        }
        return $dispensedPatientDrug1;
    }
    public static function getListOfDispensedPatientDrug($conn, $queueId)   {
        $classname = "DispensedPatientDrug";
        $tablename = Registry::getTablename($classname);
        if (is_null($tablename)) return null;
        $query = "SELECT dispensedId FROM $tablename WHERE drugQueue='$queueId'";
        $listOfDispensedDrugs = array();
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
            foreach ($records['column'] as $record1)    {
                $dispensedPatientDrug1 = Registry::getObjectReference("Delta", $conn, $classname, $record1['dispensedId']);
                if (! is_null($dispensedPatientDrug1))  {
                    $listOfDispensedDrugs[sizeof($listOfDispensedDrugs)] = $dispensedPatientDrug1;
                }          
            }
        } catch (Exception $e)  {
            $listOfDispensedDrugs = array();
        }
        return (sizeof($listOfDispensedDrugs) == 0) ? null : $listOfDispensedDrugs;
    }
    public static function getListOfDispensedDrugManagement($conn, $queueId)    {
        $listOfDrugManagement = array();
        $listOfDispensedDrugs = self::getListOfDispensedPatientDrug($conn, $queueId);
        if (is_null($listOfDispensedDrugs)) return null;
        foreach ($listOfDispensedDrugs as $dispensedPatientDrug1)   {
            $listOfDrugManagement[sizeof($listOfDrugManagement)] = $dispensedPatientDrug1->getDrugManagement();
        }
        return (sizeof($listOfDrugManagement) == 0) ? null : $listOfDrugManagement;
    }
    public static function getListOfNotYetDispensedDrugManagement($conn, $queueId)  {
        $patientDrugQueue1 = new PatientDrugQueue("Delta", $queueId, $conn);
        $listOfRequestedDrugManagement = $patientDrugQueue1->getListOfDrugManagements();
        if (is_null($listOfRequestedDrugManagement)) return null;
        $listOfDispensedDrugManagement = self::getListOfDispensedDrugManagement($conn, $queueId);
        if (is_null($listOfDispensedDrugManagement)) return $listOfRequestedDrugManagement;
        $listOfDispensedDrugManagementIds = __data__::convertListObjectsToArray($listOfDispensedDrugManagement);
        $listOfNotYetDispensedDrugManagement = array();
        foreach ($listOfRequestedDrugManagement as $reqManagement1) {
            if (! in_array($reqManagement1->getManagementId(), $listOfDispensedDrugManagementIds))  {
                $listOfNotYetDispensedDrugManagement[sizeof($listOfNotYetDispensedDrugManagement)] = $reqManagement1;
            }
        }    
        return (sizeof($listOfNotYetDispensedDrugManagement) == 0) ? null : $listOfNotYetDispensedDrugManagement;
    }
}
?>