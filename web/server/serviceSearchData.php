<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
require_once('../common/__autoload__.php');
function getLikeOperatorArray($dataArray1) {
    $tArray1 = array();
    foreach ($dataArray1 as $dt)    {
        $index = sizeof($tArray1);
        $tArray1[$index] = array();
        $colname = key($dt);
        $value = $tArray1[$colname];
        $tArray1[$index][JSON2SQL::$__OP_LIKE] = array();
        $tArray1[$index][JSON2SQL::$__OP_LIKE][$colname] = "%".$value."%";
    }
    return $tArray1;
}
if (! isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'message' => 'Session Expired, Kindly login again or refresh the page')));
if (! isset($_POST['__classname__'])) die(json_encode(array('code' => 1, 'message' => 'Class is not specified')));
if (! isset($_POST['__search_input__'])) die(json_encode(array('code' => 2, 'message' => 'Search Input is not specified')));
if (! isset($_POST['__bound_columns__'])) die(json_encode(array('code' => 3, 'message' => 'Bound Columns not specified')));
if (! isset($_POST['__display_columns__'])) die(json_encode(array('code' => 4, 'message' => 'Display Columns not specified')));
if (! isset($_POST['__search_input_text__'])) die(json_encode(array('code' => 5, 'message' => 'Search Input Text is not specified')));
//You need to check rule $classname_search
$classname = $_POST['__classname__'];
$searchInput = $_POST['__search_input__']; //form or text
$searchInputText = $_POST['__search_input_text__']; //Actual value in case text
$boundColumnArray1 = $_POST['__bound_columns__']; //Criteria to search
$config1 = new ConfigurationData("../config.php");
//Save policies as they do not need Active Connection
$allow_details = null;
$allow_create = null;
$allow_update = null;
$allow_delete = null;
try {
    $allow_details = Authorize::isAllowable($config1, $classname."_read", "normal", "donotsetlog", null, null);
    $allow_create = Authorize::isAllowable($config1, $classname."_create", "normal", "donotsetlog", null, null);
    $allow_update = Authorize::isAllowable($config1, $classname."_update", "normal", "donotsetlog", null, null);
    $allow_delete = Authorize::isAllowable($config1, $classname."_delete", "normal", "donotsetlog", null, null);
} catch (Exception $e)  {  
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$conn = null;
$profile1 = null;
$login1 = null;
try {
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($dbname, __data__::$__PROFILE_INIT_ID, $conn);
    $login1 = new Login($dbname, $_SESSION['login'][0]['id'], $conn);
} catch (Exception $e)  {
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$tabularData1 = array();
$tabularData1['code'] = 0;
$tabularData1['message'] = "OK";
$tabularData1['policy'] = array();
$tabularData1['policy']['details'] = $allow_details;
$tabularData1['policy']['create'] = $allow_create;
$tabularData1['policy']['update'] = $allow_update;
$tabularData1['policy']['delete'] = $allow_delete;
//Now Working if external-link
if (isset($_POST['__external_link__']) && isset($_POST['__external_link__']['href']) && isset($_POST['__external_link__']['caption'])) {
    $tabularData1['elink'] = array(
        'href' => ( $_POST['__external_link__']['href'] ),
        'caption' => ( $_POST['__external_link__']['caption'] )
    );
}
$tabularData1['maximumRecordsPerPage'] = $profile1->getMaximumNumberOfDisplayedRowsPerPage();
//You need to verify displayColumns are really the ones you can search 
$searchableColumns = Registry::getSearchableColumns($classname);
if (is_null($searchableColumns)) die(json_encode(array('code' => 6, 'message' => 'Could not find valid search columns')));
$displayColumnArray1 = array();
foreach ($_POST['__display_columns__'] as $colname) {
    if (in_array($colname, $searchableColumns)) $displayColumnArray1[sizeof($displayColumnArray1)] = $colname;
}
if (sizeof($displayColumnArray1) == 0) die(json_encode(array('code' => 7, 'message' => 'Submitted Search Columns could not be validated')));
//Building where clause
$tArray1 = array();
foreach ($boundColumnArray1 as $key => $value)  {
  $value = trim($value);
  if ($searchInput == "text")   {
    $pname = $value;
    $value = $searchInputText;
  }  else if ($searchInput == "form")   {
    //Filter if fields are empty
    if (in_array($value, array("", Constant::$default_select_empty_value))) continue;
    $pname = $key;
  } else continue;
  //Now we have [$pname , $value]
  //We need colname from $pname 
  $colname = Registry::property2column($classname, $pname);
  if (is_null($colname)) continue;
  $listsize = sizeof($tArray1);
  $tArray1[$listsize] = array();
  $tArray1[$listsize]["$colname"] = $value;
}
$whereArray1 = null;
if (sizeof($tArray1) > 0)   {
    $whereArray1 = array();
    if ($searchInput == "text") {
        $whereArray1[JSON2SQL::$__OP_OR] = $tArray1;
    } else if ($searchInput == "form") {
        //Use like
        //$whereArray1["\$and"] = getLikeOperatorArray($tArray1);
        $whereArray1[JSON2SQL::$__OP_AND] = $tArray1;
    } else $whereArray1 = null;
}
//Record Arrays 
$tArray1 = array();
foreach ($displayColumnArray1 as $pname)    {
    $colname = Registry::property2column($classname, $pname);
    if (is_null($colname)) continue;
    $tArray1[sizeof($tArray1)]  = $colname;
}
$tArray1[sizeof($tArray1)] = Registry::getId0Columnname($classname);
$displayColumnArray1[sizeof($displayColumnArray1)] = "id";
$tabularData1['headers'] = $displayColumnArray1;
try {
    $jresults1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
        array(Registry::getTablename($classname)),
        $tArray1,
        $whereArray1
    ),$conn);
    //We need to update cols back to property
    $jArray1 = json_decode($jresults1, true);
    if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
    if ($jArray1['count'] == 0) throw new Exception('Results Could not be matched');
    //$tabularData1['rows'] = column2Property($classname, $jArray1['rows']);
    $tabularData1['rows'] = __data__::convertRawSQLDataToTabularData($conn, $classname, $jArray1['rows']);
    $tabularData1['count'] = sizeof($tabularData1['rows']);
} catch (Exception $e)  {
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$tabularData1['boundColumnArray0001'] = $boundColumnArray1;
$conn = null;
echo json_encode($tabularData1);
?>
