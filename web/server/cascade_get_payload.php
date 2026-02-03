<?php 
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
require_once('../common/__autoload__.php');
if (! isset($_POST['cascade-select'])) die(json_encode(array("code" => 1, "message" => "data not set properly")));
$sourceClassname = $_POST['source']['class'];
$sourceId = $_POST['source']['id'];
$targetClassname = $_POST['target']['class'];
$listOfForeignKeys = $_POST['target']['foreign-keys'];
if (sizeof($listOfForeignKeys) != 1) die(json_encode(array("code" => 1, "message" => "Empty or Multiple Foreign keys")));
//Working with listOfColumns 
$listOfColumns = null; //db-columns
$listOfColumnPlaceholder = array();
$format = null;
if (isset($_POST['format']))    {
    $format = $_POST['format'];
    $listOfColumns = __data__::getListOfColumns($targetClassname, $_POST['format'], $listOfColumnPlaceholder);
} else {
    $listOfColumns = Registry::getValueColumnnames($targetClassname);
}
if (sizeof($listOfColumnPlaceholder) == 0) $listOfColumnPlaceholder = null;
if (is_null($listOfColumns)) die(json_encode(array("code" => 2, "message" => "List of Columns could not be generated")));
//targetPrimaryKey
$idcolumn = Registry::getId0Columnname($targetClassname);
$listOfColumns[sizeof($listOfColumns)] = $idcolumn;
//Now build Query
$dbcollist = implode(",", $listOfColumns);
$foreigncolumn = Registry::property2column($targetClassname, $listOfForeignKeys[0]);
if (is_null($foreigncolumn)) die(json_encode(array("code" => 3, "message" => "Could not get Foreign key")));
$tablename = Registry::getTablename($targetClassname);
if (is_null($tablename)) die(json_encode(array("code" => 3, "message" => "Could not get tablename")));
$query = "SELECT $dbcollist FROM $tablename WHERE $foreigncolumn = '$sourceId'";
$listOfOptions = array();
try {
    $config1 = new ConfigurationData("../config.php");
    $dbname = $config1->getDatabase();
    $host = $config1->getHostname();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    $records = __data__::getSelectedRecords($conn, $query, false);
    foreach ($records['column'] as $record1)    {
        $myformat = is_null($format) ? null : (is_null($listOfColumnPlaceholder) ? null : $format);
        $index = sizeof($listOfOptions);
        $listOfOptions[$index] = array();
        foreach ($listOfColumns as $col)    {
            $val = $record1[$col];
            if ($col == $idcolumn)  {
                $listOfOptions[$index]['value'] = $val;
            } else {
                if (! is_null($format) && ! is_null($listOfColumnPlaceholder)) {
                    $plc = $listOfColumnPlaceholder[$col];
                    $myformat = str_replace($plc, $val, $myformat);
                } else {
                    if (is_null($myformat)) $myformat = $val;
                    else $myformat .= ", $val";
                }
            }
        }
        $listOfOptions[$index]['label'] = $myformat;
    }
} catch (Exception $e)  {
    die(json_encode(array("code" => 3, "message" => ($e->getMessage()))));
}
echo json_encode(array("code" => 0, "message" => "server-ok", "options" => $listOfOptions));
?>