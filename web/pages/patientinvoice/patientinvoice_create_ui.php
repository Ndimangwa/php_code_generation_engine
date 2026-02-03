<?php
$invoice1 = null;
$enableRollBack = false;
?>
<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    CREATE A NEW INVOICE
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage . "?page=patientinvoice";
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        $financeQueue1 = new PatientFinanceQueue("q", $_REQUEST['qid'], $conn);
                        $listOfServices = $financeQueue1->getListOfServices();
                        if (is_null($listOfServices) || sizeof($listOfServices) == 0) throw new Exception("Empty List of Services");
                        if (isset($_POST['invoice_generate']) && isset($_POST['discount']) && isset($_POST['ucost']) && isset($_POST['quantity'])) {
                            $conn->beginTransaction();
                            $enableRollBack = true;
                            $discount = floatval($_POST['discount']);
                            $invoicedServiceList = array();
                            $currencyId = $_POST['currencyId'];
                            $chargeTo = $financeQueue1->getPatientCase()->getPatient()->getPatientName();
                            if (isset($_POST['chargeTo'])) $chargeTo = $_POST['chargeTo'];
                            $insuranceId = null;
                            if (isset($_POST['insurance'])) {
                                $insuranceId = $_POST['insurance'];
                                $insurance1 = new HealthInsurance("delta", $insuranceId, $conn);
                                $chargeTo = $insurance1->getInsuranceName();
                            }
                            foreach ($listOfServices as $service1) {
                                $index = sizeof($invoicedServiceList);
                                $invoicedServiceList[$index] = array();
                                $invoicedServiceList[$index]['serviceId'] = $service1->getServiceId();
                                $invoicedServiceList[$index]['unitCost'] = (isset($_POST['ucost'][$service1->getServiceId()]) ? floatVal($_POST['ucost'][$service1->getServiceId()]) : $service1->getAmount());
                                $invoicedServiceList[$index]['quantity'] = (isset($_POST['quantity'][$service1->getServiceId()]) ? intval($_POST['quantity'][$service1->getServiceId()]) : 1);
                            }
                            $invoice1 = Finance::raisePatientInvoice($conn, $login1, $systemTime1, $financeQueue1, $invoicedServiceList, $currencyId, $chargeTo, $insuranceId, 100, null, null, null, $discount, $_POST['comments'], !$enableRollBack);
                            $invoice1->setExtraFilter(__object__::getRandomMD5Key(rand(10, 99)));
                            $invoice1->update(!$enableRollBack);
                            //Write on PatientLog 
                            switch ($invoice1->getActionStage()->getStageId()) {
                                case (PatientMovementStage::$__NEW_REGISTRATION):
                                    PatientFile::addNewRegistrationInvoiceLog($conn, $systemTime1, $financeQueue1->getVisit(), $login1, $invoice1, $financeQueue1->getVisit()->getTemporaryStringHolder(), !$enableRollBack);
                                    break;
                                case (PatientMovementStage::$__CONTINUING_VISIT):
                                    PatientFile::addContinuingVisitInvoiceLog($conn, $systemTime1, $financeQueue1->getVisit(), $login1, $invoice1, $financeQueue1->getVisit()->getTemporaryStringHolder(), !$enableRollBack);
                                    break;
                                case (PatientMovementStage::$__LABORATORY_EXAMINATION):
                                    $patientExaminationQueue1 = Registry::getInstance("Data", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($patientExaminationQueue1)) throw new Exception("Could not get reference Patient Examination Queue");
                                    PatientFile::addExaminationQueueInvoiceLog($conn, $systemTime1, $financeQueue1->getVisit(), $login1, $invoice1, $patientExaminationQueue1->getTemporaryStringHolder(), !$enableRollBack);
                                    break;
                                case (PatientMovementStage::$__NURSE_STATION):
                                    $nurseStationQueue1 = Registry::getInstance("Delta", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($nurseStationQueue1)) throw new Exception("Could not get Nurse Station reference");
                                    PatientFile::addNurseStationQueueInvoiceLog($conn, $systemTime1, $financeQueue1->getVisit(), $login1, $invoice1, $nurseStationQueue1->getBundleCode(), ! $enableRollBack);
                                    break;
                                case (PatientMovementStage::$__PHARMACY):
                                    $patientDrugQueue1 = Registry::getInstance("Delta", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($patientDrugQueue1)) throw new Exception("Could not get Patient Drug Queue reference");
                                    PatientFile::addPatientDrugQueueInvoiceLog($conn, $systemTime1, $financeQueue1->getVisit(), $login1, $invoice1, $patientDrugQueue1->getBundleCode(), ! $enableRollBack);
                                    break;
                                case (PatientMovementStage::$__ADMISSION):
                                    $patientAdmissionQueue1 = Registry::getInstance("Delta", $conn, $invoice1->getTemporaryObjectHolder());
                                    if (is_null($patientAdmissionQueue1)) throw new Exception("Could not get Patient Admission Queue reference");
                                    PatientFile::addPatientAdmissionInvoiceLog($conn, $systemTime1, $financeQueue1->getVisit(), $login1, $invoice1, $patientAdmissionQueue1->getBundleCode(), ! $enableRollBack);
                                    break;
                            }
                            $financeQueue1->delete(!$enableRollBack);
                            $invoiceNumber = $invoice1->getInvoiceNumber();
                            $totalSavedInvoiceAmount = $invoice1->getAmount();
                            $conn->commit();
                            $enableRollBack = false;
                    ?>
                            <div class="document-creator m-2">
                                <?= __data__::showPrimaryAlert("Invoice [ $invoiceNumber ] of amount [ $totalSavedInvoiceAmount ], were successful created") ?>
                            </div>
                            <?php
                            if (Authorize::isAllowable2($conn, "patientinvoice_read", "normal", "donotsetlog", null, null)) {
                                $pdfpage = ($profile1->getBaseURL()) . "/documents/pdf/__get_document__.php?id=" . ($invoice1->getInvoiceId()) . "&dtype=" . (Documents::$__PDF_INVOICE);
                                $pdfpage = str_replace("//", "/", $pdfpage);
                            ?>
                                <div class="document-controls-bottom-right">
                                    <a target="_blank" href="<?= $pdfpage ?>" class="btn btn-primary btn-control" data-toggle="tooltip" title="Click to Download an invoice in a pdf format"><i class="fa fa-file-pdf-o fa-2x"></i>Download Invoice</a>
                                </div>
                            <?php
                            }
                            ?>
                        <?php
                        } else {
                            $quantity = 1;
                            $t1 = intval($financeQueue1->getTemporaryIntegerHolder());
                            if ($t1 > 1 && sizeof($listOfServices) == 1) {
                                $quantity = $t1;
                            }
                            $savedQuantity = $quantity;
                        ?>
                            <div class="document-creator m-2">
                                <div class="bg-primary p-1">
                                    <div class="bg-warning p-1">
                                        <div class="bg-primary p-1">
                                            <div class="bg-white p-1">
                                                <!--Begin 0001-->
                                                <h3 class="text-center">Invoice for <i><?= $financeQueue1->getPatient()->getPatientName() ?></i></h3>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <form method="POST" id="__form_01__">
                                                            <input type="hidden" name="page" value="<?= $page ?>" />
                                                            <input type="hidden" name="qid" value="<?= $_REQUEST['qid'] ?>" />
                                                            <input type="hidden" name="invoice_generate" value="inkx" />
                                                            <?php
                                                            $service0 = $listOfServices[0];
                                                            $currency0 = $service0->getCurrency();
                                                            ?>
                                                            <div class="tabular-data">
                                                                <table class="table">
                                                                    <thead class="thead-datk">
                                                                        <th colspan="6" class="text-center"><?= $currency0->getCurrencyName() ?>(<?= $currency0->getCode() ?>)</th>
                                                                    </thead>
                                                                    <thead class="thead-dark">
                                                                        <th scope="col"></th>
                                                                        <th>Service Name</th>
                                                                        <th>Unit Cost</th>
                                                                        <th>Quantity</th>
                                                                        <th>Amount</th>
                                                                        <th></th>
                                                                    </thead>
                                                                    <tbody class="data-body">
                                                                        <?php
                                                                        $count = 0;
                                                                        $totalAmount = 0;
                                                                        $quantityArray1 = __object__::string2Array($financeQueue1->getQuantityString());
                                                                        if (is_null($quantityArray1)) $quantityArray1 = array();
                                                                        foreach ($listOfServices as $service1) {
                                                                            $sn = $count + 1;
                                                                            $totalAmount += $service1->getAmount();
                                                                            if ($service1->getCurrency()->getCurrencyId() != $currency0->getCurrencyId()) throw new Exception("[ $sn ] : Could not build invoice with mixed currencies");
                                                                            $serviceId = $service1->getServiceId();
                                                                            $quantity = isset($quantityArray1[$serviceId]) ? $quantityArray1[$serviceId] : $savedQuantity;
                                                                        ?>
                                                                            <tr class="data-row">
                                                                                <th scope="row"><?= $sn ?></th>
                                                                                <td><?= $service1->getServiceName() ?></td>
                                                                                <td class="cell-unit-cost"><input class="text-right form-control" type="number" name="ucost[<?= $service1->getServiceId() ?>]" value="<?= $service1->getAmount() ?>" /></td>
                                                                                <td class="cell-quantity"><input class="text-right form-control" type="number" name="quantity[<?= $service1->getServiceId() ?>]" value="<?= $quantity ?>"  <?= ($service1->isCountable()) ? "": "readonly" ?>/></td>
                                                                                <td class="cell-total text-right"><?= number_format($service1->getAmount(), 2, ".", ",") ?></td>
                                                                                <td>
                                                                                    <input type="hidden" name="service[<?= $count ?>]" value="<?= $service1->getServiceId() ?>" />
                                                                                    <input type="hidden" class="row-value" value="<?=  $service1->getAmount() ?>"/>
                                                                                    <input checked disabled name="service-disabled-for-now[<?= $count ?>]" value="<?= $service1->getServiceId() ?>" class="form-check-input" type="checkbox" />
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                            $count++;
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="4"><i>SUB - TOTAL </i></td>
                                                                            <td><i><b><span id="sub-total"><?= number_format($totalAmount, 2, ".", ",") ?></span></b></i></td>
                                                                            <td><input type="hidden" id="sub-total-value" value=<?= $totalAmount ?>/></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="discount mt-2">
                                                                <div class="form-group row">
                                                                    <label for="discount" class="col-form-label offset-md-6 col-md-2">Discount</label>
                                                                    <div class="col-md-4">
                                                                        <input type="number" id="discount" name="discount" value="0" class="form-control text-right" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="chargeTo mt-2">
                                                                <?= __data__::createMutualExclusiveControls($conn, "PatientInvoice", "Invoice will be charged to", array(
                                                                    "pname" => "insurance",
                                                                    "caption" => "Medical Insurance",
                                                                    "required" => true
                                                                ), array(
                                                                    "pname" => "chargeTo",
                                                                    "caption" => "Invoice Name",
                                                                    "value" => $financeQueue1->getPatient()->getPatientName(),
                                                                    "title" => "Name that will appear on the invoice, if you leave black the patient name will be used instead"
                                                                ), $financeQueue1->getPatientCase()->isInsured(), false, null) ?>
                                                            </div>
                                                            <div class="comments mt-2">
                                                                <?= __data__::createFormTextInput("PatientInvoice", "comments", "Comments", is_null($financeQueue1->getComments()) ? "" : $financeQueue1->getComments(), false) ?>
                                                            </div>
                                                            <div class="total mt-2">
                                                                TOTAL INVOICE VALUE : <b><i><span data-amount="<?= $totalAmount ?>" id="total-invoice-value"><?= number_format($totalAmount, 2, '.', ',') ?></span></i></b>
                                                            </div>
                                                            <br />
                                                            <div>
                                                                <div id="__error_01__" class="p-2 ui-sys-error-message"></div>
                                                                <button type="button" class="btn-general-submit btn-execute-on-click btn btn-primary btn-block btn-click-default" data-form-submit="__form_01__" data-form-error="__error_01__">Generate Invoice</button>
                                                            </div>
                                                            <input type="hidden" name="currencyId" value="<?= $currency0->getCurrencyId() ?>" />
                                                        </form>
                                                    </div>
                                                </div>
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
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Invoice</a></i>
                        <?php
                        if (!is_null($invoice1) && Authorize::isAllowable($config1, "patientreceipt_create", "normal", "donotsetlog", null, null)) {
                        ?>
                            &nbsp;&nbsp; <i style="font-size: 1.1em;"><a class="card-link" href="<?= $thispage ?>?page=patientreceipt_create&id=<?= $invoice1->getInvoiceId() ?>&efilter=<?= $invoice1->getExtraFilter() ?>">Make payment</a></i>
                        <?php
                        }
                        ?>
                        <br />
                        <span class="text-muted"><i>Rule: patientinvoice_create</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        function number_padding(number, length = 3, pad = "0")   {
            var number_s = "" + number;
            for (var i = number_s.length; i < length; i++)   number_s = pad + number_s;
            return number_s;
        }
        function number_format(number, decimals, dec_point = '.', thousands_sep = ',') {
            var empty = "";
            var thousands = 1000;
            var number_s = "";
            do {
                var rem = (number % thousands);
                number = Math.floor(number / thousands);
                if (number > 0) rem = number_padding(rem, 3, "0");
                if (number_s == empty) number_s += rem;
                else number_s = rem + thousands_sep + number_s;
            } while (number > 0);
            return number_s;
        }
        function updateTotal($row1) {
            var $subTotal1 = $('#sub-total-value');
            if (! $subTotal1.length) return false;
            var $discount1 = $('#discount');
            if (! $discount1.length) return false;
            var $total1 = $('#total-invoice-value');
            if (! $total1.length) return false;
            var discount = parseFloat($discount1.val());
            var subtotal = parseFloat($subTotal1.val());
            if (discount <= subtotal)   {
                $total1.text(number_format(subtotal - discount, 2, '.', ','));
            }
        }
        function updateSubTotal($row1)   {
            var sum = 0;
            var $target1 = $('#sub-total');
            if (! $target1.length) return false;
            var $targetValue1 = $('#sub-total-value');
            if (! $targetValue1.length) return false;
            $row1.closest('tbody.data-body').find('tr.data-row input.row-value').each(function(i, val)  {
                var $rowValue1 = $(this);
                sum += parseFloat($rowValue1.val());
            });
            $targetValue1.val(sum);
            $target1.text(number_format(sum, 2, '.', ','));
            updateTotal($row1);
        }
        function updateRowAmountBeforeDiscount($row1, $unitCost1, $quantity1) {
            if (!$unitCost1.length) return false;
            if (!$quantity1.length) return false;
            $targetCell1 = $row1.find('td.cell-total');
            if (!$targetCell1.length) return false;
            $rowValue1 = $row1.find('input.row-value');
            if (! $rowValue1.length) return false;
            var total = parseFloat($unitCost1.val()) * parseFloat($quantity1.val());
            $rowValue1.val(total);
            $targetCell1.text(number_format(total, 2, '.', ','));
            updateSubTotal($row1);
        }
        $('td.cell-unit-cost > input.form-control').on('change', function(e) {
            $unitCost1 = $(this);
            $row1 = $unitCost1.closest('tr.data-row');
            if (!$row1.length) return false;
            $quantity1 = $row1.find('td.cell-quantity > input.form-control');
            updateRowAmountBeforeDiscount($row1, $unitCost1, $quantity1);
        });
        $('td.cell-quantity > input.form-control').on('change', function(e) {
            $quantity1 = $(this);
            $row1 = $quantity1.closest('tr.data-row');
            if (!$row1.length) return false;
            $unitCost1 = $row1.find('td.cell-unit-cost > input.form-control');
            updateRowAmountBeforeDiscount($row1, $unitCost1, $quantity1);
        });
        $('#discount').on('change', function(e) {
            var $text1 = $(this);
            var $span1 = $('#total-invoice-value');
            if (!$span1.length) return false;
            var $totalAmountBeforeDiscount1 = $('#sub-total-value');
            if (! $totalAmountBeforeDiscount1.length) return false;
            var totalAmount = parseFloat($totalAmountBeforeDiscount1.val());
            var t1 = $.trim($text1.val());
            if (t1 == "") t1 = 0;
            var discountAmount = parseFloat(t1);
            if (discountAmount > totalAmount) {
                discountAmount = 0;
            }
            $text1.val(discountAmount);
            totalAmount -= discountAmount;
            $span1.text(number_format(totalAmount, 2, '.', ','));
        });
        //Trigger Quantity Change
        $('td.cell-quantity > input.form-control').trigger('change');
    })(jQuery);
</script>