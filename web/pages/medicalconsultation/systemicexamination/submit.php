<?php
$fieldArray1 = array("examination");
//Now we need to insert into systemicExamination, otherwise we need to update the existing 
if (is_null($systemicExamination1)) {
    //Now preparing payloads
    foreach ($fieldArray1 as $colname) {
        if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
            $colArray1[$colname] = __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                "comments" => $_POST[$colname]
            )), !$erollback);
        }
    }
    //New One 
    $systemicExamination1 = new SystemicExamination("Delta", __data__::insert($conn, "SystemicExamination", $colArray1, !$erollback), $conn);
    //Now move the applicationCounter
    $consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_PROVISION_DIAGNOSIS)->update(!$erollback);
} else {
    //Now we need to update-or-insert the missing fields 
    $updateArray1 = array(
        "timeOfUpdation" => ($systemTime1->getTimestamp())
    );
    foreach ($fieldArray1 as $colname) {
        if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
            //We need to check if exists link or we need to establish a new one
            $medicalComment1 = $systemicExamination1->getMyPropertyValue($colname);
            if (is_null($medicalComment1)) {
                //This was not initially set
                $updateArray1[$colname] = __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                    "comments" => $_POST[$colname]
                )), !$erollback);
            } else {
                //Was initially set , just update 
                if ($_POST[$colname] != ($medicalComment1->getComments())) {
                    $medicalComment1->setComments($_POST[$colname])->setTimeOfUpdation($systemTime1->getTimestamp())->update(!$erollback);
                }
            }
        }
    }
    //Update 
    //$systemicExamination1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
    $systemicExamination1->setUpdateList($updateArray1)->update(!$erollback);
}
//Build UI
echo UICardView::getSuccesfulReportCard("Systemic Examination", "You have successful Recorded Systemic Examination");
