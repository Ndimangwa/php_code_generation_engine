<?php 
//Standard; return [code, query, message]
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
$conn = null;
$profile1 = null; $login1 = null;
$config1 = new ConfigurationData('../config.php');
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
try {   
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($config1->getDatabase(), __data__::$__PROFILE_INIT_ID, $conn);
    $login1 = new Login($config1->getDatabase(), $_SESSION['login'][0]['id'], $conn);
} catch (PDOException $e)   {
    die(json_encode(array('code' => 5, 'query' => 'Init', 'message' => $e->getMessage())));
}
$conn = null; //You need to close connection while calling Authorization::isAllowable

if (! isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'query' => 'Error', 'message' => 'Kindy log-in first')));
if (! isset($_POST['__query__'])) die(json_encode(array('code' => 2, 'query' => 'Error', 'message' => 'Query Parameter was not specified')));
if (! isset($_POST['__classname__'])) die(json_encode(array('code' => 3, 'query' => 'Error', 'message' => 'Classname was not specified')));
$query = $_POST['__query__'];
$classname = $_POST['__classname__'];
//You need to make sure the account we are dealing with is the one which is logged-in
if (! (isset($_POST['__id__']) && $login1->getLoginId() == $_POST['__id__'])) die(json_encode(array('code' => 4, 'query' => $query, 'message' => 'Account Manipulation, this account should be yours or some parameters are missing')));
if (! (isset($_POST['oldPassword']) && isset($_POST['newPassword']))) die(json_encode(array('code' => 5, 'query' => $query, 'message' => 'Either Old Password or New Password is not set')));
//We need to work for context position $cname 
$actionLookup = array('select' => 'read', 'insert' => 'create', 'update' => 'update', 'delete' => 'delete');
if (! in_array($query, $actionLookup)) die(json_encode(array('code' => 4, 'query' => $query, 'message' => '[ $query ] => Could not decode the query operation')));
$cname = strtolower($classname."_".$actionLookup[$query]); //ContextPosition.cName
if (isset($_POST['__custom_context_name__'])) $cname = $_POST['__custom_context_name__'];
if (! ($login1->isRoot() || Authorize::isAllowable($config1, $cname, "normal", "donotsetlog", null, null))) die(json_encode(array('code' => 8, 'query' => $query, 'message' => '[ $cname ] => You are not authorized to carry this operation!!')));
$classid = null; if (isset($_POST['__id__'])) $classid = $_POST['__id__'];
$successfulMessage = "Successful Operation"; if (isset($_POST['__modal_success_message__'])) $successfulMessage = $_POST['__modal_success_message__'];
$logMessage = "Default Message";
$queryArray1 = null;
try {   
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
} catch (PDOException $e)   {
    die(json_encode(array('code' => 5, 'query' => $query, 'message' => $e->getMessage())));
}
        
//You need to pull the oldPasswordRecord
$oldPassword = sha1($_POST['oldPassword']);
$queryArray1 = ServiceToolbox::selectQuery($conn, $classname, $classid, array('password' => $_POST['oldPassword']), array('password' => $oldPassword));
if (is_null($queryArray1)) die(json_encode(array('code' => 6, 'query' => $query, 'message' => 'Could not get data from the database')));
if (intval($queryArray1['code']) !== 0 ) die(json_encode(array('code' => 7, 'query' => $query, 'message' => $queryArray1['message'])));
if (intval($queryArray1['count']) !== 1) die(json_encode(array('code' => 8, 'query' => $query, 'message' => 'You have supplied an incorrect password')));
//You need to update to the New Password
$newPassword = sha1($_POST['newPassword']);
$queryArray1 = ServiceToolbox::updateQuery($conn, $classname, $classid, array('password' => $newPassword));
$conn = null; //You need to close connection prior configuring system-logs
if (is_null($queryArray1)) die(json_encode(array('code' => 6, 'query' => $query, 'message' => 'Could not get data from the database')));
if (intval($queryArray1['code']) !== 0 ) die(json_encode(array('code' => 7, 'query' => $query, 'message' => $queryArray1['message'])));
//Activate Logs
//Working with Logs
if (in_array($query, array('insert', 'update', 'delete')))  {
    date_default_timezone_set($profile1->getPHPTimezone()->getZoneName());
    $systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));
    try {
        if (isset($_POST['__log_message__'])) $logMessage = $_POST['__log_message__'];
        SystemLogs::addLog($config1, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $logMessage);
    } catch (Exception $e)  {
        $message = "System Logs Failed to register : [ ".$e->getMessage()." ]";
        die(json_encode(array("code" => 5, "query" => $query, "message" => $message)));
    }    
}
echo json_encode(array('code' => 0, 'query' => $query, 'message' => $successfulMessage));
?>
