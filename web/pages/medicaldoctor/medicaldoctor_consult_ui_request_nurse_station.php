<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("NurseStation".( $systemTime1->getTimestamp() ), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (MedicalDoctorConsultationQueue::$__TAB_NURSE_STATION))) {
    $conn->beginTransaction();
    $dbTransactionON = true;
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_POST['qid'], $conn);
    if ($_POST['efilter'] != $medicalDoctorQueue1->getNurseStationFilter()) throw new Exception("Multiple Submission for same queue detected");
    $medicalDoctorQueue1->setNurseStationFilter($bundleCode)->update(false);
    //We need to write to ExaminationQueue, however notify payment not yet
    $colArray1 = $medicalDoctorQueue1->getMyPayload(array('queueName', 'visit', 'patientCase', 'patient'));
    $colArray1 = array_merge($_POST, $colArray1);
    $colArray1['timeOfCreation'] = $colArray1['timeOfUpdation'] = $systemTime1->getTimestamp();
    $colArray1['pendingPayment'] = 1;
    $colArray1['currentAttendanceSequence'] = 0;
    $colArray1['requestedBy'] = $login1->getLoginId();
    $colArray1['bundleCode'] = $bundleCode;
    $colArray1['pendingPayment'] = 1;
    $colArray1['completed'] = 0;

    $nurseStationQueue1 = new NurseStationQueue("Hello", __data__::insert($conn, "NurseStationQueue", $colArray1, !$dbTransactionON, Constant::$default_select_empty_value), $conn);
    //Now proceed with invoice settings
    //We just need to raise an invoice
    $caption = "Nurse Station";
    $patient1 = $medicalDoctorQueue1->getPatient();
    //Step 01: Updating case
    $case1 = $medicalDoctorQueue1->getPatientCase();
    $visit1 = $medicalDoctorQueue1->getVisit();
    //We need to save the patientExaminationQueue instance to visit
    //$visit1->setTemporaryObjectHolder($nurseStationQueue1->getObjectReferenceString())->update(! $dbTransactionON);
    //Instead of a visit we need to save nurseStationQueue instance to PatientMovementStageMonitor , we will need while issuing receipt 
    __data__::insert($conn, "PatientMovementStageMonitor", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "stage" => (PatientMovementStage::$__NURSE_STATION),
        "bundleCode" => $bundleCode,
        "temporaryObjectHolder" => $nurseStationQueue1->getObjectReferenceString()
    ), !$dbTransactionON);
    $nurseStationQueue1->setMedicalDoctorConsultationQueue($medicalDoctorQueue1->getQueueId())->setTemporaryObjectHolder($medicalDoctorQueue1->getObjectReferenceString())->setTemporaryStringHolder($bundleCode)->update(!$dbTransactionON);
    //Step 02: Update PatientLog (may be useless)
    __data__::insert($conn, "PatientLog", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "attendedBy" => $login1->getLoginName(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "patientVisitReference" => $visit1->getObjectReferenceString(),
        "actionStage" => PatientMovementStage::$__NURSE_STATION,
        "patientName" => $patient1->getPatientName(),
        "caption" => $caption
    ), false, null);
    //Step 03: Patient Finance Queue
    __data__::insert($conn, "PatientFinanceQueue", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "listOfServices" => $_POST['service'],
        "actionStage" => (PatientMovementStage::$__NURSE_STATION),
        "temporaryObjectHolder" => $nurseStationQueue1->getObjectReferenceString(),
        "temporaryIntegerHolder" => intval($_POST['maximumNumberOfAttendance']),
        "comments" => $nurseStationQueue1->getComments(),
        "bundleCode" => $bundleCode,
        "trackMonitor" => $bundleCode
    ), !$dbTransactionON);
    //Step 04: Set Pending Payment
    $medicalDoctorQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_NURSE_STATION_PENDING_PAYMENT)->setOnNurseStation(true)->update(!$dbTransactionON);
    //Step 05.pre -- addPatientLog 
    PatientFile::addNurseStationQueueLog($conn, $systemTime1, $visit1, $login1, $nurseStationQueue1, $nurseStationQueue1->getTemporaryStringHolder(), !$dbTransactionON);
    //Step 05: General Log 
    $caption = $caption . "[ " . $patient1->getPatientName() . " ]";
    $conn->commit();
    $dbTransactionON = false;
    SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $page, $caption);
    //Successful Window
    echo UICardView::getSuccesfulReportCard($caption, "Once the payment is done, the patient can proceed to the respective nursing station");
} else {
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_REQUEST['qid'], $conn);
    $medicalDoctorQueue1->setNurseStationFilter($bundleCode)->update(true);
?>
    <div class="nurse-station-portal border border-primary p-1 m-1">
        <div class="bg-primary text-white">
            <h4>Nurse Station</h4>
        </div>
        <div class="nurse-station-content">
            <?php
            $listOfMonitors = PatientMovementStageMonitor::getAllMonitorsForAStage($conn, $medicalDoctorQueue1->getVisit()->getVisitId(), (PatientMovementStage::$__NURSE_STATION));
            $listOfNurseStationQueues = NurseStationQueue::getNurseStationQueuesForMedicalConsultationQueue($conn, $medicalDoctorQueue1->getQueueId());
            if (!is_null($listOfNurseStationQueues)) {
                $window1 = NurseStationActivity::getActivityUIForNurseStationQueue($thispage, $conn, $listOfNurseStationQueues);
                echo UIView::wrap($window1);
                echo "<br/>";
            }
            if (!is_null($listOfMonitors)) {
                //throw new Exception("Kindly pay first for the previous Nurse Items");
                echo UICardView::getDangerReportCard("Pending Payment", "There is a pending payment for previous item");
            } else {
                echo UIView::wrap(__data__::createDataCaptureForm($thispage, "NurseStationQueue", array(
                    array('pname' => 'service', 'caption' => 'Service', 'required' => true, 'filter' => array('category' => array((ServiceCategory::$__NURSE_STATION)))),
                    array('pname' => 'maximumNumberOfAttendance', 'caption' => 'Number of Times', 'required' => true, 'placeholder' => 1, 'value' => 1, 'title' => 'How many times this service should be performed'),
                    array('pname' => 'comments', 'caption' => 'Additional Instructions', 'title' => 'Example Morning and Evening', 'required' => false)
                ), "Request Nurse Attention", "create", $conn, 0, array(
                    "page" => $page,
                    "qid" => $_REQUEST['qid'],
                    "qtype" => (MedicalDoctorConsultationQueue::$__TAB_NURSE_STATION),
                    "tabbedNavigationIndex" => (MedicalDoctorConsultationQueue::$__TAB_NURSE_STATION),
                    "efilter" => $medicalDoctorQueue1->getNurseStationFilter(),
                    "submit" => 1
                ), null, null, "my-own-way", $thispage, true));
            }
            ?>
        </div>
    </div>
<?php
}
?>
