<?php 
//Now you need to lock this page against multiple-submission
$elogin1 = new Login("Delta", $login1->getLoginId(), $conn);
$elogin1->setExtraFilter(__object__::getMD5CodedString("Login Lock", 32))->update(! $erollback);
echo UIView::wrap(__data__::createDataCaptureForm($thispage, "ServiceLogSequence", array(
    array("pname" => "serviceName", "use-class" => "Service", "caption" => "Select at least one service from the list for $patientName", "type" => "label"),
    array("pname" => "service", "caption" => "List of Services", "type" => "list-object", "required" => true, "filter" => array("category" => $_POST['category']), "include-columns" => array("serviceName" => array("caption" => "Service Category"), "currency" => array("caption" => "Currency", "map" => "Currency.code"), "amount" => array("caption" => "Amount")))
), "Service Selection", "create", $conn, 0, array(
    "page" => $page,
    "seq" => $__SEQ_SUBMIT,
    "id" => ( $patient1->getPatientId() ),
    "efilter" => ($elogin1->getExtraFilter())
), null, null, "select-service-category", $thispage, true, null));
//Now working with back - link
$link= UIControls::getAnchorTag("Back to Service Categories",$thispage, array(
    'page' => $page,
    'seq'=> $__SEQ_CATEGORIES,
    'id' => ( $patient1->getPatientId() )
), array('card-link'));
echo "<div class=\"text-center mt-1 pt-1\"><i>$link</i></div>";
?>