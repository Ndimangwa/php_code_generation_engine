<?php 
require_once("../html/vendor/autoload.php");
require_once("../html/sys/__autoload__.php");
$datafile1 = new DataFile("tservices.csv");
$colname = "serviceName";
foreach ($datafile1->getDataLines() as $dataLine1)  {
    $val = $dataLine1->getValuesAtColumns($colname, 0);
    $tr1 = explode(" ", $val);
    $val = "";
    foreach ($tr1 as $ival) {
        $ival = trim($ival);
        if ($ival == "") continue;
        $ival = strtolower($ival);
        $ival = ucfirst($ival);
        $val = ($val == "") ? $ival : ( $val . " " . $ival );
    } 
    $dataLine1->setValuesAtColumns($colname, $val, 0);
}
$datafile1->setFilename("tservices_new.csv");
$datafile1->write();
?>