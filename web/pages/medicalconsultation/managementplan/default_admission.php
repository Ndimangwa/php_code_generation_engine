<?php 
$consultationQueue1->setAdmissionFilter(__object__::getMD5CodedString("Delta", 32))->update(! $erollback);
if ($consultationQueue1->getPatient()->isAdmitted())  {
    throw new Exception("Patient is Admitted");
} else {
    echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientAdmissionQueue", array(
        array('pname' => 'listOfServices', 'caption' => 'List of Operations', 'required' => true, 'include-columns' => array('serviceName' => array('caption' => 'Service'), 'currency' => array('caption' => 'Currency', 'map' => 'Currency.code'), 'amount' => array('caption' => 'Amount')), 'filter' => array('category' => array((ServiceCategory::$__OPEN_SURGERY), (ServiceCategory::$__ENDOSCOPIC_SURGERY)))),
        array('pname' => 'numberOfDays', 'caption' => 'Number of Days', 'required' => true, 'value' => '1', 'placeholder' => '1'),
        array('pname' => 'queueName', 'type' => 'switch-label', 'target-class' => 'operation-theatre', 'checked' => false, 'group' => array('name' => 'operation', 'classes' => array('border', 'border-primary', 'm-1', 'p-1')), 'caption' => 'Book for Operation', 'title' => 'Slide to Book or Un-book an opperation'),
        array('pname' => 'theatre', 'classes' => array('operation-theatre'), 'group' => array('name' => 'operation'), 'use-class' => 'PatientOperationQueue',  'caption' => 'Theatre', 'required' => true, 'disabled' => true),
        array('pname' => 'surgeon', 'classes' => array('operation-theatre'), 'group' => array('name' => 'operation'), 'use-class' => 'PatientOperationQueue', 'caption' => 'Surgeon', 'required' => false, 'disabled' => true, 'filter' => array('specialist' => array('1'))),
        array('pname' => 'anaesthetist', 'classes' => array('operation-theatre'), 'group' => array('name' => 'operation'), 'use-class' => 'PatientOperationQueue', 'caption' => 'Anaesthetist', 'required' => false, 'disabled' => true, 'filter' => array('specialist' => array('1'))),
        array('pname' => 'timeOfAppointment', 'placeholder' => '05/29/2009', 'classes' => array('operation-theatre'), 'type' => 'date', 'group' => array('name' => 'operation'), 'title' => 'An expected date for this opeation', 'use-class' => 'PatientOperationQueue', 'caption' => 'Operation Date', 'disabled' => true)
    ), "Request Admission", "create", $conn, 0, array(
        "page" => $page,
        "qid" => $consultationQueue1->getQueueId(),
        "counter" => $currentcount,
        "submit" => 1,
        "efilter" => $consultationQueue1->getExtraFilter(),
        "tab" => ( GeneralMedicalWorkingBlock::$__TAB_ADMISSION ),
        "efilter2" => $consultationQueue1->getAdmissionFilter()
    ), null, null, "my-own-way", $thispage, true));
}
?>