<?php 
class PatientTriageQueue    {
    public static function isThereAnyPendingQueue($conn)    {
        $tablename = self::getTablename();
        //Now do query
        $records = null;
        $query = "SELECT COUNT(triageId) as count1 FROM $tablename";
        try {
            $records = __data__::getSelectedRecords($conn, $query, true);
        } catch (Exception $e)  {
            $records = null;
        }   
        return (! is_null($records) && ( $records['column'][0]['count1'] > 0 ));
    }
    public static function isThereAnyPendingQueueForPatient($conn, $visitId)    {
        //We need to base on the current case 
        $visit1 = new PatientVisit("Delta", $visitId, $conn);
        $patientCase1 = $visit1->getPatientCase();
        $caseId = $patientCase1->getCaseId();
        $tablename = self::getTablename();
        //Now do query
        $records = null;
        $query = "SELECT COUNT(triageId) as count1 FROM $tablename WHERE caseId = '$caseId'";
        try {
            $records = __data__::getSelectedRecords($conn, $query, true);
        } catch (Exception $e)  {
            $records = null;
        }   
        return (! is_null($records) && ( $records['column'][0]['count1'] > 0 ));
    }
}
?>