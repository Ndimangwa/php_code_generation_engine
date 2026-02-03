<div>
    <?php
    $vitalSignsFieldArray1 = array("weight", "height", "temperature", "bloodPressure", "pulseRate", "respirationRate", "oxygenLevel");
    //Now we need to insert into vitalSigns, otherwise we need to update the existing 
    //Begin General Items bmi and other Status
    if (isset($_POST['height']) && isset($_POST['weight']) && ( __data__::isNotEmpty($_POST['height']) ) && ( __data__::isNotEmpty($_POST['weight']) )) {
        //bmi 
        $colArray1['bmi'] = Triage::calculate_bmi($_POST['weight'], ($_POST['height'] / 100));
        $colArray1['bmiStatus'] = Triage::get_bmi_status($colArray1['bmi']);
        $colArray1['bmiColor'] = Triage::get_bmi_color($colArray1['bmiStatus']);
    }
    if (isset($_POST['bloodPressure']) && __data__::isNotEmpty($_POST['bloodPressure'])) {
        $bloodPressure1 = new BloodPressure($_POST['bloodPressure']);
        $colArray1['bloodPressureStatus'] = Triage::get_bp_status($bloodPressure1->getSystolicValue(), $bloodPressure1->getDystolicValue());
        $colArray1['bloodPressureColor'] = Triage::get_bp_color($colArray1['bloodPressureStatus']);
    }
    //End General Items bmi and other Status
    if (is_null($vitalSigns1)) {
        $enableUpdate = false;
        //Now preparing payloads
        foreach ($vitalSignsFieldArray1 as $colname) {
            if (isset($_POST[$colname])  && (__data__::isNotEmpty($_POST[$colname]))) {
                //We need to test for Blood Pressure  just make a new Object and it will auto-validate
                if ($colname == "bloodPressure") {
                    $bloodPressure1 =  new BloodPressure($_POST[$colname]); //For-Validation Sake
                    $colArray1[$colname] = $bloodPressure1->getBloodPressure();
                } else {
                    $colArray1[$colname] = $_POST[$colname];
                }
                $enableUpdate = true;
            }
        }
        //New One 
        if ($enableUpdate) {
            $vitalSigns1 = new VitalSigns("Delta", __data__::insert($conn, "VitalSigns", $colArray1, !$erollback), $conn);
        }
        //Now move the applicationCounter
        //$consultationQueue1->setApplicationCounter(MedicalDoctorConsultationQueue::$__BLOCK_LOCAL_EXAMINATION)->update(!$erollback);
    } else {
        //Now we need to update-or-insert the missing fields 
        $updateArray1 = array(
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        );
        $enableUpdate = false;
        foreach ($vitalSignsFieldArray1 as $colname) {
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
                $enableUpdate = true;
            }
        }
        //Update 
        //$vitalSigns1->setTimeOfUpdation($systemTime1->getTimestamp())->update(! $erollback);
        if ($enableUpdate) {
            $vitalSigns1->setUpdateList($updateArray1)->update(!$erollback);
        }
    }
    ?>
</div>