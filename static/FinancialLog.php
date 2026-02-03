<?php 
class FinancialLog  {
    public static function createLog($conn, $login1, $systemTime1, $caption, $targetName, $categoryId, $currencyId, $amount, $credit = true, $remainingAmount = 0, $comments = null, $rollback = true)  {
        $iscredit = 0;
        if ($credit) $iscredit = 1;
        $dataArray1 = array(
            "timeOfCreation" => $systemTime1->getTimestamp(),
            "timeOfUpdation" => $systemTime1->getTimestamp(),
            "category" => $categoryId,
            "username" => $login1->getLoginName(),
            "target" => $targetName,
            "caption" => $caption,
            "credit" => $iscredit,
            "currency" => $currencyId,
            "amount" => $amount,
            "remainingAmount" => $remainingAmount
        );
        if (! is_null($comments)) $dataArray1["extraInformation"] = $comments;
        return __data__::insert($conn, "FinancialLog", $dataArray1, $rollback);
    }
}
?>