<div>
    <?php
    $generalExaminationFieldArray1 = array("generalExamination");
    $generalExaminationLookupArray1 = array("generalExamination" => "examination");
    //Now we need to insert into generalExamination, otherwise we need to update the existing 
    if (is_null($generalExamination1)) {
        $enableUpdate = false;
        //Now preparing payloads
        foreach ($generalExaminationFieldArray1 as $colname) {
            $pname = isset($generalExaminationLookupArray1[$colname]) ? $generalExaminationLookupArray1[$colname] : $colname;
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                $colArray1[$pname] = __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                    "comments" => $_POST[$colname]
                )), !$erollback);
                $enableUpdate = true;
            }
        }
        //New One 
        if ($enableUpdate) {
            $generalExamination1 = new GeneralExamination("Delta", __data__::insert($conn, "GeneralExamination", $colArray1, !$erollback), $conn);
        }
        //Now move the applicationCounter
        //$consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_VITAL_SIGNS)->update(!$erollback);
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        $enableUpdate = false;
        foreach ($generalExaminationFieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
                //We need to check if exists link or we need to establish a new one
                $pname = isset($generalExaminationLookupArray1[$colname]) ? $generalExaminationLookupArray1[$colname] : $colname;
                $medicalComment1 = $generalExamination1->getMyPropertyValue($pname);
                if (is_null($medicalComment1)) {
                    //This was not initially set
                    $updateArray1[$pname] = __data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
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
        //$generalExamination1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
        if ($enableUpdate) {
            $generalExamination1->setUpdateList($updateArray1)->update(!$erollback);
        }
    }
    ?>
</div>