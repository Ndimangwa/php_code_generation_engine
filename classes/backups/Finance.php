<?php
class Finance
{
    public static function raisePatientInvoice($conn, $login1, $systemTime1, $financeQueue1, $listOfServices, $currencyId, $chargeTo = "self", $insuranceId = null, $numberOfValidDays = 100, $notifyRef = null, $notifyForwardURL = null, $approvalDataId = null, $totalDiscountedAmount = null, $comments = null, $rollback = true, $default_select_empty_value = null)
    {
        //$listOfServices = [{serviceId, unitCost, quantity}]
        //From financeQueue1 
        $invoiceNumber = Hospital::generateInvoiceNumber($conn, $rollback);
        $bundleCode = __object__::getRandomMD5Key($invoiceNumber);
        $visit1 = $financeQueue1->getVisit();
        $payload = array(
            "timeOfCreation" => $systemTime1->getTimestamp(),
            "timeOfUpdation" => $systemTime1->getTimestamp(),
            "invoiceNumber" => $invoiceNumber,
            "actionStage" => $financeQueue1->getActionStage()->getStageId(),
            "currency" => $currencyId,
            "amountBeforeDiscount" => 0,
            "discount" => 0,
            "amount" => 0,
            "chargeTo" => $chargeTo,
            "bundleCode" => $bundleCode,
            "visit" => $visit1->getVisitId(),
            "patientCase" => $visit1->getPatientCase()->getCaseId(),
            "patient" => $visit1->getPatientCase()->getPatient()->getPatientId(),
            "preparedBy" => $login1->getLoginId(),
            "preparedByCaption" => $login1->getLoginName(),
            "closed" => 0
        );
        if (!is_null($comments)) $payload['comments'] = $comments;
        $objectreference = $financeQueue1->getTemporaryObjectHolder();
        if (!is_null($objectreference)) $payload['temporaryObjectHolder'] = $objectreference;
        $t1 = $financeQueue1->getTemporaryStringHolder();
        if (! is_null($t1)) $payload['temporaryStringHolder'] = $t1;
        $t1 = intval($financeQueue1->getTemporaryIntegerHolder());
        if ($t1 > 0) $payload['temporaryIntegerHolder'] = $t1;
        $t1 = $financeQueue1->getTrackMonitor();
        if (! is_null($t1)) $payload['trackMonitor'] = $t1;
        $invoice1 = new PatientInvoice("Ndimangwa", __data__::insert($conn, "PatientInvoice", $payload, $rollback, $default_select_empty_value), $conn);
        $totalAmount = 0;
        $listOfLogSequence = array();
        foreach ($listOfServices as $serviceArray1) {
            $service1 = null;
            $amount = 0;
            $quantity = 1;
            $unitCost = 0;
            //Step 1. Load Currency and Amount 
            if (!isset($serviceArray1['serviceId'])) throw new Exception("Service Reference Should be set");
            //Default Configuration with Service
            $service1 = new Service("ndima", $serviceArray1['serviceId'], $conn);
            $currency1 = $service1->getCurrency();
            $serviceCaption = $service1->getServiceName();
            if ($currency1->getCurrencyId() != $currencyId) throw new Exception("[ raiseInvoice() ] , Service [ $serviceCaption ] can not be charged under the selected currency scheme");
            $unitCost = $service1->getAmount();
            //End of Default Configuration with Service
            if (isset($serviceArray1['quantity'])) {
                $quantity = $serviceArray1['quantity'];
            }
            if (isset($serviceArray1['unitCost'])) {
                $unitCost = $serviceArray1['unitCost'];
            }
            $amount = $unitCost * $quantity;
            //You may override on approval
            $totalAmount += floatval($amount);
            //Step 2. Update Service Log
            $dataArray1 = array(
                "timeOfCreation" => $systemTime1->getTimestamp(),
                "timeOfUpdation" => $systemTime1->getTimestamp(),
                "bundleCode" => $bundleCode,
                "documentReferenceNumber" => $invoice1->getInvoiceNumber(),
                "documentReference" => $invoice1->getObjectReferenceString(),
                "currency" => $currencyId,
                "amount" => $amount,
                "unitCost" => $unitCost,
                "quantity" => $quantity
            );
            if (!is_null($service1)) {
                $dataArray1['service'] = $service1->getServiceId();
                $dataArray1['serviceCaption'] = $service1->getServiceName();
                $dataArray1['serviceReference'] = $service1->getObjectReferenceString();
                $dataArray1['typeReference'] = $service1->getCategory()->getObjectReferenceString();
                $dataArray1['typeCaption'] = $service1->getCategory()->getCategoryName();
            }
            $listOfLogSequence[sizeof($listOfLogSequence)] = __data__::insert($conn, "ServiceLogSequence", $dataArray1, $rollback);
        }
        //Update invoice
        if (sizeof($listOfLogSequence) == 0) {
            $invoice1->delete($rollback);
            throw new Exception("[ raisePatientInvoice() ] : Could not process invoice details");
        }
        if (is_null($totalDiscountedAmount)) $totalDiscountedAmount = 0;

        $invoice1->setAmountBeforeDiscount($totalAmount);
        if (true /*!is_null($approvalDataId) Pending*/) {
            //$invoice1->setUpdateApproval($approvalDataId);
            if ($totalDiscountedAmount > $totalAmount) throw new Exception("You can not discount more that the total invoice amount");
            $totalAmount -= $totalDiscountedAmount;
            $invoice1->setDiscount($totalDiscountedAmount);
        }
        $invoice1->setAmount($totalAmount);
        $invoice1->setBalance($totalAmount);
        $invoice1->setInsured("0");
        if (!is_null($insuranceId)) {
            $invoice1->setInsured("1");
            $invoice1->setInsurance($insuranceId);
        }
        $invoice1->setListOfLogSequence(implode(",", $listOfLogSequence));
        $invoice1->update($rollback);
        //Step 5. Build Notification
        if (!is_null($notifyRef)) {
            Notification::createNotification($conn, $systemTime1, "Invoice Raised ( $invoiceNumber )", $notifyRef, NotificationCategory::$__INVOICE_RAISED, $notifyForwardURL, $numberOfValidDays, $rollback);
        }
        //invoiceId, timeOfCreation, createdBy, services [ serviceId, amount, billTo,  ]
        return $invoice1;
    }
    public static function issuePatientReceipt($conn, $login1, $systemTime1, $invoiceId, $receivedAmount, $notifyRef = null, $notifyForwardURL = null, $payerName = null, $payerPhone = null, $rollback = true)
    {
        $invoice1 = new PatientInvoice("Delta Variant", $invoiceId, $conn);
        $invoiceNumber = $invoice1->getInvoiceNumber();
        //Step 00: Check if the invoice is not closed
        if ($invoice1->isClosed()) throw new Exception("[ issuePatientReceipt() ] : Invoice [ $invoiceNumber ] is already closed");
        //Step 01: Check remaining Amount for the stated invoice 
        $remainingAmount = $invoice1->getUnpaidAmount();
        //Step 02: Check the amount you receive, is not greater than remaining amount 
        if ($receivedAmount > $remainingAmount) throw new Exception("[ issuePatientReceipt() ] : Invoice [ $invoiceNumber ] , you are receiving more amount than the invoice remaining amount");
        //Step 03: Generate receipt number 
        $receiptNumber = Hospital::generateReceiptNumber($conn, $rollback);
        //Step 04: Create a receipt 
        $dataArray1 = array(
            "timeOfCreation" => $systemTime1->getTimestamp(),
            "timeOfUpdation" => $systemTime1->getTimestamp(),
            "receiptNumber" => $receiptNumber,
            "invoice" => $invoice1->getInvoiceId(),
            "amount" => $receivedAmount,
            "preparedBy" => $login1->getLoginId()
        );
        if (!is_null($payerName)) {
            $dataArray1["payerName"] = $payerName;
        } else {
            $dataArray1["payerName"] = $invoice1->getChargeTo();
        }
        $savedPayerName = $dataArray1["payerName"];
        if (!is_null($payerPhone)) $dataArray1["payerPhone"] = $payerPhone;
        $receipt1 = new PatientReceipt("Ndimangwa", __data__::insert(
            $conn,
            "PatientReceipt",
            $dataArray1,
            $rollback
        ), $conn);
        //Step 05: Check Again remaining Amount for stated invoice 
        $remainingAmount = $invoice1->getUnpaidAmount();
        $comments = "Remaing Amount : $remainingAmount";
        $invoice1->setTotalPaid($invoice1->getPaidAmount());
        $invoice1->setBalance($remainingAmount);
        //if Amount is 0, close the invoice
        if ($remainingAmount == 0) {
            $invoice1->setClosed(1);
            $comments = "Invoice Cleared : $remainingAmount";
        }
        $invoice1->update($rollback);
        $logCaption = "Receipt Issued ( $receiptNumber )";
        //Step 06: Build Financial Log
        FinancialLog::createLog($conn, $login1, $systemTime1, $logCaption, $savedPayerName, FinancialLogCategory::$__RECEIPT, $invoice1->getCurrency()->getCurrencyId(), $receivedAmount, true, $remainingAmount, $comments, $rollback);
        //Step 07: Update Notification Accordingly
        if (!is_null($notifyRef)) {
            Notification::createNotification($conn, $systemTime1, $logCaption, $notifyRef, NotificationCategory::$__RECEIPT_ISSUED, $notifyForwardURL, 1000, $rollback);
        }
        //Refresh this receipt so to point to the updated invoice
        return (new PatientReceipt("Delta", $receipt1->getReceiptId(), $conn));
    }
}
