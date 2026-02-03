<?php 
//Heavily relies on DataFile
class CSV2JSON  {
    private static $__STANDARD_TYPE_COLUMN = "stdtype";
    private static function format($dataStorageArr1)   {
        $jsonArray1 = array();
        foreach ($dataStorageArr1 as $classname => $classBlockArray1)   {
            $classIndex = sizeof($jsonArray1);
            $jsonArray1[$classIndex] = array();
            $jsonArray1[$classIndex]['class'] = $classname;
            $jsonArray1[$classIndex]['table'] = $classBlockArray1['table'];
            //Attach columns
            $jsonArray1[$classIndex]['columns'] = $classBlockArray1['columns'];
        }
        return $jsonArray1;
    }
    private static function transformToNestedArray($larray1, $val, $levelArray1, $index = 0)    {
        if (sizeof($levelArray1) == $index) {
            return $val;
        }
        if (! isset($larray1[$levelArray1[$index]])) $larray1[$levelArray1[$index]] = array();
        $larray1[$levelArray1[$index]] = self::transformToNestedArray($larray1[$levelArray1[$index]], $val, $levelArray1, $index + 1); 
        return $larray1;
    }
    private static function applyTypeStandards($dataLine1, $standardDataFile1)  {
        if (is_null($standardDataFile1)) return $dataLine1;
        $type = $dataLine1->getValuesAtColumns("type", 0);
        if (is_null($type)) return $dataLine1;
        $standardDataLine1 = null;
        try {
            $standardDataLine1 = $standardDataFile1->filterDataLines(self::$__STANDARD_TYPE_COLUMN, $type, 0);
        } catch (Exception $e)  {
            $standardDataLine1 = null;
        }   
        if (is_null($standardDataLine1)) return $dataLine1;
        //use standardDataLine1 to update dataLine1 if no value specified 
        foreach ($standardDataLine1->getHeaderLine()->getArray() as $header)    {
            $standardValue = $standardDataLine1->getValuesAtColumns($header, 0);
            if (is_null($standardValue) || $standardValue == "") continue;
            $currentValue = $dataLine1->getValuesAtColumns($header, 0);
            if (is_null($currentValue) || $currentValue == "" || $header == "type")  $dataLine1->setValuesAtColumns($header, $standardValue, 0);
        } 
        return $dataLine1->synchronize();
    }
    public static function transform($csvfile, $standardTypeFile = null /*If present must have a stdtype column*/)   {
        /*
        Will transform CSVFile having a program structure to JSON structure 
        */
        $dataFile1 = new DataFile($csvfile);
        $standardDataFile1 = null; if (! is_null($standardTypeFile)) $standardDataFile1 = new DataFile($standardTypeFile, array(self::$__STANDARD_TYPE_COLUMN));
        $prevclassname = null;
        $prevtablename = null;
        $classname = null;
        $tablename = null;
        //
        $dataStorageArr1 = array();
        if (! in_array("class", $dataFile1->getHeaderLine()->getArray())) throw new Exception("class is not found in column definition");
        if (! in_array("table", $dataFile1->getHeaderLine()->getArray())) throw new Exception("table is not found in column definition");
        if (! in_array("colname", $dataFile1->getHeaderLine()->getArray())) throw new Exception("colname is not found in column definition");
        foreach ($dataFile1->getDataLines() as $dataLine1)  {
            //per-row basis 
            if ($dataLine1->isEmpty()) continue;
            $classname = $dataLine1->getValuesAtColumns("class", 0);
            $tablename = $dataLine1->getValuesAtColumns("table", 0);
            $colname = $dataLine1->getValuesAtColumns("colname", 0);
            if ($classname == "" && is_null($prevclassname)) throw new Exception("Could not figure the class");
            if ($classname == "") $classname = $prevclassname; else $prevclassname = $classname;
            if ($tablename == "" && is_null($prevtablename)) throw new Exception("Could not figure the table");
            if ($tablename == "") $tablename = $prevtablename; else $prevtablename = $tablename;
            //We need to Update this dataLine1 , priory continuation at this point 
            $dataLine1 = self::applyTypeStandards($dataLine1, $standardDataFile1);
            //Preparing data-structure 
            if (! isset($dataStorageArr1[$classname])) {
                $dataStorageArr1[$classname] = array();
                $dataStorageArr1[$classname]['table'] = $tablename;
            }
            if (! isset($dataStorageArr1[$classname]['columns']))   {
                $dataStorageArr1[$classname]['columns'] = array();
            }
            $currIndex = sizeof($dataStorageArr1[$classname]['columns']);
            if (! isset($dataStorageArr1[$classname]['columns'][$currIndex]))    {
                $dataStorageArr1[$classname]['columns'][$currIndex] = array();
                $dataStorageArr1[$classname]['columns'][$currIndex]['colname'] = $colname;
            }
            //per-col basis
            foreach ($dataFile1->getHeaderLine()->getArray() as $colHeaderBundle)   {
                //$colHeaderBundle as settings.data.unique
                $colval = $dataLine1->getValuesAtColumns($colHeaderBundle, 0); //1st occurance
                if (is_null($colval) || $colval == "") continue;
                if (strtolower($colval) == "true") $colval = true;
                if (strtolower($colval) == "false") $colval = false;
                if (in_array($colHeaderBundle, array('class', 'table', 'colname'))) continue;
                //We have $dataStorageArr1[$classname][$colname] = array();
                //echo "\n[ classname , colname ] = [ $classname , $colname ]\n";
                $dataStorageArr1[$classname]['columns'][$currIndex] = self::transformToNestedArray($dataStorageArr1[$classname]['columns'][$currIndex], $colval, explode(".", $colHeaderBundle), 0);
            }
        }
        return json_encode(self::format($dataStorageArr1));
    }
}
?>