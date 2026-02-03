<?php
class VitalSigns {
    public static function getListOfVitalSignsForPatient($conn, $visitId)    {
        //We need to get this specific case
        $visit1 = new PatientVisit("Delta", $visitId, $conn);
        $patientCase1 = $visit1->getPatientCase();
        $caseId = $patientCase1->getCaseId();
        $list1 = array();
        //Get From Triage First 
        $query = "SELECT triageId FROM _triage WHERE caseId = '$caseId'";
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
        } catch (Exception $e)  {
            $records = null;
        }
        if (! is_null($records))    {
            foreach ($records['column'] as $record1)    {
                $list1[sizeof($list1)] = new Triage("Delta", $record1['triageId'], $conn);
            }
        }
        //Then Get the VitalSigns
        $query = "SELECT signsId FROM  _vital_signs WHERE caseId = '$caseId'";
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
        } catch (Exception $e)  {
            $records = null;
        }
        if (! is_null($records))    {
            foreach ($records['column'] as $record1)    {
                $list1[sizeof($list1)] = new VitalSigns("Delta", $record1['signsId'], $conn);
            }
        }
        if (sizeof($list1) == 0) $list1 = null;
        return $list1;
    }
}
?>