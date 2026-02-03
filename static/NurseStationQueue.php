<?php 
class NurseStationQueue {
    public static function getNurseStationQueuesForMedicalConsultationQueue($conn, $medicalConsultationQueueId) {
        $list = array();
        try {
        $records = __data__::getSelectedRecords($conn, "SELECT queueId FROM _nurse_station_queue WHERE consultationQueue = '$medicalConsultationQueueId'");
        foreach ($records['column'] as $row)    {
            $list[sizeof($list)] = new NurseStationQueue("Delta", $row['queueId'], $conn);
        }
    } catch (Exception $e)  {}
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
}
