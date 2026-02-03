<?php 
if (session_status() == PHP_SESSION_NONE)   {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
if (! (isset($_POST['username']) || isset($_POST['password']))) die(json_encode(array("code" => 1, "message" => "Username or Password not set ")));
//Perform login
$username = trim($_POST['username']);
$password = sha1($_POST['password']);
$conn = null; $login1 = null; $config1 = null; $profile1 = null;
try {
    $config1 = new ConfigurationData("../config.php");
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $query = SimpleQueryBuilder::buildSelect(array('_login'), array('loginId', 'loginName', 'email', 'password'), array("__or__" => array(array("loginName" => $username), array("email" => $username)), "password" => $password));
    $jresult1 = SQLEngine::execute($query, $conn);
	$jArray1 = json_decode($jresult1, true);
	if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
	if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
    $resultSet = $jArray1['rows'][0];
    //Double check to ensure no SQL-Injections etc
    if (! ($resultSet['loginName'] == $username || $resultSet['email'] == $username) && ($resultSet['password'] == $password)) throw new Exception("The system could not proceed to login, due to security concerns");
    //Build Login object only-if you have successful passed logged in test
    $profile1 = new Profile($dbname, __data__::$__PROFILE_INIT_ID, $conn);
    $login1 = new Login($dbname, $resultSet['loginId'], $conn);
} catch (PDOException $e)   {
    die(json_encode(array("code" => 2, "message" => $e->getMessage())));
} catch (Exception $e)  {
    die(json_encode(array("code" => 3, "message" => $e->getMessage())));
}
$conn = null;
if (is_null($login1)) die(json_encode(array("code" => 4, "message" => "Could not Logged - In")));
//Now logIn so to satisfy the Authorize class
$_SESSION['login'] = array();
$_SESSION['login'][0] = array();
$_SESSION['login'][0]['id'] = $login1->getLoginId();
if (! Authorize::isAllowable($config1, "login", "normal", "donotsetlog", null, null))  {
    //Not Allowed At-All
    $_SESSION = array();
    session_destroy();
    die(json_encode(array("code" => 5, "message" => "You have reached firewall, kindly consult the system administrator")));
}
//Working with Logs
date_default_timezone_set($profile1->getPHPTimezone()->getZoneName());
$systemTime1 = new DateAndTime(date("Y:m:d:H:i:s"));
try {
    SystemLogs::addLog($config1, $systemTime1->getTimestamp(), $login1->getLoginName(), "login", "Logged-In into your account");
} catch (Exception $e)  {
    $_SESSION = array();
    session_destroy();
    $message = "System Logs Failed to register : [ ".$e->getMessage()." ]";
    die(json_encode(array("code" => 5, "message" => $message)));
}
echo json_encode(array("code" => 0, "message" => "Successful"));
?>
