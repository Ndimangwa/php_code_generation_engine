<?php 
class ServiceToolbox    {
    public static function array2String($array1, $parentKey = null)    {
        if (is_null($array1) || ! is_array($array1)) return "";
        //Currently one level only
        $str1 = "";
        foreach ($array1 as $key => $val)   {
            if (is_null($parentKey)) $currentKey = $key;
            else $currentKey = $parentKey." > ".$key;
            if (is_array($val))   {
                $str1 .= self::array2String($val, $currentKey);
            } else{
                $str1 .= " [ $currentKey ] => $val ; ";
            }
        }
        return $str1;
    }
    public static function convertDataBundleToKeyValueArray($dataBundle1) {
        $listOfColumns = array();
        foreach ($dataBundle1['columns'] as $index => $colname)  {
            $colval = $dataBundle1['values'][$index];
            $listOfColumns[$colname] = $colval;
        }
        if (sizeof($listOfColumns) == 0) $listOfColumns = null;
        return $listOfColumns;
    }
    public static function getDataColumnsFromTheSubmittedData($classname, $dataArray1)    {
        //return only-columns which are found in tables 
        //Remember dataArray carries Object properties , but in future we will have ui-columns too
        $listOfColumns = array();
        $listOfColvals = array();
        $index = 0;
        foreach ($dataArray1 as $pname => $colval)    {
            //We are interested with colname for a moment
            $colname = Registry::property2column($classname, $pname);
            if (! is_null($colname))    {
                //We need to make adjustment if necessary
                $refclass = Registry::getReferenceClass($classname, $pname);
                if ($refclass == "DateAndTime") {
                    try { 
                        //$colval = ~DateAndTime~::~convertFromGUIDateFormatToSystemDateAndTimeFormat($colval);
                        $t1 = DateAndTime::createDateAndTimeFromGUIDate($colval);
                        $colval = $t1->getTimestamp();
                    } catch (Exception $e)    {}
                }
                //This need to be submitted
                $listOfColumns[$index] = $colname;
                $listOfColvals[$index] = $colval;
                $index++;
            }
        }
        $colNamesAndValuesBundle = null;
        if (sizeof($listOfColumns) !== 0) {
            $colNamesAndValuesBundle = array('columns' => $listOfColumns, 'values' => $listOfColvals);
        }
        return $colNamesAndValuesBundle;
    }
    public static function deleteQuery($conn, $classname, $id, $dataArray1)  {
        $whereClause = Registry::getId0Columnname($classname);
        $whereClause = array($whereClause => $id);
        $jresult1 = null;
        try {
            $jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildDelete(
                Registry::getTablename($classname),
                $whereClause
            ), $conn);
        } catch (Exception $e)  {
            die(json_encode(array('code' => 8, 'message' => $e->getMessage())));
        }
        return json_decode($jresult1, true);
    }
    public static function createQuery($conn, $classname, $dataArray1)  {
        $dataBundle1 = self::getDataColumnsFromTheSubmittedData($classname, $dataArray1);
        if (is_null($dataBundle1)) die(json_encode(array('code' => 1, 'query' => 'create', 'message' => 'Columns could not match with the schema')));
        $jresult1 = null;
        try {
            $jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildInsert(
                Registry::getTablename($classname),
                self::convertDataBundleToKeyValueArray($dataBundle1)
            ), $conn);
        } catch (Exception $e)  {
            die(json_encode(array('code' => 8, 'message' => $e->getMessage())));
        }
        return json_decode($jresult1, true);
    }
    public static function updateQuery($conn, $classname, $id, $dataArray1)  {
        if (is_null($id)) die(json_encode(array('code' => 1, 'query' => 'update', 'message' => 'Target class id is not set')));
        $dataBundle1 = self::getDataColumnsFromTheSubmittedData($classname, $dataArray1);
        if (is_null($dataBundle1)) die(json_encode(array('code' => 5, 'query' => 'update', 'message' => 'Columns could not match with the schema')));
        $whereClause = Registry::getId0Columnname($classname);
        $whereClause = array($whereClause => $id);
        $jresult1 = null;
        try {
            $jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildUpdate(
                Registry::getTablename($classname),
                self::convertDataBundleToKeyValueArray($dataBundle1),
                $whereClause
            ), $conn);
        } catch (Exception $e)  {
            die(json_encode(array('code' => 8, 'message' => $e->getMessage())));
        }
        return json_decode($jresult1, true); //[code, message]
    }
    public static function selectQuery($conn, $classname, $id, $dataArray1, $optWhereConstraints = null) {
        //if $id = null , means select all records
        $dataBundle1 = self::getDataColumnsFromTheSubmittedData($classname, $dataArray1);
        if (is_null($dataBundle1)) die(json_encode(array('code' => 5, 'query' => 'select', 'message' => 'Columns could not match with the schema')));
        $whereClause = null;
        $primaryColumn = Registry::getId0Columnname($classname);
        if (! is_null($id)) {
            $whereClause = array($primaryColumn => $id);
        }
        if (! is_null($whereClause) && ! is_null($optWhereConstraints)) $whereClause = array_merge($whereClause, $optWhereConstraints);
        else if (! is_null($optWhereConstraints)) $whereClause = $optWhereConstraints;
        $jresult1 = null;
        //Make sure the primary-column is included
        $columnList = $dataBundle1['columns'];
        if (! in_array($primaryColumn, $columnList)) $columnList[sizeof($columnList)] = $primaryColumn;
        try {
            $jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
                array(Registry::getTablename($classname)),
                $columnList,
                $whereClause
            ), $conn);
        } catch (Exception $e)  {
            die(json_encode(array('code' => 8, 'message' => $e->getMessage())));
        }
        return json_decode($jresult1, true); // [code, message]
    }
}
?>
