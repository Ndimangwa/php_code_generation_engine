<?php 
class NotificationManager {
    public static function getListOfNotificationManagers($conn, $opname)    {
        $jresult = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
            array('_notificationManager'),
            array('managerId'),
            array('opname' => $opname)
        ) ,$conn);
        if (is_null($jresult)) throw new Exception("[ NotificationManager ] : Could not return list");
        $jArray1 = json_decode($jresult, true);
        if (is_null($jArray1)) throw new Exception("[ NotificationManager ] : Could not decode result list");
        if ($jArray1['code'] != 0) throw new Exception("[ NotificationManager ] : ".$jArray1['message']);
        if ($jArray1['count'] == 0) throw new Exception("[ NotificationManager ] : Returned Empty List");
        $list1 = array();
        foreach ($jArray1['row'] as $row) $list1[sizeof($list1)] = $row['managerId'];
        return $list1; 
    }
}
?>