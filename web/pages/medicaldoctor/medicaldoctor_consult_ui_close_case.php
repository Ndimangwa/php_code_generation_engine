<?php
$initialTabIndex = isset($_REQUEST['tabbedNavigationIndex']) ? intval($_REQUEST['tabbedNavigationIndex']) : -1;
$bundleCode = __object__::getMD5CodedString("PatientCase" . ($systemTime1->getTimestamp()), 32);
$__CLOSE_CASE_ACCEPT = 1;
$__CLOSE_CASE_CANCEL = 2;
if (isset($_POST['submit']) && isset($_POST['qtype']) && ($_POST['qtype'] == (MedicalDoctorConsultationQueue::$__TAB_CASE_MANAGEMENT))) {
    $conn->beginTransaction();
    $dbTransactionON = true;
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_POST['qid'], $conn);
    if ($_POST['efilter'] != $medicalDoctorQueue1->getCaseManagementFilter()) throw new Exception("Multiple Submission for same queue detected");
    $medicalDoctorQueue1->setCaseManagementFilter($bundleCode)->update(false);
    $case1 = $medicalDoctorQueue1->getPatientCase();
    //Step 1: Preparing colArray1 if available comments
    $comments = trim($_POST['comments']);
    if ($comments == "")    {
        $colArray1 = array_merge($medicalDoctorQueue1->getMyPayload(array("patient", "patientCase", "visit")), array(
            "timeOfCreation" => ( $systemTime1->getTimestamp() ),
            "timeOfUpdation" => ( $systemTime1->getTimestamp() ),
            "bundleCode" => $bundleCode,
            "comments" => $comments,
            "temporaryObjectHolder" => ( $case1->getObjectReferenceString() )
        ));
        $comment1 = new MedicalComment("Delta", __data__::insert($conn, "MedicalComment", $colArray1, ! $dbTransactionON), $conn);
    }
    //Step 2: Update relevant case
    $case1->setClosed(true)->setCaseType(PatientCaseType::$__CLOSED)->update(! $dbTransactionON);
    //Step 3: Update PatientFile CaseClosed 
    PatientFile::addPatientCaseClosedLog($conn, $systemTime1, $medicalDoctorQueue1->getVisit(), $login1, $case1, $bundleCode, ! $dbTransactionON);
    //Step 4: Successful reports 
    echo UICardView::getSuccesfulReportCard("Case Closing", "Case was successful closed");
    $conn->commit();
    $dbTransactionON = false;
} else if (isset($_REQUEST['action']))  {
    if ($_REQUEST['action'] != $__CLOSE_CASE_ACCEPT) throw new Exception("Could not interpret action");
    echo UIView::wrap(__data__::createDataCaptureForm($thispage, "MedicalComment", array(
        array("pname" => "comments", "caption" => "Additional Comments", "placeholder" => "OK")
    ), "Close Case", "create", $conn, 0, array_merge($_REQUEST, array(
        "submit" => $__CLOSE_CASE_ACCEPT
    )), null, null, "close-case", $thispage, true, null));
} else {
    $medicalDoctorQueue1 = new MedicalDoctorConsultationQueue("Hello", $_REQUEST['qid'], $conn);
    $medicalDoctorQueue1->setCaseManagementFilter($bundleCode)->update(true);
    $patient1 = $medicalDoctorQueue1->getPatient();
?>
    <div class="case-management-portal border border-primary p-1 m-1">
        <div class="bg-primary text-white">
            <h4>Case Management</h4>
        </div>
        <div class="case-management-content">
            <?php
            if ($patient1->hasPendingBalance()) {
                $window1 = $patient1->getBalanceStatusScreen();
                echo "<div><h4>Patient has pending payment</h4><div>$window1</div></div>";
            } else {
            ?>
                <div class="card border border-outline-danger text-center" style="font-size: 1.1em;">
                    <div class="card-header bg-danger">Case Closing Confirmation</div>
                    <div class="card-body">
                        <p>Are you sure you want to close case for patient <?= $patient1->getPatientName() ?><br />
                            <i class="bg-danger">Note: This action will not enable the patient to receive service under the current case.</i>
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                        <a href="<?= $thispage ?>?page=<?= $page ?>&action=<?= $__CLOSE_CASE_ACCEPT ?>&qid=<?= $_REQUEST['qid'] ?>&qtype=<?= MedicalDoctorConsultationQueue::$__TAB_CASE_MANAGEMENT ?>&tabbedNavigationIndex=<?= MedicalDoctorConsultationQueue::$__TAB_CASE_MANAGEMENT ?>&efilter=<?= $medicalDoctorQueue1->getCaseManagementFilter() ?>" class="btn btn-primary col-sm-6">Yes, Close Case</a>
                        <a href="<?= $thispage ?>?page=<?= $page ?>&qid=<?= $_REQUEST['qid'] ?>&qtype=<?= MedicalDoctorConsultationQueue::$__TAB_VITAL_SIGNS ?>&tabbedNavigationIndex=<?= MedicalDoctorConsultationQueue::$__TAB_VITAL_SIGNS ?>" class="btn btn-warning col-sm-6">No, Cancel</a>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
<?php
}
?>