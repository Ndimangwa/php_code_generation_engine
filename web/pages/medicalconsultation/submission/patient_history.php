<div>
    <?php
    $patientHistoryFieldArray1 = array("chiefComplaints", "reviewOfOtherServices", "pastMedicalHistory", "familyAndSocialHistory");
    //Now we need to insert into patientHistory, otherwise we need to update the existing 
    if (is_null($patientHistory1)) {
        $enableUpdate = false;
        //Now preparing payloads
        foreach ($patientHistoryFieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                $colArray1[$colname] = __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                    "comments" => $_POST[$colname]
                )), !$erollback);
                $enableUpdate = true;
            }
        }
        //New One 
        if ($enableUpdate) {
            $patientHistory1 = new PatientHistory("Delta", __data__::insert($conn, "PatientHistory", $colArray1, !$erollback), $conn);
        }
        //Now move the applicationCounter
        //$consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_GENERAL_EXAMINATION)->update(!$erollback);
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        $enableUpdate = false;
        foreach ($patientHistoryFieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
                //We need to check if exists link or we need to establish a new one
                $medicalComment1 = $patientHistory1->getMyPropertyValue($colname);
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
                $enableUpdate = true;
            }
        }
        //Update 
        //$patientHistory1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
        if ($enableUpdate) {
            $patientHistory1->setUpdateList($updateArray1)->update(!$erollback);
        }
    }
    ?>
</div>