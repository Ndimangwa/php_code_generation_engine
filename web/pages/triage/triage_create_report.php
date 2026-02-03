<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!--  <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    VITAL SIGNS REPORT
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=triage";
                    $enableRollBack = false;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $conn->beginTransaction();
                        $enableRollBack = true;
                        $queue1 = new PatientTriageQueue("Delta", $_REQUEST['qid'], $conn);
                        $patientName = $queue1->getPatient()->getPatientName();
                        $payload = array_merge($_REQUEST, $queue1->getMyPayload(array("visit", "patientCase", "patient")));
                        $payload["timeOfCreation"] = $systemTime1->getTimestamp();
                        $payload["timeOfUpdation"] = $systemTime1->getTimestamp();
                        $payload["attendedBy"] = $login1->getLoginId();
                        //Working with bmi
                        if (trim($payload['weight']) == "") $payload['weight'] = (Triage::$__DEFAULT_NUMBER_VALUE);
                        if (trim($payload['height']) == "") $payload['height'] = (Triage::$__DEFAULT_NUMBER_VALUE);
                        if (($payload['weight'] == Triage::$__DEFAULT_NUMBER_VALUE) || ($payload['height'] == Triage::$__DEFAULT_NUMBER_VALUE)) {
                            $payload["bmi"] = Triage::$__DEFAULT_NUMBER_VALUE;
                        } else {
                            $payload["bmi"] = Triage::calculate_bmi($payload["weight"], $payload["height"] / 100);
                        }
                        $payload["bmiStatus"] = Triage::get_bmi_status($payload["bmi"]);
                        $payload["bmiColor"] = Triage::get_bmi_color($payload["bmiStatus"]);
                        //Working with bp
                        if (trim($payload['bloodPressure']) == "")  $payload['bloodPressure'] = ( Triage::$__DEFAULT_BP_VALUE );
                        $bloodPressure1 = new BloodPressure($payload["bloodPressure"]);
                        $payload['bloodPressure'] = $bloodPressure1->getBloodPressure();
                        $payload['systolicBloodPressure'] = $bloodPressure1->getSystolicValue();
                        $payload['dystolicBloodPressure'] = $bloodPressure1->getDystolicValue();
                        $payload["bloodPressureStatus"] = Triage::get_bp_status($payload["systolicBloodPressure"], $payload["dystolicBloodPressure"]);
                        $payload["bloodPressureColor"] = Triage::get_bp_color($payload["bloodPressureStatus"]);
                        //Working
                        if (trim($payload["pulseRate"]) == "") $payload["pulseRate"] = ( Triage::$__DEFAULT_NUMBER_VALUE );
                        if (trim($payload["oxygenLevel"]) == "") $payload["oxygenLevel"] = ( Triage::$__DEFAULT_NUMBER_VALUE );
                        //Now we need to trace bundleCode 
                        $payload["bundleCode"] = __object__::getMD5CodedString("Triage and Doctor Consultation Queue", 32);
                        $triage1 = new Triage("Delta", __data__::insert($conn, "Triage", $payload, !$enableRollBack), $conn);
                        $windowToDisplay1 = __data__::createDetailsPage($nextPage, "Triage", array('timeOfCreation', 'visit', 'attendedBy', 'weight', 'height', 'systolicBloodPressure', 'dystolicBloodPressure', 'temperature', 'pulseRate', 'respirationRate', 'oxygenLevel'), $conn, $triage1->getTriageId(), null, array(
                            'weight' => ' (Kg)',
                            'height' => ' (cm)',
                            'bloodPressure' => ' (mmHg)',
                            'temperature' => ' (deg C)',
                            'pulseRate' => ' (bpm)',
                            'respirationRate' => ' (bpm)',
                            'oxygenLevel' => ' (%)'
                        ));
                        $windowToDisplay1 .= "<br/>" . $triage1->getMyVitalSignsTable();
                        $windowToDisplay1 = "<div><div class=\"text-center bg-primary\">$patientName</div><div class=\"mt-1\">$windowToDisplay1</div></div>";
                        echo UIView::wrap($windowToDisplay1, "triage-root");
                        //--------------------
                        $case1 = $queue1->getPatientCase();
                        $visit1 = $queue1->getVisit();
                        if (!is_null($visit1->getMedicalDoctor())) {
                            $case1->setNextStage(PatientMovementStage::$__MEDICAL_DOCTOR_CONSULTATION)->update(!$enableRollBack);
                            PatientMovementStage::updatePatientStageAndQueue($conn, $systemTime1, null, $visit1, $login1, !$enableRollBack, null, $triage1->getBundleCode());
                        }
                        $queue1->delete(!$enableRollBack);
                        PatientFile::addTriageLog($conn, $systemTime1, $visit1, $login1, $triage1, __object__::getMD5CodedString("Triage"), !$enableRollBack);
                        $conn->commit();
                        $enableRollBack = false;
                    } catch (Exception $e) {
                        if ($enableRollBack) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Triage</a></i><br />
                        <span class="text-muted"><i>Rule: triage_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>