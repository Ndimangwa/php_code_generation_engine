<?php
$erollback = false;
$conn = null;
$consultationQueue1 = null;
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $conn->beginTransaction();
    $erollback = true;
    //We need to get the right consultationQueue1, not mid now is carrying ConsultationQueueManager.managerId
    $consultationQueueManager1 = new ConsultationQueueManager("Delta", $_REQUEST['mid'], $conn);
    $consultationQueue1 = isset($_REQUEST['qid']) ? ( new MedicalDoctorConsultationQueue("Delta", $_REQUEST['qid'], $conn) ) : ( MedicalDoctorConsultationQueue::getConsultationQueueForAccount($conn, $login1->getLoginId(), $consultationQueueManager1->getManagerId()) );
    if (is_null($consultationQueue1)) {
        //We need to build a new one
        if (MedicalDoctorConsultationQueue::canCreateNewConsultationQueue($conn, $consultationQueueManager1->getManagerId())) {
            $consultationQueue1 = new MedicalDoctorConsultationQueue("Delta", __data__::insert($conn, "MedicalDoctorConsultationQueue", array_merge($consultationQueueManager1->getMyPayload(array("visit", "patientCase", "patient", "bundleCode")), array(
                "timeOfCreation" => $systemTime1->getTimestamp(),
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "medicalDoctor" => $consultationQueueManager1->getVisit()->getMedicalDoctor()->getDoctorId(),
                "queueName" => $consultationQueueManager1->getManagerName(),
                "attendedBy" => $login1->getLoginId(),
                "attended" => 0,
                "active" => 1,
                "queueManager" => $consultationQueueManager1->getManagerId(),
                "applicationCounter" => (MedicalDoctorConsultationQueue::$__BLOCK_PATIENT_HISTORY)
            )), !$erollback), $conn);
        } else {
            throw new Exception("Can not create Consultation Queue, perhaps already exists");
        }
    }
    if (is_null($consultationQueue1)) throw new Exception("Consultation Queue, Unknown error has occured");
    if ($consultationQueue1->getPatientCase()->isClosed()) {
        throw new Exception("This case for this patient is already closed");
    }
    //Now pulling previous saved-values
    $patientHistory1 = PatientHistory::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $generalExamination1 = GeneralExamination::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $vitalSigns1 = VitalSigns::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $localExamination1 = LocalExamination::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $systemicExamination1 = SystemicExamination::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $provisionDiagnosis1 = ProvisionDiagnosis::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $examinationQueue1 = PatientExaminationQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $workingDiagnosis1 = WorkingDiagnosis::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $patientDrugQueue1 = PatientDrugQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $patientAdmissionQueue1 = PatientAdmissionQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    $patientOperationQueue1 = PatientOperationQueue::getWorkingBlockForConsultationQueue($conn, $consultationQueue1->getQueueId());
    if (isset($_POST['new-sheet']) && isset($_POST['efilter'])) {
        if ($_POST['efilter'] != $consultationQueue1->getExtraFilter()) throw new Exception("Perhaps multiple submission for the browser");
        $consultationQueue1->setExtraFilter(__object__::getMD5CodedString("Delta", 32))->update(!$erollback);  
        //Work at this point
        //Step 1: Disable the current 
         $consultationQueue1->setActive(false)->update(! $erollback);
        //Step 2: Check canCreate
        if (MedicalDoctorConsultationQueue::canCreateNewConsultationQueue($conn, $consultationQueueManager1->getManagerId())) {
            $consultationQueue1 = new MedicalDoctorConsultationQueue("Delta", __data__::insert($conn, "MedicalDoctorConsultationQueue", array_merge($consultationQueueManager1->getMyPayload(array("visit", "patientCase", "patient", "bundleCode")), array(
                "timeOfCreation" => $systemTime1->getTimestamp(),
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "medicalDoctor" => $consultationQueueManager1->getVisit()->getMedicalDoctor()->getDoctorId(),
                "queueName" => $consultationQueueManager1->getManagerName(),
                "attendedBy" => $login1->getLoginId(),
                "attended" => 0,
                "active" => 1,
                "queueManager" => $consultationQueueManager1->getManagerId(),
                "applicationCounter" => (MedicalDoctorConsultationQueue::$__BLOCK_PATIENT_HISTORY)
            )), !$erollback), $conn);
        } else {
            throw new Exception("Can not create Consultation Queue, perhaps previous not activated");
        }
        //Step 3: Do create AnchorTag
        $link1 = UIControls::getAnchorTag("Click here to proceed to New Sheet", $thispage, array(
            "page" => $page,
            "mid" => ( $consultationQueueManager1->getManagerId() ),
            "qid" => ( $consultationQueue1->getQueueId() )
        ), array("card-link"), array("text-align: center"));
        //You need to create an AnchorTag to proceed
        echo UICardView::getSuccesfulReportCard("New Sheet Creation", "You have successful created a new sheet. <br/> $link1");
    } else if (isset($_POST['submit']) && isset($_POST['efilter'])) {
        if ($_POST['efilter'] != $consultationQueue1->getExtraFilter()) throw new Exception("Perhaps multiple submission for the browser");
        $consultationQueue1->setExtraFilter(__object__::getMD5CodedString("Delta", 32))->update(!$erollback);
        //Now build colArray1
        $colArray1 = array_merge($consultationQueue1->getMyPayload(array("queueName", "visit", "patientCase", "patient", "bundleCode")), array(
            "timeOfCreation" => ($systemTime1->getTimestamp()),
            "timeOfUpdation" => ($systemTime1->getTimestamp()),
            "consultationQueue" => ($consultationQueue1->getQueueId()),
            "attendedBy" => ( $login1->getLoginId() ),
            "ownerReference" => ( $consultationQueue1->getObjectReferenceString() )
        ));
        //Handling Submission --begin
        include("submit_consultation_main.php");
        //Handling Submission --end
        //Now Updating the attended status
        if (!$consultationQueue1->isAttended()) {
            $consultationQueue1->setAttendedBy($login1->getLoginId())->setAttended(true)->update(!$erollback);
        }
        //Now Display successful 
        echo UICardView::getSuccesfulReportCard("Consultation UI", "You have successful submitted patient data");
    } else {
        $vitalSigns1 = is_null($vitalSigns1) ? (Triage::getTriageForConsultationQueue($conn, $consultationQueue1)) : $vitalSigns1;
        $listOfDifferentialDiseases = array();
        if (!is_null($provisionDiagnosis1)) {
            if (!is_null($provisionDiagnosis1->getMainDisease())) $listOfDifferentialDiseases[0] = $provisionDiagnosis1->getMainDisease();
            if (!is_null($provisionDiagnosis1->getListOfDifferentialDiseases())) $listOfDifferentialDiseases = array_merge($listOfDifferentialDiseases, $provisionDiagnosis1->getListOfDifferentialDiseases());
        }
        //My UI -Page
        $consultationQueue1->setExtraFilter(__object__::getMD5CodedString("Delta", 32))->update(!$erollback);
        $controlDisabled = false;
        include("ui_consultation_main.php");
    }
    $conn->commit();
    $erollback = false;
    $conn = null;
} catch (Exception $e) {
    if (!is_null($conn) && $erollback) $conn->rollBack();
    //We need to build link
    $message = $e->getMessage();
    $message = "<div><div>$message</div><div>If there is any pending payment should be paid first</div></div>";
    $link1 = UIControls::getAnchorTag("Reload Page", $thispage, array(
        "page" => $page,
        "mid" => (is_null($consultationQueueManager1) ? $_REQUEST['mid'] : ($consultationQueueManager1->getManagerId()))
    ), array("card-link"), array("text-align: center"));
    $window1 = "<div><div>$message</div><div>$link1</div></div>";
    echo UICardView::getDangerReportCard("Medical Consultation", $window1);
}
?>
