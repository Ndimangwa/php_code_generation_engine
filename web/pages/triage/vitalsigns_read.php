<?php
$__REPORT = 1;
$__GRAPH = 2;
$__WEIGHT = 1;
$__HEIGHT = 2;
$__TEMPERATURE = 3;
$__BLOOD_PRESSURE = 4;
$__PULSE_RATE = 5;
$__RESPIRATION_RATE = 6;
$__OXYGEN_LEVEL = 7;
$__BMI = 27;
$currentTab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : $__REPORT;
$pageTitleArray1 = array(
    ($__REPORT) => "VITAL SIGNS REPORT",
    ($__GRAPH) => "VITAL SIGNS GRAPH"
);
$innerTab = isset($_REQUEST['tab2']) ? $_REQUEST['tab2'] : $__WEIGHT;
function getChartData($listOfVitalSigns, & $summaryLegendArray1 )
{
    $__WEIGHT = 1;
    $__HEIGHT = 2;
    $__TEMPERATURE = 3;
    $__BLOOD_PRESSURE = 4;
    $__PULSE_RATE = 5;
    $__RESPIRATION_RATE = 6;
    $__OXYGEN_LEVEL = 7;
    $__BMI = 27;
    $weightArray1 = array();
    $heightArray1 = array();
    $temperatureArray1 = array();
    $bloodPressureArray1 = array();
    $pulseRateArray1 = array();
    $respirationRateArray1 = array();
    $oxygenLevelArray1 = array();
    $bmiArray1 = array();
    //summary legend
    foreach (array($__WEIGHT, $__HEIGHT, $__TEMPERATURE, $__BLOOD_PRESSURE, $__PULSE_RATE, $__RESPIRATION_RATE, $__OXYGEN_LEVEL, $__BMI) as $index)    {
        $summaryLegendArray1[$index] = array();
    }
    foreach ($listOfVitalSigns as $vitalSigns1) {
        $weight = $vitalSigns1->getWeight();
        $height = $vitalSigns1->getHeight();
        $temperature = $vitalSigns1->getTemperature();
        $bloodPressure = $vitalSigns1->getBloodPressure();
        $pulseRate = $vitalSigns1->getPulseRate();
        $respirationRate = $vitalSigns1->getRespirationRate();
        $oxygenLevel = $vitalSigns1->getOxygenLevel();
        $bmi = $vitalSigns1->getBmi();
        $time = $vitalSigns1->getTimeOfUpdation()->getDateAndTimeString();
        $attendedBy = $vitalSigns1->getAttendedBy();
        $attendedBy = is_null($attendedBy) ? $attendedBy : ( $attendedBy->getLoginName() );
        if (!is_null($weight) && ($weight != Triage::$__DEFAULT_NUMBER_VALUE)) {
            if (sizeof($weightArray1) == 0) {
                $weightArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Weight', 'data' => array())));
            }
            $index = sizeof($weightArray1['labels']);
            $weightArray1['labels'][$index] = $time;
            $weightArray1['datasets'][0]['data'][$index] = $weight;
            $summaryLegendArray1[$__WEIGHT][$index] = $attendedBy;
        }
        if (!is_null($height) && ($height != Triage::$__DEFAULT_NUMBER_VALUE)) {
            if (sizeof($heightArray1) == 0) {
                $heightArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Height', 'data' => array())));
            }
            $index = sizeof($heightArray1['labels']);
            $heightArray1['labels'][$index] = $time;
            $heightArray1['datasets'][0]['data'][$index] = $height;
            $summaryLegendArray1[$__HEIGHT][$index] = $attendedBy;
        }
        if (!is_null($bmi) && ($bmi != Triage::$__DEFAULT_NUMBER_VALUE)) {
            if (sizeof($bmiArray1) == 0) {
                $bmiArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Body Mass Index', 'data' => array())));
            }
            $index = sizeof($bmiArray1['labels']);
            $bmiArray1['labels'][$index] = $time;
            $bmiArray1['datasets'][0]['data'][$index] = $bmi;
            $summaryLegendArray1[$__BMI][$index] = $attendedBy;
        }
        if (!is_null($temperature) && ($temperature != Triage::$__DEFAULT_NUMBER_VALUE)) {
            if (sizeof($temperatureArray1) == 0) {
                $temperatureArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Temperature', 'data' => array())));
            }
            $index = sizeof($temperatureArray1['labels']);
            $temperatureArray1['labels'][$index] = $time;
            $temperatureArray1['datasets'][0]['data'][$index] = $temperature;
            $summaryLegendArray1[$__TEMPERATURE][$index] = $attendedBy;
        }
        if (!is_null($bloodPressure) && ($bloodPressure != Triage::$__DEFAULT_BP_VALUE)) {
            if (sizeof($bloodPressureArray1) == 0) {
                $bloodPressureArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Systolic Blood Pressure', 'data' => array()), array('label' => 'Dystolic Blood Pressure', 'data' => array())));
            }
            try {
                $index = sizeof($bloodPressureArray1['labels']);
                $bloodPressure1 = new BloodPressure($bloodPressure);
                $bloodPressureArray1['labels'][$index] = $time;
                $bloodPressureArray1['datasets'][0]['data'][$index] = $bloodPressure1->getSystolicValue();
                $bloodPressureArray1['datasets'][1]['data'][$index] = $bloodPressure1->getDystolicValue();  
                $summaryLegendArray1[$__BLOOD_PRESSURE][$index] = $attendedBy; 
            } catch (Exception $e)  {}
        }
        if (!is_null($pulseRate) && ($pulseRate != Triage::$__DEFAULT_NUMBER_VALUE)) {
            if (sizeof($pulseRateArray1) == 0) {
                $pulseRateArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Pulse Rate', 'data' => array())));
            }
            $index = sizeof($pulseRateArray1['labels']);
            $pulseRateArray1['labels'][$index] = $time;
            $pulseRateArray1['datasets'][0]['data'][$index] = $pulseRate;
            $summaryLegendArray1[$__PULSE_RATE][$index] = $attendedBy;
        }
        if (!is_null($respirationRate) && ($respirationRate != Triage::$__DEFAULT_NUMBER_VALUE)) {
            if (sizeof($respirationRateArray1) == 0) {
                $respirationRateArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Respiration Rate', 'data' => array())));
            }
            $index = sizeof($respirationRateArray1['labels']);
            $respirationRateArray1['labels'][$index] = $time;
            $respirationRateArray1['datasets'][0]['data'][$index] = $respirationRate;
            $summaryLegendArray1[$__RESPIRATION_RATE][$index] = $attendedBy;
        }
        if (!is_null($oxygenLevel) && ($oxygenLevel != Triage::$__DEFAULT_NUMBER_VALUE)) {
            if (sizeof($oxygenLevelArray1) == 0) {
                $oxygenLevelArray1 = array('labels' => array(), 'datasets' => array(array('label' => 'Oxygen Saturation Level', 'data' => array())));
            }
            $index = sizeof($oxygenLevelArray1['labels']);
            $oxygenLevelArray1['labels'][$index] = $time;
            $oxygenLevelArray1['datasets'][0]['data'][$index] = $oxygenLevel;
            $summaryLegendArray1[$__OXYGEN_LEVEL][$index] = $attendedBy;
        }
    }
    $finalArray1 = array();
    if (sizeof($weightArray1) > 0) $finalArray1[$__WEIGHT] = $weightArray1;
    if (sizeof($heightArray1) > 0) $finalArray1[$__HEIGHT] = $heightArray1;
    if (sizeof($temperatureArray1) > 0) $finalArray1[$__TEMPERATURE] = $temperatureArray1;
    if (sizeof($bloodPressureArray1) > 0) $finalArray1[$__BLOOD_PRESSURE] = $bloodPressureArray1;
    if (sizeof($pulseRateArray1) > 0) $finalArray1[$__PULSE_RATE] = $pulseRateArray1;
    if (sizeof($respirationRateArray1) > 0) $finalArray1[$__RESPIRATION_RATE] = $respirationRateArray1;
    if (sizeof($oxygenLevelArray1) > 0) $finalArray1[$__OXYGEN_LEVEL] = $oxygenLevelArray1;
    if (sizeof($bmiArray1) > 0) $finalArray1[$__BMI] = $bmiArray1;
    return $finalArray1;
}
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <?= $pageTitleArray1[$currentTab] ?>
                </div>
                <div class="card-body">
                    <?php
                    $nextPage = $thispage . "?page=triage";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $patient1 = new Patient("Delta", $_REQUEST['patientId'], $conn);
                        //Get List of Triages
                        $listOfVitalSigns = VitalSigns::getListOfVitalSignsForPatient($conn, $patient1->getCurrentVisit());
                        if (is_null($listOfVitalSigns)) {
                            echo UICardView::getDangerReportCard("Vital Signs", "There is no previous vital signs for the patient");
                        } else {
                            $prefix = "__ul_by_hkix__";
                            $prefix2 = "__alt_ui_bf__";
                            $legendArray1 = array();
                            $chartDataArray1 = getChartData($listOfVitalSigns, $legendArray1);
                    ?>
                            <div class="tab-container container">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="#<?= $prefix ?><?= $__REPORT ?>" class="nav-link <?= ($currentTab == $__REPORT) ? "active" : "" ?>" data-bs-toggle="tab" tab-index="<?= $__REPORT ?>">Vital Signs Reports</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#<?= $prefix ?><?= $__GRAPH ?>" class="nav-link <?= ($currentTab == $__GRAPH) ? "active" : "" ?>" data-bs-toggle="tab" tab-index="<?= $__GRAPH ?>">Vital Signs Graphs</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="<?= $prefix ?><?= $__REPORT ?>" class="tab-pane fade <?= ($currentTab == $__REPORT) ? "show active" : "" ?>">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>Created On</th>
                                                        <th>Updated On</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $pdfpage = (($profile1->getBaseURL()) . "/documents/pdf/__get_document__.php");
                                                    $pdfpage = str_replace((DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR, $pdfpage);
                                                    foreach ($listOfVitalSigns as $index => $vitalSigns1) {
                                                        $link1 = UIControls::getAnchorTag("View Report", $pdfpage, array(
                                                            "id" => ($vitalSigns1->getId0()),
                                                            "class" => ($vitalSigns1->getMyClassname()),
                                                            "dtype" => (Documents::$__VITAL_SIGNS)
                                                        ), array('card-link'), null, array('target' => '_blank'));
                                                    ?>
                                                        <tr>
                                                            <th><?= $index + 1 ?></th>
                                                            <td><?= $vitalSigns1->getTimeOfCreation()->getDateAndTimeString() ?></td>
                                                            <td><?= $vitalSigns1->getTimeOfUpdation()->getDateAndTimeString() ?></td>
                                                            <td><?= $link1 ?></td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="<?= $prefix ?><?= $__GRAPH ?>" class="tab-pane fade <?= ($currentTab == $__GRAPH) ? "show active" : "" ?>">
                                        <!--Begin Inner Tabs -->
                                        <div id="accordion" class="my-2">
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__WEIGHT ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__WEIGHT ?>" aria-expanded="true" arial-controls="<?= $prefix2 ?><?= $__WEIGHT ?>">Weight</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__WEIGHT ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__WEIGHT ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Weight Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__WEIGHT])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__WEIGHT], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__WEIGHT]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Weight Content-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__HEIGHT ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__HEIGHT ?>" aria-expanded="false" arial-controls="<?= $prefix2 ?><?= $__HEIGHT ?>">Height</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__HEIGHT ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__HEIGHT ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Height Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__HEIGHT])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__HEIGHT], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__HEIGHT]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Height Content-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__BMI ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__BMI ?>" aria-expanded="false" arial-controls="<?= $prefix2 ?><?= $__BMI ?>">Body Mass Index (BMI)</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__BMI ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__BMI ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Body Mass Index (BMI) Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__BMI])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__BMI], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__BMI]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Body Mass Index (BMI) Content-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__TEMPERATURE ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__TEMPERATURE ?>" aria-expanded="false" arial-controls="<?= $prefix2 ?><?= $__TEMPERATURE ?>">Temperature</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__TEMPERATURE ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__TEMPERATURE ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Temperature Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__TEMPERATURE])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__TEMPERATURE], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__TEMPERATURE]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Temperature Content-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__BLOOD_PRESSURE ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__BLOOD_PRESSURE ?>" aria-expanded="false" arial-controls="<?= $prefix2 ?><?= $__BLOOD_PRESSURE ?>">Blood Pressure</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__BLOOD_PRESSURE ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__BLOOD_PRESSURE ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Blood Pressure Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__BLOOD_PRESSURE])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__BLOOD_PRESSURE], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__BLOOD_PRESSURE]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Blood Pressure Content-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__PULSE_RATE ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__PULSE_RATE ?>" aria-expanded="false" arial-controls="<?= $prefix2 ?><?= $__PULSE_RATE ?>">Pulse Rate</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__PULSE_RATE ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__PULSE_RATE ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Pulse Rate Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__PULSE_RATE])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__PULSE_RATE], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__PULSE_RATE]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Pulse Rate Content-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__RESPIRATION_RATE ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__RESPIRATION_RATE ?>" aria-expanded="false" arial-controls="<?= $prefix2 ?><?= $__RESPIRATION_RATE ?>">Respiration Rate</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__RESPIRATION_RATE ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__RESPIRATION_RATE ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Respiration Rate Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__RESPIRATION_RATE])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__RESPIRATION_RATE], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__RESPIRATION_RATE]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Respiration Rate Content-->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mb-2">
                                                <div id="<?= $prefix2 ?><?= $__OXYGEN_LEVEL ?>header" class="card-header bg-dark">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link text-white" data-toggle="collapse" data-target="#<?= $prefix2 ?><?= $__OXYGEN_LEVEL ?>" aria-expanded="false" arial-controls="<?= $prefix2 ?><?= $__OXYGEN_LEVEL ?>">Oxygen Saturation Level</button>
                                                    </h5>
                                                </div>
                                                <div id="<?= $prefix2 ?><?= $__OXYGEN_LEVEL ?>" class="collapse" aria-labelledby="<?= $prefix2 ?><?= $__OXYGEN_LEVEL ?>header" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <!--Begin Oxygen Saturation Level Content-->
                                                        <div>
                                                            <?php
                                                            if (isset($chartDataArray1[$__OXYGEN_LEVEL])) {
                                                                echo  ChartEngine::getChart(600, 400, array(
                                                                    'type' => 'line',
                                                                    'options' => array(
                                                                        'responsive' => true,
                                                                        'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
                                                                    )
                                                                ), $chartDataArray1[$__OXYGEN_LEVEL], null, 3, 12, array('x-axis' => array(array('caption' => 'Attended By', 'data' => $legendArray1[$__OXYGEN_LEVEL]))));
                                                            }
                                                            ?>
                                                        </div>
                                                        <!--End Oxygen Saturation Level Content-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--End Inner Tabs-->
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                        //Add New Triage
                        $link1 = UIControls::getAnchorTag("Record Vital Signs", $thispage, array(
                            "page" => "vitalsigns_create",
                            "patientId" => ($patient1->getPatientId())
                        ), array("btn", "btn-primary", "add-record"), array(), array());
                        echo "<div class=\"my-1 text-right\">$link1</div>";
                        $conn = null;
                    } catch (Exception $e) {
                        echo UICardView::getDangerReportCard("Vital Signs", $e->getMessage());
                    }
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Triage</a></i><br />
                        <span class="text-muted"><i>Rule: vitalsigns</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        $(function() {
            window.setTabbedNavigation($('div.tab-container'), <?= $currentTab ?>);
            window.setTabbedNavigation($('div.tab-container-2'), <?= $innerTab ?>);
        });
    })(jQuery);
</script>