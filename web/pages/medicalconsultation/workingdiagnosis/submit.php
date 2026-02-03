<?php
$mylistcolumn = "listOfDiseases";
$fieldArray1 = array($mylistcolumn);
//Now we need to insert into workingDiagnosis, otherwise we need to update the existing 
if (is_null($workingDiagnosis1)) {
    //Now preparing payloads
    foreach ($fieldArray1 as $colname) {
        if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
            if ($colname == $mylistcolumn) {
                $colArray1[$colname] = implode(",", $_POST[$colname]);
            }
        }
    }
    //New One 
    $workingDiagnosis1 = new WorkingDiagnosis("Delta", __data__::insert($conn, "WorkingDiagnosis", $colArray1, !$erollback), $conn);
    //Now move the applicationCounter
    $consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_MANAGEMENT_PLAN)->update(!$erollback);
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
                $updateArray1[$colname] = implode(",", $_POST[$colname]);
            }
            //end do here
        }
    }
    //Update 
    //$workingDiagnosis1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
    $workingDiagnosis1->setUpdateList($updateArray1)->update(!$erollback);
}
//Build UI
echo UICardView::getSuccesfulReportCard("Working Diagnosis", "You have successful Recorded Working Diagnosis");
