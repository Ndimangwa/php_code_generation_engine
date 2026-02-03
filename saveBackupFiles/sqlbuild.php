<?php
class SQLBuild {
    public static $__COMMA_PLACEHOLDER = "_@COMMA@_";
    public static $__DEFAULT_COLUMN_WIDTH = 24;
    private static $__INIT_MAX_COUNT_OF_NON_IMPORTS_FOUND = -1;
    private static function getStandardizedProperties($columnBlock1, $pkey = null)  {
        $column1 = array();
        foreach ($columnBlock1 as $key => $tdata) {
            $tkey = $key;
            if (! is_null($pkey)) $tkey = $pkey."-".$key; 
            if (! is_array($tdata)) {
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
    private static function escapeData($string1, $start, $end)    {
        //Will escape string which found between [start, end)
        if (strlen($string1) < abs($end - $start)) return $string1;
        $leftStr = substr($string1, 0, $start);
        $rightLen = strlen($string1) - $end;
        if ($end < 0) $rightLen = $end * -1;
        $rightStr = substr($string1, $end, $rightLen);
        $workingString = substr($string1, $start, $end);
        $workingString = str_replace("'", "\'", $workingString);
        return $leftStr.$workingString.$rightStr;
    }
    private static function getFromImportedFiles($filename, $tablename, $initialValuesArray1, $lookupArray1)   {
        //lookupArray['dbcol'] = filecol
        //Make use of DataFile/CSVFile
        /*echo "\n*********************************************\n";
        var_dump($lookupArray1);
        echo "\n*********************************************\n";*/
        $argumentArray1 = array();
        $requiredColumns = array();
        foreach ($lookupArray1 as $dbcolname => $csvcolname)  {
            $argumentArray1[$dbcolname] = array();
            $argumentArray1[$dbcolname]['val'] = $initialValuesArray1[$dbcolname]['val'];
            $argumentArray1[$dbcolname]['type'] = $initialValuesArray1[$dbcolname]['type'];
            $argumentArray1[$dbcolname]['csvcolname'] = $lookupArray1[$dbcolname];
            $requiredColumns[sizeof($requiredColumns)] = $dbcolname;
        }
        $datafile1 = new DataFile(
            $filename, 
            null, 
            function($headerLine1, $dataArray1, $optArgs1)  {
                //Header Shaping Function -- now $optArgs1 is $lookupArray1 
                $lArray1 = array();
                /*echo "\n============================================\n";
                var_dump($optArgs1);
                echo "\n============================================\n";*/
                //foreach ($optArgs1 as $dbcol => $csvcol)    $lArray1[sizeof($lArray1)] = $dbcol;
                foreach ($headerLine1->getArray() as $defaultColname)   {
                    foreach ($optArgs1 as $dbcol => $csvcol)    {
                        if ($csvcol == $defaultColname) {
                            $defaultColname = $dbcol;
                            break;
                        }
                    }
                    $lArray1[sizeof($lArray1)] = $defaultColname;
                }  
                return $lArray1;
            }/*kd788 namingShapingFunction*/,
            function($dataLine1, $dataArray1, $optArgs1)    {
                //var_dump($dataLine1->getArray()); die("STOP");
                $lineArray1 = explode($dataLine1->getDelimiter(), Tool::convertArrayToString($dataLine1->getArrayValues(), $dataLine1->getDelimiter()));
                //We need to check type here and shape data accordingly
                if (sizeof($lineArray1) !== sizeof($dataLine1->getHeaderLine()->getArray())) return $dataArray1;
                $index = 0;
                $lineFinalArray1 = array();
                foreach ($dataLine1->getHeaderLine()->getArray() as $colname)   {
                    $dt = $lineArray1[$index];
                    //We need to remove ' and special characters
                    $dt = str_replace("'", "\'", $dt);
                    if (isset($optArgs1[$colname]) && isset($optArgs1[$colname]['type']))   {
                        $type = $optArgs1[$colname]['type'];
                        if ($type == "text") $dt = "'".$dt."'";
                    }
                    $lineFinalArray1[$index] = $dt;
                    $index++;
                }    
                return (new DataLine($lineFinalArray1, $dataLine1->getHeaderLine()))->getArray();
            }/*dataShapingFunction*/,
            0,
            ',',
            null,
            80,
            $argumentArray1
        );
        $datafile1->columns($requiredColumns);
        $datafile1->synchronize();
        //var_dump($datafile1->getDataLines()[0]->getArray());
        //$datafile1->head(2)->view();
        //Exchanging header to become the ones in db
        /*$dataArray1 = array();
        foreach ($lookupArray1 as $dbcolname => $csvcolname)    {
            $dataArray1[sizeof($dataArray1)] = $dbcolname;
        }
        $datafile1->setHeaderLine(new HeaderLine($dataArray1))->synchronize();
        */
        $line = "INSERT INTO $tablename (";
        $line .= $datafile1->getHeaderLine()->getLine();
        $line .= ") VALUES";
        $count = 0;
        foreach ($datafile1->getDataLines() as $dataLine1)  {
            $tval = ", (";
            if ($count == 0) $tval = " (";
            $line .= $tval;
            $line .= $dataLine1->getLine();
            $line .= ")";
            $count++;
        }
        return $line;
    }
    private static function convertCurledDataListToArray($curledData)   {
        $curledData = trim($curledData);
        if (substr($curledData, 0, 1) !== '{' || substr($curledData, -1, 1) !== '}') return null;
        $list = substr($curledData, 1, -1);
        if ($list === false) return null;
        $listArray1 = array();
        foreach (explode(",", $list) as $dt) $listArray1[sizeof($listArray1)] = self::escapeData(trim($dt), 1, -1);
        return $listArray1;
    }
    private static function addInitialValues($workingFolder, $tablename, $initialValuesArray1)  {
        if (sizeof($initialValuesArray1) == 0) return ""; //Nothing were found 
        //We need to validate, if @import only or non-import only
        $importFound = false;
        $nonImportFound = false;
        $maxCountOfNonImportsFound = self::$__INIT_MAX_COUNT_OF_NON_IMPORTS_FOUND; //The should match
        $line = "INSERT INTO $tablename ";
        $collist = "";
        $colvalArray1 = array();
        //Used in imports statement
        $filename = null;
        $lookupArray1 = array();
        $count = 0;
        foreach ($initialValuesArray1 as $colname => $initialValue) {
            $initialValue = $initialValue['val'];
            //We already packed each thing with no empty
            if (! $importFound && strpos($initialValue, "@import://") === false)  {
                $dataArray1 = self::convertCurledDataListToArray($initialValue);
                if (is_null($dataArray1)) continue;
                $lsize = sizeof($dataArray1);
                $logic_A = $maxCountOfNonImportsFound == self::$__INIT_MAX_COUNT_OF_NON_IMPORTS_FOUND;
                $logic_B = $lsize == $maxCountOfNonImportsFound;
                //Check width
                if (! ($logic_A xor $logic_B)) {
                    $importFound = true;
                    $nonImportFound = true;
                    $line = "";
                    //echo "\n[ lsize , maxCount, logic_A, logic_B ] : [ $lsize, $maxCountOfNonImportsFound, $logic_A, $logic_B ]\n";
                    break;
                }
                //Now update maxCount
                if ($logic_A) $maxCountOfNonImportsFound = $lsize;
                
                if ($count == 0) $collist = $colname; else $collist .= ", $colname";
                $icount = 0;
                foreach ($dataArray1 as $dt)    {
                    if ($count == 0) $colvalArray1[$icount] = $dt; else $colvalArray1[$icount] .= ", $dt";
                    $icount++;
                }
                $nonImportFound = true;
            } else if (! $nonImportFound) {
                //We need to check that they originate from same file
                //Extracting the leading import
                $extract1 = substr($initialValue, 10); //Skipped @import://
                //Now we need to separate between filename and colname 
                $tArray1 = explode("/", $extract1);
                if (sizeof($tArray1) !== 2) {
                    $importFound = true;
                    $nonImportFound = true;
                    $line = "";
                    break;
                }
                $logic_A = is_null($filename);
                $logic_B = $tArray1[0] == $filename;
                if (! ($logic_A xor $logic_B))  {
                    $importFound = true;
                    $nonImportFound = true;
                    $line = "";
                    break; 
                }
                if ($logic_A) $filename = $tArray1[0];
                $lookupArray1[$colname] = $tArray1[1];
                $importFound = true;
            } else {
                //Found Mixing between import and non-import statements
                $line = "";
                break;
            }
            $count++;
        }
        if ($line !== "" && $nonImportFound && ! $importFound)   {
            //Something Found Which is non-import
            $line .= "( $collist ) VALUES ";
            //Continue with values
            $icount = 0;
            foreach ($colvalArray1 as $dt)  {
                $dt = str_replace(self::$__COMMA_PLACEHOLDER, ",", $dt); //Check if null need to be treated
                $term = "( $dt )";
                if ($icount == 0) $line .= $term; else $line .= ", $term";
                $icount++;
            }
        } else if (! is_null($filename) && sizeof($lookupArray1) > 0 && $importFound && ! $nonImportFound)  {
            $line = self::getFromImportedFiles(join(DIRECTORY_SEPARATOR, array($workingFolder, $filename)), $tablename, $initialValuesArray1, $lookupArray1);
        }   {

        }
        if ($line != "") $line .= ";\n";
        return $line;
    }
    private static function doGenerateSQLTable($columnlist) {
        $generateSQLTable = true;
        //If found only one saying false, then do not generate
        $testColumn = "settings-code-generate-sql-table";
        foreach ($columnlist as $column1)   {
            $stdColumns = self::getStandardizedProperties($column1);
            if (isset($stdColumns[$testColumn]) && ! $stdColumns[$testColumn]) {
                $generateSQLTable = false;
                break;
            }
        }
        return $generateSQLTable;
    }
    public static function build($workingFolder, $schemaArray1)  {
        $schema1 = new Schema($schemaArray1);
        if (is_null($schemaArray1)) return null;
        //Will return SQL string
        $sql = "";
        foreach ($schemaArray1 as $classBlock1) {
            $foreignKeySql = "";
            $blocksql = "";
            if (! isset($classBlock1['class']) || ! isset($classBlock1['table']) || ! isset($classBlock1['columns'])) continue;
            $classname = $classBlock1['class'];
            $tablename = $classBlock1['table'];
            $initialValuesArray1 = array();
            $columnlist = $classBlock1['columns'];
            if (! self::doGenerateSQLTable($columnlist)) continue;
            $blocksql .= "DROP TABLE IF EXISTS $tablename;\n";
            $blocksql .= "CREATE TABLE $tablename(";
            $columncount = 0;
            foreach ($classBlock1['columns'] as $columnArray1)   {
                $colname = null; if (isset($columnArray1['colname'])) $colname = $columnArray1['colname'];
                $property = null; $refObject = null; $objectType = "integer"; $refProperty = null;
                if (isset($columnArray1['property'])) {
                    if (isset($columnArray1['property']['pname'])) $property = $columnArray1['property']['pname'];
                    $refProperty = $property; 
                    if (isset($columnArray1['property']['ref-property'])) $refProperty = $columnArray1['property']['ref-property'];
                    if (isset($columnArray1['property']['object'])) $refObject = $columnArray1['property']['object'];
                    if (isset($columnArray1['property']['type'])) $objectType = $columnArray1['property']['type'];
                }
                $initialValue = null;
                if (isset($columnArray1['data']))   {
                    if (isset($columnArray1['data']['initialvalues'])) $initialValue = trim($columnArray1['data']['initialvalues']);
                }
                if (! is_null($initialValue) && ! $initialValue == "")  {
                    $initialValuesArray1[$colname] = array(); 
                    $initialValuesArray1[$colname]['val'] = $initialValue;
                }
                $type = null; if (isset($columnArray1['type'])) $type = $columnArray1['type'];  
                if (is_null($colname) || is_null($property) || is_null($type)) continue;
                $dataSettingsArray1 = null; if (isset($columnArray1['settings']) && isset($columnArray1['settings']['data'])) $dataSettingsArray1 = $columnArray1['settings']['data'];
                $role = null; if (isset($dataSettingsArray1['role'])) $role = $dataSettingsArray1['role'];
                $unique = false; if (isset($dataSettingsArray1['unique'])) $unique = $dataSettingsArray1['unique'] == true;
                $regex = null; if (isset($dataSettingsArray1['regex'])) $regex = $dataSettingsArray1['regex'];
                $width = self::$__DEFAULT_COLUMN_WIDTH; if (isset($dataSettingsArray1['width'])) $width = intval($dataSettingsArray1['width']);
                $fixedWidth = false; if (isset($dataSettingsArray1['fixed-width'])) $fixedWidth = $dataSettingsArray1['fixed-width'] == true;
                $default = null; if (isset($dataSettingsArray1['default'])) $default = $dataSettingsArray1['default'];
                $allowNull = true; if (isset($dataSettingsArray1['allow-null'])) $allowNull = $dataSettingsArray1['allow-null'] == true; 
                $refKeyCheck = true; if (isset($dataSettingsArray1['ref-key-check'])) $refKeyCheck = $dataSettingsArray1['ref-key-check'] == true;
                $tsql = null;
                //Assign generally and correct whenever necessary, based on type
                if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = $type;
                if ($type == "integer") {
                    $tsql = "$colname INT";
                } else if ($type == "float")    {
                    $tsql = "$colname FLOAT";
                } else if ($type == "double")   {
                    $tsql = "$colname DOUBLE";
                } else if ($type == "boolean")  {
                    $tsql = "$colname TINYINT";
                } else if ($type == "text") {
                    $tsql = "$colname VARCHAR($width)";
                    if ($fixedWidth) $tsql = "$colname CHAR($width)";
                } else if ($type == "object")   {
                    if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = "integer";
                    if ($objectType == "integer")   {
                        $tsql = "$colname INT";
                    } else {
                        //Assume text 
                        $tsql = "$colname VARCHAR($width)";
                        if ($fixedWidth) $tsql = "$colname CHAR($width)";
                        if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = "text";
                    }
                    //We need to perform lookup and get the type and its width properly
                    if ($refKeyCheck && ! is_null($refObject))   {
                        //die("\n\nClassname : $classname ; Propertyname: $property\n\n");
                        //doLookup on the host Object
                        $columnBlock1 = Schema::getColumnArrayFromClassAndProperty($schemaArray1, $refObject, $refProperty); 
                        if (! is_null($columnBlock1))   {
                            $dtype = $type;
                            $dwidth = $width;

                            if (isset($columnBlock1['type'])) $dtype = $columnBlock1['type'];
                            if (isset($columnBlock1['settings']) && isset($columnBlock1['settings']['data']) && isset($columnBlock1['settings']['data']['width']))  {
                                $dwidth = $columnBlock1['settings']['data']['width'];
                            } 
                            $tsql = "$colname VARCHAR($dwidth)"; 
                            if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = "text";
                            if ($dtype == "integer")    {
                                $tsql = "$colname INT UNSIGNED";
                                if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = "integer";
                            } else if ($dtype == "float")    {
                                $tsql = "$colname FLOAT";
                                if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = "float";
                            } else if ($dtype == "double")  {
                                $tsql = "$colname DOUBLE";
                                if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = "double";
                            }
                        }
                        //We need tablename and column-name of the host-table
                        $tablename = $schema1->getTablenameFromAClassname($refObject);
                        $refcol = $schema1->getColumnnameWithAPrimaryRoleFromAPropertyname($refObject, $refProperty);
                        if (! is_null($tablename) && ! is_null($refcol)) {
                            $foreignKeySql .= ",\n\tFOREIGN KEY($colname) REFERENCES $tablename($refcol)";
                        }
                    } 
                } else if ($type == "list-object")  {
                    $tsql = "$colname VARCHAR($width)";
                    if (isset($initialValuesArray1[$colname])) $initialValuesArray1[$colname]['type'] = "text";
                }
                if (! $allowNull || $unique) $tsql .= " NOT NULL";
                if ($unique) $tsql .= " UNIQUE";
                if (! is_null($default) && ! $unique) $tsql .= " DEFAULT $default";

                if ($role == "primary" && in_array($type, ["integer", "text"])) {
                    if ($type == "integer") $tsql = "$colname INT UNSIGNED NOT NULL AUTO_INCREMENT";
                    else {
                        //Now text 
                        if ($fixedWidth)    $tsql = "$colname CHAR($width) NOT NULL";
                        else $tsql = "$colname VARCHAR($width) NOT NULL";
                    }
                }
                if (! is_null($tsql)) {
                    //For testing, put in a new line
                    $tsql = "\n\t".$tsql;
                    if ($columncount == 0) $blocksql .= $tsql;
                    else $blocksql .= ",".$tsql;
                }
                $columncount++;
            }
            //Add All Uniques 
            
            $blocksql .= $foreignKeySql;
            //Primary Keys
            $listOfPrimaryKeys = Schema::getCommaSeparatedListOfPrimaryColumnsFromColumnListArray($classBlock1['columns']);
            if (! is_null($listOfPrimaryKeys)) $blocksql .= ",\n\tPRIMARY KEY($listOfPrimaryKeys)";
            $blocksql .= ") engine=innoDB;\n";
            //We need to initialValues here for this table
            $blocksql .= self::addInitialValues($workingFolder, $classBlock1['table'], $initialValuesArray1);
            $sql .= $blocksql;
        }
        if ($sql == "") $sql = null;
        return $sql;
    }
}
?>