<?php 
$vitalSigns1 = is_null($vitalSigns1) ? ( Triage::getTriageForConsultationQueue($conn, $consultationQueue1) ) : $vitalSigns1;
echo __data__::createDataCaptureForm($thispage, "VitalSigns", array(
    array("pname" => "weight", "value" => ( ( ! ( is_null($vitalSigns1) || ( $vitalSigns1->getWeight() == Triage::$__DEFAULT_NUMBER_VALUE ) ) ) ? ( $vitalSigns1->getWeight() ) : "" ), "caption" => "Weight (Kg)", "required" => false, "placeholder" => "78"),
    array("pname" => "height", "value" => ( ( ! ( is_null($vitalSigns1) || ( $vitalSigns1->getHeight() == Triage::$__DEFAULT_NUMBER_VALUE ) ) ) ? ( $vitalSigns1->getHeight() ) : "" ), "caption" => "Height (cm)", "required" => false, "placeholder" => "176"),
    array("pname" => "temperature", "value" => ( ( ! ( is_null($vitalSigns1) || ( $vitalSigns1->getTemperature() == Triage::$__DEFAULT_NUMBER_VALUE ) ) ) ? ( $vitalSigns1->getTemperature() ) : "" ), "caption" => "Temperature (deg C)", "required" => false, "placeholder" => "36.9"),
    array("pname" => "bloodPressure", "value" => ( ( ! ( is_null($vitalSigns1) || ( $vitalSigns1->getBloodPressure() == Triage::$__DEFAULT_BP_VALUE ) ) ) ? ( $vitalSigns1->getBloodPressure() ) : "" ), "caption" => "Blood Pressure (mmHg)", "required" => false, "placeholder" => "118/79"),
    array("pname" => "pulseRate", "value" => ( ( ! ( is_null($vitalSigns1) || ( $vitalSigns1->getPulseRate() == Triage::$__DEFAULT_NUMBER_VALUE ) ) ) ? ( $vitalSigns1->getPulseRate() ) : "" ), "caption" => "Pulse Rate (bpm)", "required" => false, "placeholder" => "64"),
    array("pname" => "respirationRate", "value" => ( ( ! ( is_null($vitalSigns1) || ( $vitalSigns1->getRespirationRate() == Triage::$__DEFAULT_NUMBER_VALUE ) ) ) ? ( $vitalSigns1->getRespirationRate() ) : "" ), "caption" => "Respiration Rate (bpm)", "required" => false, "placeholder" => "16"),
    array("pname" => "oxygenLevel", "value" => ( ( ! ( is_null($vitalSigns1) || ( $vitalSigns1->getOxygenLevel() == Triage::$__DEFAULT_NUMBER_VALUE ) ) ) ? ( $vitalSigns1->getOxygenLevel() ) : "" ), "caption" => "Saturation Level (%)", "required" => false, "placeholder" => "99")
), "Record Vital Signs", "create", null, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter())
), null, null, "vital-signs", $thispage, true);
?>