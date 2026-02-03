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
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        $queue1 = new PatientTriageQueue("delta", $_REQUEST['qid'], $conn);
        $temperature = "human-temperature-celsius";
        $formToDisplay = __data__::createDataCaptureForm($nextPage, "Triage", array(
            array('pname' => 'weight', 'caption' => 'Weight (Kg)', 'required' => false, 'placeholder' => '45'),
            array('pname' => 'height', 'caption' => 'Height (cm)', 'required' => false, 'placeholder' => '146'),
            array('pname' => 'bloodPressure', 'caption' => 'Blood Pressure (mmHg)', 'required' => false, 'placeholder' => '119/78'),
            array('pname' => 'temperature', 'caption' => 'Temperature (deg C)', 'required' => false, 'placeholder' => '36.9'),
            array('pname' => 'pulseRate', 'caption' => 'Pulse Rate (bpm)', 'required' => false, 'placeholder' => '72'),
            array('pname' => 'respirationRate', 'caption' => 'Respiration Rate (bpm)', 'required' => false, 'placeholder' => '64'),
            array('pname' => 'oxygenLevel', 'caption' => 'Oxygen Level (%)', 'required' => false, 'placeholder' => '99.9')
        ), "Record Vital Signs", "create", $conn, 0, array(
            'report' => 's201',
            'page' => $page,
            'qid' => $queue1->getQueueId()
        ), null, null, "btn-general-submit");
        echo $formToDisplay;
    } catch (Exception $e)  {
        //echo __data__::showDangerAlert($e->getMessage());
        echo UICardView::getDangerReportCard("Vital Signs", $e->getMessage());
    }
    $conn = null;
?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Triage</a></i><br/>
                        <span class="text-muted"><i>Rule: triage_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>