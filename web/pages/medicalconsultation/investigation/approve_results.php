<?php 
$conn->beginTransaction();
$erollback = true;
//Step 1: Get Service 
$service1 = new Service("Delta", $_REQUEST['id'], $conn);
//Step 2: Gelt List of Not Approved Results for service
$listOfResults = PatientExaminationResults::getListOfApprovedResultsForService($conn, $examinationQueue1->getQueueId(), $service1->getServiceId(), false);
if (is_null($listOfResults)) throw new Exception("Could not get any of un-approved results");
//Step 3: For each results
foreach ($listOfResults as $results1)   {
    //3.1 Approve
    $results1->setApproved(true)->update(! $erollback);
}
//Step 4: We need to check the list for entire queue 
//$listOfResults = PatientExaminationResults::getListOfApprovedResultsForService($conn, $examinationQueue1->getQueueId(), $service1->getServiceId(), false);
//$listOfResults = PatientExaminationResults::getListOfResultsForExaminationQueue($conn, $examinationQueue1->getQueueId());
if ($examinationQueue1->isCompleted())    {
    //We have completed verification
    $consultationQueue1->setOnExaminationResultsVerification(false)->update(! $erollback);
    //Save next page 
    $nextPage = MedicalDoctorConsultationQueue::$__BLOCK_WORKING_DIAGNOSIS;
    $applicationCounter = $consultationQueue1->getApplicationCounter();
    if ($nextPage > $applicationCounter)    {
        $consultationQueue1->setApplicationCounter($nextPage)->update(! $erollback);
    }
}
//Step 5: Give a successful report
echo UICardView::getSuccesfulReportCard("Approve Results", "You have successful approved results");
//Connection
$conn->commit();
$erollback = false;
?>