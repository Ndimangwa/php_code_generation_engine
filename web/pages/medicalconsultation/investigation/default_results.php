<?php 
if ($examinationQueue1->isPendingPayment()) {
    echo UICardView::getSuccesfulReportCard("Pending Payment", "Kindly make payment for laboratory investigation");
} else {
    //
    $window1 = "<div><div>";
    $window1 .= PatientExaminationResults::getResultsUIForExaminationQueue($thispage, $conn, $examinationQueue1->getQueueId());
    $window1 .= "</div><div class=\"text-right\">";
    $window1 .= UIControls::getAnchorTag("Add more Investigation", $thispage, array(
        "page" => $page,
        "qid" => ( $consultationQueue1->getQueueId() ),
        "counter" => $currentCounter,
        "add-in-list" => 1,
        "efilter" => ( $consultationQueue1->getExtraFilter() )
    ), array("card-link", "text-center"));
    $window1 .= "</div></div>";
    echo UIView::wrap($window1);
}
?>