<?php 
class MedicalDoctor {
    public static function loadAllData($conn)   {
        $query = "SELECT doctorId, fullName FROM _medicalDoctor as d, _login as l WHERE (d.loginId = l.loginId)";
        $records = __data__::getSelectedRecords($conn, $query, false);
        $list = array();
        foreach ($records['column'] as $record1)    {
            $index = sizeof($list);
            $list[$index]['__id__'] = $record1['doctorId'];
            $list[$index]['__name__'] = $record1['fullName'];
        }
        return $list;
    }
}
?>