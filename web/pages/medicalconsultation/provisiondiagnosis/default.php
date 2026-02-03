<?php 
$listOfDifferentialDiseases = array();
if (! is_null($provisionDiagnosis1))   {
    if (! is_null($provisionDiagnosis1->getMainDisease())) $listOfDifferentialDiseases[0] = $provisionDiagnosis1->getMainDisease();
    if (! is_null($provisionDiagnosis1->getListOfDifferentialDiseases())) $listOfDifferentialDiseases = array_merge($listOfDifferentialDiseases, $provisionDiagnosis1->getListOfDifferentialDiseases());
}
//if (sizeof($listOfDifferentialDiseases) == 0) $listOfDifferentialDiseases = null;
echo __data__::createDataCaptureForm($thispage, "ProvisionDiagnosis", array(
    array("pname" => "mainDisease", "caption" => "The first disease in the list will be considered as Main Disease, the rest will be consided as differential diagnosis", "value" => "link one", "type" => "label"),
    array("pname" => "listOfDifferentialDiseases", "value" => $listOfDifferentialDiseases, "caption" => "Provision Diagnosis", "required" => true,  "items-count" => array("minimum" => 1, "maximum" => 100), "include-columns" => array("icd10Code" => array("caption" => "ICD 10 Code"), "whoFullDescription" => array("caption" => "Description (WHO)")))
), "Record Provision Diagnosis", "create", null, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter())
), null, null, "provision-diagnosis", $thispage, true);
?>