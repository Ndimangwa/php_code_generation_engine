<?php
$conn->beginTransaction();
$erollback = true;
//You can use secondFilter
//Step 1: Get Service
$service1 = new Service("Delta", $_REQUEST['id'], $conn);
//Step 2: GetList of NOT ApprovedResults you can repeat only which are Not Approved last arg is false
$listOfResults = PatientExaminationResults::getListOfApprovedResultsForService($conn, $examinationQueue1->getQueueId(), $service1->getServiceId(), false);
if (is_null($listOfResults)) throw new Exception("We do not have the corresponding results");
//Step 3: Foreach results 
foreach ($listOfResults as $results1) {
    //3.1 Remove corresponding PatientFile 
    $listOfFiles = PatientFile::getListOfFiles($conn, $results1->getMyClassname(), $results1->getResultsId());
    //I expect only one entry but just delete all
    if (!is_null($listOfFiles)) {
        foreach ($listOfFiles as $patientFile1) {
            $patientFile1->delete(!$erollback);
        }
    }
    //3.2 Remove the results itself
    $results1->delete(!$erollback);
}
//Step 4: Remove from Attended-list in examinationQueue
$listOfAttendedServices = $examinationQueue1->getListOfAttendedServices();
if (!is_null($listOfAttendedServices)) {
    $newListOfServices = __data__::substractArray(__data__::convertListObjectsToArray($listOfAttendedServices), array($service1->getServiceId()));
    $examinationQueue1->setListOfAttendedServices(implode(",", $newListOfServices));
}
//Step 5: Update queue flags
$examinationQueue1->setCompleted(false)->setRequestedBy($login1->getLoginId())->setTimeOfUpdation($systemTime1->getTimestamp())->update(!$erollback);
//Step 6: Set flags on consultationQueue
$consultationQueue1->setOnMedicalExamination(true)->update(!$erollback);
//Step 7: Add to sub queue 
$tArray1 = array_merge($colArray1, array(
    "examinationQueue" => ($examinationQueue1->getQueueId()),
    "service" => ($service1->getServiceId())
));
switch ($service1->getCategory()->getCategoryId()) {
    case (ServiceCategory::$__LABORATORY_EXAMINATION):
        __data__::insert($conn, "QueueNotifyWetLab", $tArray1, !$erollback);
        break;
    case (ServiceCategory::$__ULTRA_SOUND):
        __data__::insert($conn, "QueueNotifyUltrasound", $tArray1, !$erollback);
        break;
    case (ServiceCategory::$__PLAIN_CONVENTION_X_RAY):
        __data__::insert($conn, "QueueNotifyPlainXRAY", $tArray1, !$erollback);
        break;
    case (ServiceCategory::$__CONTRAST_STUDIES):
        break;
}
//Step 8: Successful report
echo UICardView::getSuccesfulReportCard("Repeat Examination", "Your request to re-examination has been granted");
//connection
$conn->commit();
$erollback = false;
