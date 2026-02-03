<?php 
echo __data__::createDataCaptureForm($thispage, "PatientHistory", array(
    array("pname" => "chiefComplaints", "value" => (is_null($patientHistory1) ? "" : (is_null($patientHistory1->getChiefComplaints()) ? "" : ($patientHistory1->getChiefComplaints()->getComments()))), "caption" => "Chief Complaints (C/C)", "type" => "text", "required" => true, "placeholder" => "Header-ache"),
    array("pname" => "reviewOfOtherServices", "value" => (is_null($patientHistory1) ? "" : (is_null($patientHistory1->getReviewOfOtherServices()) ? "" : ($patientHistory1->getReviewOfOtherServices()->getComments()))), "caption" => "Review of Other Services (RoS)", "type" => "text", "required" => false, "placeholder" => "Fever"),
    array("pname" => "pastMedicalHistory", "value" => (is_null($patientHistory1) ? "" : (is_null($patientHistory1->getPastMedicalHistory()) ? "" : ($patientHistory1->getPastMedicalHistory()->getComments()))), "caption" => "Past Medical History (PMHs)", "type" => "text", "required" => false, "placeholder" => "Fever"),
    array("pname" => "familyAndSocialHistory", "value" => (is_null($patientHistory1) ? "" : (is_null($patientHistory1->getFamilyAndSocialHistory()) ? "" : ($patientHistory1->getFamilyAndSocialHistory()->getComments()))), "caption" => "Family And Social History (FSMx)", "type" => "text", "required" => false, "placeholder" => "Fever")
), "Record Patient History", "create", null, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentCounter,
    "submit" => 1,
    "efilter" => ($consultationQueue1->getExtraFilter())
), null, null, "patient-history", $thispage, true);
?>