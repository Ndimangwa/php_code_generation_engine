<?php
//First-Filter
$temperature = "human-temperature-celsius";
$respiratory = "human-respiratory-count";
$pulse = "human-pulse-count";
$bloodPressure = "human-blood-pressure";
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("Anaesthesia Record" . ($systemTime1->getTimestamp()), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (Theatre::$__TAB_ANAESTHESIA))) {
    $conn->beginTransaction();
    $erollback = true;
    if ($_POST['efilter'] != $queue1->getFirstFilter()) throw new Exception("Multiple Submission was detected of the same data");
    $queue1->setFirstFilter($bundleCode)->update(!$erollback);
    //1. common colArray1
    $colArray1 = array(
        "timeOfCreation" => ($systemTime1->getTimestamp()),
        "timeOfUpdation" => ($systemTime1->getTimestamp()),
        "login" => ($login1->getLoginId()),
        "group" => $analyticsGroup,
        "referenceString" => ($queue1->getObjectReferenceString()),
        "flags" => 0
    );
    //2. Extract none-empty fields and format properly
    $temperature = trim($_POST['temperature']);
    $respirationRate = trim($_POST['respirationRate']);
    $pulseRate = trim($_POST['pulseRate']);
    $bloodPressure = trim($_POST['bloodPressure']);
    $valueArray1 = array();
    if ($temperature != "") $valueArray1[sizeof($valueArray1)] = array("type" => (Theatre::$__TYPE_TEMPERATURE), "value" => $temperature);
    if ($respirationRate != "") $valueArray1[sizeof($valueArray1)] = array("type" => (Theatre::$__TYPE_RESPIRATION_RATE), "value" => $respirationRate);
    if ($pulseRate != "") $valueArray1[sizeof($valueArray1)] = array("type" => (Theatre::$__TYPE_PULSE_RATE), "value" => $pulseRate);
    if ($bloodPressure != "") {
        $bloodPressure1 = new BloodPressure($bloodPressure);
        $valueArray1[sizeof($valueArray1)] = array("type" => ( Theatre::$__TYPE_BLOOD_PRESSURE_SYSTOLIC ), "value" => ( $bloodPressure1->getSystolicValue() ));
        $valueArray1[sizeof($valueArray1)] = array("type" => ( Theatre::$__TYPE_BLOOD_PRESSURE_DYSTOLIC ), "value" => ( $bloodPressure1->getDystolicValue() ));
    }
    //3. Record readings  -- will use float data analytics
    if (sizeof($valueArray1) == 0) throw new Exception("[ Empty data ] => None of data were recorded");
    foreach ($valueArray1 as $datablock1)   {
        __data__::insert($conn, "PreAnalyticsFloatData", array_merge($colArray1, $datablock1), ! $erollback, Constant::$default_select_empty_value);
    }
    //4. Successful report
    echo UICardView::getSuccesfulReportCard("Record Confirmation", "You have successful Recorded Data");
    $conn->commit();
    $erollback = false;
} else {
    $queue1->setFirstFilter($bundleCode)->update(!$erollback);
    $__WIDTH = 600;
    $__HEIGHT = 200;
    $__CHART_TYPE = "line";
?>
    <!--Get Graphs-->
    <div class="graphical-display">
        <div class="row container">
            <?php 
            function displayWindow($window1)    {
                return ($window1 == "" ? "" : ( "<div class=\"col-md-12\"><div class=\"border border-primary p-1\" style=\"position: relative;\">$window1</div></div>" ));
            }
            //Respiration
            $window1 = displayWindow(PreAnalyticsFloatData::getChartByTimeOfCreation($conn, $__WIDTH, $__HEIGHT, $__CHART_TYPE, "Respiration (bpm)", null, Theatre::$__TYPE_RESPIRATION_RATE, null, $queue1->getObjectReferenceString(), null, null, null));
            //Pulse Rate
            $window1 .= displayWindow(PreAnalyticsFloatData::getChartByTimeOfCreation($conn, $__WIDTH, $__HEIGHT, $__CHART_TYPE, "Pulse Rate (bpm)", null, Theatre::$__TYPE_PULSE_RATE, null, $queue1->getObjectReferenceString(), null, null, null));
            //Temperature deg Cent
            $window1 .= displayWindow(PreAnalyticsFloatData::getChartByTimeOfCreation($conn, $__WIDTH, $__HEIGHT, $__CHART_TYPE, "Temperature (deg Centrigrade)", null, Theatre::$__TYPE_TEMPERATURE, null, $queue1->getObjectReferenceString(), null, null, null));
            //Blood Pressure
            $window1 .= displayWindow(PreAnalyticsFloatData::getChartByTimeOfCreation($conn, $__WIDTH, $__HEIGHT, $__CHART_TYPE, "Pressure (mmHg)", array("Systolic (mmHg)", "Dystolic (mmHg)"), array(( Theatre::$__TYPE_BLOOD_PRESSURE_SYSTOLIC ), ( Theatre::$__TYPE_BLOOD_PRESSURE_DYSTOLIC )), null, $queue1->getObjectReferenceString(), null, null, null));
            //Display
            echo $window1;
            ?>
        </div>
    </div>
    <!--Record Data-->
    <div class="record-data card">
        <div class="card-header bg-primary">
            <div class="card-title">Anaesthesia Recording</div>
        </div>
        <div class="card-body">
            <?php 
            $submissionArray1 =  array(
                "page" => $page,
                "tabbedNavigationIndex" => (Theatre::$__TAB_ANAESTHESIA),
                "qid" => ($queue1->getQueueId()),
                "qtype" => (Theatre::$__TAB_ANAESTHESIA),
                "efilter" => ($queue1->getFirstFilter()),
                "submit" => 1
            );
            if ($analyticsGroup == ( Theatre::$__POST_OPERATION ))  {
                $submissionArray1['id'] = $operation1->getOperationId();
            }
            ?>
            <?= UIView::wrap(__data__::createDataCaptureForm($thispage, "Triage", array(
                array("pname" => "temperature", "required" => false, "caption" => "Temperature (deg C)", "placeholder" => "36.5", "validation" => array("rule" => (RegularExpression::getRule($temperature)), "message" => (RegularExpression::getMessage($temperature)))),
                array("pname" => "respirationRate", "required" => false, "caption" => "Respiration Rate (per minute)", "placeholder" => "16", "validation" => array("rule" => (RegularExpression::getRule($respiratory)), "message" => (RegularExpression::getMessage($respiratory)))),
                array("pname" => "pulseRate", "required" => false, "caption" => "Pulse Rate (per minute)", "placeholder" => "64", "validation" => array("rule" => (RegularExpression::getRule($pulse)), "message" => (RegularExpression::getMessage($pulse)))),
                array("pname" => "bloodPressure", "required" => false, "caption" => "Blood Pressure (mmHg)", "placeholder" => "120/80", "validation" => array("rule" => (RegularExpression::getRule($bloodPressure)), "message" => (RegularExpression::getMessage($bloodPressure))))
            ), "Record", "create", $conn, 0,$submissionArray1, null, null, "btn-operation", $thispage, true)) ?>
        </div>
        <div class="card-footer"></div>
    </div>
<?php
}
?>