<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("Admission" . ($systemTime1->getTimestamp()), 32);
$__SUBMIT_ADMISSION = 1;
$__SUBMIT_DISCHARGE = 2;
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (MedicalDoctorConsultationQueue::$__TAB_ADMISSION))) {
    $conn->beginTransaction();
    $dbTransactionON = true;
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_POST['qid'], $conn);
    if ($_POST['efilter'] != $medicalDoctorQueue1->getAdmissionFilter()) throw new Exception("Multiple Submission for same queue detected");
    $medicalDoctorQueue1->setAdmissionFilter($bundleCode)->update(! $dbTransactionON);
    if ($_POST['submit'] == $__SUBMIT_ADMISSION) {
        $listOfServices = (implode(",", $_POST['listOfServices']));
        $numberOfDays = $_POST['numberOfDays'];
        //Step 1: Preparing colArray1 payload
        $colArray1 = array_merge($medicalDoctorQueue1->getMyPayload(array("queueName", "patient", "patientCase", "visit")), array(
            "bundleCode" => $bundleCode,
            "timeOfCreation" => ($systemTime1->getTimestamp()),
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        ));
        //Step 2: Insert into PatientAdmissionQueue
        $tcolArray1 = $colArray1;
        if ($_POST['comments'] != "") {
            $tcolArray1 = array_merge($colArray1, array(
                "medicalComment" => (__data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                    "comments" => ($_POST['comments'])
                )), !$dbTransactionON))
            ));
        }
        $patientAdmissionQueue1 = new PatientAdmissionQueue("Hello", __data__::insert($conn, "PatientAdmissionQueue", array_merge($tcolArray1, array(
            "listOfServices" => $listOfServices,
            "numberOfDays" => $numberOfDays,
            "completed" => 0,
            "pendingPayment" => 1
        )), !$dbTransactionON, Constant::$default_select_empty_value), $conn);
        //Now working with PatientOperationQueue
        if (isset($_POST['theatre'])) {
            $timeOfAppointment = 0;
            try {
                $t1 = DateAndTime::createDateAndTimeFromGUIDate($_POST['timeOfAppointment']);
                $timeOfAppointment = $t1->getTimestamp();
            } catch (Exception $e)  {  

            }
            $patientOperationQueue1 = new PatientOperationQueue("Delta", __data__::insert($conn, "PatientOperationQueue", array_merge($tcolArray1, array(
                "listOfServices" => $listOfServices,
                "timeOfAppointment" => $timeOfAppointment,
                "theatre" => $_POST['theatre'],
                "duration" => $numberOfDays,
                "completed" => 0,
                "pendingPayment" => 1,
                "admissionQueue" => ( $patientAdmissionQueue1->getQueueId() ),
                "admissionQueueReference" => ($patientAdmissionQueue1->getObjectReferenceString())
            )), ! $dbTransactionON, Constant::$default_select_empty_value), $conn);
            //Update patientAdmissionQueue1
            $patientAdmissionQueue1->setInOperation(true)->setOperationQueueReference($patientOperationQueue1->getObjectReferenceString())->update(! $dbTransactionON);
        }
        //Step 3: Update PatientMovementStageMonitor
        __data__::insert($conn, "PatientMovementStageMonitor", array_merge($colArray1, array(
            "stage" => (PatientMovementStage::$__ADMISSION),
            "temporaryObjectHolder" => ($patientAdmissionQueue1->getObjectReferenceString())
        )), !$dbTransactionON);
        //I think this queue need to point to MedicalConsultationQueue
        //Step 4: Raise Invoice
        __data__::insert($conn, "PatientFinanceQueue", array_merge($colArray1, array(
            "listOfServices" => implode(",", $_POST['listOfServices']),
            "actionStage" => (PatientMovementStage::$__ADMISSION),
            "temporaryObjectHolder" => ($patientAdmissionQueue1->getObjectReferenceString()),
            "trackMonitor" => $bundleCode
        )), !$dbTransactionON);
        //Step 5: Update PatientFile
        PatientFile::addPatientAdmissionLog($conn, $systemTime1, $patientAdmissionQueue1->getVisit(), $login1, $patientAdmissionQueue1, $bundleCode, !$dbTransactionON);
        //Step 6: Flags Pending Payment
        $medicalDoctorQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_ADMISSION_PENDING_PAYMENT)->setOnAdmission(true)->update(!$dbTransactionON);
        //Step 7: UICardView
        echo UICardView::getSuccesfulReportCard("Admission Requested", "Once the payment is done, the patient can proceed to the admission procedure");
    } else if ($_POST['submit'] == $__SUBMIT_DISCHARGE) {
        $patient1 = $medicalDoctorQueue1->getPatient();
        //Step 1: Check if exists pending balance
        if ($patient1->hasPendingBalance()) throw new Exception($patient1->getBalanceStatusScreen());      
        //Step 2: Preparing colArray1 payload
        $colArray1 = array_merge($medicalDoctorQueue1->getMyPayload(array("queueName", "patient", "patientCase", "visit")), array(
            "timeOfCreation" => ($systemTime1->getTimestamp()),
            "timeOfUpdation" => ($systemTime1->getTimestamp())
        ));
        //Step 3: Insert into PatientDischargeQueue
        $tcolArray1 = $colArray1;
        if ($_POST['comments'] != "") {
            $tcolArray1 = array_merge($colArray1, array(
                "medicalComment" => (__data__::insert($conn, "MedicalComment", array_merge($colArray1, array(
                    "comments" => ($_POST['comments'])
                )), !$dbTransactionON))
            ));
        }
        //get PatientAdmission
        $patientAdmission1 = new PatientAdmission("Delta", $_POST['aid'], $conn);
        if ($patientAdmission1->isInOperation()) throw new Exception("Patient is still in Operation");
        $patientDischargeQueue1 = new PatientDischargeQueue("Delta", __data__::insert($conn, "PatientDischargeQueue", array_merge($tcolArray1, array(
            "admission" => ( $patientAdmission1->getAdmissionId() ),
            "bundleCode" => ( $patientAdmission1->getBundleCode() ),
            "pendingPayment" => 0,
            "completed" => 0
        )), ! $dbTransactionON), $conn);
        //Step 4: Update PatientFile
        PatientFile::addPatientAdmissionBeingDischargedLog($conn, $systemTime1, $patientDischargeQueue1->getVisit(), $login1, $patientDischargeQueue1, $patientDischargeQueue1->getBundleCode(), ! $dbTransactionON);
        //Step 5: Update relevant flags
        $patient1->setAdmitted(false)->update(! $dbTransactionON);
        //Step 6: UICardView
        echo UICardView::getSuccesfulReportCard("Discharge Summary", "Patient has been discharged successful");
    }
    //Commit 
    $conn->commit();
    $dbTransactionON = false;
} else {
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_REQUEST['qid'], $conn);
    $medicalDoctorQueue1->setAdmissionFilter($bundleCode)->update(true);
    //isThereAnyAdmittedPatient
    $patient1 = $medicalDoctorQueue1->getPatient();
    $titleCaption = ( $patient1->isAdmitted() ) ? "Discharge Patient" : "Patient Admission";
