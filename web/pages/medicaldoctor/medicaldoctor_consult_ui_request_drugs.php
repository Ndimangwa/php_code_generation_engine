<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("PatientDrugQueue".( $systemTime1->getTimestamp() ), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (MedicalDoctorConsultationQueue::$__TAB_DRUGS_MANAGEMENT))) {
    $conn->beginTransaction();
    $dbTransactionON = true;
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_POST['qid'], $conn);
    if ($_POST['efilter'] != $medicalDoctorQueue1->getDrugsManagementFilter()) throw new Exception("Multiple Submission for same queue detected");
    $medicalDoctorQueue1->setDrugsManagementFilter($bundleCode)->update(false);
    //We need to write to ExaminationQueue, however notify payment not yet
    $colArray1 = $medicalDoctorQueue1->getMyPayload(array('queueName', 'visit', 'patientCase', 'patient'));
    $colArray1['timeOfCreation'] = $colArray1['timeOfUpdation'] = $systemTime1->getTimestamp();
    $colArray1['bundleCode'] = $bundleCode;
    $colArray1['pendingPayment'] = 1;
    $colArray1['completed'] = 0;
    //Visit-Case-Patient
    $visit1 = $medicalDoctorQueue1->getVisit();
    $case1 = $medicalDoctorQueue1->getPatientCase();
    $patient1 = $medicalDoctorQueue1->getPatient();
    //Step 01: Insert Drug, each set pendingPayment is on
    $listOfDrugManagements = array();
    $listOfServices = array();
    $listOfServiceQuantities = array();
    foreach ($_POST['pharmaceuticalDrug'] as $key => $drugId)   {
        if (! isset($_POST['usage'][$key])) throw new Exception("Usage for drug not understood");
        if (! isset($_POST['temporaryIntegerHolder'][$key])) throw new Exception("Quantity for drug not understood");
        $usage = $_POST['usage'][$key];
        $quantity = $_POST['temporaryIntegerHolder'][$key];
        //Working with listOfServices
        $drug1 = new PharmaceuticalDrug("Delta", $drugId, $conn);
        $serviceId = $drug1->getService()->getServiceId();
        $listOfServices[sizeof($listOfServices)] = $serviceId;
        $listOfServiceQuantities[$serviceId] = $quantity;
        $listOfDrugManagements[sizeof($listOfDrugManagements)] = __data__::insert($conn, "PatientDrugManagement", array_merge($colArray1, array(
            "pharmaceuticalDrug" => $drugId,
            "usage" => $usage,
            "quantity" => $quantity
        )), ! $dbTransactionON, Constant::$default_select_empty_value);
    }
    //We need to update PatientDrugQueue
    $patientDrugQueue1 = new PatientDrugQueue("Delta", __data__::insert($conn, "PatientDrugQueue", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "queueName" => $patient1->getPatientName(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "listOfDrugManagements" => implode(",", $listOfDrugManagements),
        "bundleCode" => $bundleCode,
        "pendingPayment" => 1
    ), ! $dbTransactionON), $conn);
    //Step 01.b PatientMovementStageMonitor
    __data__::insert($conn, "PatientMovementStageMonitor", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "stage" => (PatientMovementStage::$__PHARMACY),
        "bundleCode" => $bundleCode,
        "temporaryObjectHolder" => $patientDrugQueue1->getObjectReferenceString()
    ),! $dbTransactionON);
    //Step 02: Raise Invoice
    __data__::insert($conn, "PatientFinanceQueue", array(
        "timeOfCreation" => $systemTime1->getTimestamp(),
        "timeOfUpdation" => $systemTime1->getTimestamp(),
        "visit" => $visit1->getVisitId(),
        "patientCase" => $case1->getCaseId(),
        "patient" => $patient1->getPatientId(),
        "listOfServices" => implode(",",$listOfServices),
        "quantityString" => __object__::array2String($listOfServiceQuantities, "1"),
        "actionStage" => (PatientMovementStage::$__PHARMACY),
	    "bundleCode" => $bundleCode,
        "trackMonitor" => $bundleCode,
        "temporaryObjectHolder" => $patientDrugQueue1->getObjectReferenceString()
    ), ! $dbTransactionON);
    //Step 03: Write to PatientFile
    PatientFile::addPatientDrugQueueLog($conn, $systemTime1, $visit1, $login1, $patientDrugQueue1, $bundleCode, ! $dbTransactionON);
    //Setting Pending Payments etc
    $medicalDoctorQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_PHARMACY_PENDING_PAYMENT)->setOnPharmacy(true)->update(! $dbTransactionON);
    //Step 04: General System Log
    $caption = $caption . "[ " . $patient1->getPatientName() . " ]";
    SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $page, $caption, ! $dbTransactionON);
    //Commit
    $conn->commit();
    $dbTransactionON = false;
    //Step 05: Successful report
    //Successful Window
    echo UICardView::getSuccesfulReportCard($caption, "Once the payment is done, the patient can proceed to pharmacy");
} else {
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_REQUEST['qid'], $conn);
    $medicalDoctorQueue1->setDrugsManagementFilter($bundleCode)->update(true);
?>
    <div class="drugs-management-portal border border-primary p-1 m-1">
        <div class="bg-primary text-white">
            <h4>Drugs Management</h4>
        </div>
        <div class="drugs-management-content">
            <?php
            echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientDrugManagement", array(
                array('pname' => 'pharmaceuticalDrug', 'caption' => 'Drugs Selection', 'type' => 'list-object', 'required' => true, 'placeholder' => 'Drugs', 'include-columns' => array('drugName' => array('caption' => 'Name of Drug'), 'unitOfMeasurement' => array('caption' => 'Units'), 'temporaryIntegerHolder' => array('caption' => 'Quantity', 'render-control' => array('required' => true, 'value' => '1', 'placeholder' => '1')), 'usage' => array('caption' => 'Usage', 'render-control' => array('required' => true, 'placeholder' => '1 * 3'))))
            ),"Assign Drugs", "create", $conn, 0, array(
                "page" => $page,
                "qid" => $_REQUEST['qid'],
                "qtype" => (MedicalDoctorConsultationQueue::$__TAB_DRUGS_MANAGEMENT),
                "tabbedNavigationIndex" => (MedicalDoctorConsultationQueue::$__TAB_DRUGS_MANAGEMENT),
                "efilter" => $medicalDoctorQueue1->getDrugsManagementFilter(),
                "submit" => 1
            ), null, null, "my-own-way", $thispage, true));
            ?>
        </div>
    </div>
<?php
}
?>