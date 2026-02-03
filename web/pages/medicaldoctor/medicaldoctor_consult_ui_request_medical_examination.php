<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("PatientExaminationQueue".( $systemTime1->getTimestamp() ), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (MedicalDoctorConsultationQueue::$__TAB_MEDICAL_EXAMINATION))) {
    if (sizeof($_POST['listOfServices']) == 0) throw new Exception("You must select at least a service for Examination");
    $conn->beginTransaction();
    $dbTransactionON = true;    
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_POST['qid'], $conn);
    if ($_POST['efilter'] != $medicalDoctorQueue1->getMedicalExaminationFilter()) throw new Exception("Multiple Submission for same queue detected");
    if ($medicalDoctorQueue1->isFlagSetAt(MedicalDoctorConsultationQueue::$__FLAG_LABORATORY_EXAMINATION_PENDING_PAYMENT)) throw new Exception("The Laboratory requested has been processed, there is a pending payment which need to be cleared");
    $medicalDoctorQueue1->setMedicalExaminationFilter($bundleCode)->update(false);
    //We need to write to ExaminationQueue, however notify payment not yet
    $gcolArray1 = $medicalDoctorQueue1->getMyPayload(array('queueName', 'visit', 'patientCase', 'patient'));
    $colArray1 = $gcolArray1;
    $colArray1 = array_merge($_POST, $colArray1);
    $colArray1['timeOfCreation'] = $colArray1['timeOfUpdation'] = $systemTime1->getTimestamp();
    $colArray1['pendingPayment'] = 1;
    $colArray1['requestedBy'] = $login1->getLoginId();
    $colArray1['bundleCode'] = $bundleCode;
    //Putting properly listOfServices
    $colArray1['listOfServices'] = implode(",", $colArray1['listOfServices']);
    //Dealing with comments 
    unset($colArray1['comments']);
    if ($_POST['comments'] != "") {
        $tcolArray1 = array_merge($gcolArray1, array(
            "comments" => $_POST['comments'],
            "bundleCode" => $bundleCode
        ));
        $colArray1['comments'] = __data__::insert($conn, "MedicalComment", $tcolArray1, false);
    }
    $patientExaminationQueue1 = new PatientExaminationQueue("Hello", __data__::insert($conn, "PatientExaminationQueue", $colArray1, false, Constant::$default_select_empty_value), $conn);
    //Now work for subqueue 
    foreach ($patientExaminationQueue1->getListOfServices() as $service1)   {
        $colArray1 = array(
           "timeOfCreation" => $systemTime1->getTimestamp(),
           "timeOfUpdation" => $systemTime1->getTimestamp(),
           "examinationQueue" => $patientExaminationQueue1->getQueueId(),
           "service" => $service1->getServiceId() 
        );
        switch ($service1->getCategory()->getCategoryId())  {
            case (ServiceCategory::$__LABORATORY_EXAMINATION):
                __data__::insert($conn, "QueueNotifyWetLab", $colArray1, false);
                break;
            case (ServiceCategory::$__ULTRA_SOUND):
                __data__::insert($conn, "QueueNotifyUltrasound", $colArray1, false);
                break;
            case (ServiceCategory::$__PLAIN_CONVENTION_X_RAY):
                __data__::insert($conn, "QueueNotifyPlainXRAY", $colArray1, false);
                break;
            case (ServiceCategory::$__CONTRAST_STUDIES):
                break;
        }
    }
    //Now proceed with invoice settings
    //We just need to raise an invoice
    $caption = "Laboratory Examination";
    $patient1 = $medicalDoctorQueue1->getPatient();
    //Step 01: Updating case
    $case1 = $medicalDoctorQueue1->getPatientCase();
    //$case1->setCurrentStage(PatientMovementStage::$__MEDICAL_DOCTOR_CONSULTATION)->update(false);
    $visit1 = $medicalDoctorQueue1->getVisit();
    //We need to save the patientExaminationQueue instance to visit
    //$visit1->setTemporaryObjectHolder($patientExaminationQueue1->getObjectReferenceString())->update(false);
    //Instead of a visit we need to save patientExaminationQueue instance to PatientMovementStageMonitor , we will need while issuing receipt 
    __data__::insert($conn, "PatientMovementStageMonitor", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "stage" => (PatientMovementStage::$__LABORATORY_EXAMINATION),
        "bundleCode" => $bundleCode,
        "temporaryObjectHolder" => $patientExaminationQueue1->getObjectReferenceString()
    ),false);
    $patientExaminationQueue1->setMedicalDoctorConsultationQueue($medicalDoctorQueue1->getQueueId())->setTemporaryObjectHolder($medicalDoctorQueue1->getObjectReferenceString())->setTemporaryStringHolder($bundleCode)->update(false);
    //Step 02: Update PatientLog
    __data__::insert($conn, "PatientLog", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "attendedBy" => $login1->getLoginName(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "patientVisitReference" => $visit1->getObjectReferenceString(),
        "actionStage" => PatientMovementStage::$__LABORATORY_EXAMINATION,
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
        "listOfServices" => implode(",", $_POST['listOfServices']),
        "actionStage" => (PatientMovementStage::$__LABORATORY_EXAMINATION),
        "temporaryObjectHolder" => $patientExaminationQueue1->getObjectReferenceString(),
	    "bundleCode" => $bundleCode,
        "trackMonitor" => $bundleCode
    ), false);
    //Step 04: Set Pending Payment 
    $medicalDoctorQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_LABORATORY_EXAMINATION_PENDING_PAYMENT)->setOnMedicalExamination("1")->update(false);
    //Step 05.pre -- addPatientLog 
    PatientFile::addExaminationQueueLog($conn, $systemTime1, $visit1, $login1, $patientExaminationQueue1, $bundleCode, false);
    //Step 05: General Log 
    $caption = $caption . "[ " . $patient1->getPatientName() . " ]";
    $conn->commit();
    $dbTransactionON = false;
    SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $page, $caption);
    //Successful Window
    echo UICardView::getSuccesfulReportCard($caption, "Once the payment is done, the patient can proceed to the respective examination laboratory");
} else {
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_REQUEST['qid'], $conn);
    $medicalDoctorQueue1->setMedicalExaminationFilter($bundleCode)->update(true);