?>
    <div class="admission-station-portal border border-primary p-1 m-1">
        <div class="bg-primary text-white">
            <h4><?= $titleCaption ?></h4>
        </div>
        <div class="admission-content">
            <?php
            /* $listOfMonitors = PatientMovementStageMonitor::getAllMonitorsForAStage($conn, $medicalDoctorQueue1->getVisit()->getVisitId(), (PatientMovementStage::$__NURSE_STATION));
            $listOfNurseStationQueues = NurseStationQueue::getNurseStationQueuesForMedicalConsultationQueue($conn, $medicalDoctorQueue1->getQueueId());
            if (!is_null($listOfNurseStationQueues)) {
                $window1 = NurseStationActivity::getActivityUIForNurseStationQueue($thispage, $conn, $listOfNurseStationQueues);
                echo UIView::wrap($window1);
                echo "<br/>";
            }*/
            $listOfMonitors = PatientMovementStageMonitor::getAllMonitorsForAStage($conn, $medicalDoctorQueue1->getVisit()->getVisitId(), (PatientMovementStage::$__ADMISSION));
            if (!is_null($listOfMonitors)) {
                //throw new Exception("Kindly pay first for the previous Nurse Items");
                echo UICardView::getDangerReportCard("Pending Payment", "There is a pending payment for previous item");
            } else {
                if ($patient1->isAdmitted()) {
                    //AdmissionId
                    $patientAdmission1 = Registry::getInstance("Delta", $conn, $patient1->getAdmissionReference());
                    //You need to Discharge this person
                    echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientDischargeQueue", array(
                        array('pname' => 'comments', 'caption' => 'Additional Instructions', 'title' => 'Comments before discharging', 'required' => false)
                    ), "Discharge Patient", "create", $conn, 0, array(
                        "page" => $page,
                        "qid" => $_REQUEST['qid'],
                        "aid" => ( $patientAdmission1->getAdmissionId() ),
                        "qtype" => (MedicalDoctorConsultationQueue::$__TAB_ADMISSION),
                        "tabbedNavigationIndex" => (MedicalDoctorConsultationQueue::$__TAB_ADMISSION),
                        "efilter" => $medicalDoctorQueue1->getAdmissionFilter(),
                        "submit" => $__SUBMIT_DISCHARGE
                    ), null, null, "discharge-me", $thispage, true));
                } else {
                    echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientAdmissionQueue", array(
                        array('pname' => 'listOfServices', 'caption' => 'List of Operations', 'required' => true, 'include-columns' => array('serviceName' => array('caption' => 'Service'), 'currency' => array('caption' => 'Currency', 'map' => 'Currency.code'), 'amount' => array('caption' => 'Amount')), 'filter' => array('category' => array((ServiceCategory::$__OPEN_SURGERY), (ServiceCategory::$__ENDOSCOPIC_SURGERY)))),
                        array('pname' => 'numberOfDays', 'caption' => 'Number of Days', 'required' => true, 'value' => '1', 'placeholder' => '1'),
                        array('pname' => 'queueName', 'type' => 'switch-label', 'target-class' => 'operation-theatre', 'checked' => false, 'group' => array('name' => 'operation', 'classes' => array('border', 'border-primary', 'm-1', 'p-1')), 'caption' => 'Book for Operation', 'title' => 'Slide to Book or Un-book an opperation'),
                        array('pname' => 'theatre', 'classes' => array('operation-theatre'), 'group' => array('name' => 'operation'), 'use-class' => 'PatientOperationQueue',  'caption' => 'Theatre', 'required' => true, 'disabled' => true),
                        array('pname' => 'surgeon', 'classes' => array('operation-theatre'), 'group' => array('name' => 'operation'), 'use-class' => 'PatientOperationQueue', 'caption' => 'Surgeon', 'required' => false, 'disabled' => true, 'filter' => array('specialist' => array('1'))),
                        array('pname' => 'anaesthetist', 'classes' => array('operation-theatre'), 'group' => array('name' => 'operation'), 'use-class' => 'PatientOperationQueue', 'caption' => 'Anaesthetist', 'required' => false, 'disabled' => true, 'filter' => array('specialist' => array('1'))),
                        array('pname' => 'timeOfAppointment', 'placeholde' => '05/29/2009', 'classes' => array('operation-theatre'), 'type' => 'date', 'group' => array('name' => 'operation'), 'title' => 'An expected date for this opeation', 'use-class' => 'PatientOperationQueue', 'caption' => 'Operation Date', 'disabled' => true),
                        array('pname' => 'comments', 'caption' => 'Additional Instructions', 'title' => 'Example Morning and Evening', 'required' => false)
                    ), "Request Admission", "create", $conn, 0, array(
                        "page" => $page,
                        "qid" => $_REQUEST['qid'],
                        "qtype" => (MedicalDoctorConsultationQueue::$__TAB_ADMISSION),
                        "tabbedNavigationIndex" => (MedicalDoctorConsultationQueue::$__TAB_ADMISSION),
                        "efilter" => $medicalDoctorQueue1->getAdmissionFilter(),
                        "submit" => $__SUBMIT_ADMISSION
                    ), null, null, "my-own-way", $thispage, true));
                }
            }
            ?>
        </div>
    </div>
<?php
}
?>
