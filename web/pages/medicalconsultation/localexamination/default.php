<?php 
echo __data__::createDataCaptureForm($thispage, "LocalExamination", array(
    array("pname" => "examination", "value" => (is_null($localExamination1) ? "" : (is_null($localExamination1->getExamination()) ? "" : ($localExamination1->getExamination()->getComments()))), "caption" => "Local Examination", "type" => "textarea", "required" => false, "placeholder" => "Spots")
), "Record Local Examination", "create", null, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter())
), null, null, "local-examination", $thispage, true);
?>