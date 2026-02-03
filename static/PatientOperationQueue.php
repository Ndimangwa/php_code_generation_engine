<?php 
class PatientOperationQueue {
    public static function getListOfPatientOperations($conn, $queueId, $extraFilterPayload = null)   {
        $payload = array(
            'operationQueue' => $queueId
        );
        $payload = is_null($extraFilterPayload) ? $payload : array_merge($payload, $extraFilterPayload);
        return ( PatientOperation::filterRecords($conn, $payload) );
    }
    public static function getListOfAttendedServices($conn, $queueId, $extraFilterPayload = null) {
        $listOfPatientOperations = self::getListOfPatientOperations($conn, $queueId, $extraFilterPayload);
        if (is_null($listOfPatientOperations)) return null;
        $listOfServices = array();
        foreach ($listOfPatientOperations as $patientOperation1)    {
            $listOfServices[sizeof($listOfServices)] = $patientOperation1->getService();
        }
        return (sizeof($listOfServices) == 0) ? null : $listOfServices;
    }
    public static function getListOfNotYetAttendedServices($conn, $queueId, $extraFilterPayload = null) {
        $patientOperationQueue1 = Registry::getObjectReference("Delta", $conn, ( self::getClassname() ), $queueId);
        if (is_null($patientOperationQueue1)) return null;
        $listOfServices = $patientOperationQueue1->getListOfServices();
        if (is_null($listOfServices)) return null;
        $listOfAttendedList = self::getListOfAttendedServices($conn, $queueId, $extraFilterPayload);
        if (is_null($listOfAttendedList)) return $listOfServices;
        $listOfNotYetAttendedList = array();
        $listOfAttendedList = __data__::convertListObjectsToArray($listOfAttendedList);
        foreach ($listOfServices as $service1)  {
            if (! in_array(( $service1->getServiceId() ), $listOfAttendedList))  {
                $listOfNotYetAttendedList[sizeof($listOfNotYetAttendedList)] = $service1;
            }
        }
        return (sizeof($listOfNotYetAttendedList) == 0) ? null : $listOfNotYetAttendedList;
    }
}
?>