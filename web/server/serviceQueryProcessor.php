<?php
//Standard; return [code, query, message]
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('../common/__autoload__.php');
$conn = null;
$profile1 = null;
$login1 = null;
$config1 = new ConfigurationData('../config.php');
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
$systemTime1 = new DateAndTime("0000:00:00:00:00:00"); //Default
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($config1->getDatabase(), __data__::$__PROFILE_INIT_ID, $conn);
    date_default_timezone_set($profile1->getPHPTimezone()->getZoneName());
    $systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));    
    $login1 = new Login($config1->getDatabase(), $_SESSION['login'][0]['id'], $conn);
} catch (PDOException $e) {
    die(json_encode(array('code' => 5, 'query' => 'Init', 'message' => $e->getMessage())));
}
$conn = null; //You need to close connection while calling Authorization::isAllowable
if (!isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'query' => 'Error', 'message' => 'Kindy log-in first')));
if (!isset($_POST['__query__'])) die(json_encode(array('code' => 2, 'query' => 'Error', 'message' => 'Query Parameter was not specified')));
if (!isset($_POST['__classname__'])) die(json_encode(array('code' => 3, 'query' => 'Error', 'message' => 'Classname was not specified')));
$query = $_POST['__query__'];
$classname = $_POST['__classname__'];
//We need to work for context position $cname 
//$actionLookup = array('select' => 'read', 'insert' => 'create', 'update' => 'update', 'delete' => 'delete');
//if (! in_array($query, $actionLookup)) die(json_encode(array('code' => 4, 'query' => $query, 'message' => '[ $query ] => Could not decode the query operation')));
//$cname = strtolower($classname."_".$actionLookup[$query]); //We need to allow custom_cname $_POST['custom_context_name']//ContextPosition.cName
$cname = strtolower($classname . "_$query");
if (isset($_POST['__custom_context_name__'])) $cname = $_POST['__custom_context_name__'];
try {
SystemRules::evaluate($conn, $profile1, $login1, $classname, $query, $cname, $_POST);
} catch (Exception $e)  {
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
if (! Authorize::isAllowable($config1, $cname, "normal", "donotsetlog", null, null)) die(json_encode(array('code' => 8, 'query' => $query, 'message' => '[ $cname ] => You are not authorized to carry this operation!!')));
$classid = null;
if (isset($_POST['__id__'])) $classid = $_POST['__id__'];
$successfulMessage = "Successful Operation";
if (isset($_POST['__modal_success_message__'])) $successfulMessage = $_POST['__modal_success_message__'];
$logMessage = "Default Message";
if (isset($_POST['__log_message__'])) $logMessage = $_POST['__log_message__'];
$queryArray1 = array(
    "code" => 77,
    "message" => "Nothing Happened, the original Payload were untouched"
);
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
} catch (PDOException $e) {
    die(json_encode(array('code' => 5, 'query' => $query, 'message' => $e->getMessage())));
}
//We need to try to fetch $affectedObject 
$affectedObject = "";
$colWithVal = Registry::getValue0Columnname($classname);
if (! is_null($colWithVal) && $colWithVal != "")    {
    $pname = Registry::column2Property($classname, $colWithVal);
    if (! is_null($pname) && isset($_POST[$pname])) {
        $affectedObject = "[ ".$_POST[$pname]." ] ";
    }
}
switch ($query) {
    case 'read':

        break;
    case 'create':
        //$queryArray1 = ServiceToolbox::createQuery($conn, $classname, $_POST);
        try {
            $id = __data__::insert($conn, $classname, $_POST, true, Constant::$default_select_empty_value);
            $logMessage = $affectedObject."Created Successful";
            $queryArray1 = array(
                "id" => $id,
                "timestamp" => $systemTime1->getTimestamp(),
                "code" => 0,
                "message" => $logMessage,
                "classname" => $classname,
                "query" => $query
            );
            SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $logMessage, true);
        } catch (Exception $e)  {
            $message = $e->getMessage();
            die(json_encode(array(
                "code" => 1,
                "message" => "[ $classname => $query ] : [ $message ]"
            )));
        }
        break;
    case 'update':
        //$queryArray1 = ServiceToolbox::updateQuery($conn, $classname, $classid, $_POST);
        try {
            if (is_null($classid)) throw new Exception("Could not get a classid");
            $object1 = Registry::getObjectReference("Hello", $conn, $classname, $classid);
            if (is_null($object1)) throw new Exception("Could not get an Object Reference");
            $object1->updateList($_POST, Constant::$default_select_empty_value)->update(true);
            $logMessage = $affectedObject."Updated Successful";
            $queryArray1 = array(
                "id" => ($object1->getId0()),
                "timestamp" => $systemTime1->getTimestamp(),
                "code" => 0,
                "message" => $logMessage,
                "classname" => $classname,
                "query" => $query
            );
            SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $logMessage, true);
        } catch (Exception $e)  {
            $message = $e->getMessage();
            die(json_encode(array(
                "code" => 1,
                "message" => "[ $classname => $query ] : [ $message ]"
            )));
        }
        break;
    case 'delete':
        //$queryArray1 = ServiceToolbox::deleteQuery($conn, $classname, $classid, $_POST);
        try {
            if (is_null($classid)) throw new Exception("Could not get a classid");
            $object1 = Registry::getObjectReference("Hello", $conn, $classname, $classid);
            if (is_null($object1)) throw new Exception("Could not get an Object Reference");
            $object1->delete(true);
            $logMessage = $affectedObject."Deleted Successful";
            $queryArray1 = array(
                "id" => ($object1->getId0()),
                "timestamp" => $systemTime1->getTimestamp(),
                "code" => 0,
                "message" => $logMessage,
                "classname" => $classname,
                "query" => $query
            );
            SystemLogs::addLog2($conn, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $logMessage, true);
        } catch (Exception $e)  {
            $message = $e->getMessage();
            die(json_encode(array(
                "code" => 1,
                "message" => "[ $classname => $query ] : [ $message ]"
            )));
        }
        break;
    default:
        $conn = null;
        die(json_encode(array('code' => 4, 'query' => $query, 'message' => "Query [ $query ] could not be interpreted")));
}
$conn = null; //You need to close connection prior configuring system-logs
if (intval($queryArray1['code']) !== 0) die(json_encode(array('code' => 7, 'query' => $query, 'message' => $queryArray1['message'])));
echo json_encode($queryArray1);
