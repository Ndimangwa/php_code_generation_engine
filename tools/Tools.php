<?php 
class Tools {
    public static function insertElementInArray($dataArray1, $itemToInsert, $index = 0) {
        $newDataArray1 = array();
        //Check index should be in range 
        $len = sizeof($dataArray1);
        if ($len == 0) return $dataArray1;
        $index = ( $index % $len );
        if ($index < 0) $index = $len - $index;
        foreach ($dataArray1 as $i => $arrayItem)   {
            if ($i == $index)    {
                $newDataArray1[sizeof($newDataArray1)] = $itemToInsert;
            }
            $newDataArray1[sizeof($newDataArray1)] = $arrayItem;
        }    
        return $newDataArray1;
    }
    public static function createReplicaArray($sourceArray1, $defaultValue = "", $assignValueArrayUseStdPropertiesColumnNaming = null, $pkey = null) {
        $assignValueArrayUseStdPropertiesColumnNaming = is_null($assignValueArrayUseStdPropertiesColumnNaming) ? array() : $assignValueArrayUseStdPropertiesColumnNaming;
        $replicaArray1 = array();
        foreach ($sourceArray1 as $key => $tdata)   {
            $tkey = is_null($pkey) ? $key : ( $pkey . "-" . $key );
            if (! is_array($tdata)) {
                //Check if available in assignValues
                $value = isset($assignValueArrayUseStdPropertiesColumnNaming[$tkey]) ? $assignValueArrayUseStdPropertiesColumnNaming[$tkey] : $defaultValue;
                $replicaArray1[$key] = $value;
            } else {
                $tReplicaArray1 = self::createReplicaArray($tdata, $defaultValue, $assignValueArrayUseStdPropertiesColumnNaming, $tkey);
                //Now working 
                $replicaArray1[$key] = $tReplicaArray1;
            }
        }
        return $replicaArray1;
    }
    public static function getStandardizedProperties($columnBlock1, $pkey = null)
    {
        $column1 = array();
        foreach ($columnBlock1 as $key => $tdata) {
            $tkey = $key;
            if (!is_null($pkey)) $tkey = $pkey . "-" . $key;
            if (!is_array($tdata)) {
                $column1[$tkey] = $tdata;
            } else {
                //Working with array
                $tcolumn1 = self::getStandardizedProperties($tdata, $tkey);
                //Now Append in $column1
                foreach ($tcolumn1 as $tkey => $tdata)  $column1[$tkey] = $tdata;
            }
        }
        return $column1;
    }
    public static function getOneValueFromColumns($columnlist, $standardizedPropertyName)  {
        $value = null;
        foreach ($columnlist as $column1)   {
            $stdProperties = Tools::getStandardizedProperties($column1);
            if (isset($stdProperties[$standardizedPropertyName]))   {
                $tval = $stdProperties[$standardizedPropertyName];
                if (trim($tval) == "") continue;
                if (is_null($value)) $value = $tval;
                else if ($value != $tval) {
                    $value = null;
                    break; //Found multiple different values
                }
            }
        }
        return $value;
    }
    public static function foundValueInOneOfColumns($columnlist, $standardizedPropertyName, $value) {
        $found = false;
        foreach ($columnlist as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            $found = isset($stdProperties[$standardizedPropertyName]) && ($stdProperties[$standardizedPropertyName] == $value);
            if ($found) break;
        }
        return $found;
    }

}
?>