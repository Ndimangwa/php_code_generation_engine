<?php 
echo __data__::createDataCaptureForm($thispage, "WorkingDiagnosis", array(
    array("pname" => "listOfDiseases", "value" => (is_null($workingDiagnosis1) ? null : ( $workingDiagnosis1->getListOfDiseases() )),"required" => false, "caption" => "Working Diagnosis", "items-count" => array("maximum" => 100), "include-columns" => array("icd10Code" => array("caption" => "ICD 10 Code"), "whoFullDescription" => array("caption" => "Description (WHO)")))    
), "Record Working Diagnosis", "create", null, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter())
), null, null, "working-diagnosis", $thispage, true);
?>