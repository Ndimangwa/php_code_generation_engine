<?php 
//Standard; return [code, query, message]
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require "../vendor/autoload.php";
require_once('../sys/__autoload__.php');
require_once('__service_toolbox__.php');
$config1 = new ConfigurationData('../config.php');
$host = $config1->getHostname();
$dbname = $config1->getDatabase();
if (! (isset($_POST['classname']) && isset($_POST['columns']))) die(json_encode(array('code' => 1, 'message' => 'Important Parameters are not set')));
$classname = $_POST['classname'];
$columnList = $_POST['columns'];
$conn = null;
$dataArray1 = array();
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    //Need to verify if all columns really belongs to our class
    //$dbColArray1 = array();
    //$classColArray1 = array(); //Only those available
    $dataArray1['code'] = 0;
    $dataArray1['message'] = "Successful";
    $dataArray1['records'] = array();
    $tArray1 = null;
    $dbColumnList = Registry::getIdColumnnames($classname);
    foreach ($columnList as $pname) {
        //We are not allowing Any of the primary Column to Appear in Search
        $dbcolumn = Registry::property2column($classname, $pname);
        if (! is_null($dbcolumn) && ! is_null($dbColumnList) && in_array($dbcolumn, $dbColumnList)) continue; //Exclude primaries
        $colname = Registry::property2column($classname, $pname);
        $type = Registry::getColumnType($classname, $pname);
        if (! (is_null($colname) || is_null($type))) {
           //$recordsize = sizeof($dataArray1['records']);
           $tArray1 = array();
           $tArray1['pname'] = $pname;
           $tArray1['type'] = $type;
           $tArray1['error-level'] = false;
           if (in_array($type, array('boolean')))    {
                if (! isset($tArray1['values'])) $tArray1['values'] = array();
                $valuesize = sizeof($tArray1['values']);
                $tArray1['values'][$valuesize] = array();
                $tArray1['values'][$valuesize]['caption'] = "True";
                $tArray1['values'][$valuesize]['value'] = 1;
                $valuesize = sizeof($tArray1['values']);
                $tArray1['values'][$valuesize] = array();
                $tArray1['values'][$valuesize]['caption'] = "False";
                $tArray1['values'][$valuesize]['value'] = 0;
           } else if (in_array($type, array('object'))) {
                $tArray1['error-level'] = true;
                $refclass = Registry::getReferenceClass($classname, $pname);
                if (is_null($refclass)) continue;
                $dtArray1 = Registry::loadAllData($conn, $refclass);
                if (is_null($dtArray1)) continue;
                if (! isset($tArray1['values'])) $tArray1['values'] = array();
                foreach ($dtArray1 as $dtBlock) {
                    $valuesize = sizeof($tArray1['values']);
                    $tArray1['values'][$valuesize] = array();
                    $tArray1['values'][$valuesize]['caption'] = $dtBlock['__name__'];
                    $tArray1['values'][$valuesize]['value'] = $dtBlock['__id__'];
                } 
                $tArray1['error-level'] = false;             
           }
           if (! $tArray1['error-level'])   {
               $recordsize = sizeof($dataArray1['records']);
               $dataArray1['records'][$recordsize] = $tArray1;
           }
        }
    }
} catch (Exception $e)  {
    die(json_encode(array('code' => 1, 'message' => $e->getMessage())));
}
$conn = null;
echo json_encode($dataArray1);
?>
