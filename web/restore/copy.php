<?php 
require "../vendor/autoload.php";
require_once("../common/__autoload__.php");
require_once("../sys/__autoload__.php");
$contextFiles = array("Login", "JobTitle", "Group");
$config1 = new ConfigurationData("../config.php");
$baseFolder = "data";
try {
    $host = $config1->getHostname();
    $dbname = $config1->getDatabase();
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
    foreach ($contextFiles as $contextFile) {
        $filename = join(DIRECTORY_SEPARATOR, [ $baseFolder , "sample.csv" ]);
        $datafile1 = new DataFile($filename);
        
        $tablename = Registry::getTablename($contextFile);
        $idcol = Registry::getId0Columnname($contextFile);
        $query = "SELECT $idcol as id, context FROM $tablename";
        $records = __data__::getSelectedRecords($conn, $query, false);
        foreach ($records['column'] as $record1)    {
            
        }

        $filename = join(DIRECTORY_SEPARATOR, [ $baseFolder , ( $contextFile . ".csv" ) ]);
        $datafile1->setFilename($filename);
        $datafile1->write();
    }
    $conn = null;
} catch (Exception $e)  {
    die($e->getMessage());
}
?>