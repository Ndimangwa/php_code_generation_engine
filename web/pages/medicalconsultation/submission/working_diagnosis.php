<div>
    <?php
    $workingDiagnosisWorkingColumn = "workingDiagnosis";
    $workingDiagnosisFieldArray1 = array($workingDiagnosisWorkingColumn);
    $workingDiagnosisLookupArray1 = array($workingDiagnosisWorkingColumn => "listOfDiseases");
    //Now we need to insert into workingDiagnosis, otherwise we need to update the existing 
    if (is_null($workingDiagnosis1)) {
        $enableUpdate = false;
        //Now preparing payloads
        foreach ($workingDiagnosisFieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                $pname = isset($workingDiagnosisLookupArray1[$colname]) ? $workingDiagnosisLookupArray1[$colname] : $colname;
                if ($colname == $workingDiagnosisWorkingColumn) {
                    $colArray1[$pname] = implode(",", $_POST[$colname]);
                    $enableUpdate = true;
                }
            }
        }
        //New One 
        if ($enableUpdate) {
            $workingDiagnosis1 = new WorkingDiagnosis("Delta", __data__::insert($conn, "WorkingDiagnosis", $colArray1, !$erollback), $conn);
        }
        //Now move the applicationCounter
        //$consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_MANAGEMENT_PLAN)->update(!$erollback);
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        $enableUpdate = false;
        foreach ($workingDiagnosisFieldArray1 as $colname) {
            if (isset($_POST[$colname]) && (__data__::isNotEmpty($_POST[$colname]))) {
                $pname = isset($workingDiagnosisLookupArray1[$colname]) ? $workingDiagnosisLookupArray1[$colname] : $colname;
                //We need to check if exists link or we need to establish a new one
                //start do here
                if ($colname == $workingDiagnosisWorkingColumn) {
                    $updateArray1[$pname] = implode(",", $_POST[$colname]);
                    $enableUpdate = true;
                }
                //end do here
            }
        }
        //Update 
        //$workingDiagnosis1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
        if ($enableUpdate) {
            $workingDiagnosis1->setUpdateList($updateArray1)->update(!$erollback);
        }
    }
    ?>
</div>