<?php
class PatientHistory {
    public static function getPDFPrintOut($pdf1, $history1, $profile1, $login1, $logofilename = null, $enableHeaderAndFooter = true) {
        if ($enableHeaderAndFooter) {
            //Setting Up Header And Footer
            PDFLayout::createPageHeader($pdf1, $profile1->getProfileName(), $logofilename);
            PDFLayout::createPageFooter($pdf1, $profile1->getProfileName());
        }
        //Start A New Page 
        $pdf1->AddPage();
        //Preparing data 
        $header = array('', 'PATIENT HISTORY', '');
        $widthRatio = array(1, 5, 6);
        $dataArray1 = array();
        $generalFormat = array('align:0' => 'R', 'align:1' => 'L', 'align:2' => 'L');
        $index = 0;
        foreach(array("chiefComplaints", "reviewOfOtherServices", "pastMedicalHistory", "familyAndSocialHistory") as $pname)  {
            $object1 = $history1->getMyPropertyValue($pname);
            if (! is_null($object1) && is_object($object1)) {
                $caption = __object__::property2Caption($pname);
                $dataArray1[sizeof($dataArray1)] = array(
                    'data' => array(( $index + 1 ), $caption, $object1->getComments()),
                    'format' => $generalFormat
                );
                $index++;
            }
        }
        $timelabel = "Created On : " . ( $history1->getTimeOfCreation()->getDateAndTimeString() ) . ", Updated On : " . ( $history1->getTimeOfUpdation()->getDateAndTimeString() );
        //$dataArray1[sizeof($dataArray1)] = PDFLayout::$__NEW_LINE;
        $dataArray1[sizeof($dataArray1)] = array(
            'data' => array(
                $timelabel
            , '', ''),
            'format' => array('colspan:0' => 3)
        );
        PDFLayout::writeTable($pdf1, $dataArray1, $header, $widthRatio);
        return $pdf1;
    }
} 
?>