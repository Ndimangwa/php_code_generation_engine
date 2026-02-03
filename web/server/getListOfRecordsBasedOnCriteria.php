<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
require_once('../common/__autoload__.php');
if (! isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'message' => 'Session Expired, Kindly login again or refresh the page')));
if (! isset($_REQUEST['__search_input_text__'])) die(json_encode(array('code' => 2, 'message' => 'Search Text Not Found')));
if (! isset($_REQUEST['__classname__'])) die(json_encode(array('code' => 3, 'message' => 'Class is not defined')));
if (! isset($_REQUEST['__bound_columns__'])) die(json_encode(array('code' => 4, 'message' => 'Bound Columns Not defined')));
$classname = $_REQUEST['__classname__'];
$boundColumnArray1 = $_REQUEST['__bound_columns__'];
$searchInputText = $_REQUEST['__search_input_text__'];
$config1 = new ConfigurationData("../config.php");
$cname = strtolower($classname."_search");
if (! Authorize::isAllowable($config1, $cname, "normal", "donotsetlog", null, null)) die(json_encode(array('code' => 3, 'message' => "[ $cname ] : Not Allowed to perform action")));
$records = null;
$conn = null;
try {
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $tablename = Registry::getTablename($classname);
    if (is_null($tablename)) die(json_encode(array('code' => 1, 'message' => 'Returned Null Table')));
    $dbcolumnArray1 = array();
    foreach ($boundColumnArray1 as $pname)  {
        $col = Registry::property2Column($classname, $pname);
        if (! is_null($col)) $dbcolumnArray1[sizeof($dbcolumnArray1)] = $col; 
    }
    if (sizeof($dbcolumnArray1) == 0) die(json_encode(array('code' => 1, 'message' => 'Could Not Extract DB Columns')));
    $collist = implode(",", $dbcolumnArray1);
    $wherelist = null;
    foreach ($dbcolumnArray1 as $col)   {
        $dt = "( $col LIKE '%$searchInputText%' )";
        if (is_null($wherelist)) $wherelist = $dt;
        else $wherelist .= " OR $dt";
    }
    $query = "SELECT $collist FROM $tablename WHERE $wherelist";
    $records = __data__::getSelectedRecords($conn, $query, false);
} catch (Exception $e)  {
    $conn = null; die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$conn = null;
if (is_null($records)) die(json_encode(array('code' => 2, 'message' => 'Could not get record list')));
$autocompletedata = array();
foreach ($records['column'] as $record1)    {
    $index = sizeof($autocompletedata);
    $autocompletedata[$index] = array();
    $dt = null;
    $lcount = 0;
    foreach ($record1 as $col => $val)  {
        if ($lcount == 0)    {
            $autocompletedata[$index]['value'] = $val;
            $dt = $val;
        } else {
            $dt .= ", $val";
        }
        $lcount++;
    }
    $autocompletedata[$index]['label'] = $dt;
}
echo json_encode(array('code' => 0, 'message' => 'server-ok', 'rows' => $autocompletedata));
?>
