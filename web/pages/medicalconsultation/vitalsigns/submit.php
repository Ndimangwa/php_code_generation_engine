<?php
$fieldArray1 = array("weight", "height", "temperature", "bloodPressure", "pulseRate", "respirationRate", "oxygenLevel");
//Now we need to insert into vitalSigns, otherwise we need to update the existing 
if (is_null($vitalSigns1)) {
    //Now preparing payloads
    foreach ($fieldArray1 as $colname) {
        if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
            //We need to test for Blood Pressure  just make a new Object and it will auto-validate
            if ($colname == "bloodPressure") {
                $bloodPressure1 =  new BloodPressure($_POST[$colname]); //For-Validation Sake
                $colArray1[$colname] = $bloodPressure1->getBloodPressure();
            } else {
                $colArray1[$colname] = $_POST[$colname];
            }
        }
    }
    //New One 
    $vitalSigns1 = new VitalSigns("Delta", __data__::insert($conn, "VitalSigns", $colArray1, !$erollback), $conn);
    //Now move the applicationCounter
    $consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_LOCAL_EXAMINATION)->update(!$erollback);
} else {
    //Now we need to update-or-insert the missing fields 
    $updateArray1 = array(
        "timeOfUpdation" => ($systemTime1->getTimestamp())
    );
    foreach ($fieldArray1 as $colname) {
        if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
            //We need to check if exists link or we need to establish a new one
            $fieldValue = $vitalSigns1->getMyPropertyValue($colname);
            if ($_POST[$colname] != $fieldValue) {
                if ($colname == "bloodPressure") {
                    $bloodPressure1 = new BloodPressure($_POST[$colname]); //For-Validation Sake
                    $updateArray1[$colname] = $bloodPressure1->getBloodPressure();
                } else {
                    $updateArray1[$colname] = $_POST[$colname];
                }
            }
        }
    }
    //Update 
    //$vitalSigns1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
    $vitalSigns1->setUpdateList($updateArray1)->update(!$erollback);
}
//Build UI
echo UICardView::getSuccesfulReportCard("Vital Signs", "You have successful Recorded Vital Signs");
