<?php 
echo UIView::wrap(__data__::createDataCaptureForm($thispage, "Service", array(
    array("pname" => "serviceName", "caption" => "Select at least one category of services for $patientName", "type" => "label"),
    array("pname" => "category", "caption" => "Service Categories", "type" => "list-object", "required" => true, "include-columns" => array("categoryName" => array("caption" => "Service Category")))
), "Service Categories Selection", "create", $conn, 0, array(
    "page" => $page,
    "seq" => $__SEQ_SERVICES,
    "id" => ( $patient1->getPatientId() )
), null, null, "select-service-category", $thispage, true, null));
//Now working with back - link
$link= UIControls::getAnchorTag("Back to Patient Search",$thispage, array(
    'page' => $page
), array('card-link'));
echo "<div class=\"text-center mt-1 pt-1\"><i>$link</i></div>";
?>