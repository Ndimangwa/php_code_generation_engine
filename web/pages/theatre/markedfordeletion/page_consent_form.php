<?php
//Second-Filter
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("Consent Form" . ($systemTime1->getTimestamp()), 32);
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (Theatre::$__TAB_CONSENT_FORM))) {
    $conn->beginTransaction();
    $erollback = true;
    //Check Submission
    if ($_POST['efilter'] != ( $queue1->getSecondFilter() )) throw new Exception("Perhaps multiple submission of the consent form");
    $queue1->setSecondFilter($bundleCode)->update(! $erollback);
    //Perform update
    $queue1->setConsented(true)->update(! $erollback);
    //Successful message
    echo UICardView::getSuccesfulReportCard("Consent Form", "You have successful submitted the patient's consent form");
    $conn->commit();
    $erollback = false;
} else {
    if ($queue1->isConsented()) {
        $message = "<div>The patient has already consented for the operation</div>";
        echo UIView::wrap($message);
    } else {
        $queue1->setSecondFilter($bundleCode)->update(! $erollback);
        echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientOperationQueue", array(
            array('pname' => 'queueName', 'type' => 'label', 'caption' => 'Patient has consented')
        ), "Submit Consent", "create", $conn, 0, array(
            'page' => $page,
            'qid' => ( $queue1->getQueueId() ),
            'consented' => 1,
            'submit' => 1,
            'qtype' => ( Theatre::$__TAB_CONSENT_FORM ),
            'tabbedNavigationIndex' => ( Theatre::$__TAB_CONSENT_FORM ),
            'efilter' => ( $queue1->getSecondFilter() )
        ), null, null, "delta-init", $thispage, true, null));
    }
}
?>