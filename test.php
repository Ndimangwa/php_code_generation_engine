<?php 
    require_once("ndima0001/sys/__autoload__.php");
    require_once("algorithm/csv2json.php");
    $mydata = CSV2JSON::transform("garage/mydata.csv", "garage/myoutputfile.json");
    echo "\n$mydata\n";
?>