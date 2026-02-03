<div class="container data-container mt-2 mb-2">
    <div class="row">
       <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
--><div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    RECORD VITAL SIGNS
                </div>
                <div class="card-body">
<?php
    $conn = null;
    $nextPage = $thispage."?page=triage";
    try {
        $erollback = false;
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $patient1 = new Patient("Delta", $_REQUEST['patientId'], $conn);
        $vitalSigns1 = Triage::getTriageForPatient($conn, $patient1);
        if ($_POST['submit'])   {
            $conn->beginTransaction();
            $erollback = true;
            //Do Submission at this point 
            //Prepare colArray1 
            $colArray1 = array(
                'timeOfCreation' => ( $systemTime1->getTimestamp() ),
                'timeOfUpdation' => ( $systemTime1->getTimestamp() ),
                'patient' => ( $patient1->getPatientId() ),
                'patientCase' => ( $patient1->getCurrentCase() ),
                'visit' => ( $patient1->getCurrentVisit() ),
                'ownerReference' => ( $login1->getObjectReferenceString() ),
                'attendedBy' => ( $login1->getLoginId() )
            );
            $vitalSigns1 = null; //We are inserting new one
            include("submission/vital_signs.php");
            //After submission
            $conn->commit();
            $erollback = false;
            unset($_POST['submit']); //We need to undo-submission
            //Successful report
            echo UICardView::getSuccesfulReportCard("Vital Signs", "You have succesful sumitted Vital Signs Data");
        } else {
            $controlDisabled = false;
            $formToDisplay = __data__::createDataCaptureForm($thispage, "VitalSigns", array(
                array("disabled" => $controlDisabled, "pname" => "weight", "value" => ((!(is_null($vitalSigns1) || ($vitalSigns1->getWeight() == Triage::$__DEFAULT_NUMBER_VALUE))) ? ($vitalSigns1->getWeight()) : ""), "caption" => "Weight (Kg)", "required" => false, "placeholder" => "78"),
                array("disabled" => $controlDisabled, "pname" => "height", "value" => ((!(is_null($vitalSigns1) || ($vitalSigns1->getHeight() == Triage::$__DEFAULT_NUMBER_VALUE))) ? ($vitalSigns1->getHeight()) : ""), "caption" => "Height (cm)", "required" => false, "placeholder" => "176"),
                array("disabled" => $controlDisabled, "pname" => "temperature", "value" => ((!(is_null($vitalSigns1) || ($vitalSigns1->getTemperature() == Triage::$__DEFAULT_NUMBER_VALUE))) ? ($vitalSigns1->getTemperature()) : ""), "caption" => "Temperature (deg C)", "required" => false, "placeholder" => "36.9"),
                array("disabled" => $controlDisabled, "pname" => "bloodPressure", "value" => ((!(is_null($vitalSigns1) || ($vitalSigns1->getBloodPressure() == Triage::$__DEFAULT_BP_VALUE))) ? ($vitalSigns1->getBloodPressure()) : ""), "caption" => "Blood Pressure (mmHg)", "required" => false, "placeholder" => "118/79"),
                array("disabled" => $controlDisabled, "pname" => "pulseRate", "value" => ((!(is_null($vitalSigns1) || ($vitalSigns1->getPulseRate() == Triage::$__DEFAULT_NUMBER_VALUE))) ? ($vitalSigns1->getPulseRate()) : ""), "caption" => "Pulse Rate (bpm)", "required" => false, "placeholder" => "64"),
                array("disabled" => $controlDisabled, "pname" => "respirationRate", "value" => ((!(is_null($vitalSigns1) || ($vitalSigns1->getRespirationRate() == Triage::$__DEFAULT_NUMBER_VALUE))) ? ($vitalSigns1->getRespirationRate()) : ""), "caption" => "Respiration Rate (bpm)", "required" => false, "placeholder" => "16"),
                array("disabled" => $controlDisabled, "pname" => "oxygenLevel", "value" => ((!(is_null($vitalSigns1) || ($vitalSigns1->getOxygenLevel() == Triage::$__DEFAULT_NUMBER_VALUE))) ? ($vitalSigns1->getOxygenLevel()) : ""), "caption" => "Saturation Level (%)", "required" => false, "placeholder" => "99")
            ), "Record Vital Signs", "create", $conn, 0, array(
                'page' => $page,
                'patientId' => ( $patient1->getPatientId() ),
                'submit' => 1
            ), null, null, "delta-vital-signs", $thispage, true);
            echo $formToDisplay;
        }
        $conn = null;
    } catch (Exception $e)  {
        //echo __data__::showDangerAlert($e->getMessage());
        if (! is_null($conn) && $erollback) $conn->rollBack();
        echo UICardView::getDangerReportCard("Vital Signs", $e->getMessage());
    }
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Triage</a></i><br/>
                        <span class="text-muted"><i>Rule: vitalsigns_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>