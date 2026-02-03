<?php 
echo __data__::createDataCaptureForm($thispage, "PatientExaminationQueue", array(
    array("pname" => "listOfServices", "value" => (is_null($examinationQueue1) ? null : ($examinationQueue1->getListOfServices())), "caption" => "Services (Lab/X-Ray/Ultasound)", "required" => true, "include-columns" => array("serviceName" => array("caption" => "Service Name"), "currency" => array("caption" => "Currency", "map" => "Currency.code"), "amount" => array("caption" => "Amount")), "filter" => array("category" => array((ServiceCategory::$__LABORATORY_EXAMINATION), (ServiceCategory::$__PLAIN_CONVENTION_X_RAY), (ServiceCategory::$__ULTRA_SOUND))))
), "Request Investigation", "create", $conn, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter()),
    "add-in-list" => 1
), null, null, "request-investigation", $thispage, true);
?>