<?php 
//Standard; return [code,  message]
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
if (! isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'message' => 'Kindy log-in first')));
if (! isset($_POST['__command__'])) die(json_encode(array('code' => 2, 'message' => 'Command not Specified')));
if (! isset($_POST['__id__'])) die(json_encode(array('code' => 3, 'message' => 'The Class ID is not set')));
if (! isset($_POST['__class__'])) die(json_encode(array('code' => 4, 'message' => 'The Target Class is not Specified')));
$command = $_POST['__command__'];
$classname = $_POST['__class__'];
$login1 = null; $profile1 = null;
$cname = "firewall_update";
$config1 = new ConfigurationData('../config.php');
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
$conn = null;
try {
    if (! in_array($classname, array('Login', 'JobTitle', 'Group'))) throw new Exception("[ $classname ] not in a valid range of Firewall Target");
    $conn  = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $login1 = new Login($dbname, $_SESSION['login'][0]['id'], $conn);
    $profile1 = new Profile($config1->getDatabase(), __data__::$__PROFILE_INIT_ID, $conn);
    if (! $login1->isRoot()) throw new Exception("[ $cname ] operation, is for Root User Only");
    $object1 = Registry::getObjectReference("Ndimangwa", $conn, $classname, $_POST['__id__']);
    if (is_null($object1)) throw new Exception("Could Not Recreate Object Reference for [ $classname ]");
    $objectName = $object1->getName0();
    //Perform According to Command
    $context = $object1->getContext();
    switch ($command)   {
        case ContextPosition::$__ALLOW_ALL:
            $context = Authorize::buildAllContextStringTo($config1->getDatabase(), $conn, $context, ContextPosition::$__ALLOW);
            break;
        case ContextPosition::$__DENY_ALL:
            $context = Authorize::buildAllContextStringTo($config1->getDatabase(), $conn, $context, ContextPosition::$__DENY);
            break;
        case ContextPosition::$__DONOTCARE_ALL:
            $context = Authorize::buildAllContextStringTo($config1->getDatabase(), $conn, $context, ContextPosition::$__DONOTCARE);
            break;
        case ContextPosition::$__CUSTOMIZE:
            $context = Authorize::buildTheEntireContextString($config1->getDatabase(), $conn, $context, $_POST['position']);
            break;
        default:
            throw new Exception("Could not decode the command [ $command ]");
    }
    $object1->setContext($context);
    $object1->update();
} catch (Exception $e)  {
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$conn = null;
date_default_timezone_set($profile1->getPHPTimezone()->getZoneName());
$systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));
try {
    $logMessage = "Firewall Updated for $classname > $objectName";  
    
    SystemLogs::addLog($config1, $systemTime1->getTimestamp(), $login1->getLoginName(), $cname, $logMessage);
} catch (Exception $e)  {
    $logMessage = "UPDATED SUCCESSFUL. However there were Errors ".$e->getMessage();
    die(json_encode(array('code' => 1, 'message' => $logMessage)));
}
$systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));
echo json_encode(array('code' => 0, 'message' => 'Successful'));
?>