?>
    <div class="examination-portal border border-primary p-1 m-1">
        <div class="bg-primary text-white">
            <h4>Examination</h4>
        </div>
        <div class="examination-content">
        <?php 
        if ($medicalDoctorQueue1->isOnMedicalExamination() || $medicalDoctorQueue1->isOnExaminationResultsVerification()) {
            //echo __data__::showPrimaryAlert("The patient is still on previous Medical Examination");
            //We may need to add Approved Sequence for this 
            //For sure here we have a PatientExaminationQueue Already set 
            $listOfExaminationQueues = PatientExaminationQueue::getExaminationQueuesForMedicalConsultationQueue($conn, $medicalDoctorQueue1->getQueueId());
            if (! is_null($listOfExaminationQueues))    {
                $window1 = ""; $count = 0;
                foreach ($listOfExaminationQueues as $examinationQueue1)    {
                    $t1 = PatientExaminationResults::getResultsUIForExaminationQueue($thispage, $conn, $examinationQueue1->getQueueId());
                    if ($count == 0) $window1 = $t1;
                    else $window1 .= "<br/>$t1";
                    $count++;
                }
                echo UIView::wrap($window1);
            }
        } else {
            echo __data__::createDataCaptureForm($thispage, "PatientExaminationQueue", array(
                array('pname' => 'preliminaryExamination', 'caption' => 'Preliminary Examination', 'required' => true, 'placeholder' => 'Malaria or Typhoid', 'title' => 'You should write what you suspect prior further examination'),
                array('pname' => 'comments' , 'use-class'=> 'MedicalComment', 'type' => 'ckeditor', 'caption' => 'Medical Comments', 'required' => true, 'placeholder' => 'High Fever', 'title' => 'You should add your notes concerning the patients condition'),
                array('pname' => 'listOfServices', 'include-columns' => array('serviceName' => array('caption' => 'Service Name'), 'currency' => array('caption' => 'Currency', 'map' => 'Currency.code'), 'amount' => array('caption' => 'Amount')), 'caption' => 'Service Selection', 'required' => true, 'filter' => array("category" => array((ServiceCategory::$__LABORATORY_EXAMINATION), (ServiceCategory::$__PLAIN_CONVENTION_X_RAY), (ServiceCategory::$__ULTRA_SOUND))))
                //Add listOfService the drag and drop UI -- 
            ), "Request Further Examination", "create", $conn, 0, array(
                "page" => $page,
                "qid" => $_REQUEST['qid'],
                "qtype" => (MedicalDoctorConsultationQueue::$__TAB_MEDICAL_EXAMINATION),
                "tabbedNavigationIndex" => (MedicalDoctorConsultationQueue::$__TAB_MEDICAL_EXAMINATION),
                "efilter" => $medicalDoctorQueue1->getMedicalExaminationFilter(),
                "submit" => 1
            ), null, null, "my-own-way", $thispage, true);
        }
        ?>
        </div>
    </div>
<?php
}
?>
