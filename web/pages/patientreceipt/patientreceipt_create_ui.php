<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    MAKE PAYMENT
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $enableRollBack = false;
                    $nextPage = $thispage . "?page=patientreceipt";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $invoice1 = new PatientInvoice("Delta", $_REQUEST['id'], $conn);
                        if ($_REQUEST['efilter'] != $invoice1->getExtraFilter()) throw new Exception("Perhaps the receipt had already being issued, or another person has replayed the browser window");
                        if (isset($_REQUEST['receipt_generate'])) {
                            $conn->beginTransaction();
                            $enableRollBack = true;
                            $invoice1->setExtraFilter(__object__::getRandomMD5Key(rand(10, 99)))->update(!$enableRollBack);
                            $amount = $_REQUEST['amount'];
                            $payerName = $_REQUEST['payerName'];
                            $payerPhone = $_REQUEST['payerPhone'];
                            $receipt1 = Finance::issuePatientReceipt($conn, $login1, $systemTime1, $invoice1->getInvoiceId(), $amount, null, null, $payerName, $payerPhone, !$enableRollBack);
                            $receiptNumber = $receipt1->getReceiptNumber();

                            //throw new Exception("00001");
                            //You need to update the patient Appropriately -- 
                            switch ($invoice1->getActionStage()->getStageId()) {
                                case (PatientMovementStage::$__NEW_REGISTRATION):
                                    PatientFile::addNewRegistrationReceiptLog($conn, $systemTime1, $invoice1->getVisit(), $login1, $receipt1, $invoice1->getVisit()->getTemporaryStringHolder(), !$enableRollBack);
                                    break;
                                case (PatientMovementStage::$__CONTINUING_VISIT):
                                    PatientFile::addContinuingVisitReceiptLog($conn, $systemTime1, $invoice1->getVisit(), $login1, $receipt1, $invoice1->getVisit()->getTemporaryStringHolder(), !$enableRollBack);
                                    break;
                                case (PatientMovementStage::$__LABORATORY_EXAMINATION):
                                    $patientExaminationQueue1 = Registry::getInstance("Data", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($patientExaminationQueue1)) throw new Exception("Could not get reference Patient Examination Queue");
                                    PatientFile::addExaminationQueueReceiptLog($conn, $systemTime1, $invoice1->getVisit(), $login1, $receipt1, $patientExaminationQueue1->getTemporaryStringHolder(), !$enableRollBack);
                                    break;
                                case (PatientMovementStage::$__NURSE_STATION):
                                    $nurseStationQueue1 = Registry::getInstance("Delta", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($nurseStationQueue1)) throw new Exception("Could not get Nurse Station Reference");
                                    PatientFile::addNurseStationQueueReceiptLog($conn, $systemTime1, $invoice1->getVisit(), $login1, $receipt1, $nurseStationQueue1->getBundleCode(), !$enableRollBack);
                                    break;
                                case (PatientMovementStage::$__PHARMACY):
                                    $patientDrugQueue1 = Registry::getInstance("Delta", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($patientDrugQueue1)) throw new Exception("Could not get Drug Queue reference");
                                    PatientFile::addPatientDrugQueueReceiptLog($conn, $systemTime1, $invoice1->getVisit(), $login1, $receipt1, $patientDrugQueue1->getBundleCode(), ! $enableRollBack);
                                    break;
                                case (PatientMovementStage::$__ADMISSION):
                                    $patientAdmissionQueue1 = Registry::getInstance("OmniCron", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($patientAdmissionQueue1)) throw new Exception("Could not get Patient Admission Queue reference");
                                    PatientFile::addPatientAdmissionReceiptLog($conn, $systemTime1, $invoice1->getVisit(), $login1, $receipt1, $patientAdmissionQueue1->getBundleCode(), ! $enableRollBack);
                                    break;
                            }
                            //You need to reload invoice and verify if it has been cleared
                            $invoice1 = new PatientInvoice("Need to Read DB", $invoice1->getInvoiceId(), $conn);
                            if ($invoice1->isClosed()) { 
                                PatientMovementStage::updatePatientStageAndQueue($conn, $systemTime1, $invoice1, $invoice1->getVisit(), $login1, !$enableRollBack, is_null($invoice1->getActionStage()) ? null : $invoice1->getActionStage()->getStageId(), null);
                            }
                            $conn->commit();
                            $enableRollBack = false;
                    ?>
                            <div class="document-creator m-2">
                                <?= __data__::showPrimaryAlert("Receipt [ $receiptNumber ] of amount [ $amount ], were successful issued") ?>
                            </div>
                            <?php
                            if (Authorize::isAllowable2($conn, "patientreceipt_read", "normal", "donotsetlog", null, null)) {
                                $pdfpage = ($profile1->getBaseURL()) . "/documents/pdf/__get_document__.php?id=" . ($receipt1->getReceiptId()) . "&dtype=" . (Documents::$__PDF_RECEIPT);
                                $pdfpage = str_replace("//", "/", $pdfpage);
                            ?>
                                <div class="document-controls-bottom-right">
                                    <a target="_blank" href="<?= $pdfpage ?>" class="btn btn-primary btn-control" data-toggle="tooltip" title="Click to Download a receipt in a pdf format"><i class="fa fa-file-pdf-o fa-2x"></i>Download Receipt</a>
                                </div>
                            <?php
                            }
                            ?>
                        <?php
                        } else {
                        ?>
                            <div class="document-creator m-2">
                                <div class="bg-primary p-1">
                                    <div class="bg-warning p-1">
                                        <div class="bg-primary p-1">
                                            <div class="bg-white p-1">
                                                <!--Begin 0001-->
                                                <?php
                                                $phoneNumber = $invoice1->getPatient()->getPhone();
                                                if (is_null($phoneNumber)) $phoneNumber = "";
                                                $formToDisplay1 = __data__::createDataCaptureForm($thispage, "PatientReceipt", array(
                                                    array('pname' => 'amount', 'required' => true, 'caption' => 'Received Amount', 'placeholder' => '0.00', 'value' => $invoice1->getUnpaidAmount()),
                                                    array('pname' => 'payerName', 'caption' => 'Payer Name', 'required' => true, 'placeholder' => 'Name of the Payer', 'value' => $invoice1->getChargeTo()),
                                                    array('pname' => 'payerPhone', 'caption' => 'Payer Phone', 'required' => false, 'placeholder' => '0XXXXXXXXX', 'value' => $phoneNumber)
                                                ), "Issue Receipt", "create", $conn, -1, array(
                                                    "page" => "patientreceipt_create",
                                                    "invoice" => $invoice1->getInvoiceId(),
                                                    "id" => $invoice1->getInvoiceId(),
                                                    "receipt_generate" => true,
                                                    "efilter" => $invoice1->getExtraFilter()
                                                ), null, null, "btn-general-submit");
                                                echo $formToDisplay1;
                                                ?>
                                                <!--End 0001-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } catch (Exception $e) {
                        if ($enableRollBack) $conn->rollBack();
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Receipt</a></i><br />
                        <span class="text-muted"><i>Rule: patientreceipt_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        $('#discount').on('change', function(e) {
            var $text1 = $(this);
            var $span1 = $('#total-invoice-value');
            if (!$span1.length) return false;
            var totalAmount = parseFloat($span1.data('amount'));
            var t1 = $.trim($text1.val());
            if (t1 == "") t1 = 0;
            var discountAmount = parseFloat(t1);
            if (discountAmount > totalAmount) {
                discountAmount = 0;
            }
            $text1.val(discountAmount);
            totalAmount -= discountAmount;
            $span1.text(totalAmount);
        });
    })(jQuery);
</script>