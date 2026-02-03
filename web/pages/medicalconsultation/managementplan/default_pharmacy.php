<?php 
$consultationQueue1->setDrugsManagementFilter(__object__::getMD5CodedString("Delta", 32))->update(! $erollback);
echo UIView::wrap(__data__::createDataCaptureForm($thispage, "PatientDrugManagement", array(
    array("pname" => "pharmaceuticalDrug", "caption" => "Drugs Selection", "type" => "list-object", "required" => false, "include-columns" => array("drugName" => array("caption" => "Name of Drug"), "unitOfMeasurement" => array("caption" => "Units"), "temporaryIntegerHolder" => array("caption" => "Quantity", "render-control" => array("required" => true, "value" => "1", "placeholder" => "1")), "usage" => array("caption" => "Usage", "render-control" => array("required" => true, "placeholder" => "1 * 3"))))
), "Assign Drugs", "create", $conn, 0, array(
    "page" => $page,
    "qid" => $consultationQueue1->getQueueId(),
    "counter" => $currentcount,
    "submit" => 1,
    "efilter" => $consultationQueue1->getExtraFilter(),
    "tab" => ( GeneralMedicalWorkingBlock::$__TAB_PHARMACY ),
    "efilter2" => $consultationQueue1->getDrugsManagementFilter()
), null, null, "assign-drugs", $thispage, true));
?>