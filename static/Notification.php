<?php 
class Notification {
    public static function createNotification($conn, $systemTime1, $caption, $targetReference, $categoryId, $forwardURL = null, $numberOfValidDays = 100, $rollback = true) {
        $dataArray1 = array(
            "timeOfCreation" => $systemTime1->getTimestamp(),
            "timeOfUpdation" => $systemTime1->getTimestamp(),
            "caption" => $caption,
            "numberOfValidDays" => $numberOfValidDays,
            "targetReference" => $targetReference,
            "category" => $categoryId,
            "closed" => 0
        );
        if (! is_null($forwardURL)) $dataArray1['forwardURL'] = $forwardURL;
       return __data__::insert($conn, "Notification", $dataArray1, $rollback);
    }
}
?>