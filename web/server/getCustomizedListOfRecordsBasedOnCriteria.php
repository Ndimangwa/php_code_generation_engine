<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
require_once('../common/__autoload__.php');
/*Not necessarily to be logged in*/
//if (!isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'message' => 'Session Expired, Kindly login again or refresh the page')));
if (!isset($_REQUEST['__search_input_text__'])) die(json_encode(array('code' => 2, 'message' => 'Search Text Not Found')));
if (!isset($_REQUEST['__classname__'])) die(json_encode(array('code' => 3, 'message' => 'Class is not defined')));
if (!isset($_REQUEST['__bound_columns__'])) die(json_encode(array('code' => 4, 'message' => 'Bound Columns Not defined')));
if (!isset($_REQUEST['__include_columns__'])) die(json_encode(array('code' => 5, 'message' => 'Include Columns Not defined')));
if (! isset($_REQUEST['__target_container__'])) die(json_encode(array('code' => 6, 'message' => 'Target Container is not set')));
$classname = $_REQUEST['__classname__'];
$boundColumnArray1 = $_REQUEST['__bound_columns__'];
$includeColumnArray1 = $_REQUEST['__include_columns__'];
$searchInputText = $_REQUEST['__search_input_text__'];
$filterArray1 = isset($_REQUEST['__filter__']) ? $_REQUEST['__filter__'] : null;
$filterOp = isset($_REQUEST['__filter_op__']) ? $_REQUEST['__filter_op__'] : null;
$targetContainer = $_REQUEST['__target_container__'];
$config1 = new ConfigurationData("../config.php");
$cname = isset($_REQUEST['__context_name__']) ? $_REQUEST['__context_name__'] : strtolower($classname . "_search");
// -- Login Not Required if (!Authorize::isAllowable($config1, $cname, "normal", "donotsetlog", null, null)) die(json_encode(array('code' => 3, 'message' => "[ $cname ] : Not Allowed to perform action")));
$records = null;
$conn = null;
$primaryColumn = null;
$valueColumns = null;
$captionColumnArray1 = array();
$mapColumnArray1 = array();
$query = null;
try {
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $tablename = Registry::getTablename($classname);
    if (is_null($tablename)) die(json_encode(array('code' => 1, 'message' => 'Returned Null Table')));
    $dbcolumnArray1 = array();
    foreach ($boundColumnArray1 as $pname) {
        $col = Registry::property2Column($classname, $pname);
        if (!is_null($col)) $dbcolumnArray1[sizeof($dbcolumnArray1)] = $col;
    }
    if (sizeof($dbcolumnArray1) == 0) die(json_encode(array('code' => 1, 'message' => 'Could Not Extract DB Columns')));
    $wherelist = null;
    foreach ($dbcolumnArray1 as $col) {
        $dt = "( $col LIKE '%$searchInputText%' )";
        if (is_null($wherelist)) $wherelist = $dt;
        else $wherelist .= " OR $dt";
    }
    //Performing Filteration
    if (!is_null($filterArray1)) {
        $filterlist = null;
        foreach ($filterArray1 as $pname => $filterData) {
            $colname = Registry::property2column($classname, $pname);
            if (is_null($colname)) continue;
            $t1 = implode(",", $filterData);
            $t1 = "($colname IN ( $t1 ))";
            if (is_null($filterlist)) $filterlist = $t1;
            else $filterlist .= " AND $t1";
        }
        if (!is_null($filterlist)) {
            if (!is_null($filterOp)) {
                switch ($filterOp) {
                    case "not":
                        $filterlist = "NOT ( $filterlist )";
                        break;
                }
            }
            $wherelist = "( $wherelist ) AND ( $filterlist )";
        }
    }
    //Working with collist
    $collistArray1 = array();
    foreach ($includeColumnArray1 as $pname => $psettings) {
        //--Start --settings
        if (is_array($psettings)) {
            foreach ($psettings as $key => $val) {
                if ($key == "caption") $captionColumnArray1[$pname] = $val;
                else if ($key == "map") $mapColumnArray1[$pname] = array($val);
            }
        } else {
            $pname = $psettings;
        }
        //if the caption were not found just assign caption as the $pname itself
        if (!isset($captionColumnArray1[$pname])) $captionColumnArray1[$pname] = __object__::property2Caption($pname);
        //--End --settings
        $col = Registry::property2column($classname, $pname);
        if (!is_null($col)) $collistArray1[sizeof($collistArray1)] = $col;
    }
    //You must include primaryColumn and valueColumns 
    $primaryColumn = Registry::getId0Columnname($classname);
    if (is_null($primaryColumn)) throw new Exception("Class : [ $classname ] has no a primary key");
    $valueColumns = Registry::getValueColumnnames($classnames);
    if (!is_null($valueColumns)) {
        foreach ($valueColumns as $vcolname) {
            if (!in_array($vcolname, $collistArray1)) $collistArray1[sizeof($collistArray1)] = $vcolname;
        }
    }
    if (!in_array($primaryColumn, $collistArray1)) $collistArray1[sizeof($collistArray1)] = $primaryColumn;
    $collist = implode(",", $collistArray1);
    $query = "SELECT $collist FROM $tablename WHERE $wherelist";
    $records = __data__::getSelectedRecords($conn, $query, false);

    if (is_null($records)) throw new Exception("Could not get record list");
    //Working Column for display
    $workingDisplayArray1 = is_null($valueColumns) ? $dbcolumnArray1 : $valueColumns;
    if (is_null($workingDisplayArray1)) throw new Exception("Could not get display array options");
    //Now shape autocompletedata
    $autocompletedata = __data__::convertRawSQLDataToTabularData($conn, $classname, $records['column'], $mapColumnArray1);
    //Now rewrite __id__ and __name__
    foreach ($autocompletedata as $index => $pblock1) {
        $dispData = "";
        $dispCount = 0;
        foreach ($pblock1 as $pname => $val) {
            $col = Registry::property2column($classname, $pname);
            if (is_null($col)) continue;
            //displayed values
            if (in_array($col, $workingDisplayArray1)) {
                if ($dispCount == 0) $dispData = $val;
                else $dispData .= ", $val";
                $dispCount++;
            }
        }
        $autocompletedata[$index]['__name__'] = $dispData;
        $autocompletedata[$index]['__id__'] = $autocompletedata[$index]['id'];
    }
} catch (Exception $e) {
    $conn = null;
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$conn = null;
//Now perform packaging 
$payload = array(
    "code" => 0,
    "message" => "server_ok",
    "rows" => $autocompletedata,
    "captions" => $captionColumnArray1,
    "container" => $targetContainer,
    "listEmptyMessage" => ( __data__::$__LIST_EMPTY_MESSAGE )
);
echo json_encode($payload);
