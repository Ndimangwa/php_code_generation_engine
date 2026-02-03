<?php
function customizeMyErrorMessage($thispage, $seq, $message)
{
    $link = $thispage . "?page=patientinvoice_custom&seq=$seq";
    $window1 =  "<div class=\"text-center mt-1 pt-1\"><i><a href=\"$link\">Back to Category Selection</a></i></div>";
    $window1 = "<div class=\"my-customize-error-message\"><div class=\"text-center\">$message</div>$window1</div>";
    return $window1;
}
//Transaction
$conn->beginTransaction();
$erollback = true;

$elogin1 = new Login("Delta", $login1->getLoginId(), $conn);
if ($_POST['efilter'] != $elogin1->getExtraFilter()) throw new Exception("Multiple submission detected");
$bundleCoce = $elogin1->getExtraFilter();
$elogin1->setExtraFilter(__object__::getMD5CodedString("Delta Init", 32))->update(! $erollback);
//Codes in case of error
//Step 1 : Ensure only one patient
$case1 = new PatientCase("Delta", $patient1->getCurrentCase(), $conn);
$visit1 = new PatientVisit("Delta", $case1->getCurrentVisit(), $conn);
//Step 2: Preparing colArray1
$colArray1 = array(
    "timeOfCreation" => ( $systemTime1->getTimestamp() ),
    "timeOfUpdation" => ( $systemTime1->getTimestamp() ),
    "visit" => ( $visit1->getVisitId() ),
    "patientCase" => ( $case1->getCaseId() ),
    "patient" => ( $patient1->getPatientId() ),
    "queueName" => ( $patient1->getPatientName() ),
    "listOfServices" => implode(",", $_POST['service']),
    "actionStage" => ( PatientMovementStage::$__CUSTOM_INVOICE ),
    "bundleCode" => $bundleCoce,
    "trackMonitor" => $bundleCoce,
    "temporaryObjectHolder" => ( $visit1->getObjectReferenceString() ),
    "completed" => 0,
    "pendingPayment" => 1 
);
//Step 3: Insert into patient finance queue
$financeQueue1 = new PatientFinanceQueue("Delta Init", __data__::insert($conn, "PatientFinanceQueue", $colArray1, ! $erollback), $conn);
//Step 4: Display a successful report
$link1 = $thispage . "?page=patientinvoice_create";
$buildInvoice = "<a class=\"card-link\" href=\"$link1\">Default Invoice</a>";
echo UICardView::getSuccesfulReportCard("Custom Invoice", "You have successful initiated a custom invoice for [ $patientName ]. Kindly proceed to $buildInvoice to complete this invoice");
//Commit
$conn->commit();
$erollback = false;
?>