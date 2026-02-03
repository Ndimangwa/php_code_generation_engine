<?php
//Standard; return [code, query, message]
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
if (!isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'message' => 'Kindy log-in first')));
if (! isset($_POST['__lastresort__'])) die(json_encode(array('code' => 2, 'message' => 'Arguments were not set properly')));
$lastresort = $_POST['__lastresort__'];
$conn = null;
$profile1 = null;
$login1 = null;
$config1 = new ConfigurationData('../config.php');
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $profile1 = new Profile($config1->getDatabase(), __data__::$__PROFILE_INIT_ID, $conn);
    $login1 = new Login($config1->getDatabase(), $_SESSION['login'][0]['id'], $conn);
    if (! $login1->isRoot()) throw new Exception('This operation is for Root User Accounts Only');
    $jresults1 = SQLEngine::execute(SimpleQueryBuilder::buildUpdate(
        '_contextManager',
        array('defaultXValue' => $lastresort),
        array("\$not" => array('defaultXValue' => $lastresort))
    ),$conn);
    if (is_null($jresults1)) throw new Exception('Query Could not return feedback');
    $jArray1 = json_decode($jresults1, true);
    if (is_null($jArray1)) throw new Exception('Malformed Query Results');
    if ($jArray1['code'] != 0) throw new Exception($jArray1['message']);
} catch (Exception $e) {
    die(json_encode(array('code' => 5, 'message' => $e->getMessage())));
}
$conn = null; //You need to close connection while calling Authorization::isAllowable
$cname = "system_lastresort_donotcare";
//Working with Logs
date_default_timezone_set($profile1->getPHPTimezone()->getZoneName());
$systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));
try {
    $logMessage = "Last Resort for Do Not Care Updated";
    SystemLogs::addLog($config1, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $logMessage);
} catch (Exception $e) {
    $message = "System Logs Failed to register : [ " . $e->getMessage() . " ]";
    die(json_encode(array("code" => 5, "message" => $message)));
}
echo json_encode(array('code' => 0, 'message' => "You have successful Updated the Last Resort of Do Not Care"));
