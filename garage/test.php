<?php 
require_once("../html/vendor/autoload.php");
require_once("../html/sys/__autoload__.php");
$config1 = new ConfigurationData("../html/config.php");
$conn = null;
try {
	$host = $config1->getHostname();
	$dbname = $config1->getDatabase();
	$conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
	//Now working 

	$admissionQueue1 = new PatientAdmissionQueue("Delta", 1, $conn);
	$list1 = __data__::getObjectData($admissionQueue1, array('timeOfCreation', 'patient', 'listOfServices'));
	var_dump($list1);
} catch (Exception $e)	{
	die($e->getMessage());
}
$conn = null;
?>
