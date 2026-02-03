<?php
$mylistcolumn = "listOfDifferentialDiseases";
$fieldArray1 = array($mylistcolumn);
//Now we need to insert into provisionDiagnosis, otherwise we need to update the existing 
if (is_null($provisionDiagnosis1)) {
    //Now preparing payloads
    foreach ($fieldArray1 as $colname) {
        if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
            if ($colname == $mylistcolumn) {
                $tArray1 = $_POST[$colname];
                $diffDiseaseArray1 = array();
                for ($i = 0; $i < sizeof($tArray1); $i++) {
                    $t1 = $tArray1[$i];
                    if ($i == 0) {
                        $colArray1["mainDisease"] = $t1;
                    } else {
                        $diffDiseaseArray1[sizeof($diffDiseaseArray1)] = $t1;
                    }
                }
                if (sizeof($diffDiseaseArray1) > 0) {
                    $colArray1[$colname] = implode(",", $diffDiseaseArray1);
                }
            }
        }
    }
    //New One 
    $provisionDiagnosis1 = new ProvisionDiagnosis("Delta", __data__::insert($conn, "ProvisionDiagnosis", $colArray1, !$erollback), $conn);
    //Now move the applicationCounter
    $consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_INVESTIGATION)->update(!$erollback);
} else {
    //Now we need to update-or-insert the missing fields 
    $updateArray1 = array(
        "timeOfUpdation" => ($systemTime1->getTimestamp())
    );
    foreach ($fieldArray1 as $colname) {
        if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
            //We need to check if exists link or we need to establish a new one
            //start do here
            if ($colname == $mylistcolumn) {
                $tArray1 = $_POST[$colname];
                $diffDiseaseArray1 = array();
                for ($i = 0; $i < sizeof($tArray1); $i++) {
                    $t1 = $tArray1[$i];
                    if ($i == 0) {
                        $updateArray1["mainDisease"] = $t1;
                    } else {
                        $diffDiseaseArray1[sizeof($diffDiseaseArray1)] = $t1;
                    }
                }
                $updateArray1[$colname] = implode(",", $diffDiseaseArray1);
            }
            //end do here
        }
    }
    //Update 
    //$provisionDiagnosis1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
    $provisionDiagnosis1->setUpdateList($updateArray1)->update(!$erollback);
}
//Build UI
echo UICardView::getSuccesfulReportCard("Provision Diagnosis", "You have successful Recorded Provision Diagnosis");
