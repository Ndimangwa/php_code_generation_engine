<?php 
class GeneralMedicalWorkingBlock    {
    public static function getWorkingBlockForClassAndConsultationQueue($conn, $classname, $queueId) {
        $tablename = Registry::getTablename($classname);
        $id0col = Registry::getId0Columnname($classname);
        $query = "SELECT $id0col FROM $tablename WHERE consultationQueue = '$queueId'";
        $object1 = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, true);
            $object1 = Registry::getObjectReference("Delta", $conn, $classname, $records['column'][0][$id0col]);
        } catch (Exception $e)  {
            $object1 = null;
        }
        return $object1;
    }
    public static function getWorkingBlockForConsultationQueue($conn, $queueId)    {
        $tablename = self::getTablename();
        $id0col = self::getId0Columnname();
        $query = "SELECT $id0col FROM $tablename WHERE consultationQueue = '$queueId'";
        $object1 = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, true);
            $object1 = Registry::getObjectReference("Delta", $conn, self::getClassname(), $records['column'][0][$id0col]);
        } catch (Exception $e)  {
            $object1 = null;
        }
        return $object1;
    }
}
?>