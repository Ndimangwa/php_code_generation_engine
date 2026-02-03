<?php 
echo __data__::createDataCaptureForm($thispage, "GeneralExamination", array(
    array("pname" => "examination", "value" => (is_null($generalExamination1) ? "" : (is_null($generalExamination1->getExamination()) ? "" : ($generalExamination1->getExamination()->getComments()))), "caption" => "General Examination", "type" => "textarea", "required" => true, "placeholder" => "Spots")
), "Record General Examination", "create", null, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter())
), null, null, "general-examination", $thispage, true);
?>