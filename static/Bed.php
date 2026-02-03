<?php 
class Bed {
    public static function getListOfOccupiedBeds($conn)  {
        $query = "SELECT bedId FROM _bed WHERE occupied = 1";
        $records = null;
        try {
            $records = __data__::getSelectedRecords($conn, $query, false);
        } catch (Exception $e)  {
        
        }
        $list = array();
        if (! is_null($records))    {
            foreach ($records['column'] as $record1)    {
                $list[sizeof($list)] = new Bed("Delta", $record1['bedId'], $conn);
            }
        }
        if (sizeof($list) == 0) $list = null;
        return $list;
    }
}
?>