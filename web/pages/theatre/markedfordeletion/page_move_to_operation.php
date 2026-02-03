<?php
//Third-Filter
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("Move to Operation" . ($systemTime1->getTimestamp()), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (Theatre::$__TAB_MOVE_TO_OPERATION))) {
    $conn->beginTransaction();
    $erollback = true;
    //Check Submission
    if ($_POST['efilter'] != ( $queue1->getThirdFilter() )) throw new Exception("Perhaps multiple submission of the consent form");
    $queue1->setThirdFilter($bundleCode)->update(! $erollback);
    //Perform update
    //Step 1: Get Payload for patient, patientCase, visit and bundleCode and add times
    $colArray1 = array_merge($queue1->getMyPayload(array(
        "visit", "patientCase", "patient", "medicalComment", "admissionQueue", "listOfServices", "theatre", "surgeon", "anaesthetist"
    ), null, true), array(
        "timeOfCreation" => ( $systemTime1->getTimestamp() ),
        "timeOfUpdation" => ( $systemTime1->getTimestamp() ),
        "operationQueue" => ( $queue1->getQueueId() ),
        "completed" => 0
    ));
    //Step 2: Add to patient operation
    $patientOperation1 = new PatientOperation("Delta", __data__::insert($conn, "PatientOperation", $colArray1, ! $erollback), $conn);
    //Step 3: Close this queue
    $queue1->setCompleted(true)->update(! $erollback);
    //Successful message
    echo UICardView::getSuccesfulReportCard("Moved to Operation", "You have successful moved the patient to the Operation Theatre");
    $conn->commit();
    $erollback = false;
} else {
    if ($queue1->isConsented()) {
        $queue1->setThirdFilter($bundleCode)->update(! $erollback);
        echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientOperationQueue", array(
            array('pname' => 'queueName', 'type' => 'label', 'caption' => 'You are about to move this patient to the operation; Kindly click on Move to Operation button to proceed')
        ), "Move to Operation", "create", $conn, 0, array(
            'page' => $page,
            'qid' => ( $queue1->getQueueId() ),
            'consented' => 1,
            'submit' => 1,
            'qtype' => ( Theatre::$__TAB_MOVE_TO_OPERATION ),
            'tabbedNavigationIndex' => ( Theatre::$__TAB_MOVE_TO_OPERATION ),
            'efilter' => ( $queue1->getThirdFilter() )
        ), null, null, "delta-init", $thispage, true, null));
    } else {
        echo UICardView::getDangerReportCard("Move to Operation", "The patient has not consented for this operation, kindly consult with the patient and the next of kin");
    }
}
?>
