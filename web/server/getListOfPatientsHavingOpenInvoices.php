<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
require_once('../common/__autoload__.php');
if (! isset($_SESSION['login'][0]['id'])) die(json_encode(array('code' => 1, 'message' => 'Session Expired, Kindly login again or refresh the page')));
if (! isset($_POST['patientName'])) die(json_encode(array('code' => 2, 'message' => 'Search Details were not submitted', 'data' => $_POST)));
$patientName = $_POST['patientName'];
$config1 = new ConfigurationData("../config.php");
if (! Authorize::isAllowable($config1, "patient_search", "normal", "donotsetlog", null, null)) die(json_encode(array('code' => 3, 'message' => 'Not Allowed to search patients')));
$records = null;
$conn = null;
try {
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $query = "SELECT surname, otherNames FROM _patient_invoice as pi, _patient as p WHERE (pi.patientId = p.patientId) AND (pi.closed = 0) AND (pi.invoiceNumber LIKE '%$patientName%' OR p.surname LIKE '%$patientName%' OR p.otherNames LIKE '%$patientName%')"; 
    $records = __data__::getSelectedRecords($conn, $query, false);
} catch (Exception $e)  {
    $conn = null; die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$conn = null;
if (is_null($records)) die(json_encode(array('code' => 2, 'message' => 'Could not get record list')));
echo json_encode(array('code' => 0, 'message' => 'server-ok', 'rows' => $records['column'], 'query' => $query));
?>
