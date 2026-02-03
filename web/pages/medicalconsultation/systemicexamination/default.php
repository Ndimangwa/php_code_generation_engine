<?php 
echo __data__::createDataCaptureForm($thispage, "SystemicExamination", array(
    array("pname" => "examination", "value" => (is_null($systemicExamination1) ? "" : (is_null($systemicExamination1->getExamination()) ? "" : ($systemicExamination1->getExamination()->getComments()))), "caption" => "Systemic Examination", "type" => "textarea", "required" => false, "placeholder" => "Spots")
), "Record Systemic Examination", "create", null, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter())
), null, null, "systemic-examination", $thispage, true);
?>