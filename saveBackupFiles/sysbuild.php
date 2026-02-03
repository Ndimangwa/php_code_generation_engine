<?php 
class SYSBuild {
    private static $listOfLoadAllDataClasses = array();
    private static $listOfValueColumnsInAllClasses = null;
    private static $staticFolder = null;
    private static $classesFolder = null;
    private static $accessForbiddenFolder = null;
    private static function shootException($message)    {
        throw new Exception($message);
    }
    private static function getOneValueFromColumns($columnlist, $standardizedPropertyName)  {
        $value = null;
        foreach ($columnlist as $column1)   {
            $stdProperties = self::getStandardizedProperties($column1);
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
    private static function foundValueInOneOfColumns($columnlist, $standardizedPropertyName, $value) {
        $found = false;
        foreach ($columnlist as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            $found = isset($stdProperties[$standardizedPropertyName]) && ($stdProperties[$standardizedPropertyName] == $value);
            if ($found) break;
        }
        return $found;
    }
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
    private static function removeDirectoryStructure($dir) {
        //Copied from https://www.php.net/manual/en/function.rmdir.php
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::removeDirectoryStructure("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
    private static function createDirectoryStructure($initFolder, $targetFolder, $subDirectories = ["lang", "ui", "sys", "sql"], $listOfForbiddenFolders = ["lang", "ui", "sys", "sql"])    {
        //If targetFolder exists , try to remove it 
        if (file_exists($targetFolder)) self::removeDirectoryStructure($targetFolder);
        if (file_exists($targetFolder) || ! mkdir($targetFolder)) throw new Exception("Target Folder [ $targetFolder ] could not be created");
        $sourceFolder = trim(str_replace(DIRECTORY_SEPARATOR, "", self::$accessForbiddenFolder));
        $indexFile = "index.html"; $cssFile = "forbidden.css";
        //Save the Current Working Directory
        $currentDir = getcwd();
        //Change to the target Folder
        chdir($targetFolder);
        foreach ($subDirectories as $sub01) {
            if (! mkdir($sub01)) throw new Exception("Sub Folder [ $targetFolder :: $sub01 ] , can not make folder");
        }
        //Resumme to the original Working Directory
        chdir($currentDir);
        //Now test here
        foreach ($subDirectories as $sub01) {
            //Now working with forbidden
            if (in_array($sub01, $listOfForbiddenFolders))  {
                copy(join(DIRECTORY_SEPARATOR, [$sourceFolder, $indexFile]), join(DIRECTORY_SEPARATOR, [$targetFolder, $sub01, $indexFile]));
                copy(join(DIRECTORY_SEPARATOR, [$sourceFolder, $cssFile]), join(DIRECTORY_SEPARATOR, [$targetFolder, $sub01, $cssFile]));
            }
        }
        return $subDirectories;
    }
    private static function createSQLFile($schemaDir, $jsonArray1, $sqlFilePath)    {
        $sqlStatements = SQLBuild::build($schemaDir, $jsonArray1);
        if (is_null($sqlStatements)) throw new Exception("Error, Null SQL Statements");
        file_put_contents($sqlFilePath, $sqlStatements);
        return $sqlFilePath;
    }
    private static function copyClassesFiles($classesFolder, $sysFolder)    {
        $files = array_diff(scandir($classesFolder), array('.', '..'));
        foreach ($files as $file)    {
            copy(join(DIRECTORY_SEPARATOR, [$classesFolder, $file]), join(DIRECTORY_SEPARATOR, [$sysFolder, $file]));
        }
        return $sysFolder;
    }
    private static function copyInitFiles($initFolder, $sysFolder)  {
        $files = array_diff(scandir($initFolder), array('.', '..'));
        foreach ($files as $file)   {
            copy(join(DIRECTORY_SEPARATOR, [$initFolder, $file]), join(DIRECTORY_SEPARATOR, [$sysFolder, $file]));
        }
        return $sysFolder;
    }
    private static function deleteGetStandardizedProperties($columnBlock1) {
        $column1 = array();
        if (isset($columnBlock1['colname'])) $column1['colname'] = $columnBlock1['colname'];
        if (isset($columnBlock1['property']))   {
            if (isset($columnBlock1['property']['pname'])) $column1['property-pname'] = $columnBlock1['property']['pname'];
            if (isset($columnBlock1['property']['object'])) $column1['property-object'] = $columnBlock1['property']['object'];
            if (isset($columnBlock1['property']['ref-property'])) $column1['property-ref-property'] = $columnBlock1['property']['ref-property'];
        }
        if (isset($columnBlock1['type'])) $column1['type'] = $columnBlock1['type'];
        if (isset($columnBlock1['settings']))   {
            if (isset($columnBlock1['settings']['data']))   {
                if (isset($columnBlock1['settings']['data']['role'])) $column1['settings-data-role'] = $columnBlock1['settings']['data']['role'];
                if (isset($columnBlock1['settings']['data']['ref-key-check'])) $column1['settings-data-ref-key-check'] = $columnBlock1['settings']['data']['ref-key-check'];
                if (isset($columnBlock1['settings']['data']['regex'])) {
                    if (isset($columnBlock1['settings']['data']['regex']['rule'])) $column1['settings-data-regex-rule'] = $columnBlock1['settings']['data']['regex']['rule'];
                    if (isset($columnBlock1['settings']['data']['regex']['rule'])) $column1['settings-data-regex-message'] = $columnBlock1['settings']['data']['regex']['message'];
                }
                if (isset($columnBlock1['settings']['data']['width'])) $column1['settings-data-width'] = $columnBlock1['settings']['data']['width'];
            }
        }
        return $column1;
    }
    private static function createGetPropertyValue($columnArray1)   {
        $line = "\tpublic function getMyPropertyValue(\$pname)  {\n";
        $count = 0;
        foreach ($columnArray1 as $column1)    {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['property-pname'])) continue;
            $pname = $stdProperties['property-pname'];
            $dt = "if (\$pname == \"$pname\") return \$this->$pname;";
            if ($count == 0) $line .= "\t\t$dt\n";
            else $line .= "\t\telse $dt\n";
            $count++;
        }
        $line .= "\t\treturn null;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createGetNameAndGetName0($columnArray1) {
        $lineGetName = "\tpublic function getName() {\n";
        $lineGetName0 = "\tpublic function getName0() {\n";
        //$lineGetName0 .= "\t\t\$namedValue = null;\n";
        $temp1 = "\t\t\$namedValue = null;\n";
        $lineGetName .= "\t\treturn array(";
        $count = 0;
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['settings-data-role'])) continue;
            $role = $stdProperties['settings-data-role'];
            if (! isset($stdProperties['property-pname'])) continue;
            $pname = $stdProperties['property-pname'];
            if ($role == "value")   {
                $dt = "\$this->$pname";
                if ($count == 0)  {
                    $temp1 = "\t\t\$namedValue = \$this->$pname;\n";
                    $lineGetName .= $dt;
                }  else {
                    $lineGetName .= ", $dt";
                }
                $count++;
            }
        }
        $lineGetName0 .= $temp1;
        $lineGetName .= ");\n";
        $lineGetName0 .= "\t\treturn \$namedValue;\n";
        $lineGetName0 .= "\t}\n";
        $lineGetName .= "\t}\n";
        return $lineGetName.$lineGetName0;
    }
    private static function createValueColumns($columnArray1)   {
        $value0Line = "\tpublic static function getValue0Columnname() {\n";
        $valuesLine = "\tpublic static function getValueColumnnames()   {\n";
        $valuesLine .= "\t\treturn array(";
        $count = 0;
        foreach ($columnArray1 as $column1)    {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['colname'])) continue;
            $colname = $stdProperties['colname'];
            $role = null; if (isset($stdProperties['settings-data-role'])) $role = $stdProperties['settings-data-role'];
            if (is_null($role)) continue;
            if ($role == "value")   {
                $dt = "'$colname'";
                if ($count == 0)    {
                    $value0Line .= "\t\treturn \"$colname\";\n";
                    $valuesLine .= $dt;
                } else {
                    $valuesLine .= ", $dt";
                }
                $count++;
            }
        }
        $valuesLine .= ");\n";
        $value0Line .= "\t}\n";
        $valuesLine .= "\t}\n";
        return $value0Line.$valuesLine;
    }
    private static function createGetSearchableColumns($columnArray1)   {
        $line = "\tpublic static function getSearchableColumns()    {\n";
        $line .= "\t\t/* Will return list of Searchable Properties */\n";
        $line .= "\t\treturn array(";
        $count = 0;
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['property-pname'])) continue;
            $pname = $stdProperties['property-pname'];
            $role = "others"; if (isset($stdProperties['settings-data-role'])) $role = $stdProperties['settings-data-role'];
            $searchable = true; if (isset($stdProperties['settings-data-searchable']) && $stdProperties['settings-data-searchable'] === false) $searchable = false;
            if ($searchable && $role != "primary")  {
                //You can not search a primary-column 
                $dt = "'$pname'";
                if ($count == 0) $line .= $dt;
                else $line .= ", $dt";
                $count++;
            }
        }
        $line .= ");\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createASearchUI($classname, $columnArray1)  {
        $line = "\tpublic static function getASearchUI(\$page, \$listOfColumnsToDisplay, \$optIndex = 0)    {\n";
        $searchableColumns = array();
        $valuedColumns = array(); //must be searchable 
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            $role = "others"; if (isset($stdProperties['settings-data-role'])) $role = $stdProperties['settings-data-role'];
            $searchable = true; if (isset($stdProperties['settings-data-searchable']) && $stdProperties['settings-data-searchable'] === false) $searchable = false;
            if (! isset($stdProperties['property-pname'])) continue;
            $pname = $stdProperties['property-pname'];
            if ($searchable)    {
                $searchableColumns[sizeof($searchableColumns)] = $pname;
                if ($role == "value") $valuedColumns[sizeof($valuedColumns)] = $pname;
            }
        }   
        $line .= "\t\t\$line = \"\";\n";
        if (sizeof($searchableColumns) > 0) {
            $line .= "\t\t\$mycolumnlist = json_encode(\$listOfColumnsToDisplay);\n";
            $line .= "\t\t\$line .= \"&lt;div class=&quot;container __ui_search_container__ py-2&quot;&gt;    &lt;div class=&quot;row&quot;&gt;\";\n"; 
            $offset = "offset-md-3";
            if (sizeof($valuedColumns) > 0) {
                $offset = "";
                $mycolumnlist = htmlentities(json_encode($valuedColumns));
                //$line .= "\t\t\$line .= \"&lt;div class=&quot;col-md-6 $offset&quot;&gt;    &lt;div class=&quot;input-group mb-3&quot;&gt;        &lt;input name=&quot;__ui_search_input__&quot; id=&quot;__ui_search_input__\$optIndex&quot; type=&quot;search&quot; data-min-length=&quot;3&quot; class=&quot;form-control&quot;            placeholder=&quot;Search&quot; aria-label=&quot;Search&quot; aria-describedby=&quot;basic-addon2&quot; /&gt;        &lt;div class=&quot;input-group-append&quot;&gt;            &lt;button id=&quot;__ui_search_button__\$optIndex&quot; data-output-target=&quot;__ui_search_output_target__\$optIndex&quot; data-display-column='\$mycolumnlist' data-error-target=&quot;__ui_search_error__\$optIndex&quot; data-column='$mycolumnlist' data-page='\$page' data-class='$classname' class=&quot;btn btn-outline-primary btn-perform-search btn-click-default&quot;                type=&quot;button&quot; data-search-input=&quot;text&quot; data-search-input-id=&quot;__ui_search_input__\$optIndex&quot; data-toggle=&quot;tooltip&quot; title=&quot;This is a basic search&quot;&gt;Search&lt;/button&gt;        &lt;/div&gt;    &lt;/div&gt;&lt;/div&gt;\";\n";
                $line .= "\t\t\$line .= \"&lt;div class=&quot;col-md-6 $offset&quot;&gt;    &lt;form id=&quot;__delta_init_basic__&quot;&gt;        &lt;div class=&quot;input-group mb-3&quot;&gt;            &lt;input name=&quot;__ui_search_input__&quot; id=&quot;__ui_search_input__\$optIndex&quot; type=&quot;search&quot; data-column='$mycolumnlist' data-class='$classname' data-min-length=&quot;3&quot;                class=&quot;form-control ui-txt-search-input&quot;required placeholder=&quot;Search&quot; aria-label=&quot;Search&quot; aria-describedby=&quot;basic-addon2&quot; /&gt;            &lt;div class=&quot;input-group-append&quot;&gt;                &lt;button id=&quot;__ui_search_button__\$optIndex&quot; data-form-id=&quot;__delta_init_basic__&quot; data-output-target=&quot;__ui_search_output_target__\$optIndex&quot;                    data-display-column='\$mycolumnlist' data-error-target=&quot;__ui_search_error__\$optIndex&quot;                    data-column='$mycolumnlist' data-page='\$page' data-class='$classname'                    class=&quot;btn btn-outline-primary btn-perform-search&quot; type=&quot;button&quot;      data-search-input=&quot;text&quot; data-search-input-id=&quot;__ui_search_input__\$optIndex&quot; data-toggle=&quot;tooltip&quot;                    title=&quot;This is a basic search&quot;&gt;Search&lt;/button&gt;            &lt;/div&gt;        &lt;/div&gt;    &lt;/form&gt;&lt;/div&gt;\";\n";
            }
            $mycolumnlist = htmlentities(json_encode($searchableColumns));
            $line .= "\t\t\$line .= \"&lt;div class=&quot;col-md-6 $offset&quot;&gt;&lt;button type=&quot;button &quot;class=&quot;btn btn-outline-primary btn-block&quot; name=&quot;__ui_advanced_search_button__&quot; id=&quot;__ui_advanced_search_button__\$optIndex&quot; data-output-target=&quot;__ui_search_output_target__\$optIndex&quot; data-display-column='\$mycolumnlist' data-error-target=&quot;__ui_search_error__\$optIndex&quot; data-column='$mycolumnlist'  data-min-length=&quot;3&quot; data-page='\$page' data-class='$classname' data-search-dialog=&quot;__dialog_search_container_01__&quot; data-toggle=&quot;tooltip&quot; title=&quot;This is a more advanced search technique&quot;&gt;Advanced Search&lt;/button&gt;&lt;/div&gt;&lt;/div&gt;&lt;br/&gt;&lt;div id=&quot;__ui_search_error__\$optIndex&quot; class=&quot;p-1 ui-sys-error-message&quot;&gt;&lt;/div&gt;&lt;div style=&quot;overflow-x: scroll;&quot; id=&quot;__ui_search_output_target__\$optIndex&quot;&gt;&lt;/div&gt;&lt;/div&gt;\";\n";
        }
        //Script
        //$line .= "\t\t\$line .= \"\";\n";
        $line .= "\t\t\$line .= \"&lt;script type=&quot;text/javascript&quot;&gt;(function($)    {    $(function()    {        var callbackFunction\$optIndex = function(\\\$button1, data, textStatus, optionArgumentArray1) {            var \\\$dialog1 = $('#' + \\\$button1.data('searchDialog'));            \\\$dialog1 = showAdvancedSearchDialog(\\\$button1, \\\$dialog1, data, Constant);            \\\$dialog1.modal('show');      };        $('#__ui_advanced_search_button__\$optIndex').on('click', function(e)   {            var \\\$button1 = \$(this);            var columnList = \\\$button1.data('column');            var classname = \\\$button1.data('class');            var payload = { columns: columnList, classname: classname };            fSendAjax(\\\$button1,                    \$('&lt;span/&gt;'),                    '../server/serviceGetAdvancedSearchPayload.php',                    payload,                    null,                    null,                    callbackFunction\$optIndex,                    null,                    null,                    &quot;POST&quot;,                    true,                    false,                    &quot;Processing ....&quot;,                    null,                    null);        });    });})(jQuery);&lt;/script&gt;\";\n";
        $line .= "\t\treturn htmlspecialchars_decode(\$line);\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createGetReferenceClass($columnArray1)  {
        $line = "\tpublic static function getReferenceClass(\$pname)    {\n";
        $line .= "\t\t\$tArray1 = array(";
        $count = 0;
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! (isset($stdProperties['property-pname']) && isset($stdProperties['property-object']))) continue;
            $pname = $stdProperties['property-pname'];
            $pobject = $stdProperties['property-object'];
            if ($pobject != "") {
                $dt = "'$pname' => '$pobject'";
                if ($count == 0) $line .= $dt;
                else $line .= ", $dt";
                $count++;
            }
        } 
        $line .= ");\n";
        $line .= "\t\t\$refclass = null; if (isset(\$tArray1[\$pname])) \$refclass = \$tArray1[\$pname];\n";
        $line .= "\t\treturn \$refclass;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createGetColumnType($columnArray1)  {
        $line = "\tpublic static function getColumnType(\$pname)    {\n";
        $line .= "\t\t\$tArray1 = array(";
        $count = 0;
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! (isset($stdProperties['property-pname']) & isset($stdProperties['type']))) continue;
            $pname = $stdProperties['property-pname'];
            $type = $stdProperties['type'];
            if (isset($stdProperties['property-type'])) $type = $stdProperties['property-type']; //ie DateAndTime obj
            $dt = "'$pname' => '$type'";
            if ($count == 0) $line .= $dt;
            else $line .= ", $dt";
            $count++;
        }
        $line .= ");\n";
        $line .= "\t\t\$type = null; if (isset(\$tArray1[\$pname])) \$type = \$tArray1[\$pname];\n";
        $line .= "\t\treturn \$type;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createLoadAllData($columnArray1)    {
        $valueCols = array();
        $primaryCol = null;
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! (isset($stdProperties['property-pname']) && isset($stdProperties['settings-data-role']) && isset($stdProperties['colname']))) continue;
            $pname = $stdProperties['property-pname'];
            $role = $stdProperties['settings-data-role'];
            $colname = $stdProperties['colname'];
            if ($role == "primary" & is_null($primaryCol))  $primaryCol = $colname; //Only 1st primary column
            if ($role == "value")   $valueCols[sizeof($valueCols)] = $colname;
        }
        if (sizeof($valueCols) == 0) $valueCols = null;
        $line = "";
        if (! (is_null($primaryCol) || is_null($valueCols)))    {
            $line = "\tpublic static function loadAllData(\$__conn) {\n";
            $line .= "\t\t\$colArray1 = array('$primaryCol'";
            foreach ($valueCols as $valCol) $line .= ", '$valCol'";
            $line .= ");\n";
            $line .= "\t\t\$query = SimpleQueryBuilder::buildSelect(array(self::getTablename()), \$colArray1, null);\n";
            $line .= "\t\t\$jresult1 = SQLEngine::execute(\$query, \$__conn);\n";
            $line .= "\t\t\$jArray1 = json_decode(\$jresult1, true);\n";
            $line .= "\t\tif (\$jArray1['code'] !== 0) throw new Exception(\$jArray1['message']);\n";
            $line .= "\t\t\$dataArray1 = array();\n";
            $line .= "\t\tforeach (\$jArray1['rows'] as \$resultSet)    {\n";
            $line .= "\t\t\t\$index = sizeof(\$dataArray1); \$dataArray1[\$index] = array();\n";
            $line .= "\t\t\t\$dataArray1[\$index]['__id__'] = \$resultSet['$primaryCol'];\n";
            $line .= "\t\t\t\$myval = \"\";\n";
            foreach ($valueCols as $valCol) {
                $line .= "\t\t\t\$myval .= \" \".\$resultSet['$valCol'];\n";
            }
            $line .= "\t\t\t\$dataArray1[\$index]['__name__'] = trim(\$myval);\n";
            $line .= "\t\t}\n";
            $line .= "\t\treturn \$dataArray1;\n";
            $line .= "\t}\n";
        }
        return $line;
    }
    private static function createAConstructor($columnArray1)   {
        $line = "\tpublic function __construct(\$database, \$id, \$conn)    {\n";
        $line .= "\t\t\$this->lazyregex = true;\n";
        $line .= "\t\t\$this->setMe(\$database, \$id, \$conn);\n";
        $line .= "\t\t\$this->lazyregex = false;\n";
        $line .= "\t}\n";
        $line .= "\tpublic function setMe(\$database, \$id, \$conn)    {\n";
        $line .= "\t\t\$this->database = \$database;\n";
        $line .= "\t\t\$this->conn = \$conn;\n";
        //Build Query
        /*$line .= "\t\t\$whereClause = self::getId0Columnname();\n";
        $line .= "\t\t\$whereClause = array(\$whereClause => \$id);\n";
        $line .= "\t\t\$query = SimpleQueryBuilder::buildSelect(array(self::getTablename()), array('*'), \$whereClause);\n";
        $line .= "\t\t\$jresult1 = SQLEngine::execute(\$query, \$conn);\n";
        $line .= "\t\t\$jArray1 = json_decode(\$jresult1, true);\n";
        $line .= "\t\tif (\$jArray1['code'] !== 0) throw new Exception(\$jArray1['message']);\n";
        $line .= "\t\tif (\$jArray1['count'] !== 1) throw new Exception(\"Duplicate or no record found\");\n";
        $line .= "\t\t\$resultSet = \$jArray1['rows'][0];\n";
        */
        $line .= "\t\t\$t1 = __data__::selectQuery(\$conn, self::getClassname(), array('*'), array((self::getId0Columnname()) => \$id), true);\n";
        $line .= "\t\t\$resultSet = \$t1['column'][0];\n";
        foreach ($columnArray1 as $column1)    {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['property-pname']) || ! isset($stdProperties['type'])) continue;
            $pname = $stdProperties['property-pname'];
            $type = $stdProperties['type'];
            $refObject = null; if (isset($stdProperties['property-object'])) $refObject = $stdProperties['property-object'];
            $refProperty = $pname; if (isset($stdProperties['property-ref-property'])) $refProperty = $stdProperties['property-ref-property'];
            $role = "others"; if (isset($stdProperties['settings-data-role'])) $role = $stdProperties['settings-data-role'];
            $refKeyCheck = true; if (isset($stdProperties['settings-data-ref-key-check'])) $refKeyCheck = $stdProperties['settings-data-ref-key-check'];
            $colname = null; if (isset($stdProperties['colname'])) $colname = $stdProperties['colname'];
            //Working
            if (is_null($pname) || is_null($colname) || is_null($role)) throw new Exception("Key parameters not set while constructing a constructor");
            $line .= "\t\tif (! array_key_exists(\"$colname\", \$resultSet)) throw new Exception(\"Column [$colname] not available while pulling data\");\n";
            if ($role == "primary")  {
                $line .= "\t\t\$this->$pname = \$resultSet[\"$colname\"];\n";
            } else {
                $line .= "\t\t\$this->set".ucfirst($pname)."(\$resultSet[\"$colname\"]);\n";
            }
        }
        $line .= "\t\t\$this->clearUpdateList();\n";
        $line .= "\t\treturn \$this;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createObjectSettersAndGetters($columnArray1)    {
        $line = "";
        $staticLine = "";
        $primaryKeyLine = "";
        $primaryKeyColumnList = null;
        $whereClause = null;
        $primaryKeyCount = 0;
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['property-pname']) || ! isset($stdProperties['type'])) continue;
            $pname = $stdProperties['property-pname'];
            $type = $stdProperties['type'];
            $refObject = null; if (isset($stdProperties['property-object'])) $refObject = $stdProperties['property-object'];
            $refProperty = $pname; if (isset($stdProperties['property-ref-property'])) $refProperty = $stdProperties['property-ref-property'];
            $role = "others"; if (isset($stdProperties['settings-data-role'])) $role = $stdProperties['settings-data-role'];
            $refKeyCheck = true; if (isset($stdProperties['settings-data-ref-key-check'])) $refKeyCheck = $stdProperties['settings-data-ref-key-check'];
            $colname = null; if (isset($stdProperties['colname'])) $colname = $stdProperties['colname'];
            //Setters 
            $setLine = "";
            if ($role != "primary") {
                $setLine .= "\tpublic function set".ucfirst($pname)."(\$$refProperty){\n";
                $setLine .= "\t\t\$maxLength = self::getMaximumLength('$pname');\n";
                $setLine .= "\t\tif (\$this->lazyregex) \$maxLength = null;\n";
                $setLine .= "\t\tif (! (is_null(\$maxLength) || ! (strlen(\$$refProperty) > \$maxLength))) throw new Exception(\"[ $pname (\$maxLength) ] : Data Length has exceeded the size\");\n";
                $setLine .= "\t\t\$regex = self::getRegularExpression('$pname');\n";
                $setLine .= "\t\tif (\$this->lazyregex) \$regex = null;\n";
                $setLine .= "\t\tif (! (is_null(\$regex) || preg_match(\"/\".\$regex['rule'].\"/\", \$$refProperty) === 1)) throw new Exception(\"[ $pname ] : \".\$regex['message']);\n";
                switch ($type)  {
                    case "boolean":
                        $setLine .= "\t\t\$this->$pname = (intval(\$$refProperty) == 1);\n";
                        break;
                    case "object":
                        $setLine .= "\t\tif (is_null(\$$refProperty)) return \$this;\n";
                        if (is_null($refObject))    {
                            $setLine .= "\t\t\$this->$pname = \$$refProperty;\n";
                        } else if ($refKeyCheck) {
                            $setLine .= "\t\t\$this->$pname = new $refObject(\$this->database, \$$refProperty, \$this->conn);\n";
                        } else {
                            $setLine .= "\t\t\$this->$pname = new $refObject(\$$refProperty);\n";
                        }
                        break;
                    case "list-object":
                        $setLine .= "\t\tif (is_null(\$$refProperty)) return \$this;\n";
                        if (is_null($refObject))    {
                            $setLine .= "\t\t$this->$pname = \$$refProperty;\n";
                        } else     {
                            $setLine .= "\t\t\$tempArray1 = explode(\",\", \$$refProperty);\n";
                            $setLine .= "\t\t\$this->$pname = array();\n";
                            $setLine .= "\t\tforeach (\$tempArray1 as \$apropid)  {\n";
                            if ($refKeyCheck)   {
                                $setLine .= "\t\t\t\$this->$pname"."[sizeof(\$this->$pname)]"." = new $refObject(\$this->database, \$apropid, \$this->conn);\n";
                            } else {
                                $setLine .= "\t\t\t\$this->$pname"."[sizeof(\$this->$pname)]"." = new $refObject(\$apropid);\n";
                            }
                            $setLine .= "\t\t}\n";
                        }
                        break;
                    default:
                        $setLine .= "\t\t\$this->$pname = \$$refProperty;\n";
                }
                if (! is_null($colname)) {
                    $setLine .= "\t\t\$this->addToUpdateList(\"$colname\", \$$refProperty);\n";
                    $setLine .= "\t\t\$this->addToPropertyUpdateList(\"$pname\", \$$refProperty);\n";
                }
                $setLine .= "\t\treturn \$this;\n";
                $setLine .= "\t}\n";
            }
            //Getters
            $getLine = "";
            //Join At this point
           if ($role == "primary") {
               $twhere = null;
               if (! is_null($colname)) {
                    $twhere = "\\\"$colname\\\" : \$this->$pname";
               }
                if ($primaryKeyCount == 0) {
                    $getLine .= "\tpublic function getId0()  { return \$this->$pname; }\n";
                    if (! is_null($colname)) {
                        $staticLine .= "\tpublic static function getId0Columnname()   { return \"$colname\"; }\n";
                        $getLine .= "\tpublic function getId0WhereClause()  { return \"{ $twhere }\"; }\n";
                    }
                    $primaryKeyLine = "\$this->$pname";
                } else {
                    $primaryKeyLine .= ".\$this->$pname";
                }
                if (! is_null($colname)) {
                    $tval = "\"$colname\"";
                    if (is_null($primaryKeyColumnList)) $primaryKeyColumnList = $tval;
                    else $primaryKeyColumnList .= ",".$tval;
                }
                if (! is_null($twhere)) {
                    if (is_null($whereClause)) $whereClause = $twhere;
                    else $whereClause .= ", $twhere";
                }
                $primaryKeyCount++;
            }
            if ($type == "boolean") $getLine .= "\tpublic function is".ucfirst($pname)."(){\n";
            else $getLine .= "\tpublic function get".ucfirst($pname)."(){\n";
            $getLine .= "\t\treturn \$this->$pname;\n";
            $getLine .= "\t}\n";
            $line .= $setLine.$getLine;
        }
        $primaryKeyLine = "\tpublic function getId() { return md5($primaryKeyLine); }\n";
        $staticLine .= "\tpublic static function getIdColumnnames() { return array($primaryKeyColumnList); }\n";
        $primaryKeyLine .= "\tpublic function getIdWhereClause() { return \"{ $whereClause }\"; }\n";
        $line = $primaryKeyLine.$line.$staticLine;
        return $line;
    }
    private static function createObjectProperties($columnArray1)   {
        $line = "\tprotected \$database;\n";
        $line .= "\tprotected \$conn;\n";
        foreach ($columnArray1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['property-pname'])) continue;
            $pname = $stdProperties['property-pname'];
            $line .= "\tprivate \$$pname;\n";
        }
        $line .= "\tprivate \$lazyregex;\n";
        return $line;
    }
    private static function createProperty2Column($columnArray1)    {
        $line = "\tpublic static function property2Column(\$pname)    {\n";
        $line .= "\t\t\$tArray1 = array(\n";
        $count = 0;
        foreach ($columnArray1 as $columnBlock1)    {
            $column1 = self::getStandardizedProperties($columnBlock1);
            if (! isset($column1['colname']) || ! isset($column1['property-pname'])) continue;
            $colname = $column1['colname'];
            $pname = $column1['property-pname'];
            $dt = "\"$pname\" => \"$colname\"";
            if ($count == 0) $line .= "\t\t\t$dt\n";
            else $line .= "\t\t\t, $dt\n";
            $count++;
        }
        $line .="\t\t);\n";
        $line .= "\t\t\$colname = null;\n";
        $line .= "\t\tif (isset(\$tArray1[\$pname])) \$colname = \$tArray1[\$pname];\n";
        $line .= "\t\treturn \$colname;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createColumn2Property($columnArray1)    {
        $line = "\tpublic static function column2Property(\$colname)    {\n";
        $line .= "\t\t\$tArray1 = array(\n";
        $count = 0;
        foreach ($columnArray1 as $columnBlock1)    {
            $column1 = self::getStandardizedProperties($columnBlock1);
            if (! isset($column1['colname']) || ! isset($column1['property-pname'])) continue;
            $colname = $column1['colname'];
            $pname = $column1['property-pname'];
            $dt = "\"$colname\" => \"$pname\"";
            if ($count == 0) $line .= "\t\t\t$dt\n";
            else $line .= "\t\t\t, $dt\n";
            $count++;
        }
        $line .="\t\t);\n";
        $line .= "\t\t\$pname = null;\n";
        $line .= "\t\tif (isset(\$tArray1[\$colname])) \$pname = \$tArray1[\$colname];\n";
        $line .= "\t\treturn \$pname;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createClassRegularExpressionLookup($columnArray1)       {
        $line = "\tpublic static function getRegularExpression(\$colname)   {\n";
        $line .= "\t\t\$tArray1 = array();\n";
        foreach ($columnArray1 as $columnBlock1)    {
            $column1 = self::getStandardizedProperties($columnBlock1);
            if (! isset($column1['property-pname']) || ! isset($column1['settings-data-regex-rule'])) continue;
            $pname = $column1['property-pname'];
            $regex = $column1['settings-data-regex-rule'];
            $message = "Wrong Format received"; if (isset($column1['settings-data-regex-message'])) $message = $column1['settings-data-regex-message'];
            if ($regex != "")   {
                $line .= "\t\t\$tArray1['$pname'] = array();";
                $line .= "\t\t\$tArray1['$pname']['rule'] = $regex;";
                $line .= "\t\t\$tArray1['$pname']['message'] = $message;\n";
            }
        }
        $line .= "\t\t\$regexArray1 = null;\n";
        $line .= "\t\tif (isset(\$tArray1[\$colname])) \$regexArray1 = \$tArray1[\$colname];\n";
        $line .= "\t\treturn \$regexArray1;\n";
        $line .= "\t}\n";
        return $line;
    } 
    private static function createClassMaximumLengthLookup($columnArray1)    {
        $line = "\tpublic static function getMaximumLength(\$colname)    {\n";
        $line .= "\t\t\$tArray1 = array();\n";
        foreach ($columnArray1 as $columnBlock1)    {
            $column1 = self::getStandardizedProperties($columnBlock1);
            if (! isset($column1['property-pname']) || ! isset($column1['settings-data-width'])) continue;
            $pname = $column1['property-pname'];
            $width = $column1['settings-data-width'];
            if ($width != "")   {
                $line .= "\t\t\$tArray1['$pname'] = $width; \n";
            }
        }
        $line .= "\t\t\$length = null;\n";
        $line .= "\t\tif (isset(\$tArray1[\$colname])) \$length = \$tArray1[\$colname];\n";
        $line .= "\t\treturn \$length;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createColumnTransitiveMap($classname, $columnArray1)    {
        //Before Moving we need to a lookup-table for all classes with values 
        if (is_null(self::$listOfValueColumnsInAllClasses) || ! isset(self::$listOfValueColumnsInAllClasses[$classname]))   {
            //Fill it once 
            //array[classname][i] = value 
            if (is_null(self::$listOfValueColumnsInAllClasses)) self::$listOfValueColumnsInAllClasses = array();
            if (! isset(self::$listOfValueColumnsInAllClasses[$classname])) self::$listOfValueColumnsInAllClasses[$classname] = array();
            $tArray1 = array();
            foreach ($columnArray1 as $columnBlock1)    {
                $column1 = self::getStandardizedProperties($columnBlock1);
                if (! isset($column1['property-pname'])) continue;
                $pname = $column1['property-pname'];
                $role = "others"; if (isset($column1['settings-data-role'])) $role = $column1['settings-data-role'];
                if ($role == "value") {
                    $tArray1[sizeof($tArray1)] = $pname; 
                }
            }
            self::$listOfValueColumnsInAllClasses[$classname] = $tArray1;
        }
        $line = "\tpublic static function columnTransitiveMap(\$pname)  {\n";
        $line .= "\t\t\$tArray1 =  array(";
        $arrayData = null;
        foreach ($columnArray1 as $columnBlock1)    {
            $column1 = self::getStandardizedProperties($columnBlock1);
            if (! isset($column1['property-pname'])) continue;
            $pname = $column1['property-pname'];
            if (! isset($column1['type'])) continue;
            $type = $column1['type'];
            if (isset($column1['property-type'])) $type = $column1['property-type'];
            $refObj = null; if (isset($column1['property-object'])) $refObj = $column1['property-object'];
            //if ($classname == "Login") echo json_encode(self::$listOfValueColumnsInAllClasses)."  [type is $type ; refObj is $refObj; Curr Class : $classname]\n";
            if (! is_null($refObj)  && ($type == "object") && isset(self::$listOfValueColumnsInAllClasses[$refObj]))  {
                $dt = null;
                foreach (self::$listOfValueColumnsInAllClasses[$refObj] as $colname)    {
                    $t_dt = "'$refObj.$colname'";
                    if ($t_dt == "") continue;
                    if (is_null($dt)) $dt = $t_dt;
                    else $dt .= ", $t_dt";
                }
                if (! is_null($dt)) $dt = "'$pname' => array($dt)";
                else $dt = "'$pname' => '$pname'";
                if (is_null($arrayData)) $arrayData = $dt;
                else $arrayData .= ", $dt";
            } else {
                $dt = "'$pname' => '$pname'";
                if (is_null($arrayData)) $arrayData = $dt;
                else $arrayData .= ", $dt";
            }
        }
        if (! is_null($arrayData)) $line .= $arrayData;
        $line .= ");\n";
        $line .= "\t\t\$pmap = null; if (isset(\$tArray1[\$pname])) \$pmap = \$tArray1[\$pname];\n";
        $line .= "\t\treturn \$pmap;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createColumnLookupTable($columnArray1)  { 
        $line = "\tpublic static function getColumnLookupTable()   {\n";
        $line .= "\t\t\$tArray1 = array();\n";
        foreach ($columnArray1 as $columnBlock1)    {
            $column1 = self::getStandardizedProperties($columnBlock1);
            if (! isset($column1['colname']) || ! isset($column1['property-pname'])) continue;
            $colname = $column1['colname'];
            $pname = $column1['property-pname'];
            $line .= "\t\t\$tsize = sizeof(\$tArray1);";
            $line .= "\t\t\$tArray1[\$tsize] = array();";
            $line .= "\t\t\$tArray1[\$tsize]['colname'] = \"$colname\";";
            $line .= "\t\t\$tArray1[\$tsize]['pname'] = \"$pname\";\n";
        }
        $line .= "\t\treturn \$tArray1;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function stringCenter($string1, $width)  {
        $pad = " ";
        for ($i = strlen($string1); $i < $width; $i = $i + 2) $string1 = $pad.$string1.$pad;
        if (strlen($string1) < $width + 1) $string1 .= $pad; //Balance between even and odd lengths
        return $string1;
    }
    private static function createCopyrightInformation($classname = null)    {
        date_default_timezone_set("africa/dar_es_salaam");
        $timestamp = date("Y:m:d:H:i:s");
        $line = "/******************************************************\n";
        $line .= "**                                                   **\n";
        if (! is_null($classname))  {
            $line .= "**  ".self::stringCenter("CLASSNAME : ".$classname, 46)."  **\n";
        }
        $line .= "**  Copyright (c) Zoomtong Company Limited           **\n";
        $line .= "**  Developed by : Ndimangwa Fadhili Ngoya           **\n";
        $line .= "**  Timestamp    : $timestamp               **\n";
        $line .= "**  Phones       : +255 787 101 808 / 762 357 596    **\n";
        $line .= "**  Email        : ndimangwa@gmail.com               **\n";
        $line .= "**  Address      : P.O BOX 7436 MOSHI, TANZANIA      **\n";
        $line .= "**                                                   **\n";
        $line .= "**  Dedication to my dear wife Valentina             **\n";
        $line .= "**                my daughters Raheli & Keziah       **\n";
        $line .= "**                                                   **\n";
        $line .= "*******************************************************/\n";
        return $line;
    }
    private static function createCustomCodesPlaceholder()  {
        $line = "/*BEGIN OF CUSTOM CODES : You should Add Your Custom Codes Below this line*/\n\n";
        $line .= "/*END OF CUSTOM CODES : You should Add Your Custom Codes Above this line*/\n";
        return $line;
    }
    private static function formatSpaces($string1, $findSpaceArray1, $replaceBySpace = " ")    {
        foreach ($findSpaceArray1 as $space)    {
            if ($space == $replaceBySpace) continue;
            $string1 = str_replace($space, $replaceBySpace, $string1);
        } 
        $t1 = $replaceBySpace.$replaceBySpace;
        return str_replace($t1, $replaceBySpace, $string1);
    }
    private static function createStaticMethods($columnBlock1)  {
        //static.methods = "File.php://Class1.method1,Class1.method2,Class2.method1"
        $spaceArray1 = array(" ", "\t", "\n");
        $defaultSpace = " ";
        $staticFolder = self::$staticFolder;
        $line = "";
        foreach ($columnBlock1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['static-methods'])) continue;
            $tArray1 = explode(":", $stdProperties['static-methods']);
            if (sizeof($tArray1) != 2) continue;
            $filename = $staticFolder.$tArray1[0];
            $methodlist = explode(",", trim(str_replace("//", "", $tArray1[1])));
            $file1 = fopen($filename, "r") or self::shootException("Could not open file [ $filename ]");
            $bracket = 0;
            $currentClass = null;
            $currentMethod = null;
            $buffer = ""; $idname = null;
            while(! feof($file1))   {
                $char = fgetc($file1);
                //echo " [ $bracket , $char ] ";
                if ($bracket == 0 && $char == "{")  {
                    $pos = strpos($buffer, "class");
                    if ($pos === false) self::shootException("class [$buffer] not declared in the original file");
                    $buffer = substr($buffer, $pos);
                    $buffer = self::formatSpaces($buffer, $spaceArray1, $defaultSpace);
                    $tArray1 = explode($defaultSpace, $buffer);
                    if (sizeof($tArray1) < 2) self::shootException("Classname Could not be extracted");
                    $currentClass = $tArray1[1];
                    $buffer = "";
                    //Found Classname 
                    $bracket++;
                } else if ($bracket == 0 && $char == "}")   {
                    self::shootException("[ Static Methods ] : Unsupported brackets pair, more closing brackets than opened brackets");
                } else if ($bracket == 1 && $char == "{")   {
                    $pos = strpos($buffer, "function");
                    if ($pos === false) self::shootException("function not declared");
                    $lpos = strpos($buffer, "(");
                    if ($lpos == false) self::shootException("function paranthesis not defined");
                    if (! ($pos < $lpos)) self::shootException("Paranthesis defined prior function declaration");
                    $idname = substr($buffer, $pos, $lpos - $pos);
                    $idname = self::formatSpaces($idname, $spaceArray1, $defaultSpace);
                    $tArray1 = explode($defaultSpace, $idname);
                    if (sizeof($tArray1) < 2) self::shootException("Function name could not be extracted");
                    $currentMethod = $tArray1[1];
                    //Found Method name
                    $buffer .= $char;
                    $idname = $currentClass.".".$currentMethod;
                    if (in_array($idname, $methodlist)) {
                        $line .= $buffer;
                    } else {
                        $idname = null;
                    }
                    $bracket++;
                    $buffer = "";
                } else if ($bracket == 2 && $char == "}")   {
                    //Closing Method 
                    $buffer = "";
                    if (! is_null($idname)) $line .= $char;
                    $bracket--;
                } else if ($bracket == 1 && $char == "}")   {
                    //Closing Class 
                    $bracket--;
                } else if ($char == "{")    {
                    if (! is_null($idname)) $line .= $char;
                    $bracket++;
                } else if ($char == "}")    {
                    if (! is_null($idname)) $line .= $char;
                    $bracket--;
                } else if ($bracket == 0 || $bracket == 1)   {
                    //Any Char -- we need to figure the classname  or function name
                    $buffer .= $char;
                } else if ($bracket > 1)    {
                    //Inside a function 
                    if (! is_null($idname)) $line .= $char;
                }
            }
            if ($bracket != 0) self::shootException("[ Static Methods ] : Unsupported bracket pairs, not all open brackets are closed");
            fclose($file1);
        }
        //Format line properly
        $line = trim($line);
        $line = "\t$line\n";
        return $line;
    }
    private static function createStaticVariables($columnBlock1)    {
        $line = "";
        foreach ($columnBlock1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['static-variables'])) continue;
            $jsonVariable1 = $stdProperties['static-variables'];
            if ($jsonVariable1 == "" || is_null($jsonVariable1)) continue;
            $jsonVariable1 = str_replace("'", "\"", $jsonVariable1);
            $jArray1 = json_decode($jsonVariable1, true);
            if (is_null($jArray1)) continue;
            foreach ($jArray1 as $variable1 => $value) $line .= "\tpublic static \$__$variable1 = $value;\n"; 
        }
        return $line;
    }
    private static function createGetSystemBinaryConstraints($columnBlock1) {
        $line = "\tprotected function getMySystemBinaryConstraints()   { return self::getSystemBinaryConstraints(); }\n";
        $line .= "\tpublic static function getSystemBinaryConstraints()  {\n";
        $line .= "\t\t\$constraints = array(\n";
        $acount = 0;
        foreach ($columnBlock1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['property-pname'])) continue;
            if (! isset($stdProperties['settings-data-binary-constraints'])) continue;
            $jsonVariable1 = $stdProperties['settings-data-binary-constraints'];
            if ($jsonVariable1 == "" || is_null($jsonVariable1)) continue;
            $jsonVariable1 = str_replace("'", "\"", $jsonVariable1);
            $jArray1 = json_decode($jsonVariable1, true);
            if (is_null($jArray1)) continue;
            if (! (isset($jArray1['pname']) && isset($jArray1['op']) && isset($jArray1['negate']) && isset($jArray1['error-message']))) continue;
            $lpname = $stdProperties['property-pname'];
            $rpname = $jArray1['pname'];
            $op = $jArray1['op'];
            $errorMessage = $jArray1['error-message'];
            $negate = "false"; if($jArray1['negate']) $negate = "true";
            $dt = "array(\"lpname\" => \"$lpname\", \"rpname\" => \"$rpname\", \"negate\" => $negate, \"op\" => \"$op\", \"error-message\" => \"$errorMessage\")\n";
            if ($acount == 0) $line .= "\t\t\t$dt";
            else $line .= "\n\t\t\t, $dt";
            $acount++;
        }
        $line .= "\t\t);\n";
        $line .= "\t\tif (sizeof(\$constraints) == 0) \$constraints = null;\n";
        $line .= "\t\treturn \$constraints;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createGetListOfPropertiesOfClass($columnBlock1)    {
        $lineArray1 = array();
        foreach ($columnBlock1 as $column1) {
            $stdProperties = self::getStandardizedProperties($column1);
            if (! isset($stdProperties['property-object'])) continue;
            $pobject = $stdProperties['property-object'];
            if (! isset($stdProperties['property-pname'])) continue;
            $pname = $stdProperties['property-pname'];
            if (! isset($lineArray1[$pobject])) $lineArray1[$pobject] = array();
            $lineArray1[$pobject][sizeof($lineArray1[$pobject])] = $pname;
        }
        $line = "\tpublic static function getListOfPropertiesOfClass(\$__classname)   {\n";
        $line .= "\t\t\$tArray1 = array(\n";
        $icount = 0;
        foreach ($lineArray1 as $pobject => $pArray1)  {
            $dti = "\"$pobject\" => array(";
            $jcount = 0;
            foreach ($pArray1 as $pname)    {
                $dtj = "\"$pname\"";
                if ($jcount == 0) $dti .= $dtj;
                else $dti .= ", $dtj";
                $jcount++;
            }
            $dti .= ")\n";
            if ($icount == 0) $line .= "\t\t\t$dti";
            else $line .= "\t\t\t, $dti";
            $icount++;
        }
        $line .= "\t\t);\n";
        $line .= "\t\t\$pList = null; if (isset(\$tArray1[\$__classname])) \$pList = \$tArray1[\$__classname];\n";
        $line .= "\t\treturn \$pList;\n";
        $line .= "\t}\n";
        return $line;
    }
    private static function createAClass($classArray1)  {
        $classname = $classArray1['class'];
        $tablename = $classArray1['table'];
        $defaultExtendClass = "__data__";
        $customExtendClass = self::getOneValueFromColumns($classArray1['columns'], "settings-code-inherit-class");
        if (! is_null($customExtendClass)) $defaultExtendClass = $customExtendClass;
        $line = "<?php\n";
        $line .= self::createCopyrightInformation($classname);
        $line .= "class $classname extends $defaultExtendClass {\n";
        //Building class properties
        $line .= self::createStaticVariables($classArray1['columns']);
        $line .= self::createObjectProperties($classArray1['columns']);
        $line .= self::createStaticMethods($classArray1['columns']);
        $line .= self::createCustomCodesPlaceholder();
        $line .= "\tpublic static function create(\$database, \$id, \$conn) { return new $classname(\$database, \$id, \$conn); }\n";
        $line .= self::createAConstructor($classArray1['columns']);
        //loadAllData
        $loadAllDataLine = self::createLoadAllData($classArray1['columns']);
        if ($loadAllDataLine != "") self::$listOfLoadAllDataClasses[sizeof(self::$listOfLoadAllDataClasses)] = $classname;
        $line .= $loadAllDataLine;
        //Setters and Getters
        $line .= self::createObjectSettersAndGetters($classArray1['columns']);
        //Create A Reference Class 
        $line .= self::createGetReferenceClass($classArray1['columns']);
        //Create A createGetColumnType
        $line .= self::createGetColumnType($classArray1['columns']);
        //Create RegularExpression ReferenceMethod
        $line .= self::createClassRegularExpressionLookup($classArray1['columns']);
        $line .= self::createClassMaximumLengthLookup($classArray1['columns']);
        //These lookups and general functions can stay at the end
        $line .= "\tpublic function getObjectReferenceString()  { \$t1 = self::getClassname(); \$t2 = \$this->getId0(); return \"\$t1.\$t2\"; }\n";
        $line .= "\tpublic function getMyClassname()    { return self::getClassname(); }\n";
        $line .= "\tpublic function getMyTablename()    { return self::getTablename(); }\n";
        $line .= "\tpublic function getMyId0Columnname()  { return self::getId0Columnname(); }\n";
        $line .= "\tpublic static function getClassname()  { return \"$classname\"; }\n";
        $line .= "\tpublic static function getTablename()  { return \"$tablename\"; }\n";
        $line .= self::createColumn2Property($classArray1['columns']);
        $line .= self::createProperty2Column($classArray1['columns']);
        $line .= self::createColumnLookupTable($classArray1['columns']);
        $line .= self::createColumnTransitiveMap($classname, $classArray1['columns']);
        $line .= self::createGetSearchableColumns($classArray1['columns']);
        $line .= self::createASearchUI($classname, $classArray1['columns']);
        $line .= self::createGetPropertyValue($classArray1['columns']);
        $line .= self::createValueColumns($classArray1['columns']);
        $line .= self::createGetNameAndGetName0($classArray1['columns']);
        $line .= self::createGetSystemBinaryConstraints($classArray1['columns']);
        $line .= self::createGetListOfPropertiesOfClass($classArray1['columns']);
        $line .= "\tpublic function update(\$rollback = true)    { parent::update(\$rollback); return \$this; }\n";
        $line .= "\tpublic function delete(\$rollback = true)    { parent::delete(\$rollback); return \$this; }\n";
        $line .= "}\n";
        $line .= "?>";

        return $line;
    }
    private static function createClasses($jsonArray1, $classFolder, $autoLoadFile = "__autoload__.php", $initialAutoLoadFiles = array(
            "__configurationdata__.php",
            "__systemrules__.php",
            "__jsontosql__.php", 
            "__sqlengine__.php", 
            "__pdfengine__.php",
            "__simplequerybuilder__.php",
            "__dateandtime__.php",
            "__datafile__.php", 
            "__promise__.php",
            "__collection__.php",
            "__network__.php",
            "__nodetree__.php",
            "__system__.php",
            "__number__.php",
            "__object__.php", 
            "__registry__.php", 
            "__data__.php",
            "__contextmanager__.php",
            "__contextlookup__.php",
            "__contextposition__.php",
            "__authorization__.php",
            "__systemlogs__.php",
            "__pagemovement__.php",
            "__ui_view__.php",
            "__ui_tabular_view__.php",
            "__ui_card_view__.php"
        ))  {
        $lineAutoLoad = "<?php\n";
        foreach ($initialAutoLoadFiles as $fname) $lineAutoLoad .= "require_once(\"$fname\");\n";
        foreach ($jsonArray1 as $classArray1)   {
            if (! isset($classArray1['table'])) throw new Exception("Table not set");
            if (! isset($classArray1['class'])) throw new Exception("Class not set");
            $classname = $classArray1['class']; 
            if (! isset($classArray1['columns'])) throw new Exception("Columns not set");
            if (self::foundValueInOneOfColumns($classArray1['columns'], "settings-code-generate-modal-class", false)) continue;
            file_put_contents(join(DIRECTORY_SEPARATOR, [$classFolder, $classname.".php"]), self::createAClass($classArray1));
            $lineAutoLoad .= "require_once(\"$classname.php\");\n";
        }
        //We need to add also classes
        if (! is_null(self::$classesFolder))    {
           foreach (array_diff(scandir(self::$classesFolder), array('.', '..')) as $file) $lineAutoLoad .= "require_once(\"$file\");\n";
        }
        $lineAutoLoad .= "?>";
        file_put_contents(join(DIRECTORY_SEPARATOR, [$classFolder, $autoLoadFile]), $lineAutoLoad);
    }
    private static function createDocumentation($jsonArray1, $docFolder, $docFile, $sourceIndexFile, $destinationIndexFile = "index.php")   {
        $line = "<div class=\"ui-documentation\">";
        foreach ($jsonArray1 as $classArray1)   {
            if (! isset($classArray1['table'])) throw new Exception("Table not set");
            if (! isset($classArray1['class'])) throw new Exception("Class not set");
            $classname = $classArray1['class'];
            $tablename = $classArray1['table'];
            if (! isset($classArray1['columns'])) throw new Exception("Columns not set");
            $line .= "<div class=\"border border-primary mb-2\"><table class=\"table\"><thead><tr><th colspan=\"6\">Class : $classname </th></tr><tr><th colspan=\"6\">Table : $tablename </th></tr><tr><th scope=\"col\"></th><th>role</th><th>pname</th><th>refClass</th><th>Type</th><th>Comments</th></tr></thead><tbody>";
            $count = 0;
            $staticArray1 = array();
            foreach ($classArray1['columns'] as $columnBlock1)   {
                $stdProperties = self::getStandardizedProperties($columnBlock1);
                if (! isset($stdProperties['property-pname'])) continue;
                $pname = $stdProperties['property-pname'];
                $refClass = ""; if (isset($stdProperties['property-object'])) $refClass = $stdProperties['property-object'];
                $type = ""; if (isset($stdProperties['type'])) $type = $stdProperties['type'];
                if (isset($stdProperties['property-type'])) $type .= "/".$stdProperties['property-type'];
                $comments = ""; if (isset($stdProperties['comments'])) $comments = $stdProperties['comments'];
                $width = "";  $twidth = $width;
                if (isset($stdProperties['settings-data-width'])) { $twidth = $stdProperties['settings-data-width']; $width = "(~$twidth)"; }
                if ($twidth != "" && isset($stdProperties['settings-data-fixed-width']) && $stdProperties['settings-data-fixed-width']) { $width = "($twidth)";  }
                $roleText = "";
                if (isset($stdProperties['settings-data-role'])) {
                    $role = $stdProperties['settings-data-role'];
                    if ($role == "primary") $roleText = "*pri*";
                    else if ($role == "value") $roleText = "*val*";
                    //Format
                }
                $sn = $count + 1;
                $line .= "<tr><th scope=\"row\">$sn</th><td><b><i>$roleText</i></b></td><td>$pname</td><td>$refClass</td><td>$type $width </td><td>$comments</td></tr>";
                if (isset($stdProperties['static-variables']))  {
                    $jArray1 = str_replace("'", "\"", $stdProperties['static-variables']);
                    $jArray1 = json_decode($jArray1, true);
                    if (! is_null($jArray1))    {
                        foreach ($jArray1 as $key => $val)  $staticArray1["__$key"] = $val;
                    }
                }
                $count++;
            }
            $line .= "</tbody></table>";
            if (sizeof($staticArray1) > 0)  {
                $line .= "<div class=\"text-muted\" style=\"font-size: 0.9em;\"><table class=\"table table-sm mb-2\"><thead><tr><th colspan=\"3\">Static Variables</th></tr><tr><th scope=\"col\"></th><th>Key</th><th>Val</th></tr></thead><tbody>";
                $count = 0;
                foreach ($staticArray1 as $key => $val) {
                    $sn = $count + 1;
                    $line .= "<tr><th scope=\"row\">$sn</th><td>$key</td><td>$val</td></tr>";
                    $count++;
                }
                $line .= "</tbody></table></div>";
            }
            $line .= "</div>";
        }
        $line .= "</div>";
        //Put index File
        copy($sourceIndexFile, join(DIRECTORY_SEPARATOR, [$docFolder, $destinationIndexFile]));
        file_put_contents(join(DIRECTORY_SEPARATOR, [$docFolder, $docFile]), $line);
    }
    private static function createRegistry($jsonArray1, $classFolder, $registryFile = "__registry__.php")    {
        $line = "<?php\n";
        $line .= "class Registry extends __object__ {\n";
        $objrefline = "\tpublic static function getObjectReference(\$__database, \$__conn, \$__classname, \$__id) {\n";
        $objrefline .= "\t\t\$refObj = null;\n";
        $tablenameline = "\tpublic static function getTablename(\$__classname)  {\n";
        $tablenameline .= "\t\t\$tname = null;\n";
        $uiControlValidationLine = "\tpublic static function getUIControlValidations(\$__classname, \$__columnname, \$__controltype = \"text\")  {\n";
        $uiControlValidationLine .= "\t\t\$uirule = null;\n";
        $uiControlValidationLine .= "\t\t\$regexArray1 = null; \$maxLength = null;\n";
        $property2columnLine = "\tpublic static function property2column(\$__classname, \$__pname)  {\n";
        $property2columnLine .= "\t\t\$colname = null;\n"; 
        $column2PropertyLine = "\tpublic static function column2Property(\$__classname, \$__colname)    {\n";
        $column2PropertyLine .= "\t\t\$pname = null;\n";
        $loadAllDataLine = "\tpublic static function loadAllData(\$__conn, \$__classname)   {\n";    
        $loadAllDataLine .= "\t\t\$dataArray1 = null;\n";
        $refClassLine = "\tpublic static function getReferenceClass(\$__classname, \$__pname)   {\n";
        $refClassLine .= "\t\t\$refclass = null;\n";
        $id0ColumnnameLine = "\tpublic static function getId0Columnname(\$__classname)    {\n";
        $id0ColumnnameLine .= "\t\t\$colname = \"\";\n"; 
        $columnTypeLine = "\tpublic static function getColumnType(\$__classname, \$__pname) {\n";
        $columnTypeLine .= "\t\t\$coltype = null;\n";  
        $idColumnnamesLine = "\tpublic static function getIdColumnnames(\$__classname)  {\n";
        $idColumnnamesLine .= "\t\t\$colname = null;\n";
        $searchableLine = "\tpublic static function getSearchableColumns(\$__classname) {\n";
        $searchableLine .= "\t\t\$pname = null;\n";
        $transitiveMapLine = "\tpublic static function columnTransitiveMap(\$__classname, \$__pname)    {\n";
        $transitiveMapLine .= "\t\t\$pmap = null;\n";
        $value0Line = "\tpublic static function getValue0Columnname(\$__classname)  {\n";
        $value0Line .= "\t\t\$colname = null;\n";
        $valueLines = "\tpublic static function getValueColumnnames(\$__classname)  {\n";
        $valueLines .= "\t\t\$colname = null;\n";
        $instanceLine = "\tpublic static function getInstance(\$__database, \$__conn, \$__objectreference) {\n";
        $instanceLine .= "\t\tif (is_null(\$__objectreference)) return null;\n";
        $instanceLine .= "\t\t\$tArray1 = explode(\".\", \$__objectreference);\n";
        $instanceLine .= "\t\tif (sizeof(\$tArray1) != 2) throw new Exception(\"[ getInstance() ] : class or id not specified properly classname.id\");\n";
        $instanceLine .= "\t\treturn self::getObjectReference(\$__database, \$__conn, \$tArray1[0], \$tArray1[1]);\n";
        $instanceLine .= "\t}\n";
        $maxLengthLine = "\tpublic static function getMaximumLength(\$__classname, \$__colname) {\n";
        $maxLengthLine .= "\t\t\$maxlen = null;\n";
        $regexLine = "\tpublic static function getRegularExpression(\$__classname, \$__colname) {\n";
        $regexLine .= "\t\t\$regex = null;\n";
        $listOfObjectsLine = "\tpublic static function getListOfObjects(\$__conn, \$__classname, \$__listOfIds) {\n";
        $listOfObjectsLine .= "\t\tif (is_null(\$__listOfIds)) return null;\n";
        $listOfObjectsLine .= "\t\t\$listOfObjects = null;\n";
        $listOfObjectsLine .= "\t\tforeach (\$__listOfIds as \$id)  { ";
        $listOfObjectsLine .= " \$listOfObjects[sizeof(\$listOfObjects)] = self::getObjectReference(\"Delta Int\", \$__conn, \$__classname, \$id); ";
        $listOfObjectsLine .= " }\n";
        $listOfObjectsLine .= "\t\treturn \$listOfObjects;\n";
        $listOfObjectsLine .= "\t}\n";
        $lineBinaryConstraints = "\tpublic static function getSystemBinaryConstraints(\$__classname) {\n";
        $lineBinaryConstraints .= "\t\t\$constraints = null;\n";
        $linePropertiesForClass = "\tpublic static function getListOfPropertiesOfClass(\$__classname, \$__class_we_need_properties) {\n";
        $linePropertiesForClass .= "\t\t\$pList = null;\n";
        $count = 0; $loadAllDataCount = 0;
        foreach ($jsonArray1 as $classArray1)   {
            if (! isset($classArray1['table'])) throw new Exception("Table not set");
            if (! isset($classArray1['class'])) throw new Exception("Class not set");
            if (! isset($classArray1['columns'])) throw new Exception("Columns not set");
            if (self::foundValueInOneOfColumns($classArray1['columns'], "settings-code-generate-modal-class", false)) continue;
            $classname = $classArray1['class']; 
            $dt = "if (\$__classname == \"$classname\") \$refObj = new $classname(\$__database, \$__id, \$__conn);";
            if ($count == 0) $objrefline .= "\t\t$dt\n";
            else $objrefline .= "\t\telse $dt\n";
            //Working for table
            $dt = "if (\$__classname == \"$classname\") \$tname = $classname::getTablename();";
            if ($count == 0) $tablenameline .= "\t\t$dt\n";
            else $tablenameline .= "\t\telse $dt\n";
            //Working with $uiControlValidationLine
            $dt = "if (\$__classname == \"$classname\") {\n";
            $dt .= "\t\t\t\$regexArray1 = $classname::getRegularExpression(\$__columnname);\n";
            $dt .= "\t\t\t\$maxLength = $classname::getMaximumLength(\$__columnname);\n";
            $dt .= "\t\t}";
            if ($count == 0) $uiControlValidationLine .= "\t\t$dt";
            else $uiControlValidationLine .= " else $dt"; 
            //Working for property2Column
            $dt = "if (\$__classname == \"$classname\") {\n";
            $dt .= "\t\t\t\$colname = $classname::property2Column(\$__pname);\n";
            $dt .= "\t\t}";
            if ($count == 0) $property2columnLine .= "\t\t$dt";
            else $property2columnLine .= " else $dt\t";
            //Working for column2Property
            $dt = "if (\$__classname == \"$classname\") {\n";
            $dt .= "\t\t\t\$pname = $classname::column2Property(\$__colname);\n";
            $dt .= "\t\t}";
            if ($count == 0) $column2PropertyLine .= "\t\t$dt";
            else $column2PropertyLine .= " else $dt\t";
            //Working with loadAllData
            $dt = "if (\$__classname == \"$classname\" && method_exists(\"$classname\", \"loadAllData\")) \$dataArray1 = $classname::loadAllData(\$__conn);";
            if ($count == 0) $loadAllDataLine .= "\t\t$dt\n";
            else $loadAllDataLine .= "\t\telse $dt\n";
            //Working with referenceClass
            $dt = "if (\$__classname == \"$classname\") {\n";
            $dt .= "\t\t\t\$refclass = $classname::getReferenceClass(\$__pname);\n";    
            $dt .= "\t\t}";
            if ($count == 0) $refClassLine .= "\t\t$dt";
            else $refClassLine .= " else $dt\t";
            //Working with id0Columnname
            $dt = "if (\$__classname == \"$classname\") {\n";
            $dt .= "\t\t\t\$colname = $classname::getId0Columnname();\n";    
            $dt .= "\t\t}";
            if ($count == 0) $id0ColumnnameLine .= "\t\t$dt";
            else $id0ColumnnameLine .= " else $dt\t";
            //Working with coltype
            $dt = "if (\$__classname == \"$classname\") \$coltype = $classname::getColumnType(\$__pname);";
            if ($count == 0) $columnTypeLine .= "\t\t$dt\n";
            else $columnTypeLine .= "\t\telse $dt\n";
            //Working with idColumnnames 
            $dt = "if (\$__classname == \"$classname\") \$colname = $classname::getIdColumnnames();";
            if ($count == 0) $idColumnnamesLine .= "\t\t$dt\n";
            else $idColumnnamesLine .= "\t\telse $dt\n";
            //Working with searchableColumns
            $dt = "if (\$__classname == \"$classname\") \$pname = $classname::getSearchableColumns();";
            if ($count == 0) $searchableLine .= "\t\t$dt\n";
            else $searchableLine .= "\t\telse $dt\n";
            //Working for transitive map
            $dt = "if (\$__classname == \"$classname\") \$pmap = $classname::columnTransitiveMap(\$__pname);";
            if ($count == 0) $transitiveMapLine .= "\t\t$dt\n";
            else $transitiveMapLine .= "\t\telse $dt\n";
            //value0Columnname
            $dt = "if (\$__classname == \"$classname\") \$colname = $classname::getValue0Columnname();";
            if ($count == 0) $value0Line .= "\t\t$dt\n";
            else $value0Line .= "\t\telse $dt\n";
            //valueColumnnames
            $dt = "if (\$__classname == \"$classname\") \$colname = $classname::getValueColumnnames();";
            if ($count == 0) $valueLines .= "\t\t$dt\n";
            else $valueLines .= "\t\telse $dt\n";
            //MaxLength
            $dt = "if (\$__classname == \"$classname\") \$maxlen = $classname::getMaximumLength(\$__colname);";
            if ($count == 0) $maxLengthLine .= "\t\t$dt\n";
            else $maxLengthLine .= "\t\telse $dt\n";
            //Regex
            $dt = "if (\$__classname == \"$classname\") \$regex = $classname::getRegularExpression(\$__colname);";
            if ($count == 0) $regexLine .= "\t\t$dt\n";
            else $regexLine .= "\t\telse $dt\n";
            //BinaryConstraints
            $dt = "if (\$__classname == \"$classname\") \$constraints = $classname::getSystemBinaryConstraints();";
            if ($count == 0) $lineBinaryConstraints .= "\t\t$dt\n";
            else $lineBinaryConstraints .= "\t\telse $dt\n";
            //Properties For A Class
            $dt = "if (\$__classname == \"$classname\") \$pList = $classname::getListOfPropertiesOfClass(\$__class_we_need_properties);";
            if ($count == 0) $linePropertiesForClass .= "\t\t$dt\n";
            else $linePropertiesForClass .= "\t\telse $dt\n";
            $count++;
        }
        $linePropertiesForClass .= "\t\treturn \$pList;\n";
        $linePropertiesForClass .= "\t}\n";
        $lineBinaryConstraints .= "\t\treturn \$constraints;\n";
        $lineBinaryConstraints .= "\t}\n";
        $regexLine .= "\t\treturn \$regex;\n";
        $regexLine .= "\t}\n";
        $maxLengthLine .= "\t\treturn \$maxlen;\n";
        $maxLengthLine .= "\t}\n";
        $valueLines .= "\t\treturn \$colname;\n";
        $valueLines .= "\t}\n";
        $value0Line .= "\t\treturn \$colname;\n";
        $value0Line .= "\t}\n";
        $transitiveMapLine .= "\t\treturn \$pmap;\n";
        $transitiveMapLine .= "\t}\n";
        $searchableLine .= "\t\treturn \$pname;\n";
        $searchableLine .= "\t}\n";
        $idColumnnamesLine .= "\t\treturn \$colname;\n";
        $idColumnnamesLine .= "\t}\n";
        $columnTypeLine .= "\t\treturn \$coltype;\n";
        $columnTypeLine .= "\t}\n"; 
        $id0ColumnnameLine .= "\n\t\treturn \$colname;\n";
        $id0ColumnnameLine .= "\t}\n";
        $refClassLine .= "\n\t\treturn \$refclass;\n";
        $refClassLine .= "\t}\n";
        $loadAllDataLine .= "\n\t\treturn \$dataArray1;\n";
        $loadAllDataLine .= "\t}\n";
        $objrefline .= "\t\treturn \$refObj;\n";
        $objrefline .= "\t}\n";
        $tablenameline .= "\t\treturn \$tname;\n";
        $tablenameline .= "\t}\n";
        $uiControlValidationLine .= "\n"; //Move Cursor to the next-line
        $uiControlValidationLine .= "\t\tif (! is_null(\$regexArray1)) { \$rule = \$regexArray1['rule']; \$message = \$regexArray1['message']; \$uirule = \"data-validation=\\\"true\\\" data-validation-control=\\\"\$__controltype\\\" data-validation-expression=\\\"\$rule\\\" data-validation-message=\\\"\$message\\\"\"; }\n";
        $uiControlValidationLine .= "\t\tif (! is_null(\$maxLength)) { \$dt = \"data-max-length = \\\"\$maxLength\\\"\"; if (is_null(\$uirule))  \$uirule = \$dt; else \$uirule .= \" \".\$dt; }\n";
        $uiControlValidationLine .= "\t\tif (is_null(\$uirule)) \$uirule = \"\";\n";
        $uiControlValidationLine .= "\t\treturn \$uirule;\n";
        $uiControlValidationLine .= "\t}\n";
        $column2PropertyLine .= "\n\t\treturn \$pname;\n";
        $column2PropertyLine .= "\t}\n";
        $property2columnLine .= "\n\t\treturn \$colname;\n";
        $property2columnLine .= "\t}\n";
        $line .= $objrefline.$instanceLine.$listOfObjectsLine.$tablenameline.$uiControlValidationLine.$column2PropertyLine.$property2columnLine.$loadAllDataLine.$refClassLine.$id0ColumnnameLine.$idColumnnamesLine.$columnTypeLine.$searchableLine.$transitiveMapLine.$value0Line.$valueLines.$maxLengthLine.$regexLine;
        $line .= $lineBinaryConstraints.$linePropertiesForClass;
        $line .= "}\n";
        $line .= "?>";
        file_put_contents(join(DIRECTORY_SEPARATOR, [$classFolder, $registryFile]), $line);
    }
    private static function createWebDirectory($targetFolder, $webFolder = "web", $indexFiles = array('index.html', 'index.php'))   {
        $files = array_diff(scandir($webFolder), array('.', '..'));
        $foundIndexFile = false;
        $sourceFolder = trim(str_replace(DIRECTORY_SEPARATOR, "", self::$accessForbiddenFolder));
        foreach ($files as $file)   {  
            $tfile = join(DIRECTORY_SEPARATOR, [$webFolder, $file]);
            if (is_dir($tfile)) {
                $t_targetFolder = join(DIRECTORY_SEPARATOR, [$targetFolder, $file]);
                if (! mkdir($t_targetFolder)) throw new Exception("Could not create $t_targetFolder");
                self::createWebDirectory($t_targetFolder, $tfile);
            } else  copy($tfile, join(DIRECTORY_SEPARATOR, [$targetFolder, $file]));
            if (in_array($file, $indexFiles)) $foundIndexFile = true;
        }
        if (! $foundIndexFile)  {
            $indexFile = "index.html"; $cssFile = "forbidden.css";
            copy(join(DIRECTORY_SEPARATOR, [$sourceFolder, $indexFile]), join(DIRECTORY_SEPARATOR, [$targetFolder, $indexFile]));
            copy(join(DIRECTORY_SEPARATOR, [$sourceFolder, $cssFile]), join(DIRECTORY_SEPARATOR, [$targetFolder, $cssFile]));
        }
        return $targetFolder;
    }
    public static function build($initFolder, $targetFolder, $schemaPath, $staticFolder, $classesFolder, $jsonContent, $sourceIndexDocFile, $accessForbiddenFolder, $seqNumber = 0)  {
        self::$staticFolder = $staticFolder;
        self::$classesFolder = $classesFolder;
        self::$accessForbiddenFolder = $accessForbiddenFolder;
        //Step 01: Validating the jsonContent 
        if (! file_exists($initFolder)) throw new Exception("Initialization Folder does not exists");
        $jsonArray1 = json_decode($jsonContent, true);
        if (is_null($jsonArray1)) throw new Exception("Error in parsing JSON File");
        $targetFolder = trim(str_replace(DIRECTORY_SEPARATOR, "", $targetFolder));
        //Step 02: Create a Directory Structure 
        $langFolder = "lang";
        $uiFolder = "ui";
        $sysFolder = "sys";
        $sqlFolder = "sql";
        $webFolder = "web";
        $docFolder = "docs";
        $subFolders = [$langFolder, $uiFolder, $sysFolder, $sqlFolder, $docFolder];
        $listOfForbiddenFolders = [$langFolder, $uiFolder, $sqlFolder, $sysFolder];
        $registryFile = "__registry__.php";
        self::createDirectoryStructure($initFolder, $targetFolder, $subFolders, $listOfForbiddenFolders);

        //Step 03: Creating Documentation
        $docFile = "__my_documentation__.php";
        self::createDocumentation($jsonArray1, join(DIRECTORY_SEPARATOR, [$targetFolder, $docFolder]), $docFile, $sourceIndexDocFile, "index.php");
        //Step 04: Creating an SQL file 
        $sqlFile = "__init_db__.sql";
        self::createSQLFile(dirname($schemaPath), $jsonArray1, join(DIRECTORY_SEPARATOR, [$targetFolder, $sqlFolder, $sqlFile]));
        //Step 05: Copy All init Files to sysFolder
        self::copyInitFiles($initFolder, join(DIRECTORY_SEPARATOR, [$targetFolder, $sysFolder]));
        //Copy All Classes Files to sysFolder
        self::copyClassesFiles($classesFolder, join(DIRECTORY_SEPARATOR, [$targetFolder, $sysFolder]));
        //Step 06: Build All Classes
        self::createClasses($jsonArray1, join(DIRECTORY_SEPARATOR, [$targetFolder, $sysFolder]));
        
        //Step 07: Build Registry
        self::createRegistry($jsonArray1, join(DIRECTORY_SEPARATOR, [$targetFolder, $sysFolder]), $registryFile);
    
        //Step 08: Copying web directory
        self::createWebDirectory($targetFolder, $webFolder);
    }
}
