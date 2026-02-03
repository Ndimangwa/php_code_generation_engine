<?php 
class Dummy {
    public static function filterRecords($conn, $filterPropertiesArray1 = null)   {
        $idcolumn = self::getId0Columnname();
        $classname = self::getClassname();
        $resultSet = null;
        //Note: in filtering the columns are db-names and here we have properties 
        $filterColumnsArray1 = array();
        foreach ($filterPropertiesArray1 as $pname => $value)   {
            $colname = Registry::property2column($classname, $pname);
            if (! is_null($colname))    {
                $filterColumnsArray1[$colname] = $value;
            }
        }
        if (sizeof($filterColumnsArray1) == 0) return null;
        try {
            $t1 = __data__::selectQuery($conn, $classname, array($idcolumn), $filterColumnsArray1, false);
            $resultSet = $t1['column'];
        } catch (Exception $e)  {
            $resultSet = null;
        }
        if (is_null($resultSet)) return null;
        //
        $listOfRecords = array();
        foreach ($resultSet as $record1)    {
            $t1 = Registry::getObjectReference("Delta", $conn, $classname, $record1[$idcolumn]);
            if (! is_null($t1))	{
		        $listOfRecords[sizeof($listOfRecords)] = $t1;
	        }
        }
        if (sizeof($listOfRecords) == 0) $listOfRecords = null;
        return $listOfRecords;
    } 
}
?>
