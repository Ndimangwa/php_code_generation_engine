<?php
$newAddedServices = null;
$submittedServices = $_POST['listOfServices'];
//We need to buid a queue examinationQueue1
//Step 1: Putting colArray1 properly
$dataArray1 = array_merge($colArray1, array(
    "pendingPayment" => 1,
    "requestedBy" => ($login1->getLoginId()),
    "listOfServices" => implode(",", $submittedServices),
    "temporaryObjectHolder" => ($consultationQueue1->getObjectReferenceString()),
    "temporaryStringHolder" => ($consultationQueue1->getBundleCode()),
    "completed" => 0
));
//Step 2: Build Queue
if (is_null($examinationQueue1)) {
    //build new
    $examinationQueue1 = new PatientExaminationQueue("Delta", __data__::insert($conn, "PatientExaminationQueue", $dataArray1, !$erollback), $conn);
    $newAddedServices = $submittedServices;
} else {
    //Do not Allow multiple unpaid invoice  Monitor confusion
    if ($examinationQueue1->isPendingPayment()) throw new Exception("The patient has to pay the previous list prior appending new list");
    $existingServices = __data__::convertListObjectsToArray($examinationQueue1->getListOfServices());
    //We need to make sure all-existing list is in submitted list
    if (! __data__::isArrayASubsetOfAnotherArray($existingServices, $submittedServices)) throw new Exception("All services existed before must be present");
    //Need to Update services and  
    $newAddedServices = __data__::substractArray($submittedServices, $existingServices);
    //Now we need to update
    $examinationQueue1->setUpdateList(array(
        "timeOfUpdation" => ($systemTime1->getTimestamp()),
        "listOfServices" => implode(",", $submittedServices),
        "pendingPayment" => 1,
        "completed" => 0
    ))->update(!$erollback);
}
//You need to check if there is any new service 
$newAddedServices = (sizeof($newAddedServices) == 0) ? null : $newAddedServices;
if (is_null($newAddedServices)) throw new Exception("There is no any new service added");
//We need to reload examinationQueue since we are using updateList 
//$examinationQueue1 = new PatientExaminationQueue("Delta", $examinationQueue1->getQueueId(), $conn);
//Step 3: Work for sub-queues
foreach ($newAddedServices as $serviceId) {
    $service1 = new Service("Delta", $serviceId, $conn);
    //We need to work with only newAddedServices 
    $dataArray1 = array_merge($colArray1, array(
        "examinationQueue" => ($examinationQueue1->getQueueId()),
        "service" => ($service1->getServiceId())
    ));
    switch ($service1->getCategory()->getCategoryId()) {
        case (ServiceCategory::$__LABORATORY_EXAMINATION):
            __data__::insert($conn, "QueueNotifyWetLab", $dataArray1, !$erollback);
            break;
        case (ServiceCategory::$__ULTRA_SOUND):
            __data__::insert($conn, "QueueNotifyUltrasound", $dataArray1, !$erollback);
            break;
        case (ServiceCategory::$__PLAIN_CONVENTION_X_RAY):
            __data__::insert($conn, "QueueNotifyPlainXRAY", $dataArray1, !$erollback);
            break;
        case (ServiceCategory::$__CONTRAST_STUDIES):
            break;
    }
}
//Step 4: Build Monitor
__data__::insert($conn, "PatientMovementStageMonitor", array_merge($colArray1, array(
    "stage" => (PatientMovementStage::$__LABORATORY_EXAMINATION),
    "temporaryObjectHolder" => ($examinationQueue1->getObjectReferenceString())
)), !$erollback);
//Step 5: Build PatientFinanceQueue (charge only those service previous not on the queue, incase of added other services)
__data__::insert($conn, "PatientFinanceQueue", array_merge($colArray1, array(
    "listOfServices" => implode(",", $newAddedServices),
    "actionStage" => (PatientMovementStage::$__LABORATORY_EXAMINATION),
    "temporaryObjectHolder" => ($examinationQueue1->getObjectReferenceString()),
    "trackMonitor" => ($examinationQueue1->getBundleCode())
)), !$erollback);
//Step 6: Put Flags properly
$consultationQueue1->setFlagAt(MedicalDoctorConsultationQueue::$__FLAG_LABORATORY_EXAMINATION_PENDING_PAYMENT)->setOnMedicalExamination(true)->update(!$erollback);
//Step 7: Update PatientFile
PatientFile::addExaminationQueueLog($conn, $systemTime1, $examinationQueue1->getVisit(), $login1, $examinationQueue1, $examinationQueue1->getBundleCode(), !$erollback);
//Step 8:  Get successful report
echo UICardView::getSuccesfulReportCard("Updated Laboratory Examination", "Once the payment is done, the patient can proceed to the respective examination laboratory");
?>