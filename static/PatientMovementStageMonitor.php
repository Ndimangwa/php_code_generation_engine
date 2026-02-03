<?php

use PatientMovementStageMonitor as GlobalPatientMovementStageMonitor;

class PatientMovementStageMonitor   {
    public static function getMonitor($conn, $visitId, $stageId, $bundleCode, $group = 0)  {
        $tablename = self::getTablename();
        $query = "SELECT monitorId FROM $tablename WHERE visitId='$visitId' AND stageId='$stageId' AND bundleCode='$bundleCode' AND _group = '$group'";
        $monitor1 = null;
        try {
            $t1 = __data__::getSelectedRecords($conn, $query, true);
            $monitor1 = new PatientMovementStageMonitor("Delta", $t1['column'][0]['monitorId'], $conn);
        } catch (Exception $e)  {
            $monitor1 = null;
        }
        return $monitor1;
    }
    public static function getAllMonitorsForAStage($conn, $visitId, $stageId)   {
        $tablename = self::getTablename();
        $query = "SELECT monitorId FROM $tablename WHERE visitId='$visitId' AND stageId='$stageId'";
        $listOfMonitors = array();
        try {
            $recordList = __data__::getSelectedRecords($conn, $query, false);
            foreach ($recordList['column'] as $row1)    {
                $listOfMonitors[sizeof($listOfMonitors)] = new PatientMovementStageMonitor("Delta", $row1['monitorId'], $conn);
            }
        } catch (Exception $e)  {
            //Do-Nothing
        }
        if (sizeof($listOfMonitors) == 0) $listOfMonitors = null;
        return $listOfMonitors;
    }
}
?>